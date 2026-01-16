<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 24px;
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            text-align: center;
        }
        .card-header {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff;
            padding: 50px 40px;
        }
        .success-icon {
            width: 90px;
            height: 90px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 40px;
        }
        .card-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .card-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .card-body {
            padding: 40px;
        }
        .booking-info {
            background: #f9fafb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: left;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
            font-size: 14px;
        }
        .info-value {
            font-weight: 600;
            color: #111827;
            font-size: 14px;
        }
        .message {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 25px;
        }
        .btn {
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
        .btn:hover {
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
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Al Betaald</h1>
            <p>Deze afspraak is al bevestigd</p>
        </div>

        <div class="card-body">
            <div class="booking-info">
                <div class="info-row">
                    <span class="info-label">Dienst</span>
                    <span class="info-value"><?= htmlspecialchars($booking['service_name'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Datum</span>
                    <span class="info-value"><?= date('d-m-Y', strtotime($booking['appointment_date'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tijd</span>
                    <span class="info-value"><?= date('H:i', strtotime($booking['appointment_time'])) ?></span>
                </div>
            </div>

            <p class="message">
                Je hebt al betaald voor deze afspraak. We zien je graag op de afgesproken datum en tijd!
            </p>

            <a href="/" class="btn">
                <i class="fas fa-home"></i> Naar Homepage
            </a>
        </div>

        <div class="card-footer">
            <p>&copy; <?= date('Y') ?> GlamourSchedule</p>
        </div>
    </div>
</body>
</html>
