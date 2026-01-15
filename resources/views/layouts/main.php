<!DOCTYPE html>
<?php
$isLoggedIn = isset($_SESSION['user_id']);
$isBusiness = isset($_SESSION['business_id']);
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Glamourschedule' ?> - Beauty Booking Platform</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/prestige.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Mobile Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="sidebar-logo">Glamourschedule</a>
            <button class="sidebar-close" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Navigatie</div>
            </div>
            <a href="/" class="sidebar-link <?= $currentPath === '/' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="/search" class="sidebar-link <?= $currentPath === '/search' ? 'active' : '' ?>">
                <i class="fas fa-search"></i> Zoeken
            </a>

            <div class="sidebar-divider"></div>

            <!-- Categories -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Categorieën</div>
            </div>
            <a href="/search?group=haar" class="sidebar-link"><i class="fas fa-cut"></i> Haar</a>
            <a href="/search?group=nagels" class="sidebar-link"><i class="fas fa-hand-sparkles"></i> Nagels</a>
            <a href="/search?group=huid" class="sidebar-link"><i class="fas fa-spa"></i> Skincare</a>
            <a href="/search?group=lichaam" class="sidebar-link"><i class="fas fa-hands"></i> Massage</a>
            <a href="/search?group=makeup" class="sidebar-link"><i class="fas fa-paint-brush"></i> Make-up</a>
            <a href="/search?group=wellness" class="sidebar-link"><i class="fas fa-hot-tub"></i> Wellness</a>

            <div class="sidebar-divider"></div>

            <!-- Account Section -->
            <?php if ($isLoggedIn): ?>
            <div class="sidebar-section">
                <div class="sidebar-section-title">Mijn Account</div>
            </div>
            <a href="/dashboard" class="sidebar-link <?= strpos($currentPath, '/dashboard') === 0 ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Dashboard
            </a>
            <a href="/dashboard/bookings" class="sidebar-link">
                <i class="fas fa-calendar-check"></i> Mijn Boekingen
            </a>
            <a href="/dashboard/settings" class="sidebar-link">
                <i class="fas fa-cog"></i> Instellingen
            </a>
            <?php endif; ?>

            <?php if ($isBusiness): ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-section">
                <div class="sidebar-section-title">Mijn Salon</div>
            </div>
            <a href="/business/dashboard" class="sidebar-link">
                <i class="fas fa-store"></i> Salon Dashboard
            </a>
            <a href="/business/bookings" class="sidebar-link">
                <i class="fas fa-calendar-alt"></i> Afspraken
            </a>
            <a href="/business/services" class="sidebar-link">
                <i class="fas fa-list"></i> Diensten
            </a>
            <?php endif; ?>

            <div class="sidebar-divider"></div>

            <!-- Business & Info -->
            <div class="sidebar-section">
                <div class="sidebar-section-title">Voor Bedrijven</div>
            </div>
            <a href="/business/register" class="sidebar-link">
                <i class="fas fa-rocket"></i> Salon Aanmelden
            </a>
            <a href="/sales/register" class="sidebar-link">
                <i class="fas fa-handshake"></i> Word Partner
            </a>

            <div class="sidebar-divider"></div>

            <!-- Info -->
            <a href="/about" class="sidebar-link"><i class="fas fa-info-circle"></i> Over Ons</a>
            <a href="/contact" class="sidebar-link"><i class="fas fa-envelope"></i> Contact</a>
        </nav>

        <div class="sidebar-footer">
            <?php if ($isLoggedIn): ?>
                <a href="/logout" class="sidebar-btn sidebar-btn-outline">
                    <i class="fas fa-sign-out-alt"></i> Uitloggen
                </a>
            <?php else: ?>
                <a href="/login" class="sidebar-btn">Inloggen</a>
                <a href="/register" class="sidebar-btn sidebar-btn-outline">Registreren</a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Navigation -->
    <nav class="nav-prestige">
        <button class="nav-toggle" onclick="openSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <a href="/" class="logo-prestige">
            <span>Glamourschedule</span>
        </a>

        <ul class="nav-links-prestige" id="navMenu">
            <li><a href="/search"><?= $translations['search'] ?? 'Zoeken' ?></a></li>
            <?php if (isset($user)): ?>
                <li><a href="/dashboard"><?= $translations['dashboard'] ?? 'Dashboard' ?></a></li>
                <li><a href="/dashboard/bookings"><?= $translations['bookings'] ?? 'Boekingen' ?></a></li>
            <?php endif; ?>
            <li><a href="/business/register"><?= $translations['for_business'] ?? 'Voor Salons' ?></a></li>
        </ul>

        <div class="nav-actions-prestige">
            <?php if (isset($user)): ?>
                <a href="/dashboard" class="btn btn-primary"><?= $translations['my_account'] ?? 'Account' ?></a>
            <?php else: ?>
                <a href="/login" class="btn btn-secondary"><?= $translations['login'] ?? 'Inloggen' ?></a>
                <a href="/register" class="btn btn-primary"><?= $translations['register'] ?? 'Registreren' ?></a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php if (isset($flashMessage)): ?>
            <div class="container" style="padding-top: 6rem;">
                <div class="alert alert-<?= $flashType ?? 'success' ?>"><?= $flashMessage ?></div>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="footer-prestige">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="/" class="footer-logo">Glamourschedule</a>
                <p><?= $translations['footer_desc'] ?? 'Het premium beauty booking platform van Nederland. Vind en boek de beste salons.' ?></p>
            </div>

            <div>
                <h4><?= $translations['platform'] ?? 'Platform' ?></h4>
                <ul>
                    <li><a href="/search"><?= $translations['search'] ?? 'Zoeken' ?></a></li>
                    <li><a href="/business/register"><?= $translations['for_business'] ?? 'Voor Salons' ?></a></li>
                    <li><a href="/sales/login">Sales Portal</a></li>
                </ul>
            </div>

            <div>
                <h4><?= $translations['company'] ?? 'Bedrijf' ?></h4>
                <ul>
                    <li><a href="/about"><?= $translations['about'] ?? 'Over ons' ?></a></li>
                    <li><a href="/contact"><?= $translations['contact'] ?? 'Contact' ?></a></li>
                    <li><a href="https://www.kvk.nl/zoeken/?source=all&q=81973667" target="_blank">KVK: 81973667</a></li>
                </ul>
            </div>

            <div>
                <h4><?= $translations['legal'] ?? 'Juridisch' ?></h4>
                <ul>
                    <li><a href="/terms"><?= $translations['terms'] ?? 'Voorwaarden' ?></a></li>
                    <li><a href="/privacy"><?= $translations['privacy'] ?? 'Privacy' ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Glamourschedule. <?= $translations['rights'] ?? 'Alle rechten voorbehouden.' ?></p>
            <a href="https://phantrium.com" target="_blank">
                <i class="fas fa-code"></i> Ontwikkeld door Phantrium
            </a>
        </div>
    </footer>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('active');
            document.getElementById('sidebarOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('sidebarOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
