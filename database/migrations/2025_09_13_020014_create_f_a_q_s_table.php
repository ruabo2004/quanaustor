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
        Schema::create('f_a_q_s', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('general'); // general, orders, shipping, returns, payments, products
            $table->json('tags')->nullable(); // For better search
            $table->integer('view_count')->default(0);
            $table->integer('helpful_count')->default(0); // Users found it helpful
            $table->integer('not_helpful_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false); // Show in featured FAQs
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes for search performance
            $table->index(['category', 'is_published']);
            $table->index(['is_featured', 'sort_order']);
            $table->fullText(['question', 'answer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_a_q_s');
    }
};
