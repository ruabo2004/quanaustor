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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Point balances
            $table->integer('total_earned')->default(0); // Total points ever earned
            $table->integer('current_balance')->default(0); // Available points
            $table->integer('total_spent')->default(0); // Total points spent
            $table->integer('total_expired')->default(0); // Total points expired
            
            // Membership tier info
            $table->string('current_tier')->default('bronze'); // bronze, silver, gold, platinum, diamond
            $table->integer('tier_progress')->default(0); // Points towards next tier
            $table->integer('points_to_next_tier')->default(1000); // Points needed for next tier
            $table->date('tier_expiry_date')->nullable(); // When tier expires
            
            // Annual stats (resets yearly)
            $table->integer('annual_points_earned')->default(0);
            $table->decimal('annual_spending', 12, 2)->default(0);
            $table->integer('annual_orders')->default(0);
            $table->date('year_start_date')->default(now()->startOfYear());
            
            // Special achievements
            $table->json('achievements')->nullable(); // Special milestones
            $table->integer('referral_points')->default(0); // Points from referrals
            $table->integer('birthday_bonus_claimed')->default(0); // Birthday bonus status
            $table->date('last_activity_date')->nullable(); // Last earning activity
            
            $table->timestamps();
            
            // Indexes
            $table->unique('user_id');
            $table->index(['current_tier', 'current_balance']);
            $table->index('last_activity_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
