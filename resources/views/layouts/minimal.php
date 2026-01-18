<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'GlamourSchedule') ?></title>

    <!-- Early Theme Detection (prevents flash of wrong theme) -->
    <script>
    (function() {
        var saved = localStorage.getItem('glamour_theme_mode');
        var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/prestige.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/mobile-friendly.css?v=<?= time() ?>">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            background: var(--theme-bg, #000000);
            color: var(--theme-text, #ffffff);
        }
    </style>
</head>
<body>
    <?= $content ?? '' ?>

    <!-- Theme Manager -->
    <script src="/js/theme.js?v=<?= time() ?>"></script>
</body>
</html>
