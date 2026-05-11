-- ============================================================
-- Migration v6: Restaurants jadvalini to'ldirish
-- MySQL 8.0 bilan mos — procedure orqali xavfsiz
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP PROCEDURE IF EXISTS migration_v6;

DELIMITER //
CREATE PROCEDURE migration_v6()
BEGIN

-- restaurants: name_uz
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='name_uz') THEN
    ALTER TABLE `restaurants` ADD COLUMN `name_uz` VARCHAR(255) NULL;
END IF;

-- restaurants: name_ru
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='name_ru') THEN
    ALTER TABLE `restaurants` ADD COLUMN `name_ru` VARCHAR(255) NULL;
END IF;

-- restaurants: name_en
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='name_en') THEN
    ALTER TABLE `restaurants` ADD COLUMN `name_en` VARCHAR(255) NULL;
END IF;

-- restaurants: description_uz
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='description_uz') THEN
    ALTER TABLE `restaurants` ADD COLUMN `description_uz` TEXT NULL;
END IF;

-- restaurants: description_ru
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='description_ru') THEN
    ALTER TABLE `restaurants` ADD COLUMN `description_ru` TEXT NULL;
END IF;

-- restaurants: description_en
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='description_en') THEN
    ALTER TABLE `restaurants` ADD COLUMN `description_en` TEXT NULL;
END IF;

-- restaurants: price_range
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='price_range') THEN
    ALTER TABLE `restaurants` ADD COLUMN `price_range` ENUM('low','mid','high') DEFAULT 'mid';
END IF;

-- restaurants: image
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='image') THEN
    ALTER TABLE `restaurants` ADD COLUMN `image` VARCHAR(500) NULL;
END IF;

-- restaurants: latitude
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='latitude') THEN
    ALTER TABLE `restaurants` ADD COLUMN `latitude` DECIMAL(10,7) NULL;
END IF;

-- restaurants: longitude
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='longitude') THEN
    ALTER TABLE `restaurants` ADD COLUMN `longitude` DECIMAL(10,7) NULL;
END IF;

-- restaurants: status
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='status') THEN
    ALTER TABLE `restaurants` ADD COLUMN `status` ENUM('active','draft') DEFAULT 'active';
END IF;

-- restaurants: views
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='views') THEN
    ALTER TABLE `restaurants` ADD COLUMN `views` INT DEFAULT 0;
END IF;

-- restaurants: video_url
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='restaurants' AND COLUMN_NAME='video_url') THEN
    ALTER TABLE `restaurants` ADD COLUMN `video_url` VARCHAR(500) NULL;
END IF;

-- places: video_url
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='places' AND COLUMN_NAME='video_url') THEN
    ALTER TABLE `places` ADD COLUMN `video_url` VARCHAR(500) NULL;
END IF;

-- people: video_url
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='people' AND COLUMN_NAME='video_url') THEN
    ALTER TABLE `people` ADD COLUMN `video_url` VARCHAR(500) NULL;
END IF;

-- people: bio_en
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='people' AND COLUMN_NAME='bio_en') THEN
    ALTER TABLE `people` ADD COLUMN `bio_en` TEXT NULL;
END IF;

-- gallery: caption
IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='gallery' AND COLUMN_NAME='caption') THEN
    ALTER TABLE `gallery` ADD COLUMN `caption` VARCHAR(255) NULL;
END IF;

-- Mavjud ma'lumotlarni yangi ustunlarga ko'chirish
UPDATE `restaurants` SET
    `name_uz` = `name`,
    `description_uz` = COALESCE(`description`, ''),
    `price_range` = COALESCE(`price_level`, 'mid'),
    `image` = `image_path`,
    `status` = 'active'
WHERE (`name_uz` IS NULL OR `name_uz` = '');

END//
DELIMITER ;

CALL migration_v6();
DROP PROCEDURE IF EXISTS migration_v6;

SET FOREIGN_KEY_CHECKS = 1;
SELECT 'Migration v6 completed successfully!' AS status;
