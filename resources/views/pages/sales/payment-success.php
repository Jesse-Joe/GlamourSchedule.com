<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie Voltooid - GlamourSchedule</title>
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
            background: linear-gradient(135deg, #333333, #000000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        .success-icon i {
            font-size: 3rem;
            color: #ffffff;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        h1 {
            color: #333333;
            margin: 0 0 1rem;
            font-size: 1.75rem;
        }
        p {
            color: #999999;
            line-height: 1.6;
            margin: 0 0 1.5rem;
        }
        .info-box {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }
        .info-box p {
            margin: 0;
            color: #333333;
        }
        .info-box strong {
            color: #000000;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .note {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Welkom, Sales Partner!</h1>
        <p>Je betaling is ontvangen en je account is nu actief. We hebben je een e-mail gestuurd met je inloggegevens.</p>

        <div class="info-box">
            <p><i class="fas fa-envelope"></i> Check je inbox voor je tijdelijke wachtwoord</p>
            <p style="margin-top:0.75rem"><i class="fas fa-key"></i> Je kunt na het inloggen een nieuw wachtwoord instellen</p>
            <p style="margin-top:0.75rem"><i class="fas fa-tag"></i> Je referral code: <strong><?= htmlspecialchars($salesUser['referral_code'] ?? '') ?></strong></p>
        </div>

        <a href="/sales/login" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i> Nu Inloggen
        </a>

        <p class="note">
            <i class="fas fa-info-circle"></i> Geen email ontvangen? Check je spam folder.
        </p>
    </div>
</body>
</html>
