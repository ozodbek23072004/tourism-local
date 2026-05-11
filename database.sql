-- Database Schema for Tourism Project
-- All tables are InnoDB, utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `regions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_uz` VARCHAR(255) NOT NULL,
    `name_ru` VARCHAR(255) NOT NULL,
    `name_en` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_uz` VARCHAR(255) NOT NULL,
    `name_ru` VARCHAR(255) NOT NULL,
    `name_en` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(50),
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `places` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_uz` VARCHAR(255) NOT NULL,
    `name_ru` VARCHAR(255) NOT NULL,
    `name_en` VARCHAR(255) NOT NULL,
    `description_uz` TEXT,
    `description_ru` TEXT,
    `category_id` INT,
    `region_id` INT,
    `address` VARCHAR(500),
    `latitude` DECIMAL(10,7),
    `longitude` DECIMAL(10,7),
    `image` VARCHAR(255),
    `status` ENUM('active','draft') DEFAULT 'draft',
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`status`),
    INDEX(`category_id`),
    INDEX(`region_id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `people` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_uz` VARCHAR(255) NOT NULL,
    `name_ru` VARCHAR(255) NOT NULL,
    `name_en` VARCHAR(255) NOT NULL,
    `bio_uz` TEXT,
    `bio_ru` TEXT,
    `born_year` YEAR,
    `died_year` YEAR NULL,
    `region_id` INT,
    `image` VARCHAR(255),
    `status` ENUM('active','draft') DEFAULT 'draft',
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`status`),
    INDEX(`region_id`),
    FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `restaurants` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description_uz` TEXT,
    `cuisine_type` VARCHAR(100),
    `price_range` ENUM('low','mid','high'),
    `phone` VARCHAR(20),
    `working_hours` VARCHAR(100),
    `region_id` INT,
    `address` VARCHAR(500),
    `latitude` DECIMAL(10,7),
    `longitude` DECIMAL(10,7),
    `image` VARCHAR(255),
    `status` ENUM('active','draft') DEFAULT 'draft',
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`status`),
    INDEX(`region_id`),
    FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hotels` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description_uz` TEXT,
    `stars` TINYINT,
    `price_from` DECIMAL(10,2),
    `phone` VARCHAR(20),
    `region_id` INT,
    `address` VARCHAR(500),
    `image` VARCHAR(255),
    `status` ENUM('active','draft') DEFAULT 'draft',
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(`status`),
    INDEX(`region_id`),
    FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (username: admin, password: admin123)
INSERT INTO `admins` (`username`, `password`) VALUES ('admin', '$2y$10$DKG1rjvQ2Y2ZcRFgrkeu.uZ2hn9JquE9mwO7Vs.eB.G2DxToW28Ya');

SET FOREIGN_KEY_CHECKS = 1;
