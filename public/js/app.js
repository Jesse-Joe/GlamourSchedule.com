/**
 * GlamourSchedule - Modern JavaScript Application
 * Version: 2.1
 * Features: Smooth animations, Glassmorphism effects, Interactive UI
 */

// ========================================
// UTILITY FUNCTIONS
// ========================================

const Utils = {
    // Debounce function for performance
    debounce(func, wait = 100) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function
    throttle(func, limit = 100) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Smooth scroll to element
    scrollTo(element, offset = 0) {
        const target = typeof element === 'string' ? document.querySelector(element) : element;
        if (target) {
            const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    },

    // Format currency
    formatCurrency(amount, currency = 'EUR') {
        return new Intl.NumberFormat('nl-NL', {
            style: 'currency',
            currency
        }).format(amount);
    },

    // Format date
    formatDate(date, options = {}) {
        const defaultOptions = { day: 'numeric', month: 'long', year: 'numeric' };
        return new Intl.DateTimeFormat('nl-NL', { ...defaultOptions, ...options }).format(new Date(date));
    },

    // Generate unique ID
    uniqueId(prefix = 'id') {
        return `${prefix}_${Math.random().toString(36).substr(2, 9)}`;
    },

    // Check if element is in viewport
    isInViewport(element, threshold = 0) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) - threshold &&
            rect.bottom >= threshold
        );
    },

    // Animate counter
    animateCounter(element, target, duration = 2000) {
        const start = 0;
        const startTime = performance.now();

        const updateCounter = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(start + (target - start) * easeOut);

            element.textContent = current.toLocaleString('nl-NL');

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        };

        requestAnimationFrame(updateCounter);
    }
};

// ========================================
// NAVBAR CONTROLLER
// ========================================

class NavbarController {
    constructor() {
        this.navbar = document.querySelector('.navbar');
        this.menuToggle = document.querySelector('.menu-toggle');
        this.mobileMenu = document.querySelector('.mobile-menu');
        this.isScrolled = false;
        this.lastScrollY = 0;

        this.init();
    }

    init() {
        if (!this.navbar) return;

        // Scroll handler
        window.addEventListener('scroll', Utils.throttle(() => this.handleScroll(), 50));

        // Mobile menu toggle
        if (this.menuToggle) {
            this.menuToggle.addEventListener('click', () => this.toggleMobileMenu());
        }

        // Close mobile menu on link click
        document.querySelectorAll('.navbar-menu a, .mobile-menu a').forEach(link => {
            link.addEventListener('click', () => this.closeMobileMenu());
        });

        // Initial check
        this.handleScroll();
    }

    handleScroll() {
        const scrollY = window.scrollY;

        // Add/remove scrolled class
        if (scrollY > 50 && !this.isScrolled) {
            this.navbar.classList.add('scrolled');
            this.isScrolled = true;
        } else if (scrollY <= 50 && this.isScrolled) {
            this.navbar.classList.remove('scrolled');
            this.isScrolled = false;
        }

        // Hide/show navbar on scroll direction
        if (scrollY > this.lastScrollY && scrollY > 200) {
            this.navbar.style.transform = 'translateY(-100%)';
        } else {
            this.navbar.style.transform = 'translateY(0)';
        }

        this.lastScrollY = scrollY;
    }

    toggleMobileMenu() {
        this.mobileMenu?.classList.toggle('active');
        this.menuToggle?.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    }

    closeMobileMenu() {
        this.mobileMenu?.classList.remove('active');
        this.menuToggle?.classList.remove('active');
        document.body.classList.remove('menu-open');
    }
}

// ========================================
// SCROLL ANIMATIONS
// ========================================

class ScrollAnimations {
    constructor() {
        this.animatedElements = document.querySelectorAll('[data-animate]');
        this.counters = document.querySelectorAll('[data-counter]');
        this.staggerContainers = document.querySelectorAll('.stagger-children');

        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            // Fallback: show all elements
            this.animatedElements.forEach(el => el.classList.add('animate'));
        }
    }

    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '0px 0px -100px 0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const delay = el.dataset.delay || 0;

                    setTimeout(() => {
                        el.classList.add('animate');

                        // Handle counters
                        if (el.dataset.counter) {
                            Utils.animateCounter(el, parseInt(el.dataset.counter));
                        }
                    }, delay);

                    observer.unobserve(el);
                }
            });
        }, options);

        // Observe animated elements
        this.animatedElements.forEach(el => observer.observe(el));

        // Observe counters
        this.counters.forEach(el => observer.observe(el));

        // Observe stagger containers
        this.staggerContainers.forEach(el => observer.observe(el));
    }
}

// ========================================
// MAGNETIC BUTTON EFFECT
// ========================================

class MagneticButtons {
    constructor() {
        this.buttons = document.querySelectorAll('.magnetic');
        this.init();
    }

    init() {
        this.buttons.forEach(button => {
            button.addEventListener('mousemove', (e) => this.handleMouseMove(e, button));
            button.addEventListener('mouseleave', (e) => this.handleMouseLeave(e, button));
        });
    }

    handleMouseMove(e, button) {
        const rect = button.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;

        button.style.transform = `translate(${x * 0.2}px, ${y * 0.2}px)`;
    }

    handleMouseLeave(e, button) {
        button.style.transform = 'translate(0, 0)';
    }
}

// ========================================
// PARALLAX EFFECT
// ========================================

class ParallaxEffect {
    constructor() {
        this.elements = document.querySelectorAll('[data-parallax]');
        this.init();
    }

    init() {
        if (this.elements.length === 0) return;

        window.addEventListener('scroll', Utils.throttle(() => this.update(), 16));
        this.update();
    }

    update() {
        const scrollY = window.scrollY;

        this.elements.forEach(el => {
            const speed = parseFloat(el.dataset.parallax) || 0.5;
            const offset = scrollY * speed;
            el.style.transform = `translateY(${offset}px)`;
        });
    }
}

// ========================================
// SEARCH FUNCTIONALITY
// ========================================

class SearchController {
    constructor() {
        this.form = document.querySelector('.search-form');
        this.searchInput = document.querySelector('#search-query');
        this.locationInput = document.querySelector('#search-location');
        this.categorySelect = document.querySelector('#search-category');
        this.suggestions = document.querySelector('.search-suggestions');

        this.init();
    }

    init() {
        if (!this.form) return;

        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Live search suggestions
        if (this.searchInput) {
            this.searchInput.addEventListener('input', Utils.debounce((e) => {
                this.handleSearchInput(e.target.value);
            }, 300));
        }

        // Location autocomplete (could integrate with Google Places API)
        if (this.locationInput) {
            this.locationInput.addEventListener('focus', () => this.showLocationSuggestions());
        }
    }

    handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        const params = new URLSearchParams();

        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        window.location.href = `/search?${params.toString()}`;
    }

    async handleSearchInput(query) {
        if (query.length < 2) {
            this.hideSuggestions();
            return;
        }

        try {
            // Simulated API call - replace with actual endpoint
            const suggestions = await this.fetchSuggestions(query);
            this.showSuggestions(suggestions);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    async fetchSuggestions(query) {
        // Replace with actual API call
        return [
            { type: 'business', name: 'Beauty Salon Amsterdam', slug: 'beauty-salon-amsterdam' },
            { type: 'service', name: 'Manicure', category: 'nails' },
            { type: 'category', name: 'Haar & Styling', slug: 'hair' }
        ];
    }

    showSuggestions(suggestions) {
        if (!this.suggestions) return;

        this.suggestions.innerHTML = suggestions.map(s => `
            <div class="suggestion-item" data-type="${s.type}" data-value="${s.slug || s.name}">
                <span class="suggestion-icon">${this.getIcon(s.type)}</span>
                <span class="suggestion-text">${s.name}</span>
                <span class="suggestion-type">${s.type}</span>
            </div>
        `).join('');

        this.suggestions.classList.add('active');
    }

    hideSuggestions() {
        if (this.suggestions) {
            this.suggestions.classList.remove('active');
        }
    }

    showLocationSuggestions() {
        // Implement location suggestions
    }

    getIcon(type) {
        const icons = {
            business: '🏪',
            service: '✨',
            category: '📁'
        };
        return icons[type] || '🔍';
    }
}

// ========================================
// BOOKING MODAL
// ========================================

class BookingModal {
    constructor() {
        this.modal = document.querySelector('#booking-modal');
        this.triggers = document.querySelectorAll('[data-booking]');
        this.closeBtn = document.querySelector('.modal-close');

        this.init();
    }

    init() {
        // Open modal triggers
        this.triggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const businessSlug = trigger.dataset.booking;
                this.open(businessSlug);
            });
        });

        // Close button
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        // Close on backdrop click
        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) this.close();
            });
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.close();
        });
    }

    async open(businessSlug) {
        if (!this.modal) return;

        // Load business data
        await this.loadBusinessData(businessSlug);

        // Show modal
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus first input
        setTimeout(() => {
            this.modal.querySelector('input, select')?.focus();
        }, 300);
    }

    close() {
        if (!this.modal) return;

        this.modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    async loadBusinessData(slug) {
        try {
            const response = await fetch(`/api/services/${slug}`);
            const data = await response.json();
            this.renderServices(data.services);
        } catch (error) {
            console.error('Failed to load business data:', error);
        }
    }

    renderServices(services) {
        const container = this.modal?.querySelector('.services-list');
        if (!container) return;

        container.innerHTML = services.map(service => `
            <div class="service-option" data-service-id="${service.id}">
                <div class="service-option-info">
                    <h4>${service.name}</h4>
                    <p>${service.duration} min</p>
                </div>
                <div class="service-option-price">${Utils.formatCurrency(service.price)}</div>
            </div>
        `).join('');
    }
}

// ========================================
// TOAST NOTIFICATIONS
// ========================================

class Toast {
    static container = null;

    static init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    }

    static show(message, type = 'info', duration = 4000) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <span class="toast-message">${message}</span>
            <button class="toast-close">&times;</button>
        `;

        // Close button handler
        toast.querySelector('.toast-close').addEventListener('click', () => {
            this.hide(toast);
        });

        this.container.appendChild(toast);

        // Auto hide
        setTimeout(() => this.hide(toast), duration);

        return toast;
    }

    static hide(toast) {
        toast.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }

    static success(message, duration) {
        return this.show(message, 'success', duration);
    }

    static error(message, duration) {
        return this.show(message, 'error', duration);
    }

    static info(message, duration) {
        return this.show(message, 'info', duration);
    }
}

// ========================================
// THEME MANAGER
// ========================================

class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'dark';
        this.toggleBtn = document.querySelector('[data-theme-toggle]');

        this.init();
    }

    init() {
        // Apply saved theme
        this.applyTheme(this.theme);

        // Toggle button
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', () => this.toggle());
        }

        // System preference listener
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                this.applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    toggle() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        this.applyTheme(this.theme);
        localStorage.setItem('theme', this.theme);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        this.theme = theme;
    }
}

// ========================================
// LANGUAGE SWITCHER
// ========================================

class LanguageSwitcher {
    constructor() {
        this.currentLang = document.documentElement.lang || 'nl';
        this.switcher = document.querySelector('.language-switcher');

        this.init();
    }

    init() {
        if (!this.switcher) return;

        this.switcher.querySelectorAll('[data-lang]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchLanguage(btn.dataset.lang);
            });
        });
    }

    switchLanguage(lang) {
        // Set cookie
        document.cookie = `lang=${lang};path=/;max-age=31536000`;

        // Update URL or reload
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    }
}

// ========================================
// SMOOTH SCROLL LINKS
// ========================================

class SmoothScroll {
    constructor() {
        this.links = document.querySelectorAll('a[href^="#"]');
        this.init();
    }

    init() {
        this.links.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href === '#') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    Utils.scrollTo(target, 100);
                }
            });
        });
    }
}

// ========================================
// IMAGE LAZY LOADING
// ========================================

class LazyLoader {
    constructor() {
        this.images = document.querySelectorAll('img[data-src]');
        this.init();
    }

    init() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadImage(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { rootMargin: '50px' });

            this.images.forEach(img => observer.observe(img));
        } else {
            // Fallback: load all images
            this.images.forEach(img => this.loadImage(img));
        }
    }

    loadImage(img) {
        const src = img.dataset.src;
        if (src) {
            img.src = src;
            img.removeAttribute('data-src');
            img.classList.add('loaded');
        }
    }
}

// ========================================
// FORM VALIDATION
// ========================================

class FormValidator {
    constructor(form) {
        this.form = form;
        this.inputs = form.querySelectorAll('input, select, textarea');
        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        this.inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearError(input));
        });
    }

    handleSubmit(e) {
        let isValid = true;

        this.inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            Toast.error('Controleer de gemarkeerde velden');
        }
    }

    validateField(input) {
        const value = input.value.trim();
        const rules = input.dataset.validate?.split('|') || [];

        // Required check
        if (input.required && !value) {
            this.showError(input, 'Dit veld is verplicht');
            return false;
        }

        // Email validation
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showError(input, 'Voer een geldig e-mailadres in');
                return false;
            }
        }

        // Phone validation
        if (input.type === 'tel' && value) {
            const phoneRegex = /^[\d\s\-+()]{10,}$/;
            if (!phoneRegex.test(value)) {
                this.showError(input, 'Voer een geldig telefoonnummer in');
                return false;
            }
        }

        // Min length
        if (input.minLength && value.length < input.minLength) {
            this.showError(input, `Minimaal ${input.minLength} tekens vereist`);
            return false;
        }

        this.clearError(input);
        return true;
    }

    showError(input, message) {
        this.clearError(input);

        input.classList.add('error');

        const errorEl = document.createElement('span');
        errorEl.className = 'form-error';
        errorEl.textContent = message;

        input.parentNode.appendChild(errorEl);
    }

    clearError(input) {
        input.classList.remove('error');
        const error = input.parentNode.querySelector('.form-error');
        if (error) error.remove();
    }
}

// ========================================
// CAROUSEL / SLIDER
// ========================================

class Carousel {
    constructor(element) {
        this.carousel = element;
        this.track = element.querySelector('.carousel-track');
        this.slides = element.querySelectorAll('.carousel-slide');
        this.prevBtn = element.querySelector('.carousel-prev');
        this.nextBtn = element.querySelector('.carousel-next');
        this.dots = element.querySelector('.carousel-dots');

        this.currentIndex = 0;
        this.autoplayInterval = null;

        this.init();
    }

    init() {
        if (this.slides.length === 0) return;

        // Create dots
        this.createDots();

        // Button handlers
        this.prevBtn?.addEventListener('click', () => this.prev());
        this.nextBtn?.addEventListener('click', () => this.next());

        // Touch support
        this.setupTouch();

        // Autoplay
        if (this.carousel.dataset.autoplay) {
            this.startAutoplay(parseInt(this.carousel.dataset.autoplay) || 5000);
        }

        // Initial position
        this.goTo(0);
    }

    createDots() {
        if (!this.dots) return;

        this.slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot';
            dot.addEventListener('click', () => this.goTo(i));
            this.dots.appendChild(dot);
        });

        this.updateDots();
    }

    goTo(index) {
        this.currentIndex = Math.max(0, Math.min(index, this.slides.length - 1));

        const offset = -this.currentIndex * 100;
        this.track.style.transform = `translateX(${offset}%)`;

        this.updateDots();
    }

    prev() {
        this.goTo(this.currentIndex - 1);
    }

    next() {
        if (this.currentIndex >= this.slides.length - 1) {
            this.goTo(0);
        } else {
            this.goTo(this.currentIndex + 1);
        }
    }

    updateDots() {
        const dots = this.dots?.querySelectorAll('.carousel-dot');
        dots?.forEach((dot, i) => {
            dot.classList.toggle('active', i === this.currentIndex);
        });
    }

    setupTouch() {
        let startX = 0;
        let isDragging = false;

        this.track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
        });

        this.track.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            const diff = startX - e.touches[0].clientX;
            if (Math.abs(diff) > 50) {
                isDragging = false;
                diff > 0 ? this.next() : this.prev();
            }
        });

        this.track.addEventListener('touchend', () => {
            isDragging = false;
        });
    }

    startAutoplay(interval) {
        this.stopAutoplay();
        this.autoplayInterval = setInterval(() => this.next(), interval);

        // Pause on hover
        this.carousel.addEventListener('mouseenter', () => this.stopAutoplay());
        this.carousel.addEventListener('mouseleave', () => this.startAutoplay(interval));
    }

    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
}

// ========================================
// ACCORDION
// ========================================

class Accordion {
    constructor(element) {
        this.accordion = element;
        this.items = element.querySelectorAll('.accordion-item');
        this.allowMultiple = element.dataset.multiple === 'true';

        this.init();
    }

    init() {
        this.items.forEach(item => {
            const header = item.querySelector('.accordion-header');
            header?.addEventListener('click', () => this.toggle(item));
        });
    }

    toggle(item) {
        const isOpen = item.classList.contains('active');

        if (!this.allowMultiple) {
            this.items.forEach(i => i.classList.remove('active'));
        }

        item.classList.toggle('active', !isOpen);
    }
}

// ========================================
// TABS
// ========================================

class Tabs {
    constructor(element) {
        this.tabs = element;
        this.buttons = element.querySelectorAll('.tab-button');
        this.panels = element.querySelectorAll('.tab-panel');

        this.init();
    }

    init() {
        this.buttons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.tab;
                this.activate(targetId);
            });
        });
    }

    activate(tabId) {
        // Update buttons
        this.buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabId);
        });

        // Update panels
        this.panels.forEach(panel => {
            panel.classList.toggle('active', panel.id === tabId);
        });
    }
}

// ========================================
// INITIALIZE APPLICATION
// ========================================

document.addEventListener('DOMContentLoaded', () => {
    // Core controllers
    new NavbarController();
    new ScrollAnimations();
    new MagneticButtons();
    new ParallaxEffect();
    new SmoothScroll();
    new LazyLoader();
    new ThemeManager();
    new LanguageSwitcher();

    // Page-specific controllers
    new SearchController();
    new BookingModal();

    // Initialize carousels
    document.querySelectorAll('.carousel').forEach(el => new Carousel(el));

    // Initialize accordions
    document.querySelectorAll('.accordion').forEach(el => new Accordion(el));

    // Initialize tabs
    document.querySelectorAll('.tabs').forEach(el => new Tabs(el));

    // Initialize form validators
    document.querySelectorAll('form[data-validate]').forEach(form => new FormValidator(form));

    // Page load animation
    document.body.classList.add('loaded');

    console.log('✨ GlamourSchedule 2.0 initialized');
});

// ========================================
// EXPORTS
// ========================================

window.GlamourSchedule = {
    Utils,
    Toast,
    FormValidator,
    Carousel,
    Accordion,
    Tabs
};
