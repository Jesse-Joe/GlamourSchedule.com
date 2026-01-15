/**
 * GlamourSchedule - Language Manager
 * 
 * Handles:
 * - Language detection from IP
 * - Language selection and persistence
 * - Dynamic content translation
 */

class LanguageManager {
    constructor() {
        this.STORAGE_KEY = 'glamour_language';
        this.SUPPORTED_LANGUAGES = ['nl', 'en', 'de', 'fr'];
        this.DEFAULT_LANGUAGE = 'nl';
        
        this.translations = {};
        this.currentLanguage = null;
        
        this.init();
    }
    
    async init() {
        // Check for saved preference
        let language = localStorage.getItem(this.STORAGE_KEY);
        
        if (!language) {
            // Try to detect from IP
            language = await this.detectLanguageFromIP();
        }
        
        if (!language) {
            // Try to detect from browser
            language = this.detectLanguageFromBrowser();
        }
        
        // Fallback to default
        if (!language || !this.SUPPORTED_LANGUAGES.includes(language)) {
            language = this.DEFAULT_LANGUAGE;
        }
        
        await this.setLanguage(language, false);
        this.setupEventListeners();
    }
    
    async detectLanguageFromIP() {
        try {
            const response = await fetch('http://ip-api.com/json/?fields=countryCode');
            const data = await response.json();
            
            const countryToLanguage = {
                'NL': 'nl',
                'BE': 'nl',
                'DE': 'de',
                'AT': 'de',
                'CH': 'de',
                'FR': 'fr',
                'GB': 'en',
                'US': 'en',
                'CA': 'en',
                'AU': 'en'
            };
            
            return countryToLanguage[data.countryCode] || null;
        } catch (error) {
            console.warn('Could not detect language from IP:', error);
            return null;
        }
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
    
    async setLanguage(language, save = true) {
        if (!this.SUPPORTED_LANGUAGES.includes(language)) {
            console.warn(`Language ${language} not supported`);
            return;
        }
        
        this.currentLanguage = language;
        document.documentElement.setAttribute('lang', language);
        
        if (save) {
            localStorage.setItem(this.STORAGE_KEY, language);
        }
        
        // Load translations
        await this.loadTranslations(language);
        
        // Update UI
        this.updateLanguageUI();
        
        // Translate page
        this.translatePage();
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('languageChanged', { 
            detail: { language } 
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
        // Language selector options
        document.querySelectorAll('.language-selector__option').forEach(option => {
            option.addEventListener('click', () => {
                this.setLanguage(option.dataset.lang);
            });
        });
    }
}

// Initialize language manager
const languageManager = new LanguageManager();

// Export for use in other scripts
window.GlamourLang = languageManager;

// Shorthand translation function
window.__ = (key, replacements) => languageManager.t(key, replacements);
