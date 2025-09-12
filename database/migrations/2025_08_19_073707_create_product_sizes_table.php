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
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('size'); // S, M, L, XL, XXL
            $table->string('measurements')->nullable(); // Số đo cụ thể cho size này
            $table->string('length')->nullable(); // Độ dài cho size này
            $table->string('garment_size')->nullable(); // Kích thước quần áo cho size này
            $table->string('chest_measurement')->nullable(); // Số đo ngực
            $table->string('waist_measurement')->nullable(); // Số đo eo
            $table->string('hip_measurement')->nullable(); // Số đo hông
            $table->integer('stock_quantity')->default(0); // Số lượng tồn kho cho size này
            $table->decimal('price_adjustment', 10, 2)->default(0); // Điều chỉnh giá cho size đặc biệt (nếu có)
            $table->timestamps();
            
            // Đảm bảo không có duplicate product_id + size
            $table->unique(['product_id', 'size']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};