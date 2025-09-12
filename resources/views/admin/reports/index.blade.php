@extends('admin.layouts.admin')

@section('title', 'Báo Cáo')
@section('page-title', 'BÁO CÁO THỐNG KÊ')
@section('breadcrumb', 'Báo Cáo / Thống Kê')

@section('content')
<div class="container-fluid">
    <!-- Header với bộ lọc ngày -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">📊 BÁO CÁO TỔNG QUAN</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reports.charts') }}" class="btn btn-success">
                    <i class="fas fa-chart-line"></i> Xem Biểu Đồ
                </a>
                <input type="date" class="form-control" id="start_date" style="width: auto;">
                <input type="date" class="form-control" id="end_date" style="width: auto;">
                <button class="btn btn-primary">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="stats-grid mb-4">
        <div class="stat-card text-center">
            <div style="background: linear-gradient(135deg, #10b981, #065f46); color: white; padding: 20px; border-radius: 12px;">
                <i class="fas fa-dollar-sign fa-2x mb-3"></i>
                <h3 style="margin: 0; font-size: 28px; font-weight: 900;">{{ number_format($totalRevenue) }} đ</h3>
                <p style="margin: 5px 0 0; opacity: 0.9;">TỔNG DOANH THU</p>
            </div>
        </div>
        
        <div class="stat-card text-center">
            <div style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 20px; border-radius: 12px;">
                <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                <h3 style="margin: 0; font-size: 28px; font-weight: 900;">{{ number_format($totalOrders) }}</h3>
                <p style="margin: 5px 0 0; opacity: 0.9;">TỔNG ĐỠN HÀNG</p>
            </div>
        </div>
        
        <div class="stat-card text-center">
            <div style="background: linear-gradient(135deg, #8b5cf6, #5b21b6); color: white; padding: 20px; border-radius: 12px;">
                <i class="fas fa-users fa-2x mb-3"></i>
                <h3 style="margin: 0; font-size: 28px; font-weight: 900;">{{ number_format($totalUsers) }}</h3>
                <p style="margin: 5px 0 0; opacity: 0.9;">KHÁCH HÀNG</p>
            </div>
        </div>
        
        <div class="stat-card text-center">
            <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 20px; border-radius: 12px;">
                <i class="fas fa-box fa-2x mb-3"></i>
                <h3 style="margin: 0; font-size: 28px; font-weight: 900;">{{ number_format($totalProducts) }}</h3>
                <p style="margin: 5px 0 0; opacity: 0.9;">SẢN PHẨM</p>
            </div>
        </div>
    </div>

    <!-- Biểu đồ -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">📈 DOANH THU THEO NGÀY (30 NGÀY QUA)</h4>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">📊 TRẠNG THÁI ĐƠN HÀNG</h4>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm và khách hàng -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🏆 TOP SẢN PHẨM BÁN CHẠY</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                                <tr>
                                    <th>SẢN PHẨM</th>
                                    <th>SỐ LƯỢNG</th>
                                    <th>DOANH THU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 10px;" 
                                                 alt="{{ $product->name }}">
                                            <span>{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td><strong>{{ number_format($product->total_sold) }}</strong></td>
                                    <td><span style="color: #10b981; font-weight: 700;">{{ number_format($product->total_revenue) }}đ</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">👑 TOP KHÁCH HÀNG</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                                <tr>
                                    <th>KHÁCH HÀNG</th>
                                    <th>ĐƠN HÀNG</th>
                                    <th>CHI TIÊU</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $customer)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $customer->name }}</strong>
                                            <br><small style="color: #6b7280;">{{ $customer->email }}</small>
                                        </div>
                                    </td>
                                    <td><strong>{{ number_format($customer->total_orders) }}</strong></td>
                                    <td><span style="color: #10b981; font-weight: 700;">{{ number_format($customer->total_spent) }}đ</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê danh mục -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">📂 DOANH SỐ THEO DANH MỤC</h4>
                </div>
                <div class="card-body">
                    @foreach($categorySales as $category)
                    <div style="margin-bottom: 20px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-weight: 600;">{{ $category->category_name }}</span>
                            <span style="color: #10b981; font-weight: 700;">{{ number_format($category->total_revenue) }}đ</span>
                        </div>
                        <div style="background: #f3f4f6; height: 10px; border-radius: 5px; overflow: hidden;">
                            <div style="background: linear-gradient(135deg, #e74c3c, #c0392b); height: 100%; width: {{ $categorySales->max('total_revenue') > 0 ? ($category->total_revenue / $categorySales->max('total_revenue')) * 100 : 0 }}%; border-radius: 5px;"></div>
                        </div>
                        <small style="color: #6b7280;">Đã bán: {{ number_format($category->total_sold) }} sản phẩm</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">💳 PHƯƠNG THỨC THANH TOÁN</h4>
                </div>
                <div class="card-body">
                    @foreach($paymentMethods as $method)
                    <div style="margin-bottom: 15px; padding: 15px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #e74c3c;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-weight: 600; text-transform: uppercase;">
                                @if($method->payment_method == 'cod')
                                    💵 THANH TOÁN KHI NHẬN HÀNG
                                @elseif($method->payment_method == 'momo')
                                    📱 MOMO
                                @elseif($method->payment_method == 'vnpay')
                                    💳 VNPAY
                                @else
                                    {{ $method->payment_method }}
                                @endif
                            </span>
                            <span style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 700;">
                                {{ number_format($method->count) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ doanh thu theo ngày
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyRevenue->pluck('date')) !!},
        datasets: [{
            label: 'Doanh Thu (đ)',
            data: {!! json_encode($dailyRevenue->pluck('revenue')) !!},
            borderColor: '#e74c3c',
            backgroundColor: 'rgba(231, 76, 60, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                    }
                }
            }
        }
    }
});

// Biểu đồ trạng thái đơn hàng
const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($orderStatus->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($orderStatus->pluck('count')) !!},
            backgroundColor: [
                '#e74c3c',
                '#3b82f6', 
                '#10b981',
                '#f59e0b',
                '#8b5cf6'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection



