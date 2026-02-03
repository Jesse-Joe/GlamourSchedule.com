<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 1: Neon Cyberpunk - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept1.css">
</head>
<body>
    <div class="cyber-bg"></div>

    <nav class="nav-cyber">
        <a href="/" class="logo-cyber">GlamourSchedule</a>
        <ul class="nav-links-cyber">
            <li><a href="/concept1">Concept 1</a></li>
            <li><a href="/concept2">Concept 2</a></li>
            <li><a href="/concept3">Concept 3</a></li>
            <li><a href="/concept4">Concept 4</a></li>
            <li><a href="/concept5">Concept 5</a></li>
        </ul>
        <a href="/register" class="btn-cyber"><?= $__('concept_join_now') ?></a>
    </nav>

    <section class="hero-cyber">
        <div>
            <h1><?= $__('concept_book_your_future') ?></h1>
            <p><?= $__('concept_book_your_future_desc') ?></p>
            <form class="search-cyber">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('concept_scan') ?></button>
            </form>
        </div>
    </section>

    <section class="section-cyber">
        <div class="section-title-cyber">
            <h2><?= $__('concept_categories') ?></h2>
        </div>
        <div class="grid-cyber">
            <div class="card-cyber">
                <h3><i class="fas fa-cut"></i> <?= $__('concept_hair') ?></h3>
                <p><?= $__('concept_hair_desc') ?></p>
            </div>
            <div class="card-cyber">
                <h3><i class="fas fa-hand-sparkles"></i> <?= $__('concept_nails') ?></h3>
                <p><?= $__('concept_nails_desc') ?></p>
            </div>
            <div class="card-cyber">
                <h3><i class="fas fa-spa"></i> <?= $__('concept_skincare') ?></h3>
                <p><?= $__('concept_skincare_desc') ?></p>
            </div>
            <div class="card-cyber">
                <h3><i class="fas fa-hands"></i> <?= $__('concept_massage') ?></h3>
                <p><?= $__('concept_massage_desc') ?></p>
            </div>
        </div>
    </section>

    <footer class="footer-cyber">
        <p>&copy; 2026 GlamourSchedule // <?= $__('concept_all_systems_online') ?></p>
    </footer>
</body>
</html>
