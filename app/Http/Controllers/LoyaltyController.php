<?php

namespace App\Http\Controllers;

use App\Services\LoyaltyService;
use App\Models\LoyaltyPoint;
use App\Models\MembershipTier;
use App\Models\PointTransaction;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoyaltyController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display loyalty dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $dashboard = $this->loyaltyService->getLoyaltyDashboard($user->id);
        
        return view('loyalty.dashboard', compact('dashboard'));
    }

    /**
     * Get loyalty data via API
     */
    public function getLoyaltyData()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $dashboard = $this->loyaltyService->getLoyaltyDashboard(Auth::id());
        
        return response()->json([
            'success' => true,
            'data' => $dashboard
        ]);
    }

    /**
     * Get point transactions history
     */
    public function getTransactions(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $perPage = $request->input('per_page', 15);
        $type = $request->input('type'); // earned, spent, expired, etc.

        $query = PointTransaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($type) {
            $query->where('type', $type);
        }

        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'transactions' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'type' => $transaction->type,
                    'type_label' => $transaction->type_label,
                    'points' => $transaction->points,
                    'formatted_points' => $transaction->formatted_points,
                    'description' => $transaction->description,
                    'source' => $transaction->source_display,
                    'date' => $transaction->created_at->format('d/m/Y H:i'),
                    'icon' => $transaction->type_icon,
                    'balance_after' => $transaction->balance_after,
                    'expires_at' => $transaction->expires_at ? $transaction->expires_at->format('d/m/Y') : null,
                    'is_expiring_soon' => $transaction->isExpiringSoon()
                ];
            }),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total()
            ]
        ]);
    }

    /**
     * Get membership tiers info
     */
    public function getTiers()
    {
        $tiers = MembershipTier::getAllTiersForDisplay();
        
        return response()->json([
            'success' => true,
            'tiers' => $tiers
        ]);
    }

    /**
     * Get user's tier benefits
     */
    public function getTierBenefits()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $benefits = $this->loyaltyService->getUserTierBenefits(Auth::id());
        
        return response()->json([
            'success' => true,
            'benefits' => $benefits
        ]);
    }

    /**
     * Award points manually (Admin only)
     */
    public function awardPoints(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'description' => 'required|string|max:255'
        ]);

        try {
            $transaction = $this->loyaltyService->awardPoints(
                $request->user_id,
                $request->points,
                'Admin',
                Auth::id(),
                $request->description,
                ['admin_user' => Auth::user()->name]
            );

            return response()->json([
                'success' => true,
                'message' => 'Điểm đã được thêm thành công',
                'transaction' => [
                    'id' => $transaction->id,
                    'points' => $transaction->points,
                    'description' => $transaction->description
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Claim birthday bonus
     */
    public function claimBirthdayBonus()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        try {
            $transaction = $this->loyaltyService->awardBirthdayBonus(Auth::id());
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể nhận thưởng sinh nhật. Có thể bạn đã nhận rồi hoặc chưa cập nhật ngày sinh.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Chúc mừng sinh nhật! Bạn đã nhận {$transaction->points} điểm thưởng.",
                'points' => $transaction->points
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get expiring points
     */
    public function getExpiringPoints()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $expiringTransactions = PointTransaction::where('user_id', Auth::id())
            ->expiringSoon(30)
            ->orderBy('expires_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'expiring_points' => $expiringTransactions->map(function ($transaction) {
                return [
                    'points' => $transaction->points,
                    'expires_at' => $transaction->expires_at->format('d/m/Y'),
                    'days_until_expiry' => $transaction->expires_at->diffInDays(now()),
                    'description' => $transaction->description
                ];
            }),
            'total_expiring' => $expiringTransactions->sum('points')
        ]);
    }

    /**
     * Get loyalty statistics for admin
     */
    public function getStatistics()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_members' => LoyaltyPoint::count(),
            'total_points_issued' => LoyaltyPoint::sum('total_earned'),
            'total_points_redeemed' => LoyaltyPoint::sum('total_spent'),
            'active_points' => LoyaltyPoint::sum('current_balance'),
            'tier_distribution' => LoyaltyPoint::select('current_tier', DB::raw('count(*) as count'))
                ->groupBy('current_tier')
                ->get()
                ->pluck('count', 'current_tier'),
            'recent_activities' => PointTransaction::with('user')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'user_name' => $transaction->user->name,
                        'type' => $transaction->type_label,
                        'points' => $transaction->formatted_points,
                        'description' => $transaction->description,
                        'date' => $transaction->created_at->format('d/m/Y H:i')
                    ];
                })
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Get tier progress for widget
     */
    public function getTierProgress()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $loyaltyAccount = LoyaltyPoint::where('user_id', Auth::id())->first();
        if (!$loyaltyAccount) {
            $loyaltyAccount = $this->loyaltyService->initializeLoyaltyAccount(Auth::id());
        }

        $currentTier = $loyaltyAccount->membershipTier;
        $nextTier = $loyaltyAccount->next_tier;

        return response()->json([
            'success' => true,
            'progress' => [
                'current_tier' => [
                    'name' => $currentTier ? $currentTier->display_name : 'Đồng',
                    'color' => $currentTier ? $currentTier->color : '#CD7F32',
                    'icon' => $currentTier ? $currentTier->icon : 'fas fa-medal'
                ],
                'points' => [
                    'current' => $loyaltyAccount->current_balance,
                    'total_earned' => $loyaltyAccount->total_earned
                ],
                'next_tier' => $nextTier ? [
                    'name' => $nextTier->display_name,
                    'points_required' => $loyaltyAccount->points_to_next_tier,
                    'progress_percentage' => $loyaltyAccount->tier_progress_percentage
                ] : null
            ]
        ]);
    }

    /**
     * Redeem reward points
     */
    public function redeemReward(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $request->validate([
            'reward_id' => 'required|exists:rewards,id'
        ]);

        try {
            $reward = Reward::findOrFail($request->reward_id);
            
            // Check if user can redeem
            $loyaltyAccount = LoyaltyPoint::where('user_id', Auth::id())->first();
            if (!$loyaltyAccount || !$loyaltyAccount->canRedeemReward($reward->points_cost)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điểm không đủ để đổi phần thưởng này'
                ], 400);
            }

            // Spend points
            $transaction = $this->loyaltyService->spendPoints(
                Auth::id(),
                $reward->points_cost,
                'Reward',
                $reward->id,
                "Đổi phần thưởng: {$reward->name}",
                ['reward_name' => $reward->name, 'reward_type' => $reward->type]
            );

            // Update reward redemption count
            $reward->increment('redeemed_count');

            return response()->json([
                'success' => true,
                'message' => "Bạn đã đổi thành công {$reward->name}!",
                'transaction' => [
                    'id' => $transaction->id,
                    'points_spent' => $transaction->points,
                    'remaining_balance' => $transaction->balance_after
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
