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
            max-width: 500px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .card-header {
            background: #000000;
            color: #ffffff;
            padding: 40px;
            text-align: center;
        }
        .card-header .icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }
        .card-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .card-header p {
            opacity: 0.8;
            font-size: 14px;
        }
        .card-body {
            padding: 30px;
        }
        .business-info {
            background: #f9fafb;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .business-name {
            font-size: 22px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 15px;
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
            color: #111827;
            font-weight: 600;
            font-size: 14px;
        }
        .info-value.warning {
            color: #dc2626;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            background: #fef3c7;
            color: #92400e;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-approve {
            background: #000000;
            color: #ffffff;
        }
        .btn-reject {
            background: #ffffff;
            color: #000000;
            border: 2px solid #000000;
        }
        .reject-form {
            display: <?= ($action === 'reject') ? 'block' : 'none' ?>;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #e5e7eb;
        }
        .reject-form h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #111827;
        }
        .reject-form textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            resize: vertical;
            min-height: 120px;
            margin-bottom: 15px;
        }
        .reject-form textarea:focus {
            outline: none;
            border-color: #000000;
        }
        .btn-submit-reject {
            width: 100%;
            padding: 16px;
            background: #dc2626;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .error-msg {
            background: #fef2f2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .card-footer {
            background: #000000;
            padding: 20px;
            text-align: center;
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
            <div class="icon">🏢</div>
            <h1>Bedrijf Verifiëren</h1>
            <p>Beoordeel deze bedrijfsregistratie</p>
        </div>

        <div class="card-body">
            <?php if (isset($_GET['error']) && $_GET['error'] === 'reason_required'): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i> Vul een reden in voor de afwijzing.
                </div>
            <?php endif; ?>

            <div class="business-info">
                <div class="business-name"><?= htmlspecialchars($business['name']) ?></div>
                <div class="info-row">
                    <span class="info-label">E-mail</span>
                    <span class="info-value"><?= htmlspecialchars($business['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">KVK-nummer</span>
                    <span class="info-value <?= empty($business['kvk_number']) ? 'warning' : '' ?>">
                        <?= !empty($business['kvk_number']) ? htmlspecialchars($business['kvk_number']) : 'Niet ingevuld' ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adres</span>
                    <span class="info-value"><?= htmlspecialchars($business['address'] ?? '-') ?>, <?= htmlspecialchars($business['city'] ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="status-badge">Wacht op verificatie</span>
                </div>
            </div>

            <?php if ($action !== 'reject'): ?>
                <div class="action-buttons">
                    <form method="POST" action="/admin/verify-business/<?= htmlspecialchars($token) ?>" style="flex:1">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-approve" style="width:100%">
                            <i class="fas fa-check"></i> Accepteren
                        </button>
                    </form>
                    <a href="/admin/verify-business/<?= htmlspecialchars($token) ?>?action=reject" class="btn btn-reject">
                        <i class="fas fa-times"></i> Weigeren
                    </a>
                </div>
            <?php endif; ?>

            <div class="reject-form" id="rejectForm">
                <h3><i class="fas fa-ban"></i> Registratie Afwijzen</h3>
                <form method="POST" action="/admin/verify-business/<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="action" value="reject">
                    <textarea name="reason" placeholder="Geef een reden voor de afwijzing. Deze wordt naar de klant gestuurd..." required></textarea>
                    <button type="submit" class="btn-submit-reject">
                        <i class="fas fa-paper-plane"></i> Afwijzen & Klant Informeren
                    </button>
                </form>
                <div style="text-align:center;margin-top:15px">
                    <a href="/admin/verify-business/<?= htmlspecialchars($token) ?>" style="color:#6b7280;text-decoration:none;font-size:14px">
                        <i class="fas fa-arrow-left"></i> Terug
                    </a>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <p>&copy; 2025 GlamourSchedule Admin</p>
        </div>
    </div>
</body>
</html>
