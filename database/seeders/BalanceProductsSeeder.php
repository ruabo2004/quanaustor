<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BalanceProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các danh mục
        $quanAuNamCategory = Category::where('name', 'Quần âu nam')->first();
        $quanAuNuCategory = Category::where('name', 'Quần âu nữ')->first();
        $congSoCategory = Category::where('name', 'Quần âu công sở')->first();
        $duTiecCategory = Category::where('name', 'Quần âu dự tiệc')->first();
        $casualCategory = Category::where('name', 'Quần âu casual')->first();

        // Thêm sản phẩm cho Quần âu nam (cần 4 sản phẩm thêm để có 6)
        $newProductsNam = [
            [
                'name' => 'Quần âu nam đen classic',
                'description' => 'Quần âu nam màu đen cổ điển, thiết kế thanh lịch, phù hợp cho mọi dịp từ công sở đến dự tiệc.',
                'price' => 750000,
                'image' => 'storage/products/quan_au_nam_den_classic.jpg',
                'category_id' => $quanAuNamCategory->id,
                'measurements' => 'Eo 79cm, Mông 97cm',
                'length' => 'Dài 99cm',
                'garment_size' => 'Size M',
                'style' => 'Classic, Thanh lịch',
                'fit' => 'Regular fit',
                'material' => 'Vải Wool blend',
                'leg_style' => 'Ống đứng',
            ],
            [
                'name' => 'Quần âu nam nâu tây phong cách',
                'description' => 'Quần âu nam màu nâu tây sang trọng, phong cách hiện đại, chất liệu cao cấp thoáng mát.',
                'price' => 780000,
                'image' => 'storage/products/quan_au_nam_nau_tay.jpg',
                'category_id' => $quanAuNamCategory->id,
                'measurements' => 'Eo 77cm, Mông 95cm',
                'length' => 'Dài 97cm',
                'garment_size' => 'Size M',
                'style' => 'Phong cách tây',
                'fit' => 'Slim fit',
                'material' => 'Vải Cotton Premium',
                'leg_style' => 'Ống côn',
            ],
            [
                'name' => 'Quần âu nam xanh petrol thời trang',
                'description' => 'Quần âu nam màu xanh petrol độc đáo, thiết kế thời trang trẻ trung, dễ phối đồ.',
                'price' => 720000,
                'image' => 'storage/products/quan_au_nam_xanh_petrol.jpg',
                'category_id' => $quanAuNamCategory->id,
                'measurements' => 'Eo 76cm, Mông 94cm',
                'length' => 'Dài 95cm',
                'garment_size' => 'Size S',
                'style' => 'Thời trang trẻ',
                'fit' => 'Slim fit',
                'material' => 'Vải Cotton Stretch',
                'leg_style' => 'Ống côn nhẹ',
            ],
            [
                'name' => 'Quần âu nam kẻ sọc tinh tế',
                'description' => 'Quần âu nam họa tiết kẻ sọc tinh tế, phong cách doanh nhân thành đạt, chất liệu không nhăn.',
                'price' => 820000,
                'image' => 'storage/products/quan_au_nam_ke_soc.jpg',
                'category_id' => $quanAuNamCategory->id,
                'measurements' => 'Eo 81cm, Mông 99cm',
                'length' => 'Dài 101cm',
                'garment_size' => 'Size L',
                'style' => 'Doanh nhân',
                'fit' => 'Regular fit',
                'material' => 'Vải Polyester kẻ sọc',
                'leg_style' => 'Ống thẳng',
            ],
        ];

        // Thêm sản phẩm cho Quần âu nữ (cần 3 sản phẩm thêm để có 6)
        $newProductsNu = [
            [
                'name' => 'Quần âu nữ trắng elegant',
                'description' => 'Quần âu nữ màu trắng tinh khôi, thiết kế elegant sang trọng, hoàn hảo cho nữ công sở hiện đại.',
                'price' => 690000,
                'image' => 'storage/products/quan_au_nu_trang_elegant.jpg',
                'category_id' => $quanAuNuCategory->id,
                'measurements' => 'Eo 67cm, Mông 91cm',
                'length' => 'Dài 92cm',
                'garment_size' => 'Size S',
                'style' => 'Elegant, Sang trọng',
                'fit' => 'Straight fit',
                'material' => 'Vải Crepe cao cấp',
                'leg_style' => 'Ống đứng',
            ],
            [
                'name' => 'Quần âu nữ xám khói hiện đại',
                'description' => 'Quần âu nữ màu xám khói, thiết kế hiện đại với đường cắt tinh tế, tôn dáng người mặc.',
                'price' => 720000,
                'image' => 'storage/products/quan_au_nu_xam_khoi.jpg',
                'category_id' => $quanAuNuCategory->id,
                'measurements' => 'Eo 69cm, Mông 93cm',
                'length' => 'Dài 94cm',
                'garment_size' => 'Size M',
                'style' => 'Hiện đại, Tôn dáng',
                'fit' => 'Slim fit',
                'material' => 'Vải Ponte',
                'leg_style' => 'Ống côn',
            ],
            [
                'name' => 'Quần âu nữ burgundy quyến rũ',
                'description' => 'Quần âu nữ màu burgundy quyến rũ, phong cách vintage kết hợp hiện đại, thích hợp cho các buổi tối.',
                'price' => 750000,
                'image' => 'storage/products/quan_au_nu_burgundy.jpg',
                'category_id' => $quanAuNuCategory->id,
                'measurements' => 'Eo 71cm, Mông 95cm',
                'length' => 'Dài 96cm',
                'garment_size' => 'Size M',
                'style' => 'Vintage, Quyến rũ',
                'fit' => 'High waist',
                'material' => 'Vải Wool blend',
                'leg_style' => 'Ống suông',
            ],
        ];

        // Thêm 1 sản phẩm cho mỗi danh mục còn lại để có 6 sản phẩm
        $additionalProducts = [
            // Quần âu công sở
            [
                'name' => 'Quần âu công sở navy blue professional',
                'description' => 'Quần âu công sở navy blue, thiết kế professional chuẩn mực, chất liệu cao cấp không phai màu.',
                'price' => 830000,
                'image' => 'storage/products/quan_au_cong_so_navy_pro.jpg',
                'category_id' => $congSoCategory->id,
                'measurements' => 'Eo 78cm, Mông 96cm',
                'length' => 'Dài 98cm',
                'garment_size' => 'Size M',
                'style' => 'Professional',
                'fit' => 'Regular fit',
                'material' => 'Vải Wool Professional',
                'leg_style' => 'Ống đứng',
            ],

            // Quần âu dự tiệc
            [
                'name' => 'Quần âu dự tiệc bạc platinum sang trọng',
                'description' => 'Quần âu dự tiệc màu bạc platinum, thiết kế sang trọng với ánh kim nhẹ, hoàn hảo cho các dịp đặc biệt.',
                'price' => 960000,
                'image' => 'storage/products/quan_au_du_tiec_bac_platinum.jpg',
                'category_id' => $duTiecCategory->id,
                'measurements' => 'Eo 76cm, Mông 94cm',
                'length' => 'Dài 96cm',
                'garment_size' => 'Size M',
                'style' => 'Luxury party',
                'fit' => 'Slim fit',
                'material' => 'Vải Metallic Platinum',
                'leg_style' => 'Ống côn',
            ],

            // Quần âu casual
            [
                'name' => 'Quần âu casual mint green tươi mát',
                'description' => 'Quần âu casual màu mint green tươi mát, phong cách trẻ trung năng động, thích hợp cho mùa hè.',
                'price' => 610000,
                'image' => 'storage/products/quan_au_casual_mint_green.jpg',
                'category_id' => $casualCategory->id,
                'measurements' => 'Eo 75cm, Mông 93cm',
                'length' => 'Dài 91cm',
                'garment_size' => 'Size M',
                'style' => 'Summer casual',
                'fit' => 'Relaxed fit',
                'material' => 'Vải Cotton Linen',
                'leg_style' => 'Ống rộng nhẹ',
            ],
        ];

        // Tạo sản phẩm cho Quần âu nam
        foreach ($newProductsNam as $product) {
            Product::create($product);
        }

        // Tạo sản phẩm cho Quần âu nữ
        foreach ($newProductsNu as $product) {
            Product::create($product);
        }

        // Tạo sản phẩm bổ sung
        foreach ($additionalProducts as $product) {
            Product::create($product);
        }

        echo "✅ Đã thêm sản phẩm để cân bằng các danh mục!\n";
    }
}










