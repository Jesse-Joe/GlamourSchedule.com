-- Cryptographic signing for bookings
-- Each business gets a unique signing key for HMAC-SHA256 signatures
-- This prevents fraud: only the server can create/validate booking signatures

-- Add signing key column to businesses table
ALTER TABLE businesses
ADD COLUMN signing_key VARCHAR(64) NULL COMMENT 'HMAC-SHA256 signing key (256-bit hex)',
ADD COLUMN signing_key_created_at TIMESTAMP NULL COMMENT 'When signing key was generated';

-- Add signature columns to bookings table
ALTER TABLE bookings
ADD COLUMN signature VARCHAR(64) NULL COMMENT 'HMAC-SHA256 signature of booking data',
ADD COLUMN signature_version TINYINT DEFAULT 1 COMMENT 'Signature format version for future upgrades';

-- Create index for faster signature lookups during verification
CREATE INDEX idx_bookings_signature ON bookings(signature);

-- Note: Run scripts/migrate_signatures.php after this migration to:
-- 1. Generate signing keys for all existing businesses
-- 2. Sign all existing bookings with their business key
