<!-- PWA Install Prompt -->
<div id="pwaInstallPrompt" class="pwa-install-prompt" style="display:none">
    <div class="pwa-install-content">
        <button class="pwa-install-close" onclick="dismissInstallPrompt()">&times;</button>
        <div class="pwa-install-icon">
            <img src="/icon-192.png" alt="GlamourSchedule" width="60" height="60">
        </div>
        <div class="pwa-install-text">
            <h3>Installeer GlamourSchedule</h3>
            <p>Voeg toe aan je startscherm voor snelle toegang</p>
            <p class="pwa-appstore-note">Binnenkort ook in de App Store verkrijgbaar!</p>
        </div>
        <button class="pwa-install-btn" id="pwaInstallBtn">
            <i class="fas fa-download"></i> Installeren
        </button>
    </div>
</div>

<!-- iOS Install Instructions -->
<div id="iosInstallPrompt" class="pwa-install-prompt ios-prompt" style="display:none">
    <div class="pwa-install-content ios-content">
        <button class="pwa-install-close" onclick="dismissIosPrompt()">&times;</button>
        <div class="pwa-install-icon">
            <img src="/icon-192.png" alt="GlamourSchedule" width="60" height="60">
        </div>
        <div class="pwa-install-text">
            <h3>Installeer GlamourSchedule</h3>
            <p>Tik op <span class="ios-share-icon"><i class="fas fa-share-square"></i></span> en dan <strong>"Zet op beginscherm"</strong></p>
            <p class="pwa-appstore-note">Binnenkort ook in de App Store verkrijgbaar!</p>
        </div>
        <div class="ios-steps">
            <div class="ios-step">
                <span class="step-num">1</span>
                <span>Tik op <i class="fas fa-share-square"></i> onderaan</span>
            </div>
            <div class="ios-step">
                <span class="step-num">2</span>
                <span>Scroll naar "Zet op beginscherm"</span>
            </div>
            <div class="ios-step">
                <span class="step-num">3</span>
                <span>Tik op "Voeg toe"</span>
            </div>
        </div>
        <button class="pwa-install-btn ios-dismiss" onclick="dismissIosPrompt()">
            Begrepen
        </button>
    </div>
</div>

<style>
.pwa-install-prompt {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 10003;
    padding: 1rem;
    animation: slideUp 0.3s ease;
}
@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.pwa-install-content {
    max-width: 400px;
    margin: 0 auto;
    background: #000000;
    border: 1px solid #333333;
    border-radius: 20px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 -10px 40px rgba(0,0,0,0.5);
    position: relative;
}
.pwa-install-close {
    position: absolute;
    top: 0.5rem;
    right: 0.75rem;
    background: none;
    border: none;
    color: #666;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.25rem;
    line-height: 1;
}
.pwa-install-close:hover {
    color: #fff;
}
.pwa-install-icon img {
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.pwa-install-text {
    flex: 1;
}
.pwa-install-text h3 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    color: #ffffff;
    font-weight: 600;
}
.pwa-install-text p {
    margin: 0;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.6);
}
.pwa-appstore-note {
    margin-top: 0.5rem !important;
    font-size: 0.75rem !important;
    color: #f59e0b !important;
    font-weight: 500;
}
.pwa-install-btn {
    background: #ffffff;
    color: #000000;
    border: none;
    padding: 0.75rem 1.25rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    white-space: nowrap;
}
.pwa-install-btn:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(255,255,255,0.2);
}

/* iOS specific styles */
.ios-prompt .pwa-install-content {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
}
.ios-prompt .pwa-install-text {
    margin-bottom: 1rem;
}
.ios-share-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: #007aff;
    color: white;
    border-radius: 6px;
    margin: 0 0.25rem;
    font-size: 0.9rem;
}
.ios-steps {
    width: 100%;
    margin-bottom: 1rem;
}
.ios-step {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
}
.ios-step .step-num {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: #333;
    border-radius: 50%;
    font-size: 0.8rem;
    font-weight: 600;
    color: #fff;
}
.ios-step i {
    color: #007aff;
}
.ios-dismiss {
    width: 100%;
    justify-content: center;
}

@media (max-width: 480px) {
    .pwa-install-content {
        flex-wrap: wrap;
        justify-content: center;
        text-align: center;
        padding-top: 2rem;
    }
    .pwa-install-text {
        width: 100%;
    }
    .pwa-install-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// PWA Install Prompt Logic
let deferredPrompt = null;
let installPromptShown = false;

// Check if already installed as PWA
function isPWA() {
    return window.matchMedia('(display-mode: standalone)').matches ||
           window.navigator.standalone === true ||
           document.referrer.includes('android-app://');
}

// Check if iOS
function isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
}

// Check if Android
function isAndroid() {
    return /Android/.test(navigator.userAgent);
}

// Show install prompt
function showInstallPrompt() {
    if (installPromptShown || isPWA()) return;
    if (localStorage.getItem('pwaInstallDismissed')) return;

    if (isIOS() && !window.navigator.standalone) {
        document.getElementById('iosInstallPrompt').style.display = 'block';
        installPromptShown = true;
    } else if (deferredPrompt) {
        document.getElementById('pwaInstallPrompt').style.display = 'block';
        installPromptShown = true;
    }
}

// Dismiss prompts
function dismissInstallPrompt() {
    document.getElementById('pwaInstallPrompt').style.display = 'none';
    localStorage.setItem('pwaInstallDismissed', Date.now());
}

function dismissIosPrompt() {
    document.getElementById('iosInstallPrompt').style.display = 'none';
    localStorage.setItem('pwaInstallDismissed', Date.now());
}

// Handle beforeinstallprompt event
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Show prompt after a delay
    setTimeout(showInstallPrompt, 5000);
});

// Handle install button click
document.getElementById('pwaInstallBtn')?.addEventListener('click', async () => {
    if (!deferredPrompt) return;

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;

    if (outcome === 'accepted') {
        console.log('PWA installed');
    }

    deferredPrompt = null;
    dismissInstallPrompt();
});

// Handle successful installation
window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    dismissInstallPrompt();
    console.log('PWA installed successfully');
});

// Show iOS prompt after delay
if (isIOS() && !isPWA()) {
    const dismissed = localStorage.getItem('pwaInstallDismissed');
    // Show again after 7 days
    if (!dismissed || (Date.now() - parseInt(dismissed)) > 7 * 24 * 60 * 60 * 1000) {
        setTimeout(() => {
            document.getElementById('iosInstallPrompt').style.display = 'block';
            installPromptShown = true;
        }, 10000);
    }
}

// Register service worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => {
            console.log('ServiceWorker registration failed:', err);
        });
    });
}
</script>
