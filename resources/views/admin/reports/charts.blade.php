@extends('admin.layouts.admin')

@section('title', 'Biểu Đồ Báo Cáo')
@section('page-title', 'BIỂU ĐỒ THỐNG KÊ')
@section('breadcrumb', 'Báo Cáo / Biểu Đồ')

@section('content')
<style>
/* Custom Puma Chart Styles */
.chart-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    margin-bottom: 30px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.chart-header {
    background: linear-gradient(135deg, var(--puma-red) 0%, #e53e3e 100%);
    color: white;
    padding: 20px 25px;
    border-bottom: none;
}

.chart-header h4 {
    margin: 0;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    font-size: 1.1rem;
    letter-spacing: 0.5px;
}

.chart-body {
    padding: 30px 25px;
}

.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 25px;
    border-radius: 15px;
    border-left: 5px solid var(--puma-red);
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--puma-red), #e53e3e);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    margin-bottom: 15px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    font-family: 'Inter', sans-serif;
}

.stat-label {
    color: #4a5568;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 5px;
}

.chart-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.chart-full {
    grid-column: 1 / -1;
}

@media (max-width: 968px) {
    .chart-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #2d3748; font-weight: 700; margin: 0;">📈 Biểu Đồ Thống Kê</h2>
            <p style="color: #4a5568; margin: 5px 0 0;">Phân tích dữ liệu trực quan</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary">
                <i class="fas fa-table"></i> Báo Cáo Chi Tiết
            </a>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> In Báo Cáo
            </button>
        </div>
    </div>
    <div class="stats-overview">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value">{{ number_format($totalRevenue) }}đ</div>
            <div class="stat-label">Tổng Doanh Thu</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-value">{{ number_format($totalOrders) }}</div>
            <div class="stat-label">Tổng Đơn Hàng</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="stat-label">Khách Hàng</div>
        </div>
        
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
            <div class="stat-label">Sản Phẩm</div>
        </div>
    </div>
    <div class="chart-grid">
        <!-- Revenue by Category -->
        <div class="chart-container">
            <div class="chart-header">
                <h4><i class="fas fa-tags me-2"></i>Doanh Thu Theo Danh Mục</h4>
            </div>
            <div class="chart-body">
                <canvas id="categoryRevenueChart" height="300"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <div class="chart-header">
                <h4><i class="fas fa-credit-card me-2"></i>Doanh Thu Theo Thanh Toán</h4>
            </div>
            <div class="chart-body">
                <canvas id="paymentMethodChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="chart-container chart-full">
        <div class="chart-header">
            <h4><i class="fas fa-calendar-day me-2"></i>Doanh Thu Theo Ngày (30 Ngày Qua)</h4>
        </div>
        <div class="chart-body">
            <canvas id="dailyRevenueChart" height="100"></canvas>
        </div>
    </div>
    <div class="chart-grid">
        <div class="chart-container">
            <div class="chart-header">
                <h4><i class="fas fa-calendar-alt me-2"></i>Doanh Thu Theo Tháng</h4>
            </div>
            <div class="chart-body">
                <canvas id="monthlyRevenueChart" height="300"></canvas>
            </div>
        </div>
        <div class="chart-container">
            <div class="chart-header">
                <h4><i class="fas fa-chart-pie me-2"></i>Trạng Thái Đơn Hàng</h4>
            </div>
            <div class="chart-body">
                <canvas id="orderStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="chart-container chart-full">
        <div class="chart-header">
            <h4><i class="fas fa-chart-bar me-2"></i>Doanh Thu Theo Năm</h4>
        </div>
        <div class="chart-body">
            <canvas id="yearlyRevenueChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Puma Color Palette
    const pumaColors = {
        primary: '#ff0000',
        secondary: '#ffd700', 
        dark: '#2d3748',
        light: '#f8f9fa',
        success: '#10b981',
        info: '#3b82f6',
        warning: '#f59e0b',
        danger: '#ef4444'
    };

    // Default Chart Options
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    font: {
                        family: 'Inter',
                        weight: '600'
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    font: {
                        family: 'Inter'
                    }
                }
            },
            x: {
                ticks: {
                    font: {
                        family: 'Inter'
                    }
                }
            }
        }
    };

    // 1. Category Revenue Chart
    const categoryCtx = document.getElementById('categoryRevenueChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: @json($categoryLabels),
            datasets: [{
                label: 'Doanh Thu (đ)',
                data: @json($categoryRevenue),
                backgroundColor: [
                    pumaColors.primary,
                    pumaColors.secondary,
                    pumaColors.info,
                    pumaColors.success,
                    pumaColors.warning,
                    pumaColors.danger
                ],
                borderWidth: 0,
                borderRadius: 8
            }]
        },
        options: {
            ...defaultOptions,
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

    // 2. Payment Method Chart
    const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: @json($paymentMethodLabels),
            datasets: [{
                data: @json($paymentMethodRevenue),
                backgroundColor: [
                    pumaColors.primary,
                    pumaColors.secondary,
                    pumaColors.info,
                    pumaColors.success
                ],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // 3. Daily Revenue Chart
    const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Doanh Thu (đ)',
                data: @json($dailyRevenue),
                borderColor: pumaColors.primary,
                backgroundColor: pumaColors.primary + '20',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: pumaColors.primary,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 3,
                pointRadius: 5
            }]
        },
        options: {
            ...defaultOptions,
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

    // 4. Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                label: 'Doanh Thu (đ)',
                data: @json($monthlyRevenue),
                backgroundColor: pumaColors.info,
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            ...defaultOptions,
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

    // 5. Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: @json($orderStatusLabels),
            datasets: [{
                data: @json($orderStatusData),
                backgroundColor: [
                    pumaColors.warning,
                    pumaColors.info,
                    pumaColors.success,
                    pumaColors.danger
                ],
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // 6. Yearly Revenue Chart
    const yearlyCtx = document.getElementById('yearlyRevenueChart').getContext('2d');
    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: @json($yearlyLabels),
            datasets: [{
                label: 'Doanh Thu (đ)',
                data: @json($yearlyRevenue),
                backgroundColor: pumaColors.success,
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            ...defaultOptions,
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
});
</script>
@endsection
