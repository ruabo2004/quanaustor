@extends('layouts.app')

@section('content')
<div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh; padding: 1.5rem 0;">
<div class="container">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 10px; margin-bottom: 20px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="background: none; padding: 0; font-size: 13px;">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color: #6c757d; text-decoration: none;">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index') }}" style="color: #6c757d; text-decoration: none;">Sản phẩm</a>
                </li>
                <li class="breadcrumb-item active" style="color: #dc3545; font-weight: 600;">{{ $product->name }}</li>
            </ol>
        </nav>

    <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-3">
                <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); position: relative;">
                    @if($product->image)
                        <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             style="width: 100%; height: 350px; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 350px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div class="text-center">
                                <i class="fas fa-image" style="font-size: 3rem; opacity: 0.7;"></i>
                                <p class="mt-2" style="font-size: 1.1rem;">{{ $product->name }}</p>
                            </div>
        </div>
            @endif
                    
                    <!-- Image Badge -->
                    <div style="position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase;">
                        <i class="fas fa-star"></i> Mới
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                    <!-- Product Title -->
                    <h1 style="font-size: 1.6rem; font-weight: 700; color: #2c3e50; margin-bottom: 10px; line-height: 1.3;">
                        {{ $product->name }}
                    </h1>
                    
                    <!-- Rating -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rating">
                            @php
                                $avgRating = $product->average_rating;
                                $reviewCount = $product->review_count;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($avgRating))
                                    <i class="fas fa-star" style="color: #ffc107; font-size: 14px;"></i>
                                @elseif($i <= ceil($avgRating))
                                    <i class="fas fa-star-half-alt" style="color: #ffc107; font-size: 14px;"></i>
                                @else
                                    <i class="far fa-star" style="color: #e9ecef; font-size: 14px;"></i>
                                @endif
                            @endfor
                            <span style="color: #6c757d; margin-left: 6px; font-size: 12px;">
                                @if($reviewCount > 0)
                                    ({{ number_format($avgRating, 1) }} - {{ $reviewCount }} đánh giá)
                                @else
                                    (Chưa có đánh giá)
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="mb-3" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe6e6 100%); padding: 15px; border-radius: 10px; border-left: 4px solid #dc3545;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span style="font-size: 1.6rem; font-weight: 700; color: #dc3545;">
                                    {{ number_format($product->price) }} VNĐ
                                </span>
                                <div style="font-size: 12px; color: #6c757d; margin-top: 3px;">
                                    <del>{{ number_format($product->price * 1.2) }} VNĐ</del> 
                                    <span style="color: #28a745; font-weight: 600; margin-left: 8px;">Tiết kiệm 17%</span>
                                </div>
                            </div>
                            <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 6px 10px; border-radius: 15px; font-size: 10px; font-weight: 600;">
                                GIẢM GIÁ
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <h4 style="font-size: 14px; font-weight: 600; color: #2c3e50; margin-bottom: 10px;">Mô tả sản phẩm</h4>
                        <p style="color: #6c757d; line-height: 1.5; font-size: 13px; margin-bottom: 12px;">
                            {{ $product->description }}
                        </p>
                        
                        <!-- Key Features -->
                        <div class="mt-2">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle" style="color: #28a745; margin-right: 8px; font-size: 12px;"></i>
                                <span style="color: #495057; font-size: 12px;">Chất liệu cao cấp, bền đẹp</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle" style="color: #28a745; margin-right: 8px; font-size: 12px;"></i>
                                <span style="color: #495057; font-size: 12px;">Thiết kế hiện đại, phù hợp mọi dịp</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle" style="color: #28a745; margin-right: 8px; font-size: 12px;"></i>
                                <span style="color: #495057; font-size: 12px;">Dễ dàng phối đồ, thoải mái cả ngày</span>
                            </div>
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div class="mb-3">
                        <h4 style="font-size: 14px; font-weight: 600; color: #2c3e50; margin-bottom: 10px;">
                            <i class="fas fa-ruler" style="font-size: 12px;"></i> Chọn kích thước
                        </h4>
                        <div class="size-selection" style="display: flex; gap: 8px; flex-wrap: wrap;">
                            @if($product->sizes && $product->sizes->count() > 0)
                                @foreach($product->sizes as $productSize)
                                    <label class="size-option" style="cursor: pointer;">
                                        <input type="radio" name="size" value="{{ $productSize->size }}" 
                                               data-size-id="{{ $productSize->id }}"
                                               data-price-adjustment="{{ $productSize->price_adjustment }}"
                                               data-stock="{{ $productSize->stock_quantity }}"
                                               style="display: none;"
                                               {{ !$productSize->inStock() ? 'disabled' : '' }}>
                                        <div class="size-btn" style="
                                            width: 60px; height: 50px; 
                                            border: 2px solid {{ !$productSize->inStock() ? '#dc3545' : ($productSize->stock_quantity <= 3 ? '#ffc107' : '#28a745') }}; 
                                            border-radius: 8px; 
                                            display: flex; flex-direction: column; align-items: center; justify-content: center; 
                                            font-weight: 600; color: #495057; font-size: 12px;
                                            transition: all 0.3s ease;
                                            {{ !$productSize->inStock() ? 'opacity: 0.5; cursor: not-allowed; background-color: #f8f9fa;' : '' }}
                                        ">
                                            <span style="font-size: 14px; font-weight: 700;">{{ $productSize->size }}</span>
                                            <span style="font-size: 10px; color: {{ !$productSize->inStock() ? '#dc3545' : ($productSize->stock_quantity <= 3 ? '#fd7e14' : '#28a745') }};">
                                                @if($productSize->stock_quantity <= 0)
                                                    Hết hàng
                                                @elseif($productSize->stock_quantity <= 3)
                                                    Còn {{ $productSize->stock_quantity }}
                                                @else
                                                    Còn {{ $productSize->stock_quantity }}
                                                @endif
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <p style="color: #6c757d; font-style: italic; font-size: 13px;">Chưa có thông tin kích thước</p>
            @endif
                        </div>
                    </div>

                    <!-- Stock Summary -->
                    <div class="mb-3" style="background: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 4px solid #007bff;">
                        <div style="display: flex; justify-content: between; align-items: center; gap: 10px;">
                            <div style="flex: 1;">
                                <small style="color: #6c757d; font-weight: 600;">Tình trạng kho:</small>
                                @php
                                    $totalStock = $product->sizes->sum('stock_quantity');
                                    $availableSizes = $product->sizes->where('stock_quantity', '>', 0)->count();
                                    $totalSizes = $product->sizes->count();
                                @endphp
                                <span style="font-weight: 700; color: {{ $totalStock > 0 ? '#28a745' : '#dc3545' }};">
                                    @if($totalStock > 0)
                                        Còn {{ $totalStock }} sản phẩm ({{ $availableSizes }}/{{ $totalSizes }} sizes)
                                    @else
                                        Hết hàng tất cả sizes
                                    @endif
                                </span>
                            </div>
                            @if($totalStock > 0 && $totalStock <= 10)
                                <div style="background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                    ⚠️ Sắp hết
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Size Info Display -->
                    <div id="size-info" class="mb-3" style="display: none; background: #f8f9fa; padding: 12px; border-radius: 8px; border-left: 3px solid #dc3545;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong style="color: #2c3e50; font-size: 13px;">Size: <span id="selected-size-display"></span></strong>
                            </div>
                            <div class="text-end">
                                <div style="font-size: 14px; font-weight: 600; color: #dc3545;" id="final-price">{{ number_format($product->price) }} VNĐ</div>
                                <div style="font-size: 11px; color: #28a745;" id="stock-status"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="action-buttons">
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="add-to-cart-form">
                @csrf
                            <input type="hidden" name="size" id="selected-size" value="">
                            <input type="hidden" name="size_id" id="selected-size-id" value="">
                            
                            <div class="d-flex gap-2 mb-3">
                                <button type="submit" 
                                        class="btn flex-fill" 
                                        id="add-to-cart-btn"
                                        style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 
                                               border: none; color: white; padding: 10px 20px; 
                                               font-size: 13px; font-weight: 600; border-radius: 8px; 
                                               transition: all 0.3s ease;"
                                        disabled>
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    THÊM VÀO GIỎ HÀNG
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-outline-dark"
                                        style="padding: 10px 15px; border-radius: 8px; border-width: 2px; font-weight: 600; font-size: 13px;">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
            </form>
                    </div>

                    <!-- Delivery Info -->
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-top: 15px;">
                        <h5 style="font-size: 13px; font-weight: 600; color: #2c3e50; margin-bottom: 10px;">
                            <i class="fas fa-truck" style="color: #dc3545; font-size: 12px;"></i> Thông tin giao hàng
                        </h5>
                        <div class="delivery-options">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span style="color: #495057; font-size: 12px;">
                                    <i class="fas fa-clock" style="color: #28a745; width: 16px; font-size: 11px;"></i>
                                    Giao hàng nhanh (2-3 ngày)
                                </span>
                                <span style="color: #dc3545; font-weight: 600; font-size: 11px;">MIỄN PHÍ</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span style="color: #495057; font-size: 12px;">
                                    <i class="fas fa-undo" style="color: #ffc107; width: 16px; font-size: 11px;"></i>
                                    Đổi trả trong 7 ngày
                                </span>
                                <span style="color: #28a745; font-weight: 600; font-size: 11px;">MIỄN PHÍ</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span style="color: #495057; font-size: 12px;">
                                    <i class="fas fa-shield-alt" style="color: #007bff; width: 16px; font-size: 11px;"></i>
                                    Bảo hành 12 tháng
                                </span>
                                <span style="color: #007bff; font-weight: 600; font-size: 11px;">CHÍNH HÃNG</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                    <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px; text-align: center; font-size: 1.3rem;">
                        <i class="fas fa-info-circle" style="color: #dc3545; font-size: 16px;"></i>
                        CHI TIẾT SẢN PHẨM
                    </h3>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <h4 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">Mô tả chi tiết</h4>
                            <p style="color: #6c757d; line-height: 1.6; font-size: 14px; margin-bottom: 15px;">
                                {{ $product->description }} Đây là sản phẩm quần âu cao cấp được thiết kế đặc biệt cho người đàn ông hiện đại. 
                                Với chất liệu vải cao cấp và đường may tinh xảo, sản phẩm mang lại sự thoải mái tối đa và phong cách lịch lãm.
                            </p>
                            
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 12px; font-size: 1rem;">Đặc điểm nổi bật:</h5>
                            <ul style="color: #6c757d; line-height: 1.6; font-size: 13px;">
                                <li>Chất liệu vải cao cấp nhập khẩu từ châu Âu</li>
                                <li>Thiết kế slim fit ôm dáng, tôn lên vóc dáng nam tính</li>
                                <li>Công nghệ chống nhăn và thấm hút mồ hôi</li>
                                <li>Màu sắc bền đẹp, không phai sau nhiều lần giặt</li>
                                <li>Phù hợp cho cả môi trường công sở và dự tiệc</li>
                            </ul>
                        </div>
                        <div class="col-lg-4">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 10px;">
                                <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 12px; font-size: 1rem;">Thông số kỹ thuật</h5>
                                <div class="spec-item" style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef; font-size: 13px;">
                                    <span style="color: #495057; font-weight: 600;">Chất liệu:</span>
                                    <span style="color: #6c757d;">Cotton Premium</span>
                                </div>
                                <div class="spec-item" style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef; font-size: 13px;">
                                    <span style="color: #495057; font-weight: 600;">Form dáng:</span>
                                    <span style="color: #6c757d;">Slim Fit</span>
                                </div>
                                <div class="spec-item" style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef; font-size: 13px;">
                                    <span style="color: #495057; font-weight: 600;">Xuất xứ:</span>
                                    <span style="color: #6c757d;">Việt Nam</span>
                                </div>
                                <div class="spec-item" style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px;">
                                    <span style="color: #495057; font-weight: 600;">Bảo quản:</span>
                                    <span style="color: #6c757d;">Giặt máy 30°C</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="row mt-4">
            <div class="col-12">
                <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                    <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px; text-align: center; font-size: 1.3rem;">
                        <i class="fas fa-heart" style="color: #dc3545; font-size: 16px;"></i>
                        SẢN PHẨM LIÊN QUAN
                    </h3>
                    
                    <div class="row justify-content-center">
                        @php
                            $relatedProducts = \App\Models\Product::where('id', '!=', $product->id)
                                                                 ->inRandomOrder()
                                                                 ->take(4)
                                                                 ->get();
                        @endphp
                        
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card h-100" style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                    @if($relatedProduct->image)
                                        <img src="{{ filter_var($relatedProduct->image, FILTER_VALIDATE_URL) ? $relatedProduct->image : asset('storage/' . $relatedProduct->image) }}" 
                                             class="card-img-top" 
                                             alt="{{ $relatedProduct->name }}" 
                                             style="height: 160px; object-fit: cover;">
                                    @endif
                                    <div class="card-body" style="padding: 15px;">
                                        <h5 class="card-title" style="font-size: 14px; font-weight: 600; color: #2c3e50; margin-bottom: 8px;">
                                            {{ Str::limit($relatedProduct->name, 30) }}
                                        </h5>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span style="font-size: 16px; font-weight: 700; color: #dc3545;">
                                                {{ number_format($relatedProduct->price) }} VNĐ
                                            </span>
                                            <a href="{{ route('products.show', $relatedProduct) }}" 
                                               class="btn btn-sm"
                                               style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 11px;">
                                                XEM
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 8px 25px rgba(0,0,0,0.1);">
                    <h3 style="color: #2c3e50; font-weight: 600; margin-bottom: 20px; text-align: center; font-size: 1.3rem;">
                        <i class="fas fa-star" style="color: #ffc107; font-size: 16px;"></i>
                        ĐÁNH GIÁ SẢN PHẨM
                    </h3>

                    <!-- Review Form -->
                    @auth
                        @if(!$userReview)
                            <div class="review-form mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 10px; border: 2px solid #e9ecef;">
                                <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">
                                    <i class="fas fa-edit" style="color: #dc3545;"></i> Viết đánh giá của bạn
                                </h5>
                                <form action="{{ route('reviews.store', $product) }}" method="POST" id="reviewForm">
                                    @csrf
                                    
                                    <!-- Star Rating -->
                                    <div class="mb-3">
                                        <label style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Đánh giá:</label>
                                        <div class="star-rating" style="font-size: 20px;">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" style="display: none;" required>
                                                <label for="star{{ $i }}" style="color: #ddd; cursor: pointer; transition: color 0.2s;">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                        <small style="color: #6c757d;">Click vào sao để đánh giá</small>
                                    </div>

                                    <!-- Comment -->
                                    <div class="mb-3">
                                        <label for="comment" style="color: #495057; font-weight: 600; margin-bottom: 8px; display: block;">Nhận xét (tùy chọn):</label>
                                        <textarea name="comment" id="comment" rows="3" 
                                                  style="width: 100%; padding: 12px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 14px; resize: vertical;"
                                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                                    </div>

                                    <button type="submit" 
                                            style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); 
                                                   border: none; color: white; padding: 10px 20px; 
                                                   font-size: 14px; font-weight: 600; border-radius: 8px; 
                                                   transition: all 0.3s ease;">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        GỬI ĐÁNH GIÁ
                                    </button>
                                </form>
                            </div>
                        @else
                            <!-- User's existing review -->
                            <div class="user-review mb-4" style="background: #e7f3ff; padding: 20px; border-radius: 10px; border: 2px solid #007bff;">
                                <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">
                                    <i class="fas fa-user" style="color: #007bff;"></i> Đánh giá của bạn
                                </h5>
                                
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star" style="color: {{ $i <= $userReview->rating ? '#ffc107' : '#e9ecef' }}; font-size: 16px;"></i>
                                            @endfor
                                            <span style="color: #6c757d; margin-left: 8px;">{{ $userReview->rating }}/5</span>
                                        </div>
                                        @if($userReview->comment)
                                            <p style="color: #495057; margin: 0; line-height: 1.5;">{{ $userReview->comment }}</p>
                                        @endif
                                        <small style="color: #6c757d;">
                                            Đăng lúc: {{ $userReview->created_at->format('d/m/Y H:i') }}
                                            @if(!$userReview->approved)
                                                <span style="color: #ffc107; font-weight: 600;"> - Đang chờ duyệt</span>
                                            @endif
                                        </small>
                                    </div>
                                    
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteReview({{ $userReview->id }})"
                                                style="font-size: 12px; padding: 4px 8px;">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                            <p style="color: #856404; margin: 0;">
                                <i class="fas fa-sign-in-alt"></i>
                                <a href="{{ route('login') }}" style="color: #dc3545; text-decoration: none; font-weight: 600;">Đăng nhập</a> 
                                để có thể đánh giá sản phẩm
                            </p>
                        </div>
                    @endauth

                    <!-- Reviews List -->
                    @if($product->approvedReviews->count() > 0)
                        <div class="reviews-list">
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">
                                <i class="fas fa-comments" style="color: #28a745;"></i> 
                                Đánh giá từ khách hàng ({{ $product->approvedReviews->count() }})
                            </h5>
                            
                            @foreach($product->approvedReviews as $review)
                                <div class="review-item" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #28a745;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong style="color: #2c3e50;">{{ $review->user->name }}</strong>
                                            <div class="mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star" style="color: {{ $i <= $review->rating ? '#ffc107' : '#e9ecef' }}; font-size: 14px;"></i>
                                                @endfor
                                                <span style="color: #6c757d; margin-left: 6px; font-size: 12px;">{{ $review->rating }}/5</span>
                                            </div>
                                        </div>
                                        <small style="color: #6c757d;">{{ $review->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    
                                    @if($review->comment)
                                        <p style="color: #495057; margin: 0; line-height: 1.5; font-size: 14px;">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 30px; color: #6c757d;">
                            <i class="fas fa-star" style="font-size: 48px; color: #e9ecef; margin-bottom: 15px;"></i>
                            <p style="margin: 0; font-size: 16px;">Chưa có đánh giá nào cho sản phẩm này</p>
                            <p style="margin: 5px 0 0 0; font-size: 14px;">Hãy là người đầu tiên đánh giá!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.size-btn:hover {
    border-color: #dc3545 !important;
    color: #dc3545 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
}

.size-option input:checked + .size-btn {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    color: white !important;
    border-color: #dc3545 !important;
}

#add-to-cart-btn:enabled:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
}

#add-to-cart-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sizeInputs = document.querySelectorAll('input[name="size"]');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const selectedSizeInput = document.getElementById('selected-size');
    const selectedSizeIdInput = document.getElementById('selected-size-id');
    const sizeInfo = document.getElementById('size-info');
    const selectedSizeDisplay = document.getElementById('selected-size-display');
    const finalPrice = document.getElementById('final-price');
    const stockStatus = document.getElementById('stock-status');

    const basePrice = {{ $product->price }};

    sizeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked && !this.disabled) {
                // Update hidden inputs
                selectedSizeInput.value = this.value;
                selectedSizeIdInput.value = this.dataset.sizeId;

                // Calculate final price
                const priceAdjustment = parseFloat(this.dataset.priceAdjustment) || 0;
                const totalPrice = basePrice + priceAdjustment;

                // Update UI
                selectedSizeDisplay.textContent = this.value;
                finalPrice.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' VNĐ';
                stockStatus.textContent = `Còn ${this.dataset.stock} sản phẩm`;

                // Show size info
                sizeInfo.style.display = 'block';

                // Enable add to cart button
                addToCartBtn.disabled = false;
                addToCartBtn.style.opacity = '1';
                addToCartBtn.style.cursor = 'pointer';
            }
        });
    });

    // Star Rating System
    const starInputs = document.querySelectorAll('.star-rating input[type="radio"]');
    const starLabels = document.querySelectorAll('.star-rating label');

    starLabels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
            starLabels.forEach((l, i) => {
                if (i >= index) {
                    l.style.color = '#ffc107';
                } else {
                    l.style.color = '#ddd';
                }
            });
        });

        label.addEventListener('mouseleave', function() {
            const checkedInput = document.querySelector('.star-rating input[type="radio"]:checked');
            if (checkedInput) {
                const checkedIndex = Array.from(starInputs).indexOf(checkedInput);
                starLabels.forEach((l, i) => {
                    if (i >= starInputs.length - checkedIndex - 1) {
                        l.style.color = '#ffc107';
                    } else {
                        l.style.color = '#ddd';
                    }
                });
            } else {
                starLabels.forEach(l => l.style.color = '#ddd');
            }
        });

        label.addEventListener('click', function() {
            const rating = this.getAttribute('for').replace('star', '');
            starLabels.forEach((l, i) => {
                if (i >= starInputs.length - rating) {
                    l.style.color = '#ffc107';
                } else {
                    l.style.color = '#ddd';
                }
            });
        });
    });
});

// Delete Review Function
function deleteReview(reviewId) {
    if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
        fetch(`/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa đánh giá.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa đánh giá.');
        });
    }
}
</script>
@endsection