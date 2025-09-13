/**
 * Personalization & Recommendation System
 * Handles user behavior tracking and personalized content delivery
 */

class PersonalizationEngine {
    constructor() {
        this.userId = this.getCurrentUserId();
        this.sessionId = this.getSessionId();
        this.viewStartTime = null;
        this.currentProductId = null;
        this.interactionData = [];
        this.isTracking = true;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.startSessionTracking();
        
        // Load personalized content on page load
        if (this.shouldLoadRecommendations()) {
            this.loadRecommendations();
            this.loadRecentlyViewed();
        }
        
        // Load user behavior analysis if on profile page
        if (this.isProfilePage()) {
            this.loadUserBehaviorAnalysis();
        }
    }
    
    bindEvents() {
        // Track page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.onPageHide();
            } else {
                this.onPageShow();
            }
        });
        
        // Track before page unload
        window.addEventListener('beforeunload', () => {
            this.onPageUnload();
        });
        
        // Track scroll behavior
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.trackScrollBehavior();
            }, 100);
        });
        
        // Track clicks on products
        document.addEventListener('click', (e) => {
            this.trackProductClick(e);
        });
        
        // Track time spent on elements
        this.observeElementViewing();
    }
    
    startSessionTracking() {
        // Track current product if on product page
        const productId = this.extractProductIdFromURL();
        if (productId) {
            this.trackProductView(productId);
        }
    }
    
    trackProductView(productId) {
        if (!this.isTracking) return;
        
        this.currentProductId = productId;
        this.viewStartTime = Date.now();
        this.interactionData = [];
        
        const data = {
            product_id: productId,
            interaction_data: {
                user_agent: navigator.userAgent,
                screen_resolution: `${screen.width}x${screen.height}`,
                viewport_size: `${window.innerWidth}x${window.innerHeight}`,
                referrer: document.referrer,
                timestamp: new Date().toISOString()
            }
        };
        
        this.sendTrackingData('/recommendations/track-view', data);
    }
    
    trackScrollBehavior() {
        if (!this.currentProductId) return;
        
        const scrollPercent = Math.round(
            (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
        );
        
        this.interactionData.push({
            type: 'scroll',
            value: scrollPercent,
            timestamp: Date.now() - this.viewStartTime
        });
    }
    
    trackProductClick(e) {
        const productCard = e.target.closest('[data-product-id]');
        if (!productCard) return;
        
        const productId = productCard.dataset.productId;
        const clickType = this.getClickType(e.target);
        
        this.interactionData.push({
            type: 'click',
            target: clickType,
            product_id: productId,
            timestamp: Date.now() - (this.viewStartTime || Date.now())
        });
    }
    
    getClickType(element) {
        if (element.closest('.quick-add-to-cart')) return 'add_to_cart';
        if (element.closest('.quick-view')) return 'quick_view';
        if (element.closest('.product-image')) return 'image';
        if (element.closest('.product-title')) return 'title';
        if (element.closest('.product-price')) return 'price';
        return 'general';
    }
    
    observeElementViewing() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const productId = entry.target.dataset.productId;
                    if (productId) {
                        this.trackElementView(productId, entry.target);
                    }
                }
            });
        }, {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        });
        
        // Observe all product cards
        document.querySelectorAll('[data-product-id]').forEach(element => {
            observer.observe(element);
        });
    }
    
    trackElementView(productId, element) {
        this.interactionData.push({
            type: 'element_view',
            product_id: productId,
            element_type: element.className,
            timestamp: Date.now() - (this.viewStartTime || Date.now())
        });
    }
    
    onPageHide() {
        this.updateViewDuration();
    }
    
    onPageShow() {
        this.viewStartTime = Date.now();
    }
    
    onPageUnload() {
        this.updateViewDuration();
    }
    
    updateViewDuration() {
        if (!this.currentProductId || !this.viewStartTime) return;
        
        const duration = Math.round((Date.now() - this.viewStartTime) / 1000);
        
        const data = {
            product_id: this.currentProductId,
            duration: duration,
            interaction_data: this.interactionData
        };
        
        // Use sendBeacon for reliable tracking on page unload
        this.sendTrackingData('/recommendations/update-duration', data, true);
    }
    
    loadRecommendations() {
        const containers = document.querySelectorAll('[data-recommendations]');
        
        containers.forEach(container => {
            const type = container.dataset.recommendations;
            const limit = parseInt(container.dataset.limit) || 12;
            const excludeIds = this.getExcludeIds(container);
            
            this.fetchRecommendations(type, limit, excludeIds)
                .then(recommendations => {
                    this.renderRecommendations(container, recommendations, type);
                })
                .catch(error => {
                    console.error('Error loading recommendations:', error);
                    this.renderRecommendationError(container);
                });
        });
    }
    
    async fetchRecommendations(type, limit = 12, excludeIds = []) {
        const params = new URLSearchParams({
            type: type,
            limit: limit,
            exclude: excludeIds.join(',')
        });
        
        // Add product ID for similar recommendations
        if (type === 'similar' && this.currentProductId) {
            params.set('product_id', this.currentProductId);
        }
        
        const response = await fetch(`/recommendations/get?${params.toString()}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to fetch recommendations');
        }
        
        return data.recommendations;
    }
    
    loadRecentlyViewed() {
        const container = document.querySelector('[data-recently-viewed]');
        if (!container) return;
        
        const limit = parseInt(container.dataset.limit) || 10;
        
        this.fetchRecentlyViewed(limit)
            .then(products => {
                this.renderRecentlyViewed(container, products);
            })
            .catch(error => {
                console.error('Error loading recently viewed:', error);
            });
    }
    
    async fetchRecentlyViewed(limit = 10) {
        const response = await fetch(`/recommendations/recently-viewed?limit=${limit}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to fetch recently viewed');
        }
        
        return data.recently_viewed;
    }
    
    renderRecommendations(container, recommendations, type) {
        if (!recommendations || recommendations.length === 0) {
            this.renderEmptyRecommendations(container, type);
            return;
        }
        
        const html = recommendations.map(product => this.createRecommendationCard(product, type)).join('');
        container.innerHTML = html;
        
        // Add click handlers
        this.bindRecommendationEvents(container);
        
        // Animate in
        this.animateRecommendations(container);
    }
    
    createRecommendationCard(product, type) {
        const badgeText = this.getBadgeText(type);
        const badgeClass = type;
        
        return `
            <div class="recommendation-card" data-product-id="${product.id}">
                <div class="recommendation-image">
                    ${product.image 
                        ? `<img src="${product.image}" alt="${product.name}" loading="lazy">`
                        : `<div class="recommendation-placeholder"><i class="fas fa-image"></i></div>`
                    }
                    <div class="recommendation-badge ${badgeClass}">${badgeText}</div>
                </div>
                <div class="recommendation-content">
                    ${product.category ? `<div class="recommendation-category">${product.category}</div>` : ''}
                    <h4 class="recommendation-title">${product.name}</h4>
                    <div class="recommendation-price">${product.formatted_price}</div>
                </div>
            </div>
        `;
    }
    
    renderRecentlyViewed(container, products) {
        if (!products || products.length === 0) {
            container.innerHTML = `
                <div class="empty-recommendations">
                    <div class="empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="empty-title">Chưa có lịch sử duyệt</div>
                    <div class="empty-description">Hãy khám phá các sản phẩm để xem lịch sử duyệt của bạn</div>
                </div>
            `;
            return;
        }
        
        const html = products.map(product => this.createRecentlyViewedCard(product)).join('');
        container.innerHTML = html;
        
        this.bindRecommendationEvents(container);
    }
    
    createRecentlyViewedCard(product) {
        return `
            <div class="recommendation-card" data-product-id="${product.id}">
                <div class="recommendation-image">
                    ${product.image 
                        ? `<img src="${product.image}" alt="${product.name}" loading="lazy">`
                        : `<div class="recommendation-placeholder"><i class="fas fa-image"></i></div>`
                    }
                </div>
                <div class="recommendation-content">
                    ${product.category ? `<div class="recommendation-category">${product.category}</div>` : ''}
                    <h4 class="recommendation-title">${product.name}</h4>
                    <div class="recommendation-price">${product.formatted_price}</div>
                </div>
            </div>
        `;
    }
    
    renderEmptyRecommendations(container, type) {
        const messages = {
            personalized: {
                icon: 'fas fa-user-circle',
                title: 'Đang học thói quen của bạn',
                description: 'Hãy duyệt thêm sản phẩm để nhận gợi ý cá nhân hóa'
            },
            similar: {
                icon: 'fas fa-search',
                title: 'Không tìm thấy sản phẩm tương tự',
                description: 'Hãy thử khám phá các danh mục khác'
            },
            trending: {
                icon: 'fas fa-fire',
                title: 'Đang cập nhật xu hướng',
                description: 'Quay lại sau để xem sản phẩm hot nhất'
            }
        };
        
        const message = messages[type] || messages.personalized;
        
        container.innerHTML = `
            <div class="empty-recommendations">
                <div class="empty-icon">
                    <i class="${message.icon}"></i>
                </div>
                <div class="empty-title">${message.title}</div>
                <div class="empty-description">${message.description}</div>
            </div>
        `;
    }
    
    renderRecommendationError(container) {
        container.innerHTML = `
            <div class="empty-recommendations">
                <div class="empty-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="empty-title">Không thể tải gợi ý</div>
                <div class="empty-description">Vui lòng thử lại sau</div>
            </div>
        `;
    }
    
    bindRecommendationEvents(container) {
        container.querySelectorAll('.recommendation-card').forEach(card => {
            card.addEventListener('click', (e) => {
                const productId = card.dataset.productId;
                const url = `/products/${productId}`;
                
                // Track recommendation click
                this.trackRecommendationClick(productId, e);
                
                // Navigate to product
                window.location.href = url;
            });
        });
    }
    
    trackRecommendationClick(productId, event) {
        this.interactionData.push({
            type: 'recommendation_click',
            product_id: productId,
            timestamp: Date.now() - (this.viewStartTime || Date.now())
        });
    }
    
    animateRecommendations(container) {
        const cards = container.querySelectorAll('.recommendation-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('new');
            }, index * 100);
        });
    }
    
    async loadUserBehaviorAnalysis() {
        try {
            const [statsResponse, profileResponse] = await Promise.all([
                fetch('/profile/browsing-stats'),
                fetch('/profile/preferences')
            ]);
            
            const statsData = await statsResponse.json();
            const profileData = await profileResponse.json();
            
            if (statsData.success) {
                this.renderBrowsingStats(statsData.stats);
            }
            
            if (profileData.success) {
                this.renderUserProfile(profileData.profile);
            }
        } catch (error) {
            console.error('Error loading user behavior analysis:', error);
        }
    }
    
    renderBrowsingStats(stats) {
        const container = document.querySelector('[data-browsing-stats]');
        if (!container) return;
        
        container.innerHTML = `
            <div class="behavior-stats">
                <div class="behavior-stat">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-value">${stats.total_views || 0}</div>
                    <div class="stat-label">Sản phẩm đã xem</div>
                </div>
                <div class="behavior-stat">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-value">${stats.unique_products || 0}</div>
                    <div class="stat-label">Sản phẩm duy nhất</div>
                </div>
                <div class="behavior-stat">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">${Math.round(stats.average_session_time || 0)}s</div>
                    <div class="stat-label">Thời gian xem TB</div>
                </div>
                <div class="behavior-stat">
                    <div class="stat-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-value">${stats.most_viewed_day || 'N/A'}</div>
                    <div class="stat-label">Ngày hoạt động nhiều</div>
                </div>
            </div>
        `;
    }
    
    renderUserProfile(profile) {
        const container = document.querySelector('[data-user-profile]');
        if (!container) return;
        
        if (profile.type === 'new_user') {
            container.innerHTML = `
                <div class="empty-recommendations">
                    <div class="empty-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="empty-title">Người dùng mới</div>
                    <div class="empty-description">${profile.message}</div>
                </div>
            `;
            return;
        }
        
        const behaviorTypes = {
            price_conscious: 'Nhạy cảm giá',
            brand_loyal: 'Trung thành thương hiệu',
            impulse_buyer: 'Mua hàng bốc đồng',
            style_focused: 'Tập trung phong cách',
            explorer: 'Khám phá đa dạng'
        };
        
        container.innerHTML = `
            <div class="user-profile-display">
                <div class="profile-type">${behaviorTypes[profile.behavior_type] || 'Chưa xác định'}</div>
                <div class="profile-metrics">
                    <div class="profile-metric">
                        <span class="metric-label">Nhạy cảm giá</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${(profile.price_sensitivity * 100)}%"></div>
                        </div>
                        <span class="metric-value">${Math.round(profile.price_sensitivity * 100)}%</span>
                    </div>
                    <div class="profile-metric">
                        <span class="metric-label">Trung thành thương hiệu</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${(profile.brand_loyalty * 100)}%"></div>
                        </div>
                        <span class="metric-value">${Math.round(profile.brand_loyalty * 100)}%</span>
                    </div>
                    <div class="profile-metric">
                        <span class="metric-label">Nhất quán phong cách</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${(profile.style_consistency * 100)}%"></div>
                        </div>
                        <span class="metric-value">${Math.round(profile.style_consistency * 100)}%</span>
                    </div>
                    <div class="profile-metric">
                        <span class="metric-label">Mua hàng bốc đồng</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${(profile.impulse_buying * 100)}%"></div>
                        </div>
                        <span class="metric-value">${Math.round(profile.impulse_buying * 100)}%</span>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Utility methods
    getCurrentUserId() {
        // Extract from meta tag or global variable
        const meta = document.querySelector('meta[name="user-id"]');
        return meta ? meta.getAttribute('content') : null;
    }
    
    getSessionId() {
        // Extract from meta tag or generate
        const meta = document.querySelector('meta[name="session-id"]');
        return meta ? meta.getAttribute('content') : this.generateSessionId();
    }
    
    generateSessionId() {
        return 'session_' + Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
    }
    
    extractProductIdFromURL() {
        const match = window.location.pathname.match(/\/products\/(\d+)/);
        return match ? parseInt(match[1]) : null;
    }
    
    shouldLoadRecommendations() {
        return document.querySelector('[data-recommendations]') !== null ||
               document.querySelector('[data-recently-viewed]') !== null;
    }
    
    isProfilePage() {
        return window.location.pathname.includes('/profile') ||
               document.querySelector('[data-browsing-stats]') !== null;
    }
    
    getExcludeIds(container) {
        const exclude = container.dataset.exclude;
        return exclude ? exclude.split(',').map(id => parseInt(id)).filter(Boolean) : [];
    }
    
    getBadgeText(type) {
        const badges = {
            personalized: 'Dành cho bạn',
            similar: 'Tương tự',
            trending: 'Thịnh hành'
        };
        return badges[type] || '';
    }
    
    async sendTrackingData(url, data, useBeacon = false) {
        try {
            if (useBeacon && navigator.sendBeacon) {
                const formData = new FormData();
                formData.append('_token', this.getCSRFToken());
                Object.keys(data).forEach(key => {
                    if (typeof data[key] === 'object') {
                        formData.append(key, JSON.stringify(data[key]));
                    } else {
                        formData.append(key, data[key]);
                    }
                });
                navigator.sendBeacon(url, formData);
            } else {
                await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.getCSRFToken()
                    },
                    body: JSON.stringify(data)
                });
            }
        } catch (error) {
            console.error('Error sending tracking data:', error);
        }
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
}

// Auto-initialize personalization engine
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.personalizationEngine === 'undefined') {
        window.personalizationEngine = new PersonalizationEngine();
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PersonalizationEngine;
}
