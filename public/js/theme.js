/**
 * GlamourSchedule - Theme Manager
 * 
 * Handles:
 * - Light/Dark mode toggle
 * - Male/Female theme selection
 * - Theme persistence in localStorage
 * - System preference detection
 */

class ThemeManager {
    constructor() {
        this.STORAGE_KEY_MODE = 'glamour_theme_mode';
        this.STORAGE_KEY_GENDER = 'glamour_theme_gender';
        
        this.init();
    }
    
    init() {
        // Load saved preferences or detect from system
        const savedMode = localStorage.getItem(this.STORAGE_KEY_MODE);
        const savedGender = localStorage.getItem(this.STORAGE_KEY_GENDER);
        
        // Set initial theme
        if (savedMode) {
            this.setMode(savedMode, false);
        } else {
            // Detect system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.setMode(prefersDark ? 'dark' : 'light', false);
        }
        
        // Set initial gender theme
        this.setGender(savedGender || 'female', false);
        
        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem(this.STORAGE_KEY_MODE)) {
                this.setMode(e.matches ? 'dark' : 'light', false);
            }
        });
        
        // Setup event listeners
        this.setupEventListeners();
    }
    
    setMode(mode, save = true) {
        document.documentElement.setAttribute('data-theme', mode);
        
        if (save) {
            localStorage.setItem(this.STORAGE_KEY_MODE, mode);
        }
        
        // Update toggle UI
        const darkModeToggle = document.querySelector('.theme-switch input');
        if (darkModeToggle) {
            darkModeToggle.checked = mode === 'dark';
        }
        
        // Update any mode buttons
        document.querySelectorAll('[data-mode]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { mode, gender: this.getGender() } 
        }));
    }
    
    setGender(gender, save = true) {
        document.documentElement.setAttribute('data-gender', gender);
        
        if (save) {
            localStorage.setItem(this.STORAGE_KEY_GENDER, gender);
        }
        
        // Update gender buttons
        document.querySelectorAll('.gender-theme-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.gender === gender);
        });
        
        // Dispatch event
        window.dispatchEvent(new CustomEvent('themeChanged', { 
            detail: { mode: this.getMode(), gender } 
        }));
    }
    
    getMode() {
        return document.documentElement.getAttribute('data-theme') || 'light';
    }
    
    getGender() {
        return document.documentElement.getAttribute('data-gender') || 'female';
    }
    
    toggleMode() {
        const currentMode = this.getMode();
        this.setMode(currentMode === 'light' ? 'dark' : 'light');
    }
    
    setupEventListeners() {
        // Dark mode toggle switch
        document.querySelectorAll('.theme-switch input').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.setMode(e.target.checked ? 'dark' : 'light');
            });
        });

        // Mode buttons
        document.querySelectorAll('[data-mode]').forEach(btn => {
            btn.addEventListener('click', () => {
                this.setMode(btn.dataset.mode);
            });
        });

        // Gender theme buttons
        document.querySelectorAll('.gender-theme-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                this.setGender(btn.dataset.gender);
            });
        });

        // Theme toggle buttons (.theme-toggle class)
        document.querySelectorAll('.theme-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                this.toggleMode();
                this.updateToggleUI();
            });
        });

        // Initial UI update
        this.updateToggleUI();
    }

    /**
     * Update the UI of theme toggle buttons
     */
    updateToggleUI() {
        const currentMode = this.getMode();

        // Update toggle button text
        document.querySelectorAll('.theme-toggle-text').forEach(el => {
            el.textContent = currentMode === 'dark' ? 'Lichte modus' : 'Donkere modus';
        });

        // Update theme toggle icons (CSS handles visibility via data-theme attribute)
    }
}

// Initialize theme manager
const themeManager = new ThemeManager();

// Export for use in other scripts
window.GlamourTheme = themeManager;
