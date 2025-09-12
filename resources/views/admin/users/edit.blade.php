@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa người dùng')
@section('page-title', 'Chỉnh sửa người dùng')
@section('breadcrumb', 'Quản lý / Người dùng / Chỉnh sửa')

@section('content')
<style>
/* ====================================
   PUMA USER EDIT PAGE - COMPACT & MODERN
   ==================================== */

.puma-edit-container {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Main Card */
.puma-edit-card {
    background: linear-gradient(145deg, 
        rgba(255, 255, 255, 0.98) 0%, 
        rgba(248, 250, 252, 0.95) 100%);
    border-radius: 20px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.08),
        0 8px 20px rgba(255, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    border: 2px solid transparent;
    overflow: hidden;
    backdrop-filter: blur(10px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.puma-edit-card:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.12),
        0 10px 25px rgba(255, 0, 0, 0.15);
}

/* Header */
.puma-edit-header {
    background: linear-gradient(135deg, 
        var(--puma-red) 0%, 
        #dc2626 100%);
    color: white;
    padding: 24px 32px;
    border-radius: 20px 20px 0 0;
    position: relative;
    overflow: hidden;
}

.puma-edit-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, 
        var(--puma-gold) 0%, 
        var(--puma-red) 50%, 
        var(--puma-gold) 100%);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.puma-back-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.puma-back-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    text-decoration: none;
    transform: translateX(-2px);
}

.puma-edit-title {
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    font-size: 1.4rem;
}

.puma-edit-subtitle {
    opacity: 0.9;
    margin: 0;
    font-size: 0.95rem;
}

/* Avatar Section */
.puma-avatar-section {
    text-align: center;
    padding: 32px 24px;
    background: linear-gradient(135deg, 
        rgba(255, 0, 0, 0.03) 0%, 
        rgba(255, 215, 0, 0.02) 100%);
}

.puma-avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 16px;
}

.puma-user-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--puma-red);
    box-shadow: 0 8px 20px rgba(255, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.puma-avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--puma-red), #dc2626);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 2rem;
    border: 4px solid var(--puma-red);
    box-shadow: 0 8px 20px rgba(255, 0, 0, 0.3);
}

.puma-edit-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 24px;
    height: 24px;
    background: white;
    border: 2px solid var(--puma-red);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.puma-user-name {
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    color: var(--puma-black);
    margin: 8px 0 4px 0;
    font-size: 1.2rem;
}

.puma-user-email {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0 0 12px 0;
}

.puma-role-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Form Section */
.puma-form-section {
    padding: 32px;
}

.puma-form-label {
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    color: var(--puma-black);
    font-size: 0.9rem;
    margin-bottom: 8px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.puma-label-icon {
    color: var(--puma-red);
    margin-right: 8px;
    font-size: 0.9rem;
}

.puma-form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-family: 'Inter', sans-serif;
    font-size: 0.95rem;
    background: white;
    transition: all 0.3s ease;
    margin-bottom: 4px;
}

.puma-form-control:focus {
    outline: none;
    border-color: var(--puma-red);
    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
    transform: translateY(-1px);
}

.puma-form-select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    cursor: pointer;
}

.puma-form-help {
    color: #64748b;
    font-size: 0.8rem;
    font-weight: normal;
    text-transform: none;
    letter-spacing: normal;
}

.puma-error-text {
    color: var(--puma-red);
    font-size: 0.8rem;
    font-weight: 500;
    margin-top: 4px;
}

/* Grid Layout */
.puma-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

/* Action Buttons */
.puma-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 24px 32px;
    border-top: 1px solid #e2e8f0;
    background: rgba(248, 250, 252, 0.5);
}

.puma-btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    border: none;
    cursor: pointer;
}

.puma-btn-outline {
    background: transparent;
    border: 2px solid #e2e8f0;
    color: #64748b;
}

.puma-btn-outline:hover {
    border-color: var(--puma-red);
    color: var(--puma-red);
    text-decoration: none;
    transform: translateY(-1px);
}

.puma-btn-primary {
    background: linear-gradient(135deg, var(--puma-red) 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

.puma-btn-primary:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .puma-edit-header {
        padding: 20px 24px;
    }
    
    .puma-edit-title {
        font-size: 1.2rem;
    }
    
    .puma-form-section {
        padding: 24px;
    }
    
    .puma-form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .puma-actions {
        padding: 20px 24px;
        flex-direction: column;
    }
    
    .puma-btn {
        justify-content: center;
    }
}
</style>

<div class="container-fluid puma-edit-container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="puma-edit-card">
                <!-- Header -->
                <div class="puma-edit-header">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.users') }}" class="puma-back-btn me-3">
                            <i class="fas fa-arrow-left me-2"></i>QUAY LẠI
                        </a>
                        <div class="flex-grow-1">
                            <h3 class="puma-edit-title">👤 Chỉnh sửa người dùng</h3>
                            <p class="puma-edit-subtitle">{{ $user->name }}</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    <!-- Avatar Section -->
                    <div class="col-lg-4">
                        <div class="puma-avatar-section">
                            <div class="puma-avatar-wrapper">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="puma-user-avatar">
                                @else
                                    <div class="puma-avatar-placeholder">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="puma-edit-badge">
                                    <i class="fas fa-edit text-danger" style="font-size: 10px;"></i>
                                </div>
                            </div>
                            <h5 class="puma-user-name">{{ $user->name }}</h5>
                            <p class="puma-user-email">{{ $user->email }}</p>
                            <span class="puma-role-badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }} text-white">
                                {{ $user->role === 'admin' ? 'ADMIN' : 'USER' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Form Section -->
                    <div class="col-lg-8">
                        <div class="puma-form-section">
                            <form id="user-edit-form" action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="puma-form-grid">
                                    <div>
                                        <label for="name" class="puma-form-label">
                                            <i class="puma-label-icon fas fa-user"></i>Tên người dùng
                                        </label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                               class="puma-form-control" required>
                                        @error('name')
                                            <div class="puma-error-text">{{ $message }}</div>
                                        @enderror
                                    </div>
                
                                    <div>
                                        <label for="email" class="puma-form-label">
                                            <i class="puma-label-icon fas fa-envelope"></i>Email
                                        </label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                               class="puma-form-control" required>
                                        @error('email')
                                            <div class="puma-error-text">{{ $message }}</div>
                                        @enderror
                                    </div>
                
                                    <div>
                                        <label for="password" class="puma-form-label">
                                            <i class="puma-label-icon fas fa-lock"></i>Mật khẩu mới 
                                            <span class="puma-form-help">(để trống nếu không đổi)</span>
                                        </label>
                                        <input type="password" id="password" name="password" class="puma-form-control">
                                        @error('password')
                                            <div class="puma-error-text">{{ $message }}</div>
                                        @enderror
                                    </div>
                
                                    <div>
                                        <label for="role" class="puma-form-label">
                                            <i class="puma-label-icon fas fa-shield-alt"></i>Quyền hạn
                                        </label>
                                        <select id="role" name="role" class="puma-form-control puma-form-select">
                                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Người dùng</option>
                                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                        </select>
                                        @error('role')
                                            <div class="puma-error-text">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="puma-actions">
                    <a href="{{ route('admin.users') }}" class="puma-btn puma-btn-outline">
                        <i class="fas fa-times me-2"></i>HỦY
                    </a>
                    <button type="submit" form="user-edit-form" class="puma-btn puma-btn-primary">
                        <i class="fas fa-save me-2"></i>CẬP NHẬT
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
