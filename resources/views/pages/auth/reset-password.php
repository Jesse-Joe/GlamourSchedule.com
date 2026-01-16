<?php ob_start(); ?>

<style>
    .auth-container {
        max-width: 480px;
        margin: 1rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .auth-container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .auth-card {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
        }
    }
    .auth-card {
        background: #000000;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        border: 2px solid #333333;
    }
    .auth-header {
        background: #000000;
        color: #ffffff;
        padding: 3rem 2rem;
        text-align: center;
        border-bottom: 2px solid #333333;
        border-radius: 0 0 30px 30px;
    }
    .auth-header.expired {
        background: #000000;
    }
    .auth-header i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
        color: #ffffff;
    }
    .auth-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: #ffffff;
    }
    .auth-header p {
        margin: 0.75rem 0 0;
        color: rgba(255, 255, 255, 0.8);
    }
    .auth-body {
        padding: 2rem;
        background: #000000;
    }
    .form-group {
        margin-bottom: 1.5rem;
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
    .form-control:disabled {
        color: rgba(255, 255, 255, 0.5);
        border-bottom-color: rgba(255, 255, 255, 0.2);
    }
    .form-hint {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 0.35rem;
    }
    .btn-submit {
        width: 100%;
        padding: 1.1rem;
        background: #ffffff;
        color: #000000;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255,255,255,0.3);
    }
    .btn-secondary {
        width: 100%;
        padding: 1rem;
        background: transparent;
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #ffffff;
    }
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .alert-danger {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }
    .alert-danger i {
        color: #ffffff;
    }
    .expired-content {
        text-align: center;
    }
    .expired-content p {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 1.5rem;
        line-height: 1.6;
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

    /* Dark Mode - Already dark */
    [data-theme="dark"] .auth-card {
        background: #000000;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <?php if (isset($error) && $error === 'expired'): ?>
            <div class="auth-header expired">
                <i class="fas fa-clock"></i>
                <h1><?= $__('link_expired') ?></h1>
                <p><?= $__('link_expired_desc') ?></p>
            </div>

            <div class="auth-body">
                <div class="expired-content">
                    <p><?= $__('link_expired_desc') ?></p>
                    <a href="/forgot-password" class="btn-submit">
                        <i class="fas fa-redo"></i> <?= $__('request_new_link') ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="auth-header">
                <i class="fas fa-lock"></i>
                <h1><?= $__('new_password') ?></h1>
                <p><?= $__('choose_strong_password') ?></p>
            </div>

            <div class="auth-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <?php
                            $errorKey = $_GET['error'];
                            if ($errorKey === 'csrf') echo $__('error_csrf');
                            elseif ($errorKey === 'password_short') echo $__('error_password_min');
                            elseif ($errorKey === 'password_mismatch') echo $__('error_password_match');
                            else echo $__('error_generic');
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/reset-password">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> <?= $__('email') ?></label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> <?= $__('new_password') ?></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" placeholder="<?= $__('min_chars', ['count' => 8]) ?>" minlength="8" required autofocus>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-hint"><?= $__('use_strong_password') ?></div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> <?= $__('confirm_password') ?></label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="<?= $__('repeat_password') ?>" minlength="8" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)" aria-label="Wachtwoord tonen">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> <?= $__('password_save') ?>
                    </button>
                </form>

                <a href="/login" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> <?= $__('back_to_login') ?>
                </a>
            </div>
        <?php endif; ?>
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
