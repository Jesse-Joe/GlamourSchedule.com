<?php
// Get custom colors from settings
$primaryColor = $settings['primary_color'] ?? '#000000';
$secondaryColor = $settings['secondary_color'] ?? '#1a1a1a';
$accentColor = $settings['accent_color'] ?? '#ffffff';
$showReviews = ($settings['show_reviews'] ?? 1) == 1;
$showPrices = ($settings['show_prices'] ?? 1) == 1;
$showDuration = ($settings['show_duration'] ?? 1) == 1;
$galleryStyle = $settings['gallery_style'] ?? 'grid';
$coverImage = $business['cover_image'] ?? null;
?>
<?php ob_start(); ?>

<style>
    /* ============================================
       THEME VARIABLES - Dark Mode (Default)
       Platform is donker van zichzelf
       ============================================ */
    :root {
        /* Business custom colors */
        --business-primary: <?= htmlspecialchars($primaryColor) ?>;
        --business-secondary: <?= htmlspecialchars($secondaryColor) ?>;
        --business-accent: <?= htmlspecialchars($accentColor) ?>;

        /* Dark Mode Colors (Default) */
        --page-bg: #0a0a0a;
        --card-bg: #111111;
        --card-border: #222222;
        --card-shadow: 0 2px 8px rgba(0,0,0,0.3);

        --text-primary: #ffffff;
        --text-secondary: #a1a1aa;
        --text-muted: #71717a;

        --input-bg: #1a1a1a;
        --input-border: #333333;

        --service-bg: #1a1a1a;
        --service-hover: #222222;
        --service-border: #333333;

        --divider: #333333;

        --btn-primary-bg: #ffffff;
        --btn-primary-text: #000000;
        --btn-secondary-bg: #1f1f1f;
        --btn-secondary-text: #ffffff;
    }

    /* ============================================
       LIGHT MODE VARIABLES
       Witte achtergronden met zwarte tekst
       ============================================ */
    [data-theme="light"] {
        --page-bg: #f8f9fa;
        --card-bg: #ffffff;
        --card-border: #e5e7eb;
        --card-shadow: 0 2px 8px rgba(0,0,0,0.06);

        --text-primary: #111827;
        --text-secondary: #4b5563;
        --text-muted: #9ca3af;

        --input-bg: #ffffff;
        --input-border: #d1d5db;

        --service-bg: #f9fafb;
        --service-hover: #f3f4f6;
        --service-border: #e5e7eb;

        --divider: #e5e7eb;

        --btn-primary-bg: #000000;
        --btn-primary-text: #ffffff;
        --btn-secondary-bg: #f3f4f6;
        --btn-secondary-text: #374151;
    }

    /* ============================================
       BASE PAGE STYLES
       ============================================ */
    .business-page-wrapper {
        padding: 0;
        max-width: 100%;
        background: var(--page-bg);
        min-height: 100vh;
        transition: background-color 0.3s ease;
    }

    /* ============================================
       HERO SECTION - Premium Design
       ============================================ */
    .business-hero {
        position: relative;
        min-height: 320px;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
    }

    .business-hero-bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--business-primary), var(--business-secondary));
    }

    .business-hero-bg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7;
    }

    .business-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top,
            rgba(0,0,0,0.95) 0%,
            rgba(0,0,0,0.7) 40%,
            rgba(0,0,0,0.3) 70%,
            rgba(0,0,0,0.1) 100%
        );
    }

    .business-hero-content {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1.25rem;
    }

    .business-hero-flex {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
    }

    /* Logo - Glassmorphism Style */
    .business-logo {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }

    .business-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .business-hero-info {
        flex: 1;
        min-width: 0;
        color: white;
    }

    .business-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .business-tagline {
        font-size: 0.9rem;
        opacity: 0.85;
        margin-top: 0.4rem;
        font-weight: 400;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .business-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 0.75rem;
        align-items: center;
    }

    .business-location {
        font-size: 0.8rem;
        opacity: 0.8;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .business-rating-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 0.4rem 0.75rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .business-rating-badge .star {
        color: #fbbf24;
    }

    /* Floating Book Button */
    .hero-book-btn {
        position: absolute;
        bottom: 1.5rem;
        right: 1.25rem;
        background: white;
        color: #000;
        padding: 0.875rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        display: none;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 10;
    }

    .hero-book-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    }

    /* Mobile Book Button */
    .mobile-book-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        margin-top: 1rem;
        background: white;
        color: #000;
        padding: 0.875rem;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.2s;
    }

    .mobile-book-btn:active {
        transform: scale(0.98);
    }

    /* ============================================
       BOOK BUTTON - Mobile First
       ============================================ */
    .btn-book {
        background: #ffffff;
        color: var(--business-primary);
        padding: 0.75rem 1.25rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .btn-book:active {
        transform: scale(0.98);
    }
    .btn-book-mobile {
        width: 100%;
        margin-top: 1rem;
    }
    .btn-book-desktop {
        display: none;
    }

    /* ============================================
       MAIN LAYOUT - Mobile First
       ============================================ */
    .business-content {
        padding: 1rem;
    }
    .business-layout {
        display: flex;
        flex-direction: column;
        gap: 0;
        max-width: 1200px;
        margin: 0 auto;
    }
    .business-main {
        order: 1;
    }
    .business-sidebar {
        order: 2;
    }

    /* ============================================
       CARDS - Using Theme Variables
       ============================================ */
    .biz-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--card-border);
        color: var(--text-primary);
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .biz-card h3 {
        font-size: 1rem;
        margin: 0 0 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-primary);
    }
    .biz-card h3 i {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* ============================================
       SERVICES - Using Theme Variables
       ============================================ */
    .service-item {
        display: flex;
        flex-direction: column;
        padding: 0.875rem;
        border-radius: 12px;
        background: var(--service-bg);
        margin-bottom: 0.625rem;
        transition: all 0.2s;
        border: 1px solid var(--service-border);
    }
    .service-item:last-child {
        margin-bottom: 0;
    }
    .service-item:active {
        transform: scale(0.99);
    }
    .service-item:hover {
        background: var(--service-hover);
        border-color: var(--business-primary);
    }
    .service-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .service-info {
        flex: 1;
        min-width: 0;
    }
    .service-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin: 0;
        line-height: 1.3;
        color: var(--text-primary);
    }
    .service-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        margin-top: 0.2rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    .service-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.35rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .service-price {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
    }
    .service-book-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        padding: 0.65rem;
        margin-top: 0.625rem;
        background: var(--btn-primary-bg);
        color: var(--btn-primary-text);
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .service-book-btn:active {
        transform: scale(0.98);
    }
    .service-book-btn:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        opacity: 0.9;
    }

    /* ============================================
       GALLERY COLLAGE - Mobile First
       ============================================ */
    .gallery-collage {
        display: grid;
        gap: 0.35rem;
        margin-top: 0.5rem;
    }

    /* Single image */
    .gallery-collage.single {
        grid-template-columns: 1fr;
    }
    .gallery-collage.single .gallery-item {
        aspect-ratio: 16/9;
    }

    /* 2 images */
    .gallery-collage.duo {
        grid-template-columns: 1fr 1fr;
    }
    .gallery-collage.duo .gallery-item {
        aspect-ratio: 1;
    }

    /* 3 images */
    .gallery-collage.trio {
        grid-template-columns: 2fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    .gallery-collage.trio .gallery-item:first-child {
        grid-row: 1 / 3;
        aspect-ratio: auto;
    }
    .gallery-collage.trio .gallery-item:not(:first-child) {
        aspect-ratio: 1.5;
    }

    /* 4+ images - masonry collage */
    .gallery-collage.multi {
        grid-template-columns: 2fr 1fr 1fr;
        grid-template-rows: 1fr 1fr;
    }
    .gallery-collage.multi .gallery-item:first-child {
        grid-row: 1 / 3;
        aspect-ratio: auto;
    }
    .gallery-collage.multi .gallery-item:not(:first-child) {
        aspect-ratio: 1.3;
    }

    .gallery-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        aspect-ratio: 1;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .gallery-item:active img {
        transform: scale(1.05);
    }
    .gallery-item-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.3s;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 0.75rem;
    }
    .gallery-item:active .gallery-item-overlay {
        opacity: 1;
    }
    .gallery-item-overlay i {
        color: white;
        font-size: 1.25rem;
        background: rgba(255,255,255,0.2);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* More photos indicator */
    .gallery-more {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.6);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    .gallery-more-count {
        font-size: 1.5rem;
        line-height: 1;
    }
    .gallery-more-text {
        font-size: 0.75rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    /* Lightbox Modal */
    .lightbox-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.95);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }
    .lightbox-overlay.active {
        display: flex;
    }
    .lightbox-close {
        position: fixed;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.15);
        border: none;
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 1.25rem;
        cursor: pointer;
        z-index: 2002;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .lightbox-close:active {
        background: rgba(255,255,255,0.3);
    }
    .lightbox-content {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3.5rem 0.5rem 5rem;
    }
    .lightbox-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 8px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .lightbox-image.loaded {
        opacity: 1;
    }
    .lightbox-nav {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.15);
        border: none;
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 1.25rem;
        cursor: pointer;
        z-index: 2002;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .lightbox-nav:active {
        background: rgba(255,255,255,0.3);
    }
    .lightbox-nav.prev {
        left: 0.75rem;
    }
    .lightbox-nav.next {
        right: 0.75rem;
    }
    .lightbox-counter {
        position: fixed;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: 0.9rem;
        background: rgba(0,0,0,0.5);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        z-index: 2002;
    }
    .lightbox-thumbnails {
        position: fixed;
        bottom: 4rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.4rem;
        max-width: 90%;
        overflow-x: auto;
        padding: 0.5rem;
        z-index: 2002;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .lightbox-thumbnails::-webkit-scrollbar {
        display: none;
    }
    .lightbox-thumb {
        width: 50px;
        height: 50px;
        border-radius: 6px;
        overflow: hidden;
        flex-shrink: 0;
        opacity: 0.5;
        transition: opacity 0.2s;
        cursor: pointer;
        border: 2px solid transparent;
    }
    .lightbox-thumb.active {
        opacity: 1;
        border-color: white;
    }
    .lightbox-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* View all gallery button */
    .gallery-view-all {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem;
        margin-top: 0.75rem;
        background: var(--secondary);
        color: var(--text);
        border: none;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .gallery-view-all:active {
        background: var(--business-primary);
        color: white;
    }
    .gallery-view-all i {
        font-size: 0.85rem;
    }

    /* ============================================
       REVIEWS - Using Theme Variables
       ============================================ */
    .review-card {
        padding: 0.875rem 0;
        border-bottom: 1px solid var(--divider);
    }
    .review-card:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
    }
    .review-author {
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .review-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--btn-primary-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--btn-primary-text);
        font-weight: 600;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .review-author-info strong {
        display: block;
        font-size: 0.85rem;
        color: var(--text-primary);
    }
    .review-stars {
        color: #fbbf24;
        font-size: 0.7rem;
    }
    .review-date {
        font-size: 0.7rem;
        color: var(--text-muted);
        white-space: nowrap;
    }
    .review-text {
        margin: 0.625rem 0 0;
        font-size: 0.85rem;
        line-height: 1.5;
        color: var(--text-secondary);
    }
    .review-response {
        margin-top: 0.625rem;
        padding: 0.625rem;
        background: var(--service-bg);
        border-radius: 8px;
        border-left: 3px solid var(--business-primary);
        font-size: 0.8rem;
    }
    .response-header {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.35rem;
    }
    .response-header i {
        color: var(--text-secondary);
        font-size: 0.75rem;
    }
    .response-header strong {
        font-size: 0.75rem;
        color: var(--text-primary);
    }
    .review-response p {
        margin: 0;
        font-size: 0.8rem;
        line-height: 1.4;
        color: var(--text-secondary);
    }

    /* ============================================
       RATING SUMMARY - Using Theme Variables
       ============================================ */
    .rating-summary {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.875rem;
        background: var(--service-bg);
        border-radius: 12px;
        margin-bottom: 0.75rem;
    }
    .rating-big {
        text-align: center;
        min-width: 70px;
    }
    .rating-big-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }
    .rating-big .review-stars {
        margin-top: 0.25rem;
    }
    .rating-big small {
        display: block;
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
    .rating-bars {
        flex: 1;
    }
    .rating-bar {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-bottom: 0.2rem;
        font-size: 0.7rem;
        color: var(--text-secondary);
    }
    .rating-bar:last-child {
        margin-bottom: 0;
    }
    .rating-bar-label {
        width: 12px;
        text-align: center;
    }
    .rating-bar i {
        color: #fbbf24;
        font-size: 0.6rem;
    }
    .rating-bar-track {
        flex: 1;
        height: 5px;
        background: var(--divider);
        border-radius: 3px;
        overflow: hidden;
    }
    .rating-bar-fill {
        height: 100%;
        background: #fbbf24;
        border-radius: 3px;
    }

    /* ============================================
       OPENING HOURS - Using Theme Variables
       ============================================ */
    .hours-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .hours-item {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        border-bottom: 1px solid var(--divider);
        font-size: 0.85rem;
        color: var(--text-primary);
    }
    .hours-item:last-child {
        border-bottom: none;
    }
    .hours-item.today {
        font-weight: 600;
        color: var(--business-primary);
    }
    .hours-closed {
        color: var(--text-muted);
    }

    /* ============================================
       CONTACT - Using Theme Variables
       ============================================ */
    .contact-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--divider);
    }
    .contact-section h4 {
        font-size: 0.9rem;
        margin: 0 0 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--text-primary);
    }
    .contact-section h4 i {
        color: var(--text-secondary);
        font-size: 0.85rem;
    }
    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.4rem 0;
        text-decoration: none;
        color: var(--text-secondary);
        font-size: 0.85rem;
        transition: color 0.2s;
    }
    .contact-item:hover {
        color: var(--text-primary);
    }
    .contact-item i {
        width: 18px;
        color: var(--text-muted);
        text-align: center;
        font-size: 0.85rem;
    }

    /* ============================================
       ABOUT & MISC - Using Theme Variables
       ============================================ */
    .welcome-msg {
        font-size: 0.95rem;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-weight: 500;
        line-height: 1.4;
    }
    .about-text {
        line-height: 1.6;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    .empty-text {
        color: var(--text-muted);
        padding: 0.75rem 0;
        font-size: 0.85rem;
    }
    .empty-reviews {
        text-align: center;
        padding: 1.5rem 0;
        color: var(--text-muted);
    }
    .empty-reviews i {
        font-size: 1.75rem;
        opacity: 0.3;
        display: block;
        margin-bottom: 0.4rem;
        color: var(--text-muted);
    }
    .empty-reviews p {
        margin: 0;
        font-size: 0.85rem;
    }

    /* ============================================
       SOCIAL LINKS - Using Theme Variables
       ============================================ */
    .social-section {
        margin-top: 0.875rem;
        padding-top: 0.875rem;
        border-top: 1px solid var(--divider);
    }
    .social-links {
        display: flex;
        gap: 0.4rem;
    }
    .social-link {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--btn-secondary-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--btn-secondary-text);
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .social-link:active, .social-link:hover {
        background: var(--btn-primary-bg);
        color: var(--btn-primary-text);
    }

    /* ============================================
       SIDEBAR BOOK BUTTON - Using Theme Variables
       ============================================ */
    .sidebar-book-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        padding: 0.75rem;
        margin-top: 1rem;
        background: var(--btn-primary-bg);
        color: var(--btn-primary-text);
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .sidebar-book-btn:active {
        transform: scale(0.98);
    }
    .sidebar-book-btn:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        opacity: 0.9;
    }

    /* ============================================
       TABLET STYLES (iPad Mini & Regular)
       ============================================ */
    @media (min-width: 600px) {
        .business-header {
            padding: 1.5rem;
        }
        .business-header-flex {
            gap: 1rem;
        }
        .business-logo {
            width: 85px;
            height: 85px;
            border-radius: 16px;
            font-size: 2rem;
        }
        .business-name {
            font-size: 1.5rem;
        }
        .business-tagline {
            font-size: 0.9rem;
        }
        .business-location {
            font-size: 0.85rem;
        }
        .business-rating {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
        }
        .business-rating .stars i {
            font-size: 0.75rem;
        }
        .business-content {
            padding: 1.25rem;
        }
        .biz-card {
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-radius: 18px;
        }
        .biz-card h3 {
            font-size: 1.1rem;
        }
        .service-item {
            padding: 1rem;
        }
        .service-name {
            font-size: 1rem;
        }
        .service-desc {
            font-size: 0.85rem;
        }
        .service-price {
            font-size: 1.1rem;
        }
        .service-book-btn {
            font-size: 0.9rem;
            padding: 0.75rem;
        }
        .gallery-collage {
            gap: 0.5rem;
        }
        .gallery-item {
            border-radius: 12px;
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        .gallery-item:hover .gallery-item-overlay {
            opacity: 1;
        }
        .lightbox-thumb {
            width: 60px;
            height: 60px;
        }
        .gallery-view-all:hover {
            background: var(--business-primary);
            color: white;
        }
        .review-avatar {
            width: 40px;
            height: 40px;
            font-size: 0.9rem;
        }
        .review-author-info strong {
            font-size: 0.9rem;
        }
        .review-text {
            font-size: 0.9rem;
        }
        .rating-big-number {
            font-size: 2.25rem;
        }
    }

    /* ============================================
       TABLET LANDSCAPE & LARGER (iPad Pro)
       ============================================ */
    @media (min-width: 768px) {
        .business-header {
            padding: 2rem;
        }
        .business-header-flex {
            gap: 1.25rem;
            position: relative;
            padding-right: 180px;
        }
        .business-logo {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }
        .business-name {
            font-size: 1.75rem;
        }
        .business-tagline {
            font-size: 1rem;
            -webkit-line-clamp: 3;
        }
        .business-location {
            font-size: 0.9rem;
        }
        .business-rating {
            font-size: 0.9rem;
            padding: 0.4rem 0.875rem;
        }
        .btn-book-mobile {
            display: none;
        }
        .btn-book-desktop {
            display: inline-flex;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
        }
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }
        .btn-book-desktop:hover {
            transform: translateY(calc(-50% - 2px));
        }
        .business-content {
            padding: 1.5rem;
        }
        .business-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.5rem;
        }
        .biz-card {
            padding: 1.5rem;
            border-radius: 20px;
        }
        .biz-card h3 {
            font-size: 1.15rem;
            margin-bottom: 1rem;
        }
        .service-item {
            flex-direction: row;
            align-items: center;
            padding: 1.25rem;
            gap: 1rem;
        }
        .service-header {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .service-info {
            flex: 1;
            min-width: 0;
        }
        .service-price {
            flex-shrink: 0;
        }
        .service-book-btn {
            width: auto;
            margin-top: 0;
            padding: 0.625rem 1.25rem;
            flex-shrink: 0;
        }
        .service-book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .gallery-collage {
            gap: 0.625rem;
        }
        .gallery-collage.multi {
            grid-template-columns: 1.5fr 1fr 1fr;
        }
        .gallery-item {
            border-radius: 14px;
        }
        .lightbox-nav {
            width: 50px;
            height: 50px;
        }
        .lightbox-nav.prev {
            left: 1.5rem;
        }
        .lightbox-nav.next {
            right: 1.5rem;
        }
        .lightbox-content {
            padding: 4rem 4rem 6rem;
        }
        .sticky-sidebar {
            position: sticky;
            top: 1rem;
        }
        .social-link:hover {
            background: var(--business-primary);
            color: white;
        }
        .sidebar-book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    }

    /* ============================================
       DESKTOP STYLES
       ============================================ */
    @media (min-width: 1024px) {
        .business-header {
            padding: 2.5rem;
        }
        .business-header-flex {
            padding-right: 200px;
        }
        .business-logo {
            width: 110px;
            height: 110px;
        }
        .business-name {
            font-size: 2rem;
        }
        .business-content {
            padding: 2rem;
        }
        .business-layout {
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }
        .gallery-collage.multi {
            grid-template-columns: 1.4fr 1fr 1fr;
        }
        .lightbox-thumb {
            width: 70px;
            height: 70px;
        }
    }

    /* ============================================
       LARGE DESKTOP
       ============================================ */
    @media (min-width: 1200px) {
        .business-name {
            font-size: 2.25rem;
        }
        .business-layout {
            grid-template-columns: 1fr 380px;
        }
    }

    /* ============================================
       GALLERY VIEW ALL - Using Theme Variables
       ============================================ */
    .gallery-view-all {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem;
        margin-top: 0.75rem;
        background: var(--btn-secondary-bg);
        color: var(--btn-secondary-text);
        border: none;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .gallery-view-all:hover {
        background: var(--btn-primary-bg);
        color: var(--btn-primary-text);
    }
</style>

<div class="business-page-wrapper">
    <!-- Hero Section with Cover Image -->
    <section class="business-hero">
        <div class="business-hero-bg">
            <?php if (!empty($coverImage)): ?>
                <img src="<?= htmlspecialchars($coverImage) ?>" alt="<?= htmlspecialchars($business['name']) ?>">
            <?php endif; ?>
        </div>
        <div class="business-hero-overlay"></div>

        <div class="business-hero-content">
            <div class="business-hero-flex">
                <div class="business-logo">
                    <?php if (!empty($business['logo'])): ?>
                        <img src="<?= htmlspecialchars($business['logo']) ?>" alt="<?= htmlspecialchars($business['name']) ?>">
                    <?php else: ?>
                        <i class="fas fa-spa"></i>
                    <?php endif; ?>
                </div>
                <div class="business-hero-info">
                    <h1 class="business-name"><?= htmlspecialchars($business['name']) ?></h1>
                    <?php if (!empty($settings['tagline'])): ?>
                        <p class="business-tagline"><?= htmlspecialchars($settings['tagline']) ?></p>
                    <?php endif; ?>
                    <div class="business-meta">
                        <span class="business-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($business['address']) ?><?php if (!empty($business['house_number'])): ?> <?= htmlspecialchars($business['house_number']) ?><?php endif; ?>, <?= htmlspecialchars($business['city']) ?>
                        </span>
                        <?php if ($business['avg_rating'] > 0): ?>
                            <span class="business-rating-badge">
                                <i class="fas fa-star star"></i>
                                <span><?= number_format($business['avg_rating'], 1) ?></span>
                                <span style="opacity:0.7">(<?= $business['review_count'] ?>)</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="mobile-book-btn">
                <i class="fas fa-calendar-plus"></i> Boek Nu
            </a>
        </div>

        <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="hero-book-btn">
            <i class="fas fa-calendar-plus"></i> Reserveer Nu
        </a>
    </section>

    <div class="business-content">
        <div class="business-layout">
            <div class="business-main">
                <!-- Welcome Message / About -->
                <?php if (!empty($settings['welcome_message']) || !empty($settings['about_text'])): ?>
                    <div class="biz-card">
                        <h3><i class="fas fa-info-circle"></i> <?= htmlspecialchars($settings['about_title'] ?? 'Over Ons') ?></h3>
                        <?php if (!empty($settings['welcome_message'])): ?>
                            <p class="welcome-msg"><?= htmlspecialchars($settings['welcome_message']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($settings['about_text'])): ?>
                            <p class="about-text"><?= nl2br(htmlspecialchars($settings['about_text'])) ?></p>
                        <?php elseif (!empty($business['description'])): ?>
                            <p class="about-text"><?= nl2br(htmlspecialchars($business['description'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php elseif (!empty($business['description'])): ?>
                    <div class="biz-card">
                        <h3><i class="fas fa-info-circle"></i> Over Ons</h3>
                        <p class="about-text"><?= nl2br(htmlspecialchars($business['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Gallery Collage -->
                <?php if (!empty($images)):
                    $imageCount = count($images);
                    $collageClass = 'single';
                    if ($imageCount == 2) $collageClass = 'duo';
                    elseif ($imageCount == 3) $collageClass = 'trio';
                    elseif ($imageCount >= 4) $collageClass = 'multi';
                    $maxShow = min(5, $imageCount);
                    $hiddenCount = $imageCount - $maxShow;
                ?>
                    <div class="biz-card">
                        <h3><i class="fas fa-images"></i> Foto's (<?= $imageCount ?>)</h3>
                        <div class="gallery-collage <?= $collageClass ?>">
                            <?php foreach (array_slice($images, 0, $maxShow) as $index => $image): ?>
                                <div class="gallery-item" onclick="openLightbox(<?= $index ?>)">
                                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($image['alt_text'] ?? 'Foto ' . ($index + 1)) ?>" loading="lazy">
                                    <div class="gallery-item-overlay">
                                        <i class="fas fa-expand"></i>
                                    </div>
                                    <?php if ($index == $maxShow - 1 && $hiddenCount > 0): ?>
                                        <div class="gallery-more">
                                            <span class="gallery-more-count">+<?= $hiddenCount ?></span>
                                            <span class="gallery-more-text">meer foto's</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($imageCount > 1): ?>
                            <button class="gallery-view-all" onclick="openLightbox(0)">
                                <i class="fas fa-th"></i> Bekijk alle <?= $imageCount ?> foto's
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Services -->
                <div class="biz-card">
                    <h3><i class="fas fa-cut"></i> Onze Diensten</h3>
                    <?php if (empty($services)): ?>
                        <p class="empty-text">Nog geen diensten toegevoegd.</p>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <div class="service-item">
                                <div class="service-header">
                                    <div class="service-info">
                                        <h4 class="service-name"><?= htmlspecialchars($service['name']) ?></h4>
                                        <?php if (!empty($service['description'])): ?>
                                            <p class="service-desc"><?= htmlspecialchars($service['description']) ?></p>
                                        <?php endif; ?>
                                        <div class="service-meta">
                                            <?php if ($showDuration): ?>
                                                <span><i class="fas fa-clock"></i> <?= $service['duration_minutes'] ?> min</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($showPrices): ?>
                                        <span class="service-price">&euro;<?= number_format($service['price'], 2, ',', '.') ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="/book/<?= htmlspecialchars($business['slug']) ?>?service=<?= $service['id'] ?>" class="service-book-btn">
                                    <i class="fas fa-calendar-plus"></i> Boeken
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Reviews -->
                <?php if ($showReviews): ?>
                    <div class="biz-card">
                        <h3><i class="fas fa-star"></i> Reviews</h3>

                        <!-- Rating Summary -->
                        <?php if (($reviewStats['total'] ?? 0) > 0): ?>
                            <div class="rating-summary">
                                <div class="rating-big">
                                    <div class="rating-big-number"><?= number_format($reviewStats['average'] ?? 0, 1) ?></div>
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= round($reviewStats['average'] ?? 0) ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small><?= $reviewStats['total'] ?? 0 ?> reviews</small>
                                </div>
                                <div class="rating-bars">
                                    <?php
                                    $total = max(1, $reviewStats['total']);
                                    for ($star = 5; $star >= 1; $star--):
                                        $count = $reviewStats[['', 'one', 'two', 'three', 'four', 'five'][$star] . '_star'] ?? 0;
                                        $percentage = ($count / $total) * 100;
                                    ?>
                                        <div class="rating-bar">
                                            <span class="rating-bar-label"><?= $star ?></span>
                                            <i class="fas fa-star"></i>
                                            <div class="rating-bar-track">
                                                <div class="rating-bar-fill" style="width:<?= $percentage ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($reviews)): ?>
                            <div class="empty-reviews">
                                <i class="fas fa-star"></i>
                                <p>Nog geen reviews</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-card">
                                    <div class="review-header">
                                        <div class="review-author">
                                            <div class="review-avatar">
                                                <?= strtoupper(substr($review['first_name'] ?? 'G', 0, 1)) ?>
                                            </div>
                                            <div class="review-author-info">
                                                <strong><?= htmlspecialchars(($review['first_name'] ?? 'Gast') . ' ' . substr($review['last_name'] ?? '', 0, 1) . '.') ?></strong>
                                                <div class="review-stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="review-date"><?= date('d-m-Y', strtotime($review['created_at'])) ?></span>
                                    </div>
                                    <?php if (!empty($review['comment'])): ?>
                                        <p class="review-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($review['business_response'])): ?>
                                        <div class="review-response">
                                            <div class="response-header">
                                                <i class="fas fa-reply"></i>
                                                <strong>Reactie van <?= htmlspecialchars($business['name']) ?></strong>
                                            </div>
                                            <p><?= nl2br(htmlspecialchars($review['business_response'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="business-sidebar">
                <div class="biz-card sticky-sidebar">
                    <h3><i class="fas fa-clock"></i> Openingstijden</h3>
                    <ul class="hours-list">
                        <?php
                        $dayKeys = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $dayNames = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];
                        foreach ($dayKeys as $i => $dayKey):
                            $h = $hours[$i] ?? null;
                            $isToday = date('N') - 1 == $i;
                        ?>
                            <li class="hours-item <?= $isToday ? 'today' : '' ?>">
                                <span><?= $dayNames[$i] ?></span>
                                <span class="<?= (!$h || $h['closed']) ? 'hours-closed' : '' ?>">
                                    <?php if ($h && !$h['closed']): ?>
                                        <?= substr($h['open'], 0, 5) ?> - <?= substr($h['close'], 0, 5) ?>
                                    <?php else: ?>
                                        Gesloten
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Contact Info -->
                    <div class="contact-section">
                        <h4><i class="fas fa-address-card"></i> Contact</h4>
                        <?php if (!empty($business['phone'])): ?>
                            <a href="tel:<?= htmlspecialchars($business['phone']) ?>" class="contact-item">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($business['phone']) ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($business['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($business['email']) ?>" class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <?= htmlspecialchars($business['email']) ?>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($business['website'])): ?>
                            <a href="<?= htmlspecialchars($business['website']) ?>" target="_blank" class="contact-item">
                                <i class="fas fa-globe"></i>
                                Website
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Social Links -->
                    <?php if (!empty($settings['facebook_url']) || !empty($settings['instagram_url']) || !empty($settings['twitter_url']) || !empty($settings['tiktok_url'])): ?>
                        <div class="social-section">
                            <div class="social-links">
                                <?php if (!empty($settings['facebook_url'])): ?>
                                    <a href="<?= htmlspecialchars($settings['facebook_url']) ?>" target="_blank" class="social-link">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($settings['instagram_url'])): ?>
                                    <a href="<?= htmlspecialchars($settings['instagram_url']) ?>" target="_blank" class="social-link">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($settings['twitter_url'])): ?>
                                    <a href="<?= htmlspecialchars($settings['twitter_url']) ?>" target="_blank" class="social-link">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($settings['tiktok_url'])): ?>
                                    <a href="<?= htmlspecialchars($settings['tiktok_url']) ?>" target="_blank" class="social-link">
                                        <i class="fab fa-tiktok"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="sidebar-book-btn">
                        <i class="fas fa-calendar-alt"></i> Maak een Afspraak
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($images)): ?>
<!-- Lightbox Modal -->
<div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">
        <i class="fas fa-times"></i>
    </button>
    <button class="lightbox-nav prev" onclick="prevImage()">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="lightbox-nav next" onclick="nextImage()">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="lightbox-content" onclick="closeLightbox()">
        <img class="lightbox-image" id="lightboxImage" src="" alt="" onclick="event.stopPropagation()">
    </div>
    <div class="lightbox-counter" id="lightboxCounter">1 / <?= count($images) ?></div>
    <div class="lightbox-thumbnails" id="lightboxThumbs">
        <?php foreach ($images as $i => $img): ?>
            <div class="lightbox-thumb <?= $i === 0 ? 'active' : '' ?>" onclick="goToImage(<?= $i ?>)">
                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="">
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const galleryImages = <?= json_encode(array_map(function($img) {
    return [
        'src' => $img['image_path'],
        'alt' => $img['alt_text'] ?? ''
    ];
}, $images)) ?>;

let currentImageIndex = 0;
let touchStartX = 0;
let touchEndX = 0;

function openLightbox(index) {
    currentImageIndex = index;
    showImage(index);
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = '';
}

function showImage(index) {
    const img = document.getElementById('lightboxImage');
    img.classList.remove('loaded');
    img.src = galleryImages[index].src;
    img.alt = galleryImages[index].alt;
    img.onload = () => img.classList.add('loaded');

    document.getElementById('lightboxCounter').textContent = (index + 1) + ' / ' + galleryImages.length;

    // Update thumbnails
    document.querySelectorAll('.lightbox-thumb').forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });

    // Scroll active thumbnail into view
    const activeThumb = document.querySelector('.lightbox-thumb.active');
    if (activeThumb) {
        activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    showImage(currentImageIndex);
}

function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    showImage(currentImageIndex);
}

function goToImage(index) {
    currentImageIndex = index;
    showImage(index);
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox.classList.contains('active')) return;

    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
});

// Touch swipe support
document.getElementById('lightbox').addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
}, false);

document.getElementById('lightbox').addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
}, false);

function handleSwipe() {
    const diff = touchStartX - touchEndX;
    if (Math.abs(diff) > 50) {
        if (diff > 0) nextImage();
        else prevImage();
    }
}
</script>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
