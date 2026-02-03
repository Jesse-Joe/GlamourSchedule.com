<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 15: Blanc Luxe - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept15.css">
</head>
<body>
    <nav class="nav-blanc">
        <a href="/" class="logo-blanc">Glamour<span>Schedule</span></a>
        <ul class="nav-links-blanc">
            <li><a href="/concept11">XI</a></li>
            <li><a href="/concept12">XII</a></li>
            <li><a href="/concept13">XIII</a></li>
            <li><a href="/concept14">XIV</a></li>
            <li><a href="/concept15">XV</a></li>
        </ul>
        <a href="/register" class="btn-blanc"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-blanc">
        <div class="hero-content-blanc">
            <h1>Premium Beauty<br><span>At Your Fingertips</span></h1>
            <p>Connect with the best salons and beauty professionals. Experience seamless booking for exceptional results.</p>
            <form class="search-blanc">
                <input type="text" placeholder="<?= $__('concept_search_salons_placeholder') ?>">
                <button type="submit"><?= $__('search') ?></button>
            </form>
        </div>
        <div class="hero-visual-blanc">
            <i class="fas fa-spa"></i>
        </div>
    </section>

    <section class="section-blanc">
        <div class="section-title-blanc">
            <span><?= $__('concept_categories') ?></span>
            <h2><?= $__('concept_services') ?></h2>
        </div>
    </section>

    <div class="grid-blanc">
        <div class="card-blanc">
            <div class="card-icon-blanc"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-blanc">
            <div class="card-icon-blanc"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-blanc">
            <div class="card-icon-blanc"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skin') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-blanc">
            <div class="card-icon-blanc"><i class="fas fa-eye"></i></div>
            <h3><?= $__('concept_lashes') ?></h3>
            <p><?= $__('concept_lashes_desc') ?></p>
        </div>
    </div>

    <div class="cta-blanc">
        <div>
            <h2><?= $__('concept_grow_business') ?></h2>
            <p><?= $__('concept_grow_business_desc') ?></p>
        </div>
        <a href="/business/register" class="btn-blanc"><?= $__('concept_get_started') ?></a>
    </div>

    <footer class="footer-blanc">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('concept_home') ?></a>
    </footer>
</body>
</html>
