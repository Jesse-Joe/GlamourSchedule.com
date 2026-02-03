<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 10: Obsidian Platinum - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Didact+Gothic&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept10.css">
</head>
<body>
    <div class="bg-obsidian"></div>

    <nav class="nav-obsidian">
        <a href="/" class="logo-obsidian">Glamour</a>
        <ul class="nav-links-obsidian">
            <li><a href="/concept6">VI</a></li>
            <li><a href="/concept7">VII</a></li>
            <li><a href="/concept8">VIII</a></li>
            <li><a href="/concept9">IX</a></li>
            <li><a href="/concept10">X</a></li>
        </ul>
        <a href="/register" class="btn-obsidian"><?= $__('concept_register') ?></a>
    </nav>

    <section class="hero-obsidian">
        <h1>The Art of Beauty<span>Refined Excellence</span></h1>
        <div class="hero-divider-obsidian"></div>
        <p>Discover premium beauty services curated for those who appreciate sophistication and quality.</p>
        <form class="search-obsidian">
            <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
            <button type="submit"><?= $__('search') ?></button>
        </form>
    </section>

    <section class="section-obsidian">
        <div class="section-title-obsidian">
            <span><?= $__('concept_services') ?></span>
            <h2><?= $__('concept_premium_treatments') ?></h2>
        </div>
    </section>

    <div class="grid-obsidian">
        <div class="card-obsidian">
            <div class="card-icon-obsidian"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-obsidian">
            <div class="card-icon-obsidian"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-obsidian">
            <div class="card-icon-obsidian"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skin') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-obsidian">
            <div class="card-icon-obsidian"><i class="fas fa-magic"></i></div>
            <h3><?= $__('concept_makeup') ?></h3>
            <p><?= $__('concept_makeup_desc') ?></p>
        </div>
        <div class="card-obsidian">
            <div class="card-icon-obsidian"><i class="fas fa-hands"></i></div>
            <h3><?= $__('concept_massage') ?></h3>
            <p><?= $__('concept_massage_desc') ?></p>
        </div>
        <div class="card-obsidian">
            <div class="card-icon-obsidian"><i class="fas fa-eye"></i></div>
            <h3><?= $__('concept_lashes') ?></h3>
            <p><?= $__('concept_lashes_desc') ?></p>
        </div>
    </div>

    <section class="cta-obsidian">
        <h2><?= $__('concept_elevate_salon') ?></h2>
        <p><?= $__('concept_elevate_salon_desc') ?></p>
        <a href="/business/register" class="btn-obsidian"><?= $__('concept_apply_now') ?></a>
    </section>

    <footer class="footer-obsidian">
        <p>&copy; 2026 <a href="/">GlamourSchedule</a>. Excellence in Every Detail.</p>
    </footer>
</body>
</html>
