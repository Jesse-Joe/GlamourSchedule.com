-- Migration: Add currency fields for Wise international payouts
-- Date: 2026-02-03

-- 1. Create exchange_rates table for historical tracking
CREATE TABLE IF NOT EXISTS exchange_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_currency VARCHAR(3) NOT NULL,
    target_currency VARCHAR(3) NOT NULL,
    rate DECIMAL(12,6) NOT NULL,
    mid_market_rate DECIMAL(12,6) NOT NULL,
    margin_percentage DECIMAL(5,4) DEFAULT 0,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_currencies (source_currency, target_currency),
    INDEX idx_recorded (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add currency columns to business_payouts
ALTER TABLE business_payouts
    ADD COLUMN target_currency VARCHAR(3) DEFAULT 'EUR',
    ADD COLUMN exchange_rate DECIMAL(12,6) DEFAULT NULL,
    ADD COLUMN mid_market_rate DECIMAL(12,6) DEFAULT NULL,
    ADD COLUMN margin_percentage DECIMAL(5,4) DEFAULT NULL,
    ADD COLUMN margin_amount DECIMAL(10,2) DEFAULT NULL,
    ADD COLUMN wise_fee DECIMAL(10,2) DEFAULT NULL,
    ADD COLUMN target_amount DECIMAL(10,2) DEFAULT NULL,
    ADD COLUMN wise_quote_id VARCHAR(100) DEFAULT NULL;

-- 3. Add preferred currency to businesses
ALTER TABLE businesses
    ADD COLUMN preferred_payout_currency VARCHAR(3) DEFAULT 'EUR';
