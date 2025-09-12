@extends('layouts.app')

@section('content')
<!-- Category Hero Section -->
<section class="puma-hero">
    <div class="puma-hero-overlay"></div>
    <div class="container position-relative" style="z-index: 10;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb puma-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="puma-breadcrumb-link">
                        <i class="fas fa-home"></i> TRANG CHỦ
                    </a>
                </li>
                <li class="breadcrumb-item active puma-breadcrumb-active">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="text-center">
            <h1 class="puma-hero-title">
                {{ $category->name }}
            </h1>
            <p class="puma-hero-subtitle">
                {{ $category->description ?? 'Bộ sưu tập quần âu chuyên nghiệp' }}
            </p>
            <div class="puma-hero-badge">
                <span class="puma-hero-badge-text">
                    <i class="fas fa-box"></i> {{ $products->count() }} SẢN PHẨM
                </span>
            </div>
            

        </div>
    </div>
</section>

<!-- Products Section -->
<section class="puma-products-section">
    <div class="container">
    
        <div class="row justify-content-center">
            @foreach($products as $product)
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
    
    @if($products->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-box-open" style="font-size: 64px; color: var(--puma-medium-grey); margin-bottom: 20px;"></i>
        <h3 style="color: var(--puma-medium-grey);">Chưa có sản phẩm trong danh mục này</h3>
        <p style="color: var(--puma-medium-grey);">Danh mục "{{ $category->name }}" hiện chưa có sản phẩm nào</p>
        
        <a href="{{ route('home') }}" class="puma-btn puma-btn-primary mt-3">
            <i class="fas fa-arrow-left"></i>
            VỀ TRANG CHỦ
        </a>
    </div>
    @endif
    
    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="puma-btn puma-btn-outline">
            <i class="fas fa-home"></i>
            VỀ TRANG CHỦ
        </a>
        <a href="{{ route('products.index') }}" class="puma-btn puma-btn-outline ms-2">
            <i class="fas fa-grid-3x3"></i>
            XEM TẤT CẢ SẢN PHẨM
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Quick size selector functionality
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
    
    // Size button selection
    document.querySelectorAll('.quick-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
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
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    });
    
    // Notification function
    function showNotification(type, message) {
        const existingNotifications = document.querySelectorAll('.custom-notification');
        existingNotifications.forEach(n => n.remove());
        
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
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>
@endsection
