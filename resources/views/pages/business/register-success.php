<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Registratie Gelukt') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #000000, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .icon i {
            font-size: 40px;
            color: #ffffff;
        }
        h1 {
            color: #333333;
            font-size: 1.75rem;
            margin-bottom: 15px;
        }
        .subtitle {
            color: #666666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .email-box {
            background: #f8f9fa;
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .email-box p {
            color: #666666;
            margin-bottom: 10px;
        }
        .email-box strong {
            color: #333333;
            font-size: 1.1rem;
        }
        .steps {
            text-align: left;
            background: #fefce8;
            border: 1px solid #fef08a;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .steps h3 {
            color: #854d0e;
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .steps ol {
            color: #713f12;
            padding-left: 20px;
        }
        .steps li {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .info {
            background: #ecfdf5;
            border: 1px solid #000000;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info p {
            color: #047857;
            margin: 0;
            font-weight: 500;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #333333, #000000);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .footer {
            margin-top: 30px;
            color: #999999;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-envelope-circle-check"></i>
        </div>

        <h1>Check je e-mail!</h1>
        <p class="subtitle">We hebben een verificatie-email gestuurd</p>

        <div class="email-box">
            <p>Verstuurd naar:</p>
            <strong><?= htmlspecialchars($email ?? '') ?></strong>
        </div>

        <div class="info">
            <p><i class="fas fa-gift"></i> Je krijgt 14 dagen GRATIS om alles te proberen!</p>
        </div>

        <div class="steps">
            <h3><i class="fas fa-list-check"></i> Volgende stappen:</h3>
            <ol>
                <li>Open de email van GlamourSchedule</li>
                <li>Klik op de verificatie-link</li>
                <li>Vul je salongegevens aan</li>
                <li>Begin met online boekingen!</li>
            </ol>
        </div>

        <a href="https://mail.google.com" target="_blank" class="btn">
            <i class="fas fa-envelope"></i> Open e-mail
        </a>

        <p class="footer">
            Geen email ontvangen? Check je spam folder of <a href="/contact" style="color:#333333;">neem contact op</a>
        </p>
    </div>
</body>
</html>
