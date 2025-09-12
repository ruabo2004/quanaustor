@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<style>
    /* PUMA ORDERS PAGE - MODERN BRIGHT THEME */
    .orders-container {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 1rem 0;
        position: relative;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .orders-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.3" fill="rgba(255,0,0,0.02)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.5;
        pointer-events: none;
    }
    
    .orders-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        backdrop-filter: blur(20px);
        border-radius: 16px;
        padding: 2rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 32px rgba(255, 0, 0, 0.1);
        border: 2px solid rgba(255, 0, 0, 0.1);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .orders-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 0, 0, 0.05), transparent);
        animation: shimmer 4s ease-in-out infinite;
        pointer-events: none;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .orders-title {
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 2rem;
        font-weight: 900;
        margin: 0;
        position: relative;
        z-index: 1;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-family: 'Inter', sans-serif;
    }
    
    .orders-subtitle {
        color: #4a5568;
        margin: 0.5rem 0 0 0;
        font-size: 1rem;
        position: relative;
        z-index: 1;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .order-card {
        background: #ffffff;
        backdrop-filter: blur(20px);
        border-radius: 16px;
        border: 2px solid rgba(255, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(255, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .order-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(255, 0, 0, 0.15);
        border-color: rgba(255, 0, 0, 0.2);
    }
    
    .order-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ff0000, #ffd700, #ff0000);
        background-size: 200% 100%;
        animation: borderGlow 4s ease-in-out infinite;
    }
    
    @keyframes borderGlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .order-header {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        padding: 1.5rem 2rem;
        border-bottom: 2px solid rgba(255, 0, 0, 0.08);
    }
    
    .order-id {
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 900;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Inter', sans-serif;
    }
    
    .order-date {
        color: #4a5568;
        font-size: 0.9rem;
        margin-top: 0.3rem;
        font-weight: 500;
    }
    
    .status-badge {
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.8rem;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Inter', sans-serif;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #ffd700, #ffb000);
        color: #1a1a1a;
        box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
    }
    
    .status-completed {
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        color: white;
        box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #2d3748, #1a202c);
        color: white;
        box-shadow: 0 6px 20px rgba(45, 55, 72, 0.4);
    }
    
    .order-body {
        padding: 2rem;
    }
    
    .info-section {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 2px solid rgba(255, 0, 0, 0.08);
        box-shadow: 0 4px 16px rgba(255, 0, 0, 0.05);
    }
    
    .info-title {
        color: #1a1a1a;
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Inter', sans-serif;
    }
    
    .info-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
    }
    
    .info-item {
        margin-bottom: 0.8rem;
        color: #2d3748;
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .info-label {
        font-weight: 700;
        color: #1a1a1a;
        display: inline-block;
        min-width: 120px;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        color: #4a5568;
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.2rem;
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        border-radius: 12px;
        margin-bottom: 0.8rem;
        border: 2px solid rgba(255, 0, 0, 0.05);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(255, 0, 0, 0.05);
    }
    
    .product-item:hover {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        transform: translateX(8px);
        border-color: rgba(255, 0, 0, 0.15);
        box-shadow: 0 4px 16px rgba(255, 0, 0, 0.1);
    }
    
    .product-info {
        flex: 1;
    }
    
    .product-name {
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.4rem;
        font-size: 1rem;
        font-family: 'Inter', sans-serif;
        letter-spacing: 0.3px;
    }
    
    .product-details {
        font-size: 0.85rem;
        color: #4a5568;
        font-weight: 500;
    }
    
    .size-badge {
        background: linear-gradient(135deg, #ffd700, #ffb000);
        color: #1a1a1a;
        padding: 0.3rem 0.8rem;
        border-radius: 25px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    }
    
    .product-price {
        font-weight: 900;
        color: #ff0000;
        font-size: 1.1rem;
        font-family: 'Inter', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .order-total {
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        color: white;
        padding: 1.5rem;
        border-radius: 16px;
        text-align: center;
        margin-top: 1rem;
        box-shadow: 0 8px 32px rgba(255, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .total-label {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        opacity: 0.95;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .total-amount {
        font-size: 1.8rem;
        font-weight: 900;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        font-family: 'Inter', sans-serif;
        letter-spacing: 1px;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: #ffffff;
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 2px solid rgba(255, 0, 0, 0.1);
        box-shadow: 0 8px 32px rgba(255, 0, 0, 0.1);
    }
    
    .empty-icon {
        font-size: 4rem;
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.5rem;
    }
    
    .empty-title {
        color: #1a1a1a;
        font-weight: 900;
        font-size: 1.8rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Inter', sans-serif;
    }
    
    .empty-text {
        color: #4a5568;
        margin-bottom: 2rem;
        font-size: 1.1rem;
        font-weight: 500;
    }
    
    .continue-shopping-btn {
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 32px rgba(255, 0, 0, 0.4);
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Inter', sans-serif;
    }
    
    .continue-shopping-btn:hover {
        color: white;
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(255, 0, 0, 0.4);
        text-decoration: none;
    }
    
    .orders-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: #ffffff;
        backdrop-filter: blur(20px);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        border: 2px solid rgba(255, 0, 0, 0.08);
        box-shadow: 0 8px 32px rgba(255, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 48px rgba(255, 0, 0, 0.15);
        border-color: rgba(255, 0, 0, 0.2);
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 900;
        background: linear-gradient(135deg, #ff0000, #e53e3e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif;
        letter-spacing: 1px;
    }
    
    .stat-label {
        color: #4a5568;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Inter', sans-serif;
    }
    
    @media (max-width: 768px) {
        .orders-container {
            padding: 0.8rem 0;
        }
        
        .orders-header {
            padding: 1.5rem 1rem;
            margin-bottom: 1.2rem;
        }
        
        .orders-title {
            font-size: 1.5rem;
        }
        
        .orders-subtitle {
            font-size: 0.9rem;
        }
        
        .order-header {
            padding: 1rem 1.2rem;
        }
        
        .order-body {
            padding: 1.2rem;
        }
        
        .info-section {
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .orders-stats {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.8rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .product-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.6rem;
            padding: 1rem;
        }
        
        .product-name {
            font-size: 0.9rem;
        }
        
        .product-details {
            font-size: 0.8rem;
        }
        
        .product-price {
            align-self: flex-end;
            font-size: 1rem;
        }
        
        .total-amount {
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
        }
        
        .info-title {
            font-size: 1rem;
        }
        
        .info-item {
            font-size: 0.85rem;
        }
        
        .info-label {
            font-size: 0.85rem;
            min-width: 100px;
        }
        
        .info-value {
            font-size: 0.85rem;
        }
        
        .empty-title {
            font-size: 1.4rem;
        }
        
        .empty-text {
            font-size: 1rem;
        }
        
        .continue-shopping-btn {
            padding: 0.8rem 1.5rem;
            font-size: 0.9rem;
        }
    }
</style>

<div class="orders-container">
<div class="container">
        <div class="orders-header">
            <h1 class="orders-title">
                <i class="fas fa-shopping-bag"></i> Đơn hàng của tôi
            </h1>
            <p class="orders-subtitle">Theo dõi và quản lý các đơn hàng của bạn</p>
        </div>
        
        @if($orders->count() > 0)
            <!-- Orders Statistics -->
            <div class="orders-stats">
                <div class="stat-card">
                    <div class="stat-number">{{ $orders->count() }}</div>
                    <div class="stat-label">Tổng đơn hàng</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $orders->where('status', 'completed')->count() }}</div>
                    <div class="stat-label">Đã hoàn thành</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $orders->where('status', 'pending')->count() }}</div>
                    <div class="stat-label">Đang xử lý</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ number_format($orders->sum('total'), 0, ',', '.') }}đ</div>
                    <div class="stat-label">Tổng giá trị</div>
                </div>
            </div>
            
            @foreach($orders as $order)
            <div class="order-card">
                <div class="order-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="order-id">
                                <i class="fas fa-receipt"></i> Đơn hàng #{{ $order->id }}
                            </div>
                            <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="status-badge status-{{ $order->status }}">
                                @if($order->status == 'pending')
                                    <i class="fas fa-clock"></i> Đang xử lý
                                @elseif($order->status == 'completed')
                                    <i class="fas fa-check-circle"></i> Hoàn thành
                                @else
                                    <i class="fas fa-times-circle"></i> Đã hủy
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="order-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="info-section">
                                <div class="info-title">
                                    <div class="info-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    Thông tin giao hàng
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Họ tên:</span>
                                    <span class="info-value">{{ $order->customer_name }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Email:</span>
                                    <a href="mailto:{{ $order->customer_email }}" class="info-value text-decoration-none">{{ $order->customer_email }}</a>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Điện thoại:</span>
                                    <a href="tel:{{ $order->customer_phone }}" class="info-value text-decoration-none">{{ $order->customer_phone }}</a>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Địa chỉ:</span>
                                    <div class="info-value mt-1">{{ $order->customer_address }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="info-section">
                                <div class="info-title">
                                    <div class="info-icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    Chi tiết đơn hàng
                                </div>
                                @if($order->items && $order->items->count() > 0)
                                    @foreach($order->items as $item)
                                    <div class="product-item">
                                        <div class="product-info">
                                            <div class="product-name">
                                                {{ $item->product->name ?? 'Sản phẩm không tồn tại' }}
                                                @if($item->size)
                                                    <span class="size-badge">{{ $item->size }}</span>
                                                @endif
                                            </div>
                                            <div class="product-details">Số lượng: {{ $item->quantity }}</div>
                                        </div>
                                        <div class="product-price">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                    <div class="order-total">
                                        <div class="total-label">Tổng cộng</div>
                                        <div class="total-amount">{{ number_format($order->total, 0, ',', '.') }}đ</div>
                                    </div>
                                @else
                                    <p class="text-muted">Không có sản phẩm nào trong đơn hàng này.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2 class="empty-title">Chưa có đơn hàng nào</h2>
                <p class="empty-text">Bạn chưa đặt đơn hàng nào. Hãy khám phá sản phẩm của chúng tôi!</p>
                <a href="{{ route('home') }}" class="continue-shopping-btn">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
            </div>
        @endif
    </div>
</div>
@endsection