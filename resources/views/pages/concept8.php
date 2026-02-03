<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 8: Dark Emerald - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept8.css">
</head>
<body>
    <div class="bg-emerald"></div>

    <nav class="nav-emerald">
        <a href="/" class="logo-emerald"><i class="fas fa-leaf"></i> Glamour</a>
        <ul class="nav-links-emerald">
            <li><a href="/concept6">VI</a></li>
            <li><a href="/concept7">VII</a></li>
            <li><a href="/concept8">VIII</a></li>
            <li><a href="/concept9">IX</a></li>
            <li><a href="/concept10">X</a></li>
        </ul>
        <a href="/register" class="btn-emerald"><?= $__('concept_get_started') ?></a>
    </nav>

    <section class="hero-emerald">
        <div class="hero-content-emerald">
            <h1>Natural Beauty,<br><span>Elevated.</span></h1>
            <p>Connect with premium beauty professionals who understand the art of looking and feeling your best.</p>
            <form class="search-emerald">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('concept_find') ?></button>
            </form>
        </div>
    </section>

    <div class="stats-emerald">
        <div class="stat-emerald">
            <h3>500+</h3>
            <p><?= $__('concept_salons') ?></p>
        </div>
        <div class="stat-emerald">
            <h3>50K+</h3>
            <p><?= $__('concept_bookings') ?></p>
        </div>
        <div class="stat-emerald">
            <h3>4.9</h3>
            <p><?= $__('concept_rating') ?></p>
        </div>
    </div>

    <section class="section-emerald">
        <div class="section-title-emerald">
            <span><?= $__('concept_services') ?></span>
            <h2><?= $__('concept_what_we_offer') ?></h2>
        </div>
    </section>

    <div class="grid-emerald">
        <div class="card-emerald">
            <div class="card-icon-emerald"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_care') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-emerald">
            <div class="card-icon-emerald"><i class="fas fa-hand-sparkles"></i></div>
            <h3><?= $__('concept_nail_care') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-emerald">
            <div class="card-icon-emerald"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skincare') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-emerald">
            <div class="card-icon-emerald"><i class="fas fa-magic"></i></div>
            <h3><?= $__('concept_makeup') ?></h3>
            <p><?= $__('concept_makeup_desc') ?></p>
        </div>
    </div>

    <section class="cta-emerald">
        <h2><?= $__('concept_grow_business') ?></h2>
        <p><?= $__('concept_grow_business_desc') ?></p>
        <a href="/business/register" class="btn-emerald"><?= $__('concept_join_now') ?></a>
    </section>

    <footer class="footer-emerald">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
