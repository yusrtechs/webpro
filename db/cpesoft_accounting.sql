-- Database Dump: cpesoft_accounting
-- Date: 2026-10-10 22:57:29

SET FOREIGN_KEY_CHECKS=0;


-- Table: sma_gl_accounting_period
DROP TABLE IF EXISTS `sma_gl_accounting_period`;
CREATE TABLE `sma_gl_accounting_period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `period_year` int(4) NOT NULL,
  `period_year_status` tinyint(1) NOT NULL DEFAULT '1',
  `period_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `period_from_date` date NOT NULL,
  `period_to_date` date NOT NULL,
  `period_status` int(1) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: sma_gl_accounts
DROP TABLE IF EXISTS `sma_gl_accounts`;
CREATE TABLE `sma_gl_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accountID` bigint(20) NOT NULL,
  `userID` bigint(20) NOT NULL,
  `accountName` text NOT NULL,
  `accountNameEn` text,
  `accountCategory` varchar(50) NOT NULL,
  `accountCategorySub` varchar(50) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `master_account` tinyint(1) NOT NULL DEFAULT '0',
  `accountSide` varchar(25) DEFAULT NULL,
  `accountBalance` decimal(10,2) DEFAULT NULL,
  `openingBalance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `accountDebit` decimal(10,2) DEFAULT NULL,
  `accountCredit` decimal(10,2) DEFAULT NULL,
  `accountOrder` int(11) DEFAULT NULL,
  `accountStatus` int(1) NOT NULL DEFAULT '1',
  `accountStatement` varchar(50) DEFAULT NULL,
  `accountCreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `account_type` varchar(50) DEFAULT NULL,
  `account_ref_id` int(11) DEFAULT NULL,
  `costCenterId` text NOT NULL,
  `costCenterPercentage` text NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  `account_ref_id2` int(11) DEFAULT NULL,
  `account_ref_id3` int(11) DEFAULT NULL,
  `account_ref_id4` int(11) DEFAULT NULL,
  `account_ref_id5` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;

INSERT INTO `sma_gl_accounts` VALUES ('1', '1', '1', 'اصول', 'Assets', 'اصول', NULL, '0', '0', NULL, NULL, '0.00', NULL, NULL, NULL, '1', NULL, '2021-08-31 18:58:53', NULL, NULL, '', '', '1', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('2', '2', '1', 'الإلتزامات وحقوق الملكية', 'Liabilities &Equity Owners', 'الإلتزامات وحقوق الملكية', NULL, '0', '0', NULL, NULL, '0.00', NULL, NULL, NULL, '1', NULL, '2021-09-01 15:08:38', NULL, NULL, '', '', '1', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('3', '3', '1', 'المصروفات', 'Expenses', 'المصروفات', NULL, '0', '0', NULL, NULL, '0.00', NULL, NULL, NULL, '1', NULL, '2021-09-01 15:09:21', NULL, NULL, '', '', '1', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('4', '4', '1', 'الإيرادات', 'Revenues', 'الإيرادات', NULL, '0', '0', NULL, NULL, '0.00', NULL, NULL, NULL, '1', NULL, '2021-09-01 15:09:57', NULL, NULL, '', '', '1', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('5', '11', '1', 'الأصول المتداولة', 'Current Assets', 'اصول', NULL, '1', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 15:10:48', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('6', '12', '1', 'الأصول الثابته', 'Fixed Assets', 'اصول', NULL, '1', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 15:11:26', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('7', '13', '1', 'الأصول الأخرى', 'Other Assets', 'اصول', NULL, '1', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 15:12:53', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('8', '1101', '1', 'الصندوق', 'The Found', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', '1000.00', NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:31:50', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('9', '110101', '1', 'الصندوق الرئيسي', 'The Main Fund', 'اصول', NULL, '8', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:32:38', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('10', '1102', '1', 'البنوك', 'The Banks', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:33:05', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('11', '1103', '1', 'المخزن', 'The Store', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:33:36', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('12', '110301', '1', 'المخزن الرئيسي', 'The Main Store', 'اصول', NULL, '11', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:34:03', 'warehouses', '1', '[]', '[]', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('13', '1104', '1', 'مصروفات مدفوعة مقدما', 'Advance Prepaid Expenses', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:34:58', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('14', '110401', '1', 'ايجارات مدفوعة مقدما', 'Renral Paid In Advance', 'اصول', NULL, '13', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:35:25', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('15', '110402', '1', 'اقامات مدفوعة مقدما', 'Iqama Paid In Advance', 'اصول', NULL, '13', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:39:37', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('16', '110302', '1', 'بضاعة بالطريق', 'Store Goods By Road', 'اصول', NULL, '11', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:40:28', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('17', '110303', '1', 'بضاعة قيد التسليم', 'Store Goods Receiving', 'اصول', NULL, '11', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:41:09', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('18', '1201', '1', 'الات ومعدات', 'Machinery And Equipment', 'اصول', NULL, '6', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:53:53', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('19', '1202', '1', 'سيارات', 'Cars', 'اصول', NULL, '6', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:54:23', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('20', '1203', '1', 'اثاثات', 'Furnitures', 'اصول', NULL, '6', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:54:48', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('21', '1204', '1', 'اجهزة كهربائية', 'Electrical Devices', 'اصول', NULL, '6', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:55:08', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('22', '1205', '1', 'اجهزة حاسب آلي وطابعات', 'Computer And Printer', 'اصول', NULL, '6', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:55:35', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('23', '1206', '1', 'مباني', 'Building', 'اصول', NULL, '6', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:56:06', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('24', '120101', '1', 'ماكينات', 'Machines', 'اصول', NULL, '18', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:56:35', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('26', '120301', '1', 'اثاث مكتبي', 'Office Furniture', 'اصول', NULL, '20', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:57:36', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('29', '120601', '1', 'مبنى الادارة', 'Administration buildings', 'اصول', NULL, '23', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 16:58:45', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('30', '110304', '1', 'مخزن المواد الخام', 'Raw Material Store', 'اصول', NULL, '11', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:00:18', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('31', '110305', '1', 'مخزن انتاج تحت التشغيل', 'Ametrial Store In Operation', 'اصول', NULL, '11', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:00:45', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('32', '110306', '1', 'مخزن الانتاج التام', 'Aperfect Product Store', 'اصول', NULL, '11', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:01:04', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('34', '1105', '1', 'العملاء', 'Customers', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:03:23', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('35', '110501', '1', 'عملاء محليين', 'Local Customers', 'اصول', NULL, '34', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:03:49', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('36', '110502', '1', 'عملاء دوليين', 'International Customers', 'اصول', NULL, '34', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:04:19', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('37', '1106', '1', 'ايرادات مستحقة', 'Accrued Revenues', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:04:46', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('38', '110601', '1', 'ايراد ايجار مستحق', 'Accrued Rent Revenue ', 'اصول', NULL, '37', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:05:17', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('39', '1107', '1', 'سلف العاملين', 'Advance Warkers', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:05:48', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('40', '1108', '1', 'عهد مالية', 'Petty Cash', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:06:19', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('41', '21', '1', 'التزامات متداولة', 'Current Liabilities', 'الإلتزامات وحقوق الملكية', NULL, '2', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:06:58', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('42', '22', '1', 'التزامات طويلة الاجل', 'Long Term Liabilities', 'الإلتزامات وحقوق الملكية', NULL, '2', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:07:42', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('43', '2101', '1', 'الموردين', 'Suppliers', 'الإلتزامات وحقوق الملكية', NULL, '41', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:09:15', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('44', '210101', '1', 'موردين محليين', 'Local Supplier', 'الإلتزامات وحقوق الملكية', NULL, '43', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:09:44', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('45', '210102', '1', 'موردين دوليين', 'International Supplier', 'الإلتزامات وحقوق الملكية', NULL, '43', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:10:09', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('46', '2102', '1', 'مستحقات موظفين', 'Employee Entitlements', 'الإلتزامات وحقوق الملكية', NULL, '41', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:10:48', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('47', '210201', '1', 'رواتب مستحقة', NULL, 'الإلتزامات وحقوق الملكية', NULL, '46', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:11:24', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('48', '210202', '1', 'نهاية خدمة مستحقة', NULL, 'الإلتزامات وحقوق الملكية', NULL, '46', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:11:56', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('49', '210203', '1', 'تزاكر سفر مستحقة', NULL, 'الإلتزامات وحقوق الملكية', NULL, '46', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:12:28', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('50', '2103', '1', 'مصروفات مستحقة', 'Accrued Expenses', 'الإلتزامات وحقوق الملكية', NULL, '41', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:13:10', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('51', '210301', '1', 'ايجارات مستحقة', NULL, 'الإلتزامات وحقوق الملكية', NULL, '50', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:13:50', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('52', '210302', '1', 'فوائد مستحقة', NULL, 'الإلتزامات وحقوق الملكية', NULL, '50', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:14:25', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('53', '2201', '1', 'المخصصات', 'The Provisions', 'الإلتزامات وحقوق الملكية', NULL, '42', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:14:49', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('54', '220101', '1', 'مخصص اهلاك الاصول الثابتة', NULL, 'الإلتزامات وحقوق الملكية', NULL, '53', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:15:38', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('55', '220102', '1', 'مخصص ديون مشكوك في تحصيلها', NULL, 'الإلتزامات وحقوق الملكية', NULL, '53', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:16:25', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('56', '23', '1', 'حقوق الملكية', 'Owner Equity', 'الإلتزامات وحقوق الملكية', NULL, '2', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:17:16', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('57', '2301', '1', 'راس المال', 'Capital', 'الإلتزامات وحقوق الملكية', NULL, '56', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:17:54', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('58', '2302', '1', 'حساب جاري الشركاء', 'Current Account Partners', 'الإلتزامات وحقوق الملكية', NULL, '56', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:18:32', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('59', '2303', '1', 'ارباح وخسائر العام', 'Profit And Loss For The Year', 'الإلتزامات وحقوق الملكية', NULL, '56', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:19:09', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('60', '230301', '1', 'ارباح مرحلة', 'Profits Stage', 'الإلتزامات وحقوق الملكية', NULL, '59', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:19:33', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('61', '230302', '1', 'خسائر مرحلة', 'Losses Stage', 'الإلتزامات وحقوق الملكية', NULL, '59', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:19:57', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('62', '2304', '1', 'الاحتياطيات', 'Reserves', 'الإلتزامات وحقوق الملكية', NULL, '56', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:20:29', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('64', '31', '1', 'مصاريف ادارية', 'Administrative Expenses', 'المصروفات', NULL, '3', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:21:21', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('65', '32', '1', 'مصاريف بيعية وتسويقية', 'Selling And Markiting Expenses', 'المصروفات', NULL, '3', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:21:40', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('66', '33', '1', 'مصاريف اخرى', 'Other Expenses', 'المصروفات', NULL, '3', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:22:02', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('67', '41', '1', 'المبيعات', 'Sales', 'الإيرادات', NULL, '4', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:24:11', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('68', '42', '1', 'ايرادات خدمات', 'Services Revenue', 'الإيرادات', NULL, '4', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:24:37', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('69', '43', '1', 'ايرادات اخرى', 'Other Revenues', 'الإيرادات', NULL, '4', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:25:15', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('70', '4301', '1', 'ارباح بيع اصول ثابتة', 'Profits Sale Fixed Assets', 'الإيرادات', NULL, '69', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:25:50', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('71', '4302', '1', 'تسوية مخزن (فائض )', 'Store Settlement  (Surplus)', 'الإيرادات', NULL, '69', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:26:26', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('72', '4303', '1', 'ارباح اعادة التقييم', 'Revaluation Profite', 'الإيرادات', NULL, '69', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:26:45', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('73', '3101', '1', 'الرواتب والاجور / اداري', 'Salaries', 'المصروفات', NULL, '64', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:27:28', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('74', '34', '1', 'ايجارات', 'Rents', 'المصروفات', NULL, '3', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:27:59', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('75', '3102', '1', 'كهرباء / اداري', 'Electricity', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:28:47', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('79', '3103', '1', 'انترنت', 'Internet', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:30:05', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('80', '3104', '1', 'صيانة واصلاحات', 'Maintenance And Repairs', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:30:23', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('81', '3105', '1', 'عمولات بنكية', 'Bank Commisson', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:30:45', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('83', '3106', '1', 'هاتف', 'Telephone', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:31:07', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('85', '3107', '1', 'نظافة', 'Cleaner', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:31:28', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('86', '3108', '1', 'مصروفات حكومية', 'Govermental Expenses', 'المصروفات', NULL, '64', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:31:53', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('87', '310801', '1', 'تاشيرات', 'Visa', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:32:24', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('88', '310802', '1', 'تاشيرة خروج وعودة', 'Exit And Return Visa', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:32:54', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('89', '3109', '1', 'تجديد اقامات', 'Renewal Of Residence', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:33:10', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('90', '310803', '1', 'رسوم خدمة ابشر', 'Absher Service Fees', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:33:41', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('91', '31010', '1', 'رسوم ترخيص', 'License Fees', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:33:56', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('92', '31011', '1', 'رسوم السجل التجاري', 'Commercial Registration Fees', 'المصروفات', NULL, '64', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:34:15', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('93', '310804', '1', 'رسوم الكرت الصحي', 'Health Card Fees', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:34:53', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('94', '310805', '1', 'بريد واصل', 'Wassil Email', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:35:18', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('95', '310806', '1', 'تامين صحي', 'Health Insurance', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:35:44', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('96', '310807', '1', 'الزكاة والدخل', 'Zakkat & Income', 'المصروفات', NULL, '86', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:36:30', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('97', '3401', '1', 'ايجار', 'Rent', 'المصروفات', NULL, '74', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:37:11', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('98', '31012', '1', 'مصاريف اهلاك', 'Depreciation Expenses', 'المصروفات', NULL, '64', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:38:31', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('99', '35', '1', 'تكلفة البضاعة المباعة', 'Cost Of Good Sold', 'المصروفات', NULL, '3', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:39:43', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('100', '3501', '1', 'تكلفة البضاعة المباعة', 'Cost Of Good Sold', 'المصروفات', NULL, '99', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:40:02', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('101', '3301', '1', 'تلف مخزني', 'Destruction inventory', 'المصروفات', NULL, '66', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:40:38', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('102', '3302', '1', 'تسوية المخزن (عجز )', 'Settlement Of Warhouse Defict', 'المصروفات', NULL, '66', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:41:01', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('103', '3303', '1', 'خسائر بيع اصول ثابتة', 'Losses On Fixed Assets', 'المصروفات', NULL, '66', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:41:26', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('104', '3304', '1', 'خسائر اعادة التقييم', 'Revaluation Losses', 'المصروفات', NULL, '66', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:41:43', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('105', '3201', '1', 'رواتب واجور / تسويقي', 'Salaries / Marketing', 'المصروفات', NULL, '65', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:42:14', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('106', '3202', '1', 'دعاية واعلان', 'Advertising', 'المصروفات', NULL, '65', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:42:32', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('107', '3203', '1', 'عمولة وكلاء البيع', 'Selling Agents Commission', 'المصروفات', NULL, '65', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:42:48', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('108', '3204', '1', 'عمولات بنكية / تسويقي', 'Bank Commisson / Marketing', 'المصروفات', NULL, '65', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:44:02', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('110', '3205', '1', 'خصم مسموح به', 'Discount Allowance', 'المصروفات', NULL, '65', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:49:56', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('111', '3206', '1', 'خصم ممنوح للغير', 'Discount Given To Others', 'المصروفات', NULL, '65', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:50:13', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('112', '44', '1', 'خصم مكتسب', 'Discount Earend', 'الإيرادات', NULL, '4', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 17:50:33', NULL, NULL, '', '', '2', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('113', '3305', '1', 'خسائر مرتجع شحن', 'Shipping Return Losses', 'المصروفات', NULL, '66', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-09-01 18:49:37', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('119', '230101', '1', 'راس المال', 'Capital', 'الإلتزامات وحقوق الملكية', NULL, '57', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-12 11:18:14', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('120', '2202', '1', 'قروض طويلة الاجل', 'Longer - term loans', 'الإلتزامات وحقوق الملكية', NULL, '42', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-12 11:19:56', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('121', '1301', '1', 'ضريبة القيمة المضافة على المدخلات', '', 'اصول', NULL, '7', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-12-20 15:02:38', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('122', '2104', '1', 'ضريبة القيمة المضافة على المخرجات', '', 'الإلتزامات وحقوق الملكية', NULL, '41', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-12-20 15:03:20', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('123', '4304', '1', 'ايرادات اخرى شحن', '', 'الإيرادات', NULL, '69', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-12-20 15:04:25', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('124', '4101', '1', 'مردودات المبيعات', '', 'الإيرادات', NULL, '67', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2021-12-20 15:05:00', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('125', '3306', '1', 'خسائر فروق عملة', '', 'المصروفات', NULL, '66', '0', 'Debit', NULL, '0.00', '3000.00', NULL, NULL, '1', 'Income Statement', '2022-03-01 16:55:23', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('126', '4305', '1', 'ارباح فروق عملة', '', 'الإيرادات', NULL, '69', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2022-03-01 16:55:44', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('127', '3502', '1', 'مخزون اول الفترة', '', 'المصروفات', NULL, '99', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2022-09-18 11:23:06', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('128', '3503', '1', 'المشتريات', '', 'المصروفات', NULL, '99', '0', 'Debit', NULL, '0.00', '0.00', NULL, NULL, '1', 'Income Statement', '2022-09-18 11:23:30', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('129', '3504', '1', 'مردودات المشتريات', '', 'المصروفات', NULL, '99', '0', 'Credit', NULL, '0.00', NULL, '0.00', NULL, '1', 'Income Statement', '2022-09-18 11:23:54', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('130', '350101', '1', 'مخزون اخر الفترة', '', 'المصروفات', NULL, '100', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2022-09-18 11:24:45', '', NULL, '[]', '[]', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('131', '3505', '1', 'ملخص الدخل مدين', '', 'المصروفات', NULL, '99', '0', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2022-09-18 11:25:53', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('132', '3506', '1', 'ملخص الدخل دائن', '', 'المصروفات', NULL, '99', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Income Statement', '2022-09-18 11:26:24', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('133', '3307', '1', 'مدفوعات بطاقة هدية', 'مدفوعات بطاقة هدية', 'المصروفات', NULL, '66', '0', 'Debit', NULL, '0.00', '0.00', '0.00', NULL, '1', 'Income Statement', '2022-07-24 19:07:44', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('134', '4306', '1', 'مبيعات بطاقة هدية', 'مبيعات بطاقة هدية', 'الإيرادات', NULL, '69', '0', 'Credit', NULL, '0.00', '0.00', '0.00', NULL, '1', 'Income Statement', '2022-07-24 18:56:33', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('135', '210303', '1', 'تبرعات مستحقه للجمعيات الخيرية', NULL, 'الإلتزامات وحقوق الملكية', NULL, '50', '0', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:14:25', NULL, NULL, '', '', '4', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('136', '1109', '1', 'تامين ضمان الاعمال', 'Business guarantee insurance', 'اصول', NULL, '5', '1', 'Debit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-09-01 17:06:19', NULL, NULL, '', '', '3', NULL, NULL, NULL, NULL);
INSERT INTO `sma_gl_accounts` VALUES ('137', '2105', '1', 'دفعات مقدمة من العملاء', 'Advance payments from customers', 'الإلتزامات وحقوق الملكية', NULL, '41', '1', 'Credit', NULL, '0.00', NULL, NULL, NULL, '1', 'Balance Sheet', '2021-12-20 15:03:20', '', NULL, '[]', '[]', '3', NULL, NULL, NULL, NULL);

-- Table: sma_gl_accounts_categories
DROP TABLE IF EXISTS `sma_gl_accounts_categories`;
CREATE TABLE `sma_gl_accounts_categories` (
  `categoryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `categoryNameEn` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `categoryDescription` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sma_gl_accounts_categories` VALUES ('1', 'اصول', 'Assets', 'اصول');
INSERT INTO `sma_gl_accounts_categories` VALUES ('2', 'الإلتزامات وحقوق الملكية', 'Liabilities &Equity Owners', 'الإلتزامات وحقوق الملكية');
INSERT INTO `sma_gl_accounts_categories` VALUES ('3', 'المصروفات', 'Expenses', 'المصروفات');
INSERT INTO `sma_gl_accounts_categories` VALUES ('4', 'الإيرادات', 'Revenues', 'الإيرادات');

-- Table: sma_gl_accounts_settings
DROP TABLE IF EXISTS `sma_gl_accounts_settings`;
CREATE TABLE `sma_gl_accounts_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_acct` int(11) NOT NULL,
  `sales_cost_of_good_sold_acct` int(11) NOT NULL,
  `sales_boxes_acct` int(11) NOT NULL,
  `sales_return_acct` int(11) NOT NULL,
  `sales_vat_acct` int(11) NOT NULL,
  `sales_store_goods_receiving_acct` int(11) NOT NULL,
  `sales_discount_acct` int(11) NOT NULL,
  `sales_extra_charge_acct` int(11) NOT NULL,
  `purchases_acct` int(11) NOT NULL,
  `purchases_return_acct` int(11) NOT NULL,
  `purchases_vat_acct` int(11) NOT NULL,
  `purchases_store_goods_by_road_acct` int(11) NOT NULL,
  `purchases_foreign_orders_acct` int(11) NOT NULL,
  `expenses_shipping_acct` int(11) NOT NULL,
  `warehouses_acct` int(11) NOT NULL,
  `warehouses_revaluation_losses` int(11) NOT NULL DEFAULT '0',
  `warehouses_revaluation_profite` int(11) NOT NULL DEFAULT '0',
  `warehouses_adjustment_surplus` int(11) NOT NULL DEFAULT '0',
  `warehouses_adjustment_inability` int(11) NOT NULL DEFAULT '0',
  `warehouses_destruction` int(11) NOT NULL DEFAULT '0',
  `warehouses_products_beginning_period_acct` int(11) NOT NULL,
  `warehouses_products_ending_period_acct` int(11) NOT NULL,
  `accounts_main_boxes_acct` int(11) NOT NULL,
  `accounts_main_banks_acct` int(11) NOT NULL,
  `accounts_loans_acct` int(11) NOT NULL,
  `accounts_customers_acct` int(11) NOT NULL,
  `accounts_local_customers_acct` int(11) NOT NULL,
  `accounts_international_customers_acct` int(11) NOT NULL,
  `accounts_suppliers_acct` int(11) NOT NULL,
  `accounts_local_suppliers_acct` int(11) NOT NULL,
  `accounts_international_suppliers_acct` int(11) NOT NULL,
  `accounts_bank_commisson_acct` int(11) NOT NULL,
  `accounts_shipping_return_losses` int(11) NOT NULL,
  `accounts_shipping_other_revenues` int(11) NOT NULL,
  `accounts_bank_acct` int(11) NOT NULL,
  `accounts_ready_funds_acct` int(11) NOT NULL,
  `accounts_operating_expenses_acct` int(11) NOT NULL,
  `accounts_marketing_expenses_acct` int(11) NOT NULL,
  `accounts_general_expenses_acct` int(11) NOT NULL,
  `accounts_fixed_assets_acct` int(11) NOT NULL,
  `accounts_current_assets_acct` int(11) NOT NULL,
  `accounts_provisions_acct` int(11) NOT NULL,
  `cost_centers` tinyint(1) NOT NULL DEFAULT '0',
  `accounts_profits_stage_acct` int(11) NOT NULL DEFAULT '60',
  `accounts_losses_stage_acct` int(11) NOT NULL DEFAULT '61',
  `accounts_currency_differences_profit_acct` int(11) NOT NULL,
  `accounts_currency_spread_losses_acct` int(11) NOT NULL,
  `accounts_gift_card_sales` int(11) NOT NULL,
  `accounts_gift_card_payments` int(11) NOT NULL,
  `accounts_discount_allowance` int(11) NOT NULL,
  `accounts_discount_earned` int(11) NOT NULL,
  `donations_acct` int(11) NOT NULL DEFAULT '0',
  `business_guarantee_insurance_acct` int(11) NOT NULL DEFAULT '0',
  `advance_payments_acct` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_gl_accounts_settings` VALUES ('1', '67', '100', '0', '124', '122', '17', '0', '0', '128', '129', '121', '16', '0', '50', '11', '104', '72', '71', '102', '101', '0', '0', '8', '10', '0', '0', '35', '36', '43', '44', '45', '81', '113', '123', '0', '56', '64', '65', '0', '6', '5', '53', '0', '60', '61', '126', '125', '134', '133', '110', '112', '135', '136', '137');

-- Table: sma_gl_catch_receipts
DROP TABLE IF EXISTS `sma_gl_catch_receipts`;
CREATE TABLE `sma_gl_catch_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bond_no` varchar(20) NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` decimal(25,4) NOT NULL,
  `tax_rate` int(11) NOT NULL,
  `tax_amount` decimal(25,4) NOT NULL,
  `total_amount` decimal(25,4) NOT NULL,
  `paid_by` varchar(55) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `toAccountID` bigint(20) NOT NULL,
  `fromAccountID` bigint(20) NOT NULL,
  `receipt_name` varchar(255) DEFAULT NULL,
  `note` text,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(100) DEFAULT NULL,
  `costCenterDebitId` text NOT NULL,
  `costCenterDebitAmount` text NOT NULL,
  `costCenterCreditId` text NOT NULL,
  `costCenterCreditAmount` text NOT NULL,
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(25,4) NOT NULL DEFAULT '1.0000',
  `equivalent_amount` decimal(25,4) NOT NULL,
  `equivalent_tax_amount` decimal(25,4) NOT NULL,
  `equivalent_total_amount` decimal(25,4) NOT NULL,
  `entryCreditAccount` text,
  `entryCreditBalance` text,
  `entryDebitAccount` text,
  `entryDebitBalance` text,
  `fromAccountText` varchar(1000) DEFAULT NULL,
  `toAccountText` varchar(500) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `entryCreditDescription` text,
  `entryDebitDescription` text,
  `salesman_id` int(11) DEFAULT NULL,
  `salesman_name` varchar(100) DEFAULT NULL,
  `inv_no` varchar(100) DEFAULT NULL,
  `the_oldest` tinyint(1) NOT NULL DEFAULT '0',
  `invoices` text,
  `payments` text,
  `equivalent_entryCreditBalance` text,
  `equivalent_entryDebitBalance` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_cost_centers
DROP TABLE IF EXISTS `sma_gl_cost_centers`;
CREATE TABLE `sma_gl_cost_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `costID` bigint(20) NOT NULL,
  `userID` bigint(20) NOT NULL,
  `costName` text NOT NULL,
  `costNameEn` text,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `master_cost` tinyint(1) NOT NULL DEFAULT '0',
  `costCreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `sma_gl_cost_centers` VALUES ('1', '0', '1', 'مراكز التكلفة', 'Cost centers', '0', '0', '2022-05-27 14:53:15');

-- Table: sma_gl_entries
DROP TABLE IF EXISTS `sma_gl_entries`;
CREATE TABLE `sma_gl_entries` (
  `entryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `userID` bigint(20) NOT NULL,
  `reference_no` varchar(55) DEFAULT NULL,
  `entryDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entryDescription` text NOT NULL,
  `entryType` varchar(30) NOT NULL,
  `entryDebitAccount` text NOT NULL,
  `entryDebitBalance` text NOT NULL,
  `entryCreditAccount` text NOT NULL,
  `entryCreditBalance` text NOT NULL,
  `entryStatus` int(1) NOT NULL DEFAULT '0',
  `entryStatusComment` text,
  `created_by` int(11) DEFAULT NULL,
  `entryCreateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `costCenterDebitId` text NOT NULL,
  `costCenterDebitAmount` text NOT NULL,
  `costCenterCreditId` text NOT NULL,
  `costCenterCreditAmount` text NOT NULL,
  `entryDebitDescription` text NOT NULL,
  `entryCreditDescription` text NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  `timePeriod` varchar(10) NOT NULL DEFAULT 'daily',
  `start_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `latest_recurring_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`entryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_notice_creditor
DROP TABLE IF EXISTS `sma_gl_notice_creditor`;
CREATE TABLE `sma_gl_notice_creditor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bond_no` varchar(20) NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` decimal(25,4) NOT NULL,
  `tax_rate` int(11) NOT NULL,
  `tax_amount` decimal(25,4) NOT NULL,
  `total_amount` decimal(25,4) NOT NULL,
  `paid_by` varchar(20) DEFAULT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `toAccountID` bigint(20) NOT NULL,
  `fromAccountID` bigint(20) NOT NULL,
  `receipt_name` varchar(255) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_by` int(11) unsigned NOT NULL,
  `attachment` varchar(100) DEFAULT NULL,
  `costCenterDebitId` text NOT NULL,
  `costCenterDebitAmount` text NOT NULL,
  `costCenterCreditId` text NOT NULL,
  `costCenterCreditAmount` text NOT NULL,
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(25,4) NOT NULL DEFAULT '1.0000',
  `equivalent_amount` decimal(25,4) NOT NULL,
  `equivalent_tax_amount` decimal(25,4) NOT NULL,
  `equivalent_total_amount` decimal(25,4) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `warehouse_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sma_gl_notice_creditor_FK` (`created_by`),
  KEY `sma_gl_notice_creditor_bond_no_IDX` (`bond_no`,`reference_no`,`toAccountID`,`fromAccountID`,`receipt_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_opening_balance
DROP TABLE IF EXISTS `sma_gl_opening_balance`;
CREATE TABLE `sma_gl_opening_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openingDate` datetime NOT NULL,
  `note` text NOT NULL,
  `userID` bigint(20) NOT NULL,
  `openingCreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(12,4) NOT NULL DEFAULT '1.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_opening_balance_items
DROP TABLE IF EXISTS `sma_gl_opening_balance_items`;
CREATE TABLE `sma_gl_opening_balance_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openingId` int(11) NOT NULL,
  `accountID` bigint(20) NOT NULL,
  `debit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(10,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost_center` varchar(50) DEFAULT NULL,
  `cost_center_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_payment_methods
DROP TABLE IF EXISTS `sma_gl_payment_methods`;
CREATE TABLE `sma_gl_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_name` varchar(250) NOT NULL,
  `payment_ename` varchar(250) NOT NULL,
  `commisson_perc` decimal(10,2) NOT NULL,
  `payment_account` int(11) NOT NULL,
  `commisson_account` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `payment_cc_id` int(11) NOT NULL DEFAULT '0',
  `payment_type` varchar(100) NOT NULL,
  `tax_rate` int(11) NOT NULL,
  `casher_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_receipts
DROP TABLE IF EXISTS `sma_gl_receipts`;
CREATE TABLE `sma_gl_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bond_no` varchar(20) NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` decimal(25,4) NOT NULL,
  `tax_rate` int(11) NOT NULL,
  `tax_amount` decimal(25,4) NOT NULL,
  `total_amount` decimal(25,4) NOT NULL,
  `paid_by` varchar(55) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `toAccountID` bigint(20) NOT NULL,
  `fromAccountID` bigint(20) NOT NULL,
  `receipt_name` varchar(255) DEFAULT NULL,
  `note` text,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(100) DEFAULT NULL,
  `costCenterDebitId` text NOT NULL,
  `costCenterDebitAmount` text NOT NULL,
  `costCenterCreditId` text NOT NULL,
  `costCenterCreditAmount` text NOT NULL,
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(25,4) NOT NULL DEFAULT '1.0000',
  `equivalent_amount` decimal(25,4) NOT NULL,
  `equivalent_tax_amount` decimal(25,4) NOT NULL,
  `equivalent_total_amount` decimal(25,4) NOT NULL,
  `entryDebitAccount` text,
  `entryDebitBalance` text,
  `entryCreditAccount` text,
  `entryCreditBalance` text,
  `toAccountText` varchar(500) DEFAULT NULL,
  `fromAccountText` varchar(1000) DEFAULT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  `entryDebitDescription` text,
  `entryCreditDescription` text,
  `equivalent_entryCreditBalance` text,
  `equivalent_entryDebitBalance` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: sma_gl_refunding_bonds
DROP TABLE IF EXISTS `sma_gl_refunding_bonds`;
CREATE TABLE `sma_gl_refunding_bonds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bond_no` varchar(20) NOT NULL,
  `reference_no` varchar(55) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` decimal(25,4) NOT NULL,
  `tax_rate` int(11) NOT NULL,
  `tax_amount` decimal(25,4) NOT NULL,
  `total_amount` decimal(25,4) NOT NULL,
  `paid_by` varchar(20) NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `cc_type` varchar(20) DEFAULT NULL,
  `toAccountID` bigint(20) NOT NULL,
  `fromAccountID` bigint(20) NOT NULL,
  `receipt_name` varchar(255) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `attachment` varchar(100) DEFAULT NULL,
  `costCenterDebitId` text NOT NULL,
  `costCenterDebitAmount` text NOT NULL,
  `costCenterCreditId` text NOT NULL,
  `costCenterCreditAmount` text NOT NULL,
  `currency_code` varchar(5) NOT NULL DEFAULT 'SR',
  `currency_rate` decimal(25,4) NOT NULL DEFAULT '1.0000',
  `equivalent_amount` decimal(25,4) NOT NULL,
  `equivalent_tax_amount` decimal(25,4) NOT NULL,
  `equivalent_total_amount` decimal(25,4) NOT NULL,
  `grpId` int(11) NOT NULL DEFAULT '1',
  `coId` int(11) NOT NULL DEFAULT '1',
  `brId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS=1;
