-- Add security_pin column to users table
ALTER TABLE users ADD COLUMN security_pin VARCHAR(255) DEFAULT NULL AFTER password_hash;

-- Add security_pin column to businesses table
ALTER TABLE businesses ADD COLUMN security_pin VARCHAR(255) DEFAULT NULL;

-- Add security_pin column to sales_users table
ALTER TABLE sales_users ADD COLUMN security_pin VARCHAR(255) DEFAULT NULL;
