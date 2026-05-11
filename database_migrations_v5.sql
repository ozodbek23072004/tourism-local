-- ============================================================
-- Migration v5: Gallery limit & Hero Sliders file upload support
-- Idempotent — safe to re-run multiple times
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------
-- 1. Universal gallery table (if not exists)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entity_type` ENUM('place','person','restaurant','hotel') NOT NULL,
    `entity_id` INT NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 2. Hero Sliders table (ensure exists)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hero_sliders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_key` VARCHAR(50) NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Migration v5 completed successfully!' AS status;
