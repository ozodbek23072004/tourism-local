-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 11, 2026 at 08:58 AM
-- Server version: 8.0.30
-- PHP Version: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tourism_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`%` PROCEDURE `add_column_safe` (IN `tbl` VARCHAR(64), IN `col` VARCHAR(64), IN `definition` TEXT)   BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `add_fulltext_safe` (IN `tbl` VARCHAR(64), IN `idx` VARCHAR(64), IN `cols` TEXT)   BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND INDEX_NAME = idx
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', tbl, '` ADD FULLTEXT INDEX `', idx, '` (', cols, ')');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('superadmin','editor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'editor',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'superadmin', '$2y$10$DKG1rjvQ2Y2ZcRFgrkeu.uZ2hn9JquE9mwO7Vs.eB.G2DxToW28Ya', '2026-04-28 12:33:37', '2026-04-28 15:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `entity_type` enum('hotel','restaurant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `guest_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guest_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date DEFAULT NULL COMMENT 'Faqat mehmonxona uchun',
  `guests_count` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name_uz` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name_uz`, `name_ru`, `name_en`, `icon`, `slug`, `created_at`) VALUES
(1, 'Tarixiy obidalar', '???????????? ?????????', 'Historical monuments', 'museum', 'historical', '2026-04-28 13:18:17'),
(2, 'Tabiat', '???????', 'Nature', 'landscape', 'nature', '2026-04-28 13:18:17'),
(3, 'Muzeylar', '?????', 'Museums', 'account_balance', 'museums', '2026-04-28 13:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` enum('place','hotel','restaurant','person') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int NOT NULL,
  `entity_type` enum('place','person','hotel','restaurant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `image_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `entity_type`, `entity_id`, `image_path`, `caption`, `sort_order`, `created_at`) VALUES
(1, 'place', 10, 'gallery/places/10/img_69fadcbf321571.29307940.webp', NULL, 1, '2026-05-06 06:16:31'),
(2, 'place', 10, 'gallery/places/10/img_69fadce5b81af0.79999296.webp', NULL, 2, '2026-05-06 06:17:12'),
(3, 'place', 10, 'gallery/places/10/img_69fadce817aa83.06802712.webp', NULL, 3, '2026-05-06 06:17:12'),
(4, 'place', 10, 'gallery/places/10/img_69fadce85f91c3.96118987.webp', NULL, 4, '2026-05-06 06:17:12'),
(5, 'place', 10, 'gallery/places/10/img_69fadce8633df0.77236698.webp', NULL, 5, '2026-05-06 06:17:12'),
(6, 'place', 10, 'gallery/places/10/img_69fadce8703600.07519511.webp', NULL, 6, '2026-05-06 06:17:12');

-- --------------------------------------------------------

--
-- Table structure for table `hero_sliders`
--

CREATE TABLE `hero_sliders` (
  `id` int NOT NULL,
  `page_key` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hero_sliders`
--

INSERT INTO `hero_sliders` (`id`, `page_key`, `image_path`, `sort_order`, `created_at`) VALUES
(141, 'home', '/public/uploads/sliders/home_1.jpg', 0, '2026-05-05 13:01:52'),
(142, 'home', '/public/uploads/sliders/home_2.jpg', 1, '2026-05-05 13:01:52'),
(143, 'home', '/public/uploads/sliders/home_3.jpg', 2, '2026-05-05 13:01:52'),
(144, 'home', '/public/uploads/sliders/home_4.jpg', 3, '2026-05-05 13:01:52'),
(145, 'home', '/public/uploads/sliders/home_5.jpg', 4, '2026-05-05 13:01:52'),
(146, 'home', '/public/uploads/sliders/home_6.jpg', 5, '2026-05-05 13:01:52'),
(147, 'home', '/public/uploads/sliders/home_7.jpg', 6, '2026-05-05 13:01:52'),
(148, 'restaurants', '/public/uploads/sliders/rest_1.jpg', 0, '2026-05-05 13:01:52'),
(149, 'restaurants', '/public/uploads/sliders/rest_2.jpg', 1, '2026-05-05 13:01:52'),
(150, 'restaurants', '/public/uploads/sliders/rest_3.jpg', 2, '2026-05-05 13:01:52'),
(151, 'restaurants', '/public/uploads/sliders/rest_4.jpg', 3, '2026-05-05 13:01:52'),
(152, 'restaurants', '/public/uploads/sliders/rest_5.jpg', 4, '2026-05-05 13:01:52'),
(153, 'restaurants', '/public/uploads/sliders/rest_6.jpg', 5, '2026-05-05 13:01:52'),
(154, 'restaurants', '/public/uploads/sliders/rest_7.jpg', 6, '2026-05-05 13:01:52'),
(155, 'hotels', '/public/uploads/sliders/hotel_1.jpg', 0, '2026-05-05 13:01:52'),
(156, 'hotels', '/public/uploads/sliders/hotel_2.jpg', 1, '2026-05-05 13:01:52'),
(157, 'hotels', '/public/uploads/sliders/hotel_3.jpg', 2, '2026-05-05 13:01:52'),
(158, 'hotels', '/public/uploads/sliders/hotel_4.jpg', 3, '2026-05-05 13:01:52'),
(159, 'hotels', '/public/uploads/sliders/hotel_5.jpg', 4, '2026-05-05 13:01:52'),
(160, 'hotels', '/public/uploads/sliders/hotel_6.jpg', 5, '2026-05-05 13:01:52'),
(161, 'hotels', '/public/uploads/sliders/hotel_7.jpg', 6, '2026-05-05 13:01:52'),
(162, 'places', '/public/uploads/sliders/places_1.jpg', 0, '2026-05-05 13:01:52'),
(163, 'places', '/public/uploads/sliders/places_2.jpg', 1, '2026-05-05 13:01:52'),
(164, 'places', '/public/uploads/sliders/places_3.jpg', 2, '2026-05-05 13:01:52'),
(165, 'places', '/public/uploads/sliders/places_4.jpg', 3, '2026-05-05 13:01:52'),
(166, 'places', '/public/uploads/sliders/places_5.jpg', 4, '2026-05-05 13:01:52'),
(167, 'places', '/public/uploads/sliders/places_6.jpg', 5, '2026-05-05 13:01:52'),
(168, 'places', '/public/uploads/sliders/places_7.jpg', 6, '2026-05-05 13:01:52'),
(169, 'people', '/public/uploads/sliders/people_1.jpg', 0, '2026-05-05 13:01:52'),
(170, 'people', '/public/uploads/sliders/people_2.jpg', 1, '2026-05-05 13:01:52'),
(171, 'people', '/public/uploads/sliders/people_3.jpg', 2, '2026-05-05 13:01:52'),
(172, 'people', '/public/uploads/sliders/people_4.jpg', 3, '2026-05-05 13:01:52'),
(173, 'people', '/public/uploads/sliders/people_5.jpg', 4, '2026-05-05 13:01:52'),
(174, 'people', '/public/uploads/sliders/people_6.jpg', 5, '2026-05-05 13:01:52'),
(175, 'people', '/public/uploads/sliders/people_7.jpg', 6, '2026-05-05 13:01:52');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int NOT NULL,
  `name_uz` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ru` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_uz` text COLLATE utf8mb4_unicode_ci,
  `description_ru` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `stars` tinyint DEFAULT NULL,
  `price_from` decimal(10,2) DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `id` int NOT NULL,
  `name_uz` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio_uz` text COLLATE utf8mb4_unicode_ci,
  `bio_ru` text COLLATE utf8mb4_unicode_ci,
  `bio_en` text COLLATE utf8mb4_unicode_ci,
  `born_year` int DEFAULT NULL,
  `died_year` int DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `people`
--

INSERT INTO `people` (`id`, `name_uz`, `name_ru`, `name_en`, `bio_uz`, `bio_ru`, `bio_en`, `born_year`, `died_year`, `region_id`, `image`, `video_url`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Amir Temur', 'Амир Тимур', 'Amir Timur', 'Amir Temur (1336-1405) — buyuk davlat arbobi, mohir sarkarda va markazlashgan Temuriylar davlatining asoschisi. U Shahrisabz yaqinidagi Xo\'ja Ilg\'or qishlog\'ida tug\'ilgan. Temur o\'zining harbiy yurishlari natijasida Hindistondan Turkiyagacha bo\'lgan ulkan imperiyani barpo etgan. U nafaqat jangchi, balki madaniyat va ilm-fan homiysi ham bo\'lgan. Samarqandni poytaxt etib belgilab, uni dunyoning eng go\'zal shahriga aylantirgan. \'Kuch - adolatdadir\' shiori uning davlat boshqaruvidagi asosiy tamoyili edi.', 'Амир Тимур (1336-1405) — великий государственный деятель, полководец и основатель империи Тимуридов со столицей в Самарканде. Он родился в селении Ходжа-Ильгар недалеко от Шахрисабза. В результате своих походов он создал огромную державу, простиравшуюся от Индии до Средиземного моря. Тимур уделял большое внимание развитию науки, культуры и архитектуры. Самарканд при нем стал крупнейшим культурным и научным центром Востока. Его девиз \'Сила в справедливости\' стал основой его правления.', '', 1336, 1405, 1, 'amirtemur.png', 'https://youtu.be/rhCqrCOhqiY?si=z3I4Kdu_eQD1IHDf', 'active', 4, '2026-04-28 13:34:39', '2026-05-06 18:47:32'),
(2, 'Mirzo Ulug&#039;bek', 'Мирзо Улугбек', 'Mirzo Ulugh Beg', 'Mirzo Ulug\'bek (1394-1449) — buyuk astronom va matematik, Amir Temurning nabirasi va Samarqand hukmdori. U Samarqandda o\'sha davrdagi eng mashhur rasadxonani barpo etgan va 1018 ta yulduzning holatini aniq tasvirlab bergan \'Ziji jadidi Ko\'ragoniy\' asarini yozgan. Ulug\'bek davrida Samarqand jahon ilm-fani markaziga aylandi. U ilm-fanga bo\'lgan ishtiyoqi tufayli nafaqat hukmdor, balki buyuk olim sifatida ham tarixda qoldi.', 'Мирзо Улугбек (1394-1449) — великий астроном и математик, внук Амира Тимура и правитель Самарканда. Он построил в Самарканде уникальную обсерваторию, где составил \'Зидж Улугбека\' — каталог 1018 звезд, отличающийся поразительной точностью. При его правлении Самарканд стал мировой столицей науки. Улугбек вошел в историю как \'ученый на троне\', для которого знания были превыше власти.', '', 1394, 1449, 1, 'ulughbek.png', 'https://youtu.be/VZhedHnvwKg?si=NydQWc9d24oBsLnI', 'active', 5, '2026-04-28 13:34:39', '2026-05-06 18:47:26'),
(3, 'Abu Ali ibn Sino', 'Абу Али ибн Сина', 'Avicenna', 'Abu Ali ibn Sino (980-1037) — dunyoga mashhur olim, faylasuf va tabib, G\'arbda Avitsenna nomi bilan tanilgan. U Buxoro yaqinidagi Afshona qishlog\'ida tug\'ilgan. Uning \'Tib qonunlari\' asari bir necha asrlar davomida Yevropa universitetlarida asosiy darslik bo\'lib xizmat qilgan. Ibn Sino tibbiyotdan tashqari, falsafa, astronomiya va matematika sohalarida ham buyuk kashfiyotlar qilgan. U jami 450 dan ortiq asar yozgan bo\'lib, ulardan 240 tasi bizgacha saqlanib qolgan.', 'Абу Али ибн Сина (980-1037), известный на Западе как Авиценна — великий ученый-энциклопедист, философ и врач. Родился в селении Афшана близ Бухары. Его главный труд \'Канон врачебной науки\' на протяжении веков был настольной книгой медиков во всем мире. Ибн Сина внес неоценимый вклад в развитие философии, астрономии и математики. Он является автором более 450 трудов, охватывающих почти все области знаний того времени.', '', 980, 1037, 2, 'ibnsino.png', 'https://youtu.be/AbiATAA6xAU?si=E2N6iGkUt8lsaTY2', 'active', 3, '2026-04-28 13:34:39', '2026-05-06 18:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `places`
--

CREATE TABLE `places` (
  `id` int NOT NULL,
  `name_uz` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_uz` text COLLATE utf8mb4_unicode_ci,
  `description_ru` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `category_id` int DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `places`
--

INSERT INTO `places` (`id`, `name_uz`, `name_ru`, `name_en`, `description_uz`, `description_ru`, `description_en`, `category_id`, `region_id`, `address`, `latitude`, `longitude`, `image`, `video_url`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Registon maydoni', 'Площадь Регистан', 'Registan Square', 'Registon maydoni — Samarqand shahrining qadimiy markazi bo\'lib, o\'zining uchta ulkan va mahobatli madrasasi bilan butun dunyoga mashhur. Bu maydon asrlar davomida shahar ijtimoiy, iqtisodiy va siyosiy hayotining markazi bo\'lib xizmat qilgan. \r\n\r\n1. Ulug\'bek madrasasi (1417–1420): Mirzo Ulug\'bek tomonidan bunyod etilgan ushbu madrasa o\'z davrining eng yirik ilmiy akademiyasi bo\'lgan. Bu yerda matematika, astronomiya va din ilmlari o\'qitilgan. Madrasa peshtog\'idagi yulduzli naqshlar Ulug\'bekning ilm-fanga bo\'lgan ishtiyoqini anglatadi.\r\n2. Sherdor madrasasi (1619–1636): Samarqand hokimi Yalangto\'sh Bahodir buyrug\'i bilan qurilgan. Uning peshtog\'ida ikki sher (yo\'lbars) ohu ortidan quvayotgani va ularning ortida odam qiyofasidagi quyosh tasvirlangan. Bu tasvir Samarqand ramziga aylangan.\r\n3. Tillakori madrasasi (1646–1660): Majmuani yakunlovchi ushbu bino ham madrasa, ham juma masjidi vazifasini o\'tagan. Binoning ichki qismi \'kundal\' uslubida oltin suvi bilan nihoyatda boy bezatilgani uchun unga \'Tillakori\' (oltin bilan bezatilgan) nomi berilgan.\r\n\r\nRegiston maydoni 2001-yilda UNESCO Butunjahon merosi ro\'yxatiga kiritilgan va bugungi kunda O\'zbekistonning tashrif qog\'ozi hisoblanadi.', 'Площадь Регистан — это сердце древнего Самарканда, величественный архитектурный ансамбль, признанный во всем мире. На протяжении столетий Регистан был центром торговли, политики и религии.\r\n\r\n1. Медресе Улугбека (1417–1420): Построенное великим астрономом и правителем Мирзо Улугбеком, это здание служило крупнейшим научным центром своего времени. Здесь преподавали точные науки, такие как математика и астрономия. Фасад украшен мозаикой с изображением звезд.\r\n2. Медресе Шердор (1619–1636): Построено по приказу хокима Самарканда Ялангтуша Бахадура. Название \'Шердор\' означает \'Обитель львов\'. На его портале изображены львы (тигры), преследующие ланей на фоне восходящего солнца с человеческим лицом.\r\n3. Медресе Тилля-Кари (1646–1660): Завершает ансамбль с северной стороны. Оно выполняло функции школы и главной мечети города. Интерьер мечети поражает роскошью: огромные поверхности покрыты сусальным золотом в технике \'кундаль\', за что медресе и получило свое название.\r\n\r\nРегистан включен в список Всемирного наследия ЮНЕСКО в 2001 году и является символом мастерства восточных зодчих.', 'Registan Square is the crown jewel of Samarkand, a monumental public square framed by three massive, azure-domed Islamic schools (madrasahs). It was the public center of the city, where royal edicts were read and celebrations held.\r\n\r\n1. Ulugh Beg Madrasah (1417–1420): Built by the scholar-king Ulugh Beg, Timur\'s grandson, it was one of the world\'s leading educational institutions. Stars on its portal reflect Ulugh Beg\'s passion for astronomy.\r\n2. Sher-Dor Madrasah (1619–1636): Commissioned by Yalangtush Bahadur, the governor of Samarkand. Its name means \'Possessing Lions,\' referring to the unique mosaics on its portal showing lions (or tigers) chasing deer under a human-faced sun.\r\n3. Tilla-Kari Madrasah (1646–1660): This final addition served as both a madrasah and the city\'s grand Friday mosque. The interior is lavishly decorated with gold leaf (Tilla-Kari means \'gilded\'), creating a breathtaking golden glow within the prayer hall.\r\n\r\nIn 2001, the entire complex was inscribed on the UNESCO World Heritage List, representing the pinnacle of Timurid and Shaybanid architecture.', 1, 1, '', '39.6537601', '66.9759845', 'registan.png', 'https://youtu.be/iv0Bg1-kem8?si=c6Z6T-AXgaw5sIGY', 'active', 10, '2026-04-28 13:22:36', '2026-05-06 06:53:18'),
(2, 'Ichan Qal&#039;a', 'Ичан-Кала', 'Itchan Kala', 'Ichan Qal\'a — Xiva shahrining markazida joylashgan, baland devorlar bilan o\'ralgan \'ichki shahar\'. Bu shahar Ochiq osmon ostidagi muzey deb ataladi, chunki unda o\'rta asrlarga xos atmosfera va me\'moriy obidalar to\'liq saqlanib qolgan.\r\n\r\nQal\'aning mudofaa devorlari 2 kilometrga yaqin bo\'lib, balandligi 8-10 metrni tashkil etadi. Shaharga to\'rtta darvoza: Ota darvoza, Bog\'cha darvoza, Polvon darvoza va Tosh darvoza orqali kiriladi. \r\n\r\nAsosiy yodgorliklar:\r\n- Kalta Minor: Moviy koshinlar bilan to\'liq qoplangan, ammo qurilishi yakunlanmay qolgan ulkan minora.\r\n- Juma masjidi: 212 ta qadimiy o\'ymakor yog\'och ustunlari bilan mashhur bo\'lgan betakror bino.\r\n- Islom Xo\'ja majmuasi: Xivaning eng baland minorasi va madrasasi.\r\n- Toshhovli saroyi: Xiva xonlarining hashamatli qarorgohi.\r\n\r\nIchan Qal\'a 1990-yilda O\'zbekistondagi birinchi ob\'ekt sifatida UNESCO Butunjahon merosi ro\'yxatiga kiritilgan.', 'Ичан-Кала — это обнесенный мощными стенами внутренний город Хивы, настоящий \'музей под открытым небом\'. Здесь сохранился дух древнего Востока и более 50 уникальных архитектурных памятников.\r\n\r\nСтены крепости имеют протяженность около 2 км и высоту до 10 метров. В город ведут четыре монументальных ворот, ориентированных по сторонам света. \r\n\r\nЗнаковые памятники:\r\n- Минарет Кальта-Минар: Полностью покрыт глазурованной плиткой бирюзового цвета, остался незавершенным, но стал символом города.\r\n- Мечеть Джума: Знаменита своим залом с 212 резными деревянными колоннами, некоторые из которых датируются X веком.\r\n- Дворец Таш-Хаули: Резиденция хивинских ханов с великолепными внутренними двориками и майоликовой отделкой.\r\n- Минарет Ислам-Ходжа: Самый высокий минарет в Хиве, служивший маяком для караванов.\r\n\r\nИчан-Кала стала первым объектом в Узбекистане, включенным в список Всемирного наследия ЮНЕСКО (1990).', 'Itchan Kala is the walled inner city of Khiva, acting as a living \'open-air museum.\' It was the first site in Central Asia to be inscribed on the UNESCO World Heritage List.\r\n\r\nThe city is protected by crenelated mud-brick walls nearly 2 km long and up to 10 meters high. Access is granted through four massive gates: Ata Darvaza, Bagcha Darvaza, Palvan Darvaza, and Tash Darvaza.\r\n\r\nNotable Monuments:\r\n- Kalta Minor: A short but stout minaret entirely covered in exquisite turquoise tiles, left unfinished but iconic.\r\n- Juma Mosque: A unique hypostyle mosque featuring a forest of 212 carved wooden columns, some dating back to the 10th century.\r\n- Tash Khauli Palace: The luxurious residence of the Khiva Khans, renowned for its intricate tilework and courtyards.\r\n- Islam Khoja Minaret: The tallest minaret in Khiva, dominating the city skyline.\r\n\r\nIts labyrinthine streets and well-preserved architecture offer a glimpse into the glory of the Khiva Khanate.', 1, 3, '', NULL, NULL, 'itchan_kala.png', 'https://youtu.be/HjhZSCDWXk4?si=-FlIq2yKn219fGz9', 'active', 0, '2026-04-28 13:22:36', '2026-05-06 06:08:06'),
(3, 'Ark qal&#039;asi', 'Крепость Арк', 'Ark of Bukhara', 'Ark — Buxoro shahrining eng qadimgi yodgorligi bo\'lib, u asrlar davomida hukmdorlar qarorgohi bo\'lib xizmat qilgan ulkan qal\'adir. Qal\'a miloddan avvalgi birinchi ming yillikka borib taqaladi va u sun\'iy barpo etilgan tepalik ustida joylashgan.\r\n\r\nQal\'aning devorlari 789 metr uzunlikka ega bo\'lib, o\'rta asrlarda u haqiqiy \'shahar ichidagi shahar\' bo\'lgan. Uning ichida amirlarning saroyi, masjidlar, zarbxona, xazina, ma\'muriy binolar, otxonalar va hatto zindon ham bo\'lgan.\r\n\r\nAfsonaga ko\'ra, Arkni qadimgi fors shahzodasi Siyovush barpo etgan. Qal\'aga kirishda ikkita ulkan minorali darvoza va baland yo\'lak (pando\'s) orqali o\'tiladi. 1920-yildagi bombardimon natijasida qal\'aning katta qismi vayron bo\'lgan, ammo uning saqlanib qolgan qismlarida hozirgi kunda Buxoro davlat muzey-qo\'riqxonasi ko\'rgazmalari joylashgan.', 'Арк — древняя цитадель в Бухаре, на протяжении веков служившая резиденцией бухарских эмиров. Это мощное фортификационное сооружение, расположенное на искусственном холме, чья история уходит корнями в первое тысячелетие до нашей эры.\r\n\r\nСтены крепости имеют длину 789 метров. В средние века Арк был настоящим \'городом в городе\'. За его стенами находились дворцы правителей, мечети, монетный двор, казначейство, арсенал и тюрьмы.\r\n\r\nПо легенде, крепость была заложена мифическим принцем Сиявушем. Вход в Арк оформлен массивными воротами с двумя башнями, за которыми идет крутой подъем. Несмотря на то, что большая часть построек была разрушена в 1920 году, сохранившиеся здания сегодня занимают экспозиции Бухарского государственного музея-заповедника.', 'The Ark of Bukhara is a massive fortress that served as the residence of the Emirs of Bukhara for centuries. It is the oldest structure in the city, with foundations dating back to the 1st millennium BC, built atop an artificial hill.\r\n\r\nThe perimeter of the fortress walls is 789 meters. In the Middle Ages, the Ark was a \'city within a city,\' containing the emir\'s palace, mosques, a mint, government offices, a treasury, and even a prison.\r\n\r\nLegend attributes its construction to the mythical Persian prince Siyavush. The grand entrance features twin towers and a ramp leading up to the main citadel. Although much of it was damaged during the 1920 siege, the remaining structures now house the collections of the Bukhara State Museum-Reserve.', 1, 2, '', NULL, NULL, 'ark.png', 'https://youtu.be/1Rb86_XRBJ4?si=1PEWtphZcXAlt_f1', 'active', 1, '2026-04-28 13:22:36', '2026-05-06 06:07:29'),
(4, 'Shohi Zinda majmuasi', 'Ансамбль Шахи-Зинда', 'Shah-i-Zinda Ensemble', 'Shohi Zinda — Samarqanddagi eng sirli va go\'zal me\'moriy yodgorliklardan biri. Bu majmua XI-XV asrlarda shakllangan va bir qator maqbaralardan iborat \'ko\'cha\' ko\'rinishiga ega.\r\n\r\nNomi \'Shohi Zinda\' — \'Tirik shoh\' ma\'nosini anglatadi. Bu nom Muhammad payg\'ambarning amakivachchalari Qusam ibn Abbos bilan bog\'liq. Afsonaga ko\'ra, u dushmanlar hujumidan yer ostiga yashiringan va hanuzgacha o\'sha yerda yashaydi.\r\n\r\nMajmua o\'zining ko\'k, moviy va firuza rangli koshinlari bilan mashhur. Har bir maqbara o\'ziga xos geometrik naqshlar, xattotlik namunalari va ranglar jilosiga ega. Shohi Zindada Amir Temurning qarindoshlari, sarkardalari va yaqinlari dafn etilgan. Bu yerda O\'rta Osiyo me\'morchilik san\'atining barcha nozik qirralarini ko\'rish mumkin.', 'Шахи-Зинда — один из самых загадочных и живописных памятников Самарканда. Этот ансамбль представляет собой улицу из великолепных мавзолеев, строившихся на протяжении XI-XV веков.\r\n\r\nНазвание \'Шахи-Зинда\' переводится как \'Живой царь\'. Оно связано с легендой о двоюродном брате пророка Мухаммеда — Кусаме ибн Аббасе, который, согласно преданию, скрылся под землей от преследователей и живет там по сей день.\r\n\r\nАнсамбль знаменит своей потрясающей отделкой: здесь можно увидеть лучшие образцы глазурованной керамики синих и бирюзовых оттенков. Каждый мавзолей уникален своими орнаментами и каллиграфией. Здесь похоронены родственники и приближенные Амира Тимура, что делает это место важным историческим некрополем.', 'Shah-i-Zinda is a stunning necropolis in Samarkand, often described as an \'avenue of mausoleums.\' Formed between the 11th and 15th centuries, it is one of the most spiritually significant and visually breathtaking sites in Uzbekistan.\r\n\r\nThe name means \'The Living King,\' referring to Kusam ibn Abbas, a cousin of the Prophet Muhammad. Legend says he fled underground to escape enemies and remains alive to this day.\r\n\r\nThe complex is world-famous for its incredible blue and turquoise tilework. Each mausoleum is a masterpiece of majolica and terracotta decoration, featuring intricate geometric patterns and sacred calligraphy. It serves as the final resting place for members of Timur\'s family and his generals, showcasing the evolution of Timurid decorative arts.', 1, 1, '', NULL, NULL, 'shahizinda.png', 'https://youtu.be/NObxATiD5RQ?si=AgDBlDiPTUrN8aSR', 'active', 5, '2026-04-28 13:32:52', '2026-05-06 06:45:16'),
(5, 'Go&#039;ri Amir maqbarasi', 'Мавзолей Гур-Эмир', 'Gur-e-Amir Mausoleum', 'Go\'ri Amir (forscha \'Podshoh qabri\') — buyuk sarkarda Amir Temur va temuriylar sulolasi vakillari dafn etilgan maqbara. Bu bino O\'rta asr Sharq me\'morchiligining eng buyuk asarlaridan biri hisoblanadi.\r\n\r\nDastlab maqbara Amir Temurning to\'satdan vafot etgan nabirasi Muhammad Sulton uchun 1403-yilda qurila boshlagan. Ammo 1405-yilda Amir Temurning o\'zi vafot etgach, qishki qor tufayli uning jasadi Shahrisabzga emas, aynan shu yerga dafn etilgan.\r\n\r\nMaqbara o\'zining qovurg\'asimon moviy gumbazi bilan mashhur. Ichki qismi oltin suvi, loklangan papier-mashe va marmar o\'ymakorligi bilan nihoyatda hashamatli bezatilgan. Amir Temurning qabr toshi dunyodagi eng yirik nefrit toshidan yasalgan. Go\'ri Amir Hindistondagi mashhur Toj Mahal uchun me\'moriy namuna bo\'lib xizmat qilgan.', 'Гур-Эмир (в переводе — \'Гробница царя\') — фамильная усыпальница великого полководца Амира Тимура и его потомков. Этот мавзолей является шедевром средневекового исламского зодчества.\r\n\r\nСтроительство началось в 1403 году по приказу Тимура для его любимого внука Мухаммада Султана. Однако после внезапной смерти самого Тимура в 1405 году, его тело было упокоено именно здесь. Позже здесь были похоронены его сыновья и внуки, включая великого ученого Улугбека.\r\n\r\nМавзолей знаменит своим огромным ребристым куполом ярко-синего цвета. Интерьер поражает богатством отделки: золоченая роспись, мраморные панели и резьба по ганчу. Надгробие Тимура выполнено из цельного куска темно-зеленого нефрита. Гур-Эмир послужил прообразом для таких великих памятников, как Тадж-Махал в Индии.', 'Gur-e-Amir (Persian for \'Tomb of the King\') is the final resting place of the conqueror Amir Timur (Tamerlane) and his heirs. It is a masterpiece of Central Asian Islamic architecture.\r\n\r\nThe project began in 1403 following the sudden death of Timur\'s favorite grandson, Muhammad Sultan. When Timur himself passed away in 1405, he was interred here due to heavy snow blocking the passes to his hometown. It later became the dynastic crypt for other Timurid rulers, including the astronomer Ulugh Beg.\r\n\r\nThe mausoleum is iconic for its large, fluted azure dome. The interior is sumptuously decorated with gold leaf, painted papier-mâché, and carved marble. Timur\'s cenotaph is carved from a single massive block of dark green jade. Gur-e-Amir is recognized as a primary architectural inspiration for the Taj Mahal in Agra.', 1, 1, '', NULL, NULL, 'gureamir.png', NULL, 'active', 0, '2026-04-28 13:32:52', '2026-05-06 06:05:52'),
(6, 'Poi Kalon majmuasi', 'Ансамбль Пои-Калян', 'Po-i-Kalyan Complex', 'Poi Kalon (forscha \'Ulug\' poyidagi joy\') — Buxoroning markaziy diniy majmuasi. U Minorai Kalon, Masjidi Kalon va Mir Arab madrasasidan iborat.\r\n\r\n- Minorai Kalon (1127): Buxoroning ramzi bo\'lgan ushbu minora 47 metr balandlikka ega. U g\'isht terish san\'atining betakror namunasi bo\'lib, 12 ta turli xil naqshli belbog\' bilan bezatilgan. Qizig\'i shundaki, Chingizxon Buxoroni vayron qilganda, minora tepasiga qarab uning salobatidan lol qolgan va uni buzmaslikka buyruq bergan.\r\n- Masjidi Kalon: 12 ming kishini sig\'dira oladigan ulkan juma masjidi.\r\n- Mir Arab madrasasi: XVI asrda qurilgan va hanuzgacha faoliyat ko\'rsatayotgan nufuzli islom ta\'lim muassasasi.\r\n\r\nMajmua asrlar davomida Buxoroning ma\'naviy va ta\'lim markazi bo\'lib kelgan va o\'zining mustahkam brick-style arxitekturasi bilan ajralib turadi.', 'Пои-Калян (в переводе — \'Подножие Великого\') — центральный архитектурный ансамбль Бухары. Он включает в себя минарет Калян, одноименную мечеть и медресе Мири-Араб.\r\n\r\n- Минарет Калян (1127): Визитная карточка Бухары. Его высота составляет 47 метров. Минарет украшен 12 полосами уникальной кирпичной кладки, которые никогда не повторяются. Легенда гласит, что даже Чингисхан был настолько поражен его величием, что приказал сохранить его во время разрушения города.\r\n- Мечеть Калян: Одна из древнейших и крупнейших мечетей Центральной Азии, вмещающая до 12 тысяч верующих.\r\n- Медресе Мири-Араб: Построено в XVI веке и по сей день является действующим духовным учебным заведением.\r\n\r\nАнсамбль Пои-Калян олицетворяет величие \'Благородной Бухары\' и ее многовековые традиции кирпичного зодчества.', 'Po-i-Kalyan (Persian for \'At the Foot of the Great One\') is the main religious complex of Bukhara. It consists of the Kalyan Minaret, the Kalyan Mosque, and the Mir-i-Arab Madrasah.\r\n\r\n- Kalyan Minaret (1127): A masterpiece of brickwork standing 47 meters tall. It features 12 bands of unique patterns. Legend has it that Genghis Khan was so awestruck by its grandeur that he spared it from destruction while the rest of the city was razed.\r\n- Kalyan Mosque: One of the largest Friday mosques in Central Asia, capable of holding 12,000 worshippers.\r\n- Mir-i-Arab Madrasah: Built in the 16th century, it remains one of the most prestigious Islamic educational institutions in the region to this day.\r\n\r\nThe complex stands as a testament to the architectural and spiritual legacy of \'Bukhara the Noble.\'', 1, 2, '', NULL, NULL, 'kalyan.png', 'https://youtu.be/unFF_gqvbsM?si=4aBTMeu1g9e5X3PG', 'active', 1, '2026-04-28 13:32:52', '2026-05-06 06:21:43'),
(7, 'Bibixonim masjidi', 'Мечеть Биби-Ханым', 'Bibi-Khanym Mosque', 'Bibixonim masjidi — Amir Temurning buyrug\'i bilan uning Hindiston yurishidan so\'ng 1399-1404 yillarda qurilgan. Bu o\'sha davrdagi islom dunyosining eng yirik va mahobatli juma masjidlaridan biri bo\'lgan.\r\n\r\nAfsonaga ko\'ra, masjid Amir Temurning sevimli rafiqasi Bibixonim sharafiga qurilgan. Masjidning kirish peshtog\'i 35 metr balandlikka ega bo\'lib, u o\'z davrining muhandislik cho\'qqisi bo\'lgan. Masjid hovlisining o\'rtasida marmardan yasalgan ulkan Qur\'on kursi joylashgan bo\'lib, u hozirgacha saqlanib qolgan.\r\n\r\nGarchi zilzilalar va vaqt ta\'sirida bino qisman vayron bo\'lgan bo\'lsa-da, uning qayta tiklangan gumbazlari va ulkan peshtog\'i Samarqandning qudratini namoyish etib turibdi. Moviy gumbazlarning kattaligi va koshinlarining jilvasi bu yerga kelgan har qanday sayyohni hayratda qoldiradi.', 'Мечеть Биби-Ханым была построена по приказу Амира Тимура после его победоносного похода в Индию в 1399-1404 годах. В то время она была одной из крупнейших и величественных соборных мечетей мусульманского мира.\r\n\r\nСогласно легенде, мечеть была названа в честь любимой жены Тимура. Высота входного портала составляла 35 метров, что было пределом инженерных возможностей той эпохи. В центре двора до сих пор стоит гигантская мраморная подставка для Корана, изготовленная при Улугбеке.\r\n\r\nХотя землетрясения сильно повредили структуру в прошлые века, масштаб и красота восстановленных куполов и портала по-прежнему поражают воображение. Синие купола Биби-Ханым видны из многих точек Самарканда как символ его былого могущества.', 'The Bibi-Khanym Mosque was commissioned by Amir Timur following his successful campaign in India between 1399 and 1404. It was designed to be one of the largest and most magnificent congregational mosques in the Islamic world at the time.\r\n\r\nNamed after Timur\'s favorite wife according to legend, the mosque featured an entrance portal 35 meters high, pushing the limits of medieval engineering. In the center of the courtyard stands a massive marble lectern for the Quran, placed there by Ulugh Beg.\r\n\r\nThough partially destroyed by earthquakes over the centuries, the restored blue domes and monumental portals still showcase the grand scale of Timurid ambition. Its tiles and sheer size remain some of the most impressive sights in Samarkand.', 1, 1, '', NULL, NULL, 'bibikhanym.png', 'https://youtu.be/a5VH9BxOWeo?si=kq3fjS6u15OZj0c6', 'active', 1, '2026-04-28 13:32:52', '2026-05-06 06:04:03'),
(8, 'Chor Minor madrasasi', 'Медресе Чор-Минор', 'Chor Minor Madrasah', 'Chor Minor (forscha \'to\'rtta minora\') — Buxorodagi eng o\'ziga xos me\'moriy yodgorliklardan biri. U 1807-yilda boy turkman savdogari Xalifa Niyozqul tomonidan barpo etilgan. \r\n\r\nBino aslida kattaroq madrasa majmuasining darvozaxonasi bo\'lgan, ammo vaqt o\'tishi bilan madrasaning o\'zi vayron bo\'lgan. Chor Minorning to\'rtta minorasi bor va ularning har biri moviy gumbaz bilan tugallangan. Minoralar turlicha bezatilgan bo\'lib, ba\'zi tadqiqotchilar buni dunyodagi to\'rtta asosiy dinning (islom, xristianlik, buddizm, induizm) ramziy tasviri deb hisoblashadi. Binoning ichida kichik masjid va kutubxona joylashgan. Bu obida o\'zining ixchamligi va betakror dizayni bilan Buxoroning boshqa yirik inshootlaridan ajralib turadi.', 'Чор-Минор (в переводе — \'четыре минарета\') — одно из самых необычных и запоминающихся зданий в Бухаре. Оно было построено в 1807 году на средства богатого туркменского купца Халифа Ниязкула.\r\n\r\nЗдание служило входным порталом в ныне разрушенное медресе. Главная особенность — четыре башни, увенчанные бирюзовыми куполами. Декор каждой башни уникален, что породило теории о том, что они символизируют четыре мировые религии. Внутри здания располагались мечеть и библиотека. Чор-Минор отличается от классических монументальных построек Бухары своей камерностью и изяществом. Объект является частью Всемирного наследия ЮНЕСКО.', 'Chor Minor (Persian for \'four minarets\') is one of the most charming and unique structures in Bukhara. Built in 1807 by Khalif Niyaz-kul, a wealthy merchant, it served as the gatehouse to a larger madrasah complex that has since disappeared.\r\n\r\nThe building is famous for its four towers topped with vibrant azure domes. Each tower features distinct decorative elements, leading some scholars to believe they symbolize the four major world religions. The interior contains a small prayer hall and a library. Its compact size and whimsical design make it a standout monument in the historic center of Bukhara.', 1, 2, '', NULL, NULL, 'chorminor.png', 'https://youtu.be/5RF-_VB4xVs?si=KEWXcGDQRfIJBh_D', 'active', 2, '2026-04-28 14:05:26', '2026-05-06 06:22:08'),
(9, 'Savitskiy nomidagi san&#039;at muzeyi', 'Музей искусств имени Савицкого', 'Savitsky State Museum of Arts', 'Nukus shahridagi Savitskiy muzeyi — dunyoga mashhur \'Sahrodagi Luvr\' nomi bilan tanilgan. U 1966-yilda olim va rassom Igor Savitskiy tomonidan tashkil etilgan. \r\n\r\nMuzey o\'zining rus avangard san\'ati to\'plami bilan mashhur bo\'lib, bu borada dunyoda Sankt-Peterburgdagi Rus muzeyidan keyin ikkinchi o\'rinda turadi. Savitskiy sho\'ro davrida taqiqlangan va \'begona\' deb topilgan asarlarni butun Ittifoq bo\'ylab yig\'ib, ularni chekka Nukus shahriga olib kelish orqali yo\'q bo\'lib ketishdan saqlab qolgan. Bundan tashqari, muzeyda Qoraqalpog\'istonning qadimiy arxeologik topilmalari va amaliy san\'at namunalarining eng boy kolleksiyasi mavjud. Bu muzey bugungi kunda O\'zbekistonning eng muhim madaniy xazinalaridan biri hisoblanadi.', 'Музей Савицкого в Нукусе, известный во всем мире как \'Лувр в пустыне\', является одним из самых значимых культурных центров Узбекистана. Он был основан в 1966 году художником и этнографом Игорем Савицким.\r\n\r\nМузей обладает второй по значимости в мире коллекцией произведений русского авангарда. Савицкий годами собирал картины, которые советская власть считала \'идеологически вредными\', и свозил их в Нукус, спасая от уничтожения. Помимо авангарда, в музее представлена богатейшая коллекция древних находок из Хорезма и уникальные изделия каракалпакского народного творчества. Этот музей стал местом паломничества для любителей искусства со всего мира.', 'The Savitsky State Museum of Arts in Nukus is world-renowned as the \'Louvre of the Desert.\' Founded in 1966 by the visionary artist and collector Igor Savitsky, it holds a treasure trove of cultural heritage in the remote region of Karakalpakstan.\r\n\r\nThe museum is most famous for housing the world\'s second-largest collection of Russian avant-garde art. Savitsky rescued these works from Soviet censorship by moving them to this isolated desert city. Additionally, the museum contains an extensive collection of ancient artifacts from the Khorezm civilization and traditional Karakalpak folk art. It is considered one of the most unexpected and significant art repositories in the world.', 3, 6, '', NULL, NULL, 'savitsky.png', 'https://youtu.be/FPYuoy9F4yA?si=odcc0Vq-_2vlccye', 'active', 0, '2026-04-28 14:05:26', '2026-05-06 06:02:15'),
(10, 'Hazrati Imom majmuasi', 'Комплекс Хазрет Имам', 'Hazrati Imam Complex', 'Hazrati Imom (Hast Imom) majmuasi — Toshkentning eng muhim diniy va ma\'naviy markazidir. Majmua X asrda yashagan buyuk olim va imom Abu Bakr ash-Qaffol Shoshiy qabri atrofida shakllangan.\r\n\r\nMajmuaning eng qimmatli boyligi — Muyi Muborak madrasasida saqlanayotgan dunyodagi eng qadimiy Qur\'on qo\'lyozmasi, ya\'ni \'Usmon Qur\'oni\' (VII asr) hisoblanadi. Shuningdek, bu yerda Baroqxon madrasasi, Tilla Shayx masjidi va muhtasham Hazrati Imom jome masjidi joylashgan. 2007-yilda qayta ta\'mirlangan ushbu majmua o\'zining baland minoralari va sharqona uslubdagi me\'morchiligi bilan Toshkentning ko\'rkiga aylanib qolgan. Bu yerda O\'zbekiston Musulmonlari idorasi ham joylashgan bo\'lib, u butun mamlakatning islomiy markazi hisoblanadi.', 'Комплекс Хазрет Имам (Хаст Имам) — духовное сердце Ташкента. Он возник вокруг захоронения великого ученого и первого проповедника ислама в Ташкенте — Абу Бакра Мухаммада ибн Али аш-Каффаль Шаши, жившего в X веке.\r\n\r\nГлавным сокровищем комплекса является медресе Муйи Муборак, где хранится знаменитый Коран Османа — старейшая в мире рукопись священной книги, датируемая VII веком. Ансамбль также включает в себя медресе Барак-хана, мечеть Тилля-Шейха и грандиозную соборную мечеть Хазрет Имам, возведенную в 2007 году. Комплекс с его величественными 50-метровыми минаретами и резными деревянными колоннами является главной достопримечательностью старого города Ташкента.', 'The Hazrati Imam (Hast Imam) Complex is the spiritual heart of Tashkent. It was built around the tomb of Abu Bakr Muhammad ibn Ali Ismoil ash-Qaffol Shoshiy, a revered 10th-century scholar and preacher.\r\n\r\nThe most precious artifact within the complex is the Uthman Quran, kept in the Muyi Muborak Madrasah. It is recognized as the world\'s oldest surviving Quran manuscript, dating back to the 7th century. The complex also features the Barak-Khan Madrasah, the Tilla Sheikh Mosque, and the grand Hazrati Imam Mosque built in 2007. With its towering minarets and intricate wood carvings, the complex serves as the primary center of Islamic culture and administration in Uzbekistan.', 1, 4, '', NULL, NULL, 'places/10/img_69fadbcc379bc6.62409014.webp', 'https://youtu.be/-AYpIfMfFl4?si=IRzqjx7uvpM0fzFG', 'active', 45, '2026-04-28 14:05:26', '2026-05-06 18:39:28');

-- --------------------------------------------------------

--
-- Table structure for table `place_gallery`
--

CREATE TABLE `place_gallery` (
  `id` int NOT NULL,
  `place_id` int NOT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `place_people`
--

CREATE TABLE `place_people` (
  `id` int NOT NULL,
  `place_id` int NOT NULL,
  `person_id` int NOT NULL,
  `relationship` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` int NOT NULL,
  `name_uz` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name_uz`, `name_ru`, `name_en`, `slug`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Samarqand', '?????????', 'Samarkand', 'samarqand', NULL, '2026-04-28 13:18:17', NULL),
(2, 'Buxoro', '??????', 'Bukhara', 'buxoro', NULL, '2026-04-28 13:18:17', NULL),
(3, 'Xiva', '????', 'Khiva', 'xiva', NULL, '2026-04-28 13:18:17', NULL),
(4, 'Toshkent', '???????', 'Tashkent', 'toshkent', NULL, '2026-04-28 13:18:17', NULL),
(5, 'Shahrisabz', '?????????', 'Shahrisabz', 'shahrisabz', NULL, '2026-04-28 13:18:17', NULL),
(6, 'Qoraqalpog\'iston', '??????????????', 'Karakalpakstan', 'karakalpakstan', NULL, '2026-04-28 14:04:01', NULL),
(7, 'Jizzax', '??', 'Jizzakh', 'jizzakh', NULL, '2026-05-04 12:18:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `cuisine_type` varchar(100) DEFAULT NULL,
  `price_level` enum('low','mid','high') DEFAULT 'mid',
  `price_level_num` tinyint DEFAULT '2',
  `rating` decimal(2,1) DEFAULT '0.0',
  `image_path` varchar(500) DEFAULT NULL,
  `working_hours` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `region_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `name_uz` varchar(255) DEFAULT NULL,
  `name_ru` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `description_uz` text,
  `description_ru` text,
  `description_en` text,
  `price_range` enum('low','mid','high') DEFAULT 'mid',
  `image` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `status` enum('active','draft') DEFAULT 'active',
  `views` int DEFAULT '0',
  `video_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `cuisine_type`, `price_level`, `price_level_num`, `rating`, `image_path`, `working_hours`, `phone`, `address`, `region_id`, `created_at`, `name_uz`, `name_ru`, `name_en`, `description_uz`, `description_ru`, `description_en`, `price_range`, `image`, `latitude`, `longitude`, `status`, `views`, `video_url`) VALUES
(1, 'Samarqand Uyi', 'Milliy taomlar', 'O\'zbek', 'mid', 2, '4.8', NULL, '09:00-23:00', NULL, NULL, 4, '2026-05-05 04:11:11', 'Samarqand Uyi', NULL, NULL, 'Milliy taomlar', NULL, NULL, 'mid', NULL, NULL, NULL, 'active', 1, NULL),
(2, 'Registon Choyxona', 'An\'anaviy choyxona', 'O\'zbek', 'low', 1, '4.5', NULL, '08:00-22:00', NULL, NULL, 4, '2026-05-05 04:11:11', 'Registon Choyxona', NULL, NULL, 'An\'anaviy choyxona', NULL, NULL, 'low', NULL, NULL, NULL, 'active', 0, NULL),
(3, 'Buxoro Oshxonasi', 'Buxoro milliy taomlari', 'O\'zbek', 'mid', 2, '4.7', NULL, '10:00-22:00', NULL, NULL, 2, '2026-05-05 04:11:11', 'Buxoro Oshxonasi', NULL, NULL, 'Buxoro milliy taomlari', NULL, NULL, 'mid', NULL, NULL, NULL, 'active', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `entity_type` enum('place','hotel','restaurant','person') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int NOT NULL,
  `author_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Mehmon',
  `rating` tinyint UNSIGNED NOT NULL DEFAULT '5',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('groq_api_key', 'YOUR_GROQ_API_KEY_HERE'),
('groq_model', 'llama-3.1-8b-instant');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entity_type` (`entity_type`,`entity_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fav` (`session_id`,`entity_type`,`entity_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `hero_sliders`
--
ALTER TABLE `hero_sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `region_id` (`region_id`);
ALTER TABLE `hotels` ADD FULLTEXT KEY `ft_hotels_search` (`name_uz`,`name_ru`,`name_en`,`description_uz`,`description_ru`,`description_en`);

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `region_id` (`region_id`);
ALTER TABLE `people` ADD FULLTEXT KEY `ft_people_search` (`name_uz`,`name_ru`,`name_en`,`bio_uz`,`bio_ru`,`bio_en`);

--
-- Indexes for table `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `region_id` (`region_id`);
ALTER TABLE `places` ADD FULLTEXT KEY `ft_places_search` (`name_uz`,`name_ru`,`name_en`,`description_uz`,`description_ru`,`description_en`);

--
-- Indexes for table `place_gallery`
--
ALTER TABLE `place_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `place_people`
--
ALTER TABLE `place_people`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pp` (`place_id`,`person_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id` (`region_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entity_type` (`entity_type`,`entity_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hero_sliders`
--
ALTER TABLE `hero_sliders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `places`
--
ALTER TABLE `places`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `place_gallery`
--
ALTER TABLE `place_gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `place_people`
--
ALTER TABLE `place_people`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `people_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `places`
--
ALTER TABLE `places`
  ADD CONSTRAINT `places_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `places_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `place_gallery`
--
ALTER TABLE `place_gallery`
  ADD CONSTRAINT `place_gallery_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `place_people`
--
ALTER TABLE `place_people`
  ADD CONSTRAINT `place_people_ibfk_1` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `place_people_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
