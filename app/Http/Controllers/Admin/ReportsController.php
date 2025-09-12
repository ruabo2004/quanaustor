<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Thống kê tổng quan
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalUsers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();

        // Doanh thu theo ngày (30 ngày qua)
        $dailyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Top sản phẩm bán chạy
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.name',
                'products.image',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Thống kê theo danh mục
        $categorySales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Thống kê trạng thái đơn hàng
        $orderStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Thống kê phương thức thanh toán
        $paymentMethods = Order::select('payment_method', DB::raw('count(*) as count'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        // Top khách hàng
        $topCustomers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalRevenue',
            'totalOrders', 
            'totalUsers',
            'totalProducts',
            'dailyRevenue',
            'topProducts',
            'categorySales',
            'orderStatus',
            'paymentMethods',
            'topCustomers'
        ));
    }

    public function charts()
    {
        // FAKE DATA cho demo - có thể comment để dùng real data
        $useFakeData = true; // Đặt false để dùng dữ liệu thật
        
        if ($useFakeData) {
            // Thống kê tổng quan FAKE
            $totalRevenue = 45870000; // 45.87 triệu
            $totalOrders = 324;
            $totalCustomers = 156;
            $totalProducts = 89;
        } else {
            // Thống kê tổng quan cho biểu đồ từ database thật
            $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
            $totalOrders = Order::count();
            $totalCustomers = User::where('role', 'customer')->count();
            $totalProducts = Product::count();
        }

        // 1. Doanh thu theo danh mục
        if ($useFakeData) {
            $categoryLabels = ['Áo Thun', 'Quần Jean', 'Áo Khoác', 'Giày Dép', 'Phụ Kiện'];
            $categoryRevenue = [15200000, 12500000, 8900000, 6800000, 2470000];
        } else {
            $categoryStats = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where('orders.payment_status', 'paid')
                ->select(
                    'categories.name as category_name',
                    DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('revenue')
                ->get();

            $categoryLabels = $categoryStats->pluck('category_name')->toArray();
            $categoryRevenue = $categoryStats->pluck('revenue')->toArray();
        }

        // 2. Doanh thu theo ngày (30 ngày qua)
        if ($useFakeData) {
            $dailyLabels = [];
            $dailyRevenue = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dailyLabels[] = $date->format('d/m');
                // Random revenue từ 500k - 2.5M mỗi ngày
                $dailyRevenue[] = rand(500000, 2500000);
            }
        } else {
            $startDate = Carbon::now()->subDays(30);
            $endDate = Carbon::now();
            
            $dailyStats = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $dailyLabels = [];
            $dailyRevenue = [];
            
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $dailyLabels[] = $date->format('d/m');
                $dailyRevenue[] = isset($dailyStats[$dateStr]) ? $dailyStats[$dateStr]->revenue : 0;
            }
        }

        // 3. Doanh thu theo tháng (12 tháng qua)
        if ($useFakeData) {
            $monthlyLabels = [];
            $monthlyRevenue = [];
            $fakeMonthlyData = [32500000, 28900000, 35200000, 41800000, 38600000, 
                               42100000, 39800000, 44700000, 46200000, 43500000, 
                               47800000, 45870000]; // Tăng dần theo trend
            
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthlyLabels[] = $month->format('m/Y');
                $monthlyRevenue[] = $fakeMonthlyData[11 - $i];
            }
        } else {
            $monthlyStats = Order::where('payment_status', 'paid')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as revenue')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $monthlyLabels = [];
            $monthlyRevenue = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthKey = $month->format('Y-m');
                $monthlyLabels[] = $month->format('m/Y');
                $monthlyRevenue[] = isset($monthlyStats[$monthKey]) ? $monthlyStats[$monthKey]->revenue : 0;
            }
        }

        // 4. Doanh thu theo năm
        if ($useFakeData) {
            $yearlyLabels = ['2022', '2023', '2024'];
            $yearlyRevenue = [285600000, 394200000, 458700000];
        } else {
            $yearlyStats = Order::where('payment_status', 'paid')
                ->selectRaw('YEAR(created_at) as year, SUM(total) as revenue')
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            $yearlyLabels = $yearlyStats->pluck('year')->toArray();
            $yearlyRevenue = $yearlyStats->pluck('revenue')->toArray();
        }

        // 5. Doanh thu theo phương thức thanh toán
        if ($useFakeData) {
            $paymentMethodLabels = ['Thanh toán khi nhận hàng', 'MoMo', 'VNPay', 'Banking'];
            $paymentMethodRevenue = [18900000, 15200000, 8450000, 3320000];
        } else {
            $paymentStats = Order::where('payment_status', 'paid')
                ->selectRaw('payment_method, SUM(total) as revenue')
                ->groupBy('payment_method')
                ->get();

            $paymentMethodLabels = [];
            $paymentMethodRevenue = [];
            
            foreach ($paymentStats as $stat) {
                switch ($stat->payment_method) {
                    case 'cod':
                        $paymentMethodLabels[] = 'Thanh toán khi nhận hàng';
                        break;
                    case 'momo':
                        $paymentMethodLabels[] = 'MoMo';
                        break;
                    case 'vnpay':
                        $paymentMethodLabels[] = 'VNPay';
                        break;
                    default:
                        $paymentMethodLabels[] = ucfirst($stat->payment_method);
                }
                $paymentMethodRevenue[] = $stat->revenue;
            }
        }

        // 6. Trạng thái đơn hàng
        if ($useFakeData) {
            $orderStatusLabels = ['Chờ xử lý', 'Đang xử lý', 'Hoàn thành', 'Đã hủy'];
            $orderStatusData = [45, 89, 167, 23];
        } else {
            $orderStatusStats = Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            $orderStatusLabels = [];
            $orderStatusData = [];
            
            foreach ($orderStatusStats as $stat) {
                switch ($stat->status) {
                    case 'pending':
                        $orderStatusLabels[] = 'Chờ xử lý';
                        break;
                    case 'processing':
                        $orderStatusLabels[] = 'Đang xử lý';
                        break;
                    case 'shipped':
                        $orderStatusLabels[] = 'Đã giao';
                        break;
                    case 'delivered':
                        $orderStatusLabels[] = 'Hoàn thành';
                        break;
                    case 'cancelled':
                        $orderStatusLabels[] = 'Đã hủy';
                        break;
                    default:
                        $orderStatusLabels[] = ucfirst($stat->status);
                }
                $orderStatusData[] = $stat->count;
            }
        }

        return view('admin.reports.charts', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers', 
            'totalProducts',
            'categoryLabels',
            'categoryRevenue',
            'dailyLabels',
            'dailyRevenue',
            'monthlyLabels',
            'monthlyRevenue',
            'yearlyLabels',
            'yearlyRevenue',
            'paymentMethodLabels',
            'paymentMethodRevenue',
            'orderStatusLabels',
            'orderStatusData'
        ));
    }
}
