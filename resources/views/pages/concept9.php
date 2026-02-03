<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 9: Dark Rose - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Tenor+Sans&family=Cormorant:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept9.css">
</head>
<body>
    <div class="bg-rose"></div>

    <nav class="nav-rose">
        <a href="/" class="logo-rose">Glamour<span>.</span></a>
        <ul class="nav-links-rose">
            <li><a href="/concept6">VI</a></li>
            <li><a href="/concept7">VII</a></li>
            <li><a href="/concept8">VIII</a></li>
            <li><a href="/concept9">IX</a></li>
            <li><a href="/concept10">X</a></li>
        </ul>
        <a href="/register" class="btn-rose"><?= $__('concept_register') ?></a>
    </nav>

    <section class="hero-rose">
        <div class="hero-content-rose">
            <h1>Where Beauty<br>Meets <span>Passion</span></h1>
            <div class="hero-divider-rose"></div>
            <p>Experience the finest beauty treatments from passionate professionals dedicated to making you shine.</p>
            <form class="search-rose">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-rose">
        <div class="section-title-rose">
            <span><?= $__('concept_premium_treatments') ?></span>
            <h2><?= $__('concept_our_services') ?></h2>
        </div>
    </section>

    <div class="grid-rose">
        <div class="card-rose">
            <div class="card-icon-rose"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_artistry') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-rose">
            <div class="card-icon-rose"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nail_couture') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-rose">
            <div class="card-icon-rose"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_body_rituals') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
    </div>

    <section class="cta-rose">
        <h2><?= $__('concept_join_platform') ?></h2>
        <p><?= $__('concept_join_platform_desc') ?></p>
        <a href="/business/register" class="btn-rose"><?= $__('concept_apply') ?></a>
    </section>

    <footer class="footer-rose">
        <p>&copy; 2026 GlamourSchedule. Beauty Elevated.</p>
        <a href="/"><?= $__('concept_home') ?></a>
    </footer>
</body>
</html>
