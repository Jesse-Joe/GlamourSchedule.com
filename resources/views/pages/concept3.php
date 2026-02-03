<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 3: Brutalist - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept3.css">
</head>
<body>
    <nav class="nav-brutal">
        <a href="/" class="logo-brutal">GlamourSchedule</a>
        <ul class="nav-links-brutal">
            <li><a href="/concept1">01</a></li>
            <li><a href="/concept2">02</a></li>
            <li><a href="/concept3">03</a></li>
            <li><a href="/concept4">04</a></li>
            <li><a href="/concept5">05</a></li>
        </ul>
        <a href="/register" class="btn-brutal"><?= $__('concept_register') ?></a>
    </nav>

    <section class="hero-brutal">
        <div>
            <h1><?= $__('concept_book_beauty_now') ?></h1>
            <p><?= $__('concept_book_beauty_now_desc') ?></p>
            <form class="search-brutal">
                <input type="text" placeholder="<?= $__('concept_search_placeholder') ?>">
                <button type="submit"><?= $__('concept_go') ?></button>
            </form>
        </div>
    </section>

    <section class="section-brutal">
        <div class="section-title-brutal">
            <span><?= $__('concept_services') ?></span>
            <h2><?= $__('concept_what_we_offer') ?></h2>
        </div>
    </section>

    <div class="grid-brutal">
        <div class="card-brutal">
            <div class="card-num">01</div>
            <h3><?= $__('concept_hair') ?></h3>
            <p><?= $__('concept_hair_desc') ?></p>
        </div>
        <div class="card-brutal">
            <div class="card-num">02</div>
            <h3><?= $__('concept_nails') ?></h3>
            <p><?= $__('concept_nails_desc') ?></p>
        </div>
        <div class="card-brutal">
            <div class="card-num">03</div>
            <h3><?= $__('concept_skin') ?></h3>
            <p><?= $__('concept_skincare_desc') ?></p>
        </div>
        <div class="card-brutal">
            <div class="card-num">04</div>
            <h3><?= $__('concept_body') ?></h3>
            <p><?= $__('concept_massage_desc') ?></p>
        </div>
        <div class="card-brutal">
            <div class="card-num">05</div>
            <h3><?= $__('concept_makeup') ?></h3>
            <p><?= $__('concept_makeup_desc') ?></p>
        </div>
        <div class="card-brutal">
            <div class="card-num">06</div>
            <h3><?= $__('concept_more') ?></h3>
            <p><?= $__('concept_more_desc') ?></p>
        </div>
    </div>

    <section class="cta-brutal">
        <h2><?= $__('concept_start_now_free') ?></h2>
        <a href="/business/register" class="btn-brutal"><?= $__('concept_register_salon') ?></a>
    </section>

    <footer class="footer-brutal">
        <p>&copy; 2026 GlamourSchedule</p>
        <p><?= $__('concept_built_different') ?></p>
    </footer>
</body>
</html>
