@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background: none; padding: 0;">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" style="color: #6c757d; text-decoration: none;">
                    <i class="fas fa-home"></i> Trang chủ
                </a>
            </li>
            <li class="breadcrumb-item active" style="color: #dc3545; font-weight: 600;">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="puma-section-title text-center mb-5">
        <h2>{{ strtoupper($category->name) }}</h2>
        <p>{{ $category->description ?? 'Bộ sưu tập quần âu chuyên nghiệp' }}</p>
        <div style="background: #f8f9fa; padding: 10px; border-radius: 8px; display: inline-block; margin-top: 10px;">
            <small style="color: #495057; font-weight: 600;">
                <i class="fas fa-box"></i> {{ $category->products->count() }} sản phẩm | 
                <i class="fas fa-filter"></i> Hiển thị {{ $products->count() }} sản phẩm
            </small>
        </div>
    </div>
    
    <div class="puma-products-grid">
        @foreach($products as $product)
            <div class="puma-card">
                @if($product->image)
                    <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="puma-card-img">
                @else
                    <div class="puma-card-img" style="background: var(--puma-gradient-1); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                @endif
                
                <div class="puma-card-body">
                    <h3 class="puma-card-title">{{ $product->name }}</h3>
                    <p class="puma-card-text">{{ Str::limit($product->description, 100) }}</p>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="puma-price">{{ number_format($product->price) }} VNĐ</span>
                    </div>
                    
                                    <!-- Quick Size Selector -->
                <div class="quick-size-selector mb-2" style="display: none;">
                    <label style="font-size: 10px; color: var(--puma-medium-grey); margin-bottom: 4px; display: block;">CHỌN SIZE:</label>
                    <div class="d-flex gap-1 mb-2">
                        @if($product->sizes && $product->sizes->count() > 0)
                            @foreach($product->sizes->take(5) as $size)
                                <button type="button" class="quick-size-btn" 
                                        data-size="{{ $size->size }}" 
                                        data-size-id="{{ $size->id }}"
                                        data-price-adjustment="{{ $size->price_adjustment }}"
                                        style="width: 24px; height: 24px; border: 1px solid var(--puma-medium-grey); background: white; border-radius: 3px; font-size: 9px; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                                    {{ $size->size }}
                                </button>
                            @endforeach
                        @else
                            <button type="button" class="quick-size-btn" data-size="XL" data-size-id="" data-price-adjustment="0" style="width: 24px; height: 24px; border: 1px solid var(--puma-medium-grey); background: white; border-radius: 3px; font-size: 9px; font-weight: 700; cursor: pointer; transition: all 0.2s;">XL</button>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2 mb-2">
                        <a href="{{ route('products.show', $product->id) }}" class="puma-btn puma-btn-black flex-fill" style="justify-content: center;">
                            <i class="fas fa-eye"></i>
                            XEM
                        </a>
                        <button type="button" class="show-size-selector puma-btn puma-btn-gold flex-fill" style="justify-content: center;">
                            <i class="fas fa-plus"></i>
                            THÊM
                        </button>
                    </div>
                    
                    <!-- Hidden form for cart -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="quick-add-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="size" class="selected-size" value="">
                        <input type="hidden" name="size_id" class="selected-size-id" value="">
                        <div class="d-flex gap-2">
                            <button type="submit" class="puma-btn puma-btn-primary flex-fill" style="justify-content: center;">
                                <i class="fas fa-cart-plus"></i>
                                THÊM VÀO GIỎ
                            </button>
                            <button type="button" class="cancel-size-selector puma-btn puma-btn-black" style="justify-content: center;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>
                    
                    @auth
                    @if(Auth::user()->is_admin)
                    <div class="d-flex gap-1">
                        <a href="{{ route('products.edit', $product->id) }}" class="puma-btn puma-btn-gold flex-fill" style="padding: 4px 8px; font-size: 10px; justify-content: center;">
                            <i class="fas fa-edit"></i>
                            SỬA
                        </a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="puma-btn puma-btn-primary w-100" style="padding: 4px 8px; font-size: 10px; background: var(--puma-gradient-2); justify-content: center;" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                <i class="fas fa-trash"></i>
                                XÓA
                            </button>
                        </form>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
    
    @if($products->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-box-open" style="font-size: 64px; color: var(--puma-medium-grey); margin-bottom: 20px;"></i>
        <h3 style="color: var(--puma-medium-grey);">Chưa có sản phẩm trong danh mục này</h3>
        <p style="color: var(--puma-medium-grey);">Các sản phẩm mới sẽ được cập nhật sớm</p>
        <a href="{{ route('products.index') }}" class="puma-btn puma-btn-primary mt-3">
            <i class="fas fa-arrow-left"></i>
            VỀ TRANG SẢN PHẨM
        </a>
    </div>
    @endif
    
    <div class="text-center mt-4">
        <a href="{{ route('products.index') }}" class="puma-btn puma-btn-outline">
            <i class="fas fa-grid-3x3"></i>
            XEM TẤT CẢ SẢN PHẨM
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick size selector functionality
    document.querySelectorAll('.show-size-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.puma-card');
            const sizeSelector = card.querySelector('.quick-size-selector');
            const mainButtons = card.querySelector('.d-flex.gap-2.mb-2');
            const addForm = card.querySelector('.quick-add-form');
            
            // Show size selector and hide main buttons
            sizeSelector.style.display = 'block';
            mainButtons.style.display = 'none';
            addForm.style.display = 'block';
        });
    });
    
    // Cancel size selection
    document.querySelectorAll('.cancel-size-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.puma-card');
            const sizeSelector = card.querySelector('.quick-size-selector');
            const mainButtons = card.querySelector('.d-flex.gap-2.mb-2');
            const addForm = card.querySelector('.quick-add-form');
            
            // Hide size selector and show main buttons
            sizeSelector.style.display = 'none';
            mainButtons.style.display = 'flex';
            addForm.style.display = 'none';
            
            // Reset size selection
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.style.background = 'white';
                sizeBtn.style.color = 'var(--puma-black)';
                sizeBtn.style.borderColor = 'var(--puma-medium-grey)';
            });
        });
    });
    
    // Size button selection
    document.querySelectorAll('.quick-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.puma-card');
            const form = card.querySelector('.quick-add-form');
            const sizeInput = form.querySelector('.selected-size');
            const sizeIdInput = form.querySelector('.selected-size-id');
            
            // Reset all size buttons in this card
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.style.background = 'white';
                sizeBtn.style.color = 'var(--puma-black)';
                sizeBtn.style.borderColor = 'var(--puma-medium-grey)';
            });
            
            // Highlight selected size
            this.style.background = 'var(--puma-red)';
            this.style.color = 'white';
            this.style.borderColor = 'var(--puma-red)';
            
            // Update form values
            sizeInput.value = this.dataset.size;
            sizeIdInput.value = this.dataset.sizeId;
        });
    });
});
</script>
@endsection

                            @endforeach
                        @else
                            <button type="button" class="quick-size-btn" data-size="XL" data-size-id="" data-price-adjustment="0" style="width: 24px; height: 24px; border: 1px solid var(--puma-medium-grey); background: white; border-radius: 3px; font-size: 9px; font-weight: 700; cursor: pointer; transition: all 0.2s;">XL</button>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2 mb-2">
                        <a href="{{ route('products.show', $product->id) }}" class="puma-btn puma-btn-black flex-fill" style="justify-content: center;">
                            <i class="fas fa-eye"></i>
                            XEM
                        </a>
                        <button type="button" class="show-size-selector puma-btn puma-btn-gold flex-fill" style="justify-content: center;">
                            <i class="fas fa-plus"></i>
                            THÊM
                        </button>
                    </div>
                    
                    <!-- Hidden form for cart -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="quick-add-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="size" class="selected-size" value="">
                        <input type="hidden" name="size_id" class="selected-size-id" value="">
                        <div class="d-flex gap-2">
                            <button type="submit" class="puma-btn puma-btn-primary flex-fill" style="justify-content: center;">
                                <i class="fas fa-cart-plus"></i>
                                THÊM VÀO GIỎ
                            </button>
                            <button type="button" class="cancel-size-selector puma-btn puma-btn-black" style="justify-content: center;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>
                    
                    @auth
                    @if(Auth::user()->is_admin)
                    <div class="d-flex gap-1">
                        <a href="{{ route('products.edit', $product->id) }}" class="puma-btn puma-btn-gold flex-fill" style="padding: 4px 8px; font-size: 10px; justify-content: center;">
                            <i class="fas fa-edit"></i>
                            SỬA
                        </a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="puma-btn puma-btn-primary w-100" style="padding: 4px 8px; font-size: 10px; background: var(--puma-gradient-2); justify-content: center;" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                <i class="fas fa-trash"></i>
                                XÓA
                            </button>
                        </form>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>
        @endforeach
    </div>
    
    @if($products->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-box-open" style="font-size: 64px; color: var(--puma-medium-grey); margin-bottom: 20px;"></i>
        <h3 style="color: var(--puma-medium-grey);">Chưa có sản phẩm trong danh mục này</h3>
        <p style="color: var(--puma-medium-grey);">Các sản phẩm mới sẽ được cập nhật sớm</p>
        <a href="{{ route('products.index') }}" class="puma-btn puma-btn-primary mt-3">
            <i class="fas fa-arrow-left"></i>
            VỀ TRANG SẢN PHẨM
        </a>
    </div>
    @endif
    
    <div class="text-center mt-4">
        <a href="{{ route('products.index') }}" class="puma-btn puma-btn-outline">
            <i class="fas fa-grid-3x3"></i>
            XEM TẤT CẢ SẢN PHẨM
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick size selector functionality
    document.querySelectorAll('.show-size-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.puma-card');
            const sizeSelector = card.querySelector('.quick-size-selector');
            const mainButtons = card.querySelector('.d-flex.gap-2.mb-2');
            const addForm = card.querySelector('.quick-add-form');
            
            // Show size selector and hide main buttons
            sizeSelector.style.display = 'block';
            mainButtons.style.display = 'none';
            addForm.style.display = 'block';
        });
    });
    
    // Cancel size selection
    document.querySelectorAll('.cancel-size-selector').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.puma-card');
            const sizeSelector = card.querySelector('.quick-size-selector');
            const mainButtons = card.querySelector('.d-flex.gap-2.mb-2');
            const addForm = card.querySelector('.quick-add-form');
            
            // Hide size selector and show main buttons
            sizeSelector.style.display = 'none';
            mainButtons.style.display = 'flex';
            addForm.style.display = 'none';
            
            // Reset size selection
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.style.background = 'white';
                sizeBtn.style.color = 'var(--puma-black)';
                sizeBtn.style.borderColor = 'var(--puma-medium-grey)';
            });
        });
    });
    
    // Size button selection
    document.querySelectorAll('.quick-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.puma-card');
            const form = card.querySelector('.quick-add-form');
            const sizeInput = form.querySelector('.selected-size');
            const sizeIdInput = form.querySelector('.selected-size-id');
            
            // Reset all size buttons in this card
            card.querySelectorAll('.quick-size-btn').forEach(sizeBtn => {
                sizeBtn.style.background = 'white';
                sizeBtn.style.color = 'var(--puma-black)';
                sizeBtn.style.borderColor = 'var(--puma-medium-grey)';
            });
            
            // Highlight selected size
            this.style.background = 'var(--puma-red)';
            this.style.color = 'white';
            this.style.borderColor = 'var(--puma-red)';
            
            // Update form values
            sizeInput.value = this.dataset.size;
            sizeIdInput.value = this.dataset.sizeId;
        });
    });
});
</script>
@endsection
