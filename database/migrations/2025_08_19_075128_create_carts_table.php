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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size', 10)->default('XL');
            $table->foreignId('product_size_id')->nullable()->constrained('product_sizes')->onDelete('set null');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_adjustment', 10, 2)->default(0);
            $table->timestamps();
            
            // Ensure unique cart items per user/session
            $table->unique(['user_id', 'product_id', 'size']);
            $table->unique(['session_id', 'product_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
