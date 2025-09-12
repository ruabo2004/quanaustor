@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')
@section('breadcrumb', 'Quản lý / Sản phẩm / Chỉnh sửa')

@section('content')
<style>
/* ====================================
   PUMA PRODUCT EDIT - ULTIMATE THEME
   ==================================== */

/* Global Puma Theme Setup */
body {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    font-family: 'Puma', 'Arial Black', sans-serif;
}

.container-fluid {
    animation: pumaPageEntrance 1s ease-out;
}

@keyframes pumaPageEntrance {
    0% { opacity: 0; transform: translateY(50px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* ====================================
   PUMA PRODUCT FORM CONTAINER
   ==================================== */
.puma-product-form {
    background: linear-gradient(145deg, 
        rgba(255, 255, 255, 0.98) 0%, 
        rgba(248, 249, 250, 0.95) 100%);
    border-radius: 25px;
    box-shadow: 
        0 25px 60px rgba(0, 0, 0, 0.1),
        0 15px 35px rgba(255, 0, 0, 0.15),
        inset 0 2px 5px rgba(255, 255, 255, 0.9);
    border: 4px solid transparent;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(20px) saturate(1.5);
    margin: 20px 0;
    transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.puma-product-form::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, 
        #FF0000 0%, 
        #FFD700 25%, 
        #FF0000 50%, 
        #FFD700 75%, 
        #FF0000 100%);
    background-size: 400% 100%;
    animation: pumaTopShimmer 3s ease-in-out infinite;
}

.puma-product-form:hover {
    transform: translateY(-5px);
    box-shadow: 
        0 35px 80px rgba(0, 0, 0, 0.15),
        0 25px 50px rgba(255, 0, 0, 0.2),
        inset 0 2px 5px rgba(255, 255, 255, 1);
}

@keyframes pumaTopShimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* ====================================
   PUMA PRODUCT HEADER
   ==================================== */
.puma-product-header {
    background: linear-gradient(135deg, 
        #1a1a1a 0%, 
        #2d2d2d 50%, 
        #1a1a1a 100%);
    color: #ffffff;
    padding: 30px 35px;
    border-radius: 25px 25px 0 0;
    position: relative;
    overflow: hidden;
}

.puma-product-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.1) 50%, 
        transparent 100%);
    animation: pumaHeaderSweep 4s ease-in-out infinite;
}

.puma-product-header::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    right: 0;
    height: 10px;
    background: linear-gradient(to bottom, 
        rgba(0,0,0,0.2), 
        transparent);
    filter: blur(5px);
}

.puma-product-header h1 {
    font-family: 'Puma', 'Arial Black', sans-serif;
    font-weight: 900;
    font-size: 1.8rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

@keyframes pumaHeaderSweep {
    0%, 100% { left: -100%; }
    50% { left: 100%; }
}

/* ====================================
   PUMA BACK BUTTON
   ==================================== */
.puma-back-btn {
    background: linear-gradient(145deg, 
        rgba(255, 255, 255, 0.25), 
        rgba(255, 255, 255, 0.1));
    border: 2px solid rgba(255, 255, 255, 0.4);
    color: #ffffff;
    padding: 12px 20px;
    border-radius: 15px;
    text-decoration: none;
    transition: all 0.4s ease;
    display: inline-flex;
    align-items: center;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.puma-back-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.3) 50%, 
        transparent 100%);
    transition: all 0.6s ease;
}

.puma-back-btn:hover::before {
    left: 100%;
}

.puma-back-btn:hover {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    color: #1a1a1a;
    transform: translateX(-8px) translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    text-decoration: none;
    border-color: #FFD700;
}

/* ====================================
   PUMA FORM ELEMENTS
   ==================================== */
.puma-form-label {
    font-family: 'Puma', 'Arial Black', sans-serif;
    font-weight: 800;
    color: #1a1a1a;
    text-transform: uppercase;
    font-size: 1rem;
    letter-spacing: 1px;
    margin-bottom: 12px;
    display: block;
    position: relative;
    padding-left: 25px;
}

.puma-form-label::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #FF0000, #FFD700);
    border-radius: 2px;
    box-shadow: 0 0 8px rgba(255, 0, 0, 0.3);
}

.puma-form-control {
    width: 100%;
    padding: 18px 24px;
    border: 3px solid #e9ecef;
    border-radius: 15px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 1.1rem;
    transition: all 0.4s ease;
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
}

.puma-form-control:focus {
    outline: none;
    border-color: #FF0000;
    box-shadow: 
        0 0 0 6px rgba(255, 0, 0, 0.15),
        0 8px 25px rgba(255, 0, 0, 0.1),
        inset 0 2px 5px rgba(0, 0, 0, 0.05);
    transform: translateY(-4px) scale(1.01);
    background: #ffffff;
}

.puma-form-control:hover {
    border-color: #FFD700;
    transform: translateY(-2px);
    box-shadow: 
        0 4px 15px rgba(255, 215, 0, 0.2),
        inset 0 2px 5px rgba(0, 0, 0, 0.05);
}

.puma-form-select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 20px;
    cursor: pointer;
}

.puma-error-text {
    color: var(--puma-red);
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 5px;
}

/* 5. Puma Section Titles */
.puma-section-title {
    font-family: var(--puma-font-primary);
    font-weight: 900;
    color: var(--puma-black);
    text-transform: uppercase;
    font-size: 1.1rem;
    letter-spacing: 1px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid var(--puma-red);
    position: relative;
}

.puma-section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--puma-gradient-3);
}

/* 6. Puma Form Section */
.puma-form-section {
    margin-bottom: 30px;
    position: relative;
}

/* ====================================
   PUMA SIZE TABS - ULTIMATE DESIGN
   ==================================== */
.puma-size-tabs {
    background: linear-gradient(145deg, #f8f9fa, #e9ecef);
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 30px;
    border: 3px solid transparent;
    box-shadow: 
        inset 0 2px 8px rgba(0, 0, 0, 0.1),
        0 8px 25px rgba(0, 0, 0, 0.08);
    position: relative;
}

.puma-size-tabs::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, 
        rgba(255, 0, 0, 0.02) 0%, 
        rgba(255, 215, 0, 0.02) 100%);
    border-radius: 20px;
    pointer-events: none;
}

.puma-size-tab {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border: 3px solid #dee2e6;
    color: #6c757d;
    border-radius: 18px;
    padding: 16px 28px;
    margin: 0 12px 12px 0;
    font-family: 'Puma', 'Arial Black', sans-serif;
    font-weight: 900;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    overflow: hidden;
    letter-spacing: 1.5px;
    display: inline-block;
    min-width: 80px;
    text-align: center;
}

.puma-size-tab::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, 
        #FF0000 0%, 
        #FFD700 50%, 
        #FF0000 100%);
    transition: all 0.7s ease;
    z-index: 1;
}

.puma-size-tab::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 0;
    height: 0;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
    transition: all 0.5s ease;
    z-index: 2;
}

.puma-size-tab:hover::before,
.puma-size-tab.active::before {
    left: 0;
}

.puma-size-tab:hover::after,
.puma-size-tab.active::after {
    width: 120%;
    height: 120%;
    opacity: 1;
}

.puma-size-tab:hover,
.puma-size-tab.active {
    color: #ffffff;
    border-color: #FFD700;
    transform: translateY(-8px) scale(1.1) rotate(3deg);
    box-shadow: 
        0 20px 40px rgba(255, 0, 0, 0.4),
        0 8px 20px rgba(255, 215, 0, 0.3),
        inset 0 2px 5px rgba(255, 255, 255, 0.2);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.puma-size-tab > * {
    position: relative;
    z-index: 3;
}

/* ====================================
   PUMA BUTTONS & SECTION TITLES
   ==================================== */
.puma-section-title {
    font-family: 'Puma', 'Arial Black', sans-serif;
    font-weight: 900;
    color: #1a1a1a;
    text-transform: uppercase;
    font-size: 1.3rem;
    letter-spacing: 2px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 4px solid #FF0000;
    position: relative;
}

.puma-section-title::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #FFD700, #FF0000);
    border-radius: 2px;
}

.btn-primary {
    background: linear-gradient(135deg, #FF0000 0%, #dc3545 100%) !important;
    border: 3px solid transparent !important;
    border-radius: 15px !important;
    padding: 15px 30px !important;
    font-family: 'Puma', 'Arial Black', sans-serif !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    position: relative !important;
    overflow: hidden !important;
    transition: all 0.5s ease !important;
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3) !important;
    color: #ffffff !important;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.3) 50%, 
        transparent 100%);
    transition: all 0.6s ease;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-5px) scale(1.05) !important;
    box-shadow: 0 15px 40px rgba(255, 0, 0, 0.5) !important;
    background: linear-gradient(135deg, #FFD700 0%, #ffc107 100%) !important;
    color: #1a1a1a !important;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    border: 3px solid transparent !important;
    border-radius: 15px !important;
    font-family: 'Puma', 'Arial Black', sans-serif !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    transition: all 0.5s ease !important;
}

.btn-secondary:hover {
    transform: translateY(-3px) !important;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
}

/* ====================================
   PUMA INFO ALERTS & SPECIAL EFFECTS
   ==================================== */
.puma-info-alert {
    background: linear-gradient(135deg, 
        rgba(255, 0, 0, 0.12), 
        rgba(255, 215, 0, 0.08), 
        rgba(255, 0, 0, 0.06));
    border: 3px solid rgba(255, 0, 0, 0.3);
    border-radius: 18px;
    padding: 25px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(5px);
}

.puma-info-alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 8px;
    background: linear-gradient(180deg, #FF0000, #FFD700, #FF0000);
    border-radius: 18px 0 0 18px;
    animation: alertPulse 2.5s ease-in-out infinite;
}

.puma-info-alert::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: linear-gradient(45deg, 
        transparent 0%, 
        rgba(255, 215, 0, 0.1) 50%, 
        transparent 100%);
    animation: alertShimmer 4s ease-in-out infinite;
}

@keyframes alertPulse {
    0%, 100% { opacity: 0.6; transform: scaleX(1); }
    50% { opacity: 1; transform: scaleX(1.2); }
}

@keyframes alertShimmer {
    0%, 100% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
}

/* Special Measurement Fields */
.measurement-field {
    border: 3px solid #e9ecef !important;
    border-radius: 12px !important;
    padding: 12px 16px !important;
    background: linear-gradient(145deg, #ffffff, #f8f9fa) !important;
    transition: all 0.4s ease !important;
}

.measurement-field:focus {
    border-color: #FF0000 !important;
    box-shadow: 
        0 0 0 6px rgba(255, 0, 0, 0.15) !important,
        0 8px 25px rgba(255, 0, 0, 0.1) !important;
    transform: translateY(-3px) scale(1.02) !important;
    background: #ffffff !important;
}

/* Enhanced Card Animations */
.card {
    transition: all 0.5s ease;
    border-radius: 20px !important;
    border: 3px solid transparent !important;
    overflow: hidden !important;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    border-color: rgba(255, 215, 0, 0.3) !important;
}

/* Puma Textarea */
textarea.puma-form-control {
    min-height: 120px;
    resize: vertical;
}

/* File Input Enhancement */
input[type="file"] {
    border: 3px dashed #e9ecef !important;
    border-radius: 15px !important;
    padding: 20px !important;
    background: linear-gradient(145deg, #f8f9fa, #e9ecef) !important;
    transition: all 0.4s ease !important;
}

input[type="file"]:hover {
    border-color: #FFD700 !important;
    background: linear-gradient(145deg, #ffffff, #f8f9fa) !important;
    transform: translateY(-2px) !important;
}

/* ====================================
   PUMA ACTION BUTTONS - PREMIUM DESIGN
   ==================================== */
.puma-btn {
    font-family: 'Puma', 'Arial Black', sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    padding: 15px 30px;
    border-radius: 15px;
    border: 3px solid transparent;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
    min-width: 160px;
    cursor: pointer;
    backdrop-filter: blur(10px);
}

.puma-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.3) 50%, 
        transparent 100%);
    transition: all 0.6s ease;
    z-index: 1;
}

.puma-btn:hover::before {
    left: 100%;
}

.puma-btn > * {
    position: relative;
    z-index: 2;
}

/* Primary Button - Puma Red */
.puma-btn-primary {
    background: linear-gradient(135deg, #FF0000 0%, #dc3545 100%);
    color: #ffffff;
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3);
}

.puma-btn-primary:hover {
    background: linear-gradient(135deg, #FFD700 0%, #ffc107 100%);
    color: #1a1a1a;
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 40px rgba(255, 215, 0, 0.5);
    text-decoration: none;
}

/* Outline Button - Puma Style */
.puma-btn-outline {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(248, 249, 250, 0.9));
    border-color: #dee2e6;
    color: #6c757d;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.puma-btn-outline:hover {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-color: #FFD700;
    color: #ffffff;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    text-decoration: none;
}

/* Success Button - Green */
.puma-btn-success {
    background: linear-gradient(135deg, #28a745 0%, #198754 100%);
    color: #ffffff;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.puma-btn-success:hover {
    background: linear-gradient(135deg, #20c997 0%, #0dcaf0 100%);
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 40px rgba(32, 201, 151, 0.5);
    text-decoration: none;
}

/* Small Button Variant */
.puma-btn-sm {
    padding: 10px 20px;
    min-width: 120px;
    font-size: 0.9rem;
    border-radius: 12px;
}

/* Icon buttons */
.puma-btn i {
    font-size: 1.1em;
    margin-right: 8px;
}
</style>

<div class="container-fluid">
    <div class="puma-product-form">
        <div class="puma-product-header">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.products') }}" class="puma-back-btn me-4">
                    <i class="fas fa-arrow-left me-2"></i>QUAY LẠI
                </a>
                <div>
                    <h3 class="mb-0 fw-bold text-uppercase">✏️ Chỉnh sửa sản phẩm</h3>
                    <p class="mb-0 opacity-75">{{ $product->name }}</p>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">#{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>
        <div class="p-4">
            
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Thông tin cơ bản -->
                <div class="puma-form-section">
                    <h5 class="puma-section-title">
                        <i class="fas fa-info-circle me-2" style="color: var(--puma-red);"></i>Thông tin cơ bản
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="puma-form-label">
                                <i class="fas fa-tag me-2" style="color: var(--puma-red);"></i>Tên sản phẩm
                            </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" 
                                   class="puma-form-control" required>
                        @error('name')
                                <div class="puma-error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="puma-form-label">
                                <i class="fas fa-list me-2" style="color: var(--puma-red);"></i>Danh mục
                            </label>
                            <select id="category_id" name="category_id" class="puma-form-control puma-form-select" required>
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                                <div class="puma-error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                    <div class="mb-4">
                        <label for="description" class="puma-form-label">
                            <i class="fas fa-align-left me-2" style="color: var(--puma-red);"></i>Mô tả sản phẩm
                        </label>
                    <textarea id="description" name="description" rows="4" 
                                  class="puma-form-control" 
                              required>{{ old('description', $product->description) }}</textarea>
                    @error('description')
                            <div class="puma-error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="puma-form-label">
                                <i class="fas fa-dollar-sign me-2" style="color: var(--puma-red);"></i>Giá (VNĐ)
                            </label>
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="1000"
                                   class="puma-form-control" required>
                            @error('price')
                                <div class="puma-error-text">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="image" class="puma-form-label">
                                <i class="fas fa-image me-2" style="color: var(--puma-red);"></i>Hình ảnh mới 
                                <small class="text-muted normal-case">(để trống nếu không đổi)</small>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" class="puma-form-control">
                        @if($product->image)
                                <div class="mt-3">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                         class="rounded border border-3"
                                         style="width: 80px; height: 80px; object-fit: cover; border-color: var(--puma-red) !important; box-shadow: var(--puma-shadow-sm);">
                            </div>
                        @endif
                        @error('image')
                                <div class="puma-error-text">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Chi tiết chung sản phẩm -->
                <div class="puma-form-section">
                    <h5 class="puma-section-title">
                        <i class="fas fa-cogs me-2" style="color: var(--puma-red);"></i>Chi tiết chung sản phẩm
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="style" class="puma-form-label">
                                <i class="fas fa-palette me-2" style="color: var(--puma-gold);"></i>Phong cách
                            </label>
                            <input type="text" id="style" name="style" value="{{ old('style', $product->style) }}" 
                                   class="puma-form-control">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="fit" class="puma-form-label">
                                <i class="fas fa-cut me-2" style="color: var(--puma-gold);"></i>Kiểu dáng
                            </label>
                            <input type="text" id="fit" name="fit" value="{{ old('fit', $product->fit) }}" 
                                   class="puma-form-control">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="material" class="puma-form-label">
                                <i class="fas fa-tshirt me-2" style="color: var(--puma-gold);"></i>Chất liệu
                            </label>
                            <input type="text" id="material" name="material" value="{{ old('material', $product->material) }}" 
                                   class="puma-form-control">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="leg_style" class="puma-form-label">
                                <i class="fas fa-socks me-2" style="color: var(--puma-gold);"></i>Kiểu chân
                            </label>
                            <input type="text" id="leg_style" name="leg_style" value="{{ old('leg_style', $product->leg_style) }}" 
                                   class="puma-form-control">
                        </div>
                    </div>
                </div>

                <!-- Quản lý Size và Số đo -->
                <div class="puma-form-section">
                    <h5 class="puma-section-title">
                        <i class="fas fa-ruler-combined me-2" style="color: var(--puma-red);"></i>
                        Quản lý Số đo và Chiều dài theo Size
                    </h5>
                    
                    @if($product->sizes && $product->sizes->count() > 0)
                        <div class="puma-info-alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-info-circle me-3" style="color: var(--puma-red); font-size: 1.2rem;"></i>
                                <span class="fw-bold text-uppercase" style="color: var(--puma-black);">Hướng dẫn:</span>
                            </div>
                            <p class="mb-0" style="color: var(--puma-dark-grey);">
                                <strong>Click vào từng size tab</strong> bên dưới để điều chỉnh <strong>số đo tổng quát</strong> và <strong>chiều dài</strong> riêng biệt cho mỗi size. 
                                Chỉ thông tin của size được chọn sẽ hiển thị để chỉnh sửa.
                            </p>
                        </div>

                        <!-- Current Size Indicator -->
                        <div id="current-size-indicator" class="alert alert-dark border-0 mb-3" style="display: none; border-radius: var(--puma-radius-lg); background: var(--puma-gradient-1) !important;">
                            <div class="d-flex align-items-center text-white">
                                <i class="fas fa-edit me-2"></i>
                                <span>
                                    ĐANG CHỈNH SỬA: <strong id="current-size-name" class="text-warning"></strong>
                                </span>
                            </div>
                        </div>

                        <!-- Size Tabs -->
                        <div class="puma-size-tabs">
                            <div class="d-flex flex-wrap justify-content-center">
                                @foreach($product->sizes as $index => $size)
                                    <button type="button" 
                                            class="puma-size-tab size-tab {{ $index === 0 ? 'active' : '' }}"
                                            data-size="{{ $size->size }}"
                                            data-size-id="{{ $size->id }}">
                                        <div class="text-center">
                                            <div class="fw-bold fs-5">{{ $size->size }}</div>
                                            <div class="small">
                                                <span class="{{ $size->stock_quantity <= 0 ? 'text-danger' : ($size->stock_quantity <= 3 ? 'text-warning' : 'text-success') }}">
                                                    {{ $size->stock_quantity }} sp
                                            </span>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                            </div>

                        <!-- Size Content -->
                            @foreach($product->sizes as $index => $size)
                            <div class="puma-size-content size-content {{ $index === 0 ? 'show active' : '' }}" 
                                     data-size="{{ $size->size }}" 
                                     id="size-content-{{ $size->size }}"
                                     style="{{ $index === 0 ? 'display: block;' : 'display: none;' }}">
                                     
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-uppercase mb-0" style="color: var(--puma-black);">
                                        <i class="fas fa-tag me-2" style="color: var(--puma-red);"></i>Chi tiết Size {{ $size->size }}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark border-2" style="border-color: var(--puma-medium-grey) !important;">
                                            <i class="fas fa-boxes me-1"></i>{{ $size->stock_quantity }} SP
                                            </span>
                                            @if($size->price_adjustment != 0)
                                            <span class="badge {{ $size->price_adjustment > 0 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $size->price_adjustment > 0 ? '+' : '' }}{{ number_format($size->price_adjustment) }} VNĐ
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                <div class="row">
                                        <!-- Số đo tổng quát -->
                                    <div class="col-md-6 mb-3">
                                        <label class="puma-form-label">
                                            <i class="fas fa-ruler me-2" style="color: var(--puma-gold);"></i>Số đo tổng quát
                                            </label>
                                            <input type="text" 
                                                   name="sizes[{{ $size->id }}][measurements]" 
                                                   value="{{ old('sizes.'.$size->id.'.measurements', $size->measurements) }}" 
                                                   placeholder="VD: Eo 30-32, Dài 102cm"
                                               class="puma-form-control measurement-field">
                                        <div class="form-text text-muted">Nhập thông tin số đo tổng quát cho size này</div>
                                        </div>

                                        <!-- Chiều dài -->
                                    <div class="col-md-6 mb-3">
                                        <label class="puma-form-label">
                                            <i class="fas fa-arrows-alt-v me-2" style="color: var(--puma-gold);"></i>Chiều dài
                                            </label>
                                            <input type="text" 
                                                   name="sizes[{{ $size->id }}][length]" 
                                                   value="{{ old('sizes.'.$size->id.'.length', $size->length) }}" 
                                                   placeholder="VD: 102cm"
                                               class="puma-form-control measurement-field">
                                        <div class="form-text text-muted">Chiều dài quần cho size này</div>
                                    </div>
                                    </div>

                                        <!-- Thông tin bổ sung (có thể chỉnh sửa) -->
                                        <div class="border-top pt-3 mt-3">
                                            <div class="row">
                                                <!-- Tồn kho - CÓ THỂ CHỈNH SỬA -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="puma-form-label">
                                                        <i class="fas fa-boxes me-2" style="color: var(--puma-red);"></i>Số lượng tồn kho
                                                </label>
                                                    <div class="input-group">
                                                    <input type="number" 
                                                               name="sizes[{{ $size->id }}][stock_quantity]"
                                                               value="{{ old('sizes.'.$size->id.'.stock_quantity', $size->stock_quantity) }}" 
                                                               min="0"
                                                               class="puma-form-control stock-input" 
                                                               style="border-radius: 15px 0 0 15px; border-right: none;"
                                                               data-size-id="{{ $size->id }}">
                                                        <span class="input-group-text stock-status-{{ $size->id }} {{ $size->stock_quantity <= 0 ? 'bg-danger text-white' : ($size->stock_quantity <= 3 ? 'bg-warning text-dark' : 'bg-success text-white') }}" 
                                                              style="border-radius: 0 15px 15px 0; font-size: 12px; font-weight: 700; border-left: none; text-transform: uppercase; letter-spacing: 1px;">
                                                            <span class="status-text">{{ $size->stock_quantity <= 0 ? 'HẾT' : ($size->stock_quantity <= 3 ? 'SẮP HẾT' : 'CÒN') }}</span>
                                                    </span>
                                                    </div>
                                                    <div class="form-text text-primary">
                                                        <i class="fas fa-edit me-1"></i>Có thể chỉnh sửa số lượng tồn kho
                                                    </div>
                                            </div>

                                            <!-- Giá cuối -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">
                                                        <i class="fas fa-dollar-sign text-secondary me-2"></i>Giá bán cuối cùng
                                                </label>
                                                    <div class="alert alert-light border mb-0 p-3" style="border-radius: 10px;">
                                                        <div class="h5 fw-bold text-success mb-1">
                                                            {{ number_format($product->price + $size->price_adjustment) }} VNĐ
                                                    </div>
                                                    @if($size->price_adjustment != 0)
                                                            <div class="small {{ $size->price_adjustment > 0 ? 'text-danger' : 'text-success' }}">
                                                            {{ $size->price_adjustment > 0 ? '+' : '' }}{{ number_format($size->price_adjustment) }} VNĐ điều chỉnh
                                                        </div>
                                                    @else
                                                            <div class="small text-muted">Không có điều chỉnh giá</div>
                                                    @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                        </div>
                    @else
                        <div class="alert alert-warning border-0" style="border-radius: 12px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-3" style="font-size: 24px;"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Chưa có size nào</h6>
                                    <p class="mb-0 small">
                                        Sản phẩm này chưa có size. Hãy tạo size cho sản phẩm để có thể quản lý số đo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-3 mt-5 pt-4" style="border-top: 2px solid var(--puma-light-grey);">
                    <a href="{{ route('admin.products') }}" class="puma-btn puma-btn-outline">
                        <i class="fas fa-times me-2"></i>HỦY BỎ
                    </a>
                    <button type="submit" class="puma-btn puma-btn-primary">
                        <i class="fas fa-save me-2"></i>CẬP NHẬT SẢN PHẨM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Size tab switching functionality
    const sizeTabs = document.querySelectorAll('.size-tab');
    const sizeContents = document.querySelectorAll('.size-content');
    
    sizeTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetSize = this.dataset.size;
            
            // Remove active class from all tabs
            sizeTabs.forEach(t => {
                t.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all content panels
            sizeContents.forEach(content => {
                content.style.display = 'none';
                content.classList.remove('show', 'active');
            });
            
            // Show target content panel
            const targetContent = document.querySelector(`#size-content-${targetSize}`);
            if (targetContent) {
                targetContent.style.display = 'block';
                targetContent.classList.add('show', 'active');
                
                // Update current size indicator
                const indicator = document.getElementById('current-size-indicator');
                const sizeName = document.getElementById('current-size-name');
                if (indicator && sizeName) {
                    indicator.style.display = 'block';
                    sizeName.textContent = `SIZE ${targetSize}`;
                }
            }
        });
    });
    
    // Initialize first tab as active
    if (sizeTabs.length > 0) {
        const firstTab = sizeTabs[0];
        const firstSize = firstTab.dataset.size;
        const indicator = document.getElementById('current-size-indicator');
        const sizeName = document.getElementById('current-size-name');
        
        if (indicator && sizeName) {
            indicator.style.display = 'block';
            sizeName.textContent = `Size ${firstSize}`;
        }
    }
    
    // Cập nhật trạng thái tồn kho real-time
    const stockInputs = document.querySelectorAll('.stock-input');
    
    stockInputs.forEach(input => {
        input.addEventListener('input', function() {
            const sizeId = this.dataset.sizeId;
            const quantity = parseInt(this.value) || 0;
            const statusElement = document.querySelector(`.stock-status-${sizeId}`);
            const statusText = statusElement.querySelector('.status-text');
            
            // Reset classes
            statusElement.className = `input-group-text stock-status-${sizeId}`;
            statusElement.style.borderRadius = '0 15px 15px 0';
            statusElement.style.fontSize = '12px';
            statusElement.style.fontWeight = '700';
            statusElement.style.borderLeft = 'none';
            statusElement.style.textTransform = 'uppercase';
            statusElement.style.letterSpacing = '1px';
            
            // Cập nhật trạng thái và màu sắc
            if (quantity <= 0) {
                statusElement.classList.add('bg-danger', 'text-white');
                statusText.textContent = 'HẾT';
            } else if (quantity <= 3) {
                statusElement.classList.add('bg-warning', 'text-dark');
                statusText.textContent = 'SẮP HẾT';
            } else {
                statusElement.classList.add('bg-success', 'text-white');
                statusText.textContent = 'CÒN';
            }
        });
    });

    // Form validation for measurements and stock
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let hasEmptyMeasurements = false;
        let hasErrors = false;
        
        const measurementInputs = document.querySelectorAll('input[name*="[measurements]"], input[name*="[length]"]');
        const requiredFields = form.querySelectorAll('[required]');
        
        // Check required fields
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                hasErrors = true;
                field.style.borderColor = '#FF0000';
                field.style.boxShadow = '0 0 0 3px rgba(255, 0, 0, 0.2)';
            } else {
                field.style.borderColor = '';
                field.style.boxShadow = '';
            }
        });
        
        // Check measurements
        measurementInputs.forEach(input => {
            if (input.value.trim() === '') {
                hasEmptyMeasurements = true;
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return;
        }
        
        if (hasEmptyMeasurements) {
            const confirmSubmit = confirm('Một số size chưa có thông tin số đo tổng quát hoặc chiều dài. Bạn có muốn tiếp tục không?');
            if (!confirmSubmit) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection
