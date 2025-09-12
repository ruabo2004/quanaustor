<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Models\Coupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // Users Management
    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin'),
        ]);

        return redirect()->route('admin.users')->with('success', 'Người dùng đã được tạo thành công!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'is_admin' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->has('is_admin'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Thông tin người dùng đã được cập nhật!');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Người dùng đã được xóa!');
    }

    // Products Management
    public function products()
    {
        $products = Product::with(['category', 'sizes'])
            ->withSum('sizes', 'stock_quantity')
            ->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'measurements' => 'nullable|string',
            'length' => 'nullable|string',
            'garment_size' => 'nullable|string',
            'style' => 'nullable|string',
            'fit' => 'nullable|string',
            'material' => 'nullable|string',
            'leg_style' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'measurements' => $request->measurements,
            'length' => $request->length,
            'garment_size' => $request->garment_size,
            'style' => $request->style,
            'fit' => $request->fit,
            'material' => $request->material,
            'leg_style' => $request->leg_style,
        ]);

        return redirect()->route('admin.products')->with('success', 'Sản phẩm đã được tạo thành công!');
    }

    public function editProduct(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'style' => 'nullable|string',
            'fit' => 'nullable|string',
            'material' => 'nullable|string',
            'leg_style' => 'nullable|string',
            // Validation cho size-specific measurements (chỉ giữ 2 trường)
            'sizes' => 'nullable|array',
            'sizes.*.measurements' => 'nullable|string',
            'sizes.*.length' => 'nullable|string',
        ]);

        // Cập nhật thông tin chung của sản phẩm (loại bỏ measurements cũ)
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'style' => $request->style,
            'fit' => $request->fit,
            'material' => $request->material,
            'leg_style' => $request->leg_style,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // Cập nhật size-specific measurements
        if ($request->has('sizes') && is_array($request->sizes)) {
            foreach ($request->sizes as $sizeId => $sizeData) {
                $productSize = ProductSize::find($sizeId);
                
                if ($productSize && $productSize->product_id == $product->id) {
                    $updateData = [];
                    
                    // Cập nhật measurements, length và stock_quantity
                    if (isset($sizeData['measurements'])) {
                        $updateData['measurements'] = $sizeData['measurements'];
                    }
                    if (isset($sizeData['length'])) {
                        $updateData['length'] = $sizeData['length'];
                    }
                    if (isset($sizeData['stock_quantity'])) {
                        $updateData['stock_quantity'] = max(0, (int)$sizeData['stock_quantity']);
                    }
                    
                    if (!empty($updateData)) {
                        $productSize->update($updateData);
                    }
                }
            }
        }

        return redirect()->route('admin.products')->with('success', 'Sản phẩm, số đo và tồn kho đã được cập nhật thành công!');
    }

    public function deleteProduct(Product $product)
    {
        // Delete image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Sản phẩm đã được xóa!');
    }

    // Categories Management
    public function categories()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Danh mục đã được tạo thành công!');
    }

    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categories')->with('success', 'Danh mục đã được cập nhật!');
    }

    public function deleteCategory(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories')->with('error', 'Không thể xóa danh mục đang có sản phẩm!');
        }
        
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Danh mục đã được xóa!');
    }

    // Orders Management
    public function orders()
    {
        $orders = Order::with('user')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Trạng thái đơn hàng đã được cập nhật!');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        // Kiểm tra nếu payment status đã được cập nhật trước đó
        if ($order->payment_status_updated) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', 'Trạng thái thanh toán đã được cập nhật trước đó và không thể thay đổi!');
        }

        // Cập nhật payment status và đánh dấu đã được cập nhật
        $order->update([
            'payment_status' => $request->payment_status,
            'payment_status_updated' => true,
            'payment_status_updated_at' => now(),
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Trạng thái thanh toán đã được cập nhật!');
    }

    /**
     * Admin Reviews Management
     */
    public function reviews(Request $request)
    {
        $query = \App\Models\Review::with(['user', 'product']);
        
        // Filter by status if provided
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('approved', true);
            }
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $pendingCount = \App\Models\Review::where('approved', false)->count();
        $approvedCount = \App\Models\Review::where('approved', true)->count();
        
        return view('admin.reviews.index', compact('reviews', 'pendingCount', 'approvedCount'));
    }

    /**
     * Approve review
     */
    public function approveReview(\App\Models\Review $review)
    {
        $review->update(['approved' => true]);
        
        return back()->with('success', 'Đánh giá đã được duyệt.');
    }

    /**
     * Reject review (set as not approved)
     */
    public function rejectReview(\App\Models\Review $review)
    {
        $review->update(['approved' => false]);
        
        return back()->with('success', 'Đánh giá đã bị từ chối.');
    }

    /**
     * Delete review
     */
    public function deleteReview(\App\Models\Review $review)
    {
        $review->delete();
        
        return back()->with('success', 'Đánh giá đã được xóa.');
    }

    // ================================
    // COUPONS MANAGEMENT
    // ================================

    /**
     * Display coupons list
     */
    public function coupons()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show create coupon form
     */
    public function createCoupon()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function storeCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:coupons',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        // Additional validation for percentage type
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Phần trăm giảm giá không được vượt quá 100%'])->withInput();
        }

        Coupon::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_amount' => $request->minimum_amount,
            'maximum_discount' => $request->maximum_discount,
            'usage_limit' => $request->usage_limit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Mã giảm giá đã được tạo thành công!');
    }

    /**
     * Show edit coupon form
     */
    public function editCoupon(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon
     */
    public function updateCoupon(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        // Additional validation for percentage type
        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Phần trăm giảm giá không được vượt quá 100%'])->withInput();
        }

        $coupon->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'value' => $request->value,
            'minimum_amount' => $request->minimum_amount,
            'maximum_discount' => $request->maximum_discount,
            'usage_limit' => $request->usage_limit,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Mã giảm giá đã được cập nhật thành công!');
    }

    /**
     * Delete coupon
     */
    public function deleteCoupon(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Mã giảm giá đã được xóa thành công!');
    }

    /**
     * Toggle coupon status
     */
    public function toggleCouponStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        
        $status = $coupon->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        return back()->with('success', "Mã giảm giá đã được {$status}.");
    }

}
