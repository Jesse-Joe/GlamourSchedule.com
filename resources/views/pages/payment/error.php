<?php ob_start(); ?>

<div class="container" style="max-width:600px">
    <div class="card text-center">
        <div style="width:100px;height:100px;background:var(--danger);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
            <i class="fas fa-exclamation-triangle" style="font-size:3rem;color:white"></i>
        </div>
        <h1 style="color:var(--danger)"><?= $__('payment_error_title') ?></h1>
        <p style="color:var(--text-light);font-size:1.1rem;margin-bottom:2rem">
            <?= htmlspecialchars($error ?? $__('unknown_error')) ?>
        </p>
    </div>

    <div style="text-align:center;margin-top:2rem">
        <a href="javascript:history.back()" class="btn">
            <i class="fas fa-arrow-left"></i> <?= $__('go_back') ?>
        </a>
        <a href="/" class="btn btn-secondary" style="margin-left:1rem">
            <i class="fas fa-home"></i> <?= $__('to_home') ?>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
