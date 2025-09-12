@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<style>
    .checkout-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 0.5rem 0;
        position: relative;
    }
    
    .checkout-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="50" cy="50" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.1;
    }
    
    .checkout-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .checkout-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .checkout-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shimmer 3s infinite;
        pointer-events: none;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .checkout-title {
        color: white;
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }
    
    .checkout-subtitle {
        color: rgba(255,255,255,0.9);
        margin: 0.2rem 0 0 0;
        font-size: 0.8rem;
        position: relative;
        z-index: 1;
    }
    
    .progress-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0.5rem 0;
        padding: 0 0.5rem;
    }
    
    .step {
        display: flex;
        align-items: center;
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .step.active {
        color: #667eea;
        font-weight: 600;
    }
    
    .step-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.3rem;
        font-size: 0.6rem;
        border: 2px solid #dee2e6;
    }
    
    .step.active .step-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-color: #667eea;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .step-divider {
        width: 30px;
        height: 2px;
        background: #dee2e6;
        margin: 0 0.5rem;
    }
    
    .step.active + .step-divider {
        background: linear-gradient(90deg, #667eea, #dee2e6);
    }
    
    .section-card {
        background: white;
        border-radius: 8px;
        padding: 0.8rem;
        margin-bottom: 0.7rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .section-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    
    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
    }
    
    .section-title {
        color: #2c3e50;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.7rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .section-icon {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
    }
    
    .form-group {
        margin-bottom: 0.7rem;
        position: relative;
    }
    
    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 6px;
        padding: 0.5rem 0.6rem;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: white;
        transform: translateY(-1px);
    }
    
    .form-control:hover {
        border-color: #b8c6db;
        background: white;
    }
    
    .order-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 0.7rem;
        position: sticky;
        top: 0.5rem;
    }
    
    .order-item {
        display: flex;
        justify-content: between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }
    
    .order-item:hover {
        background: rgba(255, 255, 255, 0.5);
        border-radius: 8px;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .order-total {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 0.6rem;
        border-radius: 6px;
        margin-top: 0.6rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        font-size: 0.9rem;
    }
    
    .checkout-btn {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        border-radius: 20px;
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .checkout-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s;
    }
    
    .checkout-btn:hover::before {
        left: 100%;
    }
    
    .checkout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    }
    
    .back-btn {
        background: white;
        border: 2px solid #6c757d;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        color: #6c757d;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.6rem;
        font-size: 0.8rem;
    }
    
    .back-btn:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-1px);
        text-decoration: none;
    }
    
    .payment-info {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
        padding: 0.6rem;
        border-radius: 6px;
        margin-top: 0.6rem;
        text-align: center;
        font-size: 0.8rem;
    }
    
    .shipping-info {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: #212529;
        padding: 0.6rem;
        border-radius: 6px;
        margin-bottom: 0.6rem;
        text-align: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Payment Methods */
    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 15px;
    }

    .payment-option {
        position: relative;
    }

    .payment-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .payment-label {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .payment-label::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s;
    }

    .payment-label:hover::before {
        transform: translateX(100%);
    }

    .payment-label:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    }

    .payment-option input[type="radio"]:checked + .payment-label {
        border-color: #667eea;
        background: linear-gradient(135deg, #f8f9ff, #ffffff);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .payment-option input[type="radio"]:checked + .payment-label::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        animation: checkmark 0.3s ease-in-out;
    }

    @keyframes checkmark {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .payment-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .payment-icon:not(.vnpay):not(.momo) {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .payment-icon.vnpay,
    .payment-icon.momo {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .payment-icon img {
        width: 32px;
        height: 32px;
        object-fit: contain;
    }

    .payment-info {
        flex: 1;
    }

    .payment-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .payment-desc {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .payment-badge {
        margin-left: auto;
    }

    .payment-badge .badge {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
    
    @media (max-width: 768px) {
        .checkout-container {
            padding: 0.3rem;
        }
        
        .section-card {
            padding: 0.6rem;
        }
        
        .progress-steps {
            flex-direction: column;
            gap: 0.3rem;
            padding: 0.3rem;
        }
        
        .step-divider {
            width: 2px;
            height: 15px;
            margin: 0.2rem 0;
        }
        
        .order-summary {
            position: static;
            margin-top: 0.7rem;
        }
        
        .checkout-title {
            font-size: 1.1rem;
        }
        
        .checkout-subtitle {
            font-size: 0.7rem;
        }
        
        .section-title {
            font-size: 0.85rem;
        }
        
        .form-control {
            padding: 0.4rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>

<div class="checkout-container">
<div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="checkout-card">
                    <div class="checkout-header">
                        <h1 class="checkout-title">
                            <i class="fas fa-credit-card"></i> Thanh toán đơn hàng
                        </h1>
                        <p class="checkout-subtitle">Vui lòng điền thông tin giao hàng để hoàn tất đơn hàng</p>
                    </div>
                    
                    <!-- Progress Steps -->
                    <div class="progress-steps">
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <span>Giỏ hàng</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step active">
                            <div class="step-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <span>Thanh toán</span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span>Hoàn tất</span>
                        </div>
                    </div>
    <div class="row">
                        <!-- Customer Information Form -->
                        <div class="col-lg-7">
                            <div class="section-card">
                                <h2 class="section-title">
                                    <div class="section-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    Thông tin giao hàng
                                </h2>
                                
                                <form action="{{ route('orders.store') }}" method="POST" id="checkoutForm">
                @csrf
                                    
                                    <!-- Họ và tên -->
                                    <div class="form-group">
                                        <label for="customer_name" class="form-label">
                                            <i class="fas fa-user text-primary"></i> Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" 
                                               name="customer_name" 
                                               value="{{ old('customer_name', Auth::user()->name ?? '') }}" 
                                               placeholder="Nhập họ và tên đầy đủ"
                                               required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="customer_email" class="form-label">
                                            <i class="fas fa-envelope text-primary"></i> Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('customer_email') is-invalid @enderror" 
                                               id="customer_email" 
                                               name="customer_email" 
                                               value="{{ old('customer_email', Auth::user()->email ?? '') }}" 
                                               placeholder="email@example.com"
                                               required>
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Số điện thoại -->
                                    <div class="form-group">
                                        <label for="customer_phone" class="form-label">
                                            <i class="fas fa-phone text-primary"></i> Số điện thoại <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" 
                                               class="form-control @error('customer_phone') is-invalid @enderror" 
                                               id="customer_phone" 
                                               name="customer_phone" 
                                               value="{{ old('customer_phone') }}" 
                                               placeholder="Nhập số điện thoại">
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                    </div>

                                    <!-- Địa chỉ -->
                                    <div class="form-group">
                                        <label for="customer_address" class="form-label">
                                            <i class="fas fa-map-marker-alt text-primary"></i> Địa chỉ giao hàng <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                                  id="customer_address" 
                                                  name="customer_address" 
                                                  rows="4" 
                                                  placeholder="Nhập địa chỉ đầy đủ: số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                                  required>{{ old('customer_address') }}</textarea>
                                        @error('customer_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-muted">
                                            <i class="fas fa-truck"></i> Địa chỉ chi tiết giúp giao hàng nhanh chóng và chính xác
                                        </div>
                                    </div>

                                    <!-- Phương thức thanh toán -->
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-credit-card text-primary"></i> Phương thức thanh toán <span class="text-danger">*</span>
                                        </label>
                                        <div class="payment-methods">
                                            <!-- COD -->
                                            <div class="payment-option">
                                                <input type="radio" id="cod" name="payment_method" value="cod" checked>
                                                <label for="cod" class="payment-label">
                                                    <div class="payment-icon">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <div class="payment-info">
                                                        <div class="payment-title">Thanh toán COD</div>
                                                        <div class="payment-desc">Thanh toán khi nhận hàng</div>
                                                    </div>
                                                    <div class="payment-badge">
                                                        <span class="badge bg-success">Phổ biến</span>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- VNPAY -->
                                            <div class="payment-option">
                                                <input type="radio" id="vnpay" name="payment_method" value="vnpay">
                                                <label for="vnpay" class="payment-label">
                                                    <div class="payment-icon vnpay">
                                                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iOCIgZmlsbD0iIzAwNTFBNSIvPgo8cGF0aCBkPSJNMTIgMTJIMjhWMTZIMTJWMTJaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTIgMjBIMjhWMjRIMTJWMjBaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTIgMjhIMjRWMzJIMTJWMjhaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K" alt="VNPAY">
                                                    </div>
                                                    <div class="payment-info">
                                                        <div class="payment-title">VNPAY</div>
                                                        <div class="payment-desc">Thẻ ATM, Visa, MasterCard</div>
                                                    </div>
                                                    <div class="payment-badge">
                                                        <span class="badge bg-primary">An toàn</span>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- MOMO -->
                                            <div class="payment-option">
                                                <input type="radio" id="momo" name="payment_method" value="momo">
                                                <label for="momo" class="payment-label">
                                                    <div class="payment-icon momo">
                                                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iOCIgZmlsbD0iI0E1MDA4NSIvPgo8Y2lyY2xlIGN4PSIyMCIgY3k9IjIwIiByPSIxMiIgZmlsbD0id2hpdGUiLz4KPHN2ZyB4PSIxNCIgeT0iMTQiIHdpZHRoPSIxMiIgaGVpZ2h0PSIxMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjQTUwMDg1Ij4KICA8cGF0aCBkPSJNMTIgMkM2LjQ4IDIgMiA2LjQ4IDIgMTJzNC40OCAxMCAxMCAxMCAxMC00LjQ4IDEwLTEwUzE3LjUyIDIgMTIgMnptNSAxNmwtNS01LTUtNWwxLjQxLTEuNDFMMTIgMTAuMTdsMy41OS0zLjU5TDE3IDhsLTUgNS01IDV6Ii8+Cjwvc3ZnPgo8L3N2Zz4K" alt="MOMO">
                                                    </div>
                                                    <div class="payment-info">
                                                        <div class="payment-title">Ví MoMo</div>
                                                        <div class="payment-desc">Thanh toán bằng ví điện tử</div>
                                                    </div>
                                                    <div class="payment-badge">
                                                        <span class="badge bg-warning text-dark">Nhanh chóng</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @error('payment_method')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="d-grid">
                                        <button type="submit" class="checkout-btn" id="submitBtn">
                                            <i class="fas fa-lock"></i> <span id="submitText">Xác nhận đặt hàng</span>
                                        </button>
                                    </div>
                                    
                                    <div class="text-center">
                                        <a href="{{ route('cart.index') }}" class="back-btn">
                                            <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                                        </a>
                                    </div>
            </form>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="col-lg-5">
                            <div class="section-card">
                                <h2 class="section-title">
                                    <div class="section-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    Tóm tắt đơn hàng
                                </h2>
                                
                                <div class="order-summary">
                                    <!-- Shipping Info -->
                                    <div class="shipping-info">
                                        <i class="fas fa-shipping-fast"></i> <strong>MIỄN PHÍ VẬN CHUYỂN</strong>
                                        <br><small>Giao hàng nhanh toàn quốc</small>
                                    </div>
                                    
                                    @php $total = 0; @endphp
                                    @foreach($cartItems as $item)
                                        @php 
                                            $itemTotal = $item->price * $item->quantity;
                                            $total += $itemTotal;
                                        @endphp
                                        <div class="order-item">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary">Size {{ $item->size }}</span>
                                                    <span class="badge bg-secondary">x{{ $item->quantity }}</span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-success">{{ number_format($itemTotal) }} VNĐ</div>
                                                <small class="text-muted">{{ number_format($item->price) }} VNĐ/sp</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div class="order-total">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="mb-0">Tổng thanh toán:</h4>
                                                <small class="opacity-75">{{ count($cartItems) }} sản phẩm</small>
                                            </div>
                                            <h3 class="mb-0 fw-bold">{{ number_format($total) }} VNĐ</h3>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Info -->
                                    <div class="payment-info" id="paymentInfo">
                                        <h6 class="mb-2" id="paymentTitle">
                                            <i class="fas fa-money-bill-wave" id="paymentIcon"></i> <span id="paymentName">Thanh toán COD</span>
                                        </h6>
                                        <small id="paymentDesc">Thanh toán khi nhận hàng - An toàn & Tiện lợi</small>
                                    </div>
                                    
                                    <!-- Security Notice -->
                                    <div class="mt-3 p-2 bg-white rounded text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt text-success"></i>
                                            Thông tin của bạn được bảo mật tuyệt đối
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('checkoutForm');
    const inputs = form.querySelectorAll('input, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });
    

    
    // Smooth scroll to error
    const firstError = document.querySelector('.is-invalid');
    if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentInfo = {
        cod: {
            name: 'Thanh toán COD',
            desc: 'Thanh toán khi nhận hàng - An toàn & Tiện lợi',
            icon: 'fas fa-money-bill-wave',
            buttonText: 'Xác nhận đặt hàng'
        },
        vnpay: {
            name: 'VNPAY',
            desc: 'Thanh toán qua thẻ ATM, Visa, MasterCard',
            icon: 'fas fa-credit-card',
            buttonText: 'Thanh toán qua VNPAY'
        },
        momo: {
            name: 'Ví MoMo',
            desc: 'Thanh toán bằng ví điện tử MoMo',
            icon: 'fab fa-mobile',
            buttonText: 'Thanh toán qua MoMo'
        }
    };

    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            const selectedMethod = this.value;
            const info = paymentInfo[selectedMethod];
            
            // Update payment info in summary
            document.getElementById('paymentName').textContent = info.name;
            document.getElementById('paymentDesc').textContent = info.desc;
            document.getElementById('paymentIcon').className = info.icon;
            document.getElementById('submitText').textContent = info.buttonText;
            
            // Update button appearance
            const submitBtn = document.getElementById('submitBtn');
            if (selectedMethod === 'vnpay') {
                submitBtn.style.background = 'linear-gradient(135deg, #0051A5, #003d7a)';
            } else if (selectedMethod === 'momo') {
                submitBtn.style.background = 'linear-gradient(135deg, #A50085, #7a0064)';
            } else {
                submitBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            }
        });
    });
    
    // Loading animation on submit
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('.checkout-btn');
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        let loadingText = 'Đang xử lý...';
        if (selectedMethod === 'vnpay') {
            loadingText = 'Chuyển đến VNPAY...';
        } else if (selectedMethod === 'momo') {
            loadingText = 'Chuyển đến MoMo...';
        }
        
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
        submitBtn.disabled = true;
    });
});
</script>
@endsection
