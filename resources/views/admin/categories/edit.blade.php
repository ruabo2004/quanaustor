@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa danh mục')
@section('page-title', 'Chỉnh sửa danh mục')
@section('breadcrumb', 'Quản lý / Danh mục / Chỉnh sửa')

@section('content')
<div class="container-fluid">
<style>
/* PUMA CATEGORY EDIT - COMPACT & MODERN */
.puma-edit-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 1rem;
}

.puma-edit-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid rgba(255, 0, 0, 0.08);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(255, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}

.puma-edit-card:hover {
    box-shadow: 0 12px 48px rgba(255, 0, 0, 0.15);
    transform: translateY(-2px);
}

.puma-edit-header {
    background: linear-gradient(135deg, #ff0000 0%, #e53e3e 100%);
    color: white;
    padding: 1.5rem;
    border-bottom: none;
}

.puma-edit-body {
    padding: 2rem;
}

.puma-back-btn {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    padding: 0.6rem 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.puma-back-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    text-decoration: none;
    transform: translateX(-3px);
}

.puma-edit-title {
    color: white;
    font-size: 1.4rem;
    font-weight: 900;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: 'Inter', sans-serif;
}

.puma-edit-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin: 0.3rem 0 0 0;
    font-weight: 500;
}

.puma-edit-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    align-items: start;
}

.puma-form-section {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border: 2px solid rgba(255, 0, 0, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
}

.puma-form-title {
    color: #ff0000;
    font-size: 1rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.puma-form-label {
    color: #1a1a1a;
    font-weight: 700;
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    font-family: 'Inter', sans-serif;
}

.puma-form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 2px solid rgba(255, 0, 0, 0.1);
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #ffffff;
    color: #1a1a1a;
    font-weight: 500;
}

.puma-form-control:focus {
    outline: none;
    border-color: #ff0000;
    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
    background: #ffffff;
}

.puma-form-help {
    color: #6b7280;
    font-size: 0.8rem;
    margin-top: 0.3rem;
    font-weight: 500;
}

.puma-alert {
    background: rgba(255, 0, 0, 0.05);
    border: 2px solid rgba(255, 0, 0, 0.1);
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.puma-alert-icon {
    color: #ff0000;
    font-size: 1.1rem;
}

.puma-alert-content {
    flex: 1;
}

.puma-alert-title {
    color: #1a1a1a;
    font-weight: 700;
    margin: 0;
    font-size: 0.9rem;
}

.puma-alert-text {
    color: #4a5568;
    margin: 0;
    font-size: 0.85rem;
    font-weight: 500;
}

.puma-stats-section {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border: 2px solid rgba(255, 0, 0, 0.05);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
}

.puma-category-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem auto;
    color: white;
    font-size: 2rem;
    box-shadow: 0 4px 16px rgba(255, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.puma-category-icon:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 24px rgba(255, 0, 0, 0.4);
}

.puma-category-name {
    color: #1a1a1a;
    font-weight: 900;
    margin-bottom: 0.3rem;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
}

.puma-category-id {
    color: #6b7280;
    font-size: 0.8rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.puma-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.puma-stat-box {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    color: white;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
}

.puma-stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(255, 0, 0, 0.3);
}

.puma-stat-number {
    font-size: 1.5rem;
    font-weight: 900;
    margin-bottom: 0.2rem;
    font-family: 'Inter', sans-serif;
}

.puma-stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}

.puma-btn {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.8rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: 'Inter', sans-serif;
    width: 100%;
    justify-content: center;
    margin-bottom: 0.8rem;
}

.puma-btn:hover {
    background: linear-gradient(135deg, #e53e3e, #ff0000);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(255, 0, 0, 0.3);
    color: white;
    text-decoration: none;
}

.puma-btn-secondary {
    background: linear-gradient(135deg, #ffd700, #ffb000);
    color: #1a1a1a;
}

.puma-btn-secondary:hover {
    background: linear-gradient(135deg, #ffb000, #ffd700);
    color: #1a1a1a;
}

.puma-btn-outline {
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    color: #1a1a1a;
    border: 2px solid rgba(255, 0, 0, 0.2);
}

.puma-btn-outline:hover {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-color: #ff0000;
    color: #1a1a1a;
}

.puma-btn-danger {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.puma-btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c, #dc2626);
}

.puma-btn:disabled {
    background: linear-gradient(135deg, #e5e7eb, #d1d5db);
    color: #9ca3af;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.puma-error-text {
    color: #dc2626;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.3rem;
}

@media (max-width: 768px) {
    .puma-edit-container {
        padding: 0.5rem;
    }
    
    .puma-edit-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .puma-edit-header {
        padding: 1rem;
    }
    
    .puma-edit-body {
        padding: 1.5rem;
    }
    
    .puma-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

    <div class="puma-edit-card">
        <!-- Header -->
        <div class="puma-edit-header">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <a href="{{ route('admin.categories') }}" class="puma-back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                    <div>
                        <h3 class="puma-edit-title">🏷️ Chỉnh sửa danh mục</h3>
                        <p class="puma-edit-subtitle">{{ $category->name }}</p>
                    </div>
                </div>
                <div>
                    <span style="background: rgba(255,255,255,0.2); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; font-size: 0.9rem;">
                        #{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="puma-edit-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="puma-edit-grid">
                    <!-- Form Section -->
                    <div class="puma-form-section">
                        <h4 class="puma-form-title">
                            <i class="fas fa-edit"></i>
                            Thông tin danh mục
                        </h4>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label for="name" class="puma-form-label">Tên danh mục</label>
                            <input type="text" id="name" class="puma-form-control" name="name" 
                                   value="{{ old('name', $category->name) }}" required 
                                   placeholder="Nhập tên danh mục...">
                            <div class="puma-form-help">Nhập tên danh mục sản phẩm</div>
                            @error('name')
                                <div class="puma-error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Alert -->
                        <div class="puma-alert">
                            <i class="fas fa-info-circle puma-alert-icon"></i>
                            <div class="puma-alert-content">
                                <p class="puma-alert-title">LƯU Ý QUAN TRỌNG</p>
                                <p class="puma-alert-text">Thay đổi tên danh mục sẽ ảnh hưởng đến tất cả sản phẩm thuộc danh mục này.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="puma-stats-section">
                        <!-- Category Icon -->
                        <div class="puma-category-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        
                        <h4 class="puma-category-name">{{ $category->name }}</h4>
                        <p class="puma-category-id">ID: {{ $category->id }}</p>

                        <!-- Stats Grid -->
                        <div class="puma-stats-grid">
                            <div class="puma-stat-box">
                                <div class="puma-stat-number">{{ $category->products->count() }}</div>
                                <div class="puma-stat-label">Sản phẩm</div>
                            </div>
                            <div class="puma-stat-box">
                                <div class="puma-stat-number">{{ $category->products->sum(function($product) { return $product->sizes->sum('stock_quantity'); }) }}</div>
                                <div class="puma-stat-label">Tồn kho</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <button type="submit" class="puma-btn">
                            <i class="fas fa-save"></i>
                            Cập nhật danh mục
                        </button>
                        
                        <a href="{{ route('admin.categories') }}" class="puma-btn puma-btn-outline">
                            <i class="fas fa-times"></i>
                            Hủy bỏ
                        </a>
                        
                        @if($category->products->count() == 0)
                            <button type="button" class="puma-btn puma-btn-danger" 
                                    onclick="if(confirm('Bạn có chắc muốn xóa danh mục này?')) { document.getElementById('delete-form').submit(); }">
                                <i class="fas fa-trash"></i>
                                Xóa danh mục
                            </button>
                        @else
                            <button type="button" class="puma-btn" disabled>
                                <i class="fas fa-lock"></i>
                                Không thể xóa
                            </button>
                            <small style="color: #6b7280; font-size: 0.8rem; text-align: center; display: block; margin-top: 0.5rem;">
                                Danh mục có sản phẩm không thể xóa
                            </small>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Delete Form -->
            @if($category->products->count() == 0)
                <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
</div>
@endsection