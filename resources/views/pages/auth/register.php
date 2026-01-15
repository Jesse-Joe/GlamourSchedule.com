<?php ob_start(); ?>

<style>
    .register-container {
        max-width: 520px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .register-container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .register-card {
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        .form-group {
            text-align: left;
        }
        .form-group label {
            justify-content: flex-start;
        }
        .form-control {
            width: 100%;
            max-width: 100%;
            text-align: left;
        }
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        .password-wrapper {
            width: 100%;
        }
    }
    .register-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .register-header {
        background: #ffffff;
        color: #000000;
        padding: 2rem;
        text-align: center;
        border-bottom: 2px solid #000000;
    }
    .register-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #000000;
    }
    .register-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #000000;
    }
    .register-header p {
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
        color: #000000;
    }
    .register-body {
        padding: 2rem;
    }
    .account-type-banner {
        background: #ffffff;
        border: 2px solid #000000;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .account-type-banner i {
        font-size: 1.5rem;
        color: #000000;
    }
    .account-type-banner h4 {
        margin: 0;
        color: #000000;
        font-size: 0.95rem;
    }
    .account-type-banner p {
        margin: 0.25rem 0 0 0;
        font-size: 0.8rem;
        color: #737373;
    }
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
    }
    .form-group label i {
        color: #000000;
    }
    .form-control {
        width: 100%;
        padding: 0.85rem 0;
        background: transparent;
        border: none;
        border-bottom: 2px solid rgba(0, 0, 0, 0.3);
        border-radius: 0;
        font-size: 1rem;
        color: #000000;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-bottom-color: #000000;
        box-shadow: none;
    }
    .form-control::placeholder {
        color: rgba(0, 0, 0, 0.4);
    }
    .btn-register {
        width: 100%;
        padding: 1rem;
        background: #000000;
        color: white;
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
        margin-top: 1.5rem;
    }
    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .register-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid #f5f5f5;
        margin-top: 1.5rem;
    }
    .register-footer p {
        margin: 0.5rem 0;
        color: #6b7280;
    }
    .register-footer a {
        color: #000000;
        text-decoration: none;
        font-weight: 500;
    }
    .register-footer a:hover {
        text-decoration: underline;
    }
    .business-cta {
        background: #f5f5f5;
        border: 2px solid #000000;
        border-radius: 12px;
        padding: 1.25rem;
        margin-top: 1.5rem;
        text-align: center;
    }
    .business-cta i {
        font-size: 1.5rem;
        color: #000000;
        margin-bottom: 0.5rem;
        display: block;
    }
    .business-cta h4 {
        margin: 0;
        color: #000000;
        font-size: 0.95rem;
    }
    .business-cta p {
        margin: 0.25rem 0 0.75rem 0;
        font-size: 0.85rem;
        color: #262626;
    }
    .business-cta a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        background: linear-gradient(135deg, #000000, #262626);
        color: white;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s;
    }
    .business-cta a:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 500px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
    .alert-danger {
        background: #f5f5f5;
        border: 1px solid #e5e5e5;
        color: #000000;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.25rem;
    }

    /* Dark Mode */
    [data-theme="dark"] .register-card {
        background: var(--bg-card);
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .account-type-banner {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
    }
    [data-theme="dark"] .account-type-banner h4 {
        color: #ffffff;
    }
    [data-theme="dark"] .account-type-banner p {
        color: #d4d4d4;
    }
    [data-theme="dark"] .form-group label {
        color: var(--text);
    }
    [data-theme="dark"] .form-control {
        background: transparent;
        border-bottom-color: rgba(255, 255, 255, 0.3);
        color: var(--white);
    }
    [data-theme="dark"] .form-control:focus {
        border-bottom-color: var(--white);
        box-shadow: none;
    }
    [data-theme="dark"] .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }
    [data-theme="dark"] .register-footer {
        border-top-color: var(--border);
    }
    [data-theme="dark"] .register-footer p {
        color: var(--text-light);
    }
    [data-theme="dark"] .business-cta {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
    }
    [data-theme="dark"] .business-cta h4 {
        color: #ffffff;
    }
    [data-theme="dark"] .business-cta p {
        color: #d4d4d4;
    }
    [data-theme="dark"] .alert-danger {
        background: rgba(0, 0, 0, 0.1);
        border-color: rgba(0, 0, 0, 0.1);
        color: #d4d4d4;
    }

    /* Password Toggle */
    .password-wrapper {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(0, 0, 0, 0.5);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-toggle:hover {
        color: #000000;
    }
    .password-wrapper .form-control {
        padding-right: 2rem;
    }
    [data-theme="dark"] .password-toggle {
        color: rgba(255, 255, 255, 0.5);
    }
    [data-theme="dark"] .password-toggle:hover {
        color: var(--white);
    }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h1>Account Aanmaken</h1>
            <p>Registreer je gratis en boek afspraken</p>
        </div>

        <div class="register-body">
            <div class="account-type-banner">
                <i class="fas fa-user-circle"></i>
                <div>
                    <h4>Persoonlijk Account</h4>
                    <p>Voor het boeken van afspraken bij salons</p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/register">
                <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">

                <div class="grid-2">
                    <div class="form-group">
                        <label for="first_name"><i class="fas fa-user"></i> Voornaam</label>
                        <input type="text" id="first_name" name="first_name" class="form-control"
                               placeholder="Je voornaam"
                               value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
                        <?php if (isset($errors['first_name'])): ?>
                            <small style="color:#333333;font-size:0.8rem"><?= $errors['first_name'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="last_name"><i class="fas fa-user"></i> Achternaam</label>
                        <input type="text" id="last_name" name="last_name" class="form-control"
                               placeholder="Je achternaam"
                               value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
                        <?php if (isset($errors['last_name'])): ?>
                            <small style="color:#333333;font-size:0.8rem"><?= $errors['last_name'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mailadres</label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="jouw@email.nl"
                           value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <small style="color:#333333;font-size:0.8rem"><?= $errors['email'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Telefoon (optioneel)</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           placeholder="06-12345678"
                           value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Wachtwoord</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-control"
                               placeholder="Minimaal 8 tekens" minlength="8" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Wachtwoord tonen">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <small style="color:#333333;font-size:0.8rem"><?= $errors['password'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password_confirm"><i class="fas fa-lock"></i> Bevestig wachtwoord</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                               placeholder="Herhaal je wachtwoord" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="Wachtwoord tonen">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password_confirm'])): ?>
                        <small style="color:#333333;font-size:0.8rem"><?= $errors['password_confirm'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:flex-start;gap:0.75rem;cursor:pointer;font-weight:400">
                        <input type="checkbox" name="accept_terms" id="accept_terms" required
                               style="margin-top:0.2rem;width:20px;height:20px;accent-color:#000000"
                               <?= isset($data['accept_terms']) ? 'checked' : '' ?>>
                        <span style="font-size:0.9rem;color:#6b7280">
                            Ik ga akkoord met de
                            <a href="/terms" target="_blank" style="color:#000000">Algemene Voorwaarden</a>
                            en het
                            <a href="/privacy" target="_blank" style="color:#000000">Privacybeleid</a>
                        </span>
                    </label>
                    <?php if (isset($errors['accept_terms'])): ?>
                        <small style="color:#333333;font-size:0.8rem"><?= $errors['accept_terms'] ?></small>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Account Aanmaken
                </button>
            </form>

            <div class="register-footer">
                <p>
                    Al een account? <a href="/login">Inloggen</a>
                </p>
            </div>

            <div class="business-cta">
                <i class="fas fa-store"></i>
                <h4>Heb je een salon of bedrijf?</h4>
                <p>Registreer je bedrijf en ontvang boekingen van klanten</p>
                <a href="/business/register">
                    <i class="fas fa-arrow-right"></i> Bedrijf Registreren
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
