@extends('layouts.app')

@section('content')
<!-- Cart Hero Section -->
<section class="puma-hero">
    <div class="puma-hero-overlay"></div>
    <div class="container">
        <div class="text-center">
            <h1 class="puma-hero-title">
                <i class="fas fa-shopping-cart"></i> GIỎ HÀNG CỦA BẠN
            </h1>
            <p class="puma-hero-subtitle">
                Kiểm tra và hoàn tất đơn hàng của bạn
            </p>
        </div>
    </div>
</section>

<!-- Cart Content Section -->
<section class="puma-products-section">
    <div class="container">
        @if($cartItems->count() > 0)
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8 mb-4">
                    <div class="puma-card">
                        <div class="puma-card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-shopping-bag"></i> SẢN PHẨM TRONG GIỎ ({{ $cartItems->count() }})
                            </h3>
                        </div>
                        <div class="puma-card-body p-0">
                            @php $total = 0 @endphp
                            @foreach($cartItems as $cartItem)
                                @php $total += $cartItem->price * $cartItem->quantity @endphp
                                <div class="cart-item" data-cart-id="{{ $cartItem->id }}" data-product-id="{{ $cartItem->product_id }}">
                                    <div class="row align-items-center">
                                        <!-- Product Info -->
                                        <div class="col-md-5">
                                            <div class="product-info">
                                                @if($cartItem->product->image)
                                                    <img src="{{ filter_var($cartItem->product->image, FILTER_VALIDATE_URL) ? $cartItem->product->image : asset('storage/' . $cartItem->product->image) }}" 
                                                         alt="{{ $cartItem->product->name }}" 
                                                         class="product-image">
                                                @else
                                                    <div class="product-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                                <div class="product-details">
                                                    <h5 class="product-name">{{ $cartItem->product->name }}</h5>
                                                    <div class="size-display">
                                                        <span class="current-size">SIZE {{ $cartItem->size }}</span>
                                                        <button type="button" class="change-size-btn" data-cart-id="{{ $cartItem->id }}">
                                                            <i class="fas fa-edit"></i> Đổi size
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Size Selector (Hidden by default) -->
                                                    <div class="size-selector-cart" style="display: none;" data-cart-id="{{ $cartItem->id }}">
                                                        <label class="size-label">CHỌN SIZE MỚI:</label>
                                                        <div class="size-options-cart">
                                                            @foreach($cartItem->product->sizes as $size)
                                                                @if($size->stock_quantity > 0)
                                                                    <button type="button" 
                                                                            class="size-btn-cart {{ $cartItem->size == $size->size ? 'active' : '' }}"
                                                                            data-size="{{ $size->size }}"
                                                                            data-size-id="{{ $size->id }}"
                                                                            data-adjustment="{{ $size->price_adjustment }}"
                                                                            data-stock="{{ $size->stock_quantity }}">
                                                                        {{ $size->size }}
                                                                        @if($size->price_adjustment > 0)
                                                                            <small>+{{ number_format($size->price_adjustment) }}đ</small>
                                                                        @endif
                                                                    </button>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        <div class="size-actions-cart">
                                                            <button type="button" class="update-size-btn-cart" data-cart-id="{{ $cartItem->id }}" disabled>
                                                                <i class="fas fa-check"></i> Cập nhật
                                                            </button>
                                                            <button type="button" class="cancel-size-btn-cart" data-cart-id="{{ $cartItem->id }}">
                                                                <i class="fas fa-times"></i> Hủy
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if($cartItem->price_adjustment > 0)
                                                        <small class="price-adjustment">
                                                            +{{ number_format($cartItem->price_adjustment) }} VNĐ cho size {{ $cartItem->size }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quantity -->
                                        <div class="col-md-2 text-center">
                                            <div class="quantity-section">
                                                <label class="quantity-label">Số lượng</label>
                                                <div class="quantity-controls">
                                                    <button type="button" class="quantity-btn minus" data-action="decrease" data-cart-id="{{ $cartItem->id }}">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="quantity-value">{{ $cartItem->quantity }}</span>
                                                    <button type="button" class="quantity-btn plus" data-action="increase" data-cart-id="{{ $cartItem->id }}">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Unit Price -->
                                        <div class="col-md-2 text-center">
                                            <div class="price-section">
                                                <label class="price-label">Đơn giá</label>
                                                <div class="unit-price">{{ number_format($cartItem->price) }} VNĐ</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Total Price -->
                                        <div class="col-md-2 text-center">
                                            <div class="price-section">
                                                <label class="price-label">Thành tiền</label>
                                                <div class="total-price">{{ number_format($cartItem->price * $cartItem->quantity) }} VNĐ</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="col-md-1 text-center">
                                            <form action="{{ route('cart.remove', $cartItem->id) }}" method="POST" class="remove-form">
                                                @csrf
                                                <button type="submit" class="remove-btn" title="Xóa sản phẩm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>


                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="puma-card order-summary" style="margin-top: 30px;">
                        <div class="puma-card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-receipt"></i> TÓM TẮT ĐƠN HÀNG
                            </h4>
                        </div>
                        <div class="puma-card-body">
                            <div class="summary-row">
                                <span class="label">Tạm tính:</span>
                                <span class="value subtotal-amount">{{ number_format($total) }} VNĐ</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Phí vận chuyển:</span>
                                <span class="value text-success">Miễn phí</span>
                            </div>
                            
                            <!-- Discount Row (hidden by default) -->
                            <div class="summary-row discount-row" style="display: none;">
                                <span class="label text-success">Giảm giá (<span class="coupon-code"></span>):</span>
                                <span class="value text-success discount-amount">-0 VNĐ</span>
                            </div>
                            
                            <div class="summary-divider"></div>
                            <div class="summary-total">
                                <span class="label">Tổng cộng:</span>
                                <span class="value final-total">{{ number_format($total) }} VNĐ</span>
                            </div>
                            
                            <div class="checkout-actions">
                                <a href="{{ route('home') }}" class="puma-btn puma-btn-outline continue-shopping">
                                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                                </a>
                                <a href="{{ route('checkout') }}" class="puma-btn puma-btn-primary checkout-btn">
                                    <i class="fas fa-credit-card"></i> Thanh toán
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="puma-card mt-3">
                        <div class="puma-card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-tags"></i> Mã giảm giá
                            </h5>
                        </div>
                        <div class="puma-card-body">
                            <!-- Applied Coupon Display (hidden by default) -->
                            <div class="applied-coupon-display" style="display: none;">
                                <div class="alert alert-success d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Mã <span class="applied-coupon-code"></span></strong>
                                        <div class="small">Giảm <span class="applied-coupon-value"></span></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-coupon-btn">
                                        <i class="fas fa-times"></i> Bỏ
                                    </button>
                                </div>
                            </div>

                            <!-- Coupon Input (hidden when coupon is applied) -->
                            <div class="promo-code-section">
                                <div class="input-group">
                                    <input type="text" id="coupon-input" class="form-control" 
                                           placeholder="Nhập mã giảm giá" maxlength="20">
                                    <button type="button" id="apply-coupon-btn" class="btn btn-outline-primary">
                                        <i class="fas fa-tags"></i> Áp dụng
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <button type="button" id="show-coupons-btn" class="btn btn-link btn-sm p-0">
                                        <i class="fas fa-eye"></i> Xem mã giảm giá có sẵn
                                    </button>
                                </div>
                            </div>

                            <!-- Available Coupons Modal Trigger -->
                            <div class="available-coupons mt-3" style="display: none;">
                                <h6>Mã giảm giá có sẵn:</h6>
                                <div class="coupons-list"></div>
                            </div>
                        </div>
        </div>
        </div>
    </div>
    @else
            <!-- Empty Cart -->
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="empty-cart-title">Giỏ hàng của bạn đang trống</h3>
                <p class="empty-cart-text">Hãy khám phá các sản phẩm tuyệt vời của chúng tôi!</p>
                <a href="{{ route('home') }}" class="puma-btn puma-btn-primary">
                    <i class="fas fa-shopping-bag"></i> Bắt đầu mua sắm
                </a>
            </div>
    @endif
</div>
</section>

<style>
/* ====================================
   CART PAGE STYLES
   ==================================== */

.cart-item {
    padding: 25px;
    border-bottom: 1px solid var(--puma-light-grey);
    transition: var(--puma-transition);
    background: var(--puma-white);
}

.cart-item:hover {
    background: #f8f9fa;
}

.cart-item:last-child {
    border-bottom: none;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--puma-radius);
    border: 3px solid var(--puma-light-grey);
}

.product-placeholder {
    width: 80px;
    height: 80px;
    background: var(--puma-light-grey);
    border-radius: var(--puma-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--puma-grey);
    font-size: 1.5rem;
}

.product-details {
    flex: 1;
}

.product-name {
    font-weight: 700;
    color: var(--puma-black);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 8px 0;
    font-size: 1.1rem;
}

.size-display {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.current-size {
    background: var(--puma-gradient-2);
    color: var(--puma-white);
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.change-size-btn {
    background: none;
    border: none;
    color: var(--puma-primary);
    font-size: 11px;
    font-weight: 600;
    text-decoration: underline;
    cursor: pointer;
    transition: var(--puma-transition);
}

.change-size-btn:hover {
    color: var(--puma-primary-dark);
}

.price-adjustment {
    color: var(--puma-gold);
    font-weight: 600;
    display: block;
    font-size: 0.85rem;
}

.quantity-section,
.price-section {
    text-align: center;
}

.quantity-label,
.price-label {
    font-size: 0.8rem;
    color: var(--puma-grey);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 8px;
}

.quantity-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: var(--puma-light-grey);
    border-radius: 8px;
    padding: 8px;
}

.quantity-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: var(--puma-white);
    color: var(--puma-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--puma-transition);
    font-size: 0.8rem;
}

.quantity-btn:hover {
    background: var(--puma-primary);
    color: var(--puma-white);
}

.quantity-value {
    font-weight: 700;
    color: var(--puma-black);
    min-width: 30px;
    text-align: center;
}

.unit-price,
.total-price {
    font-weight: 700;
    color: var(--puma-red);
    font-size: 1.1rem;
}

.remove-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: var(--puma-red);
    color: var(--puma-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--puma-transition);
}

.remove-btn:hover {
    background: var(--puma-red-dark);
    transform: scale(1.1);
}

/* Order Summary */
.order-summary .puma-card-body {
    padding: 25px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.summary-row .label {
    color: var(--puma-grey);
    font-weight: 600;
}

.summary-row .value {
    font-weight: 700;
    color: var(--puma-black);
}

.summary-divider {
    height: 1px;
    background: var(--puma-light-grey);
    margin: 20px 0;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding: 15px;
    background: var(--puma-light-grey);
    border-radius: var(--puma-radius);
}

.summary-total .label {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--puma-black);
    text-transform: uppercase;
}

.summary-total .value {
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--puma-red);
}

.checkout-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.continue-shopping {
    order: 2;
}

.checkout-btn {
    order: 1;
    font-size: 1.1rem;
    padding: 15px;
}

/* Promo Code */
.promo-code-section {
    display: flex;
    gap: 10px;
}

.promo-code-section .form-control {
    flex: 1;
}

/* Size Selector Cart */
.size-selector-cart {
    margin-top: 15px;
    padding: 20px;
    background: #f8f9fa !important;
    border-radius: 12px;
    border: 3px solid #007bff !important;
    box-shadow: 0 4px 12px rgba(0,123,255,0.2) !important;
    position: relative;
}

.size-label {
    font-size: 0.9rem !important;
    color: #007bff !important;
    font-weight: 900 !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 15px;
    text-align: center;
    background: white;
    padding: 8px;
    border-radius: 6px;
    border: 2px solid #007bff;
}

.size-options-cart {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 15px;
}

.size-btn-cart {
    background: var(--puma-white);
    border: 2px solid var(--puma-light-grey);
    border-radius: var(--puma-radius);
    padding: 8px 12px;
    cursor: pointer;
    transition: var(--puma-transition);
    font-size: 0.9rem;
    font-weight: 600;
    min-width: 50px;
    text-align: center;
}

.size-btn-cart:hover {
    border-color: var(--puma-primary);
    background: var(--puma-primary);
    color: var(--puma-white);
}

.size-btn-cart.active {
    border-color: var(--puma-primary);
    background: var(--puma-primary);
    color: var(--puma-white);
}

.size-btn-cart small {
    display: block;
    font-size: 0.7rem;
    opacity: 0.8;
}

.size-actions-cart {
    display: flex !important;
    gap: 10px;
    margin-top: 15px;
    padding: 10px;
    background: rgba(0,0,0,0.05);
    border-radius: 8px;
    justify-content: space-between;
}

.update-size-btn-cart,
.cancel-size-btn-cart {
    padding: 8px 16px;
    border: none;
    border-radius: var(--puma-radius);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--puma-transition);
    display: flex;
    align-items: center;
    gap: 5px;
}

.update-size-btn-cart {
    background: #28a745 !important;
    color: white !important;
    border: 2px solid #28a745 !important;
    font-weight: 700 !important;
}

.update-size-btn-cart:disabled {
    background: #6c757d !important;
    color: #fff !important;
    border: 2px solid #6c757d !important;
    cursor: not-allowed;
    opacity: 0.6;
}

.update-size-btn-cart:not(:disabled):hover {
    background: #218838 !important;
    border-color: #218838 !important;
    transform: scale(1.05);
}

.cancel-size-btn-cart {
    background: #dc3545 !important;
    color: white !important;
    border: 2px solid #dc3545 !important;
    font-weight: 600 !important;
}

.cancel-size-btn-cart:hover {
    background: #c82333 !important;
    border-color: #c82333 !important;
    transform: scale(1.05);
}

/* Empty Cart */
.empty-cart {
    text-align: center;
    padding: 80px 20px;
}

.empty-cart-icon {
    font-size: 5rem;
    color: var(--puma-light-grey);
    margin-bottom: 30px;
}

.empty-cart-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--puma-black);
    margin-bottom: 15px;
    text-transform: uppercase;
}

.empty-cart-text {
    font-size: 1.1rem;
    color: var(--puma-grey);
    margin-bottom: 40px;
}

/* Responsive */
@media (max-width: 768px) {
    .cart-item {
        padding: 15px;
    }
    
    .product-info {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .cart-item .row > div {
        margin-bottom: 15px;
    }
    
    .checkout-actions {
        flex-direction: column;
    }
    
    .size-options {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded');
    
    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    console.log('CSRF token found:', csrfToken ? 'Yes' : 'No');
    
    // Quantity Controls
    const quantityBtns = document.querySelectorAll('.quantity-btn');
    console.log('Found quantity buttons:', quantityBtns.length);
    
    quantityBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Quantity button clicked');
            const action = this.dataset.action;
            const cartId = this.dataset.cartId;
            const quantityEl = this.parentElement.querySelector('.quantity-value');
            let currentQty = parseInt(quantityEl.textContent);
            
            console.log('Action:', action, 'Cart ID:', cartId, 'Current Qty:', currentQty);
            
            if (action === 'increase') {
                updateQuantity(cartId, currentQty + 1, quantityEl);
            } else if (action === 'decrease' && currentQty > 1) {
                updateQuantity(cartId, currentQty - 1, quantityEl);
            }
        });
    });
    
    // Change Size Button
    document.querySelectorAll('.change-size-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const sizeSelector = document.querySelector(`.size-selector-cart[data-cart-id="${cartId}"]`);
            const sizeDisplay = this.closest('.size-display');
            
            // Hide size display and show selector
            sizeDisplay.style.display = 'none';
            sizeSelector.style.display = 'block';
        });
    });
    
    // Size Selection in Cart
    document.querySelectorAll('.size-btn-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId = this.closest('.size-selector-cart').dataset.cartId;
            const sizeSelector = this.closest('.size-selector-cart');
            
            // Remove active from siblings
            sizeSelector.querySelectorAll('.size-btn-cart').forEach(b => b.classList.remove('active'));
            
            // Add active to clicked
            this.classList.add('active');
            
            // Enable update button
            const updateBtn = sizeSelector.querySelector('.update-size-btn-cart');
            updateBtn.disabled = false;
            updateBtn.dataset.selectedSize = this.dataset.size;
            updateBtn.dataset.selectedSizeId = this.dataset.sizeId;
            updateBtn.dataset.selectedAdjustment = this.dataset.adjustment;
        });
    });
    
    // Update Size
    const updateSizeBtns = document.querySelectorAll('.update-size-btn-cart');
    console.log('Found update size buttons:', updateSizeBtns.length);
    
    updateSizeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Update size button clicked');
            const cartId = this.dataset.cartId;
            const size = this.dataset.selectedSize;
            const sizeId = this.dataset.selectedSizeId;
            const adjustment = this.dataset.selectedAdjustment;
            
            console.log('Size update data:', {cartId, size, sizeId, adjustment});
            updateSize(cartId, size, sizeId, adjustment);
        });
    });
    
    // Cancel Size Change
    document.querySelectorAll('.cancel-size-btn-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartId = this.dataset.cartId;
            const sizeSelector = document.querySelector(`.size-selector-cart[data-cart-id="${cartId}"]`);
            const sizeDisplay = sizeSelector.previousElementSibling;
            
            // Hide selector and show display
            sizeSelector.style.display = 'none';
            sizeDisplay.style.display = 'flex';
            
            // Reset selection
            sizeSelector.querySelectorAll('.size-btn-cart').forEach(b => {
                b.classList.remove('active');
                if (b.dataset.size === sizeDisplay.querySelector('.current-size').textContent.replace('SIZE ', '')) {
                    b.classList.add('active');
                }
            });
            
            // Disable update button
            const updateBtn = sizeSelector.querySelector('.update-size-btn-cart');
            updateBtn.disabled = true;
        });
    });
    
    // Remove Item Confirmation
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                e.preventDefault();
            }
        });
    });
});

function updateQuantity(cartId, newQuantity, quantityEl) {
    console.log('updateQuantity called:', {cartId, newQuantity});
    
    fetch(`/cart/update-quantity/${cartId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: newQuantity })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            quantityEl.textContent = newQuantity;
            // Update prices
            const cartItem = quantityEl.closest('.cart-item');
            const totalPriceEl = cartItem.querySelector('.total-price');
            const unitPrice = parseInt(cartItem.querySelector('.unit-price').textContent.replace(/[^\d]/g, ''));
            totalPriceEl.textContent = (unitPrice * newQuantity).toLocaleString() + ' VNĐ';
            
            // Update summary
            updateOrderSummary();
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật số lượng: ' + error.message, 'error');
    });
}

function updateSize(cartId, size, sizeId, adjustment) {
    const updateBtn = document.querySelector(`.update-size-btn-cart[data-cart-id="${cartId}"]`);
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...';
    updateBtn.disabled = true;
    
    fetch(`/cart/update-size/${cartId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            size: size, 
            size_id: sizeId,
            price_adjustment: adjustment 
        })
    })
    .then(response => {
        console.log('Size update response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Size update response data:', data);
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Hide selector and show display
            const sizeSelector = document.querySelector(`.size-selector-cart[data-cart-id="${cartId}"]`);
            const sizeDisplay = sizeSelector.previousElementSibling;
            
            sizeSelector.style.display = 'none';
            sizeDisplay.style.display = 'flex';
            
            // Update display
            const currentSizeSpan = sizeDisplay.querySelector('.current-size');
            currentSizeSpan.textContent = `SIZE ${size}`;
            
            // Reload page after short delay to update prices
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message, 'error');
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Size update error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật size: ' + error.message, 'error');
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    });
}

function updateOrderSummary() {
    let total = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const totalPrice = parseInt(item.querySelector('.total-price').textContent.replace(/[^\d]/g, ''));
        total += totalPrice;
    });
    
    document.querySelectorAll('.summary-total .value').forEach(el => {
        el.textContent = total.toLocaleString() + ' VNĐ';
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// ================================
// COUPON FUNCTIONALITY
// ================================

// Global variable to store current cart total
let currentCartTotal = {{ $total }};

// Apply coupon
document.getElementById('apply-coupon-btn').addEventListener('click', function() {
    const couponCode = document.getElementById('coupon-input').value.trim();
    
    if (!couponCode) {
        showNotification('Vui lòng nhập mã giảm giá', 'error');
        return;
    }
    
    const originalText = this.innerHTML;
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...';
    this.disabled = true;
    
    fetch('{{ route("coupon.validate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            coupon_code: couponCode,
            cart_total: currentCartTotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCouponApplied(data.coupon);
            showNotification(data.message, 'success');
            document.getElementById('coupon-input').value = '';
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi áp dụng mã giảm giá', 'error');
    })
    .finally(() => {
        this.innerHTML = originalText;
        this.disabled = false;
    });
});

// Remove coupon
document.querySelector('.remove-coupon-btn').addEventListener('click', function() {
    fetch('{{ route("coupon.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideCouponApplied();
            showNotification(data.message, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi hủy mã giảm giá', 'error');
    });
});

// Show available coupons
document.getElementById('show-coupons-btn').addEventListener('click', function() {
    const couponsSection = document.querySelector('.available-coupons');
    
    if (couponsSection.style.display === 'none') {
        loadAvailableCoupons();
        couponsSection.style.display = 'block';
        this.innerHTML = '<i class="fas fa-eye-slash"></i> Ẩn mã giảm giá';
    } else {
        couponsSection.style.display = 'none';
        this.innerHTML = '<i class="fas fa-eye"></i> Xem mã giảm giá có sẵn';
    }
});

// Enter key support for coupon input
document.getElementById('coupon-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('apply-coupon-btn').click();
    }
});

function showCouponApplied(coupon) {
    // Update applied coupon display
    document.querySelector('.applied-coupon-code').textContent = coupon.code;
    document.querySelector('.applied-coupon-value').textContent = coupon.formatted_discount;
    
    // Show applied coupon section
    document.querySelector('.applied-coupon-display').style.display = 'block';
    
    // Hide coupon input section
    document.querySelector('.promo-code-section').style.display = 'none';
    
    // Update order summary
    updateOrderSummaryWithDiscount(coupon.discount_amount);
}

function hideCouponApplied() {
    // Hide applied coupon section
    document.querySelector('.applied-coupon-display').style.display = 'none';
    
    // Show coupon input section
    document.querySelector('.promo-code-section').style.display = 'block';
    
    // Reset order summary
    resetOrderSummary();
    
    // Hide available coupons if shown
    document.querySelector('.available-coupons').style.display = 'none';
    document.getElementById('show-coupons-btn').innerHTML = '<i class="fas fa-eye"></i> Xem mã giảm giá có sẵn';
}

function updateOrderSummaryWithDiscount(discountAmount) {
    // Show discount row
    const discountRow = document.querySelector('.discount-row');
    discountRow.style.display = 'flex';
    
    // Update discount amount
    document.querySelector('.discount-amount').textContent = '-' + discountAmount.toLocaleString() + ' VNĐ';
    document.querySelector('.coupon-code').textContent = document.querySelector('.applied-coupon-code').textContent;
    
    // Update final total
    const newTotal = currentCartTotal - discountAmount;
    document.querySelector('.final-total').textContent = newTotal.toLocaleString() + ' VNĐ';
}

function resetOrderSummary() {
    // Hide discount row
    document.querySelector('.discount-row').style.display = 'none';
    
    // Reset final total to original
    document.querySelector('.final-total').textContent = currentCartTotal.toLocaleString() + ' VNĐ';
}

function loadAvailableCoupons() {
    const couponsList = document.querySelector('.coupons-list');
    couponsList.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
    
    fetch('{{ route("coupons.available") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.coupons.length > 0) {
            couponsList.innerHTML = '';
            data.coupons.forEach(coupon => {
                const couponElement = createCouponElement(coupon);
                couponsList.appendChild(couponElement);
            });
        } else {
            couponsList.innerHTML = '<div class="text-muted">Không có mã giảm giá nào khả dụng</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        couponsList.innerHTML = '<div class="text-danger">Có lỗi xảy ra khi tải mã giảm giá</div>';
    });
}

function createCouponElement(coupon) {
    const div = document.createElement('div');
    div.className = 'coupon-item p-2 border rounded mb-2';
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-primary">${coupon.code}</strong>
                <div class="small text-muted">${coupon.name}</div>
                <div class="small">Giảm ${coupon.formatted_value}</div>
                ${coupon.formatted_minimum ? `<div class="small text-warning">Tối thiểu: ${coupon.formatted_minimum}</div>` : ''}
                <div class="small text-info">HSD: ${coupon.end_date}</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary use-coupon-btn" data-code="${coupon.code}">
                Sử dụng
            </button>
        </div>
    `;
    
    // Add click handler for use button
    div.querySelector('.use-coupon-btn').addEventListener('click', function() {
        document.getElementById('coupon-input').value = coupon.code;
        document.getElementById('apply-coupon-btn').click();
    });
    
    return div;
}

// Check for applied coupon on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("coupon.applied") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCouponApplied(data.coupon);
        }
    })
    .catch(error => {
        console.log('No applied coupon found');
    });
});

</script>
@endsection