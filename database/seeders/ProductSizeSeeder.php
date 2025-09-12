<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSizeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Lấy tất cả sản phẩm
        $products = Product::all();

        foreach ($products as $product) {
            // Kiểm tra xem sản phẩm đã có sizes chưa
            if ($product->sizes()->count() > 0) {
                continue; // Bỏ qua nếu đã có sizes
            }
            
            // Tạo dữ liệu size cho mỗi sản phẩm
            $sizes = [
                [
                    'size' => 'S',
                    'measurements' => 'Eo 68cm, Mông 88cm',
                    'length' => 'Dài 90cm',
                    'garment_size' => 'Size S',
                    'chest_measurement' => '88cm',
                    'waist_measurement' => '68cm',
                    'hip_measurement' => '88cm',
                    'stock_quantity' => 10,
                    'price_adjustment' => 0,
                ],
                [
                    'size' => 'M',
                    'measurements' => 'Eo 72cm, Mông 92cm',
                    'length' => 'Dài 92cm',
                    'garment_size' => 'Size M',
                    'chest_measurement' => '92cm',
                    'waist_measurement' => '72cm',
                    'hip_measurement' => '92cm',
                    'stock_quantity' => 10,
                    'price_adjustment' => 0,
                ],
                [
                    'size' => 'L',
                    'measurements' => 'Eo 76cm, Mông 96cm',
                    'length' => 'Dài 94cm',
                    'garment_size' => 'Size L',
                    'chest_measurement' => '96cm',
                    'waist_measurement' => '76cm',
                    'hip_measurement' => '96cm',
                    'stock_quantity' => 10,
                    'price_adjustment' => 0,
                ],
                [
                    'size' => 'XL',
                    'measurements' => 'Eo 80cm, Mông 100cm',
                    'length' => 'Dài 96cm',
                    'garment_size' => 'Size XL',
                    'chest_measurement' => '100cm',
                    'waist_measurement' => '80cm',
                    'hip_measurement' => '100cm',
                    'stock_quantity' => 10,
                    'price_adjustment' => 10000, // Tăng giá 10k cho size XL
                ],
                [
                    'size' => 'XXL',
                    'measurements' => 'Eo 84cm, Mông 104cm',
                    'length' => 'Dài 98cm',
                    'garment_size' => 'Size XXL',
                    'chest_measurement' => '104cm',
                    'waist_measurement' => '84cm',
                    'hip_measurement' => '104cm',
                    'stock_quantity' => 10,
                    'price_adjustment' => 20000, // Tăng giá 20k cho size XXL
                ],
            ];

            foreach ($sizes as $sizeData) {
                ProductSize::create([
                    'product_id' => $product->id,
                    ...$sizeData
                ]);
            }
        }
    }
}