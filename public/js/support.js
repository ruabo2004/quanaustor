/**
 * Customer Support System
 * Handles FAQ search, live chat, and ticket management
 */

class SupportSystem {
    constructor() {
        this.searchTimeout = null;
        this.chatSocket = null;
        this.chatSessionId = null;
        this.isTyping = false;
        this.currentChatStatus = 'offline';
        
        this.init();
    }
    
    init() {
        this.initFAQSearch();
        this.initFAQAccordion();
        this.initFAQFeedback();
        this.initChatWidget();
        this.initTicketForm();
        this.loadPopularFAQs();
    }
    
    // =====================================
    // FAQ SYSTEM
    // =====================================
    
    initFAQSearch() {
        const searchBox = document.querySelector('.faq-search-box');
        const suggestionsContainer = document.querySelector('.faq-search-suggestions');
        
        if (!searchBox) return;
        
        // Real-time search with debounce
        searchBox.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            if (query.length >= 2) {
                this.searchTimeout = setTimeout(() => {
                    this.performFAQSearch(query);
                    this.loadSearchSuggestions(query);
                }, 300);
            } else {
                this.hideSuggestions();
                this.resetFAQResults();
            }
        });
        
        // Handle suggestion clicks
        if (suggestionsContainer) {
            suggestionsContainer.addEventListener('click', (e) => {
                const suggestionItem = e.target.closest('.faq-suggestion-item');
                if (suggestionItem) {
                    const text = suggestionItem.querySelector('.suggestion-text').textContent;
                    searchBox.value = text;
                    this.performFAQSearch(text);
                    this.hideSuggestions();
                }
            });
        }
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.faq-search-container')) {
                this.hideSuggestions();
            }
        });
        
        // Category filters
        document.addEventListener('click', (e) => {
            if (e.target.matches('.category-filter')) {
                e.preventDefault();
                this.filterByCategory(e.target.dataset.category);
            }
        });
    }
    
    async performFAQSearch(query) {
        try {
            const category = document.querySelector('.category-filter.active')?.dataset.category;
            const params = new URLSearchParams({ q: query });
            if (category) params.set('category', category);
            
            const response = await fetch(`/faq/search/ajax?${params}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderFAQResults(data.faqs, query);
            }
        } catch (error) {
            console.error('FAQ search error:', error);
            this.showError('Lỗi khi tìm kiếm FAQ');
        }
    }
    
    async loadSearchSuggestions(query) {
        try {
            const response = await fetch(`/faq/search/suggestions?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            
            this.renderSuggestions(suggestions);
        } catch (error) {
            console.error('FAQ suggestions error:', error);
        }
    }
    
    renderSuggestions(suggestions) {
        const container = document.querySelector('.faq-search-suggestions');
        if (!container) return;
        
        if (suggestions.length === 0) {
            this.hideSuggestions();
            return;
        }
        
        const html = suggestions.map(suggestion => `
            <div class="faq-suggestion-item" data-id="${suggestion.id}">
                <div class="suggestion-text">${suggestion.text}</div>
                <div class="suggestion-category">${suggestion.category}</div>
            </div>
        `).join('');
        
        container.innerHTML = html;
        container.style.display = 'block';
    }
    
    hideSuggestions() {
        const container = document.querySelector('.faq-search-suggestions');
        if (container) {
            container.style.display = 'none';
        }
    }
    
    renderFAQResults(faqs, query) {
        const container = document.querySelector('.faq-results') || document.querySelector('.faq-list');
        if (!container) return;
        
        if (faqs.length === 0) {
            container.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Không tìm thấy kết quả</h3>
                    <p>Không có FAQ nào phù hợp với từ khóa "${query}"</p>
                    <a href="#" class="btn-contact-support">Liên hệ hỗ trợ</a>
                </div>
            `;
            return;
        }
        
        const html = faqs.map(faq => this.createFAQItemHTML(faq)).join('');
        container.innerHTML = html;
        
        // Reinitialize accordion for new content
        this.initFAQAccordion();
    }
    
    createFAQItemHTML(faq) {
        return `
            <div class="faq-item" data-id="${faq.id}">
                <div class="faq-item-header">
                    <h3 class="faq-item-question">${faq.question}</h3>
                    <div class="faq-toggle">
                        <i class="fas fa-plus"></i>
                    </div>
                </div>
                <div class="faq-item-content">
                    <div class="faq-answer">${faq.answer}</div>
                    <div class="faq-meta">
                        <div class="faq-category-badge">
                            <i class="${faq.category_icon}"></i>
                            ${faq.category}
                        </div>
                        <div class="faq-stats">
                            <div class="faq-stat">
                                <i class="fas fa-eye"></i>
                                ${faq.view_count}
                            </div>
                            <div class="faq-stat">
                                <i class="fas fa-thumbs-up"></i>
                                ${faq.helpfulness}%
                            </div>
                        </div>
                    </div>
                    <div class="faq-feedback">
                        <span class="feedback-question">Câu trả lời này có hữu ích không?</span>
                        <div class="feedback-buttons">
                            <button class="feedback-btn helpful" data-action="helpful" data-id="${faq.id}">
                                <i class="fas fa-thumbs-up"></i>
                                Có
                            </button>
                            <button class="feedback-btn not-helpful" data-action="not-helpful" data-id="${faq.id}">
                                <i class="fas fa-thumbs-down"></i>
                                Không
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    resetFAQResults() {
        // Reset to original FAQ list or show all FAQs
        const container = document.querySelector('.faq-list');
        if (container && !container.querySelector('.faq-item')) {
            this.loadAllFAQs();
        }
    }
    
    filterByCategory(category) {
        // Update active category filter
        document.querySelectorAll('.category-filter').forEach(filter => {
            filter.classList.toggle('active', filter.dataset.category === category);
        });
        
        // Perform search with category filter
        const searchBox = document.querySelector('.faq-search-box');
        const query = searchBox ? searchBox.value.trim() : '';
        
        if (query) {
            this.performFAQSearch(query);
        } else {
            this.loadFAQsByCategory(category);
        }
    }
    
    async loadFAQsByCategory(category) {
        try {
            const url = category ? `/faq?category=${category}` : '/faq';
            const response = await fetch(url);
            const html = await response.text();
            
            // Extract FAQ list from response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newFAQList = doc.querySelector('.faq-list');
            
            if (newFAQList) {
                const currentContainer = document.querySelector('.faq-list');
                if (currentContainer) {
                    currentContainer.innerHTML = newFAQList.innerHTML;
                    this.initFAQAccordion();
                }
            }
        } catch (error) {
            console.error('Category filter error:', error);
        }
    }
    
    initFAQAccordion() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.faq-item-header')) {
                const faqItem = e.target.closest('.faq-item');
                const isActive = faqItem.classList.contains('active');
                
                // Close all other FAQ items
                document.querySelectorAll('.faq-item.active').forEach(item => {
                    if (item !== faqItem) {
                        item.classList.remove('active');
                    }
                });
                
                // Toggle current item
                faqItem.classList.toggle('active', !isActive);
                
                // Track view if opening
                if (!isActive) {
                    this.trackFAQView(faqItem.dataset.id);
                }
            }
        });
    }
    
    initFAQFeedback() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.feedback-btn[data-action]')) {
                e.preventDefault();
                this.submitFAQFeedback(e.target);
            }
        });
    }
    
    async submitFAQFeedback(button) {
        const action = button.dataset.action;
        const faqId = button.dataset.id;
        
        try {
            const endpoint = action === 'helpful' ? 'helpful' : 'not-helpful';
            const response = await fetch(`/faq/${faqId}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message);
                
                // Disable feedback buttons
                const feedbackContainer = button.closest('.faq-feedback');
                feedbackContainer.querySelectorAll('.feedback-btn').forEach(btn => {
                    btn.classList.add('voted');
                    btn.disabled = true;
                });
                
                // Update helpfulness display if needed
                this.updateFAQHelpfulness(faqId, data.helpfulness_percentage);
            }
        } catch (error) {
            console.error('FAQ feedback error:', error);
            this.showError('Lỗi khi gửi phản hồi');
        }
    }
    
    updateFAQHelpfulness(faqId, percentage) {
        const faqItem = document.querySelector(`[data-id="${faqId}"]`);
        if (faqItem) {
            const helpfulnessStat = faqItem.querySelector('.faq-stat:last-child');
            if (helpfulnessStat) {
                helpfulnessStat.innerHTML = `<i class="fas fa-thumbs-up"></i> ${percentage}%`;
            }
        }
    }
    
    async trackFAQView(faqId) {
        try {
            await fetch(`/faq/${faqId}/view`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
        } catch (error) {
            // Silent fail for analytics
        }
    }
    
    async loadPopularFAQs() {
        try {
            const response = await fetch('/faq/api/popular');
            const data = await response.json();
            
            if (data.success) {
                this.renderPopularFAQsWidget(data.faqs);
            }
        } catch (error) {
            console.error('Popular FAQs error:', error);
        }
    }
    
    renderPopularFAQsWidget(faqs) {
        const widget = document.querySelector('.popular-faqs-widget');
        if (!widget) return;
        
        const html = faqs.map(faq => `
            <div class="popular-faq-item">
                <a href="/faq/${faq.id}" class="popular-faq-link">
                    <div class="popular-faq-question">${faq.question}</div>
                    <div class="popular-faq-meta">
                        <span class="popular-faq-category">
                            <i class="${faq.category_icon}"></i>
                            ${faq.category}
                        </span>
                        <span class="popular-faq-views">${faq.view_count} lượt xem</span>
                    </div>
                </a>
            </div>
        `).join('');
        
        widget.innerHTML = html;
    }
    
    // =====================================
    // LIVE CHAT SYSTEM
    // =====================================
    
    initChatWidget() {
        const chatToggle = document.querySelector('.chat-toggle');
        const chatWindow = document.querySelector('.chat-window');
        const chatClose = document.querySelector('.chat-close');
        const chatForm = document.querySelector('.chat-input-form');
        
        if (!chatToggle) return;
        
        // Toggle chat window
        chatToggle.addEventListener('click', () => {
            this.toggleChatWindow();
        });
        
        if (chatClose) {
            chatClose.addEventListener('click', () => {
                this.closeChatWindow();
            });
        }
        
        if (chatForm) {
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendChatMessage();
            });
        }
        
        // Auto-resize chat input
        const chatInput = document.querySelector('.chat-input');
        if (chatInput) {
            chatInput.addEventListener('input', (e) => {
                this.autoResizeChatInput(e.target);
            });
        }
        
        // Check chat availability
        this.checkChatAvailability();
        
        // Connect to chat socket
        this.initChatSocket();
    }
    
    toggleChatWindow() {
        const chatWindow = document.querySelector('.chat-window');
        const isOpen = chatWindow.classList.contains('active');
        
        if (isOpen) {
            this.closeChatWindow();
        } else {
            this.openChatWindow();
        }
    }
    
    openChatWindow() {
        const chatWindow = document.querySelector('.chat-window');
        chatWindow.classList.add('active');
        
        // Focus on input
        const chatInput = document.querySelector('.chat-input');
        if (chatInput) {
            setTimeout(() => chatInput.focus(), 300);
        }
        
        // Start chat session if not already started
        if (!this.chatSessionId) {
            this.startChatSession();
        }
        
        // Mark messages as read
        this.markChatMessagesAsRead();
    }
    
    closeChatWindow() {
        const chatWindow = document.querySelector('.chat-window');
        chatWindow.classList.remove('active');
    }
    
    async startChatSession() {
        try {
            const response = await fetch('/chat/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    user_info: this.getUserInfo()
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.chatSessionId = data.session_id;
                this.updateChatStatus(data.status);
                
                // Show welcome message
                this.addChatMessage('system', 'Chào mừng bạn! Chúng tôi sẽ hỗ trợ bạn trong giây lát.');
                
                if (data.queue_position > 0) {
                    this.addChatMessage('system', `Bạn đang ở vị trí ${data.queue_position} trong hàng đợi.`);
                }
            }
        } catch (error) {
            console.error('Start chat session error:', error);
            this.addChatMessage('system', 'Xin lỗi, hiện tại không thể kết nối chat. Vui lòng thử lại sau.');
        }
    }
    
    async sendChatMessage() {
        const chatInput = document.querySelector('.chat-input');
        const message = chatInput.value.trim();
        
        if (!message || !this.chatSessionId) return;
        
        try {
            // Add message to UI immediately
            this.addChatMessage('user', message);
            chatInput.value = '';
            this.autoResizeChatInput(chatInput);
            
            // Send to server
            const response = await fetch('/chat/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    session_id: this.chatSessionId,
                    message: message
                })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                this.addChatMessage('system', 'Không thể gửi tin nhắn. Vui lòng thử lại.');
            }
        } catch (error) {
            console.error('Send message error:', error);
            this.addChatMessage('system', 'Lỗi khi gửi tin nhắn.');
        }
    }
    
    addChatMessage(sender, message, timestamp = null) {
        const messagesContainer = document.querySelector('.chat-messages');
        if (!messagesContainer) return;
        
        const messageElement = document.createElement('div');
        messageElement.className = `chat-message ${sender}`;
        
        const avatarClass = sender === 'user' ? 'user' : 'agent';
        const avatarText = sender === 'user' ? 'B' : (sender === 'system' ? 'S' : 'A');
        
        messageElement.innerHTML = `
            <div class="message-avatar ${avatarClass}">${avatarText}</div>
            <div class="message-content">
                <p class="message-text">${message}</p>
                <div class="message-time">${timestamp || this.formatTime(new Date())}</div>
            </div>
        `;
        
        messagesContainer.appendChild(messageElement);
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Animate message
        messageElement.style.opacity = '0';
        messageElement.style.transform = 'translateY(10px)';
        
        requestAnimationFrame(() => {
            messageElement.style.transition = 'all 0.3s ease-out';
            messageElement.style.opacity = '1';
            messageElement.style.transform = 'translateY(0)';
        });
    }
    
    autoResizeChatInput(input) {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 100) + 'px';
    }
    
    updateChatStatus(status) {
        this.currentChatStatus = status;
        const statusElement = document.querySelector('.chat-status');
        
        if (statusElement) {
            const statusTexts = {
                'waiting': 'Đang chờ...',
                'active': 'Đang hoạt động',
                'ended': 'Đã kết thúc',
                'offline': 'Ngoại tuyến'
            };
            
            statusElement.textContent = statusTexts[status] || 'Không xác định';
        }
        
        // Update toggle button
        const chatToggle = document.querySelector('.chat-toggle');
        if (chatToggle) {
            const hasUnread = this.hasUnreadMessages();
            chatToggle.classList.toggle('has-unread', hasUnread);
        }
    }
    
    async checkChatAvailability() {
        try {
            const response = await fetch('/chat/availability');
            const data = await response.json();
            
            const chatWidget = document.querySelector('.chat-widget');
            if (chatWidget) {
                chatWidget.style.display = data.available ? 'block' : 'none';
            }
            
            if (!data.available) {
                this.updateChatStatus('offline');
            }
        } catch (error) {
            console.error('Chat availability error:', error);
        }
    }
    
    initChatSocket() {
        // Initialize WebSocket connection for real-time chat
        // This would typically use Socket.IO or similar
        if (typeof io !== 'undefined') {
            this.chatSocket = io('/chat');
            
            this.chatSocket.on('message', (data) => {
                this.addChatMessage('agent', data.message, data.timestamp);
            });
            
            this.chatSocket.on('status_change', (data) => {
                this.updateChatStatus(data.status);
            });
            
            this.chatSocket.on('agent_joined', (data) => {
                this.addChatMessage('system', `${data.agent_name} đã tham gia cuộc trò chuyện.`);
            });
        }
    }
    
    markChatMessagesAsRead() {
        if (!this.chatSessionId) return;
        
        fetch('/chat/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            },
            body: JSON.stringify({
                session_id: this.chatSessionId
            })
        }).catch(error => {
            console.error('Mark messages as read error:', error);
        });
    }
    
    hasUnreadMessages() {
        // Check for unread messages indicator
        return document.querySelector('.chat-messages .message-unread') !== null;
    }
    
    // =====================================
    // TICKET SYSTEM
    // =====================================
    
    initTicketForm() {
        const ticketForm = document.querySelector('.ticket-form');
        if (!ticketForm) return;
        
        ticketForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitTicket();
        });
        
        // File upload handling
        const fileInput = document.querySelector('.file-upload-input');
        const fileUpload = document.querySelector('.file-upload');
        
        if (fileInput && fileUpload) {
            fileUpload.addEventListener('click', () => {
                fileInput.click();
            });
            
            fileInput.addEventListener('change', (e) => {
                this.handleFileUpload(e.target.files);
            });
            
            // Drag and drop
            fileUpload.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUpload.classList.add('dragover');
            });
            
            fileUpload.addEventListener('dragleave', () => {
                fileUpload.classList.remove('dragover');
            });
            
            fileUpload.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUpload.classList.remove('dragover');
                this.handleFileUpload(e.dataTransfer.files);
            });
        }
        
        // Auto-suggest related FAQs based on subject
        const subjectInput = document.querySelector('[name="subject"]');
        if (subjectInput) {
            subjectInput.addEventListener('input', (e) => {
                this.suggestRelatedFAQs(e.target.value);
            });
        }
    }
    
    async submitTicket() {
        const form = document.querySelector('.ticket-form');
        const formData = new FormData(form);
        
        try {
            this.showLoading('Đang gửi ticket...');
            
            const response = await fetch('/support/tickets', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken()
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess('Ticket đã được gửi thành công! Mã ticket của bạn là: ' + data.ticket_number);
                form.reset();
                
                // Redirect to ticket view
                setTimeout(() => {
                    window.location.href = `/support/tickets/${data.ticket_id}`;
                }, 2000);
            } else {
                this.showError(data.message || 'Có lỗi xảy ra khi gửi ticket');
            }
        } catch (error) {
            console.error('Submit ticket error:', error);
            this.showError('Lỗi kết nối khi gửi ticket');
        } finally {
            this.hideLoading();
        }
    }
    
    handleFileUpload(files) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];
        
        Array.from(files).forEach(file => {
            if (file.size > maxSize) {
                this.showError(`File ${file.name} quá lớn. Kích thước tối đa là 5MB.`);
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                this.showError(`File ${file.name} không được hỗ trợ.`);
                return;
            }
            
            this.addFileToUploadList(file);
        });
    }
    
    addFileToUploadList(file) {
        const uploadList = document.querySelector('.upload-list') || this.createUploadList();
        
        const fileItem = document.createElement('div');
        fileItem.className = 'upload-item';
        fileItem.innerHTML = `
            <div class="upload-item-info">
                <i class="fas fa-file"></i>
                <span class="upload-item-name">${file.name}</span>
                <span class="upload-item-size">(${this.formatFileSize(file.size)})</span>
            </div>
            <button type="button" class="upload-item-remove">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        fileItem.querySelector('.upload-item-remove').addEventListener('click', () => {
            fileItem.remove();
        });
        
        uploadList.appendChild(fileItem);
    }
    
    createUploadList() {
        const fileUpload = document.querySelector('.file-upload');
        const uploadList = document.createElement('div');
        uploadList.className = 'upload-list';
        fileUpload.parentNode.insertBefore(uploadList, fileUpload.nextSibling);
        return uploadList;
    }
    
    async suggestRelatedFAQs(subject) {
        if (subject.length < 3) {
            this.hideRelatedFAQs();
            return;
        }
        
        try {
            const response = await fetch(`/faq/search/ajax?q=${encodeURIComponent(subject)}`);
            const data = await response.json();
            
            if (data.success && data.faqs.length > 0) {
                this.showRelatedFAQs(data.faqs.slice(0, 3));
            } else {
                this.hideRelatedFAQs();
            }
        } catch (error) {
            console.error('Related FAQs error:', error);
        }
    }
    
    showRelatedFAQs(faqs) {
        let container = document.querySelector('.related-faqs');
        
        if (!container) {
            container = document.createElement('div');
            container.className = 'related-faqs';
            const subjectGroup = document.querySelector('[name="subject"]').closest('.form-group');
            subjectGroup.parentNode.insertBefore(container, subjectGroup.nextSibling);
        }
        
        container.innerHTML = `
            <div class="related-faqs-header">
                <i class="fas fa-lightbulb"></i>
                Có thể các câu hỏi này đã được trả lời:
            </div>
            <div class="related-faqs-list">
                ${faqs.map(faq => `
                    <a href="/faq/${faq.id}" class="related-faq-item" target="_blank">
                        <div class="related-faq-question">${faq.question}</div>
                        <div class="related-faq-category">${faq.category}</div>
                    </a>
                `).join('')}
            </div>
        `;
        
        container.style.display = 'block';
    }
    
    hideRelatedFAQs() {
        const container = document.querySelector('.related-faqs');
        if (container) {
            container.style.display = 'none';
        }
    }
    
    // =====================================
    // UTILITY FUNCTIONS
    // =====================================
    
    getUserInfo() {
        return {
            url: window.location.href,
            referrer: document.referrer,
            user_agent: navigator.userAgent,
            screen_resolution: `${screen.width}x${screen.height}`,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
        };
    }
    
    formatTime(date) {
        return date.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    showLoading(message = 'Đang tải...') {
        this.showToast(message, 'info', 0);
    }
    
    hideLoading() {
        this.hideToast();
    }
    
    showSuccess(message) {
        this.showToast(message, 'success');
    }
    
    showError(message) {
        this.showToast(message, 'error');
    }
    
    showToast(message, type = 'info', duration = 4000) {
        const existingToast = document.querySelector('.support-toast');
        if (existingToast) {
            existingToast.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = `support-toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="toast-icon fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span class="toast-message">${message}</span>
                ${duration > 0 ? '<button class="toast-close"><i class="fas fa-times"></i></button>' : ''}
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Close button
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.hideToast());
        }
        
        // Auto hide
        if (duration > 0) {
            setTimeout(() => this.hideToast(), duration);
        }
    }
    
    hideToast() {
        const toast = document.querySelector('.support-toast');
        if (toast) {
            toast.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
}

// Auto-initialize support system
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.supportSystem === 'undefined') {
        window.supportSystem = new SupportSystem();
    }
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SupportSystem;
}
