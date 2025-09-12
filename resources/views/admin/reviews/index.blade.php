@extends('admin.layouts.admin')

@section('title', 'Quản lý đánh giá')
@section('page-title', 'Quản lý đánh giá')
@section('breadcrumb', 'Quản lý / Đánh giá')

@section('content')
<style>
/* ====================================
   PUMA REVIEWS MANAGEMENT - MODERN THEME
   ==================================== */

.puma-reviews-container {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Stats Cards */
.puma-stats-row {
    margin-bottom: 30px;
}

.puma-stat-card {
    background: linear-gradient(145deg, 
        rgba(255, 255, 255, 0.98) 0%, 
        rgba(248, 250, 252, 0.95) 100%);
    border-radius: 15px;
    padding: 25px;
    box-shadow: 
        0 10px 25px rgba(0, 0, 0, 0.08),
        0 4px 10px rgba(255, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    border: 2px solid transparent;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.puma-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 15px 35px rgba(0, 0, 0, 0.12),
        0 6px 15px rgba(255, 0, 0, 0.1);
}

.puma-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, 
        var(--puma-red) 0%, 
        var(--puma-gold) 50%, 
        var(--puma-red) 100%);
    background-size: 200% 100%;
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.puma-stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 15px;
    background: linear-gradient(135deg, var(--puma-red), #dc2626);
    color: white;
    box-shadow: 0 6px 15px rgba(255, 0, 0, 0.3);
}

.puma-stat-number {
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    font-size: 2.5rem;
    color: var(--puma-black);
    margin: 0;
    line-height: 1;
}

.puma-stat-label {
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 5px 0 0 0;
}

/* Main Card */
.puma-reviews-card {
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
}

/* Header */
.puma-reviews-header {
    background: linear-gradient(135deg, 
        var(--puma-red) 0%, 
        #dc2626 100%);
    color: white;
    padding: 25px 30px;
    border-radius: 20px 20px 0 0;
    position: relative;
    overflow: hidden;
}

.puma-reviews-header::before {
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

.puma-reviews-title {
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
    font-size: 1.4rem;
}

/* Filter Tabs */
.puma-filter-tabs {
    padding: 20px 30px;
    border-bottom: 1px solid #e2e8f0;
    background: rgba(248, 250, 252, 0.5);
}

.puma-tab {
    padding: 10px 20px;
    border-radius: 25px;
    border: 2px solid #e2e8f0;
    background: white;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    margin-right: 10px;
    display: inline-block;
}

.puma-tab:hover {
    border-color: var(--puma-red);
    color: var(--puma-red);
    text-decoration: none;
    transform: translateY(-1px);
}

.puma-tab.active {
    background: linear-gradient(135deg, var(--puma-red) 0%, #dc2626 100%);
    color: white;
    border-color: var(--puma-red);
}

/* Review Items */
.puma-reviews-content {
    padding: 30px;
}

.puma-review-item {
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    position: relative;
}

.puma-review-item:hover {
    border-color: #e2e8f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}

.puma-review-item.pending {
    border-left: 4px solid #fbbf24;
    background: linear-gradient(135deg, 
        rgba(251, 191, 36, 0.05) 0%, 
        rgba(255, 255, 255, 0.98) 100%);
}

.puma-review-item.approved {
    border-left: 4px solid #10b981;
    background: linear-gradient(135deg, 
        rgba(16, 185, 129, 0.05) 0%, 
        rgba(255, 255, 255, 0.98) 100%);
}

.puma-review-header {
    display: flex;
    justify-content: between;
    align-items: start;
    margin-bottom: 15px;
}

.puma-review-user {
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    color: var(--puma-black);
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.puma-review-product {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0 0 8px 0;
}

.puma-review-rating {
    margin-bottom: 10px;
}

.puma-review-comment {
    color: #374151;
    line-height: 1.6;
    margin: 15px 0;
    font-size: 0.95rem;
}

.puma-review-meta {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

.puma-review-date {
    color: #6b7280;
    font-size: 0.8rem;
}

.puma-status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.puma-status-pending {
    background: #fef3c7;
    color: #92400e;
}

.puma-status-approved {
    background: #d1fae5;
    color: #065f46;
}

/* Action Buttons */
.puma-actions {
    display: flex;
    gap: 8px;
}

.puma-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    border: none;
    cursor: pointer;
}

.puma-btn-approve {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.puma-btn-approve:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    text-decoration: none;
    color: white;
}

.puma-btn-reject {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.puma-btn-reject:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    text-decoration: none;
    color: white;
}

.puma-btn-delete {
    background: linear-gradient(135deg, var(--puma-red) 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

.puma-btn-delete:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
    text-decoration: none;
    color: white;
}

/* Pagination */
.puma-pagination {
    padding: 20px 30px;
    border-top: 1px solid #e2e8f0;
    background: rgba(248, 250, 252, 0.5);
}

/* Empty State */
.puma-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.puma-empty-icon {
    font-size: 4rem;
    color: #e5e7eb;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .puma-reviews-header {
        padding: 20px;
    }
    
    .puma-reviews-title {
        font-size: 1.2rem;
    }
    
    .puma-reviews-content {
        padding: 20px;
    }
    
    .puma-review-header {
        flex-direction: column;
        align-items: start;
    }
    
    .puma-review-meta {
        flex-direction: column;
        align-items: start;
        gap: 10px;
    }
    
    .puma-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .puma-btn {
        justify-content: center;
    }
}
</style>

<div class="container-fluid puma-reviews-container">
    <!-- Stats Row -->
    <div class="row puma-stats-row">
        <div class="col-lg-6">
            <div class="puma-stat-card">
                <div class="puma-stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="puma-stat-number">{{ $pendingCount }}</h3>
                <p class="puma-stat-label">Chờ duyệt</p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="puma-stat-card">
                <div class="puma-stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="puma-stat-number">{{ $approvedCount }}</h3>
                <p class="puma-stat-label">Đã duyệt</p>
            </div>
        </div>
    </div>

    <!-- Main Reviews Card -->
    <div class="puma-reviews-card">
        <!-- Header -->
        <div class="puma-reviews-header">
            <h3 class="puma-reviews-title">
                <i class="fas fa-star me-2"></i>Quản lý đánh giá sản phẩm
            </h3>
        </div>

        <!-- Filter Tabs -->
        <div class="puma-filter-tabs">
            <a href="{{ route('admin.reviews') }}" class="puma-tab {{ !request('status') ? 'active' : '' }}">
                <i class="fas fa-list me-1"></i>Tất cả
            </a>
            <a href="{{ route('admin.reviews') }}?status=pending" class="puma-tab {{ request('status') === 'pending' ? 'active' : '' }}">
                <i class="fas fa-clock me-1"></i>Chờ duyệt
            </a>
            <a href="{{ route('admin.reviews') }}?status=approved" class="puma-tab {{ request('status') === 'approved' ? 'active' : '' }}">
                <i class="fas fa-check-circle me-1"></i>Đã duyệt
            </a>
        </div>

        <!-- Reviews Content -->
        <div class="puma-reviews-content">
            @if($reviews->count() > 0)
                @foreach($reviews as $review)
                    <div class="puma-review-item {{ $review->approved ? 'approved' : 'pending' }}">
                        <div class="puma-review-header">
                            <div class="flex-grow-1">
                                <h5 class="puma-review-user">{{ $review->user->name }}</h5>
                                <p class="puma-review-product">
                                    <i class="fas fa-tshirt me-1"></i>
                                    <a href="{{ route('products.show', $review->product) }}" target="_blank" style="color: var(--puma-red); text-decoration: none;">
                                        {{ $review->product->name }}
                                    </a>
                                </p>
                                <div class="puma-review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star" style="color: {{ $i <= $review->rating ? '#ffc107' : '#e9ecef' }}; font-size: 14px;"></i>
                                    @endfor
                                    <span style="color: #6b7280; margin-left: 6px; font-size: 0.9rem;">{{ $review->rating }}/5</span>
                                </div>
                            </div>
                            <div>
                                <span class="puma-status-badge {{ $review->approved ? 'puma-status-approved' : 'puma-status-pending' }}">
                                    {{ $review->approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                                </span>
                            </div>
                        </div>

                        @if($review->comment)
                            <div class="puma-review-comment">
                                "{{ $review->comment }}"
                            </div>
                        @endif

                        <div class="puma-review-meta">
                            <div class="puma-review-date">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $review->created_at->format('d/m/Y H:i') }}
                            </div>
                            
                            <div class="puma-actions">
                                @if(!$review->approved)
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="puma-btn puma-btn-approve">
                                            <i class="fas fa-check me-1"></i>Duyệt
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="puma-btn puma-btn-reject">
                                            <i class="fas fa-times me-1"></i>Từ chối
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="puma-btn puma-btn-delete">
                                        <i class="fas fa-trash me-1"></i>Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="puma-empty-state">
                    <div class="puma-empty-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 style="color: #374151; font-weight: 600; margin-bottom: 10px;">Chưa có đánh giá nào</h5>
                    <p style="margin: 0;">Các đánh giá từ khách hàng sẽ hiển thị tại đây</p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="puma-pagination">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
