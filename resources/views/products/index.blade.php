@extends('layouts.app')

@section('content')
<!-- Advanced Search & Filter Hero -->
<section class="modern-hero" style="padding: var(--space-8) 0; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
    <div class="container">
        <div class="text-center">
            <div class="animate-fadeInUp">
                <h1 style="color: white; font-size: clamp(1.5rem, 4vw, 2.5rem); font-weight: var(--font-black); margin-bottom: var(--space-3);">
                    <i class="fas fa-search me-3"></i>Khám phá sản phẩm
                </h1>
                <p style="color: rgba(255,255,255,0.9); font-size: 1.125rem; margin-bottom: 0;">
                    Tìm kiếm thông minh với bộ lọc nâng cao - {{ $filterCounts['total'] }} sản phẩm
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Advanced Search Container -->
<section style="background: var(--gray-50); padding: var(--space-6) 0;">
    <div class="container">
        <!-- Main Search & Filter Panel -->
        <div class="advanced-search-container">
            <!-- Search Header with Toggle -->
            <div class="search-header">
                <h3 class="search-title">
                    <i class="fas fa-filter"></i>
                    Tìm kiếm & Lọc sản phẩm
                </h3>
                <button type="button" class="filter-toggle-btn" id="filterToggle">
                    <i class="fas fa-sliders-h"></i>
                    Bộ lọc nâng cao
                </button>
            </div>

            <!-- Main Search Box with Autocomplete -->
            <div style="padding: var(--space-6); background: white;">
                <form method="GET" action="{{ route('products.index') }}" id="advancedFilterForm">
                    <div class="search-box-advanced">
                        <i class="fas fa-search search-icon-advanced"></i>
                        <input type="text" 
                               class="search-input-advanced" 
                               name="search" 
                               id="searchInput"
                               placeholder="Tìm kiếm theo tên, mô tả, chất liệu, danh mục..." 
                               value="{{ request('search') }}"
                               autocomplete="off">
                        <button type="button" class="search-clear-btn" id="searchClear">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <!-- Autocomplete Dropdown -->
                        <div class="autocomplete-dropdown" id="autocompleteDropdown">
                            <!-- Dynamic suggestions will be inserted here -->
                        </div>
                    </div>

                    <!-- Quick Filters -->
                    <div class="quick-filters" style="margin-top: var(--space-4);">
                        <button type="button" class="quick-filter-btn {{ !request()->hasAny(['search', 'categories', 'styles', 'materials', 'fits', 'sizes', 'price_range', 'min_price', 'max_price']) ? 'active' : '' }}" data-filter="all">
                            <i class="fas fa-th"></i> Tất cả
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('price_range') == 'under_500k' ? 'active' : '' }}" data-filter="price_range" data-value="under_500k">
                            <i class="fas fa-tag"></i> Dưới 500K
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('price_range') == '500k_1m' ? 'active' : '' }}" data-filter="price_range" data-value="500k_1m">
                            <i class="fas fa-tags"></i> 500K - 1M
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('price_range') == '1m_2m' ? 'active' : '' }}" data-filter="price_range" data-value="1m_2m">
                            <i class="fas fa-star"></i> 1M - 2M
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('price_range') == 'over_2m' ? 'active' : '' }}" data-filter="price_range" data-value="over_2m">
                            <i class="fas fa-crown"></i> Trên 2M
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('availability') == 'in_stock' ? 'active' : '' }}" data-filter="availability" data-value="in_stock">
                            <i class="fas fa-check-circle"></i> Còn hàng
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('sort') == 'newest' ? 'active' : '' }}" data-filter="sort" data-value="newest">
                            <i class="fas fa-clock"></i> Mới nhất
                        </button>
                        <button type="button" class="quick-filter-btn {{ request('sort') == 'popularity' ? 'active' : '' }}" data-filter="sort" data-value="popularity">
                            <i class="fas fa-fire"></i> Phổ biến
                        </button>
                    </div>

                    <!-- Advanced Filter Panel (Hidden by Default) -->
                    <div class="filter-panel" id="filterPanel">
                        <div class="filter-sections">
                            <!-- Categories Filter -->
                            <div class="filter-section">
                                <h4 class="filter-section-title">
                                    <i class="fas fa-list"></i>
                                    Danh mục
                                </h4>
                                <div class="checkbox-filters">
                                    @foreach($categories as $category)
                                        <label class="checkbox-filter-item">
                                            <div class="custom-checkbox">
                                                <input type="checkbox" 
                                                       name="categories[]" 
                                                       value="{{ $category->id }}"
                                                       {{ in_array($category->id, (array) request('categories', [])) ? 'checked' : '' }}>
                                                <span class="checkmark"></span>
                                            </div>
                                            <span class="filter-item-label">{{ $category->name }}</span>
                                            <span class="filter-item-count">{{ $category->products_count ?? 0 }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range Filter -->
                            <div class="filter-section">
                                <h4 class="filter-section-title">
                                    <i class="fas fa-dollar-sign"></i>
                                    Khoảng giá
                                </h4>
                                <div class="price-range-container">
                                    <div class="price-range-inputs">
                                        <input type="number" 
                                               class="price-input" 
                                               name="min_price" 
                                               placeholder="Từ {{ number_format($priceStats->min_price ?? 0) }}"
                                               value="{{ request('min_price') }}"
                                               min="{{ $priceStats->min_price ?? 0 }}"
                                               max="{{ $priceStats->max_price ?? 0 }}">
                                        <input type="number" 
                                               class="price-input" 
                                               name="max_price" 
                                               placeholder="Đến {{ number_format($priceStats->max_price ?? 0) }}"
                                               value="{{ request('max_price') }}"
                                               min="{{ $priceStats->min_price ?? 0 }}"
                                               max="{{ $priceStats->max_price ?? 0 }}">
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--gray-500); text-align: center; margin-top: var(--space-2);">
                                        Giá trung bình: {{ number_format($priceStats->avg_price ?? 0) }} VNĐ
                                    </div>
                                </div>
                            </div>

                            <!-- Sizes Filter -->
                            <div class="filter-section">
                                <h4 class="filter-section-title">
                                    <i class="fas fa-ruler"></i>
                                    Kích cỡ
                                </h4>
                                <div class="size-filters">
                                    @foreach($availableSizes as $size)
                                        <button type="button" 
                                                class="size-filter-pill {{ in_array($size->size, (array) request('sizes', [])) ? 'active' : '' }}"
                                                data-size="{{ $size->size }}"
                                                title="{{ $size->product_count }} sản phẩm, {{ $size->total_stock }} tồn kho">
                                            {{ $size->size }}
                                            <small style="display: block; font-size: 0.6rem; opacity: 0.8;">{{ $size->product_count }}</small>
                                        </button>
                                    @endforeach
                                </div>
                                <!-- Hidden input for sizes -->
                                <div id="selectedSizes" style="display: none;">
                                    @foreach((array) request('sizes', []) as $selectedSize)
                                        <input type="hidden" name="sizes[]" value="{{ $selectedSize }}">
                                    @endforeach
                                </div>
                            </div>

                            <!-- Styles Filter -->
                            <div class="filter-section">
                                <h4 class="filter-section-title">
                                    <i class="fas fa-palette"></i>
                                    Phong cách
                                </h4>
                                <div class="checkbox-filters">
                                    @foreach($styles as $style)
                                        <label class="checkbox-filter-item">
                                            <div class="custom-checkbox">
                                                <input type="checkbox" 
                                                       name="styles[]" 
                                                       value="{{ $style->style }}"
                                                       {{ in_array($style->style, (array) request('styles', [])) ? 'checked' : '' }}>
                                                <span class="checkmark"></span>
                                            </div>
                                            <span class="filter-item-label">{{ $style->style }}</span>
                                            <span class="filter-item-count">{{ $style->count }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Materials Filter -->
                            <div class="filter-section">
                                <h4 class="filter-section-title">
                                    <i class="fas fa-tshirt"></i>
                                    Chất liệu
                                </h4>
                                <div class="checkbox-filters">
                                    @foreach($materials as $material)
                                        <label class="checkbox-filter-item">
                                            <div class="custom-checkbox">
                                                <input type="checkbox" 
                                                       name="materials[]" 
                                                       value="{{ $material->material }}"
                                                       {{ in_array($material->material, (array) request('materials', [])) ? 'checked' : '' }}>
                                                <span class="checkmark"></span>
                                            </div>
                                            <span class="filter-item-label">{{ $material->material }}</span>
                                            <span class="filter-item-count">{{ $material->count }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Fits Filter -->
                            <div class="filter-section">
                                <h4 class="filter-section-title">
                                    <i class="fas fa-user"></i>
                                    Kiểu dáng
                                </h4>
                                <div class="checkbox-filters">
                                    @foreach($fits as $fit)
                                        <label class="checkbox-filter-item">
                                            <div class="custom-checkbox">
                                                <input type="checkbox" 
                                                       name="fits[]" 
                                                       value="{{ $fit->fit }}"
                                                       {{ in_array($fit->fit, (array) request('fits', [])) ? 'checked' : '' }}>
                                                <span class="checkmark"></span>
                                            </div>
                                            <span class="filter-item-label">{{ $fit->fit }}</span>
                                            <span class="filter-item-count">{{ $fit->count }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-6); padding-top: var(--space-4); border-top: 1px solid var(--gray-200);">
                            <button type="button" class="clear-all-filters" id="clearAllFilters">
                                <i class="fas fa-times"></i> Xóa tất cả bộ lọc
                            </button>
                            <div style="display: flex; gap: var(--space-3);">
                                <button type="button" class="filter-toggle-btn" id="hideFilters">
                                    <i class="fas fa-eye-slash"></i> Ẩn bộ lọc
                                </button>
                                <button type="submit" style="background: var(--primary); color: white; border: none; border-radius: var(--radius-lg); padding: var(--space-3) var(--space-5); font-weight: var(--font-semibold);">
                                    <i class="fas fa-search"></i> Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['search', 'categories', 'styles', 'materials', 'fits', 'sizes', 'price_range', 'min_price', 'max_price', 'availability']))
            <div class="active-filters">
                @if(request('search'))
                    <span class="active-filter-tag">
                        <i class="fas fa-search"></i>
                        "{{ request('search') }}"
                        <button type="button" class="active-filter-remove" data-filter="search">×</button>
                    </span>
                @endif
                
                @if(request('price_range'))
                    <span class="active-filter-tag">
                        <i class="fas fa-dollar-sign"></i>
                        @switch(request('price_range'))
                            @case('under_500k') Dưới 500K @break
                            @case('500k_1m') 500K - 1M @break
                            @case('1m_2m') 1M - 2M @break
                            @case('over_2m') Trên 2M @break
                        @endswitch
                        <button type="button" class="active-filter-remove" data-filter="price_range">×</button>
                    </span>
                @endif

                @foreach((array) request('categories', []) as $categoryId)
                    @php $category = $categories->find($categoryId); @endphp
                    @if($category)
                        <span class="active-filter-tag">
                            <i class="fas fa-list"></i>
                            {{ $category->name }}
                            <button type="button" class="active-filter-remove" data-filter="categories" data-value="{{ $categoryId }}">×</button>
                        </span>
                    @endif
                @endforeach

                @foreach((array) request('sizes', []) as $size)
                    <span class="active-filter-tag">
                        <i class="fas fa-ruler"></i>
                        Size {{ $size }}
                        <button type="button" class="active-filter-remove" data-filter="sizes" data-value="{{ $size }}">×</button>
                    </span>
                @endforeach

                <button type="button" class="clear-all-filters" onclick="window.location.href='{{ route('products.index') }}'">
                    <i class="fas fa-times"></i> Xóa tất cả
                </button>
            </div>
        @endif

        <!-- Sort and View Options -->
        <div class="sort-view-container">
            <div class="results-info">
                Hiển thị <span class="results-count">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> 
                trong tổng số <span class="results-count">{{ $products->total() }}</span> sản phẩm
            </div>
            
            <div class="sort-controls">
                <select name="sort" class="sort-select" onchange="submitSort(this.value)">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Phổ biến nhất</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Giá thấp → cao</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Giá cao → thấp</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A → Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z → A</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao</option>
                </select>
                
                <div class="view-toggle">
                    <button type="button" class="view-btn active" data-view="grid" title="Lưới">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="view-btn" data-view="list" title="Danh sách">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
                
                <select name="per_page" class="per-page-select" onchange="submitPerPage(this.value)">
                    <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 / trang</option>
                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 / trang</option>
                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 / trang</option>
                    <option value="96" {{ request('per_page') == 96 ? 'selected' : '' }}>96 / trang</option>
                </select>
            </div>
        </div>

        <!-- Loading State -->
        <div class="search-loading" id="searchLoading">
            <div class="loading-spinner"></div>
            <span>Đang tìm kiếm...</span>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer">
            @include('products.partials.product-grid')
        </div>

        <!-- Pagination -->
        <div id="paginationContainer">
            @include('products.partials.pagination')
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initAdvancedSearch();
});

function initAdvancedSearch() {
    // Filter toggle
    const filterToggle = document.getElementById('filterToggle');
    const filterPanel = document.getElementById('filterPanel');
    const hideFilters = document.getElementById('hideFilters');
    
    filterToggle?.addEventListener('click', function() {
        filterPanel.classList.toggle('show');
        this.classList.toggle('active');
    });
    
    hideFilters?.addEventListener('click', function() {
        filterPanel.classList.remove('show');
        filterToggle.classList.remove('active');
    });

    // Search input with autocomplete
    const searchInput = document.getElementById('searchInput');
    const autocompleteDropdown = document.getElementById('autocompleteDropdown');
    const searchClear = document.getElementById('searchClear');
    
    let searchTimeout;
    
    searchInput?.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        } else {
            autocompleteDropdown.classList.remove('show');
        }
    });

    searchClear?.addEventListener('click', function() {
        searchInput.value = '';
        autocompleteDropdown.classList.remove('show');
        submitFilters();
    });

    // Quick filters
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const value = this.dataset.value;
            
            if (filter === 'all') {
                window.location.href = '{{ route("products.index") }}';
                return;
            }
            
            // Remove active from siblings
            this.parentElement.querySelectorAll('.quick-filter-btn').forEach(b => {
                b.classList.remove('active');
            });
            
            // Add active to clicked
            this.classList.add('active');
            
            // Submit with filter
            const form = document.getElementById('advancedFilterForm');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = filter;
            input.value = value;
            form.appendChild(input);
            form.submit();
        });
    });

    // Size filter pills
    document.querySelectorAll('.size-filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            const size = this.dataset.size;
            this.classList.toggle('active');
            
            updateSizeInputs();
        });
    });

    // Active filter removal
    document.querySelectorAll('.active-filter-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            const value = this.dataset.value;
            
            removeFilter(filter, value);
        });
    });

    // Clear all filters
    document.getElementById('clearAllFilters')?.addEventListener('click', function() {
        window.location.href = '{{ route("products.index") }}';
    });

    // Form submission with AJAX
    const form = document.getElementById('advancedFilterForm');
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        submitFilters();
    });
}

function fetchSuggestions(query) {
    fetch(`{{ route('products.search.suggestions') }}?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(suggestions => {
            displaySuggestions(suggestions);
        })
        .catch(error => {
            console.error('Error fetching suggestions:', error);
        });
}

function displaySuggestions(suggestions) {
    const dropdown = document.getElementById('autocompleteDropdown');
    
    if (suggestions.length === 0) {
        dropdown.classList.remove('show');
        return;
    }
    
    dropdown.innerHTML = suggestions.map(suggestion => `
        <div class="autocomplete-item" data-value="${suggestion.value}">
            <div class="autocomplete-icon ${suggestion.type}">
                <i class="fas fa-${getIconForType(suggestion.type)}"></i>
            </div>
            <span>${suggestion.value}</span>
        </div>
    `).join('');
    
    dropdown.classList.add('show');
    
    // Add click handlers
    dropdown.querySelectorAll('.autocomplete-item').forEach(item => {
        item.addEventListener('click', function() {
            document.getElementById('searchInput').value = this.dataset.value;
            dropdown.classList.remove('show');
            submitFilters();
        });
    });
}

function getIconForType(type) {
    const icons = {
        product: 'box',
        category: 'list',
        style: 'palette',
        material: 'tshirt'
    };
    return icons[type] || 'search';
}

function updateSizeInputs() {
    const selectedSizes = Array.from(document.querySelectorAll('.size-filter-pill.active'))
        .map(pill => pill.dataset.size);
    
    const container = document.getElementById('selectedSizes');
    container.innerHTML = selectedSizes.map(size => 
        `<input type="hidden" name="sizes[]" value="${size}">`
    ).join('');
}

function removeFilter(filter, value = null) {
    const url = new URL(window.location);
    
    if (value) {
        const values = url.searchParams.getAll(filter + '[]');
        url.searchParams.delete(filter + '[]');
        values.filter(v => v !== value).forEach(v => {
            url.searchParams.append(filter + '[]', v);
        });
    } else {
        url.searchParams.delete(filter);
        url.searchParams.delete(filter + '[]');
    }
    
    window.location.href = url.toString();
}

function submitFilters() {
    showLoading();
    
    const form = document.getElementById('advancedFilterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    fetch(`{{ route('products.filter.ajax') }}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('productsContainer').innerHTML = data.html;
            document.getElementById('paginationContainer').innerHTML = data.pagination;
            
            // Update URL without reload
            const url = new URL(window.location);
            url.search = params.toString();
            window.history.pushState({}, '', url);
            
            // Update results count
            updateResultsInfo(data.showing);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        hideLoading();
    });
}

function submitSort(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    window.location.href = url.toString();
}

function submitPerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}

function showLoading() {
    document.getElementById('searchLoading')?.classList.add('show');
    document.getElementById('productsContainer')?.style.setProperty('opacity', '0.5');
}

function hideLoading() {
    document.getElementById('searchLoading')?.classList.remove('show');
    document.getElementById('productsContainer')?.style.setProperty('opacity', '1');
}

function updateResultsInfo(showing) {
    const info = document.querySelector('.results-info');
    if (info && showing) {
        info.innerHTML = `Hiển thị <span class="results-count">${showing.from}-${showing.to}</span> trong tổng số <span class="results-count">${showing.total}</span> sản phẩm`;
    }
}
</script>
@endsection