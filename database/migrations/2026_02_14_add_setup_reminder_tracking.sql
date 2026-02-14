-- Add setup reminder tracking columns to businesses table
-- Used by GlamoriManager to track escalating setup reminders

ALTER TABLE businesses
  ADD COLUMN setup_reminder_count INT DEFAULT 0 AFTER reminder_sent_at,
  ADD COLUMN setup_reminder_last_sent_at DATETIME NULL AFTER setup_reminder_count;
