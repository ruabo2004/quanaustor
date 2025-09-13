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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // Unique transaction identifier
            
            // Transaction details
            $table->enum('type', ['earned', 'spent', 'expired', 'adjusted', 'bonus', 'refund']); // Transaction type
            $table->integer('points')->default(0); // Points involved (positive for earn, negative for spend)
            $table->integer('balance_before')->default(0); // Balance before transaction
            $table->integer('balance_after')->default(0); // Balance after transaction
            
            // Source information
            $table->string('source_type'); // Order, Referral, Birthday, Admin, etc.
            $table->string('source_id')->nullable(); // Related order ID, referral ID, etc.
            $table->text('description'); // Human readable description
            $table->json('metadata')->nullable(); // Additional data
            
            // Order-related (when source is order)
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('order_total', 12, 2)->nullable(); // Order total when points earned
            $table->string('order_status')->nullable(); // Order status when points earned/spent
            
            // Expiry information (for earned points)
            $table->date('expires_at')->nullable(); // When these points expire
            $table->boolean('is_expired')->default(false); // Whether points have expired
            $table->foreignId('expired_by_transaction_id')->nullable(); // Transaction that expired these points
            
            // Administrative
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->onDelete('set null'); // Admin who made adjustment
            $table->text('admin_notes')->nullable(); // Admin notes for adjustments
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['expires_at', 'is_expired']);
            $table->index(['source_type', 'source_id']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
