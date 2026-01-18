-- Migration: Add language column to businesses table
-- Date: 2026-01-18
-- Description: Enables multi-language notifications per business

-- Add language column to businesses table (after city column)
ALTER TABLE businesses ADD COLUMN language VARCHAR(5) DEFAULT 'nl' AFTER city;

-- Create index for faster lookups
CREATE INDEX idx_businesses_language ON businesses(language);

-- Update existing businesses to use 'nl' as default (already handled by DEFAULT, but explicit for clarity)
UPDATE businesses SET language = 'nl' WHERE language IS NULL;
