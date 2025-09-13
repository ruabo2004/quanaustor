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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Preferred categories (JSON array of category IDs)
            $table->json('preferred_categories')->nullable();
            
            // Preferred styles, materials, fits
            $table->json('preferred_styles')->nullable();
            $table->json('preferred_materials')->nullable();
            $table->json('preferred_fits')->nullable();
            $table->json('preferred_sizes')->nullable();
            
            // Price preferences
            $table->decimal('preferred_min_price', 10, 2)->nullable();
            $table->decimal('preferred_max_price', 10, 2)->nullable();
            $table->decimal('average_spent', 10, 2)->default(0);
            
            // Behavioral patterns
            $table->integer('total_products_viewed')->default(0);
            $table->integer('total_purchases')->default(0);
            $table->decimal('conversion_rate', 5, 4)->default(0); // Percentage
            $table->time('preferred_browsing_time')->nullable();
            $table->json('device_preferences')->nullable(); // mobile, desktop, tablet
            
            // Recommendation weights (learned from user behavior)
            $table->decimal('price_sensitivity', 3, 2)->default(0.5); // 0-1 scale
            $table->decimal('brand_loyalty', 3, 2)->default(0.5);
            $table->decimal('style_consistency', 3, 2)->default(0.5);
            $table->decimal('impulse_buying', 3, 2)->default(0.5);
            
            // Last updated
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();
            
            // Unique constraint
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
