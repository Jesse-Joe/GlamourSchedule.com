<?php ob_start(); ?>

<style>
/* =============================================
   SEARCH PAGE - Theme Support
   Responds to data-theme attribute on html element
   ============================================= */

/* Dark Theme (Default) */
.search-page-wrapper,
[data-theme="dark"] .search-page-wrapper {
    --search-bg: #000000;
    --search-surface: #1a1a1a;
    --search-border: #333333;
    --search-border-light: #444444;
    --search-text: #ffffff;
    --search-text-muted: rgba(255, 255, 255, 0.6);
    --search-text-placeholder: rgba(255, 255, 255, 0.5);
    --search-input-bg: rgba(255, 255, 255, 0.1);
    --search-input-border: rgba(255, 255, 255, 0.2);
    --search-input-focus: #ffffff;
    --search-btn-bg: #ffffff;
    --search-btn-text: #000000;
    --search-card-bg: #1a1a1a;
    --search-card-border: #444444;
    --search-card-hover: #666666;
    --search-checkbox-bg: rgba(255, 255, 255, 0.05);
    --search-checkbox-selected: rgba(255, 255, 255, 0.25);
    --search-shadow: rgba(0, 0, 0, 0.3);
    --search-divider: rgba(255, 255, 255, 0.15);
}

/* Light Theme */
[data-theme="light"] .search-page-wrapper {
    --search-bg: #ffffff;
    --search-surface: #f5f5f5;
    --search-border: #e0e0e0;
    --search-border-light: #d0d0d0;
    --search-text: #000000;
    --search-text-muted: rgba(0, 0, 0, 0.6);
    --search-text-placeholder: rgba(0, 0, 0, 0.5);
    --search-input-bg: rgba(0, 0, 0, 0.05);
    --search-input-border: rgba(0, 0, 0, 0.2);
    --search-input-focus: #000000;
    --search-btn-bg: #000000;
    --search-btn-text: #ffffff;
    --search-card-bg: #ffffff;
    --search-card-border: #e0e0e0;
    --search-card-hover: #000000;
    --search-checkbox-bg: rgba(0, 0, 0, 0.03);
    --search-checkbox-selected: rgba(0, 0, 0, 0.1);
    --search-shadow: rgba(0, 0, 0, 0.1);
    --search-divider: rgba(0, 0, 0, 0.1);
}

/* !! CRITICAL v3 - Business Card Styles - Must override everything !! */
.results-container .business-grid .biz-card,
body .results-container .biz-card,
html body .biz-card {
    display: flex !important;
    flex-direction: column !important;
    background: var(--search-card-bg) !important;
    border: 2px solid var(--search-card-border) !important;
    border-radius: 16px !important;
    overflow: hidden !important;
    text-decoration: none !important;
    color: var(--search-text) !important;
    min-height: 340px !important;
    box-sizing: border-box !important;
    width: 100% !important;
    transition: transform 0.2s, box-shadow 0.2s, background 0.3s, border-color 0.3s !important;
}
.results-container .business-grid .biz-card:hover,
body .results-container .biz-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 8px 30px var(--search-shadow) !important;
    border-color: var(--search-card-hover) !important;
}
.biz-card * {
    box-sizing: border-box !important;
}
.results-container div.business-grid,
body div.business-grid,
html body div.business-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    gap: 1.25rem !important;
    padding: 1rem !important;
    width: 100% !important;
}
@media (max-width: 600px) {
    .results-container div.business-grid,
    body div.business-grid {
        grid-template-columns: 1fr !important;
    }
}
.results-container .biz-card .biz-card-img,
body .biz-card .biz-card-img {
    position: relative !important;
    width: 100% !important;
    height: 150px !important;
    background: linear-gradient(135deg, var(--search-surface) 0%, var(--search-bg) 100%) !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
}
.results-container .biz-card .biz-card-body,
body .biz-card .biz-card-body {
    padding: 14px !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
}
.results-container .biz-card .biz-card-footer,
body .biz-card .biz-card-footer {
    margin-top: auto !important;
    padding-top: 12px !important;
    border-top: 1px solid var(--search-border) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}

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
    background: var(--search-bg);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 40px var(--search-shadow);
    border: 2px solid var(--search-border);
    margin-bottom: 1.5rem;
    transition: background 0.3s, border-color 0.3s;
}
.search-header {
    background: var(--search-bg);
    color: var(--search-text);
    padding: 2rem;
    text-align: center;
    border-bottom: 2px solid var(--search-border);
    transition: background 0.3s, color 0.3s;
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
    color: var(--search-text);
}
.search-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--search-text);
}
.search-header p {
    margin: 0.5rem 0 0 0;
    font-size: 0.95rem;
    color: var(--search-text-muted);
}
.search-body {
    padding: 2rem;
    background: var(--search-bg);
    transition: background 0.3s;
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
    color: var(--search-text);
}
.search-bar-input input {
    width: 100%;
    padding: 0.85rem 0 0.85rem 1.75rem;
    border: none;
    border-bottom: 2px solid var(--search-input-border);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: var(--search-text);
    transition: all 0.3s ease;
}
.search-bar-input input:focus {
    outline: none;
    border-bottom-color: var(--search-input-focus);
    box-shadow: none;
}
.search-bar-input input::placeholder {
    color: var(--search-text-placeholder);
}
.search-bar-select select,
.search-bar-select input {
    width: 100%;
    padding: 0.85rem 0;
    border: none;
    border-bottom: 2px solid var(--search-input-border);
    border-radius: 0;
    font-size: 1rem;
    background: transparent;
    color: var(--search-text);
    appearance: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
.search-bar-select select:focus,
.search-bar-select input:focus {
    outline: none;
    border-bottom-color: var(--search-input-focus);
    box-shadow: none;
}
.search-bar-select select::placeholder,
.search-bar-select input::placeholder {
    color: var(--search-text-placeholder);
}
.search-bar-select select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0 center;
}
.search-bar-select select option {
    background: var(--search-bg);
    color: var(--search-text);
}
.search-bar-btn {
    width: 100%;
    padding: 1rem;
    background: var(--search-btn-bg);
    color: var(--search-btn-text);
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
    box-shadow: 0 10px 30px var(--search-shadow);
}

/* Advanced Filters Panel */
.filters-panel {
    background: transparent;
    border: none;
    border-radius: 0;
    margin-bottom: 0;
    overflow: hidden;
    border-top: 1px solid var(--search-divider);
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
    color: var(--search-text);
}
.filters-toggle i {
    transition: transform 0.3s;
    color: var(--search-text);
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
    color: var(--search-text);
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
    color: var(--search-text-muted);
    margin-bottom: 0.25rem;
}
.price-input-group input {
    width: 100%;
    padding: 0.6rem;
    background: var(--search-input-bg);
    border: 1px solid var(--search-input-border);
    border-radius: 8px;
    color: var(--search-text);
    font-size: 0.9rem;
}
.price-input-group input:focus {
    outline: none;
    border-color: var(--search-input-focus);
}
.price-input-group input::placeholder {
    color: var(--search-text-placeholder);
}
.price-separator {
    color: var(--search-text-muted);
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
    background: var(--search-input-bg);
    border: 1px solid var(--search-input-border);
    border-radius: 8px;
    color: var(--search-text);
    font-size: 0.9rem;
}
.availability-options input[type="date"]:focus,
.availability-options select:focus {
    outline: none;
    border-color: var(--search-input-focus);
}
.availability-options select option {
    background: var(--search-bg);
    color: var(--search-text);
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
    background: var(--search-checkbox-bg);
    border: 1px solid var(--search-input-border);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
    color: var(--search-text-muted);
}
.filter-checkbox:hover {
    border-color: var(--search-border-light);
    background: var(--search-input-bg);
}
.filter-checkbox.selected {
    background: var(--search-checkbox-selected);
    color: var(--search-text);
    border-color: var(--search-border-light);
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
    background: var(--search-divider);
    margin: 1rem 0 1.5rem;
}

.filter-search {
    margin-bottom: 1.5rem;
}
.filter-search input {
    width: 100%;
    padding: 0.85rem 0;
    border: none;
    border-bottom: 2px solid var(--search-input-border);
    border-radius: 0;
    font-size: 0.95rem;
    background: transparent;
    color: var(--search-text);
    transition: all 0.3s ease;
}
.filter-search input:focus {
    outline: none;
    border-bottom-color: var(--search-input-focus);
}
.filter-search input::placeholder {
    color: var(--search-text-placeholder);
}

/* Location Button */
.location-btn {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--search-text-muted);
    cursor: pointer;
    padding: 0.5rem;
    transition: all 0.2s;
}
.location-btn:hover {
    color: var(--search-text);
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
    gap: 0.2rem;
    padding: 0.15rem 0.35rem;
    background: rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 4px;
    font-size: 0.65rem;
    color: #60a5fa;
    margin-left: 0.3rem;
    flex-shrink: 0;
}
.biz-card-distance i {
    font-size: 0.6rem;
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
    margin: 0 auto 2rem;
    padding: 2rem 1rem;
    background: var(--search-bg);
    transition: background 0.3s;
}
@media (min-width: 1024px) {
    .results-container {
        max-width: 1600px;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
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
    color: var(--search-text);
}
@media (min-width: 1024px) {
    .results-title {
        font-size: 1.5rem;
        gap: 1rem;
    }
}
.results-title i {
    color: var(--search-text);
}
.results-count {
    background: var(--search-btn-bg);
    color: var(--search-btn-text);
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
    color: var(--search-text-muted);
    font-size: 0.9rem;
}
.results-sort select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 2px solid var(--search-input-border);
    border-radius: 8px;
    font-size: 0.9rem;
    background: var(--search-input-bg);
    color: var(--search-text);
    cursor: pointer;
    appearance: none;
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
}
[data-theme="dark"] .results-sort select,
.search-page-wrapper .results-sort select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
}
[data-theme="light"] .results-sort select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%23000000' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
}
.results-sort select option {
    background: var(--search-surface);
    color: var(--search-text);
}

/* =============================================
   BUSINESS CARDS - Complete Self-Contained Cards
   ============================================= */

/* Grid Layout - Each card in its own cell */
.results-container .business-grid {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1.25rem !important;
    padding: 1rem !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
@media (min-width: 540px) {
    .results-container .business-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1.25rem !important;
    }
}
@media (min-width: 900px) {
    .results-container .business-grid {
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 1.5rem !important;
    }
}
@media (min-width: 1200px) {
    .results-container .business-grid {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 1.5rem !important;
    }
}

/* Single Business Card - Complete Self-Contained Box */
.results-container .biz-card {
    display: flex !important;
    flex-direction: column !important;
    background: #1a1a1a !important;
    border: 2px solid #444 !important;
    border-radius: 16px !important;
    overflow: hidden !important;
    text-decoration: none !important;
    color: #fff !important;
    transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
    min-height: 340px !important;
    box-sizing: border-box !important;
}
.results-container .biz-card:hover {
    transform: translateY(-6px) !important;
    border-color: #fff !important;
    box-shadow: 0 16px 40px rgba(0,0,0,0.5) !important;
}

/* Card Image Section */
.results-container .biz-card-img {
    position: relative !important;
    width: 100% !important;
    height: 150px !important;
    background: var(--search-surface) !important;
    overflow: hidden !important;
    flex-shrink: 0 !important;
}
@media (min-width: 768px) {
    .results-container .biz-card-img { height: 170px !important; }
}
.results-container .biz-card-img img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
}
.results-container .biz-card-img-placeholder {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: linear-gradient(135deg, #1f1f1f 0%, #0d0d0d 100%) !important;
}
.results-container .biz-card-img-placeholder i {
    font-size: 3rem !important;
    color: var(--search-border) !important;
}
.results-container .biz-card-letter {
    font-size: 4rem !important;
    font-weight: 800 !important;
    color: #333 !important;
    text-transform: uppercase !important;
    user-select: none !important;
}

/* Badges on Image */
.results-container .biz-card-badges {
    position: absolute !important;
    top: 10px !important;
    left: 10px !important;
    right: 10px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: flex-start !important;
    pointer-events: none !important;
    z-index: 2 !important;
}
.results-container .biz-badge {
    padding: 5px 10px !important;
    border-radius: 6px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}
.results-container .biz-badge-new { background: #fbbf24 !important; color: #000 !important; }
.results-container .biz-badge-popular { background: #ef4444 !important; color: #fff !important; }
.results-container .biz-card-category {
    background: rgba(0,0,0,0.8) !important;
    color: #fff !important;
    padding: 5px 10px !important;
    border-radius: 6px !important;
    font-size: 10px !important;
    font-weight: 600 !important;
}

/* Card Body - Contains All Info */
.results-container .biz-card-body {
    padding: 14px !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    min-height: 180px !important;
    box-sizing: border-box !important;
}
@media (min-width: 768px) {
    .results-container .biz-card-body { padding: 16px !important; }
}

/* Business Name */
.results-container .biz-card-name {
    font-size: 16px !important;
    font-weight: 700 !important;
    margin: 0 0 8px 0 !important;
    color: var(--search-text) !important;
    line-height: 1.3 !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
}

/* Location Row */
.results-container .biz-card-location {
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    font-size: 13px !important;
    color: var(--search-text-muted) !important;
    margin-bottom: 10px !important;
}
.results-container .biz-card-location i {
    font-size: 11px !important;
    color: var(--search-text-muted) !important;
    flex-shrink: 0 !important;
}
.results-container .biz-card-distance {
    background: rgba(59,130,246,0.25) !important;
    color: #60a5fa !important;
    padding: 3px 8px !important;
    border-radius: 4px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    margin-left: auto !important;
    flex-shrink: 0 !important;
}

/* Rating Row */
.results-container .biz-card-rating {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-bottom: 10px !important;
}
.results-container .biz-card-stars {
    display: flex !important;
    gap: 2px !important;
}
.results-container .biz-card-stars i {
    font-size: 12px !important;
    color: #fbbf24 !important;
}
.results-container .biz-card-stars i.empty {
    color: var(--search-border) !important;
}
.results-container .biz-card-rating-text {
    font-size: 13px !important;
    color: var(--search-text-muted) !important;
}
.results-container .biz-card-rating-score {
    font-weight: 700 !important;
    color: var(--search-text) !important;
}

/* Services Tags */
.results-container .biz-card-services {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 5px !important;
    margin-bottom: 12px !important;
    flex: 1 !important;
}
.results-container .biz-service-tag {
    background: var(--search-surface) !important;
    color: var(--search-text-muted) !important;
    padding: 4px 10px !important;
    border-radius: 4px !important;
    font-size: 11px !important;
    border: 1px solid var(--search-border) !important;
}

/* Footer with Price & Button */
.results-container .biz-card-footer {
    margin-top: auto !important;
    padding-top: 12px !important;
    border-top: 1px solid var(--search-border) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 10px !important;
}
.results-container .biz-card-price {
    font-size: 13px !important;
    color: var(--search-text-muted) !important;
}
.results-container .biz-card-price strong {
    font-size: 15px !important;
    color: var(--search-text) !important;
    font-weight: 700 !important;
}
.results-container .biz-card-cta {
    background: var(--search-btn-bg) !important;
    color: var(--search-btn-text) !important;
    padding: 10px 16px !important;
    border-radius: 25px !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    transition: background 0.2s ease, color 0.2s ease !important;
}
.results-container .biz-card:hover .biz-card-cta {
    opacity: 0.85 !important;
}

/* Route Button */
.results-container .biz-card-route {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 34px !important;
    height: 34px !important;
    background: rgba(59,130,246,0.15) !important;
    border: 1px solid rgba(59,130,246,0.4) !important;
    border-radius: 50% !important;
    color: #60a5fa !important;
    font-size: 13px !important;
    text-decoration: none !important;
    margin-right: 8px !important;
    transition: all 0.2s ease !important;
    flex-shrink: 0 !important;
}
.results-container .biz-card-route:hover {
    background: #3b82f6 !important;
    color: #fff !important;
    border-color: #3b82f6 !important;
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
    margin: 2rem auto 2rem;
    padding: 0 1rem;
}
@media (min-width: 1024px) {
    .salon-map-section {
        max-width: 1600px;
        padding: 0 2rem;
        margin: 3rem auto 3rem;
    }
}
.salon-map-card {
    background: var(--search-checkbox-bg);
    border: 2px solid var(--search-input-border);
    border-radius: 16px;
    overflow: hidden;
    transition: background 0.3s, border-color 0.3s;
}
.salon-map-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--search-divider);
}
.salon-map-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--search-text);
    margin: 0;
}
.salon-map-title i {
    color: #60a5fa;
}
.country-filter-select {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.country-filter-select select {
    appearance: none;
    -webkit-appearance: none;
    background: var(--search-input-bg);
    border: 1px solid var(--search-input-border);
    border-radius: 20px;
    padding: 0.5rem 2.5rem 0.5rem 1rem;
    color: var(--search-text);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 160px;
}
.country-filter-select select:hover {
    border-color: var(--search-input-focus);
    background: var(--search-checkbox-selected);
}
.country-filter-select select:focus {
    outline: none;
    border-color: var(--search-input-focus);
    background: var(--search-checkbox-selected);
}
.country-filter-select select option {
    background: var(--search-surface);
    color: var(--search-text);
    padding: 0.5rem;
}
.country-filter-select::after {
    content: '\f107';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 12px;
    pointer-events: none;
    color: var(--search-text-muted);
    font-size: 0.8rem;
}
#salon-map {
    height: 450px;
    background: var(--search-surface);
}
/* Pink salon markers - Override Leaflet defaults */
.leaflet-marker-icon.salon-marker-pink,
.salon-marker-pink {
    background: transparent !important;
    border: none !important;
    width: 30px !important;
    height: 40px !important;
    margin-left: -15px !important;
    margin-top: -40px !important;
}
.salon-pin {
    color: #ec4899;
    font-size: 30px;
    text-shadow: 0 2px 8px rgba(236, 72, 153, 0.5), 0 0 0 2px #ffffff;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    line-height: 1;
    display: flex;
    align-items: flex-start;
    justify-content: center;
}
.salon-pin i {
    display: block;
}
/* Pink cluster styling */
.salon-cluster {
    background: transparent;
}
.salon-cluster-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #ec4899, #db2777);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4), 0 0 0 3px rgba(255,255,255,0.8);
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
    }
    /* Business cards on mobile */
    .results-container .business-grid {
        gap: 1rem !important;
        padding: 0.75rem !important;
    }
    .results-container .biz-card {
        min-height: 300px !important;
    }
    .results-container .biz-card-img {
        height: 130px !important;
    }
    .results-container .biz-card-body {
        padding: 12px !important;
        min-height: 160px !important;
    }
    .results-container .biz-card-name {
        font-size: 15px !important;
    }
    .results-container .biz-card-footer {
        padding-top: 10px !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .results-container .biz-card-cta {
        padding: 8px 14px !important;
        font-size: 11px !important;
    }
}

/* Extra small screens */
@media (max-width: 400px) {
    .results-container .business-grid {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
        padding: 0.5rem !important;
        margin-bottom: 0 !important;
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

<div class="search-page-wrapper">
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
                                        <?= htmlspecialchars($cat['translated_name'] ?? $cat['name'] ?? '') ?>
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
                        <div class="search-bar-select country-select">
                            <select name="country" id="search-country" onchange="this.form.submit()">
                                <option value=""><?= $translations['all_countries'] ?? 'All Countries' ?> 🌍</option>
                                <optgroup label="Europe">
                                    <option value="NL" <?= ($filters['country'] ?? '') === 'NL' ? 'selected' : '' ?>>🇳🇱 Nederland</option>
                                    <option value="BE" <?= ($filters['country'] ?? '') === 'BE' ? 'selected' : '' ?>>🇧🇪 België</option>
                                    <option value="DE" <?= ($filters['country'] ?? '') === 'DE' ? 'selected' : '' ?>>🇩🇪 Deutschland</option>
                                    <option value="FR" <?= ($filters['country'] ?? '') === 'FR' ? 'selected' : '' ?>>🇫🇷 France</option>
                                    <option value="GB" <?= ($filters['country'] ?? '') === 'GB' ? 'selected' : '' ?>>🇬🇧 United Kingdom</option>
                                    <option value="ES" <?= ($filters['country'] ?? '') === 'ES' ? 'selected' : '' ?>>🇪🇸 España</option>
                                    <option value="IT" <?= ($filters['country'] ?? '') === 'IT' ? 'selected' : '' ?>>🇮🇹 Italia</option>
                                    <option value="PT" <?= ($filters['country'] ?? '') === 'PT' ? 'selected' : '' ?>>🇵🇹 Portugal</option>
                                    <option value="AT" <?= ($filters['country'] ?? '') === 'AT' ? 'selected' : '' ?>>🇦🇹 Österreich</option>
                                    <option value="CH" <?= ($filters['country'] ?? '') === 'CH' ? 'selected' : '' ?>>🇨🇭 Schweiz</option>
                                    <option value="PL" <?= ($filters['country'] ?? '') === 'PL' ? 'selected' : '' ?>>🇵🇱 Polska</option>
                                    <option value="SE" <?= ($filters['country'] ?? '') === 'SE' ? 'selected' : '' ?>>🇸🇪 Sverige</option>
                                    <option value="NO" <?= ($filters['country'] ?? '') === 'NO' ? 'selected' : '' ?>>🇳🇴 Norge</option>
                                    <option value="DK" <?= ($filters['country'] ?? '') === 'DK' ? 'selected' : '' ?>>🇩🇰 Danmark</option>
                                    <option value="FI" <?= ($filters['country'] ?? '') === 'FI' ? 'selected' : '' ?>>🇫🇮 Suomi</option>
                                    <option value="RU" <?= ($filters['country'] ?? '') === 'RU' ? 'selected' : '' ?>>🇷🇺 Россия</option>
                                </optgroup>
                                <optgroup label="Asia">
                                    <option value="JP" <?= ($filters['country'] ?? '') === 'JP' ? 'selected' : '' ?>>🇯🇵 日本</option>
                                    <option value="KR" <?= ($filters['country'] ?? '') === 'KR' ? 'selected' : '' ?>>🇰🇷 한국</option>
                                    <option value="CN" <?= ($filters['country'] ?? '') === 'CN' ? 'selected' : '' ?>>🇨🇳 中国</option>
                                    <option value="TH" <?= ($filters['country'] ?? '') === 'TH' ? 'selected' : '' ?>>🇹🇭 ไทย</option>
                                    <option value="VN" <?= ($filters['country'] ?? '') === 'VN' ? 'selected' : '' ?>>🇻🇳 Việt Nam</option>
                                    <option value="ID" <?= ($filters['country'] ?? '') === 'ID' ? 'selected' : '' ?>>🇮🇩 Indonesia</option>
                                    <option value="MY" <?= ($filters['country'] ?? '') === 'MY' ? 'selected' : '' ?>>🇲🇾 Malaysia</option>
                                    <option value="IN" <?= ($filters['country'] ?? '') === 'IN' ? 'selected' : '' ?>>🇮🇳 India</option>
                                </optgroup>
                                <optgroup label="Middle East">
                                    <option value="TR" <?= ($filters['country'] ?? '') === 'TR' ? 'selected' : '' ?>>🇹🇷 Türkiye</option>
                                    <option value="SA" <?= ($filters['country'] ?? '') === 'SA' ? 'selected' : '' ?>>🇸🇦 السعودية</option>
                                    <option value="IL" <?= ($filters['country'] ?? '') === 'IL' ? 'selected' : '' ?>>🇮🇱 ישראל</option>
                                </optgroup>
                                <optgroup label="Americas">
                                    <option value="US" <?= ($filters['country'] ?? '') === 'US' ? 'selected' : '' ?>>🇺🇸 United States</option>
                                </optgroup>
                            </select>
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
                           data-name="<?= htmlspecialchars(strtolower($cat['translated_name'] ?? $cat['name'] ?? '')) ?>">
                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= $isSelected ? 'checked' : '' ?>>
                        <i class="fas fa-<?= $icon ?>"></i>
                        <span><?= htmlspecialchars($cat['translated_name'] ?? $cat['name'] ?? '') ?></span>
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
        <div class="business-grid" style="display:grid !important; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)) !important; gap:1.25rem !important; padding:1rem !important;">
            <?php foreach ($businesses as $biz):
                $rating = round($biz['avg_rating'] ?? 0, 1);
                $fullStars = floor($rating);
                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                $isNew = isset($biz['created_at']) && strtotime($biz['created_at']) > strtotime('-30 days');
                $isPopular = ($biz['review_count'] ?? 0) >= 10;
            ?>
                <div class="biz-card" onclick="window.location='/business/<?= htmlspecialchars($biz['slug']) ?>'" style="display:flex !important; flex-direction:column !important; border-radius:16px !important; overflow:hidden !important; text-decoration:none !important; min-height:340px !important; cursor:pointer !important;">
                    <!-- Banner/Image Section -->
                    <div class="biz-card-img" style="position:relative !important; width:100% !important; height:150px !important; background:linear-gradient(135deg,#1a1a2e,#16213e) !important; overflow:hidden !important; flex-shrink:0 !important;">
                        <?php
                        // Priority: banner_image > logo > placeholder
                        $imageUrl = null;
                        $imagePosition = 'center';

                        if (!empty($biz['banner_image'])) {
                            // Use banner image
                            $imageUrl = $biz['banner_image'];
                            $imagePosition = $biz['banner_position'] ?? 'center';
                        } elseif (!empty($biz['logo'])) {
                            // Fall back to logo
                            $imageUrl = $biz['logo'];
                            // Check if it's an external URL or local path
                            if (!str_starts_with($imageUrl, 'http://') && !str_starts_with($imageUrl, 'https://')) {
                                $imageUrl = '/uploads/businesses/' . $imageUrl;
                            }
                        }
                        ?>
                        <?php if ($imageUrl): ?>
                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                 alt="<?= htmlspecialchars($biz['name']) ?>"
                                 style="object-position: <?= htmlspecialchars($imagePosition) ?>">
                        <?php else: ?>
                            <div class="biz-card-img-placeholder">
                                <?php
                                // Show first letter of business name as fallback
                                $firstLetter = mb_strtoupper(mb_substr($biz['name'] ?? 'S', 0, 1));
                                ?>
                                <span class="biz-card-letter"><?= $firstLetter ?></span>
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
                    <div class="biz-card-body" style="padding:14px !important; display:flex !important; flex-direction:column !important; flex:1 !important;">
                        <h3 class="biz-card-name" style="margin:0 0 8px 0 !important; font-size:1.1rem !important; font-weight:600 !important;"><?= htmlspecialchars($biz['name']) ?></h3>

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
                        <div class="biz-card-footer" style="margin-top:auto !important; padding-top:12px !important; display:flex !important; align-items:center !important; justify-content:space-between !important;">
                            <?php if (!empty($biz['min_price'])): ?>
                                <span class="biz-card-price">
                                    <?= $translations['from_price'] ?? 'From' ?> <strong><?php
                                        $minPriceDisplay = $currencyService->convertFromEur((float)$biz['min_price'], $visitorCurrency);
                                        echo $minPriceDisplay['local_symbol'] . number_format($minPriceDisplay['local_amount'], 0);
                                    ?></strong>
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
                </div>
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
            <div class="country-filter-select">
                <select id="country-filter" onchange="filterMapCountry(this.value)">
                    <option value=""><?= $translations['all_countries'] ?? 'All Countries' ?> 🌍</option>
                    <optgroup label="<?= $translations['europe'] ?? 'Europe' ?>">
                        <option value="NL">🇳🇱 Nederland</option>
                        <option value="BE">🇧🇪 België</option>
                        <option value="DE">🇩🇪 Deutschland</option>
                        <option value="FR">🇫🇷 France</option>
                        <option value="GB">🇬🇧 United Kingdom</option>
                        <option value="ES">🇪🇸 España</option>
                        <option value="IT">🇮🇹 Italia</option>
                        <option value="PT">🇵🇹 Portugal</option>
                        <option value="AT">🇦🇹 Österreich</option>
                        <option value="CH">🇨🇭 Schweiz</option>
                        <option value="PL">🇵🇱 Polska</option>
                        <option value="SE">🇸🇪 Sverige</option>
                        <option value="NO">🇳🇴 Norge</option>
                        <option value="DK">🇩🇰 Danmark</option>
                        <option value="FI">🇫🇮 Suomi</option>
                        <option value="IE">🇮🇪 Ireland</option>
                        <option value="GR">🇬🇷 Ελλάδα</option>
                        <option value="CZ">🇨🇿 Česko</option>
                        <option value="HU">🇭🇺 Magyarország</option>
                        <option value="RO">🇷🇴 România</option>
                        <option value="BG">🇧🇬 България</option>
                        <option value="HR">🇭🇷 Hrvatska</option>
                        <option value="SK">🇸🇰 Slovensko</option>
                        <option value="SI">🇸🇮 Slovenija</option>
                        <option value="LU">🇱🇺 Luxembourg</option>
                        <option value="RU">🇷🇺 Россия</option>
                        <option value="UA">🇺🇦 Україна</option>
                        <option value="EE">🇪🇪 Eesti</option>
                        <option value="LV">🇱🇻 Latvija</option>
                        <option value="LT">🇱🇹 Lietuva</option>
                    </optgroup>
                    <optgroup label="<?= $translations['americas'] ?? 'Americas' ?>">
                        <option value="US">🇺🇸 United States</option>
                        <option value="CA">🇨🇦 Canada</option>
                        <option value="MX">🇲🇽 México</option>
                        <option value="BR">🇧🇷 Brasil</option>
                        <option value="AR">🇦🇷 Argentina</option>
                        <option value="CO">🇨🇴 Colombia</option>
                        <option value="CL">🇨🇱 Chile</option>
                    </optgroup>
                    <optgroup label="<?= $translations['asia_pacific'] ?? 'Asia & Pacific' ?>">
                        <option value="AU">🇦🇺 Australia</option>
                        <option value="NZ">🇳🇿 New Zealand</option>
                        <option value="JP">🇯🇵 日本</option>
                        <option value="KR">🇰🇷 한국</option>
                        <option value="CN">🇨🇳 中国</option>
                        <option value="SG">🇸🇬 Singapore</option>
                        <option value="MY">🇲🇾 Malaysia</option>
                        <option value="TH">🇹🇭 ไทย</option>
                        <option value="ID">🇮🇩 Indonesia</option>
                        <option value="PH">🇵🇭 Philippines</option>
                        <option value="IN">🇮🇳 India</option>
                        <option value="AE">🇦🇪 UAE</option>
                        <option value="SA">🇸🇦 السعودية</option>
                        <option value="TR">🇹🇷 Türkiye</option>
                        <option value="IL">🇮🇱 ישראל</option>
                        <option value="VN">🇻🇳 Việt Nam</option>
                        <option value="IR">🇮🇷 ایران</option>
                    </optgroup>
                    <optgroup label="<?= $translations['africa'] ?? 'Africa' ?>">
                        <option value="ZA">🇿🇦 South Africa</option>
                        <option value="EG">🇪🇬 مصر</option>
                        <option value="MA">🇲🇦 المغرب</option>
                        <option value="NG">🇳🇬 Nigeria</option>
                        <option value="KE">🇰🇪 Kenya</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div id="salon-map"></div>
    </div>
</div>
</div><!-- End search-page-wrapper -->

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

    // Group filter (from group buttons)
    const activeGroupBtn = document.querySelector('.filter-group-btn.active');
    if (activeGroupBtn && activeGroupBtn.dataset.group && activeGroupBtn.dataset.group !== 'all') {
        url.searchParams.set('group', activeGroupBtn.dataset.group);
    }

    // Categories (only if no group is set, or specific categories are checked)
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
const searchCountry = '<?= $searchCountry ?? '' ?>';

// Language to country mapping - comprehensive list
const langCountryDefaults = {
    // Western Europe
    nl: 'NL', de: 'DE', fr: 'FR', es: 'ES', it: 'IT', pt: 'PT', en: 'GB',
    // Northern Europe
    sv: 'SE', da: 'DK', fi: 'FI', no: 'NO',
    // Eastern Europe
    pl: 'PL', cs: 'CZ', hu: 'HU', ro: 'RO', bg: 'BG', hr: 'HR', sk: 'SK',
    sl: 'SI', ru: 'RU', uk: 'UA', et: 'EE', lv: 'LV', lt: 'LT',
    // Southern Europe
    el: 'GR',
    // Asia
    ja: 'JP', ko: 'KR', zh: 'CN', th: 'TH', id: 'ID', vi: 'VN', ms: 'MY',
    hi: 'IN', tl: 'PH',
    // Middle East
    tr: 'TR', ar: 'SA', he: 'IL', fa: 'IR',
    // Africa
    sw: 'KE', af: 'ZA'
};

// Country map views: [lat, lng, zoom]
const countryMapViews = {
    '': [30, 0, 2],           // World view (All)
    // Europe
    'NL': [52.2, 5.3, 7],     // Netherlands
    'BE': [50.5, 4.5, 8],     // Belgium
    'DE': [51.2, 10.4, 6],    // Germany
    'FR': [46.6, 2.5, 6],     // France
    'GB': [54.0, -2.0, 6],    // United Kingdom
    'ES': [40.0, -3.7, 6],    // Spain
    'IT': [42.5, 12.5, 6],    // Italy
    'PT': [39.5, -8.0, 7],    // Portugal
    'AT': [47.5, 14.5, 7],    // Austria
    'CH': [46.8, 8.2, 8],     // Switzerland
    'PL': [52.0, 19.5, 6],    // Poland
    'SE': [62.0, 15.0, 5],    // Sweden
    'NO': [64.0, 10.0, 5],    // Norway
    'DK': [56.0, 10.0, 7],    // Denmark
    'FI': [64.0, 26.0, 5],    // Finland
    'IE': [53.4, -8.0, 7],    // Ireland
    'GR': [39.0, 22.0, 6],    // Greece
    'CZ': [49.8, 15.5, 7],    // Czech Republic
    'HU': [47.2, 19.5, 7],    // Hungary
    'RO': [45.9, 25.0, 6],    // Romania
    'BG': [42.7, 25.5, 7],    // Bulgaria
    'HR': [45.1, 16.0, 7],    // Croatia
    'SK': [48.7, 19.7, 7],    // Slovakia
    'SI': [46.2, 14.8, 8],    // Slovenia
    'LU': [49.6, 6.1, 9],     // Luxembourg
    'RU': [61.5, 105.3, 3],   // Russia
    'UA': [48.4, 31.2, 6],    // Ukraine
    'EE': [58.6, 25.0, 7],    // Estonia
    'LV': [56.9, 24.1, 7],    // Latvia
    'LT': [55.2, 23.9, 7],    // Lithuania
    // Americas
    'US': [39.8, -98.5, 4],   // United States
    'CA': [56.0, -96.0, 4],   // Canada
    'MX': [23.6, -102.5, 5],  // Mexico
    'BR': [-14.0, -51.9, 4],  // Brazil
    'AR': [-38.4, -63.6, 4],  // Argentina
    'CO': [4.6, -74.1, 5],    // Colombia
    'CL': [-35.7, -71.5, 4],  // Chile
    // Asia & Pacific
    'AU': [-25.3, 133.8, 4],  // Australia
    'NZ': [-41.3, 174.9, 5],  // New Zealand
    'JP': [36.2, 138.3, 5],   // Japan
    'KR': [35.9, 127.8, 7],   // South Korea
    'CN': [35.9, 104.2, 4],   // China
    'SG': [1.4, 103.8, 11],   // Singapore
    'MY': [4.2, 101.9, 6],    // Malaysia
    'TH': [15.9, 100.9, 6],   // Thailand
    'ID': [-2.5, 118.0, 4],   // Indonesia
    'PH': [12.9, 121.8, 6],   // Philippines
    'IN': [20.6, 79.0, 5],    // India
    'AE': [23.4, 53.8, 7],    // UAE
    'SA': [23.9, 45.1, 5],    // Saudi Arabia
    'TR': [39.0, 35.2, 6],    // Turkey
    'IL': [31.0, 34.8, 8],    // Israel
    'VN': [14.1, 108.3, 5],   // Vietnam
    'IR': [32.4, 53.7, 5],    // Iran
    // Africa
    'ZA': [-30.6, 22.9, 5],   // South Africa
    'EG': [26.8, 30.8, 6],    // Egypt
    'MA': [31.8, -7.1, 6],    // Morocco
    'NG': [9.1, 8.7, 6],      // Nigeria
    'KE': [1.0, 38.0, 6]      // Kenya
};

function filterMapCountry(country) {
    // Update dropdown selection
    const select = document.getElementById('country-filter');
    if (select && select.value !== country) {
        select.value = country;
    }

    // Set map view based on country
    if (salonMap && countryMapViews[country]) {
        const [lat, lng, zoom] = countryMapViews[country];
        salonMap.setView([lat, lng], zoom);
    }

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
                const marker = L.marker([s.lat, s.lng], { icon: pinkMarkerIcon });
                marker.bindPopup(
                    '<div style="min-width:200px;font-family:system-ui,-apple-system,sans-serif">' +
                    '<strong style="font-size:1.1em;color:#1a1a1a">' + s.name.replace(/</g,'&lt;') + '</strong><br>' +
                    '<span style="color:#666;font-size:0.9em"><i class="fas fa-map-marker-alt" style="color:#ec4899"></i> ' + (s.city || '').replace(/</g,'&lt;') + '</span><br>' +
                    '<div style="margin:6px 0"><span style="color:#f59e0b">' + stars + '</span> <small style="color:#888">(' + s.reviews + ')</small></div>' +
                    '<div style="margin-top:10px;display:flex;gap:8px">' +
                    '<a href="/business/' + encodeURIComponent(s.slug) + '" style="padding:6px 14px;background:linear-gradient(135deg,#ec4899,#db2777);color:#fff;border-radius:20px;text-decoration:none;font-size:0.8rem;font-weight:600;box-shadow:0 2px 8px rgba(236,72,153,0.3)">' + (detectedLang === 'nl' ? 'Bekijk' : 'View') + '</a>' +
                    '<a href="https://www.google.com/maps/dir/?api=1&destination=' + s.lat + ',' + s.lng + '" target="_blank" rel="noopener" style="padding:6px 14px;background:#1a1a1a;color:#fff;border-radius:20px;text-decoration:none;font-size:0.8rem;font-weight:600">Route</a>' +
                    '</div></div>'
                );
                salonMarkers.addLayer(marker);
            });
            salonMap.addLayer(salonMarkers);
            // Only fit bounds for specific country, not for "All" (world view)
            if (country && data.length > 0) {
                const bounds = salonMarkers.getBounds();
                if (bounds.isValid()) salonMap.fitBounds(bounds, { padding: [30, 30] });
            }
        })
        .catch(() => {});
}

// Pink marker icon - defined inside initSalonMap after Leaflet loads
let pinkMarkerIcon = null;

function initSalonMap() {
    if (salonMap) return;

    // Create pink marker icon now that Leaflet is loaded
    pinkMarkerIcon = L.divIcon({
        className: 'salon-marker-pink',
        html: '<div class="salon-pin"><i class="fas fa-map-marker-alt"></i></div>',
        iconSize: [30, 40],
        iconAnchor: [15, 40],
        popupAnchor: [0, -40]
    });

    // Priority: search location > language default > world view
    const defaultCountry = searchCountry || langCountryDefaults[detectedLang] || '';

    // Get initial view based on default country
    const [lat, lng, zoom] = countryMapViews[defaultCountry] || countryMapViews[''];

    salonMap = L.map('salon-map', { scrollWheelZoom: false }).setView([lat, lng], zoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 18
    }).addTo(salonMap);
    salonMarkers = L.markerClusterGroup({
        iconCreateFunction: function(cluster) {
            const count = cluster.getChildCount();
            return L.divIcon({
                html: '<div class="salon-cluster-icon">' + count + '</div>',
                className: 'salon-cluster',
                iconSize: [40, 40]
            });
        }
    });

    // Set dropdown to default country
    const select = document.getElementById('country-filter');
    if (select && defaultCountry) {
        select.value = defaultCountry;
    }

    loadMapMarkers(defaultCountry);

    // Try to detect country from IP if no search country and permission granted
    if (!searchCountry && !langCountryDefaults[detectedLang]) {
        detectCountryFromIP();
    }
}

// Detect country from IP address using free geoip service
function detectCountryFromIP() {
    fetch('https://ipapi.co/json/')
        .then(r => r.json())
        .then(data => {
            if (data.country_code && countryMapViews[data.country_code]) {
                const select = document.getElementById('country-filter');
                if (select && !select.value) {
                    filterMapCountry(data.country_code);
                }
            }
        })
        .catch(() => {});
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
