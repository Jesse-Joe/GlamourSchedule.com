<?php ob_start(); ?>

<style>
    .about-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }
    .about-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .about-header {
        background: #ffffff;
        color: #000000;
        padding: 3rem 2rem;
        text-align: center;
        border-bottom: 2px solid #000000;
    }
    .about-header h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        color: #000000;
    }
    .about-header p {
        margin-top: 0.75rem;
        font-size: 1.1rem;
        color: #000000;
    }
    .about-body {
        padding: 2.5rem;
        line-height: 1.8;
        color: var(--text);
    }
    .about-section {
        margin-bottom: 3rem;
    }
    .about-section:last-child {
        margin-bottom: 0;
    }
    .about-section h2 {
        color: #000000;
        font-size: 1.4rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .about-section h2 i {
        color: #000000;
    }
    .about-section p {
        color: #4b5563;
        margin-bottom: 1rem;
    }

    /* How it works steps */
    .steps-container {
        display: grid;
        gap: 1.5rem;
        margin-top: 1.5rem;
    }
    .step-card {
        display: flex;
        gap: 1.25rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        border-radius: 16px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .step-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(218, 165, 32, 0.15);
    }
    .step-number {
        width: 48px;
        height: 48px;
        background: #000000;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .step-content h3 {
        color: #374151;
        font-size: 1.1rem;
        margin: 0 0 0.5rem 0;
    }
    .step-content p {
        color: #6b7280;
        margin: 0;
        font-size: 0.95rem;
    }

    /* Features grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-top: 1.5rem;
    }
    .feature-card {
        background: #fafafa;
        border-radius: 14px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .feature-card i {
        font-size: 2rem;
        color: #000000;
        margin-bottom: 1rem;
    }
    .feature-card h3 {
        color: #374151;
        font-size: 1rem;
        margin: 0 0 0.5rem 0;
    }
    .feature-card p {
        color: #6b7280;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Stats */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 2rem 0;
        text-align: center;
    }
    .stat-item {
        padding: 1.5rem;
        background: linear-gradient(135deg, #000000 0%, #000000 30%, #000000 70%, #333333 100%);
        border-radius: 14px;
        color: white;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }
    .stat-label {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    /* Mission box */
    .mission-box {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        border-left: 4px solid #000000;
    }
    .mission-box p {
        color: #262626;
        font-size: 1.1rem;
        font-style: italic;
        margin: 0;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #000000 0%, #000000 30%, #000000 70%, #333333 100%);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
    }
    .cta-section h3 {
        color: white;
        font-size: 1.3rem;
        margin: 0 0 1rem 0;
    }
    .cta-section p {
        color: rgba(255,255,255,0.9);
        margin-bottom: 1.5rem;
    }
    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .cta-btn-white {
        background: #ffffff;
        color: #000000;
    }
    .cta-btn-outline {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    @media (max-width: 768px) {
        .about-header h1 {
            font-size: 1.6rem;
        }
        .about-body {
            padding: 1.5rem;
        }
        .stats-row {
            grid-template-columns: 1fr;
        }
        .step-card {
            flex-direction: column;
            text-align: center;
        }
        .step-number {
            margin: 0 auto;
        }
    }

    /* Dark Mode */
    [data-theme="dark"] .about-card {
        background: var(--bg-card);
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .about-section p {
        color: var(--text-light);
    }
    [data-theme="dark"] .step-card {
        background: var(--bg-secondary);
    }
    [data-theme="dark"] .step-content h3 {
        color: var(--text);
    }
    [data-theme="dark"] .step-content p {
        color: var(--text-light);
    }
    [data-theme="dark"] .feature-card {
        background: var(--bg-secondary);
    }
    [data-theme="dark"] .feature-card h3 {
        color: var(--text);
    }
    [data-theme="dark"] .feature-card p {
        color: var(--text-light);
    }
    [data-theme="dark"] .mission-box {
        background: var(--bg-secondary);
    }
</style>

<div class="about-container">
    <div class="about-card">
        <div class="about-header">
            <h1><i class="fas fa-heart"></i> <?= $lang === 'nl' ? 'Over GlamourSchedule' : 'About GlamourSchedule' ?></h1>
            <p><?= $lang === 'nl' ? 'Het online boekingsplatform voor beauty & wellness' : 'The online booking platform for beauty & wellness' ?></p>
        </div>

        <div class="about-body">
            <?php if ($lang === 'nl'): ?>
            <!-- DUTCH VERSION -->

            <div class="about-section">
                <h2><i class="fas fa-sparkles"></i> Wat is GlamourSchedule?</h2>
                <p>GlamourSchedule is het moderne boekingsplatform dat beauty- en wellnesssalons verbindt met klanten. Wij maken het makkelijk om je favoriete behandelingen te boeken, wanneer het jou uitkomt.</p>
                <p>Of je nu op zoek bent naar een kapper, nagelstudio, schoonheidssalon of massagepraktijk - bij ons vind je alles op één plek. Geen gedoe met telefoontjes of wachten op een reactie. Direct online boeken, 24/7.</p>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-user"></i> Hoe werkt het voor klanten?</h2>
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Zoek een salon</h3>
                            <p>Blader door salons in jouw buurt of zoek op behandeling. Bekijk foto's, reviews en beschikbaarheid.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Kies je behandeling</h3>
                            <p>Selecteer de gewenste service, bekijk de prijs en duur, en kies een datum en tijd die jou uitkomt.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Boek en betaal</h3>
                            <p>Rond je boeking af met een veilige online betaling via iDEAL, creditcard of andere methoden.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Geniet van je afspraak</h3>
                            <p>Ontvang je bevestiging met QR-code per e-mail. Check in bij de salon en geniet van je behandeling!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-store"></i> Voor salons en bedrijven</h2>
                <p>GlamourSchedule biedt salons een compleet pakket om hun business te laten groeien:</p>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>Online Agenda</h3>
                        <p>Beheer al je afspraken in één overzichtelijk dashboard</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-credit-card"></i>
                        <h3>Veilige Betalingen</h3>
                        <p>Ontvang betalingen direct via Mollie, zonder gedoe</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-globe"></i>
                        <h3>Eigen Webpagina</h3>
                        <p>Krijg je eigen professionele pagina om te delen</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-star"></i>
                        <h3>Reviews & Ratings</h3>
                        <p>Bouw vertrouwen op met klantbeoordelingen</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-qrcode"></i>
                        <h3>QR Check-in</h3>
                        <p>Snelle check-in van klanten met QR-scanner</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Inzichten</h3>
                        <p>Bekijk statistieken over je boekingen en omzet</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-shield-alt"></i> Waarom GlamourSchedule?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-clock"></i>
                        <h3>24/7 Beschikbaar</h3>
                        <p>Boek wanneer het jou uitkomt, ook buiten openingstijden</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-lock"></i>
                        <h3>Veilig & Betrouwbaar</h3>
                        <p>SSL-beveiliging en veilige betalingen via Mollie</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Mobielvriendelijk</h3>
                        <p>Werkt perfect op telefoon, tablet en computer</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-headset"></i>
                        <h3>Nederlandse Support</h3>
                        <p>Vragen? Ons team helpt je graag verder</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-bullseye"></i> Onze Missie</h2>
                <div class="mission-box">
                    <p>"Wij geloven dat iedereen makkelijk toegang moet hebben tot beauty- en wellnessbehandelingen. Door technologie en persoonlijke service te combineren, maken wij het boekingsproces eenvoudig voor zowel klanten als ondernemers."</p>
                </div>
            </div>

            <div class="cta-section">
                <h3>Klaar om te beginnen?</h3>
                <p>Ontdek salons bij jou in de buurt of meld je salon aan</p>
                <div class="cta-buttons">
                    <a href="/search" class="cta-btn cta-btn-white">
                        <i class="fas fa-search"></i> Zoek salons
                    </a>
                    <a href="/business/register" class="cta-btn cta-btn-outline">
                        <i class="fas fa-store"></i> Salon aanmelden
                    </a>
                </div>
            </div>

            <?php else: ?>
            <!-- ENGLISH VERSION -->

            <div class="about-section">
                <h2><i class="fas fa-sparkles"></i> What is GlamourSchedule?</h2>
                <p>GlamourSchedule is the modern booking platform that connects beauty and wellness salons with customers. We make it easy to book your favorite treatments whenever it suits you.</p>
                <p>Whether you're looking for a hairdresser, nail studio, beauty salon or massage practice - you'll find everything in one place. No hassle with phone calls or waiting for a response. Book directly online, 24/7.</p>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-user"></i> How does it work for customers?</h2>
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Find a salon</h3>
                            <p>Browse salons in your area or search by treatment. View photos, reviews and availability.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Choose your treatment</h3>
                            <p>Select the desired service, view the price and duration, and choose a date and time that suits you.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Book and pay</h3>
                            <p>Complete your booking with a secure online payment via iDEAL, credit card or other methods.</p>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Enjoy your appointment</h3>
                            <p>Receive your confirmation with QR code by email. Check in at the salon and enjoy your treatment!</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-store"></i> For salons and businesses</h2>
                <p>GlamourSchedule offers salons a complete package to grow their business:</p>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>Online Calendar</h3>
                        <p>Manage all your appointments in one clear dashboard</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-credit-card"></i>
                        <h3>Secure Payments</h3>
                        <p>Receive payments directly via Mollie, hassle-free</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-globe"></i>
                        <h3>Own Webpage</h3>
                        <p>Get your own professional page to share</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-star"></i>
                        <h3>Reviews & Ratings</h3>
                        <p>Build trust with customer reviews</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-qrcode"></i>
                        <h3>QR Check-in</h3>
                        <p>Quick customer check-in with QR scanner</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Insights</h3>
                        <p>View statistics about your bookings and revenue</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-shield-alt"></i> Why GlamourSchedule?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <i class="fas fa-clock"></i>
                        <h3>24/7 Available</h3>
                        <p>Book whenever it suits you, even outside business hours</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-lock"></i>
                        <h3>Safe & Reliable</h3>
                        <p>SSL security and secure payments via Mollie</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Mobile Friendly</h3>
                        <p>Works perfectly on phone, tablet and computer</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-headset"></i>
                        <h3>Dutch Support</h3>
                        <p>Questions? Our team is happy to help</p>
                    </div>
                </div>
            </div>

            <div class="about-section">
                <h2><i class="fas fa-bullseye"></i> Our Mission</h2>
                <div class="mission-box">
                    <p>"We believe everyone should have easy access to beauty and wellness treatments. By combining technology and personal service, we make the booking process simple for both customers and entrepreneurs."</p>
                </div>
            </div>

            <div class="cta-section">
                <h3>Ready to get started?</h3>
                <p>Discover salons near you or register your salon</p>
                <div class="cta-buttons">
                    <a href="/search" class="cta-btn cta-btn-white">
                        <i class="fas fa-search"></i> Find salons
                    </a>
                    <a href="/business/register" class="cta-btn cta-btn-outline">
                        <i class="fas fa-store"></i> Register salon
                    </a>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
