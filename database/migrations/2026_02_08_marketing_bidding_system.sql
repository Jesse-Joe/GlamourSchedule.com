-- Marketing Bidding System
-- Businesses can see reservations per section and bid for featured placement

-- Marketing sections (advertising slots on the platform)
CREATE TABLE IF NOT EXISTS marketing_sections (
    id INT(11) NOT NULL AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name_key VARCHAR(100) NOT NULL,
    description_key VARCHAR(100) DEFAULT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    category_group VARCHAR(50) DEFAULT NULL,
    min_bid_eur DECIMAL(10,2) NOT NULL DEFAULT 50.00,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT(11) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bids placed by businesses on marketing sections
CREATE TABLE IF NOT EXISTS marketing_bids (
    id INT(11) NOT NULL AUTO_INCREMENT,
    uuid VARCHAR(36) NOT NULL UNIQUE,
    business_id INT(11) NOT NULL,
    section_id INT(11) NOT NULL,
    bid_amount_eur DECIMAL(10,2) NOT NULL,
    bid_currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
    bid_amount_local DECIMAL(15,2) NOT NULL,
    status ENUM('active','outbid','won','expired','cancelled') NOT NULL DEFAULT 'active',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    payment_status ENUM('pending','paid','refunded') NOT NULL DEFAULT 'pending',
    mollie_payment_id VARCHAR(100) DEFAULT NULL,
    payment_provider VARCHAR(20) DEFAULT 'mollie',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_business (business_id),
    KEY idx_section (section_id),
    KEY idx_status (status),
    KEY idx_dates (start_date, end_date),
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES marketing_sections(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default marketing sections based on service categories
INSERT INTO marketing_sections (slug, name_key, description_key, icon, category_group, min_bid_eur, sort_order) VALUES
('homepage-hero', 'bid_section_homepage_hero', 'bid_section_homepage_hero_desc', 'fas fa-star', NULL, 100.00, 1),
('search-top', 'bid_section_search_top', 'bid_section_search_top_desc', 'fas fa-search', NULL, 75.00, 2),
('hair-care', 'bid_section_hair_care', 'bid_section_hair_care_desc', 'fas fa-cut', 'hair', 50.00, 3),
('skin-care', 'bid_section_skin_care', 'bid_section_skin_care_desc', 'fas fa-spa', 'skin', 50.00, 4),
('nails', 'bid_section_nails', 'bid_section_nails_desc', 'fas fa-hand-sparkles', 'nails', 50.00, 5),
('massage-wellness', 'bid_section_massage', 'bid_section_massage_desc', 'fas fa-leaf', 'massage', 50.00, 6),
('makeup', 'bid_section_makeup', 'bid_section_makeup_desc', 'fas fa-paint-brush', 'makeup', 50.00, 7),
('barbershop', 'bid_section_barbershop', 'bid_section_barbershop_desc', 'fas fa-user-tie', 'barbershop', 50.00, 8),
('sidebar-banner', 'bid_section_sidebar', 'bid_section_sidebar_desc', 'fas fa-ad', NULL, 40.00, 9),
('email-featured', 'bid_section_email', 'bid_section_email_desc', 'fas fa-envelope-open', NULL, 60.00, 10);
