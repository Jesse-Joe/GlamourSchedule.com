<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - GlamourSchedule Admin</title>
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
            background: #000000;
            color: #ffffff;
            padding: 50px 40px;
        }
        .error-icon {
            width: 90px;
            height: 90px;
            background: #ef4444;
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
            opacity: 0.8;
            font-size: 14px;
        }
        .card-body {
            padding: 40px;
        }
        .message {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        .btn-admin {
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
        .btn-admin:hover {
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
            <div class="error-icon">
                <i class="fas fa-link-slash"></i>
            </div>
            <h1>Ongeldige Link</h1>
            <p>Deze verificatielink is niet geldig</p>
        </div>

        <div class="card-body">
            <p class="message">
                Deze link is ongeldig, verlopen of al eerder gebruikt.
                Het bedrijf is mogelijk al geverifieerd of afgewezen.
            </p>

            <a href="/admin/businesses" class="btn-admin">
                <i class="fas fa-building"></i> Bekijk Alle Bedrijven
            </a>
        </div>

        <div class="card-footer">
            <p>&copy; 2025 GlamourSchedule Admin</p>
        </div>
    </div>
</body>
</html>
