<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 6: Dark Violet Elegance - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept6.css">
</head>
<body>
    <div class="bg-orbs"></div>

    <nav class="nav-dark">
        <a href="/" class="logo-dark"><i class="fas fa-gem"></i> Glamour</a>
        <ul class="nav-links-dark">
            <li><a href="/concept6">VI</a></li>
            <li><a href="/concept7">VII</a></li>
            <li><a href="/concept8">VIII</a></li>
            <li><a href="/concept9">IX</a></li>
            <li><a href="/concept10">X</a></li>
        </ul>
        <a href="/register" class="btn-dark"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-dark">
        <div>
            <h1><?= $__('concept_discover_glow') ?></h1>
            <p><?= $__('concept_discover_glow_desc') ?></p>
            <form class="search-dark">
                <input type="text" placeholder="<?= $__('concept_search_salons_placeholder') ?>">
                <button type="submit"><i class="fas fa-search"></i> <?= $__('concept_search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-dark">
        <div class="section-title-dark">
            <span><?= $__('concept_our_services') ?></span>
            <h2><?= $__('concept_premium_treatments') ?></h2>
        </div>
    </section>

    <div class="grid-dark">
        <div class="card-dark">
            <div class="card-icon-dark"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_styling') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-dark">
            <div class="card-icon-dark"><i class="fas fa-hand-sparkles"></i></div>
            <h3><?= $__('concept_nail_art') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-dark">
            <div class="card-icon-dark"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skincare') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
    </div>

    <section class="cta-dark">
        <h2><?= $__('concept_join_platform') ?></h2>
        <p><?= $__('concept_join_platform_desc') ?></p>
        <a href="/business/register" class="btn-dark"><?= $__('concept_get_started') ?></a>
    </section>

    <footer class="footer-dark">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('concept_home') ?></a>
    </footer>
</body>
</html>
