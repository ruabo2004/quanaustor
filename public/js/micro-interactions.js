/**
 * MICRO-INTERACTIONS FRAMEWORK
 * Smooth animations and interactive elements
 */

class MicroInteractions {
    constructor() {
        this.init();
    }

    init() {
        this.setupScrollAnimations();
        this.setupButtonEffects();
        this.setupCardHovers();
        this.setupToastSystem();
        this.setupLazyLoading();
        this.setupSmoothScrolling();
        this.setupFormEnhancements();
        this.setupLoadingStates();
        this.setupImageZoom();
        this.setupParallaxEffects();
    }

    // Scroll-triggered animations
    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateElement(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements with animation classes
        document.querySelectorAll('[class*="animate-"], .product-card-modern, .category-card-modern, .feature-card-modern').forEach(el => {
            observer.observe(el);
        });
    }

    animateElement(element) {
        const animationClass = this.getAnimationClass(element);
        element.style.opacity = '1';
        element.style.transform = 'translateY(0) scale(1)';
        element.classList.add('animate-in');
        
        // Add a subtle entrance effect based on element type
        if (element.classList.contains('product-card-modern')) {
            element.style.animation = 'fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards';
        } else if (element.classList.contains('category-card-modern')) {
            element.style.animation = 'fadeInScale 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards';
        }
    }

    getAnimationClass(element) {
        const classes = element.className.split(' ');
        return classes.find(cls => cls.startsWith('animate-')) || 'animate-fadeInUp';
    }

    // Enhanced button effects
    setupButtonEffects() {
        document.querySelectorAll('.btn-modern, .quick-action-btn-modern').forEach(btn => {
            // Ripple effect
            btn.addEventListener('click', (e) => {
                this.createRipple(e, btn);
            });

            // Magnetic effect on hover
            btn.addEventListener('mousemove', (e) => {
                this.magneticEffect(e, btn);
            });

            btn.addEventListener('mouseleave', () => {
                btn.style.transform = '';
            });
        });
    }

    createRipple(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    magneticEffect(event, element) {
        const rect = element.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const deltaX = (event.clientX - centerX) * 0.15;
        const deltaY = (event.clientY - centerY) * 0.15;

        element.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
    }

    // Enhanced card hover effects
    setupCardHovers() {
        document.querySelectorAll('.product-card-modern, .category-card-modern').forEach(card => {
            card.addEventListener('mouseenter', () => {
                this.enhancedCardHover(card, true);
            });

            card.addEventListener('mouseleave', () => {
                this.enhancedCardHover(card, false);
            });
        });
    }

    enhancedCardHover(card, isHovering) {
        const img = card.querySelector('img');
        const overlay = card.querySelector('.quick-actions-overlay');
        
        if (isHovering) {
            card.style.transform = 'translateY(-8px) scale(1.02)';
            card.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
            card.style.zIndex = '10';
            
            if (img) {
                img.style.transform = 'scale(1.1)';
                img.style.filter = 'brightness(1.1)';
            }
            
            if (overlay) {
                overlay.style.opacity = '1';
            }
        } else {
            card.style.transform = '';
            card.style.boxShadow = '';
            card.style.zIndex = '';
            
            if (img) {
                img.style.transform = '';
                img.style.filter = '';
            }
            
            if (overlay) {
                overlay.style.opacity = '';
            }
        }
    }

    // Modern toast notification system
    setupToastSystem() {
        window.showToast = (type, message, duration = 4000) => {
            this.showToast(type, message, duration);
        };
    }

    showToast(type, message, duration = 4000) {
        // Remove existing toasts
        document.querySelectorAll('.toast-modern').forEach(toast => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        });

        const toast = document.createElement('div');
        toast.className = `toast-modern ${type}`;
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            warning: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };

        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas ${icons[type] || icons.info}" style="color: var(--${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'});"></i>
                <span style="flex: 1; font-weight: 500;">${message}</span>
                <button type="button" style="background: none; border: none; color: var(--gray-400); padding: 4px; cursor: pointer;" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="toast-progress" style="
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                background: var(--${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'});
                width: 100%;
                transform-origin: left;
                animation: toastProgress ${duration}ms linear forwards;
            "></div>
        `;

        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Auto remove
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }

    // Lazy loading for images
    setupLazyLoading() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Smooth scrolling for anchor links
    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 100; // Account for fixed header
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // Form enhancements
    setupFormEnhancements() {
        // Floating labels
        document.querySelectorAll('input, textarea, select').forEach(input => {
            if (input.placeholder && !input.closest('.search-box-modern')) {
                this.createFloatingLabel(input);
            }

            // Input focus effects
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentElement.classList.remove('focused');
                }
            });
        });
    }

    createFloatingLabel(input) {
        const wrapper = document.createElement('div');
        wrapper.className = 'floating-input-wrapper';
        wrapper.style.cssText = 'position: relative;';
        
        const label = document.createElement('label');
        label.textContent = input.placeholder;
        label.style.cssText = `
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            transition: all 0.3s ease;
            color: var(--gray-400);
            font-size: 0.875rem;
        `;

        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(label);

        input.addEventListener('focus', () => {
            label.style.top = '8px';
            label.style.fontSize = '0.75rem';
            label.style.color = 'var(--primary)';
        });

        input.addEventListener('blur', () => {
            if (!input.value) {
                label.style.top = '50%';
                label.style.fontSize = '0.875rem';
                label.style.color = 'var(--gray-400)';
            }
        });
    }

    // Loading states for buttons and forms
    setupLoadingStates() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    this.setLoadingState(submitBtn, true);
                }
            });
        });

        // AJAX form handling
        document.querySelectorAll('[data-ajax-form]').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleAjaxForm(form);
            });
        });
    }

    setLoadingState(button, isLoading) {
        if (isLoading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            button.disabled = true;
        } else {
            button.innerHTML = button.dataset.originalText || button.innerHTML;
            button.disabled = false;
        }
    }

    // Image zoom on hover
    setupImageZoom() {
        document.querySelectorAll('.product-image img').forEach(img => {
            img.addEventListener('mousemove', (e) => {
                const rect = img.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                img.style.transformOrigin = `${x}% ${y}%`;
            });
        });
    }

    // Parallax effects
    setupParallaxEffects() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length > 0) {
            window.addEventListener('scroll', () => {
                requestAnimationFrame(() => {
                    const scrolled = window.pageYOffset;
                    
                    parallaxElements.forEach(element => {
                        const speed = element.dataset.parallax || 0.5;
                        const yPos = -(scrolled * speed);
                        element.style.transform = `translateY(${yPos}px)`;
                    });
                });
            });
        }
    }
}

// CSS Animations
const css = `
@keyframes ripple {
    to {
        transform: scale(2);
        opacity: 0;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes toastProgress {
    from {
        transform: scaleX(1);
    }
    to {
        transform: scaleX(0);
    }
}

.loaded {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.floating-input-wrapper input:focus + label,
.floating-input-wrapper input:not(:placeholder-shown) + label {
    top: 8px !important;
    font-size: 0.75rem !important;
    color: var(--primary) !important;
}
`;

// Inject CSS
const style = document.createElement('style');
style.textContent = css;
document.head.appendChild(style);

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => new MicroInteractions());
} else {
    new MicroInteractions();
}
