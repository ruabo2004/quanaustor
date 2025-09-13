@extends('layouts.app')

@section('content')
<!-- Modern Hero Section -->
<section class="modern-hero">
    <div class="container">
        <div class="modern-hero-content">
            <div class="animate-fadeInUp">
                <h1>PHONG CÁCH CÔNG SỞ HIỆN ĐẠI</h1>
                <p>Bộ sưu tập quần âu chuyên nghiệp - Chất lượng vượt trội, thiết kế tinh tế và đẳng cấp cho người thành đạt</p>
                <div class="d-flex gap-4 justify-content-center flex-wrap mt-8">
                    <a href="{{ route('products.index') }}" class="btn-modern btn-modern-primary hover-lift">
                        <i class="fas fa-shopping-bag"></i>
                        Mua ngay
                    </a>
                    <a href="#categories" class="btn-modern btn-modern-outline hover-lift">
                        <i class="fas fa-compass"></i>
                        Khám phá
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div style="position: absolute; bottom: -1px; left: 0; right: 0; height: 60px; background: var(--gray-50); clip-path: polygon(0 100%, 100% 100%, 100% 0, 0 80%);"></div>
</section>

<!-- Modern Categories Section -->
<section class="section-modern" id="categories" style="background: white;">
    <div class="container">
        <div class="section-title-modern">
            <h2>Danh mục sản phẩm</h2>
            <p>Khám phá bộ sưu tập đa dạng được thiết kế dành riêng cho phong cách của bạn</p>
        </div>
        
        <div class="row g-4">
            @php
                $categoryIcons = [
                    'Quần âu nam' => 'fas fa-user-tie',
                    'Quần âu nữ' => 'fas fa-female', 
                    'Quần âu công sở' => 'fas fa-briefcase',
                    'Quần âu dự tiệc' => 'fas fa-glass-cheers',
                    'Quần âu casual' => 'fas fa-walking'
                ];
                
                $categoryDescriptions = [
                    'Quần âu nam' => 'Phong cách lịch lãm và mạnh mẽ dành cho quý ông hiện đại, từ công sở đến dạo phố',
                    'Quần âu nữ' => 'Thiết kế thanh lịch và nữ tính cho phái đẹp tự tin, tôn vinh vẻ đẹp tinh tế',
                    'Quần âu công sở' => 'Phong cách chuyên nghiệp cho môi trường văn phòng với thiết kế hiện đại và form dáng hoàn hảo',
                    'Quần âu dự tiệc' => 'Phong cách lịch lãm cho những dịp đặc biệt, hoàn hảo cho các buổi lễ và sự kiện trang trọng',
                    'Quần âu casual' => 'Phong cách hiện đại thoải mái cho cuộc sống hàng ngày, kết hợp sự thoải mái với thời trang đương đại'
                ];
                
                $categoryColors = [
                    'Quần âu nam' => 'linear-gradient(135deg, #1e40af, #3b82f6)',
                    'Quần âu nữ' => 'linear-gradient(135deg, #be185d, #ec4899)',
                    'Quần âu công sở' => 'linear-gradient(135deg, #059669, #10b981)',
                    'Quần âu dự tiệc' => 'linear-gradient(135deg, #dc2626, #ef4444)',
                    'Quần âu casual' => 'linear-gradient(135deg, #7c3aed, #a855f7)'
                ];
            @endphp
            
            @foreach($categories->take(5) as $index => $category)
            <div class="col-lg-4 col-md-6">
                <div class="category-card-modern hover-lift animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
                    <div class="category-icon-modern" style="background: {{ $categoryColors[$category->name] ?? 'linear-gradient(135deg, var(--primary), var(--primary-dark))' }};">
                        <i class="{{ $categoryIcons[$category->name] ?? 'fas fa-tags' }}"></i>
                    </div>
                    <h3 class="category-title-modern">{{ $category->name }}</h3>
                    <p class="category-description-modern">{{ $categoryDescriptions[$category->name] ?? 'Bộ sưu tập đa dạng và chất lượng cao' }}</p>
                    
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-6">
                        <span class="badge" style="background: var(--gray-100); color: var(--gray-700); padding: var(--space-2) var(--space-3); border-radius: var(--radius-full); font-size: 0.75rem; font-weight: var(--font-semibold);">
                            <i class="fas fa-box me-1"></i>
                            {{ $category->actual_product_count ?? $category->products->count() }} sản phẩm
                        </span>
                    </div>
                    
                    <a href="{{ route('categories.show', $category->id) }}" class="btn-modern btn-modern-secondary hover-lift" style="width: 100%;">
                        <i class="fas fa-arrow-right"></i>
                        Xem bộ sưu tập
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Modern Featured Products Section -->
<section class="section-modern" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-title-modern">
            <h2>Sản phẩm nổi bật</h2>
            <p>Khám phá những mẫu quần âu đẳng cấp, được yêu thích nhất từ bộ sưu tập của chúng tôi</p>
        </div>
        
        <div class="row g-4">
            @foreach($products->take(8) as $index => $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card-modern hover-lift animate-fadeInUp" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="product-image">
                        @if($product->image)
                            <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}">
                        @else
                                <div style="background: linear-gradient(135deg, var(--gray-200), var(--gray-300)); color: var(--gray-500); display: flex; align-items: center; justify-content: center; height: 100%;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        @endif
                        
                            <!-- Modern Overlay with Quick Actions -->
                            <div class="product-overlay">
                                <div class="quick-actions">
                                    <button class="quick-btn quick-view" data-product-id="{{ $product->id }}">
                                        <i class="fas fa-eye"></i> Xem nhanh
                                    </button>
                                    <button class="quick-btn quick-add" data-product-id="{{ $product->id }}">
                                        <i class="fas fa-shopping-cart"></i> Thêm giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="product-info-modern">
                            <h3 class="product-title-modern">{{ $product->name }}</h3>
                            <p class="product-description-modern">{{ $product->description }}</p>
                            
                            <div class="product-price-modern">
                                {{ number_format($product->price) }}đ
                            </div>
                            
                            <!-- Size Options -->
                                    @if($product->sizes && $product->sizes->count() > 0)
                                <div class="size-options-modern mb-4">
                                    <div style="font-size: 0.75rem; font-weight: var(--font-semibold); color: var(--gray-600); margin-bottom: var(--space-2);">
                                        SIZE CÓ SẴN:
                                    </div>
                                    <div class="d-flex gap-1">
                                        @foreach($product->sizes->take(4) as $size)
                                            <span class="size-badge" style="
                                                display: inline-block;
                                                padding: var(--space-1) var(--space-2);
                                                background: var(--gray-100);
                                                border-radius: var(--radius);
                                                font-size: 0.75rem;
                                                font-weight: var(--font-medium);
                                                color: var(--gray-700);
                                            ">
                                                {{ $size->size }}
                                            </span>
                                        @endforeach
                                        @if($product->sizes->count() > 4)
                                            <span style="font-size: 0.75rem; color: var(--gray-500); padding: var(--space-1);">+{{ $product->sizes->count() - 4 }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn-modern btn-modern-outline" style="flex: 1; font-size: 0.75rem; padding: var(--space-2) var(--space-3);">
                                    <i class="fas fa-eye"></i>
                                    Chi tiết
                                </a>
                                <button type="button" class="btn-modern btn-modern-primary quick-add-btn" data-product-id="{{ $product->id }}" style="flex: 2; font-size: 0.75rem; padding: var(--space-2) var(--space-3);">
                                    <i class="fas fa-plus"></i>
                                    Thêm giỏ hàng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-16">
            <a href="{{ route('products.index') }}" class="btn-modern btn-modern-primary hover-lift" style="padding: var(--space-4) var(--space-8);">
                <i class="fas fa-th"></i>
                Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</section>

<!-- Modern Features Section -->
<section class="section-modern" style="background: white;">
    <div class="container">
        <div class="section-title-modern">
            <h2>Tại sao chọn chúng tôi?</h2>
            <p>Cam kết mang đến trải nghiệm mua sắm tuyệt vời và sản phẩm chất lượng cao</p>
        </div>
        
        <div class="features-grid-modern">
            <div class="feature-card-modern hover-lift animate-fadeInUp" style="animation-delay: 0s;">
                <div class="feature-icon-modern" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3 class="feature-title-modern">Miễn phí vận chuyển</h3>
                <p class="feature-description-modern">Giao hàng miễn phí toàn quốc cho đơn hàng từ 500.000đ. Nhanh chóng và an toàn.</p>
            </div>
            
            <div class="feature-card-modern hover-lift animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="feature-icon-modern" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-undo"></i>
                </div>
                <h3 class="feature-title-modern">Đổi trả 30 ngày</h3>
                <p class="feature-description-modern">Chính sách đổi trả linh hoạt trong 30 ngày. Không hài lòng? Chúng tôi hoàn tiền 100%.</p>
            </div>
            
            <div class="feature-card-modern hover-lift animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="feature-icon-modern" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-award"></i>
                </div>
                <h3 class="feature-title-modern">Chất lượng cao cấp</h3>
                <p class="feature-description-modern">Sử dụng chất liệu cao cấp nhập khẩu và công nghệ may tiên tiến. Đảm bảo độ bền và thẩm mỹ.</p>
            </div>
            
            <div class="feature-card-modern hover-lift animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="feature-icon-modern" style="background: linear-gradient(135deg, #ec4899, #be185d);">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="feature-title-modern">Hỗ trợ 24/7</h3>
                <p class="feature-description-modern">Đội ngũ tư vấn chuyên nghiệp luôn sẵn sàng hỗ trợ bạn mọi lúc mọi nơi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Modern Newsletter Section -->
<section class="section-modern" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="animate-fadeInLeft">
                    <h2 class="text-4xl font-bold mb-4" style="color: white;">
                        Luôn cập nhật xu hướng
                </h2>
                    <p class="text-lg" style="opacity: 0.9; color: white;">
                        Đăng ký nhận bản tin để không bỏ lỡ những bộ sưu tập mới nhất và ưu đãi độc quyền
                </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="animate-fadeInRight">
                    <form class="d-flex gap-3 flex-wrap">
                    <div style="flex: 1; min-width: 250px;">
                        <input type="email" 
                                   class="form-control" 
                               placeholder="Nhập địa chỉ email của bạn"
                                   style="
                                       background: rgba(255,255,255,0.1); 
                                       border: 2px solid rgba(255,255,255,0.3); 
                                       color: white;
                                       border-radius: var(--radius-lg);
                                       padding: var(--space-3) var(--space-4);
                                       font-weight: var(--font-medium);
                                   ">
                    </div>
                        <button type="submit" class="btn-modern btn-modern-outline" style="border-color: white; color: white;">
                        <i class="fas fa-paper-plane"></i>
                            Đăng ký
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Personalized Recommendations Section -->
@auth
<section class="recently-viewed-section">
    <div class="container">
        <div class="section-header-personalized">
            <h3 class="section-title-personalized">
                <div class="section-icon-personalized">
                    <i class="fas fa-user-check"></i>
                </div>
                Gợi ý dành riêng cho bạn
            </h3>
            <a href="{{ route('products.index') }}" class="view-all-link">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
            </div>
            
        <div class="recommendations-container" data-recommendations="personalized" data-limit="8">
            <!-- Personalized recommendations will be loaded here -->
            <div class="recommendations-loading">
                <div class="loading-spinner-recommendations"></div>
                    </div>
                </div>
            </div>
</section>
@endauth

<!-- Recently Viewed Section -->
<section class="recently-viewed-section">
    <div class="container">
        <div class="section-header-personalized">
            <h3 class="section-title-personalized">
                <div class="section-icon-personalized">
                    <i class="fas fa-history"></i>
                </div>
                @auth
                    Sản phẩm bạn đã xem
                @else
                    Sản phẩm đã xem gần đây
                @endauth
            </h3>
            </div>
            
        <div class="recommendations-container" data-recently-viewed="true" data-limit="6">
            <!-- Recently viewed products will be loaded here -->
        </div>
                    </div>
</section>

<!-- Trending Products Section -->
<section class="recently-viewed-section" style="background: var(--gray-50);">
    <div class="container">
        <div class="section-header-personalized">
            <h3 class="section-title-personalized">
                <div class="section-icon-personalized" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-fire"></i>
                </div>
                Xu hướng thời trang
            </h3>
            <a href="{{ route('products.index') }}?sort=popularity" class="view-all-link">
                Khám phá thêm <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="recommendations-container" data-recommendations="trending" data-limit="8">
            <!-- Trending recommendations will be loaded here -->
            <div class="recommendations-loading">
                <div class="loading-spinner-recommendations"></div>
            </div>
        </div>
    </div>
</section>

<!-- Footer được hiển thị từ layouts/app.blade.php -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modern Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe modern elements
    document.querySelectorAll('.animate-fadeInUp, .category-card-modern, .product-card-modern, .feature-card-modern').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });
    
    // Enhanced product card interactions
    document.querySelectorAll('.product-card-modern').forEach(card => {
        const img = card.querySelector('img');
        const overlay = card.querySelector('.product-overlay');
        
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = 'var(--shadow-2xl)';
            if (img) img.style.transform = 'scale(1.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'var(--shadow)';
            if (img) img.style.transform = 'scale(1)';
        });
    });
    
    // Enhanced category card interactions
    document.querySelectorAll('.category-card-modern').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-6px) scale(1.02)';
            this.style.boxShadow = 'var(--shadow-xl)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = 'var(--shadow)';
        });
    });
    
    // Quick add to cart functionality
    document.querySelectorAll('.quick-add-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            
            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
            this.disabled = true;
            
            // Show toast notification (simulated)
            setTimeout(() => {
                showToast('success', 'Đã thêm sản phẩm vào giỏ hàng!');
                this.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
                
                // Reset after delay
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 1500);
            }, 800);
        });
    });
    
    // Modern toast notifications
    function showToast(type, message) {
        // Remove existing toasts
        document.querySelectorAll('.toast-modern').forEach(t => t.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast-modern ${type}`;
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}" style="color: var(--${type === 'success' ? 'success' : 'danger'});"></i>
                <span style="font-weight: var(--font-medium);">${message}</span>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Show toast
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Auto hide
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.modern-hero');
        if (hero) {
            hero.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
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