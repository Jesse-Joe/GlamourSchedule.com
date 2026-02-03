<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 7: Midnight Sapphire - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept7.css">
</head>
<body>
    <div class="bg-stars"></div>

    <nav class="nav-sapphire">
        <a href="/" class="logo-sapphire"><i class="fas fa-star"></i> Glamour</a>
        <ul class="nav-links-sapphire">
            <li><a href="/concept6">VI</a></li>
            <li><a href="/concept7">VII</a></li>
            <li><a href="/concept8">VIII</a></li>
            <li><a href="/concept9">IX</a></li>
            <li><a href="/concept10">X</a></li>
        </ul>
        <a href="/register" class="btn-sapphire"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-sapphire">
        <div class="hero-content-sapphire">
            <h1>Beauty That<br><span>Shines Bright</span></h1>
            <p>Discover exceptional beauty services from the best professionals. Your perfect look is just a click away.</p>
            <form class="search-sapphire">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('search') ?></button>
            </form>
        </div>
    </section>

    <section class="section-sapphire">
        <div class="section-title-sapphire">
            <span><?= $__('concept_what_we_offer') ?></span>
            <h2><?= $__('concept_exceptional_services') ?></h2>
        </div>
    </section>

    <div class="grid-sapphire">
        <div class="card-sapphire">
            <div class="card-icon-sapphire"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_excellence') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-sapphire">
            <div class="card-icon-sapphire"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nail_artistry') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-sapphire">
            <div class="card-icon-sapphire"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_wellness') ?></h3>
            <p><?= $__('concept_wellness_desc') ?></p>
        </div>
        <div class="card-sapphire">
            <div class="card-icon-sapphire"><i class="fas fa-eye"></i></div>
            <h3><?= $__('concept_lashes') ?></h3>
            <p><?= $__('concept_lashes_desc') ?></p>
        </div>
    </div>

    <section class="cta-sapphire">
        <h2><?= $__('concept_elevate_salon') ?></h2>
        <p><?= $__('concept_elevate_salon_desc') ?></p>
        <a href="/business/register" class="btn-sapphire"><?= $__('concept_partner_with_us') ?></a>
    </section>

    <footer class="footer-sapphire">
        <p>&copy; 2026 GlamourSchedule. All rights reserved.</p>
        <a href="/"><?= $__('concept_home') ?></a>
    </footer>
</body>
</html>
