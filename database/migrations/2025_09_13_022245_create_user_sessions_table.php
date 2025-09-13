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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_id')->nullable(); // For guest tracking
            
            // Session info
            $table->ipAddress('ip_address');
            $table->text('user_agent');
            $table->string('device_type')->nullable(); // mobile, tablet, desktop
            $table->string('browser')->nullable();
            $table->string('platform')->nullable(); // OS
            $table->json('device_info')->nullable(); // Detailed device data
            
            // Location data
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone')->nullable();
            
            // Referrer & utm tracking
            $table->string('referrer_domain')->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            
            // Session metrics
            $table->timestamp('started_at');
            $table->timestamp('last_activity_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('page_views')->default(0);
            $table->integer('interactions')->default(0);
            $table->decimal('bounce_rate', 5, 2)->nullable(); // 0-100%
            
            // Conversion tracking
            $table->boolean('is_converted')->default(false);
            $table->string('conversion_type')->nullable(); // purchase, signup, etc.
            $table->decimal('conversion_value', 10, 2)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'started_at']);
            $table->index(['guest_id', 'started_at']);
            $table->index(['ip_address', 'started_at']);
            $table->index(['device_type', 'started_at']);
            $table->index(['country', 'started_at']);
            $table->index(['utm_source', 'utm_campaign']);
            $table->index(['is_converted', 'conversion_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
