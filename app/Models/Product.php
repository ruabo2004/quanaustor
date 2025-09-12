<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image', 'category_id', 'measurements', 'length', 'garment_size', 'style', 'fit', 'material', 'leg_style'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Quan hệ với ProductSize
     */
    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    /**
     * Lấy danh sách size có sẵn
     */
    public function getAvailableSizesAttribute()
    {
        return $this->sizes()->where('stock_quantity', '>', 0)->pluck('size')->toArray();
    }

    /**
     * Quan hệ với Review
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Lấy reviews đã được duyệt
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->approved();
    }

    /**
     * Tính điểm đánh giá trung bình
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?: 0;
    }

    /**
     * Lấy tổng số đánh giá đã duyệt
     */
    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Kiểm tra user đã đánh giá sản phẩm này chưa
     */
    public function hasUserReviewed($userId)
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * Lấy review của user cho sản phẩm này
     */
    public function getUserReview($userId)
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }
}
