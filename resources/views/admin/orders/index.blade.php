@extends('admin.layouts.admin')

@section('title', 'Quản Lý Đơn Hàng')
@section('page-title', 'QUẢN LÝ ĐƠN HÀNG')
@section('breadcrumb', 'Quản Lý / Đơn Hàng')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">🛒 DANH SÁCH ĐƠN HÀNG</h3>
            <div class="d-flex gap-2">
                <select class="form-select" style="width: auto;">
                    <option>Tất cả trạng thái</option>
                    <option>Đang chờ</option>
                    <option>Đã xác nhận</option>
                    <option>Đang giao</option>
                    <option>Hoàn thành</option>
                    <option>Đã hủy</option>
                </select>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $orders->total() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Tổng Đơn Hàng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $orders->where('status', 'pending')->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Đang Chờ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #10b981, #065f46); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $orders->where('status', 'completed')->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Hoàn Thành</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #059669, #047857); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ number_format($orders->where('payment_status', 'paid')->sum('total')) }}đ</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Tổng Doanh Thu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                        <tr>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Đơn Hàng</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Khách Hàng</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Tổng Tiền</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Thanh Toán</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Trạng Thái</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Ngày Tạo</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div>
                                        <span style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; font-family: monospace;">
                                            #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                            {{ $order->created_at->format('H:i d/m/Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-weight: 700; color: white; font-size: 14px;">
                                            {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1f2937; font-size: 13px;">{{ $order->user->name }}</div>
                                            <div style="font-size: 11px; color: #6b7280;">{{ $order->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #059669; font-size: 16px;">
                                        {{ number_format($order->total) }}đ
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    @if($order->payment_method == 'cod')
                                        <span style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 2px 6px; border-radius: 6px; font-size: 9px; font-weight: 600; display: inline-block;">
                                            COD
                                        </span>
                                    @elseif($order->payment_method == 'momo')
                                        <span style="background: linear-gradient(135deg, #ec4899, #be185d); color: white; padding: 2px 6px; border-radius: 6px; font-size: 9px; font-weight: 600; display: inline-block;">
                                            MOMO
                                        </span>
                                    @elseif($order->payment_method == 'vnpay')
                                        <span style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 2px 6px; border-radius: 6px; font-size: 9px; font-weight: 600; display: inline-block;">
                                            VNPAY
                                        </span>
                                    @endif
                                    
                                    <div style="margin-top: 4px;">
                                        @if($order->payment_status == 'paid')
                                            <span style="background: #10b981; color: white; padding: 2px 5px; border-radius: 6px; font-size: 8px; font-weight: 600; display: inline-block;">
                                                ✓ Đã TT
                                            </span>
                                        @else
                                            <span style="background: #f59e0b; color: white; padding: 2px 5px; border-radius: 6px; font-size: 8px; font-weight: 600; display: inline-block;">
                                                ⏳ Chưa TT
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    @if($order->status == 'pending')
                                        <span style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-clock" style="font-size: 8px; margin-right: 3px;"></i>Chờ
                                        </span>
                                    @elseif($order->status == 'confirmed')
                                        <span style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-check" style="font-size: 8px; margin-right: 3px;"></i>Xác nhận
                                        </span>
                                    @elseif($order->status == 'shipping')
                                        <span style="background: linear-gradient(135deg, #8b5cf6, #5b21b6); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-truck" style="font-size: 8px; margin-right: 3px;"></i>Giao
                                        </span>
                                    @elseif($order->status == 'completed')
                                        <span style="background: linear-gradient(135deg, #059669, #047857); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-check-circle" style="font-size: 8px; margin-right: 3px;"></i>Xong
                                        </span>
                                    @elseif($order->status == 'cancelled')
                                        <span style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-times" style="font-size: 8px; margin-right: 3px;"></i>Hủy
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; color: #6b7280; font-weight: 500;">
                                    <i class="fas fa-calendar me-2" style="color: #e74c3c;"></i>{{ $order->created_at->format('d/m/Y') }}
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status != 'completed' && $order->status != 'cancelled')
                                            <div class="dropdown">
                                                <button style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 8px 12px; border-radius: 8px; border: none; font-size: 12px;" data-bs-toggle="dropdown">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($order->status == 'pending')
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">Xác nhận đơn</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'cancelled')">Hủy đơn</a></li>
                                                    @elseif($order->status == 'confirmed')
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'shipping')">Bắt đầu giao</a></li>
                                                    @elseif($order->status == 'shipping')
                                                        <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 'completed')">Hoàn thành</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 50px;">
                                    <div style="color: #9ca3af;">
                                        <i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: 15px; color: #d1d5db;"></i>
                                        <h5 style="color: #6b7280; font-weight: 600;">Chưa Có Đơn Hàng Nào</h5>
                                        <p style="color: #9ca3af; margin: 0;">Đơn hàng sẽ hiển thị ở đây khi khách hàng đặt mua</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* PUMA PAGINATION - MODERN DESIGN */
.pagination {
    gap: 8px;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    color: #ff0000;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border: 2px solid rgba(255, 0, 0, 0.2);
    padding: 12px 18px;
    border-radius: 12px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(255, 0, 0, 0.1);
    min-width: 50px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.pagination .page-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 0, 0.1), transparent);
    transition: left 0.5s ease;
}

.pagination .page-link:hover::before {
    left: 100%;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    color: white;
    border-color: #ff0000;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    border-color: #ff0000;
    color: white;
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.4);
    transform: translateY(-1px);
}

.pagination .page-item.disabled .page-link {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #6c757d;
    border-color: #dee2e6;
    box-shadow: none;
    cursor: not-allowed;
}

.pagination .page-item.disabled .page-link:hover {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #6c757d;
    transform: none;
    box-shadow: none;
}

/* Previous/Next Buttons Special Styling */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-size: 14px;
    padding: 12px 20px;
    font-weight: 900;
    background: linear-gradient(135deg, #ffd700, #ffb000);
    color: #1a1a1a;
    border-color: #ffd700;
}

.pagination .page-item:first-child .page-link:hover,
.pagination .page-item:last-child .page-link:hover {
    background: linear-gradient(135deg, #ffb000, #ffd700);
    color: #1a1a1a;
    border-color: #ffb000;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .pagination .page-link {
        padding: 10px 14px;
        font-size: 12px;
        min-width: 40px;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        padding: 10px 16px;
    }
}

.dropdown-menu {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
}

.dropdown-item {
    padding: 8px 15px;
    font-size: 12px;
    font-weight: 600;
}

.dropdown-item:hover {
    background-color: #f8fafc;
    color: #e74c3c;
}
</style>

<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng này?')) {
        // Create a form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/orders/${orderId}/status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection