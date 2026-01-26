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
    <title><?= $pageTitle ?? 'Glamourschedule' ?> - Booking Platform</title>

    <!-- Early Theme Detection (prevents flash of wrong theme) -->
    <script>
    (function() {
        var saved = localStorage.getItem('glamour_theme_mode');
        var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>

    <link rel="icon" type="image/svg+xml" href="/images/gs-logo-circle.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#000000">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/prestige.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/mobile-friendly.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Critical Mobile Navigation CSS (inline to avoid caching issues) -->
    <style>
    @media (max-width: 768px) {
        .nav-toggle {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            z-index: 100;
        }
        .sidebar-overlay {
            pointer-events: none;
        }
        .sidebar-overlay.active {
            pointer-events: auto;
        }
    }
    </style>
</head>
<body>
    <!-- Global Search Modal -->
    <div class="global-search-overlay" id="globalSearchOverlay" onclick="closeGlobalSearch()"></div>
    <div class="global-search-modal" id="globalSearchModal">
        <div class="global-search-header">
            <i class="fas fa-search"></i>
            <input type="text" id="globalSearchInput" placeholder="<?= $translations['search_placeholder'] ?? 'Search salons, services, pages...' ?>" autocomplete="off" oninput="handleGlobalSearch(this.value)">
            <button class="global-search-close" onclick="closeGlobalSearch()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="global-search-results" id="globalSearchResults">
            <div class="search-quick-links">
                <div class="search-section-title"><?= $translations['quick_links'] ?? 'Quick links' ?></div>
                <a href="/search" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-store"></i>
                    <span><?= $translations['view_all_salons'] ?? 'View All Salons' ?></span>
                </a>
                <a href="/search?category=kapper" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-cut"></i>
                    <span><?= $translations['hairdressers'] ?? 'Hairdressers' ?></span>
                </a>
                <a href="/search?category=schoonheid" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-spa"></i>
                    <span><?= $translations['beauty_specialists'] ?? 'Beauty Specialists' ?></span>
                </a>
                <a href="/search?category=nagels" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-hand-sparkles"></i>
                    <span><?= $translations['nail_salons'] ?? 'Nail Salons' ?></span>
                </a>
                <a href="/search?category=massage" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-hands"></i>
                    <span><?= $translations['massage'] ?? 'Massage' ?></span>
                </a>
            </div>
            <div class="search-section-title"><?= $translations['pages'] ?? 'Pages' ?></div>
            <a href="/register?type=business" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-rocket"></i>
                <span><?= $translations['register_salon'] ?? 'Register Salon' ?></span>
            </a>
            <a href="/marketing" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-bullhorn"></i>
                <span><?= $translations['marketing_services'] ?? 'Marketing Services' ?></span>
            </a>
            <a href="/about" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-cogs"></i>
                <span><?= $translations['platform_features'] ?? 'Platform Features' ?></span>
            </a>
            <a href="/contact" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-envelope"></i>
                <span><?= $translations['contact'] ?? 'Contact' ?></span>
            </a>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Mobile Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="sidebar-logo">
                <span>Glamourschedule</span>
            </a>
            <button class="sidebar-close" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="sidebar-section">
                <div class="sidebar-section-title"><?= $translations['navigation'] ?? 'Navigation' ?></div>
            </div>
            <a href="/" class="sidebar-link <?= $currentPath === '/' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> <?= $translations['home'] ?? 'Home' ?>
            </a>
            <a href="/search" class="sidebar-link <?= $currentPath === '/search' ? 'active' : '' ?>">
                <i class="fas fa-search"></i> <?= $translations['search'] ?? 'Search' ?>
            </a>

            <div class="sidebar-divider"></div>

            <!-- Account Section -->
            <?php if ($isLoggedIn): ?>
            <div class="sidebar-section">
                <div class="sidebar-section-title"><?= $translations['my_account'] ?? 'My Account' ?></div>
            </div>
            <a href="/dashboard" class="sidebar-link <?= strpos($currentPath, '/dashboard') === 0 ? 'active' : '' ?>">
                <i class="fas fa-user"></i> <?= $translations['dashboard'] ?? 'Dashboard' ?>
            </a>
            <a href="/dashboard/bookings" class="sidebar-link">
                <i class="fas fa-calendar-check"></i> <?= $translations['my_bookings'] ?? 'My Bookings' ?>
            </a>
            <a href="/dashboard/settings" class="sidebar-link">
                <i class="fas fa-cog"></i> <?= $translations['settings'] ?? 'Settings' ?>
            </a>
            <?php endif; ?>

            <?php if ($isBusiness): ?>
            <div class="sidebar-divider"></div>
            <div class="sidebar-section">
                <div class="sidebar-section-title"><?= $translations['my_salon'] ?? 'My Salon' ?></div>
            </div>
            <a href="/business/dashboard" class="sidebar-link">
                <i class="fas fa-store"></i> <?= $translations['salon_dashboard'] ?? 'Salon Dashboard' ?>
            </a>
            <a href="/business/bookings" class="sidebar-link">
                <i class="fas fa-calendar-alt"></i> <?= $translations['appointments'] ?? 'Appointments' ?>
            </a>
            <a href="/business/services" class="sidebar-link">
                <i class="fas fa-list"></i> <?= $translations['services'] ?? 'Services' ?>
            </a>
            <?php endif; ?>

            <div class="sidebar-divider"></div>

            <!-- Business & Info -->
            <div class="sidebar-section">
                <div class="sidebar-section-title"><?= $translations['for_business'] ?? 'For Business' ?></div>
            </div>
            <a href="/register?type=business" class="sidebar-link">
                <i class="fas fa-rocket"></i> <?= $translations['register_salon'] ?? 'Register Salon' ?>
            </a>
            <a href="/sales/register" class="sidebar-link">
                <i class="fas fa-handshake"></i> <?= $translations['become_partner'] ?? 'Become Partner' ?>
            </a>

            <div class="sidebar-divider"></div>

            <!-- Info -->
            <a href="/marketing" class="sidebar-link"><i class="fas fa-bullhorn"></i> <?= $translations['marketing'] ?? 'Marketing' ?></a>
            <a href="/about" class="sidebar-link"><i class="fas fa-cogs"></i> <?= $translations['features'] ?? 'Features' ?></a>
            <a href="/contact" class="sidebar-link"><i class="fas fa-envelope"></i> <?= $translations['contact'] ?? 'Contact' ?></a>
        </nav>

        <div class="sidebar-footer">
            <!-- Mobile Language Selector -->
            <?php
            // Build language URLs for mobile that preserve current query parameters
            $mobileLangFlags = [
                'en' => ['code' => 'EN', 'color' => '#003399', 'name' => 'English'],
                'nl' => ['code' => 'NL', 'color' => '#FF6B00', 'name' => 'Nederlands'],
                'de' => ['code' => 'DE', 'color' => '#DD0000', 'name' => 'Deutsch'],
                'fr' => ['code' => 'FR', 'color' => '#0055A4', 'name' => 'Français'],
                'es' => ['code' => 'ES', 'color' => '#AA151B', 'name' => 'Español'],
                'it' => ['code' => 'IT', 'color' => '#009246', 'name' => 'Italiano'],
                'pt' => ['code' => 'PT', 'color' => '#006600', 'name' => 'Português'],
                'ru' => ['code' => 'RU', 'color' => '#0039A6', 'name' => 'Русский'],
                'ja' => ['code' => 'JA', 'color' => '#BC002D', 'name' => '日本語'],
                'ko' => ['code' => 'KO', 'color' => '#003478', 'name' => '한국어'],
                'zh' => ['code' => 'ZH', 'color' => '#DE2910', 'name' => '中文'],
                'ar' => ['code' => 'AR', 'color' => '#006C35', 'name' => 'العربية'],
                'tr' => ['code' => 'TR', 'color' => '#E30A17', 'name' => 'Türkçe'],
                'pl' => ['code' => 'PL', 'color' => '#DC143C', 'name' => 'Polski'],
                'sv' => ['code' => 'SV', 'color' => '#006AA7', 'name' => 'Svenska'],
                'no' => ['code' => 'NO', 'color' => '#BA0C2F', 'name' => 'Norsk'],
                'da' => ['code' => 'DA', 'color' => '#C8102E', 'name' => 'Dansk'],
                'fi' => ['code' => 'FI', 'color' => '#003580', 'name' => 'Suomi'],
                'el' => ['code' => 'EL', 'color' => '#0D5EAF', 'name' => 'Ελληνικά'],
                'cs' => ['code' => 'CS', 'color' => '#11457E', 'name' => 'Čeština'],
                'hu' => ['code' => 'HU', 'color' => '#436F4D', 'name' => 'Magyar'],
                'ro' => ['code' => 'RO', 'color' => '#002B7F', 'name' => 'Română'],
                'bg' => ['code' => 'BG', 'color' => '#00966E', 'name' => 'Български'],
                'hr' => ['code' => 'HR', 'color' => '#FF0000', 'name' => 'Hrvatski'],
                'sk' => ['code' => 'SK', 'color' => '#0B4EA2', 'name' => 'Slovenčina'],
                'sl' => ['code' => 'SL', 'color' => '#005DA4', 'name' => 'Slovenščina'],
                'et' => ['code' => 'ET', 'color' => '#0072CE', 'name' => 'Eesti'],
                'lv' => ['code' => 'LV', 'color' => '#9E3039', 'name' => 'Latviešu'],
                'lt' => ['code' => 'LT', 'color' => '#006A44', 'name' => 'Lietuvių'],
                'uk' => ['code' => 'UK', 'color' => '#005BBB', 'name' => 'Українська'],
                'hi' => ['code' => 'HI', 'color' => '#FF9933', 'name' => 'हिन्दी'],
                'th' => ['code' => 'TH', 'color' => '#2D2A4A', 'name' => 'ไทย'],
                'vi' => ['code' => 'VI', 'color' => '#DA251D', 'name' => 'Tiếng Việt'],
                'id' => ['code' => 'ID', 'color' => '#FF0000', 'name' => 'Bahasa Indonesia'],
                'ms' => ['code' => 'MS', 'color' => '#010066', 'name' => 'Bahasa Melayu'],
                'tl' => ['code' => 'TL', 'color' => '#0038A8', 'name' => 'Tagalog'],
                'he' => ['code' => 'HE', 'color' => '#0038B8', 'name' => 'עברית'],
                'fa' => ['code' => 'FA', 'color' => '#239F40', 'name' => 'فارسی'],
                'sw' => ['code' => 'SW', 'color' => '#006600', 'name' => 'Kiswahili'],
                'af' => ['code' => 'AF', 'color' => '#007A4D', 'name' => 'Afrikaans']
            ];
            $currentLangMobile = $lang ?? 'en';

            $buildMobileLangUrl = function($newLang) {
                $params = $_GET;
                $params['lang'] = $newLang;
                $query = http_build_query($params);
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                return $path . '?' . $query;
            };
            ?>
            <div class="sidebar-lang-selector">
                <span class="sidebar-lang-label"><?= $translations['language'] ?? 'Language' ?></span>
                <div class="sidebar-lang-options">
                    <?php foreach ($mobileLangFlags as $lCode => $lData): ?>
                    <a href="<?= htmlspecialchars($buildMobileLangUrl($lCode)) ?>" class="sidebar-lang-btn <?= $currentLangMobile === $lCode ? 'active' : '' ?>" title="<?= $lData['name'] ?>">
                        <span class="lang-flag-badge" style="background: <?= $lData['color'] ?>"><?= $lData['code'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="theme-toggle">
                <i class="fas fa-sun theme-icon-light"></i>
                <i class="fas fa-moon theme-icon-dark"></i>
                <span class="theme-toggle-text"><?= $translations['light_mode'] ?? 'Light Mode' ?></span>
            </button>
            <?php if ($isLoggedIn): ?>
                <a href="/logout" class="sidebar-btn sidebar-btn-outline">
                    <i class="fas fa-sign-out-alt"></i> <?= $translations['logout'] ?? 'Logout' ?>
                </a>
            <?php else: ?>
                <a href="/login" class="sidebar-btn"><?= $translations['login'] ?? 'Login' ?></a>
                <a href="/register" class="sidebar-btn sidebar-btn-outline"><?= $translations['register'] ?? 'Register' ?></a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Navigation -->
    <nav class="nav-prestige">
        <button class="nav-toggle" id="mobileMenuBtn" type="button" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>

        <a href="/" class="logo-prestige">
            <span class="logo-mark">GS</span>
            <span class="logo-text">Glamourschedule</span>
        </a>

        <ul class="nav-links-prestige" id="navMenu">
            <li><a href="javascript:void(0)" class="nav-search-btn" onclick="openGlobalSearch()" title="<?= $translations['search'] ?? 'Search' ?>"><i class="fas fa-search"></i> <?= $translations['search'] ?? 'Search' ?></a></li>
            <li><a href="/register?type=business"><?= $translations['register_salon'] ?? 'Register Salon' ?></a></li>
            <li><a href="/sales/register"><?= $translations['become_partner'] ?? 'Become Partner' ?></a></li>
            <li><a href="/sales/login"><?= $translations['sales_portal'] ?? 'Sales Portal' ?></a></li>
            <li><a href="/marketing"><?= $translations['marketing'] ?? 'Marketing' ?></a></li>
            <li><a href="/about"><?= $translations['features'] ?? 'Features' ?></a></li>
            <li><a href="/contact"><?= $translations['contact'] ?? 'Contact' ?></a></li>

            <?php if (isset($user)): ?>
                <li><a href="/dashboard"><?= $translations['dashboard'] ?? 'Dashboard' ?></a></li>
                <li><a href="/dashboard/bookings"><?= $translations['my_bookings'] ?? 'My Bookings' ?></a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions-prestige">
            <button class="nav-theme-toggle theme-toggle" title="<?= $translations['toggle_theme'] ?? 'Toggle theme' ?>">
                <i class="fas fa-sun theme-icon-light"></i>
                <i class="fas fa-moon theme-icon-dark"></i>
            </button>

            <a href="javascript:void(0)" class="nav-search-mobile" title="<?= $translations['search'] ?? 'Search' ?>" onclick="openGlobalSearch()">
                <i class="fas fa-search"></i>
            </a>

            <!-- Language Selector Dropdown -->
            <?php
            // Build language URLs that preserve current query parameters
            // Reuse mobileLangFlags for consistency
            $langFlags = [];
            $langNames = [];
            foreach ($mobileLangFlags as $code => $data) {
                $langFlags[$code] = ['code' => $data['code'], 'color' => $data['color']];
                $langNames[$code] = $data['name'];
            }
            $currentLang = $lang ?? 'en';
            $currentFlag = $langFlags[$currentLang] ?? $langFlags['en'];

            // Function to build lang URL preserving other query params
            $buildLangUrl = function($newLang) {
                $params = $_GET;
                $params['lang'] = $newLang;
                $query = http_build_query($params);
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                return $path . '?' . $query;
            };
            ?>
            <div class="lang-dropdown">
                <button class="lang-dropdown-toggle" onclick="toggleLangDropdown()" title="<?= $translations['language'] ?? 'Language' ?>">
                    <span class="lang-flag-badge" style="background: <?= $currentFlag['color'] ?>"><?= $currentFlag['code'] ?></span>
                    <i class="fas fa-chevron-down lang-arrow"></i>
                </button>
                <div class="lang-dropdown-menu" id="langDropdownMenu">
                    <?php foreach ($langFlags as $langCode => $flagData): ?>
                    <a href="<?= htmlspecialchars($buildLangUrl($langCode)) ?>" class="lang-dropdown-item <?= $currentLang === $langCode ? 'active' : '' ?>">
                        <span class="lang-flag-badge" style="background: <?= $flagData['color'] ?>"><?= $flagData['code'] ?></span>
                        <span class="lang-name"><?= $langNames[$langCode] ?? $langCode ?></span>
                        <?php if ($currentLang === $langCode): ?>
                        <i class="fas fa-check lang-check"></i>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (isset($user)): ?>
                <div class="account-dropdown">
                    <button class="btn btn-primary account-dropdown-toggle" onclick="toggleAccountDropdown()">
                        <i class="fas fa-user"></i> <?= $translations['my_account'] ?? 'Account' ?> <i class="fas fa-chevron-down" style="font-size:0.7rem;margin-left:0.25rem"></i>
                    </button>
                    <div class="account-dropdown-menu" id="accountDropdownMenu">
                        <a href="/dashboard" class="account-dropdown-item">
                            <i class="fas fa-tachometer-alt"></i> <?= $translations['dashboard'] ?? 'Dashboard' ?>
                        </a>
                        <a href="/dashboard/settings" class="account-dropdown-item">
                            <i class="fas fa-cog"></i> <?= $translations['settings'] ?? 'Settings' ?>
                        </a>
                        <a href="/dashboard/bookings" class="account-dropdown-item">
                            <i class="fas fa-calendar-check"></i> <?= $translations['my_bookings'] ?? 'My Bookings' ?>
                        </a>
                        <div class="account-dropdown-divider"></div>
                        <a href="/logout" class="account-dropdown-item account-dropdown-logout">
                            <i class="fas fa-sign-out-alt"></i> <?= $translations['logout'] ?? 'Logout' ?>
                        </a>
                    </div>
                </div>
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
                <p><?= $translations['footer_desc'] ?? 'The premium booking platform. Find and book the best salons.' ?></p>
            </div>

            <div>
                <h4><?= $translations['platform'] ?? 'Platform' ?></h4>
                <ul>
                    <li><a href="/search"><?= $translations['search'] ?? 'Search' ?></a></li>
                    <li><a href="/register?type=business"><?= $translations['register_salon'] ?? 'Register Salon' ?></a></li>
                    <li><a href="/sales/login"><?= $translations['sales_portal'] ?? 'Sales Portal' ?></a></li>
                </ul>
            </div>

            <div>
                <h4><?= $translations['company'] ?? 'Company' ?></h4>
                <ul>
                    <li><a href="/about"><?= $translations['features'] ?? 'Features' ?></a></li>
                    <li><a href="/marketing"><?= $translations['marketing'] ?? 'Marketing' ?></a></li>
                    <li><a href="/contact"><?= $translations['contact'] ?? 'Contact' ?></a></li>
                    <li><a href="https://www.kvk.nl/bestellen/#/81973667000048233005?origin=search" target="_blank">KVK: 81973667</a></li>
                </ul>
            </div>

            <div>
                <h4><?= $translations['legal'] ?? 'Legal' ?></h4>
                <ul>
                    <li><a href="/terms"><?= $translations['terms'] ?? 'Terms' ?></a></li>
                    <li><a href="/privacy"><?= $translations['privacy'] ?? 'Privacy' ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Glamourschedule. <?= $translations['rights'] ?? 'All rights reserved.' ?></p>
            <a href="https://phantrium.com" target="_blank">
                <i class="fas fa-code"></i> <?= $translations['developed_by'] ?? 'Developed by' ?> Phantrium
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

        // Mobile menu button - with touch support
        document.addEventListener('DOMContentLoaded', function() {
            var menuBtn = document.getElementById('mobileMenuBtn');
            if (menuBtn) {
                menuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openSidebar();
                });
                menuBtn.addEventListener('touchend', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openSidebar();
                });
            }
        });

        // Global Search Functions
        function openGlobalSearch() {
            document.getElementById('globalSearchOverlay').classList.add('active');
            document.getElementById('globalSearchModal').classList.add('active');
            document.getElementById('globalSearchInput').focus();
            document.body.style.overflow = 'hidden';
        }

        function closeGlobalSearch() {
            document.getElementById('globalSearchOverlay').classList.remove('active');
            document.getElementById('globalSearchModal').classList.remove('active');
            document.getElementById('globalSearchInput').value = '';
            document.body.style.overflow = '';
            // Reset to default results
            resetSearchResults();
        }

        let searchTimeout;
        const defaultResults = document.getElementById('globalSearchResults').innerHTML;

        function resetSearchResults() {
            document.getElementById('globalSearchResults').innerHTML = defaultResults;
        }

        function handleGlobalSearch(query) {
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                resetSearchResults();
                return;
            }

            searchTimeout = setTimeout(() => {
                const lang = '<?= $lang ?? 'nl' ?>';
                fetch('/api/global-search?q=' + encodeURIComponent(query) + '&lang=' + lang)
                    .then(response => response.json())
                    .then(data => {
                        displaySearchResults(data, query);
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                    });
            }, 300);
        }

        function displaySearchResults(data, query) {
            const container = document.getElementById('globalSearchResults');
            let html = '';

            // Salons
            if (data.salons && data.salons.length > 0) {
                html += '<div class="search-section-title"><?= addslashes($translations['salons'] ?? 'Salons') ?></div>';
                data.salons.forEach(salon => {
                    const photo = salon.cover_image || salon.logo || '/images/placeholder-salon.jpg';
                    html += `
                        <a href="/business/${salon.slug}" class="search-result-item" onclick="closeGlobalSearch()">
                            <img src="${photo}" alt="" class="search-result-avatar" onerror="this.src='/images/placeholder-salon.jpg'">
                            <div class="search-result-info">
                                <span class="search-result-name">${salon.company_name}</span>
                                <span class="search-result-meta"><i class="fas fa-map-marker-alt"></i> ${salon.city || '<?= addslashes($translations['netherlands'] ?? 'Netherlands') ?>'}</span>
                            </div>
                        </a>
                    `;
                });
            }

            // Services
            if (data.services && data.services.length > 0) {
                html += '<div class="search-section-title"><?= addslashes($translations['services'] ?? 'Services') ?></div>';
                data.services.forEach(service => {
                    const price = service.price ? ` <small style="opacity:0.6">€${parseFloat(service.price).toFixed(2)}</small>` : '';
                    html += `
                        <a href="/business/${service.business_slug}?service=${service.id}" class="search-quick-link" onclick="closeGlobalSearch()">
                            <i class="fas fa-cut"></i>
                            <span>${service.name}${price} <small style="opacity:0.6"><?= addslashes($translations['at'] ?? 'at') ?> ${service.business_name}</small></span>
                        </a>
                    `;
                });
            }

            // Categories
            if (data.categories && data.categories.length > 0) {
                html += '<div class="search-section-title"><?= addslashes($translations['categories'] ?? 'Categories') ?></div>';
                data.categories.forEach(cat => {
                    const icon = cat.icon ? (cat.icon.startsWith('fa-') ? cat.icon : 'fa-' + cat.icon) : 'fa-tag';
                    const count = cat.salon_count > 0 ? ` <small style="opacity:0.6">(${cat.salon_count} salons)</small>` : '';
                    html += `
                        <a href="/search?category=${cat.slug}" class="search-quick-link" onclick="closeGlobalSearch()">
                            <i class="fas ${icon}"></i>
                            <span>${cat.name}${count}</span>
                        </a>
                    `;
                });
            }

            // Locations
            if (data.locations && data.locations.length > 0) {
                html += '<div class="search-section-title"><?= addslashes($translations['locations'] ?? 'Locations') ?></div>';
                data.locations.forEach(loc => {
                    html += `
                        <a href="/search?location=${encodeURIComponent(loc.city)}" class="search-quick-link" onclick="closeGlobalSearch()">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${loc.city} <small style="opacity:0.6">(${loc.salon_count} salons)</small></span>
                        </a>
                    `;
                });
            }

            // Pages (static matches)
            const pages = [
                { url: '/search', name: '<?= addslashes($translations['all_salons'] ?? 'All Salons') ?>', icon: 'fa-store', keywords: ['salon', 'zoek', 'vind', 'all', 'search', 'browse'] },
                { url: '/register?type=business', name: '<?= addslashes($translations['register_salon'] ?? 'Register Salon') ?>', icon: 'fa-rocket', keywords: ['aanmeld', 'registr', 'start', 'salon', 'register', 'nieuw'] },
                { url: '/sales/register', name: '<?= addslashes($translations['become_partner'] ?? 'Become Partner') ?>', icon: 'fa-handshake', keywords: ['partner', 'sales', 'verkoop', 'samenwerk'] },
                { url: '/sales/login', name: '<?= addslashes($translations['sales_portal'] ?? 'Sales Portal') ?>', icon: 'fa-briefcase', keywords: ['sales', 'portal', 'verkoop', 'agent'] },
                { url: '/marketing', name: '<?= addslashes($translations['marketing_services'] ?? 'Marketing Services') ?>', icon: 'fa-bullhorn', keywords: ['market', 'reclame', 'promot', 'advert', 'social'] },
                { url: '/about', name: '<?= addslashes($translations['platform_features'] ?? 'Platform Features') ?>', icon: 'fa-cogs', keywords: ['functie', 'feature', 'over', 'about', 'info', 'platform'] },
                { url: '/contact', name: '<?= addslashes($translations['contact'] ?? 'Contact') ?>', icon: 'fa-envelope', keywords: ['contact', 'help', 'vraag', 'mail', 'support', 'bericht'] },
                { url: '/terms', name: '<?= addslashes($translations['terms'] ?? 'Terms') ?>', icon: 'fa-file-contract', keywords: ['voorwaard', 'terms', 'regel', 'conditions', 'algemene'] },
                { url: '/privacy', name: '<?= addslashes($translations['privacy'] ?? 'Privacy') ?>', icon: 'fa-shield-alt', keywords: ['privacy', 'gegeven', 'data', 'cookies', 'gdpr', 'avg'] },
                { url: '/login', name: '<?= addslashes($translations['login'] ?? 'Login') ?>', icon: 'fa-sign-in-alt', keywords: ['login', 'inlog', 'aanmeld', 'account', 'sign in'] },
                { url: '/register', name: '<?= addslashes($translations['register'] ?? 'Register') ?>', icon: 'fa-user-plus', keywords: ['registr', 'aanmeld', 'account', 'nieuw', 'sign up'] },
                { url: '/dashboard', name: '<?= addslashes($translations['dashboard'] ?? 'Dashboard') ?>', icon: 'fa-tachometer-alt', keywords: ['dashboard', 'overzicht', 'account', 'mijn'] },
                { url: '/dashboard/bookings', name: '<?= addslashes($translations['my_bookings'] ?? 'My Bookings') ?>', icon: 'fa-calendar-check', keywords: ['boeking', 'afspraak', 'booking', 'reserv', 'agenda'] },
                { url: '/dashboard/settings', name: '<?= addslashes($translations['settings'] ?? 'Settings') ?>', icon: 'fa-cog', keywords: ['instelling', 'settings', 'profiel', 'wachtwoord', 'account'] },
            ];

            const q = query.toLowerCase();
            const matchedPages = pages.filter(p =>
                p.name.toLowerCase().includes(q) ||
                p.keywords.some(k => k.includes(q) || q.includes(k))
            );

            if (matchedPages.length > 0) {
                html += '<div class="search-section-title"><?= addslashes($translations['pages'] ?? 'Pages') ?></div>';
                matchedPages.forEach(page => {
                    html += `
                        <a href="${page.url}" class="search-quick-link" onclick="closeGlobalSearch()">
                            <i class="fas ${page.icon}"></i>
                            <span>${page.name}</span>
                        </a>
                    `;
                });
            }

            if (html === '') {
                html = `
                    <div class="search-no-results">
                        <i class="fas fa-search"></i>
                        <p><?= addslashes($translations['no_results_for'] ?? 'No results for') ?> "${query}"</p>
                        <a href="/search?q=${encodeURIComponent(query)}" class="btn btn-primary" style="margin-top:1rem" onclick="closeGlobalSearch()">
                            <?= addslashes($translations['search_all_salons'] ?? 'Search all salons') ?>
                        </a>
                    </div>
                `;
            }

            container.innerHTML = html;
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
                closeGlobalSearch();
            }
        });

        // Theme Toggle - uses ThemeManager from theme.js
        function toggleTheme() {
            if (window.GlamourTheme) {
                window.GlamourTheme.toggleMode();
            } else {
                // Fallback if theme.js not loaded
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('glamour_theme_mode', newTheme);
            }
            updateThemeToggleText();
        }

        function updateThemeToggleText() {
            const theme = document.documentElement.getAttribute('data-theme') || 'dark';
            const toggleText = document.querySelector('.theme-toggle-text');
            if (toggleText) {
                const themeTexts = {
                    nl: { light: 'Lichte modus', dark: 'Donkere modus' },
                    en: { light: 'Light mode', dark: 'Dark mode' },
                    de: { light: 'Heller Modus', dark: 'Dunkler Modus' },
                    fr: { light: 'Mode clair', dark: 'Mode sombre' }
                };
                const pageLang = document.documentElement.lang || 'nl';
                const texts = themeTexts[pageLang] || themeTexts['nl'];
                toggleText.textContent = theme === 'dark' ? texts.light : texts.dark;
            }
        }

        // Initialize theme toggle text on page load
        document.addEventListener('DOMContentLoaded', updateThemeToggleText);
    </script>

    <!-- Theme Manager -->
    <script src="/js/theme.js?v=<?= time() ?>"></script>

    <!-- Glamori AI Chatbot -->
    <?php include BASE_PATH . '/resources/views/components/glamori-chat.php'; ?>

    <!-- Early Bird Popup -->
    <div id="earlyBirdPopup" class="early-bird-popup" style="display:none;">
        <div class="early-bird-overlay" onclick="closeEarlyBirdPopup()"></div>
        <div class="early-bird-modal">
            <button class="early-bird-close" onclick="closeEarlyBirdPopup()">&times;</button>
            <div class="early-bird-content">
                <div class="early-bird-badge">
                    <i class="fas fa-star"></i> <?= $translations['early_bird_offer'] ?? 'Early Bird Offer' ?>
                </div>
                <h2><?= $translations['start_salon_for'] ?? 'Start your salon for only' ?></h2>
                <div class="early-bird-price">
                    <span class="currency">&euro;</span>
                    <span class="amount">0,99</span>
                </div>
                <p class="early-bird-subtitle"><?= $translations['early_bird_subtitle'] ?? 'One-time for the first 100 businesses' ?></p>
                <ul class="early-bird-features">
                    <li><i class="fas fa-check"></i> <?= $translations['early_bird_feature_1'] ?? '14 days free trial' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['early_bird_feature_2'] ?? 'Online booking system' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['early_bird_feature_3'] ?? 'Your own business page' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['early_bird_feature_4'] ?? 'No monthly costs' ?></li>
                </ul>
                <a href="/register?type=business" class="early-bird-btn">
                    <i class="fas fa-rocket"></i> <?= $translations['register_now'] ?? 'Register Now' ?>
                </a>
                <p class="early-bird-note"><?= $translations['early_bird_note'] ?? 'After this €99.99 for new businesses' ?></p>
            </div>
        </div>
    </div>

    <style>
    .early-bird-popup { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000; display: flex; align-items: center; justify-content: center; }
    .early-bird-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); }
    .early-bird-modal { position: relative; background: #000; border: 2px solid #333; border-radius: 24px; padding: 2.5rem; max-width: 420px; width: 90%; animation: earlyBirdSlide 0.4s ease; }
    @keyframes earlyBirdSlide { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .early-bird-close { position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #666; font-size: 2rem; cursor: pointer; line-height: 1; transition: color 0.2s; }
    .early-bird-close:hover { color: #fff; }
    .early-bird-content { text-align: center; }
    .early-bird-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #000; padding: 0.5rem 1.25rem; border-radius: 50px; font-size: 0.9rem; font-weight: 700; margin-bottom: 1.5rem; }
    .early-bird-content h2 { color: #fff; font-size: 1.5rem; margin: 0 0 1rem; font-weight: 600; }
    .early-bird-price { display: flex; align-items: flex-start; justify-content: center; gap: 0.25rem; margin-bottom: 0.5rem; }
    .early-bird-price .currency { font-size: 1.5rem; color: #fbbf24; font-weight: 700; margin-top: 0.5rem; }
    .early-bird-price .amount { font-size: 4rem; color: #fbbf24; font-weight: 800; line-height: 1; }
    .early-bird-subtitle { color: rgba(255,255,255,0.7); margin: 0 0 1.5rem; font-size: 0.95rem; }
    .early-bird-features { list-style: none; padding: 0; margin: 0 0 1.5rem; text-align: left; }
    .early-bird-features li { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; color: rgba(255,255,255,0.9); font-size: 0.95rem; }
    .early-bird-features li i { color: #22c55e; font-size: 0.85rem; }
    .early-bird-btn { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 1rem; background: #fff; color: #000; border-radius: 50px; font-size: 1.1rem; font-weight: 700; text-decoration: none; transition: all 0.3s; }
    .early-bird-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(255,255,255,0.2); }
    .early-bird-note { color: rgba(255,255,255,0.5); font-size: 0.85rem; margin: 1rem 0 0; }
    </style>

    <script>
    // Early Bird Popup - shows once after 10 seconds
    (function() {
        function getCookie(name) {
            const value = '; ' + document.cookie;
            const parts = value.split('; ' + name + '=');
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
        }

        // Check if popup was already shown
        if (!getCookie('earlyBirdShown')) {
            setTimeout(function() {
                document.getElementById('earlyBirdPopup').style.display = 'flex';
                setCookie('earlyBirdShown', '1', 30); // Don't show again for 30 days
            }, 10000); // 10 seconds
        }
    })();

    function closeEarlyBirdPopup() {
        document.getElementById('earlyBirdPopup').style.display = 'none';
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEarlyBirdPopup();
    });
    </script>

    <style>
    /* Account Dropdown */
    .account-dropdown {
        position: relative;
        display: inline-block;
    }
    .account-dropdown-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .account-dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: #000000;
        border: 1px solid #333333;
        border-radius: 12px;
        min-width: 200px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        z-index: 1000;
        overflow: hidden;
    }
    .account-dropdown-menu.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .account-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .account-dropdown-item:hover {
        background: #1a1a1a;
    }
    .account-dropdown-item i {
        width: 18px;
        text-align: center;
        color: rgba(255,255,255,0.7);
    }
    .account-dropdown-divider {
        height: 1px;
        background: #333333;
        margin: 0.25rem 0;
    }
    .account-dropdown-logout {
        color: #f87171;
    }
    .account-dropdown-logout i {
        color: #f87171;
    }
    .account-dropdown-logout:hover {
        background: rgba(220, 38, 38, 0.2);
    }

    /* Language Dropdown */
    .lang-dropdown {
        position: relative;
        display: inline-block;
    }
    .lang-dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: transparent;
        border: 1px solid #333;
        border-radius: 8px;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }
    .lang-dropdown-toggle:hover {
        border-color: #555;
        background: rgba(255,255,255,0.05);
    }
    .lang-flag-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.5px;
    }
    .lang-arrow {
        font-size: 0.65rem;
        opacity: 0.7;
        transition: transform 0.2s;
    }
    .lang-dropdown.open .lang-arrow {
        transform: rotate(180deg);
    }
    .lang-dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: #000;
        border: 1px solid #333;
        border-radius: 12px;
        min-width: 160px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s ease;
        z-index: 1001;
        overflow: hidden;
    }
    .lang-dropdown.open .lang-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .lang-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #fff;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .lang-dropdown-item:hover {
        background: #1a1a1a;
    }
    .lang-dropdown-item.active {
        background: rgba(255,255,255,0.08);
    }
    .lang-name {
        flex: 1;
    }
    .lang-check {
        color: #22c55e;
        font-size: 0.8rem;
    }

    /* Mobile Sidebar Language Selector */
    .sidebar-lang-selector {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem 0;
        border-bottom: 1px solid #222;
        margin-bottom: 1rem;
    }
    .sidebar-lang-label {
        font-size: 0.75rem;
        color: rgba(255,255,255,0.5);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .sidebar-lang-options {
        display: flex;
        gap: 0.5rem;
    }
    .sidebar-lang-btn {
        padding: 0.5rem;
        border-radius: 8px;
        background: rgba(255,255,255,0.05);
        border: 1px solid transparent;
        transition: all 0.2s;
        text-decoration: none;
    }
    .sidebar-lang-btn:hover {
        background: rgba(255,255,255,0.1);
    }
    .sidebar-lang-btn.active {
        border-color: #fff;
        background: rgba(255,255,255,0.1);
    }

    @media (max-width: 768px) {
        .lang-dropdown {
            display: none;
        }
    }
    </style>

    <script>
    function toggleAccountDropdown() {
        const menu = document.getElementById('accountDropdownMenu');
        menu.classList.toggle('active');
        // Close language dropdown if open
        document.querySelector('.lang-dropdown')?.classList.remove('open');
    }

    function toggleLangDropdown() {
        const dropdown = document.querySelector('.lang-dropdown');
        dropdown.classList.toggle('open');
        // Close account dropdown if open
        document.getElementById('accountDropdownMenu')?.classList.remove('active');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const accountDropdown = document.querySelector('.account-dropdown');
        const accountMenu = document.getElementById('accountDropdownMenu');
        if (accountDropdown && accountMenu && !accountDropdown.contains(e.target)) {
            accountMenu.classList.remove('active');
        }

        const langDropdown = document.querySelector('.lang-dropdown');
        if (langDropdown && !langDropdown.contains(e.target)) {
            langDropdown.classList.remove('open');
        }
    });
    </script>

    <!-- Language Selection Popup - Shows on .com when detected language differs from English -->
    <?php if (isset($domainSwitchPopup) && $domainSwitchPopup && $domainSwitchPopup['show']): ?>
    <div id="languagePopup" class="language-popup" style="display: none;">
        <div class="language-popup-overlay" onclick="dismissLanguagePopup()"></div>
        <div class="language-popup-modal">
            <button class="language-popup-close" onclick="dismissLanguagePopup()">&times;</button>
            <div class="language-popup-content">
                <div class="language-popup-icon">
                    <i class="fas fa-globe-europe"></i>
                </div>

                <h2>We detected you're visiting from <?= htmlspecialchars($domainSwitchPopup['detected_country_name']) ?></h2>
                <p>Would you like to view the website in <?= htmlspecialchars($domainSwitchPopup['detected_lang_native']) ?> (<?= htmlspecialchars($domainSwitchPopup['detected_lang_name']) ?>) or continue in English?</p>

                <div class="language-popup-buttons">
                    <a href="<?= htmlspecialchars($domainSwitchPopup['switch_url']) ?>" class="language-popup-btn language-popup-btn-primary" onclick="setLanguageChoice('<?= $domainSwitchPopup['detected_lang'] ?>')">
                        <?php
                        $langFlags = ['nl' => '🇳🇱', 'de' => '🇩🇪', 'fr' => '🇫🇷'];
                        $flag = $langFlags[$domainSwitchPopup['detected_lang']] ?? '🌐';
                        ?>
                        <span class="lang-flag"><?= $flag ?></span>
                        Continue in <?= htmlspecialchars($domainSwitchPopup['detected_lang_native']) ?>
                    </a>
                    <a href="<?= htmlspecialchars($domainSwitchPopup['stay_url']) ?>" class="language-popup-btn language-popup-btn-secondary" onclick="setLanguageChoice('en')">
                        <span class="lang-flag">🇬🇧</span>
                        Keep English
                    </a>
                </div>

                <p class="language-popup-note">You can always change your language preference in the menu.</p>
            </div>
        </div>
    </div>

    <style>
    .language-popup { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10001; display: flex; align-items: center; justify-content: center; }
    .language-popup-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); }
    .language-popup-modal { position: relative; background: linear-gradient(180deg, #111 0%, #000 100%); border: 1px solid #333; border-radius: 24px; padding: 2.5rem; max-width: 440px; width: 90%; animation: langPopupSlide 0.4s ease; }
    @keyframes langPopupSlide { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .language-popup-close { position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #666; font-size: 1.75rem; cursor: pointer; line-height: 1; transition: color 0.2s; padding: 0.5rem; }
    .language-popup-close:hover { color: #fff; }
    .language-popup-content { text-align: center; }
    .language-popup-icon { margin-bottom: 1.5rem; }
    .language-popup-icon i { font-size: 3.5rem; color: #fff; }
    .language-popup-content h2 { color: #fff; font-size: 1.4rem; margin: 0 0 0.75rem; font-weight: 600; }
    .language-popup-content > p { color: rgba(255,255,255,0.7); margin: 0 0 1.75rem; font-size: 0.95rem; line-height: 1.5; }
    .language-popup-buttons { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem; }
    .language-popup-btn { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 1rem 1.25rem; border-radius: 12px; font-size: 1rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s; border: none; }
    .language-popup-btn .lang-flag { font-size: 1.4rem; }
    .language-popup-btn-primary { background: #fff; color: #000; }
    .language-popup-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,255,255,0.15); }
    .language-popup-btn-secondary { background: transparent; color: #fff; border: 1px solid #444; }
    .language-popup-btn-secondary:hover { background: rgba(255,255,255,0.05); border-color: #666; }
    .language-popup-note { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin: 0; }
    @media (max-width: 480px) {
        .language-popup-modal { padding: 2rem 1.5rem; }
        .language-popup-icon i { font-size: 2.5rem; }
        .language-popup-content h2 { font-size: 1.2rem; }
    }
    </style>

    <script>
    function dismissLanguagePopup() {
        document.getElementById('languagePopup').style.display = 'none';
        // Set cookie to not show again for 30 days
        document.cookie = 'lang_popup_dismissed=1;max-age=' + (30 * 24 * 60 * 60) + ';path=/';
    }

    function setLanguageChoice(lang) {
        // Mark user's explicit choice
        document.cookie = 'lang=' + lang + ';max-age=' + (365 * 24 * 60 * 60) + ';path=/';
        // The link will redirect to the chosen language
    }

    // Show popup after a short delay
    setTimeout(function() {
        const popup = document.getElementById('languagePopup');
        if (popup) popup.style.display = 'flex';
    }, 2000);

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const popup = document.getElementById('languagePopup');
            if (popup && popup.style.display !== 'none') dismissLanguagePopup();
        }
    });
    </script>
    <?php endif; ?>

    <!-- Cookie Consent Banner -->
    <div id="cookieConsent" class="cookie-banner" style="display:none">
        <div class="cookie-content">
            <div class="cookie-header">
                <i class="fas fa-cookie-bite"></i>
                <h3><?= $translations['cookie_title'] ?? 'Cookie instellingen' ?></h3>
            </div>
            <p><?= $translations['cookie_text'] ?? 'Wij gebruiken cookies om je ervaring te verbeteren, gepersonaliseerde content te tonen en ons verkeer te analyseren. Je kunt kiezen welke cookies je accepteert.' ?></p>

            <div class="cookie-options">
                <label class="cookie-option">
                    <input type="checkbox" checked disabled>
                    <span class="cookie-label">
                        <strong><?= $translations['cookie_essential'] ?? 'Essentieel' ?></strong>
                        <small><?= $translations['cookie_essential_desc'] ?? 'Noodzakelijk voor het functioneren van de website' ?></small>
                    </span>
                </label>
                <label class="cookie-option">
                    <input type="checkbox" id="cookieAnalytics">
                    <span class="cookie-label">
                        <strong><?= $translations['cookie_analytics'] ?? 'Analytisch' ?></strong>
                        <small><?= $translations['cookie_analytics_desc'] ?? 'Helpt ons te begrijpen hoe bezoekers onze site gebruiken' ?></small>
                    </span>
                </label>
                <label class="cookie-option">
                    <input type="checkbox" id="cookieMarketing">
                    <span class="cookie-label">
                        <strong><?= $translations['cookie_marketing'] ?? 'Marketing' ?></strong>
                        <small><?= $translations['cookie_marketing_desc'] ?? 'Voor relevante advertenties op basis van je interesses' ?></small>
                    </span>
                </label>
                <label class="cookie-option">
                    <input type="checkbox" id="cookiePersonalization">
                    <span class="cookie-label">
                        <strong><?= $translations['cookie_personalization'] ?? 'Personalisatie' ?></strong>
                        <small><?= $translations['cookie_personalization_desc'] ?? 'Voor gepersonaliseerde aanbevelingen van salons' ?></small>
                    </span>
                </label>
            </div>

            <div class="cookie-buttons">
                <button onclick="acceptAllCookies()" class="cookie-btn cookie-btn-accept">
                    <i class="fas fa-check"></i> <?= $translations['cookie_accept_all'] ?? 'Alles accepteren' ?>
                </button>
                <button onclick="saveSelectedCookies()" class="cookie-btn cookie-btn-save">
                    <?= $translations['cookie_save'] ?? 'Selectie opslaan' ?>
                </button>
                <button onclick="rejectAllCookies()" class="cookie-btn cookie-btn-reject">
                    <?= $translations['cookie_reject'] ?? 'Alleen essentieel' ?>
                </button>
            </div>

            <p class="cookie-footer">
                <a href="/privacy"><?= $translations['privacy'] ?? 'Privacybeleid' ?></a> |
                <a href="/terms"><?= $translations['terms'] ?? 'Voorwaarden' ?></a>
            </p>
        </div>
    </div>

    <style>
    /* Cookie Consent Banner */
    .cookie-banner {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10002;
        padding: 1rem;
    }
    .cookie-content {
        max-width: 500px;
        margin: 0 auto;
        background: #000;
        border: 1px solid #333;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 -10px 40px rgba(0,0,0,0.3);
    }
    .cookie-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .cookie-header i {
        font-size: 1.5rem;
        color: #fbbf24;
    }
    .cookie-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #fff;
    }
    .cookie-content > p {
        color: rgba(255,255,255,0.7);
        font-size: 0.85rem;
        line-height: 1.5;
        margin: 0 0 1rem;
    }
    .cookie-options {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .cookie-option {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.5rem;
        background: rgba(255,255,255,0.05);
        border-radius: 10px;
        cursor: pointer;
    }
    .cookie-option input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: #fff;
    }
    .cookie-label {
        flex: 1;
    }
    .cookie-label strong {
        display: block;
        color: #fff;
        font-size: 0.9rem;
    }
    .cookie-label small {
        color: rgba(255,255,255,0.5);
        font-size: 0.75rem;
    }
    .cookie-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .cookie-btn {
        flex: 1;
        min-width: 120px;
        padding: 0.75rem 1rem;
        border: none;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cookie-btn-accept {
        background: #fff;
        color: #000;
    }
    .cookie-btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255,255,255,0.2);
    }
    .cookie-btn-save {
        background: transparent;
        color: #fff;
        border: 1px solid #444;
    }
    .cookie-btn-save:hover {
        border-color: #666;
        background: rgba(255,255,255,0.05);
    }
    .cookie-btn-reject {
        background: transparent;
        color: rgba(255,255,255,0.6);
        border: 1px solid #333;
    }
    .cookie-btn-reject:hover {
        color: #fff;
        border-color: #444;
    }
    .cookie-footer {
        text-align: center;
        margin: 1rem 0 0;
        font-size: 0.75rem;
    }
    .cookie-footer a {
        color: rgba(255,255,255,0.5);
        text-decoration: none;
    }
    .cookie-footer a:hover {
        color: #fff;
    }
    @media (max-width: 500px) {
        .cookie-content { padding: 1.25rem; }
        .cookie-buttons { flex-direction: column; }
        .cookie-btn { min-width: 100%; }
    }
    </style>

    <script>
    // Cookie Consent Management
    const CookieConsent = {
        init() {
            if (!this.hasConsent()) {
                setTimeout(() => {
                    document.getElementById('cookieConsent').style.display = 'block';
                }, 1500);
            } else {
                // Initialize tracking if consent given
                const prefs = this.getPreferences();
                if (prefs.marketing || prefs.personalization) {
                    AdTracking.init();
                }
            }
        },
        hasConsent() {
            return document.cookie.includes('gs_consent=');
        },
        getPreferences() {
            const match = document.cookie.match(/gs_consent=([^;]+)/);
            if (match) {
                try {
                    return JSON.parse(decodeURIComponent(match[1]));
                } catch (e) {}
            }
            return { essential: true, analytics: false, marketing: false, personalization: false };
        }
    };

    // Advertising & Personalization Tracking
    const AdTracking = {
        data: null,

        init() {
            this.loadData();
            this.trackPageView();
            this.setupEventListeners();
        },

        loadData() {
            const match = document.cookie.match(/gs_ad_profile=([^;]+)/);
            if (match) {
                try {
                    this.data = JSON.parse(decodeURIComponent(match[1]));
                } catch (e) {
                    this.data = this.getDefaultProfile();
                }
            } else {
                this.data = this.getDefaultProfile();
            }
        },

        getDefaultProfile() {
            return {
                interests: [],
                categories: [],
                viewedServices: [],
                viewedBusinesses: [],
                searchQueries: [],
                lastVisit: null,
                visitCount: 0
            };
        },

        saveData() {
            const expires = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
            document.cookie = `gs_ad_profile=${encodeURIComponent(JSON.stringify(this.data))}; expires=${expires}; path=/; SameSite=Lax`;
        },

        trackPageView() {
            this.data.lastVisit = new Date().toISOString();
            this.data.visitCount++;

            // Track category from URL or page content
            const url = window.location.pathname;
            const params = new URLSearchParams(window.location.search);

            // Track search queries
            const query = params.get('q');
            if (query && !this.data.searchQueries.includes(query)) {
                this.data.searchQueries.push(query);
                if (this.data.searchQueries.length > 20) this.data.searchQueries.shift();
            }

            // Track category interest
            const category = params.get('category') || params.get('group');
            if (category && !this.data.categories.includes(category)) {
                this.data.categories.push(category);
                if (this.data.categories.length > 10) this.data.categories.shift();
            }

            // Track business page views
            if (url.startsWith('/salon/') || url.startsWith('/business/')) {
                const slug = url.split('/')[2];
                if (slug && !this.data.viewedBusinesses.includes(slug)) {
                    this.data.viewedBusinesses.push(slug);
                    if (this.data.viewedBusinesses.length > 20) this.data.viewedBusinesses.shift();
                }
            }

            this.saveData();
        },

        setupEventListeners() {
            // Track service clicks
            document.addEventListener('click', (e) => {
                const serviceCard = e.target.closest('[data-service-id]');
                if (serviceCard) {
                    const serviceId = serviceCard.dataset.serviceId;
                    const serviceName = serviceCard.dataset.serviceName || '';
                    if (serviceId && !this.data.viewedServices.includes(serviceId)) {
                        this.data.viewedServices.push(serviceId);
                        if (this.data.viewedServices.length > 30) this.data.viewedServices.shift();
                    }
                    // Extract interest keywords from service name
                    this.extractInterests(serviceName);
                    this.saveData();
                }

                // Track category clicks
                const categoryLink = e.target.closest('[data-category]');
                if (categoryLink) {
                    const cat = categoryLink.dataset.category;
                    if (cat && !this.data.categories.includes(cat)) {
                        this.data.categories.push(cat);
                        if (this.data.categories.length > 10) this.data.categories.shift();
                        this.saveData();
                    }
                }
            });
        },

        extractInterests(text) {
            if (!text) return;
            const keywords = ['haar', 'nagels', 'huid', 'massage', 'makeup', 'beauty', 'wellness',
                            'kapper', 'manicure', 'pedicure', 'facial', 'waxing', 'laser',
                            'botox', 'fillers', 'spray tan', 'sauna', 'spa'];
            const lower = text.toLowerCase();
            keywords.forEach(kw => {
                if (lower.includes(kw) && !this.data.interests.includes(kw)) {
                    this.data.interests.push(kw);
                    if (this.data.interests.length > 15) this.data.interests.shift();
                }
            });
        },

        // Get relevant content recommendations
        getRecommendations() {
            return {
                categories: this.data.categories.slice(-5),
                interests: this.data.interests.slice(-10),
                isFrequentVisitor: this.data.visitCount > 5
            };
        },

        // Clear all tracking data
        clearData() {
            this.data = this.getDefaultProfile();
            document.cookie = 'gs_ad_profile=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        }
    };

    function acceptAllCookies() {
        saveCookiePreferences({
            essential: true,
            analytics: true,
            marketing: true,
            personalization: true
        });
        AdTracking.init();
    }

    function rejectAllCookies() {
        saveCookiePreferences({
            essential: true,
            analytics: false,
            marketing: false,
            personalization: false
        });
        AdTracking.clearData();
    }

    function saveSelectedCookies() {
        const prefs = {
            essential: true,
            analytics: document.getElementById('cookieAnalytics').checked,
            marketing: document.getElementById('cookieMarketing').checked,
            personalization: document.getElementById('cookiePersonalization').checked
        };
        saveCookiePreferences(prefs);
        if (prefs.marketing || prefs.personalization) {
            AdTracking.init();
        } else {
            AdTracking.clearData();
        }
    }

    function saveCookiePreferences(prefs) {
        const expires = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
        document.cookie = `gs_consent=${encodeURIComponent(JSON.stringify(prefs))}; expires=${expires}; path=/; SameSite=Lax`;
        document.getElementById('cookieConsent').style.display = 'none';
    }

    // Initialize cookie consent on page load
    document.addEventListener('DOMContentLoaded', () => {
        CookieConsent.init();
    });
    </script>

    <!-- Security PIN Setup Popup -->
    <?php include BASE_PATH . '/resources/views/components/security-pin-popup.php'; ?>

    <!-- PWA Install Prompt -->
    <?php include BASE_PATH . '/resources/views/components/pwa-install-prompt.php'; ?>
</body>
</html>
