<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 18: Silver Line - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept18.css">
</head>
<body>
    <nav class="nav-silver">
        <a href="/" class="logo-silver">Glamour<span>Schedule</span></a>
        <ul class="nav-links-silver">
            <li><a href="/concept16">XVI</a></li>
            <li><a href="/concept17">XVII</a></li>
            <li><a href="/concept18">XVIII</a></li>
            <li><a href="/concept19">XIX</a></li>
            <li><a href="/concept20">XX</a></li>
        </ul>
        <a href="/register" class="btn-silver"><?= $__('concept_register') ?></a>
    </nav>

    <section class="hero-silver">
        <div class="hero-content-silver">
            <div class="hero-badge"><?= $__('concept_premium_platform') ?></div>
            <h1>The Art of <strong>Beauty</strong></h1>
            <div class="hero-line"></div>
            <p>Experience refined elegance with our curated selection of premium beauty services and talented professionals.</p>
            <form class="search-silver">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-silver">
        <div class="section-title-silver">
            <span><?= $__('concept_our_services') ?></span>
            <h2><?= $__('concept_premium_treatments') ?></h2>
        </div>
    </section>

    <div class="grid-silver">
        <div class="card-silver">
            <div class="card-icon-silver"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-silver">
            <div class="card-icon-silver"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-silver">
            <div class="card-icon-silver"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skin') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-silver">
            <div class="card-icon-silver"><i class="fas fa-eye"></i></div>
            <h3><?= $__('concept_lashes') ?></h3>
            <p><?= $__('concept_lashes_desc') ?></p>
        </div>
    </div>

    <section class="cta-silver">
        <h2><?= $__('concept_partner_with_us') ?></h2>
        <p><?= $__('concept_partner_with_us_desc') ?></p>
        <a href="/business/register" class="btn-silver"><?= $__('concept_apply_now') ?></a>
    </section>

    <footer class="footer-silver">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
