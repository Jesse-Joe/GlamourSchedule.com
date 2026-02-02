<?php
/**
 * BUSINESS PAGE TEMPLATE
 * ======================
 * Layout: Customizable via business theme settings
 * Theme: Follows user preference (dark/light toggle)
 * Accents: Uses business colors if set, otherwise default
 */

// Business accent color from settings
$rawAccentColor = $settings['primary_color'] ?? '';
$secondaryColor = $settings['secondary_color'] ?? '#333333';

// Helper function to calculate color luminance (0-255)
function getColorLuminance($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    if (strlen($hex) !== 6) return 128; // Default mid-range
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return (0.299 * $r + 0.587 * $g + 0.114 * $b);
}

// Determine accent colors for both themes
$defaultDarkAccent = '#ffffff';
$defaultLightAccent = '#000000';

$accentLuminance = !empty($rawAccentColor) ? getColorLuminance($rawAccentColor) : -1;

// For dark theme: accent should be bright (luminance > 50)
$darkThemeAccent = ($accentLuminance > 50) ? $rawAccentColor : $defaultDarkAccent;

// For light theme: accent should be dark (luminance < 200)
$lightThemeAccent = ($accentLuminance >= 0 && $accentLuminance < 200) ? $rawAccentColor : $defaultLightAccent;

// Display settings
$showReviews = ($settings['show_reviews'] ?? 1) == 1;
$showPrices = ($settings['show_prices'] ?? 1) == 1;
$showDuration = ($settings['show_duration'] ?? 1) == 1;

// Theme settings
$layoutTemplate = $settings['layout_template'] ?? 'classic';
$fontFamily = $settings['font_family'] ?? 'playfair';
$fontStyle = $settings['font_style'] ?? 'elegant';
$buttonStyle = $settings['button_style'] ?? 'rounded';
$headerStyle = $settings['header_style'] ?? 'gradient';
$galleryStyle = $settings['gallery_style'] ?? 'grid';
$customCss = $settings['custom_css'] ?? '';

// Font mapping
$fontFamilyMap = [
    'playfair' => "'Playfair Display', serif",
    'cormorant' => "'Cormorant Garamond', serif",
    'lora' => "'Lora', serif",
    'montserrat' => "'Montserrat', sans-serif",
    'poppins' => "'Poppins', sans-serif",
    'dancing' => "'Dancing Script', cursive",
    'great-vibes' => "'Great Vibes', cursive",
    'raleway' => "'Raleway', sans-serif",
];
$headingFont = $fontFamilyMap[$fontFamily] ?? $fontFamilyMap['playfair'];

// Button border radius
$buttonRadiusMap = [
    'rounded' => '25px',
    'square' => '4px',
    'pill' => '50px',
    'sharp' => '0',
];
$buttonRadius = $buttonRadiusMap[$buttonStyle] ?? '25px';

// Images
$coverImage = $business['cover_image'] ?? $business['banner_image'] ?? null;
$logoUrl = $business['logo'] ?? null;

// Contact info
$businessPhone = $business['phone'] ?? null;
$businessEmail = $business['email'] ?? null;
$businessWebsite = $business['website'] ?? null;

// First photo as fallback cover if no cover image set
if (!$coverImage && !empty($images)) {
    $coverImage = $images[0]['image_path'] ?? null;
}
?>
<?php ob_start(); ?>

<!-- Google Fonts for theme -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Dancing+Script:wght@400;500;600;700&family=Great+Vibes&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* =============================================
   BUSINESS PAGE - Theme Aware + Custom Settings
   ============================================= */

/* ========== DARK THEME (Default) ========== */
.biz-page,
[data-theme="dark"] .biz-page {
    --accent: <?= htmlspecialchars($darkThemeAccent) ?>;
    --accent-text: #000000;
    --secondary: <?= htmlspecialchars($secondaryColor) ?>;

    --bg: #000000;
    --bg-elevated: #0a0a0a;
    --surface: #111111;
    --surface-hover: #1a1a1a;

    --border: #222222;
    --border-light: #333333;

    --text: #ffffff;
    --text-secondary: #a0a0a0;
    --text-muted: #666666;

    --hero-overlay: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.6) 40%, rgba(0,0,0,0.2) 100%);
    --map-filter: grayscale(100%) invert(100%) contrast(90%);

    --heading-font: <?= $headingFont ?>;
    --button-radius: <?= $buttonRadius ?>;
}

/* ========== LIGHT THEME ========== */
[data-theme="light"] .biz-page {
    --accent: <?= htmlspecialchars($lightThemeAccent) ?>;
    --accent-text: #ffffff;
    --secondary: <?= htmlspecialchars($secondaryColor) ?>;

    --bg: #ffffff;
    --bg-elevated: #f8f8f8;
    --surface: #ffffff;
    --surface-hover: #f5f5f5;

    --border: #e0e0e0;
    --border-light: #f0f0f0;

    --text: #000000;
    --text-secondary: #555555;
    --text-muted: #888888;

    --hero-overlay: linear-gradient(to top, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.6) 40%, rgba(255,255,255,0.2) 100%);
    --map-filter: none;

    --heading-font: <?= $headingFont ?>;
    --button-radius: <?= $buttonRadius ?>;
}

/* ========== FONT STYLES ========== */
.biz-title,
.biz-card-title,
.biz-service-name {
    font-family: var(--heading-font);
}

<?php if ($fontStyle === 'elegant'): ?>
.biz-title { font-style: italic; }
<?php elseif ($fontStyle === 'bold'): ?>
.biz-title { font-weight: 800; }
<?php elseif ($fontStyle === 'light'): ?>
.biz-title { font-weight: 300; }
<?php endif; ?>

/* ========== BUTTON STYLES ========== */
.biz-nav-cta,
.biz-float-btn {
    border-radius: var(--button-radius);
}

/* ========== HEADER STYLES ========== */
<?php if ($headerStyle === 'solid'): ?>
.biz-hero-bg {
    background: var(--accent) !important;
}
.biz-hero-bg img {
    display: none;
}
<?php elseif ($headerStyle === 'transparent'): ?>
.biz-hero {
    min-height: 200px;
    height: auto;
}
.biz-hero-bg {
    background: transparent !important;
}
.biz-hero-bg img {
    display: none;
}
.biz-hero-overlay {
    background: none !important;
}
.biz-tagline,
.biz-location-hero {
    color: var(--text-secondary);
}
<?php elseif ($headerStyle === 'image'): ?>
.biz-hero-bg {
    background: #000 !important;
}
.biz-hero-bg img {
    opacity: 1;
}
<?php endif; ?>

/* ========== GALLERY STYLES ========== */
<?php if ($galleryStyle === 'carousel'): ?>
.biz-gallery {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    gap: 8px;
    padding-bottom: 12px;
    scrollbar-width: thin;
    scrollbar-color: var(--accent) var(--border);
}
.biz-gallery::-webkit-scrollbar {
    height: 6px;
}
.biz-gallery::-webkit-scrollbar-track {
    background: var(--border);
    border-radius: 3px;
}
.biz-gallery::-webkit-scrollbar-thumb {
    background: var(--accent);
    border-radius: 3px;
}
.biz-gallery-item {
    flex: 0 0 280px;
    scroll-snap-align: start;
    aspect-ratio: 4/3;
    border-radius: 12px;
}
.biz-gallery-item:first-child {
    grid-column: auto;
    grid-row: auto;
}
@media (min-width: 768px) {
    .biz-gallery-item {
        flex: 0 0 350px;
    }
}
<?php elseif ($galleryStyle === 'masonry'): ?>
.biz-gallery {
    display: block;
    columns: 2;
    column-gap: 4px;
}
.biz-gallery-item {
    break-inside: avoid;
    margin-bottom: 4px;
    aspect-ratio: auto;
    border-radius: 8px;
}
.biz-gallery-item:first-child {
    grid-column: auto;
    grid-row: auto;
}
.biz-gallery-item img {
    width: 100%;
    height: auto;
}
@media (min-width: 768px) {
    .biz-gallery {
        columns: 3;
    }
}
<?php endif; ?>

/* ========== LAYOUT: SIDEBAR ========== */
<?php if ($layoutTemplate === 'sidebar'): ?>
@media (min-width: 768px) {
    .biz-grid {
        grid-template-columns: 340px 1fr;
    }
    .biz-sidebar {
        order: -1;
    }
    .biz-main {
        order: 1;
    }
}
<?php endif; ?>

/* ========== LAYOUT: HERO ========== */
<?php if ($layoutTemplate === 'hero'): ?>
.biz-hero {
    height: 60vh;
    min-height: 450px;
    max-height: 600px;
}
.biz-title {
    font-size: 2.5rem;
}
@media (min-width: 768px) {
    .biz-title {
        font-size: 3.5rem;
    }
}
<?php endif; ?>

/* ========== LAYOUT: MINIMAL ========== */
<?php if ($layoutTemplate === 'minimal'): ?>
.biz-hero {
    height: auto;
    min-height: 200px;
    padding: 2rem 0;
}
.biz-hero-bg img {
    display: none;
}
.biz-hero-overlay {
    background: none;
}
.biz-logo {
    display: none;
}
.biz-badges {
    display: none;
}
.biz-nav {
    display: none;
}
.biz-tagline,
.biz-location-hero {
    color: var(--text-secondary);
}
<?php endif; ?>

/* ========== LAYOUT: CARDS ========== */
<?php if ($layoutTemplate === 'cards'): ?>
.biz-services {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1rem;
    padding: 1rem;
}
.biz-service {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem !important;
    border: 1px solid var(--border) !important;
    border-radius: 16px;
    background: var(--surface);
    transition: all 0.3s;
}
.biz-service:hover {
    border-color: var(--accent) !important;
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
}
.biz-service-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    border-radius: 16px;
}
.biz-service-info {
    text-align: center;
}
.biz-service-meta {
    justify-content: center;
}
.biz-service-right {
    flex-direction: column;
    margin-top: 1rem;
    gap: 0.5rem;
}
.biz-service-price {
    font-size: 1.5rem;
}
.biz-service-arrow {
    display: none;
}
<?php endif; ?>

/* ========== LAYOUT: MAGAZINE ========== */
<?php if ($layoutTemplate === 'magazine'): ?>
@media (min-width: 768px) {
    .biz-grid {
        display: block;
    }
    .biz-main {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .biz-main > .biz-card:first-child {
        grid-column: span 2;
    }
    .biz-sidebar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .biz-sidebar .biz-card + .biz-card {
        margin-top: 0;
    }
}
<?php endif; ?>

/* ========== CUSTOM CSS ========== */
<?= $customCss ?>

* { box-sizing: border-box; }

.biz-page {
    background: var(--bg);
    min-height: 100vh;
    color: var(--text);
}

/* =============================================
   HERO - Immersive Full Width
   ============================================= */
.biz-hero {
    position: relative;
    height: 45vh;
    min-height: 350px;
    max-height: 500px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
}

.biz-hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 70%, #000) 100%);
}

.biz-hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.85;
}

.biz-hero-overlay {
    position: absolute;
    inset: 0;
    background: var(--hero-overlay);
}

.biz-hero-content {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    padding: 2rem 1.25rem;
}

.biz-hero-row {
    display: flex;
    align-items: flex-end;
    gap: 1.25rem;
}

/* Logo */
.biz-logo {
    width: 85px;
    height: 85px;
    border-radius: 18px;
    background: var(--surface);
    border: 3px solid var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 8px 30px rgba(0,0,0,0.5);
}

.biz-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.biz-logo i {
    font-size: 2rem;
    color: var(--accent);
}

/* Hero Info */
.biz-hero-info {
    flex: 1;
    min-width: 0;
}

.biz-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.6rem;
}

.biz-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.7rem;
    background: rgba(255,255,255,0.1);
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.biz-badge-verified {
    background: rgba(34, 197, 94, 0.2);
    color: #4ade80;
}

.biz-badge-rating {
    background: rgba(251, 191, 36, 0.2);
    color: #fbbf24;
}

.biz-badge-rating i { color: #fbbf24; }

.biz-title {
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
    line-height: 1.15;
    letter-spacing: -0.02em;
}

.biz-tagline {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.75);
    margin: 0.4rem 0 0;
}

.biz-location-hero {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.6rem;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.7);
}

.biz-location-hero i { color: var(--accent); }

/* =============================================
   STICKY NAVIGATION
   ============================================= */
.biz-nav {
    position: sticky;
    top: 0;
    z-index: 100;
    background: var(--bg-elevated);
    border-bottom: 1px solid var(--border);
}

.biz-nav-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 56px;
}

.biz-nav-links {
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: none;
}

.biz-nav-links::-webkit-scrollbar { display: none; }

.biz-nav-link {
    padding: 0.5rem 0.9rem;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 500;
    border-radius: 8px;
    white-space: nowrap;
    transition: all 0.2s;
}

.biz-nav-link:hover {
    color: var(--text);
    background: var(--surface);
}

.biz-nav-link.active {
    color: var(--accent);
    background: var(--surface);
}

.biz-nav-link i {
    margin-right: 0.35rem;
    font-size: 0.75rem;
}

.biz-nav-cta {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: var(--accent);
    color: var(--accent-text);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.8rem;
    border-radius: 10px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.biz-nav-cta:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
}

.biz-nav-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Theme Toggle Button */
.biz-theme-toggle {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.biz-theme-toggle:hover {
    background: var(--surface-hover);
    border-color: var(--accent);
}

/* Show sun icon in dark mode, moon icon in light mode */
.biz-theme-light { display: inline-block; }
.biz-theme-dark { display: none; }

[data-theme="light"] .biz-theme-light { display: none; }
[data-theme="light"] .biz-theme-dark { display: inline-block; }

/* =============================================
   MAIN CONTENT
   ============================================= */
.biz-content {
    max-width: 1100px;
    margin: 0 auto;
}

.biz-grid {
    display: grid;
    grid-template-columns: 1fr;
}

.biz-main {
    display: flex;
    flex-direction: column;
}

.biz-sidebar {
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
}

/* Sidebar cards spacing */
.biz-sidebar .biz-card + .biz-card {
    margin-top: 1rem;
}

/* =============================================
   CARDS
   ============================================= */
.biz-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
}

.biz-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.biz-card-title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.biz-card-title i {
    color: var(--accent);
    font-size: 0.9rem;
}

.biz-card-body {
    padding: 1.25rem;
}

.biz-card-count {
    font-size: 0.75rem;
    color: var(--text-muted);
    background: var(--bg);
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
}

/* =============================================
   GALLERY - Grid Style
   ============================================= */
.biz-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    border-radius: 12px;
    overflow: hidden;
}

.biz-gallery-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    cursor: pointer;
    background: var(--bg);
}

.biz-gallery-item:first-child {
    grid-column: span 2;
    grid-row: span 2;
}

.biz-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.biz-gallery-item:hover img {
    transform: scale(1.05);
}

.biz-gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.biz-gallery-item:hover .biz-gallery-overlay {
    opacity: 1;
}

.biz-gallery-overlay i {
    color: #fff;
    font-size: 1.25rem;
}

.biz-gallery-more {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.75);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.biz-gallery-more span:first-child {
    font-size: 1.75rem;
    font-weight: 700;
}

.biz-gallery-more span:last-child {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* =============================================
   SERVICES
   ============================================= */
.biz-services {
    display: flex;
    flex-direction: column;
}

.biz-service {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.2s;
    text-decoration: none;
    color: inherit;
}

.biz-service:last-child {
    border-bottom: none;
}

.biz-service:hover {
    background: var(--surface-hover);
}

.biz-service-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.biz-service-icon i {
    color: var(--accent-text);
    font-size: 1.1rem;
}

.biz-service-info {
    flex: 1;
    min-width: 0;
}

.biz-service-name {
    font-weight: 600;
    font-size: 0.95rem;
    margin: 0;
}

.biz-service-meta {
    display: flex;
    gap: 0.6rem;
    margin-top: 0.2rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.biz-service-desc {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-top: 0.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.biz-service-right {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

.biz-service-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--accent);
}

.biz-service-arrow {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    font-size: 0.75rem;
    transition: all 0.2s;
}

.biz-service:hover .biz-service-arrow {
    background: var(--accent);
    color: var(--accent-text);
}

/* =============================================
   ABOUT
   ============================================= */
.biz-about {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.7;
    margin: 0;
}

/* =============================================
   CONTACT
   ============================================= */
.biz-contact-list {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.biz-contact-item {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.9rem;
    background: var(--bg);
    border-radius: 12px;
    text-decoration: none;
    color: var(--text);
    transition: all 0.2s;
    border: 1px solid transparent;
}

.biz-contact-item:hover {
    border-color: var(--accent);
    transform: translateX(4px);
}

.biz-contact-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.biz-contact-icon i {
    color: var(--accent-text);
    font-size: 0.9rem;
}

.biz-contact-info {
    flex: 1;
    min-width: 0;
}

.biz-contact-label {
    font-size: 0.7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.biz-contact-value {
    font-weight: 600;
    font-size: 0.9rem;
    margin-top: 0.1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Mini Map */
.biz-map {
    height: 160px;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 1rem;
    border: 1px solid var(--border);
}

.biz-map iframe {
    width: 100%;
    height: 100%;
    border: none;
    filter: var(--map-filter);
}

/* =============================================
   HOURS
   ============================================= */
.biz-hours-row {
    display: flex;
    justify-content: space-between;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}

.biz-hours-row:last-child {
    border-bottom: none;
}

.biz-hours-day {
    color: var(--text);
}

.biz-hours-row.today .biz-hours-day {
    color: var(--accent);
    font-weight: 600;
}

.biz-hours-time {
    color: var(--text-secondary);
}

.biz-hours-time.closed {
    color: #ef4444;
}

/* =============================================
   REVIEWS
   ============================================= */
.biz-reviews-summary {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.25rem;
    background: var(--bg);
    border-radius: 12px;
    margin-bottom: 1rem;
}

.biz-reviews-score {
    text-align: center;
    padding-right: 1.5rem;
    border-right: 1px solid var(--border);
}

.biz-reviews-number {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
    color: var(--accent);
}

.biz-reviews-stars {
    color: #fbbf24;
    margin-top: 0.4rem;
    font-size: 0.85rem;
}

.biz-reviews-count {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 0.2rem;
}

.biz-reviews-bars {
    flex: 1;
}

.biz-reviews-bar {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.35rem;
    font-size: 0.75rem;
}

.biz-reviews-bar-label {
    width: 16px;
    color: var(--text-muted);
}

.biz-reviews-bar-track {
    flex: 1;
    height: 6px;
    background: var(--border);
    border-radius: 3px;
    overflow: hidden;
}

.biz-reviews-bar-fill {
    height: 100%;
    background: var(--accent);
    border-radius: 3px;
}

/* Review Card */
.biz-review {
    padding: 1rem 0;
    border-bottom: 1px solid var(--border);
}

.biz-review:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.biz-review-header {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    margin-bottom: 0.6rem;
}

.biz-review-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--accent-text);
    font-weight: 700;
    font-size: 0.9rem;
}

.biz-review-info { flex: 1; }

.biz-review-name {
    font-weight: 600;
    font-size: 0.9rem;
}

.biz-review-date {
    font-size: 0.75rem;
    color: var(--text-muted);
}

.biz-review-rating {
    color: #fbbf24;
    font-size: 0.8rem;
}

.biz-review-text {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.6;
}

/* =============================================
   FLOATING BUTTON (Mobile)
   ============================================= */
.biz-float-btn {
    position: fixed;
    bottom: 1.25rem;
    left: 1rem;
    right: 1rem;
    z-index: 90;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.9rem;
    background: var(--accent);
    color: var(--accent-text);
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
}

/* =============================================
   LIGHTBOX
   ============================================= */
.biz-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    background: rgba(0,0,0,0.95);
    align-items: center;
    justify-content: center;
}

.biz-lightbox.active { display: flex; }

.biz-lightbox-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: none;
    color: #fff;
    font-size: 1.25rem;
    cursor: pointer;
}

.biz-lightbox-img {
    max-width: 90%;
    max-height: 85vh;
    border-radius: 8px;
}

.biz-lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: none;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
}

.biz-lightbox-prev { left: 1rem; }
.biz-lightbox-next { right: 1rem; }

.biz-lightbox-counter {
    position: absolute;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 0.85rem;
    background: rgba(0,0,0,0.6);
    padding: 0.4rem 0.9rem;
    border-radius: 16px;
}

/* Empty state */
.biz-empty {
    text-align: center;
    padding: 2rem;
    color: var(--text-muted);
}

.biz-empty i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.4;
}

/* =============================================
   MOBILE OPTIMIZATIONS (Default Mobile First)
   ============================================= */
/* Better spacing for mobile cards */
.biz-card + .biz-card {
    margin-top: 1rem;
}

.biz-card-body {
    padding: 1rem;
}

.biz-card-header {
    padding: 0.875rem 1rem;
}

/* Mobile service items */
.biz-service {
    padding: 0.875rem 1rem;
    gap: 0.75rem;
}

.biz-service-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
}

.biz-service-icon i {
    font-size: 1rem;
}

/* Mobile reviews */
.biz-reviews-summary {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
}

.biz-reviews-score {
    padding-right: 0;
    padding-bottom: 1rem;
    border-right: none;
    border-bottom: 1px solid var(--border);
}

.biz-reviews-bars {
    width: 100%;
}

/* Mobile contact items */
.biz-contact-item {
    padding: 0.75rem;
}

.biz-contact-icon {
    width: 36px;
    height: 36px;
}

/* Mobile gallery */
.biz-gallery {
    grid-template-columns: repeat(2, 1fr);
    gap: 3px;
    border-radius: 10px;
}

.biz-gallery-item:first-child {
    grid-column: span 2;
    grid-row: span 1;
    aspect-ratio: 16/9;
}

/* Mobile about text */
.biz-about {
    font-size: 0.9rem;
    line-height: 1.6;
}

/* Mobile hours */
.biz-hours-row {
    padding: 0.5rem 0;
    font-size: 0.85rem;
}

/* Mobile reviews */
.biz-review {
    padding: 0.75rem 0;
}

.biz-review-header {
    gap: 0.6rem;
    margin-bottom: 0.5rem;
}

.biz-review-avatar {
    width: 36px;
    height: 36px;
    font-size: 0.85rem;
}

.biz-review-text {
    font-size: 0.85rem;
    line-height: 1.55;
}

/* Mobile empty state */
.biz-empty {
    padding: 1.5rem;
}

.biz-empty i {
    font-size: 1.75rem;
}

.biz-empty p {
    font-size: 0.85rem;
    margin: 0.5rem 0 0;
}

/* Mobile hero adjustments */
.biz-hero {
    height: 40vh;
    min-height: 280px;
}

.biz-hero-content {
    padding: 1.5rem 1rem;
}

.biz-logo {
    width: 70px;
    height: 70px;
    border-radius: 14px;
    border-width: 2px;
}

.biz-title {
    font-size: 1.5rem;
}

.biz-tagline {
    font-size: 0.85rem;
}

.biz-badges {
    gap: 0.4rem;
    margin-bottom: 0.5rem;
}

.biz-badge {
    padding: 0.3rem 0.6rem;
    font-size: 0.65rem;
}

/* Mobile nav */
.biz-nav-actions {
    gap: 0.4rem;
}

.biz-theme-toggle {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
}

.biz-nav-inner {
    height: 52px;
    padding: 0 0.75rem;
}

.biz-nav-link {
    padding: 0.4rem 0.7rem;
    font-size: 0.75rem;
}

.biz-nav-cta {
    padding: 0.5rem 0.9rem;
    font-size: 0.75rem;
}

/* Mobile content area */
.biz-content {
    padding: 1rem 0.75rem 5rem;
}

.biz-grid {
    gap: 0;
}

/* Mobile floating button */
.biz-float-btn {
    bottom: 1rem;
    left: 0.75rem;
    right: 0.75rem;
    padding: 0.85rem;
    font-size: 0.9rem;
    border-radius: 12px;
}

/* =============================================
   RESPONSIVE - TABLET AND UP
   ============================================= */
@media (min-width: 768px) {
    .biz-hero {
        height: 50vh;
        min-height: 400px;
    }

    .biz-hero-content {
        padding: 2rem 1.25rem;
    }

    .biz-logo {
        width: 100px;
        height: 100px;
        border-radius: 18px;
        border-width: 3px;
    }

    .biz-title {
        font-size: 2.25rem;
    }

    .biz-tagline {
        font-size: 0.95rem;
    }

    .biz-badges {
        gap: 0.5rem;
        margin-bottom: 0.6rem;
    }

    .biz-badge {
        padding: 0.35rem 0.7rem;
        font-size: 0.7rem;
    }

    .biz-nav-inner {
        height: 56px;
        padding: 0 1rem;
    }

    .biz-nav-link {
        padding: 0.5rem 0.9rem;
        font-size: 0.8rem;
    }

    .biz-nav-cta {
        padding: 0.55rem 1.1rem;
        font-size: 0.8rem;
    }

    .biz-nav-actions {
        gap: 0.5rem;
    }

    .biz-theme-toggle {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
    }

    .biz-content {
        padding: 2rem;
    }

    .biz-grid {
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
    }

    .biz-card + .biz-card {
        margin-top: 1.5rem;
    }

    .biz-card-body {
        padding: 1.25rem;
    }

    .biz-card-header {
        padding: 1rem 1.25rem;
    }

    .biz-service {
        padding: 1rem 1.25rem;
        gap: 1rem;
    }

    .biz-service-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
    }

    .biz-service-icon i {
        font-size: 1.1rem;
    }

    .biz-reviews-summary {
        flex-direction: row;
        gap: 1.5rem;
        padding: 1.25rem;
    }

    .biz-reviews-score {
        padding-right: 1.5rem;
        padding-bottom: 0;
        border-right: 1px solid var(--border);
        border-bottom: none;
    }

    .biz-contact-item {
        padding: 0.9rem;
    }

    .biz-contact-icon {
        width: 40px;
        height: 40px;
    }

    .biz-gallery {
        grid-template-columns: repeat(3, 1fr);
        gap: 4px;
        border-radius: 12px;
    }

    .biz-gallery-item:first-child {
        grid-column: span 2;
        grid-row: span 2;
        aspect-ratio: 1;
    }

    .biz-hours-row {
        padding: 0.6rem 0;
        font-size: 0.9rem;
    }

    .biz-about {
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .biz-review {
        padding: 1rem 0;
    }

    .biz-review-header {
        gap: 0.7rem;
        margin-bottom: 0.6rem;
    }

    .biz-review-avatar {
        width: 40px;
        height: 40px;
        font-size: 0.9rem;
    }

    .biz-review-text {
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .biz-empty {
        padding: 2rem;
    }

    .biz-empty i {
        font-size: 2rem;
    }

    .biz-empty p {
        font-size: 0.9rem;
    }

    .biz-float-btn {
        display: none;
    }

    .biz-sidebar {
        margin-top: 0;
    }

    .biz-sidebar .biz-card + .biz-card {
        margin-top: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .biz-title {
        font-size: 2.5rem;
    }

    .biz-sidebar {
        position: sticky;
        top: 76px;
        align-self: start;
    }
}
</style>

<div class="biz-page biz-layout-<?= htmlspecialchars($layoutTemplate) ?>" data-layout="<?= htmlspecialchars($layoutTemplate) ?>" data-gallery="<?= htmlspecialchars($galleryStyle) ?>">
    <!-- Hero -->
    <section class="biz-hero">
        <div class="biz-hero-bg">
            <?php if ($coverImage): ?>
                <img src="<?= htmlspecialchars($coverImage) ?>" alt="<?= htmlspecialchars($business['name']) ?>">
            <?php endif; ?>
        </div>
        <div class="biz-hero-overlay"></div>

        <div class="biz-hero-content">
            <div class="biz-hero-row">
                <div class="biz-logo">
                    <?php if ($logoUrl): ?>
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-store"></i>
                    <?php endif; ?>
                </div>
                <div class="biz-hero-info">
                    <div class="biz-badges">
                        <?php if ($business['is_verified'] ?? false): ?>
                            <span class="biz-badge biz-badge-verified"><i class="fas fa-check-circle"></i> Geverifieerd</span>
                        <?php endif; ?>
                        <?php if ($business['avg_rating'] > 0): ?>
                            <span class="biz-badge biz-badge-rating">
                                <i class="fas fa-star"></i> <?= number_format($business['avg_rating'], 1) ?> (<?= $business['review_count'] ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="biz-title"><?= htmlspecialchars($business['name']) ?></h1>
                    <?php if (!empty($settings['tagline'])): ?>
                        <p class="biz-tagline"><?= htmlspecialchars($settings['tagline']) ?></p>
                    <?php endif; ?>
                    <span class="biz-location-hero">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($business['city']) ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Nav -->
    <nav class="biz-nav">
        <div class="biz-nav-inner">
            <div class="biz-nav-links">
                <a href="#diensten" class="biz-nav-link active"><i class="fas fa-cut"></i> Diensten</a>
                <?php if (!empty($images)): ?>
                    <a href="#fotos" class="biz-nav-link"><i class="fas fa-images"></i> Foto's</a>
                <?php endif; ?>
                <?php if ($showReviews && !empty($reviews)): ?>
                    <a href="#reviews" class="biz-nav-link"><i class="fas fa-star"></i> Reviews</a>
                <?php endif; ?>
                <a href="#contact" class="biz-nav-link"><i class="fas fa-phone"></i> Contact</a>
            </div>
            <div class="biz-nav-actions">
                <button class="biz-theme-toggle" onclick="toggleBizTheme()" title="Toggle theme">
                    <i class="fas fa-sun biz-theme-light"></i>
                    <i class="fas fa-moon biz-theme-dark"></i>
                </button>
                <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="biz-nav-cta">
                    <i class="fas fa-calendar-plus"></i> Boeken
                </a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="biz-content">
        <div class="biz-grid">
            <!-- Main -->
            <div class="biz-main">
                <!-- About -->
                <?php if (!empty($settings['about_text']) || !empty($business['description'])): ?>
                <div class="biz-card">
                    <div class="biz-card-header">
                        <h2 class="biz-card-title"><i class="fas fa-info-circle"></i> Over ons</h2>
                    </div>
                    <div class="biz-card-body">
                        <p class="biz-about"><?= nl2br(htmlspecialchars($settings['about_text'] ?? $business['description'])) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Gallery -->
                <?php if (!empty($images)):
                    $imgCount = count($images);
                    // Carousel shows all images, grid/masonry show limited
                    $maxShow = ($galleryStyle === 'carousel') ? $imgCount : min(5, $imgCount);
                ?>
                <div class="biz-card" id="fotos">
                    <div class="biz-card-header">
                        <h2 class="biz-card-title"><i class="fas fa-images"></i> Foto's</h2>
                        <span class="biz-card-count"><?= $imgCount ?></span>
                    </div>
                    <div class="biz-card-body" style="padding:0.5rem">
                        <div class="biz-gallery biz-gallery-<?= htmlspecialchars($galleryStyle) ?>">
                            <?php foreach (array_slice($images, 0, $maxShow) as $i => $img): ?>
                                <div class="biz-gallery-item" onclick="openLightbox(<?= $i ?>)">
                                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="" loading="lazy">
                                    <?php if ($galleryStyle === 'grid' && $i == $maxShow - 1 && $imgCount > $maxShow): ?>
                                        <div class="biz-gallery-more">
                                            <span>+<?= $imgCount - $maxShow ?></span>
                                            <span>meer</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="biz-gallery-overlay"><i class="fas fa-search-plus"></i></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Services -->
                <div class="biz-card" id="diensten">
                    <div class="biz-card-header">
                        <h2 class="biz-card-title"><i class="fas fa-cut"></i> Diensten</h2>
                        <?php if (!empty($services)): ?>
                            <span class="biz-card-count"><?= count($services) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($services)): ?>
                        <div class="biz-card-body">
                            <div class="biz-empty">
                                <i class="fas fa-cut"></i>
                                <p>Nog geen diensten</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="biz-services">
                            <?php foreach ($services as $svc): ?>
                                <a href="/book/<?= htmlspecialchars($business['slug']) ?>?service=<?= $svc['id'] ?>" class="biz-service">
                                    <div class="biz-service-icon">
                                        <i class="fas fa-cut"></i>
                                    </div>
                                    <div class="biz-service-info">
                                        <h3 class="biz-service-name"><?= htmlspecialchars($svc['name']) ?></h3>
                                        <div class="biz-service-meta">
                                            <?php if ($showDuration): ?>
                                                <span><i class="fas fa-clock"></i> <?= $svc['duration_minutes'] ?> min</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($svc['description'])): ?>
                                            <p class="biz-service-desc"><?= htmlspecialchars($svc['description']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="biz-service-right">
                                        <?php if ($showPrices): ?>
                                            <span class="biz-service-price">&euro;<?= number_format($svc['price'], 0) ?></span>
                                        <?php endif; ?>
                                        <span class="biz-service-arrow"><i class="fas fa-chevron-right"></i></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reviews -->
                <?php if ($showReviews): ?>
                <div class="biz-card" id="reviews">
                    <div class="biz-card-header">
                        <h2 class="biz-card-title"><i class="fas fa-star"></i> Reviews</h2>
                        <?php if (!empty($reviews)): ?>
                            <span class="biz-card-count"><?= count($reviews) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="biz-card-body">
                        <?php if (($reviewStats['total'] ?? 0) > 0): ?>
                            <div class="biz-reviews-summary">
                                <div class="biz-reviews-score">
                                    <div class="biz-reviews-number"><?= number_format($reviewStats['average'] ?? 0, 1) ?></div>
                                    <div class="biz-reviews-stars">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <i class="fas fa-star<?= $s <= round($reviewStats['average'] ?? 0) ? '' : '-half-alt' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="biz-reviews-count"><?= $reviewStats['total'] ?> reviews</div>
                                </div>
                                <div class="biz-reviews-bars">
                                    <?php for ($star = 5; $star >= 1; $star--):
                                        $cnt = $reviewStats[['', 'one', 'two', 'three', 'four', 'five'][$star] . '_star'] ?? 0;
                                        $pct = ($cnt / max(1, $reviewStats['total'])) * 100;
                                    ?>
                                        <div class="biz-reviews-bar">
                                            <span class="biz-reviews-bar-label"><?= $star ?></span>
                                            <div class="biz-reviews-bar-track">
                                                <div class="biz-reviews-bar-fill" style="width:<?= $pct ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($reviews)): ?>
                            <div class="biz-empty">
                                <i class="fas fa-star"></i>
                                <p>Nog geen reviews</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($reviews, 0, 5) as $rev): ?>
                                <div class="biz-review">
                                    <div class="biz-review-header">
                                        <div class="biz-review-avatar"><?= strtoupper(substr($rev['customer_name'] ?? 'A', 0, 1)) ?></div>
                                        <div class="biz-review-info">
                                            <div class="biz-review-name"><?= htmlspecialchars($rev['customer_name'] ?? 'Anoniem') ?></div>
                                            <div class="biz-review-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></div>
                                        </div>
                                        <div class="biz-review-rating">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <i class="fas fa-star<?= $s <= $rev['rating'] ? '' : '-o' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($rev['comment'])): ?>
                                        <p class="biz-review-text"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="biz-sidebar">
                <!-- Contact -->
                <div class="biz-card" id="contact">
                    <div class="biz-card-header">
                        <h2 class="biz-card-title"><i class="fas fa-address-card"></i> Contact</h2>
                    </div>
                    <div class="biz-card-body">
                        <div class="biz-contact-list">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($business['address'] . ' ' . ($business['house_number'] ?? '') . ', ' . $business['city']) ?>" target="_blank" class="biz-contact-item">
                                <div class="biz-contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="biz-contact-info">
                                    <div class="biz-contact-label">Adres</div>
                                    <div class="biz-contact-value"><?= htmlspecialchars($business['address']) ?> <?= htmlspecialchars($business['house_number'] ?? '') ?>, <?= htmlspecialchars($business['city']) ?></div>
                                </div>
                            </a>
                            <?php if ($businessPhone): ?>
                                <a href="tel:<?= htmlspecialchars($businessPhone) ?>" class="biz-contact-item">
                                    <div class="biz-contact-icon"><i class="fas fa-phone"></i></div>
                                    <div class="biz-contact-info">
                                        <div class="biz-contact-label">Telefoon</div>
                                        <div class="biz-contact-value"><?= htmlspecialchars($businessPhone) ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                            <?php if ($businessEmail): ?>
                                <a href="mailto:<?= htmlspecialchars($businessEmail) ?>" class="biz-contact-item">
                                    <div class="biz-contact-icon"><i class="fas fa-envelope"></i></div>
                                    <div class="biz-contact-info">
                                        <div class="biz-contact-label">E-mail</div>
                                        <div class="biz-contact-value"><?= htmlspecialchars($businessEmail) ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($business['latitude']) && !empty($business['longitude'])): ?>
                            <div class="biz-map">
                                <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=<?= $business['longitude'] - 0.008 ?>,<?= $business['latitude'] - 0.004 ?>,<?= $business['longitude'] + 0.008 ?>,<?= $business['latitude'] + 0.004 ?>&layer=mapnik&marker=<?= $business['latitude'] ?>,<?= $business['longitude'] ?>" loading="lazy"></iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hours -->
                <?php if (!empty($hours)): ?>
                <div class="biz-card">
                    <div class="biz-card-header">
                        <h2 class="biz-card-title"><i class="fas fa-clock"></i> Openingstijden</h2>
                    </div>
                    <div class="biz-card-body">
                        <?php
                        $days = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];
                        $today = date('N') - 1;
                        foreach ($hours as $idx => $hr):
                            // Cast to int to handle string '0' and '1' from database
                            $isClosed = (int)($hr['closed'] ?? $hr['is_closed'] ?? 0) === 1;
                        ?>
                            <div class="biz-hours-row <?= $idx === $today ? 'today' : '' ?>">
                                <span class="biz-hours-day"><?= $hr['day'] ?? ($days[$idx] ?? '') ?></span>
                                <?php if ($isClosed): ?>
                                    <span class="biz-hours-time closed">Gesloten</span>
                                <?php else: ?>
                                    <span class="biz-hours-time"><?= substr($hr['open'], 0, 5) ?> - <?= substr($hr['close'], 0, 5) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Float Button -->
    <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="biz-float-btn">
        <i class="fas fa-calendar-plus"></i> Nu Boeken
    </a>
</div>

<!-- Lightbox -->
<?php if (!empty($images)): ?>
<div class="biz-lightbox" id="lightbox">
    <button class="biz-lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
    <button class="biz-lightbox-nav biz-lightbox-prev" onclick="prevImg()"><i class="fas fa-chevron-left"></i></button>
    <img src="" alt="" class="biz-lightbox-img" id="lbImg">
    <button class="biz-lightbox-nav biz-lightbox-next" onclick="nextImg()"><i class="fas fa-chevron-right"></i></button>
    <div class="biz-lightbox-counter" id="lbCounter"></div>
</div>
<script>
const imgs = <?= json_encode(array_map(fn($i) => $i['image_path'], $images)) ?>;
let idx = 0;
function openLightbox(i) {
    idx = i;
    showImg();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = '';
}
function showImg() {
    document.getElementById('lbImg').src = imgs[idx];
    document.getElementById('lbCounter').textContent = (idx + 1) + ' / ' + imgs.length;
}
function nextImg() { idx = (idx + 1) % imgs.length; showImg(); }
function prevImg() { idx = (idx - 1 + imgs.length) % imgs.length; showImg(); }
document.getElementById('lightbox').onclick = e => { if (e.target.id === 'lightbox') closeLightbox(); };
document.onkeydown = e => {
    if (!document.getElementById('lightbox').classList.contains('active')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextImg();
    if (e.key === 'ArrowLeft') prevImg();
};
document.querySelectorAll('.biz-nav-link').forEach(a => {
    a.onclick = e => {
        if (a.getAttribute('href').startsWith('#')) {
            e.preventDefault();
            document.querySelector(a.getAttribute('href'))?.scrollIntoView({behavior:'smooth'});
            document.querySelectorAll('.biz-nav-link').forEach(l => l.classList.remove('active'));
            a.classList.add('active');
        }
    };
});
</script>
<?php endif; ?>

<!-- Theme Toggle Script -->
<script>
function toggleBizTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme') || 'dark';
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('glamour_theme_mode', newTheme);

    // Sync with main theme manager if available
    if (window.GlamourTheme) {
        window.GlamourTheme.setMode(newTheme);
    }
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
