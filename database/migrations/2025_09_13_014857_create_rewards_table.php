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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Reward name
            $table->text('description'); // Reward description
            $table->string('type'); // discount, coupon, free_shipping, gift, cash_back
            $table->string('image')->nullable(); // Reward image
            
            // Cost and availability
            $table->integer('points_cost'); // Points required to redeem
            $table->integer('stock_quantity')->nullable(); // Available quantity (null = unlimited)
            $table->integer('redeemed_count')->default(0); // Times redeemed
            $table->integer('max_redemptions_per_user')->nullable(); // Max per user (null = unlimited)
            
            // Discount details (when type is discount/coupon)
            $table->decimal('discount_amount', 10, 2)->nullable(); // Fixed discount amount
            $table->integer('discount_percentage')->nullable(); // Percentage discount
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Minimum order for discount
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Maximum discount cap
            
            // Coupon details
            $table->string('coupon_code')->nullable(); // Auto-generated coupon code
            $table->integer('coupon_usage_limit')->nullable(); // How many times coupon can be used
            $table->date('coupon_valid_from')->nullable(); // Coupon valid from date
            $table->date('coupon_valid_until')->nullable(); // Coupon expiry date
            
            // Tier restrictions
            $table->json('allowed_tiers')->nullable(); // Which tiers can redeem this reward
            $table->string('min_tier_required')->nullable(); // Minimum tier required
            
            // Availability
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Featured in rewards page
            $table->date('available_from')->nullable(); // Available from date
            $table->date('available_until')->nullable(); // Available until date
            
            // Categories and tags
            $table->string('category')->default('general'); // general, seasonal, tier_exclusive, etc.
            $table->json('tags')->nullable(); // Tags for filtering
            $table->integer('sort_order')->default(0); // Display order
            
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active', 'available_from', 'available_until']);
            $table->index(['points_cost', 'is_active']);
            $table->index(['category', 'is_featured']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
