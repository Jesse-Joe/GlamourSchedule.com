-- ============================================================================
-- PLATFORM FEE NAAR BEDRIJF MIGRATIE
-- Created: 2026-01-26
-- Description: Verplaatst boekingskosten van klant naar bedrijf
--              Platform fee wordt van bedrijf afgetrokken, niet van klant
-- ============================================================================

-- 1. Voeg platform_fee kolom toe (het bedrag dat van het bedrijf wordt afgetrokken)
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS platform_fee DECIMAL(10,2) DEFAULT 1.75 COMMENT 'Platform fee afgetrokken van bedrijf';

-- 2. Voeg business_payout kolom toe (wat het bedrijf daadwerkelijk ontvangt)
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS business_payout DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Netto uitbetaling aan bedrijf na platform fee';

-- 3. Update bestaande boekingen: bereken business_payout
-- Voor bestaande boekingen waar admin_fee bij klant was opgeteld:
-- business_payout = total_price - admin_fee (want admin_fee was bij klant)
-- Voor nieuwe boekingen wordt dit anders berekend in de code
UPDATE bookings
SET platform_fee = 1.75,
    business_payout = GREATEST(0, service_price - loyalty_discount - 1.75)
WHERE business_payout = 0 OR business_payout IS NULL;

-- 4. Index voor rapportage per bedrijf
CREATE INDEX IF NOT EXISTS idx_bookings_business_payout ON bookings(business_id, business_payout, payment_status);

-- 5. Optionele: view voor bedrijfsoverzicht uitbetalingen
CREATE OR REPLACE VIEW business_payout_summary AS
SELECT
    b.business_id,
    biz.company_name,
    DATE(b.appointment_date) as booking_date,
    COUNT(*) as total_bookings,
    SUM(b.service_price) as total_service_revenue,
    SUM(b.platform_fee) as total_platform_fees,
    SUM(b.business_payout) as total_payout,
    SUM(b.loyalty_discount) as total_loyalty_discounts
FROM bookings b
JOIN businesses biz ON b.business_id = biz.id
WHERE b.payment_status = 'paid'
  AND b.status NOT IN ('cancelled', 'rejected')
GROUP BY b.business_id, biz.company_name, DATE(b.appointment_date);
