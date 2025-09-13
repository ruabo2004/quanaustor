/**
 * Advanced Search & Filter System
 * Provides real-time filtering, autocomplete, and smooth UX
 */

class AdvancedSearch {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.autocompleteDropdown = document.getElementById('autocompleteDropdown');
        this.filterForm = document.getElementById('advancedFilterForm');
        this.filterPanel = document.getElementById('filterPanel');
        this.searchTimeout = null;
        this.selectedIndex = -1;
        this.isLoading = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initQuickFilters();
        this.initSizeFilters();
        this.initFilterToggle();
        this.initActiveFilters();
        this.initFormSubmission();
        
        // Initialize URL state
        this.updateUIFromURL();
    }
    
    bindEvents() {
        // Search input with autocomplete
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                this.handleSearchInput(e.target.value);
            });
            
            this.searchInput.addEventListener('keydown', (e) => {
                this.handleSearchKeydown(e);
            });
            
            this.searchInput.addEventListener('focus', () => {
                if (this.searchInput.value.trim().length >= 2) {
                    this.autocompleteDropdown?.classList.add('show');
                }
            });
        }
        
        // Clear search button
        const searchClear = document.getElementById('searchClear');
        searchClear?.addEventListener('click', () => {
            this.clearSearch();
        });
        
        // Click outside to close autocomplete
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-box-advanced')) {
                this.hideAutocomplete();
            }
        });
        
        // Form inputs change detection
        this.filterForm?.addEventListener('change', (e) => {
            if (e.target.type !== 'submit') {
                this.debounceFormSubmit();
            }
        });
        
        // Price inputs with validation
        const priceInputs = this.filterForm?.querySelectorAll('.price-input');
        priceInputs?.forEach(input => {
            input.addEventListener('input', () => {
                this.validatePriceInputs();
                this.debounceFormSubmit();
            });
        });
    }
    
    initQuickFilters() {
        const quickFilters = document.querySelectorAll('.quick-filter-btn');
        quickFilters.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleQuickFilter(btn);
            });
        });
    }
    
    initSizeFilters() {
        const sizePills = document.querySelectorAll('.size-filter-pill');
        sizePills.forEach(pill => {
            pill.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSizeFilter(pill);
            });
        });
    }
    
    initFilterToggle() {
        const filterToggle = document.getElementById('filterToggle');
        const hideFilters = document.getElementById('hideFilters');
        
        filterToggle?.addEventListener('click', () => {
            this.toggleFilterPanel();
        });
        
        hideFilters?.addEventListener('click', () => {
            this.hideFilterPanel();
        });
    }
    
    initActiveFilters() {
        const removeButtons = document.querySelectorAll('.active-filter-remove');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                const value = btn.dataset.value;
                this.removeFilter(filter, value);
            });
        });
        
        const clearAllBtn = document.getElementById('clearAllFilters');
        clearAllBtn?.addEventListener('click', () => {
            this.clearAllFilters();
        });
    }
    
    initFormSubmission() {
        this.filterForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitFilters();
        });
    }
    
    handleSearchInput(value) {
        const query = value.trim();
        
        // Clear previous timeout
        clearTimeout(this.searchTimeout);
        
        // Show/hide clear button
        const clearBtn = document.getElementById('searchClear');
        if (clearBtn) {
            clearBtn.style.display = query ? 'flex' : 'none';
        }
        
        if (query.length >= 2) {
            this.searchTimeout = setTimeout(() => {
                this.fetchSuggestions(query);
            }, 300);
        } else {
            this.hideAutocomplete();
        }
    }
    
    handleSearchKeydown(e) {
        const items = this.autocompleteDropdown?.querySelectorAll('.autocomplete-item');
        if (!items || items.length === 0) return;
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, items.length - 1);
                this.updateSelection();
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.updateSelection();
                break;
                
            case 'Enter':
                e.preventDefault();
                if (this.selectedIndex >= 0) {
                    this.selectSuggestion(items[this.selectedIndex]);
                } else {
                    this.submitFilters();
                }
                break;
                
            case 'Escape':
                this.hideAutocomplete();
                this.searchInput.blur();
                break;
        }
    }
    
    async fetchSuggestions(query) {
        try {
            const response = await fetch(`/products/search/suggestions?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            this.displaySuggestions(suggestions);
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            this.hideAutocomplete();
        }
    }
    
    displaySuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            this.hideAutocomplete();
            return;
        }
        
        const html = suggestions.map((suggestion, index) => `
            <div class="autocomplete-item" data-value="${suggestion.value}" data-index="${index}">
                <div class="autocomplete-icon ${suggestion.type}">
                    <i class="fas fa-${this.getIconForType(suggestion.type)}"></i>
                </div>
                <span>${this.highlightMatch(suggestion.value, this.searchInput.value)}</span>
                <small style="margin-left: auto; color: var(--gray-500); font-size: 0.7rem; text-transform: uppercase;">
                    ${this.getTypeLabel(suggestion.type)}
                </small>
            </div>
        `).join('');
        
        this.autocompleteDropdown.innerHTML = html;
        this.autocompleteDropdown.classList.add('show');
        this.selectedIndex = -1;
        
        // Add click handlers
        this.autocompleteDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectSuggestion(item);
            });
        });
    }
    
    selectSuggestion(item) {
        this.searchInput.value = item.dataset.value;
        this.hideAutocomplete();
        this.submitFilters();
    }
    
    updateSelection() {
        const items = this.autocompleteDropdown?.querySelectorAll('.autocomplete-item');
        if (!items) return;
        
        items.forEach((item, index) => {
            item.classList.toggle('selected', index === this.selectedIndex);
        });
    }
    
    hideAutocomplete() {
        this.autocompleteDropdown?.classList.remove('show');
        this.selectedIndex = -1;
    }
    
    clearSearch() {
        this.searchInput.value = '';
        this.hideAutocomplete();
        document.getElementById('searchClear').style.display = 'none';
        this.submitFilters();
    }
    
    handleQuickFilter(btn) {
        const filter = btn.dataset.filter;
        const value = btn.dataset.value;
        
        // Remove active from siblings
        btn.parentElement.querySelectorAll('.quick-filter-btn').forEach(b => {
            b.classList.remove('active');
        });
        
        if (filter === 'all') {
            this.clearAllFilters();
            return;
        }
        
        // Add active to clicked
        btn.classList.add('active');
        
        // Update form and submit
        this.setFormValue(filter, value);
        this.submitFilters();
    }
    
    toggleSizeFilter(pill) {
        const size = pill.dataset.size;
        pill.classList.toggle('active');
        
        this.updateSizeInputs();
        this.debounceFormSubmit();
    }
    
    updateSizeInputs() {
        const selectedSizes = Array.from(document.querySelectorAll('.size-filter-pill.active'))
            .map(pill => pill.dataset.size);
        
        const container = document.getElementById('selectedSizes');
        if (container) {
            container.innerHTML = selectedSizes.map(size => 
                `<input type="hidden" name="sizes[]" value="${size}">`
            ).join('');
        }
    }
    
    toggleFilterPanel() {
        const toggle = document.getElementById('filterToggle');
        this.filterPanel?.classList.toggle('show');
        toggle?.classList.toggle('active');
    }
    
    hideFilterPanel() {
        const toggle = document.getElementById('filterToggle');
        this.filterPanel?.classList.remove('show');
        toggle?.classList.remove('active');
    }
    
    removeFilter(filter, value = null) {
        const url = new URL(window.location);
        
        if (value) {
            // Handle array filters
            const values = url.searchParams.getAll(filter + '[]');
            url.searchParams.delete(filter + '[]');
            values.filter(v => v !== value).forEach(v => {
                url.searchParams.append(filter + '[]', v);
            });
        } else {
            // Handle single filters
            url.searchParams.delete(filter);
            url.searchParams.delete(filter + '[]');
        }
        
        url.searchParams.delete('page'); // Reset pagination
        window.location.href = url.toString();
    }
    
    clearAllFilters() {
        window.location.href = window.location.pathname;
    }
    
    setFormValue(name, value) {
        // Remove existing inputs with this name
        this.filterForm?.querySelectorAll(`input[name="${name}"]`).forEach(input => {
            input.remove();
        });
        
        // Add new hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        this.filterForm?.appendChild(input);
    }
    
    validatePriceInputs() {
        const minInput = this.filterForm?.querySelector('input[name="min_price"]');
        const maxInput = this.filterForm?.querySelector('input[name="max_price"]');
        
        if (minInput && maxInput) {
            const min = parseFloat(minInput.value) || 0;
            const max = parseFloat(maxInput.value) || Infinity;
            
            if (min > max && max > 0) {
                maxInput.style.borderColor = 'var(--danger)';
                minInput.style.borderColor = 'var(--danger)';
            } else {
                maxInput.style.borderColor = '';
                minInput.style.borderColor = '';
            }
        }
    }
    
    debounceFormSubmit() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.submitFilters();
        }, 500);
    }
    
    async submitFilters() {
        if (this.isLoading) return;
        
        this.showLoading();
        
        try {
            const formData = new FormData(this.filterForm);
            const params = new URLSearchParams(formData);
            
            const response = await fetch(`/products/filter/ajax?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update content
                const productsContainer = document.getElementById('productsContainer');
                const paginationContainer = document.getElementById('paginationContainer');
                
                if (productsContainer) {
                    productsContainer.innerHTML = data.html;
                    this.initProductInteractions();
                }
                
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination || '';
                }
                
                // Update URL without reload
                const url = new URL(window.location);
                url.search = params.toString();
                window.history.pushState({}, '', url);
                
                // Update results info
                this.updateResultsInfo(data.showing);
                
                // Scroll to results
                this.scrollToResults();
                
                // Show success message
                this.showToast(`Tìm thấy ${data.total} sản phẩm`, 'success');
            }
        } catch (error) {
            console.error('Error submitting filters:', error);
            this.showToast('Có lỗi xảy ra khi tìm kiếm', 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    initProductInteractions() {
        // Re-initialize product card interactions
        const quickAddBtns = document.querySelectorAll('.quick-add-to-cart');
        quickAddBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Quick add to cart functionality
                if (typeof quickAddToCart === 'function') {
                    quickAddToCart(this.dataset.productId, this);
                }
            });
        });
    }
    
    showLoading() {
        this.isLoading = true;
        const loadingEl = document.getElementById('searchLoading');
        const container = document.getElementById('productsContainer');
        
        loadingEl?.classList.add('show');
        if (container) {
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';
        }
    }
    
    hideLoading() {
        this.isLoading = false;
        const loadingEl = document.getElementById('searchLoading');
        const container = document.getElementById('productsContainer');
        
        loadingEl?.classList.remove('show');
        if (container) {
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';
        }
    }
    
    updateResultsInfo(showing) {
        const info = document.querySelector('.results-info');
        if (info && showing) {
            info.innerHTML = `Hiển thị <span class="results-count">${showing.from}-${showing.to}</span> trong tổng số <span class="results-count">${showing.total}</span> sản phẩm`;
        }
    }
    
    scrollToResults() {
        const container = document.getElementById('productsContainer');
        if (container) {
            container.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    }
    
    updateUIFromURL() {
        const params = new URLSearchParams(window.location.search);
        
        // Update active quick filters
        document.querySelectorAll('.quick-filter-btn').forEach(btn => {
            const filter = btn.dataset.filter;
            const value = btn.dataset.value;
            
            if (filter && value && params.get(filter) === value) {
                btn.classList.add('active');
            }
        });
        
        // Update active size filters
        const sizes = params.getAll('sizes[]');
        document.querySelectorAll('.size-filter-pill').forEach(pill => {
            if (sizes.includes(pill.dataset.size)) {
                pill.classList.add('active');
            }
        });
    }
    
    showToast(message, type = 'info') {
        // Create toast if toast system exists
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            // Fallback to simple alert
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
    
    getIconForType(type) {
        const icons = {
            product: 'box',
            category: 'list',
            style: 'palette',
            material: 'tshirt'
        };
        return icons[type] || 'search';
    }
    
    getTypeLabel(type) {
        const labels = {
            product: 'Sản phẩm',
            category: 'Danh mục',
            style: 'Phong cách',
            material: 'Chất liệu'
        };
        return labels[type] || 'Khác';
    }
    
    highlightMatch(text, query) {
        if (!query) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    }
}

// Utility functions for external use
window.submitSort = function(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    url.searchParams.delete('page');
    window.location.href = url.toString();
};

window.submitPerPage = function(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page');
    window.location.href = url.toString();
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize on products page
    if (document.getElementById('advancedFilterForm')) {
        window.advancedSearch = new AdvancedSearch();
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdvancedSearch;
}
