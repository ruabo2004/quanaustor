<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponController extends Controller
{
    /**
     * Validate and apply coupon
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255'
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại.'
            ], 400);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc không còn hiệu lực.'
            ], 400);
        }

        // Get cart total (you might need to adjust this based on your cart logic)
        $cartTotal = $request->input('cart_total', 0);

        if (!$coupon->canApplyTo($cartTotal)) {
            $minAmount = number_format($coupon->minimum_amount);
            return response()->json([
                'success' => false,
                'message' => "Đơn hàng tối thiểu {$minAmount} VNĐ để sử dụng mã này."
            ], 400);
        }

        $discountAmount = $coupon->calculateDiscount($cartTotal);

        // Store coupon in session
        Session::put('applied_coupon', [
            'code' => $coupon->code,
            'discount_amount' => $discountAmount,
            'coupon_id' => $coupon->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_amount' => $discountAmount,
                'formatted_discount' => number_format($discountAmount) . ' VNĐ',
                'type' => $coupon->type,
                'value' => $coupon->formatted_value
            ]
        ]);
    }

    /**
     * Remove applied coupon
     */
    public function removeCoupon()
    {
        Session::forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy mã giảm giá.'
        ]);
    }

    /**
     * Get current applied coupon
     */
    public function getAppliedCoupon()
    {
        $appliedCoupon = Session::get('applied_coupon');

        if (!$appliedCoupon) {
            return response()->json([
                'success' => false,
                'message' => 'Không có mã giảm giá nào được áp dụng.'
            ]);
        }

        return response()->json([
            'success' => true,
            'coupon' => $appliedCoupon
        ]);
    }

    /**
     * List available coupons for customer
     */
    public function availableCoupons()
    {
        $coupons = Coupon::valid()
            ->where('is_active', true)
            ->orderBy('value', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'coupons' => $coupons->map(function ($coupon) {
                return [
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'description' => $coupon->description,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'formatted_value' => $coupon->formatted_value,
                    'minimum_amount' => $coupon->minimum_amount,
                    'formatted_minimum' => $coupon->minimum_amount ? number_format($coupon->minimum_amount) . ' VNĐ' : null,
                    'end_date' => $coupon->end_date->format('d/m/Y'),
                    'usage_limit' => $coupon->usage_limit,
                    'remaining_uses' => $coupon->usage_limit ? ($coupon->usage_limit - $coupon->used_count) : null
                ];
            })
        ]);
    }
}
