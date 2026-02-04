<?php ob_start(); ?>

<style>
/* Pricing Page */
.pricing-page {
    padding-top: 6rem;
    background: #000000;
    min-height: 100vh;
}

/* Hero Section */
.pricing-hero {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    padding: 4rem 1.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.pricing-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 70% 30%, rgba(255,255,255,0.05) 0%, transparent 50%);
    pointer-events: none;
}
.pricing-hero-content {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}
.pricing-hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 1rem;
    line-height: 1.2;
}
.pricing-hero p {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.8);
    margin: 0;
    line-height: 1.6;
}
@media (min-width: 768px) {
    .pricing-hero {
        padding: 5rem 2rem;
    }
    .pricing-hero h1 {
        font-size: 3rem;
    }
}

/* Pricing Cards Container */
.pricing-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 1.5rem;
}

.pricing-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
    margin-bottom: 4rem;
}
@media (min-width: 768px) {
    .pricing-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .pricing-cards {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Pricing Card */
.pricing-card {
    background: #111111;
    border: 2px solid #333333;
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    transition: all 0.3s ease;
}
.pricing-card:hover {
    border-color: #555555;
    transform: translateY(-5px);
}
.pricing-card.featured {
    border-color: #22c55e;
    background: linear-gradient(135deg, #111111 0%, #0a1f0a 100%);
}
.pricing-card.featured:hover {
    border-color: #4ade80;
}

/* Featured Badge */
.pricing-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.4rem 1.25rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.pricing-badge i {
    margin-right: 0.35rem;
}

/* Card Header */
.pricing-card-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #333333;
}
.pricing-card-icon {
    width: 64px;
    height: 64px;
    background: #ffffff;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}
.pricing-card-icon i {
    font-size: 1.5rem;
    color: #000000;
}
.pricing-card h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem;
}
.pricing-card-subtitle {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.6);
    margin: 0;
}

/* Price Display */
.pricing-price {
    text-align: center;
    margin-bottom: 2rem;
}
.pricing-price-old {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.4);
    text-decoration: line-through;
    margin-bottom: 0.25rem;
}
.pricing-price-amount {
    font-size: 3.5rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
}
.pricing-price-amount .currency {
    font-size: 1.75rem;
    vertical-align: super;
}
.pricing-price-period {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.6);
    margin-top: 0.5rem;
}
.pricing-price-local {
    font-size: 0.9rem;
    color: #22c55e;
    margin-top: 0.5rem;
}

/* Spots Left Badge */
.spots-left-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    margin-top: 1rem;
}
.spots-left-badge i {
    font-size: 0.75rem;
}

/* Features List */
.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem;
}
.pricing-features li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.6rem 0;
    font-size: 0.95rem;
    color: rgba(255,255,255,0.85);
}
.pricing-features li i {
    color: #22c55e;
    font-size: 0.85rem;
    margin-top: 0.2rem;
    flex-shrink: 0;
}
.pricing-features li.disabled {
    color: rgba(255,255,255,0.4);
}
.pricing-features li.disabled i {
    color: rgba(255,255,255,0.3);
}

/* CTA Button */
.pricing-cta {
    display: block;
    width: 100%;
    padding: 1rem 1.5rem;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
}
.pricing-cta-primary {
    background: #ffffff;
    color: #000000;
}
.pricing-cta-primary:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
}
.pricing-cta-secondary {
    background: transparent;
    color: #ffffff;
    border: 2px solid #555555;
}
.pricing-cta-secondary:hover {
    border-color: #ffffff;
    transform: translateY(-2px);
}

/* Transaction Fee Section */
.transaction-fee {
    background: #111111;
    border: 2px solid #333333;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 4rem;
}
.transaction-fee-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.transaction-fee-icon {
    width: 48px;
    height: 48px;
    background: #ffffff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.transaction-fee-icon i {
    font-size: 1.25rem;
    color: #000000;
}
.transaction-fee-header h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}
.transaction-fee-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 768px) {
    .transaction-fee-content {
        grid-template-columns: 1fr 1fr;
    }
}
.transaction-fee-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}
.transaction-fee-item i {
    color: #22c55e;
    font-size: 1rem;
    margin-top: 0.2rem;
}
.transaction-fee-item h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 0.25rem;
}
.transaction-fee-item p {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
    margin: 0;
    line-height: 1.5;
}

/* FAQ Section */
.pricing-faq {
    margin-bottom: 4rem;
}
.pricing-faq h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    margin: 0 0 2rem;
}
.faq-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 768px) {
    .faq-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
.faq-item {
    background: #111111;
    border: 1px solid #333333;
    border-radius: 16px;
    padding: 1.5rem;
}
.faq-item h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 0.75rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}
.faq-item h4 i {
    color: #22c55e;
    margin-top: 0.1rem;
}
.faq-item p {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.7);
    margin: 0;
    line-height: 1.6;
    padding-left: 1.75rem;
}

/* Comparison Table */
.comparison-section {
    margin-bottom: 4rem;
}
.comparison-section h2 {
    font-size: 2rem;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    margin: 0 0 2rem;
}
.comparison-table {
    background: #111111;
    border: 2px solid #333333;
    border-radius: 20px;
    overflow: hidden;
}
.comparison-table table {
    width: 100%;
    border-collapse: collapse;
}
.comparison-table th,
.comparison-table td {
    padding: 1rem 1.25rem;
    text-align: left;
    border-bottom: 1px solid #333333;
}
.comparison-table th {
    background: #1a1a1a;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.9rem;
}
.comparison-table td {
    color: rgba(255,255,255,0.85);
    font-size: 0.9rem;
}
.comparison-table tr:last-child td {
    border-bottom: none;
}
.comparison-table td:first-child {
    font-weight: 500;
}
.comparison-table .check {
    color: #22c55e;
}
.comparison-table .cross {
    color: rgba(255,255,255,0.3);
}

/* Bottom CTA */
.pricing-bottom-cta {
    background: linear-gradient(135deg, #111111 0%, #1a1a1a 100%);
    border: 2px solid #333333;
    border-radius: 24px;
    padding: 3rem 2rem;
    text-align: center;
}
.pricing-bottom-cta h3 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 1rem;
}
.pricing-bottom-cta p {
    font-size: 1rem;
    color: rgba(255,255,255,0.7);
    margin: 0 0 1.5rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}
.pricing-bottom-cta-btns {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<div class="pricing-page">
    <!-- Hero -->
    <section class="pricing-hero">
        <div class="pricing-hero-content">
            <h1><?= $translations['pricing_title'] ?? 'Transparante Prijzen' ?></h1>
            <p><?= $translations['pricing_subtitle'] ?? 'Eenvoudige, eerlijke prijzen zonder verborgen kosten. Betaal pas na je 14-dagen proefperiode.' ?></p>
        </div>
    </section>

    <!-- Pricing Cards -->
    <div class="pricing-container">
        <div class="pricing-cards">
            <!-- Customer Card - Free -->
            <div class="pricing-card">
                <div class="pricing-card-header">
                    <div class="pricing-card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3><?= $translations['for_customers'] ?? 'Voor Klanten' ?></h3>
                    <p class="pricing-card-subtitle"><?= $translations['customer_subtitle'] ?? 'Boek bij jouw favoriete salon' ?></p>
                </div>

                <div class="pricing-price">
                    <div class="pricing-price-amount"><span class="currency">&euro;</span>0</div>
                    <div class="pricing-price-period"><?= $translations['always_free'] ?? 'Altijd gratis' ?></div>
                </div>

                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> <?= $translations['unlimited_bookings'] ?? 'Onbeperkt boekingen maken' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['search_salons'] ?? 'Salons zoeken in je buurt' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['secure_payment'] ?? 'Veilig online betalen' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['email_confirmations'] ?? 'E-mail bevestigingen' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['qr_checkin'] ?? 'QR-code check-in' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['leave_reviews'] ?? 'Reviews achterlaten' ?></li>
                </ul>

                <a href="/search" class="pricing-cta pricing-cta-secondary">
                    <i class="fas fa-search"></i> <?= $translations['find_salon'] ?? 'Vind een Salon' ?>
                </a>
            </div>

            <!-- Early Bird Card -->
            <?php if (($promo['is_promo'] ?? false) && ($promo['spots_left'] ?? 0) > 0): ?>
            <div class="pricing-card featured">
                <div class="pricing-badge"><i class="fas fa-bolt"></i> Early Bird</div>
                <div class="pricing-card-header">
                    <div class="pricing-card-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3><?= $translations['early_bird'] ?? 'Early Bird' ?></h3>
                    <p class="pricing-card-subtitle"><?= $translations['early_bird_subtitle'] ?? 'Eerste 100 salons per land' ?></p>
                </div>

                <div class="pricing-price">
                    <div class="pricing-price-old"><?= $promo['local_original'] ?? '€99,99' ?></div>
                    <div class="pricing-price-amount"><?= $promo['local_price'] ?? '€0,99' ?></div>
                    <div class="pricing-price-period"><?= $translations['one_time_after_trial'] ?? 'eenmalig na 14 dagen proeftijd' ?></div>
                    <?php if ($showDualCurrency ?? false): ?>
                    <div class="pricing-price-local">(<?= $promo['eur_price'] ?? '€0,99' ?>)</div>
                    <?php endif; ?>
                    <div class="spots-left-badge">
                        <i class="fas fa-fire"></i>
                        <?= sprintf($translations['spots_left'] ?? 'Nog %d plekken in %s', $promo['spots_left'] ?? 0, $promo['country'] ?? 'jouw land') ?>
                    </div>
                </div>

                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> <?= $translations['trial_14_days'] ?? '14 dagen gratis uitproberen' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['own_salon_page'] ?? 'Eigen salonpagina' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['unlimited_services'] ?? 'Onbeperkt diensten' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['online_booking_system'] ?? 'Online boekingssysteem' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['automatic_payments'] ?? 'Automatische betalingen' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['dashboard_analytics'] ?? 'Dashboard & statistieken' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['qr_scanner'] ?? 'QR-code scanner' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['email_notifications'] ?? 'E-mail notificaties' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['lifetime_access'] ?? 'Levenslange toegang' ?></li>
                </ul>

                <a href="/business/register" class="pricing-cta pricing-cta-primary">
                    <i class="fas fa-rocket"></i> <?= $translations['claim_early_bird'] ?? 'Claim Early Bird' ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Standard Card -->
            <div class="pricing-card">
                <div class="pricing-card-header">
                    <div class="pricing-card-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3><?= $translations['for_salons'] ?? 'Voor Salons' ?></h3>
                    <p class="pricing-card-subtitle"><?= $translations['salon_subtitle'] ?? 'Volledige functionaliteit' ?></p>
                </div>

                <div class="pricing-price">
                    <div class="pricing-price-amount"><?= $promo['local_original'] ?? '€99,99' ?></div>
                    <div class="pricing-price-period"><?= $translations['one_time_after_trial'] ?? 'eenmalig na 14 dagen proeftijd' ?></div>
                    <?php if ($showDualCurrency ?? false): ?>
                    <div class="pricing-price-local">(<?= $promo['eur_original'] ?? '€99,99' ?>)</div>
                    <?php endif; ?>
                </div>

                <ul class="pricing-features">
                    <li><i class="fas fa-check"></i> <?= $translations['trial_14_days'] ?? '14 dagen gratis uitproberen' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['own_salon_page'] ?? 'Eigen salonpagina' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['unlimited_services'] ?? 'Onbeperkt diensten' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['online_booking_system'] ?? 'Online boekingssysteem' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['automatic_payments'] ?? 'Automatische betalingen' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['dashboard_analytics'] ?? 'Dashboard & statistieken' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['qr_scanner'] ?? 'QR-code scanner' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['email_notifications'] ?? 'E-mail notificaties' ?></li>
                    <li><i class="fas fa-check"></i> <?= $translations['lifetime_access'] ?? 'Levenslange toegang' ?></li>
                </ul>

                <a href="/business/register" class="pricing-cta pricing-cta-secondary">
                    <i class="fas fa-arrow-right"></i> <?= $translations['start_trial'] ?? 'Start Proefperiode' ?>
                </a>
            </div>
        </div>

        <!-- Transaction Fee Info -->
        <div class="transaction-fee">
            <div class="transaction-fee-header">
                <div class="transaction-fee-icon">
                    <i class="fas fa-hand-holding-euro"></i>
                </div>
                <h3><?= $translations['transaction_fees'] ?? 'Transactiekosten' ?></h3>
            </div>
            <div class="transaction-fee-content">
                <div class="transaction-fee-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4><?= $promo['fee_display'] ?? '€1,75' ?> <?= $translations['per_booking'] ?? 'per boeking' ?></h4>
                        <p><?= $translations['booking_fee_desc'] ?? 'Alleen wanneer een klant betaalt via het platform. Geen boeking = geen kosten.' ?></p>
                    </div>
                </div>
                <div class="transaction-fee-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4><?= $translations['no_hidden_costs'] ?? 'Geen verborgen kosten' ?></h4>
                        <p><?= $translations['no_hidden_costs_desc'] ?? 'Geen maandelijkse abonnementen, geen opstartkosten, geen minimum aantal boekingen.' ?></p>
                    </div>
                </div>
                <div class="transaction-fee-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4><?= $translations['automatic_payout'] ?? 'Automatische uitbetaling' ?></h4>
                        <p><?= $translations['automatic_payout_desc'] ?? 'Je ontvangt je geld automatisch na de afspraak op je gekoppelde bankrekening.' ?></p>
                    </div>
                </div>
                <div class="transaction-fee-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h4><?= $translations['transparent_pricing'] ?? 'Transparante prijzen' ?></h4>
                        <p><?= $translations['transparent_pricing_desc'] ?? 'Je ziet altijd precies wat je ontvangt per boeking in je dashboard.' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="comparison-section">
            <h2><?= $translations['what_you_get'] ?? 'Wat krijg je?' ?></h2>
            <div class="comparison-table">
                <table>
                    <thead>
                        <tr>
                            <th><?= $translations['feature'] ?? 'Functie' ?></th>
                            <th><?= $translations['included'] ?? 'Inbegrepen' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $translations['own_salon_page'] ?? 'Eigen salonpagina' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['unlimited_services'] ?? 'Onbeperkt diensten toevoegen' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['online_booking_system'] ?? 'Online boekingssysteem' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['secure_payments'] ?? 'Veilige betalingen (Mollie)' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['automatic_payout'] ?? 'Automatische uitbetalingen' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['dashboard_analytics'] ?? 'Dashboard & statistieken' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['qr_checkin_system'] ?? 'QR-code check-in systeem' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['email_notifications'] ?? 'E-mail notificaties' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['photo_gallery'] ?? 'Foto galerij' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['review_management'] ?? 'Review management' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['pos_system'] ?? 'Digitaal kassasysteem' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                        <tr>
                            <td><?= $translations['international_support'] ?? 'International support' ?></td>
                            <td><i class="fas fa-check check"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="pricing-faq">
            <h2><?= $translations['faq_title'] ?? 'Veelgestelde vragen' ?></h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle"></i> <?= $translations['faq_trial_q'] ?? 'Hoe werkt de proefperiode?' ?></h4>
                    <p><?= $translations['faq_trial_a'] ?? 'Je kunt 14 dagen lang gratis alle functies uitproberen. Pas daarna betaal je eenmalig voor levenslange toegang.' ?></p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle"></i> <?= $translations['faq_cancel_q'] ?? 'Kan ik opzeggen tijdens de proefperiode?' ?></h4>
                    <p><?= $translations['faq_cancel_a'] ?? 'Ja, je kunt op elk moment stoppen tijdens de proefperiode zonder kosten.' ?></p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle"></i> <?= $translations['faq_early_bird_q'] ?? 'Wat is Early Bird?' ?></h4>
                    <p><?= $translations['faq_early_bird_a'] ?? 'De eerste 100 salons per land krijgen levenslange toegang voor slechts &euro;0,99 in plaats van &euro;99,99.' ?></p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle"></i> <?= $translations['faq_payment_q'] ?? 'Hoe ontvang ik betalingen?' ?></h4>
                    <p><?= $translations['faq_payment_a'] ?? 'Betalingen worden automatisch verwerkt via Mollie en uitbetaald op je gekoppelde bankrekening.' ?></p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle"></i> <?= $translations['faq_transaction_fee_q'] ?? 'Wanneer betaal ik transactiekosten?' ?></h4>
                    <p><?= $translations['faq_transaction_fee_a'] ?? 'Alleen wanneer een klant online boekt en betaalt. Contante betalingen in je salon zijn kosteloos.' ?></p>
                </div>
                <div class="faq-item">
                    <h4><i class="fas fa-question-circle"></i> <?= $translations['faq_lifetime_q'] ?? 'Wat betekent levenslange toegang?' ?></h4>
                    <p><?= $translations['faq_lifetime_a'] ?? 'Na je eenmalige betaling heb je voor altijd toegang tot alle functies, zonder maandelijkse kosten.' ?></p>
                </div>
            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="pricing-bottom-cta">
            <h3><?= $translations['cta_ready'] ?? 'Klaar om te beginnen?' ?></h3>
            <p><?= $translations['cta_ready_desc'] ?? 'Start vandaag nog met je 14-dagen gratis proefperiode en ontdek alle mogelijkheden.' ?></p>
            <div class="pricing-bottom-cta-btns">
                <a href="/business/register" class="pricing-cta pricing-cta-primary">
                    <i class="fas fa-rocket"></i> <?= $translations['start_free_trial'] ?? 'Start Gratis Proefperiode' ?>
                </a>
                <a href="/contact" class="pricing-cta pricing-cta-secondary">
                    <i class="fas fa-envelope"></i> <?= $translations['contact_us'] ?? 'Neem Contact Op' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
