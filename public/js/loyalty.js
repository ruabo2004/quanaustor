/**
 * Loyalty Program System
 * Handles loyalty points, tier progress, and rewards management
 */

class LoyaltySystem {
    constructor() {
        this.currentData = null;
        this.isLoading = false;
        this.animationQueue = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadLoyaltyData();
        this.initializeWidgets();
        
        // Auto-refresh data every 5 minutes
        setInterval(() => {
            this.loadLoyaltyData();
        }, 300000);
    }
    
    bindEvents() {
        // Birthday bonus claim
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-claim-birthday]')) {
                e.preventDefault();
                this.claimBirthdayBonus();
            }
        });
        
        // Reward redemption
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-redeem-reward]')) {
                e.preventDefault();
                const rewardId = e.target.dataset.rewardId;
                this.redeemReward(rewardId);
            }
        });
        
        // Transaction history pagination
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-load-transactions]')) {
                e.preventDefault();
                const page = e.target.dataset.page;
                const type = e.target.dataset.type;
                this.loadTransactions(page, type);
            }
        });
        
        // Tier info tooltips
        document.addEventListener('mouseenter', (e) => {
            if (e.target.matches('[data-tier-tooltip]')) {
                this.showTierTooltip(e.target);
            }
        });
        
        // Refresh data button
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-refresh-loyalty]')) {
                e.preventDefault();
                this.loadLoyaltyData(true);
            }
        });
    }
    
    async loadLoyaltyData(forceRefresh = false) {
        if (this.isLoading && !forceRefresh) return;
        
        this.isLoading = true;
        this.showLoading();
        
        try {
            const response = await fetch('/loyalty/data');
            const data = await response.json();
            
            if (data.success) {
                this.currentData = data.data;
                this.renderLoyaltyDashboard();
                this.updateWidgets();
                this.checkExpiringPoints();
            } else {
                this.showError('Không thể tải dữ liệu loyalty');
            }
        } catch (error) {
            console.error('Error loading loyalty data:', error);
            this.showError('Lỗi kết nối khi tải dữ liệu');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }
    
    renderLoyaltyDashboard() {
        if (!this.currentData) return;
        
        const account = this.currentData.account;
        
        // Update main stats
        this.updateElement('[data-current-points]', account.points.current.toLocaleString());
        this.updateElement('[data-total-earned]', account.points.total_earned.toLocaleString());
        this.updateElement('[data-total-spent]', account.points.total_spent.toLocaleString());
        this.updateElement('[data-expiring-points]', account.points.expiring_soon.toLocaleString());
        
        // Update tier display
        this.updateTierDisplay(account.current_tier);
        
        // Update progress bar
        this.updateProgressBar(account.progress);
        
        // Update annual summary
        this.updateAnnualSummary(account.annual);
        
        // Update recent transactions
        this.updateTransactionsList(this.currentData.recent_transactions);
        
        // Update achievements
        this.updateAchievements(account.achievements);
        
        // Animate numbers
        this.animateNumbers();
    }
    
    updateTierDisplay(tierData) {
        const tierContainer = document.querySelector('[data-tier-display]');
        if (!tierContainer) return;
        
        // Update tier icon
        const tierIcon = tierContainer.querySelector('.tier-icon');
        if (tierIcon) {
            tierIcon.style.background = tierData.color;
            tierIcon.innerHTML = `<i class="${tierData.icon}"></i>`;
        }
        
        // Update tier name
        this.updateElement('[data-tier-name]', tierData.name);
        
        // Add tier-specific class
        tierContainer.className = `tier-display tier-${tierData.name.toLowerCase()}`;
    }
    
    updateProgressBar(progressData) {
        const progressBar = document.querySelector('[data-progress-bar]');
        const progressText = document.querySelector('[data-progress-text]');
        
        if (progressBar) {
            const percentage = progressData.percentage || 0;
            progressBar.style.width = percentage + '%';
            
            // Animate progress bar
            setTimeout(() => {
                progressBar.style.transition = 'width 1.5s ease-out';
            }, 100);
        }
        
        if (progressText && progressData.next_tier_name) {
            progressText.textContent = `${progressData.to_next_tier.toLocaleString()} điểm để đạt ${progressData.next_tier_name}`;
        } else if (progressText) {
            progressText.textContent = 'Đã đạt hạng cao nhất!';
        }
    }
    
    updateAnnualSummary(annualData) {
        this.updateElement('[data-annual-points]', annualData.points_earned.toLocaleString());
        this.updateElement('[data-annual-spending]', this.formatCurrency(annualData.amount_spent));
        this.updateElement('[data-annual-orders]', annualData.orders_placed.toString());
        this.updateElement('[data-avg-order]', this.formatCurrency(annualData.average_order_value));
    }
    
    updateTransactionsList(transactions) {
        const container = document.querySelector('[data-transactions-list]');
        if (!container) return;
        
        if (transactions.length === 0) {
            container.innerHTML = `
                <div class="empty-transactions">
                    <div class="empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <p>Chưa có giao dịch nào</p>
                </div>
            `;
            return;
        }
        
        const html = transactions.map(transaction => this.createTransactionItem(transaction)).join('');
        container.innerHTML = html;
        
        // Animate transaction items
        this.animateTransactionItems();
    }
    
    createTransactionItem(transaction) {
        const iconClass = this.getTransactionIconClass(transaction.type);
        const pointsClass = this.getPointsClass(transaction.type);
        
        return `
            <div class="transaction-item" data-transaction-id="${transaction.id}">
                <div class="transaction-icon">
                    <i class="${iconClass}"></i>
                </div>
                <div class="transaction-details">
                    <div class="transaction-description">${transaction.description}</div>
                    <div class="transaction-meta">
                        ${transaction.source} • ${transaction.date}
                    </div>
                </div>
                <div class="transaction-points">
                    <div class="points-amount ${pointsClass}">
                        ${transaction.points}
                    </div>
                    <div class="transaction-date">${transaction.date}</div>
                </div>
            </div>
        `;
    }
    
    getTransactionIconClass(type) {
        const icons = {
            'earned': 'fas fa-plus-circle text-success',
            'spent': 'fas fa-minus-circle text-danger',
            'expired': 'fas fa-clock text-warning',
            'bonus': 'fas fa-gift text-primary',
            'refund': 'fas fa-undo text-info'
        };
        return icons[type] || 'fas fa-circle text-muted';
    }
    
    getPointsClass(type) {
        const classes = {
            'earned': 'earned',
            'bonus': 'earned',
            'refund': 'earned',
            'spent': 'spent',
            'expired': 'expired'
        };
        return classes[type] || '';
    }
    
    updateAchievements(achievements) {
        const container = document.querySelector('[data-achievements]');
        if (!container || !achievements) return;
        
        if (achievements.length === 0) {
            container.innerHTML = '<p class="text-muted">Chưa có thành tựu nào</p>';
            return;
        }
        
        const html = achievements.map(achievement => {
            const info = this.getAchievementInfo(achievement);
            return `
                <div class="achievement-badge" title="${info.description}">
                    <i class="${info.icon}"></i>
                    <span>${info.name}</span>
                </div>
            `;
        }).join('');
        
        container.innerHTML = html;
    }
    
    getAchievementInfo(achievement) {
        const achievements = {
            'points_500': { name: '500 điểm', icon: 'fas fa-star', description: 'Đạt 500 điểm tích lũy' },
            'points_1000': { name: '1K điểm', icon: 'fas fa-star', description: 'Đạt 1,000 điểm tích lũy' },
            'points_5000': { name: '5K điểm', icon: 'fas fa-medal', description: 'Đạt 5,000 điểm tích lũy' },
            'tier_upgrade_silver': { name: 'Hạng Bạc', icon: 'fas fa-medal', description: 'Nâng cấp lên hạng Bạc' },
            'tier_upgrade_gold': { name: 'Hạng Vàng', icon: 'fas fa-crown', description: 'Nâng cấp lên hạng Vàng' },
            'orders_10': { name: '10 đơn hàng', icon: 'fas fa-shopping-bag', description: 'Hoàn thành 10 đơn hàng' },
            'spending_5000000': { name: '5M VNĐ', icon: 'fas fa-coins', description: 'Chi tiêu 5 triệu VNĐ' }
        };
        
        return achievements[achievement] || { name: achievement, icon: 'fas fa-trophy', description: 'Thành tựu đặc biệt' };
    }
    
    async claimBirthdayBonus() {
        try {
            this.showLoading();
            
            const response = await fetch('/loyalty/claim-birthday', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadLoyaltyData(); // Refresh data
                this.celebratePoints(data.points);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Lỗi khi nhận thưởng sinh nhật');
        } finally {
            this.hideLoading();
        }
    }
    
    async redeemReward(rewardId) {
        if (!confirm('Bạn có chắc muốn đổi phần thưởng này?')) {
            return;
        }
        
        try {
            this.showLoading();
            
            const response = await fetch('/loyalty/redeem-reward', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({ reward_id: rewardId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                this.loadLoyaltyData(); // Refresh data
                this.celebrateRedemption(data.transaction.points_spent);
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            this.showError('Lỗi khi đổi phần thưởng');
        } finally {
            this.hideLoading();
        }
    }
    
    async loadTransactions(page = 1, type = null) {
        const params = new URLSearchParams({ page, per_page: 15 });
        if (type) params.set('type', type);
        
        try {
            const response = await fetch(`/loyalty/transactions?${params}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderTransactionHistory(data.transactions, data.pagination);
            }
        } catch (error) {
            console.error('Error loading transactions:', error);
        }
    }
    
    renderTransactionHistory(transactions, pagination) {
        const container = document.querySelector('[data-transaction-history]');
        if (!container) return;
        
        const html = transactions.map(transaction => this.createTransactionItem(transaction)).join('');
        container.innerHTML = html;
        
        // Update pagination
        this.updatePagination(pagination);
    }
    
    updatePagination(pagination) {
        const container = document.querySelector('[data-pagination]');
        if (!container) return;
        
        let html = '';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<button class="btn-pagination" data-load-transactions data-page="${pagination.current_page - 1}">‹ Trước</button>`;
        }
        
        // Page numbers
        for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.last_page, pagination.current_page + 2); i++) {
            const active = i === pagination.current_page ? 'active' : '';
            html += `<button class="btn-pagination ${active}" data-load-transactions data-page="${i}">${i}</button>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<button class="btn-pagination" data-load-transactions data-page="${pagination.current_page + 1}">Sau ›</button>`;
        }
        
        container.innerHTML = html;
    }
    
    async checkExpiringPoints() {
        try {
            const response = await fetch('/loyalty/expiring-points');
            const data = await response.json();
            
            if (data.success && data.total_expiring > 0) {
                this.showExpiringPointsAlert(data.total_expiring, data.expiring_points);
            }
        } catch (error) {
            console.error('Error checking expiring points:', error);
        }
    }
    
    showExpiringPointsAlert(totalPoints, expiringPoints) {
        const alertContainer = document.querySelector('[data-expiring-alert]');
        if (!alertContainer) return;
        
        const nearestExpiry = expiringPoints[0];
        const message = `${totalPoints.toLocaleString()} điểm sẽ hết hạn trong ${nearestExpiry.days_until_expiry} ngày`;
        
        alertContainer.innerHTML = `
            <div class="expiring-alert">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <div class="alert-title">Điểm sắp hết hạn!</div>
                    <div class="alert-message">${message}</div>
                </div>
            </div>
        `;
        
        alertContainer.style.display = 'block';
    }
    
    initializeWidgets() {
        // Initialize tier progress widgets
        this.updateTierProgressWidgets();
        
        // Initialize mini loyalty display
        this.updateMiniLoyaltyDisplay();
    }
    
    async updateWidgets() {
        try {
            const response = await fetch('/loyalty/tier-progress');
            const data = await response.json();
            
            if (data.success) {
                this.renderWidgets(data.progress);
            }
        } catch (error) {
            console.error('Error updating widgets:', error);
        }
    }
    
    renderWidgets(progressData) {
        // Update loyalty widgets throughout the site
        document.querySelectorAll('[data-loyalty-widget]').forEach(widget => {
            this.updateWidget(widget, progressData);
        });
    }
    
    updateWidget(widget, data) {
        const pointsElement = widget.querySelector('[data-widget-points]');
        const tierElement = widget.querySelector('[data-widget-tier]');
        const progressElement = widget.querySelector('[data-widget-progress]');
        
        if (pointsElement) {
            pointsElement.textContent = data.points.current.toLocaleString();
        }
        
        if (tierElement) {
            tierElement.textContent = data.current_tier.name;
            tierElement.style.color = data.current_tier.color;
        }
        
        if (progressElement && data.next_tier) {
            const percentage = data.next_tier.progress_percentage;
            progressElement.style.width = percentage + '%';
        }
    }
    
    updateTierProgressWidgets() {
        // Update navigation bar tier display
        const navTierWidget = document.querySelector('[data-nav-tier]');
        if (navTierWidget && this.currentData) {
            this.updateWidget(navTierWidget, this.currentData.account);
        }
    }
    
    updateMiniLoyaltyDisplay() {
        // Update mini displays in checkout, cart, etc.
        const miniDisplays = document.querySelectorAll('[data-mini-loyalty]');
        miniDisplays.forEach(display => {
            if (this.currentData) {
                this.updateWidget(display, this.currentData.account);
            }
        });
    }
    
    celebratePoints(points) {
        // Show celebration animation for earning points
        this.showCelebration(`🎉 +${points.toLocaleString()} điểm!`);
        this.animatePointsIncrease();
    }
    
    celebrateRedemption(pointsSpent) {
        // Show celebration for successful redemption
        this.showCelebration(`✨ Đã đổi ${pointsSpent.toLocaleString()} điểm!`);
    }
    
    showCelebration(message) {
        // Create celebration overlay
        const celebration = document.createElement('div');
        celebration.className = 'loyalty-celebration';
        celebration.innerHTML = `
            <div class="celebration-content">
                <div class="celebration-message">${message}</div>
            </div>
        `;
        
        document.body.appendChild(celebration);
        
        // Animate and remove
        setTimeout(() => {
            celebration.classList.add('fade-out');
            setTimeout(() => {
                document.body.removeChild(celebration);
            }, 500);
        }, 2000);
    }
    
    animateNumbers() {
        // Animate number counters
        document.querySelectorAll('[data-animate-number]').forEach(element => {
            this.animateCounter(element);
        });
    }
    
    animateCounter(element) {
        const target = parseInt(element.textContent.replace(/,/g, ''));
        const duration = 1500;
        const start = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(target * this.easeOutCubic(progress));
            element.textContent = current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }
    
    animateTransactionItems() {
        const items = document.querySelectorAll('.transaction-item');
        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'all 0.3s ease-out';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 50);
            }, index * 100);
        });
    }
    
    animatePointsIncrease() {
        const pointsElement = document.querySelector('[data-current-points]');
        if (pointsElement) {
            pointsElement.classList.add('points-increase-animation');
            setTimeout(() => {
                pointsElement.classList.remove('points-increase-animation');
            }, 1000);
        }
    }
    
    easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }
    
    showLoading() {
        const loader = document.querySelector('[data-loyalty-loader]');
        if (loader) loader.style.display = 'flex';
    }
    
    hideLoading() {
        const loader = document.querySelector('[data-loyalty-loader]');
        if (loader) loader.style.display = 'none';
    }
    
    showSuccess(message) {
        this.showToast(message, 'success');
    }
    
    showError(message) {
        this.showToast(message, 'error');
    }
    
    showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `loyalty-toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="toast-icon fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span class="toast-message">${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Remove after delay
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }
    
    updateElement(selector, content) {
        const element = document.querySelector(selector);
        if (element) {
            element.textContent = content;
        }
    }
    
    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
}

// Auto-initialize loyalty system
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.loyaltySystem === 'undefined') {
        window.loyaltySystem = new LoyaltySystem();
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LoyaltySystem;
}
