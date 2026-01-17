<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle ?? 'Sales Dashboard') ?> - GlamourSchedule</title>
    <link rel="manifest" href="/manifest-sales.json">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/images/sales-icon-192.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            min-height: 100vh;
        }

        /* Sidebar */
        .sales-sidebar {
            width: 280px;
            background: linear-gradient(180deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            padding: 1.5rem;
            z-index: 1000;
            border-right: 2px solid rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .sales-sidebar .logo {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #ffffff;
        }
        .sales-sidebar .logo i {
            color: #ffffff;
        }
        .sales-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
        }
        .sales-sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .sales-sidebar nav a.active {
            background: #ffffff;
            color: #000000;
        }
        .sales-sidebar nav a i {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 1.5rem;
            left: 1.5rem;
            right: 1.5rem;
        }
        .sidebar-footer .user-info {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 1rem;
            margin-bottom: 1rem;
        }
        .sidebar-footer .user-name {
            margin: 0;
            font-size: 0.9rem;
            color: #ffffff;
            font-weight: 500;
        }
        .sidebar-footer .user-email {
            margin: 0.25rem 0 0 0;
            font-size: 0.8rem;
            color: #999999;
        }
        .sidebar-footer .logout-btn {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 0;
            transition: color 0.2s;
        }
        .sidebar-footer .logout-btn:hover {
            color: #ffffff;
        }

        /* Main Content */
        .sales-main {
            margin-left: 280px;
            padding: 1.5rem;
            min-height: 100vh;
            background: #0a0a0a;
        }
        .sales-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .sales-header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 1.5rem;
        }
        .referral-code-box {
            background: #000000;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid #333333;
        }
        .referral-code-box span {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
        }
        .referral-code-box code {
            font-size: 1.1rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #ffffff;
        }
        .referral-code-box button {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #444444;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .referral-code-box button:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Stats Cards */
        .stat-card {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #333333;
        }
        .stat-card h4 {
            color: #a1a1a1;
            font-size: 0.85rem;
            margin: 0 0 0.75rem 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #ffffff;
        }

        /* Cards */
        .card {
            background: #1a1a1a;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #333333;
        }
        .card h3 {
            color: #ffffff;
            margin: 0 0 1rem 0;
        }

        /* Alert */
        .alert {
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
        }
        .alert-success {
            background: #1a1a1a;
            border: 1px solid #22c55e;
            color: #22c55e;
        }
        .alert-error {
            background: #1a1a1a;
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 0.75rem;
            left: 0.75rem;
            z-index: 1001;
            background: #000;
            border: none;
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .mobile-menu-btn:active {
            transform: scale(0.95);
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            z-index: 999;
        }

        /* Bottom Navigation for Mobile */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #0a0a0a;
            border-top: 1px solid #333333;
            padding: 0.5rem 0.25rem;
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.3);
        }
        .mobile-bottom-nav nav {
            display: flex;
            justify-content: space-around;
            max-width: 500px;
            margin: 0 auto;
        }
        .mobile-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem 0.75rem;
            color: #666;
            text-decoration: none;
            font-size: 0.65rem;
            flex: 1;
            text-align: center;
            border-radius: 8px;
            transition: all 0.2s;
            min-height: 50px;
            justify-content: center;
        }
        .mobile-bottom-nav a i {
            font-size: 1.3rem;
            margin-bottom: 0.2rem;
        }
        .mobile-bottom-nav a.active {
            color: #fff;
            background: #1a1a1a;
        }
        .mobile-bottom-nav a:active {
            transform: scale(0.95);
            background: #222;
        }
        .mobile-bottom-nav a.nav-logout {
            color: #ef4444;
        }
        .mobile-bottom-nav a.nav-logout:active {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
            }
            .sales-sidebar {
                transform: translateX(-100%);
            }
            .sales-sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.show {
                display: block;
            }
            .sales-main {
                margin-left: 0;
                padding: 0.75rem;
                padding-top: 65px;
                padding-bottom: 85px;
            }
            .sales-header {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                margin-bottom: 1rem;
            }
            .sales-header h1 {
                font-size: 1.1rem;
                text-align: center;
                margin: 0;
            }
            .referral-code-box {
                justify-content: space-between;
                padding: 0.6rem 1rem;
                border-radius: 10px;
            }
            .referral-code-box span {
                font-size: 0.75rem;
            }
            .referral-code-box code {
                font-size: 1rem;
                letter-spacing: 1px;
            }
            .referral-code-box button {
                padding: 0.4rem 0.6rem;
            }
            .mobile-bottom-nav {
                display: block;
            }
            /* Stats grid 2x2 on mobile */
            .grid-4 {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.5rem;
            }
            .stat-card {
                padding: 1rem;
                border-radius: 12px;
            }
            .stat-card h4 {
                font-size: 0.7rem;
                margin-bottom: 0.5rem;
            }
            .stat-card h4 i {
                display: none;
            }
            .stat-card .value {
                font-size: 1.4rem;
            }
            /* Cards on mobile */
            .card {
                padding: 1rem;
                border-radius: 12px;
            }
            .card h3 {
                font-size: 1rem;
                margin-bottom: 0.75rem;
            }
            /* Buttons */
            .btn {
                padding: 0.65rem 1.25rem;
                font-size: 0.9rem;
                width: 100%;
                justify-content: center;
            }
            /* Tables on mobile */
            .referral-table-desktop {
                display: none !important;
            }
            .referral-cards-mobile {
                display: block !important;
            }
            table th, table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }
            /* Alert */
            .alert {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }
        }

        /* Hide mobile cards by default, show table */
        .referral-cards-mobile {
            display: none;
        }
        .referral-card {
            background: #1a1a1a;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }
        .referral-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .referral-card-name {
            font-weight: 600;
            color: #fff;
        }
        .referral-card-date {
            font-size: 0.8rem;
            color: #888;
        }
        .referral-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .referral-card-commission {
            font-weight: 700;
            color: #fff;
            font-size: 1.1rem;
        }

        /* Safe area for notch devices */
        @supports (padding-top: env(safe-area-inset-top)) {
            .sales-sidebar {
                padding-top: calc(1.5rem + env(safe-area-inset-top));
            }
            .sales-main {
                padding-bottom: calc(80px + env(safe-area-inset-bottom));
            }
            .mobile-bottom-nav {
                padding-bottom: env(safe-area-inset-bottom);
            }
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333333;
        }
        th {
            color: #a1a1a1;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        td {
            color: #ffffff;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #333333, #000000);
            color: #ffffff;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #000000, #333333);
            transform: translateY(-1px);
        }

        /* Links */
        a {
            color: #ffffff;
        }
        a:hover {
            color: #ffffff;
        }

        /* Grid layouts */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .grid-2, .grid-3 {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            .grid-4 {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }
        }
        @media (min-width: 769px) and (max-width: 1024px) {
            .grid-3, .grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars" id="menuIcon"></i>
    </button>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sales-sidebar" id="sidebar">
        <div class="logo">
            <i class="fas fa-chart-line"></i>
            Sales Portal
        </div>

        <nav>
            <a href="/sales/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/dashboard') !== false ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/sales/early-birds" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/early-birds') !== false ? 'active' : '' ?>" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#000">
                <i class="fas fa-seedling"></i> Early Birds
            </a>
            <a href="/sales/referrals" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/referrals') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Referrals
            </a>
            <a href="/sales/payouts" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/payouts') !== false ? 'active' : '' ?>">
                <i class="fas fa-euro-sign"></i> Uitbetalingen
            </a>
            <a href="/sales/materials" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/materials') !== false ? 'active' : '' ?>">
                <i class="fas fa-bullhorn"></i> Materiaal
            </a>
            <a href="/sales/guide" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/guide') !== false ? 'active' : '' ?>">
                <i class="fas fa-clipboard-list"></i> Stappenplan
            </a>
            <a href="/sales/account" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/account') !== false ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Account
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <p class="user-name"><?= htmlspecialchars($salesUser['name'] ?? '') ?></p>
                <p class="user-email"><?= htmlspecialchars($salesUser['email'] ?? '') ?></p>
            </div>
            <a href="/sales/logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Uitloggen
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="sales-main">
        <div class="sales-header">
            <h1><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <div class="referral-code-box">
                <span>Jouw code:</span>
                <code id="refCode"><?= htmlspecialchars($salesUser['referral_code'] ?? '') ?></code>
                <button onclick="copyCode()" id="copyBtn">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] === 'success' ? 'success' : 'error' ?>">
                <i class="fas fa-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav">
        <nav>
            <a href="/sales/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/dashboard') !== false ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                Home
            </a>
            <a href="/sales/early-birds" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/early-birds') !== false ? 'active' : '' ?>" style="<?= strpos($_SERVER['REQUEST_URI'], '/sales/early-birds') !== false ? '' : 'color:#f59e0b' ?>">
                <i class="fas fa-seedling"></i>
                Early Birds
            </a>
            <a href="/sales/referrals" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/referrals') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                Referrals
            </a>
            <a href="/sales/payouts" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/payouts') !== false ? 'active' : '' ?>">
                <i class="fas fa-euro-sign"></i>
                Geld
            </a>
            <a href="/sales/logout" class="nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                Uitloggen
            </a>
        </nav>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const icon = document.getElementById('menuIcon');

            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const icon = document.getElementById('menuIcon');

            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            icon.classList.add('fa-bars');
            icon.classList.remove('fa-times');
        }

        function copyCode() {
            const code = document.getElementById('refCode').textContent;
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            });
        }

        // Close sidebar when clicking on a link (mobile)
        document.querySelectorAll('.sales-sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // Register service worker for PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw-sales.js').catch(() => {});
        }
    </script>

    <!-- PWA Install Prompt for Sales -->
    <div id="salesInstallPrompt" class="sales-install-prompt" style="display:none">
        <div class="install-content">
            <button class="install-close" onclick="this.parentElement.parentElement.style.display='none';localStorage.setItem('salesInstallDismissed','1')">&times;</button>
            <img src="/images/sales-icon-192.png" alt="GS Sales" width="48" height="48" style="border-radius:10px">
            <div class="install-text">
                <strong>Installeer GS Sales</strong>
                <span>Snelle toegang tot je sales dashboard</span>
            </div>
            <button class="install-btn" id="salesInstallBtn">Installeren</button>
        </div>
    </div>

    <!-- iOS Install Instructions for Sales -->
    <div id="salesIosPrompt" class="sales-install-prompt ios-prompt" style="display:none">
        <div class="install-content ios-content">
            <button class="install-close" onclick="this.parentElement.parentElement.style.display='none';localStorage.setItem('salesInstallDismissed','1')">&times;</button>
            <img src="/images/sales-icon-192.png" alt="GS Sales" width="48" height="48" style="border-radius:10px">
            <div class="install-text" style="text-align:center;width:100%">
                <strong style="display:block;margin-bottom:0.5rem">Installeer GS Sales</strong>
                <span>Tik op <i class="fas fa-share-square" style="color:#007aff"></i> en dan <strong>"Zet op beginscherm"</strong></span>
            </div>
            <button class="install-btn" style="width:100%;margin-top:1rem;justify-content:center" onclick="this.parentElement.parentElement.style.display='none';localStorage.setItem('salesInstallDismissed','1')">Begrepen</button>
        </div>
    </div>

    <style>
    .sales-install-prompt {
        position: fixed;
        bottom: 80px;
        left: 1rem;
        right: 1rem;
        z-index: 10000;
        animation: slideUpPrompt 0.3s ease;
    }
    @keyframes slideUpPrompt {
        from { transform: translateY(100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .sales-install-prompt .install-content {
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
        box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    }
    .sales-install-prompt .install-close {
        position: absolute;
        top: 0.25rem;
        right: 0.5rem;
        background: none;
        border: none;
        color: #666;
        font-size: 1.25rem;
        cursor: pointer;
    }
    .sales-install-prompt .install-text {
        flex: 1;
    }
    .sales-install-prompt .install-text strong {
        display: block;
        color: #fff;
        font-size: 0.9rem;
    }
    .sales-install-prompt .install-text span {
        font-size: 0.8rem;
        color: #888;
    }
    .sales-install-prompt .install-btn {
        background: #fff;
        color: #000;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .ios-prompt .install-content {
        flex-wrap: wrap;
        justify-content: center;
    }
    @media (min-width: 769px) {
        .sales-install-prompt { bottom: 1rem; }
    }
    </style>

    <script>
    (function() {
        let deferredPrompt = null;
        const isPWA = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
        const dismissed = localStorage.getItem('salesInstallDismissed');
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        if (isPWA || dismissed) return;

        // iOS - show custom instructions
        if (isIOS) {
            setTimeout(() => {
                document.getElementById('salesIosPrompt').style.display = 'block';
            }, 8000);
            return;
        }

        // Android/Chrome - use beforeinstallprompt
        window.addEventListener('beforeinstallprompt', e => {
            e.preventDefault();
            deferredPrompt = e;
            setTimeout(() => {
                document.getElementById('salesInstallPrompt').style.display = 'block';
            }, 5000);
        });

        document.getElementById('salesInstallBtn')?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            document.getElementById('salesInstallPrompt').style.display = 'none';
            if (outcome === 'accepted') {
                localStorage.setItem('salesInstallDismissed', '1');
            }
        });

        window.addEventListener('appinstalled', () => {
            document.getElementById('salesInstallPrompt').style.display = 'none';
            localStorage.setItem('salesInstallDismissed', '1');
        });
    })();
    </script>
</body>
</html>
