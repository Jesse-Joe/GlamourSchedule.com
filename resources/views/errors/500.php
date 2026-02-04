<?php
// Load translations for error page with English fallback
$lang = $_COOKIE['lang'] ?? $_GET['lang'] ?? 'en';
$langFile = __DIR__ . '/../../lang/' . $lang . '/messages.php';
$enFile = __DIR__ . '/../../lang/en/messages.php';

// Always load English as fallback
$enTranslations = file_exists($enFile) ? include $enFile : [];

// Load selected language (or use English if file doesn't exist)
if (file_exists($langFile) && $lang !== 'en') {
    $langTranslations = include $langFile;
    $translations = array_merge($enTranslations, $langTranslations);
} else {
    $translations = $enTranslations;
}

$__ = function($key) use ($translations) {
    return $translations[$key] ?? $key;
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $__('error_500_title') ?> - GlamourSchedule</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fafafa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f2937;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 2rem;
            letter-spacing: -0.5px;
        }
        .error-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #fafafa, #f5f5f5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 2px solid #e5e7eb;
        }
        .error-icon i {
            font-size: 2rem;
            color: #dc2626;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #000;
        }
        p {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #000;
            color: #fff;
            padding: 0.875rem 1.75rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-secondary {
            background: #f5f5f5;
            color: #000;
            margin-left: 0.5rem;
        }
        .btn-secondary:hover {
            background: #e5e5e5;
        }
        .help-text {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #9ca3af;
        }
        .help-text a {
            color: #000;
            text-decoration: none;
        }
        .help-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">GlamourSchedule</div>
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1><?= $__('error_500_title') ?></h1>
            <p><?= $__('error_500_message') ?></p>
            <div>
                <a href="/" class="btn">
                    <i class="fas fa-home"></i> <?= $__('error_go_home') ?>
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> <?= $__('back') ?>
                </a>
            </div>
            <p class="help-text">
                <?= $__('error_problem_persists') ?> <a href="/contact"><?= $__('error_contact_us') ?></a>
            </p>
        </div>
    </div>
</body>
</html>
