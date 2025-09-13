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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt'); // Short summary
            $table->longText('content');
            
            // Featured media
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            
            // Categorization
            $table->unsignedBigInteger('category_id')->nullable();
            $table->json('tags')->nullable(); // Array of tag IDs
            
            // SEO optimization
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            
            // Open Graph & Social
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_card')->default('summary_large_image');
            
            // Publication settings
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            
            // Content settings
            $table->integer('reading_time')->nullable(); // Minutes
            $table->string('language')->default('vi'); // vi, en, etc.
            $table->json('related_posts')->nullable(); // Manual related posts
            
            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->decimal('avg_rating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->timestamp('last_viewed_at')->nullable();
            
            // Author info
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'published_at']);
            $table->index(['category_id', 'status']);
            $table->index(['is_featured', 'published_at']);
            $table->index(['author_id', 'status']);
            $table->index(['view_count', 'published_at']);
            $table->index('slug');
            $table->fullText(['title', 'content', 'excerpt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
