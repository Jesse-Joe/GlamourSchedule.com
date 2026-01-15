<?php ob_start(); ?>

<!-- Search Header -->
<section class="hero-prestige" style="padding-top: 8rem; padding-bottom: 3rem; min-height: auto;">
    <h1 style="font-size: 2rem;">Zoek <span>Salons</span></h1>
    <div class="hero-divider"></div>
    <p>Vind de perfecte salon voor jouw behandeling</p>
</section>

<div class="search-section" style="padding-bottom: 3rem;">
    <form action="/search" method="GET" class="search-prestige" style="max-width: 900px;">
        <div class="search-input-group" style="flex: 2;">
            <i class="fas fa-search"></i>
            <input type="text" name="q" placeholder="Salon of behandeling..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>
        <div class="search-divider"></div>
        <div class="search-input-group">
            <i class="fas fa-map-marker-alt"></i>
            <input type="text" name="location" placeholder="Stad of postcode" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
        </div>
        <div class="search-divider"></div>
        <div class="search-input-group">
            <i class="fas fa-filter"></i>
            <select name="category" style="border: none; background: transparent; font-family: inherit; font-size: 0.9rem; color: var(--charcoal); outline: none; cursor: pointer;">
                <option value="">Alle categorieën</option>
                <option value="haar" <?= ($_GET['category'] ?? '') === 'haar' ? 'selected' : '' ?>>Haar</option>
                <option value="nagels" <?= ($_GET['category'] ?? '') === 'nagels' ? 'selected' : '' ?>>Nagels</option>
                <option value="huid" <?= ($_GET['category'] ?? '') === 'huid' ? 'selected' : '' ?>>Skincare</option>
                <option value="lichaam" <?= ($_GET['category'] ?? '') === 'lichaam' ? 'selected' : '' ?>>Massage</option>
                <option value="makeup" <?= ($_GET['category'] ?? '') === 'makeup' ? 'selected' : '' ?>>Make-up</option>
                <option value="wellness" <?= ($_GET['category'] ?? '') === 'wellness' ? 'selected' : '' ?>>Wellness</option>
                <option value="ontharing" <?= ($_GET['category'] ?? '') === 'ontharing' ? 'selected' : '' ?>>Ontharing</option>
                <option value="bruinen" <?= ($_GET['category'] ?? '') === 'bruinen' ? 'selected' : '' ?>>Bruinen</option>
            </select>
        </div>
        <button type="submit">Zoeken</button>
    </form>
</div>

<!-- Results Section -->
<section class="section section-light">
    <div style="max-width: 1100px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <p style="font-size: 0.85rem; color: var(--silver);">
                <span id="results-count"><?= count($businesses ?? []) ?></span> salons gevonden
            </p>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <label style="font-size: 0.75rem; color: var(--silver); letter-spacing: 1px; text-transform: uppercase;">Sorteren:</label>
                <select id="sort-by" onchange="sortResults(this.value)" style="border: 1px solid rgba(255,255,255,0.1); padding: 0.5rem 1rem; font-family: inherit; font-size: 0.85rem; background: rgba(30, 30, 35, 0.8); color: var(--white); cursor: pointer; border-radius: 4px;">
                    <option value="rating">Hoogste beoordeling</option>
                    <option value="reviews">Meeste reviews</option>
                    <option value="name">Naam A-Z</option>
                </select>
            </div>
        </div>

        <div class="business-grid" id="search-results">
            <?php if (!empty($businesses)): ?>
                <?php foreach ($businesses as $biz): ?>
                <div class="business-card">
                    <div class="business-image">
                        <?php if (!empty($biz['logo_url'])): ?>
                            <img src="<?= htmlspecialchars($biz['logo_url']) ?>" alt="<?= htmlspecialchars($biz['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <i class="fas fa-spa"></i>
                        <?php endif; ?>
                    </div>
                    <div class="business-body">
                        <h3 class="business-name"><?= htmlspecialchars($biz['name']) ?></h3>
                        <div class="business-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($biz['city'] ?? 'Nederland') ?></span>
                        </div>
                        <div class="business-rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= round($biz['avg_rating'] ?? 5) ? '' : ($i - 0.5 <= ($biz['avg_rating'] ?? 5) ? '-half-alt' : '') ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="count">(<?= $biz['review_count'] ?? 0 ?>)</span>
                        </div>
                        <a href="/business/<?= htmlspecialchars($biz['slug']) ?>" class="business-btn">Bekijk & Boek</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: rgba(25, 25, 30, 0.6); border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                    <i class="fas fa-search" style="font-size: 3rem; color: var(--silver); margin-bottom: 1.5rem; display: block;"></i>
                    <h3 style="font-size: 1rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--white);">Geen resultaten</h3>
                    <p style="color: var(--silver); font-size: 0.9rem;">Probeer een andere zoekopdracht of locatie.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($businesses) && count($businesses) >= 12): ?>
        <div id="pagination" style="display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 3rem;">
            <button class="btn btn-outline btn-sm" id="prev-page" disabled>
                <i class="fas fa-chevron-left"></i> Vorige
            </button>
            <span id="page-info" style="font-size: 0.85rem; color: var(--silver);">Pagina 1</span>
            <button class="btn btn-outline btn-sm" id="next-page">
                Volgende <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
    function sortResults(sortBy) {
        const params = new URLSearchParams(window.location.search);
        params.set('sort', sortBy);
        window.location.search = params.toString();
    }
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
