<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_earned',
        'current_balance',
        'total_spent',
        'total_expired',
        'current_tier',
        'tier_progress',
        'points_to_next_tier',
        'tier_expiry_date',
        'annual_points_earned',
        'annual_spending',
        'annual_orders',
        'year_start_date',
        'achievements',
        'referral_points',
        'birthday_bonus_claimed',
        'last_activity_date'
    ];

    protected $casts = [
        'achievements' => 'array',
        'tier_expiry_date' => 'date',
        'year_start_date' => 'date',
        'last_activity_date' => 'date',
        'annual_spending' => 'decimal:2'
    ];

    /**
     * Get the user that owns the loyalty points
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user's point transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Get the current membership tier
     */
    public function membershipTier(): BelongsTo
    {
        return $this->belongsTo(MembershipTier::class, 'current_tier', 'name');
    }

    /**
     * Get next membership tier
     */
    public function getNextTierAttribute()
    {
        return MembershipTier::where('min_points_required', '>', $this->total_earned)
            ->where('is_active', true)
            ->orderBy('min_points_required', 'asc')
            ->first();
    }

    /**
     * Get tier progress percentage
     */
    public function getTierProgressPercentageAttribute()
    {
        $currentTier = $this->membershipTier;
        $nextTier = $this->next_tier;
        
        if (!$nextTier) {
            return 100; // Max tier reached
        }
        
        $currentTierPoints = $currentTier ? $currentTier->min_points_required : 0;
        $nextTierPoints = $nextTier->min_points_required;
        $userPoints = $this->total_earned;
        
        $progress = ($userPoints - $currentTierPoints) / ($nextTierPoints - $currentTierPoints) * 100;
        return min(100, max(0, $progress));
    }

    /**
     * Get user's tier benefits
     */
    public function getTierBenefitsAttribute()
    {
        $tier = $this->membershipTier;
        if (!$tier) {
            return null;
        }

        return [
            'discount_percentage' => $tier->discount_percentage,
            'points_per_vnd' => $tier->points_per_vnd,
            'free_shipping_threshold' => $tier->free_shipping_threshold,
            'priority_support' => $tier->priority_support,
            'early_access' => $tier->early_access,
            'birthday_bonus_points' => $tier->birthday_bonus_points,
            'referral_bonus_points' => $tier->referral_bonus_points,
            'exclusive_rewards' => $tier->exclusive_rewards
        ];
    }

    /**
     * Check if points are about to expire
     */
    public function getExpiringPointsAttribute()
    {
        $thirtyDaysFromNow = Carbon::now()->addDays(30);
        
        return PointTransaction::where('user_id', $this->user_id)
            ->where('type', 'earned')
            ->where('is_expired', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $thirtyDaysFromNow)
            ->sum('points');
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactionsAttribute()
    {
        return $this->transactions()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Check if user can redeem reward
     */
    public function canRedeemReward($pointsCost)
    {
        return $this->current_balance >= $pointsCost;
    }

    /**
     * Get annual summary
     */
    public function getAnnualSummaryAttribute()
    {
        return [
            'points_earned' => $this->annual_points_earned,
            'amount_spent' => $this->annual_spending,
            'orders_placed' => $this->annual_orders,
            'average_order_value' => $this->annual_orders > 0 ? $this->annual_spending / $this->annual_orders : 0,
            'year_start' => $this->year_start_date,
            'months_active' => Carbon::now()->diffInMonths($this->year_start_date) + 1
        ];
    }

    /**
     * Reset annual stats (called yearly)
     */
    public function resetAnnualStats()
    {
        $this->update([
            'annual_points_earned' => 0,
            'annual_spending' => 0,
            'annual_orders' => 0,
            'year_start_date' => Carbon::now()->startOfYear()
        ]);
    }

    /**
     * Add achievement
     */
    public function addAchievement($achievement)
    {
        $achievements = $this->achievements ?: [];
        if (!in_array($achievement, $achievements)) {
            $achievements[] = $achievement;
            $this->update(['achievements' => $achievements]);
        }
    }

    /**
     * Check if user has achievement
     */
    public function hasAchievement($achievement)
    {
        return in_array($achievement, $this->achievements ?: []);
    }

    /**
     * Get loyalty status summary
     */
    public function getStatusSummaryAttribute()
    {
        $tier = $this->membershipTier;
        $nextTier = $this->next_tier;
        
        return [
            'current_tier' => [
                'name' => $tier ? $tier->display_name : 'Đồng',
                'color' => $tier ? $tier->color : '#CD7F32',
                'icon' => $tier ? $tier->icon : 'fas fa-medal'
            ],
            'points' => [
                'current' => $this->current_balance,
                'total_earned' => $this->total_earned,
                'total_spent' => $this->total_spent,
                'expiring_soon' => $this->expiring_points
            ],
            'progress' => [
                'to_next_tier' => $this->points_to_next_tier,
                'percentage' => $this->tier_progress_percentage,
                'next_tier_name' => $nextTier ? $nextTier->display_name : null
            ],
            'annual' => $this->annual_summary,
            'achievements' => $this->achievements ?: [],
            'last_activity' => $this->last_activity_date ? $this->last_activity_date->diffForHumans() : null
        ];
    }
}
