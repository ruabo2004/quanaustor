<!-- Modern Pagination Component -->
@if ($products->hasPages())
    <div style="
        background: white;
        border-radius: var(--radius-xl);
        padding: var(--space-6);
        margin-top: var(--space-8);
        box-shadow: var(--shadow-sm);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: var(--space-4);
    ">
        <!-- Pagination Info -->
        <div style="
            color: var(--gray-600);
            font-size: 0.875rem;
        ">
            Hiển thị {{ $products->firstItem() }} đến {{ $products->lastItem() }} 
            trong tổng số {{ $products->total() }} sản phẩm
        </div>
        
        <!-- Pagination Links -->
        <nav style="display: flex; align-items: center; gap: var(--space-1);">
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
                <span style="
                    padding: var(--space-2) var(--space-3);
                    color: var(--gray-400);
                    cursor: not-allowed;
                    border-radius: var(--radius-md);
                    background: var(--gray-100);
                    font-size: 0.875rem;
                ">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" 
                   style="
                       padding: var(--space-2) var(--space-3);
                       color: var(--primary);
                       text-decoration: none;
                       border-radius: var(--radius-md);
                       background: white;
                       border: 1px solid var(--gray-200);
                       transition: all var(--transition-base);
                       font-size: 0.875rem;
                   "
                   onmouseover="this.style.background='var(--primary)'; this.style.color='white'; this.style.borderColor='var(--primary)'"
                   onmouseout="this.style.background='white'; this.style.color='var(--primary)'; this.style.borderColor='var(--gray-200)'">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max($products->currentPage() - 2, 1);
                $end = min($start + 4, $products->lastPage());
                $start = max($end - 4, 1);
            @endphp
            
            {{-- First Page --}}
            @if($start > 1)
                <a href="{{ $products->url(1) }}" 
                   style="
                       padding: var(--space-2) var(--space-3);
                       color: var(--gray-700);
                       text-decoration: none;
                       border-radius: var(--radius-md);
                       background: white;
                       border: 1px solid var(--gray-200);
                       transition: all var(--transition-base);
                       font-size: 0.875rem;
                       min-width: 36px;
                       text-align: center;
                   "
                   onmouseover="this.style.background='var(--gray-50)'"
                   onmouseout="this.style.background='white'">
                    1
                </a>
                @if($start > 2)
                    <span style="
                        padding: var(--space-2);
                        color: var(--gray-400);
                        font-size: 0.875rem;
                    ">...</span>
                @endif
            @endif
            
            {{-- Page Numbers --}}
            @for($page = $start; $page <= $end; $page++)
                @if ($page == $products->currentPage())
                    <span style="
                        padding: var(--space-2) var(--space-3);
                        background: var(--primary);
                        color: white;
                        border-radius: var(--radius-md);
                        font-weight: var(--font-bold);
                        font-size: 0.875rem;
                        min-width: 36px;
                        text-align: center;
                        box-shadow: var(--shadow-sm);
                    ">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $products->url($page) }}" 
                       style="
                           padding: var(--space-2) var(--space-3);
                           color: var(--gray-700);
                           text-decoration: none;
                           border-radius: var(--radius-md);
                           background: white;
                           border: 1px solid var(--gray-200);
                           transition: all var(--transition-base);
                           font-size: 0.875rem;
                           min-width: 36px;
                           text-align: center;
                       "
                       onmouseover="this.style.background='var(--primary)'; this.style.color='white'; this.style.borderColor='var(--primary)'; this.style.transform='translateY(-1px)'"
                       onmouseout="this.style.background='white'; this.style.color='var(--gray-700)'; this.style.borderColor='var(--gray-200)'; this.style.transform='translateY(0)'">
                        {{ $page }}
                    </a>
                @endif
            @endfor
            
            {{-- Last Page --}}
            @if($end < $products->lastPage())
                @if($end < $products->lastPage() - 1)
                    <span style="
                        padding: var(--space-2);
                        color: var(--gray-400);
                        font-size: 0.875rem;
                    ">...</span>
                @endif
                <a href="{{ $products->url($products->lastPage()) }}" 
                   style="
                       padding: var(--space-2) var(--space-3);
                       color: var(--gray-700);
                       text-decoration: none;
                       border-radius: var(--radius-md);
                       background: white;
                       border: 1px solid var(--gray-200);
                       transition: all var(--transition-base);
                       font-size: 0.875rem;
                       min-width: 36px;
                       text-align: center;
                   "
                   onmouseover="this.style.background='var(--gray-50)'"
                   onmouseout="this.style.background='white'">
                    {{ $products->lastPage() }}
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" 
                   style="
                       padding: var(--space-2) var(--space-3);
                       color: var(--primary);
                       text-decoration: none;
                       border-radius: var(--radius-md);
                       background: white;
                       border: 1px solid var(--gray-200);
                       transition: all var(--transition-base);
                       font-size: 0.875rem;
                   "
                   onmouseover="this.style.background='var(--primary)'; this.style.color='white'; this.style.borderColor='var(--primary)'"
                   onmouseout="this.style.background='white'; this.style.color='var(--primary)'; this.style.borderColor='var(--gray-200)'">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span style="
                    padding: var(--space-2) var(--space-3);
                    color: var(--gray-400);
                    cursor: not-allowed;
                    border-radius: var(--radius-md);
                    background: var(--gray-100);
                    font-size: 0.875rem;
                ">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </nav>
        
        <!-- Quick Jump -->
        @if($products->lastPage() > 10)
            <div style="
                display: flex;
                align-items: center;
                gap: var(--space-2);
                font-size: 0.875rem;
                color: var(--gray-600);
            ">
                <span>Đi đến trang:</span>
                <input type="number" 
                       id="jumpToPage"
                       min="1" 
                       max="{{ $products->lastPage() }}" 
                       value="{{ $products->currentPage() }}"
                       style="
                           width: 60px;
                           padding: var(--space-1) var(--space-2);
                           border: 1px solid var(--gray-300);
                           border-radius: var(--radius-md);
                           text-align: center;
                           font-size: 0.875rem;
                       "
                       onkeypress="if(event.key==='Enter') jumpToPage(this.value)">
                <button type="button" 
                        onclick="jumpToPage(document.getElementById('jumpToPage').value)"
                        style="
                            background: var(--primary);
                            color: white;
                            border: none;
                            border-radius: var(--radius-md);
                            padding: var(--space-1) var(--space-2);
                            font-size: 0.75rem;
                            cursor: pointer;
                            transition: all var(--transition-base);
                        "
                        onmouseover="this.style.background='var(--primary-dark)'"
                        onmouseout="this.style.background='var(--primary)'">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        @endif
    </div>
    
    <!-- Mobile Pagination (Simplified) -->
    <div style="
        display: none;
        background: white;
        border-radius: var(--radius-xl);
        padding: var(--space-4);
        margin-top: var(--space-6);
        box-shadow: var(--shadow-sm);
        text-align: center;
    " class="mobile-pagination">
        <div style="
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: var(--space-3);
        ">
            Trang {{ $products->currentPage() }} / {{ $products->lastPage() }}
        </div>
        
        <div style="display: flex; justify-content: center; gap: var(--space-2);">
            @if (!$products->onFirstPage())
                <a href="{{ $products->previousPageUrl() }}" 
                   style="
                       flex: 1;
                       max-width: 120px;
                       background: var(--primary);
                       color: white;
                       text-decoration: none;
                       border-radius: var(--radius-lg);
                       padding: var(--space-3);
                       text-align: center;
                       font-weight: var(--font-semibold);
                       transition: all var(--transition-base);
                   "
                   onmouseover="this.style.background='var(--primary-dark)'"
                   onmouseout="this.style.background='var(--primary)'">
                    <i class="fas fa-chevron-left me-2"></i>Trước
                </a>
            @endif
            
            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" 
                   style="
                       flex: 1;
                       max-width: 120px;
                       background: var(--primary);
                       color: white;
                       text-decoration: none;
                       border-radius: var(--radius-lg);
                       padding: var(--space-3);
                       text-align: center;
                       font-weight: var(--font-semibold);
                       transition: all var(--transition-base);
                   "
                   onmouseover="this.style.background='var(--primary-dark)'"
                   onmouseout="this.style.background='var(--primary)'">
                    Sau<i class="fas fa-chevron-right ms-2"></i>
                </a>
            @endif
        </div>
    </div>
@endif

<script>
function jumpToPage(page) {
    const maxPage = {{ $products->lastPage() }};
    const pageNum = parseInt(page);
    
    if (pageNum >= 1 && pageNum <= maxPage) {
        const url = new URL(window.location);
        url.searchParams.set('page', pageNum);
        window.location.href = url.toString();
    } else {
        alert(`Vui lòng nhập số trang từ 1 đến ${maxPage}`);
    }
}

// Show mobile pagination on small screens
function updatePaginationDisplay() {
    const isMobile = window.innerWidth <= 768;
    const desktopPagination = document.querySelector('nav');
    const mobilePagination = document.querySelector('.mobile-pagination');
    
    if (desktopPagination && mobilePagination) {
        if (isMobile) {
            desktopPagination.style.display = 'none';
            mobilePagination.style.display = 'block';
        } else {
            desktopPagination.style.display = 'flex';
            mobilePagination.style.display = 'none';
        }
    }
}

// Initial check and window resize listener
updatePaginationDisplay();
window.addEventListener('resize', updatePaginationDisplay);
</script>

<style>
@media (max-width: 768px) {
    .mobile-pagination {
        display: block !important;
    }
    
    nav {
        display: none !important;
    }
    
    /* Hide pagination info on mobile to save space */
    .pagination-info {
        display: none;
    }
}

@media (max-width: 640px) {
    /* Stack pagination elements vertically on very small screens */
    div[style*="display: flex; justify-content: space-between"] {
        flex-direction: column !important;
        gap: var(--space-3) !important;
        text-align: center !important;
    }
}
</style>
