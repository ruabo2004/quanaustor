@extends('admin.layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)
@section('page-title', 'Chi tiết đơn hàng #' . $order->id)
@section('breadcrumb', 'Quản lý / Đơn hàng / Chi tiết')

@section('content')
<div class="container-fluid">
<style>
/* PUMA ORDER DETAIL - COMPACT & MODERN DESIGN */
.puma-order-container {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.puma-order-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid rgba(255, 0, 0, 0.08);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(255, 0, 0, 0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.puma-order-card:hover {
    box-shadow: 0 8px 32px rgba(255, 0, 0, 0.12);
    transform: translateY(-2px);
}

.puma-card-header {
    background: linear-gradient(135deg, #ff0000 0%, #e53e3e 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-bottom: none;
}

.puma-card-body {
    padding: 1.5rem;
}

.puma-back-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    padding: 0.5rem 0.8rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.puma-back-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    text-decoration: none;
    transform: translateX(-3px);
}

.puma-order-title {
    color: white;
    font-size: 1.4rem;
    font-weight: 900;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: 'Inter', sans-serif;
}

.puma-status-form {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.puma-status-select {
    background: white;
    border: 2px solid rgba(255, 0, 0, 0.2);
    border-radius: 8px;
    padding: 0.5rem 0.8rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1a1a1a;
    transition: all 0.3s ease;
}

.puma-status-select:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
}

.puma-update-btn {
    background: linear-gradient(135deg, #ffd700, #ffb000);
    color: #1a1a1a;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.puma-update-btn:hover {
    background: linear-gradient(135deg, #ffb000, #ffd700);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.puma-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.2rem;
    margin-top: 1.5rem;
}

.puma-info-section {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border: 2px solid rgba(255, 0, 0, 0.05);
    border-radius: 12px;
    padding: 1.2rem;
    transition: all 0.3s ease;
}

.puma-info-section:hover {
    border-color: rgba(255, 0, 0, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(255, 0, 0, 0.1);
}

.puma-info-title {
    color: #ff0000;
    font-size: 0.95rem;
    font-weight: 800;
    margin-bottom: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
}

.puma-customer-info {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.puma-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 900;
    font-size: 1.1rem;
    box-shadow: 0 2px 8px rgba(255, 0, 0, 0.3);
}

.puma-customer-name {
    color: #1a1a1a;
    font-weight: 700;
    margin: 0;
    font-size: 1rem;
}

.puma-customer-email {
    color: #4a5568;
    font-size: 0.85rem;
    margin: 0;
    font-weight: 500;
}

.puma-status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
}

/* Order Status Badges */
.status-pending { background: linear-gradient(135deg, #ffd700, #ffb000); color: #1a1a1a; }
.status-processing { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }
.status-shipped { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
.status-completed { background: linear-gradient(135deg, #ff0000, #e53e3e); color: white; }
.status-cancelled { background: linear-gradient(135deg, #6b7280, #4b5563); color: white; }

/* Payment Status Badges */
.status-paid { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; }
.status-failed { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.status-refunded { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }

.puma-date-text {
    color: #4a5568;
    font-weight: 600;
    font-size: 0.95rem;
}

.puma-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(255, 0, 0, 0.05);
}

.puma-table {
    width: 100%;
    border-collapse: collapse;
}

.puma-table th {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
}

.puma-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 0, 0, 0.05);
    vertical-align: middle;
}

.puma-table tr:hover {
    background: rgba(255, 0, 0, 0.02);
}

.puma-product-info {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.puma-product-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid rgba(255, 0, 0, 0.1);
}

.puma-product-placeholder {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 1.2rem;
}

.puma-product-name {
    color: #1a1a1a;
    font-weight: 700;
    margin: 0;
    font-size: 0.95rem;
}

.puma-product-category {
    color: #4a5568;
    font-size: 0.8rem;
    margin: 0;
    font-weight: 500;
}

.puma-quantity {
    background: linear-gradient(135deg, #ffd700, #ffb000);
    color: #1a1a1a;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-block;
}

.puma-price {
    color: #ff0000;
    font-weight: 700;
    font-size: 0.95rem;
    font-family: 'Inter', sans-serif;
}

.puma-total-row {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    color: white;
}

.puma-total-row td {
    padding: 1.2rem;
    border: none;
    font-weight: 700;
    font-size: 1rem;
}

.puma-total-amount {
    font-size: 1.3rem;
    font-weight: 900;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.5px;
}

.puma-payment-btn {
    background: linear-gradient(135deg, #ffd700, #ffb000);
    color: #1a1a1a;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

.puma-payment-btn:hover {
    background: linear-gradient(135deg, #ffb000, #ffd700);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(255, 215, 0, 0.4);
}

.puma-updated-notice {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid rgba(34, 197, 94, 0.2);
    font-family: 'Inter', sans-serif;
}

@media (max-width: 768px) {
    .puma-order-container {
        padding: 0.8rem;
    }
    
    .puma-card-body {
        padding: 1rem;
    }
    
    .puma-info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .puma-status-form {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .puma-table-container {
        overflow-x: auto;
    }
    
    .puma-product-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<div class="puma-order-container">
    <!-- Order Info -->
    <div class="puma-order-card">
        <div class="puma-card-header">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <a href="{{ route('admin.orders') }}" class="puma-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <h2 class="puma-order-title">🛒 Đơn hàng #{{ $order->id }}</h2>
                </div>
                
                <!-- Status Update Form -->
                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="puma-status-form">
                    @csrf
                    @method('PUT')
                    <select name="status" class="puma-status-select">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Đã gửi</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <button type="submit" class="puma-update-btn">
                        <i class="fas fa-save"></i>
                        Cập nhật
                    </button>
                </form>
            </div>
        </div>
        
        <div class="puma-card-body">
            <div class="puma-info-grid">
                <!-- Customer Info -->
                <div class="puma-info-section">
                    <h3 class="puma-info-title">👤 Thông tin khách hàng</h3>
                    <div class="puma-customer-info">
                        <div class="puma-avatar">
                            {{ strtoupper(substr($order->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="puma-customer-name">{{ $order->user->name }}</p>
                            <p class="puma-customer-email">{{ $order->user->email }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Order Status -->
                <div class="puma-info-section">
                    <h3 class="puma-info-title">📊 Trạng thái đơn hàng</h3>
                    @php
                        $statusLabels = [
                            'pending' => 'Chờ xử lý',
                            'processing' => 'Đang xử lý',
                            'shipped' => 'Đã gửi',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ];
                    @endphp
                    <span class="puma-status-badge status-{{ $order->status }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
                
                <!-- Order Date -->
                <div class="puma-info-section">
                    <h3 class="puma-info-title">📅 Ngày đặt hàng</h3>
                    <p class="puma-date-text">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                
                <!-- Payment Method -->
                <div class="puma-info-section">
                    <h3 class="puma-info-title">💳 Phương thức thanh toán</h3>
                    @php
                        $paymentMethods = [
                            'cod' => '💵 Thanh toán khi nhận hàng',
                            'vnpay' => '🏧 VNPay',
                            'momo' => '📱 MoMo'
                        ];
                    @endphp
                    <p class="puma-date-text">{{ $paymentMethods[$order->payment_method] ?? $order->payment_method }}</p>
                </div>
                
                <!-- Payment Status -->
                <div class="puma-info-section">
                    <h3 class="puma-info-title">💰 Trạng thái thanh toán</h3>
                    @php
                        $paymentStatusLabels = [
                            'pending' => 'Chờ thanh toán',
                            'paid' => 'Đã thanh toán',
                            'failed' => 'Thanh toán thất bại',
                            'refunded' => 'Đã hoàn tiền'
                        ];
                    @endphp
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <span class="puma-status-badge status-{{ $order->payment_status }}">
                            {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
                        </span>
                        
                        @if($order->payment_method !== 'cod' && !$order->payment_status_updated)
                            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="payment_status" value="{{ $order->payment_status === 'pending' ? 'paid' : 'pending' }}">
                                <button type="submit" class="puma-payment-btn" 
                                        onclick="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái thanh toán? Hành động này chỉ có thể thực hiện 1 lần!')">
                                    @if($order->payment_status === 'pending')
                                        ✅ Đánh dấu đã thanh toán
                                    @else
                                        ⏳ Đánh dấu chưa thanh toán
                                    @endif
                                </button>
                            </form>
                        @elseif($order->payment_status_updated)
                            <small class="puma-updated-notice">
                                ✓ Đã cập nhật lúc {{ $order->payment_status_updated_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="puma-order-card">
        <div class="puma-card-header">
            <h3 class="puma-order-title">🛍️ Sản phẩm đã đặt</h3>
        </div>
        <div class="puma-card-body">
            <div class="puma-table-container">
                <table class="puma-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="puma-product-info">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="puma-product-image">
                                        @else
                                            <div class="puma-product-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="puma-product-name">{{ $item->product->name }}</p>
                                            <p class="puma-product-category">{{ $item->product->category->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="puma-quantity">{{ $item->quantity }}</span>
                                </td>
                                <td>
                                    <span class="puma-price">{{ number_format($item->price, 0, ',', '.') }} VNĐ</span>
                                </td>
                                <td>
                                    <span class="puma-price">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VNĐ</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="puma-total-row">
                            <td colspan="3" style="text-align: right;">Tổng cộng:</td>
                            <td>
                                <span class="puma-total-amount">{{ number_format($order->total, 0, ',', '.') }} VNĐ</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
