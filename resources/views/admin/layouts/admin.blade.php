<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Quan Au Store</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/puma-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin-puma.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --puma-red: #ff0000;
            --puma-black: #1a1a1a;
            --puma-white: #ffffff;
            --puma-gold: #ffd700;
            --puma-light-gray: #f8f9fa;
            --puma-medium-gray: #6c757d;
            --puma-dark-gray: #343a40;
            --puma-text-primary: #2d3748;
            --puma-text-secondary: #4a5568;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            background-attachment: fixed;
            min-height: 100vh;
            overflow-x: hidden;
            color: var(--puma-text-primary) !important;
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            color: #2d3748;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            border-right: 5px solid var(--puma-red);
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .admin-sidebar.collapsed {
            width: 80px;
        }
        
        .admin-sidebar.collapsed .nav-text,
        .admin-sidebar.collapsed .nav-badge,
        .admin-sidebar.collapsed .nav-title,
        .admin-sidebar.collapsed .logo-text {
            opacity: 0;
            visibility: hidden;
            width: 0;
            overflow: hidden;
        }
        
        .admin-sidebar.collapsed .admin-logo {
            justify-content: center;
        }
        
        .admin-sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 15px 12px;
        }
        
        .admin-sidebar.collapsed .nav-section {
            padding: 0 8px;
        }
        
        
        .sidebar-toggle {
            position: absolute;
            top: 20px;
            right: -18px;
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--puma-red), #e53e3e);
            color: white;
            border: 3px solid #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 14px;
            font-weight: 900;
        }
        
        .sidebar-toggle:hover {
            background: linear-gradient(135deg, #e53e3e, var(--puma-red));
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(255, 0, 0, 0.4);
        }
        
        .sidebar-toggle i {
            transition: transform 0.3s ease;
        }
        
        .admin-sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }
        
        /* Tooltip for collapsed sidebar */
        .admin-sidebar.collapsed .nav-link {
            position: relative;
        }
        
        .admin-sidebar.collapsed .nav-link:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 10px;
            background: var(--puma-red);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            z-index: 1002;
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
            pointer-events: none;
            opacity: 0;
            animation: tooltipShow 0.2s ease forwards;
        }
        
        .admin-sidebar.collapsed .nav-link:hover::before {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 5px;
            border: 5px solid transparent;
            border-right-color: var(--puma-red);
            z-index: 1002;
            pointer-events: none;
            opacity: 0;
            animation: tooltipShow 0.2s ease forwards;
        }
        
        @keyframes tooltipShow {
            from {
                opacity: 0;
                transform: translateY(-50%) translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(-50%) translateX(0);
            }
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 2px solid var(--puma-red);
            background: linear-gradient(135deg, var(--puma-red) 0%, #e53e3e 100%);
        }
        
        .sidebar-header * {
            color: white !important;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
        }
        
        .logo-icon {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white !important;
        }
        
        .logo-text h2 {
            font-size: 18px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
            color: white !important;
        }
        
        .logo-text p {
            font-size: 11px;
            margin: 0;
            opacity: 0.8;
            color: white !important;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-section {
            margin-bottom: 30px;
        }
        
        .nav-title {
            padding: 0 20px 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--puma-red);
            font-family: 'Inter', sans-serif;
        }
        
        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin: 2px 10px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #4a5568;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: linear-gradient(135deg, var(--puma-red), #e53e3e);
            text-decoration: none;
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(255,0,0,0.3);
        }
        
        .nav-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,0,0,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 18px;
            color: var(--puma-red);
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar.collapsed .nav-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
            margin-right: 0;
            border-radius: 12px;
        }
        
        .nav-text {
            font-size: 14px;
            font-weight: 600;
        }
        
        .nav-badge {
            margin-left: auto;
            background: linear-gradient(135deg, var(--puma-red), #e53e3e);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 2px 8px rgba(255,0,0,0.3);
        }
        
        .admin-main {
            flex: 1;
            margin-left: 280px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            min-height: 100vh;
            color: var(--puma-text-primary) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .admin-main.sidebar-collapsed {
            margin-left: 80px;
        }
        
        .admin-header {
            height: 70px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
            text-transform: uppercase;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }
        
        .user-info h4 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }
        
        .user-info p {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        
        .admin-content {
            padding: 30px;
            min-height: calc(100vh - 70px);
            color: #1f2937 !important;
        }
        
        .admin-content * {
            color: inherit !important;
        }
        
        .admin-content h1, .admin-content h2, .admin-content h3, 
        .admin-content h4, .admin-content h5, .admin-content h6 {
            color: #1f2937 !important;
        }
        
        .admin-content p, .admin-content span, .admin-content div {
            color: #1f2937 !important;
        }
        
        .admin-content .text-muted, .admin-content small {
            color: #6b7280 !important;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            color: #1f2937 !important;
        }
        
        .card * {
            color: #1f2937 !important;
        }
        
        .card .text-muted, .card small {
            color: #6b7280 !important;
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .quick-action {
            background: white;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .quick-action:hover {
            transform: translateY(-5px);
            text-decoration: none;
            color: inherit;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        
        /* PUMA BRIGHT THEME - MODERN & CLEAN */
        body, body *, .admin-content, .admin-content *, 
        .card, .card *, .admin-main, .admin-main * {
            color: var(--puma-text-primary) !important;
        }
        
        /* Puma Modern Headings */
        h1, h2, h3, h4, h5, h6 {
            color: var(--puma-text-primary) !important;
            font-weight: 700 !important;
            font-family: 'Inter', sans-serif !important;
            letter-spacing: -0.5px !important;
        }
        
        /* Puma Accent Colors */
        .text-primary, .text-danger, .puma-accent {
            color: var(--puma-red) !important;
        }
        
        .text-warning, .puma-gold {
            color: var(--puma-gold) !important;
        }
        
        /* Keep specific elements with their intended colors */
        .admin-sidebar, .admin-sidebar *, 
        .nav-link, .sidebar-nav a, .admin-logo,
        .badge, .btn, .alert {
            color: inherit !important;
        }
        
        .text-muted, small, .small {
            color: var(--puma-text-secondary) !important;
            font-weight: 500 !important;
        }
        
        /* White text elements */
        .text-white, .bg-dark *, .bg-primary * {
            color: white !important;
        }
        
        /* Puma Modern Card Styling */
        .card-title {
            color: var(--puma-text-primary) !important;
            border-bottom: 2px solid var(--puma-red) !important;
            padding-bottom: 8px !important;
            margin-bottom: 15px !important;
            font-weight: 600 !important;
        }
        
        /* Puma Modern Table Headers */
        .table th {
            background: linear-gradient(135deg, var(--puma-red), #e53e3e) !important;
            color: white !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
            border: none !important;
        }
        
        /* Puma Modern Links */
        a:not(.nav-link):not(.btn) {
            color: var(--puma-red) !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
        }
        
        a:not(.nav-link):not(.btn):hover {
            color: var(--puma-gold) !important;
            text-decoration: none !important;
            opacity: 0.8 !important;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar" id="adminSidebar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                    <div class="logo-icon">📦</div>
                    <div class="logo-text">
                        <h2>QUẦN ÁO ADMIN</h2>
                        <p>Hệ thống quản lý</p>
                    </div>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-title">ĐIỀU KHIỂN</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="TỔNG QUAN">
                                <div class="nav-icon"><i class="fas fa-chart-pie"></i></div>
                                <span class="nav-text">TỔNG QUAN</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-title">QUẢN LÝ</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" data-tooltip="NGƯỜI DÙNG">
                                <div class="nav-icon"><i class="fas fa-users"></i></div>
                                <span class="nav-text">NGƯỜI DÙNG</span>
                                @if(App\Models\User::count() > 0)
                                    <span class="nav-badge">{{ App\Models\User::count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" data-tooltip="SẢN PHẨM">
                                <div class="nav-icon"><i class="fas fa-box"></i></div>
                                <span class="nav-text">SẢN PHẨM</span>
                                @if(App\Models\Product::count() > 0)
                                    <span class="nav-badge">{{ App\Models\Product::count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" data-tooltip="DANH MỤC">
                                <div class="nav-icon"><i class="fas fa-tags"></i></div>
                                <span class="nav-text">DANH MỤC</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" data-tooltip="ĐƠN HÀNG">
                                <div class="nav-icon"><i class="fas fa-shopping-cart"></i></div>
                                <span class="nav-text">ĐƠN HÀNG</span>
                                @if(App\Models\Order::where('status', 'pending')->count() > 0)
                                    <span class="nav-badge">{{ App\Models\Order::where('status', 'pending')->count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.charts') }}" class="nav-link {{ request()->routeIs('admin.reports.charts') ? 'active' : '' }}" data-tooltip="BÁO CÁO">
                                <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
                                <span class="nav-text">BÁO CÁO</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}" data-tooltip="ĐÁNH GIÁ">
                                <div class="nav-icon"><i class="fas fa-star"></i></div>
                                <span class="nav-text">ĐÁNH GIÁ</span>
                                @php
                                    $pendingReviews = \App\Models\Review::where('approved', false)->count();
                                @endphp
                                @if($pendingReviews > 0)
                                    <span class="nav-badge">{{ $pendingReviews }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="nav-section">
                    <div class="nav-title">HỆ THỐNG</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link" target="_blank" data-tooltip="XEM WEBSITE">
                                <div class="nav-icon"><i class="fas fa-external-link-alt"></i></div>
                                <span class="nav-text">XEM WEBSITE</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-tooltip="ĐĂNG XUẤT">
                                <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
                                <span class="nav-text">ĐĂNG XUẤT</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="d-md-none btn" onclick="toggleSidebar()" style="background: none; border: none; font-size: 18px; margin-right: 15px;">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div>
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                        <div class="breadcrumb">
                            <i class="fas fa-home"></i>
                            <span>/</span>
                            <span>@yield('breadcrumb', 'Dashboard')</span>
                        </div>
                    </div>
                </div>
                
                <div class="header-right">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <h4>{{ Auth::user()->name }}</h4>
                            <p>Administrator</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            const adminMain = document.querySelector('.admin-main');
            
            // Load saved state from localStorage
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                adminMain.classList.add('sidebar-collapsed');
            }
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                adminMain.classList.toggle('sidebar-collapsed');
                
                // Save state to localStorage
                const collapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', collapsed);
            });
        });

        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('mobile-open');
        }

        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            });
        }, 5000);
    </script>
    
    @yield('scripts')
</body>
</html>
