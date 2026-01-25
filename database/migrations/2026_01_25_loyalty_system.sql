-- ============================================================================
-- LOYALTY POINTS SYSTEM MIGRATION
-- Created: 2026-01-25
-- Description: Adds loyalty points system tables and columns
-- ============================================================================

-- 1. Create loyalty_points table (balance per user per business)
CREATE TABLE IF NOT EXISTS loyalty_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_id INT NOT NULL,
    total_points INT DEFAULT 0,
    lifetime_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_business (user_id, business_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_business_id (business_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create loyalty_transactions table (transaction history)
CREATE TABLE IF NOT EXISTS loyalty_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    business_id INT NOT NULL,
    booking_id INT NULL,
    review_id INT NULL,
    transaction_type ENUM('earn_booking','earn_review','redeem','expire','adjustment') NOT NULL,
    points INT NOT NULL,
    points_before INT NOT NULL,
    points_after INT NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_user_business (user_id, business_id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add loyalty columns to business_settings table
ALTER TABLE business_settings
ADD COLUMN IF NOT EXISTS loyalty_enabled TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS loyalty_max_redeem_points INT DEFAULT 2000;

-- 4. Add loyalty columns to bookings table
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS loyalty_discount DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS loyalty_points_redeemed INT DEFAULT 0;

-- 5. Create index for faster lookups on bookings with loyalty
CREATE INDEX IF NOT EXISTS idx_bookings_loyalty ON bookings(loyalty_points_redeemed);
