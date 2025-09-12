<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MigrateCartOnLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only run if user just logged in and has session cart items
        if (Auth::check() && Session::has('cart_migrated') === false) {
            $sessionId = Session::getId();
            
            // Get guest cart items from database
            $guestCartItems = Cart::where('session_id', $sessionId)
                                 ->whereNull('user_id')
                                 ->get();

            if ($guestCartItems->count() > 0) {
                foreach ($guestCartItems as $guestItem) {
                    // Check if user already has this product+size in cart
                    $existingItem = Cart::where('user_id', Auth::id())
                                       ->where('product_id', $guestItem->product_id)
                                       ->where('size', $guestItem->size)
                                       ->first();

                    if ($existingItem) {
                        // Merge quantities
                        $existingItem->increment('quantity', $guestItem->quantity);
                        $guestItem->delete();
                    } else {
                        // Transfer ownership to logged in user
                        $guestItem->update([
                            'user_id' => Auth::id(),
                            'session_id' => null
                        ]);
                    }
                }
            }

            // Mark cart as migrated for this session
            Session::put('cart_migrated', true);
        }

        return $next($request);
    }
}
















