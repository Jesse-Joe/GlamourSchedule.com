<?php ob_start(); ?>

<style>
/* Search Page Styles */
.search-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Search Card - matching registration form */
.search-card {
    background: #ffffff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
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
    margin: 0.5rem 0 0 0;
    font-size: 0.95rem;
    color: #000000;
}
.search-body {
    padding: 2rem;
}

/* Search Bar - matching registration inputs */
.search-bar {
    background: transparent;
    border: none;
    border-radius: 0;
    padding: 0;
    margin-bottom: 0;
}
.search-bar-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.search-bar-row {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.search-bar-input {
    flex: 1;
    min-width: 100%;
    position: relative;
}
.search-bar-input i {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    color: #000000;
}
.search-bar-input input {
    width: 100%;
    padding: 0.85rem 0 0.85rem 1.75rem;
    border: none;
    border-bottom: 2px solid rgba(0, 0, 0, 0.3);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: #000000;
    transition: all 0.3s ease;
}
.search-bar-input input:focus {
    outline: none;
    border-bottom-color: #000000;
    box-shadow: none;
}
.search-bar-input input::placeholder {
    color: rgba(0, 0, 0, 0.4);
}
.search-bar-select {
    flex: 1;
    min-width: 100%;
}
.search-bar-select select,
.search-bar-select input {
    width: 100%;
    padding: 0.85rem 0;
    border: none;
    border-bottom: 2px solid rgba(0, 0, 0, 0.3);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: #000000;
    appearance: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
.search-bar-select select:focus,
.search-bar-select input:focus {
    outline: none;
    border-bottom-color: #000000;
    box-shadow: none;
}
.search-bar-select select::placeholder,
.search-bar-select input::placeholder {
    color: rgba(0, 0, 0, 0.4);
}
.search-bar-select select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23000000' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0 center;
}
.search-bar-btn {
    width: 100%;
    padding: 1rem;
    background: #000000;
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.search-bar-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

/* Advanced Filters Panel */
.filters-panel {
    background: transparent;
    border: none;
    border-radius: 0;
    margin-bottom: 0;
    overflow: hidden;
    border-top: 1px solid #f5f5f5;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
}
.filters-toggle {
    width: 100%;
    padding: 0.75rem 0;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: 0.95rem;
    font-weight: 600;
    color: #000000;
}
.filters-toggle i {
    transition: transform 0.3s;
}
.filters-toggle.active i {
    transform: rotate(180deg);
}
.filters-content {
    display: none;
    padding: 1.5rem 0 0 0;
    border-top: none;
}
.filters-content.show {
    display: block;
}
.filter-search {
    margin-bottom: 1.5rem;
}
.filter-search input {
    width: 100%;
    padding: 0.85rem 0;
    border: none;
    border-bottom: 2px solid rgba(0, 0, 0, 0.3);
    border-radius: 0;
    font-size: 0.95rem;
    background: transparent;
    color: #000000;
    transition: all 0.3s ease;
}
.filter-search input:focus {
    outline: none;
    border-bottom-color: #000000;
}
.filter-search input::placeholder {
    color: rgba(0, 0, 0, 0.4);
}
.filter-groups {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
.filter-group-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #000000;
    border-radius: 50px;
    background: #ffffff;
    color: #000000;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
.filter-group-btn:hover,
.filter-group-btn.active {
    background: #000000;
    color: #ffffff;
}
.filter-categories {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 0.5rem;
}
.filter-cat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
}
.filter-cat-item:hover {
    border-color: #000000;
}
.filter-cat-item.selected {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
}
.filter-cat-item input {
    display: none;
}
.filter-cat-item i {
    width: 20px;
    text-align: center;
}

/* Results Container - wider than search form */
.results-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
    background: #000000;
}

/* Category Pills */
.category-pills {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.category-pills::-webkit-scrollbar {
    display: none;
}
.category-pill {
    flex-shrink: 0;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    background: transparent;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s;
    border: 2px solid #ffffff;
}
.category-pill:hover {
    background: #ffffff;
    color: #000000;
}
.category-pill.active {
    background: #ffffff;
    color: #000000;
}

/* Results Header */
.results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding: 0 0.25rem;
}
.results-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}
.results-title i {
    color: #ffffff;
}
.results-count {
    background: #ffffff;
    color: #000000;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}
.results-sort {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.results-sort label {
    color: #ffffff;
    font-size: 0.9rem;
}
.results-sort select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 2px solid #ffffff;
    border-radius: 8px;
    font-size: 0.9rem;
    background: transparent;
    color: #ffffff;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
}

/* Business Grid */
.business-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 640px) {
    .business-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .business-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
}
@media (min-width: 1280px) {
    .business-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Business Card - Modern Style */
.biz-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    border: 2px solid #e5e5e5;
}
.biz-card:hover {
    transform: translateY(-4px);
    border-color: #000000;
}

/* Card Image */
.biz-card-img {
    position: relative;
    height: 160px;
    background: #f5f5f5;
    overflow: hidden;
    border-bottom: 2px solid #000000;
}
.biz-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.biz-card:hover .biz-card-img img {
    transform: scale(1.05);
}
.biz-card-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.biz-card-img-placeholder i {
    font-size: 3rem;
    color: rgba(255,255,255,0.4);
}

/* Card Badges */
.biz-card-badges {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    right: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.biz-badge {
    padding: 0.35rem 0.6rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.biz-badge-verified {
    background: #000000;
    color: white;
}
.biz-badge-new {
    background: #000000;
    color: white;
}
.biz-badge-popular {
    background: #000000;
    color: white;
}
.biz-card-category {
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 0.35rem 0.6rem;
    border-radius: 6px;
    font-size: 0.75rem;
}

/* Card Body */
.biz-card-body {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.biz-card-name {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0 0 0.5rem;
    color: #000000;
    line-height: 1.3;
}
.biz-card-location {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: #000000;
    font-size: 0.85rem;
    margin-bottom: 0.75rem;
}
.biz-card-location i {
    color: #000000;
    font-size: 0.8rem;
}

/* Rating */
.biz-card-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.biz-card-stars {
    display: flex;
    gap: 2px;
}
.biz-card-stars i {
    font-size: 0.8rem;
    color: #000000;
}
.biz-card-stars i.empty {
    color: #cccccc;
}
.biz-card-rating-text {
    font-size: 0.85rem;
    color: #000000;
}
.biz-card-rating-score {
    font-weight: 700;
    color: #000000;
}

/* Services Preview */
.biz-card-services {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
}
.biz-service-tag {
    padding: 0.25rem 0.5rem;
    background: #f5f5f5;
    border-radius: 4px;
    font-size: 0.7rem;
    color: #000000;
}

/* Price */
.biz-card-footer {
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e5e5;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.biz-card-price {
    font-size: 0.85rem;
    color: #000000;
}
.biz-card-price strong {
    font-size: 1.1rem;
    color: #000000;
}
.biz-card-cta {
    padding: 0.5rem 1rem;
    background: #ffffff;
    color: #000000;
    border: 2px solid #000000;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s;
}
.biz-card:hover .biz-card-cta {
    background: #000000;
    color: #ffffff;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: transparent;
    border: 2px solid #ffffff;
    border-radius: 16px;
}
.empty-state-icon {
    width: 80px;
    height: 80px;
    background: transparent;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    border: 2px solid #ffffff;
}
.empty-state-icon i {
    font-size: 2rem;
    color: #ffffff;
}
.empty-state h3 {
    margin: 0 0 0.5rem;
    font-size: 1.25rem;
    color: #ffffff;
}
.empty-state p {
    color: #ffffff;
    margin: 0 0 1.5rem;
}
.empty-state .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: transparent;
    color: #ffffff;
    border: 2px solid #ffffff;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
}
.empty-state .btn:hover {
    background: #ffffff;
    color: #000000;
}

/* Dark Mode */
[data-theme="dark"] .search-card {
    background: var(--bg-card);
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}
[data-theme="dark"] .search-header {
    border-bottom-color: rgba(255, 255, 255, 0.3);
}
[data-theme="dark"] .search-header i,
[data-theme="dark"] .search-header h1,
[data-theme="dark"] .search-header p {
    color: var(--white);
}
[data-theme="dark"] .search-bar-input input,
[data-theme="dark"] .search-bar-select select,
[data-theme="dark"] .search-bar-select input {
    background: transparent;
    color: var(--white);
    border-bottom-color: rgba(255, 255, 255, 0.3);
}
[data-theme="dark"] .search-bar-input input:focus,
[data-theme="dark"] .search-bar-select select:focus,
[data-theme="dark"] .search-bar-select input:focus {
    border-bottom-color: var(--white);
}
[data-theme="dark"] .search-bar-input input::placeholder,
[data-theme="dark"] .search-bar-select input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}
[data-theme="dark"] .search-bar-input i {
    color: var(--white);
}
[data-theme="dark"] .filters-panel {
    border-top-color: var(--border);
}
[data-theme="dark"] .filters-toggle,
[data-theme="dark"] .filter-search input {
    color: var(--white);
}
[data-theme="dark"] .filter-search input {
    border-bottom-color: rgba(255, 255, 255, 0.3);
}
[data-theme="dark"] .filter-search input:focus {
    border-bottom-color: var(--white);
}
[data-theme="dark"] .biz-card {
    background: var(--bg-card);
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
[data-theme="dark"] .biz-card:hover {
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
}
[data-theme="dark"] .biz-service-tag {
    background: var(--bg-secondary);
}
[data-theme="dark"] .results-sort select {
    background: var(--bg-card);
    border-color: var(--border);
    color: var(--text);
}

/* Mobile Adjustments */
@media (max-width: 768px) {
    .search-page {
        padding: 0;
        max-width: 100%;
        margin: 0;
    }
    .search-card {
        border-radius: 0;
        box-shadow: none;
    }
    .search-header {
        padding: 1.5rem 1rem;
    }
    .search-body {
        padding: 1.5rem 1rem;
    }
    .search-bar-input input,
    .search-bar-select select,
    .search-bar-select input {
        text-align: left;
    }
    .results-header {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        padding: 1rem;
    }
    .results-title {
        justify-content: flex-start;
    }
    .category-pills {
        padding: 1rem;
        margin-bottom: 0;
    }
    .business-grid {
        padding: 1rem;
    }
    .biz-card-img {
        height: 140px;
    }
    .empty-state {
        border-radius: 0;
        border-left: none;
        border-right: none;
        margin: 0;
    }
    .filter-groups {
        justify-content: flex-start;
    }
    .results-container {
        padding: 1.5rem 1rem;
    }
}
</style>

<div class="search-page">
    <!-- Search Card -->
    <div class="search-card">
        <div class="search-header">
            <i class="fas fa-search"></i>
            <h1>Zoek Salons</h1>
            <p>Vind de perfecte salon bij jou in de buurt</p>
        </div>

        <div class="search-body">
            <!-- Search Bar -->
            <div class="search-bar">
                <form method="GET" action="/search" class="search-bar-form">
                    <div class="search-bar-row">
                        <div class="search-bar-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="q" placeholder="Zoek salon, behandeling..."
                                   value="<?= htmlspecialchars($query) ?>">
                        </div>
                        <div class="search-bar-select">
                            <select name="category">
                                <option value="">Alle categorieën</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['translated_name'] ?? $cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="search-bar-select">
                            <input type="text" name="location" placeholder="Stad of postcode"
                                   value="<?= htmlspecialchars($location) ?>">
                        </div>
                    </div>
                    <button type="submit" class="search-bar-btn">
                        <i class="fas fa-search"></i>
                        <span>Zoeken</span>
                    </button>
                </form>
            </div>

            <!-- Advanced Filters Panel -->
            <div class="filters-panel">
                <button class="filters-toggle" onclick="toggleFilters(this)">
                    <span><i class="fas fa-sliders-h"></i> Uitgebreid zoeken</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="filters-content" id="filters-content">
            <!-- Search within categories -->
            <div class="filter-search">
                <input type="text" id="cat-filter-search" placeholder="Zoek specifieke categorie..." oninput="filterCategoryList()">
            </div>

            <!-- Group buttons -->
            <div class="filter-groups">
                <?php
                $groups = [
                    'all' => ['name' => 'Alles', 'icon' => 'th'],
                    'haar' => ['name' => 'Haar', 'icon' => 'cut'],
                    'nagels' => ['name' => 'Nagels', 'icon' => 'hand-sparkles'],
                    'huid' => ['name' => 'Huid', 'icon' => 'spa'],
                    'lichaam' => ['name' => 'Lichaam', 'icon' => 'hands'],
                    'ontharing' => ['name' => 'Ontharing', 'icon' => 'feather'],
                    'makeup' => ['name' => 'Make-up', 'icon' => 'paint-brush'],
                    'wellness' => ['name' => 'Wellness', 'icon' => 'hot-tub'],
                    'bruinen' => ['name' => 'Bruinen', 'icon' => 'sun'],
                    'medisch' => ['name' => 'Medisch', 'icon' => 'user-md'],
                    'alternatief' => ['name' => 'Alternatief', 'icon' => 'yin-yang'],
                ];
                $activeGroup = $_GET['group'] ?? 'all';
                foreach ($groups as $slug => $grp): ?>
                    <button type="button" class="filter-group-btn <?= $activeGroup === $slug ? 'active' : '' ?>"
                            data-group="<?= $slug ?>" onclick="selectGroup('<?= $slug ?>')">
                        <i class="fas fa-<?= $grp['icon'] ?>"></i> <?= $grp['name'] ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Category checkboxes -->
            <div class="filter-categories" id="filter-categories">
                <?php
                $iconMap = [
                    'hair' => 'cut', 'kapper' => 'cut', 'hairdresser' => 'cut',
                    'barber' => 'user-tie', 'nails' => 'hand-sparkles', 'nagels' => 'hand-sparkles',
                    'manicure' => 'hand-sparkles', 'pedicure' => 'shoe-prints',
                    'beauty' => 'spa', 'skincare' => 'leaf', 'facial' => 'leaf',
                    'massage' => 'hands', 'makeup' => 'paint-brush', 'waxing' => 'feather',
                    'lashes' => 'eye', 'wimpers' => 'eye', 'brows' => 'eye',
                    'tattoo' => 'pen-nib', 'piercing' => 'ring', 'wellness' => 'hot-tub',
                    'spa' => 'hot-tub', 'tanning' => 'sun', 'sunbed' => 'sun',
                    'botox' => 'syringe', 'fillers' => 'syringe', 'acupuncture' => 'yin-yang',
                ];
                $groupMapping = [
                    'haar' => ['hair', 'kapper', 'hairdresser', 'hairstylist', 'barber', 'barbershop', 'herenkapper', 'dameskapper', 'afro-hair', 'curly-hair', 'hair-colorist', 'bridal-hair'],
                    'nagels' => ['nails', 'nail-salon', 'nagelstudio', 'manicure', 'pedicure', 'gel-nails', 'gelnagels', 'acrylic-nails', 'polygel', 'nail-art'],
                    'huid' => ['beauty', 'beauty-salon', 'skincare', 'huidverzorging', 'facial', 'gezichtsbehandeling', 'acne-treatment', 'dermapen', 'microneedling', 'hydrafacial'],
                    'lichaam' => ['massage', 'massage-therapist', 'deep-tissue', 'hot-stone', 'swedish-massage', 'thai-massage', 'sports-massage', 'body-treatments', 'body-contouring', 'aromatherapy', 'reflexology'],
                    'ontharing' => ['waxing', 'waxsalon', 'brazilian-wax', 'laser-hair-removal', 'ipl-treatment', 'electrolysis', 'threading', 'sugaring'],
                    'makeup' => ['makeup', 'makeup-artist', 'visagie', 'bridal-makeup', 'permanent-makeup', 'microblading', 'eyelash-extensions', 'wimperextensions', 'lash-lift', 'brow-lamination'],
                    'wellness' => ['spa', 'day-spa', 'wellness', 'wellness-center', 'hammam', 'sauna', 'infrared-sauna', 'float-therapy'],
                    'bruinen' => ['tanning-salon', 'sunbed', 'zonnestudio', 'spray-tan', 'self-tan'],
                    'medisch' => ['botox', 'fillers', 'huidtherapeut', 'cosmetisch-arts', 'physiotherapy'],
                    'alternatief' => ['acupuncture', 'acupunctuur', 'ayurveda', 'reiki', 'meditation', 'yoga-studio', 'pilates', 'holistic-therapy'],
                ];
                foreach ($categories as $cat):
                    $slug = strtolower($cat['slug'] ?? '');
                    $icon = $iconMap[$slug] ?? 'spa';
                    $catGroup = 'overig';
                    foreach ($groupMapping as $g => $slugs) {
                        if (in_array($slug, $slugs)) { $catGroup = $g; break; }
                    }
                    $isSelected = $category == $cat['id'];
                ?>
                    <label class="filter-cat-item <?= $isSelected ? 'selected' : '' ?>"
                           data-group="<?= $catGroup ?>"
                           data-name="<?= htmlspecialchars(strtolower($cat['translated_name'] ?? $cat['name'])) ?>">
                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= $isSelected ? 'checked' : '' ?>>
                        <i class="fas fa-<?= $icon ?>"></i>
                        <span><?= htmlspecialchars($cat['translated_name'] ?? $cat['name']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:1.5rem;text-align:center">
                <button type="button" class="search-bar-btn" onclick="applyFilters()" style="display:inline-flex">
                    <i class="fas fa-search"></i> Filters toepassen
                </button>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Results Container - wider for business cards -->
    <div class="results-container">
        <!-- Quick Category Pills -->
        <div class="category-pills">
            <a href="/search" class="category-pill <?= empty($category) && empty($_GET['group']) ? 'active' : '' ?>">
                <i class="fas fa-th"></i> Alles
            </a>
        <?php
        $quickGroups = [
            'haar' => 'Haar', 'nagels' => 'Nagels', 'huid' => 'Huid',
            'lichaam' => 'Lichaam', 'ontharing' => 'Ontharing', 'makeup' => 'Make-up',
            'wellness' => 'Wellness', 'bruinen' => 'Bruinen', 'medisch' => 'Medisch', 'alternatief' => 'Alternatief'
        ];
        foreach ($quickGroups as $grpSlug => $grpName): ?>
            <a href="/search?group=<?= $grpSlug ?><?= !empty($location) ? '&location=' . urlencode($location) : '' ?>"
               class="category-pill <?= ($_GET['group'] ?? '') === $grpSlug ? 'active' : '' ?>">
                <?= $grpName ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($businesses)): ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Geen salons gevonden</h3>
            <p>Probeer een andere zoekopdracht of locatie</p>
            <a href="/search" class="btn">
                <i class="fas fa-redo"></i> Zoeken resetten
            </a>
        </div>
    <?php else: ?>
        <!-- Results Header -->
        <div class="results-header">
            <h2 class="results-title">
                <i class="fas fa-store"></i>
                <?= !empty($query) ? 'Resultaten voor "' . htmlspecialchars($query) . '"' : 'Beschikbare salons' ?>
                <span class="results-count"><?= count($businesses) ?></span>
            </h2>
            <div class="results-sort">
                <label for="sort">Sorteer:</label>
                <select id="sort" onchange="sortResults(this.value)">
                    <option value="rating" <?= ($_GET['sort'] ?? '') === 'rating' ? 'selected' : '' ?>>Beste beoordeeld</option>
                    <option value="reviews" <?= ($_GET['sort'] ?? '') === 'reviews' ? 'selected' : '' ?>>Meeste reviews</option>
                    <option value="name" <?= ($_GET['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Naam A-Z</option>
                </select>
            </div>
        </div>

        <!-- Business Grid -->
        <div class="business-grid">
            <?php foreach ($businesses as $biz):
                $rating = round($biz['avg_rating'] ?? 0, 1);
                $fullStars = floor($rating);
                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                $isNew = isset($biz['created_at']) && strtotime($biz['created_at']) > strtotime('-30 days');
                $isPopular = ($biz['review_count'] ?? 0) >= 10;
            ?>
                <a href="/business/<?= htmlspecialchars($biz['slug']) ?>" class="biz-card">
                    <!-- Image -->
                    <div class="biz-card-img">
                        <?php if (!empty($biz['logo'])): ?>
                            <img src="/uploads/businesses/<?= htmlspecialchars($biz['logo']) ?>"
                                 alt="<?= htmlspecialchars($biz['name']) ?>">
                        <?php else: ?>
                            <div class="biz-card-img-placeholder">
                                <i class="fas fa-spa"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Badges -->
                        <div class="biz-card-badges">
                            <div>
                                <?php if ($biz['is_verified'] ?? false): ?>
                                    <span class="biz-badge biz-badge-verified">
                                        <i class="fas fa-check"></i> Geverifieerd
                                    </span>
                                <?php elseif ($isNew): ?>
                                    <span class="biz-badge biz-badge-new">Nieuw</span>
                                <?php elseif ($isPopular): ?>
                                    <span class="biz-badge biz-badge-popular">
                                        <i class="fas fa-fire"></i> Populair
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($biz['category_name'])): ?>
                                <span class="biz-card-category"><?= htmlspecialchars($biz['category_name']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="biz-card-body">
                        <h3 class="biz-card-name"><?= htmlspecialchars($biz['name']) ?></h3>

                        <?php if (!empty($biz['city'])): ?>
                            <div class="biz-card-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($biz['city']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Rating -->
                        <div class="biz-card-rating">
                            <div class="biz-card-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= $i > $fullStars ? 'empty' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="biz-card-rating-text">
                                <span class="biz-card-rating-score"><?= number_format($rating, 1) ?></span>
                                (<?= $biz['review_count'] ?? 0 ?> reviews)
                            </span>
                        </div>

                        <!-- Services Preview -->
                        <?php if (!empty($biz['services_preview'])): ?>
                            <div class="biz-card-services">
                                <?php
                                $services = explode(',', $biz['services_preview']);
                                foreach (array_slice($services, 0, 3) as $service): ?>
                                    <span class="biz-service-tag"><?= htmlspecialchars(trim($service)) ?></span>
                                <?php endforeach; ?>
                                <?php if (count($services) > 3): ?>
                                    <span class="biz-service-tag">+<?= count($services) - 3 ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Footer -->
                        <div class="biz-card-footer">
                            <?php if (!empty($biz['min_price'])): ?>
                                <span class="biz-card-price">
                                    Vanaf <strong>&euro;<?= number_format($biz['min_price'], 0) ?></strong>
                                </span>
                            <?php else: ?>
                                <span class="biz-card-price"></span>
                            <?php endif; ?>
                            <span class="biz-card-cta">Bekijk</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</div>

<script>
function sortResults(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', value);
    window.location.href = url.toString();
}

// Toggle advanced filters panel
function toggleFilters(btn) {
    const content = document.getElementById('filters-content');
    btn.classList.toggle('active');
    content.classList.toggle('show');
}

// Filter category list by search
function filterCategoryList() {
    const query = document.getElementById('cat-filter-search').value.toLowerCase().trim();
    const items = document.querySelectorAll('.filter-cat-item');
    const activeGroup = document.querySelector('.filter-group-btn.active')?.dataset.group || 'all';

    items.forEach(item => {
        const name = item.dataset.name || '';
        const group = item.dataset.group || '';
        const matchesSearch = !query || name.includes(query);
        const matchesGroup = activeGroup === 'all' || group === activeGroup;
        item.style.display = (matchesSearch && matchesGroup) ? '' : 'none';
    });
}

// Select a group
function selectGroup(group) {
    document.querySelectorAll('.filter-group-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.group === group);
    });
    filterCategoryList();
}

// Toggle category selection
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.filter-cat-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT') {
                const checkbox = this.querySelector('input');
                checkbox.checked = !checkbox.checked;
            }
            this.classList.toggle('selected', this.querySelector('input').checked);
        });
    });

    // Open filters if group is set
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('group')) {
        const btn = document.querySelector('.filters-toggle');
        if (btn) toggleFilters(btn);
    }
});

// Apply filters
function applyFilters() {
    const url = new URL(window.location.href);
    const selected = [];
    document.querySelectorAll('.filter-cat-item input:checked').forEach(input => {
        selected.push(input.value);
    });

    url.searchParams.delete('category');
    url.searchParams.delete('group');

    if (selected.length === 1) {
        url.searchParams.set('category', selected[0]);
    } else if (selected.length > 1) {
        url.searchParams.set('categories', selected.join(','));
    }

    window.location.href = url.toString();
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
