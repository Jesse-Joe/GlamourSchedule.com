<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 13: Snow White - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept13.css">
</head>
<body>
    <nav class="nav-snow">
        <a href="/" class="logo-snow">Glamour</a>
        <ul class="nav-links-snow">
            <li><a href="/concept11">XI</a></li>
            <li><a href="/concept12">XII</a></li>
            <li><a href="/concept13">XIII</a></li>
            <li><a href="/concept14">XIV</a></li>
            <li><a href="/concept15">XV</a></li>
        </ul>
        <a href="/register" class="btn-snow"><?= $__('concept_get_started') ?></a>
    </nav>

    <section class="hero-snow">
        <div>
            <h1>Find Your Perfect<br><span>Beauty Match</span></h1>
            <p>Connect with top-rated beauty professionals and book appointments in seconds.</p>
            <form class="search-snow">
                <input type="text" placeholder="<?= $__('concept_search_salons_placeholder') ?>">
                <button type="submit"><i class="fas fa-search"></i> <?= $__('search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-snow">
        <div class="section-title-snow">
            <span><?= $__('concept_our_services') ?></span>
            <h2><?= $__('concept_what_we_offer') ?></h2>
        </div>
    </section>

    <div class="grid-snow">
        <div class="card-snow">
            <div class="card-icon-snow"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-snow">
            <div class="card-icon-snow"><i class="fas fa-hand-sparkles"></i></div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-snow">
            <div class="card-icon-snow"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_wellness') ?></h3>
            <p><?= $__('concept_wellness_desc') ?></p>
        </div>
    </div>

    <section class="cta-snow">
        <h2><?= $__('concept_grow_business') ?></h2>
        <p><?= $__('concept_grow_business_desc') ?></p>
        <a href="/business/register" class="btn-snow"><?= $__('concept_join_now') ?></a>
    </section>

    <footer class="footer-snow">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('concept_home') ?></a>
    </footer>
</body>
</html>
