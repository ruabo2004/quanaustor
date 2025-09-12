@extends('layouts.app')

@section('content')
<!-- Products Section with Search & Filter -->
<section class="puma-products-section">
    <div class="container">
        <div class="puma-section-title">
            <h2>TẤT CẢ SẢN PHẨM</h2>
            <p>Bộ sưu tập quần âu chuyên nghiệp</p>
        </div>

        <!-- Search & Filter Section -->
        <div class="search-filter-section mb-4">
            <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                <div class="row">
                    <!-- Search Box -->
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Tìm kiếm sản phẩm..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="category_id" onchange="submitFilter()">
                            <option value="all">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="col-md-2 mb-3">
                        <input type="number" class="form-control" name="min_price" 
                               placeholder="Giá từ" value="{{ request('min_price') }}"
                               onchange="submitFilter()">
                    </div>
                    <div class="col-md-2 mb-3">
                        <input type="number" class="form-control" name="max_price" 
                               placeholder="Đến" value="{{ request('max_price') }}"
                               onchange="submitFilter()">
                    </div>

                    <!-- Sort -->
                    <div class="col-md-2 mb-3">
                        <select class="form-select" name="sort" onchange="submitFilter()">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Giá thấp → cao</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao → thấp</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        </select>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" 
                                data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                            <i class="fas fa-filter"></i> Bộ lọc nâng cao
                        </button>
                    </div>
                </div>

                <div class="collapse" id="advancedFilters">
                    <div class="row mb-3">
                        <!-- Style Filter -->
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Kiểu dáng</label>
                            <select class="form-select" name="style" onchange="submitFilter()">
                                <option value="all">Tất cả</option>
                                @foreach($styles as $style)
                                    <option value="{{ $style }}" 
                                            {{ request('style') == $style ? 'selected' : '' }}>
                                        {{ $style }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Material Filter -->
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Chất liệu</label>
                            <select class="form-select" name="material" onchange="submitFilter()">
                                <option value="all">Tất cả</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material }}" 
                                            {{ request('material') == $material ? 'selected' : '' }}>
                                        {{ $material }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fit Filter -->
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Kiểu fit</label>
                            <select class="form-select" name="fit" onchange="submitFilter()">
                                <option value="all">Tất cả</option>
                                @foreach($fits as $fit)
                                    <option value="{{ $fit }}" 
                                            {{ request('fit') == $fit ? 'selected' : '' }}>
                                        {{ $fit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <div class="col-md-3 mb-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="results-info mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Hiển thị {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                        trong {{ $products->total() }} kết quả
                        @if(request('search'))
                            cho "<strong>{{ request('search') }}</strong>"
                        @endif
                    </small>
                </div>
                <div>
                    <a href="{{ route('products.bestsellers') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-fire"></i> Sản phẩm bán chạy
                    </a>
                </div>
            </div>
        </div>
    
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

                            @auth
                                @if(Auth::user()->isAdmin())
                                <!-- Admin Actions -->
                                <div class="d-flex gap-2 mt-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="puma-btn puma-btn-gold flex-fill"
                                       style="font-size: 10px; padding: 8px;">
                                        <i class="fas fa-edit"></i> SỬA
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="puma-btn puma-btn-primary w-100"
                                                style="font-size: 10px; padding: 8px; background: var(--puma-gradient-2);"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                            <i class="fas fa-trash"></i> XÓA
                                        </button>
                                    </form>
                                </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    
        @if($products->isEmpty())
            <div class="text-center py-5">
                <div style="width: 100px; height: 100px; background: var(--puma-gradient-1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; color: white;">
                    <i class="fas fa-search fa-3x"></i>
                </div>
                <h3 class="puma-text-black">KHÔNG TÌM THẤY SẢN PHẨM NÀO</h3>
                <p style="color: var(--puma-medium-grey); text-transform: uppercase; letter-spacing: 1px;">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                <a href="{{ route('products.index') }}" class="puma-btn puma-btn-primary puma-mt-20">
                    <i class="fas fa-redo"></i> XEM TẤT CẢ SẢN PHẨM
                </a>
            </div>
        @endif
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</section>
<script>
// Submit filter form automatically
function submitFilter() {
    document.getElementById('filterForm').submit();
}

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
            
            const addToCartBtn = sizeForm ? sizeForm.querySelector('button[type="submit"]') : null;
            
            // Reset size selection
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.classList.remove('btn-primary');
                sizeBtn.classList.add('btn-outline-secondary');
            });
            
            // Disable add to cart button
            if (addToCartBtn) addToCartBtn.disabled = true;
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
