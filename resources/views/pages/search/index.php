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

/* Location Button */
.location-btn {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.6);
    cursor: pointer;
    padding: 0.5rem;
    transition: all 0.2s;
}
.location-btn:hover {
    color: #ffffff;
}
.location-btn.loading i {
    animation: spin 1s linear infinite;
}
.location-btn.success {
    color: #10b981;
}
@keyframes spin {
    from { transform: translateY(-50%) rotate(0deg); }
    to { transform: translateY(-50%) rotate(360deg); }
}

/* Nearby Button - Prominent CTA */
.nearby-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    width: 100%;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #ffffff;
    border: none;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
}
.nearby-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
}
.nearby-btn.loading {
    opacity: 0.8;
    cursor: wait;
}
.nearby-btn.loading i.fa-location-arrow {
    display: none;
}
.nearby-btn.loading i.fa-spinner {
    display: inline-block;
}
.nearby-btn i.fa-spinner {
    display: none;
    animation: spin 1s linear infinite;
}
.nearby-btn.active {
    background: linear-gradient(135deg, #10b981, #059669);
}
.nearby-btn.active i.fa-location-arrow::before {
    content: "\f00c";
}
@media (min-width: 768px) {
    .nearby-btn {
        width: auto;
        padding: 1rem 2rem;
    }
}
@keyframes pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); }
    50% { transform: scale(1.02); box-shadow: 0 8px 30px rgba(59, 130, 246, 0.6); }
}

/* Distance Badge */
.biz-card-distance {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.5rem;
    background: rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 6px;
    font-size: 0.75rem;
    color: #60a5fa;
    margin-left: 0.5rem;
}
.biz-card-distance i {
    font-size: 0.7rem;
}

/* Location Permission Banner */
.location-banner {
    background: linear-gradient(135deg, #1e3a5f, #2d4a6f);
    border: 2px solid #3b82f6;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #ffffff;
}
.location-banner.hidden {
    display: none;
}
.location-banner-icon {
    width: 50px;
    height: 50px;
    background: rgba(59, 130, 246, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.location-banner-icon i {
    font-size: 1.5rem;
    color: #60a5fa;
}
.location-banner-content {
    flex: 1;
}
.location-banner-content h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    font-weight: 600;
}
.location-banner-content p {
    margin: 0;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.8);
}
.location-banner-btn {
    padding: 0.75rem 1.25rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.location-banner-btn:hover {
    background: #2563eb;
    transform: translateY(-2px);
}
.location-banner-btn.loading {
    opacity: 0.7;
    pointer-events: none;
}
.location-banner-btn.loading i {
    animation: spin 1s linear infinite;
}
.location-banner-close {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    padding: 0.5rem;
    font-size: 1.25rem;
    transition: color 0.2s;
}
.location-banner-close:hover {
    color: #ffffff;
}
@media (max-width: 640px) {
    .location-banner {
        flex-wrap: wrap;
        text-align: center;
        justify-content: center;
    }
    .location-banner-content {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    .location-banner-close {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
    }
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

/* Route Button */
.biz-card-route {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    background: rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(59, 130, 246, 0.4);
    border-radius: 50%;
    color: #60a5fa;
    font-size: 0.8rem;
    text-decoration: none;
    transition: all 0.2s;
}
.biz-card-route:hover {
    background: #3b82f6;
    color: #ffffff;
    border-color: #3b82f6;
    transform: translateY(-2px);
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

/* Salon Map */
.salon-map-section {
    max-width: 1400px;
    margin: 0 auto 2rem;
    padding: 0 1rem;
}
@media (min-width: 1024px) {
    .salon-map-section {
        max-width: 1600px;
        padding: 0 2rem;
    }
}
.salon-map-card {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    overflow: hidden;
}
.salon-map-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}
.salon-map-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.15rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}
.salon-map-title i {
    color: #60a5fa;
}
.country-filter-btns {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}
.country-filter-btn {
    padding: 0.4rem 0.9rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.country-filter-btn:hover {
    border-color: #ffffff;
    color: #ffffff;
}
.country-filter-btn.active {
    background: #ffffff;
    color: #000000;
    border-color: #ffffff;
}
#salon-map {
    height: 450px;
    background: #111111;
}
@media (min-width: 1024px) {
    #salon-map {
        height: 550px;
    }
}
@media (max-width: 768px) {
    .salon-map-section {
        padding: 0;
    }
    .salon-map-card {
        border-radius: 0;
        border-left: none;
        border-right: none;
    }
    #salon-map {
        height: 350px;
    }
    .salon-map-header {
        flex-direction: column;
        align-items: flex-start;
    }
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
            <h1><?= $translations['search_salons_title'] ?? 'Search Salons' ?></h1>
            <p><?= $translations['search_find_perfect'] ?? 'Find the perfect salon near you' ?></p>
        </div>

        <div class="search-body">
            <!-- Nearby Button - Primary CTA -->
            <button type="button" class="nearby-btn <?= !empty($userLat) ? 'active' : '' ?>" onclick="searchNearby()" id="nearby-btn">
                <i class="fas fa-location-arrow"></i>
                <i class="fas fa-spinner"></i>
                <span id="nearby-btn-text"><?= !empty($userLat) ? ($translations['showing_nearby'] ?? 'Showing nearby salons') : ($translations['find_nearby'] ?? 'Find salons near me') ?></span>
            </button>

            <!-- Search Bar -->
            <div class="search-bar">
                <form method="GET" action="/search" class="search-bar-form" id="search-form">
                    <div class="search-bar-row">
                        <div class="search-bar-input">
                            <i class="fas fa-search"></i>
                            <input type="text" name="q" placeholder="<?= $translations['search_salon_treatment'] ?? 'Search salon, treatment...' ?>"
                                   value="<?= htmlspecialchars($query) ?>">
                        </div>
                        <div class="search-bar-select">
                            <select name="category">
                                <option value=""><?= $translations['all_categories'] ?? 'All categories' ?></option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $category == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['translated_name'] ?? $cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="search-bar-select" style="position: relative;">
                            <input type="text" name="location" placeholder="<?= $translations['city_or_postal'] ?? 'City or postal code' ?>"
                                   value="<?= htmlspecialchars($location) ?>" id="location-input">
                            <button type="button" class="location-btn" onclick="useMyLocation()" title="<?= $translations['use_my_location'] ?? 'Use my location' ?>">
                                <i class="fas fa-crosshairs"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="lat" id="user-lat" value="<?= htmlspecialchars($userLat ?? '') ?>" data-source="<?= htmlspecialchars($locationSource ?? 'none') ?>">
                    <input type="hidden" name="lng" id="user-lng" value="<?= htmlspecialchars($userLng ?? '') ?>" data-source="<?= htmlspecialchars($locationSource ?? 'none') ?>">
                    <button type="submit" class="search-bar-btn">
                        <i class="fas fa-search"></i>
                        <span><?= $translations['search'] ?? 'Search' ?></span>
                    </button>
                </form>
            </div>

            <!-- Advanced Filters Panel -->
            <div class="filters-panel">
                <button class="filters-toggle" onclick="toggleFilters(this)">
                    <span><i class="fas fa-sliders-h"></i> <?= $translations['advanced_search'] ?? 'Advanced search' ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="filters-content" id="filters-content">

            <!-- Filter Sections Grid -->
            <div class="filter-sections">
                <!-- Price Filter -->
                <div class="filter-section">
                    <h5><i class="fas fa-euro-sign"></i> <?= $translations['price_label'] ?? 'Price' ?></h5>
                    <div class="price-range">
                        <div class="price-input-group">
                            <label><?= $translations['min_label'] ?? 'Min' ?></label>
                            <input type="number" id="price-min" name="price_min" placeholder="€0" min="0" value="<?= htmlspecialchars($_GET['price_min'] ?? '') ?>" onchange="applyFilters()">
                        </div>
                        <span class="price-separator">-</span>
                        <div class="price-input-group">
                            <label><?= $translations['max_label'] ?? 'Max' ?></label>
                            <input type="number" id="price-max" name="price_max" placeholder="€500" min="0" value="<?= htmlspecialchars($_GET['price_max'] ?? '') ?>" onchange="applyFilters()">
                        </div>
                    </div>
                </div>

                <!-- Availability Filter -->
                <div class="filter-section">
                    <h5><i class="fas fa-calendar-alt"></i> <?= $translations['availability'] ?? 'Availability' ?></h5>
                    <div class="availability-options">
                        <input type="date" id="filter-date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" min="<?= date('Y-m-d') ?>" onchange="applyFilters()">
                        <select id="filter-time" name="time_slot" onchange="applyFilters()">
                            <option value=""><?= $translations['any_time'] ?? 'Any time' ?></option>
                            <option value="morning" <?= ($_GET['time_slot'] ?? '') === 'morning' ? 'selected' : '' ?>><?= $translations['morning'] ?? 'Morning (9:00-12:00)' ?></option>
                            <option value="afternoon" <?= ($_GET['time_slot'] ?? '') === 'afternoon' ? 'selected' : '' ?>><?= $translations['afternoon'] ?? 'Afternoon (12:00-17:00)' ?></option>
                            <option value="evening" <?= ($_GET['time_slot'] ?? '') === 'evening' ? 'selected' : '' ?>><?= $translations['evening'] ?? 'Evening (17:00-21:00)' ?></option>
                        </select>
                    </div>
                </div>

                <!-- Opening Hours Filter -->
                <div class="filter-section">
                    <h5><i class="fas fa-clock"></i> <?= $translations['open_on'] ?? 'Open on' ?></h5>
                    <div class="opening-options">
                        <label class="filter-checkbox <?= isset($_GET['open_now']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="open_now" value="1" <?= isset($_GET['open_now']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span><?= $translations['open_now'] ?? 'Open now' ?></span>
                        </label>
                        <label class="filter-checkbox <?= isset($_GET['open_weekend']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="open_weekend" value="1" <?= isset($_GET['open_weekend']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span><?= $translations['open_weekend'] ?? 'Open on weekends' ?></span>
                        </label>
                        <label class="filter-checkbox <?= isset($_GET['open_evening']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="open_evening" value="1" <?= isset($_GET['open_evening']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span><?= $translations['evening_opening'] ?? 'Evening opening' ?></span>
                        </label>
                    </div>
                </div>

                <!-- Extra Options -->
                <div class="filter-section">
                    <h5><i class="fas fa-star"></i> <?= $translations['extra'] ?? 'Extra' ?></h5>
                    <div class="extra-options">
                        <label class="filter-checkbox <?= isset($_GET['high_rated']) ? 'selected' : '' ?>">
                            <input type="checkbox" name="high_rated" value="1" <?= isset($_GET['high_rated']) ? 'checked' : '' ?>>
                            <i class="fas fa-check"></i>
                            <span><?= $translations['four_plus_stars'] ?? '4+ stars' ?></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="filter-divider"></div>

            <!-- Search within categories -->
            <div class="filter-search">
                <input type="text" id="cat-filter-search" placeholder="<?= $translations['search_specific_category'] ?? 'Search specific category...' ?>" oninput="filterCategoryList()">
            </div>

            <!-- Group buttons -->
            <div class="filter-groups">
                <?php
                $groups = [
                    'all' => ['name' => $translations['group_all'] ?? 'All', 'icon' => 'th'],
                    'haar' => ['name' => $translations['group_hair'] ?? 'Hair', 'icon' => 'cut'],
                    'nagels' => ['name' => $translations['group_nails'] ?? 'Nails', 'icon' => 'hand-sparkles'],
                    'huid' => ['name' => $translations['group_skin'] ?? 'Skin', 'icon' => 'spa'],
                    'lichaam' => ['name' => $translations['group_body'] ?? 'Body', 'icon' => 'hands'],
                    'ontharing' => ['name' => $translations['group_hairremoval'] ?? 'Hair removal', 'icon' => 'feather'],
                    'makeup' => ['name' => $translations['group_makeup'] ?? 'Make-up', 'icon' => 'paint-brush'],
                    'wellness' => ['name' => $translations['group_wellness'] ?? 'Wellness', 'icon' => 'hot-tub'],
                    'bruinen' => ['name' => $translations['group_tanning'] ?? 'Tanning', 'icon' => 'sun'],
                    'medisch' => ['name' => $translations['group_medical'] ?? 'Medical', 'icon' => 'user-md'],
                    'alternatief' => ['name' => $translations['group_alternative'] ?? 'Alternative', 'icon' => 'yin-yang'],
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
                    <i class="fas fa-search"></i> <?= $translations['apply_filters'] ?? 'Apply filters' ?>
                </button>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Results Container -->
    <div class="results-container">
        <!-- Location Permission Banner (hidden since we have prominent nearby button) -->
        <div class="location-banner hidden" id="location-banner">
            <div class="location-banner-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="location-banner-content">
                <h4><i class="fas fa-location-arrow"></i> <?= $translations['find_salons_nearby'] ?? 'Find salons near you' ?></h4>
                <p><?= $translations['enable_location_desc'] ?? 'Enable location to see nearest salons with distance' ?></p>
            </div>
            <button class="location-banner-btn" onclick="requestLocationPermission()" id="location-banner-btn">
                <i class="fas fa-crosshairs"></i>
                <span><?= $translations['enable_location'] ?? 'Enable location' ?></span>
            </button>
            <button class="location-banner-close" onclick="dismissLocationBanner()" title="Sluiten">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Quick Category Pills -->
        <div class="category-pills">
            <a href="/search" class="category-pill <?= empty($category) && empty($_GET['group']) ? 'active' : '' ?>">
                <i class="fas fa-th"></i> <?= $translations['group_all'] ?? 'All' ?>
            </a>
        <?php
        $quickGroups = [
            'haar' => $translations['group_hair'] ?? 'Hair', 'nagels' => $translations['group_nails'] ?? 'Nails', 'huid' => $translations['group_skin'] ?? 'Skin',
            'lichaam' => $translations['group_body'] ?? 'Body', 'ontharing' => $translations['group_hairremoval'] ?? 'Hair removal', 'makeup' => $translations['group_makeup'] ?? 'Make-up',
            'wellness' => $translations['group_wellness'] ?? 'Wellness', 'bruinen' => $translations['group_tanning'] ?? 'Tanning', 'medisch' => $translations['group_medical'] ?? 'Medical', 'alternatief' => $translations['group_alternative'] ?? 'Alternative'
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
            <h3><?= $translations['no_salons_found'] ?? 'No salons found' ?></h3>
            <p><?= $translations['try_other_search'] ?? 'Try a different search or location' ?></p>
            <a href="/search" class="btn">
                <i class="fas fa-redo"></i> <?= $translations['reset_search'] ?? 'Reset search' ?>
            </a>
        </div>
    <?php else: ?>
        <!-- Results Header -->
        <div class="results-header">
            <h2 class="results-title">
                <i class="fas fa-store"></i>
                <?= !empty($query) ? ($translations['results_for'] ?? 'Results for') . ' "' . htmlspecialchars($query) . '"' : ($translations['available_salons'] ?? 'Available salons') ?>
                <span class="results-count"><?= count($businesses) ?></span>
            </h2>
            <div class="results-sort">
                <label for="sort"><?= $__('sort_by') ?>:</label>
                <select id="sort" onchange="sortResults(this.value)">
                    <option value="distance" <?= ($_GET['sort'] ?? '') === 'distance' ? 'selected' : '' ?>><?= $__('nearest') ?></option>
                    <option value="rating" <?= ($_GET['sort'] ?? '') === 'rating' ? 'selected' : '' ?>><?= $__('best_rated') ?></option>
                    <option value="reviews" <?= ($_GET['sort'] ?? '') === 'reviews' ? 'selected' : '' ?>><?= $__('most_reviews') ?></option>
                    <option value="price_asc" <?= ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>><?= $__('price_low_high') ?></option>
                    <option value="price_desc" <?= ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>><?= $__('price_high_low') ?></option>
                    <option value="name" <?= ($_GET['sort'] ?? '') === 'name' ? 'selected' : '' ?>><?= $__('name_az') ?></option>
                    <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>><?= $__('newest_first') ?></option>
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
                                    <span class="biz-badge biz-badge-new"><?= $translations['new_label'] ?? 'New' ?></span>
                                <?php elseif ($isPopular): ?>
                                    <span class="biz-badge biz-badge-popular">
                                        <i class="fas fa-fire"></i> <?= $translations['popular_label'] ?? 'Popular' ?>
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
                                <?php if (!empty($biz['distance'])): ?>
                                    <span class="biz-card-distance">
                                        <i class="fas fa-route"></i>
                                        <?= $biz['distance'] ?> km
                                    </span>
                                <?php endif; ?>
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
                                    <?= $translations['from_price'] ?? 'From' ?> <strong>&euro;<?= number_format($biz['min_price'], 0) ?></strong>
                                </span>
                            <?php else: ?>
                                <span class="biz-card-price"></span>
                            <?php endif; ?>
                            <span style="display:flex;align-items:center;gap:0.5rem;">
                                <?php if (!empty($biz['latitude']) && !empty($biz['longitude'])): ?>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $biz['latitude'] ?>,<?= $biz['longitude'] ?>"
                                       target="_blank" rel="noopener" class="biz-card-route" title="<?= $translations['route'] ?? 'Route' ?>"
                                       onclick="event.stopPropagation()">
                                        <i class="fas fa-route"></i>
                                    </a>
                                <?php endif; ?>
                                <span class="biz-card-cta"><?= $translations['view_book'] ?? 'View & Book' ?></span>
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</div>

<!-- Salon World Map -->
<div class="salon-map-section" id="salon-map-section">
    <div class="salon-map-card">
        <div class="salon-map-header">
            <h3 class="salon-map-title"><i class="fas fa-globe-europe"></i> <?= $translations['salon_map_title'] ?? 'Salons on the Map' ?></h3>
            <div class="country-filter-btns">
                <button class="country-filter-btn active" data-country="" onclick="filterMapCountry(this)"><?= $translations['all'] ?? 'All' ?></button>
                <button class="country-filter-btn" data-country="NL" onclick="filterMapCountry(this)">NL</button>
                <button class="country-filter-btn" data-country="BE" onclick="filterMapCountry(this)">BE</button>
                <button class="country-filter-btn" data-country="DE" onclick="filterMapCountry(this)">DE</button>
                <button class="country-filter-btn" data-country="FR" onclick="filterMapCountry(this)">FR</button>
            </div>
        </div>
        <div id="salon-map"></div>
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

// Search nearby - main CTA button
function searchNearby() {
    const btn = document.getElementById('nearby-btn');
    const btnText = document.getElementById('nearby-btn-text');
    const latInput = document.getElementById('user-lat');
    const lngInput = document.getElementById('user-lng');

    // Only skip GPS request if we already have GPS-based coordinates
    if (latInput.value && lngInput.value && latInput.dataset.source === 'gps') {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', 'distance');
        window.location.href = url.toString();
        return;
    }

    if (!navigator.geolocation) {
        alert('<?= $translations['geolocation_not_supported'] ?? 'Geolocation is not supported by your browser' ?>');
        return;
    }

    // Show loading state
    btn.classList.add('loading');
    btnText.textContent = '<?= $translations['getting_location'] ?? 'Getting your location...' ?>';

    navigator.geolocation.getCurrentPosition(
        // Success
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Store in localStorage
            localStorage.setItem('userLat', lat);
            localStorage.setItem('userLng', lng);
            localStorage.setItem('locationPermission', 'granted');

            // Redirect with coordinates and sort by distance
            const url = new URL(window.location.href);
            url.searchParams.set('lat', lat);
            url.searchParams.set('lng', lng);
            url.searchParams.set('sort', 'distance');
            window.location.href = url.toString();
        },
        // Error
        function(error) {
            btn.classList.remove('loading');
            btnText.textContent = '<?= $translations['find_nearby'] ?? 'Find salons near me' ?>';

            let message = '<?= $translations['location_error'] ?? 'Could not determine your location' ?>';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = '<?= $translations['location_denied'] ?? 'Location access denied. Please enable location in your browser settings.' ?>';
                    localStorage.setItem('locationPermission', 'denied');
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = '<?= $translations['location_unavailable'] ?? 'Location not available' ?>';
                    break;
                case error.TIMEOUT:
                    message = '<?= $translations['location_timeout'] ?? 'Location request timed out' ?>';
                    break;
            }
            alert(message);
        },
        // Options
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        }
    );
}

// Geolocation functionality
function useMyLocation() {
    const btn = document.querySelector('.location-btn');
    const locationInput = document.getElementById('location-input');
    const latInput = document.getElementById('user-lat');
    const lngInput = document.getElementById('user-lng');

    if (!navigator.geolocation) {
        alert('Geolocation wordt niet ondersteund door je browser');
        return;
    }

    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-spinner"></i>';

    navigator.geolocation.getCurrentPosition(
        // Success
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            latInput.value = lat;
            lngInput.value = lng;

            btn.classList.remove('loading');
            btn.classList.add('success');
            btn.innerHTML = '<i class="fas fa-check"></i>';

            // Reverse geocode to get city name
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(res => res.json())
                .then(data => {
                    const city = data.address?.city || data.address?.town || data.address?.village || 'Mijn locatie';
                    locationInput.value = city;
                    locationInput.placeholder = city;

                    // Auto-submit the form after getting location
                    setTimeout(() => {
                        document.querySelector('.search-bar-form').submit();
                    }, 500);
                })
                .catch(() => {
                    locationInput.value = 'Mijn locatie';
                    setTimeout(() => {
                        document.querySelector('.search-bar-form').submit();
                    }, 500);
                });
        },
        // Error
        function(error) {
            btn.classList.remove('loading');
            btn.innerHTML = '<i class="fas fa-crosshairs"></i>';

            let message = 'Kon locatie niet bepalen';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Locatietoegang geweigerd. Sta locatie toe in je browser.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Locatie niet beschikbaar';
                    break;
                case error.TIMEOUT:
                    message = 'Locatieverzoek timeout';
                    break;
            }
            alert(message);
        },
        // Options
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000 // 5 minutes cache
        }
    );
}

// Request location permission from banner
function requestLocationPermission() {
    const btn = document.getElementById('location-banner-btn');
    const banner = document.getElementById('location-banner');

    if (!navigator.geolocation) {
        alert('Geolocation wordt niet ondersteund door je browser');
        return;
    }

    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-spinner"></i><span>Locatie ophalen...</span>';

    navigator.geolocation.getCurrentPosition(
        // Success
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Store in localStorage so we remember permission was granted
            localStorage.setItem('locationPermission', 'granted');
            localStorage.setItem('userLat', lat);
            localStorage.setItem('userLng', lng);

            // Redirect with coordinates and sort by distance
            const url = new URL(window.location.href);
            url.searchParams.set('lat', lat);
            url.searchParams.set('lng', lng);
            url.searchParams.set('sort', 'distance');
            window.location.href = url.toString();
        },
        // Error
        function(error) {
            btn.classList.remove('loading');
            btn.innerHTML = '<i class="fas fa-crosshairs"></i><span><?= $translations['enable_location'] ?? 'Enable location' ?></span>';

            localStorage.setItem('locationPermission', 'denied');

            let message = 'Kon locatie niet bepalen';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    message = 'Locatietoegang geweigerd.\n\nOm salons bij jou in de buurt te vinden:\n1. Klik op het slot-icoon in je adresbalk\n2. Sta locatie toe voor deze website\n3. Ververs de pagina';
                    break;
                case error.POSITION_UNAVAILABLE:
                    message = 'Locatie niet beschikbaar. Probeer het later opnieuw.';
                    break;
                case error.TIMEOUT:
                    message = 'Locatieverzoek timeout. Probeer het opnieuw.';
                    break;
            }
            alert(message);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        }
    );
}

// Dismiss location banner
function dismissLocationBanner() {
    const banner = document.getElementById('location-banner');
    banner.classList.add('hidden');
    localStorage.setItem('locationBannerDismissed', 'true');
}

// Auto-detect location on page load if no location set
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('user-lat');
    const locationInput = document.getElementById('location-input');
    const banner = document.getElementById('location-banner');

    // If we have coordinates but no location text, show "In de buurt" indicator
    if (latInput && latInput.value && !locationInput.value) {
        const btn = document.querySelector('.location-btn');
        if (btn) {
            btn.classList.add('success');
            btn.innerHTML = '<i class="fas fa-check"></i>';
        }
    }

    // Check if banner was previously dismissed
    if (localStorage.getItem('locationBannerDismissed') === 'true') {
        if (banner) banner.classList.add('hidden');
    }

    // Check if we have stored location from previous grant
    const storedLat = localStorage.getItem('userLat');
    const storedLng = localStorage.getItem('userLng');
    const permission = localStorage.getItem('locationPermission');

    // If permission was previously granted and we don't have coords in URL, auto-apply them
    if (permission === 'granted' && storedLat && storedLng && !latInput.value) {
        const url = new URL(window.location.href);
        if (!url.searchParams.get('lat')) {
            url.searchParams.set('lat', storedLat);
            url.searchParams.set('lng', storedLng);
            // Only redirect if we're on search page without filters
            if (url.pathname === '/search' && !url.searchParams.get('q')) {
                window.location.href = url.toString();
            }
        }
    }

    // Update nearby button state based on current coords
    const nearbyBtn = document.getElementById('nearby-btn');
    const nearbyBtnText = document.getElementById('nearby-btn-text');
    if (latInput.value && nearbyBtn) {
        nearbyBtn.classList.add('active');
    }

    // Auto-check location permission status
    if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
            if (result.state === 'granted') {
                // Permission already granted, update button state
                if (banner) banner.classList.add('hidden');

                // If no coords yet, get them
                if (!latInput.value) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        localStorage.setItem('userLat', lat);
                        localStorage.setItem('userLng', lng);
                        localStorage.setItem('locationPermission', 'granted');

                        const url = new URL(window.location.href);
                        if (!url.searchParams.get('lat')) {
                            url.searchParams.set('lat', lat);
                            url.searchParams.set('lng', lng);
                            url.searchParams.set('sort', 'distance');
                            window.location.href = url.toString();
                        }
                    });
                }
            } else if (result.state === 'denied') {
                // Permission denied, update nearby button text
                if (nearbyBtnText) {
                    nearbyBtnText.textContent = '<?= $translations['location_blocked'] ?? 'Location blocked - tap to enable' ?>';
                }
            } else if (result.state === 'prompt') {
                // First visit - auto-prompt for location after 2 seconds
                const hasVisited = localStorage.getItem('searchPageVisited');
                if (!hasVisited && !latInput.value) {
                    localStorage.setItem('searchPageVisited', 'true');
                    setTimeout(function() {
                        // Flash the nearby button to draw attention
                        if (nearbyBtn) {
                            nearbyBtn.style.animation = 'pulse 1s ease-in-out 3';
                        }
                    }, 2000);
                }
            }
            // 'prompt' state - show banner normally
        });
    }
});

// === Salon Map (Leaflet + MarkerCluster, lazy loaded) ===
let salonMap = null;
let salonMarkers = null;
let allSalonData = [];
const detectedLang = '<?= $lang ?? 'nl' ?>';
const langCountryDefaults = { nl: 'NL', de: 'DE', fr: 'FR', en: '' };

function filterMapCountry(btn) {
    document.querySelectorAll('.country-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const country = btn.dataset.country;
    loadMapMarkers(country);
}

function loadMapMarkers(country) {
    if (!salonMap || !salonMarkers) return;
    const url = '/api/salons/map' + (country ? '?country=' + country : '');
    fetch(url)
        .then(r => r.json())
        .then(data => {
            allSalonData = data;
            salonMarkers.clearLayers();
            data.forEach(s => {
                const stars = '\u2605'.repeat(Math.round(s.rating)) + '\u2606'.repeat(5 - Math.round(s.rating));
                const marker = L.marker([s.lat, s.lng]);
                marker.bindPopup(
                    '<div style="min-width:180px">' +
                    '<strong style="font-size:1.05em">' + s.name.replace(/</g,'&lt;') + '</strong><br>' +
                    '<span style="color:#666">' + (s.city || '').replace(/</g,'&lt;') + '</span><br>' +
                    '<span style="color:#f59e0b">' + stars + '</span> ' +
                    '<small>(' + s.reviews + ')</small><br>' +
                    '<div style="margin-top:8px;display:flex;gap:6px">' +
                    '<a href="/business/' + encodeURIComponent(s.slug) + '" style="padding:5px 12px;background:#000;color:#fff;border-radius:20px;text-decoration:none;font-size:0.8rem;font-weight:600">' + (detectedLang === 'nl' ? 'Bekijk' : 'View') + '</a>' +
                    '<a href="https://www.google.com/maps/dir/?api=1&destination=' + s.lat + ',' + s.lng + '" target="_blank" rel="noopener" style="padding:5px 12px;background:#3b82f6;color:#fff;border-radius:20px;text-decoration:none;font-size:0.8rem;font-weight:600">Route</a>' +
                    '</div></div>'
                );
                salonMarkers.addLayer(marker);
            });
            salonMap.addLayer(salonMarkers);
            if (data.length > 0) {
                const bounds = salonMarkers.getBounds();
                if (bounds.isValid()) salonMap.fitBounds(bounds, { padding: [30, 30] });
            }
        })
        .catch(() => {});
}

function initSalonMap() {
    if (salonMap) return;
    salonMap = L.map('salon-map', { scrollWheelZoom: false }).setView([51.5, 5.5], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 18
    }).addTo(salonMap);
    salonMarkers = L.markerClusterGroup();

    // Auto-select country filter based on detected language
    const defaultCountry = langCountryDefaults[detectedLang] || '';
    if (defaultCountry) {
        const btn = document.querySelector('.country-filter-btn[data-country="' + defaultCountry + '"]');
        if (btn) {
            document.querySelectorAll('.country-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    }
    loadMapMarkers(defaultCountry);
}

// Lazy load Leaflet CSS/JS when map section is visible
(function() {
    const mapSection = document.getElementById('salon-map-section');
    if (!mapSection) return;

    let leafletLoaded = false;
    function loadLeaflet() {
        if (leafletLoaded) return;
        leafletLoaded = true;

        // Leaflet CSS
        const css1 = document.createElement('link');
        css1.rel = 'stylesheet';
        css1.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(css1);

        // MarkerCluster CSS
        const css2 = document.createElement('link');
        css2.rel = 'stylesheet';
        css2.href = 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css';
        document.head.appendChild(css2);
        const css3 = document.createElement('link');
        css3.rel = 'stylesheet';
        css3.href = 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css';
        document.head.appendChild(css3);

        // Leaflet JS
        const js1 = document.createElement('script');
        js1.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        js1.onload = function() {
            // MarkerCluster JS
            const js2 = document.createElement('script');
            js2.src = 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js';
            js2.onload = function() {
                initSalonMap();
            };
            document.head.appendChild(js2);
        };
        document.head.appendChild(js1);
    }

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                loadLeaflet();
                observer.disconnect();
            }
        }, { rootMargin: '200px' });
        observer.observe(mapSection);
    } else {
        // Fallback: load after delay
        setTimeout(loadLeaflet, 3000);
    }
})();
</script>

<?php $content = ob_get_clean(); ?>
<?php include BASE_PATH . '/resources/views/layouts/main.php'; ?>
