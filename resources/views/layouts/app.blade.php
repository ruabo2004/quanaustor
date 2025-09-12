<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Quần Âu Daily') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">
    <link href="{{ asset('css/puma-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom-sizing.css') }}" rel="stylesheet">
    <link href="{{ asset('css/compact-footer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth-forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/compact-ui.css') }}" rel="stylesheet">
    
    <!-- Compact Navbar -->
    <link href="{{ asset('css/navbar-compact.css') }}" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700;900&display=swap" rel="stylesheet">
</head>
<body class="puma-theme">
    <div id="app">
        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg puma-navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <div class="brand-logo">
                        <span class="brand-text">QUẦN ÂU DAILY</span>
                    </div>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">Sản phẩm</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Danh mục
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @foreach($categories as $category)
                                <a class="dropdown-item" href="{{ route('categories.show', $category->id) }}">
                                    @if($category->name === 'Quần âu nam')
                                        <i class="fas fa-male"></i>
                                    @elseif($category->name === 'Quần âu nữ')
                                        <i class="fas fa-female"></i>
                                    @elseif($category->name === 'Quần âu công sở')
                                        <i class="fas fa-briefcase"></i>
                                    @elseif($category->name === 'Quần âu dự tiệc')
                                        <i class="fas fa-glass-cheers"></i>
                                    @elseif($category->name === 'Quần âu casual')
                                        <i class="fas fa-tshirt"></i>
                                    @else
                                        <i class="fas fa-tags"></i>
                                    @endif
                                    {{ $category->name }}
                                </a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('products.index') }}">
                                    <i class="fas fa-th-large"></i>
                                    Xem tất cả sản phẩm
                                </a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Liên hệ</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cart.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                @php
                                    $cartCount = \App\Http\Controllers\CartController::getCartCount();
                                @endphp
                                <span class="badge bg-primary rounded-pill">{{ $cartCount }}</span>
                            </a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('orders.index') }}" title="Đơn hàng của tôi">
                                <i class="fas fa-receipt"></i>
                                <span class="d-none d-lg-inline ms-1">Đơn hàng</span>
                                @php
                                    $orderCount = Auth::user()->orders()->count();
                                @endphp
                                @if($orderCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="font-size: 0.6rem;">
                                        {{ $orderCount > 99 ? '99+' : $orderCount }}
                                        <span class="visually-hidden">đơn hàng</span>
                                    </span>
                                @endif
                            </a>
                        </li>
                        @endauth
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">
                                        <i class="fas fa-box me-2"></i>Đơn hàng của tôi
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">🚀 Admin Panel</h6>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}" style="background: var(--puma-gradient-2); color: white; font-weight: 700; border-radius: 8px; margin: 5px 10px; text-align: center; text-transform: uppercase;">
                                        <i class="fas fa-crown me-2"></i>VÀO ADMIN PANEL
                                    </a>
                                    <small class="dropdown-item-text text-muted text-center">Giao diện quản lý chuyên nghiệp</small>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer bg-dark text-white py-3" style="font-size: 14px;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-2">
                        <div class="footer-brand mb-2">
                            <span class="brand-text" style="font-size: 16px; font-weight: bold;">QUẦN ÂU DAILY</span>
                        </div>
                        <p style="font-size: 13px; line-height: 1.4; margin-bottom: 10px;">Chuyên cung cấp quần âu cao cấp với thiết kế hiện đại, chất lượng vượt trội và phong cách độc đáo.</p>
                        <div class="social-links">
                            <a href="#" class="text-white me-2" style="font-size: 14px;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white me-2" style="font-size: 14px;"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white me-2" style="font-size: 14px;"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="text-white" style="font-size: 14px;"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <h6 style="font-size: 14px; font-weight: bold; margin-bottom: 8px;">Danh mục</h6>
                        <ul class="list-unstyled" style="font-size: 12px; line-height: 1.6;">
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Quần âu công sở</a></li>
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Quần âu dự tiệc</a></li>
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Quần âu casual</a></li>
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Quần âu nam</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-2">
                        <h6 style="font-size: 14px; font-weight: bold; margin-bottom: 8px;">Hỗ trợ</h6>
                        <ul class="list-unstyled" style="font-size: 12px; line-height: 1.6;">
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Hướng dẫn mua hàng</a></li>
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Chính sách đổi trả</a></li>
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Bảo hành</a></li>
                            <li><a href="#" class="text-white-50" style="text-decoration: none;">Liên hệ</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <h6 style="font-size: 14px; font-weight: bold; margin-bottom: 8px;">Thông tin liên hệ</h6>
                        <p style="font-size: 12px; margin-bottom: 3px;"><i class="fas fa-map-marker-alt me-1" style="font-size: 11px;"></i>41A Phú Diễn, Bắc Từ Liêm, Hà Nội</p>
                        <p style="font-size: 12px; margin-bottom: 3px;"><i class="fas fa-university me-1" style="font-size: 11px;"></i>Đại học Tài nguyên và Môi trường Hà Nội</p>
                        <p style="font-size: 12px; margin-bottom: 3px;"><i class="fas fa-phone me-1" style="font-size: 11px;"></i>(024) 3756 8422</p>
                        <p style="font-size: 12px; margin-bottom: 3px;"><i class="fas fa-envelope me-1" style="font-size: 11px;"></i>hunre.store@gmail.com</p>
                        <p style="font-size: 12px; margin-bottom: 3px;"><i class="fas fa-clock me-1" style="font-size: 11px;"></i>8:00 - 17:00 (Thứ 2 - Thứ 6)</p>
                    </div>
                </div>
                <hr class="my-2" style="opacity: 0.3;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0" style="font-size: 11px; color: #aaa;">&copy; 2024 Quần Âu Daily. Tất cả quyền được bảo lưu.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <img src="https://via.placeholder.com/120x20?text=Payment+Methods" alt="Payment Methods" class="img-fluid" style="max-height: 20px;">
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>