-- Add account_code to users table
ALTER TABLE users ADD COLUMN account_code VARCHAR(9) UNIQUE NULL AFTER uuid;

-- Add user_id and account_code to pos_customers table
ALTER TABLE pos_customers ADD COLUMN user_id INT UNSIGNED NULL AFTER business_id;
ALTER TABLE pos_customers ADD COLUMN account_code VARCHAR(9) NULL AFTER user_id;

-- Add indexes for pos_customers
ALTER TABLE pos_customers ADD INDEX idx_pos_customers_user_id (user_id);
ALTER TABLE pos_customers ADD INDEX idx_pos_customers_account_code (account_code);
