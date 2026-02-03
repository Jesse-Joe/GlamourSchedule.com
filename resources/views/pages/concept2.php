<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 2: Soft Pastel - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept2.css">
</head>
<body>
    <div class="pastel-shapes">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav class="nav-pastel">
        <a href="/" class="logo-pastel">Glamour<span>Schedule</span></a>
        <ul class="nav-links-pastel">
            <li><a href="/concept1">1</a></li>
            <li><a href="/concept2">2</a></li>
            <li><a href="/concept3">3</a></li>
            <li><a href="/concept4">4</a></li>
            <li><a href="/concept5">5</a></li>
        </ul>
        <a href="/register" class="btn-pastel"><?= $__('concept_get_started') ?></a>
    </nav>

    <section class="hero-pastel">
        <div class="hero-content-pastel">
            <h1><?= $__('concept_beauty_journey') ?></h1>
            <p><?= $__('concept_beauty_journey_desc') ?></p>
            <form class="search-pastel">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('concept_search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-pastel">
        <div class="section-title-pastel">
            <h2><?= $__('concept_our_services') ?></h2>
        </div>
        <div class="grid-pastel">
            <div class="card-pastel">
                <div class="card-icon-pastel"><i class="fas fa-cut"></i></div>
                <h3><?= $__('concept_hair_styling') ?></h3>
                <p><?= $__('concept_hair_desc') ?></p>
            </div>
            <div class="card-pastel">
                <div class="card-icon-pastel"><i class="fas fa-hand-sparkles"></i></div>
                <h3><?= $__('concept_nail_care') ?></h3>
                <p><?= $__('concept_nails_desc') ?></p>
            </div>
            <div class="card-pastel">
                <div class="card-icon-pastel"><i class="fas fa-spa"></i></div>
                <h3><?= $__('concept_skincare') ?></h3>
                <p><?= $__('concept_skincare_desc') ?></p>
            </div>
            <div class="card-pastel">
                <div class="card-icon-pastel"><i class="fas fa-heart"></i></div>
                <h3><?= $__('concept_wellness') ?></h3>
                <p><?= $__('concept_wellness_desc') ?></p>
            </div>
        </div>
    </section>

    <footer class="footer-pastel">
        <p>&copy; 2026 GlamourSchedule.</p>
    </footer>
</body>
</html>
