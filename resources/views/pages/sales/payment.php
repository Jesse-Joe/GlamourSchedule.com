<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registratie Voltooien - GlamourSchedule Sales</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000000;
            padding: 2rem;
        }
        .payment-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        }
        .steps-indicator {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e5e7eb;
        }
        .step-dot.completed {
            background: #333333;
        }
        .step-dot.active {
            background: #333333;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
        }
        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .payment-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #333333, #000000);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .payment-icon i {
            font-size: 2rem;
            color: white;
        }
        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.5rem;
            color: #1f2937;
        }
        .subtitle {
            color: #6b7280;
            margin: 0;
        }
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #ffffff;
            color: #166534;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 1rem 0 1.5rem;
        }
        .order-summary {
            background: #fafafa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .order-summary h3 {
            margin: 0 0 1rem;
            font-size: 1rem;
            color: #374151;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item.total {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1f2937;
            padding-top: 1rem;
            margin-top: 0.5rem;
            border-top: 2px solid #e5e7eb;
        }
        .benefits-list {
            margin: 0 0 1.5rem;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            color: #374151;
            font-size: 0.95rem;
        }
        .benefit-item i {
            color: #333333;
            width: 20px;
        }
        .btn-pay {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .payment-method {
            background: #f5f5f5;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #6b7280;
        }
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #6b7280;
            font-size: 0.85rem;
        }
        .secure-badge i {
            color: #333333;
        }
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .alert-danger {
            background: #f5f5f5;
            border: 1px solid #e5e5e5;
            color: #000000;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="steps-indicator">
            <div class="step-dot completed"></div>
            <div class="step-dot completed"></div>
            <div class="step-dot active"></div>
        </div>

        <div class="payment-header">
            <div class="payment-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <h1>Laatste Stap!</h1>
            <p class="subtitle">Voltooi je registratie met een eenmalige betaling</p>
            <div class="verified-badge">
                <i class="fas fa-check-circle"></i> E-mail geverifieerd
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="order-summary">
            <h3><i class="fas fa-receipt"></i> Bestelling</h3>
            <div class="order-item">
                <span>Sales Partner Registratie</span>
                <span>&euro;0,99</span>
            </div>
            <div class="order-item total">
                <span>Totaal</span>
                <span>&euro;0,99</span>
            </div>
        </div>

        <div class="benefits-list">
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                <span><strong>&euro;99,99</strong> commissie per aanmelding</span>
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                <span>Eigen unieke referral code</span>
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                <span>Realtime statistieken dashboard</span>
            </div>
            <div class="benefit-item">
                <i class="fas fa-check"></i>
                <span>Maandelijkse uitbetalingen</span>
            </div>
        </div>

        <form method="POST" action="/sales/payment" id="paymentForm">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <button type="submit" class="btn-pay" id="payBtn">
                <i class="fas fa-lock" id="payIcon"></i>
                <span id="payText">Betaal &euro;0,99 met iDEAL</span>
            </button>
        </form>

        <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('payBtn');
            var icon = document.getElementById('payIcon');
            var text = document.getElementById('payText');

            // Prevent double submit
            if (btn.disabled) {
                e.preventDefault();
                return false;
            }

            btn.disabled = true;
            btn.style.opacity = '0.7';
            icon.className = 'fas fa-spinner fa-spin';
            text.textContent = 'Bezig met doorsturen naar iDEAL...';
        });
        </script>

        <div class="payment-methods">
            <span class="payment-method"><i class="fas fa-university"></i> iDEAL</span>
            <span class="payment-method"><i class="fab fa-cc-visa"></i> Visa</span>
            <span class="payment-method"><i class="fab fa-cc-mastercard"></i> Mastercard</span>
        </div>

        <div class="secure-badge">
            <i class="fas fa-shield-alt"></i>
            Veilig betalen via Mollie
        </div>
    </div>
</body>
</html>
