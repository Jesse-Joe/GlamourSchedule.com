<?php ob_start(); ?>

<style>
/* Booking page - Mobile-first dark mode */
.booking-page {
    min-height: 100vh;
    background: #000000;
    padding: 1rem;
}
.booking-card {
    max-width: 500px;
    margin: 0 auto;
    background: #0a0a0a;
    border: 1px solid #222222;
    border-radius: 24px;
    overflow: hidden;
}

/* Status Header */
.booking-header {
    padding: 2rem 1.5rem;
    text-align: center;
    background: linear-gradient(180deg, #111111 0%, #0a0a0a 100%);
}
.booking-status-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}
.booking-status-icon i {
    font-size: 1.75rem;
    color: #ffffff;
}
.booking-status-icon.success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
}
.booking-status-icon.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
}
.booking-status-icon.danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
}
.booking-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 0.5rem 0;
}
.booking-subtitle {
    color: #888888;
    font-size: 0.95rem;
    margin: 0;
}
.booking-number-badge {
    display: inline-block;
    background: #1a1a1a;
    border: 1px solid #333333;
    border-radius: 20px;
    padding: 0.4rem 1rem;
    margin-top: 1rem;
    color: #ffffff;
    font-family: monospace;
    font-size: 0.9rem;
    letter-spacing: 1px;
}

/* Content Body */
.booking-body {
    padding: 1.5rem;
}

/* Details Section */
.booking-section {
    background: #111111;
    border: 1px solid #1a1a1a;
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 1rem;
}
.booking-section-title {
    font-size: 0.75rem;
    color: #666666;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0 0 0.75rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #1a1a1a;
}
.booking-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.6rem 0;
    border-bottom: 1px solid #1a1a1a;
}
.booking-row:last-child {
    border-bottom: none;
}
.booking-label {
    color: #888888;
    font-size: 0.9rem;
}
.booking-value {
    color: #ffffff;
    font-weight: 600;
    font-size: 0.9rem;
    text-align: right;
}
.booking-value.price {
    color: #22c55e;
    font-size: 1.1rem;
}

/* Address Box */
.booking-address {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #0a0a0a;
    border-radius: 12px;
    margin-top: 0.75rem;
}
.booking-address i {
    color: #ec4899;
    margin-top: 2px;
}
.booking-address-text {
    color: #cccccc;
    font-size: 0.85rem;
    line-height: 1.4;
}
.booking-address-text a {
    color: #60a5fa;
    text-decoration: none;
}

/* Policy Box */
.booking-policy {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.1));
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 12px;
    padding: 0.875rem;
    margin-bottom: 1rem;
}
.booking-policy-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #f59e0b;
    font-weight: 600;
    font-size: 0.85rem;
    margin: 0 0 0.5rem 0;
}
.booking-policy-text {
    color: #d4a574;
    font-size: 0.8rem;
    line-height: 1.4;
    margin: 0;
}

/* Email Notice */
.booking-notice {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 10px;
    margin-bottom: 0.75rem;
}
.booking-notice i {
    color: #22c55e;
    font-size: 0.9rem;
}
.booking-notice span {
    color: #86efac;
    font-size: 0.8rem;
}
.booking-notice.reminder {
    background: #111111;
    border-color: #222222;
}
.booking-notice.reminder i {
    color: #f59e0b;
}
.booking-notice.reminder span {
    color: #888888;
}

/* QR Code Section */
.booking-qr {
    text-align: center;
    padding: 1.25rem;
    background: #111111;
    border: 2px dashed #333333;
    border-radius: 16px;
    margin-bottom: 1rem;
}
.booking-qr-title {
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 1rem 0;
}
.booking-qr-title i {
    color: #ec4899;
}
.booking-qr img {
    width: 140px;
    height: 140px;
    border-radius: 12px;
    background: #000000;
    padding: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}
.booking-qr-number {
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: #0a0a0a;
    border-radius: 8px;
    display: inline-block;
}
.booking-qr-number .label {
    color: #666666;
    font-size: 0.7rem;
    text-transform: uppercase;
}
.booking-qr-number .number {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: 2px;
    font-family: monospace;
}

/* Verification Code */
.booking-verification {
    margin-top: 1rem;
    padding: 0.875rem;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.1));
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 12px;
    display: inline-block;
}
.booking-verification .label {
    color: #f59e0b;
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.booking-verification .code {
    color: #fbbf24;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: 3px;
    font-family: monospace;
    margin-top: 0.25rem;
}
.booking-qr-hint {
    color: #666666;
    font-size: 0.75rem;
    margin: 1rem 0 0 0;
}

/* Checked In State */
.booking-checkedin {
    text-align: center;
    padding: 1.5rem;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 16px;
    margin-bottom: 1rem;
}
.booking-checkedin-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}
.booking-checkedin-icon i {
    color: #ffffff;
    font-size: 1.25rem;
}
.booking-checkedin h4 {
    color: #22c55e;
    margin: 0;
    font-size: 1rem;
}
.booking-checkedin p {
    color: #86efac;
    font-size: 0.85rem;
    margin: 0.25rem 0 0 0;
}

/* Refund Notice */
.booking-refund {
    text-align: center;
    padding: 1rem;
    background: #111111;
    border: 1px solid #222222;
    border-radius: 12px;
    margin-bottom: 1rem;
}
.booking-refund p {
    margin: 0;
    color: #ffffff;
}
.booking-refund p + p {
    margin-top: 0.25rem;
    color: #888888;
    font-size: 0.85rem;
}

/* Buttons */
.booking-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.875rem 1rem;
    border: none;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.booking-btn-primary {
    background: #ffffff;
    color: #000000;
}
.booking-btn-primary:hover {
    background: #f0f0f0;
    transform: translateY(-1px);
}
.booking-btn-secondary {
    background: #1a1a1a;
    color: #ffffff;
    border: 1px solid #333333;
}
.booking-btn-secondary:hover {
    background: #222222;
}
.booking-btn-danger {
    background: #dc2626;
    color: #ffffff;
}
.booking-btn-danger:hover {
    background: #b91c1c;
}
.booking-btn-pay {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #ffffff;
    font-size: 1.05rem;
    padding: 1rem;
}
.booking-btn-pay:hover {
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
}
.booking-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
}
.booking-actions .booking-btn {
    flex: 1;
}

/* Modal */
.booking-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    padding: 1rem;
    overflow-y: auto;
    align-items: center;
    justify-content: center;
}
.booking-modal-content {
    max-width: 360px;
    width: 100%;
    background: #0a0a0a;
    border: 1px solid #222222;
    border-radius: 20px;
    overflow: hidden;
    margin: auto;
}
.booking-modal-header {
    background: linear-gradient(135deg, #dc2626, #991b1b);
    padding: 1.5rem;
    text-align: center;
}
.booking-modal-header i {
    font-size: 2rem;
    color: #ffffff;
    margin-bottom: 0.5rem;
}
.booking-modal-header h3 {
    margin: 0;
    color: #ffffff;
    font-size: 1.1rem;
}
.booking-modal-body {
    padding: 1.5rem;
}
.booking-modal-info {
    background: #111111;
    border: 1px solid #222222;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    text-align: center;
}
.booking-modal-info p {
    margin: 0;
    color: #cccccc;
    font-size: 0.9rem;
}
.booking-modal-info .warning {
    color: #f59e0b;
    font-weight: 600;
}
.booking-modal-info .danger {
    color: #ef4444;
    font-size: 1.1rem;
    font-weight: 700;
    margin-top: 0.5rem;
}
.booking-modal-info .success {
    color: #22c55e;
}
.booking-modal-question {
    color: #888888;
    font-size: 0.9rem;
    text-align: center;
    margin-bottom: 1rem;
}

/* Light mode */
[data-theme="light"] .booking-page {
    background: #f5f5f5;
}
[data-theme="light"] .booking-card {
    background: #ffffff;
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-header {
    background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);
}
[data-theme="light"] .booking-title {
    color: #000000;
}
[data-theme="light"] .booking-number-badge {
    background: #f0f0f0;
    border-color: #e0e0e0;
    color: #000000;
}
[data-theme="light"] .booking-section {
    background: #fafafa;
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-section-title {
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-row {
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-label {
    color: #666666;
}
[data-theme="light"] .booking-value {
    color: #000000;
}
[data-theme="light"] .booking-address {
    background: #f0f0f0;
}
[data-theme="light"] .booking-address-text {
    color: #333333;
}
[data-theme="light"] .booking-qr {
    background: #fafafa;
    border-color: #cccccc;
}
[data-theme="light"] .booking-qr-title {
    color: #000000;
}
[data-theme="light"] .booking-qr-number {
    background: #f0f0f0;
}
[data-theme="light"] .booking-qr-number .number {
    color: #000000;
}
[data-theme="light"] .booking-qr img {
    background: #ffffff;
}
[data-theme="light"] .booking-btn-primary {
    background: #000000;
    color: #ffffff;
}
[data-theme="light"] .booking-btn-secondary {
    background: #f0f0f0;
    color: #000000;
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-modal-content {
    background: #ffffff;
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-modal-info {
    background: #f5f5f5;
    border-color: #e0e0e0;
}
[data-theme="light"] .booking-modal-info p {
    color: #333333;
}

/* Extra small screens */
@media (max-width: 380px) {
    .booking-page {
        padding: 0.5rem;
    }
    .booking-header {
        padding: 1.5rem 1rem;
    }
    .booking-body {
        padding: 1rem;
    }
    .booking-title {
        font-size: 1.25rem;
    }
    .booking-qr img {
        width: 120px;
        height: 120px;
    }
    .booking-verification .code {
        font-size: 1.25rem;
    }
}
</style>

<div class="booking-page">
    <?php if (isset($_GET['success']) && $_GET['success'] === 'cancelled_refund'): ?>
    <div style="max-width:500px;margin:0 auto 1rem;background:#065f46;border:1px solid #10b981;border-radius:12px;padding:1rem;text-align:center;">
        <p style="margin:0;color:#ffffff;font-weight:600;"><i class="fas fa-check-circle"></i> Boeking succesvol geannuleerd</p>
        <p style="margin:0.5rem 0 0;color:#a7f3d0;font-size:0.9rem;">Je terugbetaling wordt binnen 3-5 werkdagen verwerkt.</p>
    </div>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === 'cancelled'): ?>
    <div style="max-width:500px;margin:0 auto 1rem;background:#065f46;border:1px solid #10b981;border-radius:12px;padding:1rem;text-align:center;">
        <p style="margin:0;color:#ffffff;font-weight:600;"><i class="fas fa-check-circle"></i> Boeking succesvol geannuleerd</p>
    </div>
    <?php elseif (isset($_GET['error'])): ?>
    <div style="max-width:500px;margin:0 auto 1rem;background:#7f1d1d;border:1px solid #ef4444;border-radius:12px;padding:1rem;text-align:center;">
        <p style="margin:0;color:#ffffff;font-weight:600;"><i class="fas fa-exclamation-circle"></i> Er ging iets mis</p>
        <p style="margin:0.5rem 0 0;color:#fca5a5;font-size:0.9rem;">
            <?php
            $error = $_GET['error'];
            if ($error === 'csrf') echo 'Sessie verlopen. Ververs de pagina en probeer opnieuw.';
            elseif ($error === 'unauthorized') echo 'Je hebt geen toegang om deze boeking te annuleren.';
            else echo 'Probeer het opnieuw.';
            ?>
        </p>
    </div>
    <?php endif; ?>

    <div class="booking-card">
        <!-- Header with Status -->
        <div class="booking-header">
            <?php if ($booking['status'] === 'cancelled'): ?>
                <div class="booking-status-icon danger">
                    <i class="fas fa-times"></i>
                </div>
                <h1 class="booking-title">Boeking Geannuleerd</h1>
                <p class="booking-subtitle">Deze afspraak is geannuleerd</p>
            <?php elseif ($booking['status'] === 'pending' && $booking['payment_status'] !== 'paid'): ?>
                <div class="booking-status-icon warning">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h1 class="booking-title"><?= $__('payment_required') ?></h1>
                <p class="booking-subtitle"><?= $__('complete_payment_to_confirm') ?></p>
            <?php elseif ($booking['status'] === 'checked_in'): ?>
                <div class="booking-status-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="booking-title">Ingecheckt!</h1>
                <p class="booking-subtitle">Je aanwezigheid is bevestigd</p>
            <?php else: ?>
                <div class="booking-status-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="booking-title"><?= $__('booking_confirmed') ?></h1>
                <p class="booking-subtitle">Je afspraak is bevestigd</p>
            <?php endif; ?>
            <div class="booking-number-badge">#<?= htmlspecialchars($booking['booking_number']) ?></div>
        </div>

        <!-- Body Content -->
        <div class="booking-body">
            <?php if ($booking['status'] === 'cancelled' && ($booking['payment_status'] === 'paid' || $booking['payment_status'] === 'refunded')): ?>
                <div class="booking-refund">
                    <p><i class="fas fa-undo"></i> Terugbetaling in behandeling</p>
                    <p>Binnen 72 uur op je rekening</p>
                </div>
            <?php endif; ?>

            <?php if ($booking['status'] === 'pending' && $booking['payment_status'] !== 'paid'): ?>
                <a href="/payment/create/<?= $booking['uuid'] ?>" class="booking-btn booking-btn-pay">
                    <i class="fas fa-lock"></i> <?= $__('pay_now') ?> - <?php
                        $payPriceDisplay = $currencyService->convertFromEur((float)$booking['total_price'], $visitorCurrency);
                        echo $payPriceDisplay['local_formatted'];
                        if ($showDualCurrency): ?> <small>(<?= $payPriceDisplay['eur_formatted'] ?>)</small><?php endif;
                    ?>
                </a>
                <div style="height:1rem"></div>
            <?php endif; ?>

            <!-- Booking Details -->
            <div class="booking-section">
                <h3 class="booking-section-title"><i class="fas fa-calendar-check"></i> Afspraakdetails</h3>
                <div class="booking-row">
                    <span class="booking-label"><?= $__('salon') ?></span>
                    <span class="booking-value"><?= htmlspecialchars($booking['business_name']) ?></span>
                </div>
                <div class="booking-row">
                    <span class="booking-label"><?= $__('service') ?></span>
                    <span class="booking-value"><?= htmlspecialchars($booking['service_name']) ?></span>
                </div>
                <div class="booking-row">
                    <span class="booking-label"><?= $__('date') ?></span>
                    <span class="booking-value"><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></span>
                </div>
                <div class="booking-row">
                    <span class="booking-label"><?= $__('time') ?></span>
                    <span class="booking-value"><?= date('H:i', strtotime($booking['appointment_time'])) ?></span>
                </div>
                <div class="booking-row">
                    <span class="booking-label"><?= $__('duration') ?></span>
                    <span class="booking-value"><?= $booking['duration_minutes'] ?> min</span>
                </div>
                <div class="booking-row">
                    <span class="booking-label"><?= $__('total') ?></span>
                    <span class="booking-value price"><?php
                        $totalDisplay = $currencyService->convertFromEur((float)$booking['total_price'], $visitorCurrency);
                        echo $totalDisplay['local_formatted'];
                        if ($showDualCurrency): ?> <small style="opacity:0.7">(<?= $totalDisplay['eur_formatted'] ?>)</small><?php endif;
                    ?></span>
                </div>
                <div class="booking-address">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="booking-address-text">
                        <?= htmlspecialchars($booking['address']) ?>, <?= htmlspecialchars($booking['city']) ?>
                        <?php if (!empty($booking['business_phone'])): ?>
                            <br><a href="tel:<?= htmlspecialchars($booking['business_phone']) ?>"><i class="fas fa-phone"></i> <?= htmlspecialchars($booking['business_phone']) ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (($booking['status'] === 'confirmed' || $booking['payment_status'] === 'paid') && $booking['status'] !== 'cancelled'): ?>
                <!-- Policy -->
                <div class="booking-policy">
                    <h4 class="booking-policy-title"><i class="fas fa-exclamation-triangle"></i> 24-uurs beleid</h4>
                    <p class="booking-policy-text">Gratis annuleren tot 24u voor de afspraak. Binnen 24u: 50% kosten.</p>
                </div>

                <!-- Email Notice -->
                <div class="booking-notice">
                    <i class="fas fa-envelope-circle-check"></i>
                    <span>Bevestiging verzonden naar <?= htmlspecialchars($booking['guest_email'] ?? $booking['user_email'] ?? 'je e-mail') ?></span>
                </div>

                <!-- Reminder -->
                <div class="booking-notice reminder">
                    <i class="fas fa-bell"></i>
                    <span>Herinnering 24u en 1u voor je afspraak</span>
                </div>
            <?php endif; ?>

            <?php if (($booking['status'] === 'confirmed' || $booking['payment_status'] === 'paid') && $booking['status'] !== 'checked_in' && $booking['status'] !== 'cancelled'): ?>
                <!-- QR Code -->
                <div class="booking-qr">
                    <h4 class="booking-qr-title"><i class="fas fa-qrcode"></i> Check-in QR Code</h4>
                    <?php
                        $theme = $_COOKIE['theme'] ?? 'dark';
                        $qrColor = $theme === 'light' ? '000000' : 'ffffff';
                        $qrBg = $theme === 'light' ? 'ffffff' : '000000';
                    ?>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&color=<?= $qrColor ?>&bgcolor=<?= $qrBg ?>&data=<?= urlencode('https://glamourschedule.nl/checkin/' . $booking['uuid']) ?>" alt="QR" class="qr-code-img" data-checkin-url="<?= urlencode('https://glamourschedule.nl/checkin/' . $booking['uuid']) ?>">

                    <div class="booking-qr-number">
                        <div class="label">Boekingsnummer</div>
                        <div class="number"><?= htmlspecialchars($booking['booking_number']) ?></div>
                    </div>

                    <?php if (!empty($booking['verification_code'])): ?>
                    <div class="booking-verification">
                        <div class="label"><i class="fas fa-shield-alt"></i> Verificatiecode</div>
                        <div class="code"><?= htmlspecialchars($booking['verification_code']) ?></div>
                    </div>
                    <?php endif; ?>

                    <p class="booking-qr-hint"><i class="fas fa-info-circle"></i> Toon bij aankomst in de salon</p>
                </div>
            <?php elseif ($booking['status'] === 'checked_in'): ?>
                <div class="booking-checkedin">
                    <div class="booking-checkedin-icon"><i class="fas fa-check"></i></div>
                    <h4>Succesvol ingecheckt</h4>
                    <p>Veel plezier met je behandeling!</p>
                </div>
            <?php endif; ?>

            <?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'checked_in'): ?>
                <?php
                $appointmentDateTime = new DateTime($booking['appointment_date'] . ' ' . $booking['appointment_time']);
                $now = new DateTime();
                $hoursUntilAppointment = ($appointmentDateTime->getTimestamp() - $now->getTimestamp()) / 3600;
                $isWithin24Hours = $hoursUntilAppointment <= 24 && $hoursUntilAppointment > 0;
                $isPastAppointment = $hoursUntilAppointment <= 0;
                $halfPriceEur = $booking['total_price'] / 2;
                $halfPriceDisplay = $currencyService->convertFromEur($halfPriceEur, $visitorCurrency);
                ?>
                <div class="booking-actions">
                    <a href="/search" class="booking-btn booking-btn-secondary">
                        <i class="fas fa-plus"></i> Nieuw
                    </a>
                    <?php if (!$isPastAppointment): ?>
                    <button type="button" class="booking-btn booking-btn-danger" onclick="showCancelModal()">
                        <i class="fas fa-times"></i> Annuleer
                    </button>
                    <?php endif; ?>
                </div>
            <?php elseif ($booking['status'] === 'cancelled' || $booking['status'] === 'checked_in'): ?>
                <a href="/search" class="booking-btn booking-btn-primary" style="margin-top:1rem">
                    <i class="fas fa-search"></i> Nieuwe afspraak boeken
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($booking['status'] !== 'cancelled' && $booking['status'] !== 'checked_in' && !$isPastAppointment): ?>
<!-- Cancel Modal -->
<div id="cancelModal" class="booking-modal">
    <div class="booking-modal-content">
        <div class="booking-modal-header">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Afspraak annuleren</h3>
        </div>
        <div class="booking-modal-body">
            <div class="booking-modal-info">
                <?php if ($isWithin24Hours): ?>
                    <p class="warning"><i class="fas fa-clock"></i> Binnen 24 uur voor afspraak</p>
                    <p class="danger">50% (<?= $halfPriceDisplay['local_formatted'] ?>) gaat naar salon</p>
                    <p>Je krijgt <?= $halfPriceDisplay['local_formatted'] ?> terug</p>
                <?php else: ?>
                    <p class="success"><i class="fas fa-check-circle"></i> Gratis annuleren</p>
                    <p>Volledig bedrag (<?= $totalDisplay['local_formatted'] ?>) terug</p>
                <?php endif; ?>
            </div>
            <p class="booking-modal-question">Weet je het zeker?</p>
            <div class="booking-actions">
                <button type="button" class="booking-btn booking-btn-secondary" onclick="hideCancelModal()">Terug</button>
                <button type="button" class="booking-btn booking-btn-danger" onclick="showFinalConfirm()">Annuleer</button>
            </div>
        </div>
    </div>
</div>

<!-- Final Confirm Modal -->
<div id="finalConfirmModal" class="booking-modal" style="z-index:1001">
    <div class="booking-modal-content">
        <div class="booking-modal-body" style="text-align:center;padding:2rem">
            <i class="fas fa-question-circle" style="font-size:3rem;color:#f59e0b;margin-bottom:1rem"></i>
            <h3 style="color:#fff;margin:0 0 1rem">Laatste check</h3>
            <?php if ($isWithin24Hours): ?>
                <p style="color:#ef4444;font-weight:600;margin-bottom:1.5rem"><?= $halfPriceDisplay['local_formatted'] ?> gaat naar <?= htmlspecialchars($booking['business_name']) ?></p>
            <?php else: ?>
                <p style="color:#888;margin-bottom:1.5rem">Je afspraak wordt definitief geannuleerd</p>
            <?php endif; ?>
            <form method="POST" action="/booking/<?= $booking['uuid'] ?>/cancel">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <input type="hidden" name="confirm_cancel" value="1">
                <?php if ($isWithin24Hours): ?><input type="hidden" name="late_cancel" value="1"><?php endif; ?>
                <div class="booking-actions">
                    <button type="button" class="booking-btn booking-btn-secondary" onclick="hideFinalConfirm()">Behouden</button>
                    <button type="submit" class="booking-btn booking-btn-danger">Definitief</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function hideCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
    document.body.style.overflow = '';
}
function showFinalConfirm() {
    document.getElementById('cancelModal').style.display = 'none';
    document.getElementById('finalConfirmModal').style.display = 'flex';
}
function hideFinalConfirm() {
    document.getElementById('finalConfirmModal').style.display = 'none';
    document.getElementById('cancelModal').style.display = 'flex';
}
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) hideCancelModal();
});
document.getElementById('finalConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) hideFinalConfirm();
});
</script>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
