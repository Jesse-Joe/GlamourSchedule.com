<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($booking['company_name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .payment-card {
            background: #ffffff;
            border-radius: 24px;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .card-header {
            background: linear-gradient(135deg, #000000, #333333);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .business-logo {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
        }
        .business-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        .card-header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }
        .card-header p {
            opacity: 0.8;
            font-size: 14px;
        }

        .card-body {
            padding: 30px;
        }

        .booking-details {
            background: #f9fafb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6b7280;
            font-size: 14px;
        }
        .detail-value {
            color: #111827;
            font-weight: 600;
            font-size: 14px;
            text-align: right;
        }

        .payment-summary {
            background: linear-gradient(135deg, #f5f5f5, #e5e5e5);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .payment-summary h3 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .amount-row.total {
            border-top: 2px solid #d1d5db;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 18px;
            font-weight: 700;
        }
        .amount-row .label {
            color: #4b5563;
        }
        .amount-row .value {
            color: #111827;
            font-weight: 600;
        }
        .amount-row.total .value {
            color: #000000;
        }

        .cash-notice {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .cash-notice i {
            color: #d97706;
            font-size: 18px;
            margin-top: 2px;
        }
        .cash-notice p {
            color: #92400e;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
        }

        .btn-pay {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #000000, #333333);
            color: #ffffff;
            padding: 18px;
            border: none;
            border-radius: 14px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-pay i {
            margin-right: 8px;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            color: #6b7280;
            font-size: 12px;
        }
        .secure-badge i {
            color: #22c55e;
        }

        .card-footer {
            background: #000000;
            padding: 20px;
            text-align: center;
        }
        .card-footer p {
            color: #ffffff;
            opacity: 0.6;
            font-size: 12px;
        }

        .flash-message {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .flash-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .flash-warning {
            background: #fefce8;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="card-header">
            <div class="business-logo">
                <?php if (!empty($booking['logo'])): ?>
                    <img src="<?= htmlspecialchars($booking['logo']) ?>" alt="<?= htmlspecialchars($booking['company_name']) ?>">
                <?php else: ?>
                    <i class="fas fa-store"></i>
                <?php endif; ?>
            </div>
            <h1><?= htmlspecialchars($booking['company_name']) ?></h1>
            <p>Afspraak bevestigen</p>
        </div>

        <div class="card-body">
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="flash-message flash-<?= $_SESSION['flash']['type'] ?>">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div class="booking-details">
                <div class="detail-row">
                    <span class="detail-label">Dienst</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['service_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Datum</span>
                    <span class="detail-value"><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tijd</span>
                    <span class="detail-value"><?= date('H:i', strtotime($booking['appointment_time'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duur</span>
                    <span class="detail-value"><?= $booking['duration_minutes'] ?> minuten</span>
                </div>
                <?php if (!empty($booking['street'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Locatie</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['street'] . ' ' . $booking['house_number']) ?><br><?= htmlspecialchars($booking['postal_code'] . ' ' . $booking['city']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($booking['payment_method'] === 'cash'): ?>
            <div class="cash-notice">
                <i class="fas fa-info-circle"></i>
                <p>
                    <strong>Contante betaling</strong><br>
                    Je betaalt nu €<?= number_format($booking['service_fee'], 2, ',', '.') ?> reserveringskosten online.
                    Het resterende bedrag van €<?= number_format($booking['total_price'] - $booking['service_fee'], 2, ',', '.') ?> betaal je contant bij je afspraak.
                </p>
            </div>
            <?php endif; ?>

            <div class="payment-summary">
                <h3>Betaaloverzicht</h3>
                <div class="amount-row">
                    <span class="label"><?= htmlspecialchars($booking['service_name']) ?></span>
                    <span class="value">€<?= number_format($booking['total_price'], 2, ',', '.') ?></span>
                </div>
                <?php if ($booking['payment_method'] === 'cash'): ?>
                <div class="amount-row">
                    <span class="label">Contant bij afspraak</span>
                    <span class="value">- €<?= number_format($booking['total_price'] - $booking['service_fee'], 2, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <div class="amount-row total">
                    <span class="label">Nu te betalen</span>
                    <span class="value">€<?= number_format($paymentAmount, 2, ',', '.') ?></span>
                </div>
            </div>

            <form method="POST" action="/pay/<?= htmlspecialchars($booking['uuid']) ?>">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn-pay">
                    <i class="fas fa-lock"></i> Betaal €<?= number_format($paymentAmount, 2, ',', '.') ?>
                </button>
            </form>

            <div class="secure-badge">
                <i class="fas fa-shield-alt"></i>
                <span>Veilig betalen via iDEAL, creditcard of PayPal</span>
            </div>
        </div>

        <div class="card-footer">
            <p>&copy; <?= date('Y') ?> GlamourSchedule</p>
        </div>
    </div>
</body>
</html>
