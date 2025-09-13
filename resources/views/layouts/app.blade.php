<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Personalization Meta -->
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <meta name="session-id" content="{{ session()->getId() }}">

    <title>{{ config('app.name', 'Quần Âu Daily') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Modern CSS Framework -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navigation.css') }}" rel="stylesheet">
    <link href="{{ asset('css/products.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modern-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/products-modern.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('css/checkout-modern.css') }}" rel="stylesheet">
    <link href="{{ asset('css/advanced-search.css') }}" rel="stylesheet">
    <link href="{{ asset('css/personalization.css') }}" rel="stylesheet">
    <link href="{{ asset('css/loyalty.css') }}" rel="stylesheet">
    <link href="{{ asset('css/support.css') }}" rel="stylesheet">
    <link href="{{ asset('css/cms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/social-integration.css') }}" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700;900&display=swap" rel="stylesheet">
</head>
<body class="puma-theme">
    <div id="app">
        <!-- Modern Navigation -->
        <nav class="navbar navbar-expand-lg navbar-modern">
            <div class="container">
                <a class="navbar-brand navbar-brand-modern" href="{{ url('/') }}">
                    <i class="fas fa-store me-2 text-primary"></i>
                    QUẦN ÂU DAILY
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav-modern me-auto">
                        <li class="nav-item-modern">
                            <a class="nav-link-modern {{ Request::is('/') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="fas fa-home"></i>
                                Trang chủ
                            </a>
                        </li>
                        <li class="nav-item-modern">
                            <a class="nav-link-modern {{ Request::is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="fas fa-tshirt"></i>
                                Sản phẩm
                            </a>
                        </li>
                        <li class="nav-item-modern dropdown-modern">
                            <button class="nav-link-modern dropdown-toggle-modern" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-list"></i>
                                Danh mục
                            </button>
                            <div class="dropdown-menu-modern">
                                @foreach($categories as $category)
                                <a class="dropdown-item-modern" href="{{ route('categories.show', $category->id) }}">
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
                                <hr style="margin: var(--space-2) 0; border: 1px solid var(--gray-200);">
                                <a class="dropdown-item-modern" href="{{ route('products.index') }}">
                                    <i class="fas fa-th-large"></i>
                                    Xem tất cả sản phẩm
                                </a>
                            </div>
                        </li>
                        <li class="nav-item-modern">
                            <a class="nav-link-modern" href="#about">
                                <i class="fas fa-info-circle"></i>
                                Về chúng tôi
                            </a>
                        </li>
                        <li class="nav-item-modern">
                            <a class="nav-link-modern" href="{{ route('blog.index') }}">
                                <i class="fas fa-blog"></i>
                                Blog
                            </a>
                        </li>
                        <li class="nav-item-modern">
                            <a class="nav-link-modern" href="{{ route('faq.index') }}">
                                <i class="fas fa-question-circle"></i>
                                FAQ
                            </a>
                        </li>
                        <li class="nav-item-modern">
                            <a class="nav-link-modern" href="#contact">
                                <i class="fas fa-envelope"></i>
                                Liên hệ
                            </a>
                        </li>
                    </ul>
                    
                    <div class="user-menu-modern">
                        <a class="cart-icon-modern" href="{{ route('cart.index') }}" title="Giỏ hàng">
                            <i class="fas fa-shopping-cart"></i>
                            @php
                                $cartCount = \App\Http\Controllers\CartController::getCartCount();
                            @endphp
                            @if($cartCount > 0)
                                <span class="cart-count-modern">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        </a>
                        @guest
                            @if (Route::has('login'))
                                <a class="btn btn-secondary btn-sm" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Đăng nhập
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a class="btn btn-primary btn-sm" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus"></i>
                                    Đăng ký
                                </a>
                            @endif
                        @else
                            @auth
                            <a class="cart-icon-modern" href="{{ route('orders.index') }}" title="Đơn hàng của tôi">
                                <i class="fas fa-receipt"></i>
                                @php
                                    $orderCount = Auth::user()->orders()->count();
                                @endphp
                                @if($orderCount > 0)
                                    <span class="cart-count-modern">{{ $orderCount > 99 ? '99+' : $orderCount }}</span>
                                @endif
                            </a>
                            @endauth

                            <div class="dropdown-modern">
                                <button class="user-avatar-modern dropdown-toggle-modern" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </button>
                                <div class="dropdown-menu-modern" style="right: 0; left: auto; min-width: 250px;">
                                    <div style="padding: var(--space-4); border-bottom: 1px solid var(--gray-200);">
                                        <div style="font-weight: 600; color: var(--gray-900);">{{ Auth::user()->name }}</div>
                                        <div style="font-size: var(--text-sm); color: var(--gray-600);">{{ Auth::user()->email }}</div>
                                    </div>
                                    
                                    <!-- Loyalty Points Display -->
                                    <div style="padding: var(--space-3) var(--space-4); background: linear-gradient(135deg, #667eea, #764ba2); margin: 0 -12px var(--space-3); color: white;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-size: var(--text-xs); opacity: 0.9;">Điểm tích lũy</div>
                                                <div style="font-size: 1.25rem; font-weight: 700;" data-widget-points>--</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-size: var(--text-xs); opacity: 0.9;">Hạng thành viên</div>
                                                <div style="font-size: var(--text-sm); font-weight: 600;" data-widget-tier>--</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a class="dropdown-item-modern" href="{{ route('loyalty.dashboard') }}">
                                        <i class="fas fa-crown"></i>
                                        Chương trình thành viên
                                    </a>
                                    
                                    <a class="dropdown-item-modern" href="{{ route('orders.index') }}">
                                        <i class="fas fa-box"></i>
                                        Đơn hàng của tôi
                                    </a>
                                    
                                    @if(Auth::user()->isAdmin())
                                    <hr style="margin: var(--space-2) 0; border: 1px solid var(--gray-200);">
                                    <div style="padding: var(--space-2) var(--space-4); font-size: var(--text-xs); font-weight: 600; color: var(--gray-500); text-transform: uppercase;">
                                        Admin Panel
                                    </div>
                                    <a class="dropdown-item-modern" href="{{ route('admin.dashboard') }}" style="background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%); color: white; margin: var(--space-2); border-radius: var(--radius-lg);">
                                        <i class="fas fa-crown"></i>
                                        Quản lý hệ thống
                                    </a>
                                    @endif
                                    
                                    <hr style="margin: var(--space-2) 0; border: 1px solid var(--gray-200);">
                                    <a class="dropdown-item-modern" href="{{ route('logout') }}"
                                       onclick="handleMainLogout(event)">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Đăng xuất
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
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
    <script src="{{ asset('js/micro-interactions.js') }}"></script>
    <script src="{{ asset('js/advanced-search.js') }}"></script>
    <script src="{{ asset('js/personalization.js') }}"></script>
    <script src="{{ asset('js/loyalty.js') }}"></script>
    <script src="{{ asset('js/support.js') }}"></script>
    <script src="{{ asset('js/analytics.js') }}"></script>
    <script src="{{ asset('js/social-integration.js') }}"></script>
    
    <script>
        // Main app logout function
        function handleMainLogout(event) {
            event.preventDefault();
            console.log('Main logout clicked');
            
            const form = document.getElementById('logout-form');
            if (form) {
                console.log('Submitting main logout form');
                form.submit();
            } else {
                console.error('Main logout form not found');
                // Fallback to GET request (not recommended but better than nothing)
                window.location.href = '/logout';
            }
        }
    </script>
    
    <!-- Live Chat Widget -->
    <div class="chat-widget">
        <button class="chat-toggle" title="Chat với chúng tôi">
            <i class="fas fa-comments"></i>
        </button>
        <div class="chat-window">
            <div class="chat-header">
                <div>
                    <div class="chat-title">Hỗ trợ trực tuyến</div>
                    <div class="chat-status">Đang kết nối...</div>
                </div>
                <button class="chat-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            <div class="chat-input-container">
                <form class="chat-input-form">
                    <textarea class="chat-input" placeholder="Nhập tin nhắn của bạn..." rows="1"></textarea>
                    <button type="submit" class="chat-send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
</html>