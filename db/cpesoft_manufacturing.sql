-- Database Dump: cpesoft_manufacturing
-- Date: 2026-10-10 22:42:42

SET FOREIGN_KEY_CHECKS=0;


-- Table: sma_man_bom
DROP TABLE IF EXISTS `sma_man_bom`;
CREATE TABLE `sma_man_bom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `image` varchar(50) DEFAULT 'no_image.png',
  `description` varchar(1000) DEFAULT NULL,
  `wh_raw` int(11) NOT NULL,
  `wh_manufacturing` int(11) NOT NULL,
  `wh_complete` int(11) NOT NULL,
  `wh_destruction` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `wh_raw_name` varchar(100) DEFAULT NULL,
  `wh_manufacturing_name` varchar(100) DEFAULT NULL,
  `wh_complete_name` varchar(100) DEFAULT NULL,
  `wh_destruction_name` varchar(100) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `production_mode` varchar(20) NOT NULL DEFAULT 'single',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_man_bom_details
DROP TABLE IF EXISTS `sma_man_bom_details`;
CREATE TABLE `sma_man_bom_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(30) NOT NULL,
  `product_unit` int(11) NOT NULL,
  `product_quantity` decimal(12,4) NOT NULL,
  `product_avg_cost` decimal(24,12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_man_bom_expenses
DROP TABLE IF EXISTS `sma_man_bom_expenses`;
CREATE TABLE `sma_man_bom_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id` int(11) NOT NULL,
  `expenses_acct` int(11) NOT NULL,
  `cost` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_man_bom_labors
DROP TABLE IF EXISTS `sma_man_bom_labors`;
CREATE TABLE `sma_man_bom_labors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id` int(11) NOT NULL,
  `labor_desc` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `hours_no` decimal(12,4) NOT NULL,
  `hours_wage` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_man_bom_stages
DROP TABLE IF EXISTS `sma_man_bom_stages`;
CREATE TABLE `sma_man_bom_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `description` text,
  `planned_duration` int(11) NOT NULL DEFAULT '0',
  `work_center` varchar(100) DEFAULT NULL,
  `responsible_id` int(11) DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `can_skip` tinyint(1) NOT NULL DEFAULT '0',
  `record_quantity` tinyint(1) NOT NULL DEFAULT '1',
  `is_packaging` tinyint(1) NOT NULL DEFAULT '0',
  `require_qc` tinyint(1) NOT NULL DEFAULT '0',
  `standard_labor_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `standard_expense_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bom_id` (`bom_id`),
  KEY `sequence` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_man_manufacturing
DROP TABLE IF EXISTS `sma_man_manufacturing`;
CREATE TABLE `sma_man_manufacturing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `deadline_start` date NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'confirmed',
  `description` varchar(1000) DEFAULT NULL,
  `date_produce` timestamp NULL DEFAULT NULL,
  `date_done` timestamp NULL DEFAULT NULL,
  `confirm_qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `production_mode` varchar(20) NOT NULL DEFAULT 'single',
  `current_stage_id` int(11) DEFAULT NULL,
  `current_stage_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_man_manufacturing_details
DROP TABLE IF EXISTS `sma_man_manufacturing_details`;
CREATE TABLE `sma_man_manufacturing_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `man_id` int(11) NOT NULL,
  `bom_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_code` varchar(30) NOT NULL,
  `product_unit` int(11) NOT NULL,
  `product_quantity` decimal(12,4) NOT NULL,
  `product_avg_cost` decimal(24,12) NOT NULL,
  `req_status` varchar(20) NOT NULL DEFAULT 'normal',
  `actual_qty` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_man_manufacturing_expenses
DROP TABLE IF EXISTS `sma_man_manufacturing_expenses`;
CREATE TABLE `sma_man_manufacturing_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `man_id` int(11) NOT NULL,
  `bom_id` int(11) NOT NULL,
  `expenses_acct` int(11) NOT NULL,
  `cost` decimal(12,4) NOT NULL,
  `req_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `actual_cost` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_man_manufacturing_labors
DROP TABLE IF EXISTS `sma_man_manufacturing_labors`;
CREATE TABLE `sma_man_manufacturing_labors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `man_id` int(11) NOT NULL,
  `bom_id` int(11) NOT NULL,
  `labor_desc` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `hours_no` decimal(12,4) NOT NULL,
  `hours_wage` decimal(12,4) NOT NULL,
  `req_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `actual_hours_no` decimal(12,4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_man_manufacturing_stage_events
DROP TABLE IF EXISTS `sma_man_manufacturing_stage_events`;
CREATE TABLE `sma_man_manufacturing_stage_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `man_id` int(11) NOT NULL,
  `stage_id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_source` varchar(50) NOT NULL DEFAULT 'manual',
  `event_time` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payload` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `man_id` (`man_id`),
  KEY `stage_id` (`stage_id`),
  KEY `event_time` (`event_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_man_manufacturing_stages
DROP TABLE IF EXISTS `sma_man_manufacturing_stages`;
CREATE TABLE `sma_man_manufacturing_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `man_id` int(11) NOT NULL,
  `bom_stage_id` int(11) DEFAULT NULL,
  `sequence` int(11) NOT NULL DEFAULT '1',
  `name` varchar(100) NOT NULL,
  `description` text,
  `planned_duration` int(11) NOT NULL DEFAULT '0',
  `work_center` varchar(100) DEFAULT NULL,
  `responsible_id` int(11) DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `can_skip` tinyint(1) NOT NULL DEFAULT '0',
  `record_quantity` tinyint(1) NOT NULL DEFAULT '1',
  `is_packaging` tinyint(1) NOT NULL DEFAULT '0',
  `require_qc` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `planned_start` datetime DEFAULT NULL,
  `actual_start` datetime DEFAULT NULL,
  `actual_end` datetime DEFAULT NULL,
  `paused_at` datetime DEFAULT NULL,
  `total_paused_seconds` int(11) NOT NULL DEFAULT '0',
  `active_duration_seconds` int(11) NOT NULL DEFAULT '0',
  `input_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `output_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `rejected_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `packaged_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `stage_labor_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `stage_expense_cost` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `notes` text,
  `completed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `man_id` (`man_id`),
  KEY `status` (`status`),
  KEY `sequence` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS=1;
