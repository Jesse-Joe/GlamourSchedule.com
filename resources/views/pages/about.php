<?php ob_start(); ?>

<style>
/* Functionality Page */
.func-page {
    padding-top: 6rem;
}

/* Hero Section */
.func-hero {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    padding: 4rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.func-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 70% 30%, rgba(255,255,255,0.05) 0%, transparent 50%);
    pointer-events: none;
}
.func-hero-content {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.func-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 1rem;
    line-height: 1.2;
}
.func-hero p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.8);
    margin: 0;
    line-height: 1.6;
}

@media (min-width: 768px) {
    .func-hero {
        padding: 5rem 2rem;
    }
    .func-hero h1 {
        font-size: 3rem;
    }
}

/* Section */
.func-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 1.5rem;
    background: #000000;
}
.func-section-alt {
    background: #0a0a0a;
}
.func-section-alt .func-stat {
    background: #ffffff;
}
.func-section-alt .func-stat-number {
    color: #000000;
}
.func-section-alt .func-stat-label {
    color: #666666;
}
.func-section-header {
    text-align: center;
    margin-bottom: 3rem;
}
.func-section-header h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.75rem;
}
.func-section-header p {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.7);
    max-width: 600px;
    margin: 0 auto;
}

/* Feature Cards */
.func-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}
.func-card {
    background: #111111;
    border: 2px solid #333333;
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.3s ease;
}
.func-card:hover {
    border-color: #ffffff;
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(255,255,255,0.05);
}
.func-card-icon {
    width: 56px;
    height: 56px;
    background: #ffffff;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
}
.func-card-icon i {
    font-size: 1.4rem;
    color: #000000;
}
.func-card h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.75rem;
}
.func-card p {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.7);
    line-height: 1.6;
    margin: 0 0 1rem;
}
.func-card-features {
    list-style: none;
    padding: 0;
    margin: 0;
}
.func-card-features li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
    padding: 0.35rem 0;
}
.func-card-features li i {
    color: #22c55e;
    font-size: 0.8rem;
}

/* Highlight Feature */
.func-highlight {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    align-items: center;
    margin-bottom: 2rem;
}
@media (min-width: 768px) {
    .func-highlight {
        grid-template-columns: 1fr 1fr;
    }
    .func-highlight.reverse {
        direction: rtl;
    }
    .func-highlight.reverse > * {
        direction: ltr;
    }
}
.func-highlight-content h3 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 1rem;
}
.func-highlight-content p {
    font-size: 1rem;
    color: rgba(255,255,255,0.7);
    line-height: 1.7;
    margin: 0 0 1.5rem;
}
.func-highlight-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.func-highlight-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.5rem 0;
    font-size: 0.95rem;
    color: rgba(255,255,255,0.8);
}
.func-highlight-list li i {
    color: #22c55e;
    margin-top: 0.2rem;
}
.func-highlight-visual {
    background: linear-gradient(135deg, #000000 0%, #333333 100%);
    border-radius: 20px;
    padding: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 250px;
}
.func-highlight-visual i {
    font-size: 5rem;
    color: rgba(255,255,255,0.9);
}

/* Stats */
.func-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin: 3rem 0;
}
@media (min-width: 768px) {
    .func-stats {
        grid-template-columns: repeat(4, 1fr);
    }
}
.func-stat {
    background: #000000;
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
}
.func-stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    display: block;
}
.func-stat-label {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.8);
    margin-top: 0.25rem;
}

/* For Business Section */
.business-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.25rem;
}
.business-feature {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    background: #111111;
    border-radius: 14px;
    border: 1px solid #333333;
    transition: all 0.3s ease;
}
.business-feature:hover {
    border-color: #ffffff;
    box-shadow: 0 5px 20px rgba(255,255,255,0.05);
}
.business-feature i {
    font-size: 1.25rem;
    color: #ffffff;
    flex-shrink: 0;
    margin-top: 0.1rem;
}
.business-feature h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 0.25rem;
}
.business-feature p {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.7);
    margin: 0;
    line-height: 1.5;
}

/* CTA */
.func-cta {
    background: #000000;
    border-radius: 24px;
    padding: 3rem 2rem;
    text-align: center;
    margin-top: 2rem;
}
.func-cta h3 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 1rem;
}
.func-cta p {
    font-size: 1rem;
    color: rgba(255,255,255,0.8);
    margin: 0 0 1.5rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}
.func-cta-btns {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
.func-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}
.func-cta-btn:hover {
    transform: translateY(-2px);
}
.func-cta-btn-white {
    background: #ffffff;
    color: #000000;
}
.func-cta-btn-outline {
    background: transparent;
    color: #ffffff;
    border: 2px solid rgba(255,255,255,0.5);
}
.func-cta-btn-outline:hover {
    border-color: #ffffff;
}
</style>

<div class="func-page">
    <!-- Hero -->
    <section class="func-hero">
        <div class="func-hero-content">
            <h1><?= $translations['platform_functionality'] ?? 'Platform Features' ?></h1>
            <p><?= $translations['platform_functionality_desc'] ?? 'Discover all features GlamourSchedule has to offer for customers and salons' ?></p>
        </div>
    </section>

    <!-- For Customers -->
    <section class="func-section">
        <div class="func-section-header">
            <h2><?= $translations['for_customers'] ?? 'For Customers' ?></h2>
            <p><?= $translations['for_customers_desc'] ?? 'Easy searching, booking and paying - all online' ?></p>
        </div>

        <div class="func-grid">
            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3><?= $translations['advanced_search_title'] ?? 'Advanced Search' ?></h3>
                <p><?= $translations['advanced_search_desc'] ?? 'Quickly find the perfect salon with our comprehensive search feature.' ?></p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> <?= $translations['search_by_location'] ?? 'Search by location and postal code' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['filter_by_category'] ?? 'Filter by category' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['price_filters'] ?? 'Price filters (min/max)' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['sort_by_rating'] ?? 'Sort by rating' ?></li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?= $translations['availability_filters_title'] ?? 'Availability Filters' ?></h3>
                <p><?= $translations['availability_filters_desc'] ?? 'Find salons that are open when you are available.' ?></p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> <?= $translations['open_now_filter'] ?? 'Open now filter' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['open_weekend'] ?? 'Open on weekends' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['evening_opening'] ?? 'Evening opening' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['four_plus_stars_filter'] ?? '4+ stars filter' ?></li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3><?= $translations['online_booking_title'] ?? 'Online Booking' ?></h3>
                <p><?= $translations['online_booking_desc'] ?? 'Book directly online without calling, available 24/7.' ?></p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> <?= $translations['realtime_availability'] ?? 'Real-time availability' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['instant_confirmation'] ?? 'Instant confirmation' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['email_reminders'] ?? 'Email reminders' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['easy_modify_cancel'] ?? 'Easy to modify/cancel' ?></li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3><?= $translations['secure_payment_title'] ?? 'Secure Payment' ?></h3>
                <p><?= $translations['secure_payment_desc'] ?? 'Pay online with your favorite payment method via Mollie.' ?></p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> iDEAL</li>
                    <li><i class="fas fa-check"></i> <?= $translations['credit_card'] ?? 'Credit card' ?></li>
                    <li><i class="fas fa-check"></i> Apple Pay / Google Pay</li>
                    <li><i class="fas fa-check"></i> Bancontact</li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h3><?= $translations['qr_checkin_title'] ?? 'QR-Code Check-in' ?></h3>
                <p><?= $translations['qr_checkin_desc'] ?? 'Receive a QR code by email for quick check-in at the salon.' ?></p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> <?= $translations['automatic_qr_email'] ?? 'Automatic email with QR' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['quick_checkin'] ?? 'Quick check-in' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['no_waiting'] ?? 'No waiting time' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['digital_proof'] ?? 'Digital proof' ?></li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3><?= $translations['reviews_ratings_title'] ?? 'Reviews & Ratings' ?></h3>
                <p><?= $translations['reviews_ratings_desc'] ?? 'Read reviews from other customers and share your own experience.' ?></p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> <?= $translations['verified_reviews'] ?? 'Verified reviews' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['star_ratings'] ?? 'Star ratings' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['photo_reviews'] ?? 'Photo reviews' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['salon_responses'] ?? 'Salon responses' ?></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="func-section func-section-alt">
        <div class="func-stats">
            <div class="func-stat">
                <span class="func-stat-number">50+</span>
                <span class="func-stat-label"><?= $translations['categories'] ?? 'Categories' ?></span>
            </div>
            <div class="func-stat">
                <span class="func-stat-number">24/7</span>
                <span class="func-stat-label"><?= $translations['online_booking_title'] ?? 'Online Booking' ?></span>
            </div>
            <div class="func-stat">
                <span class="func-stat-number">100%</span>
                <span class="func-stat-label"><?= $translations['secure_payment_title'] ?? 'Secure Payment' ?></span>
            </div>
            <div class="func-stat">
                <span class="func-stat-number">86+</span>
                <span class="func-stat-label"><?= $translations['languages'] ?? 'Languages' ?></span>
            </div>
        </div>
    </section>

    <!-- For Business -->
    <section class="func-section">
        <div class="func-section-header">
            <h2><?= $translations['for_salons'] ?? 'For Salons' ?></h2>
            <p><?= $translations['for_salons_desc'] ?? 'Everything you need to run your salon professionally' ?></p>
        </div>

        <!-- Highlight 1: Dashboard -->
        <div class="func-highlight">
            <div class="func-highlight-content">
                <h3><?= $translations['complete_dashboard_title'] ?? 'Complete Dashboard' ?></h3>
                <p><?= $translations['complete_dashboard_desc'] ?? 'Manage your entire salon from a clear dashboard. From appointments to statistics - all in one place.' ?></p>
                <ul class="func-highlight-list">
                    <li><i class="fas fa-check"></i> <?= $translations['overview_all_bookings'] ?? 'Overview of all bookings' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['revenue_statistics'] ?? 'Revenue and statistics' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['customer_overview'] ?? 'Customer overview' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['calendar_integration'] ?? 'Calendar integration' ?></li>
                </ul>
            </div>
            <div class="func-highlight-visual">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>

        <!-- Highlight 2: Services -->
        <div class="func-highlight reverse">
            <div class="func-highlight-content">
                <h3><?= $translations['services_management_title'] ?? 'Services Management' ?></h3>
                <p><?= $translations['services_management_desc'] ?? 'Easily add your services with prices, duration and descriptions. Organize them in categories.' ?></p>
                <ul class="func-highlight-list">
                    <li><i class="fas fa-check"></i> <?= $translations['unlimited_services'] ?? 'Unlimited services' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['set_prices_duration'] ?? 'Set prices and duration' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['organize_categories'] ?? 'Organize categories' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['active_inactive_toggle'] ?? 'Set active/inactive' ?></li>
                </ul>
            </div>
            <div class="func-highlight-visual">
                <i class="fas fa-list-check"></i>
            </div>
        </div>

        <!-- Business Features Grid -->
        <div class="business-features">
            <div class="business-feature">
                <i class="fas fa-globe"></i>
                <div>
                    <h4><?= $translations['own_salon_page'] ?? 'Own Salon Page' ?></h4>
                    <p><?= $translations['own_salon_page_desc'] ?? 'Professional page with photos, services and reviews' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <h4><?= $translations['opening_hours'] ?? 'Opening Hours' ?></h4>
                    <p><?= $translations['opening_hours_desc'] ?? 'Set your opening hours per day' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-qrcode"></i>
                <div>
                    <h4><?= $translations['qr_scanner'] ?? 'QR Scanner' ?></h4>
                    <p><?= $translations['qr_scanner_desc'] ?? 'Scan QR codes for quick check-in' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-euro-sign"></i>
                <div>
                    <h4><?= $translations['automatic_payments'] ?? 'Automatic Payments' ?></h4>
                    <p><?= $translations['automatic_payments_desc'] ?? 'Receive payments directly via Mollie' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-bell"></i>
                <div>
                    <h4><?= $translations['notifications'] ?? 'Notifications' ?></h4>
                    <p><?= $translations['notifications_desc'] ?? 'Email alerts for new bookings' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-images"></i>
                <div>
                    <h4><?= $translations['photo_gallery'] ?? 'Photo Gallery' ?></h4>
                    <p><?= $translations['photo_gallery_desc'] ?? 'Show your work with photos and portfolio' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-users"></i>
                <div>
                    <h4><?= $translations['customer_management'] ?? 'Customer Management' ?></h4>
                    <p><?= $translations['customer_management_desc'] ?? 'Keep track of customer data and history' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-chart-line"></i>
                <div>
                    <h4><?= $translations['insights_analytics'] ?? 'Insights & Analytics' ?></h4>
                    <p><?= $translations['insights_analytics_desc'] ?? 'View statistics and growth' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-rocket"></i>
                <div>
                    <h4><?= $translations['boost_feature'] ?? 'Boost Feature' ?></h4>
                    <p><?= $translations['boost_feature_desc'] ?? 'Increase your visibility in search results' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-mobile-alt"></i>
                <div>
                    <h4><?= $translations['mobile_friendly'] ?? 'Mobile Friendly' ?></h4>
                    <p><?= $translations['mobile_friendly_desc'] ?? 'Works perfectly on all devices' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <h4><?= $translations['ssl_security'] ?? 'SSL Security' ?></h4>
                    <p><?= $translations['ssl_security_desc'] ?? 'Secure connection and data' ?></p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-headset"></i>
                <div>
                    <h4><?= $translations['international_support'] ?? 'International Support' ?></h4>
                    <p><?= $translations['international_support_desc'] ?? 'Help in your language when you need it' ?></p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="func-cta">
            <h3><?= $translations['cta_start_salon'] ?? 'Start with your salon today' ?></h3>
            <p><?= $translations['cta_start_salon_desc'] ?? 'Register your salon for only &euro;0.99 and enjoy all features.' ?></p>
            <div class="func-cta-btns">
                <a href="/register?type=business" class="func-cta-btn func-cta-btn-white">
                    <i class="fas fa-store"></i> <?= $translations['register_salon'] ?? 'Register Salon' ?>
                </a>
                <a href="/search" class="func-cta-btn func-cta-btn-outline">
                    <i class="fas fa-search"></i> <?= $translations['view_salons'] ?? 'View Salons' ?>
                </a>
            </div>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
