<?php ob_start(); ?>

<style>
    .search-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .search-container {
            padding: 0;
            margin: 0;
        }
        .search-card {
            border-radius: 0 !important;
            box-shadow: none !important;
        }
    }
    .search-card {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .search-header {
        background: #ffffff;
        color: #000000;
        padding: 2rem;
        text-align: center;
        border-bottom: 2px solid #000000;
    }
    .search-header i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #000000;
    }
    .search-header h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #000000;
    }
    .search-header p {
        margin: 0.5rem 0 0;
        color: #6b7280;
        font-size: 0.95rem;
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        background: #fafafa;
        border-bottom: 1px solid #e5e7eb;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .category-tab {
        flex: 1;
        min-width: 100px;
        padding: 1rem 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        background: transparent;
        font-size: 0.85rem;
        font-weight: 500;
        color: #6b7280;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        text-decoration: none;
        white-space: nowrap;
    }
    .category-tab:hover {
        background: #f5f5f5;
        color: #374151;
    }
    .category-tab.active {
        background: #ffffff;
        color: #000000;
        border-bottom: 3px solid #000000;
        margin-bottom: -1px;
    }
    .category-tab i {
        font-size: 1.2rem;
    }

    /* Search Form */
    .search-form-section {
        padding: 1.5rem 2rem;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
    }
    .search-form {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .search-input-wrapper {
        flex: 1;
        min-width: 200px;
        position: relative;
    }
    .search-input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    .search-input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s;
        background: #ffffff;
        color: #000000;
    }
    .search-input:focus {
        outline: none;
        border-color: #000000;
    }
    .search-input::placeholder {
        color: #9ca3af;
    }
    .search-btn {
        padding: 0.875rem 2rem;
        background: #000000;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .search-btn:hover {
        background: #333333;
    }

    /* Results Header */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 2rem;
        background: #fafafa;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .results-count {
        font-size: 0.9rem;
        color: #6b7280;
    }
    .results-count strong {
        color: #000000;
    }
    .sort-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .sort-wrapper label {
        font-size: 0.8rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .sort-select {
        padding: 0.5rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.85rem;
        background: #ffffff;
        color: #000000;
        cursor: pointer;
    }
    .sort-select:focus {
        outline: none;
        border-color: #000000;
    }

    /* Results Grid */
    .results-section {
        padding: 2rem;
        background: #ffffff;
    }
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    /* Business Card - New Style */
    .salon-card {
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
    }
    .salon-card:hover {
        border-color: #000000;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    .salon-image {
        height: 160px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .salon-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .salon-image i {
        font-size: 3rem;
        color: #d1d5db;
    }
    .salon-body {
        padding: 1.25rem;
    }
    .salon-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #000000;
        margin: 0 0 0.5rem;
    }
    .salon-location {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.75rem;
    }
    .salon-location i {
        color: #9ca3af;
    }
    .salon-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .salon-rating .stars {
        color: #fbbf24;
        font-size: 0.85rem;
    }
    .salon-rating .count {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .salon-btn {
        display: block;
        width: 100%;
        padding: 0.75rem;
        background: #000000;
        color: #ffffff;
        text-align: center;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
    }
    .salon-btn:hover {
        background: #333333;
    }

    /* Category Cards */
    .category-cards-section {
        padding: 2rem;
        background: #fafafa;
        border-bottom: 1px solid #e5e7eb;
    }
    .category-cards-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #000000;
        margin: 0 0 1.5rem;
        text-align: center;
    }
    .category-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .category-card {
        position: relative;
        height: 140px;
        border-radius: 16px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.3s;
    }
    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    }
    .category-card-image {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        transition: transform 0.5s;
    }
    .category-card:hover .category-card-image {
        transform: scale(1.1);
    }
    .category-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 100%);
    }
    .category-card-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        color: #ffffff;
        z-index: 1;
    }
    .category-card-icon {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    .category-card-content h4 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
    }
    .category-card-content .salon-count {
        font-size: 0.75rem;
        opacity: 0.9;
    }
    @media (max-width: 768px) {
        .category-cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .category-card {
            height: 120px;
        }
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: #fafafa;
        border-radius: 16px;
        border: 2px dashed #e5e7eb;
    }
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
        display: block;
    }
    .empty-state h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #000000;
        margin: 0 0 0.5rem;
    }
    .empty-state p {
        color: #6b7280;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem 2rem;
        background: #fafafa;
        border-top: 1px solid #e5e7eb;
    }
    .pagination-btn {
        padding: 0.625rem 1.25rem;
        background: #ffffff;
        color: #000000;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .pagination-btn:hover:not(:disabled) {
        border-color: #000000;
    }
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .pagination-info {
        font-size: 0.85rem;
        color: #6b7280;
    }

    /* Dark Mode */
    [data-theme="dark"] .search-card {
        background: #1a1a1a;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    [data-theme="dark"] .search-header {
        background: #1a1a1a;
        color: #ffffff;
        border-bottom-color: #333333;
    }
    [data-theme="dark"] .search-header i,
    [data-theme="dark"] .search-header h1 {
        color: #ffffff;
    }
    [data-theme="dark"] .search-header p {
        color: #9ca3af;
    }
    [data-theme="dark"] .category-tabs {
        background: #111111;
        border-bottom-color: #333333;
    }
    [data-theme="dark"] .category-tab {
        color: #9ca3af;
    }
    [data-theme="dark"] .category-tab:hover {
        background: #222222;
        color: #ffffff;
    }
    [data-theme="dark"] .category-tab.active {
        background: #1a1a1a;
        color: #ffffff;
        border-bottom-color: #ffffff;
    }
    [data-theme="dark"] .search-form-section {
        background: #1a1a1a;
        border-bottom-color: #333333;
    }
    [data-theme="dark"] .search-input {
        background: #111111;
        border-color: #333333;
        color: #ffffff;
    }
    [data-theme="dark"] .search-input:focus {
        border-color: #ffffff;
    }
    [data-theme="dark"] .search-btn {
        background: #ffffff;
        color: #000000;
    }
    [data-theme="dark"] .search-btn:hover {
        background: #e5e5e5;
    }
    [data-theme="dark"] .results-header {
        background: #111111;
        border-bottom-color: #333333;
    }
    [data-theme="dark"] .results-count strong {
        color: #ffffff;
    }
    [data-theme="dark"] .sort-select {
        background: #1a1a1a;
        border-color: #333333;
        color: #ffffff;
    }
    [data-theme="dark"] .sort-select:focus {
        border-color: #ffffff;
    }
    [data-theme="dark"] .results-section {
        background: #1a1a1a;
    }
    [data-theme="dark"] .salon-card {
        background: #111111;
        border-color: #333333;
    }
    [data-theme="dark"] .salon-card:hover {
        border-color: #ffffff;
    }
    [data-theme="dark"] .salon-image {
        background: #0a0a0a;
    }
    [data-theme="dark"] .salon-name {
        color: #ffffff;
    }
    [data-theme="dark"] .salon-btn {
        background: #ffffff;
        color: #000000;
    }
    [data-theme="dark"] .salon-btn:hover {
        background: #e5e5e5;
    }
    [data-theme="dark"] .empty-state {
        background: #111111;
        border-color: #333333;
    }
    [data-theme="dark"] .empty-state h3 {
        color: #ffffff;
    }
    [data-theme="dark"] .pagination {
        background: #111111;
        border-top-color: #333333;
    }
    [data-theme="dark"] .pagination-btn {
        background: #1a1a1a;
        color: #ffffff;
        border-color: #333333;
    }
    [data-theme="dark"] .pagination-btn:hover:not(:disabled) {
        border-color: #ffffff;
    }
    [data-theme="dark"] .category-cards-section {
        background: #111111;
        border-bottom-color: #333333;
    }
    [data-theme="dark"] .category-cards-title {
        color: #ffffff;
    }
</style>

<?php
$currentCategory = $_GET['category'] ?? $_GET['group'] ?? '';
?>

<div class="search-container">
    <div class="search-card">
        <!-- Header -->
        <div class="search-header">
            <i class="fas fa-search"></i>
            <h1>Zoek Salons</h1>
            <p>Vind de perfecte salon voor jouw behandeling</p>
        </div>

        <!-- Category Tabs - Loaded dynamically -->
        <div class="category-tabs" id="categoryTabs">
            <a href="/search" class="category-tab <?= empty($currentCategory) ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i>
                <span>Alles</span>
            </a>
            <!-- Dynamic tabs loaded via JS -->
        </div>

        <!-- Search Form -->
        <div class="search-form-section">
            <form action="/search" method="GET" class="search-form">
                <?php if ($currentCategory): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($currentCategory) ?>">
                <?php endif; ?>
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" class="search-input" placeholder="Salon of behandeling..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
                <div class="search-input-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" class="search-input" placeholder="Stad of postcode" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
                </div>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Zoeken
                </button>
            </form>
        </div>

        <!-- Category Cards - Only show when no specific category is selected -->
        <?php if (empty($currentCategory)): ?>
        <div class="category-cards-section">
            <h3 class="category-cards-title">Ontdek per categorie</h3>
            <div class="category-cards-grid" id="categoryCards">
                <!-- Loaded dynamically via JS -->
            </div>
        </div>
        <?php endif; ?>

        <!-- Results Header -->
        <div class="results-header">
            <p class="results-count">
                <strong><?= count($businesses ?? []) ?></strong> salons gevonden
            </p>
            <div class="sort-wrapper">
                <label>Sorteren:</label>
                <select class="sort-select" onchange="sortResults(this.value)">
                    <option value="rating">Hoogste beoordeling</option>
                    <option value="reviews">Meeste reviews</option>
                    <option value="name">Naam A-Z</option>
                </select>
            </div>
        </div>

        <!-- Results Grid -->
        <div class="results-section">
            <div class="results-grid">
                <?php if (!empty($businesses)): ?>
                    <?php foreach ($businesses as $biz): ?>
                    <div class="salon-card">
                        <div class="salon-image">
                            <?php if (!empty($biz['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($biz['logo_url']) ?>" alt="<?= htmlspecialchars($biz['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <i class="fas fa-spa"></i>
                            <?php endif; ?>
                        </div>
                        <div class="salon-body">
                            <h3 class="salon-name"><?= htmlspecialchars($biz['name']) ?></h3>
                            <div class="salon-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($biz['city'] ?? 'Nederland') ?></span>
                            </div>
                            <div class="salon-rating">
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star<?= $i <= round($biz['avg_rating'] ?? 5) ? '' : ($i - 0.5 <= ($biz['avg_rating'] ?? 5) ? '-half-alt' : '') ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="count">(<?= $biz['review_count'] ?? 0 ?>)</span>
                            </div>
                            <a href="/business/<?= htmlspecialchars($biz['slug']) ?>" class="salon-btn">Bekijk & Boek</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>Geen resultaten</h3>
                        <p>Probeer een andere zoekopdracht of locatie.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (!empty($businesses) && count($businesses) >= 12): ?>
        <div class="pagination">
            <button class="pagination-btn" id="prev-page" disabled>
                <i class="fas fa-chevron-left"></i> Vorige
            </button>
            <span class="pagination-info">Pagina 1</span>
            <button class="pagination-btn" id="next-page">
                Volgende <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function sortResults(sortBy) {
        const params = new URLSearchParams(window.location.search);
        params.set('sort', sortBy);
        window.location.search = params.toString();
    }

    // Load category groups dynamically
    document.addEventListener('DOMContentLoaded', function() {
        const currentCategory = '<?= htmlspecialchars($currentCategory) ?>';
        const tabsContainer = document.getElementById('categoryTabs');
        const cardsContainer = document.getElementById('categoryCards');

        fetch('/api/category-groups')
            .then(response => response.json())
            .then(data => {
                if (data.groups && data.groups.length > 0) {
                    data.groups.forEach(group => {
                        // Add tab
                        const tab = document.createElement('a');
                        tab.href = '/search?category=' + encodeURIComponent(group.slug);
                        tab.className = 'category-tab' + (currentCategory === group.slug ? ' active' : '');
                        tab.innerHTML = '<i class="fas fa-' + group.icon + '"></i><span>' + group.label + '</span>';
                        tabsContainer.appendChild(tab);

                        // Add card (only if cards container exists)
                        if (cardsContainer) {
                            const card = document.createElement('a');
                            card.href = '/search?category=' + encodeURIComponent(group.slug);
                            card.className = 'category-card';
                            card.innerHTML = `
                                <div class="category-card-image" style="background-image: url('${group.image}')"></div>
                                <div class="category-card-overlay"></div>
                                <div class="category-card-content">
                                    <div class="category-card-icon"><i class="fas fa-${group.icon}"></i></div>
                                    <h4>${group.label}</h4>
                                    <span class="salon-count">${group.salon_count} salon${group.salon_count !== 1 ? 's' : ''}</span>
                                </div>
                            `;
                            cardsContainer.appendChild(card);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Failed to load category groups:', error);
            });
    });
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
