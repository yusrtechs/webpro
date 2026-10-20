-- Database Dump: cpesoft_fixedassets
-- Date: 2026-10-10 22:42:56

SET FOREIGN_KEY_CHECKS=0;


-- Table: sma_ast_depreciations
DROP TABLE IF EXISTS `sma_ast_depreciations`;
CREATE TABLE `sma_ast_depreciations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fixed_asset_id` int(11) NOT NULL,
  `period_date` date NOT NULL,
  `start_value` decimal(12,4) NOT NULL,
  `depreciation_ratio` decimal(12,4) NOT NULL,
  `depreciation_amount` decimal(12,4) NOT NULL,
  `accumulated_value` decimal(12,4) NOT NULL,
  `end_value` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_ast_fixed_assets
DROP TABLE IF EXISTS `sma_ast_fixed_assets`;
CREATE TABLE `sma_ast_fixed_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `specifications` text COLLATE utf8_unicode_ci,
  `location` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `measurement` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `color` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `man_company` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_origin` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quality` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `style` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'no_image.png',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `parent_class_id` int(11) DEFAULT NULL,
  `sub_class_id` int(11) DEFAULT NULL,
  `branch_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cost` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `value` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `purchase_date` timestamp NULL DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `total_tax` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `grand_total` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `start_date` date DEFAULT NULL,
  `useful_life` tinyint(2) NOT NULL DEFAULT '0',
  `accountID` bigint(20) DEFAULT NULL,
  `accountIDEX` bigint(20) DEFAULT NULL,
  `depreciation_ratio` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `scrap_value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `depreciation_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `depreciation_amount_per_year` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `ast_fixed_assets_grp_FK` (`group_id`),
  CONSTRAINT `ast_fixed_assets_grp_FK` FOREIGN KEY (`group_id`) REFERENCES `sma_ast_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_ast_group
DROP TABLE IF EXISTS `sma_ast_group`;
CREATE TABLE `sma_ast_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nameEn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_ast_group` VALUES ('1', 'grp_1', 'اثاثات', '', '2024-01-01 13:53:14', '1');

-- Table: sma_ast_increasing
DROP TABLE IF EXISTS `sma_ast_increasing`;
CREATE TABLE `sma_ast_increasing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `fixed_asset_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `increasing_type` tinyint(1) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `effect_type` tinyint(1) NOT NULL,
  `cost` decimal(12,4) DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `tax` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `net` decimal(12,4) DEFAULT NULL,
  `useful_life` tinyint(2) DEFAULT NULL,
  `paid` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `payment_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_ast_increasing_payments
DROP TABLE IF EXISTS `sma_ast_increasing_payments`;
CREATE TABLE `sma_ast_increasing_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `increasing_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(12) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `amount` decimal(12,4) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `pos_paid` decimal(12,4) DEFAULT '0.0000',
  `pos_balance` decimal(12,4) DEFAULT '0.0000',
  `approval_code` varchar(50) DEFAULT NULL,
  `accountID` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_ast_parent_classes
DROP TABLE IF EXISTS `sma_ast_parent_classes`;
CREATE TABLE `sma_ast_parent_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nameEn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_ast_parent_classes` VALUES ('1', 'pc_1', 'اثاث مكتبي', '', '2024-01-01 13:53:14', '1');

-- Table: sma_ast_revaluations
DROP TABLE IF EXISTS `sma_ast_revaluations`;
CREATE TABLE `sma_ast_revaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `fixed_asset_id` int(11) NOT NULL,
  `accountID` bigint(20) NOT NULL,
  `book_value` decimal(12,4) NOT NULL,
  `revaluation_value` decimal(12,4) NOT NULL,
  `balance` decimal(12,4) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_no` (`reference_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_ast_sub_classes
DROP TABLE IF EXISTS `sma_ast_sub_classes`;
CREATE TABLE `sma_ast_sub_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nameEn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `ast_sub_classes_grp_FK` (`parent_id`),
  CONSTRAINT `ast_sub_classes_grp_FK` FOREIGN KEY (`parent_id`) REFERENCES `sma_ast_parent_classes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_ast_sub_classes` VALUES ('1', 'sc_1', 'كراسي', '', '2024-01-01 13:53:32', '1', '1');

SET FOREIGN_KEY_CHECKS=1;
