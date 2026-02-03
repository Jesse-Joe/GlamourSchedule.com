<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 5: Luxury Black & Gold - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept5.css">
</head>
<body>
    <div class="luxury-bg"></div>

    <nav class="nav-luxury">
        <a href="/" class="logo-luxury">Glamour</a>
        <ul class="nav-links-luxury">
            <li><a href="/concept1">I</a></li>
            <li><a href="/concept2">II</a></li>
            <li><a href="/concept3">III</a></li>
            <li><a href="/concept4">IV</a></li>
            <li><a href="/concept5">V</a></li>
        </ul>
        <a href="/register" class="btn-luxury"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-luxury">
        <h1><?= $__('concept_art_of_beauty') ?></h1>
        <div class="hero-divider"></div>
        <p><?= $__('concept_art_of_beauty_desc') ?></p>
        <form class="search-luxury">
            <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
            <button type="submit"><?= $__('concept_search') ?></button>
        </form>
    </section>

    <section class="section-luxury">
        <div class="section-title-luxury">
            <span><?= $__('concept_our_services') ?></span>
            <h2><?= $__('concept_exclusive_treatments') ?></h2>
        </div>
    </section>

    <div class="grid-luxury">
        <div class="card-luxury">
            <div class="card-icon-luxury"><i class="fas fa-cut"></i></div>
            <h3><?= $__('concept_hair_artistry') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-luxury">
            <div class="card-icon-luxury"><i class="fas fa-gem"></i></div>
            <h3><?= $__('concept_nail_couture') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-luxury">
            <div class="card-icon-luxury"><i class="fas fa-spa"></i></div>
            <h3><?= $__('concept_skin_perfection') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-luxury">
            <div class="card-icon-luxury"><i class="fas fa-hands"></i></div>
            <h3><?= $__('concept_body_rituals') ?></h3>
            <p><?= $__('concept_massage_desc') ?></p>
        </div>
        <div class="card-luxury">
            <div class="card-icon-luxury"><i class="fas fa-magic"></i></div>
            <h3><?= $__('concept_makeup_atelier') ?></h3>
            <p><?= $__('concept_makeup_desc') ?></p>
        </div>
        <div class="card-luxury">
            <div class="card-icon-luxury"><i class="fas fa-crown"></i></div>
            <h3><?= $__('concept_vip_experience') ?></h3>
            <p><?= $__('concept_wellness_desc') ?></p>
        </div>
    </div>

    <section class="cta-luxury">
        <h2><?= $__('concept_elevate_salon') ?></h2>
        <p><?= $__('concept_elevate_salon_desc') ?></p>
        <a href="/business/register" class="btn-luxury"><?= $__('concept_apply_now') ?></a>
    </section>

    <footer class="footer-luxury">
        <div class="footer-grid-luxury">
            <div>
                <h4>GlamourSchedule</h4>
                <p style="color: var(--gray); font-size: 0.9rem;"><?= $__('concept_premium_platform') ?></p>
            </div>
            <div>
                <h4>Platform</h4>
                <ul>
                    <li><a href="/search"><?= $__('search') ?></a></li>
                    <li><a href="/business/register"><?= $__('concept_register') ?></a></li>
                </ul>
            </div>
            <div>
                <h4><?= $__('about') ?></h4>
                <ul>
                    <li><a href="/about"><?= $__('about') ?></a></li>
                    <li><a href="/contact"><?= $__('contact') ?></a></li>
                </ul>
            </div>
            <div>
                <h4>Legal</h4>
                <ul>
                    <li><a href="/terms"><?= $__('terms') ?></a></li>
                    <li><a href="/privacy"><?= $__('privacy') ?></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom-luxury">
            <p>&copy; 2026 GlamourSchedule.</p>
        </div>
    </footer>
</body>
</html>
