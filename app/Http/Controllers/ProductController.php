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
        
        // Enhanced Search with multiple fields and weights
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('style', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('material', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('fit', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('leg_style', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('measurements', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }
        
        // Filter by multiple categories
        if ($request->filled('categories') && is_array($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        } elseif ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }
        
        // Enhanced price range filtering with min/max constraints
        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', floatval($request->min_price));
        }
        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', floatval($request->max_price));
        }
        
        // Filter by multiple styles
        if ($request->filled('styles') && is_array($request->styles)) {
            $query->whereIn('style', $request->styles);
        } elseif ($request->filled('style') && $request->style != 'all') {
            $query->where('style', $request->style);
        }
        
        // Filter by multiple materials
        if ($request->filled('materials') && is_array($request->materials)) {
            $query->whereIn('material', $request->materials);
        } elseif ($request->filled('material') && $request->material != 'all') {
            $query->where('material', $request->material);
        }
        
        // Filter by multiple fits
        if ($request->filled('fits') && is_array($request->fits)) {
            $query->whereIn('fit', $request->fits);
        } elseif ($request->filled('fit') && $request->fit != 'all') {
            $query->where('fit', $request->fit);
        }
        
        // Filter by sizes (checking product_sizes table)
        if ($request->filled('sizes') && is_array($request->sizes)) {
            $query->whereHas('sizes', function($sizeQuery) use ($request) {
                $sizeQuery->whereIn('size', $request->sizes)
                         ->where('stock_quantity', '>', 0); // Only available sizes
            });
        }
        
        // Filter by availability
        if ($request->filled('availability')) {
            switch ($request->availability) {
                case 'in_stock':
                    $query->whereHas('sizes', function($sizeQuery) {
                        $sizeQuery->where('stock_quantity', '>', 0);
                    });
                    break;
                case 'out_of_stock':
                    $query->whereDoesntHave('sizes', function($sizeQuery) {
                        $sizeQuery->where('stock_quantity', '>', 0);
                    });
                    break;
                case 'low_stock':
                    $query->whereHas('sizes', function($sizeQuery) {
                        $sizeQuery->where('stock_quantity', '<=', 5)
                                 ->where('stock_quantity', '>', 0);
                    });
                    break;
            }
        }
        
        // Filter by price ranges (quick filters)
        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'under_500k':
                    $query->where('price', '<', 500000);
                    break;
                case '500k_1m':
                    $query->whereBetween('price', [500000, 1000000]);
                    break;
                case '1m_2m':
                    $query->whereBetween('price', [1000000, 2000000]);
                    break;
                case 'over_2m':
                    $query->where('price', '>', 2000000);
                    break;
            }
        }
        
        // Enhanced Sorting with more options
        $sortBy = $request->get('sort', 'newest');
        $sortOrder = $request->get('order', 'desc');
        
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
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popularity':
                // Sort by total sold quantity
                $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                      ->select('products.*', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
                      ->groupBy('products.id')
                      ->orderByDesc('total_sold');
                break;
            case 'rating':
                // Sort by average rating
                $query->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
                      ->select('products.*', DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating'))
                      ->where(function($q) {
                          $q->where('reviews.status', 'approved')
                            ->orWhereNull('reviews.status');
                      })
                      ->groupBy('products.id')
                      ->orderByDesc('avg_rating');
                break;
            default:
                $query->orderBy('name', $sortOrder);
                break;
        }
        
        // Determine pagination
        $perPage = $request->get('per_page', 12);
        if (!in_array($perPage, [12, 24, 48, 96])) {
            $perPage = 12;
        }
        
        // Get products with pagination
        $products = $query->paginate($perPage)->appends($request->query());
        
        // Get filter options for dropdowns with counts
        $styles = Product::select('style', DB::raw('COUNT(*) as count'))
                        ->whereNotNull('style')
                        ->groupBy('style')
                        ->orderBy('count', 'desc')
                        ->get();
                        
        $materials = Product::select('material', DB::raw('COUNT(*) as count'))
                           ->whereNotNull('material')
                           ->groupBy('material')
                           ->orderBy('count', 'desc')
                           ->get();
                           
        $fits = Product::select('fit', DB::raw('COUNT(*) as count'))
                      ->whereNotNull('fit')
                      ->groupBy('fit')
                      ->orderBy('count', 'desc')
                      ->get();
        
        // Get available sizes with stock counts
        $availableSizes = DB::table('product_sizes')
                           ->join('products', 'product_sizes.product_id', '=', 'products.id')
                           ->select('product_sizes.size', DB::raw('COUNT(DISTINCT products.id) as product_count'), DB::raw('SUM(product_sizes.stock_quantity) as total_stock'))
                           ->where('product_sizes.stock_quantity', '>', 0)
                           ->groupBy('product_sizes.size')
                           ->orderBy('product_sizes.size')
                           ->get();
        
        // Get price range and statistics
        $priceStats = Product::selectRaw('
            MIN(price) as min_price, 
            MAX(price) as max_price,
            AVG(price) as avg_price,
            COUNT(*) as total_products
        ')->first();
        
        // Calculate filter counts for UI
        $filterCounts = [
            'total' => $products->total(),
            'in_stock' => Product::whereHas('sizes', function($q) {
                $q->where('stock_quantity', '>', 0);
            })->count(),
            'on_sale' => 0, // Add sale logic if needed
        ];
        
        // For AJAX requests, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('products.partials.product-grid', compact('products'))->render(),
                'pagination' => view('products.partials.pagination', compact('products'))->render(),
                'total' => $products->total(),
                'showing' => [
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'total' => $products->total()
                ]
            ]);
        }
        
        return view('products.index', compact(
            'products', 
            'categories', 
            'styles', 
            'materials', 
            'fits', 
            'availableSizes',
            'priceStats',
            'filterCounts'
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

    /**
     * Get search suggestions for autocomplete
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        // Get product name suggestions
        $productSuggestions = Product::where('name', 'LIKE', "%{$query}%")
                                   ->select('name as value', DB::raw("'product' as type"))
                                   ->limit(5)
                                   ->get();
        
        // Get category suggestions
        $categorySuggestions = Category::where('name', 'LIKE', "%{$query}%")
                                     ->select('name as value', DB::raw("'category' as type"))
                                     ->limit(3)
                                     ->get();
        
        // Get style suggestions
        $styleSuggestions = Product::where('style', 'LIKE', "%{$query}%")
                                 ->whereNotNull('style')
                                 ->select('style as value', DB::raw("'style' as type"))
                                 ->distinct()
                                 ->limit(3)
                                 ->get();
        
        // Get material suggestions
        $materialSuggestions = Product::where('material', 'LIKE', "%{$query}%")
                                    ->whereNotNull('material')
                                    ->select('material as value', DB::raw("'material' as type"))
                                    ->distinct()
                                    ->limit(3)
                                    ->get();
        
        $suggestions = collect()
            ->concat($productSuggestions)
            ->concat($categorySuggestions)
            ->concat($styleSuggestions)
            ->concat($materialSuggestions)
            ->unique('value')
            ->values();
        
        return response()->json($suggestions);
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

