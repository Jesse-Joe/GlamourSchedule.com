<?php ob_start(); ?>

<style>
    .auth-container {
        max-width: 480px;
        margin: 3rem auto;
        padding: 0 1rem;
    }
    .auth-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .auth-header {
        background: linear-gradient(135deg, #000000 0%, #000000 30%, #000000 70%, #333333 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
    }
    .auth-header.expired {
        background: linear-gradient(135deg, #000000 0%, #404040 100%);
    }
    .auth-header i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }
    .auth-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }
    .auth-header p {
        margin: 0.75rem 0 0;
        opacity: 0.9;
    }
    .auth-body {
        padding: 2.5rem 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
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
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: #000000;
        box-shadow: 0 0 0 4px rgba(0,0,0,0.1);
    }
    .form-hint {
        font-size: 0.85rem;
        color: #9ca3af;
        margin-top: 0.35rem;
    }
    .btn-submit {
        width: 100%;
        padding: 1rem;
        background: #000000;
        color: white;
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
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .btn-secondary {
        width: 100%;
        padding: 1rem;
        background: transparent;
        color: #000000;
        border: 2px solid #000000;
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
        background: #000000;
        color: white;
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
        background: #f5f5f5;
        border: 1px solid #e5e5e5;
        color: #000000;
    }
    .alert-danger i {
        color: #333333;
    }
    .expired-content {
        text-align: center;
    }
    .expired-content p {
        color: #6b7280;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    /* Dark Mode */
    [data-theme="dark"] .auth-card {
        background: var(--bg-card);
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .form-group label {
        color: var(--text);
    }
    [data-theme="dark"] .form-control {
        background: var(--bg-secondary);
        border-color: var(--border);
        color: var(--text);
    }
    [data-theme="dark"] .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(255,255,255,0.15);
    }
    [data-theme="dark"] .form-control::placeholder {
        color: var(--text-light);
    }
    [data-theme="dark"] .form-control:disabled {
        background: var(--bg-secondary);
        color: var(--text-light);
    }
    [data-theme="dark"] .form-hint {
        color: var(--text-light);
    }
    [data-theme="dark"] .alert-danger {
        background: rgba(0, 0, 0, 0.1);
        border-color: rgba(0, 0, 0, 0.1);
        color: #d4d4d4;
    }
    [data-theme="dark"] .btn-secondary {
        color: var(--primary);
        border-color: var(--primary);
    }
    [data-theme="dark"] .btn-secondary:hover {
        background: var(--primary);
        color: white;
    }
    [data-theme="dark"] .expired-content p {
        color: var(--text-light);
    }

    /* Password Toggle */
    .password-wrapper {
        position: relative;
    }
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
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
        padding-right: 44px;
    }
    [data-theme="dark"] .password-toggle {
        color: var(--text-light);
    }
    [data-theme="dark"] .password-toggle:hover {
        color: var(--primary);
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
