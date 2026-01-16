/**
 * GlamourSchedule - Language Manager
 *
 * Handles:
 * - Domain-aware language detection (.com = English, .nl = IP-based)
 * - Language selection and persistence
 * - Dynamic content translation
 */

class LanguageManager {
    constructor() {
        this.STORAGE_KEY = 'glamour_language';
        this.STORAGE_KEY_USER_CHOSEN = 'glamour_language_user_chosen';
        this.SUPPORTED_LANGUAGES = ['nl', 'en', 'de', 'fr'];

        // Domain-based default: .com = English (international), .nl = Dutch
        this.currentDomain = this.detectDomain();
        this.DEFAULT_LANGUAGE = this.currentDomain === 'com' ? 'en' : 'nl';

        this.translations = {};
        this.currentLanguage = null;
        this.detectedCountry = null;

        this.init();
    }

    /**
     * Detect current domain (.nl, .com, or localhost)
     */
    detectDomain() {
        const host = window.location.hostname;
        if (host.includes('glamourschedule.com')) return 'com';
        if (host.includes('glamourschedule.nl')) return 'nl';
        return 'localhost';
    }

    /**
     * Check if we're on the international .com domain
     */
    isComDomain() {
        return this.currentDomain === 'com';
    }

    /**
     * Check if we're on the Dutch .nl domain
     */
    isNlDomain() {
        return this.currentDomain === 'nl';
    }

    async init() {
        // Check for explicit user choice first
        let language = localStorage.getItem(this.STORAGE_KEY);
        const userChosen = localStorage.getItem(this.STORAGE_KEY_USER_CHOSEN) === 'true';

        if (language && userChosen) {
            // User made an explicit choice - respect it
            await this.setLanguage(language, false);
            this.setupEventListeners();
            return;
        }

        // For .com domain, always default to English unless user chose otherwise
        if (this.isComDomain() && !userChosen) {
            language = 'en';
        } else if (!language) {
            // For .nl or if no preference, use IP detection
            const ipData = await this.detectCountryFromIP();
            this.detectedCountry = ipData.country;
            language = ipData.lang;
        }

        if (!language) {
            // Try to detect from browser
            language = this.detectLanguageFromBrowser();
        }

        // Fallback to domain-based default
        if (!language || !this.SUPPORTED_LANGUAGES.includes(language)) {
            language = this.DEFAULT_LANGUAGE;
        }

        await this.setLanguage(language, false);
        this.setupEventListeners();
    }

    /**
     * Detect country and suggested language from IP
     */
    async detectCountryFromIP() {
        try {
            const response = await fetch('http://ip-api.com/json/?fields=countryCode,country');
            const data = await response.json();

            const countryToLanguage = {
                'NL': 'nl',
                'BE': 'nl',
                'DE': 'de',
                'AT': 'de',
                'CH': 'de',
                'FR': 'fr',
                'LU': 'fr',
                'GB': 'en',
                'US': 'en',
                'CA': 'en',
                'AU': 'en',
                'IE': 'en'
            };

            return {
                country: data.countryCode || null,
                countryName: data.country || null,
                lang: countryToLanguage[data.countryCode] || 'en'
            };
        } catch (error) {
            console.warn('Could not detect country from IP:', error);
            return { country: null, countryName: null, lang: null };
        }
    }

    /**
     * Legacy method for backwards compatibility
     */
    async detectLanguageFromIP() {
        const data = await this.detectCountryFromIP();
        return data.lang;
    }
    
    detectLanguageFromBrowser() {
        const browserLang = navigator.language || navigator.userLanguage;
        const shortLang = browserLang.split('-')[0].toLowerCase();
        
        if (this.SUPPORTED_LANGUAGES.includes(shortLang)) {
            return shortLang;
        }
        
        return null;
    }
    
    async loadTranslations(language) {
        try {
            const response = await fetch(`/api/translations/${language}`);
            if (response.ok) {
                this.translations = await response.json();
            }
        } catch (error) {
            console.warn('Could not load translations:', error);
        }
    }
    
    async setLanguage(language, save = true, userChosen = false) {
        if (!this.SUPPORTED_LANGUAGES.includes(language)) {
            console.warn(`Language ${language} not supported`);
            return;
        }

        this.currentLanguage = language;
        document.documentElement.setAttribute('lang', language);

        if (save) {
            localStorage.setItem(this.STORAGE_KEY, language);
            if (userChosen) {
                // Mark that user explicitly chose this language
                localStorage.setItem(this.STORAGE_KEY_USER_CHOSEN, 'true');
            }
        }

        // Load translations
        await this.loadTranslations(language);

        // Update UI
        this.updateLanguageUI();

        // Translate page
        this.translatePage();

        // Dispatch event
        window.dispatchEvent(new CustomEvent('languageChanged', {
            detail: { language, domain: this.currentDomain, userChosen }
        }));
    }
    
    updateLanguageUI() {
        const flags = {
            'nl': '🇳🇱',
            'en': '🇬🇧',
            'de': '🇩🇪',
            'fr': '🇫🇷'
        };
        
        const names = {
            'nl': 'Nederlands',
            'en': 'English',
            'de': 'Deutsch',
            'fr': 'Français'
        };
        
        // Update current language display
        document.querySelectorAll('.language-selector__current').forEach(el => {
            el.innerHTML = `
                <span class="flag">${flags[this.currentLanguage]}</span>
                <span class="name">${names[this.currentLanguage]}</span>
                <span class="arrow">▼</span>
            `;
        });
        
        // Update dropdown options
        document.querySelectorAll('.language-selector__option').forEach(el => {
            el.classList.toggle('active', el.dataset.lang === this.currentLanguage);
        });
    }
    
    translatePage() {
        // Translate all elements with data-i18n attribute
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.dataset.i18n;
            const translation = this.getTranslation(key);
            
            if (translation) {
                if (el.tagName === 'INPUT' && el.placeholder) {
                    el.placeholder = translation;
                } else {
                    el.textContent = translation;
                }
            }
        });
        
        // Translate elements with data-i18n-html for HTML content
        document.querySelectorAll('[data-i18n-html]').forEach(el => {
            const key = el.dataset.i18nHtml;
            const translation = this.getTranslation(key);
            
            if (translation) {
                el.innerHTML = translation;
            }
        });
    }
    
    getTranslation(key) {
        // Support nested keys like "booking.title"
        const keys = key.split('.');
        let value = this.translations;
        
        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return null;
            }
        }
        
        return typeof value === 'string' ? value : null;
    }
    
    t(key, replacements = {}) {
        let translation = this.getTranslation(key) || key;
        
        // Replace placeholders like {name}
        for (const [placeholder, value] of Object.entries(replacements)) {
            translation = translation.replace(new RegExp(`{${placeholder}}`, 'g'), value);
        }
        
        return translation;
    }
    
    setupEventListeners() {
        // Language selector options - mark as explicit user choice
        document.querySelectorAll('.language-selector__option').forEach(option => {
            option.addEventListener('click', () => {
                this.setLanguage(option.dataset.lang, true, true);
            });
        });
    }

    /**
     * Get URL to switch to a different domain
     */
    getSwitchDomainUrl(targetDomain) {
        const protocol = window.location.protocol;
        const path = window.location.pathname + window.location.search;
        const baseDomain = targetDomain === 'nl' ? 'glamourschedule.nl' : 'glamourschedule.com';
        return `${protocol}//${baseDomain}${path}`;
    }

    /**
     * Get detected country code
     */
    getDetectedCountry() {
        return this.detectedCountry;
    }

    /**
     * Get current domain
     */
    getCurrentDomain() {
        return this.currentDomain;
    }
}

// Initialize language manager
const languageManager = new LanguageManager();

// Export for use in other scripts
window.GlamourLang = languageManager;

// Shorthand translation function
window.__ = (key, replacements) => languageManager.t(key, replacements);
