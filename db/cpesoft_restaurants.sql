-- Database Dump: cpesoft_restaurants
-- Date: 2026-10-10 22:46:20

SET FOREIGN_KEY_CHECKS=0;


-- Table: sma_addresses
DROP TABLE IF EXISTS `sma_addresses`;
CREATE TABLE `sma_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `line1` varchar(50) NOT NULL,
  `line2` varchar(50) DEFAULT NULL,
  `city` varchar(25) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `state` varchar(25) NOT NULL,
  `country` varchar(50) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjust_items
DROP TABLE IF EXISTS `sma_adjust_items`;
CREATE TABLE `sma_adjust_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `cost` decimal(25,12) NOT NULL,
  `value` decimal(25,12) NOT NULL,
  `unit` int(11) NOT NULL,
  `expiry` date DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjustment_items
DROP TABLE IF EXISTS `sma_adjustment_items`;
CREATE TABLE `sma_adjustment_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `cost` decimal(25,12) NOT NULL,
  `value` decimal(25,12) NOT NULL,
  `unit` int(11) NOT NULL,
  `expiry` date DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjustment_items_count
DROP TABLE IF EXISTS `sma_adjustment_items_count`;
CREATE TABLE `sma_adjustment_items_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `cost` decimal(15,4) NOT NULL,
  `value` decimal(15,4) NOT NULL,
  `unit` int(11) NOT NULL,
  `expiry` date DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjustment_items_store
DROP TABLE IF EXISTS `sma_adjustment_items_store`;
CREATE TABLE `sma_adjustment_items_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `cost` float NOT NULL,
  `value` float NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `unit` int(11) NOT NULL,
  `reason` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjustments
DROP TABLE IF EXISTS `sma_adjustments`;
CREATE TABLE `sma_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` text,
  `approve_note` text,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjustments_count
DROP TABLE IF EXISTS `sma_adjustments_count`;
CREATE TABLE `sma_adjustments_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` text,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjustments_store
DROP TABLE IF EXISTS `sma_adjustments_store`;
CREATE TABLE `sma_adjustments_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `cost` float NOT NULL,
  `value` float NOT NULL,
  `note` text,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_adjusts
DROP TABLE IF EXISTS `sma_adjusts`;
CREATE TABLE `sma_adjusts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` text,
  `approve_note` text,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `notice_creditor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_api_keys
DROP TABLE IF EXISTS `sma_api_keys`;
CREATE TABLE `sma_api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reference` varchar(40) NOT NULL,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `is_private_key` tinyint(1) NOT NULL DEFAULT '0',
  `ip_addresses` text,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_api_limits
DROP TABLE IF EXISTS `sma_api_limits`;
CREATE TABLE `sma_api_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `count` int(10) NOT NULL,
  `hour_started` int(11) NOT NULL,
  `api_key` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_api_logs
DROP TABLE IF EXISTS `sma_api_logs`;
CREATE TABLE `sma_api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL,
  `method` varchar(6) NOT NULL,
  `params` text,
  `api_key` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` int(11) NOT NULL,
  `rtime` float DEFAULT NULL,
  `authorized` varchar(1) NOT NULL,
  `response_code` smallint(3) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_brands
DROP TABLE IF EXISTS `sma_brands`;
CREATE TABLE `sma_brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(50) DEFAULT NULL,
  `slug` varchar(55) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_brands` VALUES ('1', 'Default', 'Default', NULL, NULL, NULL, '1', '1', '1');

-- Table: sma_calendar
DROP TABLE IF EXISTS `sma_calendar`;
CREATE TABLE `sma_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `color` varchar(7) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_captcha
DROP TABLE IF EXISTS `sma_captcha`;
CREATE TABLE `sma_captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
  `captcha_time` int(10) unsigned NOT NULL,
  `ip_address` varchar(16) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `word` varchar(20) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_categories
DROP TABLE IF EXISTS `sma_categories`;
CREATE TABLE `sma_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `image` varchar(55) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `slug` varchar(55) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hide_pos` tinyint(4) NOT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `nameEn` varchar(55) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `isTobacco` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_categories` VALUES ('1', 'Default', 'Default', NULL, '0', 'default', NULL, '0', '0', NULL, '1', '1', '1', '0');

-- Table: sma_category_modifiers
DROP TABLE IF EXISTS `sma_category_modifiers`;
CREATE TABLE `sma_category_modifiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `modifier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `modifier_id` (`modifier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_combo_avg_cost
DROP TABLE IF EXISTS `sma_combo_avg_cost`;
CREATE TABLE `sma_combo_avg_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `avg_cost` decimal(25,12) NOT NULL,
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `item_cost_value` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_combo_items
DROP TABLE IF EXISTS `sma_combo_items`;
CREATE TABLE `sma_combo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_code` varchar(20) NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_combo_overselling
DROP TABLE IF EXISTS `sma_combo_overselling`;
CREATE TABLE `sma_combo_overselling` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `quantity_overselling` decimal(15,4) NOT NULL,
  `quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `item_cost_value` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_companies
DROP TABLE IF EXISTS `sma_companies`;
CREATE TABLE `sma_companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned DEFAULT NULL,
  `group_name` varchar(20) NOT NULL,
  `customer_group_id` int(11) DEFAULT NULL,
  `customer_group_name` varchar(100) DEFAULT NULL,
  `name` varchar(55) NOT NULL,
  `company` varchar(255) NOT NULL,
  `vat_no` varchar(100) DEFAULT NULL,
  `cr_no` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(55) DEFAULT NULL,
  `state` varchar(55) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `cf1` varchar(100) DEFAULT NULL,
  `cf2` varchar(100) DEFAULT NULL,
  `cf3` varchar(100) DEFAULT NULL,
  `cf4` varchar(100) DEFAULT NULL,
  `cf5` varchar(100) DEFAULT NULL,
  `cf6` varchar(100) DEFAULT NULL,
  `invoice_footer` text,
  `payment_term` int(11) DEFAULT '0',
  `logo` varchar(255) DEFAULT 'empty.png',
  `award_points` int(11) DEFAULT '0',
  `deposit_amount` decimal(25,4) DEFAULT NULL,
  `price_group_id` int(11) DEFAULT NULL,
  `price_group_name` varchar(50) DEFAULT NULL,
  `gst_no` varchar(100) DEFAULT NULL,
  `co_type` tinyint(1) DEFAULT NULL,
  `Offline_id` int(11) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `nameEn` varchar(55) DEFAULT NULL,
  `companyEn` varchar(255) DEFAULT NULL,
  `addressEn` varchar(255) DEFAULT NULL,
  `hide_in_pos` tinyint(1) NOT NULL DEFAULT '0',
  `building_no` varchar(8) DEFAULT NULL,
  `street_name` varchar(250) DEFAULT NULL,
  `district_name` varchar(250) DEFAULT NULL,
  `additional_no` varchar(8) DEFAULT NULL,
  `co_to_co` tinyint(1) NOT NULL DEFAULT '1',
  `is_auto_account` tinyint(1) NOT NULL DEFAULT '0',
  `street_nameEn` varchar(255) DEFAULT NULL,
  `district_nameEn` varchar(255) DEFAULT NULL,
  `cityEn` varchar(255) DEFAULT NULL,
  `stateEn` varchar(255) DEFAULT NULL,
  `countryEn` varchar(255) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `code` varchar(50) DEFAULT NULL,
  `account_type` tinyint(1) NOT NULL DEFAULT '0',
  `customer_ref_id` int(11) DEFAULT NULL,
  `company_tax` int(11) NOT NULL DEFAULT '1',
  `subscription_duration` int(11) DEFAULT NULL,
  `is_auto_advance_account` tinyint(1) NOT NULL DEFAULT '0',
  `start_subscription` timestamp NULL DEFAULT NULL,
  `end_subscription` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`company`,`Offline_id`,`group_id`),
  KEY `group_id` (`group_id`),
  KEY `group_id_2` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `sma_companies` VALUES ('1', NULL, 'biller', NULL, NULL, 'Casher', 'Casher', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '', '0', NULL, NULL, NULL, '', NULL, NULL, '0', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '1', '0', NULL, NULL, NULL, NULL, NULL, '1', '1', '1', NULL, '0', NULL, '1', NULL, '0', NULL, NULL);
INSERT INTO `sma_companies` VALUES ('2', '3', 'customer', '1', 'General', '', 'Default', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '0', 'logo.png', '0', NULL, '1', 'Default', NULL, '1', NULL, '0', NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, '1', '0', NULL, NULL, NULL, NULL, NULL, '1', '1', '1', NULL, '0', NULL, '1', NULL, '0', NULL, NULL);

-- Table: sma_costing
DROP TABLE IF EXISTS `sma_costing`;
CREATE TABLE `sma_costing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sale_item_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `purchase_item_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `purchase_net_unit_cost` decimal(25,4) DEFAULT NULL,
  `purchase_unit_cost` decimal(25,4) DEFAULT NULL,
  `sale_net_unit_price` decimal(25,4) NOT NULL,
  `sale_unit_price` decimal(25,4) NOT NULL,
  `quantity_balance` decimal(15,4) DEFAULT NULL,
  `inventory` tinyint(1) DEFAULT '0',
  `overselling` tinyint(1) DEFAULT '0',
  `option_id` int(11) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_coupons
DROP TABLE IF EXISTS `sma_coupons`;
CREATE TABLE `sma_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_gen_no` tinyint(3) NOT NULL,
  `coupon_code` text COLLATE utf8_unicode_ci NOT NULL,
  `discount_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'percent',
  `coupon_amount` decimal(12,4) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `products` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `product_categories` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `usage_limit` int(10) NOT NULL DEFAULT '0',
  `minimum_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `maximum_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `coupon_start_date` date DEFAULT NULL,
  `coupon_expiry_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `usage_count` int(10) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_currencies
DROP TABLE IF EXISTS `sma_currencies`;
CREATE TABLE `sma_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(55) NOT NULL,
  `rate` decimal(12,4) NOT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT '0',
  `symbol` varchar(50) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `symbol_logo` varchar(50) DEFAULT NULL,
  `fraction_name` varchar(55) DEFAULT NULL,
  `fraction_symbol` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_currencies` VALUES ('1', 'SR', 'SR', '1.0000', '0', NULL, '1', '1', '1', 'Saudi_Riyal_Symbol-1.png', NULL, NULL);

-- Table: sma_customer_groups
DROP TABLE IF EXISTS `sma_customer_groups`;
CREATE TABLE `sma_customer_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `percent` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_type` enum('percent','amount') NOT NULL DEFAULT 'percent',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_customer_groups` VALUES ('1', 'Default', '0', '0.00', 'percent');

-- Table: sma_customer_subscriptions
DROP TABLE IF EXISTS `sma_customer_subscriptions`;
CREATE TABLE `sma_customer_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `subscription_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start_subscription` timestamp NULL DEFAULT NULL,
  `end_subscription` timestamp NULL DEFAULT NULL,
  `paid_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `purchase_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_date_format
DROP TABLE IF EXISTS `sma_date_format`;
CREATE TABLE `sma_date_format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `js` varchar(20) NOT NULL,
  `php` varchar(20) NOT NULL,
  `sql` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT INTO `sma_date_format` VALUES ('1', 'mm-dd-yyyy', 'm-d-Y', '%m-%d-%Y');
INSERT INTO `sma_date_format` VALUES ('2', 'mm/dd/yyyy', 'm/d/Y', '%m/%d/%Y');
INSERT INTO `sma_date_format` VALUES ('3', 'mm.dd.yyyy', 'm.d.Y', '%m.%d.%Y');
INSERT INTO `sma_date_format` VALUES ('4', 'dd-mm-yyyy', 'd-m-Y', '%d-%m-%Y');
INSERT INTO `sma_date_format` VALUES ('5', 'dd/mm/yyyy', 'd/m/Y', '%d/%m/%Y');
INSERT INTO `sma_date_format` VALUES ('6', 'dd.mm.yyyy', 'd.m.Y', '%d.%m.%Y');
INSERT INTO `sma_date_format` VALUES ('7', 'yyyy-mm-dd', 'Y-m-d', '%Y-%m-%d');
INSERT INTO `sma_date_format` VALUES ('8', 'yyyy/mm/dd', 'Y/m/d', '%Y/%m/%d');
INSERT INTO `sma_date_format` VALUES ('9', 'yyyy.mm.dd', 'Y.m.d', '%Y.%m.%d');

-- Table: sma_deliver_items
DROP TABLE IF EXISTS `sma_deliver_items`;
CREATE TABLE `sma_deliver_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deliver_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `deliver_id` (`deliver_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_deliveries
DROP TABLE IF EXISTS `sma_deliveries`;
CREATE TABLE `sma_deliveries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sale_id` int(11) NOT NULL,
  `do_reference_no` varchar(50) NOT NULL,
  `sale_reference_no` varchar(50) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `address` varchar(1000) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL,
  `attachment` varchar(50) DEFAULT NULL,
  `delivered_by` varchar(50) DEFAULT NULL,
  `received_by` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_delivers
DROP TABLE IF EXISTS `sma_delivers`;
CREATE TABLE `sma_delivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `note` text,
  `address` text,
  `internal_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT '0.0000',
  `order_discount` decimal(25,4) DEFAULT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT '0.0000',
  `product_tax` decimal(25,4) DEFAULT '0.0000',
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT NULL,
  `shipping` decimal(25,4) DEFAULT '0.0000',
  `grand_total` decimal(25,4) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `plate_no` varchar(50) DEFAULT NULL,
  `exit_date` datetime DEFAULT NULL,
  `Offline_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_deposits
DROP TABLE IF EXISTS `sma_deposits`;
CREATE TABLE `sma_deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_id` int(11) NOT NULL,
  `amount` decimal(25,4) NOT NULL,
  `paid_by` varchar(50) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `Offline_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_dept
DROP TABLE IF EXISTS `sma_dept`;
CREATE TABLE `sma_dept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  `location` int(11) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdby` int(11) DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deactiveby` int(11) DEFAULT NULL,
  `decative_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_destruction
DROP TABLE IF EXISTS `sma_destruction`;
CREATE TABLE `sma_destruction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `cost` float NOT NULL,
  `value` float NOT NULL,
  `note` text,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `op_type` tinyint(1) NOT NULL DEFAULT '1',
  `op_acct` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_destruction_items
DROP TABLE IF EXISTS `sma_destruction_items`;
CREATE TABLE `sma_destruction_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `cost` float NOT NULL,
  `value` float NOT NULL,
  `unit` int(11) NOT NULL DEFAULT '1',
  `warehouse_id` int(11) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `adjustment_id` (`adjustment_id`),
  CONSTRAINT `sma_destruction_items_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `sma_destruction` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_expense_categories
DROP TABLE IF EXISTS `sma_expense_categories`;
CREATE TABLE `sma_expense_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sma_expense_categories` VALUES ('1', '0001', 'مصروفات عامة', '0');
INSERT INTO `sma_expense_categories` VALUES ('2', '0002', 'مصروفات تسويق', '0');
INSERT INTO `sma_expense_categories` VALUES ('3', '00003', 'مصروفات تشغيلية', '0');

-- Table: sma_expense_payments
DROP TABLE IF EXISTS `sma_expense_payments`;
CREATE TABLE `sma_expense_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expense_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(25) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `amount` decimal(25,4) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `pos_paid` decimal(25,4) DEFAULT '0.0000',
  `pos_balance` decimal(25,4) DEFAULT '0.0000',
  `approval_code` varchar(50) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_expenses
DROP TABLE IF EXISTS `sma_expenses`;
CREATE TABLE `sma_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference` varchar(50) NOT NULL,
  `amount` decimal(25,4) NOT NULL,
  `total` decimal(25,4) NOT NULL,
  `order_tax_id` int(11) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT '0.0000',
  `grand_total` decimal(25,4) NOT NULL,
  `paid` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `payment_status` varchar(20) DEFAULT 'pending',
  `note` varchar(1000) DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `other` tinyint(1) NOT NULL DEFAULT '0',
  `Offline_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `cost_id` int(11) DEFAULT NULL,
  `tax_method` tinyint(1) NOT NULL DEFAULT '1',
  `costCenterId` text,
  `costCenterAmount` text,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gift_card_topups
DROP TABLE IF EXISTS `sma_gift_card_topups`;
CREATE TABLE `sma_gift_card_topups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `card_id` int(11) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `card_id` (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gift_cards
DROP TABLE IF EXISTS `sma_gift_cards`;
CREATE TABLE `sma_gift_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `card_no` varchar(20) NOT NULL,
  `value` decimal(25,4) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `balance` decimal(25,4) NOT NULL,
  `expiry` date DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `paid_amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `note` varchar(1000) DEFAULT NULL,
  `tax_rate` int(11) NOT NULL DEFAULT '0',
  `tax_amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `total_paid_amount` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `gift_card_type` varchar(10) NOT NULL DEFAULT 'classic',
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_no` (`card_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_groups
DROP TABLE IF EXISTS `sma_groups`;
CREATE TABLE `sma_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `sma_groups` VALUES ('1', 'owner', 'Owner');
INSERT INTO `sma_groups` VALUES ('2', 'admin', 'Administrator');
INSERT INTO `sma_groups` VALUES ('3', 'customer', 'Customer');
INSERT INTO `sma_groups` VALUES ('4', 'supplier', 'Supplier');
INSERT INTO `sma_groups` VALUES ('5', 'sales', 'Sales Staff');

-- Table: sma_login_attempts
DROP TABLE IF EXISTS `sma_login_attempts`;
CREATE TABLE `sma_login_attempts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_migrations
DROP TABLE IF EXISTS `sma_migrations`;
CREATE TABLE `sma_migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sma_migrations` VALUES ('315');

-- Table: sma_modifiers
DROP TABLE IF EXISTS `sma_modifiers`;
CREATE TABLE `sma_modifiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no_image.png',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_notifications
DROP TABLE IF EXISTS `sma_notifications`;
CREATE TABLE `sma_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_date` datetime DEFAULT NULL,
  `till_date` datetime DEFAULT NULL,
  `scope` tinyint(1) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_order_ref
DROP TABLE IF EXISTS `sma_order_ref`;
CREATE TABLE `sma_order_ref` (
  `ref_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `so` int(11) NOT NULL DEFAULT '1',
  `qu` int(11) NOT NULL DEFAULT '1',
  `po` int(11) NOT NULL DEFAULT '1',
  `to` int(11) NOT NULL DEFAULT '1',
  `pos` int(11) NOT NULL DEFAULT '1',
  `do` int(11) NOT NULL DEFAULT '1',
  `pay` int(11) NOT NULL DEFAULT '1',
  `re` int(11) NOT NULL DEFAULT '1',
  `rep` int(11) NOT NULL DEFAULT '1',
  `ex` int(11) NOT NULL DEFAULT '1',
  `ppay` int(11) NOT NULL DEFAULT '1',
  `qa` int(11) DEFAULT '1',
  `rv` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ref_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_order_ref` VALUES ('1', '2020-10-01', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');

-- Table: sma_order_type
DROP TABLE IF EXISTS `sma_order_type`;
CREATE TABLE `sma_order_type` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sma_order_type` VALUES ('1', 'محلي', '1');
INSERT INTO `sma_order_type` VALUES ('2', 'سفري', '1');
INSERT INTO `sma_order_type` VALUES ('3', 'توصيل', '1');

-- Table: sma_payment_application
DROP TABLE IF EXISTS `sma_payment_application`;
CREATE TABLE `sma_payment_application` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `purpose` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `applied_amount` decimal(12,4) NOT NULL,
  `remarks` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_schedule` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `applied_schedule` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `expected_payment_date` date DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_payment_type
DROP TABLE IF EXISTS `sma_payment_type`;
CREATE TABLE `sma_payment_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `second_name` varchar(50) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `icon` blob,
  `isCredit` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(20) NOT NULL,
  `ischeque` tinyint(1) NOT NULL DEFAULT '0',
  `isCC` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `sma_payment_type` VALUES ('1', 'نقدي', 'Cash', '1', '', '0', 'cash', '0', '0', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('2', 'آجل', 'Credit', '0', '', '1', 'credit', '0', '0', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('3', 'شيك', 'Cheque', '0', '', '0', 'cheque', '1', '0', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('4', 'مدى', 'Madaa NT.', '1', '', '0', 'Mada', '0', '1', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('5', 'فيزاكارد', 'Visa Card', '1', '', '0', 'Visa', '0', '1', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('6', 'ماستركارد', 'Master Card', '1', '', '0', 'MasterCard', '0', '1', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('7', 'آمكس', 'Amex', '1', '', '0', 'Amex', '0', '1', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('8', 'Discover', 'Discover', '1', '', '0', 'Discover', '0', '1', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('9', 'STC Pay', 'STC Pay', '1', '', '0', 'STC Pay', '0', '1', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('10', 'تحويلات بنكية', 'Bank Transfer', '1', NULL, '0', 'bank_transfer', '0', '0', '1', '1', '1');
INSERT INTO `sma_payment_type` VALUES ('11', 'دفعة مقدمة', 'Deposit', '1', NULL, '0', 'deposit', '0', '0', '1', '1', '1');

-- Table: sma_payments
DROP TABLE IF EXISTS `sma_payments`;
CREATE TABLE `sma_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `sale_id` int(11) DEFAULT NULL,
  `return_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(25) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `gl_payment_method_id` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(25,4) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `pos_paid` decimal(25,4) DEFAULT '0.0000',
  `pos_balance` decimal(25,4) DEFAULT '0.0000',
  `approval_code` varchar(50) DEFAULT NULL,
  `currency_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `commercial_discount_id` varchar(20) DEFAULT NULL,
  `commercial_discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `donation` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `catch_receipt_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_paypal
DROP TABLE IF EXISTS `sma_paypal`;
CREATE TABLE `sma_paypal` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `account_email` varchar(255) NOT NULL,
  `paypal_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `fixed_charges` decimal(25,4) NOT NULL DEFAULT '2.0000',
  `extra_charges_my` decimal(25,4) NOT NULL DEFAULT '3.9000',
  `extra_charges_other` decimal(25,4) NOT NULL DEFAULT '4.4000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_permissions
DROP TABLE IF EXISTS `sma_permissions`;
CREATE TABLE `sma_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `products-index` tinyint(1) DEFAULT '0',
  `products-add` tinyint(1) DEFAULT '0',
  `products-edit` tinyint(1) DEFAULT '0',
  `products-delete` tinyint(1) DEFAULT '0',
  `products-cost` tinyint(1) DEFAULT '0',
  `products-price` tinyint(1) DEFAULT '0',
  `quotes-index` tinyint(1) DEFAULT '0',
  `quotes-add` tinyint(1) DEFAULT '0',
  `quotes-edit` tinyint(1) DEFAULT '0',
  `quotes-pdf` tinyint(1) DEFAULT '0',
  `quotes-email` tinyint(1) DEFAULT '0',
  `quotes-delete` tinyint(1) DEFAULT '0',
  `sales-index` tinyint(1) DEFAULT '0',
  `sales-add` tinyint(1) DEFAULT '0',
  `sales-edit` tinyint(1) DEFAULT '0',
  `sales-pdf` tinyint(1) DEFAULT '0',
  `sales-email` tinyint(1) DEFAULT '0',
  `sales-delete` tinyint(1) DEFAULT '0',
  `purchases-index` tinyint(1) DEFAULT '0',
  `purchases-add` tinyint(1) DEFAULT '0',
  `purchases-edit` tinyint(1) DEFAULT '0',
  `purchases-pdf` tinyint(1) DEFAULT '0',
  `purchases-email` tinyint(1) DEFAULT '0',
  `purchases-delete` tinyint(1) DEFAULT '0',
  `transfers-index` tinyint(1) DEFAULT '0',
  `transfers-add` tinyint(1) DEFAULT '0',
  `transfers-edit` tinyint(1) DEFAULT '0',
  `transfers-pdf` tinyint(1) DEFAULT '0',
  `transfers-email` tinyint(1) DEFAULT '0',
  `transfers-delete` tinyint(1) DEFAULT '0',
  `customers-index` tinyint(1) DEFAULT '0',
  `customers-add` tinyint(1) DEFAULT '0',
  `customers-edit` tinyint(1) DEFAULT '0',
  `customers-delete` tinyint(1) DEFAULT '0',
  `suppliers-index` tinyint(1) DEFAULT '0',
  `suppliers-add` tinyint(1) DEFAULT '0',
  `suppliers-edit` tinyint(1) DEFAULT '0',
  `suppliers-delete` tinyint(1) DEFAULT '0',
  `sales-deliveries` tinyint(1) DEFAULT '0',
  `sales-add_delivery` tinyint(1) DEFAULT '0',
  `sales-edit_delivery` tinyint(1) DEFAULT '0',
  `sales-delete_delivery` tinyint(1) DEFAULT '0',
  `sales-email_delivery` tinyint(1) DEFAULT '0',
  `sales-pdf_delivery` tinyint(1) DEFAULT '0',
  `sales-gift_cards` tinyint(1) DEFAULT '0',
  `sales-add_gift_card` tinyint(1) DEFAULT '0',
  `sales-edit_gift_card` tinyint(1) DEFAULT '0',
  `sales-delete_gift_card` tinyint(1) DEFAULT '0',
  `pos-index` tinyint(1) DEFAULT '0',
  `pos-return` tinyint(1) DEFAULT '0',
  `sales-return_sales` tinyint(1) DEFAULT '0',
  `reports-index` tinyint(1) DEFAULT '0',
  `reports-warehouse_stock` tinyint(1) DEFAULT '0',
  `reports-quantity_alerts` tinyint(1) DEFAULT '0',
  `reports-expiry_alerts` tinyint(1) DEFAULT '0',
  `reports-products` tinyint(1) DEFAULT '0',
  `reports-daily_sales` tinyint(1) DEFAULT '0',
  `reports-monthly_sales` tinyint(1) DEFAULT '0',
  `reports-sales` tinyint(1) DEFAULT '0',
  `reports-payments` tinyint(1) DEFAULT '0',
  `reports-purchases` tinyint(1) DEFAULT '0',
  `reports-profit_loss` tinyint(1) DEFAULT '0',
  `reports-customers` tinyint(1) DEFAULT '0',
  `reports-suppliers` tinyint(1) DEFAULT '0',
  `reports-staff` tinyint(1) DEFAULT '0',
  `reports-register` tinyint(1) DEFAULT '0',
  `sales-payments` tinyint(1) DEFAULT '0',
  `purchases-payments` tinyint(1) DEFAULT '0',
  `purchases-expenses` tinyint(1) DEFAULT '0',
  `products-adjustments` tinyint(1) NOT NULL DEFAULT '0',
  `bulk_actions` tinyint(1) NOT NULL DEFAULT '0',
  `customers-deposits` tinyint(1) NOT NULL DEFAULT '0',
  `customers-delete_deposit` tinyint(1) NOT NULL DEFAULT '0',
  `products-barcode` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-return_purchases` tinyint(1) NOT NULL DEFAULT '0',
  `reports-expenses` tinyint(1) NOT NULL DEFAULT '0',
  `reports-daily_purchases` tinyint(1) DEFAULT '0',
  `reports-monthly_purchases` tinyint(1) DEFAULT '0',
  `products-stock_count` tinyint(1) DEFAULT '0',
  `edit_price` tinyint(1) DEFAULT '0',
  `returns-index` tinyint(1) DEFAULT '0',
  `returns-add` tinyint(1) DEFAULT '0',
  `returns-edit` tinyint(1) DEFAULT '0',
  `returns-delete` tinyint(1) DEFAULT '0',
  `returns-email` tinyint(1) DEFAULT '0',
  `returns-pdf` tinyint(1) DEFAULT '0',
  `reports-tax` tinyint(1) DEFAULT '0',
  `warehouse-warehouses` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse-quantity_adjustments` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse-add_adjustment` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse-stock_counts` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse-count_stock` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse-warehouse_stock` tinyint(1) NOT NULL DEFAULT '0',
  `products-warehouses` tinyint(1) NOT NULL DEFAULT '0',
  `products-add_warehouse` tinyint(1) NOT NULL DEFAULT '0',
  `products-edit_warehouse` tinyint(1) NOT NULL DEFAULT '0',
  `products-delete_warehouse` tinyint(1) NOT NULL DEFAULT '0',
  `products-approved_adjustments` tinyint(1) NOT NULL DEFAULT '0',
  `products-destruction_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `products-reports` tinyint(1) NOT NULL DEFAULT '0',
  `sales-reports` tinyint(1) NOT NULL DEFAULT '0',
  `promos-index` tinyint(1) NOT NULL DEFAULT '0',
  `promos-add` tinyint(1) NOT NULL DEFAULT '0',
  `promos-edit` tinyint(1) NOT NULL DEFAULT '0',
  `promos-delete` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-reports` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-accounts_tree` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_account` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_account` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-delete_account` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-tree_modal_view` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-opening_balance` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_opening_balance` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_opening_balance` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-delete_opening_balance` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_opening_balance` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-entry_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-entry_add` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-entry_edit` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-entry_delete` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_entries` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-accounting_period_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_accounting_period` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_accounting_period` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-delete_accounting_period` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-fiscal_year_closing` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-receipts_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-catch_receipts_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_catch_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_catch_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_catch_receipt` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-refunding_bonds_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_refunding_bonds` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_refunding_bonds` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_refunding_bond` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-payment_types_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_payment_type` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_payment_type` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_payment_types` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-cost_centers` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_cost_center` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_cost_center` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-delete_cost_center` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-cost_centers_view_popup` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-ledgers` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-trialbalance` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-entriesreport` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-incomestatement` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-balancesheet` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-accountstatement` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-distributioncostcenters` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-agedopeninvoices` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-dashboard_acc` tinyint(1) NOT NULL DEFAULT '0',
  `products-manual_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `products-units` tinyint(1) NOT NULL DEFAULT '0',
  `products-add_unit` tinyint(1) NOT NULL DEFAULT '0',
  `products-edit_unit` tinyint(1) NOT NULL DEFAULT '0',
  `products-delete_unit` tinyint(1) NOT NULL DEFAULT '0',
  `products-units_xls` tinyint(1) NOT NULL DEFAULT '0',
  `products-categories` tinyint(1) NOT NULL DEFAULT '0',
  `products-add_category` tinyint(1) NOT NULL DEFAULT '0',
  `products-edit_category` tinyint(1) NOT NULL DEFAULT '0',
  `products-delete_category` tinyint(1) NOT NULL DEFAULT '0',
  `products-category_xls` tinyint(1) NOT NULL DEFAULT '0',
  `products-warehouse_stock` tinyint(1) NOT NULL DEFAULT '0',
  `products-adjustments_report` tinyint(1) NOT NULL DEFAULT '0',
  `products-products_item_report` tinyint(1) NOT NULL DEFAULT '0',
  `products-products_report` tinyint(1) NOT NULL DEFAULT '0',
  `products-quantity_alerts` tinyint(1) NOT NULL DEFAULT '0',
  `products-expiry_alerts` tinyint(1) NOT NULL DEFAULT '0',
  `products-warehouse_count` tinyint(1) NOT NULL DEFAULT '0',
  `products-warehouse_store` tinyint(1) NOT NULL DEFAULT '0',
  `products-destruction` tinyint(1) NOT NULL DEFAULT '0',
  `sales-details` tinyint(1) NOT NULL DEFAULT '0',
  `sales-csv` tinyint(1) NOT NULL DEFAULT '0',
  `sales-details_delivery` tinyint(1) NOT NULL DEFAULT '0',
  `sales-add_payment` tinyint(1) NOT NULL DEFAULT '0',
  `sales-edit_payment` tinyint(1) NOT NULL DEFAULT '0',
  `sales-delete_payment` tinyint(1) NOT NULL DEFAULT '0',
  `sales-payment_note` tinyint(1) NOT NULL DEFAULT '0',
  `sales-payment_note_gift_card` tinyint(1) NOT NULL DEFAULT '0',
  `sales-charge_gift_card` tinyint(1) NOT NULL DEFAULT '0',
  `sales-price_groups` tinyint(1) NOT NULL DEFAULT '0',
  `sales-add_price_group` tinyint(1) NOT NULL DEFAULT '0',
  `sales-edit_price_group` tinyint(1) NOT NULL DEFAULT '0',
  `sales-delete_price_group` tinyint(1) NOT NULL DEFAULT '0',
  `sales-group_product_prices` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-update_entries_status` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-update_accounting_period_status` tinyint(1) NOT NULL DEFAULT '0',
  `sales-best_sellers` tinyint(1) NOT NULL DEFAULT '0',
  `sales-daily_sales` tinyint(1) NOT NULL DEFAULT '0',
  `sales-monthly_sales` tinyint(1) NOT NULL DEFAULT '0',
  `sales-sales_report` tinyint(1) NOT NULL DEFAULT '0',
  `sales-transaction_report` tinyint(1) NOT NULL DEFAULT '0',
  `sales-delivery_report` tinyint(1) NOT NULL DEFAULT '0',
  `sales-payments_report` tinyint(1) NOT NULL DEFAULT '0',
  `sales-customers_report` tinyint(1) NOT NULL DEFAULT '0',
  `sales-customers_payments` tinyint(1) NOT NULL DEFAULT '0',
  `sales-register_report` tinyint(1) NOT NULL DEFAULT '0',
  `sales-credit_notes` tinyint(1) NOT NULL DEFAULT '0',
  `sales-commercial_discounts` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-daily_purchases` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-monthly_purchases` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-expenses_report` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-Debit_Notes` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-payments_report` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-expense_payments_report` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-add_expense` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-edit_expense` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-delete_expense` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-xls_expense` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-add_payment` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-edit_payment` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-delete_payment` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-email_payment` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-csv` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-update_status` tinyint(1) NOT NULL DEFAULT '0',
  `reports-users_report` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-bom` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-add_bom` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-bom_update` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-deletebom` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-bom_details` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-index` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-add_manufacturing` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-edit_manufacturing` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-deletemanufacturing` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-done_view` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-progress` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-done` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-checkstock` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-update_status` tinyint(1) NOT NULL DEFAULT '0',
  `manufacturing-progress_details` tinyint(1) NOT NULL DEFAULT '0',
  `purchases-ordered` tinyint(1) NOT NULL DEFAULT '1',
  `purchases-pending` tinyint(1) NOT NULL DEFAULT '1',
  `purchases-received` tinyint(1) NOT NULL DEFAULT '1',
  `transfers-ordered` tinyint(1) NOT NULL DEFAULT '1',
  `transfers-pending` tinyint(1) NOT NULL DEFAULT '1',
  `transfers-sent` tinyint(1) NOT NULL DEFAULT '1',
  `transfers-received` tinyint(1) NOT NULL DEFAULT '1',
  `accounts-notice_creditor_index` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-add_notice_creditor` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-edit_notice_creditor` tinyint(1) NOT NULL DEFAULT '0',
  `accounts-view_notice_creditor` tinyint(1) NOT NULL DEFAULT '0',
  `transfers_date` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_permissions` VALUES ('1', '5', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', NULL, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, '1', '1', '1', NULL, '1', '1', '1', '1', '1', '1', '1', '1', NULL, '1', '1', '1', NULL, NULL, '1', '1', '1', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, '0', '0', '1', '1', '1', '1', '1', '0', '0', '1', '1', '0', NULL, NULL, '1', '1', '1', '1', '1', '1', '1', NULL, NULL, '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0');

-- Table: sma_pos_register
DROP TABLE IF EXISTS `sma_pos_register`;
CREATE TABLE `sma_pos_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `cash_in_hand` decimal(25,4) NOT NULL,
  `status` varchar(10) NOT NULL,
  `total_cash` decimal(25,4) DEFAULT NULL,
  `total_cheques` int(11) DEFAULT NULL,
  `total_cc_slips` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `total_cash_submitted` decimal(25,4) DEFAULT NULL,
  `total_cheques_submitted` int(11) DEFAULT NULL,
  `total_cc_slips_submitted` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `note` text,
  `closed_at` timestamp NULL DEFAULT NULL,
  `transfer_opened_bills` varchar(50) DEFAULT NULL,
  `closed_by` int(11) DEFAULT NULL,
  `Offline_id` int(11) DEFAULT NULL,
  `total_cc` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `invoice_count` int(24) NOT NULL DEFAULT '0',
  `cash_in_hand_clearance` tinyint(1) NOT NULL DEFAULT '1',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `donation` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `RealCash` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `Deficitandexcess` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_pos_settings
DROP TABLE IF EXISTS `sma_pos_settings`;
CREATE TABLE `sma_pos_settings` (
  `pos_id` int(1) NOT NULL,
  `cat_limit` int(11) NOT NULL,
  `pro_limit` int(11) NOT NULL,
  `default_category` int(11) NOT NULL,
  `default_customer` int(11) NOT NULL,
  `default_biller` int(11) NOT NULL,
  `display_time` varchar(3) NOT NULL DEFAULT 'yes',
  `cf_title1` varchar(255) DEFAULT NULL,
  `cf_title2` varchar(255) DEFAULT NULL,
  `cf_value1` varchar(255) DEFAULT NULL,
  `cf_value2` varchar(255) DEFAULT NULL,
  `receipt_printer` varchar(55) DEFAULT NULL,
  `cash_drawer_codes` varchar(55) DEFAULT NULL,
  `focus_add_item` varchar(55) DEFAULT NULL,
  `add_manual_product` varchar(55) DEFAULT NULL,
  `customer_selection` varchar(55) DEFAULT NULL,
  `add_customer` varchar(55) DEFAULT NULL,
  `toggle_category_slider` varchar(55) DEFAULT NULL,
  `toggle_subcategory_slider` varchar(55) DEFAULT NULL,
  `cancel_sale` varchar(55) DEFAULT NULL,
  `suspend_sale` varchar(55) DEFAULT NULL,
  `print_items_list` varchar(55) DEFAULT NULL,
  `finalize_sale` varchar(55) DEFAULT NULL,
  `today_sale` varchar(55) DEFAULT NULL,
  `open_hold_bills` varchar(55) DEFAULT NULL,
  `close_register` varchar(55) DEFAULT NULL,
  `keyboard` tinyint(1) NOT NULL,
  `pos_printers` varchar(255) DEFAULT NULL,
  `java_applet` tinyint(1) NOT NULL,
  `product_button_color` varchar(20) NOT NULL DEFAULT 'default',
  `tooltips` tinyint(1) DEFAULT '1',
  `paypal_pro` tinyint(1) DEFAULT '0',
  `stripe` tinyint(1) DEFAULT '0',
  `rounding` tinyint(1) DEFAULT '0',
  `char_per_line` tinyint(4) DEFAULT '42',
  `pin_code` varchar(20) DEFAULT NULL,
  `report_pin_code` varchar(20) DEFAULT NULL,
  `purchase_code` varchar(100) DEFAULT 'purchase_code',
  `envato_username` varchar(50) DEFAULT 'envato_username',
  `version` varchar(10) DEFAULT '3.4.25',
  `after_sale_page` tinyint(1) DEFAULT '0',
  `item_order` tinyint(1) DEFAULT '0',
  `authorize` tinyint(1) DEFAULT '0',
  `toggle_brands_slider` varchar(55) DEFAULT NULL,
  `remote_printing` tinyint(1) DEFAULT '1',
  `printer` int(11) DEFAULT NULL,
  `order_printers` varchar(55) DEFAULT NULL,
  `auto_print` tinyint(1) DEFAULT '0',
  `customer_details` tinyint(1) DEFAULT NULL,
  `local_printers` tinyint(1) DEFAULT NULL,
  `tax_method_type` tinyint(1) NOT NULL DEFAULT '2',
  `order_type` tinyint(1) NOT NULL DEFAULT '0',
  `categories_tab` tinyint(1) NOT NULL DEFAULT '0',
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `pos_reciept_a4_page_size` tinyint(1) DEFAULT '0',
  `pos_screen_style` tinyint(4) DEFAULT '1',
  `slip_pin_code` varchar(20) DEFAULT NULL,
  `screen_style` tinyint(1) NOT NULL DEFAULT '0',
  `RestInsurance` tinyint(1) NOT NULL DEFAULT '0',
  `AnyUserCanDeleteFromBasket` tinyint(1) NOT NULL DEFAULT '1',
  `DeleteFromBaskerPinCode` varchar(20) DEFAULT NULL,
  `AnyUserCanPlusMinusFromBasket` tinyint(1) NOT NULL DEFAULT '1',
  `ShowShippingInSales` tinyint(1) NOT NULL DEFAULT '1',
  `ShowInsuranceInSales` tinyint(1) NOT NULL DEFAULT '1',
  `searchbyitemname` tinyint(1) NOT NULL DEFAULT '1',
  `Opensetting` tinyint(1) NOT NULL DEFAULT '1',
  `Opentodaysales` tinyint(1) NOT NULL DEFAULT '1',
  `OpenExpensiveView` tinyint(1) NOT NULL DEFAULT '1',
  `OpentQuotesView` tinyint(1) NOT NULL DEFAULT '1',
  `OpentCouponsInSales` tinyint(1) NOT NULL DEFAULT '1',
  `show_product_code_on_invoice` tinyint(1) NOT NULL DEFAULT '1',
  `send_receipt_to_warehouse` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse_receipt_print_copy_count` tinyint(3) NOT NULL DEFAULT '1',
  `expenses_pin_code` varchar(20) DEFAULT NULL,
  `collections_pin_code` varchar(20) DEFAULT NULL,
  `add_product_pin_code` varchar(20) DEFAULT NULL,
  `customers_pin_code` varchar(20) DEFAULT NULL,
  `nfc_enable` tinyint(1) NOT NULL DEFAULT '0',
  `product_name_display` varchar(20) NOT NULL DEFAULT 'not_selected',
  PRIMARY KEY (`pos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sma_pos_settings` VALUES ('1', '22', '24', '1', '2', '1', '1', '', '', '', '', NULL, 'x1C', 'Ctrl+F3', 'Ctrl+Shift+M', 'Ctrl+Shift+C', 'Ctrl+Shift+A', 'Ctrl+F11', 'Ctrl+F12', 'F4', 'F7', 'F9', 'ctrl+shift+Z', 'Ctrl+F1', 'Ctrl+F2', 'Ctrl+F10', '0', NULL, '0', 'default', '0', '0', '0', '0', '42', '1111', NULL, 'purchase_code', 'envato_username', '3.4.25', '0', '0', '0', '', '1', NULL, 'null', '0', '0', '1', '2', '0', '0', '0', '0', '1', NULL, '0', '0', '1', NULL, '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', NULL, NULL, NULL, NULL, '0', 'not_selected');

-- Table: sma_price_groups
DROP TABLE IF EXISTS `sma_price_groups`;
CREATE TABLE `sma_price_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_price_groups` VALUES ('1', 'Default', '0', '1', '1', '0');

-- Table: sma_print_barcode_settings
DROP TABLE IF EXISTS `sma_print_barcode_settings`;
CREATE TABLE `sma_print_barcode_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style` tinyint(4) NOT NULL DEFAULT '50',
  `width` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `orientation` tinyint(1) NOT NULL DEFAULT '0',
  `brand` tinyint(1) NOT NULL DEFAULT '0',
  `site_name` tinyint(1) NOT NULL DEFAULT '1',
  `product_name` tinyint(1) NOT NULL DEFAULT '1',
  `production_date` tinyint(1) NOT NULL DEFAULT '0',
  `expiry` tinyint(1) NOT NULL DEFAULT '0',
  `price` tinyint(1) NOT NULL DEFAULT '1',
  `currencies` tinyint(1) NOT NULL DEFAULT '0',
  `unit` tinyint(1) NOT NULL DEFAULT '1',
  `category` tinyint(1) NOT NULL DEFAULT '0',
  `variants` tinyint(1) NOT NULL DEFAULT '0',
  `product_image` tinyint(1) NOT NULL DEFAULT '0',
  `check_promo` tinyint(1) NOT NULL DEFAULT '1',
  `with_tax` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_print_barcode_settings` VALUES ('1', '50', '1.8', '1', '0', '0', '1', '1', '0', '0', '1', '0', '1', '0', '0', '0', '1', '0');

-- Table: sma_printers
DROP TABLE IF EXISTS `sma_printers`;
CREATE TABLE `sma_printers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(55) NOT NULL,
  `type` varchar(25) NOT NULL,
  `profile` varchar(25) NOT NULL,
  `char_per_line` tinyint(3) unsigned DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `ip_address` varbinary(45) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_product_photos
DROP TABLE IF EXISTS `sma_product_photos`;
CREATE TABLE `sma_product_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `photo` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_product_prices
DROP TABLE IF EXISTS `sma_product_prices`;
CREATE TABLE `sma_product_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `price_group_id` int(11) NOT NULL,
  `price` decimal(25,4) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id_2` (`product_id`,`product_unit_id`,`price_group_id`) USING BTREE,
  KEY `product_id` (`product_id`),
  KEY `price_group_id` (`price_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_product_variants
DROP TABLE IF EXISTS `sma_product_variants`;
CREATE TABLE `sma_product_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `cost` decimal(25,4) DEFAULT NULL,
  `price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_id_name` (`product_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_products
DROP TABLE IF EXISTS `sma_products`;
CREATE TABLE `sma_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `unit` int(11) DEFAULT NULL,
  `cost` decimal(25,4) DEFAULT NULL,
  `price` decimal(25,4) NOT NULL,
  `alert_quantity` decimal(15,4) DEFAULT '20.0000',
  `image` varchar(255) DEFAULT 'no_image.png',
  `category_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `cf1` varchar(255) DEFAULT NULL,
  `cf2` varchar(255) DEFAULT NULL,
  `cf3` varchar(255) DEFAULT NULL,
  `cf4` varchar(255) DEFAULT NULL,
  `cf5` varchar(255) DEFAULT NULL,
  `cf6` varchar(255) DEFAULT NULL,
  `quantity` decimal(15,4) DEFAULT '0.0000',
  `tax_rate` int(11) DEFAULT NULL,
  `track_quantity` tinyint(1) DEFAULT '1',
  `details` varchar(1000) DEFAULT NULL,
  `warehouse` int(11) DEFAULT NULL,
  `barcode_symbology` varchar(55) NOT NULL DEFAULT 'code128',
  `file` varchar(100) DEFAULT NULL,
  `product_details` text,
  `tax_method` tinyint(1) DEFAULT '0',
  `type` varchar(55) NOT NULL DEFAULT 'standard',
  `type2` varchar(255) NOT NULL,
  `supplier1` int(11) DEFAULT NULL,
  `supplier1price` decimal(25,4) DEFAULT NULL,
  `supplier2` int(11) DEFAULT NULL,
  `supplier2price` decimal(25,4) DEFAULT NULL,
  `supplier3` int(11) DEFAULT NULL,
  `supplier3price` decimal(25,4) DEFAULT NULL,
  `supplier4` int(11) DEFAULT NULL,
  `supplier4price` decimal(25,4) DEFAULT NULL,
  `supplier5` int(11) DEFAULT NULL,
  `supplier5price` decimal(25,4) DEFAULT NULL,
  `promotion` tinyint(1) DEFAULT '0',
  `promo_price` decimal(25,4) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `supplier1_part_no` varchar(50) DEFAULT NULL,
  `supplier2_part_no` varchar(50) DEFAULT NULL,
  `supplier3_part_no` varchar(50) DEFAULT NULL,
  `supplier4_part_no` varchar(50) DEFAULT NULL,
  `supplier5_part_no` varchar(50) DEFAULT NULL,
  `sale_unit` int(11) DEFAULT NULL,
  `purchase_unit` int(11) DEFAULT NULL,
  `brand` int(11) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT NULL,
  `weight` decimal(10,4) DEFAULT NULL,
  `hsn_code` int(11) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `second_name` varchar(255) DEFAULT NULL,
  `hide_pos` tinyint(1) NOT NULL DEFAULT '0',
  `product_number` varchar(255) DEFAULT NULL,
  `expiry` date DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `minimum` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `po_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `item_no` varchar(50) DEFAULT NULL,
  `minimum_selling_price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `category_id` (`category_id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `category_id_2` (`category_id`),
  KEY `unit` (`unit`),
  KEY `brand` (`brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_promo
DROP TABLE IF EXISTS `sma_promo`;
CREATE TABLE `sma_promo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_type_id` int(11) NOT NULL DEFAULT '0',
  `promo_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `offer_category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `offer_product` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `free_product` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gift_category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gift_product` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `percentage_off` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sel_option` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cashback` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cart_min_value` float NOT NULL DEFAULT '0',
  `daily_start_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `daily_end_time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `buy_quantity` int(11) NOT NULL DEFAULT '0',
  `get_quantity` int(11) NOT NULL DEFAULT '0',
  `promo_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(5) NOT NULL DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `revised_date` datetime NOT NULL,
  `offer_product_details` tinyint(1) NOT NULL DEFAULT '0',
  `hide_opt` tinyint(1) NOT NULL DEFAULT '0',
  `promo_id` int(11) DEFAULT NULL,
  `fixed_off` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `promo_sr` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_promo_details
DROP TABLE IF EXISTS `sma_promo_details`;
CREATE TABLE `sma_promo_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) NOT NULL,
  `offer_product` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `promo_type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_promo_type
DROP TABLE IF EXISTS `sma_promo_type`;
CREATE TABLE `sma_promo_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(5) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `revised_date` datetime NOT NULL,
  `promo_type_ar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_promo_type` VALUES ('1', 'According Cart Value', '0', '0', '2021-05-21 06:42:01', '2021-08-24 22:13:23', '');
INSERT INTO `sma_promo_type` VALUES ('2', 'Specified Category', '0', '0', '2021-05-21 06:42:45', '2021-05-21 06:42:45', '');
INSERT INTO `sma_promo_type` VALUES ('3', 'Buy Product Get Product free', '1', '1', '2021-05-21 06:43:51', '2021-05-21 06:43:51', 'اشتر منتج واحصل على منتج مجانا');
INSERT INTO `sma_promo_type` VALUES ('4', 'Percentage Discount', '1', '1', '2021-05-21 06:44:16', '2021-05-21 06:44:16', 'الخصم بالنسبة');
INSERT INTO `sma_promo_type` VALUES ('5', 'Occasional Offer', '0', '0', '2021-05-21 06:45:15', '2021-08-24 22:08:21', '');
INSERT INTO `sma_promo_type` VALUES ('6', 'Time Slot Offer', '0', '0', '2021-05-21 06:45:52', '2021-06-10 02:52:10', '');
INSERT INTO `sma_promo_type` VALUES ('7', 'Buy some Get Product free', '1', '1', '2021-05-21 06:43:51', '2023-05-15 19:14:27', 'اشتر منتجات واحصل على منتج مجانا');
INSERT INTO `sma_promo_type` VALUES ('8', 'Percentage Discount for some Products', '1', '1', '2021-05-21 06:44:16', '2021-05-21 06:44:16', 'الخصم بالنسبة لأكثر من منتج');
INSERT INTO `sma_promo_type` VALUES ('9', 'Buy products and get the cheapest for free', '1', '1', '2024-01-22 02:44:16', '2024-01-22 02:44:16', 'اشتر منتجات واحصل على الأرخص مجانا');
INSERT INTO `sma_promo_type` VALUES ('10', 'Fixed Discount', '1', '1', '2021-05-21 06:44:16', '2021-05-21 06:44:16', 'خصم ثابت');
INSERT INTO `sma_promo_type` VALUES ('11', 'Fixed Discount for some Products', '1', '1', '2021-05-21 06:44:16', '2021-05-21 06:44:16', 'خصم ثابت لأكثر من منتج');

-- Table: sma_promos
DROP TABLE IF EXISTS `sma_promos`;
CREATE TABLE `sma_promos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `product2buy` int(11) NOT NULL,
  `product2get` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_purchase_items
DROP TABLE IF EXISTS `sma_purchase_items`;
CREATE TABLE `sma_purchase_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) DEFAULT NULL,
  `transfer_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_cost` decimal(25,12) NOT NULL,
  `quantity` decimal(25,12) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `item_tax` decimal(25,12) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(20) DEFAULT NULL,
  `discount` varchar(20) DEFAULT NULL,
  `item_discount` decimal(25,12) DEFAULT NULL,
  `expiry` date DEFAULT NULL,
  `subtotal` decimal(25,12) NOT NULL,
  `quantity_balance` decimal(25,12) DEFAULT '0.000000000000',
  `date` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `unit_cost` decimal(25,12) DEFAULT NULL,
  `real_unit_cost` decimal(25,12) DEFAULT NULL,
  `quantity_received` decimal(25,12) DEFAULT NULL,
  `supplier_part_no` varchar(50) DEFAULT NULL,
  `purchase_item_id` int(11) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(25,12) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,12) DEFAULT NULL,
  `sgst` decimal(25,12) DEFAULT NULL,
  `igst` decimal(25,12) DEFAULT NULL,
  `return_shipping` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `shipping` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `item_cost_value` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `receiving_date` datetime DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_purchases
DROP TABLE IF EXISTS `sma_purchases`;
CREATE TABLE `sma_purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `supplier_id` int(11) NOT NULL,
  `supplier` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` varchar(1000) NOT NULL,
  `total` decimal(25,12) DEFAULT NULL,
  `product_discount` decimal(25,12) DEFAULT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `order_discount` decimal(25,12) DEFAULT NULL,
  `total_discount` decimal(25,12) DEFAULT NULL,
  `product_tax` decimal(25,12) DEFAULT NULL,
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,12) DEFAULT NULL,
  `total_tax` decimal(25,12) DEFAULT '0.000000000000',
  `shipping` decimal(25,12) DEFAULT '0.000000000000',
  `shipping_received` decimal(25,12) NOT NULL,
  `grand_total` decimal(25,12) NOT NULL,
  `paid` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `status` varchar(55) DEFAULT '',
  `receiving_status` varchar(55) DEFAULT 'received',
  `payment_status` varchar(20) DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `return_purchase_ref` varchar(55) DEFAULT NULL,
  `return_purchase_ex_ref` varchar(55) DEFAULT NULL,
  `return_surcharge_ex_ref` varchar(55) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `return_purchase_total` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `cgst` decimal(25,12) DEFAULT NULL,
  `sgst` decimal(25,12) DEFAULT NULL,
  `igst` decimal(25,12) DEFAULT NULL,
  `customs_fees` float NOT NULL,
  `other_fees` float NOT NULL,
  `tax_active_rate` decimal(12,2) NOT NULL,
  `return_ex_id` int(11) DEFAULT NULL,
  `return_ex_s_id` int(11) DEFAULT NULL,
  `supply_date` datetime DEFAULT NULL,
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `discount_earned` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `uuid` varchar(250) DEFAULT NULL,
  `costCenterId` text,
  `costCenterAmount` text,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `offline_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_quote_items
DROP TABLE IF EXISTS `sma_quote_items`;
CREATE TABLE `sma_quote_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_quotes
DROP TABLE IF EXISTS `sma_quotes`;
CREATE TABLE `sma_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `note` text,
  `internal_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT '0.0000',
  `order_discount` decimal(25,4) DEFAULT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT '0.0000',
  `product_tax` decimal(25,4) DEFAULT '0.0000',
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT NULL,
  `shipping` decimal(25,4) DEFAULT '0.0000',
  `grand_total` decimal(25,4) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `supplier` varchar(55) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `plate_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `exit_date` datetime DEFAULT NULL,
  `uuid` varchar(55) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_resturant_emp
DROP TABLE IF EXISTS `sma_resturant_emp`;
CREATE TABLE `sma_resturant_emp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dept` int(11) NOT NULL DEFAULT '0',
  `emp_type` int(11) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdby` int(11) DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deactiveby` int(11) DEFAULT NULL,
  `decative_date` timestamp NULL DEFAULT NULL,
  `decative_reason` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_resturant_emp_types
DROP TABLE IF EXISTS `sma_resturant_emp_types`;
CREATE TABLE `sma_resturant_emp_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sma_resturant_emp_types` VALUES ('1', 'مندوب', 'المندوب');
INSERT INTO `sma_resturant_emp_types` VALUES ('2', 'مباشر', 'المباشر');
INSERT INTO `sma_resturant_emp_types` VALUES ('3', 'سائق', 'السائق');

-- Table: sma_resturant_tables
DROP TABLE IF EXISTS `sma_resturant_tables`;
CREATE TABLE `sma_resturant_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dept` int(11) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `table_icon` varchar(255) DEFAULT 'no_image.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_return_items
DROP TABLE IF EXISTS `sma_return_items`;
CREATE TABLE `sma_return_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_id` (`return_id`),
  KEY `product_id` (`product_id`),
  KEY `product_id_2` (`product_id`,`return_id`),
  KEY `return_id_2` (`return_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_returns
DROP TABLE IF EXISTS `sma_returns`;
CREATE TABLE `sma_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `staff_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT '0.0000',
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT '0.0000',
  `order_discount` decimal(25,4) DEFAULT '0.0000',
  `product_tax` decimal(25,4) DEFAULT '0.0000',
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT '0.0000',
  `total_tax` decimal(25,4) DEFAULT '0.0000',
  `grand_total` decimal(25,4) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `paid` decimal(25,4) DEFAULT '0.0000',
  `surcharge` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `attachment` varchar(55) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `shipping` decimal(25,4) DEFAULT '0.0000',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_revaluation_items
DROP TABLE IF EXISTS `sma_revaluation_items`;
CREATE TABLE `sma_revaluation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `revaluation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `old_cost` decimal(25,12) NOT NULL,
  `cost` decimal(25,12) NOT NULL,
  `unit` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `revaluation_id` (`revaluation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_revaluations
DROP TABLE IF EXISTS `sma_revaluations`;
CREATE TABLE `sma_revaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `note` text,
  `attachment` varchar(55) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `count_id` int(11) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_revenue
DROP TABLE IF EXISTS `sma_revenue`;
CREATE TABLE `sma_revenue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference` varchar(50) NOT NULL,
  `amount` decimal(25,4) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  `attachment` varchar(55) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `card_id` int(11) DEFAULT NULL,
  `paid_by` varchar(20) DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_no` varchar(20) DEFAULT NULL,
  `cc_holder` varchar(25) DEFAULT NULL,
  `cc_month` varchar(2) DEFAULT NULL,
  `cc_year` varchar(4) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `gl_payment_method_id` int(11) NOT NULL DEFAULT '0',
  `currency` varchar(3) DEFAULT NULL,
  `currency_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_sale_item_modifiers
DROP TABLE IF EXISTS `sma_sale_item_modifiers`;
CREATE TABLE `sma_sale_item_modifiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_item_id` int(11) NOT NULL,
  `modifier_id` int(11) NOT NULL,
  `price` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `quantity` decimal(12,4) NOT NULL DEFAULT '1.0000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_sale_items
DROP TABLE IF EXISTS `sma_sale_items`;
CREATE TABLE `sma_sale_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `sale_item_id` int(11) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `item_cost_value` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `avg_cost` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `overselling` tinyint(1) NOT NULL DEFAULT '0',
  `quantity_overselling` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `promo_free` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `length` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `width` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `area` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `isTobacco` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  KEY `product_id_2` (`product_id`,`sale_id`),
  KEY `sale_id_2` (`sale_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_sales
DROP TABLE IF EXISTS `sma_sales`;
CREATE TABLE `sma_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `biller` varchar(55) NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `note` varchar(3000) DEFAULT NULL,
  `staff_note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `product_discount` decimal(25,4) DEFAULT '0.0000',
  `order_discount_id` varchar(20) DEFAULT NULL,
  `total_discount` decimal(25,4) DEFAULT '0.0000',
  `order_discount` decimal(25,4) DEFAULT '0.0000',
  `product_tax` decimal(25,4) DEFAULT '0.0000',
  `order_tax_id` int(11) DEFAULT NULL,
  `order_tax` decimal(25,4) DEFAULT '0.0000',
  `total_tax` decimal(25,4) DEFAULT '0.0000',
  `shipping` decimal(25,4) DEFAULT '0.0000',
  `grand_total` decimal(25,4) NOT NULL,
  `sale_status` varchar(20) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_term` tinyint(4) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_items` smallint(6) DEFAULT NULL,
  `pos` tinyint(1) NOT NULL DEFAULT '0',
  `paid` decimal(25,4) DEFAULT '0.0000',
  `return_id` int(11) DEFAULT NULL,
  `surcharge` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `attachment` varchar(55) DEFAULT NULL,
  `return_sale_ref` varchar(55) DEFAULT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `return_sale_total` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `rounding` decimal(10,4) DEFAULT NULL,
  `suspend_note` varchar(255) DEFAULT NULL,
  `api` tinyint(1) DEFAULT '0',
  `shop` tinyint(1) DEFAULT '0',
  `address_id` int(11) DEFAULT NULL,
  `reserve_id` int(11) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `manual_payment` varchar(55) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `payment_method` varchar(55) DEFAULT NULL,
  `order_type` tinyint(1) NOT NULL DEFAULT '1',
  `Offline_Pos_id` int(11) DEFAULT NULL,
  `supply_date` datetime DEFAULT NULL,
  `dept` int(11) NOT NULL DEFAULT '0',
  `table_no` int(11) NOT NULL DEFAULT '0',
  `emp_type` int(11) NOT NULL DEFAULT '0',
  `emp_id` int(11) NOT NULL DEFAULT '0',
  `plate_no` varchar(50) DEFAULT NULL,
  `exit_date` datetime DEFAULT NULL,
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `discount_allowance` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `uuid` varchar(250) DEFAULT NULL,
  `po_no` varchar(55) DEFAULT NULL,
  `costCenterId` text,
  `costCenterAmount` text,
  `declaration_no` varchar(55) DEFAULT NULL,
  `bayan_no` varchar(55) DEFAULT NULL,
  `bill_lading_no` varchar(55) DEFAULT NULL,
  `final_bayan_date` datetime DEFAULT NULL,
  `app_name` varchar(55) DEFAULT NULL,
  `app_version` varchar(55) DEFAULT NULL,
  `insurance_business` decimal(10,2) NOT NULL DEFAULT '0.00',
  `advance_payment_discount` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  `salesman_id` int(11) DEFAULT NULL,
  `commission_type` tinyint(1) DEFAULT NULL,
  `commission` decimal(12,4) DEFAULT NULL,
  `commission_amount` decimal(12,4) DEFAULT '0.0000',
  `sale_item_cost_value` decimal(25,12) DEFAULT '0.000000000000',
  `delivery_note_number` varchar(55) DEFAULT NULL,
  `isTobacco` tinyint(1) NOT NULL DEFAULT '0',
  `inv_type` tinyint(1) NOT NULL DEFAULT '1',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `rent_start_date` datetime DEFAULT NULL,
  `rent_end_date` datetime DEFAULT NULL,
  `donation` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `coupon_code` varchar(20) DEFAULT NULL,
  `debit_id` int(11) DEFAULT NULL,
  `return_debit_id` int(11) DEFAULT NULL,
  `return_debit_ref` varchar(55) DEFAULT NULL,
  `inv_src` tinyint(1) NOT NULL DEFAULT '0',
  `src_reference_no` varchar(55) DEFAULT NULL,
  `is_sub` tinyint(1) NOT NULL DEFAULT '0',
  `retention` decimal(10,2) NOT NULL DEFAULT '0.00',
  `inv_period` varchar(55) DEFAULT NULL,
  `insurance_percentage` tinyint(1) NOT NULL DEFAULT '1',
  `credit_id` int(11) DEFAULT NULL,
  `return_credit_id` int(11) DEFAULT NULL,
  `return_credit_ref` varchar(55) DEFAULT NULL,
  `rig_no` varchar(55) DEFAULT NULL,
  `rig_distance` varchar(20) DEFAULT NULL,
  `rig_start_date` datetime DEFAULT NULL,
  `rig_end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_sales_termscondi
DROP TABLE IF EXISTS `sma_sales_termscondi`;
CREATE TABLE `sma_sales_termscondi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_salesman_customers
DROP TABLE IF EXISTS `sma_salesman_customers`;
CREATE TABLE `sma_salesman_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salesman_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `salesman_id` (`salesman_id`,`customer_id`),
  KEY `salesman_customers_salesman_id_FK` (`salesman_id`),
  KEY `salesman_customers_customer_id_FK` (`customer_id`),
  CONSTRAINT `salesman_customers_customer_id_FK` FOREIGN KEY (`customer_id`) REFERENCES `sma_companies` (`id`),
  CONSTRAINT `salesman_customers_salesman_id_FK` FOREIGN KEY (`salesman_id`) REFERENCES `sma_companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_sessions
DROP TABLE IF EXISTS `sma_sessions`;
CREATE TABLE `sma_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_settings
DROP TABLE IF EXISTS `sma_settings`;
CREATE TABLE `sma_settings` (
  `setting_id` int(1) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `logo2` varchar(255) NOT NULL,
  `site_name` varchar(55) NOT NULL,
  `site_ename` varchar(55) NOT NULL,
  `language` varchar(20) NOT NULL,
  `default_warehouse` int(2) NOT NULL,
  `accounting_method` tinyint(4) NOT NULL DEFAULT '0',
  `default_currency` varchar(3) NOT NULL,
  `default_tax_rate` int(2) NOT NULL,
  `rows_per_page` int(2) NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '1.0',
  `default_tax_rate2` int(11) NOT NULL DEFAULT '0',
  `dateformat` int(11) NOT NULL,
  `sales_prefix` varchar(20) DEFAULT NULL,
  `quote_prefix` varchar(20) DEFAULT NULL,
  `purchase_prefix` varchar(20) DEFAULT NULL,
  `transfer_prefix` varchar(20) DEFAULT NULL,
  `delivery_prefix` varchar(20) DEFAULT NULL,
  `payment_prefix` varchar(20) DEFAULT NULL,
  `return_prefix` varchar(20) DEFAULT NULL,
  `returnp_prefix` varchar(20) DEFAULT NULL,
  `expense_prefix` varchar(20) DEFAULT NULL,
  `item_addition` tinyint(1) NOT NULL DEFAULT '0',
  `theme` varchar(20) NOT NULL,
  `product_serial` tinyint(4) NOT NULL,
  `default_discount` int(11) NOT NULL,
  `product_discount` tinyint(1) NOT NULL DEFAULT '0',
  `discount_method` tinyint(4) NOT NULL,
  `tax1` tinyint(4) NOT NULL,
  `tax2` tinyint(4) NOT NULL,
  `overselling` tinyint(1) NOT NULL DEFAULT '0',
  `restrict_user` tinyint(4) NOT NULL DEFAULT '0',
  `restrict_calendar` tinyint(4) NOT NULL DEFAULT '0',
  `timezone` varchar(100) DEFAULT NULL,
  `iwidth` int(11) NOT NULL DEFAULT '0',
  `iheight` int(11) NOT NULL,
  `twidth` int(11) NOT NULL,
  `theight` int(11) NOT NULL,
  `watermark` tinyint(1) DEFAULT NULL,
  `reg_ver` tinyint(1) DEFAULT NULL,
  `allow_reg` tinyint(1) DEFAULT NULL,
  `reg_notification` tinyint(1) DEFAULT NULL,
  `auto_reg` tinyint(1) DEFAULT NULL,
  `protocol` varchar(20) NOT NULL DEFAULT 'mail',
  `mailpath` varchar(55) DEFAULT '/usr/sbin/sendmail',
  `smtp_host` varchar(100) DEFAULT NULL,
  `smtp_user` varchar(100) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(10) DEFAULT '25',
  `smtp_crypto` varchar(10) DEFAULT NULL,
  `corn` datetime DEFAULT NULL,
  `customer_group` int(11) NOT NULL,
  `default_email` varchar(100) NOT NULL,
  `mmode` tinyint(1) NOT NULL,
  `bc_fix` tinyint(4) NOT NULL DEFAULT '0',
  `auto_detect_barcode` tinyint(1) NOT NULL DEFAULT '0',
  `captcha` tinyint(1) NOT NULL DEFAULT '1',
  `reference_format` tinyint(1) NOT NULL DEFAULT '1',
  `racks` tinyint(1) DEFAULT '0',
  `attributes` tinyint(1) NOT NULL DEFAULT '0',
  `product_expiry` tinyint(1) NOT NULL DEFAULT '0',
  `decimals` tinyint(2) NOT NULL DEFAULT '2',
  `qty_decimals` tinyint(2) NOT NULL DEFAULT '2',
  `decimals_sep` varchar(2) NOT NULL DEFAULT '.',
  `thousands_sep` varchar(2) NOT NULL DEFAULT ',',
  `invoice_view` tinyint(1) DEFAULT '0',
  `default_biller` int(11) DEFAULT NULL,
  `envato_username` varchar(50) DEFAULT NULL,
  `purchase_code` varchar(100) DEFAULT NULL,
  `rtl` tinyint(1) DEFAULT '0',
  `each_spent` decimal(15,4) DEFAULT NULL,
  `ca_point` tinyint(4) DEFAULT NULL,
  `each_sale` decimal(15,4) DEFAULT NULL,
  `sa_point` tinyint(4) DEFAULT NULL,
  `update` tinyint(1) DEFAULT '0',
  `sac` tinyint(1) DEFAULT '0',
  `display_all_products` tinyint(1) DEFAULT '0',
  `display_symbol` tinyint(1) DEFAULT NULL,
  `symbol` varchar(50) DEFAULT NULL,
  `remove_expired` tinyint(1) DEFAULT '0',
  `barcode_separator` varchar(2) NOT NULL DEFAULT '-',
  `set_focus` tinyint(1) NOT NULL DEFAULT '0',
  `price_group` int(11) DEFAULT NULL,
  `barcode_img` tinyint(1) NOT NULL DEFAULT '1',
  `ppayment_prefix` varchar(20) DEFAULT 'POP',
  `disable_editing` smallint(6) DEFAULT '90',
  `qa_prefix` varchar(55) DEFAULT NULL,
  `update_cost` tinyint(1) DEFAULT NULL,
  `apis` tinyint(1) NOT NULL DEFAULT '0',
  `state` varchar(100) DEFAULT NULL,
  `pdf_lib` varchar(20) DEFAULT 'dompdf',
  `pos` tinyint(1) NOT NULL DEFAULT '0',
  `accounting` tinyint(1) NOT NULL DEFAULT '0',
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `IsResturant` tinyint(1) NOT NULL DEFAULT '0',
  `IsServices` tinyint(1) NOT NULL DEFAULT '0',
  `IsLundry` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `IsConstruction` tinyint(1) NOT NULL DEFAULT '0',
  `accountingOffline` tinyint(1) NOT NULL DEFAULT '0',
  `invoices_sequence` tinyint(1) NOT NULL DEFAULT '0',
  `sales_invoice_prefix` varchar(20) DEFAULT NULL,
  `default_tax_method` tinyint(1) NOT NULL DEFAULT '0',
  `show_product_code` tinyint(1) NOT NULL DEFAULT '1',
  `accountingOnly` tinyint(1) NOT NULL DEFAULT '0',
  `invoiceNoRef` tinyint(1) NOT NULL DEFAULT '1',
  `isStation` tinyint(1) NOT NULL DEFAULT '0',
  `isOpen_invoice` tinyint(1) NOT NULL DEFAULT '0',
  `syncEveryMinues` int(10) NOT NULL DEFAULT '30',
  `isLogistic` tinyint(1) NOT NULL DEFAULT '0',
  `IsInsuranceBusiness` tinyint(1) NOT NULL DEFAULT '0',
  `isAutoAccountOfflineCustomers` tinyint(1) NOT NULL DEFAULT '0',
  `IsAutomotiveServices` tinyint(1) NOT NULL DEFAULT '0',
  `company_seal` varchar(255) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `IBAN` varchar(55) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `isShipping` tinyint(1) NOT NULL DEFAULT '0',
  `isMinimum` tinyint(1) NOT NULL DEFAULT '0',
  `isTobacco` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `revaluation_prefix` varchar(20) DEFAULT NULL,
  `IsRental` tinyint(1) NOT NULL DEFAULT '0',
  `isDonation` tinyint(1) NOT NULL DEFAULT '0',
  `isDeliveryFromSales` tinyint(1) NOT NULL DEFAULT '1',
  `IsCurtains` tinyint(1) NOT NULL DEFAULT '0',
  `tobacco_tax` tinyint(3) NOT NULL DEFAULT '100',
  `MenuNotificationsactive` tinyint(1) NOT NULL DEFAULT '0',
  `MenuNotificationsRepeatTime` tinyint(4) NOT NULL DEFAULT '1',
  `IsOneRegisterByBiller` tinyint(1) NOT NULL DEFAULT '0',
  `isDisplayCustomersAccountOnInvoice` tinyint(1) NOT NULL DEFAULT '0',
  `isDisplayWarehouseOnInvoice` tinyint(1) NOT NULL DEFAULT '0',
  `bg_invoice_image` varchar(100) DEFAULT NULL,
  `isDisplayItemsSold` tinyint(1) NOT NULL DEFAULT '1',
  `terms_renderer` tinyint(1) NOT NULL DEFAULT '0',
  `terms_attachment` varchar(55) DEFAULT NULL,
  `reverse_each_spent` decimal(15,4) DEFAULT NULL,
  `reverse_ca_point` tinyint(4) DEFAULT NULL,
  `reverse_each_sale` decimal(15,4) DEFAULT NULL,
  `reverse_sa_point` tinyint(4) DEFAULT NULL,
  `isSubscriptions` tinyint(1) NOT NULL DEFAULT '0',
  `update_price` tinyint(1) NOT NULL DEFAULT '0',
  `swift_code` varchar(55) DEFAULT NULL,
  `insuranceType` tinyint(1) NOT NULL DEFAULT '1',
  `IsEquipmentRental` tinyint(1) NOT NULL DEFAULT '0',
  `remoteJid` varchar(55) DEFAULT NULL,
  `spark_ui` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sma_settings` VALUES ('1', 'empty.png', 'logo3.png', 'Default', '', 'arabic', '1', '2', 'SR', '1', '10', '3.4.25', '1', '5', 'SALE', 'QUOTE', 'PO', 'TR', 'DO', 'IPAY', 'SR', 'PR', '', '1', 'default', '1', '1', '1', '1', '1', '1', '1', '1', '0', 'Asia/Riyadh', '800', '800', '250', '150', '0', '0', '0', '0', NULL, 'mail', '/usr/sbin/sendmail', 'pop.gmail.com', 'contact@sma.domain1.com', 'Aa123456', '25', NULL, NULL, '1', 'companypro@domain1.com', '0', '4', '1', '0', '2', '0', '1', '1', '2', '2', '.', ',', '0', '1', 'arkanpro', '34aa60eb-f0ec-42cb-aeda-a5b987f2c8d1', '1', NULL, NULL, NULL, NULL, '0', '0', '0', '0', 'SR', '0', '-', '0', '1', '1', 'POP', '90', '', '1', '1', 'AN', 'mpdf', '0', '0', '0', '1', '0', '0', '1', '0', '0', '0', NULL, '0', '1', '0', '1', '0', '0', '30', '0', '0', '0', '0', NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '1', '1', '1', NULL, '0', '0', '1', '0', '100', '0', '1', '0', '0', '0', NULL, '1', '0', NULL, NULL, NULL, NULL, NULL, '0', '0', NULL, '1', '0', NULL, '1');

-- Table: sma_skrill
DROP TABLE IF EXISTS `sma_skrill`;
CREATE TABLE `sma_skrill` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `account_email` varchar(255) NOT NULL DEFAULT 'testaccount2@moneybookers.com',
  `secret_word` varchar(20) NOT NULL DEFAULT 'mbtest',
  `skrill_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `fixed_charges` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `extra_charges_my` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `extra_charges_other` decimal(25,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_sms_settings
DROP TABLE IF EXISTS `sma_sms_settings`;
CREATE TABLE `sma_sms_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_send` tinyint(1) DEFAULT NULL,
  `config` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_sms_settings` VALUES ('1', '0', NULL);

-- Table: sma_stock_count_items
DROP TABLE IF EXISTS `sma_stock_count_items`;
CREATE TABLE `sma_stock_count_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_count_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_variant` varchar(55) DEFAULT NULL,
  `product_variant_id` int(11) DEFAULT NULL,
  `expected` decimal(15,4) NOT NULL,
  `counted` decimal(15,4) NOT NULL,
  `cost` decimal(25,4) NOT NULL,
  `unit` int(11) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `stock_count_id` (`stock_count_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_stock_counts
DROP TABLE IF EXISTS `sma_stock_counts`;
CREATE TABLE `sma_stock_counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reference_no` varchar(55) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `initial_file` varchar(50) NOT NULL,
  `final_file` varchar(50) DEFAULT NULL,
  `brands` varchar(50) DEFAULT NULL,
  `brand_names` varchar(100) DEFAULT NULL,
  `categories` varchar(50) DEFAULT NULL,
  `category_names` varchar(100) DEFAULT NULL,
  `note` text,
  `products` int(11) DEFAULT NULL,
  `rows` int(11) DEFAULT NULL,
  `differences` int(11) DEFAULT NULL,
  `matches` int(11) DEFAULT NULL,
  `missing` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `finalized` tinyint(1) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `offline_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_subscriptions
DROP TABLE IF EXISTS `sma_subscriptions`;
CREATE TABLE `sma_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `paid_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_subscriptions` VALUES ('1', 'اشتراك شهري', 'Monthly subscription', '1', '1', '2025-08-12 22:35:54', '0.0000', '0.0000');

-- Table: sma_suspended_bills
DROP TABLE IF EXISTS `sma_suspended_bills`;
CREATE TABLE `sma_suspended_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(11) NOT NULL,
  `customer` varchar(55) DEFAULT NULL,
  `count` int(11) NOT NULL,
  `order_discount_id` varchar(20) DEFAULT NULL,
  `order_tax_id` int(11) DEFAULT NULL,
  `total` decimal(25,4) NOT NULL,
  `biller_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `suspend_note` varchar(255) DEFAULT NULL,
  `shipping` decimal(15,4) DEFAULT '0.0000',
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_suspended_items
DROP TABLE IF EXISTS `sma_suspended_items`;
CREATE TABLE `sma_suspended_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `suspend_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `net_unit_price` decimal(25,4) NOT NULL,
  `unit_price` decimal(25,4) NOT NULL,
  `quantity` decimal(15,4) DEFAULT '0.0000',
  `warehouse_id` int(11) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `discount` varchar(55) DEFAULT NULL,
  `item_discount` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) NOT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `real_unit_price` decimal(25,4) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_synch_products
DROP TABLE IF EXISTS `sma_synch_products`;
CREATE TABLE `sma_synch_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) NOT NULL,
  `biller_id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouse_id` (`warehouse_id`,`biller_id`,`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_tax_category
DROP TABLE IF EXISTS `sma_tax_category`;
CREATE TABLE `sma_tax_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `sma_tax_category` VALUES ('1', 'ضريبة القيمة المضافة', 'VAT');
INSERT INTO `sma_tax_category` VALUES ('2', 'ضريبة السلع الإنتقائية', 'EGT');

-- Table: sma_tax_rates
DROP TABLE IF EXISTS `sma_tax_rates`;
CREATE TABLE `sma_tax_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `rate` decimal(12,4) NOT NULL,
  `type` varchar(50) NOT NULL,
  `tax_default` tinyint(1) NOT NULL DEFAULT '0',
  `tax_active` tinyint(1) NOT NULL DEFAULT '0',
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sma_tax_rates` VALUES ('1', '1', 'No Tax', 'NT', '0.0000', '2', '0', '1', '0', '1', '1', '1');
INSERT INTO `sma_tax_rates` VALUES ('2', '1', '0', '0', '0.0000', '1', '0', '1', '0', '1', '1', '1');
INSERT INTO `sma_tax_rates` VALUES ('3', '1', 'ضريبة النسبة الأساسية 15%', '003', '15.0000', '1', '1', '1', '0', '1', '1', '1');

-- Table: sma_transfer_items
DROP TABLE IF EXISTS `sma_transfer_items`;
CREATE TABLE `sma_transfer_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(55) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `expiry` date DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `tax_rate_id` int(11) DEFAULT NULL,
  `tax` varchar(55) DEFAULT NULL,
  `item_tax` decimal(25,4) DEFAULT NULL,
  `net_unit_cost` decimal(25,4) DEFAULT NULL,
  `subtotal` decimal(25,4) DEFAULT NULL,
  `quantity_balance` decimal(15,4) NOT NULL,
  `received_quantity` decimal(15,4) NOT NULL,
  `unit_cost` decimal(25,4) DEFAULT NULL,
  `real_unit_cost` decimal(25,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `product_unit_id` int(11) DEFAULT NULL,
  `product_unit_code` varchar(10) DEFAULT NULL,
  `unit_quantity` decimal(15,4) NOT NULL,
  `gst` varchar(20) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_transfers
DROP TABLE IF EXISTS `sma_transfers`;
CREATE TABLE `sma_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transfer_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_warehouse_id` int(11) NOT NULL,
  `from_warehouse_code` varchar(55) NOT NULL,
  `from_warehouse_name` varchar(55) NOT NULL,
  `to_warehouse_id` int(11) NOT NULL,
  `to_warehouse_code` varchar(55) NOT NULL,
  `to_warehouse_name` varchar(55) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `total` decimal(25,4) DEFAULT NULL,
  `total_tax` decimal(25,4) DEFAULT NULL,
  `grand_total` decimal(25,4) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'pending',
  `shipping` decimal(25,4) NOT NULL DEFAULT '0.0000',
  `attachment` varchar(55) DEFAULT NULL,
  `cgst` decimal(25,4) DEFAULT NULL,
  `sgst` decimal(25,4) DEFAULT NULL,
  `igst` decimal(25,4) DEFAULT NULL,
  `requested_by` varchar(250) NOT NULL,
  `project_area` varchar(250) NOT NULL,
  `driver_name` varchar(250) NOT NULL,
  `driver_mobile_no` varchar(20) NOT NULL,
  `driver_vehcile_no` varchar(30) NOT NULL,
  `driver_iqama_no` varchar(30) NOT NULL,
  `approved_by` varchar(250) NOT NULL,
  `receiver_name` varchar(250) NOT NULL,
  `receiver_no` varchar(30) NOT NULL,
  `receiver_sign` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_units
DROP TABLE IF EXISTS `sma_units`;
CREATE TABLE `sma_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(55) NOT NULL,
  `base_unit` int(11) DEFAULT NULL,
  `operator` varchar(1) DEFAULT NULL,
  `unit_value` varchar(55) DEFAULT NULL,
  `operation_value` varchar(55) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `base_unit` (`base_unit`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_units` VALUES ('1', 'واحد', 'واحد', NULL, NULL, NULL, NULL, '0', '1', '1', '1');

-- Table: sma_user_logins
DROP TABLE IF EXISTS `sma_user_logins`;
CREATE TABLE `sma_user_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_users
DROP TABLE IF EXISTS `sma_users`;
CREATE TABLE `sma_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `last_ip_address` varbinary(45) DEFAULT NULL,
  `ip_address` varbinary(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(55) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `warehouse_id` int(10) unsigned DEFAULT NULL,
  `biller_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `show_cost` tinyint(1) DEFAULT '0',
  `show_price` tinyint(1) DEFAULT '0',
  `award_points` int(11) DEFAULT '0',
  `view_right` tinyint(1) NOT NULL DEFAULT '0',
  `edit_right` tinyint(1) NOT NULL DEFAULT '0',
  `allow_discount` tinyint(1) DEFAULT '0',
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `key_p` varchar(40) DEFAULT NULL,
  `edit_price` tinyint(1) DEFAULT '1',
  `max_discount` int(3) NOT NULL DEFAULT '0',
  `isRecepion` tinyint(1) NOT NULL DEFAULT '0',
  `isPrintInvoices` tinyint(1) NOT NULL DEFAULT '1',
  `updated_on` int(11) unsigned DEFAULT NULL,
  `salesman_id` int(11) unsigned DEFAULT NULL,
  `isSupervisor` tinyint(1) NOT NULL DEFAULT '1',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `CanPrintCashierClose` tinyint(1) NOT NULL DEFAULT '1',
  `CanSeeCashierClose` tinyint(1) NOT NULL DEFAULT '1',
  `waiter_id` int(11) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT '1',
  `showpos_dashboard` tinyint(1) NOT NULL DEFAULT '0',
  `show_sales` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`,`warehouse_id`,`biller_id`),
  KEY `group_id_2` (`group_id`,`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `sma_users` VALUES ('1', '145.82.77.232', '\0\0', 'admin', '9dfa98d4c6f717c59455d5be4261b3d36942617f', NULL, 'default@gmail.com', NULL, NULL, NULL, '6e5e2f4c47ba10736e92891840965955f42f6f45', '1351661704', '1620676490', '1', 'Owner', 'owner', 'default', '12345678', NULL, 'male', '1', NULL, NULL, NULL, '0', '0', '0', '0', '0', '0', '0', NULL, '1', '0', '0', '1', NULL, NULL, '0', '1', '1', '1', '1', '1', NULL, '1', '0', '1');
INSERT INTO `sma_users` VALUES ('2', NULL, '3a3a31', 'user', 'befe4ed89f04d95af72679aa8b574c1af00dde72', NULL, 'casher@default.com', NULL, NULL, NULL, NULL, '1636751661', '1636751661', '1', 'casher', 'casher', 'casher', 'casher', NULL, 'male', '5', '1', '1', NULL, '0', '0', '0', '1', '0', '0', '0', '1234', '1', '0', '0', '1', NULL, NULL, '0', '1', '1', '1', '1', '1', NULL, '1', '0', '1');
INSERT INTO `sma_users` VALUES ('3', NULL, '\0\0', 'manager', '9dfa98d4c6f717c59455d5be4261b3d36942617f', NULL, 'manager@default.com', NULL, NULL, NULL, NULL, '1636751661', '1636751661', '1', 'Manager', 'manager', 'default', NULL, NULL, 'male', '2', NULL, NULL, NULL, '0', '0', '0', '0', '0', '0', '0', NULL, '1', '0', '0', '1', NULL, NULL, '1', '1', '1', '1', '1', '1', NULL, '1', '0', '1');

-- Table: sma_variants
DROP TABLE IF EXISTS `sma_variants`;
CREATE TABLE `sma_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_warehouses
DROP TABLE IF EXISTS `sma_warehouses`;
CREATE TABLE `sma_warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `vat_reg_no` varchar(55) DEFAULT NULL,
  `cr_no` varchar(55) DEFAULT NULL,
  `map` varchar(255) DEFAULT NULL,
  `phone` varchar(55) DEFAULT NULL,
  `email` varchar(55) DEFAULT NULL,
  `price_group_id` int(11) DEFAULT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `building_no` varchar(8) DEFAULT NULL,
  `street_name` varchar(250) DEFAULT NULL,
  `district_name` varchar(250) DEFAULT NULL,
  `additional_no` varchar(8) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(8) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `warehouse_logo` varchar(255) DEFAULT NULL,
  `tax_method` varchar(15) NOT NULL DEFAULT 'products',
  `addressEn` varchar(255) DEFAULT NULL,
  `nameEn` varchar(255) DEFAULT NULL,
  `branch_nameEn` varchar(255) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `isLogistic` tinyint(1) NOT NULL DEFAULT '0',
  `street_nameEn` varchar(255) DEFAULT NULL,
  `district_nameEn` varchar(255) DEFAULT NULL,
  `cityEn` varchar(255) DEFAULT NULL,
  `stateEn` varchar(255) DEFAULT NULL,
  `countryEn` varchar(255) DEFAULT NULL,
  `isTobacco` tinyint(1) NOT NULL DEFAULT '0',
  `isRestaurant` tinyint(1) NOT NULL DEFAULT '0',
  `warehouse_Seal` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_warehouses` VALUES ('1', 'w_1', 'Default', '', 'Default', NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'products', NULL, NULL, NULL, '1', '1', '1', '0', NULL, NULL, NULL, NULL, NULL, '0', '0', NULL);

-- Table: sma_warehouses_products
DROP TABLE IF EXISTS `sma_warehouses_products`;
CREATE TABLE `sma_warehouses_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` decimal(25,12) NOT NULL,
  `rack` varchar(55) DEFAULT NULL,
  `avg_cost` decimal(25,12) NOT NULL,
  `api` tinyint(1) NOT NULL DEFAULT '0',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id_2` (`product_id`,`warehouse_id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_warehouses_products_avg_cost
DROP TABLE IF EXISTS `sma_warehouses_products_avg_cost`;
CREATE TABLE `sma_warehouses_products_avg_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `rack` varchar(55) DEFAULT NULL,
  `avg_cost` decimal(25,12) NOT NULL,
  `total_qty_prev` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `new_avg_cost` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_warehouses_products_variants
DROP TABLE IF EXISTS `sma_warehouses_products_variants`;
CREATE TABLE `sma_warehouses_products_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `rack` varchar(55) DEFAULT NULL,
  `avg_cost` decimal(25,12) NOT NULL DEFAULT '0.000000000000',
  PRIMARY KEY (`id`),
  KEY `option_id` (`option_id`),
  KEY `product_id` (`product_id`),
  KEY `warehouse_id` (`warehouse_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_whatsapp_settings
DROP TABLE IF EXISTS `sma_whatsapp_settings`;
CREATE TABLE `sma_whatsapp_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_send` tinyint(1) DEFAULT NULL,
  `config` text,
  `sales` tinyint(1) NOT NULL DEFAULT '0',
  `quotes` tinyint(1) NOT NULL DEFAULT '0',
  `pos` tinyint(1) NOT NULL DEFAULT '0',
  `award_points` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_whatsapp_settings` VALUES ('1', '0', NULL, '0', '0', '0', '0');

-- Table: sma_whatsapp_templates
DROP TABLE IF EXISTS `sma_whatsapp_templates`;
CREATE TABLE `sma_whatsapp_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quotes` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `award_points` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reverse_award_points` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_whatsapp_templates` VALUES ('1', 'عزيزي {customer}, طلبك رقم  (Ref. {sale_reference}) بمبلغ  {grand_total} رابط التحميل {order_link}', 'عزيزي {customer}, طلبك رقم  (Ref. {quote_reference}) بمبلغ  {grand_total} رابط التحميل {order_link}', 'عزيزي {customer} لديك :\r\n({award_points}) نقطة\r\n({reverse_award_points}) {default_currency}\r\nبادر بزيارة احد فروع {site_name} او الموقع الإلكتروني للإستفادة منها دمت بخير', 'عزيزي {customer}, لقد حصلت على  ({points}) نقطة. رصيدك من النقاط  {award_points}');

SET FOREIGN_KEY_CHECKS=1;
