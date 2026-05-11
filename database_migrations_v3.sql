-- ============================================================
-- Migration v3: New features — Gallery, Reviews, Favorites, Cross-links
-- Idempotent — safe to re-run multiple times
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Helper: add column only if it doesn't exist
DROP PROCEDURE IF EXISTS add_column_safe;
DELIMITER //
CREATE PROCEDURE add_column_safe(
    IN tbl VARCHAR(64),
    IN col VARCHAR(64),
    IN definition TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

-- ----------------------------------------------------------------
-- 1. Place Gallery — multiple images per place
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `place_gallery` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `place_id` INT NOT NULL,
    `image` VARCHAR(500) NOT NULL,
    `caption` VARCHAR(255) NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`place_id`),
    FOREIGN KEY (`place_id`) REFERENCES `places`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 2. Reviews — user reviews with ratings (guest or named)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entity_type` ENUM('place','hotel','restaurant','person') NOT NULL,
    `entity_id` INT NOT NULL,
    `author_name` VARCHAR(100) NOT NULL DEFAULT 'Mehmon',
    `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '1-5 stars',
    `comment` TEXT NULL,
    `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`entity_type`, `entity_id`),
    INDEX(`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 3. Favorites — saved by visitor (cookie/session based)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(128) NOT NULL,
    `entity_type` ENUM('place','hotel','restaurant','person') NOT NULL,
    `entity_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_fav` (`session_id`, `entity_type`, `entity_id`),
    INDEX(`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 4. Place ↔ Person cross-links
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `place_people` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `place_id` INT NOT NULL,
    `person_id` INT NOT NULL,
    `relationship` VARCHAR(255) NULL COMMENT 'e.g. Built by, Buried here, Lived here',
    UNIQUE KEY `uq_pp` (`place_id`, `person_id`),
    FOREIGN KEY (`place_id`) REFERENCES `places`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`person_id`) REFERENCES `people`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 5. Add name_uz to restaurants and hotels if missing
-- ----------------------------------------------------------------
CALL add_column_safe('restaurants', 'name_uz', "VARCHAR(255) NULL AFTER `name`");
CALL add_column_safe('hotels', 'name_uz', "VARCHAR(255) NULL AFTER `name`");

-- Sync name_uz from name if empty
UPDATE restaurants SET name_uz = name WHERE name_uz IS NULL OR name_uz = '';
UPDATE hotels SET name_uz = name WHERE name_uz IS NULL OR name_uz = '';

-- ----------------------------------------------------------------
-- Cleanup
-- ----------------------------------------------------------------
DROP PROCEDURE IF EXISTS add_column_safe;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Migration v3 completed successfully!' AS status;
