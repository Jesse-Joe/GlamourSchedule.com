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
            max-width: 480px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            text-align: center;
        }
        .card-header {
            background: #000000;
            color: #ffffff;
            padding: 50px 40px;
        }
        .status-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 50px;
        }
        .status-icon.approved {
            background: #22c55e;
        }
        .status-icon.rejected {
            background: #ef4444;
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
            padding: 40px;
        }
        .business-name {
            font-size: 20px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 20px;
        }
        .message-box {
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
        }
        .message-box.approved {
            background: #f0fdf4;
            border: 2px solid #22c55e;
        }
        .message-box.rejected {
            background: #fef2f2;
            border: 2px solid #ef4444;
        }
        .message-box h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .message-box.approved h3 {
            color: #166534;
        }
        .message-box.rejected h3 {
            color: #991b1b;
        }
        .message-box p {
            font-size: 14px;
            line-height: 1.6;
        }
        .message-box.approved p {
            color: #166534;
        }
        .message-box.rejected p {
            color: #991b1b;
        }
        .email-sent {
            background: #f9fafb;
            border-radius: 12px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #6b7280;
            font-size: 14px;
        }
        .email-sent i {
            color: #22c55e;
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
            margin-top: 25px;
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
            <div class="status-icon <?= $status ?>">
                <?php if ($status === 'approved'): ?>
                    <i class="fas fa-check"></i>
                <?php else: ?>
                    <i class="fas fa-times"></i>
                <?php endif; ?>
            </div>
            <h1>
                <?php if ($status === 'approved'): ?>
                    Bedrijf Geaccepteerd
                <?php else: ?>
                    Bedrijf Afgewezen
                <?php endif; ?>
            </h1>
            <p>De actie is succesvol uitgevoerd</p>
        </div>

        <div class="card-body">
            <div class="business-name"><?= htmlspecialchars($business['name']) ?></div>

            <div class="message-box <?= $status ?>">
                <?php if ($status === 'approved'): ?>
                    <h3><i class="fas fa-check-circle"></i> Account Geactiveerd</h3>
                    <p>
                        Het bedrijf is succesvol geverifieerd en kan nu boekingen ontvangen.
                        De eigenaar is per e-mail op de hoogte gesteld.
                    </p>
                <?php else: ?>
                    <h3><i class="fas fa-ban"></i> Registratie Geweigerd</h3>
                    <p>
                        De bedrijfsregistratie is afgewezen.
                        <?php if (!empty($reason)): ?>
                            <br><br><strong>Reden:</strong> <?= htmlspecialchars($reason) ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="email-sent">
                <i class="fas fa-envelope-circle-check"></i>
                <span>Bevestigingsmail verzonden naar <?= htmlspecialchars($business['email']) ?></span>
            </div>

            <a href="/admin/dashboard" class="btn-admin">
                <i class="fas fa-arrow-left"></i> Naar Admin Dashboard
            </a>
        </div>

        <div class="card-footer">
            <p>&copy; 2025 GlamourSchedule Admin</p>
        </div>
    </div>
</body>
</html>
