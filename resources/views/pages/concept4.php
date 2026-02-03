<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 4: Nature Organic - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept4.css">
</head>
<body>
    <div class="nature-bg"></div>

    <nav class="nav-nature">
        <a href="/" class="logo-nature"><i class="fas fa-leaf"></i> GlamourSchedule</a>
        <ul class="nav-links-nature">
            <li><a href="/concept1">Concept 1</a></li>
            <li><a href="/concept2">Concept 2</a></li>
            <li><a href="/concept3">Concept 3</a></li>
            <li><a href="/concept4">Concept 4</a></li>
            <li><a href="/concept5">Concept 5</a></li>
        </ul>
        <a href="/register" class="btn-nature"><?= $__('concept_get_started') ?></a>
    </nav>

    <section class="hero-nature">
        <div>
            <h1><?= $__('concept_natural_beauty') ?></h1>
            <p><?= $__('concept_natural_beauty_desc') ?></p>
            <form class="search-nature">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('concept_search') ?></button>
            </form>
        </div>
        <div class="hero-image-nature">
            <i class="fas fa-spa"></i>
        </div>
    </section>

    <section class="section-nature">
        <div class="section-title-nature">
            <span><?= $__('concept_our_services') ?></span>
            <h2><?= $__('concept_premium_treatments') ?></h2>
        </div>
        <div class="grid-nature">
            <div class="card-nature">
                <div class="card-img-nature"><i class="fas fa-cut"></i></div>
                <div class="card-body-nature">
                    <h3><?= $__('concept_hair_care') ?></h3>
                    <p><?= $__('concept_hair_desc') ?></p>
                </div>
            </div>
            <div class="card-nature">
                <div class="card-img-nature"><i class="fas fa-leaf"></i></div>
                <div class="card-body-nature">
                    <h3><?= $__('concept_skincare') ?></h3>
                    <p><?= $__('concept_skincare_desc') ?></p>
                </div>
            </div>
            <div class="card-nature">
                <div class="card-img-nature"><i class="fas fa-hands"></i></div>
                <div class="card-body-nature">
                    <h3><?= $__('concept_massage') ?></h3>
                    <p><?= $__('concept_massage_desc') ?></p>
                </div>
            </div>
        </div>
    </section>

    <div class="cta-nature">
        <h2><?= $__('concept_green_community') ?></h2>
        <p><?= $__('concept_green_community_desc') ?></p>
        <a href="/business/register" class="btn-nature"><?= $__('concept_register') ?></a>
    </div>

    <footer class="footer-nature">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="https://phantrium.com">By Phantrium</a>
    </footer>
</body>
</html>
