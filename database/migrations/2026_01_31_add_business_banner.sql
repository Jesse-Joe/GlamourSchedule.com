-- Add banner image support to businesses table
-- Migration: 2026_01_31_add_business_banner.sql

-- Add banner_image column for the uploaded banner path
ALTER TABLE businesses
ADD COLUMN banner_image VARCHAR(255) NULL AFTER cover_image;

-- Add banner_position for crop position (top/center/bottom)
ALTER TABLE businesses
ADD COLUMN banner_position VARCHAR(20) DEFAULT 'center' AFTER banner_image;

-- Add index for faster queries on banner_image
CREATE INDEX idx_businesses_banner ON businesses(banner_image);
