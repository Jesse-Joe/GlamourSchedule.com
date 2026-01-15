<?php ob_start(); ?>

<style>
    .login-container {
        max-width: 480px;
        margin: 3rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .login-container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .login-card {
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
        .password-wrapper {
            width: 100%;
        }
        .forgot-link {
            text-align: right;
        }
    }
    .login-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .login-header {
        background: #ffffff;
        color: #000000;
        padding: 2rem;
        text-align: center;
        border-bottom: 2px solid #000000;
    }
    .login-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #000000;
    }
    .login-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #000000;
    }

    /* Account Type Tabs */
    .account-tabs {
        display: flex;
        background: #fafafa;
        border-bottom: 1px solid #e5e7eb;
    }
    .account-tab {
        flex: 1;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        background: transparent;
        font-size: 0.95rem;
        font-weight: 500;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .account-tab:hover {
        background: #f5f5f5;
        color: #374151;
    }
    .account-tab.active {
        background: #ffffff;
        color: #000000;
        border-bottom: 3px solid #000000;
        margin-bottom: -1px;
    }
    .account-tab i {
        font-size: 1.1rem;
    }
    .account-tab-label {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .account-tab-label small {
        font-size: 0.7rem;
        font-weight: 400;
        opacity: 0.8;
    }

    .login-body {
        padding: 2rem;
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
        padding: 0.9rem 0;
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
    .forgot-link {
        display: block;
        text-align: right;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #6b7280;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .forgot-link:hover {
        color: #000000;
    }
    .btn-login {
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
    .btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .btn-login.business {
        background: linear-gradient(135deg, #000000, #262626);
    }
    .btn-login.business:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .login-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid #f5f5f5;
        margin-top: 1.5rem;
    }
    .login-footer p {
        margin: 0.5rem 0;
        color: #6b7280;
    }
    .login-footer a {
        color: #000000;
        text-decoration: none;
        font-weight: 500;
    }
    .login-footer a:hover {
        text-decoration: underline;
    }
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .alert-success {
        background: #ffffff;
        border: 1px solid #333333;
        color: #000000;
    }
    .alert-success i {
        color: #333333;
    }
    .alert-danger {
        background: #f5f5f5;
        border: 1px solid #e5e5e5;
        color: #000000;
    }
    .alert-danger i {
        color: #333333;
    }
    .account-info {
        background: #ffffff;
        border: 1px solid #000000;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: #000000;
    }
    .account-info i {
        color: #000000;
    }
    .account-info.business {
        background: #ffffff;
        border-color: #e5e5e5;
        color: #000000;
    }
    .account-info.business i {
        color: #000000;
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

    /* Tab content visibility */
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    /* Dark Mode */
    [data-theme="dark"] .login-card {
        background: var(--bg-card);
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .account-tabs {
        background: var(--bg-secondary);
        border-bottom-color: var(--border);
    }
    [data-theme="dark"] .account-tab {
        color: var(--text-light);
    }
    [data-theme="dark"] .account-tab:hover {
        background: var(--bg-card);
        color: var(--text);
    }
    [data-theme="dark"] .account-tab.active {
        background: var(--bg-card);
        color: var(--primary);
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
    [data-theme="dark"] .forgot-link {
        color: var(--text-light);
    }
    [data-theme="dark"] .login-footer {
        border-top-color: var(--border);
    }
    [data-theme="dark"] .login-footer p {
        color: var(--text-light);
    }
    [data-theme="dark"] .alert-success {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
        color: #6ee7b7;
    }
    [data-theme="dark"] .alert-danger {
        background: rgba(0, 0, 0, 0.1);
        border-color: rgba(0, 0, 0, 0.1);
        color: #d4d4d4;
    }
    [data-theme="dark"] .account-info {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        color: #fde68a;
    }
    [data-theme="dark"] .account-info.business {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }
</style>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-sign-in-alt"></i>
            <h1><?= $translations['login'] ?? 'Inloggen' ?></h1>
        </div>

        <!-- Account Type Tabs -->
        <div class="account-tabs">
            <button type="button" class="account-tab active" onclick="switchTab('personal')" id="tabPersonal">
                <i class="fas fa-user"></i>
                <span class="account-tab-label">
                    Persoonlijk
                    <small>Klant account</small>
                </span>
            </button>
            <button type="button" class="account-tab" onclick="switchTab('business')" id="tabBusiness">
                <i class="fas fa-store"></i>
                <span class="account-tab-label">
                    Bedrijf
                    <small>Salon / Onderneming</small>
                </span>
            </button>
        </div>

        <div class="login-body">
            <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= $translations['password_changed'] ?? 'Je wachtwoord is gewijzigd! Je kunt nu inloggen.' ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- Personal Login Content -->
            <div class="tab-content active" id="contentPersonal">
                <div class="account-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Log in met je persoonlijke account om afspraken te boeken en beheren.</span>
                </div>

                <form method="POST" action="/login" id="formPersonal">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="account_type" value="personal">

                    <div class="form-group">
                        <label for="emailPersonal"><i class="fas fa-envelope"></i> E-mailadres</label>
                        <input type="email" id="emailPersonal" name="email" class="form-control"
                               placeholder="jouw@email.nl"
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <small style="color:#333333;font-size:0.85rem;margin-top:0.25rem;display:block"><?= $errors['email'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="passwordPersonal"><i class="fas fa-lock"></i> Wachtwoord</label>
                        <div class="password-wrapper">
                            <input type="password" id="passwordPersonal" name="password" class="form-control" placeholder="Je wachtwoord" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordPersonal', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <small style="color:#333333;font-size:0.85rem;margin-top:0.25rem;display:block"><?= $errors['password'] ?></small>
                        <?php endif; ?>
                        <a href="/forgot-password" class="forgot-link">
                            <i class="fas fa-key"></i> Wachtwoord vergeten?
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Inloggen
                    </button>
                </form>

                <div class="login-footer">
                    <p>
                        Nog geen account? <a href="/register">Gratis registreren</a>
                    </p>
                </div>
            </div>

            <!-- Business Login Content -->
            <div class="tab-content" id="contentBusiness">
                <div class="account-info business">
                    <i class="fas fa-building"></i>
                    <span>Log in met je bedrijfsaccount om je salon en boekingen te beheren.</span>
                </div>

                <form method="POST" action="/login" id="formBusiness">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="account_type" value="business">

                    <div class="form-group">
                        <label for="emailBusiness"><i class="fas fa-envelope"></i> Bedrijfs E-mail</label>
                        <input type="email" id="emailBusiness" name="email" class="form-control"
                               placeholder="info@jouwbedrijf.nl"
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="passwordBusiness"><i class="fas fa-lock"></i> Wachtwoord</label>
                        <div class="password-wrapper">
                            <input type="password" id="passwordBusiness" name="password" class="form-control" placeholder="Je wachtwoord" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordBusiness', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <a href="/forgot-password" class="forgot-link">
                            <i class="fas fa-key"></i> Wachtwoord vergeten?
                        </a>
                    </div>

                    <button type="submit" class="btn-login business">
                        <i class="fas fa-sign-in-alt"></i> Inloggen als Bedrijf
                    </button>
                </form>

                <div class="login-footer">
                    <p>
                        Nog geen bedrijfsaccount? <a href="/business/register" style="color:#000000">Registreer je bedrijf</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(type) {
        // Update tabs
        document.getElementById('tabPersonal').classList.remove('active');
        document.getElementById('tabBusiness').classList.remove('active');
        document.getElementById('tab' + type.charAt(0).toUpperCase() + type.slice(1)).classList.add('active');

        // Update content
        document.getElementById('contentPersonal').classList.remove('active');
        document.getElementById('contentBusiness').classList.remove('active');
        document.getElementById('content' + type.charAt(0).toUpperCase() + type.slice(1)).classList.add('active');

        // Store preference
        sessionStorage.setItem('loginTab', type);
    }

    // Restore tab preference
    document.addEventListener('DOMContentLoaded', function() {
        const savedTab = sessionStorage.getItem('loginTab');
        if (savedTab) {
            switchTab(savedTab);
        }
    });

    // Password toggle function
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
