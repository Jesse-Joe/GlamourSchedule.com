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
            background: #ffffff;
            color: #000000;
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
            color: #000000;
        }
        .sales-sidebar .logo i {
            color: #000000;
        }
        .sales-sidebar nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            color: #999999;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
        }
        .sales-sidebar nav a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #000000;
        }
        .sales-sidebar nav a.active {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(218, 165, 32, 0.2));
            color: #000000;
            border: 1px solid rgba(255, 215, 0, 0.4);
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
            border-top: 1px solid rgba(0,0,0,0.1);
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
            color: #999999;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 0;
            transition: color 0.2s;
        }
        .sidebar-footer .logout-btn:hover {
            color: #000000;
        }

        /* Main Content */
        .sales-main {
            margin-left: 280px;
            padding: 1.5rem;
            min-height: 100vh;
            background: #ffffff;
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
            color: #000000;
            font-size: 1.5rem;
        }
        .referral-code-box {
            background: linear-gradient(135deg, #000000, #000000);
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
            color: #000000;
        }
        .referral-code-box code {
            font-size: 1.1rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #000000;
        }
        .referral-code-box button {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid #333333;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .referral-code-box button:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        /* Stats Cards */
        .stat-card {
            background: #f5f5f5;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .stat-card h4 {
            color: #666666;
            font-size: 0.85rem;
            margin: 0 0 0.75rem 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #000000;
        }

        /* Cards */
        .card {
            background: #f5f5f5;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .card h3 {
            color: #000000;
            margin: 0 0 1rem 0;
        }

        /* Alert */
        .alert {
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
        }
        .alert-success {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid #333333;
            color: #000000;
        }
        .alert-error {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid #333333;
            color: #d4d4d4;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: #f5f5f5;
            border: 1px solid rgba(0,0,0,0.1);
            color: #000000;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.25rem;
            align-items: center;
            justify-content: center;
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
            background: #f5f5f5;
            border-top: 1px solid rgba(0,0,0,0.1);
            padding: 0.5rem;
            z-index: 1000;
        }
        .mobile-bottom-nav nav {
            display: flex;
            justify-content: space-around;
        }
        .mobile-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem;
            color: #666666;
            text-decoration: none;
            font-size: 0.7rem;
            flex: 1;
            text-align: center;
        }
        .mobile-bottom-nav a i {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }
        .mobile-bottom-nav a.active {
            color: #000000;
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
                padding: 1rem;
                padding-top: 70px;
                padding-bottom: 80px;
            }
            .sales-header {
                flex-direction: column;
                align-items: stretch;
            }
            .sales-header h1 {
                font-size: 1.25rem;
                text-align: center;
            }
            .referral-code-box {
                justify-content: center;
                flex-wrap: wrap;
            }
            .mobile-bottom-nav {
                display: block;
            }
            .stat-card .value {
                font-size: 1.5rem;
            }
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
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        th {
            color: #666666;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        td {
            color: #000000;
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
            color: #000000;
        }
        a:hover {
            color: #000000;
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
            .grid-2, .grid-3, .grid-4 {
                grid-template-columns: 1fr;
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
            <a href="/sales/referrals" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/referrals') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i>
                Referrals
            </a>
            <a href="/sales/payouts" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/payouts') !== false ? 'active' : '' ?>">
                <i class="fas fa-euro-sign"></i>
                Geld
            </a>
            <a href="/sales/materials" class="<?= strpos($_SERVER['REQUEST_URI'], '/sales/materials') !== false ? 'active' : '' ?>">
                <i class="fas fa-bullhorn"></i>
                Materiaal
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
</body>
</html>
