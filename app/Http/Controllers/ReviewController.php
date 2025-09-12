<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để có thể đánh giá sản phẩm.');
        }

        // Check if user already reviewed this product
        $existingReview = $product->getUserReview(Auth::id());
        if ($existingReview) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        // Create review
        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false // Pending admin approval
        ]);

        return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt.');
    }

    /**
     * Update existing review
     */
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền chỉnh sửa đánh giá này.');
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'approved' => false // Reset approval status when updated
        ]);

        return back()->with('success', 'Đánh giá đã được cập nhật và đang chờ duyệt lại.');
    }

    /**
     * Delete review
     */
    public function destroy(Review $review)
    {
        // Check if user owns this review or is admin
        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa đánh giá này.'], 403);
            }
            return back()->with('error', 'Bạn không có quyền xóa đánh giá này.');
        }

        $review->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đánh giá đã được xóa.']);
        }

        return back()->with('success', 'Đánh giá đã được xóa.');
    }
}