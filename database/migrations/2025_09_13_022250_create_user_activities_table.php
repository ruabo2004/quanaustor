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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->string('guest_id')->nullable();
            
            // Activity details
            $table->string('action'); // page_view, click, scroll, form_submit, purchase, etc.
            $table->string('category')->nullable(); // ecommerce, navigation, engagement
            $table->string('label')->nullable(); // Button name, product name, etc.
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Flexible data storage
            
            // Page/URL info
            $table->text('url');
            $table->string('page_title')->nullable();
            $table->string('page_type')->nullable(); // home, product, category, etc.
            $table->text('referrer_url')->nullable();
            
            // Target info (for clicks, etc.)
            $table->string('target_type')->nullable(); // button, link, product, etc.
            $table->string('target_id')->nullable(); // Element ID or product ID
            $table->string('target_text')->nullable(); // Button text, link text
            $table->json('target_attributes')->nullable();
            
            // Ecommerce specific
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('value', 10, 2)->nullable(); // Monetary value
            $table->string('currency', 3)->default('VND');
            
            // Technical info
            $table->json('coordinates')->nullable(); // x, y for clicks
            $table->json('viewport')->nullable(); // Screen dimensions
            $table->integer('scroll_depth')->nullable(); // Percentage scrolled
            $table->integer('time_on_page')->nullable(); // Seconds
            
            // Device & browser
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['category', 'action']);
            $table->index(['product_id', 'action']);
            $table->index(['page_type', 'created_at']);
            $table->index('created_at'); // For time-based queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
