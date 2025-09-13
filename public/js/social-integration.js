/**
 * Social Media Integration System
 * Handles social login, sharing, and authentication
 */

class SocialIntegration {
    constructor() {
        this.providers = {
            facebook: {
                appId: window.FB_APP_ID || null,
                loaded: false
            },
            google: {
                clientId: window.GOOGLE_CLIENT_ID || null,
                loaded: false
            },
            twitter: {
                loaded: false
            }
        };
        
        this.init();
    }
    
    init() {
        this.loadSDKs();
        this.initializeSocialButtons();
        this.initializeSharingButtons();
        console.log('Social Integration initialized');
    }
    
    // =====================================
    // SDK LOADING
    // =====================================
    
    loadSDKs() {
        this.loadFacebookSDK();
        this.loadGoogleSDK();
        this.loadTwitterSDK();
    }
    
    loadFacebookSDK() {
        if (!this.providers.facebook.appId) return;
        
        window.fbAsyncInit = () => {
            FB.init({
                appId: this.providers.facebook.appId,
                cookie: true,
                xfbml: true,
                version: 'v18.0'
            });
            
            this.providers.facebook.loaded = true;
            this.onFacebookReady();
        };
        
        // Load SDK
        if (!document.getElementById('facebook-jssdk')) {
            const js = document.createElement('script');
            js.id = 'facebook-jssdk';
            js.src = 'https://connect.facebook.net/vi_VN/sdk.js';
            document.head.appendChild(js);
        }
    }
    
    loadGoogleSDK() {
        if (!this.providers.google.clientId) return;
        
        const script = document.createElement('script');
        script.src = 'https://apis.google.com/js/platform.js';
        script.onload = () => {
            gapi.load('auth2', () => {
                gapi.auth2.init({
                    client_id: this.providers.google.clientId
                }).then(() => {
                    this.providers.google.loaded = true;
                    this.onGoogleReady();
                });
            });
        };
        document.head.appendChild(script);
    }
    
    loadTwitterSDK() {
        if (!document.getElementById('twitter-wjs')) {
            const js = document.createElement('script');
            js.id = 'twitter-wjs';
            js.src = 'https://platform.twitter.com/widgets.js';
            document.head.appendChild(js);
            this.providers.twitter.loaded = true;
        }
    }
    
    // =====================================
    // SOCIAL LOGIN
    // =====================================
    
    onFacebookReady() {
        document.querySelectorAll('[data-social-login="facebook"]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.loginWithFacebook();
            });
        });
    }
    
    onGoogleReady() {
        document.querySelectorAll('[data-social-login="google"]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.loginWithGoogle();
            });
        });
    }
    
    async loginWithFacebook() {
        if (!this.providers.facebook.loaded) {
            this.showError('Facebook SDK not loaded');
            return;
        }
        
        try {
            const response = await new Promise((resolve, reject) => {
                FB.login((response) => {
                    if (response.authResponse) {
                        resolve(response);
                    } else {
                        reject('Facebook login cancelled');
                    }
                }, {
                    scope: 'email,public_profile',
                    return_scopes: true
                });
            });
            
            // Get user info
            const userInfo = await this.getFacebookUserInfo(response.authResponse.accessToken);
            
            // Send to backend
            await this.processSocialLogin('facebook', {
                provider_id: userInfo.id,
                access_token: response.authResponse.accessToken,
                email: userInfo.email,
                name: userInfo.name,
                avatar: userInfo.picture?.data?.url,
                profile_data: userInfo
            });
            
        } catch (error) {
            console.error('Facebook login error:', error);
            this.showError('Đăng nhập Facebook thất bại');
        }
    }
    
    async loginWithGoogle() {
        if (!this.providers.google.loaded) {
            this.showError('Google SDK not loaded');
            return;
        }
        
        try {
            const authInstance = gapi.auth2.getAuthInstance();
            const user = await authInstance.signIn();
            const profile = user.getBasicProfile();
            const authResponse = user.getAuthResponse();
            
            // Send to backend
            await this.processSocialLogin('google', {
                provider_id: profile.getId(),
                access_token: authResponse.access_token,
                email: profile.getEmail(),
                name: profile.getName(),
                avatar: profile.getImageUrl(),
                profile_data: {
                    given_name: profile.getGivenName(),
                    family_name: profile.getFamilyName(),
                    locale: profile.getLocale()
                }
            });
            
        } catch (error) {
            console.error('Google login error:', error);
            this.showError('Đăng nhập Google thất bại');
        }
    }
    
    async getFacebookUserInfo(accessToken) {
        return new Promise((resolve, reject) => {
            FB.api('/me', {
                fields: 'id,name,email,picture.width(200).height(200)',
                access_token: accessToken
            }, (response) => {
                if (response && !response.error) {
                    resolve(response);
                } else {
                    reject(response.error);
                }
            });
        });
    }
    
    async processSocialLogin(provider, userData) {
        try {
            const response = await fetch('/auth/social/callback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    provider: provider,
                    ...userData
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(`Đăng nhập ${provider} thành công!`);
                
                // Redirect or reload
                setTimeout(() => {
                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                this.showError(result.message || 'Đăng nhập thất bại');
            }
            
        } catch (error) {
            console.error('Social login processing error:', error);
            this.showError('Lỗi xử lý đăng nhập');
        }
    }
    
    // =====================================
    // SOCIAL SHARING
    // =====================================
    
    initializeSharingButtons() {
        // Facebook share
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-share="facebook"]')) {
                e.preventDefault();
                this.shareOnFacebook(e.target);
            }
        });
        
        // Twitter share
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-share="twitter"]')) {
                e.preventDefault();
                this.shareOnTwitter(e.target);
            }
        });
        
        // LinkedIn share
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-share="linkedin"]')) {
                e.preventDefault();
                this.shareOnLinkedIn(e.target);
            }
        });
        
        // Pinterest share
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-share="pinterest"]')) {
                e.preventDefault();
                this.shareOnPinterest(e.target);
            }
        });
        
        // WhatsApp share
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-share="whatsapp"]')) {
                e.preventDefault();
                this.shareOnWhatsApp(e.target);
            }
        });
        
        // Copy link
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-share="copy"]')) {
                e.preventDefault();
                this.copyToClipboard(e.target);
            }
        });
    }
    
    shareOnFacebook(button) {
        const url = this.getShareUrl(button);
        const title = this.getShareTitle(button);
        const description = this.getShareDescription(button);
        const image = this.getShareImage(button);
        
        if (this.providers.facebook.loaded) {
            FB.ui({
                method: 'share',
                href: url,
                title: title,
                description: description,
                picture: image
            }, (response) => {
                if (response && !response.error_message) {
                    this.trackShare('facebook', button);
                    this.showSuccess('Đã chia sẻ lên Facebook!');
                }
            });
        } else {
            // Fallback to popup
            const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            this.openSharePopup(shareUrl, 'Facebook');
            this.trackShare('facebook', button);
        }
    }
    
    shareOnTwitter(button) {
        const url = this.getShareUrl(button);
        const text = this.getShareTitle(button);
        const hashtags = this.getShareHashtags(button);
        
        const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}&hashtags=${encodeURIComponent(hashtags)}`;
        
        this.openSharePopup(shareUrl, 'Twitter');
        this.trackShare('twitter', button);
    }
    
    shareOnLinkedIn(button) {
        const url = this.getShareUrl(button);
        const title = this.getShareTitle(button);
        const summary = this.getShareDescription(button);
        
        const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}&summary=${encodeURIComponent(summary)}`;
        
        this.openSharePopup(shareUrl, 'LinkedIn');
        this.trackShare('linkedin', button);
    }
    
    shareOnPinterest(button) {
        const url = this.getShareUrl(button);
        const description = this.getShareDescription(button);
        const image = this.getShareImage(button);
        
        const shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&description=${encodeURIComponent(description)}&media=${encodeURIComponent(image)}`;
        
        this.openSharePopup(shareUrl, 'Pinterest');
        this.trackShare('pinterest', button);
    }
    
    shareOnWhatsApp(button) {
        const url = this.getShareUrl(button);
        const text = this.getShareTitle(button);
        
        const shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
        
        if (this.isMobile()) {
            window.location.href = shareUrl;
        } else {
            this.openSharePopup(shareUrl, 'WhatsApp');
        }
        
        this.trackShare('whatsapp', button);
    }
    
    async copyToClipboard(button) {
        const url = this.getShareUrl(button);
        
        try {
            await navigator.clipboard.writeText(url);
            this.showSuccess('Đã sao chép link!');
            this.trackShare('copy', button);
        } catch (error) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            this.showSuccess('Đã sao chép link!');
            this.trackShare('copy', button);
        }
    }
    
    // =====================================
    // SOCIAL BUTTONS INITIALIZATION
    // =====================================
    
    initializeSocialButtons() {
        // Social login buttons
        document.addEventListener('click', (e) => {
            const socialButton = e.target.closest('[data-social-login]');
            if (socialButton) {
                e.preventDefault();
                const provider = socialButton.dataset.socialLogin;
                this.showLoading(socialButton);
                
                switch (provider) {
                    case 'facebook':
                        this.loginWithFacebook();
                        break;
                    case 'google':
                        this.loginWithGoogle();
                        break;
                    default:
                        this.showError('Provider không được hỗ trợ');
                }
            }
        });
        
        // Add social login buttons to forms
        this.addSocialLoginButtons();
    }
    
    addSocialLoginButtons() {
        const loginForms = document.querySelectorAll('.login-form, #loginForm');
        
        loginForms.forEach(form => {
            if (!form.querySelector('.social-login-section')) {
                const socialSection = this.createSocialLoginSection();
                form.appendChild(socialSection);
            }
        });
    }
    
    createSocialLoginSection() {
        const section = document.createElement('div');
        section.className = 'social-login-section';
        section.innerHTML = `
            <div class="social-login-divider">
                <span>Hoặc đăng nhập bằng</span>
            </div>
            <div class="social-login-buttons">
                <button type="button" class="social-login-btn facebook-btn" data-social-login="facebook">
                    <i class="fab fa-facebook-f"></i>
                    Facebook
                </button>
                <button type="button" class="social-login-btn google-btn" data-social-login="google">
                    <i class="fab fa-google"></i>
                    Google
                </button>
            </div>
        `;
        return section;
    }
    
    // =====================================
    // SHARE DATA HELPERS
    // =====================================
    
    getShareUrl(button) {
        return button.dataset.shareUrl || 
               button.dataset.url || 
               window.location.href;
    }
    
    getShareTitle(button) {
        return button.dataset.shareTitle || 
               button.dataset.title || 
               document.title;
    }
    
    getShareDescription(button) {
        return button.dataset.shareDescription || 
               button.dataset.description || 
               document.querySelector('meta[name="description"]')?.content || 
               '';
    }
    
    getShareImage(button) {
        return button.dataset.shareImage || 
               button.dataset.image || 
               document.querySelector('meta[property="og:image"]')?.content || 
               '';
    }
    
    getShareHashtags(button) {
        return button.dataset.shareHashtags || 
               button.dataset.hashtags || 
               'fashion,shopping';
    }
    
    // =====================================
    // TRACKING & ANALYTICS
    // =====================================
    
    trackShare(platform, button) {
        // Track with analytics
        if (window.analytics) {
            window.analytics.trackInteraction('social_share', {
                platform: platform,
                url: this.getShareUrl(button),
                content_type: this.getContentType(),
                content_id: this.getContentId()
            });
        }
        
        // Send to backend
        fetch('/api/social/track-share', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken()
            },
            body: JSON.stringify({
                platform: platform,
                url: this.getShareUrl(button),
                content_type: this.getContentType(),
                content_id: this.getContentId()
            })
        }).catch(error => {
            console.warn('Share tracking failed:', error);
        });
    }
    
    getContentType() {
        const path = window.location.pathname;
        if (path.includes('/products/')) return 'product';
        if (path.includes('/blog/')) return 'blog';
        if (path.includes('/categories/')) return 'category';
        return 'page';
    }
    
    getContentId() {
        const matches = window.location.pathname.match(/\/(\w+)\/(\d+)/);
        return matches ? matches[2] : null;
    }
    
    // =====================================
    // UTILITY METHODS
    // =====================================
    
    openSharePopup(url, title) {
        const width = 600;
        const height = 400;
        const left = (window.innerWidth - width) / 2;
        const top = (window.innerHeight - height) / 2;
        
        window.open(
            url,
            title,
            `width=${width},height=${height},left=${left},top=${top},toolbar=0,status=0,resizable=1`
        );
    }
    
    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }
    
    showLoading(button) {
        const originalText = button.textContent;
        button.textContent = 'Đang xử lý...';
        button.disabled = true;
        
        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
        }, 5000);
    }
    
    showSuccess(message) {
        this.showNotification(message, 'success');
    }
    
    showError(message) {
        this.showNotification(message, 'error');
    }
    
    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.social-notification').forEach(el => el.remove());
        
        const notification = document.createElement('div');
        notification.className = `social-notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button class="notification-close">×</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.socialIntegration = new SocialIntegration();
});

// Export for external use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SocialIntegration;
}
