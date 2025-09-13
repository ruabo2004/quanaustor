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
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->enum('type', ['reply', 'note', 'status_change'])->default('reply');
            $table->boolean('is_internal')->default(false); // Internal notes vs customer-visible replies
            $table->json('metadata')->nullable(); // Status changes, etc.
            $table->timestamp('read_at')->nullable(); // When customer read the reply
            $table->timestamps();
            
            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_internal', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
    }
};
