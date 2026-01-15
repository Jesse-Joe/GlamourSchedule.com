<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> - GlamourSchedule Business</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #000000;
            --primary-dark: #000000;
            --primary-light: #333333;
            --secondary: #fafafa;
            --text: #111827;
            --text-light: #6b7280;
            --white: #ffffff;
            --border: #e5e7eb;
            --success: #333333;
            --danger: #333333;
            --warning: #000000;
            --sidebar-width: 280px;
        }
        [data-theme="dark"] {
            --secondary: #0a0a0a;
            --text: #fafafa;
            --text-light: #9ca3af;
            --white: #171717;
            --border: #262626;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--secondary);
            color: var(--text);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.25rem;
        }
        .sidebar-brand i {
            font-size: 1.5rem;
        }
        .sidebar-business {
            margin-top: 1rem;
            padding: 0.75rem;
            background: var(--secondary);
            border-radius: 10px;
        }
        .sidebar-business-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text);
        }
        .sidebar-business-status {
            font-size: 0.75rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.25rem;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }
        .status-dot.pending { background: var(--warning); }

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
            color: var(--text-light);
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
            color: var(--text);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
        }
        .nav-item:hover {
            background: var(--secondary);
            color: var(--primary);
        }
        .nav-item.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border);
        }
        .view-page-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            background: var(--secondary);
            border: 2px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .view-page-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .top-bar {
            background: var(--white);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .top-bar h1 {
            font-size: 1.5rem;
            font-weight: 600;
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
        }

        /* Components */
        .card {
            background: var(--white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        }
        .alert-success {
            background: #ffffff;
            color: #000000;
        }
        .alert-danger {
            background: #f5f5f5;
            color: #000000;
        }
        .alert-warning {
            background: #ffffff;
            color: #000000;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-toggle {
                display: block;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 99;
            }
            .sidebar-overlay.open {
                display: block;
            }
        }

        /* Theme Toggle */
        .theme-toggle {
            background: var(--secondary);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
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
                <i class="fas fa-spa"></i>
                GlamourSchedule
            </a>
            <div class="sidebar-business">
                <div class="sidebar-business-name"><?= htmlspecialchars($business['company_name'] ?? 'Mijn Bedrijf') ?></div>
                <div class="sidebar-business-status">
                    <span class="status-dot <?= ($business['status'] ?? '') === 'active' ? '' : 'pending' ?>"></span>
                    <?= ($business['status'] ?? '') === 'active' ? 'Actief' : 'In afwachting' ?>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Overzicht</div>
                <a href="/business/dashboard" class="nav-item <?= $currentPath === '/business/dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/business/calendar" class="nav-item <?= $currentPath === '/business/calendar' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Agenda
                </a>
                <a href="/business/bookings" class="nav-item <?= $currentPath === '/business/bookings' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> Boekingen
                </a>
                <a href="/business/scanner" class="nav-item <?= $currentPath === '/business/scanner' ? 'active' : '' ?>" style="background:linear-gradient(135deg,#333333,#000000);color:white;border-radius:10px;margin-top:0.5rem">
                    <i class="fas fa-qrcode"></i> QR Scanner
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Beheer</div>
                <a href="/business/services" class="nav-item <?= $currentPath === '/business/services' ? 'active' : '' ?>">
                    <i class="fas fa-cut"></i> Diensten
                </a>
                <a href="/business/reviews" class="nav-item <?= $currentPath === '/business/reviews' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i> Reviews
                </a>
                <a href="/business/payouts" class="nav-item <?= $currentPath === '/business/payouts' ? 'active' : '' ?>">
                    <i class="fas fa-euro-sign"></i> Uitbetalingen
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Webpagina</div>
                <a href="/business/website" class="nav-item <?= $currentPath === '/business/website' ? 'active' : '' ?>">
                    <i class="fas fa-globe"></i> Pagina Inhoud
                </a>
                <a href="/business/photos" class="nav-item <?= $currentPath === '/business/photos' ? 'active' : '' ?>">
                    <i class="fas fa-images"></i> Foto's
                </a>
                <a href="/business/theme" class="nav-item <?= $currentPath === '/business/theme' ? 'active' : '' ?>">
                    <i class="fas fa-palette"></i> Thema & Kleuren
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Instellingen</div>
                <a href="/business/profile" class="nav-item <?= $currentPath === '/business/profile' ? 'active' : '' ?>">
                    <i class="fas fa-building"></i> Bedrijfsprofiel
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="/business/<?= htmlspecialchars($business['slug'] ?? '') ?>" target="_blank" class="view-page-btn">
                <i class="fas fa-external-link-alt"></i> Bekijk Publieke Pagina
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
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
                <a href="/logout" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Uitloggen
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
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
