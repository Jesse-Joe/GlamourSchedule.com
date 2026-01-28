<?php ob_start(); ?>

<style>
/* Subscription Page Styles */
.sub-hero {
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    border-radius: 20px;
    padding: 2.5rem;
    color: white;
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    border: 2px solid #333;
}
.sub-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
}
.sub-hero-content {
    position: relative;
    z-index: 1;
}
.sub-hero i.main-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: block;
    color: <?= $isEarlyAdopter ? '#f59e0b' : '#3b82f6' ?>;
}
.sub-hero h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.sub-hero p {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 600px;
    margin: 0 auto;
}

/* Early Bird Badge */
.early-bird-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #000;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

/* Price Box */
.price-box {
    background: var(--b-bg-card);
    border-radius: 20px;
    padding: 2.5rem;
    max-width: 500px;
    margin: 0 auto 2rem;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}
.price-box h2 {
    color: var(--b-text);
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
}
.price-box .price {
    font-size: 4rem;
    font-weight: 800;
    color: var(--b-text);
    line-height: 1;
    margin: 1rem 0;
}
.price-box .price-type {
    color: var(--b-text-muted);
    font-size: 1rem;
    margin-bottom: 1.5rem;
}
.price-box .original-price {
    text-decoration: line-through;
    color: var(--b-text-muted);
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}
.price-box .discount-tag {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

/* Features List */
.features-list {
    text-align: left;
    margin: 1.5rem 0;
    padding: 1.5rem;
    background: var(--b-bg-surface);
    border-radius: 12px;
}
.features-list h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: var(--b-text);
}
.features-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.features-list li {
    padding: 0.5rem 0;
    color: var(--b-text-muted);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.features-list li i {
    color: #22c55e;
    width: 20px;
}

/* Payment Button */
.pay-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, #000, #333);
    color: #fff;
    padding: 1.25rem 3rem;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    max-width: 350px;
}
.pay-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
.pay-btn.early-bird {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #000;
}

/* Trial Status */
.trial-status {
    background: <?= $trialExpired ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)' ?>;
    border: 1px solid <?= $trialExpired ? '#fecaca' : '#fde68a' ?>;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.trial-status i {
    font-size: 1.5rem;
    color: <?= $trialExpired ? '#dc2626' : '#f59e0b' ?>;
}
.trial-status-text h4 {
    margin: 0;
    color: var(--b-text);
}
.trial-status-text p {
    margin: 0.25rem 0 0 0;
    color: var(--b-text-muted);
    font-size: 0.9rem;
}

/* Payment Methods */
.payment-methods {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}
.payment-methods img {
    height: 30px;
    opacity: 0.7;
}

/* Already Active */
.already-active {
    background: linear-gradient(135deg, #059669, #047857);
    border-radius: 16px;
    padding: 2rem;
    color: white;
    text-align: center;
}
.already-active i {
    font-size: 3rem;
    margin-bottom: 1rem;
}
.already-active h2 {
    margin: 0 0 0.5rem 0;
}
.already-active p {
    opacity: 0.9;
    margin: 0;
}

/* Mobile */
@media (max-width: 768px) {
    .sub-hero {
        padding: 1.5rem;
    }
    .sub-hero h1 {
        font-size: 1.5rem;
    }
    .sub-hero i.main-icon {
        font-size: 3rem;
    }
    .price-box {
        padding: 1.5rem;
    }
    .price-box .price {
        font-size: 3rem;
    }
    .trial-status {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php if ($business['subscription_status'] === 'active'): ?>
    <!-- Already Active -->
    <div class="already-active">
        <i class="fas fa-check-circle"></i>
        <h2>Je abonnement is actief!</h2>
        <p>Je hebt volledige toegang tot alle functies van GlamourSchedule.</p>
        <a href="/business/dashboard" style="display:inline-block;margin-top:1.5rem;background:#fff;color:#059669;padding:0.75rem 2rem;border-radius:25px;text-decoration:none;font-weight:600">
            <i class="fas fa-arrow-left"></i> Terug naar Dashboard
        </a>
    </div>
<?php else: ?>
    <!-- Trial Status Banner -->
    <div class="trial-status">
        <i class="fas fa-<?= $trialExpired ? 'exclamation-triangle' : 'clock' ?>"></i>
        <div class="trial-status-text">
            <?php if ($trialExpired): ?>
                <h4>Je proefperiode is verlopen</h4>
                <p>Activeer je abonnement om weer volledige toegang te krijgen tot GlamourSchedule.</p>
            <?php else: ?>
                <h4>Nog <?= $daysRemaining ?> dag<?= $daysRemaining !== 1 ? 'en' : '' ?> in je proefperiode</h4>
                <p>Activeer nu je abonnement en voorkom onderbreking van je diensten.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="sub-hero">
        <div class="sub-hero-content">
            <?php if ($isEarlyAdopter): ?>
                <span class="early-bird-badge">
                    <i class="fas fa-seedling"></i> Early Bird
                </span>
            <?php endif; ?>
            <i class="fas fa-<?= $isEarlyAdopter ? 'gift' : 'crown' ?> main-icon"></i>
            <h1><?= $isEarlyAdopter ? 'Early Bird Aanbieding' : 'Abonnement Activeren' ?></h1>
            <p>
                <?php if ($isEarlyAdopter): ?>
                    Profiteer van je exclusieve Early Bird prijs en krijg volledige toegang tot GlamourSchedule.
                <?php else: ?>
                    Activeer je abonnement en maak onbeperkt gebruik van alle functies van GlamourSchedule.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Price Box -->
    <div class="price-box">
        <h2><?= $isEarlyAdopter ? 'Early Bird Aanmeldkosten' : 'Activatiekosten' ?></h2>

        <?php if ($welcomeDiscount > 0): ?>
            <p class="original-price">&euro;<?= number_format($subscriptionPrice, 2, ',', '.') ?></p>
            <span class="discount-tag">
                <i class="fas fa-tag"></i> -&euro;<?= number_format($welcomeDiscount, 2, ',', '.') ?> welkomstkorting
            </span>
        <?php endif; ?>

        <p class="price">&euro;<?= number_format($finalPrice, 2, ',', '.') ?></p>
        <p class="price-type">
            <?php if ($isEarlyAdopter): ?>
                <strong>Eenmalig</strong> - geen maandelijkse kosten!
            <?php else: ?>
                Eenmalige activatiekosten
            <?php endif; ?>
        </p>

        <div class="features-list">
            <h3><i class="fas fa-check-circle" style="color:#22c55e"></i> Dit krijg je:</h3>
            <ul>
                <li><i class="fas fa-check"></i> Online boekingssysteem</li>
                <li><i class="fas fa-check"></i> Eigen salonpagina op GlamourSchedule</li>
                <li><i class="fas fa-check"></i> Automatische herinneringen voor klanten</li>
                <li><i class="fas fa-check"></i> Online betalingen via Mollie</li>
                <li><i class="fas fa-check"></i> Agenda- en afsprakenbeheer</li>
                <li><i class="fas fa-check"></i> Klantenbeheer en statistieken</li>
                <?php if ($isEarlyAdopter): ?>
                    <li><i class="fas fa-check"></i> <strong>Levenslang Early Bird voordelen</strong></li>
                <?php endif; ?>
            </ul>
        </div>

        <form action="/business/subscription/activate" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <button type="submit" class="pay-btn <?= $isEarlyAdopter ? 'early-bird' : '' ?>">
                <i class="fas fa-lock"></i>
                Betaal &euro;<?= number_format($finalPrice, 2, ',', '.') ?>
            </button>
        </form>

        <div class="payment-methods">
            <img src="https://www.mollie.com/external/icons/payment-methods/ideal.svg" alt="iDEAL">
            <img src="https://www.mollie.com/external/icons/payment-methods/creditcard.svg" alt="Credit Card">
            <img src="https://www.mollie.com/external/icons/payment-methods/bancontact.svg" alt="Bancontact">
            <img src="https://www.mollie.com/external/icons/payment-methods/paypal.svg" alt="PayPal">
        </div>

        <p style="margin-top:1.5rem;color:var(--b-text-muted);font-size:0.85rem">
            <i class="fas fa-shield-alt"></i> Veilig betalen via Mollie
        </p>
    </div>

    <!-- FAQ / Info -->
    <div style="max-width:600px;margin:0 auto;text-align:center">
        <p style="color:var(--b-text-muted);font-size:0.9rem">
            Vragen? Neem contact op via <a href="mailto:info@glamourschedule.nl" style="color:#3b82f6">info@glamourschedule.nl</a>
        </p>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>
