-- ============================================================
-- Migration v2: Idempotent — safe to re-run multiple times
-- Uses stored procedures to check column existence before adding
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

-- Helper: add fulltext index only if it doesn't exist
DROP PROCEDURE IF EXISTS add_fulltext_safe;
DELIMITER //
CREATE PROCEDURE add_fulltext_safe(
    IN tbl VARCHAR(64),
    IN idx VARCHAR(64),
    IN cols TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND INDEX_NAME = idx
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD FULLTEXT INDEX `', idx, '` (', cols, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

-- ----------------------------------------------------------------
-- 1. places
-- ----------------------------------------------------------------
CALL add_column_safe('places', 'description_en', 'TEXT NULL AFTER `description_ru`');
CALL add_column_safe('places', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');

-- ----------------------------------------------------------------
-- 2. people
-- ----------------------------------------------------------------
CALL add_column_safe('people', 'bio_en',     'TEXT NULL AFTER `bio_ru`');
CALL add_column_safe('people', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');

-- ----------------------------------------------------------------
-- 3. restaurants
-- ----------------------------------------------------------------
CALL add_column_safe('restaurants', 'name_ru',        'VARCHAR(255) NULL AFTER `name`');
CALL add_column_safe('restaurants', 'name_en',        'VARCHAR(255) NULL AFTER `name_ru`');
CALL add_column_safe('restaurants', 'description_ru', 'TEXT NULL AFTER `description_uz`');
CALL add_column_safe('restaurants', 'description_en', 'TEXT NULL AFTER `description_ru`');
CALL add_column_safe('restaurants', 'updated_at',     'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');

-- ----------------------------------------------------------------
-- 4. hotels
-- ----------------------------------------------------------------
CALL add_column_safe('hotels', 'name_ru',        'VARCHAR(255) NULL AFTER `name`');
CALL add_column_safe('hotels', 'name_en',        'VARCHAR(255) NULL AFTER `name_ru`');
CALL add_column_safe('hotels', 'description_ru', 'TEXT NULL AFTER `description_uz`');
CALL add_column_safe('hotels', 'description_en', 'TEXT NULL AFTER `description_ru`');
CALL add_column_safe('hotels', 'latitude',       'DECIMAL(10,7) NULL AFTER `address`');
CALL add_column_safe('hotels', 'longitude',      'DECIMAL(10,7) NULL AFTER `latitude`');
CALL add_column_safe('hotels', 'updated_at',     'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');

-- ----------------------------------------------------------------
-- 5. regions
-- ----------------------------------------------------------------
CALL add_column_safe('regions', 'image',      'VARCHAR(255) NULL AFTER `slug`');
CALL add_column_safe('regions', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');

-- ----------------------------------------------------------------
-- 6. admins
-- ----------------------------------------------------------------
CALL add_column_safe('admins', 'role',       "ENUM('superadmin','editor') NOT NULL DEFAULT 'editor' AFTER `username`");
CALL add_column_safe('admins', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`');

-- Update existing admins to superadmin
UPDATE `admins` SET `role` = 'superadmin' WHERE `role` = 'editor';

-- ----------------------------------------------------------------
-- 7. Full-text indexes (idempotent)
-- ----------------------------------------------------------------
CALL add_fulltext_safe('places',      'ft_places_search',      '`name_uz`, `name_ru`, `name_en`, `description_uz`, `description_ru`, `description_en`');
CALL add_fulltext_safe('people',      'ft_people_search',      '`name_uz`, `name_ru`, `name_en`, `bio_uz`, `bio_ru`, `bio_en`');
CALL add_fulltext_safe('restaurants', 'ft_restaurants_search', '`name`, `name_ru`, `name_en`, `description_uz`, `description_ru`, `description_en`');
CALL add_fulltext_safe('hotels',      'ft_hotels_search',      '`name`, `name_ru`, `name_en`, `description_uz`, `description_ru`, `description_en`');

-- Cleanup helper procedures (optional)
DROP PROCEDURE IF EXISTS add_column_safe;
DROP PROCEDURE IF EXISTS add_fulltext_safe;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Migration completed successfully!' AS status;
