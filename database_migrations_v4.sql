-- ============================================================
-- Migration v4: Booking / Reservation System
-- Idempotent — safe to re-run multiple times
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------
-- 1. Bookings — bron qilish tizimi (mehmonxona va restoranlar)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entity_type` ENUM('hotel','restaurant') NOT NULL,
    `entity_id` INT NOT NULL,
    `guest_name` VARCHAR(100) NOT NULL,
    `guest_phone` VARCHAR(30) NOT NULL,
    `guest_email` VARCHAR(150) NULL,
    `check_in` DATE NOT NULL,
    `check_out` DATE NULL COMMENT 'Faqat mehmonxona uchun',
    `guests_count` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `message` TEXT NULL,
    `status` ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(`entity_type`, `entity_id`),
    INDEX(`status`),
    INDEX(`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Migration v4 (Bookings) completed successfully!' AS status;
