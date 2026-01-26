<?php ob_start(); ?>
<?php
$primaryColor = $settings['primary_color'] ?? '#000000';
$secondaryColor = $settings['secondary_color'] ?? '#333333';
$accentColor = $settings['accent_color'] ?? '#000000';
?>

<style>
/* Business Theme Colors */
:root {
    --business-primary: <?= htmlspecialchars($primaryColor) ?>;
    --business-secondary: <?= htmlspecialchars($secondaryColor) ?>;
    --business-accent: <?= htmlspecialchars($accentColor) ?>;
}

.checkout-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 1rem;
}
.checkout-card {
    background: var(--bg-card);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.checkout-header {
    background: linear-gradient(135deg, var(--business-primary), var(--business-secondary));
    color: white;
    padding: 1.5rem;
    text-align: center;
}
.checkout-header h2 {
    margin: 0;
    font-size: 1.5rem;
}
.checkout-header p {
    margin: 0.5rem 0 0;
    opacity: 0.8;
    font-size: 0.9rem;
}
.checkout-body {
    padding: 1.5rem;
}
.checkout-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
}
.checkout-section:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}
.checkout-section-title {
    font-size: 0.85rem;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.checkout-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.5rem 0;
}
.checkout-label {
    color: var(--text-light);
}
.checkout-value {
    font-weight: 500;
    text-align: right;
}

/* Business Card */
.business-info-card {
    background: var(--secondary);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    align-items: center;
}
.business-logo {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #333, #000);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
    overflow: hidden;
}
.business-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.business-details h3 {
    margin: 0 0 0.25rem;
    font-size: 1.1rem;
}
.business-address {
    color: var(--text-light);
    font-size: 0.9rem;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}
.business-address i {
    margin-top: 0.2rem;
    color: var(--business-primary);
}

/* Service Box */
.service-box {
    background: var(--secondary);
    border-radius: 12px;
    padding: 1rem;
}
.service-name {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}
.service-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.service-duration {
    color: var(--text-light);
    font-size: 0.9rem;
}
.service-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--business-primary);
}

/* Date Time Box */
.datetime-box {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.datetime-item {
    background: var(--secondary);
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}
.datetime-item i {
    font-size: 1.5rem;
    color: var(--business-primary);
    margin-bottom: 0.5rem;
}
.datetime-item .label {
    font-size: 0.8rem;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.datetime-item .value {
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 0.25rem;
}

/* Customer Info */
.customer-info {
    background: var(--secondary);
    border-radius: 12px;
    padding: 1rem;
}
.customer-info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
}
.customer-info-row:not(:last-child) {
    border-bottom: 1px solid var(--border);
}
.customer-info-row i {
    width: 20px;
    color: var(--text-light);
    text-align: center;
}

/* Policy Box */
.policy-box {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 1rem;
    color: #92400e;
}
.policy-box h4 {
    margin: 0 0 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #92400e;
    font-size: 0.95rem;
}
.policy-box p {
    margin: 0;
    font-size: 0.85rem;
    line-height: 1.5;
}

/* Total Box */
.total-box {
    background: linear-gradient(135deg, var(--business-primary), var(--business-secondary));
    color: white;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.total-label {
    font-size: 1.1rem;
}
.total-amount {
    font-size: 1.75rem;
    font-weight: 700;
}

/* Buttons */
.checkout-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
}
.btn-back {
    flex: 1;
    padding: 1rem;
    border: 2px solid var(--border);
    background: transparent;
    color: var(--text);
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.btn-back:hover {
    background: var(--secondary);
}
.btn-confirm {
    flex: 2;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--business-primary), var(--business-secondary));
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.btn-confirm i {
    font-size: 1.2rem;
}

/* Notes Display */
.notes-box {
    background: var(--secondary);
    border-radius: 12px;
    padding: 1rem;
    font-style: italic;
    color: var(--text-light);
}

@media (max-width: 480px) {
    .checkout-container {
        padding: 0;
    }
    .checkout-card {
        border-radius: 0;
    }
    .datetime-box {
        grid-template-columns: 1fr;
    }
    .checkout-actions {
        flex-direction: column;
    }
    .btn-back, .btn-confirm {
        flex: none;
        width: 100%;
    }
}
</style>

<div class="checkout-container">
    <div class="checkout-card">
        <div class="checkout-header">
            <h2><i class="fas fa-clipboard-check"></i> <?= $translations['checkout_review_booking'] ?? 'Review your booking' ?></h2>
            <p><?= $translations['checkout_review_details'] ?? 'Review the details before you pay' ?></p>
        </div>

        <div class="checkout-body">
            <!-- Business Info -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-store"></i> <?= $translations['salon'] ?? 'Salon' ?>
                </div>
                <div class="business-info-card">
                    <div class="business-logo">
                        <?php if (!empty($business['logo'])): ?>
                            <img src="<?= htmlspecialchars($business['logo']) ?>" alt="">
                        <?php else: ?>
                            <i class="fas fa-store"></i>
                        <?php endif; ?>
                    </div>
                    <div class="business-details">
                        <h3><?= htmlspecialchars($business['company_name'] ?? $business['name']) ?></h3>
                        <div class="business-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($business['street']) ?>, <?= htmlspecialchars($business['postal_code']) ?> <?= htmlspecialchars($business['city']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-cut"></i> <?= $translations['service'] ?? 'Service' ?>
                </div>
                <div class="service-box">
                    <div class="service-name"><?= htmlspecialchars($service['name']) ?></div>
                    <div class="service-meta">
                        <span class="service-duration">
                            <i class="fas fa-clock"></i> <?= $service['duration_minutes'] ?> <?= $translations['minutes'] ?? 'minutes' ?>
                        </span>
                        <span class="service-price">&euro;<?= number_format($service['sale_price'] ?? $service['price'], 2, ',', '.') ?></span>
                    </div>
                </div>
                <?php if (!empty($employee)): ?>
                <div style="margin-top: 0.75rem; padding: 0.75rem; background: var(--secondary); border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: <?= htmlspecialchars($employee['color'] ?? '#333') ?>; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                        <?= strtoupper(substr($employee['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight: 500;"><?= htmlspecialchars($employee['name']) ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-light);"><?= $translations['employee'] ?? 'Employee' ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Date & Time -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-calendar-alt"></i> <?= $translations['date_time'] ?? 'Date & Time' ?>
                </div>
                <div class="datetime-box">
                    <div class="datetime-item">
                        <i class="fas fa-calendar"></i>
                        <div class="label"><?= $translations['date'] ?? 'Date' ?></div>
                        <div class="value"><?= date('d-m-Y', strtotime($bookingData['date'])) ?></div>
                    </div>
                    <div class="datetime-item">
                        <i class="fas fa-clock"></i>
                        <div class="label"><?= $translations['time'] ?? 'Time' ?></div>
                        <div class="value"><?= date('H:i', strtotime($bookingData['time'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-user"></i> <?= $translations['your_details'] ?? 'Your details' ?>
                </div>
                <div class="customer-info">
                    <div class="customer-info-row">
                        <i class="fas fa-user"></i>
                        <span><?= htmlspecialchars($bookingData['customer_name']) ?></span>
                    </div>
                    <div class="customer-info-row">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($bookingData['customer_email']) ?></span>
                    </div>
                    <?php if (!empty($bookingData['customer_phone'])): ?>
                    <div class="customer-info-row">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($bookingData['customer_phone']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($bookingData['notes'])): ?>
            <!-- Notes -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-comment"></i> <?= $translations['notes'] ?? 'Notes' ?>
                </div>
                <div class="notes-box">
                    "<?= htmlspecialchars($bookingData['notes']) ?>"
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($loyaltyData) && $loyaltyData['enabled'] && $loyaltyData['user_points'] > 0): ?>
            <!-- Loyalty Points -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-star"></i> <?= $translations['loyalty_points'] ?? 'Loyaliteitspunten' ?>
                </div>
                <div class="loyalty-box" style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:2px solid #f59e0b;border-radius:12px;padding:1rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                        <div>
                            <span style="color:#92400e;font-weight:600;"><?= $translations['your_points'] ?? 'Jouw punten' ?></span>
                            <div style="font-size:1.5rem;font-weight:700;color:#92400e;"><?= number_format($loyaltyData['user_points']) ?></div>
                        </div>
                        <div style="text-align:right;">
                            <span style="color:#92400e;font-size:0.85rem;"><?= $translations['max_redeemable'] ?? 'Max. inwisselbaar' ?></span>
                            <div style="font-weight:600;color:#92400e;"><?= number_format($loyaltyData['max_redeemable']) ?> <?= $translations['points'] ?? 'punten' ?></div>
                        </div>
                    </div>

                    <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;margin-bottom:1rem;">
                        <input type="checkbox" id="use_loyalty_points" style="width:20px;height:20px;accent-color:#f59e0b;">
                        <span style="color:#92400e;font-weight:500;"><?= $translations['use_points_discount'] ?? 'Punten gebruiken voor korting' ?></span>
                    </label>

                    <div id="loyalty_slider_container" style="display:none;">
                        <div style="margin-bottom:0.5rem;">
                            <input type="range" id="loyalty_slider" min="0" max="<?= $loyaltyData['max_redeemable'] ?>" step="100" value="0" style="width:100%;accent-color:#f59e0b;">
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span id="points_selected" style="font-weight:600;color:#92400e;">0 <?= $translations['points'] ?? 'punten' ?></span>
                            <span id="discount_preview" style="font-weight:700;color:#16a34a;">-&euro;0,00</span>
                        </div>
                        <p style="margin:0.5rem 0 0;font-size:0.8rem;color:#92400e;">
                            <?= $translations['loyalty_100_equals_1'] ?? '100 punten = 1% korting' ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 24 Hour Policy -->
            <div class="checkout-section">
                <div class="policy-box">
                    <h4><i class="fas fa-exclamation-triangle"></i> <?= $translations['checkout_cancel_policy_title'] ?? '24-hour cancellation policy' ?></h4>
                    <p>
                        <?= $translations['checkout_cancel_policy_text'] ?? 'Free cancellation up to 24 hours before the appointment. Cancellation within 24 hours will incur a 50% fee.' ?>
                    </p>
                </div>
            </div>

            <!-- Total -->
            <div class="checkout-section">
                <div id="price_breakdown" style="background:var(--secondary);border-radius:12px;padding:1rem;margin-bottom:1rem;display:none;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                        <span style="color:var(--text-light);"><?= $translations['service_price'] ?? 'Dienstprijs' ?></span>
                        <span>&euro;<?= number_format($bookingData['service_price'], 2, ',', '.') ?></span>
                    </div>
                    <div id="loyalty_discount_row" style="display:flex;justify-content:space-between;color:#16a34a;">
                        <span><?= $translations['loyalty_discount'] ?? 'Puntkorting' ?></span>
                        <span id="loyalty_discount_amount">-&euro;0,00</span>
                    </div>
                </div>
                <div class="total-box">
                    <span class="total-label"><?= $translations['checkout_total_to_pay'] ?? 'Total to pay' ?></span>
                    <span class="total-amount" id="total_amount">&euro;<?= number_format($bookingData['total_price'], 2, ',', '.') ?></span>
                </div>
            </div>

            <!-- Actions -->
            <form method="POST" action="/booking/confirm">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="confirm_booking" value="1">
                <input type="hidden" name="loyalty_points" id="loyalty_points_input" value="0">

                <div class="checkout-actions">
                    <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="btn-back">
                        <i class="fas fa-arrow-left"></i> <?= $translations['back'] ?? 'Back' ?>
                    </a>
                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-lock"></i> <?= $translations['confirm_pay'] ?? 'Confirm & Pay' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($loyaltyData) && $loyaltyData['enabled'] && $loyaltyData['user_points'] > 0): ?>
<script>
(function() {
    const servicePrice = <?= $bookingData['service_price'] ?>;
    const pointsPerPercent = <?= $loyaltyData['points_per_percent'] ?>;

    const checkbox = document.getElementById('use_loyalty_points');
    const sliderContainer = document.getElementById('loyalty_slider_container');
    const slider = document.getElementById('loyalty_slider');
    const pointsSelected = document.getElementById('points_selected');
    const discountPreview = document.getElementById('discount_preview');
    const priceBreakdown = document.getElementById('price_breakdown');
    const loyaltyDiscountRow = document.getElementById('loyalty_discount_row');
    const loyaltyDiscountAmount = document.getElementById('loyalty_discount_amount');
    const totalAmount = document.getElementById('total_amount');
    const loyaltyInput = document.getElementById('loyalty_points_input');

    function formatCurrency(amount) {
        return '€' + amount.toFixed(2).replace('.', ',');
    }

    function calculateDiscount(points) {
        const discountPercent = points / pointsPerPercent;
        return Math.min((servicePrice * discountPercent) / 100, servicePrice);
    }

    function updateDisplay() {
        const points = parseInt(slider.value) || 0;
        const discount = calculateDiscount(points);
        const newTotal = Math.max(0, servicePrice - discount); // Geen platformfee voor klant

        pointsSelected.textContent = points.toLocaleString() + ' <?= $translations['points'] ?? 'punten' ?>';
        discountPreview.textContent = '-' + formatCurrency(discount);
        loyaltyDiscountAmount.textContent = '-' + formatCurrency(discount);
        totalAmount.textContent = formatCurrency(newTotal);
        loyaltyInput.value = points;

        if (points > 0) {
            priceBreakdown.style.display = 'block';
            loyaltyDiscountRow.style.display = 'flex';
        } else {
            priceBreakdown.style.display = 'none';
        }
    }

    checkbox.addEventListener('change', function() {
        sliderContainer.style.display = this.checked ? 'block' : 'none';
        if (!this.checked) {
            slider.value = 0;
            loyaltyInput.value = 0;
            priceBreakdown.style.display = 'none';
            totalAmount.textContent = formatCurrency(servicePrice); // Geen platformfee voor klant
        }
    });

    slider.addEventListener('input', updateDisplay);
})();
</script>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
