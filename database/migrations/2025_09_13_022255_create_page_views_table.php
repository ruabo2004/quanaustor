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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->string('guest_id')->nullable();
            
            // Page info
            $table->text('url');
            $table->string('page_title')->nullable();
            $table->string('page_type')->nullable(); // home, product, category, blog, etc.
            $table->json('url_parameters')->nullable(); // Query parameters
            
            // Content tracking
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('blog_post_id')->nullable()->constrained()->onDelete('set null');
            $table->string('category_slug')->nullable();
            
            // Entry/exit tracking
            $table->boolean('is_entry_page')->default(false);
            $table->boolean('is_exit_page')->default(false);
            $table->text('referrer_url')->nullable();
            $table->string('referrer_domain')->nullable();
            
            // Engagement metrics
            $table->integer('time_on_page')->nullable(); // Seconds
            $table->integer('scroll_depth')->nullable(); // Max scroll percentage
            $table->json('scroll_milestones')->nullable(); // 25%, 50%, 75%, 100%
            $table->integer('interactions')->default(0); // Clicks, forms, etc.
            $table->boolean('is_bounce')->default(false);
            
            // Performance metrics
            $table->integer('load_time')->nullable(); // Page load time in ms
            $table->integer('dom_load_time')->nullable();
            $table->integer('first_paint')->nullable();
            $table->integer('largest_contentful_paint')->nullable();
            
            // Device & technical
            $table->json('viewport_size')->nullable(); // width, height
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->json('performance_data')->nullable();
            
            // UTM tracking
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['page_type', 'created_at']);
            $table->index(['product_id', 'created_at']);
            $table->index(['is_entry_page', 'created_at']);
            $table->index(['is_exit_page', 'created_at']);
            $table->index(['utm_source', 'utm_campaign']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
