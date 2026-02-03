<!DOCTYPE html>
<html lang="<?= $lang ?? 'nl' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concept 19: Zenith - GlamourSchedule</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/concept19.css">
</head>
<body>
    <nav class="nav-zen">
        <a href="/" class="logo-zen">Glamour</a>
        <ul class="nav-links-zen">
            <li><a href="/concept16">XVI</a></li>
            <li><a href="/concept17">XVII</a></li>
            <li><a href="/concept18">XVIII</a></li>
            <li><a href="/concept19">XIX</a></li>
            <li><a href="/concept20">XX</a></li>
        </ul>
        <a href="/register" class="btn-zen"><?= $__('concept_book_now') ?></a>
    </nav>

    <section class="hero-zen">
        <h1>Beauty Simplified</h1>
        <div class="hero-zen-line"></div>
        <p>Find and book premium beauty services with effortless elegance. Less noise, more beauty.</p>
        <form class="search-zen">
            <input type="text" placeholder="<?= $__('search') ?>...">
            <button type="submit"><?= $__('concept_find') ?></button>
        </form>
    </section>

    <section class="section-zen">
        <h2><?= $__('concept_services') ?></h2>
        <div class="grid-zen">
            <div class="card-zen">
                <div class="card-zen-left">
                    <span class="card-number">01</span>
                    <div>
                        <h3><?= $__('concept_hair_care') ?></h3>
                        <p><?= $__('concept_hair_desc') ?></p>
                    </div>
                </div>
                <span class="card-arrow">&rarr;</span>
            </div>
            <div class="card-zen">
                <div class="card-zen-left">
                    <span class="card-number">02</span>
                    <div>
                        <h3><?= $__('concept_nail_art') ?></h3>
                        <p><?= $__('concept_nails_desc') ?></p>
                    </div>
                </div>
                <span class="card-arrow">&rarr;</span>
            </div>
            <div class="card-zen">
                <div class="card-zen-left">
                    <span class="card-number">03</span>
                    <div>
                        <h3><?= $__('concept_skincare') ?></h3>
                        <p><?= $__('concept_skincare_desc') ?></p>
                    </div>
                </div>
                <span class="card-arrow">&rarr;</span>
            </div>
            <div class="card-zen">
                <div class="card-zen-left">
                    <span class="card-number">04</span>
                    <div>
                        <h3><?= $__('concept_wellness') ?></h3>
                        <p><?= $__('concept_massage_desc') ?></p>
                    </div>
                </div>
                <span class="card-arrow">&rarr;</span>
            </div>
        </div>
    </section>

    <section class="cta-zen">
        <h2><?= $__('concept_for_professionals') ?></h2>
        <p><?= $__('concept_for_professionals_desc') ?></p>
        <a href="/business/register" class="btn-zen-filled"><?= $__('concept_apply') ?></a>
    </section>

    <footer class="footer-zen">
        <p>&copy; 2026 GlamourSchedule</p>
        <a href="/"><?= $__('home') ?></a>
    </footer>
</body>
</html>
