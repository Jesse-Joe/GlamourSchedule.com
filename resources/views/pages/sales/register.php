<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word Sales Partner - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
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
        .register-card {
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            border: 1px solid rgba(0,0,0,0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #333333, #000000);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .register-header .icon i {
            font-size: 2.5rem;
            color: #ffffff;
        }
        .register-header h1 {
            margin: 0;
            font-size: 1.75rem;
            color: #333333;
        }
        .register-header p {
            margin: 0.5rem 0 0 0;
            color: #999999;
        }
        .register-header strong {
            color: #000000;
        }
        .benefits {
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
        }
        .benefits h4 {
            margin: 0 0 0.75rem 0;
            color: #333333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .benefits ul {
            margin: 0;
            padding-left: 1.5rem;
            color: #333333;
        }
        .benefits li {
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333333;
            font-size: 0.95rem;
        }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            background: #ffffff;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 10px;
            font-size: 1rem;
            color: #333333;
            transition: border-color 0.2s;
        }
        .form-control::placeholder {
            color: #666666;
        }
        .form-control:focus {
            outline: none;
            border-color: #333333;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }
        .form-control.error {
            border-color: #333333;
        }
        .error-text {
            color: #d4d4d4;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .alert-error {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid #333333;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 480px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
        .price-box {
            background: #000000;
            border: 2px solid #333333;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .price-box .label {
            color: #cccccc;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .price-box .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ffffff;
        }
        .price-box .note {
            color: #cccccc;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .btn-primary {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #333333, #000000);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0,0,0,0.1);
            color: #999999;
        }
        .login-link a {
            color: #333333;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            color: #000000;
        }
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
            opacity: 0.7;
        }
        .payment-methods img {
            height: 24px;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <div class="icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h1>Word Sales Partner</h1>
            <p>Verdien <strong>49,99</strong> per aanmelding</p>
        </div>

        <div class="benefits">
            <h4><i class="fas fa-gift"></i> Wat krijg je?</h4>
            <ul>
                <li><strong>49,99</strong> commissie per geregistreerd bedrijf</li>
                <li>Geef bedrijven 25,- welkomstkorting</li>
                <li>Unieke referral code</li>
                <li>Realtime dashboard met statistieken</li>
                <li>Maandelijkse uitbetalingen</li>
            </ul>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/sales/register">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Voornaam *</label>
                    <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'error' : '' ?>"
                           value="<?= htmlspecialchars($data['first_name'] ?? '') ?>"
                           placeholder="Je voornaam" required>
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="error-text"><?= htmlspecialchars($errors['first_name']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Achternaam *</label>
                    <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'error' : '' ?>"
                           value="<?= htmlspecialchars($data['last_name'] ?? '') ?>"
                           placeholder="Je achternaam" required>
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="error-text"><?= htmlspecialchars($errors['last_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-envelope"></i> E-mailadres *</label>
                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                       value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                       placeholder="jouw@email.nl" required>
                <?php if (isset($errors['email'])): ?>
                    <p class="error-text"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="price-box">
                <div class="label">Eenmalige registratiekosten</div>
                <div class="price">0,99</div>
                <div class="note">Betaal veilig via iDEAL</div>
            </div>

            <div class="form-group" style="margin-bottom:1.5rem">
                <label style="display:flex;align-items:flex-start;gap:0.75rem;cursor:pointer;color:#999999;font-weight:400">
                    <input type="checkbox" name="terms" id="terms" required
                           style="margin-top:0.2rem;width:20px;height:20px;accent-color:#333333;flex-shrink:0"
                           <?= isset($data['terms']) && $data['terms'] ? 'checked' : '' ?>>
                    <span style="font-size:0.9rem;line-height:1.5">
                        Ik ga akkoord met de
                        <a href="/terms" target="_blank" style="color:#333333">Algemene Voorwaarden</a>
                        en het
                        <a href="/privacy" target="_blank" style="color:#333333">Privacybeleid</a> *
                    </span>
                </label>
                <?php if (isset($errors['terms'])): ?>
                    <p class="error-text"><?= htmlspecialchars($errors['terms']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-lock"></i> Betalen en Registreren
            </button>

            <div class="payment-methods">
                <span style="color:#666666;font-size:0.8rem">Betaal veilig met</span>
                <i class="fas fa-credit-card" style="color:#333333"></i>
                <span style="color:#666666;font-size:0.8rem">iDEAL</span>
            </div>
        </form>

        <div class="login-link">
            <p style="margin:0 0 0.5rem 0">Al een account?</p>
            <a href="/sales/login">Log in</a>
        </div>
    </div>
</body>
</html>
