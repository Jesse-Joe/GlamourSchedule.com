<?php ob_start(); ?>

<style>
/* Marketing Page */
.marketing-page {
    padding-top: 0;
}

/* Hero Section */
.marketing-hero {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    padding: 4rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.marketing-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 50%, rgba(255,255,255,0.05) 0%, transparent 50%);
    pointer-events: none;
}
.marketing-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.marketing-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 1rem;
    line-height: 1.2;
}
.marketing-hero p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.8);
    margin: 0 0 2rem;
    line-height: 1.6;
}
.marketing-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    color: #ffffff;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}
.marketing-hero-badge i {
    color: #fbbf24;
}

@media (min-width: 768px) {
    .marketing-hero {
        padding: 6rem 2rem;
    }
    .marketing-hero h1 {
        font-size: 3.5rem;
    }
    .marketing-hero p {
        font-size: 1.3rem;
    }
}

/* Marketing Section */
.marketing-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 1.5rem;
}
.marketing-section-dark {
    background: #000000;
    max-width: 100%;
    padding: 4rem 1.5rem;
}
.marketing-section-dark .marketing-section-inner {
    max-width: 1200px;
    margin: 0 auto;
}
.marketing-section-header {
    text-align: center;
    margin-bottom: 3rem;
}
.marketing-section-header h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.75rem;
}
.marketing-section-header p {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.8);
    max-width: 600px;
    margin: 0 auto;
}

/* Services Grid */
.marketing-services {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.marketing-service-card {
    background: #1a1a1a;
    border: 2px solid #333333;
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.3s ease;
}
.marketing-service-card:hover {
    border-color: #ffffff;
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(255,255,255,0.1);
}
.marketing-service-icon {
    width: 60px;
    height: 60px;
    background: #ffffff;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
}
.marketing-service-icon i {
    font-size: 1.5rem;
    color: #000000;
}
.marketing-service-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.75rem;
}
.marketing-service-card p {
    font-size: 0.95rem;
    color: #999999;
    line-height: 1.6;
    margin: 0;
}

/* Pricing Section */
.marketing-pricing {
    background: #111111;
}
.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
}
.pricing-card {
    background: #1a1a1a;
    border: 2px solid #333333;
    border-radius: 24px;
    padding: 2.5rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
}
.pricing-card.featured {
    border-color: #ffffff;
    transform: scale(1.02);
}
.pricing-card.featured::before {
    content: 'Populair';
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #ffffff;
    color: #000000;
    padding: 0.35rem 1rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
}
.pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(255,255,255,0.1);
}
.pricing-card.featured:hover {
    transform: scale(1.02) translateY(-5px);
}
.pricing-card h3 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem;
}
.pricing-card .price {
    font-size: 3rem;
    font-weight: 800;
    color: #ffffff;
    margin: 1rem 0;
}
.pricing-card .price span {
    font-size: 1rem;
    font-weight: 500;
    color: #999999;
}
.pricing-card .price-note {
    font-size: 0.9rem;
    color: #999999;
    margin-bottom: 1.5rem;
}
.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem;
    text-align: left;
}
.pricing-features li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 0;
    font-size: 0.95rem;
    color: #e0e0e0;
    border-bottom: 1px solid #333333;
}
.pricing-features li:last-child {
    border-bottom: none;
}
.pricing-features li i {
    color: #22c55e;
    font-size: 0.9rem;
    margin-top: 0.15rem;
}
.pricing-btn {
    display: block;
    width: 100%;
    padding: 1rem;
    background: #ffffff;
    color: #000000;
    border: none;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}
.pricing-btn:hover {
    background: #e0e0e0;
    transform: translateY(-2px);
}
.pricing-btn-outline {
    background: transparent;
    color: #ffffff;
    border: 2px solid #ffffff;
}
.pricing-btn-outline:hover {
    background: #ffffff;
    color: #000000;
}

/* Features Section */
.features-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}
.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.5rem;
    background: #1a1a1a;
    border-radius: 16px;
    border: 1px solid #333333;
}
.feature-item i {
    font-size: 1.5rem;
    color: #ffffff;
    flex-shrink: 0;
}
.feature-item h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 0.25rem;
}
.feature-item p {
    font-size: 0.9rem;
    color: #999999;
    margin: 0;
}

/* CTA Section */
.marketing-cta {
    background: #000000;
    padding: 4rem 1.5rem;
    text-align: center;
}
.marketing-cta h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 1rem;
}
.marketing-cta p {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.8);
    margin: 0 0 2rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}
.marketing-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2.5rem;
    background: #ffffff;
    color: #000000;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}
.marketing-cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255,255,255,0.2);
}

/* Contact Info */
.contact-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}
.contact-info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
}
.contact-info-card i {
    font-size: 1.5rem;
    color: #ffffff;
}
.contact-info-card span {
    color: rgba(255,255,255,0.9);
    font-size: 1rem;
}
.contact-info-card a {
    color: rgba(255,255,255,0.9);
    text-decoration: none;
}
.contact-info-card a:hover {
    color: #ffffff;
}
</style>

<div class="marketing-page">
    <!-- Hero -->
    <section class="marketing-hero">
        <div class="marketing-hero-content">
            <div class="marketing-hero-badge">
                <i class="fas fa-bullhorn"></i>
                Marketing Services
            </div>
            <h1>Laat je salon groeien met onze marketing</h1>
            <p>Van social media tot Google Ads - wij helpen jouw salon meer klanten te bereiken met professionele marketingcampagnes.</p>
        </div>
    </section>

    <!-- Services -->
    <section class="marketing-section-dark">
        <div class="marketing-section-inner">
        <div class="marketing-section-header">
            <h2>Onze Marketing Diensten</h2>
            <p>Kies uit ons complete pakket aan marketingoplossingen speciaal voor beauty & wellness</p>
        </div>

        <div class="marketing-services">
            <div class="marketing-service-card">
                <div class="marketing-service-icon">
                    <i class="fab fa-instagram"></i>
                </div>
                <h3>Social Media Marketing</h3>
                <p>Professioneel beheer van je Instagram, Facebook en TikTok. Content creatie, posting en community management voor meer engagement.</p>
            </div>

            <div class="marketing-service-card">
                <div class="marketing-service-icon">
                    <i class="fab fa-google"></i>
                </div>
                <h3>Google Ads</h3>
                <p>Gerichte advertentiecampagnes op Google voor lokale zichtbaarheid. Bereik klanten die actief zoeken naar jouw diensten.</p>
            </div>

            <div class="marketing-service-card">
                <div class="marketing-service-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>SEO Optimalisatie</h3>
                <p>Verbeter je online vindbaarheid met zoekmachineoptimalisatie. Hoger scoren in Google voor relevante zoektermen.</p>
            </div>

            <div class="marketing-service-card">
                <div class="marketing-service-icon">
                    <i class="fas fa-camera"></i>
                </div>
                <h3>Content Creatie</h3>
                <p>Professionele foto's en video's van je salon en behandelingen. Perfect voor social media en je website.</p>
            </div>

            <div class="marketing-service-card">
                <div class="marketing-service-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>E-mail Marketing</h3>
                <p>Automatische e-mailcampagnes voor klantbehoud. Nieuwsbrieven, aanbiedingen en herinnering voor afspraken.</p>
            </div>

            <div class="marketing-service-card">
                <div class="marketing-service-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Analytics & Rapportage</h3>
                <p>Maandelijkse rapportages over je marketingprestaties. Inzicht in bereik, conversies en ROI van je campagnes.</p>
            </div>
        </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="marketing-section marketing-pricing">
        <div class="marketing-section-header">
            <h2>Marketing Pakketten</h2>
            <p>Kies het pakket dat bij jouw salon past</p>
        </div>

        <div class="pricing-grid">
            <div class="pricing-card">
                <h3>Starter</h3>
                <div class="price">&euro;199<span> eenmalig</span></div>
                <div class="price-note">30 dagen campagne</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Social media beheer (2 platforms)</li>
                    <li><i class="fas fa-check"></i> 8 posts in 30 dagen</li>
                    <li><i class="fas fa-check"></i> Basis content creatie</li>
                    <li><i class="fas fa-check"></i> Rapportage na afloop</li>
                    <li><i class="fas fa-check"></i> E-mail support</li>
                </ul>
                <a href="/contact" class="pricing-btn pricing-btn-outline">Neem contact op</a>
            </div>

            <div class="pricing-card featured">
                <h3>Professional</h3>
                <div class="price">&euro;399<span> eenmalig</span></div>
                <div class="price-note">30 dagen campagne</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Social media beheer (3 platforms)</li>
                    <li><i class="fas fa-check"></i> 16 posts in 30 dagen</li>
                    <li><i class="fas fa-check"></i> Google Ads campagnes</li>
                    <li><i class="fas fa-check"></i> Professionele content</li>
                    <li><i class="fas fa-check"></i> SEO optimalisatie</li>
                    <li><i class="fas fa-check"></i> Uitgebreide rapportage</li>
                    <li><i class="fas fa-check"></i> Telefonische support</li>
                </ul>
                <a href="/contact" class="pricing-btn">Neem contact op</a>
            </div>

            <div class="pricing-card">
                <h3>Enterprise</h3>
                <div class="price">Op maat</div>
                <div class="price-note">Aangepaste duur & scope</div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> Onbeperkt social media</li>
                    <li><i class="fas fa-check"></i> Dagelijkse posts</li>
                    <li><i class="fas fa-check"></i> Full-service campagnes</li>
                    <li><i class="fas fa-check"></i> Fotoshoot inbegrepen</li>
                    <li><i class="fas fa-check"></i> Dedicated accountmanager</li>
                    <li><i class="fas fa-check"></i> Persoonlijke support</li>
                </ul>
                <a href="/contact" class="pricing-btn pricing-btn-outline">Offerte aanvragen</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="marketing-section-dark">
        <div class="marketing-section-inner">
        <div class="marketing-section-header">
            <h2>Waarom kiezen voor ons?</h2>
            <p>Wij kennen de beauty & wellness branche als geen ander</p>
        </div>

        <div class="features-list">
            <div class="feature-item">
                <i class="fas fa-spa"></i>
                <div>
                    <h4>Beauty Expertise</h4>
                    <p>Jarenlange ervaring in de beauty sector</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-users"></i>
                <div>
                    <h4>Dedicated Team</h4>
                    <p>Persoonlijke begeleiding door experts</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-bar"></i>
                <div>
                    <h4>Meetbare Resultaten</h4>
                    <p>Transparante rapportages en KPI's</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-rocket"></i>
                <div>
                    <h4>Snelle Start</h4>
                    <p>Binnen 1 week live met je campagnes</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-handshake"></i>
                <div>
                    <h4>Geen abonnement</h4>
                    <p>Eenmalige betaling, geen verplichtingen</p>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <div>
                    <h4>Nederlandse Support</h4>
                    <p>Altijd bereikbaar voor vragen</p>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="marketing-cta">
        <h2>Klaar om te groeien?</h2>
        <p>Plan een gratis adviesgesprek en ontdek wat marketing voor jouw salon kan betekenen.</p>
        <a href="/contact" class="marketing-cta-btn">
            <i class="fas fa-calendar-check"></i> Plan een gesprek
        </a>

        <div class="contact-info-grid" style="max-width: 800px; margin: 3rem auto 0;">
            <div class="contact-info-card">
                <i class="fas fa-envelope"></i>
                <a href="mailto:marketing@glamourschedule.nl">marketing@glamourschedule.nl</a>
            </div>
            <div class="contact-info-card">
                <i class="fas fa-phone"></i>
                <a href="tel:+31612345678">+31 6 12 34 56 78</a>
            </div>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
