@extends('layouts.app')

@section('content')
<!-- Best Sellers Section -->
<section class="puma-products-section">
    <div class="container">
        <div class="puma-section-title">
            <h2><i class="fas fa-fire text-warning"></i> SẢN PHẨM BÁN CHẠY</h2>
            <p>Những sản phẩm được yêu thích nhất</p>
        </div>

        <!-- Back to All Products -->
        <div class="mb-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Xem tất cả sản phẩm
            </a>
        </div>
    
        <div class="row justify-content-center">
            @foreach($bestSellers as $index => $product)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card h-100" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; background: white; position: relative;">
                        <!-- Bestseller Badge -->
                        @if($index < 3)
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                @if($index == 0)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-crown"></i> #1</span>
                                @elseif($index == 1)
                                    <span class="badge bg-secondary"><i class="fas fa-medal"></i> #2</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-trophy"></i> #3</span>
                                @endif
                            </div>
                        @else
                            <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                <span class="badge bg-success"><i class="fas fa-fire"></i> HOT</span>
                            </div>
                        @endif

                        <!-- Sales Count Badge -->
                        <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                            <span class="badge bg-dark"><i class="fas fa-shopping-cart"></i> {{ $product->total_sold }}</span>
                        </div>

                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background: var(--puma-gradient-1); color: white;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        @endif
                    
                        <!-- Product Info -->
                        <div class="card-body" style="padding: 15px;">
                            <h5 class="card-title" style="font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.3;">
                                {{ $product->name }}
                            </h5>
                            
                            <p class="card-text" style="font-size: 13px; color: #7f8c8d; margin-bottom: 15px; line-height: 1.4; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                {{ $product->description }}
                            </p>
                            
                            <!-- Price -->
                            <div style="margin-bottom: 20px;">
                                <span style="font-size: 20px; font-weight: 700; color: #e74c3c;">
                                    {{ number_format($product->price) }} VNĐ
                                </span>
                            </div>
                        
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-dark flex-fill" style="font-size: 11px; padding: 8px 12px;">
                                    <i class="fas fa-eye"></i> XEM
                                </a>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-warning flex-fill" style="font-size: 11px; padding: 8px 12px;">
                                    <i class="fas fa-plus"></i> THÊM
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    
        @if($bestSellers->isEmpty())
            <div class="text-center py-5">
                <div style="width: 100px; height: 100px; background: var(--puma-gradient-1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: white;">
                    <i class="fas fa-fire fa-3x"></i>
                </div>
                <h3 class="puma-text-black">CHƯA CÓ SẢN PHẨM BÁN CHẠY</h3>
                <p style="color: var(--puma-medium-grey);">Dữ liệu sẽ được cập nhật khi có đơn hàng</p>
                <a href="{{ route('products.index') }}" class="puma-btn puma-btn-primary">
                    <i class="fas fa-box"></i> XEM TẤT CẢ SẢN PHẨM
                </a>
            </div>
        @endif
    </div>
</section>
@endsection