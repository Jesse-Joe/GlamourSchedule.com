<?php ob_start(); ?>

<style>
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
        background: #000000;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        border: 2px solid #333333;
    }
    .login-header {
        background: #000000;
        color: #ffffff;
        padding: 3rem 2rem;
        text-align: center;
        border-bottom: 2px solid #333333;
        border-radius: 0 0 30px 30px;
    }
    .login-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #ffffff;
    }
    .login-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
    }

    /* Account Type Tabs */
    .account-tabs {
        display: flex;
        background: #000000;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
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
        color: rgba(255, 255, 255, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .account-tab:hover {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.9);
    }
    .account-tab.active {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border-bottom: 3px solid #ffffff;
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
        background: #000000;
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
        color: rgba(255, 255, 255, 0.9);
    }
    .form-group label i {
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
    .form-control:focus {
        outline: none;
        border-bottom-color: #ffffff;
        box-shadow: none;
    }
    .form-control:hover {
        border-bottom-color: rgba(255, 255, 255, 0.7);
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }
    .forgot-link {
        display: block;
        text-align: right;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .forgot-link:hover {
        color: #ffffff;
    }
    .btn-login {
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
        margin-top: 1.5rem;
    }
    .btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255,255,255,0.3);
    }
    .btn-login.business {
        background: #ffffff;
        color: #000000;
    }
    .btn-login.business:hover {
        box-shadow: 0 10px 30px rgba(255,255,255,0.3);
    }
    .login-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        margin-top: 1.5rem;
    }
    .login-footer p {
        margin: 0.5rem 0;
        color: rgba(255, 255, 255, 0.7);
    }
    .login-footer a {
        color: #ffffff;
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
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }
    .alert-success i {
        color: #ffffff;
    }
    .alert-danger {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }
    .alert-danger i {
        color: #ffffff;
    }
    .account-info {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.9);
    }
    .account-info i {
        color: #ffffff;
    }
    .account-info.business {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        color: rgba(255, 255, 255, 0.9);
    }
    .account-info.business i {
        color: #ffffff;
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
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .password-toggle:hover {
        color: #ffffff;
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

    /* Dark Mode - Already dark so minimal changes needed */
    [data-theme="dark"] .login-card {
        background: #000000;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
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
