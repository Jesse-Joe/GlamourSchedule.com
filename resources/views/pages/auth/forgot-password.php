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
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
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
    .back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        color: #6b7280;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }
    .back-link:hover {
        color: #000000;
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
    [data-theme="dark"] .back-link {
        color: var(--text-light);
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-key"></i>
            <h1><?= $__('forgot_password') ?></h1>
            <p><?= $__('password_reset_sent') ?></p>
        </div>

        <div class="auth-body">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong><?= $__('email_sent') ?></strong><br>
                        <?= $__('password_reset_sent') ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <?php
                        $errorKey = $_GET['error'];
                        if ($errorKey === 'csrf') echo $__('error_csrf');
                        elseif ($errorKey === 'invalid_email') echo $__('error_email');
                        elseif ($errorKey === 'rate_limit') echo $__('error_rate_limit');
                        elseif ($errorKey === 'invalid_token') echo $__('error_expired');
                        else echo $__('error_generic');
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!isset($_GET['success'])): ?>
                <form method="POST" action="/forgot-password">
                    <input type="hidden" name="csrf_token" value="<?= $this->csrf() ?>">

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> <?= $__('email') ?></label>
                        <input type="email" name="email" class="form-control" placeholder="jouw@email.nl" required autofocus>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> <?= $__('send_reset_link') ?>
                    </button>
                </form>
            <?php endif; ?>

            <a href="/login" class="back-link">
                <i class="fas fa-arrow-left"></i> <?= $__('back_to_login') ?>
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
