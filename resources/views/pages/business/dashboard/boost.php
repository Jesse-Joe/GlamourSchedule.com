<?php ob_start(); ?>

<?php
// Calculate boost info
$isBoosted = !empty($business['is_boosted']) && !empty($business['boost_expires_at']) && strtotime($business['boost_expires_at']) > time();
$boostExpiresAt = !empty($business['boost_expires_at']) ? strtotime($business['boost_expires_at']) : 0;
$boostDaysRemaining = $isBoosted ? max(0, ceil(($boostExpiresAt - time()) / 86400)) : 0;
$boostPrice = 299.99;
?>

<style>
/* Boost Page Styles */
.boost-hero {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 20px;
    padding: 2.5rem;
    color: white;
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.boost-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    animation: pulse 4s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}
.boost-hero-content {
    position: relative;
    z-index: 1;
}
.boost-hero i.rocket {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: block;
}
.boost-hero h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.boost-hero p {
    font-size: 1.1rem;
    opacity: 0.95;
    max-width: 600px;
    margin: 0 auto;
}

/* Active Boost Banner */
.boost-active-banner {
    background: linear-gradient(135deg, #059669, #047857);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}
.boost-active-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.boost-active-info i {
    font-size: 2.5rem;
}
.boost-active-info h3 {
    margin: 0;
    font-size: 1.25rem;
}
.boost-active-info p {
    margin: 0.25rem 0 0 0;
    opacity: 0.9;
}
.boost-countdown {
    text-align: center;
    background: rgba(255,255,255,0.2);
    padding: 1rem 1.5rem;
    border-radius: 12px;
}
.boost-countdown .number {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
}
.boost-countdown .label {
    font-size: 0.85rem;
    opacity: 0.9;
}

/* Benefits Grid */
.benefits-section {
    margin-bottom: 2rem;
}
.benefits-section h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.benefits-section h2 i {
    color: #f59e0b;
}
.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.benefit-card {
    background: var(--b-bg-card);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 2px solid transparent;
    transition: all 0.3s;
}
.benefit-card:hover {
    border-color: #f59e0b;
    transform: translateY(-4px);
}
.benefit-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}
.benefit-icon i {
    font-size: 1.5rem;
    color: white;
}
.benefit-card h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
}
.benefit-card p {
    margin: 0;
    color: var(--b-text-muted);
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Pricing Card */
.pricing-section {
    margin-bottom: 2rem;
}
.pricing-card {
    background: var(--b-bg-card);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 500px;
    margin: 0 auto;
    border: 3px solid #f59e0b;
    position: relative;
    overflow: hidden;
}
.pricing-badge {
    position: absolute;
    top: 20px;
    right: -35px;
    background: #f59e0b;
    color: white;
    padding: 0.5rem 3rem;
    font-size: 0.8rem;
    font-weight: 600;
    transform: rotate(45deg);
}
.pricing-card h2 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}
.pricing-card .subtitle {
    color: var(--b-text-muted);
    margin-bottom: 1.5rem;
}
.pricing-amount {
    margin-bottom: 1.5rem;
}
.pricing-amount .price {
    font-size: 3.5rem;
    font-weight: 700;
    color: #f59e0b;
    line-height: 1;
}
.pricing-amount .currency {
    font-size: 1.5rem;
    vertical-align: top;
}
.pricing-amount .period {
    display: block;
    color: var(--b-text-muted);
    font-size: 1rem;
    margin-top: 0.5rem;
}
.pricing-features {
    text-align: left;
    margin-bottom: 2rem;
}
.pricing-feature {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--b-border);
}
.pricing-feature:last-child {
    border-bottom: none;
}
.pricing-feature i {
    color: #059669;
    font-size: 1rem;
}
.pricing-feature span {
    color: var(--b-text);
}

.boost-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1.25rem 2rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1.15rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
}
.boost-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(245, 158, 11, 0.4);
}
.boost-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
.boost-btn i {
    font-size: 1.25rem;
}

.extend-btn {
    background: linear-gradient(135deg, #059669, #047857);
}
.extend-btn:hover {
    box-shadow: 0 10px 30px rgba(5, 150, 105, 0.4);
}

/* FAQ Section */
.faq-section {
    margin-top: 3rem;
}
.faq-section h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.faq-section h2 i {
    color: #f59e0b;
}
.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}
.faq-item {
    background: var(--b-bg-card);
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.faq-item h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    color: var(--b-text);
}
.faq-item p {
    margin: 0;
    color: var(--b-text-muted);
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Stats Preview */
.stats-preview {
    background: var(--b-bg-card);
    border-radius: 16px;
    padding: 2rem;
    margin-top: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.stats-preview h3 {
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.stats-preview h3 i {
    color: #f59e0b;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}
.stat-item {
    text-align: center;
}
.stat-item .number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #f59e0b;
    line-height: 1;
}
.stat-item .label {
    color: var(--b-text-muted);
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .boost-hero {
        padding: 2rem 1.5rem;
    }
    .boost-hero h1 {
        font-size: 1.5rem;
    }
    .boost-hero i.rocket {
        font-size: 3rem;
    }
    .boost-active-banner {
        flex-direction: column;
        text-align: center;
    }
    .boost-active-info {
        flex-direction: column;
    }
    .pricing-card {
        padding: 2rem 1.5rem;
    }
    .pricing-amount .price {
        font-size: 2.5rem;
    }
}
</style>

<?php if ($isBoosted): ?>
    <!-- Active Boost Banner -->
    <div class="boost-active-banner">
        <div class="boost-active-info">
            <i class="fas fa-rocket"></i>
            <div>
                <h3>Boost Actief!</h3>
                <p>Je bedrijf wordt uitgelicht op de homepage</p>
            </div>
        </div>
        <div class="boost-countdown">
            <p class="number"><?= $boostDaysRemaining ?></p>
            <p class="label">dagen over</p>
        </div>
    </div>
<?php else: ?>
    <!-- Hero Section -->
    <div class="boost-hero">
        <div class="boost-hero-content">
            <i class="fas fa-rocket rocket"></i>
            <h1>Boost je Bedrijf</h1>
            <p>Vergroot je zichtbaarheid en krijg meer klanten door uitgelicht te worden op de homepage!</p>
        </div>
    </div>
<?php endif; ?>

<!-- Benefits Section -->
<div class="benefits-section">
    <h2><i class="fas fa-star"></i> Voordelen van Boosten</h2>
    <div class="benefits-grid">
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-home"></i>
            </div>
            <h3>Homepage Uitlichting</h3>
            <p>Je bedrijf wordt prominent weergegeven in de "Uitgelichte Bedrijven" sectie op de homepage, zichtbaar voor alle bezoekers.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-eye"></i>
            </div>
            <h3>Meer Zichtbaarheid</h3>
            <p>Tot wel 5x meer views op je bedrijfspagina dankzij de premium positie op de homepage.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3>Meer Boekingen</h3>
            <p>Gebooste bedrijven ontvangen gemiddeld 40% meer boekingen tijdens de boost periode.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">
                <i class="fas fa-badge-check"></i>
            </div>
            <h3>Boost Badge</h3>
            <p>Je bedrijf krijgt een speciale "Uitgelicht" badge die extra vertrouwen wekt bij potentiele klanten.</p>
        </div>
    </div>
</div>

<!-- Pricing Section -->
<div class="pricing-section">
    <div class="pricing-card">
        <div class="pricing-badge">POPULAIR</div>
        <h2>30 Dagen Boost</h2>
        <p class="subtitle">Eenmalige betaling, geen abonnement</p>

        <div class="pricing-amount">
            <span class="currency">&euro;</span><span class="price"><?= number_format($boostPrice, 2, ',', '.') ?></span>
            <span class="period">voor 30 dagen</span>
        </div>

        <div class="pricing-features">
            <div class="pricing-feature">
                <i class="fas fa-check-circle"></i>
                <span>30 dagen uitgelicht op homepage</span>
            </div>
            <div class="pricing-feature">
                <i class="fas fa-check-circle"></i>
                <span>Speciale "Uitgelicht" badge</span>
            </div>
            <div class="pricing-feature">
                <i class="fas fa-check-circle"></i>
                <span>Prioriteit in zoekresultaten</span>
            </div>
            <div class="pricing-feature">
                <i class="fas fa-check-circle"></i>
                <span>Geen automatische verlenging</span>
            </div>
            <div class="pricing-feature">
                <i class="fas fa-check-circle"></i>
                <span>Handmatig verlengen wanneer je wilt</span>
            </div>
        </div>

        <?php if ($isBoosted): ?>
            <form method="POST" action="/business/boost/extend">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                <button type="submit" class="boost-btn extend-btn">
                    <i class="fas fa-sync"></i>
                    Verlengen met 30 dagen
                </button>
            </form>
            <p style="margin-top:1rem;color:var(--b-text-muted);font-size:0.9rem">
                Huidige boost verloopt op: <strong><?= date('d-m-Y', $boostExpiresAt) ?></strong>
            </p>
        <?php else: ?>
            <form method="POST" action="/business/boost/activate">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>">
                <button type="submit" class="boost-btn">
                    <i class="fas fa-rocket"></i>
                    Nu Boosten
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Preview (Example Stats) -->
<div class="stats-preview">
    <h3><i class="fas fa-chart-bar"></i> Gemiddelde resultaten van gebooste bedrijven</h3>
    <div class="stats-grid">
        <div class="stat-item">
            <p class="number">5x</p>
            <p class="label">Meer profielbezoeken</p>
        </div>
        <div class="stat-item">
            <p class="number">+40%</p>
            <p class="label">Meer boekingen</p>
        </div>
        <div class="stat-item">
            <p class="number">2.500+</p>
            <p class="label">Homepage weergaven/dag</p>
        </div>
        <div class="stat-item">
            <p class="number">30</p>
            <p class="label">Dagen zichtbaarheid</p>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="faq-section">
    <h2><i class="fas fa-question-circle"></i> Veelgestelde vragen</h2>
    <div class="faq-grid">
        <div class="faq-item">
            <h4>Hoe lang duurt een boost?</h4>
            <p>Een boost duurt exact 30 dagen vanaf het moment van activatie. Na afloop kun je handmatig verlengen.</p>
        </div>
        <div class="faq-item">
            <h4>Wordt de boost automatisch verlengd?</h4>
            <p>Nee, de boost verloopt automatisch na 30 dagen. Je kiest zelf of en wanneer je wilt verlengen.</p>
        </div>
        <div class="faq-item">
            <h4>Waar word ik weergegeven?</h4>
            <p>Je bedrijf wordt weergegeven in de "Uitgelichte Bedrijven" sectie op de homepage, zichtbaar voor alle bezoekers.</p>
        </div>
        <div class="faq-item">
            <h4>Kan ik een boost annuleren?</h4>
            <p>Aangezien het een eenmalige betaling is, is annuleren niet mogelijk. De boost blijft actief tot de vervaldatum.</p>
        </div>
        <div class="faq-item">
            <h4>Kan ik meerdere boosts tegelijk kopen?</h4>
            <p>Je kunt verlengen terwijl je boost nog actief is. De extra 30 dagen worden opgeteld bij je huidige einddatum.</p>
        </div>
        <div class="faq-item">
            <h4>Hoe betaal ik voor de boost?</h4>
            <p>Betaling verloopt veilig via iDEAL, creditcard of andere betaalmethoden via onze betalingspartner.</p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>
