<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $congSoCategory = Category::where('name', 'Quần âu công sở')->first();
        $duTiecCategory = Category::where('name', 'Quần âu dự tiệc')->first();
        $casualCategory = Category::where('name', 'Quần âu casual')->first();

        // Sản phẩm Quần âu công sở
        $officeProducts = [
            [
                'name' => 'Quần âu công sở nam đen Premium',
                'description' => 'Quần âu công sở nam màu đen cao cấp, chất liệu vải thịnh hành, dáng slimfit sang trọng, chống nhăn, thích hợp cho môi trường văn phòng chuyên nghiệp.',
                'price' => 850000,
                'image' => 'storage/products/quan_au_cong_so_den_nam.jpg',
                'category_id' => $congSoCategory->id,
                'measurements' => 'Eo 78cm, Mông 96cm',
                'length' => 'Dài 98cm',
                'garment_size' => 'Size M',
                'style' => 'Công sở chuyên nghiệp',
                'fit' => 'Slimfit',
                'material' => 'Vải Wool pha Polyester',
                'leg_style' => 'Ống côn',
            ],
            [
                'name' => 'Quần âu công sở nữ xanh navy thanh lịch',
                'description' => 'Quần âu nữ xanh navy dành cho công sở, thiết kế thanh lịch, dáng straight fit, chất liệu co giãn nhẹ, tạo cảm giác thoải mái suốt ngày dài làm việc.',
                'price' => 780000,
                'image' => 'storage/products/quan_au_cong_so_navy_nu.jpg',
                'category_id' => $congSoCategory->id,
                'measurements' => 'Eo 70cm, Mông 94cm',
                'length' => 'Dài 95cm',
                'garment_size' => 'Size S',
                'style' => 'Công sở thanh lịch',
                'fit' => 'Straight fit',
                'material' => 'Vải Polyester co giãn',
                'leg_style' => 'Ống đứng',
            ],
            [
                'name' => 'Quần âu công sở nam xám khói hiện đại',
                'description' => 'Quần âu nam màu xám khói, kiểu dáng hiện đại, chất liệu cao cấp không nhăn, phù hợp cho các cuộc họp quan trọng và thuyết trình.',
                'price' => 820000,
                'image' => 'storage/products/quan_au_cong_so_xam_nam.jpg',
                'category_id' => $congSoCategory->id,
                'measurements' => 'Eo 80cm, Mông 98cm',
                'length' => 'Dài 100cm',
                'garment_size' => 'Size L',
                'style' => 'Công sở hiện đại',
                'fit' => 'Regular fit',
                'material' => 'Vải Cotton lạnh cao cấp',
                'leg_style' => 'Ống thẳng',
            ],
            [
                'name' => 'Quần âu công sở nữ be sang trọng',
                'description' => 'Quần âu nữ màu be nhẹ nhàng, thiết kế sang trọng với đường may tinh tế, chất liệu mềm mại, thoáng mát, thích hợp cho nữ giám đốc và nhân viên văn phòng.',
                'price' => 790000,
                'image' => 'storage/products/quan_au_cong_so_be_nu.jpg',
                'category_id' => $congSoCategory->id,
                'measurements' => 'Eo 68cm, Mông 92cm',
                'length' => 'Dài 93cm',
                'garment_size' => 'Size S',
                'style' => 'Công sở sang trọng',
                'fit' => 'Slim fit',
                'material' => 'Vải Linen pha Cotton',
                'leg_style' => 'Ống côn nhẹ',
            ],
            [
                'name' => 'Quần âu công sở unisex kẻ sọc tinh tế',
                'description' => 'Quần âu unisex với họa tiết kẻ sọc tinh tế, phù hợp cho cả nam và nữ, thiết kế thời trang nhưng vẫn giữ được tính chuyên nghiệp cho môi trường công sở.',
                'price' => 860000,
                'image' => 'storage/products/quan_au_cong_so_ke_soc.jpg',
                'category_id' => $congSoCategory->id,
                'measurements' => 'Eo 75cm, Mông 95cm',
                'length' => 'Dài 97cm',
                'garment_size' => 'Size M',
                'style' => 'Công sở thời trang',
                'fit' => 'Regular fit',
                'material' => 'Vải Polyester kẻ sọc',
                'leg_style' => 'Ống đứng',
            ],
        ];

        // Sản phẩm Quần âu dự tiệc
        $partyProducts = [
            [
                'name' => 'Quần âu dự tiệc nam đen lụa sang trọng',
                'description' => 'Quần âu nam dành cho các dịp tiệc tùng, màu đen bóng lụa, chất liệu cao cấp, thiết kế lịch lãm, phù hợp cho các buổi dạ tiệc và sự kiện quan trọng.',
                'price' => 950000,
                'image' => 'storage/products/quan_au_du_tiec_den_nam.jpg',
                'category_id' => $duTiecCategory->id,
                'measurements' => 'Eo 78cm, Mông 96cm',
                'length' => 'Dài 100cm',
                'garment_size' => 'Size M',
                'style' => 'Dự tiệc sang trọng',
                'fit' => 'Tuxedo fit',
                'material' => 'Vải Satin cao cấp',
                'leg_style' => 'Ống côn',
            ],
            [
                'name' => 'Quần âu dự tiệc nữ đỏ rượu vang quyến rũ',
                'description' => 'Quần âu nữ màu đỏ rượu vang, thiết kế quyến rũ và thanh lịch, chất liệu mềm mại, phù hợp cho các buổi tiệc cocktail và gala dinner.',
                'price' => 980000,
                'image' => 'storage/products/quan_au_du_tiec_do_nu.jpg',
                'category_id' => $duTiecCategory->id,
                'measurements' => 'Eo 70cm, Mông 94cm',
                'length' => 'Dài 95cm',
                'garment_size' => 'Size S',
                'style' => 'Dự tiệc quyến rũ',
                'fit' => 'High waist',
                'material' => 'Vải Velvet',
                'leg_style' => 'Ống suông',
            ],
            [
                'name' => 'Quần âu dự tiệc nam xanh đậm hoàng gia',
                'description' => 'Quần âu nam màu xanh đậm phong cách hoàng gia, chất liệu vải thể hiện đẳng cấp, thiết kế cổ điển nhưng hiện đại, thích hợp cho các sự kiện trang trọng.',
                'price' => 920000,
                'image' => 'storage/products/quan_au_du_tiec_xanh_nam.jpg',
                'category_id' => $duTiecCategory->id,
                'measurements' => 'Eo 80cm, Mông 98cm',
                'length' => 'Dài 102cm',
                'garment_size' => 'Size L',
                'style' => 'Dự tiệc hoàng gia',
                'fit' => 'Classic fit',
                'material' => 'Vải Wool premium',
                'leg_style' => 'Ống thẳng',
            ],
            [
                'name' => 'Quần âu dự tiệc nữ vàng gold ánh kim',
                'description' => 'Quần âu nữ màu vàng gold với hiệu ứng ánh kim tinh tế, thiết kế hiện đại, thu hút mọi ánh nhìn, hoàn hảo cho các buổi tiệc năm mới và sinh nhật.',
                'price' => 890000,
                'image' => 'storage/products/quan_au_du_tiec_vang_nu.jpg',
                'category_id' => $duTiecCategory->id,
                'measurements' => 'Eo 68cm, Mông 92cm',
                'length' => 'Dài 93cm',
                'garment_size' => 'Size S',
                'style' => 'Dự tiệc lấp lánh',
                'fit' => 'Skinny fit',
                'material' => 'Vải Sequin gold',
                'leg_style' => 'Ống ôm',
            ],
            [
                'name' => 'Quần âu dự tiệc đen bạc gradient độc đáo',
                'description' => 'Quần âu unisex với hiệu ứng gradient từ đen sang bạc, thiết kế độc đáo và hiện đại, phù hợp cho các bữa tiệc themed và fashion show.',
                'price' => 990000,
                'image' => 'storage/products/quan_au_du_tiec_gradient.jpg',
                'category_id' => $duTiecCategory->id,
                'measurements' => 'Eo 75cm, Mông 95cm',
                'length' => 'Dài 98cm',
                'garment_size' => 'Size M',
                'style' => 'Dự tiệc avant-garde',
                'fit' => 'Designer fit',
                'material' => 'Vải Metallic gradient',
                'leg_style' => 'Ống bootcut',
            ],
        ];

        // Sản phẩm Quần âu casual
        $casualProducts = [
            [
                'name' => 'Quần âu casual nam nâu đất tự nhiên',
                'description' => 'Quần âu nam màu nâu đất, phong cách casual thoải mái, chất liệu cotton tự nhiên, thích hợp cho các buổi dạo phố, cafe và du lịch.',
                'price' => 620000,
                'image' => 'storage/products/quan_au_casual_nau_nam.jpg',
                'category_id' => $casualCategory->id,
                'measurements' => 'Eo 78cm, Mông 96cm',
                'length' => 'Dài 95cm',
                'garment_size' => 'Size M',
                'style' => 'Casual thoải mái',
                'fit' => 'Relaxed fit',
                'material' => 'Cotton organic',
                'leg_style' => 'Ống rộng vừa',
            ],
            [
                'name' => 'Quần âu casual nữ trắng kem thanh lịch',
                'description' => 'Quần âu nữ màu trắng kem, thiết kế thanh lịch nhưng thoải mái, chất liệu nhẹ và thoáng mát, hoàn hảo cho các buổi picnic và shopping.',
                'price' => 580000,
                'image' => 'storage/products/quan_au_casual_trang_nu.jpg',
                'category_id' => $casualCategory->id,
                'measurements' => 'Eo 70cm, Mông 94cm',
                'length' => 'Dài 90cm',
                'garment_size' => 'Size S',
                'style' => 'Casual thanh lịch',
                'fit' => 'Comfort fit',
                'material' => 'Linen pha Cotton',
                'leg_style' => 'Ống suông',
            ],
            [
                'name' => 'Quần âu casual nam kaki xanh lá trẻ trung',
                'description' => 'Quần âu nam kiểu kaki màu xanh lá, phong cách trẻ trung và năng động, dễ phối đồ, chất liệu bền và dễ giặt, thích hợp cho sinh viên và giới trẻ.',
                'price' => 550000,
                'image' => 'storage/products/quan_au_casual_xanh_nam.jpg',
                'category_id' => $casualCategory->id,
                'measurements' => 'Eo 76cm, Mông 94cm',
                'length' => 'Dài 92cm',
                'garment_size' => 'Size S',
                'style' => 'Casual trẻ trung',
                'fit' => 'Regular fit',
                'material' => 'Canvas Cotton',
                'leg_style' => 'Ống đứng',
            ],
            [
                'name' => 'Quần âu casual nữ hồng pastel ngọt ngào',
                'description' => 'Quần âu nữ màu hồng pastel nhẹ nhàng, thiết kế ngọt ngào và nữ tính, chất liệu mềm mại, thích hợp cho các buổi hẹn hò và dạo chơi cuối tuần.',
                'price' => 590000,
                'image' => 'storage/products/quan_au_casual_hong_nu.jpg',
                'category_id' => $casualCategory->id,
                'measurements' => 'Eo 68cm, Mông 92cm',
                'length' => 'Dài 88cm',
                'garment_size' => 'Size S',
                'style' => 'Casual ngọt ngào',
                'fit' => 'Cute fit',
                'material' => 'Chiffon Cotton',
                'leg_style' => 'Ống côn nhẹ',
            ],
            [
                'name' => 'Quần âu casual unisex đen trắng kẻ sọc',
                'description' => 'Quần âu unisex với họa tiết kẻ sọc đen trắng cổ điển, phong cách casual but chic, phù hợp cho cả nam và nữ, dễ mix-match với nhiều loại áo.',
                'price' => 640000,
                'image' => 'storage/products/quan_au_casual_ke_soc.jpg',
                'category_id' => $casualCategory->id,
                'measurements' => 'Eo 74cm, Mông 94cm',
                'length' => 'Dài 94cm',
                'garment_size' => 'Size M',
                'style' => 'Casual chic',
                'fit' => 'Unisex fit',
                'material' => 'Cotton blend kẻ sọc',
                'leg_style' => 'Ống thẳng',
            ],
        ];

        // Tạo sản phẩm Quần âu công sở
        foreach ($officeProducts as $product) {
            Product::create($product);
        }

        // Tạo sản phẩm Quần âu dự tiệc
        foreach ($partyProducts as $product) {
            Product::create($product);
        }

        // Tạo sản phẩm Quần âu casual
        foreach ($casualProducts as $product) {
            Product::create($product);
        }
    }
}
