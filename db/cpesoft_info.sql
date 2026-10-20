-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 19 أكتوبر 2026 الساعة 21:55
-- إصدار الخادم: 9.1.0
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cpesoft_info`
--

-- --------------------------------------------------------

--
-- بنية الجدول `active_crs`
--

DROP TABLE IF EXISTS `active_crs`;
CREATE TABLE IF NOT EXISTS `active_crs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ISIC_Code_6` varchar(11) DEFAULT NULL,
  `ISIC_Description_6` varchar(255) DEFAULT NULL,
  `CR_count` varchar(8) DEFAULT NULL,
  `Weighted_CR_Count` varchar(18) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `agents`
--

DROP TABLE IF EXISTS `agents`;
CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `logo` varchar(250) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `country_code` varchar(100) DEFAULT NULL,
  `city_code` varchar(100) DEFAULT NULL,
  `address` text,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `date_of_payment` date DEFAULT NULL,
  `number_of_payment` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint NOT NULL,
  `company_type` tinyint NOT NULL DEFAULT '1',
  `renewal_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int NOT NULL DEFAULT '1',
  `updatedBy` int DEFAULT NULL,
  `isPOS` tinyint(1) NOT NULL DEFAULT '0',
  `isAccounting` tinyint(1) NOT NULL DEFAULT '0',
  `automatedEntries` tinyint(1) NOT NULL DEFAULT '0',
  `isManufacturing` tinyint(1) NOT NULL DEFAULT '0',
  `automatedEntriesOffline` tinyint(1) NOT NULL DEFAULT '0',
  `syncEveryMinues` int NOT NULL DEFAULT '30',
  `agent_id` bigint UNSIGNED DEFAULT NULL,
  `isElectronicLinking` tinyint(1) NOT NULL DEFAULT '0',
  `isFoodics` tinyint(1) NOT NULL DEFAULT '0',
  `isStore` tinyint(1) NOT NULL DEFAULT '0',
  `blog_id` int DEFAULT NULL,
  `siteurl` varchar(500) DEFAULT NULL,
  `latest_sync_order` datetime DEFAULT NULL,
  `isFixedAsset` tinyint(1) NOT NULL DEFAULT '0',
  `tel_no` varchar(55) DEFAULT NULL,
  `activity_id` int NOT NULL DEFAULT '0',
  `address` text,
  `isGrouping` tinyint(1) NOT NULL DEFAULT '0',
  `isSynchronizewithZATCA` tinyint(1) NOT NULL DEFAULT '0',
  `isAutoSynchronizeZATCA` tinyint(1) NOT NULL DEFAULT '0',
  `isHR` tinyint(1) NOT NULL DEFAULT '0',
  `reason` varchar(1000) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `inv_no` varchar(15) DEFAULT NULL,
  `isAutoSynchronizeZATCAPOS` tinyint(1) NOT NULL DEFAULT '0',
  `isQuroosh` tinyint(1) NOT NULL DEFAULT '0',
  `isNearpay` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `company` (`company`),
  KEY `client_FK` (`agent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;
INSERT INTO `client` (`id`, `name`, `name_en`, `company`, `date_of_payment`, `number_of_payment`, `created_at`, `status`, `company_type`, `renewal_date`, `createdBy`, `updatedBy`, `isPOS`, `isAccounting`, `automatedEntries`, `isManufacturing`, `automatedEntriesOffline`, `syncEveryMinues`, `agent_id`, `isElectronicLinking`, `isFoodics`, `isStore`, `blog_id`, `siteurl`, `latest_sync_order`, `isFixedAsset`, `tel_no`, `activity_id`, `address`, `isGrouping`, `isSynchronizewithZATCA`, `isAutoSynchronizeZATCA`, `isHR`, `reason`, `updated_at`, `inv_no`, `isAutoSynchronizeZATCAPOS`, `isQuroosh`, `isNearpay`) VALUES
	(11, 'مؤسسة مكان الفشار', 'makanalf', 'makanalf', '2020-10-17', 1, '2021-03-28 23:36:48', 1, 1, '2027-01-24 18:36:48', 1, 8, 1, 1, 1, 0, 1, 30, NULL, 0, 0, 1, NULL, NULL, NULL, 0, '0505907567', 0, '                                                                                                                                                                        ', 0, 0, 0, 1, NULL, '2025-01-16 17:04:40', NULL, 0, 0, 0),

	(14, 'arkanpro', 'arkanpro', 'arkanpro', '2020-10-17', 2, '2021-04-02 19:48:30', 1, 1, '2026-12-30 00:00:00', 1, 1, 0, 1, 0, 1, 0, 30, NULL, 0, 0, 1, NULL, NULL, NULL, 1, '0505907749', 0, NULL, 1, 1, 0, 1, NULL, NULL, NULL, 0, 0, 0),
	(271, 'مطاعم امواج البحار', 'Marvel peace', 'amwaj', '2021-11-29', 1, '2021-11-29 21:21:20', 1, 1, '2025-11-29 00:00:00', 1, 6, 1, 1, 1, 0, 1, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0536598211', 0, '                                                                  Dammam                  ', 0, 1, 0, 1, NULL, NULL, NULL, 1, 0, 0),
	(518, 'مؤسسة جودة اعمار لمواد البناء', 'Quality Emaar Est for Building Materials', 'emaar', '2022-06-20', 1, '2022-06-20 16:31:14', 0, 1, '2022-06-20 00:00:00', 1, 1, 0, 1, 1, 0, 1, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, NULL, 0, NULL, 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0),
(669, 'تموينات غذاء اليمامة للمواد الغذائية', 'تموينات زهرة حمراء الاسد للمواد الغذائية', 'khalidiya', '2022-10-30', 1, '2026-04-12 22:27:49', 1, 2, '2026-04-12 11:05:43', 1, 8, 0, 0, 0, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0590687905', 0, '                                                                                                                                                                                                           maddina                                                                                                                                                                                                                                                                                                                                                                                               ', 0, 0, 0, 1, 'مماطل', '2026-04-12 17:27:49', NULL, 0, 0, 0),
	(1074, 'مؤسسة افنان سعد عبدالله الصنيعي للمواد الغذائية', 'تموينات العاصمه', 'almojaba', '0000-00-00', 1, '2023-07-19 19:24:21', 1, 2, '2026-08-10 14:24:21', 1, 11, 0, 1, 1, 0, 1, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0559090976', 0, '                                                                                                                                                                    Riyadh                                                                                                                                                                                                                                                                ', 0, 0, 0, 1, 'hanan', '2026-08-16 10:37:27', NULL, 0, 0, 0),
	(1178, 'مؤسسة هاتن للتشغيل والصيانة', 'Hatten Operation and Maintenance Corporation', 'haten', NULL, 1, '2023-11-09 21:28:44', 1, 1, '2025-11-09 15:28:44', 8, NULL, 0, 1, 1, 0, 1, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0540666847', 0, '                                                                        Jubail            ', 0, 0, 0, 1, NULL, NULL, NULL, 0, 0, 0),
	(1464, 'شركة ثوب يارد للخياطه الرجاليه', 'شركة ثوب يارد للخياطه الرجاليه', 'yard', NULL, 1, '2026-01-04 16:32:52', 1, 8, '2026-01-04 15:49:12', 8, 8, 0, 1, 1, 0, 1, 30, 3, 0, 0, 0, NULL, NULL, NULL, 0, '0564199001', 0, '                                                                                                                                                                            Riyadh                                                                                                                                                                                   ', 0, 0, 0, 0, '', '2026-01-04 10:32:52', NULL, 0, 0, 0),
	(1476, 'مقهى دون سفن لتقديم المشروبات', 'مقهى دون سفن لتقديم المشروبات', 'doon', NULL, 1, '2024-12-27 03:23:58', 1, 3, '2026-07-11 21:23:58', 8, 11, 0, 0, 0, 0, 0, 30, 7, 0, 0, 0, NULL, NULL, NULL, 0, '0558071790', 8, '                                                                                                                          Dammam                                                                                                                                                                                                                                                                                                          ', 0, 0, 0, 0, '', '2026-07-11 09:45:43', NULL, 0, 0, 0),
	(1478, 'بوفية القبعة الواحدة لتقديم الوجبات', 'بوفية القبعة الواحدة لتقديم الوجبات', 'hat', NULL, 1, '2024-12-29 19:31:24', 1, 3, '2025-12-29 13:31:24', 8, 11, 0, 1, 0, 0, 0, 30, 6, 0, 0, 0, NULL, NULL, NULL, 0, '0501942132', 8, '                                                                                                                                             Abha                                                                                                              ', 0, 0, 0, 0, '', '2026-01-17 19:21:35', NULL, 0, 0, 0),
	(1521, 'شركة صروح نجد للخدمات التجارية', 'شركة صروح نجد للخدمات التجارية', 'suruh', NULL, 1, '2025-01-30 15:55:56', 1, 1, '2026-05-02 09:55:56', 8, 11, 1, 0, 0, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0540366366', 0, '                                                                                                                Riyadh                                                                                                                                            ', 0, 0, 0, 0, 'مماطل', '2026-04-21 12:55:56', NULL, 0, 0, 0),
		(1523, 'تموينات عمر جعفر سالم الصيعري للمواد الغذائية', 'تموينات عمر جعفر سالم الصيعري للمواد الغذائية', 'omar2', NULL, 1, '2026-03-03 07:15:07', 1, 2, '2026-03-30 10:43:07', 8, 8, 0, 0, 0, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0506366090', 0, '                                                                                                                                           Dammam                                                                                                                 ', 0, 0, 0, 0, 'قفل  في وجهي ونكر انه مشترك رغم تققيمه سابقا', '2026-03-03 01:15:07', NULL, 0, 0, 0),
	(1583, 'معرض مرزوق لافي مرزوق المطيري للسيارات', 'معرض مرزوق لافي مرزوق المطيري للسيارات', 'almutairi', NULL, 1, '2025-03-23 05:02:08', 1, 7, '2026-05-23 00:02:08', 8, 11, 0, 1, 1, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0500636663', 0, '                                                                                                                                    Dammam                                                                                                                        ', 0, 0, 0, 0, 'مماطل وكل شوي له كلمه ويعطي موعد يخلف فيه', '2026-05-23 10:46:22', '2935', 0, 0, 0),
	(1870, 'شركة رواسخ نجد الذهبية', 'شركة رواسخ نجد الذهبية', 'rwaskh', NULL, 1, '2026-06-10 21:34:01', 1, 3, '2026-06-10 16:34:01', 8, 8, 1, 0, 0, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0559759298', 0, '                                        Ryadh                                                                                                                                ', 0, 0, 0, 0, NULL, '2026-06-10 16:35:17', NULL, 0, 0, 0),
	(1871, 'مؤسسة فوشيا سويت التجارية', 'مؤسسة فوشيا سويت التجارية', 'foshea', NULL, 1, '2026-06-11 19:15:58', 1, 3, '2026-06-11 14:15:58', 8, NULL, 1, 0, 0, 0, 0, 30, NULL, 0, 0, 1, NULL, NULL, NULL, 0, '0505907567', 0, '                                                                           Jubail         ', 0, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0),
	(1673, 'شركة اركان الاحتراف الحساب التجريبي', 'شركة اركان الاحتراف الحساب التجريبي', 'demo3', NULL, 1, '2025-08-12 21:13:04', 1, 1, '2026-08-12 16:13:04', 8, NULL, 1, 1, 1, 0, 1, 30, NULL, 0, 0, 1, NULL, NULL, NULL, 1, '', 0, '                                                                                    ', 0, 0, 0, 1, NULL, NULL, NULL, 0, 0, 1),
	(1873, 'مؤسسة عبدالرحمن عبدالعزيز فلاته', 'OSD Solution', 'fallatah', NULL, 1, '2026-06-17 03:56:32', 1, 4, '2026-06-16 22:56:32', 8, 11, 0, 0, 0, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0549060908', 0, '                                                                                    Jaddah                                                                                    ', 0, 0, 0, 0, NULL, '2026-07-11 14:06:25', NULL, 0, 0, 0),
	(1878, 'مؤسسة محمد علوش الهاجري للمقاولات العامة', 'مؤسسة محمد علوش الهاجري للمقاولات العامة', 'aloush', NULL, 1, '2026-06-27 17:32:43', 1, 4, '2026-06-27 12:32:43', 8, 8, 0, 0, 0, 0, 0, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0502996229', 0, '                                                    Baqiq                                                                                                                                                                                                                                                                                          ', 0, 1, 1, 0, NULL, '2026-06-27 12:43:10', NULL, 1, 0, 0),
	(1883, 'مؤسسة عبد الله جمعان الدوسري', 'BX Auto Body Expert For Car Maintenance', 'bx_auto', NULL, 1, '2026-07-08 22:29:15', 1, 5, '2026-07-08 17:29:15', 11, 11, 0, 1, 1, 0, 1, 30, NULL, 0, 0, 0, NULL, NULL, NULL, 0, '0555805110', 71, 'Al Khobar', 0, 0, 0, 0, NULL, '2026-07-08 17:37:48', '3547', 0, 0, 0),

-- --------------------------------------------------------

--
-- بنية الجدول `client_transactions`
--

DROP TABLE IF EXISTS `client_transactions`;
CREATE TABLE IF NOT EXISTS `client_transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `company_type` int NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `renewed_at` datetime DEFAULT NULL,
  `isPOS` tinyint DEFAULT '0',
  `isAccounting` tinyint DEFAULT '0',
  `automatedEntries` tinyint DEFAULT '0',
  `isManufacturing` tinyint DEFAULT '0',
  `automatedEntriesOffline` tinyint DEFAULT '0',
  `comments` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `contract_attachment` varchar(300) DEFAULT NULL,
  `renew_subscription` tinyint DEFAULT '0',
  `isElectronicLinking` tinyint(1) NOT NULL DEFAULT '0',
  `isFoodics` tinyint(1) NOT NULL DEFAULT '0',
  `isStore` tinyint(1) NOT NULL DEFAULT '0',
  `isFixedAsset` tinyint(1) NOT NULL DEFAULT '0',
  `isGrouping` tinyint(1) NOT NULL DEFAULT '0',
  `isSynchronizewithZATCA` tinyint(1) NOT NULL DEFAULT '0',
  `add_warehouse` tinyint(1) NOT NULL DEFAULT '0',
  `add_user` tinyint(1) NOT NULL DEFAULT '0',
  `isAutoSynchronizeZATCA` tinyint(1) NOT NULL DEFAULT '0',
  `isHR` tinyint(1) NOT NULL DEFAULT '0',
  `add_whatsapp_settings` int NOT NULL DEFAULT '0',
  `inv_no` varchar(15) DEFAULT NULL,
  `isAutoSynchronizeZATCAPOS` tinyint(1) NOT NULL DEFAULT '0',
  `isQuroosh` tinyint(1) NOT NULL DEFAULT '0',
  `isNearpay` tinyint(1) NOT NULL DEFAULT '0',
  `add_nearpay` tinyint(1) NOT NULL DEFAULT '0',
  `add_branch` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_idx` (`created_by`),
  KEY `client_transactions_FK` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `company_type`
--

DROP TABLE IF EXISTS `company_type`;
CREATE TABLE IF NOT EXISTS `company_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `second_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO `company_type` (`id`, `name`, `second_name`) VALUES
	(1, 'عام', 'General'),
	(2, 'سوبرماركت - بقالات', 'Supermarket - Grocery'),
	(3, 'مطاعم', 'Restaurants'),
	(4, 'مقاولات', 'Contracting'),
	(5, 'خدمات', 'Services'),
	(6, 'مغسلة', 'Lundry'),
	(7, 'معارض سيارات', 'Car Showrooms'),
	(8, 'خياطة', 'Tailoring'),
	(9, 'الموارد البشرية', 'HR');

-- --------------------------------------------------------

--
-- بنية الجدول `complaints`
--

DROP TABLE IF EXISTS `complaints`;
CREATE TABLE IF NOT EXISTS `complaints` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offline_id` int NOT NULL,
  `client_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `type` int NOT NULL DEFAULT '1',
  `status` int NOT NULL DEFAULT '1',
  `user_id` int NOT NULL,
  `biller_id` int DEFAULT NULL,
  `warehouse_code` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `respond` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `respond_by` int DEFAULT NULL,
  `respond_date` datetime DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `offline_id` (`offline_id`,`company`,`user_id`,`warehouse_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `complaints_note`
--

DROP TABLE IF EXISTS `complaints_note`;
CREATE TABLE IF NOT EXISTS `complaints_note` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offline_id` int DEFAULT NULL,
  `complaint_id` int NOT NULL,
  `client_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_id` int NOT NULL,
  `biller_id` int DEFAULT NULL,
  `warehouse_code` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `offline_id` (`offline_id`,`complaint_id`,`company`,`user_id`,`warehouse_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `complaints_status`
--

DROP TABLE IF EXISTS `complaints_status`;
CREATE TABLE IF NOT EXISTS `complaints_status` (
  `statusId` bigint NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`statusId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `complaints_type`
--

DROP TABLE IF EXISTS `complaints_type`;
CREATE TABLE IF NOT EXISTS `complaints_type` (
  `typeId` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`typeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `electronic_linking`
--

DROP TABLE IF EXISTS `electronic_linking`;
CREATE TABLE IF NOT EXISTS `electronic_linking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ename` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `edescription` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `url` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `logo` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO `electronic_linking` (`id`, `name`, `ename`, `description`, `edescription`, `url`, `logo`, `created_at`) VALUES
	(1, 'فودكس', 'Foodics', 'أطلقت فودكس منتجها الخاص بنقاط البيع وإدارة المطاعم في عام 2014م، وهو برنامج متكامل سحابي يمكِّن أصحاب المطاعم من الربط مع الكثير من تطبيقات الطرف الثالث مثل: المحاسبة، وتطبيقات التوصيل، وغيرها', 'Foodics launched its point-of-sale and restaurant management product in 2014. It is an integrated cloud program that enables restaurant owners to connect with many third-party applications such as: accounting, delivery applications, and others.', 'https://console-sandbox.foodics.com/login', 'foodics_logo.png', '2023-06-11 20:10:49'),
	(2, 'جسر', 'Jisr', 'جسر شركة تقنية ناشئة في قطاع تقنيات الموارد البشرية، تأسست في 2016 م. تعمل على رقمنة عمليات الموارد البشرية بالكامل، و تركز على تقديم حلول متوافقة مع احتياجات الشركات السعودية و متماشية مع أنظمة العمل.', 'Jisr, founded in 2016, is an emerging technology company in the human resource technology sector. It works on fully digitizing human resources operations, and focuses on providing solutions compatible with the needs of Saudi companies, in line with the Work Systems.', 'https://www.jisr.net/', 'jisr.png', '2025-01-30 21:10:49'),
	(3, 'سلة', 'Salla', 'أطلقت سلة منصتها للتجارة الإلكترونية بهدف تمكين رواد الأعمال وأصحاب المتاجر من إنشاء وإدارة متاجرهم الإلكترونية بسهولة، وتوفير حلول متكاملة للبيع وإدارة المنتجات والطلبات والمدفوعات والشحن. كما تتيح سلة التكامل مع العديد من التطبيقات والخدمات الخارجية، مما يساعد التجار على توسيع أعمالهم وأتمتة عملياتهم الرقمية', 'Salla launched its e-commerce platform to empower entrepreneurs and businesses to easily create and manage their online stores. The platform provides integrated solutions for selling, product and order management, payments, and shipping. Salla also enables merchants to integrate with a wide range of third-party applications and services, helping them expand their businesses and automate their digital operations', 'https://salla.com/', 'salla_logo.png', '2026-10-05 19:46:18');

-- --------------------------------------------------------

--
-- بنية الجدول `setting`
--

DROP TABLE IF EXISTS `setting`;
CREATE TABLE IF NOT EXISTS `setting` (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `site_name` varchar(200) NOT NULL,
  `site_name_en` varchar(250) NOT NULL,
  `image` varchar(255) NOT NULL,
  `address` varchar(250) NOT NULL,
  `address_en` varchar(255) NOT NULL,
  `commercial_registry` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `whatsap` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `facebook` text NOT NULL,
  `twitter` text NOT NULL,
  `instegram` text NOT NULL,
  `youtube` text NOT NULL,
  `snapchat` text NOT NULL,
  `android_url` varchar(250) NOT NULL,
  `ios_url` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `descrobtion_en` text NOT NULL,
  `keyword` text NOT NULL,
  `keyword_en` text NOT NULL,
  `contact_top` varchar(250) NOT NULL,
  `contact_top_en` varchar(250) NOT NULL,
  `contact_text` text NOT NULL,
  `contact_text_en` text NOT NULL,
  `about` varchar(255) NOT NULL,
  `about_en` varchar(255) NOT NULL,
  `service` text NOT NULL,
  `service_en` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `product_en` varchar(255) NOT NULL,
  `client` varchar(255) NOT NULL,
  `client_en` varchar(255) NOT NULL,
  `customer` varchar(255) NOT NULL,
  `customer_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

-- --------------------------------------------------------

--
-- بنية الجدول `shop_api_keys`
--

DROP TABLE IF EXISTS `shop_api_keys`;
CREATE TABLE IF NOT EXISTS `shop_api_keys` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `permissions` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `consumer_key` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `consumer_secret` char(43) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `consumer_key` (`consumer_key`),
  KEY `consumer_secret` (`consumer_secret`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
INSERT INTO `shop_api_keys` (`id`, `key_id`, `client_id`, `user_id`, `description`, `permissions`, `consumer_key`, `consumer_secret`) VALUES
	(1, 9, 918, 2, 'storetestApi', 'read_write', 'ck_f326614249dd8416800bb07be3a891ec32810108', 'cs_8ff65a8cc710e73432f5523ed6ef0a704542fb29'),
	(2, 1, 14, 6, 'Arkanpro Api', 'read_write', 'ck_3a59f7b3f4515c8b479bb16eeeab141426744400', 'cs_0340f1ae0b8e7a0b2dc9e98e8f73e06f64ad5db0'),
	(3, 1, 609, 5, 'Aloud Api', 'read_write', 'ck_f3676284a3d94de320a7c079f5f342187edea62b', 'cs_32da4f22f86cf138dc375f1f95c406ac0f41a2d6'),
	(4, 1, 36, 4, 'Offline2 Api', 'read_write', 'ck_7942531cefff120b38599a10f9f30a0202032d58', 'cs_29d07c6d690e1c42e2bce6145ed684ad3b72d30c'),
	(5, 1, 561, 7, 'Utoor Api', 'read_write', 'ck_8f425f86ef125e1ac02aefd8771cb2a9a6b3fda1', 'cs_52f2b148e38568bfdbfdd85fe585f82861fe92a2'),
	(6, 3, 918, 5, NULL, 'read_write', 'ck_ff042fd10e67efc8fec2d67507797ef59b0c224d', 'cs_d4b900e9bc3800eddc84ad8a6448e10e704e565c'),
	(7, 3, 393, 8, NULL, 'read_write', 'ck_7656be3f00a41c4d7c0343f87fc6e941e030f9bf', 'cs_7966107e58077aa2ea7cbe3e86db6c0cca27f8d7'),
	(10, 5, 393, 8, NULL, 'read_write', 'ck_1189a9e258a02ab601e9491b09926ac16ea96480', 'cs_43924fca7a484b3376b812d21f2af8e7724c25be'),
	(11, 1, 1168, 20, NULL, 'read_write', 'ck_c948cf2515252cbd239b788e9f04b38f62619c49', 'cs_5f499f039380b00d17be439c4934d9b20faf0d30'),
	(12, 1, 464, 21, NULL, 'read_write', 'ck_76ad4bd6b9c4161311f650b70b2299a16385448c', 'cs_1e111ccbb68ece82693da514985dbc32c9ef64a9'),
	(13, 1, 422, 22, NULL, 'read_write', 'ck_6eceee6244aa391b9f88663653ae41893da28b7a', 'cs_a09bdacc82bb5c94b34047d798ff9d7f0a963312');

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_electronic_contract`
--

DROP TABLE IF EXISTS `tbl_electronic_contract`;
CREATE TABLE IF NOT EXISTS `tbl_electronic_contract` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `party2name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `contract_date` date NOT NULL,
  `party2info` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `party2address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `desc_1` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_1` decimal(10,2) NOT NULL DEFAULT '0.00',
  `period_1` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `desc_2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `period_2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_3` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_3` decimal(10,2) NOT NULL DEFAULT '0.00',
  `period_3` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_4` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_4` decimal(10,2) NOT NULL DEFAULT '0.00',
  `period_4` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_5` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_5` decimal(10,2) NOT NULL DEFAULT '0.00',
  `period_5` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `desc_6` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_6` decimal(10,2) NOT NULL DEFAULT '0.00',
  `period_6` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `contract_attachment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `isAccounting` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO `tbl_electronic_contract` (`id`, `company`, `party2name`, `contract_date`, `party2info`, `party2address`, `desc_1`, `amount_1`, `period_1`, `desc_2`, `amount_2`, `period_2`, `desc_3`, `amount_3`, `period_3`, `desc_4`, `amount_4`, `period_4`, `desc_5`, `amount_5`, `period_5`, `desc_6`, `amount_6`, `period_6`, `total`, `tax`, `subtotal`, `contract_attachment`, `isAccounting`, `created_by`) VALUES
	(1, 'accountstest', 'المحاسبة تجريبي', '2024-11-20', 'السجل التجاري رقم 999999\r\nوتاريخه   \r\nالصادر من مدينة الجبيل\r\nالرقم الضريبي  :  999999999', 'العنوان : المملكة العربية السعودية\r\nمدينة  :الجبيل\r\nهاتف :  00966000000000\r\nايميل : ', 'رسوم التأسيس جهاز ( 0 ) طابعة ( 0 )', 0.00, '', 'رسوم التجديد السنوي للسنة القادمة', 1000.00, '', 'الرسوم للبرنامج حاليا', 1000.00, '', 'إضافة طابعات عدد ( 0 )', 0.00, '', 'االتدريب لمدة ( 5 ) ساعات', 0.00, '', 'الدعم الفني  والصيانة عن بعد لمدة العقد', 0.00, '', 1739.13, 260.87, 2000.00, './upload/Arkan_company_contract_for_accountstest.docx', 1, 8),
	(2, 'demo2', 'حميد', '2024-11-23', 'السجل التجاري رقم 88888888\r\nوتاريخه   23/11/2024\r\nالصادر من مدينة الجبيل\r\nالرقم الضريبي  :  99999999999999', 'العنوان : المملكة العربية السعودية\r\nمدينة  : الجبيل\r\nهاتف :  00966502478081\r\nايميل : ', 'رسوم التأسيس جهاز ( 0 ) طابعة ( 0 )', 0.00, '', 'رسوم التجديد السنوي للسنة القادمة', 1148.00, '', 'الرسوم للبرنامج حاليا', 1000.00, '', 'إضافة طابعات عدد ( 0 )', 0.00, '', 'االتدريب لمدة ( 5 ) ساعات', 0.00, '', 'الدعم الفني  والصيانة عن بعد لمدة العقد', 0.00, '', 1867.83, 280.17, 2148.00, './upload/Arkan_company_contract_for_demo2.docx', 0, 8),
	(3, 'makanalf', 'صالح ابو ريماس', '2025-01-16', 'السجل التجاري رقم 0000000\r\nوتاريخه   هـ\r\nالصادر من مدينة الجبيل\r\nالرقم الضريبي  :  000000000', 'العنوان : المملكة العربية السعودية\r\nمدينة  :\r\nهاتف :  00966505907567\r\nايميل : ', 'رسوم التأسيس جهاز ( 0 ) طابعة ( 0 )', 0.00, '', 'رسوم التجديد السنوي للسنة القادمة', 0.00, '', 'الرسوم للبرنامج حاليا', 0.00, '', 'إضافة طابعات عدد ( 0 )', 0.00, '', 'االتدريب لمدة ( 5 ) ساعات', 0.00, '', 'الدعم الفني  والصيانة عن بعد لمدة العقد', 0.00, '', 0.00, 0.00, 0.00, './upload/Arkan_company_contract_for_makanalf.docx', 1, 8),
	(4, 'demo3', 'حميد', '2025-12-21', 'السجل التجاري رقم \r\nوتاريخه   هـ\r\nالصادر من مدينة\r\nالرقم الضريبي  :  ', 'العنوان : المملكة العربية السعودية\r\nمدينة  :\r\nهاتف :  \r\nايميل : ', 'رسوم التأسيس جهاز ( 0 ) طابعة ( 0 )', 500.00, '', 'رسوم التجديد السنوي للسنة القادمة', 1000.00, '', 'الرسوم للبرنامج حاليا', 500.00, '', 'إضافة طابعات عدد ( 0 )', 500.00, '', 'االتدريب لمدة ( 5 ) ساعات', 500.00, '', 'الدعم الفني  والصيانة عن بعد لمدة العقد', 500.00, '', 3043.48, 456.52, 3500.00, './upload/Arkan_company_contract_for_demo3.docx', 1, 8);

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_log`
--

DROP TABLE IF EXISTS `tbl_log`;
CREATE TABLE IF NOT EXISTS `tbl_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userId` bigint NOT NULL,
  `userName` varchar(128) NOT NULL,
  `process` varchar(1024) NOT NULL,
  `processFunction` varchar(1024) NOT NULL,
  `userRoleId` bigint NOT NULL,
  `userRoleText` varchar(128) NOT NULL,
  `userIp` varchar(1024) NOT NULL,
  `userAgent` varchar(128) NOT NULL,
  `agentString` varchar(1024) NOT NULL,
  `platform` varchar(128) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_log_backup`
--

DROP TABLE IF EXISTS `tbl_log_backup`;
CREATE TABLE IF NOT EXISTS `tbl_log_backup` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userId` bigint NOT NULL,
  `userName` varchar(128) NOT NULL,
  `process` varchar(1024) NOT NULL,
  `processFunction` varchar(1024) NOT NULL,
  `userRoleId` bigint NOT NULL,
  `userRoleText` varchar(128) NOT NULL,
  `userIp` varchar(1024) NOT NULL,
  `userAgent` varchar(128) NOT NULL,
  `agentString` varchar(1024) NOT NULL,
  `platform` varchar(128) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_log_backup2`
--

DROP TABLE IF EXISTS `tbl_log_backup2`;
CREATE TABLE IF NOT EXISTS `tbl_log_backup2` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `userId` bigint NOT NULL,
  `userName` varchar(128) NOT NULL,
  `process` varchar(1024) NOT NULL,
  `processFunction` varchar(1024) NOT NULL,
  `userRoleId` bigint NOT NULL,
  `userRoleText` varchar(128) NOT NULL,
  `userIp` varchar(1024) NOT NULL,
  `userAgent` varchar(128) NOT NULL,
  `agentString` varchar(1024) NOT NULL,
  `platform` varchar(128) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_procedure`
--

DROP TABLE IF EXISTS `tbl_procedure`;
CREATE TABLE IF NOT EXISTS `tbl_procedure` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `company` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `request_by` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ptId` bigint NOT NULL,
  `comment` varchar(2048) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `createdBy` bigint NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `copied_company_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `copy_to_warehouse_code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------
INSERT INTO `tbl_procedure` (`id`, `company`, `request_by`, `ptId`, `comment`, `createdBy`, `createdDtm`, `copied_company_name`, `copy_to_warehouse_code`) VALUES
	(528, 'doyen', 'Customer', 4, '                                                                                    Aymen', 11, '2026-05-18 21:51:32', NULL, NULL);

--
-- بنية الجدول `tbl_procedure_type`
--

DROP TABLE IF EXISTS `tbl_procedure_type`;
CREATE TABLE IF NOT EXISTS `tbl_procedure_type` (
  `ptId` bigint NOT NULL AUTO_INCREMENT,
  `procedure_type` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sort_ord` tinyint NOT NULL,
  PRIMARY KEY (`ptId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO `tbl_procedure_type` (`ptId`, `procedure_type`, `sort_ord`) VALUES
	(1, 'Delete all data', 1),
	(2, 'Delete sales and purchases data', 2),
	(3, 'Copy products from another client', 4),
	(4, 'Reset password', 5),
	(5, 'Delete accounting data', 3),
	(6, 'Delete the last accounting period', 6);

-- Dumping structure for table cpesoft_info.tbl_reset_password
CREATE TABLE IF NOT EXISTS `tbl_reset_password` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `activation_id` varchar(32) NOT NULL,
  `agent` varchar(512) NOT NULL,
  `client_ip` varchar(32) NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `createdBy` bigint(20) NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL,
  `updatedBy` bigint(20) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table cpesoft_info.tbl_reset_password: ~3 rows (approximately)
INSERT INTO `tbl_reset_password` (`id`, `email`, `activation_id`, `agent`, `client_ip`, `isDeleted`, `createdBy`, `createdDtm`, `updatedBy`, `updatedDtm`) VALUES
	(1, 'yahya@arkanpro.com', 'TE6V4J9HNyKueWY', 'Chrome 111.0.0.0', '2.89.154.105', 0, 1, '2023-03-27 21:29:21', NULL, NULL);


-- --------------------------------------------------------

--
-- بنية الجدول `tbl_roles`
--

DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE IF NOT EXISTS `tbl_roles` (
  `roleId` tinyint NOT NULL AUTO_INCREMENT COMMENT 'role id',
  `role` varchar(50) NOT NULL COMMENT 'role text',
  PRIMARY KEY (`roleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
INSERT INTO `tbl_roles` (`roleId`, `role`) VALUES
	(1, 'Admin'),
	(2, 'Manager'),
	(3, 'Support');
-- --------------------------------------------------------

--
-- بنية الجدول `tbl_task`
--

DROP TABLE IF EXISTS `tbl_task`;
CREATE TABLE IF NOT EXISTS `tbl_task` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) NOT NULL,
  `comment` varchar(2048) NOT NULL,
  `statusId` int NOT NULL,
  `priorityId` int NOT NULL,
  `permalink` varchar(1024) NOT NULL,
  `createdBy` bigint NOT NULL,
  `endDtm` datetime DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_tasks_prioritys`
--

DROP TABLE IF EXISTS `tbl_tasks_prioritys`;
CREATE TABLE IF NOT EXISTS `tbl_tasks_prioritys` (
  `priorityId` bigint NOT NULL AUTO_INCREMENT,
  `priority` varchar(50) NOT NULL,
  PRIMARY KEY (`priorityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_tasks_situations`
--

DROP TABLE IF EXISTS `tbl_tasks_situations`;
CREATE TABLE IF NOT EXISTS `tbl_tasks_situations` (
  `statusId` bigint NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`statusId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL COMMENT 'login email',
  `password` varchar(128) NOT NULL COMMENT 'hashed login password',
  `name` varchar(128) DEFAULT NULL COMMENT 'full name of user',
  `mobile` varchar(20) DEFAULT NULL,
  `roleId` tinyint NOT NULL,
  `isDeleted` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `createdBy` int NOT NULL,
  `createdDtm` datetime NOT NULL,
  `updatedBy` int DEFAULT NULL,
  `updatedDtm` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- بنية الجدول `tbl_versions`
--

DROP TABLE IF EXISTS `tbl_versions`;
CREATE TABLE IF NOT EXISTS `tbl_versions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sunmi_version` varchar(55) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
INSERT INTO `tbl_versions` (`id`, `sunmi_version`) VALUES
	(1, 'V2.0.4');

-- --------------------------------------------------------

--
-- بنية الجدول `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password_hash` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password_reset_token` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `email` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `activation_code` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `user_type` tinyint NOT NULL DEFAULT '3',
  `avatar` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `address` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `country` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `city` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `region` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- بنية الجدول `whatsapp_messages`
--

DROP TABLE IF EXISTS `whatsapp_messages`;
CREATE TABLE IF NOT EXISTS `whatsapp_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `company` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_id` int NOT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_FK` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`);

--
-- قيود الجداول `client_transactions`
--
ALTER TABLE `client_transactions`
  ADD CONSTRAINT `client_transactions_FK` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `client_transactions_FK_1` FOREIGN KEY (`created_by`) REFERENCES `tbl_users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
