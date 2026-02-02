<?php ob_start(); ?>

<style>
    /* =============================================
       AUTH PAGES - Theme Support
       ============================================= */

    /* Dark Theme (Default) */
    .auth-wrapper,
    [data-theme="dark"] .auth-wrapper {
        --auth-bg: #000000;
        --auth-surface: #111111;
        --auth-border: #333333;
        --auth-text: #ffffff;
        --auth-text-muted: rgba(255, 255, 255, 0.7);
        --auth-text-placeholder: rgba(255, 255, 255, 0.5);
        --auth-input-border: rgba(255, 255, 255, 0.4);
        --auth-input-focus: #ffffff;
        --auth-btn-bg: #ffffff;
        --auth-btn-text: #000000;
        --auth-link: #ffffff;
        --auth-shadow: rgba(0, 0, 0, 0.3);
        --auth-tab-hover: rgba(255, 255, 255, 0.1);
        --auth-alert-bg: rgba(255, 255, 255, 0.1);
        --auth-alert-border: rgba(255, 255, 255, 0.3);
    }

    /* Light Theme */
    [data-theme="light"] .auth-wrapper {
        --auth-bg: #ffffff;
        --auth-surface: #f5f5f5;
        --auth-border: #e0e0e0;
        --auth-text: #000000;
        --auth-text-muted: rgba(0, 0, 0, 0.6);
        --auth-text-placeholder: rgba(0, 0, 0, 0.5);
        --auth-input-border: rgba(0, 0, 0, 0.3);
        --auth-input-focus: #000000;
        --auth-btn-bg: #000000;
        --auth-btn-text: #ffffff;
        --auth-link: #000000;
        --auth-shadow: rgba(0, 0, 0, 0.1);
        --auth-tab-hover: rgba(0, 0, 0, 0.05);
        --auth-alert-bg: rgba(0, 0, 0, 0.05);
        --auth-alert-border: rgba(0, 0, 0, 0.2);
    }

    .login-container {
        max-width: 480px;
        margin: 1rem auto;
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
            border-left: none !important;
            border-right: none !important;
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
        background: var(--auth-bg);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px var(--auth-shadow);
        border: 2px solid var(--auth-border);
        transition: background 0.3s, border-color 0.3s;
    }
    .login-header {
        background: var(--auth-bg);
        color: var(--auth-text);
        padding: 3rem 2rem;
        text-align: center;
        border-bottom: 2px solid var(--auth-border);
        border-radius: 0 0 30px 30px;
    }
    .login-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: var(--auth-text);
    }
    .login-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--auth-text);
    }

    /* Account Type Tabs */
    .account-tabs {
        display: flex;
        background: var(--auth-bg);
        border-bottom: 2px solid var(--auth-input-border);
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
        color: var(--auth-text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .account-tab:hover {
        background: var(--auth-tab-hover);
        color: var(--auth-text);
    }
    .account-tab.active {
        background: var(--auth-tab-hover);
        color: var(--auth-text);
        border-bottom: 3px solid var(--auth-text);
        margin-bottom: -2px;
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
        background: var(--auth-bg);
    }
    .form-group {
        margin-bottom: 1.25rem;
        padding: 0.9rem 0;
    }
    .form-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--auth-text);
    }
    .form-group label i {
        color: var(--auth-text);
        font-size: 0.9rem;
    }
    .form-control {
        width: 100%;
        padding: 0.9rem 0;
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--auth-input-border);
        border-radius: 0;
        font-size: 1rem;
        color: var(--auth-text);
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-bottom-color: var(--auth-input-focus);
        box-shadow: none;
    }
    .form-control:hover {
        border-bottom-color: var(--auth-text-muted);
    }
    .form-control::placeholder {
        color: var(--auth-text-placeholder);
    }
    .forgot-link {
        display: block;
        text-align: right;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: var(--auth-text-muted);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .forgot-link:hover {
        color: var(--auth-link);
    }
    .btn-login {
        width: 100%;
        padding: 1.1rem;
        background: var(--auth-btn-bg);
        color: var(--auth-btn-text);
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
        box-shadow: 0 10px 30px var(--auth-shadow);
    }
    .btn-login.business {
        background: var(--auth-btn-bg);
        color: var(--auth-btn-text);
    }
    .btn-login.business:hover {
        box-shadow: 0 10px 30px var(--auth-shadow);
    }
    .login-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid var(--auth-input-border);
        margin-top: 1.5rem;
    }
    .login-footer p {
        margin: 0.5rem 0;
        color: var(--auth-text-muted);
    }
    .login-footer a {
        color: var(--auth-link);
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
        background: var(--auth-alert-bg);
        border: 1px solid var(--auth-alert-border);
        color: var(--auth-text);
    }
    .alert-success i {
        color: #10b981;
    }
    .alert-danger {
        background: var(--auth-alert-bg);
        border: 1px solid var(--auth-alert-border);
        color: var(--auth-text);
    }
    .alert-danger i {
        color: #ef4444;
    }
    .account-info {
        background: var(--auth-alert-bg);
        border: 2px solid var(--auth-alert-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: var(--auth-text);
    }
    .account-info i {
        color: var(--auth-text);
    }
    .account-info.business {
        background: var(--auth-alert-bg);
        border-color: var(--auth-alert-border);
        color: var(--auth-text);
    }
    .account-info.business i {
        color: var(--auth-text);
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
        color: var(--auth-text-muted);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-toggle:hover {
        color: var(--auth-text);
    }
    .password-wrapper .form-control {
        padding-right: 2rem;
    }

    /* Tab content visibility */
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>

<div class="auth-wrapper">
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
                    <?= $translations['personal'] ?? 'Personal' ?>
                    <small><?= $translations['personal_account'] ?? 'Customer account' ?></small>
                </span>
            </button>
            <button type="button" class="account-tab" onclick="switchTab('business')" id="tabBusiness">
                <i class="fas fa-store"></i>
                <span class="account-tab-label">
                    <?= $translations['business_tab'] ?? 'Business' ?>
                    <small><?= $translations['salon_business'] ?? 'Salon / Business' ?></small>
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
                    <span><?= $translations['login_personal_info'] ?? 'Log in with your personal account to book and manage appointments.' ?></span>
                </div>

                <form method="POST" action="/login" id="formPersonal">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="account_type" value="personal">

                    <div class="form-group">
                        <label for="emailPersonal"><i class="fas fa-envelope"></i> <?= $translations['email'] ?? 'Email address' ?></label>
                        <input type="email" id="emailPersonal" name="email" class="form-control"
                               placeholder="<?= $translations['email_placeholder'] ?? 'your@email.com' ?>"
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <small style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin-top:0.25rem;display:block"><?= $errors['email'] ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="passwordPersonal"><i class="fas fa-lock"></i> <?= $translations['password'] ?? 'Password' ?></label>
                        <div class="password-wrapper">
                            <input type="password" id="passwordPersonal" name="password" class="form-control" placeholder="<?= $translations['your_password'] ?? 'Your password' ?>" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordPersonal', this)" aria-label="<?= $translations['show_password'] ?? 'Show password' ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <small style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin-top:0.25rem;display:block"><?= $errors['password'] ?></small>
                        <?php endif; ?>
                        <a href="/forgot-password" class="forgot-link">
                            <i class="fas fa-key"></i> <?= $translations['forgot_password'] ?? 'Forgot password?' ?>
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> <?= $translations['login'] ?? 'Login' ?>
                    </button>
                </form>

                <div class="login-footer">
                    <p>
                        <?= $translations['no_account_yet'] ?? 'No account yet?' ?> <a href="/register"><?= $translations['register_free'] ?? 'Register for free' ?></a>
                    </p>
                </div>
            </div>

            <!-- Business Login Content -->
            <div class="tab-content" id="contentBusiness">
                <div class="account-info business">
                    <i class="fas fa-building"></i>
                    <span><?= $translations['login_business_info'] ?? 'Log in with your business account to manage your salon and bookings.' ?></span>
                </div>

                <form method="POST" action="/login" id="formBusiness">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="account_type" value="business">

                    <div class="form-group">
                        <label for="emailBusiness"><i class="fas fa-envelope"></i> <?= $translations['business_email'] ?? 'Business Email' ?></label>
                        <input type="email" id="emailBusiness" name="email" class="form-control"
                               placeholder="<?= $translations['business_email_placeholder'] ?? 'info@yourbusiness.com' ?>"
                               value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="passwordBusiness"><i class="fas fa-lock"></i> <?= $translations['password'] ?? 'Password' ?></label>
                        <div class="password-wrapper">
                            <input type="password" id="passwordBusiness" name="password" class="form-control" placeholder="<?= $translations['your_password'] ?? 'Your password' ?>" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordBusiness', this)" aria-label="<?= $translations['show_password'] ?? 'Show password' ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <a href="/forgot-password" class="forgot-link">
                            <i class="fas fa-key"></i> <?= $translations['forgot_password'] ?? 'Forgot password?' ?>
                        </a>
                    </div>

                    <button type="submit" class="btn-login business">
                        <i class="fas fa-sign-in-alt"></i> <?= $translations['login_as_business'] ?? 'Login as Business' ?>
                    </button>
                </form>

                <div class="login-footer">
                    <p>
                        <?= $translations['no_business_account'] ?? 'No business account yet?' ?> <a href="/business/register"><?= $translations['register_business'] ?? 'Register your business' ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</div><!-- End auth-wrapper -->

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

    // Restore tab preference or use server-provided default
    document.addEventListener('DOMContentLoaded', function() {
        const serverTab = '<?= $accountType ?? '' ?>';
        const savedTab = sessionStorage.getItem('loginTab');
        if (serverTab === 'business') {
            switchTab('business');
        } else if (savedTab) {
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
