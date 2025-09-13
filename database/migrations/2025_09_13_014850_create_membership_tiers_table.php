<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // bronze, silver, gold, platinum, diamond
            $table->string('display_name'); // "Đồng", "Bạc", "Vàng", "Bạch Kim", "Kim Cương"
            $table->string('color')->default('#CD7F32'); // Color code for UI
            $table->string('icon')->nullable(); // Icon class or image URL
            
            // Requirements
            $table->integer('min_points_required')->default(0); // Minimum points to achieve
            $table->decimal('min_annual_spending', 12, 2)->default(0); // Minimum annual spending
            $table->integer('min_orders_per_year')->default(0); // Minimum orders per year
            
            // Benefits - Point earning rates
            $table->decimal('points_per_vnd', 8, 4)->default(0.0001); // Points per VND spent (1 point per 10,000 VND)
            $table->decimal('birthday_bonus_multiplier', 3, 2)->default(1.0); // Birthday bonus multiplier
            $table->decimal('special_event_multiplier', 3, 2)->default(1.0); // Special events multiplier
            
            // Benefits - Discounts
            $table->integer('discount_percentage')->default(0); // Base discount percentage
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Free shipping threshold
            $table->boolean('priority_support')->default(false); // Priority customer support
            $table->boolean('early_access')->default(false); // Early access to sales
            
            // Benefits - Special perks
            $table->integer('birthday_bonus_points')->default(0); // Birthday bonus points
            $table->integer('annual_bonus_points')->default(0); // Annual bonus points
            $table->integer('referral_bonus_points')->default(100); // Points for referring friends
            $table->json('exclusive_rewards')->nullable(); // Tier-exclusive rewards
            
            // Validity
            $table->integer('tier_validity_months')->default(12); // How long tier lasts
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For ordering tiers
            
            $table->timestamps();
            
            // Indexes
            $table->index(['min_points_required', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
