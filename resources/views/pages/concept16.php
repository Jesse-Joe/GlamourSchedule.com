<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 16: Dualist - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept16.css">
</head>
<body>
    <nav class="nav-dual">
        <a href="/" class="logo-dual">Glamour</a>
        <ul class="nav-links-dual">
            <li><a href="/concept16">XVI</a></li>
            <li><a href="/concept17">XVII</a></li>
            <li><a href="/concept18">XVIII</a></li>
            <li><a href="/concept19">XIX</a></li>
            <li><a href="/concept20">XX</a></li>
        </ul>
        <a href="/register" class="btn-dual"><?= $__('concept_register') ?></a>
    </nav>

    <section class="hero-dual">
        <div class="hero-dark">
            <div class="hero-content">
                <h1><strong>Dark</strong> Elegance</h1>
                <p>Experience premium beauty services in an atmosphere of refined sophistication.</p>
                <form class="search-dual">
                    <input type="text" placeholder="<?= $__('search') ?>...">
                    <button type="submit"><?= $__('concept_find') ?></button>
                </form>
            </div>
        </div>
        <div class="hero-light">
            <div class="hero-content">
                <h1><strong>Light</strong> Luxury</h1>
                <p>Discover the perfect balance between classic beauty and modern innovation.</p>
                <form class="search-dual">
                    <input type="text" placeholder="<?= $__('search') ?>...">
                    <button type="submit"><?= $__('concept_find') ?></button>
                </form>
            </div>
        </div>
    </section>

    <div class="grid-dual">
        <div class="card-dual">
            <div class="card-icon-dual"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-dual">
            <div class="card-icon-dual"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-dual">
            <div class="card-icon-dual"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skin') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-dual">
            <div class="card-icon-dual"><i class="fas fa-magic"></i></div>
            <h3><?= $__('concept_makeup') ?></h3>
            <p><?= $__('concept_makeup_desc') ?></p>
        </div>
    </div>

    <div class="cta-dual">
        <div class="cta-dark">
            <h2><?= $__('concept_for_professionals') ?></h2>
            <p><?= $__('concept_for_professionals_desc') ?></p>
            <a href="/register" class="btn-dual"><?= $__('concept_get_started') ?></a>
        </div>
        <div class="cta-light">
            <h2><?= $__('concept_grow_business') ?></h2>
            <p><?= $__('concept_grow_business_desc') ?></p>
            <a href="/business/register" class="btn-dual-dark"><?= $__('concept_partner_with_us') ?></a>
        </div>
    </div>

    <footer class="footer-dual">
        <p>&copy; 2026 <a href="/">GlamourSchedule</a></p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
