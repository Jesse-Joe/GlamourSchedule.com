<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeldige Link - GlamourSchedule</title>
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
        .invalid-card {
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .invalid-icon {
            width: 100px;
            height: 100px;
            background: rgba(251, 191, 36, 0.2);
            border: 2px solid #737373;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
        .invalid-icon i {
            font-size: 3rem;
            color: #737373;
        }
        h1 {
            color: #000000;
            margin: 0 0 1rem;
            font-size: 1.75rem;
        }
        p {
            color: #999999;
            line-height: 1.6;
            margin: 0 0 2rem;
        }
        .reasons {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 2rem;
        }
        .reasons h4 {
            color: #999999;
            margin: 0 0 0.75rem;
            font-size: 0.9rem;
        }
        .reasons ul {
            margin: 0;
            padding-left: 1.25rem;
            color: #666666;
            font-size: 0.9rem;
        }
        .reasons li {
            margin-bottom: 0.5rem;
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
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #999999;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .btn-secondary:hover {
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="invalid-card">
        <div class="invalid-icon">
            <i class="fas fa-link-slash"></i>
        </div>

        <h1>Ongeldige of Verlopen Link</h1>
        <p>Deze registratielink is niet geldig. Dit kan verschillende oorzaken hebben.</p>

        <div class="reasons">
            <h4>Mogelijke oorzaken:</h4>
            <ul>
                <li>De link is al gebruikt en je registratie is voltooid</li>
                <li>De link is verlopen</li>
                <li>De link is onjuist gekopieerd</li>
            </ul>
        </div>

        <a href="/login" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i> Probeer In te Loggen
        </a>

        <br>
        <a href="/contact" class="btn-secondary">
            <i class="fas fa-headset"></i> Hulp nodig? Neem contact op
        </a>
    </div>
</body>
</html>
