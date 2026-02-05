<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($business['name']) ?> - Boek nu online">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@300;400;500;600;700&family=Italiana&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mobile.css">

    <title><?= htmlspecialchars($business['name']) ?> - GlamourSchedule</title>

    <style>
        .business-hero {
            min-height: 50vh;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding-bottom: var(--space-3xl);
        }

        .business-hero-bg {
            position: absolute;
            inset: 0;
            z-index: -1;
        }

        .business-hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .business-hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, var(--color-black) 0%, transparent 100%);
        }

        .business-header {
            display: flex;
            align-items: flex-end;
            gap: var(--space-xl);
            flex-wrap: wrap;
        }

        .business-avatar {
            width: 120px;
            height: 120px;
            border-radius: var(--radius-xl);
            border: 4px solid var(--color-rose-gold);
            object-fit: cover;
        }

        .business-info {
            flex: 1;
        }

        .business-name {
            font-size: var(--text-4xl);
            margin-bottom: var(--space-sm);
        }

        .business-meta {
            display: flex;
            gap: var(--space-xl);
            flex-wrap: wrap;
            color: var(--color-silver);
        }

        .business-meta-item {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .business-actions {
            display: flex;
            gap: var(--space-md);
        }

        .business-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--space-3xl);
            padding: var(--space-3xl) 0;
        }

        @media (max-width: 1024px) {
            .business-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .business-hero {
                margin-top: 20px;
                min-height: 40vh;
            }

            .business-header {
                gap: var(--space-md);
            }

            .business-avatar {
                width: 80px;
                height: 80px;
            }

            .business-name {
                font-size: var(--text-2xl);
            }
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-lg);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-md);
            transition: all 0.3s ease;
        }

        .service-item:hover {
            background: var(--glass-bg-light);
            border-color: var(--color-rose-gold);
        }

        .service-info h4 {
            margin-bottom: var(--space-xs);
        }

        .service-duration {
            font-size: var(--text-sm);
            color: var(--color-light-gray);
        }

        .service-price {
            font-family: var(--font-display);
            font-size: var(--text-2xl);
            color: var(--color-rose-gold);
        }

        .booking-card {
            position: sticky;
            top: 100px;
        }

        .review-item {
            padding: var(--space-lg);
            background: var(--glass-bg);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-md);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: var(--space-md);
        }

        .review-author {
            font-weight: 600;
        }

        .review-stars {
            color: var(--color-gold);
        }

        .review-text {
            color: var(--color-silver);
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-inner">
                <a href="/" class="navbar-logo">
                    Glamour<span class="logo-accent">Schedule</span>
                </a>

                <ul class="navbar-menu">
                    <li><a href="/">Home</a></li>
                    <li><a href="/search"><?= $__('salons') ?></a></li>
                    <li><a href="/business/register"><?= $__('for_entrepreneurs') ?></a></li>
                </ul>

                <div class="navbar-actions">
                    <a href="/login" class="btn btn-glass btn-sm"><?= $__('login') ?></a>
                    <a href="/register" class="btn btn-primary btn-sm"><?= $__('register') ?></a>
                </div>

                <div class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Business Hero -->
    <section class="business-hero">
        <div class="business-hero-bg">
            <img src="<?= htmlspecialchars($business['cover_image'] ?? 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=1600&h=900&fit=crop') ?>" alt="<?= htmlspecialchars($business['name']) ?>">
        </div>

        <div class="container">
            <div class="business-header">
                <img src="<?= htmlspecialchars($business['logo'] ?? $business['cover_image'] ?? 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=200&h=200&fit=crop') ?>" alt="<?= htmlspecialchars($business['name']) ?>" class="business-avatar">

                <div class="business-info">
                    <h1 class="business-name"><?= htmlspecialchars($business['name']) ?></h1>
                    <div class="business-meta">
                        <div class="business-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($business['city'] ?? 'Nederland') ?>
                        </div>
                        <div class="business-meta-item">
                            <div class="business-card-stars" style="color: var(--color-gold);">
                                <?php
                                $avgRating = $rating['avg_rating'] ?? 0;
                                $fullStars = floor($avgRating);
                                $hasHalf = ($avgRating - $fullStars) >= 0.5;
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $fullStars) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i == $fullStars && $hasHalf) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <?= number_format($avgRating, 1) ?> (<?= $rating['count'] ?? 0 ?> reviews)
                        </div>
                    </div>
                </div>

                <div class="business-actions hide-mobile">
                    <a href="#book" class="btn btn-primary btn-lg magnetic pulse-glow">
                        <i class="fas fa-calendar-check"></i>
                        Boek Nu
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Content -->
    <section class="section" style="padding-top: var(--space-2xl);">
        <div class="container">
            <div class="business-content">
                <div class="business-main">
                    <!-- About -->
                    <?php if (!empty($business['description'])): ?>
                    <div class="glass-card mb-xl" data-animate>
                        <h2 class="mb-lg">Over ons</h2>
                        <p><?= nl2br(htmlspecialchars($business['description'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Services -->
                    <div class="mb-xl" id="services" data-animate>
                        <h2 class="mb-lg">Behandelingen</h2>

                        <?php if (empty($services)): ?>
                        <div class="glass-card text-center" style="padding: var(--space-3xl);">
                            <i class="fas fa-spa" style="font-size: 3rem; color: var(--color-rose-gold); margin-bottom: var(--space-lg);"></i>
                            <p>Nog geen behandelingen beschikbaar</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($services as $service): ?>
                        <div class="service-item" data-service-id="<?= $service['id'] ?>">
                            <div class="service-info">
                                <h4><?= htmlspecialchars($service['name']) ?></h4>
                                <div class="service-duration">
                                    <i class="far fa-clock"></i>
                                    <?= $service['duration'] ?? 30 ?> minuten
                                </div>
                            </div>
                            <div class="service-price">
                                &euro;<?= number_format($service['price'] ?? 0, 2, ',', '.') ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Reviews -->
                    <div id="reviews" data-animate>
                        <h2 class="mb-lg">Reviews</h2>

                        <?php if (empty($reviews)): ?>
                        <div class="glass-card text-center" style="padding: var(--space-3xl);">
                            <i class="fas fa-star" style="font-size: 3rem; color: var(--color-gold); margin-bottom: var(--space-lg);"></i>
                            <p>Nog geen reviews - wees de eerste!</p>
                        </div>
                        <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <span class="review-author"><?= htmlspecialchars($review['user_name'] ?? 'Anoniem') ?></span>
                                <div class="review-stars">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="<?= $i < $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($review['comment'] ?? '') ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="business-sidebar">
                    <!-- Booking Card -->
                    <div class="glass-card booking-card" id="book">
                        <h3 class="mb-lg">Boek een afspraak</h3>

                        <form action="/book/<?= htmlspecialchars($business['slug']) ?>" method="POST" data-validate>
                            <div class="form-group">
                                <label class="form-label">Kies een behandeling</label>
                                <select name="service_id" class="form-input" required>
                                    <option value="">Selecteer...</option>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['id'] ?>">
                                        <?= htmlspecialchars($service['name']) ?> - &euro;<?= number_format($service['price'], 2, ',', '.') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Datum</label>
                                <input type="date" name="date" class="form-input" required min="<?= date('Y-m-d') ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tijd</label>
                                <select name="time" class="form-input" required>
                                    <option value="">Selecteer eerst een datum</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-full btn-lg">
                                <i class="fas fa-calendar-check"></i>
                                Bevestig Boeking
                            </button>
                        </form>

                        <p class="text-center mt-auto" style="margin-top: var(--space-lg); font-size: var(--text-sm); color: var(--color-light-gray);">
                            <i class="fas fa-shield-alt"></i>
                            Veilig en gratis annuleren tot 24 uur van tevoren
                        </p>
                    </div>

                    <!-- Contact Info -->
                    <div class="glass-card" style="margin-top: var(--space-xl);">
                        <h4 class="mb-md">Contact</h4>

                        <?php if (!empty($business['phone'])): ?>
                        <p class="mb-sm">
                            <i class="fas fa-phone" style="width: 20px; color: var(--color-rose-gold);"></i>
                            <?= htmlspecialchars($business['phone']) ?>
                        </p>
                        <?php endif; ?>

                        <?php if (!empty($business['email'])): ?>
                        <p class="mb-sm">
                            <i class="fas fa-envelope" style="width: 20px; color: var(--color-rose-gold);"></i>
                            <?= htmlspecialchars($business['email']) ?>
                        </p>
                        <?php endif; ?>

                        <?php if (!empty($business['address'])): ?>
                        <p>
                            <i class="fas fa-map-marker-alt" style="width: 20px; color: var(--color-rose-gold);"></i>
                            <?= htmlspecialchars($business['address']) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; 2024 GlamourSchedule. Alle rechten voorbehouden.
                </p>
                <div class="footer-legal">
                    <a href="/privacy">Privacybeleid</a>
                    <a href="/terms">Algemene Voorwaarden</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="mobile-menu-content">
            <ul class="mobile-menu-links">
                <li><a href="/">Home</a></li>
                <li><a href="/search"><?= $__('salons') ?></a></li>
                <li><a href="/business/register"><?= $__('for_entrepreneurs') ?></a></li>
            </ul>
            <div class="mobile-menu-actions">
                <a href="/login" class="btn btn-glass w-full">Inloggen</a>
                <a href="/register" class="btn btn-primary w-full">Registreren</a>
            </div>
        </div>
    </div>

    <!-- Mobile Book Button -->
    <div class="show-mobile-only" style="position: fixed; bottom: 0; left: 0; right: 0; padding: var(--space-md); background: var(--color-charcoal); border-top: 1px solid var(--glass-border); z-index: 100;">
        <a href="#book" class="btn btn-primary w-full">
            <i class="fas fa-calendar-check"></i>
            Boek Nu
        </a>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>
