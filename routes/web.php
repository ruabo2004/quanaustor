<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MoMoController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Admin\ReportsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::resource('products', ProductController::class);
Route::get('/products-bestsellers', [ProductController::class, 'bestSellers'])->name('products.bestsellers');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Cart routes for guests
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update-quantity/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('/cart/update-size/{cartItem}', [CartController::class, 'updateSize'])->name('cart.updateSize');

// MoMo payment routes (no auth needed for callbacks)
Route::get('/momo/return', [MoMoController::class, 'return'])->name('momo.return');
Route::post('/momo/ipn', [MoMoController::class, 'ipn'])->name('momo.ipn');

// Coupon routes
Route::post('/coupon/validate', [CouponController::class, 'validateCoupon'])->name('coupon.validate');
Route::post('/coupon/remove', [CouponController::class, 'removeCoupon'])->name('coupon.remove');
Route::get('/coupon/applied', [CouponController::class, 'getAppliedCoupon'])->name('coupon.applied');
Route::get('/coupons/available', [CouponController::class, 'availableCoupons'])->name('coupons.available');

// Routes yêu cầu đăng nhập
Route::middleware(['auth', 'migrate.cart'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});
// =============================================
// AUTHENTICATION ROUTES (GET + POST)
// =============================================

// ĐĂNG NHẬP Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// ĐĂNG KÝ Routes  
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// ĐĂNG XUẤT Route
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password Reset Routes (optional)
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes  
Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-notification', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.send');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// =============================================
// ADMIN ROUTES - FULL MANAGEMENT SYSTEM  
// =============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // DASHBOARD
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard.alt');
    
    // USERS MANAGEMENT - Full CRUD
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('users.destroy');

    // PRODUCTS MANAGEMENT - Full CRUD
    Route::get('/products', [App\Http\Controllers\AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [App\Http\Controllers\AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\AdminController::class, 'deleteProduct'])->name('products.destroy');
    
    // CATEGORIES MANAGEMENT - Full CRUD
    Route::get('/categories', [App\Http\Controllers\AdminController::class, 'categories'])->name('categories');
    Route::get('/categories/create', [App\Http\Controllers\AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [App\Http\Controllers\AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}/edit', [App\Http\Controllers\AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{category}', [App\Http\Controllers\AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('categories.destroy');
    
    // ORDERS MANAGEMENT - View & Update Status
    Route::get('/orders', [App\Http\Controllers\AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [App\Http\Controllers\AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{order}/status', [App\Http\Controllers\AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::put('/orders/{order}/payment-status', [App\Http\Controllers\AdminController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    
    
    // REPORTS - Báo cáo thống kê
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/charts', [ReportsController::class, 'charts'])->name('reports.charts');
    
    // REVIEWS MANAGEMENT
    Route::get('/reviews', [App\Http\Controllers\AdminController::class, 'reviews'])->name('reviews');
    Route::put('/reviews/{review}/approve', [App\Http\Controllers\AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::put('/reviews/{review}/reject', [App\Http\Controllers\AdminController::class, 'rejectReview'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [App\Http\Controllers\AdminController::class, 'deleteReview'])->name('reviews.destroy');
    
    // COUPONS MANAGEMENT
    Route::get('/coupons', [App\Http\Controllers\AdminController::class, 'coupons'])->name('coupons');
    Route::get('/coupons/create', [App\Http\Controllers\AdminController::class, 'createCoupon'])->name('coupons.create');
    Route::post('/coupons', [App\Http\Controllers\AdminController::class, 'storeCoupon'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [App\Http\Controllers\AdminController::class, 'editCoupon'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [App\Http\Controllers\AdminController::class, 'updateCoupon'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [App\Http\Controllers\AdminController::class, 'deleteCoupon'])->name('coupons.destroy');
    Route::post('/coupons/{coupon}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleCouponStatus'])->name('coupons.toggle-status');
    
});

// REVIEW ROUTES (for customers)
Route::middleware(['auth'])->group(function () {
    Route::post('/products/{product}/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});
