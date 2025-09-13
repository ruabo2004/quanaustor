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
        Schema::create('user_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->string('guest_id')->nullable();
            
            // Interaction details
            $table->string('type'); // click, hover, focus, scroll, form_interaction, search
            $table->string('element_type')->nullable(); // button, link, input, select, etc.
            $table->string('element_id')->nullable();
            $table->string('element_class')->nullable();
            $table->text('element_text')->nullable();
            $table->json('element_attributes')->nullable();
            
            // Position & context
            $table->text('page_url');
            $table->string('page_type')->nullable();
            $table->json('click_coordinates')->nullable(); // x, y
            $table->json('element_position')->nullable(); // element bounds
            $table->json('viewport_size')->nullable();
            
            // Interaction context
            $table->string('context')->nullable(); // header, footer, product-card, etc.
            $table->string('section')->nullable(); // hero, features, products, etc.
            $table->integer('sequence_order')->nullable(); // Order in session
            $table->integer('time_to_interact')->nullable(); // ms from page load
            
            // Form interactions
            $table->string('form_id')->nullable();
            $table->string('field_name')->nullable();
            $table->string('field_type')->nullable();
            $table->text('field_value')->nullable(); // Anonymized
            $table->boolean('form_completed')->default(false);
            $table->json('form_errors')->nullable();
            
            // Search interactions
            $table->text('search_query')->nullable();
            $table->integer('search_results_count')->nullable();
            $table->string('search_type')->nullable(); // product, blog, faq
            
            // Ecommerce interactions
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ecommerce_action')->nullable(); // view, add_to_cart, remove, purchase
            $table->decimal('product_price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            
            // Device info
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['element_type', 'type']);
            $table->index(['product_id', 'ecommerce_action']);
            $table->index(['page_type', 'type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interactions');
    }
};
