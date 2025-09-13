@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Đăng nhập') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Quick Login Buttons -->
                    <div class="mb-4">
                        <div class="text-center mb-3">
                            <small class="text-muted">Đăng nhập nhanh để thử nghiệm:</small>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-dark w-100 quick-login-btn" onclick="return quickLogin('admin@gmail.com', 'admin123');">
                                    <i class="fas fa-crown mb-1"></i><br>
                                    <small><strong>ADMIN</strong></small><br>
                                    <small class="text-muted">admin@gmail.com</small>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100 quick-login-btn" onclick="return quickLogin('iezreal.com@gmail.com', 'a123456a');">
                                    <i class="fas fa-user mb-1"></i><br>
                                    <small><strong>USER</strong></small><br>
                                    <small class="text-muted">iezreal.com@gmail.com</small>
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Hoặc đăng nhập bằng tài khoản của bạn:</small>
                        </div>
                        <hr>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Địa chỉ Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mật khẩu') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Ghi nhớ đăng nhập') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Đăng nhập') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Quên mật khẩu?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quick-login-btn {
    min-height: 80px;
    border-radius: 12px !important;
    position: relative;
    overflow: hidden;
}

.quick-login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.quick-login-btn:active {
    transform: translateY(0);
}

.quick-login-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.quick-login-btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for DOM to be fully loaded
    console.log('DOM loaded, setting up quick login...');
});

function quickLogin(email, password) {
    console.log('QuickLogin called with:', email);
    
    // Prevent any default behavior
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    try {
        // Get form elements with more specific selectors
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        const loginForm = document.querySelector('form[method="POST"]');
        
        console.log('Form elements found:', {
            email: !!emailInput,
            password: !!passwordInput, 
            form: !!loginForm
        });
        
        if (!emailInput || !passwordInput || !loginForm) {
            console.error('Missing form elements');
            alert('Không tìm thấy form đăng nhập');
            return false;
        }
        
        // Fill in the form
        emailInput.value = email;
        passwordInput.value = password;
        
        console.log('Form filled, values:', {
            email: emailInput.value,
            password: passwordInput.value ? '***' : 'empty'
        });
        
        // Show loading state
        const clickedBtn = event ? event.target.closest('button') : null;
        if (clickedBtn) {
            clickedBtn.classList.add('loading');
            clickedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
            clickedBtn.disabled = true;
        }
        
        // Submit the form after a small delay
        setTimeout(function() {
            console.log('Submitting login form...');
            loginForm.submit();
        }, 100);
        
        return false;
        
    } catch (error) {
        console.error('Lỗi trong quickLogin:', error);
        alert('Có lỗi xảy ra: ' + error.message);
        return false;
    }
}
</script>
@endsection