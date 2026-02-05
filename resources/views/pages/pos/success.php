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
        .success-card {
            background: #ffffff;
            border-radius: 24px;
            width: 100%;
            max-width: 480px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            text-align: center;
        }
        .card-header {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff;
            padding: 50px 30px;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 50px;
            animation: scaleIn 0.5s ease;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .card-header h1 {
            font-size: 26px;
            margin-bottom: 10px;
        }
        .card-header p {
            opacity: 0.9;
            font-size: 15px;
        }

        .card-body {
            padding: 35px 30px;
        }

        .business-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f9fafb;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            text-align: left;
        }
        .business-logo {
            width: 50px;
            height: 50px;
            background: #000000;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        .business-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        .business-details h3 {
            font-size: 16px;
            margin-bottom: 3px;
        }
        .business-details p {
            color: #6b7280;
            font-size: 13px;
        }

        .booking-summary {
            background: #f9fafb;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: left;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: #6b7280;
            font-size: 14px;
        }
        .summary-value {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }

        <?php if ($booking['payment_method'] === 'cash'): ?>
        .cash-reminder {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            text-align: left;
        }
        .cash-reminder i {
            color: #d97706;
            font-size: 20px;
        }
        .cash-reminder p {
            color: #92400e;
            font-size: 13px;
            line-height: 1.5;
        }
        <?php endif; ?>

        .location-card {
            background: #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: left;
        }
        .location-card h4 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .location-card p {
            color: #111827;
            font-size: 15px;
            line-height: 1.5;
        }

        .confirmation-note {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .confirmation-note i {
            color: #22c55e;
        }

        .btn-home {
            display: inline-block;
            background: #000000;
            color: #ffffff;
            padding: 16px 40px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: transform 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
        }

        .card-footer {
            background: #000000;
            padding: 20px;
        }
        .card-footer p {
            color: #ffffff;
            opacity: 0.6;
            font-size: 12px;
        }

        .add-to-calendar {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .calendar-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            background: #f0f0f0;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .calendar-btn:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="card-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Afspraak Bevestigd!</h1>
            <p>Je betaling is succesvol ontvangen</p>
        </div>

        <div class="card-body">
            <div class="business-info">
                <div class="business-logo">
                    <?php if (!empty($booking['logo'])): ?>
                        <img src="<?= htmlspecialchars($booking['logo']) ?>" alt="">
                    <?php else: ?>
                        <i class="fas fa-store"></i>
                    <?php endif; ?>
                </div>
                <div class="business-details">
                    <h3><?= htmlspecialchars($booking['company_name']) ?></h3>
                    <?php if (!empty($booking['business_phone'])): ?>
                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($booking['business_phone']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="booking-summary">
                <div class="summary-row">
                    <span class="summary-label">Dienst</span>
                    <span class="summary-value"><?= htmlspecialchars($booking['service_name']) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Datum</span>
                    <span class="summary-value"><?= !empty($booking['appointment_date']) ? $formatDate($booking['appointment_date']) : '-' ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Tijd</span>
                    <span class="summary-value"><?= !empty($booking['appointment_time']) ? $formatTime($booking['appointment_time']) : '-' ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Duur</span>
                    <span class="summary-value"><?= $booking['duration_minutes'] ?? 0 ?> minuten</span>
                </div>
            </div>

            <?php if ($booking['payment_method'] === 'cash'): ?>
            <div class="cash-reminder">
                <i class="fas fa-coins"></i>
                <div>
                    <p>
                        <strong>Vergeet niet!</strong><br>
                        Neem €<?= number_format($booking['total_price'] - $booking['service_fee'], 2, ',', '.') ?> contant mee voor de rest van de betaling.
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($booking['street'])): ?>
            <div class="location-card">
                <h4><i class="fas fa-map-marker-alt"></i> Locatie</h4>
                <p>
                    <?= htmlspecialchars($booking['company_name']) ?><br>
                    <?= htmlspecialchars($booking['street'] . ' ' . $booking['house_number']) ?><br>
                    <?= htmlspecialchars($booking['postal_code'] . ' ' . $booking['city']) ?>
                </p>
            </div>
            <?php endif; ?>

            <p class="confirmation-note">
                <i class="fas fa-envelope"></i> Een bevestigingsmail is verstuurd naar je e-mailadres.
            </p>

            <a href="/business/<?= htmlspecialchars($booking['business_slug']) ?>" class="btn-home">
                Bekijk <?= htmlspecialchars($booking['company_name']) ?>
            </a>

            <?php
            // Generate calendar links
            $startDate = date('Ymd', strtotime($booking['appointment_date']));
            $startTime = date('His', strtotime($booking['appointment_time']));
            $endTime = date('His', strtotime($booking['appointment_time'] . ' +' . $booking['duration_minutes'] . ' minutes'));
            $title = urlencode($booking['service_name'] . ' bij ' . $booking['company_name']);
            $location = urlencode($booking['street'] . ' ' . $booking['house_number'] . ', ' . $booking['postal_code'] . ' ' . $booking['city']);

            $googleCalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$startDate}T{$startTime}/{$startDate}T{$endTime}&location={$location}";
            ?>

            <div class="add-to-calendar">
                <a href="<?= $googleCalUrl ?>" target="_blank" class="calendar-btn">
                    <i class="fab fa-google"></i> Google Agenda
                </a>
            </div>
        </div>

        <div class="card-footer">
            <p>&copy; <?= date('Y') ?> GlamourSchedule</p>
        </div>
    </div>
</body>
</html>
