<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quanAuNamCategory = Category::where('name', 'Quần âu nam')->first();
        $quanAuNuCategory = Category::where('name', 'Quần âu nữ')->first();

        // Quần âu nam products
        Product::create([
            'name' => 'Quần âu nam công sở đen (Dáng Slimfit)',
            'description' => 'Quần âu nam màu đen, dáng slimfit hiện đại, chất liệu vải cao cấp, không nhăn, giữ form tốt, phù hợp đi làm và dự tiệc.',
            'price' => 720000,
            'image' => 'https://via.placeholder.com/150/0000FF/FFFFFF?text=Quan+Au+Nam+Den',
            'category_id' => $quanAuNamCategory->id,
            'measurements' => 'Eo 78cm, Mông 96cm',
            'length' => 'Dài 98cm',
            'garment_size' => 'Size M',
            'style' => 'Công sở, Slimfit',
            'fit' => 'Slimfit',
            'material' => 'Vải Polyester pha Spandex',
            'leg_style' => 'Ống côn',
        ]);

        Product::create([
            'name' => 'Quần âu nam xanh navy (Dáng Đứng)',
            'description' => 'Quần âu nam màu xanh navy, dáng đứng cổ điển, vải co giãn nhẹ, mang lại sự thoải mái và lịch lãm.',
            'price' => 700000,
            'image' => 'https://via.placeholder.com/150/000080/FFFFFF?text=Quan+Au+Nam+Navy',
            'category_id' => $quanAuNamCategory->id,
            'measurements' => 'Eo 80cm, Mông 98cm',
            'length' => 'Dài 100cm',
            'garment_size' => 'Size L',
            'style' => 'Công sở, Cổ điển',
            'fit' => 'Dáng đứng',
            'material' => 'Vải Cotton pha',
            'leg_style' => 'Ống suông',
        ]);

        Product::create([
            'name' => 'Quần âu nam ghi sáng (Trẻ Trung)',
            'description' => 'Quần âu nam màu ghi sáng, thiết kế trẻ trung, dễ phối đồ, chất liệu bền đẹp, thoáng mát.',
            'price' => 680000,
            'image' => 'https://via.placeholder.com/150/A9A9A9/FFFFFF?text=Quan+Au+Nam+Ghi',
            'category_id' => $quanAuNamCategory->id,
            'measurements' => 'Eo 76cm, Mông 94cm',
            'length' => 'Dài 96cm',
            'garment_size' => 'Size S',
            'style' => 'Trẻ trung, Casual',
            'fit' => 'Regular fit',
            'material' => 'Vải Cotton pha Linen',
            'leg_style' => 'Ống đứng',
        ]);

        // Quần âu nữ products
        Product::create([
            'name' => 'Quần âu nữ công sở đen (Dáng Suông)',
            'description' => 'Quần âu nữ màu đen, dáng suông thanh lịch, chất liệu vải mềm mại, co giãn, phù hợp cho môi trường công sở.',
            'price' => 650000,
            'image' => 'https://via.placeholder.com/150/000000/FFFFFF?text=Quan+Au+Nu+Den',
            'category_id' => $quanAuNuCategory->id,
            'measurements' => 'Eo 68cm, Mông 92cm',
            'length' => 'Dài 95cm',
            'garment_size' => 'Size S',
            'style' => 'Công sở, Thanh lịch',
            'fit' => 'Dáng suông',
            'material' => 'Vải Tuyết mưa',
            'leg_style' => 'Ống đứng',
        ]);

        Product::create([
            'name' => 'Quần âu nữ ống rộng (Thời Trang)',
            'description' => 'Quần âu nữ ống rộng, phong cách thời trang, che khuyết điểm tốt, chất liệu thoáng mát, thích hợp đi chơi và dạo phố.',
            'price' => 690000,
            'image' => 'https://via.placeholder.com/150/FFC0CB/000000?text=Quan+Au+Nu+Ong+Rong',
            'category_id' => $quanAuNuCategory->id,
            'measurements' => 'Eo 70cm, Mông 94cm',
            'length' => 'Dài 100cm',
            'garment_size' => 'Size M',
            'style' => 'Thời trang, Ống rộng',
            'fit' => 'Ống rộng',
            'material' => 'Vải Đũi',
            'leg_style' => 'Ống rộng',
        ]);

        Product::create([
            'name' => 'Quần âu nữ lưng cao (Tôn Dáng)',
            'description' => 'Quần âu nữ lưng cao, giúp tôn dáng, chất liệu vải cao cấp, không nhăn, dễ dàng phối hợp với áo kiểu.',
            'price' => 710000,
            'image' => 'https://via.placeholder.com/150/800080/FFFFFF?text=Quan+Au+Nu+Lung+Cao',
            'category_id' => $quanAuNuCategory->id,
            'measurements' => 'Eo 66cm, Mông 90cm',
            'length' => 'Dài 97cm',
            'garment_size' => 'Size S',
            'style' => 'Lưng cao, Tôn dáng',
            'fit' => 'Regular fit',
            'material' => 'Vải Cotton lạnh',
            'leg_style' => 'Ống đứng',
        ]);
    }
}
