-- =====================================================
-- GLAMOURSCHEDULE - EMPLOYEES SYSTEM MIGRATION
-- Created: 2026-01-15
-- =====================================================

-- Add business_type column to businesses table
ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS business_type ENUM('eenmanszaak', 'bv') DEFAULT 'eenmanszaak' AFTER status;

-- Add employee_count column to businesses table
ALTER TABLE businesses
ADD COLUMN IF NOT EXISTS employee_count INT DEFAULT 0 AFTER business_type;

-- Create employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    photo VARCHAR(500) NULL,
    bio TEXT NULL,
    specialties TEXT NULL COMMENT 'JSON array of specialty/service IDs',
    is_active TINYINT(1) DEFAULT 1,
    color VARCHAR(7) DEFAULT '#000000' COMMENT 'Calendar color for this employee',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_active (business_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create employee_services junction table (which services each employee can perform)
CREATE TABLE IF NOT EXISTS employee_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    service_id INT NOT NULL,

    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_service (employee_id, service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create employee_hours table (working hours per employee)
CREATE TABLE IF NOT EXISTS employee_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '0=Sunday, 6=Saturday',
    open_time TIME NULL,
    close_time TIME NULL,
    is_closed TINYINT(1) DEFAULT 0,

    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee_day (employee_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add employee_id to bookings table (nullable for backwards compatibility)
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS employee_id INT NULL AFTER business_id,
ADD FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL;

-- Create index for employee bookings
ALTER TABLE bookings ADD INDEX IF NOT EXISTS idx_employee_date (employee_id, booking_date);
