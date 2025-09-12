<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name', 
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2'
    ];

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', Carbon::now())
                    ->where('end_date', '>=', Carbon::now());
    }

    /**
     * Scope for valid coupons (active and not exceeded usage limit)
     */
    public function scopeValid($query)
    {
        return $query->active()
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check date range
        $now = Carbon::now();
        if ($this->start_date > $now || $this->end_date < $now) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon can be applied to a given amount
     */
    public function canApplyTo(float $amount): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $amount): float
    {
        if (!$this->canApplyTo($amount)) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        } else { // percentage
            $discount = ($amount * $this->value) / 100;
            
            if ($this->maximum_discount) {
                $discount = min($discount, $this->maximum_discount);
            }
            
            return $discount;
        }
    }

    /**
     * Increment used count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Get formatted value for display
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'fixed') {
            return number_format($this->value) . ' VNĐ';
        } else {
            return $this->value . '%';
        }
    }

    /**
     * Get status for display
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Không hoạt động';
        }

        $now = Carbon::now();
        if ($this->start_date > $now) {
            return 'Chưa bắt đầu';
        }

        if ($this->end_date < $now) {
            return 'Đã hết hạn';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Đã hết lượt';
        }

        return 'Hoạt động';
    }
}
