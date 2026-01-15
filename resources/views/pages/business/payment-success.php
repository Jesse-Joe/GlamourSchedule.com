<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betaling Geslaagd - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 1rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .success-card {
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(0, 0, 0, 0.1);
            border: 2px solid #333333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .success-icon i {
            font-size: 3rem;
            color: #333333;
        }
        h1 {
            color: #000000;
            margin: 0 0 1rem;
            font-size: 1.75rem;
        }
        p {
            color: #999999;
            line-height: 1.6;
            margin: 0 0 1.5rem;
        }
        .email-box {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 1rem;
            margin: 1.5rem 0;
        }
        .email-box .label {
            color: #666666;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }
        .email-box .email {
            color: #333333;
            font-weight: 600;
            word-break: break-all;
        }
        .info-box {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .info-box h4 {
            color: #333333;
            margin: 0 0 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-box ul {
            margin: 0;
            padding-left: 1.5rem;
            color: #333333;
        }
        .info-box li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Betaling Geslaagd!</h1>
        <p>Bedankt voor je betaling. We hebben een e-mail gestuurd met instructies om je registratie af te ronden.</p>

        <div class="email-box">
            <div class="label">E-mail verstuurd naar:</div>
            <div class="email"><?= htmlspecialchars($email ?? '') ?></div>
        </div>

        <div class="info-box">
            <h4><i class="fas fa-envelope"></i> Check je inbox</h4>
            <ul>
                <li>Klik op de link in de e-mail</li>
                <li>Stel je wachtwoord in</li>
                <li>Vul je bedrijfsgegevens aan</li>
                <li>Ga direct aan de slag!</li>
            </ul>
        </div>

        <p style="font-size:0.9rem;color:#666666;margin-top:2rem">
            <i class="fas fa-info-circle"></i> Geen e-mail ontvangen? Check je spam folder.
        </p>
    </div>
</body>
</html>
