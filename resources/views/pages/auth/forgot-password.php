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
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
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
    .back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }
    .back-link:hover {
        color: #ffffff;
    }

    /* Dark Mode - Already dark */
    [data-theme="dark"] .auth-card {
        background: #000000;
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
