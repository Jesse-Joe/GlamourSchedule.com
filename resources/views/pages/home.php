<?php ob_start(); ?>

<!-- Hero Section with Integrated Search -->
<section class="hero-prestige">
    <div class="hero-tag"><?= $translations['hero_badge'] ?? 'Premium Booking Platform' ?></div>
    <h1><?= $translations['hero_title'] ?? 'Book your perfect appointment' ?></h1>
    <div class="hero-divider"></div>
    <p><?= $translations['hero_subtitle'] ?? 'Discover the best salons and book online instantly. Premium service, always the best price.' ?></p>

    <!-- Search Form inside Hero -->
    <form action="/search" method="GET" class="search-prestige hero-search-form">
        <div class="search-input-group">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="<?= $translations['search_what'] ?? 'What are you looking for? (hairdresser, nails, massage...)' ?>">
        </div>
        <div class="search-input-group">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" name="location" placeholder="<?= $translations['search_where'] ?? 'City or postal code' ?>">
        </div>
        <button type="submit"><?= $translations['search'] ?? 'Search' ?></button>
    </form>
</section>

<!-- Stats -->
<div class="stats-prestige">
    <div class="stat-item">
        <div class="stat-number" id="stat-businesses"><?= number_format($stats['businesses'] ?? 0) ?></div>
        <div class="stat-label"><?= $translations['stats_salons'] ?? 'Partner Salons' ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="stat-bookings"><?= number_format($stats['bookings'] ?? 0) ?></div>
        <div class="stat-label"><?= $translations['stats_bookings'] ?? 'Bookings Made' ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="stat-users"><?= number_format($stats['users'] ?? 0) ?></div>
        <div class="stat-label"><?= $translations['stats_customers'] ?? 'Happy Customers' ?></div>
    </div>
</div>

<!-- Boosted Businesses (Paid Promotion) - Now above categories -->
<section class="section section-boosted">
    <style>
        .section-boosted {
            background: var(--black, #000000);
            position: relative;
            overflow: hidden;
        }
        .section-boosted::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }
        [data-theme="light"] .section-boosted::before {
            opacity: 0.1;
        }
        .section-boosted .section-header {
            position: relative;
            z-index: 1;
        }
        .section-boosted .section-tag {
            background: var(--white, #ffffff);
            color: var(--black, #000000);
        }
        .section-boosted .section-title {
            color: var(--white, #ffffff);
        }
        .section-boosted .section-subtitle {
            color: var(--text-secondary, rgba(255, 255, 255, 0.7));
        }
        .boosted-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        @media (max-width: 992px) {
            .boosted-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .boosted-grid {
                grid-template-columns: 1fr;
            }
        }
        .boosted-card {
            background: var(--card-bg, #111111);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            border: 2px solid var(--border, #333333);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        .boosted-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border-color: var(--primary, #ffffff);
        }
        .boosted-card-image {
            height: 180px;
            background: linear-gradient(135deg, var(--charcoal, #1a1a1a), var(--graphite, #333333));
            position: relative;
            overflow: hidden;
        }
        .boosted-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .boosted-card-image .placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .boosted-card-image .placeholder i {
            font-size: 3rem;
            color: var(--text-muted, #ffffff);
            opacity: 0.3;
        }
        .boosted-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--white, #ffffff);
            color: var(--black, #000000);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .boosted-badge i {
            font-size: 0.8rem;
        }
        .boosted-card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .boosted-card-name {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
            color: var(--text-primary, #ffffff);
        }
        .boosted-card-location {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary, rgba(255, 255, 255, 0.7));
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }
        .boosted-card-location i {
            color: var(--text-primary, #ffffff);
        }
        .boosted-card-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 0.75rem;
        }
        .boosted-card-rating .stars {
            display: flex;
            gap: 2px;
        }
        .boosted-card-rating .stars i {
            font-size: 0.85rem;
            color: var(--text-primary, #ffffff);
        }
        .boosted-card-rating .count {
            font-size: 0.85rem;
            color: var(--text-secondary, rgba(255, 255, 255, 0.7));
        }
        .boosted-card-price {
            font-size: 0.9rem;
            color: var(--text-secondary, rgba(255, 255, 255, 0.7));
            margin-bottom: 1rem;
        }
        .boosted-card-price strong {
            color: var(--text-primary, #ffffff);
            font-size: 1rem;
        }
        .boosted-card-btn {
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.75rem 1.25rem;
            background: var(--white, #ffffff);
            color: var(--black, #000000);
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s;
        }
        .boosted-card-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
        }
        /* Empty slot banner styling */
        .boosted-slot-available {
            background: var(--black, #000000);
            border: 2px dashed var(--border, #444444);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 350px;
            text-decoration: none;
            color: var(--text-primary, #ffffff);
            transition: all 0.3s ease;
            padding: 2rem;
            text-align: center;
        }
        .boosted-slot-available:hover {
            border-color: var(--text-primary, #ffffff);
            background: var(--card-bg, #111111);
            transform: translateY(-4px);
        }
        .boosted-slot-icon {
            width: 80px;
            height: 80px;
            background: var(--charcoal, #1a1a1a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            border: 2px solid var(--border, #333333);
        }
        .boosted-slot-icon i {
            font-size: 2rem;
            color: var(--text-primary, #ffffff);
        }
        .boosted-slot-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary, #ffffff);
        }
        .boosted-slot-desc {
            color: var(--text-secondary, rgba(255, 255, 255, 0.7));
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .boosted-slot-price {
            background: var(--white, #ffffff);
            color: var(--black, #000000);
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .boosted-slot-duration {
            color: var(--text-muted, rgba(255, 255, 255, 0.6));
            font-size: 0.85rem;
        }
    </style>

    <div class="section-header">
        <div class="section-tag"><i class="fas fa-star"></i> <?= $translations['featured'] ?? 'Featured' ?></div>
        <h2 class="section-title"><?= $translations['featured_businesses'] ?? 'Featured Businesses' ?></h2>
        <p class="section-subtitle"><?= $translations['featured_businesses_desc'] ?? 'Premium salons that stand out' ?></p>
    </div>

    <div class="boosted-grid">
        <?php
        $boostedCount = count($boostedBusinesses ?? []);
        $totalSlots = 9;

        // Show boosted businesses
        foreach ($boostedBusinesses ?? [] as $biz):
            $logoUrl = $biz['logo'] ?? '';
            if ($logoUrl && !str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
                $logoUrl = '/uploads/businesses/' . $logoUrl;
            }
        ?>
        <a href="/business/<?= htmlspecialchars($biz['slug']) ?>" class="boosted-card">
            <div class="boosted-card-image">
                <?php if ($logoUrl): ?>
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($biz['name']) ?>" loading="lazy">
                <?php else: ?>
                    <div class="placeholder"><i class="fas fa-spa"></i></div>
                <?php endif; ?>
                <span class="boosted-badge"><i class="fas fa-rocket"></i> <?= $translations['featured'] ?? 'Featured' ?></span>
            </div>
            <div class="boosted-card-body">
                <h3 class="boosted-card-name"><?= htmlspecialchars($biz['name']) ?></h3>
                <div class="boosted-card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($biz['city'] ?? ($translations['netherlands'] ?? 'Netherlands')) ?></span>
                </div>
                <div class="boosted-card-rating">
                    <div class="stars">
                        <?php $rating = round($biz['avg_rating'] ?? 0); ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?= $i <= $rating ? '' : ' empty' ?>" style="<?= $i > $rating ? 'opacity:0.3' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="count"><?= number_format($biz['avg_rating'] ?? 0, 1) ?> (<?= $biz['review_count'] ?? 0 ?> reviews)</span>
                </div>
                <?php if (!empty($biz['min_price'])): ?>
                <div class="boosted-card-price">
                    <?= $translations['from_price'] ?? 'From' ?> <strong>&euro;<?= number_format($biz['min_price'], 0) ?></strong>
                </div>
                <?php endif; ?>
                <span class="boosted-card-btn">
                    <?= $translations['view_book'] ?? 'View & Book' ?> <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </a>
        <?php endforeach; ?>

        <?php
        // Show empty slots as purchasable banners
        $emptySlots = $totalSlots - $boostedCount;
        for ($slot = 1; $slot <= $emptySlots; $slot++):
        ?>
        <a href="/business/boost" class="boosted-slot-available">
            <div class="boosted-slot-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="boosted-slot-title"><?= $translations['slot_available'] ?? 'Spot Available' ?></div>
            <div class="boosted-slot-desc"><?= $translations['slot_available_desc'] ?? 'Put your salon in the spotlight and reach more customers' ?></div>
            <div class="boosted-slot-price">&euro;299,99</div>
            <div class="boosted-slot-duration"><?= $translations['slot_duration'] ?? '30 days featured' ?></div>
        </a>
        <?php endfor; ?>
    </div>
</section>

<!-- Categories - 10 Groups with Photos -->
<section class="section section-light">
    <div class="section-header">
        <div class="section-tag"><?= $translations['categories_label'] ?? 'Categories' ?></div>
        <h2 class="section-title"><?= $translations['categories_title'] ?? 'Discover Services' ?></h2>
        <p class="section-subtitle"><?= $translations['categories_subtitle'] ?? 'From hair to wellness - find exactly what you\'re looking for' ?></p>
    </div>

    <div class="category-grid">
        <?php
        $categories = [
            ['slug' => 'haar', 'name_key' => 'cat_hair', 'name_default' => 'Hair', 'icon' => 'cut', 'desc_key' => 'cat_hair_desc', 'desc_default' => 'Hairdresser, Barber, Stylist', 'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&h=300&fit=crop'],
            ['slug' => 'nagels', 'name_key' => 'cat_nails', 'name_default' => 'Nails', 'icon' => 'hand-sparkles', 'desc_key' => 'cat_nails_desc', 'desc_default' => 'Manicure, Pedicure, Gel', 'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&h=300&fit=crop'],
            ['slug' => 'huid', 'name_key' => 'cat_skin', 'name_default' => 'Skincare', 'icon' => 'spa', 'desc_key' => 'cat_skin_desc', 'desc_default' => 'Facial, Skincare', 'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400&h=300&fit=crop'],
            ['slug' => 'lichaam', 'name_key' => 'cat_body', 'name_default' => 'Body', 'icon' => 'hands', 'desc_key' => 'cat_body_desc', 'desc_default' => 'Massage, Body', 'image' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=400&h=300&fit=crop'],
            ['slug' => 'ontharing', 'name_key' => 'cat_hairremoval', 'name_default' => 'Hair Removal', 'icon' => 'feather', 'desc_key' => 'cat_hairremoval_desc', 'desc_default' => 'Waxing, Laser', 'image' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=400&h=300&fit=crop'],
            ['slug' => 'makeup', 'name_key' => 'cat_makeup', 'name_default' => 'Make-up', 'icon' => 'paint-brush', 'desc_key' => 'cat_makeup_desc', 'desc_default' => 'Make-up, Lashes', 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=300&fit=crop'],
            ['slug' => 'wellness', 'name_key' => 'cat_wellness', 'name_default' => 'Wellness', 'icon' => 'hot-tub', 'desc_key' => 'cat_wellness_desc', 'desc_default' => 'Spa, Sauna', 'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=400&h=300&fit=crop'],
            ['slug' => 'bruinen', 'name_key' => 'cat_tanning', 'name_default' => 'Tanning', 'icon' => 'sun', 'desc_key' => 'cat_tanning_desc', 'desc_default' => 'Sunbed, Spray tan', 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop'],
            ['slug' => 'medisch', 'name_key' => 'cat_medical', 'name_default' => 'Medical', 'icon' => 'user-md', 'desc_key' => 'cat_medical_desc', 'desc_default' => 'Botox, Fillers', 'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=300&fit=crop'],
            ['slug' => 'alternatief', 'name_key' => 'cat_alternative', 'name_default' => 'Alternative', 'icon' => 'yin-yang', 'desc_key' => 'cat_alternative_desc', 'desc_default' => 'Yoga, Reiki', 'image' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=400&h=300&fit=crop'],
        ];
        foreach ($categories as $cat):
        ?>
        <a href="/search?group=<?= $cat['slug'] ?>" class="category-card">
            <div class="category-card-image" style="background-image: url('<?= $cat['image'] ?>')"></div>
            <div class="category-card-overlay"></div>
            <div class="category-card-content">
                <div class="category-card-icon"><i class="fas fa-<?= $cat['icon'] ?>"></i></div>
                <h3><?= $translations[$cat['name_key']] ?? $cat['name_default'] ?></h3>
                <span><?= $translations[$cat['desc_key']] ?? $cat['desc_default'] ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Businesses -->
<?php if (!empty($featuredBusinesses)): ?>
<section class="section section-gray">
    <div class="section-header">
        <div class="section-tag"><?= $translations['featured_label'] ?? 'Popular' ?></div>
        <h2 class="section-title"><?= $translations['featured_title'] ?? 'Popular Salons' ?></h2>
        <p class="section-subtitle"><?= $translations['featured_subtitle'] ?? 'The best rated salons near you' ?></p>
    </div>

    <div class="business-grid" style="max-width: 1100px; margin: 0 auto;">
        <?php foreach (array_slice($featuredBusinesses, 0, 4) as $biz):
            $logoUrl = $biz['logo'] ?? '';
            if ($logoUrl && !str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
                $logoUrl = '/uploads/businesses/' . $logoUrl;
            }
        ?>
        <div class="business-card">
            <div class="business-image">
                <?php if ($logoUrl): ?>
                    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($biz['name']) ?>" loading="lazy">
                <?php else: ?>
                    <i class="fas fa-spa"></i>
                <?php endif; ?>
            </div>
            <div class="business-body">
                <h3 class="business-name"><?= htmlspecialchars($biz['name']) ?></h3>
                <div class="business-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($biz['city'] ?? ($translations['netherlands'] ?? 'Netherlands')) ?></span>
                </div>
                <div class="business-rating">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?= $i <= round($biz['avg_rating'] ?? 5) ? '' : ($i - 0.5 <= ($biz['avg_rating'] ?? 5) ? '-half-alt' : '') ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="count">(<?= $biz['review_count'] ?? 0 ?>)</span>
                </div>
                <a href="/business/<?= htmlspecialchars($biz['slug']) ?>" class="business-btn">
                    <?= $translations['view_book'] ?? 'View & Book' ?>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- CTA for Businesses -->
<section class="section section-light">
    <div class="container" style="max-width: 1100px; margin: 0 auto;">
        <div class="business-cta">
            <h2><i class="fas fa-store"></i> <?= $translations['have_salon'] ?? 'Do you have a salon?' ?></h2>
            <p><?= $translations['have_salon_desc'] ?? 'Join GlamourSchedule and receive online bookings from new customers' ?></p>
            <div class="business-cta-buttons">
                <a href="/business/register" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> <?= $translations['start_free'] ?? 'Start Free' ?>
                </a>
                <span class="badge">
                    <i class="fas fa-tag"></i> <?= str_replace(':count', '100', $translations['first_salons_offer'] ?? 'First 100 salons: only') ?> &euro;0,99
                </span>
            </div>
        </div>

        <!-- Sales Partner CTA -->
        <div class="sales-cta">
            <div>
                <h3><i class="fas fa-handshake"></i> <?= $translations['become_sales_partner'] ?? 'Become Sales Partner' ?></h3>
                <p><?= $translations['sales_partner_desc'] ?? 'Earn commission by referring businesses to GlamourSchedule' ?></p>
            </div>
            <a href="/sales/register" class="btn">
                <i class="fas fa-arrow-right"></i> <?= $translations['start_now'] ?? 'Start Now' ?>
            </a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section section-gray">
    <div class="section-header">
        <div class="section-tag"><?= $translations['steps_label'] ?? 'How It Works' ?></div>
        <h2 class="section-title"><?= $translations['steps_title'] ?? 'Booked in 3 Steps' ?></h2>
        <p class="section-subtitle"><?= $translations['steps_subtitle'] ?? 'Quick and easy appointment booking' ?></p>
    </div>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <h3 class="step-title"><?= $translations['step1_title'] ?? 'Find a salon' ?></h3>
            <p class="step-desc"><?= $translations['step1_desc'] ?? 'Find the perfect salon near you based on service, location or reviews' ?></p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <h3 class="step-title"><?= $translations['step2_title'] ?? 'Choose a service' ?></h3>
            <p class="step-desc"><?= $translations['step2_desc'] ?? 'View all services with prices, duration and available times in real-time' ?></p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <h3 class="step-title"><?= $translations['step3_title'] ?? 'Book instantly' ?></h3>
            <p class="step-desc"><?= $translations['step3_desc'] ?? 'Confirm your appointment with a few clicks and receive instant confirmation' ?></p>
        </div>
    </div>
</section>

<!-- Final CTA -->
<div class="cta-prestige">
    <div>
        <h2><?= $translations['cta_title'] ?? 'Grow Your Salon' ?></h2>
        <p><?= $translations['cta_subtitle'] ?? 'Join the premium booking platform.' ?></p>
    </div>
    <a href="/register?type=business" class="btn btn-primary btn-lg"><?= $translations['cta_start'] ?? 'Start Now' ?></a>
</div>

<script>
// Real-time Updates Manager
class LiveStats {
    constructor() {
        this.updateInterval = 30000; // 30 seconden
        this.init();
    }

    init() {
        // Start real-time updates
        this.updateStats();

        // Periodieke updates
        setInterval(() => this.updateStats(), this.updateInterval);
    }

    async updateStats() {
        try {
            const response = await fetch('/api/stats');
            const data = await response.json();

            if (data.businesses !== undefined) {
                this.animateStatCard(document.getElementById('stat-businesses'), data.businesses);
            }
            if (data.bookings !== undefined) {
                this.animateStatCard(document.getElementById('stat-bookings'), data.bookings);
            }
            if (data.users !== undefined) {
                this.animateStatCard(document.getElementById('stat-users'), data.users);
            }
        } catch (error) {
            console.log('Stats update check failed, retrying later');
        }
    }

    animateStatCard(element, newValue) {
        if (!element) return;
        const oldValue = parseInt(element.textContent.replace(/\D/g, '')) || 0;
        if (oldValue !== newValue) {
            this.animateCount(element, oldValue, newValue);
        }
    }

    animateCount(element, from, to) {
        const duration = 500;
        const startTime = performance.now();

        // Voeg pulse animatie toe
        element.classList.add('updating');
        setTimeout(() => element.classList.remove('updating'), 1000);

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const current = Math.floor(from + (to - from) * this.easeOutQuad(progress));
            element.textContent = current.toLocaleString('nl-NL');

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    easeOutQuad(t) {
        return t * (2 - t);
    }
}

// Start live updates wanneer pagina geladen is
document.addEventListener('DOMContentLoaded', () => {
    new LiveStats();
});
</script>

<style>
/* Category Cards with Photos */
.category-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}
.category-card {
    position: relative;
    height: 200px;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    color: var(--white, #ffffff);
    transition: all 0.3s ease;
}
.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}
.category-card-image {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
    transition: transform 0.5s ease;
}
.category-card:hover .category-card-image {
    transform: scale(1.1);
}
.category-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);
    transition: background 0.3s ease;
}
.category-card:hover .category-card-overlay {
    background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.8) 100%);
}
.category-card-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.25rem;
    text-align: left;
}
.category-card-icon {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 0 0.75rem 0;
    font-size: 1.1rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.category-card h3 {
    margin: 0 0 0.25rem;
    font-size: 1.1rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
.category-card span {
    font-size: 0.8rem;
    opacity: 0.9;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

/* Responsive Category Grid */
@media (max-width: 1200px) {
    .category-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
@media (max-width: 992px) {
    .category-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .category-card {
        height: 180px;
    }
}
@media (max-width: 768px) {
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
        padding: 0;
        gap: 0.5rem;
    }
    .category-card {
        height: 160px;
        border-radius: 0;
    }
    .category-card-content {
        padding: 1rem;
    }
    .category-card-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .category-card h3 {
        font-size: 1rem;
    }
}
@media (max-width: 480px) {
    .category-grid {
        grid-template-columns: 1fr 1fr;
    }
    .category-card {
        height: 140px;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
