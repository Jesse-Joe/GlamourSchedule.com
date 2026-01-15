<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betaling Verwerken - GlamourSchedule Sales</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="5;url=/sales/payment/complete">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000000;
            padding: 2rem;
        }
        .pending-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            text-align: center;
        }
        .spinner {
            width: 80px;
            height: 80px;
            border: 4px solid #e5e7eb;
            border-top-color: #333333;
            border-radius: 50%;
            margin: 0 auto 2rem;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            color: #1f2937;
        }
        p {
            color: #6b7280;
            margin: 0 0 2rem;
            line-height: 1.6;
        }
        .info-box {
            background: #ffffff;
            border: 1px solid #86efac;
            border-radius: 12px;
            padding: 1rem;
            color: #166534;
            font-size: 0.9rem;
        }
        .retry-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: #333333;
            text-decoration: none;
            font-weight: 500;
        }
        .retry-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="pending-card">
        <div class="spinner"></div>
        <h1>Betaling Verwerken...</h1>
        <p>We controleren je betaling. Dit duurt meestal maar een paar seconden.</p>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            De pagina ververst automatisch. Even geduld!
        </div>

        <a href="/sales/payment/complete" class="retry-link">
            <i class="fas fa-redo"></i> Pagina verversen
        </a>
    </div>
</body>
</html>
