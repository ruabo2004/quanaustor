<!-- Products Grid with Modern Design -->
<div class="products-grid-modern" style="
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-6);
    margin-top: var(--space-6);
">
    @forelse($products as $product)
        <div class="product-card-modern animate-fadeInUp" style="
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all var(--transition-base);
            border: 1px solid var(--gray-200);
        " onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'">
            
            <!-- Product Image -->
            <div style="position: relative; overflow: hidden; aspect-ratio: 1; background: var(--gray-100);">
                @if($product->image)
                    <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         style="
                             width: 100%; 
                             height: 100%; 
                             object-fit: cover;
                             transition: transform var(--transition-slow);
                         "
                         onmouseover="this.style.transform='scale(1.05)'"
                         onmouseout="this.style.transform='scale(1)'">
                @else
                    <div style="
                        width: 100%; 
                        height: 100%; 
                        background: var(--gray-200);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: var(--gray-500);
                        font-size: 3rem;
                    ">
                        <i class="fas fa-image"></i>
                    </div>
                @endif
                
                <!-- Quick Actions Overlay -->
                <div style="
                    position: absolute;
                    top: var(--space-3);
                    right: var(--space-3);
                    display: flex;
                    flex-direction: column;
                    gap: var(--space-2);
                    opacity: 0;
                    transition: opacity var(--transition-base);
                " class="quick-actions"
                   onmouseover="this.style.opacity='1'"
                   onmouseout="this.style.opacity='0'">
                    <a href="{{ route('products.show', $product) }}" 
                       style="
                           width: 40px;
                           height: 40px;
                           background: rgba(255, 255, 255, 0.9);
                           border-radius: var(--radius-full);
                           display: flex;
                           align-items: center;
                           justify-content: center;
                           color: var(--gray-700);
                           text-decoration: none;
                           backdrop-filter: blur(10px);
                           transition: all var(--transition-base);
                       "
                       title="Xem chi tiết"
                       onmouseover="this.style.background='white'; this.style.transform='scale(1.1)'"
                       onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='scale(1)'">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button type="button" 
                            style="
                                width: 40px;
                                height: 40px;
                                background: rgba(255, 255, 255, 0.9);
                                border: none;
                                border-radius: var(--radius-full);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                color: var(--gray-700);
                                cursor: pointer;
                                backdrop-filter: blur(10px);
                                transition: all var(--transition-base);
                            "
                            title="Yêu thích"
                            onmouseover="this.style.background='var(--danger)'; this.style.color='white'; this.style.transform='scale(1.1)'"
                            onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.color='var(--gray-700)'; this.style.transform='scale(1)'">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                
                <!-- Stock Status Badge -->
                @php
                    $totalStock = $product->sizes->sum('stock_quantity');
                    $availableSizes = $product->sizes->where('stock_quantity', '>', 0);
                @endphp
                
                @if($totalStock > 0)
                    @if($totalStock <= 10)
                        <div style="
                            position: absolute;
                            top: var(--space-3);
                            left: var(--space-3);
                            background: var(--warning);
                            color: white;
                            padding: var(--space-1) var(--space-2);
                            border-radius: var(--radius-sm);
                            font-size: 0.75rem;
                            font-weight: var(--font-bold);
                        ">
                            Sắp hết
                        </div>
                    @endif
                @else
                    <div style="
                        position: absolute;
                        top: var(--space-3);
                        left: var(--space-3);
                        background: var(--danger);
                        color: white;
                        padding: var(--space-1) var(--space-2);
                        border-radius: var(--radius-sm);
                        font-size: 0.75rem;
                        font-weight: var(--font-bold);
                    ">
                        Hết hàng
                    </div>
                @endif
            </div>
            
            <!-- Product Info -->
            <div style="padding: var(--space-4);">
                <!-- Category -->
                @if($product->category)
                    <div style="
                        background: var(--primary);
                        color: white;
                        padding: var(--space-1) var(--space-2);
                        border-radius: var(--radius-sm);
                        font-size: 0.75rem;
                        font-weight: var(--font-medium);
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        display: inline-block;
                        margin-bottom: var(--space-2);
                    ">
                        {{ $product->category->name }}
                    </div>
                @endif
                
                <!-- Product Name -->
                <h3 style="
                    font-size: 1.125rem;
                    font-weight: var(--font-bold);
                    color: var(--gray-900);
                    margin: 0 0 var(--space-2);
                    line-height: 1.4;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                ">
                    <a href="{{ route('products.show', $product) }}" 
                       style="color: inherit; text-decoration: none; transition: color var(--transition-base);"
                       onmouseover="this.style.color='var(--primary)'"
                       onmouseout="this.style.color='inherit'">
                        {{ $product->name }}
                    </a>
                </h3>
                
                <!-- Product Attributes -->
                <div style="display: flex; flex-wrap: wrap; gap: var(--space-1); margin-bottom: var(--space-3);">
                    @if($product->style)
                        <span style="
                            background: var(--gray-100);
                            color: var(--gray-700);
                            padding: 2px var(--space-2);
                            border-radius: var(--radius-sm);
                            font-size: 0.7rem;
                            font-weight: var(--font-medium);
                        ">
                            {{ $product->style }}
                        </span>
                    @endif
                    @if($product->material)
                        <span style="
                            background: var(--gray-100);
                            color: var(--gray-700);
                            padding: 2px var(--space-2);
                            border-radius: var(--radius-sm);
                            font-size: 0.7rem;
                            font-weight: var(--font-medium);
                        ">
                            {{ $product->material }}
                        </span>
                    @endif
                    @if($product->fit)
                        <span style="
                            background: var(--gray-100);
                            color: var(--gray-700);
                            padding: 2px var(--space-2);
                            border-radius: var(--radius-sm);
                            font-size: 0.7rem;
                            font-weight: var(--font-medium);
                        ">
                            {{ $product->fit }}
                        </span>
                    @endif
                </div>
                
                <!-- Available Sizes -->
                @if($availableSizes->count() > 0)
                    <div style="margin-bottom: var(--space-3);">
                        <div style="
                            font-size: 0.75rem;
                            color: var(--gray-600);
                            margin-bottom: var(--space-1);
                            font-weight: var(--font-medium);
                        ">
                            Kích cỡ có sẵn:
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: var(--space-1);">
                            @foreach($availableSizes->take(5) as $size)
                                <span style="
                                    background: var(--success);
                                    color: white;
                                    padding: 2px 6px;
                                    border-radius: var(--radius-sm);
                                    font-size: 0.7rem;
                                    font-weight: var(--font-bold);
                                    min-width: 24px;
                                    text-align: center;
                                ">
                                    {{ $size->size }}
                                </span>
                            @endforeach
                            @if($availableSizes->count() > 5)
                                <span style="
                                    background: var(--gray-200);
                                    color: var(--gray-600);
                                    padding: 2px 6px;
                                    border-radius: var(--radius-sm);
                                    font-size: 0.7rem;
                                ">
                                    +{{ $availableSizes->count() - 5 }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Price -->
                <div style="
                    font-size: 1.25rem;
                    font-weight: var(--font-black);
                    color: var(--primary);
                    margin-bottom: var(--space-4);
                ">
                    {{ number_format($product->price) }} VNĐ
                </div>
                
                <!-- Product Actions -->
                <div style="display: flex; gap: var(--space-2);">
                    <a href="{{ route('products.show', $product) }}" 
                       style="
                           flex: 1;
                           background: var(--primary);
                           color: white;
                           text-decoration: none;
                           border-radius: var(--radius-lg);
                           padding: var(--space-3);
                           text-align: center;
                           font-weight: var(--font-semibold);
                           transition: all var(--transition-base);
                           border: 2px solid var(--primary);
                       "
                       onmouseover="this.style.background='var(--primary-dark)'; this.style.borderColor='var(--primary-dark)'; this.style.transform='translateY(-1px)'"
                       onmouseout="this.style.background='var(--primary)'; this.style.borderColor='var(--primary)'; this.style.transform='translateY(0)'">
                        <i class="fas fa-eye me-2"></i>Xem chi tiết
                    </a>
                    
                    @if($totalStock > 0)
                        <button type="button" 
                                class="quick-add-to-cart"
                                data-product-id="{{ $product->id }}"
                                style="
                                    background: transparent;
                                    color: var(--primary);
                                    border: 2px solid var(--primary);
                                    border-radius: var(--radius-lg);
                                    padding: var(--space-3);
                                    cursor: pointer;
                                    transition: all var(--transition-base);
                                    min-width: 48px;
                                "
                                title="Thêm vào giỏ hàng"
                                onmouseover="this.style.background='var(--primary)'; this.style.color='white'; this.style.transform='scale(1.05)'"
                                onmouseout="this.style.background='transparent'; this.style.color='var(--primary)'; this.style.transform='scale(1)'">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    @else
                        <button type="button" 
                                style="
                                    background: var(--gray-200);
                                    color: var(--gray-500);
                                    border: 2px solid var(--gray-200);
                                    border-radius: var(--radius-lg);
                                    padding: var(--space-3);
                                    cursor: not-allowed;
                                    min-width: 48px;
                                "
                                disabled
                                title="Hết hàng">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <!-- No Products Found -->
        <div style="
            grid-column: 1 / -1;
            text-align: center;
            padding: var(--space-16);
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
        ">
            <div style="
                width: 100px;
                height: 100px;
                background: var(--gray-100);
                border-radius: var(--radius-full);
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto var(--space-6);
                color: var(--gray-400);
                font-size: 3rem;
            ">
                <i class="fas fa-search"></i>
            </div>
            <h3 style="
                font-size: 1.5rem;
                font-weight: var(--font-bold);
                color: var(--gray-900);
                margin-bottom: var(--space-3);
            ">
                Không tìm thấy sản phẩm nào
            </h3>
            <p style="
                color: var(--gray-600);
                margin-bottom: var(--space-6);
                font-size: 1.125rem;
            ">
                Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm
            </p>
            <a href="{{ route('products.index') }}" 
               style="
                   background: var(--primary);
                   color: white;
                   text-decoration: none;
                   border-radius: var(--radius-lg);
                   padding: var(--space-3) var(--space-6);
                   font-weight: var(--font-semibold);
                   transition: all var(--transition-base);
                   display: inline-block;
               "
               onmouseover="this.style.background='var(--primary-dark)'"
               onmouseout="this.style.background='var(--primary)'">
                <i class="fas fa-refresh me-2"></i>Xem tất cả sản phẩm
            </a>
        </div>
    @endforelse
</div>

<style>
/* Quick actions hover effect for parent card */
.product-card-modern:hover .quick-actions {
    opacity: 1 !important;
}

/* Animation for new products loading */
.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive grid adjustments */
@media (max-width: 768px) {
    .products-grid-modern {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)) !important;
        gap: var(--space-4) !important;
    }
    
    .product-card-modern .quick-actions {
        opacity: 1 !important; /* Always show on mobile */
    }
}

@media (max-width: 640px) {
    .products-grid-modern {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Quick add to cart functionality
document.querySelectorAll('.quick-add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.dataset.productId;
        quickAddToCart(productId, this);
    });
});

function quickAddToCart(productId, button) {
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    // Here you would typically make an AJAX call to add to cart
    // For now, just show a success message
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.background = 'var(--success)';
        button.style.borderColor = 'var(--success)';
        button.style.color = 'white';
        
        // Show toast notification
        if (typeof showToast === 'function') {
            showToast('Đã thêm vào giỏ hàng!', 'success');
        }
        
        // Reset button after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.style.background = 'transparent';
            button.style.borderColor = 'var(--primary)';
            button.style.color = 'var(--primary)';
            button.disabled = false;
        }, 2000);
    }, 1000);
}
</script>
