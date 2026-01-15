<?php ob_start(); ?>

<style>
    body {
        background: #ffffff !important;
        margin: 0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    .register-container {
        width: 100%;
        max-width: 480px;
    }
    .register-card {
        background: #f5f5f5;
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 25px 50px rgba(0,0,0,0.4);
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
        font-size: 1.5rem;
        color: #333333;
    }
    .register-header p {
        margin: 0.5rem 0 0;
        color: #999999;
    }
    .discount-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(0, 0, 0, 0.1);
        border: 1px solid #333333;
        color: #000000;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 1rem;
    }
    .partner-info {
        background: rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-top: 1rem;
        color: #333333;
        font-size: 0.9rem;
        text-align: center;
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
        color: #333333;
        font-size: 0.95rem;
    }
    .form-label i {
        color: #333333;
    }
    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        background: #ffffff;
        border: 2px solid rgba(0,0,0,0.1);
        border-radius: 10px;
        font-size: 1rem;
        color: #333333;
        transition: all 0.2s;
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
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 480px) {
        .grid-2 { grid-template-columns: 1fr; }
    }
    .price-box {
        background: #000000;
        border: 2px solid #333333;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        margin: 1.5rem 0;
    }
    .price-box .label {
        color: #cccccc;
        font-size: 0.9rem;
    }
    .price-box .price-row {
        display: flex;
        justify-content: center;
        align-items: baseline;
        gap: 0.75rem;
        margin: 0.5rem 0;
    }
    .price-box .original {
        text-decoration: line-through;
        color: #999999;
        font-size: 1.1rem;
    }
    .price-box .price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
    }
    .price-box .discount {
        background: #333333;
        color: #ffffff;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
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
    .payment-note {
        text-align: center;
        margin-top: 1rem;
        color: #666666;
        font-size: 0.85rem;
    }
    .benefits {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0,0,0,0.1);
    }
    .benefits h4 {
        color: #333333;
        margin: 0 0 1rem;
        font-size: 0.95rem;
    }
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
        color: #999999;
        font-size: 0.9rem;
    }
    .benefit-item i {
        color: #333333;
    }
    .alert-error {
        background: rgba(0, 0, 0, 0.1);
        border: 1px solid #333333;
        color: #d4d4d4;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
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
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="icon">
                <i class="fas fa-store"></i>
            </div>
            <h1>Registreer Je Salon</h1>
            <p>Start vandaag nog met online boekingen</p>

            <?php if (!empty($isSalesEarlyAdopter)): ?>
            <div class="discount-badge" style="background:rgba(16,185,129,0.2);border-color:#000000">
                <i class="fas fa-star"></i>
                Early Adopter: Nog <?= $salesEarlyAdopterSpots ?> plekken voor €0,99!
            </div>
            <?php else: ?>
            <div class="discount-badge">
                <i class="fas fa-tag"></i>
                25,- korting via partner!
            </div>
            <?php endif; ?>

            <?php if (!empty($salesPartner)): ?>
            <div class="partner-info">
                <i class="fas fa-handshake"></i>
                Via: <?= htmlspecialchars($salesPartner['name'] ?? 'GlamourSchedule Partner') ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php foreach ($errors as $error): ?>
                <?= htmlspecialchars($error) ?><br>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/partner/register">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="referral_code" value="<?= htmlspecialchars($referralCode ?? '') ?>">

            <div class="form-group">
                <label class="form-label"><i class="fas fa-store"></i> Salonnaam *</label>
                <input type="text" name="company_name" class="form-control <?= isset($errors['company_name']) ? 'error' : '' ?>"
                       value="<?= htmlspecialchars($data['company_name'] ?? '') ?>"
                       placeholder="Naam van je salon" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Voornaam *</label>
                    <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'error' : '' ?>"
                           value="<?= htmlspecialchars($data['first_name'] ?? '') ?>"
                           placeholder="Je voornaam" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Achternaam *</label>
                    <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'error' : '' ?>"
                           value="<?= htmlspecialchars($data['last_name'] ?? '') ?>"
                           placeholder="Je achternaam" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fas fa-envelope"></i> E-mailadres *</label>
                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                       value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                       placeholder="info@jouwsalon.nl" required>
            </div>

            <div class="price-box" style="background:linear-gradient(135deg,#ecfdf5,#f5f5f5);border:2px solid #000000;">
                <div class="label" style="color:#047857;">Start vandaag nog!</div>
                <div class="price-row">
                    <span class="price" style="color:#047857;">GRATIS</span>
                </div>
                <span class="discount" style="background:#000000;">14 DAGEN PROBEREN</span>
                <div style="color:#000000;font-size:0.85rem;margin-top:0.5rem">Geen betaling vooraf nodig</div>
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
                    <div class="error-text"><?= htmlspecialchars($errors['terms']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#000000,#059669);">
                <i class="fas fa-rocket"></i> Gratis Starten
            </button>

            <p class="payment-note">
                <i class="fas fa-check-circle" style="color:#000000;"></i> Direct toegang, geen creditcard nodig
            </p>

            <div class="benefits">
                <h4><i class="fas fa-check-circle"></i> Dit krijg je:</h4>
                <div class="benefit-item">
                    <i class="fas fa-gift"></i>
                    <span><strong>14 dagen gratis proberen</strong></span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-calendar-check"></i>
                    <span>Online boekingen 24/7</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-bell"></i>
                    <span>Automatische herinneringen</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-credit-card"></i>
                    <span>iDEAL betalingen</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-globe"></i>
                    <span>Eigen salonpagina</span>
                </div>
            </div>
        </form>

        <div class="login-link">
            <p style="margin:0 0 0.5rem 0">Al een account?</p>
            <a href="/login">Inloggen</a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/minimal.php'; ?>
