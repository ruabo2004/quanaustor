<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->count() > 0 && $products->count() > 0) {
            $reviews = [
                // Đánh giá 5 sao
                [
                    'rating' => 5,
                    'comment' => 'Sản phẩm rất chất lượng, may mặc tuyệt vời. Tôi rất hài lòng với việc mua hàng này. Chất liệu cao cấp, form dáng chuẩn.',
                    'approved' => true,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Quần áo Puma chất lượng cao, thiết kế đẹp, rất hài lòng! Giao hàng nhanh, đóng gói cẩn thận.',
                    'approved' => true,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Mặc rất thoải mái, chất liệu thoáng mát. Đúng như mô tả, sẽ giới thiệu cho bạn bè.',
                    'approved' => true,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Thiết kế hiện đại, phù hợp đi làm và đi chơi. Chất lượng xứng đáng với giá tiền.',
                    'approved' => true,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Tuyệt vời! Form dáng chuẩn, màu sắc đẹp. Shop phục vụ nhiệt tình, sẽ ủng hộ tiếp.',
                    'approved' => true,
                ],
                
                // Đánh giá 4 sao
                [
                    'rating' => 4,
                    'comment' => 'Chất lượng tốt, giao hàng nhanh. Sẽ mua lại lần sau. Chỉ có điều size hơi nhỏ so với mô tả.',
                    'approved' => true,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Đẹp, chất lượng tốt cho tầm giá. Màu sắc hơi khác so với hình ảnh nhưng vẫn ổn.',
                    'approved' => true,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Sản phẩm ok, chất liệu mát mẻ. Giá hợp lý, giao hàng đúng hẹn.',
                    'approved' => true,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Mặc thoải mái, form đẹp. Chỉ hơi dài so với dự kiến nhưng vẫn chấp nhận được.',
                    'approved' => true,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Chất lượng tốt, thiết kế đơn giản nhưng sang trọng. Phù hợp cho dân văn phòng.',
                    'approved' => true,
                ],
                
                // Đánh giá 3 sao
                [
                    'rating' => 3,
                    'comment' => 'Tạm ổn, nhưng màu sắc không như mong đợi. Chất liệu cũng bình thường.',
                    'approved' => true,
                ],
                [
                    'rating' => 3,
                    'comment' => 'Sản phẩm trung bình, giá hơi cao so với chất lượng. Giao hàng chậm hơn dự kiến.',
                    'approved' => false,
                ],
                [
                    'rating' => 3,
                    'comment' => 'Form dáng ổn nhưng chất liệu hơi mỏng. Mặc được nhưng không xuất sắc.',
                    'approved' => false,
                ],
                
                // Đánh giá pending (chờ duyệt)
                [
                    'rating' => 5,
                    'comment' => 'Rất đẹp và vừa vặn. Chất liệu mềm mại, thoải mái khi mặc. Sẽ mua thêm màu khác.',
                    'approved' => false,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Chất lượng như mong đợi, form chuẩn. Đóng gói cẩn thận, giao hàng nhanh.',
                    'approved' => false,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Sản phẩm đẹp, chất liệu cao cấp. Thiết kế thời trang, phù hợp với xu hướng hiện tại.',
                    'approved' => false,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Mặc thoải mái, dễ phối đồ. Giá cả hợp lý, chất lượng tốt.',
                    'approved' => false,
                ],
                [
                    'rating' => 3,
                    'comment' => 'Bình thường, không có gì đặc biệt. Size hơi lớn so với bảng size.',
                    'approved' => false,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Tuyệt vời! Chất liệu cotton cao cấp, may mặc cẩn thận. Rất đáng tiền.',
                    'approved' => false,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Thiết kế đẹp, chất lượng ổn. Màu sắc đúng như hình, giao hàng đúng hẹn.',
                    'approved' => false,
                ],
                
                // Thêm một số đánh giá ngắn
                [
                    'rating' => 5,
                    'comment' => 'Tuyệt vời! Sẽ mua lại.',
                    'approved' => true,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Chất lượng tốt, đáng mua.',
                    'approved' => true,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Rất hài lòng với sản phẩm!',
                    'approved' => true,
                ],
                [
                    'rating' => 4,
                    'comment' => 'Ổn, giá hợp lý.',
                    'approved' => false,
                ],
                [
                    'rating' => 5,
                    'comment' => 'Chất lượng cao, thiết kế đẹp.',
                    'approved' => false,
                ],
            ];

            // Tạo reviews cho từng sản phẩm
            foreach ($products as $product) {
                $reviewsForProduct = collect($reviews)->random(rand(2, 6)); // Mỗi sản phẩm có 2-6 đánh giá
                
                foreach ($reviewsForProduct as $reviewData) {
                    $randomUser = $users->random();
                    
                    // Check if this user already reviewed this product
                    $existingReview = Review::where('user_id', $randomUser->id)
                        ->where('product_id', $product->id)
                        ->first();
                    
                    if (!$existingReview) {
                        Review::create([
                            'user_id' => $randomUser->id,
                            'product_id' => $product->id,
                            'rating' => $reviewData['rating'],
                            'comment' => $reviewData['comment'],
                            'approved' => $reviewData['approved'],
                            'created_at' => now()->subDays(rand(1, 30)), // Random date trong 30 ngày qua
                        ]);
                    }
                }
            }
        }
    }
}