-- Migration: Add reminder tracking to businesses
-- Date: 2026-01-31
-- Purpose: Track when dashboard reminder emails are sent (24h after registration)

ALTER TABLE businesses
ADD COLUMN reminder_sent_at DATETIME NULL DEFAULT NULL AFTER admin_verified_at;

-- Index for efficient querying of businesses that need reminders
CREATE INDEX idx_businesses_reminder ON businesses (status, reminder_sent_at, created_at);
