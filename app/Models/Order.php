<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'total', 
        'status', 
        'customer_name', 
        'customer_email', 
        'customer_phone', 
        'customer_address',
        'payment_method',
        'payment_status',
        'payment_status_updated',
        'payment_status_updated_at',
        'momo_order_id',
        'momo_request_id',
        'coupon_code',
        'discount_amount',
        'subtotal'
    ];

    protected $casts = [
        'payment_status_updated' => 'boolean',
        'payment_status_updated_at' => 'datetime',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the coupon used for this order
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAttribute()
    {
        return number_format($this->discount_amount) . ' VNĐ';
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal) . ' VNĐ';
    }
}
