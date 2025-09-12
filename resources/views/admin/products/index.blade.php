@extends('admin.layouts.admin')

@section('title', 'Quản Lý Sản Phẩm')
@section('page-title', 'QUẢN LÝ SẢN PHẨM')
@section('breadcrumb', 'Quản Lý / Sản Phẩm')

@section('content')
<div class="container-fluid">
    <!-- Header với nút thêm -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">📦 DANH SÁCH SẢN PHẨM</h3>
            <a href="{{ route('admin.products.create') }}" class="btn" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 600; text-transform: uppercase;">
                <i class="fas fa-plus me-2"></i>
                Thêm Sản Phẩm
            </a>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $products->total() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Tổng Sản Phẩm</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #10b981, #065f46); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $products->where('sizes_sum_stock_quantity', '>', 0)->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Còn Hàng</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $products->where('sizes_sum_stock_quantity', '<=', 5)->where('sizes_sum_stock_quantity', '>', 0)->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Sắp Hết</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $products->where('sizes_sum_stock_quantity', '<=', 0)->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Hết Hàng</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                        <tr>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Hình Ảnh</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Sản Phẩm</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Danh Mục</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Giá</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Tồn Kho</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Ngày Tạo</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 15px; vertical-align: middle;">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    @else
                                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image" style="color: #9ca3af; font-size: 20px;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div>
                                        <div style="font-weight: 600; color: #1f2937; font-size: 14px; margin-bottom: 4px;">{{ $product->name }}</div>
                                        <div style="font-size: 12px; color: #6b7280; line-height: 1.4;">{{ Str::limit($product->description, 50) }}</div>
                                        <div style="margin-top: 6px;">
                                            <span style="background: #e74c3c; color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 700; font-family: monospace;">
                                                #{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <span style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                        <i class="fas fa-tag" style="font-size: 8px; margin-right: 3px;"></i>{{ $product->category->name }}
                                    </span>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #059669; font-size: 16px;">
                                        {{ number_format($product->price) }}đ
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    @php $totalStock = $product->sizes_sum_stock_quantity ?? 0; @endphp
                                    @if($totalStock > 5)
                                        <span style="background: linear-gradient(135deg, #059669, #047857); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-check-circle" style="font-size: 8px; margin-right: 3px;"></i>{{ $totalStock }}
                                        </span>
                                    @elseif($totalStock > 0)
                                        <span style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-exclamation-triangle" style="font-size: 8px; margin-right: 3px;"></i>{{ $totalStock }}
                                        </span>
                                    @else
                                        <span style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-times-circle" style="font-size: 8px; margin-right: 3px;"></i>0
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; color: #6b7280; font-weight: 500;">
                                    <i class="fas fa-calendar me-2" style="color: #e74c3c;"></i>{{ $product->created_at->format('d/m/Y') }}
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 8px 12px; border-radius: 8px; border: none; font-size: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 50px;">
                                    <div style="color: #9ca3af;">
                                        <i class="fas fa-box" style="font-size: 48px; margin-bottom: 15px; color: #d1d5db;"></i>
                                        <h5 style="color: #6b7280; font-weight: 600;">Chưa Có Sản Phẩm Nào</h5>
                                        <p style="color: #9ca3af; margin: 0;">Hãy thêm sản phẩm đầu tiên để bắt đầu bán hàng</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* PUMA PAGINATION - MODERN DESIGN */
.pagination {
    gap: 8px;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    color: #ff0000;
    background: linear-gradient(135deg, #ffffff, #f8f9fa);
    border: 2px solid rgba(255, 0, 0, 0.2);
    padding: 12px 18px;
    border-radius: 12px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(255, 0, 0, 0.1);
    min-width: 50px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.pagination .page-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 0, 0.1), transparent);
    transition: left 0.5s ease;
}

.pagination .page-link:hover::before {
    left: 100%;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    color: white;
    border-color: #ff0000;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.3);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #ff0000, #e53e3e);
    border-color: #ff0000;
    color: white;
    box-shadow: 0 8px 25px rgba(255, 0, 0, 0.4);
    transform: translateY(-1px);
}

.pagination .page-item.disabled .page-link {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #6c757d;
    border-color: #dee2e6;
    box-shadow: none;
    cursor: not-allowed;
}

.pagination .page-item.disabled .page-link:hover {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    color: #6c757d;
    transform: none;
    box-shadow: none;
}

/* Previous/Next Buttons Special Styling */
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    font-size: 14px;
    padding: 12px 20px;
    font-weight: 900;
    background: linear-gradient(135deg, #ffd700, #ffb000);
    color: #1a1a1a;
    border-color: #ffd700;
}

.pagination .page-item:first-child .page-link:hover,
.pagination .page-item:last-child .page-link:hover {
    background: linear-gradient(135deg, #ffb000, #ffd700);
    color: #1a1a1a;
    border-color: #ffb000;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .pagination .page-link {
        padding: 10px 14px;
        font-size: 12px;
        min-width: 40px;
    }
    
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        padding: 10px 16px;
    }
}
</style>
@endsection