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
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#6c757d'); // Tag color
            
            // Usage stats
            $table->integer('post_count')->default(0);
            $table->integer('usage_count')->default(0); // Total times used
            $table->timestamp('last_used_at')->nullable();
            
            // Settings
            $table->boolean('is_featured')->default(false); // Show in tag cloud
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['slug', 'is_active']);
            $table->index(['post_count', 'is_featured']);
            $table->index('usage_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_tags');
    }
};
