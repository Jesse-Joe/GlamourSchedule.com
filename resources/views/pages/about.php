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
            <h1>Platform Functionaliteit</h1>
            <p>Ontdek alle features die GlamourSchedule te bieden heeft voor klanten en salons</p>
        </div>
    </section>

    <!-- For Customers -->
    <section class="func-section">
        <div class="func-section-header">
            <h2>Voor Klanten</h2>
            <p>Eenvoudig zoeken, boeken en betalen - allemaal online</p>
        </div>

        <div class="func-grid">
            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Geavanceerd Zoeken</h3>
                <p>Vind snel de perfecte salon met onze uitgebreide zoekfunctie.</p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> Zoeken op locatie en postcode</li>
                    <li><i class="fas fa-check"></i> Filteren op categorie</li>
                    <li><i class="fas fa-check"></i> Prijsfilters (min/max)</li>
                    <li><i class="fas fa-check"></i> Sorteren op beoordeling</li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Beschikbaarheidsfilters</h3>
                <p>Vind salons die open zijn wanneer jij beschikbaar bent.</p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> Nu geopend filter</li>
                    <li><i class="fas fa-check"></i> Open in weekend</li>
                    <li><i class="fas fa-check"></i> Avondopening</li>
                    <li><i class="fas fa-check"></i> 4+ sterren filter</li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>Online Boeken</h3>
                <p>Boek direct online zonder te bellen, 24/7 beschikbaar.</p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> Real-time beschikbaarheid</li>
                    <li><i class="fas fa-check"></i> Direct bevestiging</li>
                    <li><i class="fas fa-check"></i> E-mail herinneringen</li>
                    <li><i class="fas fa-check"></i> Makkelijk wijzigen/annuleren</li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3>Veilig Betalen</h3>
                <p>Betaal online met je favoriete betaalmethode via Mollie.</p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> iDEAL</li>
                    <li><i class="fas fa-check"></i> Creditcard</li>
                    <li><i class="fas fa-check"></i> Apple Pay / Google Pay</li>
                    <li><i class="fas fa-check"></i> Bancontact</li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h3>QR-Code Check-in</h3>
                <p>Ontvang een QR-code per e-mail voor snelle check-in bij de salon.</p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> Automatische e-mail met QR</li>
                    <li><i class="fas fa-check"></i> Snelle check-in</li>
                    <li><i class="fas fa-check"></i> Geen wachttijd</li>
                    <li><i class="fas fa-check"></i> Digitaal bewijs</li>
                </ul>
            </div>

            <div class="func-card">
                <div class="func-card-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Reviews & Ratings</h3>
                <p>Lees beoordelingen van andere klanten en deel je eigen ervaring.</p>
                <ul class="func-card-features">
                    <li><i class="fas fa-check"></i> Geverifieerde reviews</li>
                    <li><i class="fas fa-check"></i> Sterrenbeoordelingen</li>
                    <li><i class="fas fa-check"></i> Foto reviews</li>
                    <li><i class="fas fa-check"></i> Salon reacties</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="func-section func-section-alt">
        <div class="func-stats">
            <div class="func-stat">
                <span class="func-stat-number">50+</span>
                <span class="func-stat-label">Categorien</span>
            </div>
            <div class="func-stat">
                <span class="func-stat-number">24/7</span>
                <span class="func-stat-label">Online Boeken</span>
            </div>
            <div class="func-stat">
                <span class="func-stat-number">100%</span>
                <span class="func-stat-label">Veilig Betalen</span>
            </div>
            <div class="func-stat">
                <span class="func-stat-number">NL</span>
                <span class="func-stat-label">Support</span>
            </div>
        </div>
    </section>

    <!-- For Business -->
    <section class="func-section">
        <div class="func-section-header">
            <h2>Voor Salons</h2>
            <p>Alles wat je nodig hebt om je salon professioneel te runnen</p>
        </div>

        <!-- Highlight 1: Dashboard -->
        <div class="func-highlight">
            <div class="func-highlight-content">
                <h3>Compleet Dashboard</h3>
                <p>Beheer je hele salon vanuit een overzichtelijk dashboard. Van afspraken tot statistieken - alles op een plek.</p>
                <ul class="func-highlight-list">
                    <li><i class="fas fa-check"></i> Overzicht van alle boekingen</li>
                    <li><i class="fas fa-check"></i> Omzet en statistieken</li>
                    <li><i class="fas fa-check"></i> Klantenoverzicht</li>
                    <li><i class="fas fa-check"></i> Agenda integratie</li>
                </ul>
            </div>
            <div class="func-highlight-visual">
                <i class="fas fa-chart-pie"></i>
            </div>
        </div>

        <!-- Highlight 2: Services -->
        <div class="func-highlight reverse">
            <div class="func-highlight-content">
                <h3>Diensten Beheer</h3>
                <p>Voeg eenvoudig je diensten toe met prijzen, duur en beschrijvingen. Organiseer ze in categorien.</p>
                <ul class="func-highlight-list">
                    <li><i class="fas fa-check"></i> Onbeperkt diensten toevoegen</li>
                    <li><i class="fas fa-check"></i> Prijzen en duur instellen</li>
                    <li><i class="fas fa-check"></i> Categorieen organiseren</li>
                    <li><i class="fas fa-check"></i> Actief/inactief zetten</li>
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
                    <h4>Eigen Salonpagina</h4>
                    <p>Professionele pagina met foto's, diensten en reviews</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <h4>Openingstijden</h4>
                    <p>Stel je openingstijden per dag in</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-qrcode"></i>
                <div>
                    <h4>QR Scanner</h4>
                    <p>Scan QR-codes voor snelle check-in</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-euro-sign"></i>
                <div>
                    <h4>Automatische Betalingen</h4>
                    <p>Ontvang betalingen direct via Mollie</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-bell"></i>
                <div>
                    <h4>Notificaties</h4>
                    <p>E-mail alerts voor nieuwe boekingen</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-images"></i>
                <div>
                    <h4>Foto Galerij</h4>
                    <p>Toon je werk met foto's en portfolio</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-users"></i>
                <div>
                    <h4>Klantenbeheer</h4>
                    <p>Houd klantgegevens en historie bij</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-chart-line"></i>
                <div>
                    <h4>Inzichten & Analytics</h4>
                    <p>Bekijk statistieken en groei</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-rocket"></i>
                <div>
                    <h4>Boost Functie</h4>
                    <p>Verhoog je zichtbaarheid in zoekresultaten</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-mobile-alt"></i>
                <div>
                    <h4>Mobielvriendelijk</h4>
                    <p>Werkt perfect op alle apparaten</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <h4>SSL Beveiliging</h4>
                    <p>Veilige verbinding en data</p>
                </div>
            </div>
            <div class="business-feature">
                <i class="fas fa-headset"></i>
                <div>
                    <h4>Nederlandse Support</h4>
                    <p>Hulp wanneer je het nodig hebt</p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="func-cta">
            <h3>Start vandaag nog met je salon</h3>
            <p>Meld je salon aan voor slechts &euro;0,99 en profiteer van alle functionaliteiten.</p>
            <div class="func-cta-btns">
                <a href="/register?type=business" class="func-cta-btn func-cta-btn-white">
                    <i class="fas fa-store"></i> Salon Aanmelden
                </a>
                <a href="/search" class="func-cta-btn func-cta-btn-outline">
                    <i class="fas fa-search"></i> Bekijk Salons
                </a>
            </div>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
