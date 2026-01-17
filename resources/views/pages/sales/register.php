<?php ob_start(); ?>
<style>
        .register-card {
            background: #000000;
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            border: 2px solid #333333;
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }
        .register-header .icon {
            width: 80px;
            height: 80px;
            background: #ffffff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .register-header .icon i {
            font-size: 2.5rem;
            color: #000000;
        }
        .register-header h1 {
            margin: 0;
            font-size: 1.75rem;
            color: #ffffff;
            font-weight: 700;
        }
        .register-header p {
            margin: 0.5rem 0 0 0;
            color: rgba(255, 255, 255, 0.7);
        }
        .register-header strong {
            color: #ffffff;
        }

        /* Section Header */
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 0;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1.5rem;
        }
        .section-header i {
            width: 40px;
            height: 40px;
            background: #ffffff;
            color: #000000;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .section-header h4 {
            margin: 0;
            color: #ffffff;
            font-size: 1.1rem;
        }

        .benefits {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .benefits h4 {
            margin: 0 0 1rem 0;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        .benefit-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }
        .benefit-item i {
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }
        .form-label i {
            color: #ffffff;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            padding: 0.9rem 0;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 0;
            font-size: 1rem;
            color: #ffffff;
            transition: all 0.3s ease;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        .form-control:focus {
            outline: none;
            border-bottom-color: #ffffff;
            box-shadow: none;
        }
        .form-control:hover {
            border-bottom-color: rgba(255, 255, 255, 0.7);
        }
        .form-control.error {
            border-bottom-color: #ef4444;
        }
        .error-text {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 480px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
            .benefits-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Pricing Card */
        .pricing-card {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            position: relative;
            overflow: hidden;
        }
        .pricing-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #ffffff;
        }
        .early-bird-badge {
            background: #fbbf24;
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .price-display {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .price-amount {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff;
        }
        .price-period {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        .price-note {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .price-note i {
            color: #ffffff;
        }
        .price-info-box {
            margin: 1rem 0;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        /* Commission Info */
        .commission-box {
            background: rgba(59, 130, 246, 0.1);
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .commission-box .amount {
            font-size: 2rem;
            font-weight: 800;
            color: #3b82f6;
            margin-bottom: 0.25rem;
        }
        .commission-box .label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .btn-primary {
            width: 100%;
            padding: 1.1rem;
            background: #ffffff;
            color: #000000;
            border: none;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        /* Terms checkbox */
        .terms-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
        }
        .terms-box label {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            margin: 0;
        }
        .terms-box span {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }
        .terms-box a {
            color: #ffffff;
            font-weight: 500;
        }
        .terms-box input[type="checkbox"] {
            width: 22px;
            height: 22px;
            accent-color: #ffffff;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-link p {
            margin: 0 0 0.5rem 0;
            color: rgba(255, 255, 255, 0.7);
        }
        .login-link a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }

        .payment-methods {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
        }
        .payment-methods i {
            color: rgba(255, 255, 255, 0.8);
        }
    .sales-register-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6rem 1rem 3rem;
    }
</style>

<div class="sales-register-wrapper">
    <div class="register-card">
        <div class="register-header">
            <div class="icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h1>Word Sales Partner</h1>
            <p>Verdien commissie per aangemeld bedrijf</p>
        </div>

        <!-- Commission Info -->
        <div class="commission-box">
            <div class="amount">&euro;99,99</div>
            <div class="label">commissie per geregistreerd bedrijf</div>
        </div>

        <div class="benefits">
            <h4><i class="fas fa-gift"></i> Wat krijg je?</h4>
            <div class="benefits-grid">
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Unieke referral code</div>
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Welkomstkorting voor bedrijven</div>
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Realtime dashboard</div>
                <div class="benefit-item"><i class="fas fa-check-circle"></i> Maandelijkse uitbetalingen</div>
            </div>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/sales/register">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div class="section-header">
                <i class="fas fa-user"></i>
                <h4>Jouw Gegevens</h4>
            </div>

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

            <!-- Pricing Card -->
            <div class="pricing-card">
                <div class="price-display">
                    <span class="price-amount">&euro;0,99</span>
                    <span class="price-period">eenmalig</span>
                </div>
                <div class="price-note">
                    <i class="fas fa-check-circle"></i>
                    <span>Eenmalige registratiekosten, geen abonnement</span>
                </div>
            </div>

            <div class="terms-box">
                <label>
                    <input type="checkbox" name="terms" id="terms" required
                           <?= isset($data['terms']) && $data['terms'] ? 'checked' : '' ?>>
                    <span>
                        Ik ga akkoord met de
                        <a href="/terms" target="_blank">Algemene Voorwaarden</a>
                        en het
                        <a href="/privacy" target="_blank">Privacybeleid</a> *
                    </span>
                </label>
                <?php if (isset($errors['terms'])): ?>
                    <p class="error-text"><?= htmlspecialchars($errors['terms']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-rocket"></i> Partner Worden
            </button>

            <div class="payment-methods">
                <i class="fas fa-shield-alt"></i>
                <span>Veilig betalen via iDEAL</span>
            </div>
        </form>

        <div class="login-link">
            <p>Al een account?</p>
            <a href="/sales/login">Inloggen</a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
