<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 12: Pearl White - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept12.css">
</head>
<body>
    <nav class="nav-pearl">
        <a href="/" class="logo-pearl">Glamour</a>
        <ul class="nav-links-pearl">
            <li><a href="/concept11">XI</a></li>
            <li><a href="/concept12">XII</a></li>
            <li><a href="/concept13">XIII</a></li>
            <li><a href="/concept14">XIV</a></li>
            <li><a href="/concept15">XV</a></li>
        </ul>
        <a href="/register" class="btn-pearl"><?= $__('concept_register') ?></a>
    </nav>

    <section class="hero-pearl">
        <div>
            <h1>Discover Your<br><em>Perfect Beauty</em></h1>
            <div class="hero-divider-pearl"></div>
            <p>Experience the finest beauty treatments from passionate professionals dedicated to making you shine.</p>
            <form class="search-pearl">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-pearl">
        <div class="section-title-pearl">
            <span><?= $__('concept_our_services') ?></span>
            <h2><?= $__('concept_premium_treatments') ?></h2>
        </div>
    </section>

    <div class="grid-pearl">
        <div class="card-pearl">
            <div class="card-icon-pearl"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_styling') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-pearl">
            <div class="card-icon-pearl"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nail_care') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-pearl">
            <div class="card-icon-pearl"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skincare') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
    </div>

    <section class="cta-pearl">
        <h2><?= $__('concept_join_platform') ?></h2>
        <p><?= $__('concept_join_platform_desc') ?></p>
        <a href="/business/register" class="btn-pearl"><?= $__('concept_register_salon') ?></a>
    </section>

    <footer class="footer-pearl">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
