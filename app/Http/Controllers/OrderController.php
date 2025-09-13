<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Services\MoMoService;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->middleware('auth');
        $this->loyaltyService = $loyaltyService;
    }

    public function checkout()
    {
        // Check if cart has items
        $cartItems = \App\Models\Cart::when(Auth::check(), function($query) {
                                        return $query->where('user_id', Auth::id());
                                    }, function($query) {
                                        return $query->where('session_id', Session::getId())
                                                    ->whereNull('user_id');
                                    })
                                    ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống!');
        }

        return view('orders.checkout', compact('cartItems'));
    }

    public function store(Request $request)
    {
        // Validate customer information
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:1000',
            'payment_method' => 'required|in:cod,vnpay,momo',
        ], [
            'customer_name.required' => 'Vui lòng nhập họ và tên',
            'customer_email.required' => 'Vui lòng nhập email',
            'customer_email.email' => 'Email không đúng định dạng',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại',
            'customer_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
        ]);

        // Get cart items from database
        $cartItems = \App\Models\Cart::with('product')
                                    ->when(Auth::check(), function($query) {
                                        return $query->where('user_id', Auth::id());
                                    }, function($query) {
                                        return $query->where('session_id', Session::getId())
                                                    ->whereNull('user_id');
                                    })
                                    ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn trống!');
        }

        // Kiểm tra tồn kho trước khi đặt hàng
        foreach ($cartItems as $cartItem) {
            $productSize = ProductSize::where('product_id', $cartItem->product_id)
                                    ->where('size', $cartItem->size)
                                    ->first();
            
            if (!$productSize) {
                return redirect()->route('cart.index')->with('error', "Size {$cartItem->size} cho sản phẩm {$cartItem->product->name} không tồn tại!");
            }
            
            if ($productSize->stock_quantity < $cartItem->quantity) {
                return redirect()->route('cart.index')->with('error', 
                    "Không đủ hàng! Size {$cartItem->size} của sản phẩm {$cartItem->product->name} chỉ còn {$productSize->stock_quantity} sản phẩm.");
            }
        }

        $total = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        $paymentMethod = $request->payment_method;
        
        // Handle different payment methods
        if ($paymentMethod === 'vnpay') {
            return $this->processVNPayPayment($request, $cartItems, $total);
        } elseif ($paymentMethod === 'momo') {
            return $this->processMoMoPayment($request, $cartItems, $total);
        }
        
        // COD payment - process normally
        // Sử dụng transaction để đảm bảo data consistency
        DB::transaction(function () use ($cartItems, $total, $request) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                // Tạo order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'size' => $cartItem->size,
                ]);

                // Giảm số lượng tồn kho
                $productSize = ProductSize::where('product_id', $cartItem->product_id)
                                        ->where('size', $cartItem->size)
                                        ->first();
                
                $productSize->stock_quantity -= $cartItem->quantity;
                $productSize->save();
            }

            // Clear cart from database
            \App\Models\Cart::when(Auth::check(), function($query) {
                                return $query->where('user_id', Auth::id());
                            }, function($query) {
                                return $query->where('session_id', Session::getId())
                                            ->whereNull('user_id');
                            })->delete();

            // Award loyalty points for COD orders (since they're paid on delivery)
            if (Auth::check()) {
                try {
                    $this->loyaltyService->awardPointsForOrder($order->id);
                } catch (\Exception $e) {
                    // Log error but don't fail the order
                    \Log::error('Failed to award loyalty points for order ' . $order->id . ': ' . $e->getMessage());
                }
            }
        });

        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được đặt thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận và giao hàng.');
    }

    public function index()
    {
        $orders = Auth::user()->orders;
        return view('orders.index', compact('orders'));
    }

    private function processVNPayPayment($request, $cartItems, $total)
    {
        // TODO: Implement VNPAY integration
        // For now, return a placeholder response
        return redirect()->back()->with('info', 'Chức năng thanh toán VNPAY đang được phát triển. Vui lòng chọn COD hoặc MoMo.');
    }

    private function processMoMoPayment($request, $cartItems, $total)
    {
        try {
            // Tạo order trong database trước
            $order = null;
            DB::transaction(function () use ($cartItems, $total, $request, &$order) {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total' => $total,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'customer_address' => $request->customer_address,
                    'payment_method' => 'momo',
                    'payment_status' => 'pending',
                ]);

                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price,
                        'size' => $cartItem->size,
                    ]);

                    // Giảm số lượng tồn kho
                    $productSize = ProductSize::where('product_id', $cartItem->product_id)
                                            ->where('size', $cartItem->size)
                                            ->first();
                    
                    $productSize->stock_quantity -= $cartItem->quantity;
                    $productSize->save();
                }

                // Xóa giỏ hàng
                \App\Models\Cart::when(Auth::check(), function($query) {
                                    return $query->where('user_id', Auth::id());
                                }, function($query) {
                                    return $query->where('session_id', Session::getId())
                                                ->whereNull('user_id');
                                })->delete();
            });

            // Khởi tạo MoMo service
            $momoService = new MoMoService();
            
            // Tạo thông tin thanh toán
            $orderId = 'QAS' . $order->id . '_' . time();
            $orderInfo = "Thanh toán đơn hàng #" . $order->id . " - " . $request->customer_name;

            // Gọi MoMo API với payWithMethod để hiển thị payment options
            $result = $momoService->createPayment(
                $orderInfo,
                $total,
                $orderId,
                null,
                null,
                'payWithMethod' // Force show payment method selection
            );

            if (isset($result['resultCode']) && $result['resultCode'] == 0) {
                // Cập nhật order với MoMo order ID
                $order->update([
                    'momo_order_id' => $orderId,
                    'momo_request_id' => $result['requestId'] ?? null
                ]);

                // Redirect đến MoMo payment URL
                return redirect($result['payUrl']);
            } else {
                // Payment creation failed
                return redirect()->route('checkout')
                    ->with('error', 'Không thể tạo thanh toán MoMo: ' . ($result['message'] ?? 'Lỗi không xác định'));
            }

        } catch (\Exception $e) {
            return redirect()->route('checkout')
                ->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
}
