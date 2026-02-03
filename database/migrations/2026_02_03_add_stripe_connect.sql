-- Add Stripe Connect fields to businesses table
ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS stripe_account_id VARCHAR(50) NULL AFTER mollie_connected_at,
ADD COLUMN IF NOT EXISTS stripe_onboarding_status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending' AFTER stripe_account_id,
ADD COLUMN IF NOT EXISTS stripe_charges_enabled TINYINT(1) DEFAULT 0 AFTER stripe_onboarding_status,
ADD COLUMN IF NOT EXISTS stripe_payouts_enabled TINYINT(1) DEFAULT 0 AFTER stripe_charges_enabled,
ADD COLUMN IF NOT EXISTS stripe_connected_at DATETIME NULL AFTER stripe_payouts_enabled;

-- Add index for faster lookups
ALTER TABLE businesses ADD INDEX idx_stripe_account (stripe_account_id);

-- Add preferred_payment_provider to let business choose Mollie Connect or Stripe Connect
ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS preferred_payment_provider ENUM('mollie', 'stripe', 'auto') DEFAULT 'auto' AFTER stripe_connected_at;
