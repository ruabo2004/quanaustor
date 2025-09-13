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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // Auto-generated unique number
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_email')->nullable(); // For non-registered users
            $table->string('guest_name')->nullable();
            
            // Ticket details
            $table->string('subject');
            $table->text('description');
            $table->enum('category', ['general', 'orders', 'shipping', 'returns', 'payments', 'products', 'technical', 'account'])->default('general');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])->default('open');
            
            // Assignment and tracking
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable(); // Future: departments
            $table->json('tags')->nullable();
            $table->json('attachments')->nullable(); // File attachments
            
            // Related order (if applicable)
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            
            // Satisfaction and feedback
            $table->integer('satisfaction_rating')->nullable(); // 1-5 stars
            $table->text('satisfaction_feedback')->nullable();
            
            // Timestamps
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['category', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
