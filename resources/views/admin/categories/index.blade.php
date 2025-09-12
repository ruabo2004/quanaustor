@extends('admin.layouts.admin')

@section('title', 'Quản Lý Danh Mục')
@section('page-title', 'QUẢN LÝ DANH MỤC')
@section('breadcrumb', 'Quản Lý / Danh Mục')

@section('content')
<div class="container-fluid">
    <!-- Header với nút thêm -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">🏷️ DANH SÁCH DANH MỤC</h3>
            <a href="{{ route('admin.categories.create') }}" class="btn" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 600; text-transform: uppercase;">
                <i class="fas fa-plus me-2"></i>
                Thêm Danh Mục
            </a>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-tags fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $categories->total() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Tổng Danh Mục</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #10b981, #065f46); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-box fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $categories->sum('products_count') }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Tổng Sản Phẩm</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #8b5cf6, #5b21b6); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $categories->where('products_count', '>', 0)->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Có Sản Phẩm</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none;">
                <div class="card-body text-center">
                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                    <h4 style="margin: 0; font-weight: 900;">{{ $categories->where('created_at', '>=', now()->subDays(7))->count() }}</h4>
                    <p style="margin: 0; opacity: 0.9; font-size: 12px; text-transform: uppercase;">Mới Tuần Này</p>
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
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">ID</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Tên Danh Mục</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Số Sản Phẩm</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Ngày Tạo</th>
                            <th style="border: none; padding: 15px; font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 15px; vertical-align: middle;">
                                    <span style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; font-family: monospace;">
                                        #{{ str_pad($category->id, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #8b5cf6, #5b21b6); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-weight: 700; color: white; font-size: 16px;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1f2937; font-size: 14px;">{{ $category->name }}</div>
                                            <div style="font-size: 12px; color: #6b7280;">Danh mục ID: {{ $category->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    @if($category->products_count > 0)
                                        <span style="background: linear-gradient(135deg, #059669, #047857); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-box" style="font-size: 8px; margin-right: 3px;"></i>{{ $category->products_count }}
                                        </span>
                                    @else
                                        <span style="background: linear-gradient(135deg, #6b7280, #4b5563); color: white; padding: 3px 8px; border-radius: 8px; font-size: 10px; font-weight: 600; display: inline-block;">
                                            <i class="fas fa-inbox" style="font-size: 8px; margin-right: 3px;"></i>0
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 15px; vertical-align: middle; color: #6b7280; font-weight: 500;">
                                    <i class="fas fa-calendar me-2" style="color: #e74c3c;"></i>{{ $category->created_at->format('d/m/Y') }}
                                </td>
                                <td style="padding: 15px; vertical-align: middle;">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($category->products_count == 0)
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 8px 12px; border-radius: 8px; border: none; font-size: 12px; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span style="background: #6b7280; color: white; padding: 8px 12px; border-radius: 8px; font-size: 12px; opacity: 0.5;" title="Không thể xóa danh mục có sản phẩm">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 50px;">
                                    <div style="color: #9ca3af;">
                                        <i class="fas fa-tags" style="font-size: 48px; margin-bottom: 15px; color: #d1d5db;"></i>
                                        <h5 style="color: #6b7280; font-weight: 600;">Chưa Có Danh Mục Nào</h5>
                                        <p style="color: #9ca3af; margin: 0;">Hãy tạo danh mục đầu tiên để phân loại sản phẩm</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($categories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <div style="background: white; padding: 15px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                        {{ $categories->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.pagination .page-link {
    color: #e74c3c;
    border: 1px solid #e74c3c;
    padding: 8px 15px;
    margin: 0 3px;
    border-radius: 8px;
    font-weight: 600;
}

.pagination .page-link:hover {
    background-color: #e74c3c;
    color: white;
}

.pagination .page-item.active .page-link {
    background-color: #e74c3c;
    border-color: #e74c3c;
    color: white;
}
</style>
@endsection