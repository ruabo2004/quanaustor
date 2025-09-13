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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // Short description
            $table->longText('content');
            $table->string('template')->default('default'); // page template
            
            // SEO fields
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->string('og_title')->nullable(); // Open Graph
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            
            // Featured image
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable(); // Additional images
            
            // Publication status
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            
            // Page settings
            $table->boolean('show_in_menu')->default(false);
            $table->integer('menu_order')->default(0);
            $table->string('parent_page')->nullable(); // For hierarchical pages
            $table->json('custom_fields')->nullable(); // Flexible custom data
            
            // Access control
            $table->boolean('require_auth')->default(false); // Login required
            $table->json('allowed_roles')->nullable(); // Role-based access
            
            // Analytics & tracking
            $table->integer('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            
            // Author information
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['slug', 'is_published']);
            $table->index(['is_published', 'published_at']);
            $table->index(['show_in_menu', 'menu_order']);
            $table->index('view_count');
            $table->fullText(['title', 'content', 'excerpt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
