<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betaling Mislukt - GlamourSchedule</title>
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
        .failed-card {
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .failed-icon {
            width: 100px;
            height: 100px;
            background: rgba(0, 0, 0, 0.1);
            border: 2px solid #333333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
        }
        .failed-icon i {
            font-size: 3rem;
            color: #333333;
        }
        h1 {
            color: #d4d4d4;
            margin: 0 0 1rem;
            font-size: 1.75rem;
        }
        p {
            color: #999999;
            line-height: 1.6;
            margin: 0 0 2rem;
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
    <div class="failed-card">
        <div class="failed-icon">
            <i class="fas fa-times"></i>
        </div>

        <h1>Betaling Mislukt</h1>
        <p>De betaling kon niet worden voltooid. Dit kan verschillende oorzaken hebben, zoals een geannuleerde transactie of onvoldoende saldo.</p>

        <a href="/sales/register" class="btn-primary">
            <i class="fas fa-redo"></i> Opnieuw Proberen
        </a>

        <br>
        <a href="/contact" class="btn-secondary">
            <i class="fas fa-headset"></i> Hulp nodig? Neem contact op
        </a>
    </div>
</body>
</html>
