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
    background: #fff;
    border-radius: 20px;
    padding: 2.5rem;
    max-width: 500px;
    margin: 0 auto 2rem;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}
.price-box h2 {
    color: #333;
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
}
.price-box .price {
    font-size: 4rem;
    font-weight: 800;
    color: #000;
    line-height: 1;
    margin: 1rem 0;
}
.price-box .price-type {
    color: #666;
    font-size: 1rem;
    margin-bottom: 1.5rem;
}
.price-box .original-price {
    text-decoration: line-through;
    color: #999;
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
    background: #f9fafb;
    border-radius: 12px;
}
.features-list h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: #333;
}
.features-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.features-list li {
    padding: 0.5rem 0;
    color: #555;
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
    background: <?= $trialExpired ? '#fef2f2' : '#fffbeb' ?>;
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
    color: #333;
}
.trial-status-text p {
    margin: 0.25rem 0 0 0;
    color: #666;
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
        <h2><?= $__('sub_already_active') ?></h2>
        <p><?= $__('sub_full_access') ?></p>
        <a href="/business/dashboard" style="display:inline-block;margin-top:1.5rem;background:#fff;color:#059669;padding:0.75rem 2rem;border-radius:25px;text-decoration:none;font-weight:600">
            <i class="fas fa-arrow-left"></i> <?= $__('sub_back_to_dashboard') ?>
        </a>
    </div>
<?php else: ?>
    <!-- Trial Status Banner -->
    <div class="trial-status">
        <i class="fas fa-<?= $trialExpired ? 'exclamation-triangle' : 'clock' ?>"></i>
        <div class="trial-status-text">
            <?php if ($trialExpired): ?>
                <h4><?= $__('sub_trial_expired') ?></h4>
                <p><?= $__('sub_trial_expired_desc') ?></p>
            <?php else: ?>
                <h4><?= $__('sub_trial_days_remaining', ['days' => $daysRemaining]) ?></h4>
                <p><?= $__('sub_activate_now') ?></p>
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
            <h1><?= $isEarlyAdopter ? $__('sub_early_bird_offer') : $__('sub_activate_subscription') ?></h1>
            <p>
                <?php if ($isEarlyAdopter): ?>
                    <?= $__('sub_early_bird_desc') ?>
                <?php else: ?>
                    <?= $__('sub_activate_desc') ?>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Price Box -->
    <div class="price-box">
        <h2><?= $isEarlyAdopter ? $__('sub_early_bird_fee') : $__('sub_activation_fee') ?></h2>

        <?php if ($welcomeDiscount > 0): ?>
            <p class="original-price">&euro;<?= number_format($subscriptionPrice, 2, ',', '.') ?></p>
            <span class="discount-tag">
                <i class="fas fa-tag"></i> -&euro;<?= number_format($welcomeDiscount, 2, ',', '.') ?> <?= $__('sub_welcome_discount') ?>
            </span>
        <?php endif; ?>

        <p class="price">&euro;<?= number_format($finalPrice, 2, ',', '.') ?></p>
        <p class="price-type">
            <?php if ($isEarlyAdopter): ?>
                <strong><?= $__('sub_one_time') ?></strong> - <?= $__('sub_no_monthly_costs') ?>
            <?php else: ?>
                <?= $__('sub_one_time_activation') ?>
            <?php endif; ?>
        </p>

        <div class="features-list">
            <h3><i class="fas fa-check-circle" style="color:#22c55e"></i> <?= $__('sub_what_you_get') ?></h3>
            <ul>
                <li><i class="fas fa-check"></i> <?= $__('sub_feature_booking') ?></li>
                <li><i class="fas fa-check"></i> <?= $__('sub_feature_salon_page') ?></li>
                <li><i class="fas fa-check"></i> <?= $__('sub_feature_reminders') ?></li>
                <li><i class="fas fa-check"></i> <?= $__('sub_feature_payments') ?></li>
                <li><i class="fas fa-check"></i> <?= $__('sub_feature_calendar') ?></li>
                <li><i class="fas fa-check"></i> <?= $__('sub_feature_customers') ?></li>
                <?php if ($isEarlyAdopter): ?>
                    <li><i class="fas fa-check"></i> <strong><?= $__('sub_feature_early_bird') ?></strong></li>
                <?php endif; ?>
            </ul>
        </div>

        <form action="/business/subscription/activate" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <button type="submit" class="pay-btn <?= $isEarlyAdopter ? 'early-bird' : '' ?>">
                <i class="fas fa-lock"></i>
                <?= $__('sub_pay_amount', ['amount' => number_format($finalPrice, 2, ',', '.')]) ?>
            </button>
        </form>

        <div class="payment-methods">
            <img src="https://www.mollie.com/external/icons/payment-methods/ideal.svg" alt="iDEAL">
            <img src="https://www.mollie.com/external/icons/payment-methods/creditcard.svg" alt="Credit Card">
            <img src="https://www.mollie.com/external/icons/payment-methods/bancontact.svg" alt="Bancontact">
            <img src="https://www.mollie.com/external/icons/payment-methods/paypal.svg" alt="PayPal">
        </div>

        <p style="margin-top:1.5rem;color:#999;font-size:0.85rem">
            <i class="fas fa-shield-alt"></i> <?= $__('sub_secure_payment_mollie') ?>
        </p>
    </div>

    <!-- FAQ / Info -->
    <div style="max-width:600px;margin:0 auto;text-align:center">
        <p style="color:#666;font-size:0.9rem">
            <?= $__('sub_questions_contact') ?>
        </p>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/business.php'; ?>
