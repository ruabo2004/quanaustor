<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'measurements',
        'length',
        'garment_size',
        'chest_measurement',
        'waist_measurement',
        'hip_measurement',
        'stock_quantity',
        'price_adjustment'
    ];

    /**
     * Quan hệ với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Lấy giá cuối cùng cho size này (giá gốc + điều chỉnh)
     */
    public function getFinalPriceAttribute()
    {
        return $this->product->price + $this->price_adjustment;
    }

    /**
     * Kiểm tra còn hàng
     */
    public function inStock()
    {
        return $this->stock_quantity > 0;
    }
}