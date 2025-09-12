<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories for filter dropdown
        $categories = Category::all();
        
        // Start query with relationships
        $query = Product::with(['sizes', 'category']);
        
        // Search by name or description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('style', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('material', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Filter by style
        if ($request->filled('style') && $request->style != 'all') {
            $query->where('style', $request->style);
        }
        
        // Filter by material
        if ($request->filled('material') && $request->material != 'all') {
            $query->where('material', $request->material);
        }
        
        // Filter by fit
        if ($request->filled('fit') && $request->fit != 'all') {
            $query->where('fit', $request->fit);
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortOrder);
                break;
        }
        
        // Get products with pagination
        $products = $query->paginate(12)->appends($request->query());
        
        // Get filter options for dropdowns
        $styles = Product::distinct()->whereNotNull('style')->pluck('style');
        $materials = Product::distinct()->whereNotNull('material')->pluck('material');
        $fits = Product::distinct()->whereNotNull('fit')->pluck('fit');
        
        // Get price range
        $priceRange = Product::selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        
        return view('products.index', compact(
            'products', 
            'categories', 
            'styles', 
            'materials', 
            'fits', 
            'priceRange'
        ));
    }

    public function show(Product $product)
    {
        $product->load(['sizes', 'approvedReviews.user']);
        
        // Get user's review if they are logged in
        $userReview = null;
        if (auth()->check()) {
            $userReview = $product->getUserReview(auth()->id());
        }
        
        return view('products.show', compact('product', 'userReview'));
    }

    /**
     * Display best selling products
     */
    public function bestSellers()
    {
        $bestSellers = Product::with(['category', 'sizes'])
            ->select('products.*', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function($query) {
                $query->where('orders.payment_status', 'paid')
                      ->orWhereNull('orders.payment_status');
            })
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(20)
            ->get();

        return view('products.bestsellers', compact('bestSellers'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'measurements' => 'nullable|string|max:255',
            'length' => 'nullable|string|max:255',
            'garment_size' => 'nullable|string|max:255',
            'style' => 'nullable|string|max:255',
            'fit' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'leg_style' => 'nullable|string|max:255', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'measurements' => $request->measurements,
            'length' => $request->length,
            'garment_size' => $request->garment_size,
            'style' => $request->style,
            'fit' => $request->fit,
            'material' => $request->material,
            'leg_style' => $request->leg_style,
        ]);

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'measurements' => 'nullable|string|max:255',
            'length' => 'nullable|string|max:255',
            'garment_size' => 'nullable|string|max:255',
            'style' => 'nullable|string|max:255',
            'fit' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'leg_style' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        } else {
            $imagePath = $product->image;
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'measurements' => $request->measurements,
            'length' => $request->length,
            'garment_size' => $request->garment_size,
            'style' => $request->style,
            'fit' => $request->fit,
            'material' => $request->material,
            'leg_style' => $request->leg_style,
        ]);

        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa thành công!');
    }
}

