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

    .register-container {
        max-width: 720px;
        margin: 1rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .register-container {
            max-width: 100%;
            padding: 0 20px;
            margin: 0;
        }
        .register-header {
            padding: 1.5rem 1.25rem;
        }
        .register-header i {
            font-size: 2rem;
            margin-bottom: 0.25rem;
        }
        .register-header h1 {
            font-size: 1.25rem;
        }
        .register-header p {
            font-size: 0.85rem;
        }
        .register-body {
            padding: 1.25rem 1rem;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1rem;
        }
        .form-group label {
            justify-content: flex-start;
            font-size: 0.9rem;
        }
        .form-control {
            width: 100%;
            max-width: 100%;
            text-align: left;
            font-size: 1rem;
            padding: 0.75rem 0;
        }
        .grid-2 {
            grid-template-columns: 1fr !important;
            gap: 0 !important;
        }
        .password-wrapper {
            width: 100%;
        }
        .section-header {
            margin-bottom: 1rem;
            padding: 0.75rem 0;
        }
        .account-info {
            font-size: 0.8rem;
            padding: 0.6rem 0.75rem;
            margin-bottom: 1rem;
        }
        .terms-box {
            padding: 0.75rem 1rem;
            margin-top: 1rem;
        }
        .terms-box span {
            font-size: 0.82rem;
        }
        .btn-register {
            margin-top: 1.25rem;
            padding: 1rem;
            font-size: 1rem;
            border-radius: 12px;
            width: 100%;
            box-sizing: border-box;
        }
        .register-footer {
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .register-footer p {
            font-size: 0.9rem;
        }
        .benefits-list {
            grid-template-columns: 1fr !important;
        }
    }
    .register-card {
        background: var(--auth-bg);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px var(--auth-shadow);
        border: 2px solid var(--auth-border);
        transition: background 0.3s, border-color 0.3s;
    }
    .register-header {
        background: var(--auth-bg);
        color: var(--auth-text);
        padding: 3rem 2rem;
        text-align: center;
        border-bottom: 2px solid var(--auth-border);
        border-radius: 0 0 30px 30px;
    }
    .register-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: var(--auth-text);
    }
    .register-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--auth-text);
    }
    .register-header p {
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
        color: var(--auth-text-muted);
    }

    .register-body {
        padding: 2rem;
        background: var(--auth-bg);
    }

    /* Grid Layout */
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 0;
        border-bottom: 2px solid var(--auth-input-border);
        margin-bottom: 1.5rem;
    }
    .section-header i {
        width: 40px;
        height: 40px;
        background: var(--auth-btn-bg);
        color: var(--auth-btn-text);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .section-header h4 {
        margin: 0;
        color: var(--auth-text);
        font-size: 1.1rem;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 1.25rem;
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
    select.form-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
        background-size: 1rem;
        padding-right: 1.5rem;
        cursor: pointer;
    }
    select.form-control option {
        background: var(--auth-bg);
        color: var(--auth-text);
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .btn-register {
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
    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px var(--auth-shadow);
    }

    .register-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid var(--auth-input-border);
        margin-top: 1.5rem;
    }
    .register-footer p {
        margin: 0.5rem 0;
        color: var(--auth-text-muted);
    }
    .register-footer a {
        color: var(--auth-link);
        text-decoration: none;
        font-weight: 500;
    }
    .register-footer a:hover {
        text-decoration: underline;
    }

    .alert-danger {
        background: var(--auth-alert-bg);
        border: 1px solid var(--auth-alert-border);
        color: var(--auth-text);
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
    }

    .account-info {
        background: var(--auth-alert-bg);
        border: 2px solid var(--auth-alert-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.85rem;
        color: var(--auth-text);
    }
    .account-info i {
        font-size: 1.25rem;
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

    /* Terms checkbox */
    .terms-box {
        background: var(--auth-alert-bg);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-top: 1.5rem;
    }
    .terms-box label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
    }
    .terms-box span {
        color: var(--auth-text);
        font-size: 0.9rem;
    }
    .terms-box a {
        color: var(--auth-link);
        font-weight: 500;
    }
    .terms-box input[type="checkbox"] {
        width: 22px;
        height: 22px;
        accent-color: var(--auth-text);
        flex-shrink: 0;
        margin-top: 2px;
    }


</style>

<div class="auth-wrapper">
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h1><?= $translations['create_account'] ?? 'Create Account' ?></h1>
            <p><?= $translations['register_free_subtitle_customer'] ?? 'Register for free as customer' ?></p>
        </div>


        <div class="register-body">
            <?php if (isset($error)): ?>
                <div class="alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Personal Registration -->
                <div class="account-info">
                    <i class="fas fa-info-circle"></i>
                    <span><?= $translations['create_personal_account'] ?? 'Create a personal account to book appointments and save your favorites.' ?></span>
                </div>

                <form method="POST" action="/register" id="formPersonal">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="account_type" value="personal">
                    <!-- Honeypot - hidden from humans, bots will fill this -->
                    <div style="position:absolute;left:-9999px;top:-9999px;opacity:0;pointer-events:none" aria-hidden="true">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> <?= $translations['first_name'] ?? 'First name' ?> *</label>
                            <input type="text" name="first_name" class="form-control" placeholder="<?= $translations['first_name_placeholder'] ?? 'Your first name' ?>" value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> <?= $translations['last_name'] ?? 'Last name' ?> *</label>
                            <input type="text" name="last_name" class="form-control" placeholder="<?= $translations['last_name_placeholder'] ?? 'Your last name' ?>" value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> <?= $translations['email'] ?? 'Email address' ?> *</label>
                        <input type="email" name="email" class="form-control" placeholder="<?= $translations['email_placeholder'] ?? 'your@email.com' ?>" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> <?= $translations['phone'] ?? 'Phone' ?> <span style="font-weight:400;color:rgba(255,255,255,0.5)">(<?= $translations['optional'] ?? 'optional' ?>)</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="<?= $translations['phone_placeholder'] ?? '06-12345678' ?>" value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> <?= $translations['password'] ?? 'Password' ?> *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password" id="passwordPersonal" class="form-control" placeholder="<?= $translations['min_8_chars'] ?? 'Minimum 8 characters' ?>" minlength="8" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordPersonal', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> <?= $translations['confirm_password'] ?? 'Confirm password' ?> *</label>
                            <div class="password-wrapper">
                                <input type="password" name="password_confirm" id="passwordConfirmPersonal" class="form-control" placeholder="<?= $translations['repeat_password'] ?? 'Repeat password' ?>" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('passwordConfirmPersonal', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="terms-box">
                        <label>
                            <input type="checkbox" name="accept_terms" required>
                            <span><?= $translations['agree_terms'] ?? 'I agree to the' ?> <a href="/terms" target="_blank"><?= $translations['terms_conditions'] ?? 'Terms and Conditions' ?></a> <?= $translations['and_the'] ?? 'and the' ?> <a href="/privacy" target="_blank"><?= $translations['privacy_policy'] ?? 'Privacy Policy' ?></a> *</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i> <?= $translations['create_account'] ?? 'Create Account' ?>
                    </button>
                </form>

                <div class="register-footer">
                    <p><?= $translations['already_account'] ?? 'Already have an account?' ?> <a href="/login"><?= $translations['login'] ?? 'Login' ?></a></p>
                </div>

        </div>
    </div>
</div>
</div><!-- End auth-wrapper -->

<script>
// Redirect business registration to dedicated page
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('type') === 'business') {
        window.location.href = '/business/register';
    }
});

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
