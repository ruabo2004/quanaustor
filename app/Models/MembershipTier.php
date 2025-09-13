<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'color',
        'icon',
        'min_points_required',
        'min_annual_spending',
        'min_orders_per_year',
        'points_per_vnd',
        'birthday_bonus_multiplier',
        'special_event_multiplier',
        'discount_percentage',
        'free_shipping_threshold',
        'priority_support',
        'early_access',
        'birthday_bonus_points',
        'annual_bonus_points',
        'referral_bonus_points',
        'exclusive_rewards',
        'tier_validity_months',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'min_annual_spending' => 'decimal:2',
        'points_per_vnd' => 'decimal:4',
        'birthday_bonus_multiplier' => 'decimal:2',
        'special_event_multiplier' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'priority_support' => 'boolean',
        'early_access' => 'boolean',
        'exclusive_rewards' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get users with this tier
     */
    public function users(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class, 'current_tier', 'name');
    }

    /**
     * Get next tier
     */
    public function getNextTierAttribute()
    {
        return static::where('min_points_required', '>', $this->min_points_required)
            ->where('is_active', true)
            ->orderBy('min_points_required', 'asc')
            ->first();
    }

    /**
     * Get previous tier
     */
    public function getPreviousTierAttribute()
    {
        return static::where('min_points_required', '<', $this->min_points_required)
            ->where('is_active', true)
            ->orderBy('min_points_required', 'desc')
            ->first();
    }

    /**
     * Calculate points required to reach this tier from a given point total
     */
    public function pointsRequiredFrom($currentPoints)
    {
        return max(0, $this->min_points_required - $currentPoints);
    }

    /**
     * Get tier benefits summary
     */
    public function getBenefitsSummaryAttribute()
    {
        $benefits = [];

        if ($this->discount_percentage > 0) {
            $benefits[] = "Giảm giá {$this->discount_percentage}% mọi đơn hàng";
        }

        if ($this->free_shipping_threshold !== null) {
            if ($this->free_shipping_threshold == 0) {
                $benefits[] = "Miễn phí giao hàng toàn bộ đơn hàng";
            } else {
                $benefits[] = "Miễn phí giao hàng cho đơn từ " . number_format($this->free_shipping_threshold) . " VNĐ";
            }
        }

        if ($this->birthday_bonus_points > 0) {
            $benefits[] = "Tặng {$this->birthday_bonus_points} điểm sinh nhật";
        }

        if ($this->annual_bonus_points > 0) {
            $benefits[] = "Thưởng {$this->annual_bonus_points} điểm hàng năm";
        }

        if ($this->priority_support) {
            $benefits[] = "Hỗ trợ khách hàng ưu tiên";
        }

        if ($this->early_access) {
            $benefits[] = "Truy cập sớm các chương trình khuyến mãi";
        }

        $pointsRate = $this->points_per_vnd * 10000; // Points per 10,000 VND
        $benefits[] = "Tích " . number_format($pointsRate, 1) . " điểm cho mỗi 10,000 VNĐ";

        return $benefits;
    }

    /**
     * Get tier icon with color
     */
    public function getIconWithColorAttribute()
    {
        return [
            'icon' => $this->icon,
            'color' => $this->color,
            'class' => $this->icon . ' loyalty-tier-icon'
        ];
    }

    /**
     * Scope for active tiers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering tiers
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('min_points_required', 'asc');
    }

    /**
     * Get tier by user's points
     */
    public static function getTierByPoints($totalPoints, $annualSpending = 0, $annualOrders = 0)
    {
        return static::where('is_active', true)
            ->where('min_points_required', '<=', $totalPoints)
            ->where('min_annual_spending', '<=', $annualSpending)
            ->where('min_orders_per_year', '<=', $annualOrders)
            ->orderBy('min_points_required', 'desc')
            ->first();
    }

    /**
     * Get all tiers for display
     */
    public static function getAllTiersForDisplay()
    {
        return static::active()
            ->ordered()
            ->get()
            ->map(function ($tier) {
                return [
                    'id' => $tier->id,
                    'name' => $tier->name,
                    'display_name' => $tier->display_name,
                    'color' => $tier->color,
                    'icon' => $tier->icon,
                    'min_points' => $tier->min_points_required,
                    'benefits' => $tier->benefits_summary,
                    'discount' => $tier->discount_percentage,
                    'free_shipping' => $tier->free_shipping_threshold,
                    'priority_support' => $tier->priority_support,
                    'early_access' => $tier->early_access
                ];
            });
    }
}
