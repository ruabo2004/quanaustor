@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Tổng quan hệ thống')

@section('content')
<!-- Welcome Section -->
<div class="card" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-left: 5px solid var(--puma-red); margin-bottom: 30px; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <div class="card-body" style="color: #2d3748; position: relative; z-index: 2;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h1 style="font-size: 32px; font-weight: 700; margin: 0 0 10px 0; color: #2d3748; letter-spacing: -1px; font-family: 'Inter', sans-serif;">
                    Chào mừng trở lại, {{ Auth::user()->name }}! 👋
                </h1>
                <p style="font-size: 16px; margin: 0; font-weight: 500; color: #4a5568; font-family: 'Inter', sans-serif;">
                    Hôm nay là {{ now()->format('d/m/Y') }} - Bạn có {{ $stats['total_orders'] }} đơn hàng cần xử lý
                </p>
            </div>
            <div style="display: none;" class="d-md-block">
                <div style="width: 120px; height: 120px; background: linear-gradient(135deg, var(--puma-red), #e53e3e); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(255,0,0,0.3);">
                    <i class="fas fa-chart-line" style="font-size: 60px; color: white;"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Background Animation -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.05) 50%, transparent 70%); animation: shine 3s ease-in-out infinite; z-index: 1;"></div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card" style="background: white; border-left: 5px solid var(--puma-red); box-shadow: 0 4px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden; border-radius: 12px;">
        <div class="stat-header" style="padding: 25px;">
            <div>
                <div class="stat-number" style="color: #2d3748; font-weight: 700; font-size: 2.5rem; font-family: 'Inter', sans-serif;">{{ number_format($stats['total_users']) }}</div>
                <div class="stat-label" style="color: #4a5568; font-weight: 600; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Tổng người dùng</div>
                <div class="stat-change positive" style="color: var(--puma-red); font-weight: 600; font-family: 'Inter', sans-serif;">
                    <i class="fas fa-arrow-up"></i> +12% so với tháng trước
                </div>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--puma-red), #e53e3e); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 15px rgba(255,0,0,0.3);">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div style="position: absolute; top: 0; right: 0; width: 100px; height: 100%; background: linear-gradient(45deg, transparent 60%, rgba(255,0,0,0.05) 100%);"></div>
    </div>

    <div class="stat-card" style="background: white; border-left: 5px solid var(--puma-gold); box-shadow: 0 4px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden; border-radius: 12px;">
        <div class="stat-header" style="padding: 25px;">
            <div>
                <div class="stat-number" style="color: #2d3748; font-weight: 700; font-size: 2.5rem; font-family: 'Inter', sans-serif;">{{ number_format($stats['total_products']) }}</div>
                <div class="stat-label" style="color: #4a5568; font-weight: 600; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Tổng sản phẩm</div>
                <div class="stat-change positive" style="color: var(--puma-gold); font-weight: 600; font-family: 'Inter', sans-serif;">
                    <i class="fas fa-arrow-up"></i> +8% sản phẩm mới
                </div>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--puma-gold), #e6c200); color: #2d3748; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div style="position: absolute; top: 0; right: 0; width: 100px; height: 100%; background: linear-gradient(45deg, transparent 60%, rgba(255,215,0,0.1) 100%);"></div>
    </div>

    <div class="stat-card" style="background: white; border-left: 5px solid #2d3748; box-shadow: 0 4px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden; border-radius: 12px;">
        <div class="stat-header" style="padding: 25px;">
            <div>
                <div class="stat-number" style="color: #2d3748; font-weight: 700; font-size: 2.5rem; font-family: 'Inter', sans-serif;">{{ number_format($stats['total_orders']) }}</div>
                <div class="stat-label" style="color: #4a5568; font-weight: 600; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Tổng đơn hàng</div>
                <div class="stat-change positive" style="color: #2d3748; font-weight: 600; font-family: 'Inter', sans-serif;">
                    <i class="fas fa-arrow-up"></i> +15% tăng trưởng
                </div>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, #2d3748, #4a5568); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 15px rgba(45, 55, 72, 0.3);">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div style="position: absolute; top: 0; right: 0; width: 100px; height: 100%; background: linear-gradient(45deg, transparent 60%, rgba(0,0,0,0.05) 100%);"></div>
    </div>

    <div class="stat-card" style="background: white; border-left: 5px solid var(--puma-red); box-shadow: 0 4px 20px rgba(0,0,0,0.1); position: relative; overflow: hidden; border-radius: 12px;">
        <div class="stat-header" style="padding: 25px;">
            <div>
                <div class="stat-number" style="color: #2d3748; font-weight: 700; font-size: 2.5rem; font-family: 'Inter', sans-serif;">{{ number_format($stats['revenue']/1000000, 1) }}M</div>
                <div class="stat-label" style="color: #4a5568; font-weight: 600; font-family: 'Inter', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Doanh thu (VNĐ)</div>
                <div class="stat-change positive" style="color: var(--puma-red); font-weight: 600; font-family: 'Inter', sans-serif;">
                    <i class="fas fa-arrow-up"></i> +23% tăng trưởng
                </div>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--puma-red), #e53e3e); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 15px rgba(255,0,0,0.3);">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div style="position: absolute; top: 0; right: 0; width: 100px; height: 100%; background: linear-gradient(45deg, transparent 60%, rgba(255,0,0,0.05) 100%);"></div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="{{ route('admin.products.create') }}" class="quick-action" style="border-left: 4px solid var(--puma-red);">
        <div class="quick-action-icon" style="background: var(--puma-gradient-2); box-shadow: var(--puma-shadow-red);">
            <i class="fas fa-plus"></i>
        </div>
        <div class="quick-action-text">
            <h4 style="color: var(--puma-black); font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">Thêm sản phẩm mới</h4>
            <p style="color: var(--puma-medium-grey); font-weight: 600; text-transform: uppercase; font-size: 11px;">Tạo sản phẩm cho cửa hàng</p>
        </div>
    </a>

    <a href="{{ route('admin.categories.create') }}" class="quick-action" style="border-left: 4px solid var(--puma-gold);">
        <div class="quick-action-icon" style="background: var(--puma-gradient-3); box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);">
            <i class="fas fa-tags"></i>
        </div>
        <div class="quick-action-text">
            <h4 style="color: var(--puma-black); font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">Thêm danh mục</h4>
            <p style="color: var(--puma-medium-grey); font-weight: 600; text-transform: uppercase; font-size: 11px;">Tạo danh mục sản phẩm mới</p>
        </div>
    </a>

    <a href="{{ route('admin.users.create') }}" class="quick-action" style="border-left: 4px solid var(--puma-dark-grey);">
        <div class="quick-action-icon" style="background: var(--puma-gradient-1); box-shadow: var(--puma-shadow-md);">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="quick-action-text">
            <h4 style="color: var(--puma-black); font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">Thêm người dùng</h4>
            <p style="color: var(--puma-medium-grey); font-weight: 600; text-transform: uppercase; font-size: 11px;">Tạo tài khoản người dùng mới</p>
        </div>
    </a>

    <a href="{{ route('admin.orders') }}" class="quick-action" style="border-left: 4px solid var(--puma-red);">
        <div class="quick-action-icon" style="background: var(--puma-gradient-2); box-shadow: var(--puma-shadow-red);">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="quick-action-text">
            <h4 style="color: var(--puma-black); font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">Quản lý đơn hàng</h4>
            <p style="color: var(--puma-medium-grey); font-weight: 600; text-transform: uppercase; font-size: 11px;">Xem và xử lý đơn hàng</p>
        </div>
    </a>


</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h2 class="card-title">Đơn hàng gần đây</h2>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">Theo dõi các đơn hàng mới nhất</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="btn btn-primary">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($stats['recent_orders']->count() > 0)
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_orders'] as $order)
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 600; color: #3b82f6;">
                                        #{{ $order->id }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 35px; height: 35px; background: var(--puma-gradient-2); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; box-shadow: var(--puma-shadow-red);">
                                            {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b;">{{ $order->user->name }}</div>
                                            <div style="font-size: 12px; color: #64748b;">{{ $order->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: var(--puma-red);">
                                        {{ number_format($order->total) }} VNĐ
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['label' => 'Chờ xử lý', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
                                            'processing' => ['label' => 'Đang xử lý', 'color' => '#3b82f6', 'bg' => '#dbeafe'],
                                            'shipped' => ['label' => 'Đã gửi', 'color' => '#8b5cf6', 'bg' => '#ede9fe'],
                                            'completed' => ['label' => 'Hoàn thành', 'color' => '#10b981', 'bg' => '#d1fae5'],
                                            'cancelled' => ['label' => 'Đã hủy', 'color' => '#ef4444', 'bg' => '#fee2e2']
                                        ];
                                        $config = $statusConfig[$order->status] ?? ['label' => $order->status, 'color' => '#64748b', 'bg' => '#f1f5f9'];
                                    @endphp
                                    <span style="display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: {{ $config['color'] }}; background: {{ $config['bg'] }};">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <span style="color: #64748b;">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                <i class="fas fa-shopping-cart" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <h3 style="margin: 0 0 5px 0;">Chưa có đơn hàng nào</h3>
                <p style="margin: 0;">Các đơn hàng mới sẽ hiển thị ở đây</p>
            </div>
        @endif
    </div>
</div>

<!-- Additional Stats -->
<div style="margin-top: 30px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- Top Categories -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh mục hàng đầu</h3>
            </div>
            <div class="card-body">
                @php
                    $topCategories = App\Models\Category::withCount('products')->orderBy('products_count', 'desc')->take(5)->get();
                @endphp
                @foreach($topCategories as $category)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 8px; height: 8px; background: var(--puma-red); border-radius: 50%;"></div>
                            <span style="font-weight: 500;">{{ $category->name }}</span>
                        </div>
                        <span style="font-weight: 600; color: #64748b;">{{ $category->products_count }} sản phẩm</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Info -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thông tin hệ thống</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <span>Laravel Version</span>
                    <span style="font-weight: 600;">{{ app()->version() }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <span>PHP Version</span>
                    <span style="font-weight: 600;">{{ PHP_VERSION }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <span>Thời gian hoạt động</span>
                    <span style="font-weight: 600;">{{ now()->format('H:i:s') }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0;">
                    <span>Admin đăng nhập</span>
                    <span style="font-weight: 600; color: var(--puma-red);">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection