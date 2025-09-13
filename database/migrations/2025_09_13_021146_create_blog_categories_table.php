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
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#007bff'); // Category color
            $table->string('icon')->nullable(); // Font Awesome icon
            $table->string('image')->nullable(); // Category image
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Hierarchy
            $table->foreignId('parent_id')->nullable()->constrained('blog_categories')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            
            // Settings
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_menu')->default(true);
            $table->integer('post_count')->default(0); // Cache post count
            
            $table->timestamps();
            
            // Indexes
            $table->index(['slug', 'is_active']);
            $table->index(['parent_id', 'sort_order']);
            $table->index('post_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
