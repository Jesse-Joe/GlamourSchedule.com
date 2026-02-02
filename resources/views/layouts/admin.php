<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin - GlamourSchedule' ?></title>

    <!-- Early Theme Detection (prevents flash of wrong theme) -->
    <script>
    (function() {
        var saved = localStorage.getItem('glamour_theme_mode');
        var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/prestige.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/mobile-friendly.css?v=<?= time() ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #000000;
            --primary-dark: #000000;
            --accent: #ffffff;
            --accent-light: #e0e0e0;
            --bg: #000000;
            --card: #0a0a0a;
            --text: #ffffff;
            --text-light: #999999;
            --border: #333333;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        [data-theme="light"] {
            --primary: #000000;
            --primary-dark: #000000;
            --accent: #000000;
            --accent-light: #333333;
            --bg: #ffffff;
            --card: #ffffff;
            --text: #000000;
            --text-light: #666666;
            --border: #e0e0e0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
        }
        .sidebar-brand i {
            font-size: 1.5rem;
            color: var(--accent);
        }
        .sidebar-brand span {
            font-size: 1.1rem;
            font-weight: 700;
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .nav-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            margin-top: 1rem;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .nav-link.active {
            background: var(--accent);
            color: white;
        }
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: var(--primary-dark);
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .admin-avatar {
            width: 40px;
            height: 40px;
            background: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .admin-name {
            font-size: 0.9rem;
            font-weight: 500;
        }
        .admin-role {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.5);
        }
        .btn-logout {
            display: block;
            width: 100%;
            padding: 0.5rem;
            background: rgba(255,255,255,0.1);
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            text-align: center;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.2);
        }
        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
        }
        .topbar {
            background: var(--card);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .theme-toggle {
            background: var(--bg);
            border: 1px solid var(--border);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .theme-toggle:hover {
            background: var(--border);
        }
        .topbar-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .topbar-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .content {
            padding: 2rem;
        }
        .card {
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
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
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .stat-card.primary { border-left: 4px solid var(--accent); }
        .stat-card.success { border-left: 4px solid var(--success); }
        .stat-card.warning { border-left: 4px solid var(--warning); }
        .stat-card.info { border-left: 4px solid #17a2b8; }
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
        }
        .stat-change {
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        .stat-change.positive { color: var(--success); }
        .stat-change.negative { color: var(--danger); }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.875rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        th {
            background: var(--bg);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-light);
            text-transform: uppercase;
        }
        tr:hover {
            background: var(--bg);
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-secondary { background: #e9ecef; color: #495057; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        .btn-primary:hover {
            background: var(--accent-light);
        }
        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: var(--border);
        }
        .btn-success {
            background: var(--success);
            color: white;
        }
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
            background: var(--card);
            color: var(--text);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
        }
        .search-box {
            display: flex;
            gap: 0.5rem;
            max-width: 400px;
        }
        .search-box .form-control {
            flex: 1;
        }
        .pagination {
            display: flex;
            gap: 0.25rem;
            margin-top: 1rem;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            text-decoration: none;
            color: var(--text);
            font-size: 0.875rem;
        }
        .pagination a:hover {
            background: var(--bg);
        }
        .pagination .active {
            background: var(--accent);
            color: white;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        .mobile-toggle {
            display: none;
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.25rem;
            cursor: pointer;
            z-index: 200;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/admin/dashboard" class="sidebar-brand">
                    <i class="fas fa-shield-alt"></i>
                    <span><?= $translations['admin_panel'] ?? 'Admin Panel' ?></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section"><?= $translations['admin_overview'] ?? 'Overzicht' ?></div>
                <a href="/admin/dashboard" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <?= $translations['admin_dashboard'] ?? 'Dashboard' ?>
                </a>
                <a href="/admin/revenue" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/revenue') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i>
                    <?= $translations['admin_revenue'] ?? 'Omzet' ?>
                </a>
                <a href="/admin/payouts" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/payouts') !== false ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-transfer"></i>
                    Uitbetalingen
                </a>

                <div class="nav-section"><?= $translations['admin_management'] ?? 'Beheer' ?></div>
                <a href="/admin/users" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <?= $translations['admin_users'] ?? 'Gebruikers' ?>
                </a>
                <a href="/admin/businesses" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/businesses') !== false ? 'active' : '' ?>">
                    <i class="fas fa-store"></i>
                    <?= $translations['admin_businesses'] ?? 'Bedrijven' ?>
                </a>
                <a href="/admin/sales-partners" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/sales-partners') !== false ? 'active' : '' ?>">
                    <i class="fas fa-handshake"></i>
                    <?= $translations['admin_sales_partners'] ?? 'Sales Partners' ?>
                </a>

                <div class="nav-section"><?= $translations['admin_system'] ?? 'Systeem' ?></div>
                <a href="/" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <?= $translations['admin_view_website'] ?? 'Website bekijken' ?>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <?= strtoupper(substr($admin['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="admin-name"><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></div>
                        <div class="admin-role"><?= ucfirst($admin['role'] ?? 'admin') ?></div>
                    </div>
                </div>
                <a href="/admin/logout" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> <?= $translations['admin_logout'] ?? 'Uitloggen' ?>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <div class="topbar">
                <h1 class="topbar-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
                <div class="topbar-actions">
                    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle dark/light mode">
                        <i class="fas fa-sun" id="themeIcon"></i>
                    </button>
                    <span style="color:var(--text-light);font-size:0.9rem">
                        <?= date('d M Y, H:i') ?>
                    </span>
                </div>
            </div>

            <div class="content">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php
                        $msg = $_GET['success'];
                        if ($msg === 'updated') echo $translations['admin_success_updated'] ?? 'Succesvol bijgewerkt.';
                        elseif ($msg === 'deleted') echo $translations['admin_success_deleted'] ?? 'Succesvol verwijderd.';
                        elseif ($msg === 'activated') echo $translations['admin_success_activated'] ?? 'Succesvol geactiveerd.';
                        else echo $translations['admin_success_action'] ?? 'Actie succesvol uitgevoerd.';
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php
                        $err = $_GET['error'];
                        if ($err === 'csrf') echo $translations['admin_error_csrf'] ?? 'Beveiligingsfout. Probeer opnieuw.';
                        elseif ($err === 'has_business') echo $translations['admin_error_has_business'] ?? 'Gebruiker heeft een bedrijf en kan niet worden verwijderd.';
                        else echo $translations['admin_error_generic'] ?? 'Er is een fout opgetreden.';
                        ?>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </main>
    </div>

    <button class="mobile-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
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
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = current === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        }

        // Initialize theme icon on load
        updateThemeIcon();
    </script>

    <!-- Theme Manager -->
    <script src="/js/theme.js?v=<?= time() ?>"></script>
</body>
</html>
