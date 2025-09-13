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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Session details
            $table->enum('status', ['waiting', 'active', 'ended', 'transferred'])->default('waiting');
            $table->string('subject')->nullable();
            $table->text('initial_message')->nullable();
            $table->json('user_info')->nullable(); // Browser, location, etc.
            
            // Metrics
            $table->timestamp('started_at')->nullable();
            $table->timestamp('agent_joined_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('response_time_avg')->nullable(); // Average response time in seconds
            $table->integer('message_count')->default(0);
            
            // Satisfaction
            $table->integer('satisfaction_rating')->nullable(); // 1-5 stars
            $table->text('satisfaction_feedback')->nullable();
            
            // Queue management
            $table->integer('queue_position')->nullable();
            $table->string('department')->default('general');
            $table->json('tags')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['agent_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
