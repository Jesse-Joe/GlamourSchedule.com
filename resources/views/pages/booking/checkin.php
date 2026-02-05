<?php ob_start(); ?>

<style>
/* Check-in page specific styles */
.checkin-container {
    max-width: 500px;
    margin: 0 auto;
    padding: 1rem;
}
.checkin-card {
    background: #000000;
    border: 1px solid #333333;
    border-radius: 20px;
    padding: 2rem;
    color: #ffffff;
}
.checkin-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.checkin-icon-success {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    box-shadow: 0 10px 30px rgba(34, 197, 94, 0.3);
}
.checkin-icon-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}
.checkin-icon-info {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
}
.checkin-icon-neutral {
    background: #1a1a1a;
}
.checkin-icon i {
    font-size: 2.5rem;
    color: #ffffff;
}
.checkin-title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    color: #ffffff;
}
.checkin-subtitle {
    text-align: center;
    color: #999999;
    font-size: 1rem;
    margin-bottom: 2rem;
}
.checkin-details {
    background: #0a0a0a;
    border: 1px solid #222222;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.checkin-details-title {
    font-size: 0.85rem;
    color: #666666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0 0 1rem 0;
}
.checkin-details table {
    width: 100%;
}
.checkin-details td {
    padding: 0.6rem 0;
    border-bottom: 1px solid #1a1a1a;
}
.checkin-details tr:last-child td {
    border-bottom: none;
}
.checkin-details .label {
    color: #888888;
    font-size: 0.9rem;
}
.checkin-details .value {
    text-align: right;
    font-weight: 600;
    color: #ffffff;
}
.checkin-details .value.highlight {
    color: #22c55e;
    font-size: 1.1rem;
}
.checkin-details .value.code {
    font-family: monospace;
    letter-spacing: 2px;
    color: #f59e0b;
    font-weight: 700;
}
.checkin-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem;
    background: #ffffff;
    color: #000000;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.checkin-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}
.checkin-btn-secondary {
    background: #1a1a1a;
    color: #ffffff;
    border: 1px solid #333333;
}
.checkin-btn-secondary:hover {
    background: #222222;
    border-color: #444444;
}
.checkin-note {
    text-align: center;
    color: #666666;
    font-size: 0.85rem;
    margin-top: 1rem;
}
.checkin-note i {
    margin-right: 0.25rem;
}
.checkin-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(34, 197, 94, 0.15);
    color: #22c55e;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}
.checkin-alert {
    background: #1a1a1a;
    border: 1px solid #333333;
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    color: #f59e0b;
}

/* Light mode overrides */
[data-theme="light"] .checkin-card {
    background: #ffffff;
    border-color: #e0e0e0;
    color: #000000;
}
[data-theme="light"] .checkin-title {
    color: #000000;
}
[data-theme="light"] .checkin-subtitle {
    color: #666666;
}
[data-theme="light"] .checkin-details {
    background: #f5f5f5;
    border-color: #e0e0e0;
}
[data-theme="light"] .checkin-details td {
    border-color: #e0e0e0;
}
[data-theme="light"] .checkin-details .label {
    color: #666666;
}
[data-theme="light"] .checkin-details .value {
    color: #000000;
}
[data-theme="light"] .checkin-btn {
    background: #000000;
    color: #ffffff;
}
[data-theme="light"] .checkin-btn:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}
[data-theme="light"] .checkin-btn-secondary {
    background: #f5f5f5;
    color: #000000;
    border-color: #e0e0e0;
}
[data-theme="light"] .checkin-note {
    color: #888888;
}
[data-theme="light"] .checkin-alert {
    background: #fff8e6;
    border-color: #f59e0b;
    color: #92400e;
}
</style>

<div class="checkin-container">
    <?php
    $success = isset($_GET['success']);
    $already = isset($_GET['already']);
    $error = $_GET['error'] ?? null;
    $customerName = $booking['guest_name'] ?? trim(($booking['first_name'] ?? '') . ' ' . ($booking['last_name'] ?? '')) ?: 'Klant';
    ?>

    <?php if ($success || $already || $booking['status'] === 'checked_in'): ?>
        <!-- Success State -->
        <div class="checkin-card" style="text-align:center">
            <div class="checkin-icon checkin-icon-success">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="checkin-title"><?= $__('checked_in_success') ?></h2>
            <p class="checkin-subtitle"><?= htmlspecialchars($customerName) ?> <?= $__('successfully_checked_in') ?></p>

            <div class="checkin-status-badge">
                <i class="fas fa-check-circle"></i> <?= $__('checkin_completed') ?>
            </div>

            <div class="checkin-details">
                <table>
                    <tr>
                        <td class="label">Boeking</td>
                        <td class="value">#<?= htmlspecialchars($booking['booking_number']) ?></td>
                    </tr>
                    <?php if (!empty($booking['verification_code'])): ?>
                    <tr>
                        <td class="label"><i class="fas fa-shield-alt" style="color:#f59e0b"></i> Verificatie</td>
                        <td class="value code"><?= htmlspecialchars($booking['verification_code']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">Dienst</td>
                        <td class="value"><?= htmlspecialchars($booking['service_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tijd</td>
                        <td class="value"><?= $formatTime($booking['appointment_time']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Duur</td>
                        <td class="value"><?= $booking['duration_minutes'] ?> min</td>
                    </tr>
                </table>
            </div>

            <p class="checkin-note">
                <i class="fas fa-euro-sign"></i> <?= $__('payout_released') ?>
            </p>

            <a href="/business/bookings" class="checkin-btn checkin-btn-secondary" style="margin-top:1rem">
                <i class="fas fa-arrow-left"></i> <?= $__('back_to_bookings') ?>
            </a>
        </div>

    <?php elseif ($error === 'unauthorized'): ?>
        <!-- Not authorized -->
        <div class="checkin-card" style="text-align:center">
            <div class="checkin-icon checkin-icon-warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 class="checkin-title"><?= $__('not_authorized') ?></h2>
            <p class="checkin-subtitle"><?= $__('login_to_checkin') ?></p>
            <a href="/business/login" class="checkin-btn">
                <i class="fas fa-sign-in-alt"></i> <?= $__('login_as_business') ?>
            </a>
        </div>

    <?php elseif ($error === 'not_paid'): ?>
        <!-- Not paid -->
        <div class="checkin-card" style="text-align:center">
            <div class="checkin-icon checkin-icon-warning">
                <i class="fas fa-credit-card"></i>
            </div>
            <h2 class="checkin-title">Niet betaald</h2>
            <p class="checkin-subtitle">Deze boeking is nog niet betaald en kan niet worden ingecheckt.</p>
            <a href="/business/bookings" class="checkin-btn checkin-btn-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar boekingen
            </a>
        </div>

    <?php elseif ($isBusinessOwner): ?>
        <!-- Check-in Form for Business Owner -->
        <div class="checkin-card">
            <div style="text-align:center;margin-bottom:2rem">
                <div class="checkin-icon checkin-icon-info">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h2 class="checkin-title">Klant Check-in</h2>
                <p class="checkin-subtitle" style="margin-bottom:0">Bevestig de aanwezigheid</p>
            </div>

            <div class="checkin-details">
                <h3 class="checkin-details-title">Boekingsdetails</h3>
                <table>
                    <tr>
                        <td class="label">Klant</td>
                        <td class="value"><?= htmlspecialchars($customerName) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Dienst</td>
                        <td class="value"><?= htmlspecialchars($booking['service_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Datum</td>
                        <td class="value"><?= $formatDate($booking['appointment_date']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Tijd</td>
                        <td class="value"><?= $formatTime($booking['appointment_time']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Bedrag</td>
                        <td class="value highlight">&euro;<?= number_format($booking['total_price'], 2, ',', '.') ?></td>
                    </tr>
                </table>
            </div>

            <?php if ($booking['payment_status'] === 'paid'): ?>
                <form method="POST" action="/checkin/<?= $booking['uuid'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <button type="submit" class="checkin-btn">
                        <i class="fas fa-check-circle"></i> Bevestig Check-in
                    </button>
                </form>
                <p class="checkin-note">
                    <i class="fas fa-info-circle"></i> Na check-in wordt de uitbetaling vrijgegeven
                </p>
            <?php else: ?>
                <div class="checkin-alert">
                    <i class="fas fa-exclamation-circle"></i> Boeking is nog niet betaald
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Not logged in as business -->
        <div class="checkin-card" style="text-align:center">
            <div class="checkin-icon checkin-icon-neutral">
                <i class="fas fa-store"></i>
            </div>
            <h2 class="checkin-title">Check-in voor <?= htmlspecialchars($booking['business_name']) ?></h2>
            <p class="checkin-subtitle">Log in als <?= htmlspecialchars($booking['business_name']) ?> om deze klant in te checken.</p>

            <div class="checkin-details" style="text-align:left">
                <table>
                    <tr>
                        <td class="label">Boeking</td>
                        <td class="value">#<?= htmlspecialchars($booking['booking_number']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Dienst</td>
                        <td class="value"><?= htmlspecialchars($booking['service_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Datum</td>
                        <td class="value"><?= $formatDate($booking['appointment_date']) ?> om <?= $formatTime($booking['appointment_time']) ?></td>
                    </tr>
                </table>
            </div>

            <a href="/business/login?redirect=<?= urlencode('/checkin/' . $booking['uuid']) ?>" class="checkin-btn">
                <i class="fas fa-sign-in-alt"></i> <?= $__('login_as_business') ?>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
