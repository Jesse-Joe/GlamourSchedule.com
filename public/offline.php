<?php
// Load translations for offline page with English fallback
$lang = $_COOKIE['lang'] ?? $_GET['lang'] ?? 'en';
$langFile = __DIR__ . '/../resources/lang/' . $lang . '/messages.php';
$enFile = __DIR__ . '/../resources/lang/en/messages.php';

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
    <title><?= $__('offline_title') ?> - GlamourSchedule</title>
    <link rel="icon" type="image/svg+xml" href="/images/gs-logo-circle.svg">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #000000;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }
        .container {
            max-width: 400px;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        p {
            color: rgba(255,255,255,0.7);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: #ffffff;
            color: #000000;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255,255,255,0.1);
        }
        .status {
            margin-top: 2rem;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.5);
        }
        .status.online {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📶</div>
        <h1><?= $__('offline_title') ?></h1>
        <p><?= $__('offline_message') ?></p>
        <button class="btn" onclick="location.reload()">
            <span>🔄</span> <?= $__('offline_retry') ?>
        </button>
        <div class="status" id="status">
            <?= $__('offline_waiting') ?>
        </div>
    </div>

    <script>
        // Translation strings for JavaScript
        const translations = {
            waiting: <?= json_encode($__('offline_waiting')) ?>,
            connected: <?= json_encode($__('offline_connected')) ?>,
            goHome: <?= json_encode($__('offline_go_home')) ?>
        };

        // Check connection status
        function updateStatus() {
            const status = document.getElementById('status');
            if (navigator.onLine) {
                status.className = 'status online';
                status.innerHTML = '✓ ' + translations.connected + ' <a href="/" style="color:#22c55e">' + translations.goHome + '</a>';
            } else {
                status.className = 'status';
                status.textContent = translations.waiting;
            }
        }

        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        updateStatus();
    </script>
</body>
</html>
