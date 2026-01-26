-- Migration: Add verification_code column to bookings table
-- This code is a SHA256-based verification code that links business_id with customer
-- Similar to Bitcoin address format for easy manual entry

ALTER TABLE bookings
ADD COLUMN verification_code VARCHAR(16) NULL AFTER qr_code_hash;

-- Create index for fast lookup
CREATE INDEX idx_bookings_verification_code ON bookings(verification_code);

-- Update existing bookings with verification codes (will be generated via PHP for new bookings)
-- Existing bookings will use booking_number as fallback
