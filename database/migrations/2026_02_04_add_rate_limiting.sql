-- Add registration_ip column to users table if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS registration_ip VARCHAR(45) NULL AFTER created_at;

-- Add registration_ip column to businesses table if not exists  
ALTER TABLE businesses ADD COLUMN IF NOT EXISTS registration_ip VARCHAR(45) NULL AFTER created_at;

-- Create rate_limits table for tracking
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_action (ip_address, action_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clean up old rate limit entries (older than 24 hours) - run periodically
-- DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);
