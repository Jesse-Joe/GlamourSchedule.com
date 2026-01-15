<?php ob_start(); ?>

<!-- Hero Section with Integrated Search -->
<section class="hero-prestige">
    <div class="hero-tag"><?= $translations['hero_badge'] ?? 'Premium Beauty Platform' ?></div>
    <h1><?= $translations['hero_title'] ?? 'Beauty' ?> <span><?= $translations['hero_highlight'] ?? 'Elevated' ?></span></h1>
    <div class="hero-divider"></div>
    <p><?= $translations['hero_subtitle'] ?? 'Ontdek de beste salons en boek direct online. Premium service, altijd de beste prijs.' ?></p>

    <!-- Search Form inside Hero -->
    <form action="/search" method="GET" class="search-prestige hero-search-form">
        <div class="search-input-group">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="<?= $translations['search_what'] ?? 'Wat zoek je? (kapper, nagels, massage...)' ?>">
        </div>
        <div class="search-input-group">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" name="location" placeholder="<?= $translations['search_where'] ?? 'Stad of postcode' ?>">
        </div>
        <button type="submit"><?= $translations['search'] ?? 'Zoeken' ?></button>
    </form>
</section>

<!-- Stats -->
<div class="stats-prestige">
    <div class="stat-item">
        <div class="stat-number" id="stat-businesses"><?= number_format($stats['businesses'] ?? 0) ?></div>
        <div class="stat-label"><?= $translations['stats_salons'] ?? 'Aangesloten Salons' ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="stat-bookings"><?= number_format($stats['bookings'] ?? 0) ?></div>
        <div class="stat-label"><?= $translations['stats_bookings'] ?? 'Boekingen Gemaakt' ?></div>
    </div>
    <div class="stat-item">
        <div class="stat-number" id="stat-users"><?= number_format($stats['users'] ?? 0) ?></div>
        <div class="stat-label"><?= $translations['stats_customers'] ?? 'Tevreden Klanten' ?></div>
    </div>
</div>

<!-- Categories - 10 Groups with Photos -->
<section class="section section-light">
    <div class="section-header">
        <div class="section-tag"><?= $translations['categories_label'] ?? 'Categorieën' ?></div>
        <h2 class="section-title"><?= $translations['categories_title'] ?? 'Ontdek Services' ?></h2>
        <p class="section-subtitle"><?= $translations['categories_subtitle'] ?? 'Van haar tot wellness - vind precies wat je zoekt' ?></p>
    </div>

    <div class="category-grid">
        <?php
        $categories = [
            ['slug' => 'haar', 'name' => 'Haar', 'icon' => 'cut', 'desc' => 'Kapper, Barber, Stylist', 'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400&h=300&fit=crop'],
            ['slug' => 'nagels', 'name' => 'Nagels', 'icon' => 'hand-sparkles', 'desc' => 'Manicure, Pedicure, Gel', 'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&h=300&fit=crop'],
            ['slug' => 'huid', 'name' => 'Skincare', 'icon' => 'spa', 'desc' => 'Facial, Skincare', 'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400&h=300&fit=crop'],
            ['slug' => 'lichaam', 'name' => 'Lichaam', 'icon' => 'hands', 'desc' => 'Massage, Body', 'image' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=400&h=300&fit=crop'],
            ['slug' => 'ontharing', 'name' => 'Ontharing', 'icon' => 'feather', 'desc' => 'Waxen, Laser', 'image' => 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=400&h=300&fit=crop'],
            ['slug' => 'makeup', 'name' => 'Make-up', 'icon' => 'paint-brush', 'desc' => 'Visagie, Wimpers', 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=300&fit=crop'],
            ['slug' => 'wellness', 'name' => 'Wellness', 'icon' => 'hot-tub', 'desc' => 'Spa, Sauna', 'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=400&h=300&fit=crop'],
            ['slug' => 'bruinen', 'name' => 'Bruinen', 'icon' => 'sun', 'desc' => 'Zonnebank, Spray tan', 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop'],
            ['slug' => 'medisch', 'name' => 'Medisch', 'icon' => 'user-md', 'desc' => 'Botox, Fillers', 'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=300&fit=crop'],
            ['slug' => 'alternatief', 'name' => 'Alternatief', 'icon' => 'yin-yang', 'desc' => 'Yoga, Reiki', 'image' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=400&h=300&fit=crop'],
        ];
        foreach ($categories as $cat):
        ?>
        <a href="/search?group=<?= $cat['slug'] ?>" class="category-card">
            <div class="category-card-image" style="background-image: url('<?= $cat['image'] ?>')"></div>
            <div class="category-card-overlay"></div>
            <div class="category-card-content">
                <div class="category-card-icon"><i class="fas fa-<?= $cat['icon'] ?>"></i></div>
                <h3><?= $cat['name'] ?></h3>
                <span><?= $cat['desc'] ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Businesses -->
<?php if (!empty($featuredBusinesses)): ?>
<section class="section section-gray">
    <div class="section-header">
        <div class="section-tag"><?= $translations['featured_label'] ?? 'Uitgelicht' ?></div>
        <h2 class="section-title"><?= $translations['featured_title'] ?? 'Populaire Salons' ?></h2>
        <p class="section-subtitle"><?= $translations['featured_subtitle'] ?? 'De best beoordeelde salons bij jou in de buurt' ?></p>
    </div>

    <div class="business-grid" style="max-width: 1100px; margin: 0 auto;">
        <?php foreach (array_slice($featuredBusinesses, 0, 4) as $biz): ?>
        <div class="business-card">
            <div class="business-image">
                <?php if (!empty($biz['logo_url'])): ?>
                    <img src="<?= htmlspecialchars($biz['logo_url']) ?>" alt="<?= htmlspecialchars($biz['name']) ?>" loading="lazy">
                <?php else: ?>
                    <i class="fas fa-spa"></i>
                <?php endif; ?>
            </div>
            <div class="business-body">
                <h3 class="business-name"><?= htmlspecialchars($biz['name']) ?></h3>
                <div class="business-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?= htmlspecialchars($biz['city'] ?? 'Nederland') ?></span>
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
                    <?= $translations['view_book'] ?? 'Bekijk & Boek' ?>
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
            <h2><i class="fas fa-store"></i> <?= $translations['have_salon'] ?? 'Heb je een salon?' ?></h2>
            <p><?= $translations['have_salon_desc'] ?? 'Sluit je aan bij GlamourSchedule en ontvang online boekingen van nieuwe klanten' ?></p>
            <div class="business-cta-buttons">
                <a href="/business/register" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> <?= $translations['start_free'] ?? 'Start Gratis' ?>
                </a>
                <span class="badge">
                    <i class="fas fa-tag"></i> <?= str_replace(':count', '20', $translations['first_salons_offer'] ?? 'Eerste 20 salons: slechts') ?> &euro;0,99
                </span>
            </div>
        </div>

        <!-- Sales Partner CTA -->
        <div class="sales-cta">
            <div>
                <h3><i class="fas fa-handshake"></i> Word Sales Partner</h3>
                <p>Verdien commissie door bedrijven aan te melden bij GlamourSchedule</p>
            </div>
            <a href="/sales/register" class="btn">
                <i class="fas fa-arrow-right"></i> Start nu
            </a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section section-gray">
    <div class="section-header">
        <div class="section-tag"><?= $translations['steps_label'] ?? 'Hoe het werkt' ?></div>
        <h2 class="section-title"><?= $translations['steps_title'] ?? 'In 3 Stappen Geboekt' ?></h2>
        <p class="section-subtitle"><?= $translations['steps_subtitle'] ?? 'Snel en eenvoudig je afspraak maken' ?></p>
    </div>

    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <h3 class="step-title"><?= $translations['step1_title'] ?? 'Zoek een salon' ?></h3>
            <p class="step-desc"><?= $translations['step1_desc'] ?? 'Vind de perfecte salon bij jou in de buurt op basis van dienst, locatie of reviews' ?></p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <h3 class="step-title"><?= $translations['step2_title'] ?? 'Kies een dienst' ?></h3>
            <p class="step-desc"><?= $translations['step2_desc'] ?? 'Bekijk alle diensten met prijzen, duur en beschikbare tijden in realtime' ?></p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <h3 class="step-title"><?= $translations['step3_title'] ?? 'Boek direct' ?></h3>
            <p class="step-desc"><?= $translations['step3_desc'] ?? 'Bevestig je afspraak met een paar klikken en ontvang direct een bevestiging' ?></p>
        </div>
    </div>
</section>

<!-- Final CTA -->
<div class="cta-prestige">
    <div>
        <h2><?= $translations['cta_title'] ?? 'Laat Je Salon Groeien' ?></h2>
        <p><?= $translations['cta_subtitle'] ?? 'Sluit je aan bij het premium beauty platform.' ?></p>
    </div>
    <a href="/business/register" class="btn btn-primary btn-lg"><?= $translations['cta_start'] ?? 'Start Nu' ?></a>
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
    color: #ffffff;
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
