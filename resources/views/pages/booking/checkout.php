<?php ob_start(); ?>

<style>
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
    background: linear-gradient(135deg, #000000, #1a1a1a);
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
    color: var(--primary);
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
    color: var(--primary);
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
    color: var(--primary);
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
    background: linear-gradient(135deg, #000000, #1a1a1a);
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
    background: linear-gradient(135deg, #000000, #1a1a1a);
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
            <h2><i class="fas fa-clipboard-check"></i> Controleer je boeking</h2>
            <p>Bekijk de details voordat je betaalt</p>
        </div>

        <div class="checkout-body">
            <!-- Business Info -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-store"></i> Salon
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
                    <i class="fas fa-cut"></i> Dienst
                </div>
                <div class="service-box">
                    <div class="service-name"><?= htmlspecialchars($service['name']) ?></div>
                    <div class="service-meta">
                        <span class="service-duration">
                            <i class="fas fa-clock"></i> <?= $service['duration_minutes'] ?> minuten
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
                        <div style="font-size: 0.8rem; color: var(--text-light);">Medewerker</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Date & Time -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-calendar-alt"></i> Datum & Tijd
                </div>
                <div class="datetime-box">
                    <div class="datetime-item">
                        <i class="fas fa-calendar"></i>
                        <div class="label">Datum</div>
                        <div class="value"><?= date('d-m-Y', strtotime($bookingData['date'])) ?></div>
                    </div>
                    <div class="datetime-item">
                        <i class="fas fa-clock"></i>
                        <div class="label">Tijd</div>
                        <div class="value"><?= date('H:i', strtotime($bookingData['time'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-user"></i> Jouw gegevens
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
                    <i class="fas fa-comment"></i> Opmerkingen
                </div>
                <div class="notes-box">
                    "<?= htmlspecialchars($bookingData['notes']) ?>"
                </div>
            </div>
            <?php endif; ?>

            <!-- 24 Hour Policy -->
            <div class="checkout-section">
                <div class="policy-box">
                    <h4><i class="fas fa-exclamation-triangle"></i> 24-uurs annuleringsbeleid</h4>
                    <p>
                        Gratis annuleren tot 24 uur voor de afspraak. Bij annulering binnen 24 uur wordt 50% van het bedrag in rekening gebracht.
                    </p>
                </div>
            </div>

            <!-- Total -->
            <div class="checkout-section">
                <div class="total-box">
                    <span class="total-label">Totaal te betalen</span>
                    <span class="total-amount">&euro;<?= number_format($bookingData['total_price'], 2, ',', '.') ?></span>
                </div>
            </div>

            <!-- Actions -->
            <form method="POST" action="/booking/confirm">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="confirm_booking" value="1">

                <div class="checkout-actions">
                    <a href="/book/<?= htmlspecialchars($business['slug']) ?>" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Terug
                    </a>
                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-lock"></i> Bevestig & Betaal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
