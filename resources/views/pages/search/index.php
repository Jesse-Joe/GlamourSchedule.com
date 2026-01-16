<?php ob_start(); ?>

<style>
/* Search Page - Dark Theme */
.search-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Desktop: wider search card */
@media (min-width: 1024px) {
    .search-page {
        max-width: 900px;
    }
}
@media (min-width: 1400px) {
    .search-page {
        max-width: 1100px;
    }
}

/* Search Card */
.search-card {
    background: #000000;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    border: 2px solid #333333;
    margin-bottom: 1.5rem;
}
.search-header {
    background: #000000;
    color: #ffffff;
    padding: 2rem;
    text-align: center;
    border-bottom: 2px solid #333333;
}
@media (min-width: 1024px) {
    .search-header {
        padding: 3rem 2rem;
    }
    .search-header i {
        font-size: 3.5rem;
        margin-bottom: 1rem;
    }
    .search-header h1 {
        font-size: 2rem;
    }
    .search-header p {
        font-size: 1.1rem;
    }
}
.search-header i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    display: block;
    color: #ffffff;
}
.search-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
}
.search-header p {
    margin: 0.5rem 0 0 0;
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.8);
}
.search-body {
    padding: 2rem;
    background: #000000;
}
@media (min-width: 1024px) {
    .search-body {
        padding: 2.5rem 3rem;
    }
}

/* Search Bar */
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
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
}
@media (min-width: 768px) {
    .search-bar-row {
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
}
@media (min-width: 1024px) {
    .search-bar-row {
        grid-template-columns: 2fr 1fr 1fr;
        gap: 2rem;
    }
}
.search-bar-input {
    position: relative;
}
.search-bar-input i {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    color: #ffffff;
}
.search-bar-input input {
    width: 100%;
    padding: 0.85rem 0 0.85rem 1.75rem;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: #ffffff;
    transition: all 0.3s ease;
}
.search-bar-input input:focus {
    outline: none;
    border-bottom-color: #ffffff;
    box-shadow: none;
}
.search-bar-input input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}
.search-bar-select select,
.search-bar-select input {
    width: 100%;
    padding: 0.85rem 0;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: #ffffff;
    appearance: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
.search-bar-select select:focus,
.search-bar-select input:focus {
    outline: none;
    border-bottom-color: #ffffff;
    box-shadow: none;
}
.search-bar-select select::placeholder,
.search-bar-select input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}
.search-bar-select select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0 center;
}
.search-bar-select select option {
    background: #000000;
    color: #ffffff;
}
.search-bar-btn {
    width: 100%;
    padding: 1rem;
    background: #ffffff;
    color: #000000;
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
@media (min-width: 1024px) {
    .search-bar-btn {
        width: auto;
        padding: 1rem 2.5rem;
        margin-top: 1rem;
        align-self: flex-start;
    }
}
.search-bar-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
}

/* Advanced Filters Panel */
.filters-panel {
    background: transparent;
    border: none;
    border-radius: 0;
    margin-bottom: 0;
    overflow: hidden;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
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
    color: #ffffff;
}
.filters-toggle i {
    transition: transform 0.3s;
    color: #ffffff;
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
/* Filter Sections Grid */
.filter-sections {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
@media (min-width: 768px) {
    .filter-sections {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }
}
@media (min-width: 1200px) {
    .filter-sections {
        grid-template-columns: repeat(4, 1fr);
    }
}
.filter-section h5 {
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 0.75rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.filter-section h5 i {
    opacity: 0.7;
}

/* Price Range */
.price-range {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.price-input-group {
    flex: 1;
}
.price-input-group label {
    display: block;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.25rem;
}
.price-input-group input {
    width: 100%;
    padding: 0.6rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: #ffffff;
    font-size: 0.9rem;
}
.price-input-group input:focus {
    outline: none;
    border-color: #ffffff;
}
.price-input-group input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}
.price-separator {
    color: rgba(255, 255, 255, 0.5);
    font-weight: 600;
    padding-top: 1.25rem;
}

/* Availability */
.availability-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.availability-options input[type="date"],
.availability-options select {
    width: 100%;
    padding: 0.6rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: #ffffff;
    font-size: 0.9rem;
}
.availability-options input[type="date"]:focus,
.availability-options select:focus {
    outline: none;
    border-color: #ffffff;
}
.availability-options select option {
    background: #000000;
    color: #ffffff;
}

/* Filter Checkboxes */
.opening-options,
.extra-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.filter-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.8);
}
.filter-checkbox:hover {
    border-color: rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.1);
}
.filter-checkbox.selected {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.5);
}
.filter-checkbox input {
    display: none;
}
.filter-checkbox i {
    font-size: 0.7rem;
    opacity: 0;
    transition: opacity 0.2s;
}
.filter-checkbox.selected i {
    opacity: 1;
}

.filter-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.15);
    margin: 1rem 0 1.5rem;
}

.filter-search {
    margin-bottom: 1.5rem;
}
.filter-search input {
    width: 100%;
    padding: 0.85rem 0;
    border: none;
    border-bottom: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 0;
    font-size: 0.95rem;
    background: transparent;
    color: #ffffff;
    transition: all 0.3s ease;
}
.filter-search input:focus {
    outline: none;
    border-bottom-color: #ffffff;
}
.filter-search input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}
.filter-groups {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
@media (min-width: 1024px) {
    .filter-groups {
        gap: 0.75rem;
        margin-bottom: 2rem;
    }
}
.filter-group-btn {
    padding: 0.5rem 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
@media (min-width: 1024px) {
    .filter-group-btn {
        padding: 0.65rem 1.25rem;
        font-size: 0.9rem;
    }
}
.filter-group-btn:hover,
.filter-group-btn.active {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
}
.filter-categories {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 0.5rem;
}
@media (min-width: 1024px) {
    .filter-categories {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
    }
}
.filter-cat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}
@media (min-width: 1024px) {
    .filter-cat-item {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
    }
}
.filter-cat-item:hover {
    border-color: #ffffff;
    color: #ffffff;
}
.filter-cat-item.selected {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
}
.filter-cat-item input {
    display: none;
}
.filter-cat-item i {
    width: 20px;
    text-align: center;
}

/* Results Container */
.results-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
    background: #000000;
}
@media (min-width: 1024px) {
    .results-container {
        max-width: 1600px;
        padding: 3rem 2rem;
    }
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
@media (min-width: 1024px) {
    .category-pills {
        gap: 0.75rem;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
}
.category-pills::-webkit-scrollbar {
    display: none;
}
.category-pill {
    flex-shrink: 0;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s;
    border: 2px solid rgba(255, 255, 255, 0.3);
}
@media (min-width: 1024px) {
    .category-pill {
        padding: 0.65rem 1.5rem;
        font-size: 0.95rem;
    }
}
.category-pill:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: #ffffff;
    color: #ffffff;
}
.category-pill.active {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
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
@media (min-width: 1024px) {
    .results-header {
        margin-bottom: 2rem;
        padding: 0;
    }
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
@media (min-width: 1024px) {
    .results-title {
        font-size: 1.5rem;
        gap: 1rem;
    }
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
@media (min-width: 1024px) {
    .results-count {
        padding: 0.35rem 1rem;
        font-size: 0.9rem;
    }
}
.results-sort {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.results-sort label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
}
.results-sort select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
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
.results-sort select option {
    background: #000000;
    color: #ffffff;
}

/* Business Grid - 3 columns on desktop */
.business-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 640px) {
    .business-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .business-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
}

/* Business Card - Dark Theme */
.biz-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    border: 2px solid rgba(255, 255, 255, 0.2);
}
.biz-card:hover {
    transform: translateY(-4px);
    border-color: #ffffff;
    box-shadow: 0 15px 40px rgba(255, 255, 255, 0.1);
}

/* Card Image */
.biz-card-img {
    position: relative;
    height: 180px;
    background: rgba(255, 255, 255, 0.08);
    overflow: hidden;
}
@media (min-width: 1024px) {
    .biz-card-img {
        height: 220px;
    }
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
    color: rgba(255, 255, 255, 0.3);
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
    background: #ffffff;
    color: #000000;
}
.biz-badge-new {
    background: #fbbf24;
    color: #000000;
}
.biz-badge-popular {
    background: #ef4444;
    color: #ffffff;
}
.biz-card-category {
    background: rgba(0, 0, 0, 0.7);
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
@media (min-width: 1024px) {
    .biz-card-body {
        padding: 1.25rem;
    }
}
.biz-card-name {
    font-size: 1.05rem;
    font-weight: 700;
    margin: 0 0 0.5rem;
    color: #ffffff;
    line-height: 1.3;
}
@media (min-width: 1024px) {
    .biz-card-name {
        font-size: 1.15rem;
        margin-bottom: 0.6rem;
    }
}
.biz-card-location {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    margin-bottom: 0.75rem;
}
.biz-card-location i {
    color: rgba(255, 255, 255, 0.5);
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
    color: #fbbf24;
}
.biz-card-stars i.empty {
    color: rgba(255, 255, 255, 0.3);
}
.biz-card-rating-text {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
}
.biz-card-rating-score {
    font-weight: 700;
    color: #ffffff;
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
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.8);
}

/* Price */
.biz-card-footer {
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.biz-card-price {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
}
.biz-card-price strong {
    font-size: 1.1rem;
    color: #ffffff;
}
.biz-card-cta {
    padding: 0.5rem 1rem;
    background: #ffffff;
    color: #000000;
    border: none;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s;
}
.biz-card:hover .biz-card-cta {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.05);
    border: 2px dashed rgba(255, 255, 255, 0.2);
    border-radius: 16px;
}
.empty-state-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
}
.empty-state-icon i {
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.5);
}
.empty-state h3 {
    margin: 0 0 0.5rem;
    font-size: 1.25rem;
    color: #ffffff;
}
.empty-state p {
    color: rgba(255, 255, 255, 0.7);
    margin: 0 0 1.5rem;
}
.empty-state .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #ffffff;
    color: #000000;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}
.empty-state .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
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
        border-left: none;
        border-right: none;
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
        margin: 0 1rem;
    }
    .filter-groups {
        justify-content: flex-start;
    }
    .results-container {
        padding: 1.5rem 0;
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

            <!-- Filter Sections Grid -->
            <div class="filter-sections">
                <!-- Price Filter -->
                <div class="filter-section">
                    <h5><i class="fas fa-euro-sign"></i> Prijs</h5>
                    <div class="price-range">
                        <div class="price-input-group">
                            <label>Min</label>
                            <input type="number" id="price-min" name="price_min" placeholder="€0" min="0" value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>" onchange="applyFilters()">
                        </div>
                        <span class="price-separator">-</span>
                        <div class="price-input-group">
                            <label>Max</label>
                            <input type="number" id="price-max" name="price_max" placeholder="€500" min="0" value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>" onchange="applyFilters()">
                        </div>
                    </div>
                </div>

                <!-- Availability Filter -->
                <div class="filter-section">
                    <h5><i class="fas fa-calendar-alt"></i> Beschikbaarheid</h5>
                    <div class="availability-options">
                        <input type="date" id="filter-date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" min="<?= date('Y-m-d') ?>" onchange="applyFilters()">
                        <select id="filter-time" name="time_slot" onchange="applyFilters()">
                            <option value="">Elk tijdstip</option>
                            <option value="morning" <?= ($_GET['time_slot'] ?? '') === 'morning' ? 'selected' : '' ?>>Ochtend (9:00-12:00)</option>
                            <option value="afternoon" <?= ($_GET['time_slot'] ?? '') === 'afternoon' ? 'selected' : '' ?>>Middag (12:00-17:00)</option>
                            <option value="evening" <?= ($_GET['time_slot'] ?? '') === 'evening' ? 'selected' : '' ?>>Avond (17:00-21:00)</option>
                        </select>
                    </div>
                </div>

                <!-- Opening Hours Filter -->
                <div class="filter-section">
                    <h5><i class="fas fa-clock"></i> Open op</h5>
                    <div class="opening-options">
                        <label class="filter-checkbox <?= isset($_GET['open_now']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="open_now" value="1" <?= isset($_GET['open_now']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span>Nu geopend</span>
                        </label>
                        <label class="filter-checkbox <?= isset($_GET['open_weekend']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="open_weekend" value="1" <?= isset($_GET['open_weekend']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span>Open in weekend</span>
                        </label>
                        <label class="filter-checkbox <?= isset($_GET['open_evening']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="open_evening" value="1" <?= isset($_GET['open_evening']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span>Avondopening</span>
                        </label>
                    </div>
                </div>

                <!-- Extra Options -->
                <div class="filter-section">
                    <h5><i class="fas fa-star"></i> Extra</h5>
                    <div class="extra-options">
                        <label class="filter-checkbox <?= isset($_GET['high_rated']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="high_rated" value="1" <?= isset($_GET['high_rated']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span>4+ sterren</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="filter-divider"></div>

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

    <!-- Results Container -->
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
                    <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Prijs laag-hoog</option>
                    <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Prijs hoog-laag</option>
                    <option value="name" <?= ($_GET['sort'] ?? '') === 'name' ? 'selected' : '' ?>>Naam A-Z</option>
                    <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Nieuwste eerst</option>
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
                            <?php
                            $logoUrl = $biz['logo'];
                            // Check if it's an external URL or local path
                            if (!str_starts_with($logoUrl, 'http://') && !str_starts_with($logoUrl, 'https://')) {
                                $logoUrl = '/uploads/businesses/' . $logoUrl;
                            }
                            ?>
                            <img src="<?= htmlspecialchars($logoUrl) ?>"
                                 alt="<?= htmlspecialchars($biz['name']) ?>">
                        <?php else: ?>
                            <div class="biz-card-img-placeholder">
                                <i class="fas fa-spa"></i>
                            </div>
                        <?php endif; ?>

                        <!-- Badges -->
                        <div class="biz-card-badges">
                            <div>
                                <?php if ($isNew): ?>
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
    // Category items
    document.querySelectorAll('.filter-cat-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT') {
                const checkbox = this.querySelector('input');
                checkbox.checked = !checkbox.checked;
            }
            this.classList.toggle('selected', this.querySelector('input').checked);
        });
    });

    // Filter checkboxes (opening hours, extra options) - auto apply on click
    document.querySelectorAll('.filter-checkbox').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const checkbox = this.querySelector('input');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected', checkbox.checked);
            // Auto apply filters after short delay
            setTimeout(() => applyFilters(), 100);
        });
    });

    // Open filters if any filter is set
    const urlParams = new URLSearchParams(window.location.search);
    const filterParams = ['group', 'price_min', 'price_max', 'date', 'time_slot', 'open_now', 'open_weekend', 'open_evening', 'high_rated'];
    const hasFilters = filterParams.some(p => urlParams.get(p));
    if (hasFilters) {
        const btn = document.querySelector('.filters-toggle');
        if (btn) toggleFilters(btn);
    }
});

// Apply filters
function applyFilters() {
    const url = new URL(window.location.href);

    // Clear existing filter params
    url.searchParams.delete('category');
    url.searchParams.delete('categories');
    url.searchParams.delete('group');
    url.searchParams.delete('price_min');
    url.searchParams.delete('price_max');
    url.searchParams.delete('date');
    url.searchParams.delete('time_slot');
    url.searchParams.delete('open_now');
    url.searchParams.delete('open_weekend');
    url.searchParams.delete('open_evening');
    url.searchParams.delete('high_rated');

    // Categories
    const selectedCats = [];
    document.querySelectorAll('.filter-cat-item input:checked').forEach(input => {
        selectedCats.push(input.value);
    });
    if (selectedCats.length === 1) {
        url.searchParams.set('category', selectedCats[0]);
    } else if (selectedCats.length > 1) {
        url.searchParams.set('categories', selectedCats.join(','));
    }

    // Price range
    const priceMin = document.getElementById('price-min')?.value;
    const priceMax = document.getElementById('price-max')?.value;
    if (priceMin) url.searchParams.set('price_min', priceMin);
    if (priceMax) url.searchParams.set('price_max', priceMax);

    // Date/time
    const date = document.getElementById('filter-date')?.value;
    const timeSlot = document.getElementById('filter-time')?.value;
    if (date) url.searchParams.set('date', date);
    if (timeSlot) url.searchParams.set('time_slot', timeSlot);

    // Checkboxes
    document.querySelectorAll('.filter-checkbox input:checked').forEach(input => {
        url.searchParams.set(input.name, '1');
    });

    window.location.href = url.toString();
}

// Reset all filters
function resetFilters() {
    window.location.href = '/search';
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
