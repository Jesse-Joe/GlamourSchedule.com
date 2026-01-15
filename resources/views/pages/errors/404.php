<?php ob_start(); ?>

<div class="container" style="max-width:600px;text-align:center;padding:4rem 1rem">
    <div style="width:120px;height:120px;background:linear-gradient(135deg,#000000,#000000);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 2rem">
        <i class="fas fa-exclamation" style="font-size:3rem;color:white"></i>
    </div>
    <h1 style="font-size:4rem;margin:0;background:linear-gradient(135deg,#000000,#000000);-webkit-background-clip:text;-webkit-text-fill-color:transparent">404</h1>
    <h2 style="color:var(--text);margin:1rem 0"><?= $__('page_not_found') ?? 'Page not found' ?></h2>
    <p style="color:var(--text-light);margin-bottom:2rem"><?= $__('page_not_found_desc') ?? 'The page you are looking for does not exist or has been moved.' ?></p>
    <a href="/" class="btn btn-primary" style="padding:1rem 2rem">
        <i class="fas fa-home"></i> <?= $__('to_home') ?? 'Back to home' ?>
    </a>
</div>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
