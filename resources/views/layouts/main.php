<!DOCTYPE html>
<?php
$isLoggedIn = isset($_SESSION['user_id']);
$isBusiness = isset($_SESSION['business_id']);
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<html lang="<?= $lang ?? 'nl' ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Glamourschedule' ?> - Booking Platform</title>

    <link rel="icon" type="image/svg+xml" href="/images/gs-logo-circle.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
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
    <!-- Global Search Modal -->
    <div class="global-search-overlay" id="globalSearchOverlay" onclick="closeGlobalSearch()"></div>
    <div class="global-search-modal" id="globalSearchModal">
        <div class="global-search-header">
            <i class="fas fa-search"></i>
            <input type="text" id="globalSearchInput" placeholder="Zoek salons, diensten, pagina's..." autocomplete="off" oninput="handleGlobalSearch(this.value)">
            <button class="global-search-close" onclick="closeGlobalSearch()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="global-search-results" id="globalSearchResults">
            <div class="search-quick-links">
                <div class="search-section-title">Snelle links</div>
                <a href="/search" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-store"></i>
                    <span>Alle Salons Bekijken</span>
                </a>
                <a href="/search?category=kapper" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-cut"></i>
                    <span>Kappers</span>
                </a>
                <a href="/search?category=schoonheid" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-spa"></i>
                    <span>Schoonheidsspecialisten</span>
                </a>
                <a href="/search?category=nagels" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-hand-sparkles"></i>
                    <span>Nagelstudio's</span>
                </a>
                <a href="/search?category=massage" class="search-quick-link" onclick="closeGlobalSearch()">
                    <i class="fas fa-hands"></i>
                    <span>Massage</span>
                </a>
            </div>
            <div class="search-section-title">Pagina's</div>
            <a href="/register?type=business" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-rocket"></i>
                <span>Salon Aanmelden</span>
            </a>
            <a href="/marketing" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-bullhorn"></i>
                <span>Marketing Services</span>
            </a>
            <a href="/about" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-cogs"></i>
                <span>Platform Functionaliteit</span>
            </a>
            <a href="/contact" class="search-quick-link" onclick="closeGlobalSearch()">
                <i class="fas fa-envelope"></i>
                <span>Contact</span>
            </a>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- Mobile Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="sidebar-logo">
                <img src="/images/gs-logo-white.svg" alt="GS" class="sidebar-logo-icon">
                <span>Glamourschedule</span>
            </a>
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
            <a href="/register?type=business" class="sidebar-link">
                <i class="fas fa-rocket"></i> Salon Aanmelden
            </a>
            <a href="/sales/register" class="sidebar-link">
                <i class="fas fa-handshake"></i> Word Partner
            </a>

            <div class="sidebar-divider"></div>

            <!-- Info -->
            <a href="/marketing" class="sidebar-link"><i class="fas fa-bullhorn"></i> Marketing</a>
            <a href="/about" class="sidebar-link"><i class="fas fa-cogs"></i> Functionaliteit</a>
            <a href="/contact" class="sidebar-link"><i class="fas fa-envelope"></i> Contact</a>
        </nav>

        <div class="sidebar-footer">
            <button class="theme-toggle" onclick="toggleTheme()">
                <i class="fas fa-sun theme-icon-light"></i>
                <i class="fas fa-moon theme-icon-dark"></i>
                <span class="theme-toggle-text">Light Mode</span>
            </button>
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
            <img src="/images/gs-logo.svg" alt="GS" class="logo-icon">
            <span class="logo-text">Glamourschedule</span>
        </a>

        <ul class="nav-links-prestige" id="navMenu">
            <li><a href="javascript:void(0)" onclick="openGlobalSearch()" title="<?= $translations['search'] ?? 'Zoeken' ?>"><i class="fas fa-search"></i></a></li>
            <li><a href="/register?type=business">Salon Aanmelden</a></li>
            <li><a href="/sales/register">Word Partner</a></li>
            <li><a href="/sales/login">Sales Portal</a></li>
            <li><a href="/marketing">Marketing</a></li>
            <li><a href="/about">Functionaliteit</a></li>
            <li><a href="/contact">Contact</a></li>

            <?php if (isset($user)): ?>
                <li><a href="/dashboard"><?= $translations['dashboard'] ?? 'Dashboard' ?></a></li>
                <li><a href="/dashboard/bookings"><?= $translations['bookings'] ?? 'Boekingen' ?></a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions-prestige">
            <a href="javascript:void(0)" class="nav-search-mobile" title="Zoeken" onclick="openGlobalSearch()">
                <i class="fas fa-search"></i>
            </a>
            <?php if (isset($user)): ?>
                <div class="account-dropdown">
                    <button class="btn btn-primary account-dropdown-toggle" onclick="toggleAccountDropdown()">
                        <i class="fas fa-user"></i> <?= $translations['my_account'] ?? 'Account' ?> <i class="fas fa-chevron-down" style="font-size:0.7rem;margin-left:0.25rem"></i>
                    </button>
                    <div class="account-dropdown-menu" id="accountDropdownMenu">
                        <a href="/dashboard" class="account-dropdown-item">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="/dashboard/settings" class="account-dropdown-item">
                            <i class="fas fa-cog"></i> Instellingen
                        </a>
                        <a href="/dashboard/bookings" class="account-dropdown-item">
                            <i class="fas fa-calendar-check"></i> Mijn Boekingen
                        </a>
                        <div class="account-dropdown-divider"></div>
                        <a href="/logout" class="account-dropdown-item account-dropdown-logout">
                            <i class="fas fa-sign-out-alt"></i> Uitloggen
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
                <p><?= $translations['footer_desc'] ?? 'Het premium booking platform van Nederland. Vind en boek de beste salons.' ?></p>
            </div>

            <div>
                <h4><?= $translations['platform'] ?? 'Platform' ?></h4>
                <ul>
                    <li><a href="/search"><?= $translations['search'] ?? 'Zoeken' ?></a></li>
                    <li><a href="/register?type=business">Salon Aanmelden</a></li>
                    <li><a href="/sales/login">Sales Portal</a></li>
                </ul>
            </div>

            <div>
                <h4><?= $translations['company'] ?? 'Bedrijf' ?></h4>
                <ul>
                    <li><a href="/about">Functionaliteit</a></li>
                    <li><a href="/marketing">Marketing</a></li>
                    <li><a href="/contact"><?= $translations['contact'] ?? 'Contact' ?></a></li>
                    <li><a href="https://www.kvk.nl/bestellen/#/81973667000048233005?origin=search" target="_blank">KVK: 81973667</a></li>
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
                fetch('/api/global-search?q=' + encodeURIComponent(query))
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
                html += '<div class="search-section-title">Salons</div>';
                data.salons.forEach(salon => {
                    const photo = salon.photos ? '/uploads/businesses/' + salon.id + '/' + salon.photos.split(',')[0] : '/images/placeholder-salon.jpg';
                    html += `
                        <a href="/business/${salon.slug}" class="search-result-item" onclick="closeGlobalSearch()">
                            <img src="${photo}" alt="" class="search-result-avatar" onerror="this.src='/images/placeholder-salon.jpg'">
                            <div class="search-result-info">
                                <span class="search-result-name">${salon.company_name}</span>
                                <span class="search-result-meta"><i class="fas fa-map-marker-alt"></i> ${salon.city || 'Nederland'}</span>
                            </div>
                        </a>
                    `;
                });
            }

            // Services
            if (data.services && data.services.length > 0) {
                html += '<div class="search-section-title">Diensten</div>';
                data.services.forEach(service => {
                    html += `
                        <a href="/business/${service.business_slug}?service=${service.id}" class="search-quick-link" onclick="closeGlobalSearch()">
                            <i class="fas fa-cut"></i>
                            <span>${service.name} <small style="opacity:0.6">bij ${service.business_name}</small></span>
                        </a>
                    `;
                });
            }

            // Pages (static matches)
            const pages = [
                { url: '/search', name: 'Alle Salons', icon: 'fa-store', keywords: ['salon', 'zoek', 'vind', 'all'] },
                { url: '/register?type=business', name: 'Salon Aanmelden', icon: 'fa-rocket', keywords: ['aanmeld', 'registr', 'start', 'salon'] },
                { url: '/marketing', name: 'Marketing Services', icon: 'fa-bullhorn', keywords: ['market', 'reclame', 'promot', 'advert'] },
                { url: '/about', name: 'Platform Functionaliteit', icon: 'fa-cogs', keywords: ['functie', 'feature', 'over', 'about', 'info'] },
                { url: '/contact', name: 'Contact', icon: 'fa-envelope', keywords: ['contact', 'help', 'vraag', 'mail'] },
                { url: '/terms', name: 'Voorwaarden', icon: 'fa-file-contract', keywords: ['voorwaard', 'terms', 'regel'] },
                { url: '/privacy', name: 'Privacy', icon: 'fa-shield-alt', keywords: ['privacy', 'gegeven', 'data'] },
            ];

            const q = query.toLowerCase();
            const matchedPages = pages.filter(p =>
                p.name.toLowerCase().includes(q) ||
                p.keywords.some(k => k.includes(q) || q.includes(k))
            );

            if (matchedPages.length > 0) {
                html += '<div class="search-section-title">Pagina\'s</div>';
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
                        <p>Geen resultaten voor "${query}"</p>
                        <a href="/search?q=${encodeURIComponent(query)}" class="btn btn-primary" style="margin-top:1rem" onclick="closeGlobalSearch()">
                            Zoek in alle salons
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

        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeToggleText(newTheme);
        }

        function updateThemeToggleText(theme) {
            const toggleText = document.querySelector('.theme-toggle-text');
            if (toggleText) {
                toggleText.textContent = theme === 'light' ? 'Dark Mode' : 'Light Mode';
            }
        }

        // Initialize theme on page load
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            updateThemeToggleText(savedTheme);
        })();
    </script>

    <!-- Glamori AI Chatbot -->
    <?php include BASE_PATH . '/resources/views/components/glamori-chat.php'; ?>

    <!-- Early Bird Popup -->
    <div id="earlyBirdPopup" class="early-bird-popup" style="display:none;">
        <div class="early-bird-overlay" onclick="closeEarlyBirdPopup()"></div>
        <div class="early-bird-modal">
            <button class="early-bird-close" onclick="closeEarlyBirdPopup()">&times;</button>
            <div class="early-bird-content">
                <div class="early-bird-badge">
                    <i class="fas fa-star"></i> Early Bird Aanbieding
                </div>
                <h2>Start je salon voor slechts</h2>
                <div class="early-bird-price">
                    <span class="currency">&euro;</span>
                    <span class="amount">0,99</span>
                </div>
                <p class="early-bird-subtitle">Eenmalig voor de eerste 100 bedrijven</p>
                <ul class="early-bird-features">
                    <li><i class="fas fa-check"></i> 14 dagen gratis proberen</li>
                    <li><i class="fas fa-check"></i> Online boekingssysteem</li>
                    <li><i class="fas fa-check"></i> Eigen bedrijfspagina</li>
                    <li><i class="fas fa-check"></i> Geen maandelijkse kosten</li>
                </ul>
                <a href="/register?type=business" class="early-bird-btn">
                    <i class="fas fa-rocket"></i> Nu Aanmelden
                </a>
                <p class="early-bird-note">Daarna &euro;99,99 voor nieuwe bedrijven</p>
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
    </style>

    <script>
    function toggleAccountDropdown() {
        const menu = document.getElementById('accountDropdownMenu');
        menu.classList.toggle('active');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.account-dropdown');
        const menu = document.getElementById('accountDropdownMenu');
        if (dropdown && menu && !dropdown.contains(e.target)) {
            menu.classList.remove('active');
        }
    });
    </script>

    <!-- Domain/Language Switch Popup -->
    <?php if (isset($domainSwitchPopup) && $domainSwitchPopup && $domainSwitchPopup['show']): ?>
    <div id="domainSwitchPopup" class="domain-switch-popup">
        <div class="domain-switch-overlay" onclick="dismissDomainPopup()"></div>
        <div class="domain-switch-modal">
            <button class="domain-switch-close" onclick="dismissDomainPopup()">&times;</button>
            <div class="domain-switch-content">
                <?php
                $countryFlags = ['NL' => '????????', 'BE' => '????????', 'DE' => '????????', 'FR' => '????????', 'GB' => '????????', 'US' => '????????'];
                $countryNames = [
                    'NL' => ['nl' => 'Nederland', 'en' => 'Netherlands', 'de' => 'Niederlande', 'fr' => 'Pays-Bas'],
                    'BE' => ['nl' => 'Belgie', 'en' => 'Belgium', 'de' => 'Belgien', 'fr' => 'Belgique'],
                    'DE' => ['nl' => 'Duitsland', 'en' => 'Germany', 'de' => 'Deutschland', 'fr' => 'Allemagne'],
                    'FR' => ['nl' => 'Frankrijk', 'en' => 'France', 'de' => 'Frankreich', 'fr' => 'France'],
                    'GB' => ['nl' => 'Verenigd Koninkrijk', 'en' => 'United Kingdom', 'de' => 'Vereinigtes Konigreich', 'fr' => 'Royaume-Uni'],
                    'US' => ['nl' => 'Verenigde Staten', 'en' => 'United States', 'de' => 'Vereinigte Staaten', 'fr' => 'Etats-Unis'],
                ];
                $detectedCountry = $domainSwitchPopup['detected_country'];
                $flag = $countryFlags[$detectedCountry] ?? '';
                $countryName = $countryNames[$detectedCountry][$lang] ?? $detectedCountry;
                ?>

                <div class="domain-switch-icon">
                    <span class="domain-switch-flag"><?= $flag ?></span>
                </div>

                <?php if ($domainSwitchPopup['current_domain'] === 'com' && $domainSwitchPopup['suggested_domain'] === 'nl'): ?>
                    <!-- User on .com detected in NL/BE - suggest Dutch site -->
                    <h2 data-i18n="domain_popup_title"><?= $translations['domain_popup_title'] ?? 'We detected you\'re in ' . $countryName ?></h2>
                    <p data-i18n="domain_popup_desc"><?= $translations['domain_popup_desc'] ?? 'Would you like to switch to our Dutch website for content in your language?' ?></p>

                    <div class="domain-switch-buttons">
                        <a href="<?= htmlspecialchars($domainSwitchPopup['switch_url']) ?>?lang=nl" class="domain-switch-btn domain-switch-btn-primary">
                            <span>????????</span> <?= $translations['domain_popup_switch_nl'] ?? 'Go to glamourschedule.nl' ?>
                        </a>
                        <button onclick="stayOnCurrentDomain()" class="domain-switch-btn domain-switch-btn-secondary">
                            <span>????????</span> <?= $translations['domain_popup_stay_en'] ?? 'Stay on English site' ?>
                        </button>
                    </div>

                <?php elseif ($domainSwitchPopup['current_domain'] === 'nl' && $domainSwitchPopup['suggested_domain'] === 'com'): ?>
                    <!-- User on .nl detected outside NL/BE - suggest international site -->
                    <h2><?= $translations['domain_popup_title_int'] ?? 'Looking for the English site?' ?></h2>
                    <p><?= $translations['domain_popup_desc_int'] ?? 'We detected you might prefer the international English version of our website.' ?></p>

                    <div class="domain-switch-buttons">
                        <a href="<?= htmlspecialchars($domainSwitchPopup['switch_url']) ?>?lang=en" class="domain-switch-btn domain-switch-btn-primary">
                            <span>????????</span> <?= $translations['domain_popup_switch_en'] ?? 'Go to glamourschedule.com' ?>
                        </a>
                        <button onclick="stayOnCurrentDomain()" class="domain-switch-btn domain-switch-btn-secondary">
                            <span>????????</span> <?= $translations['domain_popup_stay_nl'] ?? 'Blijf op Nederlandse site' ?>
                        </button>
                    </div>
                <?php endif; ?>

                <p class="domain-switch-note"><?= $translations['domain_popup_note'] ?? 'You can always change your language preference in the menu.' ?></p>
            </div>
        </div>
    </div>

    <style>
    .domain-switch-popup { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10001; display: flex; align-items: center; justify-content: center; }
    .domain-switch-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px); }
    .domain-switch-modal { position: relative; background: linear-gradient(180deg, #111 0%, #000 100%); border: 1px solid #333; border-radius: 24px; padding: 2.5rem; max-width: 440px; width: 90%; animation: domainSlide 0.4s ease; }
    @keyframes domainSlide { from { opacity: 0; transform: translateY(30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .domain-switch-close { position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #666; font-size: 1.75rem; cursor: pointer; line-height: 1; transition: color 0.2s; padding: 0.5rem; }
    .domain-switch-close:hover { color: #fff; }
    .domain-switch-content { text-align: center; }
    .domain-switch-icon { margin-bottom: 1.5rem; }
    .domain-switch-flag { font-size: 4rem; line-height: 1; }
    .domain-switch-content h2 { color: #fff; font-size: 1.4rem; margin: 0 0 0.75rem; font-weight: 600; }
    .domain-switch-content > p { color: rgba(255,255,255,0.7); margin: 0 0 1.75rem; font-size: 0.95rem; line-height: 1.5; }
    .domain-switch-buttons { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem; }
    .domain-switch-btn { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 1rem 1.25rem; border-radius: 12px; font-size: 1rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s; border: none; }
    .domain-switch-btn span { font-size: 1.25rem; }
    .domain-switch-btn-primary { background: #fff; color: #000; }
    .domain-switch-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,255,255,0.15); }
    .domain-switch-btn-secondary { background: transparent; color: #fff; border: 1px solid #444; }
    .domain-switch-btn-secondary:hover { background: rgba(255,255,255,0.05); border-color: #666; }
    .domain-switch-note { color: rgba(255,255,255,0.4); font-size: 0.8rem; margin: 0; }
    @media (max-width: 480px) {
        .domain-switch-modal { padding: 2rem 1.5rem; }
        .domain-switch-flag { font-size: 3rem; }
        .domain-switch-content h2 { font-size: 1.2rem; }
    }
    </style>

    <script>
    function dismissDomainPopup() {
        document.getElementById('domainSwitchPopup').style.display = 'none';
        // Set cookie to not show again for 30 days
        document.cookie = 'domain_popup_dismissed=1;max-age=' + (30 * 24 * 60 * 60) + ';path=/';
    }

    function stayOnCurrentDomain() {
        // Mark user's explicit choice to stay
        document.cookie = 'lang_user_chosen=1;max-age=' + (365 * 24 * 60 * 60) + ';path=/';
        dismissDomainPopup();
    }

    // Show popup after a short delay
    setTimeout(function() {
        const popup = document.getElementById('domainSwitchPopup');
        if (popup) popup.style.display = 'flex';
    }, 2000);

    // Close on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const popup = document.getElementById('domainSwitchPopup');
            if (popup && popup.style.display !== 'none') dismissDomainPopup();
        }
    });
    </script>
    <?php endif; ?>
</body>
</html>
