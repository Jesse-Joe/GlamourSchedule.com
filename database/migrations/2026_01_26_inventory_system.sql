-- ============================================================================
-- INVENTORY SYSTEM MIGRATION
-- Created: 2026-01-26
-- Description: Adds inventory/stock management for businesses
-- ============================================================================

-- 1. Create inventory table (products/items)
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    business_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    sku VARCHAR(100) NULL,
    quantity INT DEFAULT 0,
    min_quantity INT DEFAULT 0 COMMENT 'Alert when stock falls below this',
    unit VARCHAR(50) DEFAULT 'stuks' COMMENT 'Unit of measurement (stuks, ml, gram, etc)',
    purchase_price DECIMAL(10,2) DEFAULT 0.00,
    sell_price DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_id (business_id),
    INDEX idx_sku (sku),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create inventory_service_link table (link products to services)
CREATE TABLE IF NOT EXISTS inventory_service_link (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventory_id INT NOT NULL,
    service_id INT NOT NULL,
    quantity_used DECIMAL(10,2) DEFAULT 1.00 COMMENT 'Amount used per service',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_inventory_service (inventory_id, service_id),
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    INDEX idx_inventory_id (inventory_id),
    INDEX idx_service_id (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create inventory_transactions table (stock movement history)
CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    inventory_id INT NOT NULL,
    business_id INT NOT NULL,
    booking_id INT NULL COMMENT 'If stock was used for a booking',
    transaction_type ENUM('purchase','sale','adjustment','used','returned') NOT NULL,
    quantity INT NOT NULL COMMENT 'Positive for add, negative for subtract',
    quantity_before INT NOT NULL,
    quantity_after INT NOT NULL,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventory_id) REFERENCES inventory(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_inventory_id (inventory_id),
    INDEX idx_business_id (business_id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
