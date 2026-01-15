<?php
// Get custom colors from settings
$primaryColor = $settings['primary_color'] ?? '#000000';
$secondaryColor = $settings['secondary_color'] ?? '#333333';
$accentColor = $settings['accent_color'] ?? '#000000';
$showReviews = ($settings['show_reviews'] ?? 1) == 1;
$showPrices = ($settings['show_prices'] ?? 1) == 1;
$showDuration = ($settings['show_duration'] ?? 1) == 1;
$galleryStyle = $settings['gallery_style'] ?? 'grid';
?>
<?php ob_start(); ?>

<style>
    :root {
        --business-primary: <?= htmlspecialchars($primaryColor) ?>;
        --business-secondary: <?= htmlspecialchars($secondaryColor) ?>;
        --business-accent: <?= htmlspecialchars($accentColor) ?>;
    }

    /* Reset container padding for business page */
    .business-page-wrapper {
        padding: 0;
        max-width: 100%;
    }

    /* ============================================
       BUSINESS HEADER - Mobile First
       ============================================ */
    .business-header {
        background: linear-gradient(135deg, var(--business-primary), var(--business-accent));
        color: white;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .business-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.3;
    }
    .business-header-content {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
    }
    .business-header-flex {
        display: flex;
        gap: 0.875rem;
        align-items: flex-start;
    }
    .business-logo {
        width: 70px;
        height: 70px;
        border-radius: 14px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border: 2px solid rgba(255,255,255,0.3);
        overflow: hidden;
        flex-shrink: 0;
    }
    .business-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .business-info {
        flex: 1;
        min-width: 0;
    }
    .business-name {
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.25;
        word-wrap: break-word;
    }
    .business-tagline {
        font-size: 0.8rem;
        opacity: 0.9;
        margin-top: 0.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .business-location {
        font-size: 0.75rem;
        opacity: 0.9;
        margin-top: 0.35rem;
        display: flex;
        align-items: flex-start;
        gap: 0.3rem;
        line-height: 1.3;
    }
    .business-location i {
        margin-top: 0.15rem;
        flex-shrink: 0;
    }
    .business-rating {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        background: rgba(255,255,255,0.2);
        padding: 0.3rem 0.6rem;
        border-radius: 20px;
        margin-top: 0.5rem;
        font-size: 0.75rem;
    }
    .business-rating .stars {
        color: #737373;
    }
    .business-rating .stars i {
        font-size: 0.65rem;
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
       CARDS - Mobile First
       ============================================ */
    .biz-card {
        background: var(--white);
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .biz-card h3 {
        font-size: 1rem;
        margin: 0 0 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .biz-card h3 i {
        color: var(--business-primary);
        font-size: 0.9rem;
    }

    /* ============================================
       SERVICES - Mobile Optimized
       ============================================ */
    .service-item {
        display: flex;
        flex-direction: column;
        padding: 0.875rem;
        border-radius: 12px;
        background: var(--secondary);
        margin-bottom: 0.625rem;
        transition: all 0.2s;
    }
    .service-item:last-child {
        margin-bottom: 0;
    }
    .service-item:active {
        transform: scale(0.99);
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
    }
    .service-desc {
        color: var(--text-light);
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
        color: var(--text-light);
    }
    .service-price {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--business-primary);
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
        background: linear-gradient(135deg, var(--business-primary), var(--business-accent));
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .service-book-btn:active {
        transform: scale(0.98);
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
       REVIEWS - Mobile
       ============================================ */
    .review-card {
        padding: 0.875rem 0;
        border-bottom: 1px solid var(--border);
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
        background: linear-gradient(135deg, var(--business-primary), var(--business-accent));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .review-author-info strong {
        display: block;
        font-size: 0.85rem;
    }
    .review-stars {
        color: #737373;
        font-size: 0.7rem;
    }
    .review-date {
        font-size: 0.7rem;
        color: var(--text-light);
        white-space: nowrap;
    }
    .review-text {
        margin: 0.625rem 0 0;
        font-size: 0.85rem;
        line-height: 1.5;
        color: var(--text-light);
    }
    .review-response {
        margin-top: 0.625rem;
        padding: 0.625rem;
        background: var(--secondary);
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
        color: var(--business-primary);
        font-size: 0.75rem;
    }
    .response-header strong {
        font-size: 0.75rem;
    }
    .review-response p {
        margin: 0;
        font-size: 0.8rem;
        line-height: 1.4;
    }

    /* ============================================
       RATING SUMMARY - Mobile
       ============================================ */
    .rating-summary {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 0.875rem;
        background: var(--secondary);
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
        color: #737373;
        line-height: 1;
    }
    .rating-big .review-stars {
        margin-top: 0.25rem;
    }
    .rating-big small {
        display: block;
        font-size: 0.7rem;
        color: var(--text-light);
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
    }
    .rating-bar:last-child {
        margin-bottom: 0;
    }
    .rating-bar-label {
        width: 12px;
        text-align: center;
    }
    .rating-bar i {
        color: #737373;
        font-size: 0.6rem;
    }
    .rating-bar-track {
        flex: 1;
        height: 5px;
        background: var(--white);
        border-radius: 3px;
        overflow: hidden;
    }
    .rating-bar-fill {
        height: 100%;
        background: #737373;
        border-radius: 3px;
    }

    /* ============================================
       OPENING HOURS - Mobile
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
        border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
    }
    .hours-item:last-child {
        border-bottom: none;
    }
    .hours-item.today {
        font-weight: 600;
        color: var(--business-primary);
    }
    .hours-closed {
        color: var(--text-light);
    }

    /* ============================================
       CONTACT - Mobile
       ============================================ */
    .contact-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border);
    }
    .contact-section h4 {
        font-size: 0.9rem;
        margin: 0 0 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .contact-section h4 i {
        color: var(--business-primary);
        font-size: 0.85rem;
    }
    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.4rem 0;
        text-decoration: none;
        color: var(--text);
        font-size: 0.85rem;
    }
    .contact-item i {
        width: 18px;
        color: var(--business-primary);
        text-align: center;
        font-size: 0.85rem;
    }

    /* ============================================
       ABOUT & MISC - Mobile
       ============================================ */
    .welcome-msg {
        font-size: 0.95rem;
        color: var(--business-primary);
        margin-bottom: 0.5rem;
        font-weight: 500;
        line-height: 1.4;
    }
    .about-text {
        line-height: 1.6;
        color: var(--text-light);
        font-size: 0.9rem;
    }
    .empty-text {
        color: var(--text-light);
        padding: 0.75rem 0;
        font-size: 0.85rem;
    }
    .empty-reviews {
        text-align: center;
        padding: 1.5rem 0;
        color: var(--text-light);
    }
    .empty-reviews i {
        font-size: 1.75rem;
        opacity: 0.3;
        display: block;
        margin-bottom: 0.4rem;
    }
    .empty-reviews p {
        margin: 0;
        font-size: 0.85rem;
    }

    /* ============================================
       SOCIAL LINKS - Mobile
       ============================================ */
    .social-section {
        margin-top: 0.875rem;
        padding-top: 0.875rem;
        border-top: 1px solid var(--border);
    }
    .social-links {
        display: flex;
        gap: 0.4rem;
    }
    .social-link {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text);
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .social-link:active {
        background: var(--business-primary);
        color: white;
    }

    /* ============================================
       SIDEBAR BOOK BUTTON - Mobile
       ============================================ */
    .sidebar-book-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        width: 100%;
        padding: 0.75rem;
        margin-top: 1rem;
        background: linear-gradient(135deg, var(--business-primary), var(--business-accent));
        color: white;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .sidebar-book-btn:active {
        transform: scale(0.98);
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
       DARK MODE - Business Page
       ============================================ */
    [data-theme="dark"] .biz-card {
        background: var(--bg-card);
        border-color: var(--border);
        color: var(--text-primary);
    }

    [data-theme="dark"] .biz-card h3 {
        color: var(--text-primary);
    }

    [data-theme="dark"] .service-item {
        background: var(--bg-secondary);
    }

    [data-theme="dark"] .service-item:hover {
        background: var(--bg-tertiary);
    }

    [data-theme="dark"] .service-name {
        color: var(--text-primary);
    }

    [data-theme="dark"] .service-desc,
    [data-theme="dark"] .service-meta {
        color: var(--text-light);
    }

    [data-theme="dark"] .about-text {
        color: var(--text-secondary);
    }

    [data-theme="dark"] .welcome-msg {
        color: var(--primary-light);
    }

    [data-theme="dark"] .hours-list {
        color: var(--text-primary);
    }

    [data-theme="dark"] .hours-item {
        border-color: var(--border);
    }

    [data-theme="dark"] .hours-closed {
        color: var(--text-muted);
    }

    [data-theme="dark"] .contact-item {
        color: var(--text-primary);
    }

    [data-theme="dark"] .social-link {
        background: var(--bg-tertiary);
        color: var(--text-primary);
    }

    [data-theme="dark"] .social-link:hover {
        background: var(--business-primary);
        color: white;
    }

    [data-theme="dark"] .review-card {
        border-color: var(--border);
    }

    [data-theme="dark"] .review-text {
        color: var(--text-secondary);
    }

    [data-theme="dark"] .review-date {
        color: var(--text-muted);
    }

    [data-theme="dark"] .review-response {
        background: var(--bg-secondary);
        border-left-color: var(--business-primary);
    }

    [data-theme="dark"] .rating-summary {
        background: var(--bg-secondary);
    }

    [data-theme="dark"] .rating-bar-track {
        background: var(--bg-tertiary);
    }

    [data-theme="dark"] .empty-text,
    [data-theme="dark"] .empty-reviews p {
        color: var(--text-muted);
    }

    [data-theme="dark"] .gallery-view-all {
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    [data-theme="dark"] .gallery-view-all:hover {
        background: var(--business-primary);
        color: white;
    }

    [data-theme="dark"] .lightbox-overlay {
        background: rgba(0,0,0,0.98);
    }

    [data-theme="dark"] .lightbox-close,
    [data-theme="dark"] .lightbox-nav {
        background: rgba(255,255,255,0.1);
    }

    [data-theme="dark"] .lightbox-close:hover,
    [data-theme="dark"] .lightbox-nav:hover {
        background: rgba(255,255,255,0.2);
    }

    [data-theme="dark"] .lightbox-thumb {
        border-color: var(--border);
    }

    [data-theme="dark"] .lightbox-thumb.active {
        border-color: var(--primary);
    }
</style>

<div class="business-page-wrapper">
    <!-- Business Header -->
    <div class="business-header">
        <div class="business-header-content">
            <div class="business-header-flex">
                <div class="business-logo">
                    <?php if (!empty($business['logo'])): ?>
                        <img src="<?= htmlspecialchars($business['logo']) ?>" alt="<?= htmlspecialchars($business['name']) ?>">
                    <?php else: ?>
                        <i class="fas fa-spa"></i>
                    <?php endif; ?>
                </div>
                <div class="business-info">
                    <h1 class="business-name"><?= htmlspecialchars($business['name']) ?></h1>
                    <?php if (!empty($settings['tagline'])): ?>
                        <p class="business-tagline"><?= htmlspecialchars($settings['tagline']) ?></p>
                    <?php endif; ?>
                    <p class="business-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($business['address']) ?><?php if (!empty($business['house_number'])): ?> <?= htmlspecialchars($business['house_number']) ?><?php endif; ?>, <?= htmlspecialchars($business['city']) ?></span>
                    </p>
                    <div class="business-rating">
                        <span class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= round($business['avg_rating']) ? '' : '-o' ?>"></i>
                            <?php endfor; ?>
                        </span>
                        <strong><?= number_format($business['avg_rating'], 1) ?></strong>
                        <span style="opacity:0.8">(<?= $business['review_count'] ?>)</span>
                    </div>
                </div>
                <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="btn-book btn-book-desktop">
                    <i class="fas fa-calendar-plus"></i> Boek Nu
                </a>
            </div>
            <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="btn-book btn-book-mobile">
                <i class="fas fa-calendar-plus"></i> Boek Nu
            </a>
        </div>
    </div>

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
