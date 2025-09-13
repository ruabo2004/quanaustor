<?php

namespace Database\Seeders;

use App\Models\MembershipTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'bronze',
                'display_name' => 'Đồng',
                'color' => '#CD7F32',
                'icon' => 'fas fa-medal',
                'min_points_required' => 0,
                'min_annual_spending' => 0,
                'min_orders_per_year' => 0,
                'points_per_vnd' => 0.0001, // 1 điểm cho 10,000 VND
                'birthday_bonus_multiplier' => 1.0,
                'special_event_multiplier' => 1.0,
                'discount_percentage' => 0,
                'free_shipping_threshold' => null,
                'priority_support' => false,
                'early_access' => false,
                'birthday_bonus_points' => 100,
                'annual_bonus_points' => 0,
                'referral_bonus_points' => 100,
                'exclusive_rewards' => null,
                'tier_validity_months' => 12,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'silver',
                'display_name' => 'Bạc',
                'color' => '#C0C0C0',
                'icon' => 'fas fa-medal',
                'min_points_required' => 1000,
                'min_annual_spending' => 5000000, // 5 triệu VND
                'min_orders_per_year' => 3,
                'points_per_vnd' => 0.00012, // 1.2 điểm cho 10,000 VND
                'birthday_bonus_multiplier' => 1.5,
                'special_event_multiplier' => 1.2,
                'discount_percentage' => 5,
                'free_shipping_threshold' => 800000, // 800k VND
                'priority_support' => false,
                'early_access' => false,
                'birthday_bonus_points' => 200,
                'annual_bonus_points' => 500,
                'referral_bonus_points' => 150,
                'exclusive_rewards' => json_encode(['silver_exclusive_coupon']),
                'tier_validity_months' => 12,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'gold',
                'display_name' => 'Vàng',
                'color' => '#FFD700',
                'icon' => 'fas fa-crown',
                'min_points_required' => 3000,
                'min_annual_spending' => 15000000, // 15 triệu VND
                'min_orders_per_year' => 6,
                'points_per_vnd' => 0.00015, // 1.5 điểm cho 10,000 VND
                'birthday_bonus_multiplier' => 2.0,
                'special_event_multiplier' => 1.5,
                'discount_percentage' => 10,
                'free_shipping_threshold' => 500000, // 500k VND
                'priority_support' => true,
                'early_access' => true,
                'birthday_bonus_points' => 500,
                'annual_bonus_points' => 1000,
                'referral_bonus_points' => 200,
                'exclusive_rewards' => json_encode(['gold_exclusive_gift', 'premium_packaging']),
                'tier_validity_months' => 12,
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'platinum',
                'display_name' => 'Bạch Kim',
                'color' => '#E5E4E2',
                'icon' => 'fas fa-gem',
                'min_points_required' => 8000,
                'min_annual_spending' => 30000000, // 30 triệu VND
                'min_orders_per_year' => 10,
                'points_per_vnd' => 0.0002, // 2 điểm cho 10,000 VND
                'birthday_bonus_multiplier' => 3.0,
                'special_event_multiplier' => 2.0,
                'discount_percentage' => 15,
                'free_shipping_threshold' => 0, // Free shipping always
                'priority_support' => true,
                'early_access' => true,
                'birthday_bonus_points' => 1000,
                'annual_bonus_points' => 2000,
                'referral_bonus_points' => 300,
                'exclusive_rewards' => json_encode(['platinum_vip_service', 'personal_stylist', 'exclusive_collections']),
                'tier_validity_months' => 12,
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'diamond',
                'display_name' => 'Kim Cương',
                'color' => '#B9F2FF',
                'icon' => 'fas fa-diamond',
                'min_points_required' => 20000,
                'min_annual_spending' => 60000000, // 60 triệu VND
                'min_orders_per_year' => 15,
                'points_per_vnd' => 0.00025, // 2.5 điểm cho 10,000 VND
                'birthday_bonus_multiplier' => 5.0,
                'special_event_multiplier' => 3.0,
                'discount_percentage' => 20,
                'free_shipping_threshold' => 0, // Free shipping always
                'priority_support' => true,
                'early_access' => true,
                'birthday_bonus_points' => 2000,
                'annual_bonus_points' => 5000,
                'referral_bonus_points' => 500,
                'exclusive_rewards' => json_encode([
                    'diamond_concierge_service', 
                    'private_shopping_events', 
                    'custom_tailoring',
                    'luxury_packaging',
                    'priority_alterations'
                ]),
                'tier_validity_months' => 12,
                'is_active' => true,
                'sort_order' => 5
            ]
        ];

        foreach ($tiers as $tier) {
            MembershipTier::create($tier);
        }
    }
}
