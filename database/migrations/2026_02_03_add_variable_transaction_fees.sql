-- Migration: Add variable transaction fees per country
-- Date: 2026-02-03
-- Description: Adds transaction_fee and currency_code columns to country_promotions
--              for PPP-adjusted pricing per country

-- 1. Add columns to country_promotions
ALTER TABLE country_promotions
ADD COLUMN IF NOT EXISTS transaction_fee DECIMAL(5,2) DEFAULT 1.75,
ADD COLUMN IF NOT EXISTS currency_code VARCHAR(3) DEFAULT 'EUR';

-- 2. Set transaction fees by income tier (based on purchasing power parity)

-- Premium tier: Switzerland, Norway, Scandinavia, etc. (€2.50)
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'CHF' WHERE country_code = 'CH';
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'NOK' WHERE country_code = 'NO';
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'SEK' WHERE country_code = 'SE';
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'DKK' WHERE country_code = 'DK';
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'ISK' WHERE country_code = 'IS';
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'CHF' WHERE country_code = 'LI';
UPDATE country_promotions SET transaction_fee = 2.50, currency_code = 'EUR' WHERE country_code = 'MC';

-- High tier: US, Canada, Australia, Singapore, Gulf states (€1.99)
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'USD' WHERE country_code = 'US';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'CAD' WHERE country_code = 'CA';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'AUD' WHERE country_code = 'AU';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'SGD' WHERE country_code = 'SG';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'AED' WHERE country_code = 'AE';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'QAR' WHERE country_code = 'QA';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'KWD' WHERE country_code = 'KW';
UPDATE country_promotions SET transaction_fee = 1.99, currency_code = 'BHD' WHERE country_code = 'BH';

-- Standard tier: EU countries (€1.75)
UPDATE country_promotions SET transaction_fee = 1.75, currency_code = 'EUR'
WHERE country_code IN ('NL', 'BE', 'DE', 'FR', 'AT', 'LU', 'IE', 'FI', 'IT', 'ES', 'PT');

UPDATE country_promotions SET transaction_fee = 1.75, currency_code = 'GBP' WHERE country_code = 'GB';
UPDATE country_promotions SET transaction_fee = 1.75, currency_code = 'NZD' WHERE country_code = 'NZ';
UPDATE country_promotions SET transaction_fee = 1.75, currency_code = 'JPY' WHERE country_code = 'JP';

-- Medium-high tier (€1.25)
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'PLN' WHERE country_code = 'PL';
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'CZK' WHERE country_code = 'CZ';
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'EUR' WHERE country_code IN ('EE', 'LV', 'LT', 'SK', 'SI', 'HR', 'GR', 'CY', 'MT');
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'HKD' WHERE country_code = 'HK';
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'KRW' WHERE country_code = 'KR';
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'ILS' WHERE country_code = 'IL';
UPDATE country_promotions SET transaction_fee = 1.25, currency_code = 'SAR' WHERE country_code = 'SA';

-- Tier 3: Medium income (€0.99)
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'HUF' WHERE country_code = 'HU';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'RON' WHERE country_code = 'RO';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'BGN' WHERE country_code = 'BG';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'BRL' WHERE country_code = 'BR';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'MXN' WHERE country_code = 'MX';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'TRY' WHERE country_code = 'TR';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'ZAR' WHERE country_code = 'ZA';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'MYR' WHERE country_code = 'MY';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'THB' WHERE country_code = 'TH';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'CNY' WHERE country_code = 'CN';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'ARS' WHERE country_code = 'AR';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'CLP' WHERE country_code = 'CL';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'COP' WHERE country_code = 'CO';
UPDATE country_promotions SET transaction_fee = 0.99, currency_code = 'PEN' WHERE country_code = 'PE';

-- Tier 4: Lower income (€0.50)
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'INR' WHERE country_code = 'IN';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'IDR' WHERE country_code = 'ID';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'PHP' WHERE country_code = 'PH';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'VND' WHERE country_code = 'VN';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'PKR' WHERE country_code = 'PK';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'BDT' WHERE country_code = 'BD';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'NGN' WHERE country_code = 'NG';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'KES' WHERE country_code = 'KE';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'EGP' WHERE country_code = 'EG';
UPDATE country_promotions SET transaction_fee = 0.50, currency_code = 'UAH' WHERE country_code = 'UA';
