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
        Schema::table('products', function (Blueprint $table) {
            $table->string('style')->nullable();
            $table->string('fit')->nullable();
            $table->string('material')->nullable();
            $table->string('leg_style')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('style');
            $table->dropColumn('fit');
            $table->dropColumn('material');
            $table->dropColumn('leg_style');
        });
    }
};
