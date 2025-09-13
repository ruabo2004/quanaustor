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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // facebook, google, apple, twitter, github
            $table->string('provider_id'); // Social platform user ID
            $table->string('provider_username')->nullable();
            $table->string('provider_email')->nullable();
            $table->string('provider_avatar')->nullable();
            $table->json('provider_data')->nullable(); // Raw provider response
            
            // Access tokens (encrypted)
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            
            // Account info
            $table->string('nickname')->nullable();
            $table->json('profile_data')->nullable(); // Additional profile info
            $table->boolean('is_primary')->default(false); // Primary login method
            $table->boolean('is_active')->default(true);
            
            // Usage tracking
            $table->timestamp('last_login_at')->nullable();
            $table->integer('login_count')->default(0);
            $table->json('login_history')->nullable(); // Recent login timestamps
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['provider', 'provider_id']);
            $table->index(['user_id', 'provider']);
            $table->index(['provider', 'is_active']);
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
