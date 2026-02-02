<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> - GlamourSchedule Business</title>

    <!-- Early Theme Detection (prevents flash of wrong theme) -->
    <script>
    (function() {
        var saved = localStorage.getItem('glamour_theme_mode');
        var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="manifest" href="/manifest-business.json">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GS Business">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/prestige.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/mobile-friendly.css?v=<?= time() ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        /* Dark mode (default) */
        :root {
            --primary: #ffffff;
            --primary-dark: #e0e0e0;
            --primary-light: #333333;
            --secondary: #1a1a1a;
            --text: #ffffff;
            --text-light: #999999;
            --white: #000000;
            --card-bg: #0a0a0a;
            --border: #333333;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --sidebar-width: 280px;
        }
        /* Light mode */
        [data-theme="light"] {
            --primary: #000000;
            --primary-dark: #333333;
            --secondary: #f5f5f5;
            --text: #000000;
            --text-light: #666666;
            --white: #ffffff;
            --card-bg: #ffffff;
            --border: #e0e0e0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--white);
            color: var(--text);
            min-height: 100vh;
        }

        /* Sidebar - Dark Theme - Override prestige.css */
        body .sidebar {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            width: var(--sidebar-width) !important;
            height: 100vh !important;
            background: #000000 !important;
            border-right: 1px solid #333333 !important;
            display: flex !important;
            flex-direction: column !important;
            z-index: 1000 !important;
            transition: transform 0.3s ease !important;
            transform: translateX(0) !important;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #333333;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.25rem;
        }
        .sidebar-brand i {
            font-size: 1.5rem;
            color: #ffffff;
        }
        .sidebar-business {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #1a1a1a;
            border-radius: 10px;
        }
        .sidebar-business-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #ffffff;
        }
        .sidebar-business-status {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.25rem;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
        }
        .status-dot.pending { background: #f59e0b; }

        .sidebar-nav {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
        }
        .nav-section {
            margin-bottom: 1.5rem;
        }
        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            padding: 0 0.75rem;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
        }
        .nav-item i {
            width: 20px;
            text-align: center;
            color: #ffffff;
        }
        .nav-item:hover {
            background: #1a1a1a;
            color: #ffffff;
        }
        .nav-item.active {
            background: #ffffff;
            color: #000000;
        }
        .nav-item.active i {
            color: #000000;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #333333;
        }
        .view-page-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            background: #1a1a1a;
            border: 2px solid #333333;
            border-radius: 10px;
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .view-page-btn i {
            color: #ffffff;
        }
        .view-page-btn:hover {
            border-color: #ffffff;
            background: #333333;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--white);
        }
        .top-bar {
            background: var(--card-bg);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            color: var(--text);
        }
        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text);
        }
        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .page-content {
            padding: 2rem;
        }

        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text);
            cursor: pointer;
            padding: 0.5rem;
            min-width: 44px;
            min-height: 44px;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .mobile-toggle:hover,
        .mobile-toggle:active {
            opacity: 0.7;
        }

        /* Components */
        .card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            color: var(--text);
            border: 1px solid var(--border);
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text);
        }
        .card-title i {
            color: var(--primary);
        }

        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .btn-secondary {
            background: var(--secondary);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            background: var(--white);
            color: var(--text);
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .form-hint {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--card-bg);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .alert-success {
            border-left: 4px solid var(--success);
        }
        .alert-danger {
            border-left: 4px solid var(--danger);
        }
        .alert-warning {
            border-left: 4px solid var(--warning);
        }

        /* Dark mode specific overrides */
        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .btn-secondary {
            background: var(--secondary);
            color: var(--text);
            border: 1px solid var(--border);
        }

        /* Table styles */
        table {
            color: var(--text);
        }
        table th {
            background: var(--card-bg);
            color: var(--text);
            border-color: var(--border);
        }
        table td {
            border-color: var(--border);
            color: var(--text);
        }

        /* Form styles in light/dark mode */
        .form-control {
            background: var(--card-bg);
            color: var(--text);
            border-color: var(--border);
        }
        .form-control::placeholder {
            color: var(--text-light);
        }

        .grid {
            display: grid;
            gap: 1.5rem;
        }
        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }
        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        .grid-4 {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .text-center { text-align: center; }
        .text-muted { color: var(--text-light); }

        /* Desktop - Always show sidebar */
        @media (min-width: 1025px) {
            body .sidebar {
                display: flex !important;
                transform: translateX(0) !important;
                left: 0 !important;
            }
            .main-content {
                margin-left: var(--sidebar-width) !important;
            }
            .mobile-toggle {
                display: none !important;
            }
        }

        /* Tablet & Mobile */
        @media (max-width: 1024px) {
            body .sidebar {
                transform: translateX(-100%) !important;
                left: 0 !important;
            }
            body .sidebar.open {
                transform: translateX(0) !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .mobile-toggle {
                display: flex !important;
                align-items: center;
                justify-content: center;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.open {
                display: block;
            }
        }

        /* iPad Portrait */
        @media (min-width: 768px) and (max-width: 1024px) {
            .page-content {
                padding: 1.5rem;
            }
            .grid-2, .grid-3 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Mobile */
        @media (max-width: 767px) {
            .page-content {
                padding: 1rem;
            }
            .grid-2, .grid-3, .grid-4 {
                grid-template-columns: 1fr;
            }
            .top-bar {
                padding: 0.75rem 1rem;
            }
            .top-bar h1 {
                font-size: 1.2rem;
            }
        }

        /* Theme Toggle in Navigation */
        .theme-toggle-nav {
            font-family: inherit;
            font-size: inherit;
        }
        .theme-toggle-nav:hover {
            background: #1a1a1a;
        }

        /* Top bar actions mobile-friendly */
        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .top-bar-actions .btn {
            min-width: 44px;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Mobile: compact top bar actions */
        @media (max-width: 480px) {
            .top-bar-actions {
                gap: 0.35rem;
            }
            .top-bar-actions .btn span,
            .top-bar-actions .btn-text {
                display: none;
            }
            .top-bar-actions .btn {
                padding: 0.5rem;
                width: 44px;
                height: 44px;
                border-radius: 50%;
            }
            .top-bar-actions .btn i {
                margin: 0;
            }
            .top-bar h1 {
                font-size: 1rem;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }

    </style>
</head>
<body>
    <?php
    $currentPath = $_SERVER['REQUEST_URI'];
    $csrfToken = $_SESSION['csrf_token'] ?? '';
    if (empty($csrfToken)) {
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;
    }
    ?>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="sidebar-brand">
                <span style="font-weight:900;font-size:1.5rem">GS</span>
            </a>
            <div class="sidebar-business">
                <div class="sidebar-business-name"><?= htmlspecialchars($business['company_name'] ?? ($translations['default_business_name'] ?? 'My Business')) ?></div>
                <div class="sidebar-business-status">
                    <span class="status-dot <?= ($business['status'] ?? '') === 'active' ? '' : 'pending' ?>"></span>
                    <?= ($business['status'] ?? '') === 'active' ? ($translations['status_active'] ?? 'Active') : ($translations['status_pending'] ?? 'Pending') ?>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title"><?= $translations['nav_overview'] ?? 'Overview' ?></div>
                <a href="/business/dashboard" class="nav-item <?= $currentPath === '/business/dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> <?= $translations['nav_dashboard'] ?? 'Dashboard' ?>
                </a>
                <a href="/business/calendar" class="nav-item <?= $currentPath === '/business/calendar' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <?= $translations['nav_calendar'] ?? 'Calendar' ?>
                </a>
                <a href="/business/bookings" class="nav-item <?= $currentPath === '/business/bookings' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> <?= $translations['nav_bookings'] ?? 'Bookings' ?>
                </a>
                <a href="/business/scanner" class="nav-item <?= $currentPath === '/business/scanner' ? 'active' : '' ?>" style="background:linear-gradient(135deg,#333333,#000000);color:white;border-radius:10px;margin-top:0.5rem">
                    <i class="fas fa-qrcode"></i> <?= $translations['nav_qr_scanner'] ?? 'QR Scanner' ?>
                </a>
                <a href="/business/pos" class="nav-item <?= $currentPath === '/business/pos' ? 'active' : '' ?>" style="background:linear-gradient(135deg,#1e40af,#1d4ed8);color:white;border-radius:10px;margin-top:0.5rem">
                    <i class="fas fa-cash-register"></i> <?= $translations['nav_pos_system'] ?? 'POS System' ?>
                </a>
                <span class="nav-item" style="background:#333333;color:#888888;border-radius:10px;margin-top:0.5rem;cursor:not-allowed;opacity:0.7">
                    <i class="fas fa-credit-card"></i> <?= $translations['nav_pin_terminals'] ?? 'PIN Terminals' ?> <i class="fas fa-lock" style="font-size:0.7rem;margin-left:0.25rem"></i>
                </span>
            </div>

            <div class="nav-section">
                <div class="nav-section-title"><?= $translations['nav_management'] ?? 'Management' ?></div>
                <a href="/business/services" class="nav-item <?= $currentPath === '/business/services' ? 'active' : '' ?>">
                    <i class="fas fa-cut"></i> <?= $translations['nav_services'] ?? 'Services' ?>
                </a>
                <a href="/business/inventory" class="nav-item <?= $currentPath === '/business/inventory' ? 'active' : '' ?>">
                    <i class="fas fa-boxes"></i> <?= $translations['nav_inventory'] ?? 'Inventory' ?>
                </a>
                <?php if (($business['business_type'] ?? 'eenmanszaak') === 'bv'): ?>
                <a href="/business/employees" class="nav-item <?= $currentPath === '/business/employees' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> <?= $translations['nav_employees'] ?? 'Employees' ?>
                </a>
                <?php endif; ?>
                <a href="/business/reviews" class="nav-item <?= $currentPath === '/business/reviews' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i> <?= $translations['nav_reviews'] ?? 'Reviews' ?>
                </a>
                <a href="/business/payouts" class="nav-item <?= $currentPath === '/business/payouts' ? 'active' : '' ?>">
                    <i class="fas fa-euro-sign"></i> <?= $translations['nav_payouts'] ?? 'Payouts' ?>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title"><?= $translations['nav_website_section'] ?? 'Website' ?></div>
                <a href="/business/website" class="nav-item <?= $currentPath === '/business/website' ? 'active' : '' ?>">
                    <i class="fas fa-globe"></i> <?= $translations['nav_website_content'] ?? 'Page Content' ?>
                </a>
                <a href="/business/photos" class="nav-item <?= $currentPath === '/business/photos' ? 'active' : '' ?>">
                    <i class="fas fa-images"></i> <?= $translations['nav_photos'] ?? 'Photos' ?>
                </a>
                <a href="/business/theme" class="nav-item <?= $currentPath === '/business/theme' ? 'active' : '' ?>">
                    <i class="fas fa-palette"></i> <?= $translations['nav_theme_colors'] ?? 'Theme & Colors' ?>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title"><?= $translations['nav_marketing'] ?? 'Marketing' ?></div>
                <a href="/business/boost" class="nav-item <?= $currentPath === '/business/boost' ? 'active' : '' ?>" style="<?= $currentPath !== '/business/boost' ? 'background:linear-gradient(135deg,#f59e0b,#d97706);color:white;' : '' ?>">
                    <i class="fas fa-rocket"></i> <?= $translations['nav_boost_business'] ?? 'Boost your Business' ?>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title"><?= $translations['nav_settings'] ?? 'Settings' ?></div>
                <a href="/business/profile" class="nav-item <?= $currentPath === '/business/profile' ? 'active' : '' ?>">
                    <i class="fas fa-building"></i> <?= $translations['nav_business_profile'] ?? 'Business Profile' ?>
                </a>
                <a href="/business/mollie/connect" class="nav-item <?= $currentPath === '/business/mollie/connect' ? 'active' : '' ?>" style="<?= empty($business['mollie_account_id']) ? 'background:linear-gradient(135deg,#7c3aed,#5b21b6);color:white;' : '' ?>">
                    <i class="fas fa-link"></i> <?= empty($business['mollie_account_id']) ? ($translations['nav_mollie_connect'] ?? 'Connect Mollie') : ($translations['nav_mollie_connected'] ?? 'Mollie Connected') ?>
                </a>
                <button type="button" class="nav-item theme-toggle-nav" onclick="toggleTheme()" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;">
                    <i class="fas fa-sun" id="themeIconNav"></i> <span id="themeTextNav"><?= $translations['nav_light_mode'] ?? 'Light Mode' ?></span>
                </button>
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="/business/<?= htmlspecialchars($business['slug'] ?? '') ?>" target="_blank" class="view-page-btn">
                <i class="fas fa-external-link-alt"></i> <?= $translations['nav_view_public_page'] ?? 'View Public Page' ?>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="top-bar">
            <div style="display:flex;align-items:center;gap:1rem">
                <button class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            </div>
            <div class="top-bar-actions">
                <a href="/logout" class="btn btn-secondary btn-sm" title="<?= $translations['nav_logout'] ?? 'Logout' ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="btn-text"><?= $translations['nav_logout'] ?? 'Logout' ?></span>
                </a>
            </div>
        </header>

        <div class="page-content">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                    <i class="fas fa-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </div>


    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('glamour_theme_mode', next);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const icon = document.getElementById('themeIconNav');
            const text = document.getElementById('themeTextNav');
            if (icon) {
                icon.className = current === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
            if (text) {
                text.textContent = current === 'dark' ? '<?= $translations['nav_light_mode'] ?? 'Light Mode' ?>' : '<?= $translations['nav_dark_mode'] ?? 'Dark Mode' ?>';
            }
        }

        // Initialize theme icon on load
        updateThemeIcon();

        // Initialize Push Notifications for Business
        async function initBusinessPush() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
                return;
            }

            try {
                // Register service worker
                await navigator.serviceWorker.register('/sw.js');
                const registration = await navigator.serviceWorker.ready;

                // Check if already subscribed
                const existingSubscription = await registration.pushManager.getSubscription();
                if (existingSubscription) {
                    return; // Already subscribed
                }

                // Request permission
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    return;
                }

                // Get VAPID key
                const response = await fetch('/api/push/vapid-key');
                const { publicKey } = await response.json();
                if (!publicKey) return;

                // Subscribe
                const applicationServerKey = urlBase64ToUint8Array(publicKey);
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });

                // Save to server
                await fetch('/api/push/subscribe', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(subscription.toJSON())
                });

                console.log('Business push notifications enabled');
            } catch (error) {
                console.error('Push notification setup failed:', error);
            }
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }

        // Initialize push after short delay
        setTimeout(initBusinessPush, 2000);

        // Register business service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw-business.js').catch(() => {});
        }
    </script>

    <!-- PWA Install Prompt for Business -->
    <div id="businessInstallPrompt" class="business-install-prompt" style="display:none">
        <div class="install-content">
            <button class="install-close" onclick="this.parentElement.parentElement.style.display='none';localStorage.setItem('businessInstallDismissed','1')">&times;</button>
            <img src="/icon-192.png" alt="GS Business" width="48" height="48" style="border-radius:10px">
            <div class="install-text">
                <strong><?= $translations['install_gs_business'] ?? 'Install GS Business' ?></strong>
                <span><?= $translations['install_quick_access'] ?? 'Quick access to your dashboard' ?></span>
                <span style="display:block;margin-top:0.25rem;color:#f59e0b;font-size:0.7rem"><?= $translations['install_coming_soon'] ?? 'Coming soon to the App Store!' ?></span>
            </div>
            <button class="install-btn" id="businessInstallBtn"><?= $translations['install_btn'] ?? 'Install' ?></button>
        </div>
    </div>

    <style>
    .business-install-prompt {
        position: fixed;
        bottom: 80px;
        left: 1rem;
        right: 1rem;
        z-index: 10000;
    }
    .install-content {
        max-width: 400px;
        margin: 0 auto;
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 16px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }
    .install-close {
        position: absolute;
        top: 0.25rem;
        right: 0.5rem;
        background: none;
        border: none;
        color: #666;
        font-size: 1.25rem;
        cursor: pointer;
    }
    .install-text {
        flex: 1;
    }
    .install-text strong {
        display: block;
        color: #fff;
        font-size: 0.9rem;
    }
    .install-text span {
        font-size: 0.8rem;
        color: #888;
    }
    .install-btn {
        background: #fff;
        color: #000;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }
    @media (min-width: 1025px) {
        .business-install-prompt { bottom: 1rem; }
    }
    </style>

    <script>
    (function() {
        let deferredPrompt = null;
        const isPWA = window.matchMedia('(display-mode: standalone)').matches;
        const dismissed = localStorage.getItem('businessInstallDismissed');

        if (isPWA || dismissed) return;

        window.addEventListener('beforeinstallprompt', e => {
            e.preventDefault();
            deferredPrompt = e;
            setTimeout(() => {
                document.getElementById('businessInstallPrompt').style.display = 'block';
            }, 5000);
        });

        document.getElementById('businessInstallBtn')?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            document.getElementById('businessInstallPrompt').style.display = 'none';
        });
    })();
    </script>

    <!-- Security PIN Setup Popup -->
    <?php include BASE_PATH . '/resources/views/components/security-pin-popup.php'; ?>

    <!-- Theme Manager -->
    <script src="/js/theme.js?v=<?= time() ?>"></script>
</body>
</html>
