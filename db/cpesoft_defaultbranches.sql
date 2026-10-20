-- Database Dump: cpesoft_defaultbranches
-- Date: 2026-10-10 22:37:18

SET FOREIGN_KEY_CHECKS=0;


-- Table: sma_brands_details
DROP TABLE IF EXISTS `sma_brands_details`;
CREATE TABLE `sma_brands_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `brands_details_grp_FK` (`grpId`),
  KEY `brands_details_co_FK` (`coId`),
  KEY `brands_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_categories_details
DROP TABLE IF EXISTS `sma_categories_details`;
CREATE TABLE `sma_categories_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `categories_details_grp_FK` (`grpId`),
  KEY `categories_details_co_FK` (`coId`),
  KEY `categories_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_currencies_details
DROP TABLE IF EXISTS `sma_currencies_details`;
CREATE TABLE `sma_currencies_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `currencies_details_grp_FK` (`grpId`),
  KEY `currencies_details_co_FK` (`coId`),
  KEY `currencies_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_customers_details
DROP TABLE IF EXISTS `sma_customers_details`;
CREATE TABLE `sma_customers_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_id` (`customer_id`,`grpId`,`coId`,`brId`),
  KEY `customers_details_grp_FK` (`grpId`),
  KEY `customers_details_co_FK` (`coId`),
  KEY `customers_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_group_settings
DROP TABLE IF EXISTS `sma_group_settings`;
CREATE TABLE `sma_group_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_share_customers` tinyint(1) NOT NULL DEFAULT '0',
  `auto_share_suppliers` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_group_settings` VALUES ('1', '0', '0');

-- Table: sma_main_branches
DROP TABLE IF EXISTS `sma_main_branches`;
CREATE TABLE `sma_main_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grpId` int(11) NOT NULL,
  `coId` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nameEn` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `addressEn` varchar(255) DEFAULT NULL,
  `phone` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `branch_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `branch_prefix` varchar(20) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `branch_nameEn` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `main_branches_grp_FK` (`grpId`),
  KEY `main_branches_co_FK` (`coId`),
  CONSTRAINT `main_branches_co_FK` FOREIGN KEY (`coId`) REFERENCES `sma_main_companies` (`id`),
  CONSTRAINT `main_branches_grp_FK` FOREIGN KEY (`grpId`) REFERENCES `sma_main_groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_main_branches` VALUES ('1', '1', '1', 'br_1', 'الفرع الرئيسي', NULL, 'default', NULL, NULL, NULL, '0', NULL, '2024-01-17 12:29:18', '1', NULL, 'BR1', NULL, NULL);

-- Table: sma_main_companies
DROP TABLE IF EXISTS `sma_main_companies`;
CREATE TABLE `sma_main_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grpId` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nameEn` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `addressEn` varchar(255) DEFAULT NULL,
  `phone` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `company_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `main_groups_grp_FK` (`grpId`),
  CONSTRAINT `main_groups_grp_FK` FOREIGN KEY (`grpId`) REFERENCES `sma_main_groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_main_companies` VALUES ('1', '1', 'co_1', 'الشركة الرئيسية', NULL, 'default', NULL, NULL, NULL, '0', NULL, '2024-01-17 12:29:18', '1', NULL);

-- Table: sma_main_groups
DROP TABLE IF EXISTS `sma_main_groups`;
CREATE TABLE `sma_main_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nameEn` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `addressEn` varchar(255) DEFAULT NULL,
  `phone` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `group_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_main_groups` VALUES ('1', 'grp_1', 'المجموعة الرئيسية', NULL, 'default', NULL, NULL, NULL, '0', NULL, '2024-01-17 12:29:18', '1', NULL);

-- Table: sma_suppliers_details
DROP TABLE IF EXISTS `sma_suppliers_details`;
CREATE TABLE `sma_suppliers_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `suppliers_details_grp_FK` (`grpId`),
  KEY `suppliers_details_co_FK` (`coId`),
  KEY `suppliers_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_taxs_details
DROP TABLE IF EXISTS `sma_taxs_details`;
CREATE TABLE `sma_taxs_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_rate_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `taxs_details_grp_FK` (`grpId`),
  KEY `taxs_details_co_FK` (`coId`),
  KEY `taxs_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_units_details
DROP TABLE IF EXISTS `sma_units_details`;
CREATE TABLE `sma_units_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `units_details_grp_FK` (`grpId`),
  KEY `units_details_co_FK` (`coId`),
  KEY `units_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_variants_details
DROP TABLE IF EXISTS `sma_variants_details`;
CREATE TABLE `sma_variants_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variant_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `variants_details_grp_FK` (`grpId`),
  KEY `variants_details_co_FK` (`coId`),
  KEY `variants_details_br_FK` (`brId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS=1;
