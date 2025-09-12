<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'size',
        'product_size_id',
        'quantity',
        'price',
        'base_price',
        'price_adjustment'
    ];

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Quan hệ với ProductSize
     */
    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    /**
     * Scope để lấy cart của user hiện tại hoặc session
     */
    public function scopeForCurrentUser($query, $userId = null, $sessionId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        } elseif ($sessionId) {
            return $query->where('session_id', $sessionId)->whereNull('user_id');
        }
        
        return $query->whereRaw('1 = 0'); // Empty result
    }

    /**
     * Tính tổng tiền
     */
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}