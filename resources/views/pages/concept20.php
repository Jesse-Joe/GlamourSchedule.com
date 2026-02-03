<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 20: Prestige - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept20.css">
</head>
<body>
    <nav class="nav-prestige">
        <a href="/" class="logo-prestige">Glamour</a>
        <ul class="nav-links-prestige">
            <li><a href="/concept16">XVI</a></li>
            <li><a href="/concept17">XVII</a></li>
            <li><a href="/concept18">XVIII</a></li>
            <li><a href="/concept19">XIX</a></li>
            <li><a href="/concept20">XX</a></li>
        </ul>
        <a href="/register" class="btn-prestige"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-prestige">
        <div class="hero-tag"><?= $__('concept_premium_platform') ?></div>
        <h1>Beauty <span>Elevated</span></h1>
        <div class="hero-divider-prestige"></div>
        <p>Connect with the finest beauty professionals. Experience seamless booking for exceptional results.</p>
    </section>

    <div class="search-section">
        <form class="search-prestige">
            <input type="text" placeholder="<?= $__('concept_search_salons_placeholder') ?>">
            <button type="submit"><?= $__('search') ?></button>
        </form>
    </div>

    <section class="section-prestige">
        <div class="section-title-prestige">
            <span><?= $__('concept_categories') ?></span>
            <h2><?= $__('concept_our_services') ?></h2>
        </div>
        <div class="grid-prestige">
            <div class="card-prestige">
                <div class="card-icon-prestige"><i class="fas fa-cut"></i></div>
                <h3><?= $__('concept_hair') ?></h3>
                <p><?= $__('concept_hair_desc') ?></p>
            </div>
            <div class="card-prestige">
                <div class="card-icon-prestige"><i class="fas fa-gem"></i></div>
                <h3><?= $__('concept_nails') ?></h3>
                <p><?= $__('concept_nails_desc') ?></p>
            </div>
            <div class="card-prestige">
                <div class="card-icon-prestige"><i class="fas fa-spa"></i></div>
                <h3><?= $__('concept_skin') ?></h3>
                <p><?= $__('concept_skincare_desc') ?></p>
            </div>
        </div>
    </section>

    <div class="stats-prestige">
        <div class="stat-prestige">
            <h3>500+</h3>
            <p><?= $__('concept_premium_salons') ?></p>
        </div>
        <div class="stat-prestige">
            <h3>50K+</h3>
            <p><?= $__('concept_happy_clients') ?></p>
        </div>
        <div class="stat-prestige">
            <h3>4.9</h3>
            <p><?= $__('concept_average_rating') ?></p>
        </div>
    </div>

    <div class="cta-prestige">
        <div>
            <h2><?= $__('concept_grow_business') ?></h2>
            <p><?= $__('concept_grow_business_desc') ?></p>
        </div>
        <a href="/business/register" class="btn-prestige"><?= $__('concept_get_started') ?></a>
    </div>

    <footer class="footer-prestige">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
