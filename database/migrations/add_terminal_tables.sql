-- Mollie Terminal Tables
-- Run this migration to add terminal support

-- Business Terminals - links Mollie terminals to businesses
CREATE TABLE IF NOT EXISTS business_terminals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    terminal_id VARCHAR(50) NOT NULL,
    terminal_name VARCHAR(100),
    terminal_brand VARCHAR(50),
    terminal_model VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_terminal (terminal_id),
    INDEX idx_business (business_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Terminal Transactions - logs all terminal payments
CREATE TABLE IF NOT EXISTS terminal_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    terminal_id VARCHAR(50) NOT NULL,
    payment_id VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    description VARCHAR(255),
    status ENUM('pending', 'paid', 'failed', 'canceled', 'expired') DEFAULT 'pending',
    metadata JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    paid_at DATETIME DEFAULT NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_payment (payment_id),
    INDEX idx_business (business_id),
    INDEX idx_terminal (terminal_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
