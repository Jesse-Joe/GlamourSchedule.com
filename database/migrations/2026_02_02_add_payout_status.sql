-- ============================================================================
-- PAYOUT STATUS MIGRATIE
-- Created: 2026-02-02
-- Description: Voegt payout_status kolom toe voor automatische split payments
--              en QR-gebaseerde uitbetalingen na 24 uur
-- ============================================================================

-- 1. Voeg payout_status kolom toe aan bookings
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS payout_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending'
COMMENT 'Status van uitbetaling aan bedrijf: pending=wachtend, processing=in behandeling, completed=voltooid, failed=mislukt';

-- 2. Voeg payout_amount kolom toe (exacte uitbetaling, kan afwijken van business_payout door fees)
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS payout_amount DECIMAL(10,2) DEFAULT NULL
COMMENT 'Daadwerkelijk uitbetaald bedrag aan bedrijf';

-- 3. Voeg payout_date kolom toe
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS payout_date DATE DEFAULT NULL
COMMENT 'Datum waarop uitbetaling is gedaan';

-- 4. Voeg checked_in_at kolom toe als die nog niet bestaat
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS checked_in_at DATETIME DEFAULT NULL
COMMENT 'Timestamp wanneer klant is ingecheckt via QR';

-- 5. Index voor cron job queries
CREATE INDEX IF NOT EXISTS idx_bookings_payout_status ON bookings(payout_status, checked_in_at);
CREATE INDEX IF NOT EXISTS idx_bookings_checkin_payout ON bookings(status, checked_in_at, payout_status, payment_status);

-- 6. Update bestaande voltooide boekingen naar 'completed' payout status
UPDATE bookings
SET payout_status = 'completed'
WHERE payment_status = 'paid'
  AND status IN ('checked_in', 'completed')
  AND payout_status = 'pending'
  AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- ============================================================================
-- MOLLIE CONNECT KOLOMMEN VOOR BUSINESSES
-- ============================================================================

-- 7. Zorg dat Mollie Connect kolommen bestaan
ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS mollie_account_id VARCHAR(50) DEFAULT NULL
COMMENT 'Mollie organization ID van gekoppeld bedrijf';

ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS mollie_profile_id VARCHAR(50) DEFAULT NULL
COMMENT 'Mollie profile ID van gekoppeld bedrijf';

ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS mollie_onboarding_status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending'
COMMENT 'Status van Mollie Connect onboarding';

ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS mollie_access_token TEXT DEFAULT NULL
COMMENT 'OAuth access token voor Mollie API';

ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS mollie_refresh_token TEXT DEFAULT NULL
COMMENT 'OAuth refresh token voor Mollie API';

ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS mollie_connected_at DATETIME DEFAULT NULL
COMMENT 'Timestamp wanneer Mollie Connect is gekoppeld';

-- 8. Index voor Mollie Connect queries
CREATE INDEX IF NOT EXISTS idx_businesses_mollie ON businesses(mollie_account_id, mollie_onboarding_status);
