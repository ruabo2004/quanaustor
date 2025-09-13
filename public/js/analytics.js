/**
 * Advanced Analytics & User Behavior Tracking System
 * Tracks user interactions, page views, and behavior patterns
 */

class AdvancedAnalytics {
    constructor() {
        this.sessionId = null;
        this.pageStartTime = Date.now();
        this.interactions = [];
        this.scrollDepth = 0;
        this.maxScrollDepth = 0;
        this.isIdle = false;
        this.idleTimer = null;
        this.heartbeatInterval = null;
        
        this.init();
    }
    
    init() {
        this.generateSessionId();
        this.setupPageTracking();
        this.setupScrollTracking();
        this.setupClickTracking();
        this.setupFormTracking();
        this.setupIdleTracking();
        this.setupPerformanceTracking();
        this.setupUnloadTracking();
        this.startHeartbeat();
        
        // Track initial page view
        this.trackPageView();
        
        console.log('Advanced Analytics initialized');
    }
    
    generateSessionId() {
        this.sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // =====================================
    // PAGE TRACKING
    // =====================================
    
    setupPageTracking() {
        // Track page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.onPageHidden();
            } else {
                this.onPageVisible();
            }
        });
        
        // Track hash changes (SPA navigation)
        window.addEventListener('hashchange', () => {
            this.trackPageView();
        });
        
        // Track browser back/forward
        window.addEventListener('popstate', () => {
            this.trackPageView();
        });
    }
    
    trackPageView() {
        const data = {
            url: window.location.href,
            page_title: document.title,
            page_type: this.getPageType(),
            referrer: document.referrer,
            viewport: this.getViewportSize(),
            device_info: this.getDeviceInfo(),
            performance: this.getPerformanceData(),
            utm_params: this.getUTMParams(),
            timestamp: new Date().toISOString()
        };
        
        this.sendEvent('page_view', data);
        
        // Reset page tracking variables
        this.pageStartTime = Date.now();
        this.maxScrollDepth = 0;
        this.interactions = [];
    }
    
    onPageHidden() {
        const timeOnPage = Math.round((Date.now() - this.pageStartTime) / 1000);
        
        this.sendEvent('page_exit', {
            time_on_page: timeOnPage,
            scroll_depth: this.maxScrollDepth,
            interactions: this.interactions.length,
            exit_type: 'navigation'
        });
    }
    
    onPageVisible() {
        this.pageStartTime = Date.now();
    }
    
    // =====================================
    // SCROLL TRACKING
    // =====================================
    
    setupScrollTracking() {
        let scrollTimeout;
        
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.trackScroll();
            }, 100);
        });
    }
    
    trackScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const documentHeight = Math.max(
            document.body.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.clientHeight,
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight
        );
        const windowHeight = window.innerHeight;
        
        this.scrollDepth = Math.round((scrollTop + windowHeight) / documentHeight * 100);
        this.maxScrollDepth = Math.max(this.maxScrollDepth, this.scrollDepth);
        
        // Track scroll milestones
        const milestones = [25, 50, 75, 100];
        milestones.forEach(milestone => {
            if (this.scrollDepth >= milestone && !this.hasTrackedScrollMilestone(milestone)) {
                this.trackScrollMilestone(milestone);
            }
        });
    }
    
    hasTrackedScrollMilestone(milestone) {
        return this.interactions.some(interaction => 
            interaction.type === 'scroll_milestone' && 
            interaction.milestone === milestone
        );
    }
    
    trackScrollMilestone(milestone) {
        const interaction = {
            type: 'scroll_milestone',
            milestone: milestone,
            timestamp: Date.now()
        };
        
        this.interactions.push(interaction);
        
        this.sendEvent('scroll_milestone', {
            milestone: milestone,
            scroll_depth: this.scrollDepth,
            time_to_milestone: Date.now() - this.pageStartTime
        });
    }
    
    // =====================================
    // CLICK TRACKING
    // =====================================
    
    setupClickTracking() {
        document.addEventListener('click', (event) => {
            this.trackClick(event);
        }, true);
        
        // Track hover events on important elements
        document.addEventListener('mouseover', (event) => {
            if (this.isImportantElement(event.target)) {
                this.trackHover(event);
            }
        });
    }
    
    trackClick(event) {
        const element = event.target;
        const elementInfo = this.getElementInfo(element);
        
        const clickData = {
            element_type: element.tagName.toLowerCase(),
            element_id: element.id,
            element_class: element.className,
            element_text: this.getElementText(element),
            element_attributes: this.getElementAttributes(element),
            click_coordinates: {
                x: event.clientX,
                y: event.clientY
            },
            element_position: elementInfo.position,
            context: this.getElementContext(element),
            section: this.getPageSection(element),
            time_to_click: Date.now() - this.pageStartTime,
            viewport: this.getViewportSize()
        };
        
        // Add ecommerce data if applicable
        if (this.isProductElement(element)) {
            clickData.product_data = this.getProductData(element);
        }
        
        this.interactions.push({
            type: 'click',
            element: elementInfo,
            timestamp: Date.now()
        });
        
        this.sendEvent('click', clickData);
        
        // Track specific click types
        this.trackSpecificClicks(element, event);
    }
    
    trackHover(event) {
        const element = event.target;
        
        this.sendEvent('hover', {
            element_type: element.tagName.toLowerCase(),
            element_id: element.id,
            element_class: element.className,
            element_text: this.getElementText(element),
            hover_time: Date.now() - this.pageStartTime
        });
    }
    
    trackSpecificClicks(element, event) {
        // Track CTA buttons
        if (element.matches('.btn-primary, .cta-button, [data-action="purchase"]')) {
            this.sendEvent('cta_click', {
                cta_type: element.dataset.action || 'unknown',
                cta_text: this.getElementText(element),
                page_section: this.getPageSection(element)
            });
        }
        
        // Track navigation clicks
        if (element.matches('nav a, .nav-link, .menu-item')) {
            this.sendEvent('navigation_click', {
                nav_text: this.getElementText(element),
                nav_href: element.href,
                nav_type: this.getNavigationType(element)
            });
        }
        
        // Track product interactions
        if (this.isProductElement(element)) {
            this.sendEvent('product_interaction', {
                action: this.getProductAction(element),
                product_data: this.getProductData(element)
            });
        }
        
        // Track social sharing
        if (element.matches('.social-share, [data-share]')) {
            this.sendEvent('social_share', {
                platform: element.dataset.platform || 'unknown',
                content_type: this.getPageType(),
                content_id: this.getContentId()
            });
        }
    }
    
    // =====================================
    // FORM TRACKING
    // =====================================
    
    setupFormTracking() {
        // Track form interactions
        document.addEventListener('focus', (event) => {
            if (event.target.matches('input, textarea, select')) {
                this.trackFormFocus(event);
            }
        }, true);
        
        document.addEventListener('blur', (event) => {
            if (event.target.matches('input, textarea, select')) {
                this.trackFormBlur(event);
            }
        }, true);
        
        // Track form submissions
        document.addEventListener('submit', (event) => {
            this.trackFormSubmit(event);
        }, true);
        
        // Track form errors
        this.observeFormErrors();
    }
    
    trackFormFocus(event) {
        const field = event.target;
        const form = field.closest('form');
        
        this.sendEvent('form_field_focus', {
            form_id: form?.id,
            field_name: field.name,
            field_type: field.type,
            field_label: this.getFieldLabel(field)
        });
    }
    
    trackFormBlur(event) {
        const field = event.target;
        const form = field.closest('form');
        
        this.sendEvent('form_field_blur', {
            form_id: form?.id,
            field_name: field.name,
            field_type: field.type,
            field_value: this.anonymizeFieldValue(field),
            time_spent: this.getFieldTimeSpent(field)
        });
    }
    
    trackFormSubmit(event) {
        const form = event.target;
        const formData = new FormData(form);
        const fields = {};
        
        for (let [key, value] of formData.entries()) {
            fields[key] = this.anonymizeFieldValue({ name: key, value: value });
        }
        
        this.sendEvent('form_submit', {
            form_id: form.id,
            form_action: form.action,
            form_method: form.method,
            fields_count: Object.keys(fields).length,
            form_completion_time: Date.now() - this.pageStartTime
        });
    }
    
    observeFormErrors() {
        // Observe for form error messages
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1 && node.matches('.error, .invalid-feedback, [class*="error"]')) {
                        this.trackFormError(node);
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    trackFormError(errorElement) {
        const form = errorElement.closest('form');
        const field = errorElement.closest('.form-group, .field')?.querySelector('input, textarea, select');
        
        this.sendEvent('form_error', {
            form_id: form?.id,
            field_name: field?.name,
            error_message: errorElement.textContent,
            error_type: this.getErrorType(errorElement)
        });
    }
    
    // =====================================
    // IDLE & ENGAGEMENT TRACKING
    // =====================================
    
    setupIdleTracking() {
        const idleTime = 30000; // 30 seconds
        
        const resetIdleTimer = () => {
            clearTimeout(this.idleTimer);
            
            if (this.isIdle) {
                this.isIdle = false;
                this.sendEvent('user_active', {
                    idle_duration: Date.now() - this.idleStartTime
                });
            }
            
            this.idleTimer = setTimeout(() => {
                this.isIdle = true;
                this.idleStartTime = Date.now();
                this.sendEvent('user_idle');
            }, idleTime);
        };
        
        // Events that reset idle timer
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
            document.addEventListener(event, resetIdleTimer, true);
        });
        
        resetIdleTimer();
    }
    
    // =====================================
    // PERFORMANCE TRACKING
    // =====================================
    
    setupPerformanceTracking() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.trackPerformanceMetrics();
            }, 1000);
        });
    }
    
    trackPerformanceMetrics() {
        if ('performance' in window) {
            const timing = performance.timing;
            const navigation = performance.getEntriesByType('navigation')[0];
            
            const metrics = {
                page_load_time: timing.loadEventEnd - timing.navigationStart,
                dom_load_time: timing.domContentLoadedEventEnd - timing.navigationStart,
                first_paint: this.getFirstPaint(),
                largest_contentful_paint: this.getLargestContentfulPaint(),
                cumulative_layout_shift: this.getCumulativeLayoutShift(),
                first_input_delay: this.getFirstInputDelay()
            };
            
            this.sendEvent('performance_metrics', metrics);
        }
    }
    
    // =====================================
    // UNLOAD TRACKING
    // =====================================
    
    setupUnloadTracking() {
        window.addEventListener('beforeunload', () => {
            this.onPageUnload();
        });
        
        // Use Page Visibility API as backup
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.onPageUnload();
            }
        });
    }
    
    onPageUnload() {
        const timeOnPage = Math.round((Date.now() - this.pageStartTime) / 1000);
        
        const exitData = {
            time_on_page: timeOnPage,
            scroll_depth: this.maxScrollDepth,
            interactions_count: this.interactions.length,
            exit_type: 'unload'
        };
        
        // Use sendBeacon for reliable delivery
        this.sendEvent('page_unload', exitData, true);
    }
    
    // =====================================
    // HEARTBEAT & SESSION MANAGEMENT
    // =====================================
    
    startHeartbeat() {
        this.heartbeatInterval = setInterval(() => {
            if (!this.isIdle) {
                this.sendHeartbeat();
            }
        }, 30000); // Every 30 seconds
    }
    
    sendHeartbeat() {
        this.sendEvent('heartbeat', {
            session_duration: Date.now() - this.pageStartTime,
            current_scroll: this.scrollDepth,
            interactions_count: this.interactions.length
        });
    }
    
    // =====================================
    // ECOMMERCE TRACKING
    // =====================================
    
    trackPurchase(orderData) {
        this.sendEvent('purchase', {
            transaction_id: orderData.id,
            value: orderData.total,
            currency: 'VND',
            items: orderData.items,
            payment_method: orderData.payment_method
        });
    }
    
    trackAddToCart(productData) {
        this.sendEvent('add_to_cart', {
            product_id: productData.id,
            product_name: productData.name,
            product_price: productData.price,
            quantity: productData.quantity,
            category: productData.category
        });
    }
    
    trackRemoveFromCart(productData) {
        this.sendEvent('remove_from_cart', {
            product_id: productData.id,
            product_name: productData.name,
            product_price: productData.price,
            quantity: productData.quantity
        });
    }
    
    trackProductView(productData) {
        this.sendEvent('product_view', {
            product_id: productData.id,
            product_name: productData.name,
            product_price: productData.price,
            category: productData.category,
            brand: productData.brand
        });
    }
    
    // =====================================
    // SEARCH TRACKING
    // =====================================
    
    trackSearch(query, resultsCount, searchType = 'product') {
        this.sendEvent('search', {
            search_query: query,
            search_results_count: resultsCount,
            search_type: searchType,
            page_url: window.location.href
        });
    }
    
    // =====================================
    // UTILITY METHODS
    // =====================================
    
    getElementInfo(element) {
        return {
            tag: element.tagName.toLowerCase(),
            id: element.id,
            classes: Array.from(element.classList),
            text: this.getElementText(element),
            position: element.getBoundingClientRect()
        };
    }
    
    getElementText(element) {
        return element.textContent?.trim().substring(0, 100) || '';
    }
    
    getElementAttributes(element) {
        const attrs = {};
        for (let attr of element.attributes) {
            if (['data-', 'aria-'].some(prefix => attr.name.startsWith(prefix))) {
                attrs[attr.name] = attr.value;
            }
        }
        return attrs;
    }
    
    getElementContext(element) {
        const contexts = ['header', 'nav', 'main', 'aside', 'footer'];
        for (let context of contexts) {
            if (element.closest(context)) {
                return context;
            }
        }
        return 'unknown';
    }
    
    getPageSection(element) {
        const section = element.closest('[data-section]');
        return section?.dataset.section || 'unknown';
    }
    
    getPageType() {
        const path = window.location.pathname;
        if (path === '/' || path === '/home') return 'home';
        if (path.includes('/products/')) return 'product';
        if (path.includes('/categories/')) return 'category';
        if (path.includes('/blog/')) return 'blog';
        if (path.includes('/cart')) return 'cart';
        if (path.includes('/checkout')) return 'checkout';
        return 'other';
    }
    
    getContentId() {
        const matches = window.location.pathname.match(/\/(\w+)\/(\d+)/);
        return matches ? matches[2] : null;
    }
    
    getViewportSize() {
        return {
            width: window.innerWidth,
            height: window.innerHeight
        };
    }
    
    getDeviceInfo() {
        const ua = navigator.userAgent;
        return {
            user_agent: ua,
            language: navigator.language,
            platform: navigator.platform,
            cookie_enabled: navigator.cookieEnabled,
            online: navigator.onLine,
            screen: {
                width: screen.width,
                height: screen.height,
                color_depth: screen.colorDepth
            }
        };
    }
    
    getUTMParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            utm_source: params.get('utm_source'),
            utm_medium: params.get('utm_medium'),
            utm_campaign: params.get('utm_campaign'),
            utm_term: params.get('utm_term'),
            utm_content: params.get('utm_content')
        };
    }
    
    getPerformanceData() {
        if (!('performance' in window)) return null;
        
        return {
            timing: performance.timing,
            memory: performance.memory,
            connection: navigator.connection
        };
    }
    
    isImportantElement(element) {
        return element.matches('button, a, input, [data-track], .btn, .card, .product-item');
    }
    
    isProductElement(element) {
        return element.closest('.product-card, .product-item, [data-product-id]');
    }
    
    getProductData(element) {
        const productContainer = element.closest('.product-card, .product-item, [data-product-id]');
        if (!productContainer) return null;
        
        return {
            id: productContainer.dataset.productId,
            name: productContainer.querySelector('.product-name, .product-title')?.textContent,
            price: productContainer.querySelector('.product-price')?.textContent,
            category: productContainer.dataset.category
        };
    }
    
    getProductAction(element) {
        if (element.matches('[data-action="add-to-cart"]')) return 'add_to_cart';
        if (element.matches('[data-action="quick-view"]')) return 'quick_view';
        if (element.matches('.product-link, .product-image')) return 'view_product';
        return 'interact';
    }
    
    getNavigationType(element) {
        if (element.closest('.main-nav')) return 'main';
        if (element.closest('.breadcrumb')) return 'breadcrumb';
        if (element.closest('.footer')) return 'footer';
        return 'other';
    }
    
    getFieldLabel(field) {
        const label = field.closest('.form-group')?.querySelector('label');
        return label?.textContent?.trim() || field.placeholder || field.name;
    }
    
    anonymizeFieldValue(field) {
        const sensitiveFields = ['password', 'email', 'phone', 'ssn', 'credit-card'];
        if (sensitiveFields.some(type => field.name?.includes(type) || field.type === type)) {
            return '[REDACTED]';
        }
        return field.value?.substring(0, 20) || '';
    }
    
    getFieldTimeSpent(field) {
        return field.dataset.focusTime ? Date.now() - parseInt(field.dataset.focusTime) : 0;
    }
    
    getErrorType(errorElement) {
        if (errorElement.textContent.includes('required')) return 'required';
        if (errorElement.textContent.includes('invalid')) return 'validation';
        if (errorElement.textContent.includes('format')) return 'format';
        return 'unknown';
    }
    
    getFirstPaint() {
        const paint = performance.getEntriesByType('paint').find(entry => entry.name === 'first-paint');
        return paint ? paint.startTime : null;
    }
    
    getLargestContentfulPaint() {
        return new Promise((resolve) => {
            new PerformanceObserver((list) => {
                const entries = list.getEntries();
                const lastEntry = entries[entries.length - 1];
                resolve(lastEntry ? lastEntry.startTime : null);
            }).observe({ entryTypes: ['largest-contentful-paint'] });
        });
    }
    
    getCumulativeLayoutShift() {
        return new Promise((resolve) => {
            let clsValue = 0;
            new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (!entry.hadRecentInput) {
                        clsValue += entry.value;
                    }
                }
                resolve(clsValue);
            }).observe({ entryTypes: ['layout-shift'] });
        });
    }
    
    getFirstInputDelay() {
        return new Promise((resolve) => {
            new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    resolve(entry.processingStart - entry.startTime);
                    break;
                }
            }).observe({ entryTypes: ['first-input'] });
        });
    }
    
    // =====================================
    // DATA TRANSMISSION
    // =====================================
    
    sendEvent(eventType, data, useBeacon = false) {
        const payload = {
            event: eventType,
            session_id: this.sessionId,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            user_agent: navigator.userAgent,
            ...data
        };
        
        if (useBeacon && 'sendBeacon' in navigator) {
            navigator.sendBeacon('/api/analytics/track', JSON.stringify(payload));
        } else {
            this.sendAsync(payload);
        }
    }
    
    async sendAsync(data) {
        try {
            await fetch('/api/analytics/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify(data)
            });
        } catch (error) {
            console.warn('Analytics tracking failed:', error);
        }
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
}

// Initialize analytics when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.analytics = new AdvancedAnalytics();
});

// Export for external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdvancedAnalytics;
}
