<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        return view('cart.index', compact('cartItems'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $size = $request->input('size', 'XL');
        $sizeId = $request->input('size_id', null);
        $priceAdjustment = 0;
        
        if ($sizeId) {
            $productSize = ProductSize::find($sizeId);
            if ($productSize) {
                $priceAdjustment = $productSize->price_adjustment;
            }
        } else {
            // Nếu không có size_id, tìm theo product_id và size
            $productSize = ProductSize::where('product_id', $id)
                                    ->where('size', $size)
                                    ->first();
        }

        // Kiểm tra tồn kho
        if (!$productSize) {
            $errorMessage = "Size {$size} không có sẵn cho sản phẩm này!";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        if ($productSize->stock_quantity <= 0) {
            $errorMessage = "Size {$size} đã hết hàng!";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 400);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        $finalPrice = $product->price + $priceAdjustment;
        $cartItem = Cart::where('product_id', $id)
                       ->where('size', $size)
                       ->when(Auth::check(), function($query) {
                           return $query->where('user_id', Auth::id());
                       }, function($query) {
                           return $query->where('session_id', Session::getId())
                                       ->whereNull('user_id');
                       })
                       ->first();

        if ($cartItem) {
            // Kiểm tra số lượng sau khi tăng có vượt quá tồn kho không
            if ($cartItem->quantity + 1 > $productSize->stock_quantity) {
                $errorMessage = "Không thể thêm! Size {$size} chỉ còn {$productSize->stock_quantity} sản phẩm, bạn đã có {$cartItem->quantity} trong giỏ hàng.";
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 400);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            $cartItem->increment('quantity');
        } else {
            Cart::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'session_id' => Auth::check() ? null : Session::getId(),
                'product_id' => $id,
                'size' => $size,
                'product_size_id' => $sizeId,
                'quantity' => 1,
                'price' => $finalPrice,
                'base_price' => $product->price,
                'price_adjustment' => $priceAdjustment
            ]);
        }

        $message = "Sản phẩm size {$size} đã được thêm vào giỏ hàng thành công! (Còn lại: " . ($productSize->stock_quantity - ($cartItem ? $cartItem->quantity : 1)) . ")";
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => self::getCartCount()
            ]);
        }
        
        return redirect()->back()->with('success', $message);
    }

    public function remove($cartItemId)
    {
        $cartItem = Cart::when(Auth::check(), function($query) {
                               return $query->where('user_id', Auth::id());
                           }, function($query) {
                               return $query->where('session_id', Session::getId())
                                           ->whereNull('user_id');
                           })
                           ->findOrFail($cartItemId);

        $cartItem->delete();

        return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng thành công!');
    }

    public function updateQuantity(Request $request, $cartItemId)
    {
        $cartItem = Cart::when(Auth::check(), function($query) {
                               return $query->where('user_id', Auth::id());
                           }, function($query) {
                               return $query->where('session_id', Session::getId())
                                           ->whereNull('user_id');
                           })
                           ->findOrFail($cartItemId);

        $newQuantity = $request->input('quantity');
        
        // Validate quantity
        if ($newQuantity < 1) {
            return response()->json(['success' => false, 'message' => 'Số lượng phải lớn hơn 0!'], 400);
        }

        // Check stock availability
        $productSize = ProductSize::where('product_id', $cartItem->product_id)
                                 ->where('size', $cartItem->size)
                                 ->first();

        if (!$productSize || $newQuantity > $productSize->stock_quantity) {
            $available = $productSize ? $productSize->stock_quantity : 0;
            return response()->json([
                'success' => false, 
                'message' => "Chỉ còn {$available} sản phẩm size {$cartItem->size} trong kho!"
            ], 400);
        }

        $cartItem->update(['quantity' => $newQuantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật số lượng thành công!',
            'cartCount' => self::getCartCount()
        ]);
    }

    public function updateSize(Request $request, $cartItemId)
    {
        $cartItem = Cart::when(Auth::check(), function($query) {
                               return $query->where('user_id', Auth::id());
                           }, function($query) {
                               return $query->where('session_id', Session::getId())
                                           ->whereNull('user_id');
                           })
                           ->findOrFail($cartItemId);

        $newSize = $request->input('size');
        $newSizeId = $request->input('size_id');
        $priceAdjustment = $request->input('price_adjustment', 0);
        
        // Check if size is available
        $productSize = ProductSize::find($newSizeId);
        if (!$productSize || $productSize->stock_quantity < $cartItem->quantity) {
            $available = $productSize ? $productSize->stock_quantity : 0;
            return response()->json([
                'success' => false, 
                'message' => "Size {$newSize} chỉ còn {$available} sản phẩm, không đủ cho số lượng {$cartItem->quantity} bạn đã chọn!"
            ], 400);
        }

        // Check if this size already exists in cart
        $existingCartItem = Cart::where('product_id', $cartItem->product_id)
                               ->where('size', $newSize)
                               ->where('id', '!=', $cartItemId)
                               ->when(Auth::check(), function($query) {
                                   return $query->where('user_id', Auth::id());
                               }, function($query) {
                                   return $query->where('session_id', Session::getId())
                                               ->whereNull('user_id');
                               })
                               ->first();

        if ($existingCartItem) {
            // Merge quantities
            $totalQuantity = $existingCartItem->quantity + $cartItem->quantity;
            if ($totalQuantity > $productSize->stock_quantity) {
                return response()->json([
                    'success' => false, 
                    'message' => "Không thể gộp! Bạn đã có {$existingCartItem->quantity} sản phẩm size {$newSize} trong giỏ. Tổng số lượng sẽ vượt quá tồn kho ({$productSize->stock_quantity})!"
                ], 400);
            }
            
            $existingCartItem->update(['quantity' => $totalQuantity]);
            $cartItem->delete();
        } else {
            // Update current item
            $newPrice = $cartItem->base_price + $priceAdjustment;
            $cartItem->update([
                'size' => $newSize,
                'product_size_id' => $newSizeId,
                'price' => $newPrice,
                'price_adjustment' => $priceAdjustment
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật size thành {$newSize} thành công!",
            'cartCount' => self::getCartCount()
        ]);
    }
    private function getCartItems()
    {
        return Cart::with(['product', 'product.sizes'])
                   ->when(Auth::check(), function($query) {
                       return $query->where('user_id', Auth::id());
                   }, function($query) {
                       return $query->where('session_id', Session::getId())
                                   ->whereNull('user_id');
                   })
                   ->get();
    }
    public static function getCartCount()
    {
        return Cart::when(Auth::check(), function($query) {
                       return $query->where('user_id', Auth::id());
                   }, function($query) {
                       return $query->where('session_id', Session::getId())
                                   ->whereNull('user_id');
                   })
                   ->sum('quantity');
    }
}
