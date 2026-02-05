<?php ob_start(); ?>

<style>
    .search-container {
        max-width: 1200px;
        margin: 1rem auto;
        padding: 0 1rem;
    }
    @media (max-width: 768px) {
        .search-container {
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        .search-card {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
        }
        .search-hero {
            border-radius: 0 !important;
        }
        .form-group {
            text-align: left;
        }
        .form-group label {
            justify-content: flex-start;
        }
        .form-control {
            width: 100%;
            max-width: 100%;
            text-align: left;
        }
        .search-form {
            flex-direction: column;
        }
        .search-input-wrapper {
            min-width: 100%;
        }
        .results-grid {
            grid-template-columns: 1fr !important;
        }
        .category-cards-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    .search-card {
        background: #000000;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        border: 2px solid #333333;
    }
    .search-hero {
        background: #000000;
        padding: 3rem 2rem;
        text-align: center;
        color: #ffffff;
        border-bottom: 2px solid #333333;
        border-radius: 0 0 30px 30px;
    }
    .search-hero i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #ffffff;
    }
    .search-hero h1 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
    }
    .search-hero p {
        margin: 0.5rem 0 0;
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .search-body {
        padding: 2rem;
        background: #000000;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 0;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 1.5rem;
    }
    .section-header i {
        width: 40px;
        height: 40px;
        background: #ffffff;
        color: #000000;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .section-header h4 {
        margin: 0;
        color: #ffffff;
        font-size: 1.1rem;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 1.25rem;
    }
    .form-group label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
    }
    .form-group label i {
        color: #ffffff;
        font-size: 0.9rem;
    }
    .form-control {
        width: 100%;
        padding: 0.9rem 0;
        background: transparent;
        border: none;
        border-bottom: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 0;
        font-size: 1rem;
        color: #ffffff;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        outline: none;
        border-bottom-color: #ffffff;
        box-shadow: none;
    }
    .form-control:hover {
        border-bottom-color: rgba(255, 255, 255, 0.7);
    }
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }
    select.form-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
        background-size: 1rem;
        padding-right: 1.5rem;
        cursor: pointer;
    }
    select.form-control option {
        background: #000000;
        color: #ffffff;
    }

    /* Search Form Layout */
    .search-form {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }
    .search-input-wrapper {
        flex: 1;
        min-width: 200px;
    }
    .search-btn {
        padding: 1.1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        background: #ffffff;
        color: #000000;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        align-self: flex-end;
        margin-bottom: 1.25rem;
    }
    .search-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .category-tab {
        padding: 0.75rem 1.25rem;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .category-tab:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        color: #ffffff;
    }
    .category-tab.active {
        background: #ffffff;
        border-color: #ffffff;
        color: #000000;
    }

    /* Category Cards Section */
    .category-cards-section {
        margin-bottom: 2rem;
    }
    .category-cards-title {
        font-size: 1rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 1rem;
    }
    .category-cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    @media (max-width: 1024px) {
        .category-cards-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    .category-card {
        position: relative;
        height: 120px;
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.3s;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }
    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(255, 255, 255, 0.1);
        border-color: #ffffff;
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
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 100%);
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
        width: 32px;
        height: 32px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }
    .category-card-content h4 {
        font-size: 0.9rem;
        font-weight: 700;
        margin: 0 0 0.2rem;
    }
    .category-card-content .salon-count {
        font-size: 0.7rem;
        opacity: 0.9;
    }

    /* Results Header */
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .results-count {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.8);
    }
    .results-count strong {
        color: #ffffff;
    }
    .sort-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .sort-wrapper label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Results Grid - 4 per row on desktop */
    .results-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
    }
    @media (max-width: 1200px) {
        .results-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 900px) {
        .results-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Salon Card */
    .salon-card {
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
    }
    .salon-card:hover {
        border-color: #ffffff;
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(255, 255, 255, 0.1);
    }
    .salon-image {
        height: 140px;
        background: rgba(255, 255, 255, 0.08);
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
        font-size: 2.5rem;
        color: rgba(255, 255, 255, 0.3);
    }
    .salon-body {
        padding: 1rem;
    }
    .salon-name {
        font-size: 1rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 0.4rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .salon-location {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.5rem;
    }
    .salon-location i {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.75rem;
    }
    .salon-rating {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-bottom: 0.75rem;
    }
    .salon-rating .stars {
        color: #fbbf24;
        font-size: 0.75rem;
    }
    .salon-rating .count {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
    }
    .salon-btn {
        display: block;
        width: 100%;
        padding: 0.7rem;
        background: #ffffff;
        color: #000000;
        text-align: center;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s;
    }
    .salon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        border: 2px dashed rgba(255, 255, 255, 0.2);
    }
    .empty-state i {
        font-size: 3rem;
        color: rgba(255, 255, 255, 0.3);
        margin-bottom: 1rem;
        display: block;
    }
    .empty-state h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 0.5rem;
    }
    .empty-state p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        margin: 0;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        padding: 2rem 0 0;
        margin-top: 2rem;
        border-top: 2px solid rgba(255, 255, 255, 0.2);
    }
    .pagination-btn {
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .pagination-btn:hover:not(:disabled) {
        background: #ffffff;
        color: #000000;
        border-color: #ffffff;
    }
    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    .pagination-info {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
    }
</style>

<?php
$currentCategory = $_GET['category'] ?? $_GET['group'] ?? '';
?>

<div class="search-container">
    <div class="search-card">
        <div class="search-hero">
            <i class="fas fa-search"></i>
            <h1><?= $__('search_salons') ?></h1>
            <p><?= $__('find_perfect_salon') ?></p>
        </div>

        <div class="search-body">
            <!-- Search Form -->
            <div class="section-header">
                <i class="fas fa-filter"></i>
                <h4><?= $__('search_filter') ?></h4>
            </div>

            <form action="/search" method="GET" class="search-form">
                <?php if ($currentCategory): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($currentCategory) ?>">
                <?php endif; ?>

                <div class="search-input-wrapper">
                    <div class="form-group">
                        <label><i class="fas fa-spa"></i> <?= $__('salon_or_treatment') ?></label>
                        <input type="text" name="q" class="form-control" placeholder="<?= $__('search_placeholder') ?>" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                </div>

                <div class="search-input-wrapper">
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> <?= $__('location') ?></label>
                        <input type="text" name="location" class="form-control" placeholder="<?= $__('city_or_postal') ?>" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> <?= $__('search') ?>
                </button>
            </form>

            <!-- Category Tabs -->
            <div class="category-tabs" id="categoryTabs">
                <a href="/search" class="category-tab <?= empty($currentCategory) ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> <?= $__('all') ?>
                </a>
            </div>

            <!-- Category Cards -->
            <?php if (empty($currentCategory)): ?>
            <div class="category-cards-section">
                <h3 class="category-cards-title"><?= $__('discover_by_category') ?></h3>
                <div class="category-cards-grid" id="categoryCards"></div>
            </div>
            <?php endif; ?>

            <!-- Results Header -->
            <div class="results-header">
                <p class="results-count">
                    <strong><?= count($businesses ?? []) ?></strong> <?= $__('salons_found') ?>
                </p>
                <div class="sort-wrapper">
                    <label><?= $__('sort_by') ?></label>
                    <select class="form-control" style="width:auto;padding-right:1.5rem" onchange="sortResults(this.value)">
                        <option value="rating"><?= $__('highest_rating') ?></option>
                        <option value="reviews"><?= $__('most_reviews') ?></option>
                        <option value="name"><?= $__('name_az') ?></option>
                    </select>
                </div>
            </div>

            <!-- Results Grid -->
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
                                        <i class="fas fa-star<?= $i <= round($biz['avg_rating'] ?? 5) ? '' : '-half-alt' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="count">(<?= $biz['review_count'] ?? 0 ?>)</span>
                            </div>
                            <a href="/business/<?= htmlspecialchars($biz['slug']) ?>" class="salon-btn"><?= $__('view_and_book') ?></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3><?= $__('no_results') ?></h3>
                        <p><?= $__('try_different_search') ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (!empty($businesses) && count($businesses) >= 12): ?>
            <div class="pagination">
                <button class="pagination-btn" id="prev-page" disabled>
                    <i class="fas fa-chevron-left"></i> <?= $__('previous') ?>
                </button>
                <span class="pagination-info"><?= $__('page') ?> 1</span>
                <button class="pagination-btn" id="next-page">
                    <?= $__('next') ?> <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function sortResults(sortBy) {
    const params = new URLSearchParams(window.location.search);
    params.set('sort', sortBy);
    window.location.search = params.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const currentCategory = '<?= htmlspecialchars($currentCategory) ?>';
    const tabsContainer = document.getElementById('categoryTabs');
    const cardsContainer = document.getElementById('categoryCards');

    fetch('/api/category-groups')
        .then(response => response.json())
        .then(data => {
            if (data.groups && data.groups.length > 0) {
                data.groups.forEach(group => {
                    const tab = document.createElement('a');
                    tab.href = '/search?category=' + encodeURIComponent(group.slug);
                    tab.className = 'category-tab' + (currentCategory === group.slug ? ' active' : '');
                    tab.innerHTML = '<i class="fas fa-' + group.icon + '"></i> ' + group.label;
                    tabsContainer.appendChild(tab);

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
        .catch(error => console.error('Failed to load categories:', error));
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
