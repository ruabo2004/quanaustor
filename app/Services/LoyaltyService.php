<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoyaltyPoint;
use App\Models\MembershipTier;
use App\Models\PointTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoyaltyService
{
    /**
     * Initialize loyalty account for new user
     */
    public function initializeLoyaltyAccount($userId)
    {
        $loyaltyAccount = LoyaltyPoint::firstOrCreate(
            ['user_id' => $userId],
            [
                'total_earned' => 0,
                'current_balance' => 0,
                'total_spent' => 0,
                'total_expired' => 0,
                'current_tier' => 'bronze',
                'tier_progress' => 0,
                'points_to_next_tier' => 1000,
                'tier_expiry_date' => Carbon::now()->addYear(),
                'annual_points_earned' => 0,
                'annual_spending' => 0,
                'annual_orders' => 0,
                'year_start_date' => Carbon::now()->startOfYear(),
                'achievements' => [],
                'referral_points' => 0,
                'birthday_bonus_claimed' => 0,
                'last_activity_date' => Carbon::now()
            ]
        );

        // Add welcome bonus
        if ($loyaltyAccount->wasRecentlyCreated) {
            $this->awardPoints(
                $userId,
                100,
                'Registration',
                null,
                'Thưởng chào mừng thành viên mới'
            );
        }

        return $loyaltyAccount;
    }

    /**
     * Award points to user
     */
    public function awardPoints($userId, $points, $sourceType, $sourceId = null, $description = '', $metadata = [], $expiryDays = 365)
    {
        return DB::transaction(function () use ($userId, $points, $sourceType, $sourceId, $description, $metadata, $expiryDays) {
            $loyaltyAccount = LoyaltyPoint::where('user_id', $userId)->first();
            if (!$loyaltyAccount) {
                $loyaltyAccount = $this->initializeLoyaltyAccount($userId);
            }

            $balanceBefore = $loyaltyAccount->current_balance;
            $balanceAfter = $balanceBefore + $points;

            // Create transaction record
            $transaction = PointTransaction::create([
                'user_id' => $userId,
                'type' => 'earned',
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
                'metadata' => $metadata,
                'expires_at' => Carbon::now()->addDays($expiryDays),
                'status' => 'completed'
            ]);

            // Update loyalty account
            $loyaltyAccount->update([
                'total_earned' => $loyaltyAccount->total_earned + $points,
                'current_balance' => $balanceAfter,
                'annual_points_earned' => $loyaltyAccount->annual_points_earned + $points,
                'last_activity_date' => Carbon::now()
            ]);

            // Check for tier upgrade
            $this->checkTierUpgrade($loyaltyAccount);

            // Check for achievements
            $this->checkAchievements($loyaltyAccount);

            return $transaction;
        });
    }

    /**
     * Award points for order completion
     */
    public function awardPointsForOrder($orderId)
    {
        $order = Order::with('user')->find($orderId);
        if (!$order || !$order->user) {
            return null;
        }

        // Only award points for paid orders
        if ($order->payment_status !== 'paid') {
            return null;
        }

        // Check if points already awarded
        $existingTransaction = PointTransaction::where('order_id', $orderId)
            ->where('type', 'earned')
            ->first();
        if ($existingTransaction) {
            return $existingTransaction;
        }

        $loyaltyAccount = LoyaltyPoint::where('user_id', $order->user_id)->first();
        if (!$loyaltyAccount) {
            $loyaltyAccount = $this->initializeLoyaltyAccount($order->user_id);
        }

        // Get user's tier to calculate points
        $tier = $loyaltyAccount->membershipTier;
        $pointsPerVnd = $tier ? $tier->points_per_vnd : 0.0001; // Default 1 point per 10,000 VND

        // Calculate points
        $points = (int) floor($order->total_amount * $pointsPerVnd);
        
        if ($points <= 0) {
            return null;
        }

        // Award points
        $transaction = $this->awardPoints(
            $order->user_id,
            $points,
            'Order',
            $order->id,
            "Tích điểm cho đơn hàng #{$order->id}",
            [
                'order_total' => $order->total_amount,
                'points_rate' => $pointsPerVnd,
                'tier' => $tier ? $tier->name : 'bronze'
            ]
        );

        // Update transaction with order details
        $transaction->update([
            'order_id' => $orderId,
            'order_total' => $order->total_amount,
            'order_status' => $order->status
        ]);

        // Update annual spending and orders
        $loyaltyAccount->update([
            'annual_spending' => $loyaltyAccount->annual_spending + $order->total_amount,
            'annual_orders' => $loyaltyAccount->annual_orders + 1
        ]);

        return $transaction;
    }

    /**
     * Spend points (redeem rewards)
     */
    public function spendPoints($userId, $points, $sourceType, $sourceId = null, $description = '', $metadata = [])
    {
        return DB::transaction(function () use ($userId, $points, $sourceType, $sourceId, $description, $metadata) {
            $loyaltyAccount = LoyaltyPoint::where('user_id', $userId)->first();
            if (!$loyaltyAccount) {
                throw new \Exception('Tài khoản tích điểm không tồn tại');
            }

            if ($loyaltyAccount->current_balance < $points) {
                throw new \Exception('Số điểm không đủ để thực hiện giao dịch');
            }

            $balanceBefore = $loyaltyAccount->current_balance;
            $balanceAfter = $balanceBefore - $points;

            // Create transaction record
            $transaction = PointTransaction::create([
                'user_id' => $userId,
                'type' => 'spent',
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'description' => $description,
                'metadata' => $metadata,
                'status' => 'completed'
            ]);

            // Update loyalty account
            $loyaltyAccount->update([
                'current_balance' => $balanceAfter,
                'total_spent' => $loyaltyAccount->total_spent + $points,
                'last_activity_date' => Carbon::now()
            ]);

            return $transaction;
        });
    }

    /**
     * Check and upgrade tier
     */
    public function checkTierUpgrade($loyaltyAccount)
    {
        $currentTierName = $loyaltyAccount->current_tier;
        $totalPoints = $loyaltyAccount->total_earned;
        $annualSpending = $loyaltyAccount->annual_spending;
        $annualOrders = $loyaltyAccount->annual_orders;

        // Find appropriate tier
        $newTier = MembershipTier::getTierByPoints($totalPoints, $annualSpending, $annualOrders);
        
        if (!$newTier || $newTier->name === $currentTierName) {
            // Update progress to next tier
            $nextTier = MembershipTier::where('min_points_required', '>', $totalPoints)
                ->where('is_active', true)
                ->orderBy('min_points_required', 'asc')
                ->first();
            
            if ($nextTier) {
                $loyaltyAccount->update([
                    'points_to_next_tier' => $nextTier->min_points_required - $totalPoints
                ]);
            }
            return false;
        }

        // Upgrade tier
        $oldTier = MembershipTier::where('name', $currentTierName)->first();
        
        $loyaltyAccount->update([
            'current_tier' => $newTier->name,
            'tier_expiry_date' => Carbon::now()->addMonths($newTier->tier_validity_months),
            'points_to_next_tier' => $this->calculatePointsToNextTier($newTier, $totalPoints)
        ]);

        // Award tier upgrade bonus
        if ($newTier->annual_bonus_points > 0) {
            $this->awardPoints(
                $loyaltyAccount->user_id,
                $newTier->annual_bonus_points,
                'TierUpgrade',
                $newTier->id,
                "Thưởng nâng hạng lên {$newTier->display_name}",
                ['old_tier' => $oldTier?->name, 'new_tier' => $newTier->name]
            );
        }

        // Add achievement
        $loyaltyAccount->addAchievement("tier_upgrade_{$newTier->name}");

        return true;
    }

    /**
     * Calculate points to next tier
     */
    private function calculatePointsToNextTier($currentTier, $totalPoints)
    {
        $nextTier = MembershipTier::where('min_points_required', '>', $currentTier->min_points_required)
            ->where('is_active', true)
            ->orderBy('min_points_required', 'asc')
            ->first();

        return $nextTier ? $nextTier->min_points_required - $totalPoints : 0;
    }

    /**
     * Check for achievements
     */
    public function checkAchievements($loyaltyAccount)
    {
        $achievements = [];

        // Point milestones
        $pointMilestones = [500, 1000, 2500, 5000, 10000, 25000, 50000, 100000];
        foreach ($pointMilestones as $milestone) {
            if ($loyaltyAccount->total_earned >= $milestone && !$loyaltyAccount->hasAchievement("points_{$milestone}")) {
                $achievements[] = "points_{$milestone}";
                $loyaltyAccount->addAchievement("points_{$milestone}");
            }
        }

        // Spending milestones
        $spendingMilestones = [1000000, 5000000, 10000000, 25000000, 50000000, 100000000]; // VND
        foreach ($spendingMilestones as $milestone) {
            if ($loyaltyAccount->annual_spending >= $milestone && !$loyaltyAccount->hasAchievement("spending_{$milestone}")) {
                $achievements[] = "spending_{$milestone}";
                $loyaltyAccount->addAchievement("spending_{$milestone}");
            }
        }

        // Order count milestones
        $orderMilestones = [5, 10, 25, 50, 100];
        foreach ($orderMilestones as $milestone) {
            if ($loyaltyAccount->annual_orders >= $milestone && !$loyaltyAccount->hasAchievement("orders_{$milestone}")) {
                $achievements[] = "orders_{$milestone}";
                $loyaltyAccount->addAchievement("orders_{$milestone}");
            }
        }

        return $achievements;
    }

    /**
     * Award birthday bonus
     */
    public function awardBirthdayBonus($userId)
    {
        $user = User::find($userId);
        if (!$user || !$user->date_of_birth) {
            return null;
        }

        $loyaltyAccount = LoyaltyPoint::where('user_id', $userId)->first();
        if (!$loyaltyAccount) {
            $loyaltyAccount = $this->initializeLoyaltyAccount($userId);
        }

        // Check if already claimed this year
        $currentYear = Carbon::now()->year;
        if ($loyaltyAccount->birthday_bonus_claimed >= $currentYear) {
            return null;
        }

        $tier = $loyaltyAccount->membershipTier;
        $birthdayPoints = $tier ? $tier->birthday_bonus_points : 100;
        $multiplier = $tier ? $tier->birthday_bonus_multiplier : 1.0;
        
        $totalPoints = (int) ($birthdayPoints * $multiplier);

        // Award birthday points
        $transaction = $this->awardPoints(
            $userId,
            $totalPoints,
            'Birthday',
            null,
            'Thưởng sinh nhật năm ' . $currentYear,
            ['base_points' => $birthdayPoints, 'multiplier' => $multiplier]
        );

        // Mark as claimed
        $loyaltyAccount->update([
            'birthday_bonus_claimed' => $currentYear
        ]);

        return $transaction;
    }

    /**
     * Expire old points
     */
    public function expireOldPoints()
    {
        $expiredTransactions = PointTransaction::where('type', 'earned')
            ->where('is_expired', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', Carbon::now())
            ->get();

        foreach ($expiredTransactions as $transaction) {
            DB::transaction(function () use ($transaction) {
                $loyaltyAccount = LoyaltyPoint::where('user_id', $transaction->user_id)->first();
                if (!$loyaltyAccount) return;

                // Create expiry transaction
                $expiryTransaction = PointTransaction::create([
                    'user_id' => $transaction->user_id,
                    'type' => 'expired',
                    'points' => $transaction->points,
                    'balance_before' => $loyaltyAccount->current_balance,
                    'balance_after' => $loyaltyAccount->current_balance - $transaction->points,
                    'source_type' => 'Expiry',
                    'source_id' => $transaction->id,
                    'description' => "Hết hạn điểm từ giao dịch #{$transaction->transaction_id}",
                    'status' => 'completed'
                ]);

                // Update balances
                $loyaltyAccount->update([
                    'current_balance' => $loyaltyAccount->current_balance - $transaction->points,
                    'total_expired' => $loyaltyAccount->total_expired + $transaction->points
                ]);

                // Mark original transaction as expired
                $transaction->markAsExpired($expiryTransaction->id);
            });
        }

        return $expiredTransactions->count();
    }

    /**
     * Get loyalty dashboard data
     */
    public function getLoyaltyDashboard($userId)
    {
        $loyaltyAccount = LoyaltyPoint::with(['membershipTier', 'transactions'])
            ->where('user_id', $userId)
            ->first();

        if (!$loyaltyAccount) {
            $loyaltyAccount = $this->initializeLoyaltyAccount($userId);
        }

        $recentTransactions = $loyaltyAccount->transactions()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $expiringPoints = $loyaltyAccount->expiring_points;
        $nextTier = $loyaltyAccount->next_tier;
        $allTiers = MembershipTier::getAllTiersForDisplay();

        return [
            'account' => $loyaltyAccount->status_summary,
            'recent_transactions' => $recentTransactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type_label,
                    'points' => $transaction->formatted_points,
                    'description' => $transaction->description,
                    'source' => $transaction->source_display,
                    'date' => $transaction->created_at->format('d/m/Y H:i'),
                    'icon' => $transaction->type_icon
                ];
            }),
            'expiring_points' => $expiringPoints,
            'next_tier' => $nextTier ? [
                'name' => $nextTier->display_name,
                'points_required' => $loyaltyAccount->points_to_next_tier,
                'benefits' => $nextTier->benefits_summary
            ] : null,
            'all_tiers' => $allTiers
        ];
    }

    /**
     * Get user's tier benefits
     */
    public function getUserTierBenefits($userId)
    {
        $loyaltyAccount = LoyaltyPoint::where('user_id', $userId)->first();
        if (!$loyaltyAccount) {
            return null;
        }

        return $loyaltyAccount->tier_benefits;
    }

    /**
     * Award referral bonus
     */
    public function awardReferralBonus($referrerId, $referredUserId)
    {
        $referrerAccount = LoyaltyPoint::where('user_id', $referrerId)->first();
        if (!$referrerAccount) {
            $referrerAccount = $this->initializeLoyaltyAccount($referrerId);
        }

        $tier = $referrerAccount->membershipTier;
        $referralPoints = $tier ? $tier->referral_bonus_points : 100;

        // Award points to referrer
        $transaction = $this->awardPoints(
            $referrerId,
            $referralPoints,
            'Referral',
            $referredUserId,
            'Thưởng giới thiệu thành viên mới',
            ['referred_user_id' => $referredUserId]
        );

        // Update referral points counter
        $referrerAccount->update([
            'referral_points' => $referrerAccount->referral_points + $referralPoints
        ]);

        return $transaction;
    }
}
