<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 14: Cotton White - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept14.css">
</head>
<body>
    <nav class="nav-cotton">
        <a href="/" class="logo-cotton">Glamour</a>
        <ul class="nav-links-cotton">
            <li><a href="/concept11">XI</a></li>
            <li><a href="/concept12">XII</a></li>
            <li><a href="/concept13">XIII</a></li>
            <li><a href="/concept14">XIV</a></li>
            <li><a href="/concept15">XV</a></li>
        </ul>
        <a href="/register" class="btn-cotton"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-cotton">
        <div class="hero-content-cotton">
            <h1>Beauty Made <span>Simple</span></h1>
            <p>Discover and book the best beauty services near you. Your next amazing look is just a tap away.</p>
            <form class="search-cotton">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('search') ?></button>
            </form>
        </div>
    </section>

    <div class="stats-cotton">
        <div class="stat-cotton">
            <h3>500+</h3>
            <p><?= $__('concept_salons') ?></p>
        </div>
        <div class="stat-cotton">
            <h3>50K+</h3>
            <p><?= $__('concept_bookings') ?></p>
        </div>
        <div class="stat-cotton">
            <h3>4.9</h3>
            <p><?= $__('concept_rating') ?></p>
        </div>
    </div>

    <section class="section-cotton">
        <div class="section-title-cotton">
            <span><?= $__('concept_services') ?></span>
            <h2><?= $__('concept_categories') ?></h2>
        </div>
    </section>

    <div class="grid-cotton">
        <div class="card-cotton">
            <div class="card-icon-cotton"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_care') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-cotton">
            <div class="card-icon-cotton"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nail_art') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-cotton">
            <div class="card-icon-cotton"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skincare') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-cotton">
            <div class="card-icon-cotton"><i class="fas fa-magic"></i></div>
            <h3><?= $__('concept_makeup') ?></h3>
            <p><?= $__('concept_makeup_desc') ?></p>
        </div>
    </div>

    <section class="cta-cotton">
        <h2><?= $__('concept_partner_with_us') ?></h2>
        <p><?= $__('concept_partner_with_us_desc') ?></p>
        <a href="/business/register" class="btn-cotton"><?= $__('concept_get_started') ?></a>
    </section>

    <footer class="footer-cotton">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
