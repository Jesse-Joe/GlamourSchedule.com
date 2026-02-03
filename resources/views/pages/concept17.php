<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 17: Monolith - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept17.css">
</head>
<body>
    <nav class="nav-mono">
        <a href="/" class="logo-mono">Glamour</a>
        <ul class="nav-links-mono">
            <li><a href="/concept16">XVI</a></li>
            <li><a href="/concept17">XVII</a></li>
            <li><a href="/concept18">XVIII</a></li>
            <li><a href="/concept19">XIX</a></li>
            <li><a href="/concept20">XX</a></li>
        </ul>
        <a href="/register" class="btn-mono"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-mono">
        <h1>Beauty<span>Redefined for the modern age</span></h1>
        <p>Connect with premium salons. Book instantly. Experience excellence.</p>
        <form class="search-mono">
            <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
            <button type="submit"><?= $__('search') ?></button>
        </form>
    </section>

    <section class="section-white">
        <h2><?= $__('concept_services') ?></h2>
        <p>Discover the full range of beauty treatments available through our curated network of professionals.</p>
    </section>

    <div class="grid-mono">
        <div class="card-mono">
            <div class="card-icon-mono"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-mono">
            <div class="card-icon-mono"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-mono">
            <div class="card-icon-mono"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_wellness') ?></h3>
            <p><?= $__('concept_wellness_desc') ?></p>
        </div>
    </div>

    <section class="cta-mono">
        <h2><?= $__('concept_join_platform') ?></h2>
        <p><?= $__('concept_join_platform_desc') ?></p>
        <a href="/business/register" class="btn-mono"><?= $__('concept_get_started') ?></a>
    </section>

    <footer class="footer-mono">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
