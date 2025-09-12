@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="puma-hero">
    <div class="container">
        <div class="puma-hero-content">
            <h1 class="puma-text-white">PHONG CÁCH CÔNG SỞ</h1>
            <p class="puma-text-white">Bộ sưu tập quần âu chuyên nghiệp - Chất lượng cao & Thiết kế hiện đại</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('products.index') }}" class="puma-btn puma-btn-primary">
                    <i class="fas fa-fire"></i>
                    MUA NGAY
                </a>
                <a href="#categories" class="puma-btn puma-btn-outline">
                    <i class="fas fa-search"></i>
                    KHÁM PHÁ
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="puma-categories-section" id="categories">
    <div class="container">
        <div class="puma-section-title puma-bounce-in">
            <h2>DANH MỤC SẢN PHẨM</h2>
<p>Chọn phong cách hoàn hảo cho bạn</p>
        </div>
        
        <div class="row">
            @php
                $categoryIcons = [
                    'Quần âu nam' => 'fas fa-male',
                    'Quần âu nữ' => 'fas fa-female', 
                    'Quần âu công sở' => 'fas fa-briefcase',
                    'Quần âu dự tiệc' => 'fas fa-glass-cheers',
                    'Quần âu casual' => 'fas fa-walking'
                ];
                
                $categoryDescriptions = [
                    'Quần âu nam' => 'Phong cách lịch lãm và mạnh mẽ dành cho quý ông hiện đại',
                    'Quần âu nữ' => 'Thiết kế thanh lịch và nữ tính cho phái đẹp tự tin',
                    'Quần âu công sở' => 'Phong cách chuyên nghiệp cho môi trường văn phòng với thiết kế hiện đại và form dáng hoàn hảo',
                    'Quần âu dự tiệc' => 'Phong cách lịch lãm cho những dịp đặc biệt, hoàn hảo cho các buổi lễ và sự kiện trang trọng',
                    'Quần âu casual' => 'Phong cách hiện đại thoải mái cho cuộc sống hàng ngày, kết hợp sự thoải mái với thời trang đương đại'
                ];
            @endphp
            
            @foreach($categories->take(5) as $index => $category)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="puma-category-card {{ $loop->iteration % 3 == 1 ? 'puma-slide-in-left' : ($loop->iteration % 3 == 2 ? 'puma-bounce-in' : 'puma-slide-in-right') }}">
                    <div class="puma-category-icon">
                        <i class="{{ $categoryIcons[$category->name] ?? 'fas fa-tags' }}"></i>
                    </div>
                    <h3 class="puma-category-title">{{ $category->name }}</h3>
                    <p class="puma-category-desc">{{ $categoryDescriptions[$category->name] ?? 'Bộ sưu tập đa dạng và chất lượng cao' }}</p>
                    <div style="margin-bottom: 10px;">
                        <small style="color: #666; font-weight: 600;">
                            <i class="fas fa-box"></i> {{ $category->actual_product_count ?? $category->products->count() }} sản phẩm
                        </small>
                    </div>
                    <a href="{{ route('categories.show', $category->id) }}" class="puma-btn puma-btn-black puma-mt-20">
                        <i class="fas fa-arrow-right"></i>
                        XEM BỘ SƯU TẬP
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="puma-products-section">
    <div class="container">
        <div class="puma-section-title">
            <h2>SẢN PHẨM NỔI BẬT</h2>
            <p>Bộ sưu tập mới nhất và xuất sắc nhất</p>
        </div>
        
        <div class="row justify-content-center">
            @foreach($products->take(8) as $product)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card h-100" style="
                        border: none; 
                        border-radius: 12px; 
                        overflow: hidden; 
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
                        transition: all 0.3s ease;
                        background: white;
                    ">
                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="card-img-top"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background: var(--puma-gradient-1); color: white;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        @endif
                        
                        <!-- Product Info -->
                        <div class="card-body" style="padding: 15px;">
                            <h5 class="card-title" style="
                                font-size: 16px; 
                                font-weight: 700; 
                                color: #2c3e50; 
                                margin-bottom: 10px; 
                                text-transform: uppercase;
                                letter-spacing: 0.5px;
                                line-height: 1.3;
                            ">
                                {{ $product->name }}
                            </h5>
                            
                            <p class="card-text" style="
                                font-size: 13px; 
                                color: #7f8c8d; 
                                margin-bottom: 15px;
                                line-height: 1.4;
                                height: 40px;
                                overflow: hidden;
                                display: -webkit-box;
                                -webkit-line-clamp: 2;
                                -webkit-box-orient: vertical;
                            ">
                                {{ $product->description }}
                            </p>
                            
                            <!-- Price -->
                            <div style="margin-bottom: 20px;">
                                <span style="
                                    font-size: 20px; 
                                    font-weight: 700; 
                                    color: #e74c3c;
                                ">
                                    {{ number_format($product->price) }} VNĐ
                                </span>
                            </div>
                            
                            <!-- Quick Size Selector (Hidden by default) -->
                            <div class="quick-size-selector mb-3" style="display: none;">
                                <label class="form-label fw-bold text-muted" style="font-size: 12px;">CHỌN SIZE:</label>
                                <div class="d-flex gap-1 mb-2">
                                    @if($product->sizes && $product->sizes->count() > 0)
                                        @foreach($product->sizes->take(5) as $size)
                                            <button type="button" 
                                                    class="quick-size-btn btn btn-outline-secondary btn-sm" 
                                                    data-size="{{ $size->size }}" 
                                                    data-size-id="{{ $size->id }}"
                                                    data-price-adjustment="{{ $size->price_adjustment }}"
                                                    style="width: 45px; height: 35px; font-size: 11px; font-weight: 600;">
                                                {{ $size->size }}
                                            </button>
                                        @endforeach
                                    @else
                                        <button type="button" 
                                                class="quick-size-btn btn btn-outline-secondary btn-sm" 
                                                data-size="XL" 
                                                data-size-id="" 
                                                data-price-adjustment="0"
                                                style="width: 45px; height: 35px; font-size: 11px; font-weight: 600;">
                                            XL
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Default Action Buttons -->
                            <div class="default-buttons d-flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-dark flex-fill" style="font-size: 11px; padding: 8px 12px; border: 2px solid #343a40; color: #343a40; font-weight: 600;">
                                    <i class="fas fa-eye"></i> XEM
                                </a>
                                <button type="button" class="show-size-selector btn flex-fill" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; color: white; font-size: 11px; padding: 8px 12px; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(220,53,69,0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <i class="fas fa-plus"></i> THÊM
                                </button>
                            </div>
                            
                            <!-- Size Selected Buttons (Hidden by default) -->
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="size-selected-form" style="display: none;">
                                @csrf
                                <input type="hidden" name="size" class="selected-size" value="">
                                <input type="hidden" name="size_id" class="selected-size-id" value="">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn flex-fill" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; color: white; font-size: 11px; padding: 8px 12px; font-weight: 600; transition: all 0.3s ease;" disabled>
                                        <i class="fas fa-cart-plus"></i> THÊM VÀO GIỎ
                                    </button>
                                    <button type="button" class="cancel-size-selection btn btn-outline-secondary" style="font-size: 11px; padding: 8px 12px; font-weight: 600;">
                                        <i class="fas fa-times"></i> HỦY
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center puma-mt-40">
            <a href="{{ route('products.index') }}" class="puma-btn puma-btn-gold">
                <i class="fas fa-th"></i>
                XEM TẤT CẢ SẢN PHẨM
            </a>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="puma-newsletter-section" style="background: var(--puma-gradient-1); padding: 80px 0; color: var(--puma-white);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 style="font-size: 3rem; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;">
                    LUÔN CẬP NHẬT
                </h2>
                <p style="font-size: 1.2rem; margin-bottom: 0; opacity: 0.9;">
                    Nhận thông tin mới nhất về sản phẩm và khuyến mãi độc quyền
                </p>
            </div>
            <div class="col-lg-6">
                <form class="newsletter-form" style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <input type="email" 
                               class="puma-form-control" 
                               placeholder="Nhập địa chỉ email của bạn"
                               style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color: white;">
                    </div>
                    <button type="submit" class="puma-btn puma-btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        ĐĂNG KÝ
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section style="padding: 80px 0; background: var(--puma-light-grey);">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: var(--puma-gradient-2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h4 style="font-weight: 900; text-transform: uppercase; margin-bottom: 15px;">MIỄN PHÍ VẬN CHUYỂN</h4>
                    <p style="color: var(--puma-medium-grey);">Giao hàng miễn phí cho đơn hàng trên 500k VNĐ</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: var(--puma-gradient-2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h4 style="font-weight: 900; text-transform: uppercase; margin-bottom: 15px;">TRẢ HÀNG 30 NGÀY</h4>
                    <p style="color: var(--puma-medium-grey);">Dễ dàng trả hàng trong vòng 30 ngày</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: var(--puma-gradient-2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-award"></i>
                    </div>
                    <h4 style="font-weight: 900; text-transform: uppercase; margin-bottom: 15px;">CHẤT LƯỢNG CAO CẤP</h4>
                    <p style="color: var(--puma-medium-grey);">Chất liệu cao cấp và tay nghề tinh xảo</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; background: var(--puma-gradient-2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 2rem;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 style="font-weight: 900; text-transform: uppercase; margin-bottom: 15px;">HỖ TRỢ 24/7</h4>
                    <p style="color: var(--puma-medium-grey);">Dịch vụ khách hàng chuyên nghiệp</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer được hiển thị từ layouts/app.blade.php -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe all cards and sections
    document.querySelectorAll('.puma-card, .puma-category-card, .puma-section-title').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });
    
    // Add hover effects to buttons
    document.querySelectorAll('.puma-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Product cards hover effects
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
        });
    });
    
    // Show size selector when clicking THÊM
    document.querySelectorAll('.show-size-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.card');
            const sizeSelector = card.querySelector('.quick-size-selector');
            const defaultButtons = card.querySelector('.default-buttons');
            const sizeForm = card.querySelector('.size-selected-form');
            
            // Show size selector and hide default buttons
            sizeSelector.style.display = 'block';
            defaultButtons.style.display = 'none';
            sizeForm.style.display = 'block';
        });
    });
    
    // Handle size selection
    document.querySelectorAll('.quick-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.card');
            const sizeForm = card.querySelector('.size-selected-form');
            const addToCartBtn = sizeForm.querySelector('button[type="submit"]');
            const sizeInput = sizeForm.querySelector('.selected-size');
            const sizeIdInput = sizeForm.querySelector('.selected-size-id');
            
            // Reset all size buttons in this card
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.classList.remove('btn-primary');
                sizeBtn.classList.add('btn-outline-secondary');
            });
            
            // Highlight selected size
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');
            
            // Enable add to cart button and set values
            addToCartBtn.disabled = false;
            sizeInput.value = this.dataset.size;
            sizeIdInput.value = this.dataset.sizeId;
        });
    });
    
    // Cancel size selection
    document.querySelectorAll('.cancel-size-selection').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const card = this.closest('.card');
            const sizeSelector = card.querySelector('.quick-size-selector');
            const defaultButtons = card.querySelector('.default-buttons');
            const sizeForm = card.querySelector('.size-selected-form');
            
            // Hide size selector and show default buttons
            if (sizeSelector) sizeSelector.style.display = 'none';
            if (defaultButtons) defaultButtons.style.display = 'flex';
            if (sizeForm) sizeForm.style.display = 'none';
            
            // Reset size selection
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.classList.remove('btn-primary');
                sizeBtn.classList.add('btn-outline-secondary');
            });
            
            // Reset form
            if (sizeForm) {
                const addToCartBtn = sizeForm.querySelector('button[type="submit"]');
                const sizeInput = sizeForm.querySelector('.selected-size');
                const sizeIdInput = sizeForm.querySelector('.selected-size-id');
                
                if (addToCartBtn) addToCartBtn.disabled = true;
                if (sizeInput) sizeInput.value = '';
                if (sizeIdInput) sizeIdInput.value = '';
            }
        });
    });
    
    // AJAX Add to Cart
    document.querySelectorAll('.size-selected-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ĐANG THÊM...';
            
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                formData.append('_token', token.getAttribute('content'));
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification('success', data.message);
                    
                    // Reset form and hide size selector
                    const card = this.closest('.card');
                    const sizeSelector = card.querySelector('.quick-size-selector');
                    const defaultButtons = card.querySelector('.default-buttons');
                    
                    if (sizeSelector) sizeSelector.style.display = 'none';
                    if (defaultButtons) defaultButtons.style.display = 'flex';
                    this.style.display = 'none';
                    
                    // Reset size selection
                    card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                        sizeBtn.classList.remove('btn-primary');
                        sizeBtn.classList.add('btn-outline-secondary');
                    });
                    
                    // Update cart count if exists
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount && data.cartCount) {
                        cartCount.textContent = data.cartCount;
                    }
                } else {
                    showNotification('error', data.message || 'Có lỗi xảy ra');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
            })
            .finally(() => {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    });
    
    // Notification function
    function showNotification(type, message) {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.custom-notification');
        existingNotifications.forEach(n => n.remove());
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `custom-notification alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        `;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>
@endsection