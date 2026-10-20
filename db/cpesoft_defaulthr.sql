-- Database Dump: cpesoft_defaulthr
-- Date: 2026-10-10 22:39:06

SET FOREIGN_KEY_CHECKS=0;


-- Table: advance_type
DROP TABLE IF EXISTS `advance_type`;
CREATE TABLE `advance_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `advance_type` VALUES ('1', 'قرض سنوي', 'Annual loan', '1');
INSERT INTO `advance_type` VALUES ('2', 'سلفة مؤقته ', 'سلفة مؤقته ', '1');
INSERT INTO `advance_type` VALUES ('3', 'سلفة مستديمة ', 'سلفة مستديمة ', '1');
INSERT INTO `advance_type` VALUES ('4', 'سلفة علاجيه ', 'سلفة علاجيه ', '1');

-- Table: allowance
DROP TABLE IF EXISTS `allowance`;
CREATE TABLE `allowance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `allowance_type` tinyint(4) NOT NULL,
  `allowance_value` float NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO `allowance` VALUES ('1', 'بدل مواصلات', 'Transportation allowance', '1', '10', '1');
INSERT INTO `allowance` VALUES ('2', 'بدل مواصلات', 'بدل', '1', '10', '1');
INSERT INTO `allowance` VALUES ('3', 'بدل معيشة ', 'بدل معيشة', '1', '10', '1');
INSERT INTO `allowance` VALUES ('4', 'بدل سكن ', 'House allowance', '1', '25', '1');
INSERT INTO `allowance` VALUES ('5', 'بدل مواصلات ', 'Transportation allowance', '2', '500', '1');
INSERT INTO `allowance` VALUES ('6', 'بدل مواصلات', 'Transportation allowance', '1', '10', '1');
INSERT INTO `allowance` VALUES ('7', 'بدل مامورية ', 'بدل مامورية ', '1', '10', '1');

-- Table: allowance_user
DROP TABLE IF EXISTS `allowance_user`;
CREATE TABLE `allowance_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `allowance_id` int(11) NOT NULL,
  `allowance_value` float NOT NULL,
  `allowance_type` tinyint(4) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `reservation` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `allowance_id` (`allowance_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `allowance_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allowance_user_ibfk_2` FOREIGN KEY (`allowance_id`) REFERENCES `allowance` (`id`) ON DELETE CASCADE,
  CONSTRAINT `allowance_user_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: announcement
DROP TABLE IF EXISTS `announcement`;
CREATE TABLE `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `send_email` tinyint(4) NOT NULL,
  `start_ads_date` date NOT NULL,
  `end_ads_date` date NOT NULL,
  `content` text NOT NULL,
  `branch_id` varchar(255) NOT NULL,
  `department_id` varchar(255) NOT NULL,
  `job_place_id` varchar(255) NOT NULL,
  `job_title_id` varchar(255) NOT NULL,
  `employee_type` tinyint(4) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `emps_exemption` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: auth_assignment
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: auth_item
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: auth_item_child
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: auth_rule
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: automatic_reminder
DROP TABLE IF EXISTS `automatic_reminder`;
CREATE TABLE `automatic_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activate_automatic_reminder` tinyint(4) NOT NULL,
  `reminder_lang` varchar(10) NOT NULL,
  `reminder_test_period_hrm` tinyint(4) NOT NULL,
  `reminder_test_period_dm` tinyint(4) NOT NULL,
  `reminder_identity_end` tinyint(4) NOT NULL,
  `reminder_passport_end` tinyint(4) NOT NULL,
  `reminder_med_insurance_end` tinyint(4) NOT NULL,
  `reminder_contract_end` tinyint(4) NOT NULL,
  `reminder_mail` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `automatic_reminder` VALUES ('1', '1', 'ar', '7', '4', '7', '7', '7', '7', '1');

-- Table: branch
DROP TABLE IF EXISTS `branch`;
CREATE TABLE `branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_place_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `administration` int(11) DEFAULT NULL,
  `description` text,
  `description_en` text,
  `lat` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `zoom` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `administration` (`administration`),
  KEY `branch_job_place_FK` (`job_place_id`),
  CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`administration`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `branch_job_place_FK` FOREIGN KEY (`job_place_id`) REFERENCES `job_place` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `branch` VALUES ('1', NULL, 'فرع جديد', 'فرع جديد', '1', 'فرع جديد', 'فرع جديد', '25.095832694428', '46.66255075', '8', '1');

-- Table: caching_type
DROP TABLE IF EXISTS `caching_type`;
CREATE TABLE `caching_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `caching_type` VALUES ('1', 'شيك', 'شيك', '1');
INSERT INTO `caching_type` VALUES ('2', 'كاش', 'Cash', '1');
INSERT INTO `caching_type` VALUES ('3', 'تحويل بنكي', 'Bank transfer', '1');

-- Table: calculating_attendance
DROP TABLE IF EXISTS `calculating_attendance`;
CREATE TABLE `calculating_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `finger_device_no` varchar(12) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `day` varchar(4) NOT NULL,
  `month` varchar(4) NOT NULL,
  `year` varchar(4) NOT NULL,
  `on_day` time DEFAULT NULL,
  `off_day` time DEFAULT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `late` time DEFAULT NULL,
  `early` time DEFAULT NULL,
  `working_time` time DEFAULT NULL,
  `allowed_early_time` time NOT NULL,
  `allowed_late_time` time NOT NULL,
  `allowed_early_end_time` time NOT NULL,
  `allowed_out_end_time` time NOT NULL,
  `count_extra_hours` time NOT NULL,
  `absence_duration` time DEFAULT NULL,
  `attendance_duration` time DEFAULT NULL,
  `type` varchar(10) NOT NULL,
  `employee_name` varchar(100) DEFAULT NULL,
  `is_manual` bit(1) DEFAULT b'0',
  `manual_emp_id` int(11) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `posted` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `calculating_attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: calculating_salary_setting
DROP TABLE IF EXISTS `calculating_salary_setting`;
CREATE TABLE `calculating_salary_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calculating_salary` tinyint(4) NOT NULL,
  `in_advance_calculating_salary` tinyint(4) NOT NULL,
  `in_advance_calculating_salary_day` tinyint(4) NOT NULL,
  `calculating_abscence_hour_value` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `calculating_salary_setting` VALUES ('1', '1', '1', '30', '1');

-- Table: city
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `city_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=utf8;

INSERT INTO `city` VALUES ('3', '1', 'أبها', 'Abha', '1');
INSERT INTO `city` VALUES ('4', '1', 'ابقيق', 'Abqaiq', '1');
INSERT INTO `city` VALUES ('5', '1', 'ابو عجرم', 'Abu Ajram', '1');
INSERT INTO `city` VALUES ('6', '1', 'أبو عريش', 'Abu Areish', '1');
INSERT INTO `city` VALUES ('7', '1', 'أبو دهينه', 'Ad Dahinah', '1');
INSERT INTO `city` VALUES ('8', '1', 'أبو ظبية', 'Ad Dubaiyah', '1');
INSERT INTO `city` VALUES ('9', '1', 'الداير', 'Addayer', '1');
INSERT INTO `city` VALUES ('10', '1', 'عظم', 'Adham', '1');
INSERT INTO `city` VALUES ('11', '1', 'عفيف', 'Afif', '1');
INSERT INTO `city` VALUES ('12', '1', 'الأفلاج', 'Aflaj', '1');
INSERT INTO `city` VALUES ('13', '1', 'أحد المسارحة', 'Ahad Masarha ', '1');
INSERT INTO `city` VALUES ('14', '1', 'أحد رفيدة', 'Ahad Rufaidah', '1');
INSERT INTO `city` VALUES ('15', '1', 'عين دار', 'Ain Dar', '1');
INSERT INTO `city` VALUES ('16', '1', 'العضيري', 'Al Adari', '1');
INSERT INTO `city` VALUES ('17', '1', 'العيس', 'Al Ais', '1');
INSERT INTO `city` VALUES ('18', '1', 'الأجفار', 'Al Ajfar', '1');
INSERT INTO `city` VALUES ('19', '1', 'العماره', 'Al Ammarah', '1');
INSERT INTO `city` VALUES ('20', '1', 'العارضة', 'Al Ardah', '1');
INSERT INTO `city` VALUES ('21', '1', 'العرجاء', 'Al Arja', '1');
INSERT INTO `city` VALUES ('22', '1', 'الأسياح', 'Al Asyah', '1');
INSERT INTO `city` VALUES ('23', '1', 'البديع', 'Al Bada', '1');
INSERT INTO `city` VALUES ('24', '1', 'البشاير', 'Al Bashayer', '1');
INSERT INTO `city` VALUES ('25', '1', 'البترا', 'Al Batra', '1');
INSERT INTO `city` VALUES ('26', '1', 'الباجدية', 'Al Bijadyah', '1');
INSERT INTO `city` VALUES ('27', '1', 'الدليمه', 'Al Dalemya', '1');
INSERT INTO `city` VALUES ('28', '1', 'الفواليق', 'Al Fuwaileq', '1');
INSERT INTO `city` VALUES ('29', '1', 'الحيط', 'Al hait', '1');
INSERT INTO `city` VALUES ('30', '1', 'الهيث', 'Al Haith', '1');
INSERT INTO `city` VALUES ('31', '1', 'الأحساء', 'Al Hassa', '1');
INSERT INTO `city` VALUES ('32', '1', 'الهياثم', 'Al Hayathem', '1');
INSERT INTO `city` VALUES ('33', '1', 'الحفيرة', 'Al Hufayyirah', '1');
INSERT INTO `city` VALUES ('34', '1', 'الحفيرة السفلى', 'Al Hulayfah As Sufla', '1');
INSERT INTO `city` VALUES ('35', '1', 'الخشيبي', 'Al Khishaybi', '1');
INSERT INTO `city` VALUES ('36', '1', 'الخيطة', 'Al Khitah', '1');
INSERT INTO `city` VALUES ('37', '1', 'اللقايط', 'Al Laqayit', '1');
INSERT INTO `city` VALUES ('38', '1', 'المدى', 'Al Mada', '1');
INSERT INTO `city` VALUES ('39', '1', 'المهد', 'Al Mahd', '1');
INSERT INTO `city` VALUES ('40', '1', 'المدرج', 'Al Midrij', '1');
INSERT INTO `city` VALUES ('41', '1', 'المويه', 'Al Moya', '1');
INSERT INTO `city` VALUES ('42', '1', 'القرين', 'Al Qarin', '1');
INSERT INTO `city` VALUES ('43', '1', 'العويقيلة', 'Al Uwayqilah', '1');
INSERT INTO `city` VALUES ('44', '1', 'الوسيطة', 'Al Wasayta', '1');
INSERT INTO `city` VALUES ('45', '1', 'الجش', 'Al-Jsh', '1');
INSERT INTO `city` VALUES ('46', '1', 'الهدى', 'Alhada', '1');
INSERT INTO `city` VALUES ('47', '1', 'النبهانية', 'Alnabhanya', '1');
INSERT INTO `city` VALUES ('48', '1', 'الرس', 'Alrass', '1');
INSERT INTO `city` VALUES ('49', '1', 'النفيعة', 'An Nafiah', '1');
INSERT INTO `city` VALUES ('50', '1', 'النقره', 'An Nuqrah', '1');
INSERT INTO `city` VALUES ('51', '1', 'عنك', 'Anak', '1');
INSERT INTO `city` VALUES ('52', '1', 'عقيق', 'Aqiq', '1');
INSERT INTO `city` VALUES ('53', '1', 'الرديفه', 'Ar Radifah', '1');
INSERT INTO `city` VALUES ('54', '1', 'الرفايعة', 'Ar Rafi\'ah', '1');
INSERT INTO `city` VALUES ('55', '1', 'الرشاوية', 'Ar Rishawiyah', '1');
INSERT INTO `city` VALUES ('56', '1', 'عرعر', 'Arar', '1');
INSERT INTO `city` VALUES ('57', '1', 'الأرطاوية', 'Artawiah', '1');
INSERT INTO `city` VALUES ('58', '1', 'السليمانية', 'As Sulaimaniyah', '1');
INSERT INTO `city` VALUES ('59', '1', 'الصليبية', 'As Sulubiayh', '1');
INSERT INTO `city` VALUES ('60', '1', 'عصفان', 'Asfan', '1');
INSERT INTO `city` VALUES ('61', '1', 'الشراع', 'Ash Shaara', '1');
INSERT INTO `city` VALUES ('62', '1', 'الشمالي', 'Ash Shamli', '1');
INSERT INTO `city` VALUES ('63', '1', 'الشنانة', 'Ash Shananah', '1');
INSERT INTO `city` VALUES ('64', '1', 'الشميسية', 'Ash Shimasiyah', '1');
INSERT INTO `city` VALUES ('65', '1', 'لشقيق', 'Ash Shuqaiq', '1');
INSERT INTO `city` VALUES ('66', '1', 'الطوير', 'At Tuwayr', '1');
INSERT INTO `city` VALUES ('67', '1', 'الثيبية', 'Ath Thybiyah', '1');
INSERT INTO `city` VALUES ('68', '1', 'العوامية', 'Awamiah', '1');
INSERT INTO `city` VALUES ('69', '1', 'عين فهيد', 'Ayn Fuhayd  ', '1');
INSERT INTO `city` VALUES ('70', '1', 'بدايع', 'Badaya', '1');
INSERT INTO `city` VALUES ('71', '1', 'بدر', 'Bader       ', '1');
INSERT INTO `city` VALUES ('72', '1', 'بدر الجنوب', 'Badr Al Janoub', '1');
INSERT INTO `city` VALUES ('73', '1', 'الباحة', 'Baha', '1');
INSERT INTO `city` VALUES ('74', '1', 'البحره', 'Bahara', '1');
INSERT INTO `city` VALUES ('75', '1', 'بحرة المجود', 'Bahrat Al Moujoud', '1');
INSERT INTO `city` VALUES ('76', '1', 'بلحمر', 'Balahmar', '1');
INSERT INTO `city` VALUES ('77', '1', 'بلسمر', 'Balasmar', '1');
INSERT INTO `city` VALUES ('78', '1', 'بلقرن', 'Balqarn', '1');
INSERT INTO `city` VALUES ('79', '1', 'باقة الشرقية', 'Baqa Ash Sharqiyah', '1');
INSERT INTO `city` VALUES ('80', '1', 'البقعاء', 'Baqaa', '1');
INSERT INTO `city` VALUES ('81', '1', 'ابقيق', 'Baqiq', '1');
INSERT INTO `city` VALUES ('82', '1', 'بارق', 'Bareq', '1');
INSERT INTO `city` VALUES ('83', '1', 'البطحاء', 'Batha', '1');
INSERT INTO `city` VALUES ('84', '1', 'بلجرشي', 'Biljurashi', '1');
INSERT INTO `city` VALUES ('85', '1', 'بيرق', 'Birk', '1');
INSERT INTO `city` VALUES ('86', '1', 'بيش', 'Bish ', '1');
INSERT INTO `city` VALUES ('87', '1', 'بيشة', 'Bisha', '1');
INSERT INTO `city` VALUES ('88', '1', 'البكيرية', 'Bukeiriah', '1');
INSERT INTO `city` VALUES ('89', '1', 'بريدة', 'Buraidah', '1');
INSERT INTO `city` VALUES ('90', '1', 'الدلم', 'Daelim', '1');
INSERT INTO `city` VALUES ('91', '1', 'ضماد', 'Damad', '1');
INSERT INTO `city` VALUES ('92', '1', 'الدمام', 'Dammam', '1');
INSERT INTO `city` VALUES ('93', '1', 'درب', 'Darb', '1');
INSERT INTO `city` VALUES ('94', '1', 'الدرعية', 'Dariyah', '1');
INSERT INTO `city` VALUES ('95', '1', 'الدوادمي', 'Dawadmi', '1');
INSERT INTO `city` VALUES ('96', '1', 'ديراب', 'Deraab', '1');
INSERT INTO `city` VALUES ('97', '1', 'الدرعية', 'Dere\'Iyeh', '1');
INSERT INTO `city` VALUES ('98', '1', 'ذهبان', 'Dhahban', '1');
INSERT INTO `city` VALUES ('99', '1', 'الظهران', 'Dhahran', '1');
INSERT INTO `city` VALUES ('100', '1', 'ظهران الجنوب', 'Dhahran Al Janoob', '1');
INSERT INTO `city` VALUES ('101', '1', 'ضرمه', 'Dhurma', '1');
INSERT INTO `city` VALUES ('102', '1', 'دومة الجندل', 'Domat Al Jandal', '1');
INSERT INTO `city` VALUES ('103', '1', 'ضبا', 'Duba', '1');
INSERT INTO `city` VALUES ('104', '1', 'دخنه', 'Duhknah', '1');
INSERT INTO `city` VALUES ('105', '1', 'دلي رشيد', 'Dulay Rashid', '1');
INSERT INTO `city` VALUES ('106', '1', 'فرسان', 'Farasan', '1');
INSERT INTO `city` VALUES ('107', '1', 'غزاله', 'Ghazalah', '1');
INSERT INTO `city` VALUES ('108', '1', 'غطي', 'Ghtai', '1');
INSERT INTO `city` VALUES ('109', '1', 'جلوى', 'Gilwa', '1');
INSERT INTO `city` VALUES ('110', '1', 'جيزان', 'Gizan', '1');
INSERT INTO `city` VALUES ('111', '1', 'الحديثة', 'Hadeethah', '1');
INSERT INTO `city` VALUES ('112', '1', 'حفر الباطن', 'Hafer Al Batin', '1');
INSERT INTO `city` VALUES ('113', '1', 'حائل', 'Hail', '1');
INSERT INTO `city` VALUES ('114', '1', 'هجره', 'Hajrah', '1');
INSERT INTO `city` VALUES ('115', '1', 'حالة عمار', 'Halat Ammar', '1');
INSERT INTO `city` VALUES ('116', '1', 'هالي', 'Hali', '1');
INSERT INTO `city` VALUES ('117', '1', 'حقل', 'Haqil', '1');
INSERT INTO `city` VALUES ('118', '1', 'حرض', 'Harad', '1');
INSERT INTO `city` VALUES ('119', '1', 'حراجه', 'Harajah', '1');
INSERT INTO `city` VALUES ('120', '1', 'الحريق', 'Hareeq', '1');
INSERT INTO `city` VALUES ('121', '1', 'الحوية الطايف', 'Hawea/Taif', '1');
INSERT INTO `city` VALUES ('122', '1', 'الحوية ', 'Haweyah', '1');
INSERT INTO `city` VALUES ('123', '1', 'حوطة بني تميم', 'Hawtat Bani Tamim', '1');
INSERT INTO `city` VALUES ('124', '1', 'حزم الجلاميد', 'Hazm Al Jalamid', '1');
INSERT INTO `city` VALUES ('125', '1', 'هديب', 'Hedeb', '1');
INSERT INTO `city` VALUES ('126', '1', 'الحناكية', 'Hinakeya', '1');
INSERT INTO `city` VALUES ('127', '1', 'الهفوف', 'Hofuf', '1');
INSERT INTO `city` VALUES ('128', '1', 'حريمال', 'Horaimal', '1');
INSERT INTO `city` VALUES ('129', '1', 'حوطة سدير', 'Hotat Sudair', '1');
INSERT INTO `city` VALUES ('130', '1', 'حبونا', 'Hubuna', '1');
INSERT INTO `city` VALUES ('131', '1', 'حريملاء', 'Huraymala', '1');
INSERT INTO `city` VALUES ('132', '1', 'الجعرانه', 'Ja\'Araneh', '1');
INSERT INTO `city` VALUES ('133', '1', 'الجفر', 'Jafar', '1');
INSERT INTO `city` VALUES ('134', '1', 'جلاجل', 'Jalajel', '1');
INSERT INTO `city` VALUES ('135', '1', 'جدة', 'Jeddah', '1');
INSERT INTO `city` VALUES ('136', '1', 'الجوف', 'Jouf', '1');
INSERT INTO `city` VALUES ('137', '1', 'الجبيل', 'Jubail', '1');
INSERT INTO `city` VALUES ('138', '1', 'الجموم', 'Jumum', '1');
INSERT INTO `city` VALUES ('139', '1', 'خاله', 'Kahlah', '1');
INSERT INTO `city` VALUES ('140', '1', 'كرا', 'Kara', '1');
INSERT INTO `city` VALUES ('141', '1', 'قريعة', 'Kara\'A', '1');
INSERT INTO `city` VALUES ('142', '1', 'خربوص', 'Karboos', '1');
INSERT INTO `city` VALUES ('143', '1', 'الخفجي', 'Khafji', '1');
INSERT INTO `city` VALUES ('144', '1', 'خيبر', 'Khaibar', '1');
INSERT INTO `city` VALUES ('145', '1', 'خيران', 'Khairan', '1');
INSERT INTO `city` VALUES ('146', '1', 'الخماسين', 'Khamaseen', '1');
INSERT INTO `city` VALUES ('147', '1', 'خميس مشيط', 'Khamis Mushait', '1');
INSERT INTO `city` VALUES ('148', '1', 'الخرج', 'Kharj', '1');
INSERT INTO `city` VALUES ('149', '1', 'خصاويه', 'Khasawyah', '1');
INSERT INTO `city` VALUES ('150', '1', 'الخبر', 'Khobar', '1');
INSERT INTO `city` VALUES ('151', '1', 'الخضرية', 'Khodaria', '1');
INSERT INTO `city` VALUES ('152', '1', 'خليص', 'Khulais', '1');
INSERT INTO `city` VALUES ('153', '1', 'الخرمة', 'Khurma', '1');
INSERT INTO `city` VALUES ('154', '1', 'مدينة الملك خالد العسكرية', 'King Khalid Military City', '1');
INSERT INTO `city` VALUES ('155', '1', 'خباده', 'Kubadah', '1');
INSERT INTO `city` VALUES ('156', '1', 'الليث', 'Laith', '1');
INSERT INTO `city` VALUES ('157', '1', 'ليلى', 'Layla', '1');
INSERT INTO `city` VALUES ('158', '1', 'المدينة المنورة', 'Madinah', '1');
INSERT INTO `city` VALUES ('159', '1', 'مهد الذهب', 'Mahad Al Dahab', '1');
INSERT INTO `city` VALUES ('160', '1', 'المجارده', 'Majarda', '1');
INSERT INTO `city` VALUES ('161', '1', 'المجمعة', 'Majma', '1');
INSERT INTO `city` VALUES ('162', '1', 'مكة المكرمة', 'Makkah', '1');
INSERT INTO `city` VALUES ('163', '1', 'المندق', 'Mandak', '1');
INSERT INTO `city` VALUES ('164', '1', 'مستوره', 'Mastura', '1');
INSERT INTO `city` VALUES ('165', '1', 'موقق', 'Mawqaq', '1');
INSERT INTO `city` VALUES ('166', '1', 'المخواه', 'Mikhwa', '1');
INSERT INTO `city` VALUES ('167', '1', 'محايل عسير', 'Mohayel Aseer', '1');
INSERT INTO `city` VALUES ('168', '1', 'موقاق', 'Moqaq', '1');
INSERT INTO `city` VALUES ('169', '1', 'مرات', 'Mrat', '1');
INSERT INTO `city` VALUES ('170', '1', 'المبرز', 'Mubaraz', '1');
INSERT INTO `city` VALUES ('171', '1', 'المبيض', 'Mubayid', '1');
INSERT INTO `city` VALUES ('172', '1', 'مليجة', 'Mulaija', '1');
INSERT INTO `city` VALUES ('173', '1', 'مليح', 'Mulayh', '1');
INSERT INTO `city` VALUES ('174', '1', 'منيفات القايد', 'Munifat Al Qaid', '1');
INSERT INTO `city` VALUES ('175', '1', 'مطاليف', 'Muthaleif', '1');
INSERT INTO `city` VALUES ('176', '1', 'المزاحمية', 'Muzahmiah', '1');
INSERT INTO `city` VALUES ('177', '1', 'المذنب', 'Muzneb', '1');
INSERT INTO `city` VALUES ('178', '1', 'النابيه', 'Nabiya', '1');
INSERT INTO `city` VALUES ('179', '1', 'نجران', 'Najran', '1');
INSERT INTO `city` VALUES ('180', '1', 'النماص', 'Namas', '1');
INSERT INTO `city` VALUES ('181', '1', 'نمره', 'Nimra', '1');
INSERT INTO `city` VALUES ('182', '1', 'نصاب', 'Nisab', '1');
INSERT INTO `city` VALUES ('183', '1', 'النويرية', 'Noweirieh', '1');
INSERT INTO `city` VALUES ('184', '1', 'نواريه', 'Nwariah', '1');
INSERT INTO `city` VALUES ('185', '1', 'الأوجام', 'Ojam', '1');
INSERT INTO `city` VALUES ('186', '1', 'عنيزة', 'Onaiza', '1');
INSERT INTO `city` VALUES ('187', '1', 'العثمانية', 'Othmanyah', '1');
INSERT INTO `city` VALUES ('188', '1', 'العلا', 'Oula', '1');
INSERT INTO `city` VALUES ('189', '1', 'العوينه', 'Oyaynah', '1');
INSERT INTO `city` VALUES ('190', '1', 'عيون الجوا', 'Oyoon Al Jawa', '1');
INSERT INTO `city` VALUES ('191', '1', 'القحمة', 'Qahmah', '1');
INSERT INTO `city` VALUES ('192', '1', 'القاره', 'Qarah', '1');
INSERT INTO `city` VALUES ('193', '1', 'القرية العليا', 'Qariya Al Olaya', '1');
INSERT INTO `city` VALUES ('194', '1', 'قصب', 'Qasab       ', '1');
INSERT INTO `city` VALUES ('195', '1', 'القصيم', 'Qassim', '1');
INSERT INTO `city` VALUES ('196', '1', 'القطيف', 'Qatif', '1');
INSERT INTO `city` VALUES ('197', '1', 'القيصومة', 'Qaysoomah', '1');
INSERT INTO `city` VALUES ('198', '1', 'القبه', 'Qbah', '1');
INSERT INTO `city` VALUES ('199', '1', 'القوز', 'Qouz', '1');
INSERT INTO `city` VALUES ('200', '1', 'قفار', 'Qufar', '1');
INSERT INTO `city` VALUES ('201', '1', 'القنفذة', 'Qunfudah', '1');
INSERT INTO `city` VALUES ('202', '1', 'القريات', 'Qurayat', '1');
INSERT INTO `city` VALUES ('203', '1', 'القصيبة', 'Qusayba', '1');
INSERT INTO `city` VALUES ('204', '1', 'القويله', 'Quwei\'Ieh', '1');
INSERT INTO `city` VALUES ('205', '1', 'رابغ', 'Rabigh', '1');
INSERT INTO `city` VALUES ('206', '1', 'رفحاء', 'Rafha', '1');
INSERT INTO `city` VALUES ('207', '1', 'رحيمة', 'Rahima', '1');
INSERT INTO `city` VALUES ('208', '1', 'رنيه', 'Rania', '1');
INSERT INTO `city` VALUES ('209', '1', 'راس الخير', 'Ras Al Kheir', '1');
INSERT INTO `city` VALUES ('210', '1', 'راس تنورة', 'Ras Tanura', '1');
INSERT INTO `city` VALUES ('211', '1', 'روضة حباس', 'Rawdat Habbas', '1');
INSERT INTO `city` VALUES ('212', '1', 'رجال ألمع', 'Rejal Alma\'A', '1');
INSERT INTO `city` VALUES ('213', '1', 'رماح', 'Remah', '1');
INSERT INTO `city` VALUES ('214', '1', 'الرياض', 'Riyadh', '1');
INSERT INTO `city` VALUES ('215', '1', 'رياض الخبراء', 'Riyadh Al Khabra', '1');
INSERT INTO `city` VALUES ('216', '1', 'روضة سدير', 'Rowdat Sodair', '1');
INSERT INTO `city` VALUES ('217', '1', 'رفايع الجمش', 'Rvaya Aljamsh', '1');
INSERT INTO `city` VALUES ('218', '1', 'الرويضه', 'Rwaydah', '1');
INSERT INTO `city` VALUES ('219', '1', 'سبت العلايا', 'Sabt El Alaya', '1');
INSERT INTO `city` VALUES ('220', '1', 'صبيا', 'Sabya', '1');
INSERT INTO `city` VALUES ('221', '1', 'صديان', 'Sadyan', '1');
INSERT INTO `city` VALUES ('222', '1', 'السفانية', 'Safanyah', '1');
INSERT INTO `city` VALUES ('223', '1', 'صفوى', 'Safwa', '1');
INSERT INTO `city` VALUES ('224', '1', 'السحنه', 'Sahna', '1');
INSERT INTO `city` VALUES ('225', '1', 'ساجر', 'Sajir', '1');
INSERT INTO `city` VALUES ('226', '1', 'سكاكا', 'Sakaka', '1');
INSERT INTO `city` VALUES ('227', '1', 'صلبوخ', 'Salbookh', '1');
INSERT INTO `city` VALUES ('228', '1', 'سلوى', 'Salwa', '1');
INSERT INTO `city` VALUES ('229', '1', 'صامته', 'Samtah ', '1');
INSERT INTO `city` VALUES ('230', '1', 'سقف', 'Saqf', '1');
INSERT INTO `city` VALUES ('231', '1', 'صرار', 'Sarar', '1');
INSERT INTO `city` VALUES ('232', '1', 'سرات عبيده', 'Sarat Obeida', '1');
INSERT INTO `city` VALUES ('233', '1', 'ساتورب', 'Satorp', '1');
INSERT INTO `city` VALUES ('234', '1', 'سيهات', 'Seihat', '1');
INSERT INTO `city` VALUES ('235', '1', 'شقراء', 'Shaqra', '1');
INSERT INTO `city` VALUES ('236', '1', 'صحاري', 'Shari', '1');
INSERT INTO `city` VALUES ('237', '1', 'شروره', 'Sharourah', '1');
INSERT INTO `city` VALUES ('238', '1', 'الشفا', 'Shefa\'A', '1');
INSERT INTO `city` VALUES ('239', '1', 'شنانه', 'Shinanh', '1');
INSERT INTO `city` VALUES ('240', '1', 'الصعيبه', 'Shoaiba', '1');
INSERT INTO `city` VALUES ('241', '1', 'الشرايع', 'Shraie\'E', '1');
INSERT INTO `city` VALUES ('242', '1', 'الشميسي ', 'Shumeisi', '1');
INSERT INTO `city` VALUES ('243', '1', 'ساير', 'Siir', '1');
INSERT INTO `city` VALUES ('244', '1', 'السميرة', 'Simira', '1');
INSERT INTO `city` VALUES ('245', '1', 'الصبيخه', 'Subheka', '1');
INSERT INTO `city` VALUES ('246', '1', 'السليل', 'Sulaiyl', '1');
INSERT INTO `city` VALUES ('247', '1', 'صوير', 'Suwayr', '1');
INSERT INTO `city` VALUES ('248', '1', 'طبرجل', 'Tabrjal', '1');
INSERT INTO `city` VALUES ('249', '1', 'تبوك', 'Tabuk', '1');
INSERT INTO `city` VALUES ('250', '1', 'طيبة', 'Taiba', '1');
INSERT INTO `city` VALUES ('251', '1', 'الطائف', 'Taif', '1');
INSERT INTO `city` VALUES ('252', '1', 'تندا', 'Tanda', '1');
INSERT INTO `city` VALUES ('253', '1', 'تنجيب', 'Tanjeeb', '1');
INSERT INTO `city` VALUES ('254', '1', 'تنوما', 'Tanuma', '1');
INSERT INTO `city` VALUES ('255', '1', 'تنومه', 'Tanumah', '1');
INSERT INTO `city` VALUES ('256', '1', 'تاروت', 'Tarut', '1');
INSERT INTO `city` VALUES ('257', '1', 'تثليت', 'Tatleeth', '1');
INSERT INTO `city` VALUES ('258', '1', 'تيماء', 'Tayma       ', '1');
INSERT INTO `city` VALUES ('259', '1', 'طبراق', 'Tebrak', '1');
INSERT INTO `city` VALUES ('260', '1', 'ظبية', 'Thabya', '1');
INSERT INTO `city` VALUES ('261', '1', 'ثادق', 'Thadek', '1');
INSERT INTO `city` VALUES ('262', '1', 'ثرمده', 'Tharmada', '1');
INSERT INTO `city` VALUES ('263', '1', 'طيبه', 'Thebea', '1');
INSERT INTO `city` VALUES ('264', '1', 'ثمير', 'Thumair     ', '1');
INSERT INTO `city` VALUES ('265', '1', 'الثقبة', 'Thuqba', '1');
INSERT INTO `city` VALUES ('266', '1', 'الطوال', 'Towal', '1');
INSERT INTO `city` VALUES ('267', '1', 'تريب', 'Turaib', '1');
INSERT INTO `city` VALUES ('268', '1', 'طريف', 'Turaif', '1');
INSERT INTO `city` VALUES ('269', '1', 'تربه', 'Turba', '1');
INSERT INTO `city` VALUES ('270', '1', 'العضيلية', 'Udhaliyah', '1');
INSERT INTO `city` VALUES ('271', '1', 'ام الجماجم', 'Um Aljamajim', '1');
INSERT INTO `city` VALUES ('272', '1', 'أملج', 'Umluj', '1');
INSERT INTO `city` VALUES ('273', '1', 'عقلة الصقور', 'Uqlat Al Suqur', '1');
INSERT INTO `city` VALUES ('274', '1', 'أشيقر', 'Ushayqir', '1');
INSERT INTO `city` VALUES ('275', '1', 'العيون', 'Uyun', '1');
INSERT INTO `city` VALUES ('276', '1', 'وداي عين', 'Wadeien', '1');
INSERT INTO `city` VALUES ('277', '1', 'وادي بن حسبان', 'Wadi Bin Hasbal', '1');
INSERT INTO `city` VALUES ('278', '1', 'وادي الدواسر', 'Wadi El Dwaser', '1');
INSERT INTO `city` VALUES ('279', '1', 'وادي فاطمة', 'Wadi Fatmah', '1');
INSERT INTO `city` VALUES ('280', '1', 'الوجه', 'Wajeh (Al Wajh)', '1');
INSERT INTO `city` VALUES ('281', '1', 'ينبع', 'Yanbu', '1');
INSERT INTO `city` VALUES ('282', '1', 'ينبع البحر', 'Yanbu Al Baher', '1');
INSERT INTO `city` VALUES ('283', '1', 'ينبع النخيل', 'Yanbu Nakhil', '1');
INSERT INTO `city` VALUES ('284', '1', 'ذهبان', 'Zahban ', '1');
INSERT INTO `city` VALUES ('285', '1', 'ظلم', 'Zallum', '1');
INSERT INTO `city` VALUES ('286', '1', 'الزلفي', 'Zulfi', '1');

-- Table: country
DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `state_key` varchar(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `country` VALUES ('1', 'السعودية', 'Saudi Arabia', '00966', '1');

-- Table: currency
DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `symbol_native` varchar(20) NOT NULL,
  `icon_symbol_native` text,
  `sale_price` float NOT NULL,
  `buy_price` float NOT NULL,
  `updated` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `currency` VALUES ('1', 'USA', 'US Dollar', '$', '$', NULL, '3.75', '3.75', '1', '1');
INSERT INTO `currency` VALUES ('2', 'الريال السعودي', 'الريال السعودي', 'SAR ', 'ريال', 'uploads/a8e25b526ec74a9867427484f655f463.png', '1', '1', '1', '1');

-- Table: custody_type
DROP TABLE IF EXISTS `custody_type`;
CREATE TABLE `custody_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `custody_type` VALUES ('1', 'نوع عهده', 'نوع عهده', '1');
INSERT INTO `custody_type` VALUES ('2', 'عهده مؤقته ', 'عهده مؤقته ', '1');
INSERT INTO `custody_type` VALUES ('3', 'عهدة مستديمة ', 'عهدة مستديمة ', '1');

-- Table: custom_setting
DROP TABLE IF EXISTS `custom_setting`;
CREATE TABLE `custom_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `available_permissions_hours_full_type` float NOT NULL,
  `available_permissions_hours_part_type` float NOT NULL,
  `available_permissions_hours_type` tinyint(4) NOT NULL,
  `send_overtake_permissions_app` tinyint(4) NOT NULL,
  `limit_work_task_type_permissions_app` tinyint(4) NOT NULL,
  `repeat_app_handling` tinyint(4) NOT NULL,
  `auto_approve_order` tinyint(4) NOT NULL,
  `employees_embarkation_type` tinyint(4) NOT NULL,
  `leave_day` float NOT NULL,
  `activate_link_type` tinyint(4) NOT NULL,
  `transfer_app_handling` tinyint(4) NOT NULL,
  `new_employee_embarkation_active` tinyint(4) NOT NULL,
  `new_employee_period` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `custom_setting` VALUES ('1', '8', '6', '2', '1', '1', '1', '0', '1', '21', '1', '1', '1', '90');

-- Table: decision_list
DROP TABLE IF EXISTS `decision_list`;
CREATE TABLE `decision_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `decision_list` VALUES ('1', 'المدير التنفيذي', 'Executive Manager', 'القيام بأداء جميع المهمات و المسئوليات المنوطة بالمدير التنفيذي ، و المشاركة الفعالة فى تحديد و صياغة الأهداف، و كذلك تخطيط و تنظيم سير العمل بالمنشأة بما يضمن تحقيق الأهداف المحددة', 'To perform all the tasks and responsibilities of the Executive manager , and active participation in the identification and formulation of objectives, as well as planning and organizing workflow facility to ensure the achievement of the targets set', '1');

-- Table: deduction_type
DROP TABLE IF EXISTS `deduction_type`;
CREATE TABLE `deduction_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `deduction_type` tinyint(4) NOT NULL,
  `percentage_type` tinyint(4) DEFAULT NULL,
  `deduction_value` float NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `deduction_type` VALUES ('1', 'تأمين طبي	', 'تأمين طبي	', '2', NULL, '100', '0');
INSERT INTO `deduction_type` VALUES ('2', 'غياب', 'غياب', '1', '3', '300', '1');
INSERT INTO `deduction_type` VALUES ('3', 'سلفة', 'سلفة', '2', '1', '20', '0');
INSERT INTO `deduction_type` VALUES ('4', 'مخالفات ', 'مخالفات ', '2', '1', '20', '1');
INSERT INTO `deduction_type` VALUES ('5', 'تامينات ', 'تامينات', '1', '4', '100', '1');
INSERT INTO `deduction_type` VALUES ('6', 'اقتطاع', 'اقتطاع', '2', '1', '500', '1');

-- Table: department
DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `manager` int(11) DEFAULT NULL,
  `alternate_manager` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  KEY `manager` (`manager`),
  KEY `alternate_manager` (`alternate_manager`),
  CONSTRAINT `department_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`) ON DELETE CASCADE,
  CONSTRAINT `department_ibfk_2` FOREIGN KEY (`manager`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `department_ibfk_3` FOREIGN KEY (`alternate_manager`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `department` VALUES ('1', 'ادارة جديدة', 'ادارة جديدة', '1', '1', '1', '5', '5', '1');

-- Table: dismissal_setting
DROP TABLE IF EXISTS `dismissal_setting`;
CREATE TABLE `dismissal_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text_end` text NOT NULL,
  `text_default` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `text_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `text_default_en` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `dismissal_setting` VALUES ('1', '<div style=\"background-color:#fff;direction: rtl;\">\r\n<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"direction:ltr; width:100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"text-align:left; width:80%\"><img alt=\"{logo}\" src=\"{logo}\" style=\"float:left; height:125px; width:112px\" /></td>\r\n			<td style=\"width:20%\"><span style=\"color:#6b6b6b\">خطاب إنهاء الخدمه<br />\r\n			بشرية-ن-12-01 </span></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<div style=\"width:300px;float: left;\">\r\n<h5 style=\"text-align:center\">بيانات الموظف</h5>\r\n\r\n<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td style=\"background-color:#6b6b6b91; width:30%\">الاسم:</td>\r\n			<td style=\"width:70%\">{employee_name}</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"background-color:#6b6b6b91; width:30%\">الوظيفية:</td>\r\n			<td style=\"width:70%\">{job_title}</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"background-color:#6b6b6b91; width:30%\">الرقم الوظيفي:</td>\r\n			<td style=\"width:70%\">{emp_job_id}</td>\r\n		</tr>\r\n		<tr>\r\n			<td style=\"background-color:#6b6b6b91; width:30%\">تاريخ التعيين:</td>\r\n			<td style=\"width:70%\">{hire_date}</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>\r\n&nbsp;\r\n\r\n<div style=\"width:100%;\">&nbsp;</div>\r\n<br />\r\n<br />\r\n&nbsp;\r\n<div style=\"margin-left: 20px;margin-right: 20px;padding:2px 5px 2px 5px;font-weight: bold;border:1px solid #6b6b6b5c;width:100%;padding-right:40px;padding-left:40px;border-radius: 14px;padding-top:39px;text-align: center;padding-bottom: 39px;overflow: hidden;\"><br />\r\nسعادة / {employee_name} حفظه الله<br />\r\n<br />\r\nالسلام عليكم ورحمة الله وبركاته وبعد<br />\r\n<br />\r\nيؤسفنا أن نبلغكم بإنهاء خدماتكم بالشركة حيث يعتبر يوم {dismissal_day} الموافق {dismissal_date} هو آخر يوم عمل لكم.<br />\r\n<br />\r\nوذلك بسبب: عدم الرغبة في تجديد التعاقد. استقالتكم العارض الصحى الذى أصابكم وطبقا لنظام العمل السعودي انخفاض مستوى الأداء خلال فترة التجربة ظروف وطاقات العمل فى الفترة القادمة متمنين لكم التوفيق والسداد في حياتكم العملية.<br />\r\n<br />\r\nالمدير العام ............................ مدير الموارد البشرية .......................<br />\r\n<br />\r\n<br />\r\n<br />\r\n&nbsp;</div>\r\n&nbsp;\r\n\r\n<div style=\"background-color:#6b6b6b91;margin-left: 20px;margin-right: 20px;padding:2px 5px 2px 5px;font-weight: bold;border:1px solid #6b6b6b5c;width:100%;padding-right:40px;padding-left:40px;border-radius: 14px;padding-top:7px;text-align: start;padding-bottom: 7px;\">رقم الاصدار 0/2 تاريخ الاصدار 2016/01/01 م</div>\r\n</div>\r\n', '', 'نموذج خطاب الاقالة', 'نموذج خطاب الاقالة', '<p>نموذج خطاب الاقالة</p>\r\n', '0', '');
INSERT INTO `dismissal_setting` VALUES ('2', '<p>الموضوع : خطاب اقالة&nbsp;</p>\r\n\r\n<p>الى : ........................................................................................... المحترم&nbsp;</p>\r\n\r\n<p>بالاشارة الى الموضوع اعلاه تمت اقالتك من منصبك الحالي وعليه يرجي مقابلة الحسابات وادارة الموارد البشرية للأهمية&nbsp;</p>\r\n', '', 'نموذج خطاب اقالة ', 'نموذج خطاب اقالة ', '', '1', '');
INSERT INTO `dismissal_setting` VALUES ('3', '<p>بسم الله&nbsp;</p>\r\n', '', 'خطاب', '2', '', '0', '');

-- Table: division
DROP TABLE IF EXISTS `division`;
CREATE TABLE `division` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `employee` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `unit_id` (`unit_id`),
  KEY `employee` (`employee`),
  CONSTRAINT `division_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE,
  CONSTRAINT `division_ibfk_2` FOREIGN KEY (`employee`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `division` VALUES ('1', 'شعبه جديدة', 'شعبه جديدة', '1', '1', 'شعبه جديدة', 'شعبه جديدة', '1');

-- Table: documentation
DROP TABLE IF EXISTS `documentation`;
CREATE TABLE `documentation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '1',
  `expired_at` date DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: documentation_category
DROP TABLE IF EXISTS `documentation_category`;
CREATE TABLE `documentation_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `documentation_category` VALUES ('1', 'نموذج طلب مباشره عمل ', 'مباشره عمل ', '2', '1');

-- Table: email_message
DROP TABLE IF EXISTS `email_message`;
CREATE TABLE `email_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `text_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: emp_allowances
DROP TABLE IF EXISTS `emp_allowances`;
CREATE TABLE `emp_allowances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `allowance_id` int(11) NOT NULL,
  `allowance_value` float NOT NULL,
  `allowance_type` tinyint(4) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `emp_id` (`emp_id`),
  KEY `allowance_id` (`allowance_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: emp_deductions
DROP TABLE IF EXISTS `emp_deductions`;
CREATE TABLE `emp_deductions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `deduction_value` float NOT NULL,
  `deduction_type` tinyint(4) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `emp_id` (`emp_id`),
  KEY `deduction_id` (`deduction_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: employee
DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `employment_id` varchar(30) NOT NULL,
  `ta_employee_id` varchar(250) DEFAULT NULL,
  `full_name_ar` varchar(255) NOT NULL,
  `full_name_en` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `date_of_birth_hijry` varchar(100) NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nationality` tinyint(4) NOT NULL,
  `present_address` varchar(255) NOT NULL,
  `education` varchar(255) NOT NULL,
  `education_file` varchar(255) NOT NULL,
  `passport_number` varchar(200) NOT NULL,
  `passport_end` date DEFAULT NULL,
  `identity_no` varchar(100) NOT NULL,
  `identity_end` date NOT NULL,
  `work_license_no` varchar(100) NOT NULL,
  `finger_device_no` varchar(12) DEFAULT NULL,
  `work_license_date` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `maratial_status` tinyint(4) NOT NULL,
  `identity_photo` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `contract_photo` varchar(255) DEFAULT NULL,
  `other_documentation` varchar(255) DEFAULT NULL,
  `available_requests_for_employee` varchar(255) NOT NULL,
  `social_insurance` tinyint(4) NOT NULL,
  `social_insurance_type` tinyint(4) DEFAULT '0',
  `social_insurance_no` varchar(100) NOT NULL,
  `social_insurance_date` date DEFAULT NULL,
  `social_insurance_date_hijry` varchar(200) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `employee_type` tinyint(4) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `departement_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `job_place_id` int(11) NOT NULL,
  `direct_manager_id` int(11) NOT NULL,
  `employee_category_id` int(11) NOT NULL,
  `joining_date` date NOT NULL,
  `joining_date_hijry` varchar(255) NOT NULL,
  `retirement_date` date DEFAULT NULL,
  `retirement_date_hiry` varchar(255) NOT NULL,
  `job_time` tinyint(4) NOT NULL,
  `work_schedule_id` int(11) NOT NULL,
  `auto_attendance` tinyint(4) NOT NULL,
  `add_old_att_data` tinyint(4) NOT NULL,
  `job_title_id` int(11) NOT NULL,
  `holiday_no` tinyint(4) NOT NULL,
  `old_balance` tinyint(4) NOT NULL,
  `employee_salary` int(11) NOT NULL,
  `salary_currency_id` int(11) NOT NULL,
  `cost_center` int(11) NOT NULL,
  `advance_erp_account` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `passport_end_hijry` varchar(100) NOT NULL,
  `identity_end_hijry` varchar(100) NOT NULL,
  `work_license_date_hijry` varchar(100) NOT NULL,
  `medical_list_id` int(11) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_finger_device_no` (`finger_device_no`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `employee` VALUES ('1', '1', 'admin', NULL, 'مدير النظام', 'مدير النظام', '2021-12-30', '3-5-1400', '1', '123456789', 'admin@gmail.com', '1', '99', 'ماستر', '', '00', '2021-12-29', '0', '2021-12-29', '00', NULL, '2021-12-31', '16555928313287614.jpg', '1', '16555929823458853.png', '1655598367212024.png', '16555998005345026.png', '16555998001469034.png', '16555998004624642.png', '4,5,8,12,15,16,19,20,24', '1', '1', '', '2021-12-30', '9', '10', '1', '2', '2', '2', '1', '13', '1', '2021-12-30', '9', '2021-12-30', '8', '1', '1', '0', '0', '1', '8', '9', '3000', '2', '1', '1', '9', '0', '09897', '098978675', '', '', 'admin', NULL, NULL, NULL);

-- Table: employee_category
DROP TABLE IF EXISTS `employee_category`;
CREATE TABLE `employee_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `employee_category` VALUES ('1', 'الفئة ب', 'الفئة ب');
INSERT INTO `employee_category` VALUES ('2', 'الفئة أ', 'الفئة أ');
INSERT INTO `employee_category` VALUES ('3', 'فئة المدراء', 'فئة المدراء');

-- Table: employee_category_allowance
DROP TABLE IF EXISTS `employee_category_allowance`;
CREATE TABLE `employee_category_allowance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_category_id` int(11) NOT NULL,
  `allowance_id` int(11) NOT NULL,
  `allowance_value` float NOT NULL,
  `allowance_type` tinyint(4) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_category_id` (`employee_category_id`),
  KEY `allowance_id` (`allowance_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `employee_category_allowance_ibfk_1` FOREIGN KEY (`employee_category_id`) REFERENCES `employee_category` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_category_allowance_ibfk_2` FOREIGN KEY (`allowance_id`) REFERENCES `allowance` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_category_allowance_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: employee_category_deduction
DROP TABLE IF EXISTS `employee_category_deduction`;
CREATE TABLE `employee_category_deduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_category_id` int(11) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `deduction_value` float NOT NULL,
  `deduction_type` tinyint(4) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_category_id` (`employee_category_id`),
  KEY `deduction_id` (`deduction_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `employee_category_deduction_ibfk_1` FOREIGN KEY (`employee_category_id`) REFERENCES `employee_category` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_category_deduction_ibfk_2` FOREIGN KEY (`deduction_id`) REFERENCES `deduction_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_category_deduction_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: employee_round_vacation
DROP TABLE IF EXISTS `employee_round_vacation`;
CREATE TABLE `employee_round_vacation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_taked_vacation` decimal(10,0) NOT NULL,
  `old_balance` decimal(10,0) NOT NULL,
  `added_balance` decimal(10,0) NOT NULL,
  `new_balance` decimal(10,0) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_round_vacation_user_FK` (`user_id`),
  CONSTRAINT `employee_round_vacation_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: evaluation_item
DROP TABLE IF EXISTS `evaluation_item`;
CREATE TABLE `evaluation_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `question_type` tinyint(4) NOT NULL,
  `required` tinyint(4) NOT NULL,
  `weight` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `job_title_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `job_title_id` (`job_title_id`),
  CONSTRAINT `evaluation_item_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`),
  CONSTRAINT `evaluation_item_ibfk_2` FOREIGN KEY (`job_title_id`) REFERENCES `job_title` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `evaluation_item` VALUES ('1', 'سؤال جديد ', '1', '2', '1', '1', '1', '2', '1');

-- Table: events
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `details` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: exit_setting
DROP TABLE IF EXISTS `exit_setting`;
CREATE TABLE `exit_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) NOT NULL,
  `question_type` tinyint(4) NOT NULL,
  `required` tinyint(4) NOT NULL,
  `weight` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `exit_setting` VALUES ('1', 'سؤال جديد ', '1', '2', '1', '2', '1');

-- Table: extra_hours_type
DROP TABLE IF EXISTS `extra_hours_type`;
CREATE TABLE `extra_hours_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `hour_value` float NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `extra_hours_type` VALUES ('1', 'ساعة عمل', 'ساعة عمل', '4', '1');
INSERT INTO `extra_hours_type` VALUES ('2', 'اضافي عادي ', 'اضافي عادي ', '150', '1');
INSERT INTO `extra_hours_type` VALUES ('3', 'اضافي اجازة ', 'اضافي اجازة ', '200', '1');

-- Table: holiday
DROP TABLE IF EXISTS `holiday`;
CREATE TABLE `holiday` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `management_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: hr_user_role
DROP TABLE IF EXISTS `hr_user_role`;
CREATE TABLE `hr_user_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(20) NOT NULL,
  `role_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_user_role_user_id_FK` (`user_id`),
  CONSTRAINT `hr_user_role_user_id_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `hr_user_role` VALUES ('1', '1', '1');

-- Table: insurance_list
DROP TABLE IF EXISTS `insurance_list`;
CREATE TABLE `insurance_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `insurance_percent` float NOT NULL,
  `societe_percent` float NOT NULL,
  `habit_insurance` tinyint(4) NOT NULL,
  `allowance_id` varchar(100) NOT NULL,
  `support_rate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `insurance_list` VALUES ('1', 'تامينات الموظفين الجدد', 'الجدد', '0', '20', '0', '1,2,3', NULL);

-- Table: job_place
DROP TABLE IF EXISTS `job_place`;
CREATE TABLE `job_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `track_location` tinyint(4) NOT NULL,
  `lat` varchar(255) NOT NULL,
  `lang` varchar(255) NOT NULL,
  `zoom` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `job_place` VALUES ('1', 'موقع عمل', '55', '1', '26.367544991375915', '43.98188668750002', '8');

-- Table: job_time
DROP TABLE IF EXISTS `job_time`;
CREATE TABLE `job_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `working_hour_number` time NOT NULL,
  `working_period` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `job_time` VALUES ('1', 'فترة دوام كامل', '08:00:00', '');
INSERT INTO `job_time` VALUES ('2', 'فترة دوام كامل', '08:00:00', '');

-- Table: job_title
DROP TABLE IF EXISTS `job_title`;
CREATE TABLE `job_title` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO `job_title` VALUES ('1', 'المدير التنفيذي', 'Executive Manager', 'القيام بأداء جميع المهمات و المسئوليات المنوطة بالمدير التنفيذي ، و المشاركة الفعالة فى تحديد و صياغة الأهداف، و كذلك تخطيط و تنظيم سير العمل بالمنشأة بما يضمن تحقيق الأهداف المحددة', 'To perform all the tasks and responsibilities of the Executive manager , and active participation in the identification and formulation of objectives, as well as planning and organizing workflow facility to ensure the achievement of the targets set', '1');
INSERT INTO `job_title` VALUES ('2', 'دعم فني ', 'دعم فني ', '', '', '1');
INSERT INTO `job_title` VALUES ('3', 'مندوب مبيعات ', 'مندوب مبيعات', '', '', '1');
INSERT INTO `job_title` VALUES ('4', 'محاسب ', 'محاسب ', '', '', '1');
INSERT INTO `job_title` VALUES ('5', 'مبرمج ', 'مبرمج ', '', '', '1');
INSERT INTO `job_title` VALUES ('6', 'مدير مالي ', 'مدير مالي ', '', '', '1');
INSERT INTO `job_title` VALUES ('7', 'محاسب تكاليف ', 'محاسب', 'ن', 'ن', '1');

-- Table: job_types
DROP TABLE IF EXISTS `job_types`;
CREATE TABLE `job_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `job_types` VALUES ('1', 'اساسي', 'اساسي');
INSERT INTO `job_types` VALUES ('2', 'متعاون', 'متعاون');

-- Table: leave_category
DROP TABLE IF EXISTS `leave_category`;
CREATE TABLE `leave_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `leave_duration` tinyint(4) NOT NULL,
  `paid_leave` tinyint(4) NOT NULL,
  `leave_quota` tinyint(4) NOT NULL,
  `affect_balance` tinyint(4) NOT NULL,
  `calc_type` tinyint(4) NOT NULL,
  `emp_gender` tinyint(4) NOT NULL,
  `emp_maratial_status` tinyint(4) NOT NULL,
  `employee_category` int(11) NOT NULL,
  `set_replacement` tinyint(4) NOT NULL,
  `leave_tel` tinyint(4) NOT NULL,
  `leave_category_model` int(11) NOT NULL,
  `allowance_cat_id` varchar(255) NOT NULL,
  `is_annual_vacation` tinyint(4) NOT NULL,
  `weekend_included` int(11) NOT NULL DEFAULT '1',
  `vacation_type` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `leave_category` VALUES ('1', 'اجازه عاديه', 'اجازه عاديه', '21', '1', '2', '1', '1', '1', '4', '1', '1', '1', '1', '3,2,1', '0', '1', '1');
INSERT INTO `leave_category` VALUES ('2', 'اجازه سنويه', 'annual Vacation', '21', '1', '3', '1', '1', '1', '1', '1', '1', '1', '1', '6,5,4', '1', '1', '1');
INSERT INTO `leave_category` VALUES ('3', 'إجازة مرضية 30', 'Sick leave 30', '30', '1', '3', '1', '2', '1', '1', '2', '0', '0', '1', '1', '0', '1', '1');
INSERT INTO `leave_category` VALUES ('4', 'اجازه مرض ', 'Vas', '7', '0', '2', '1', '1', '1', '1', '10', '1', '1', '122', '5', '0', '1', '1');
INSERT INTO `leave_category` VALUES ('5', 'اجازه موظف', 'emp ', '5', '1', '2', '1', '1', '1', '1', '10', '1', '1', '3', '5', '0', '1', '1');
INSERT INTO `leave_category` VALUES ('6', 'اجازه غير مدفوعه ', 'اجازه غير مدفوعه ', '3', '0', '1', '1', '1', '1', '1', '12', '1', '1', '14', '5', '1', '1', '1');

-- Table: mail_box
DROP TABLE IF EXISTS `mail_box`;
CREATE TABLE `mail_box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` varchar(255) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `is_secret` tinyint(4) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message_body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file` varchar(255) NOT NULL,
  `email_type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: mandate_type
DROP TABLE IF EXISTS `mandate_type`;
CREATE TABLE `mandate_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `mandate_type` VALUES ('1', 'نوع انتداب', 'نوع انتداب', '1');
INSERT INTO `mandate_type` VALUES ('2', 'انتداب خارجي ', 'انتداب خارجي ', '1');

-- Table: medical_list
DROP TABLE IF EXISTS `medical_list`;
CREATE TABLE `medical_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `medical_list` VALUES ('1', 'تكافل الراجحي ', 'تكافل الراجحي');

-- Table: mission
DROP TABLE IF EXISTS `mission`;
CREATE TABLE `mission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_title_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_title_id` (`job_title_id`),
  CONSTRAINT `mission_ibfk_1` FOREIGN KEY (`job_title_id`) REFERENCES `job_title` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `mission` VALUES ('2', '1', '7- تحرير أي خطابات تتعلق بالموظفين	', '7- تحرير أي خطابات تتعلق بالموظفين	');
INSERT INTO `mission` VALUES ('3', '1', '8- ضبط الحضور والإنصراف	', '8- ضبط الحضور والإنصراف	');
INSERT INTO `mission` VALUES ('4', '1', '9- توفير دورات تدريبية للموظفين عند طلبها	', '9- توفير دورات تدريبية للموظفين عند طلبها	');

-- Table: mobile_message
DROP TABLE IF EXISTS `mobile_message`;
CREATE TABLE `mobile_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `text_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section` varchar(255) NOT NULL,
  `description` text,
  `content_json` text,
  `status` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notifications_user_FK_1` (`user_id`),
  CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_advance
DROP TABLE IF EXISTS `order_advance`;
CREATE TABLE `order_advance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `advance_type` int(11) NOT NULL,
  `advance_value` int(11) NOT NULL,
  `currency` int(11) NOT NULL,
  `deduction_start_date` date DEFAULT NULL,
  `payement_method` tinyint(4) DEFAULT NULL,
  `monthly_installement` float DEFAULT NULL,
  `payement_months` tinyint(4) DEFAULT NULL,
  `payement_note` text,
  `other_employee_id` int(11) DEFAULT NULL,
  `employee_note` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `note` text,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT '0',
  `advance_amount` decimal(12,4) DEFAULT '0.0000',
  `change` decimal(4,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `advance_type` (`advance_type`),
  KEY `currency` (`currency`),
  CONSTRAINT `order_advance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `order_advance_ibfk_2` FOREIGN KEY (`advance_type`) REFERENCES `advance_type` (`id`),
  CONSTRAINT `order_advance_ibfk_3` FOREIGN KEY (`currency`) REFERENCES `currency` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_caching
DROP TABLE IF EXISTS `order_caching`;
CREATE TABLE `order_caching` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `caching_type` int(11) NOT NULL,
  `beneficiary_name` varchar(255) NOT NULL,
  `bill_number` varchar(100) NOT NULL,
  `item_no` varchar(100) NOT NULL,
  `amount` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `country` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `address_in_leave` varchar(255) NOT NULL,
  `swift_code` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `caching_reason` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `currency_id` (`currency_id`),
  KEY `caching_type` (`caching_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `order_caching` VALUES ('1', '12', '1', 'مدير النظام', '12', '12', '400', '2', '6', '6', '6', '6', '6', '6', '6', '6', '6', '', '2022-03-29 18:44:14');

-- Table: order_clearance
DROP TABLE IF EXISTS `order_clearance`;
CREATE TABLE `order_clearance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `order_clearance` VALUES ('1', '12', 'خروج نهائي', 'ملاحظات', '2022-06-09 11:51:27');
INSERT INTO `order_clearance` VALUES ('2', '22', 'فتح مشروع جديد ', 'الرجاء اخلاء طرفي ', '2022-06-25 05:52:08');

-- Table: order_commission
DROP TABLE IF EXISTS `order_commission`;
CREATE TABLE `order_commission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_custody
DROP TABLE IF EXISTS `order_custody`;
CREATE TABLE `order_custody` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `custody_type` int(11) NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_def_salary
DROP TABLE IF EXISTS `order_def_salary`;
CREATE TABLE `order_def_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `company_request` varchar(255) NOT NULL,
  `confirm_latter` tinyint(4) NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `order_def_salary` VALUES ('1', '11', '1', 'شركة  اخرى', '1', '55', '2022-06-09 11:46:58');
INSERT INTO `order_def_salary` VALUES ('2', '22', '4', 'بنك البلاد', '1', 'لفتح حساب بنكي ', '2022-06-25 05:50:53');
INSERT INTO `order_def_salary` VALUES ('3', '31', '2', 'الى من يهمه الأمر', '1', 'بناءً على طلبه', '2022-08-04 05:42:30');

-- Table: order_dismissal
DROP TABLE IF EXISTS `order_dismissal`;
CREATE TABLE `order_dismissal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `last_day_date` date NOT NULL,
  `dismissal_reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `order_dismissal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_embarkation
DROP TABLE IF EXISTS `order_embarkation`;
CREATE TABLE `order_embarkation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `embarkation_coming_date` date NOT NULL,
  `user_note` text NOT NULL,
  `remainder` int(11) NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `order_embarkation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_filter_dues
DROP TABLE IF EXISTS `order_filter_dues`;
CREATE TABLE `order_filter_dues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `allowance_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `allowance_id` (`allowance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_increase_salary
DROP TABLE IF EXISTS `order_increase_salary`;
CREATE TABLE `order_increase_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `base_salary` float NOT NULL,
  `job_title_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `increase_reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `job_title_id` (`job_title_id`),
  CONSTRAINT `order_increase_salary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `order_increase_salary_ibfk_2` FOREIGN KEY (`job_title_id`) REFERENCES `job_title` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_job_assignment
DROP TABLE IF EXISTS `order_job_assignment`;
CREATE TABLE `order_job_assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `job_duration_day` tinyint(4) NOT NULL,
  `task_place` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_job_assignment_task
DROP TABLE IF EXISTS `order_job_assignment_task`;
CREATE TABLE `order_job_assignment_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_job_assignment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_job_assignment_id` (`order_job_assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_maintenance
DROP TABLE IF EXISTS `order_maintenance`;
CREATE TABLE `order_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `order_maintenance` VALUES ('1', '21', 'الجبيل الصناعية ', 'صيانة النظام ', '', '2022-06-25 05:45:47');

-- Table: order_mandate
DROP TABLE IF EXISTS `order_mandate`;
CREATE TABLE `order_mandate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `requesting_entity` varchar(255) NOT NULL,
  `mandate_task` varchar(255) NOT NULL,
  `mandate_type_id` int(11) NOT NULL,
  `mandate_address` varchar(255) NOT NULL,
  `going_date` date NOT NULL,
  `coming_date` date NOT NULL,
  `duration` tinyint(4) NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `mandate_type_id` (`mandate_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `order_mandate` VALUES ('1', '13', 'الجهة', 'مهمة', '1', 'المكان', '2022-04-23', '2022-04-30', '7', 'الملاحظات', '', '2022-04-23 10:29:23');

-- Table: order_other
DROP TABLE IF EXISTS `order_other`;
CREATE TABLE `order_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_overtime
DROP TABLE IF EXISTS `order_overtime`;
CREATE TABLE `order_overtime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `extra_hours_type_id` int(11) NOT NULL,
  `overtime_date` date NOT NULL,
  `start_hours` time NOT NULL,
  `end_hours` time NOT NULL,
  `duration` time NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `extra-hours-type_id` (`extra_hours_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_overtime_task
DROP TABLE IF EXISTS `order_overtime_task`;
CREATE TABLE `order_overtime_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_overtime_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_overtime_id` (`order_overtime_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_permission
DROP TABLE IF EXISTS `order_permission`;
CREATE TABLE `order_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_type` int(11) NOT NULL,
  `permission_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration` time NOT NULL,
  `note` text,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `permission_type` (`permission_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


-- Table: order_provision
DROP TABLE IF EXISTS `order_provision`;
CREATE TABLE `order_provision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provision_id` int(11) NOT NULL,
  `provision_value` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `provision_id` (`provision_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_purchase
DROP TABLE IF EXISTS `order_purchase`;
CREATE TABLE `order_purchase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_purchase_product
DROP TABLE IF EXISTS `order_purchase_product`;
CREATE TABLE `order_purchase_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_purchase_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_purchase_id` (`order_purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_resignation
DROP TABLE IF EXISTS `order_resignation`;
CREATE TABLE `order_resignation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `last_day_date` date NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_salary
DROP TABLE IF EXISTS `order_salary`;
CREATE TABLE `order_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `payroll_type` tinyint(4) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


-- Table: order_salary_archive
DROP TABLE IF EXISTS `order_salary_archive`;
CREATE TABLE `order_salary_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `salary_type` tinyint(4) NOT NULL,
  `job_place` varchar(255) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `departement_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `stoped_employee` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `branch_id` (`branch_id`),
  KEY `departement_id` (`departement_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `order_salary_archive_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `order_salary_archive_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  CONSTRAINT `order_salary_archive_ibfk_3` FOREIGN KEY (`departement_id`) REFERENCES `department` (`id`),
  CONSTRAINT `order_salary_archive_ibfk_4` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


-- Table: order_training
DROP TABLE IF EXISTS `order_training`;
CREATE TABLE `order_training` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `planed` tinyint(4) NOT NULL,
  `training_name` varchar(255) NOT NULL,
  `course_institute` varchar(255) NOT NULL,
  `course_start_date` date NOT NULL,
  `course_end_date` date NOT NULL,
  `amount` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `currency_id` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_transfer
DROP TABLE IF EXISTS `order_transfer`;
CREATE TABLE `order_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_title_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `job_place_id` int(11) NOT NULL,
  `direct_manager` int(11) NOT NULL,
  `job_time` tinyint(4) NOT NULL,
  `transfer_start_date` date NOT NULL,
  `note` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `job_title_id` (`job_title_id`),
  KEY `branch_id` (`branch_id`),
  KEY `department_id` (`department_id`),
  KEY `section_id` (`section_id`),
  KEY `job_place_id` (`job_place_id`),
  KEY `direct_manager` (`direct_manager`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: order_vacation
DROP TABLE IF EXISTS `order_vacation`;
CREATE TABLE `order_vacation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `days_from_admin` float NOT NULL,
  `annual_balance` float NOT NULL,
  `leaves_taken` float NOT NULL,
  `address_in_leave` varchar(255) DEFAULT NULL,
  `tel_in_leave` varchar(255) DEFAULT NULL,
  `replacement` int(11) DEFAULT NULL,
  `tiket` tinyint(4) NOT NULL,
  `going_date` date DEFAULT NULL,
  `coming_date` date DEFAULT NULL,
  `going_date_2` date DEFAULT NULL,
  `coming_date_2` date DEFAULT NULL,
  `note` text,
  `file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  CONSTRAINT `order_vacation_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


-- Table: other_vacation_balance
DROP TABLE IF EXISTS `other_vacation_balance`;
CREATE TABLE `other_vacation_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `leave_category_id` int(11) NOT NULL,
  `duration` float NOT NULL,
  `taken` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `leave_category_id` (`leave_category_id`),
  KEY `other_vacation_balance_ibfk_1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: performance_plan
DROP TABLE IF EXISTS `performance_plan`;
CREATE TABLE `performance_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `year` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `evaluation_type` varchar(255) NOT NULL,
  `direct_manager_eval_period_time` tinyint(4) NOT NULL,
  `self_evaluation_period_time` tinyint(4) NOT NULL,
  `participants_evaluation_period_time` tinyint(4) NOT NULL,
  `hr_evaluation_period_time` tinyint(4) NOT NULL,
  `calculation_type` tinyint(4) NOT NULL,
  `direct_manager_eval_percentage` tinyint(4) DEFAULT NULL,
  `self_evaluation_percentage` tinyint(4) DEFAULT NULL,
  `participants_evaluation_percentage` tinyint(4) DEFAULT NULL,
  `hr_evaluation_percentage` tinyint(4) DEFAULT NULL,
  `auto_approve` tinyint(4) NOT NULL,
  `approval_level1` int(11) DEFAULT NULL,
  `approval_level2` int(11) DEFAULT NULL,
  `approval_level3` int(11) DEFAULT NULL,
  `employee_reevaluation_app` tinyint(4) NOT NULL,
  `reevaluation_num` tinyint(4) NOT NULL,
  `reevaluation_period` int(11) NOT NULL,
  `participant_hidden` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `performance_plan` VALUES ('1', 'خطة جديدة', 'خطة جديدة', '127', '1', '1,2,3,4', '1', '1', '1', '1', '1', NULL, NULL, NULL, NULL, '0', '12', '14', '4', '1', '0', '0', '1');
INSERT INTO `performance_plan` VALUES ('2', 'موظف مثالي ', 'موظف مثالي في الحضور والانصراف والعمل ', '127', '1', '1,2,3,4', '1', '1', '1', '1', '1', NULL, NULL, NULL, NULL, '1', NULL, NULL, NULL, '1', '0', '0', '1');

-- Table: permission_type
DROP TABLE IF EXISTS `permission_type`;
CREATE TABLE `permission_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `permission_type` VALUES ('1', 'انصراف مبكر', 'انصراف مبكر', '1');
INSERT INTO `permission_type` VALUES ('2', 'اجازة ', 'اجازة ', '1');
INSERT INTO `permission_type` VALUES ('3', 'أداء عمرة ', 'أداء عمرة ', '1');
INSERT INTO `permission_type` VALUES ('4', 'اداء فريضة الحج ', 'اداء فريضة الحج ', '1');
INSERT INTO `permission_type` VALUES ('5', 'مقابلة طبيب ', 'مقابلة طبيب ', '1');

-- Table: permissions
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: posted_employees_salary
DROP TABLE IF EXISTS `posted_employees_salary`;
CREATE TABLE `posted_employees_salary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `posted_salary_id` int(11) NOT NULL,
  `total_of_absent_dayes` varchar(255) DEFAULT NULL,
  `total_of_absent_hours` varchar(255) DEFAULT NULL,
  `extra_hours` varchar(255) DEFAULT NULL,
  `employee_salary` varchar(255) DEFAULT NULL,
  `total_provisions` varchar(255) DEFAULT NULL,
  `total_allowances` varchar(255) DEFAULT NULL,
  `extra_hour_overtime` varchar(255) DEFAULT NULL,
  `total_entitlements_all` varchar(255) DEFAULT NULL,
  `total_absence` varchar(255) DEFAULT NULL,
  `total_insurance` varchar(255) DEFAULT NULL,
  `total_deductions` varchar(255) DEFAULT NULL,
  `total_vacations` varchar(255) DEFAULT NULL,
  `total_advances` varchar(255) DEFAULT NULL,
  `total_deductions_all` varchar(255) DEFAULT NULL,
  `total_net_due` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posted_employees_salary__FK` (`user_id`),
  KEY `posted_salary_id__FK` (`posted_salary_id`),
  CONSTRAINT `posted_employees_salary__FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `posted_salary_id__FK` FOREIGN KEY (`posted_salary_id`) REFERENCES `posted_salary` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: posted_salary
DROP TABLE IF EXISTS `posted_salary`;
CREATE TABLE `posted_salary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` varchar(2) DEFAULT NULL,
  `year` varchar(4) DEFAULT NULL,
  `is_posted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: prepare
DROP TABLE IF EXISTS `prepare`;
CREATE TABLE `prepare` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: provision_type
DROP TABLE IF EXISTS `provision_type`;
CREATE TABLE `provision_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `provision_type` VALUES ('1', 'امتياز مالي', 'امتياز مالي', '1');
INSERT INTO `provision_type` VALUES ('2', 'جوائز', 'جوائز', '1');
INSERT INTO `provision_type` VALUES ('3', 'مكافئة', 'مكافئة', '1');
INSERT INTO `provision_type` VALUES ('4', 'زيادة', 'زيادة', '1');
INSERT INTO `provision_type` VALUES ('5', 'بدل انتداب', 'بدل انتداب', '1');
INSERT INTO `provision_type` VALUES ('6', 'موظف مثالي ', 'موظف مثالي ', '1');

-- Table: qualification
DROP TABLE IF EXISTS `qualification`;
CREATE TABLE `qualification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `qualification` VALUES ('1', 'دكتوراه', 'Ph.D.');
INSERT INTO `qualification` VALUES ('2', 'دبلوم وسيط ', 'دبلوم وسيط ');
INSERT INTO `qualification` VALUES ('3', 'بكالريوس ', 'بكالريوس ');
INSERT INTO `qualification` VALUES ('4', 'الماجستير ', 'الماجستير ');
INSERT INTO `qualification` VALUES ('5', 'ثانوية عامة ', 'ثانوية عامة ');

-- Table: rating_name
DROP TABLE IF EXISTS `rating_name`;
CREATE TABLE `rating_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name1` varchar(255) NOT NULL,
  `from1` float NOT NULL,
  `to1` float NOT NULL,
  `name2` varchar(255) NOT NULL,
  `from2` float NOT NULL,
  `to2` float NOT NULL,
  `name3` varchar(255) NOT NULL,
  `from3` float NOT NULL,
  `to3` float NOT NULL,
  `name4` varchar(255) NOT NULL,
  `from4` float NOT NULL,
  `to4` float NOT NULL,
  `name5` varchar(255) NOT NULL,
  `from5` float NOT NULL,
  `to5` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `rating_name` VALUES ('1', 'مبدع', '90', '100', 'كفىء', '75', '89', 'فعال', '60', '74', 'مجتهد', '55', '59', 'ضعيف', '0', '54');

-- Table: relationship_list
DROP TABLE IF EXISTS `relationship_list`;
CREATE TABLE `relationship_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `relationship_list` VALUES ('1', 'اب', 'اب');
INSERT INTO `relationship_list` VALUES ('2', 'زوجة ', 'زوجة ');
INSERT INTO `relationship_list` VALUES ('3', 'ابن ', 'ابن ');
INSERT INTO `relationship_list` VALUES ('4', 'ابنه / بنت ', 'ابنه / بنت ');
INSERT INTO `relationship_list` VALUES ('5', 'ام ', 'ام ');
INSERT INTO `relationship_list` VALUES ('6', 'اخ', 'اخ');

-- Table: role_permissions
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `module_id` bigint(20) NOT NULL,
  `model_id` bigint(20) NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_permissions_roles_FK` (`role_id`),
  CONSTRAINT `role_permissions_roles_FK` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1256 DEFAULT CHARSET=utf8;

INSERT INTO `role_permissions` VALUES ('873', '1', '60000', '60001', '7');
INSERT INTO `role_permissions` VALUES ('874', '1', '60000', '60001', '1');
INSERT INTO `role_permissions` VALUES ('875', '1', '60000', '60001', '2');
INSERT INTO `role_permissions` VALUES ('876', '1', '60000', '60001', '3');
INSERT INTO `role_permissions` VALUES ('877', '1', '60000', '60001', '4');
INSERT INTO `role_permissions` VALUES ('878', '1', '60000', '60002', '7');
INSERT INTO `role_permissions` VALUES ('879', '1', '60000', '60002', '1');
INSERT INTO `role_permissions` VALUES ('880', '1', '60000', '60002', '2');
INSERT INTO `role_permissions` VALUES ('881', '1', '60000', '60002', '3');
INSERT INTO `role_permissions` VALUES ('882', '1', '60000', '60002', '4');
INSERT INTO `role_permissions` VALUES ('883', '1', '60000', '60003', '7');
INSERT INTO `role_permissions` VALUES ('884', '1', '60000', '60003', '1');
INSERT INTO `role_permissions` VALUES ('885', '1', '60000', '60003', '2');
INSERT INTO `role_permissions` VALUES ('886', '1', '60000', '60003', '3');
INSERT INTO `role_permissions` VALUES ('887', '1', '60000', '60003', '4');
INSERT INTO `role_permissions` VALUES ('888', '1', '60000', '60004', '7');
INSERT INTO `role_permissions` VALUES ('889', '1', '60000', '60004', '1');
INSERT INTO `role_permissions` VALUES ('890', '1', '60000', '60004', '2');
INSERT INTO `role_permissions` VALUES ('891', '1', '60000', '60004', '3');
INSERT INTO `role_permissions` VALUES ('892', '1', '60000', '60004', '4');
INSERT INTO `role_permissions` VALUES ('893', '1', '60000', '60005', '7');
INSERT INTO `role_permissions` VALUES ('894', '1', '60000', '60005', '1');
INSERT INTO `role_permissions` VALUES ('895', '1', '60000', '60005', '2');
INSERT INTO `role_permissions` VALUES ('896', '1', '60000', '60005', '3');
INSERT INTO `role_permissions` VALUES ('897', '1', '60000', '60005', '4');
INSERT INTO `role_permissions` VALUES ('898', '1', '60000', '60006', '7');
INSERT INTO `role_permissions` VALUES ('899', '1', '60000', '60006', '1');
INSERT INTO `role_permissions` VALUES ('900', '1', '60000', '60006', '2');
INSERT INTO `role_permissions` VALUES ('901', '1', '60000', '60006', '3');
INSERT INTO `role_permissions` VALUES ('902', '1', '60000', '60006', '4');
INSERT INTO `role_permissions` VALUES ('903', '1', '60000', '60007', '7');
INSERT INTO `role_permissions` VALUES ('904', '1', '60000', '60007', '1');
INSERT INTO `role_permissions` VALUES ('905', '1', '60000', '60007', '2');
INSERT INTO `role_permissions` VALUES ('906', '1', '60000', '60007', '3');
INSERT INTO `role_permissions` VALUES ('907', '1', '60000', '60007', '4');
INSERT INTO `role_permissions` VALUES ('908', '1', '60000', '60008', '7');
INSERT INTO `role_permissions` VALUES ('909', '1', '60000', '60008', '1');
INSERT INTO `role_permissions` VALUES ('910', '1', '60000', '60008', '2');
INSERT INTO `role_permissions` VALUES ('911', '1', '60000', '60008', '3');
INSERT INTO `role_permissions` VALUES ('912', '1', '60000', '60008', '4');
INSERT INTO `role_permissions` VALUES ('913', '1', '60000', '60009', '7');
INSERT INTO `role_permissions` VALUES ('914', '1', '60000', '60009', '1');
INSERT INTO `role_permissions` VALUES ('915', '1', '60000', '60009', '2');
INSERT INTO `role_permissions` VALUES ('916', '1', '60000', '60009', '3');
INSERT INTO `role_permissions` VALUES ('917', '1', '60000', '60009', '4');
INSERT INTO `role_permissions` VALUES ('918', '1', '60000', '60010', '7');
INSERT INTO `role_permissions` VALUES ('919', '1', '60000', '60010', '1');
INSERT INTO `role_permissions` VALUES ('920', '1', '60000', '60010', '2');
INSERT INTO `role_permissions` VALUES ('921', '1', '60000', '60010', '3');
INSERT INTO `role_permissions` VALUES ('922', '1', '60000', '60010', '4');
INSERT INTO `role_permissions` VALUES ('923', '1', '60000', '60011', '7');
INSERT INTO `role_permissions` VALUES ('924', '1', '60000', '60011', '1');
INSERT INTO `role_permissions` VALUES ('925', '1', '60000', '60011', '2');
INSERT INTO `role_permissions` VALUES ('926', '1', '60000', '60011', '3');
INSERT INTO `role_permissions` VALUES ('927', '1', '60000', '60011', '4');
INSERT INTO `role_permissions` VALUES ('928', '1', '60000', '60012', '7');
INSERT INTO `role_permissions` VALUES ('929', '1', '60000', '60012', '1');
INSERT INTO `role_permissions` VALUES ('930', '1', '60000', '60012', '2');
INSERT INTO `role_permissions` VALUES ('931', '1', '60000', '60012', '3');
INSERT INTO `role_permissions` VALUES ('932', '1', '60000', '60012', '4');
INSERT INTO `role_permissions` VALUES ('933', '1', '60000', '60013', '7');
INSERT INTO `role_permissions` VALUES ('934', '1', '60000', '60013', '1');
INSERT INTO `role_permissions` VALUES ('935', '1', '60000', '60013', '2');
INSERT INTO `role_permissions` VALUES ('936', '1', '60000', '60013', '3');
INSERT INTO `role_permissions` VALUES ('937', '1', '60000', '60013', '4');
INSERT INTO `role_permissions` VALUES ('938', '1', '60000', '60014', '7');
INSERT INTO `role_permissions` VALUES ('939', '1', '60000', '60014', '1');
INSERT INTO `role_permissions` VALUES ('940', '1', '60000', '60014', '2');
INSERT INTO `role_permissions` VALUES ('941', '1', '60000', '60014', '3');
INSERT INTO `role_permissions` VALUES ('942', '1', '60000', '60014', '4');
INSERT INTO `role_permissions` VALUES ('943', '1', '60000', '60015', '7');
INSERT INTO `role_permissions` VALUES ('944', '1', '60000', '60015', '1');
INSERT INTO `role_permissions` VALUES ('945', '1', '60000', '60015', '2');
INSERT INTO `role_permissions` VALUES ('946', '1', '60000', '60015', '3');
INSERT INTO `role_permissions` VALUES ('947', '1', '60000', '60015', '4');
INSERT INTO `role_permissions` VALUES ('1043', '1', '10000', '10001', '5');
INSERT INTO `role_permissions` VALUES ('1044', '1', '10000', '10002', '1');
INSERT INTO `role_permissions` VALUES ('1045', '1', '10000', '10002', '2');
INSERT INTO `role_permissions` VALUES ('1046', '1', '10000', '10002', '3');
INSERT INTO `role_permissions` VALUES ('1047', '1', '10000', '10002', '4');
INSERT INTO `role_permissions` VALUES ('1048', '1', '10000', '10003', '1');
INSERT INTO `role_permissions` VALUES ('1049', '1', '10000', '10003', '2');
INSERT INTO `role_permissions` VALUES ('1050', '1', '10000', '10003', '3');
INSERT INTO `role_permissions` VALUES ('1051', '1', '10000', '10003', '4');
INSERT INTO `role_permissions` VALUES ('1052', '1', '10000', '10004', '1');
INSERT INTO `role_permissions` VALUES ('1053', '1', '10000', '10004', '2');
INSERT INTO `role_permissions` VALUES ('1054', '1', '10000', '10004', '3');
INSERT INTO `role_permissions` VALUES ('1055', '1', '10000', '10004', '4');
INSERT INTO `role_permissions` VALUES ('1056', '1', '10000', '10005', '1');
INSERT INTO `role_permissions` VALUES ('1057', '1', '10000', '10005', '2');
INSERT INTO `role_permissions` VALUES ('1058', '1', '10000', '10005', '3');
INSERT INTO `role_permissions` VALUES ('1059', '1', '10000', '10005', '4');
INSERT INTO `role_permissions` VALUES ('1060', '1', '10000', '10006', '1');
INSERT INTO `role_permissions` VALUES ('1061', '1', '10000', '10006', '2');
INSERT INTO `role_permissions` VALUES ('1062', '1', '10000', '10006', '3');
INSERT INTO `role_permissions` VALUES ('1063', '1', '10000', '10006', '4');
INSERT INTO `role_permissions` VALUES ('1064', '1', '10000', '10007', '1');
INSERT INTO `role_permissions` VALUES ('1065', '1', '10000', '10007', '2');
INSERT INTO `role_permissions` VALUES ('1066', '1', '10000', '10007', '3');
INSERT INTO `role_permissions` VALUES ('1067', '1', '10000', '10007', '4');
INSERT INTO `role_permissions` VALUES ('1068', '1', '10000', '10008', '1');
INSERT INTO `role_permissions` VALUES ('1069', '1', '10000', '10008', '2');
INSERT INTO `role_permissions` VALUES ('1070', '1', '10000', '10008', '3');
INSERT INTO `role_permissions` VALUES ('1071', '1', '10000', '10008', '4');
INSERT INTO `role_permissions` VALUES ('1072', '1', '10000', '10009', '1');
INSERT INTO `role_permissions` VALUES ('1073', '1', '10000', '10009', '2');
INSERT INTO `role_permissions` VALUES ('1074', '1', '10000', '10009', '3');
INSERT INTO `role_permissions` VALUES ('1075', '1', '10000', '10009', '4');
INSERT INTO `role_permissions` VALUES ('1076', '1', '10000', '10010', '1');
INSERT INTO `role_permissions` VALUES ('1077', '1', '10000', '10010', '2');
INSERT INTO `role_permissions` VALUES ('1078', '1', '10000', '10010', '3');
INSERT INTO `role_permissions` VALUES ('1079', '1', '10000', '10010', '4');
INSERT INTO `role_permissions` VALUES ('1080', '1', '10000', '10011', '1');
INSERT INTO `role_permissions` VALUES ('1081', '1', '10000', '10011', '2');
INSERT INTO `role_permissions` VALUES ('1082', '1', '10000', '10011', '3');
INSERT INTO `role_permissions` VALUES ('1083', '1', '10000', '10011', '4');
INSERT INTO `role_permissions` VALUES ('1084', '1', '10000', '10012', '1');
INSERT INTO `role_permissions` VALUES ('1085', '1', '10000', '10012', '2');
INSERT INTO `role_permissions` VALUES ('1086', '1', '10000', '10012', '3');
INSERT INTO `role_permissions` VALUES ('1087', '1', '10000', '10012', '4');
INSERT INTO `role_permissions` VALUES ('1088', '1', '10000', '10013', '1');
INSERT INTO `role_permissions` VALUES ('1089', '1', '10000', '10013', '2');
INSERT INTO `role_permissions` VALUES ('1090', '1', '10000', '10013', '3');
INSERT INTO `role_permissions` VALUES ('1091', '1', '10000', '10013', '4');
INSERT INTO `role_permissions` VALUES ('1092', '1', '10000', '10014', '1');
INSERT INTO `role_permissions` VALUES ('1093', '1', '10000', '10014', '2');
INSERT INTO `role_permissions` VALUES ('1094', '1', '10000', '10014', '3');
INSERT INTO `role_permissions` VALUES ('1095', '1', '10000', '10014', '4');
INSERT INTO `role_permissions` VALUES ('1096', '1', '10000', '10015', '1');
INSERT INTO `role_permissions` VALUES ('1097', '1', '10000', '10015', '2');
INSERT INTO `role_permissions` VALUES ('1098', '1', '10000', '10015', '3');
INSERT INTO `role_permissions` VALUES ('1099', '1', '10000', '10015', '4');
INSERT INTO `role_permissions` VALUES ('1100', '1', '10000', '10016', '1');
INSERT INTO `role_permissions` VALUES ('1101', '1', '10000', '10016', '2');
INSERT INTO `role_permissions` VALUES ('1102', '1', '10000', '10016', '3');
INSERT INTO `role_permissions` VALUES ('1103', '1', '10000', '10016', '4');
INSERT INTO `role_permissions` VALUES ('1104', '1', '10000', '10017', '5');
INSERT INTO `role_permissions` VALUES ('1105', '1', '10000', '10018', '1');
INSERT INTO `role_permissions` VALUES ('1106', '1', '10000', '10018', '2');
INSERT INTO `role_permissions` VALUES ('1107', '1', '10000', '10018', '3');
INSERT INTO `role_permissions` VALUES ('1108', '1', '10000', '10018', '4');
INSERT INTO `role_permissions` VALUES ('1109', '1', '10000', '10019', '1');
INSERT INTO `role_permissions` VALUES ('1110', '1', '10000', '10019', '2');
INSERT INTO `role_permissions` VALUES ('1111', '1', '10000', '10019', '3');
INSERT INTO `role_permissions` VALUES ('1112', '1', '10000', '10019', '4');
INSERT INTO `role_permissions` VALUES ('1113', '1', '20000', '20001', '5');
INSERT INTO `role_permissions` VALUES ('1114', '1', '20000', '20002', '1');
INSERT INTO `role_permissions` VALUES ('1115', '1', '20000', '20002', '2');
INSERT INTO `role_permissions` VALUES ('1116', '1', '20000', '20002', '3');
INSERT INTO `role_permissions` VALUES ('1117', '1', '20000', '20002', '4');
INSERT INTO `role_permissions` VALUES ('1118', '1', '20000', '20003', '1');
INSERT INTO `role_permissions` VALUES ('1119', '1', '20000', '20003', '2');
INSERT INTO `role_permissions` VALUES ('1120', '1', '20000', '20003', '3');
INSERT INTO `role_permissions` VALUES ('1121', '1', '20000', '20003', '4');
INSERT INTO `role_permissions` VALUES ('1122', '1', '20000', '20004', '1');
INSERT INTO `role_permissions` VALUES ('1123', '1', '20000', '20004', '2');
INSERT INTO `role_permissions` VALUES ('1124', '1', '20000', '20004', '3');
INSERT INTO `role_permissions` VALUES ('1125', '1', '20000', '20004', '4');
INSERT INTO `role_permissions` VALUES ('1126', '1', '20000', '20005', '5');
INSERT INTO `role_permissions` VALUES ('1127', '1', '20000', '20006', '1');
INSERT INTO `role_permissions` VALUES ('1128', '1', '20000', '20006', '2');
INSERT INTO `role_permissions` VALUES ('1129', '1', '20000', '20006', '3');
INSERT INTO `role_permissions` VALUES ('1130', '1', '20000', '20006', '4');
INSERT INTO `role_permissions` VALUES ('1131', '1', '20000', '20007', '5');
INSERT INTO `role_permissions` VALUES ('1132', '1', '20000', '20008', '1');
INSERT INTO `role_permissions` VALUES ('1133', '1', '20000', '20008', '2');
INSERT INTO `role_permissions` VALUES ('1134', '1', '20000', '20008', '3');
INSERT INTO `role_permissions` VALUES ('1135', '1', '20000', '20008', '4');
INSERT INTO `role_permissions` VALUES ('1136', '1', '20000', '20009', '1');
INSERT INTO `role_permissions` VALUES ('1137', '1', '20000', '20009', '2');
INSERT INTO `role_permissions` VALUES ('1138', '1', '20000', '20009', '3');
INSERT INTO `role_permissions` VALUES ('1139', '1', '20000', '20009', '4');
INSERT INTO `role_permissions` VALUES ('1140', '1', '20000', '20010', '1');
INSERT INTO `role_permissions` VALUES ('1141', '1', '20000', '20010', '2');
INSERT INTO `role_permissions` VALUES ('1142', '1', '20000', '20010', '3');
INSERT INTO `role_permissions` VALUES ('1143', '1', '20000', '20010', '4');
INSERT INTO `role_permissions` VALUES ('1144', '1', '20000', '20011', '1');
INSERT INTO `role_permissions` VALUES ('1145', '1', '20000', '20011', '2');
INSERT INTO `role_permissions` VALUES ('1146', '1', '20000', '20011', '3');
INSERT INTO `role_permissions` VALUES ('1147', '1', '20000', '20011', '4');
INSERT INTO `role_permissions` VALUES ('1148', '1', '20000', '20012', '1');
INSERT INTO `role_permissions` VALUES ('1149', '1', '20000', '20012', '2');
INSERT INTO `role_permissions` VALUES ('1150', '1', '20000', '20012', '3');
INSERT INTO `role_permissions` VALUES ('1151', '1', '20000', '20012', '4');
INSERT INTO `role_permissions` VALUES ('1152', '1', '20000', '20013', '5');
INSERT INTO `role_permissions` VALUES ('1153', '1', '20000', '20014', '1');
INSERT INTO `role_permissions` VALUES ('1154', '1', '20000', '20014', '2');
INSERT INTO `role_permissions` VALUES ('1155', '1', '20000', '20014', '3');
INSERT INTO `role_permissions` VALUES ('1156', '1', '20000', '20014', '4');
INSERT INTO `role_permissions` VALUES ('1157', '1', '20000', '20015', '1');
INSERT INTO `role_permissions` VALUES ('1158', '1', '20000', '20015', '2');
INSERT INTO `role_permissions` VALUES ('1159', '1', '20000', '20015', '3');
INSERT INTO `role_permissions` VALUES ('1160', '1', '20000', '20015', '4');
INSERT INTO `role_permissions` VALUES ('1161', '1', '20000', '20016', '5');
INSERT INTO `role_permissions` VALUES ('1162', '1', '20000', '20017', '1');
INSERT INTO `role_permissions` VALUES ('1163', '1', '20000', '20017', '2');
INSERT INTO `role_permissions` VALUES ('1164', '1', '20000', '20017', '3');
INSERT INTO `role_permissions` VALUES ('1165', '1', '20000', '20017', '4');
INSERT INTO `role_permissions` VALUES ('1166', '1', '20000', '20018', '1');
INSERT INTO `role_permissions` VALUES ('1167', '1', '20000', '20018', '2');
INSERT INTO `role_permissions` VALUES ('1168', '1', '20000', '20018', '3');
INSERT INTO `role_permissions` VALUES ('1169', '1', '20000', '20018', '4');
INSERT INTO `role_permissions` VALUES ('1170', '1', '20000', '20019', '6');
INSERT INTO `role_permissions` VALUES ('1171', '1', '20000', '20020', '6');
INSERT INTO `role_permissions` VALUES ('1172', '1', '20000', '20021', '6');
INSERT INTO `role_permissions` VALUES ('1173', '1', '20000', '20022', '6');
INSERT INTO `role_permissions` VALUES ('1174', '1', '20000', '20023', '6');
INSERT INTO `role_permissions` VALUES ('1175', '1', '20000', '20024', '6');
INSERT INTO `role_permissions` VALUES ('1176', '1', '20000', '20025', '6');
INSERT INTO `role_permissions` VALUES ('1177', '1', '20000', '20026', '6');
INSERT INTO `role_permissions` VALUES ('1178', '1', '20000', '20027', '6');
INSERT INTO `role_permissions` VALUES ('1179', '1', '20000', '20028', '6');
INSERT INTO `role_permissions` VALUES ('1180', '1', '20000', '20029', '6');
INSERT INTO `role_permissions` VALUES ('1181', '1', '20000', '20030', '6');
INSERT INTO `role_permissions` VALUES ('1182', '1', '30000', '30001', '1');
INSERT INTO `role_permissions` VALUES ('1183', '1', '30000', '30001', '2');
INSERT INTO `role_permissions` VALUES ('1184', '1', '30000', '30001', '3');
INSERT INTO `role_permissions` VALUES ('1185', '1', '30000', '30001', '4');
INSERT INTO `role_permissions` VALUES ('1186', '1', '30000', '30002', '1');
INSERT INTO `role_permissions` VALUES ('1187', '1', '30000', '30002', '2');
INSERT INTO `role_permissions` VALUES ('1188', '1', '30000', '30002', '3');
INSERT INTO `role_permissions` VALUES ('1189', '1', '30000', '30002', '4');
INSERT INTO `role_permissions` VALUES ('1190', '1', '30000', '30003', '1');
INSERT INTO `role_permissions` VALUES ('1191', '1', '30000', '30003', '2');
INSERT INTO `role_permissions` VALUES ('1192', '1', '30000', '30003', '3');
INSERT INTO `role_permissions` VALUES ('1193', '1', '30000', '30003', '4');
INSERT INTO `role_permissions` VALUES ('1194', '1', '30000', '30004', '5');
INSERT INTO `role_permissions` VALUES ('1195', '1', '30000', '30005', '5');
INSERT INTO `role_permissions` VALUES ('1196', '1', '30000', '30006', '6');
INSERT INTO `role_permissions` VALUES ('1197', '1', '30000', '30007', '6');
INSERT INTO `role_permissions` VALUES ('1198', '1', '40000', '40001', '1');
INSERT INTO `role_permissions` VALUES ('1199', '1', '40000', '40001', '2');
INSERT INTO `role_permissions` VALUES ('1200', '1', '40000', '40001', '3');
INSERT INTO `role_permissions` VALUES ('1201', '1', '40000', '40001', '4');
INSERT INTO `role_permissions` VALUES ('1202', '1', '40000', '40002', '1');
INSERT INTO `role_permissions` VALUES ('1203', '1', '40000', '40002', '2');
INSERT INTO `role_permissions` VALUES ('1204', '1', '40000', '40002', '3');
INSERT INTO `role_permissions` VALUES ('1205', '1', '40000', '40002', '4');
INSERT INTO `role_permissions` VALUES ('1206', '1', '40000', '40003', '1');
INSERT INTO `role_permissions` VALUES ('1207', '1', '40000', '40003', '2');
INSERT INTO `role_permissions` VALUES ('1208', '1', '40000', '40003', '3');
INSERT INTO `role_permissions` VALUES ('1209', '1', '40000', '40003', '4');
INSERT INTO `role_permissions` VALUES ('1210', '1', '40000', '40004', '1');
INSERT INTO `role_permissions` VALUES ('1211', '1', '40000', '40004', '2');
INSERT INTO `role_permissions` VALUES ('1212', '1', '40000', '40004', '3');
INSERT INTO `role_permissions` VALUES ('1213', '1', '40000', '40004', '4');
INSERT INTO `role_permissions` VALUES ('1214', '1', '40000', '40005', '1');
INSERT INTO `role_permissions` VALUES ('1215', '1', '40000', '40005', '2');
INSERT INTO `role_permissions` VALUES ('1216', '1', '40000', '40005', '3');
INSERT INTO `role_permissions` VALUES ('1217', '1', '40000', '40005', '4');
INSERT INTO `role_permissions` VALUES ('1218', '1', '40000', '40006', '1');
INSERT INTO `role_permissions` VALUES ('1219', '1', '40000', '40006', '2');
INSERT INTO `role_permissions` VALUES ('1220', '1', '40000', '40006', '3');
INSERT INTO `role_permissions` VALUES ('1221', '1', '40000', '40006', '4');
INSERT INTO `role_permissions` VALUES ('1222', '1', '40000', '40007', '1');
INSERT INTO `role_permissions` VALUES ('1223', '1', '40000', '40007', '2');
INSERT INTO `role_permissions` VALUES ('1224', '1', '40000', '40007', '3');
INSERT INTO `role_permissions` VALUES ('1225', '1', '40000', '40007', '4');
INSERT INTO `role_permissions` VALUES ('1226', '1', '40000', '40008', '1');
INSERT INTO `role_permissions` VALUES ('1227', '1', '40000', '40008', '2');
INSERT INTO `role_permissions` VALUES ('1228', '1', '40000', '40008', '3');
INSERT INTO `role_permissions` VALUES ('1229', '1', '40000', '40008', '4');
INSERT INTO `role_permissions` VALUES ('1230', '1', '40000', '40009', '1');
INSERT INTO `role_permissions` VALUES ('1231', '1', '40000', '40009', '2');
INSERT INTO `role_permissions` VALUES ('1232', '1', '40000', '40009', '3');
INSERT INTO `role_permissions` VALUES ('1233', '1', '40000', '40009', '4');
INSERT INTO `role_permissions` VALUES ('1234', '1', '40000', '40010', '6');
INSERT INTO `role_permissions` VALUES ('1235', '1', '40000', '40011', '6');
INSERT INTO `role_permissions` VALUES ('1236', '1', '40000', '40012', '6');
INSERT INTO `role_permissions` VALUES ('1237', '1', '50000', '50001', '1');
INSERT INTO `role_permissions` VALUES ('1238', '1', '50000', '50001', '2');
INSERT INTO `role_permissions` VALUES ('1239', '1', '50000', '50001', '3');
INSERT INTO `role_permissions` VALUES ('1240', '1', '50000', '50001', '4');
INSERT INTO `role_permissions` VALUES ('1241', '1', '50000', '50002', '1');
INSERT INTO `role_permissions` VALUES ('1242', '1', '50000', '50002', '2');
INSERT INTO `role_permissions` VALUES ('1243', '1', '50000', '50002', '3');
INSERT INTO `role_permissions` VALUES ('1244', '1', '50000', '50002', '4');
INSERT INTO `role_permissions` VALUES ('1245', '1', '50000', '50003', '5');
INSERT INTO `role_permissions` VALUES ('1246', '1', '70000', '70001', '8');
INSERT INTO `role_permissions` VALUES ('1247', '1', '70000', '70001', '1');
INSERT INTO `role_permissions` VALUES ('1248', '1', '70000', '70001', '2');
INSERT INTO `role_permissions` VALUES ('1249', '1', '70000', '70001', '3');
INSERT INTO `role_permissions` VALUES ('1250', '1', '70000', '70001', '4');
INSERT INTO `role_permissions` VALUES ('1251', '1', '70000', '70002', '8');
INSERT INTO `role_permissions` VALUES ('1252', '1', '70000', '70002', '1');
INSERT INTO `role_permissions` VALUES ('1253', '1', '70000', '70002', '2');
INSERT INTO `role_permissions` VALUES ('1254', '1', '70000', '70002', '3');
INSERT INTO `role_permissions` VALUES ('1255', '1', '70000', '70002', '4');

-- Table: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `roles` VALUES ('1', 'جميع الصلاحيات', '');

-- Table: section
DROP TABLE IF EXISTS `section`;
CREATE TABLE `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `manager` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `manager` (`manager`),
  CONSTRAINT `section_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE,
  CONSTRAINT `section_ibfk_2` FOREIGN KEY (`manager`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `section` VALUES ('1', 'قسم جديد', 'قسم جديد', '1', '1', '55', '555', '1');

-- Table: setting
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `country_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `web_site` varchar(255) NOT NULL,
  `local_time` varchar(255) NOT NULL,
  `header_img` varchar(255) NOT NULL,
  `footer_img` varchar(255) NOT NULL,
  `postal_code` varchar(30) NOT NULL,
  `post_number` varchar(30) NOT NULL,
  `is_mobile_attendance_enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `setting` VALUES ('1', 'admin@admin.net', 'HR System', 'Bbw9fJIAItNuC4hFJogzeLlkeDt3z9yN.png', '123456789', '123456789', '1', '137', 'Default', '2', 'https://companypro.com/', '1', '', '', '00966', '00966', NULL);

-- Table: sms_provider
DROP TABLE IF EXISTS `sms_provider`;
CREATE TABLE `sms_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(50) CHARACTER SET utf8 NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sender` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sendgrid_username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `sendgrid_password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `paypal_type` tinyint(4) NOT NULL,
  `sandbox` varchar(255) NOT NULL,
  `production` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Table: staff
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `members` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: system_config
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: tasks
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` varchar(255) NOT NULL DEFAULT 'NEW',
  `start_at` date NOT NULL,
  `end_at` date NOT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'NORMAL',
  `assigned_to` int(11) NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `progress` double NOT NULL DEFAULT '0',
  `attachment` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tasks_user_FK_1` (`assigned_by`),
  KEY `tasks_user_FK` (`assigned_to`),
  CONSTRAINT `tasks_user_FK` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_user_FK_1` FOREIGN KEY (`assigned_by`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: termination_reason
DROP TABLE IF EXISTS `termination_reason`;
CREATE TABLE `termination_reason` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  `ffect_end_service` tinyint(4) NOT NULL,
  `contract_type` tinyint(4) NOT NULL,
  `default_reward` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `first_year` float NOT NULL DEFAULT '0',
  `secontd_year` float NOT NULL DEFAULT '0',
  `above_five_year` float NOT NULL DEFAULT '0',
  `above_ten_year` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT INTO `termination_reason` VALUES ('1', 'اتفاق العامل وصاحب العمل على انهاء العقد', '1', '2', '50', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('2', 'فسخ العقد من قبل صاحب العمل', '1', '1', '50', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('3', '       فسخ العقد من قبل صاحب العمل لأحد الحالات الواردة في المادة (80)     ', '2', '1', '0', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('4', '       ترك العامل العمل نتيجة لقوة قاهرة     ', '1', '1', '50', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('5', '       إنهاء العاملة لعقد العمل خلال ستة أشهر من عقد الزواج أو خلال ثلاثة أشهر من الوضع     ', '1', '1', '50', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('6', '       ترك العامل العمل لأحد الحالات الواردة في المادة (81)     ', '1', '1', '50', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('7', ' ترك العامل العمل دون تقديم استقالة لغير الحالات الواردة في المادة (81)     ', '2', '1', '0', '1', '0', '0', '0', '0');
INSERT INTO `termination_reason` VALUES ('8', 'استقالة العامل ', '1', '1', '50', '2', '0', '33.3333', '66.6667', '100');
INSERT INTO `termination_reason` VALUES ('9', 'الاخلال بما ورد في العقد ', '2', '2', '20', '1', '5', '10', '20', '50');

-- Table: termination_setting
DROP TABLE IF EXISTS `termination_setting`;
CREATE TABLE `termination_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `termination_auto_approve` tinyint(4) NOT NULL,
  `termination_approval_level1` int(11) NOT NULL,
  `termination_approval_level2` int(11) NOT NULL,
  `termination_approval_level3` int(11) NOT NULL,
  `term_reward_from` tinyint(4) NOT NULL,
  `reward_for_employee_less_one_year` tinyint(4) NOT NULL,
  `term_reward_in_days_less_5years` int(11) NOT NULL,
  `reward_for_employee_more_5_years` tinyint(4) NOT NULL,
  `term_reward_in_days_more5years` int(11) NOT NULL,
  `term_reward_in_days_less_5years_custom` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: unit
DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `section_id` int(11) NOT NULL,
  `employee` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `employee` (`employee`),
  CONSTRAINT `unit_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE,
  CONSTRAINT `unit_ibfk_2` FOREIGN KEY (`employee`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `unit` VALUES ('1', 'وحده تجربة', 'وحده تجربة', '1', '1', '', '', '1');

-- Table: user
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(250) CHARACTER SET utf8 NOT NULL,
  `password_hash` varchar(250) CHARACTER SET utf8 NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `password_reset_token` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(250) CHARACTER SET utf8 NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8 NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `activation_code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `user_type` tinyint(4) NOT NULL DEFAULT '2',
  `avatar` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  `last_login_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fingerprint` varchar(500) DEFAULT NULL,
  `fcm_token` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `user` VALUES ('1', 'admin', 'adc763ad31c3ad32a6bdd95a22a817f0dd34265b', 'oJchu1kU-_nJSF4aM-kDfUeaKIANk2Cx', '', 'admin@gmail.com', '10', '1494888883', '1671630634', '+201147368438', 'مدير النظام', '', '0', '2', '14949048781929518.png', '', '2024-06-30 13:19:47', NULL, NULL);

-- Table: user_advance
DROP TABLE IF EXISTS `user_advance`;
CREATE TABLE `user_advance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `advance_id` int(11) NOT NULL,
  `advance_value` float NOT NULL,
  `payment_method` tinyint(4) NOT NULL,
  `monthly_installment` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `payement_month` tinyint(4) NOT NULL,
  `start_date` date NOT NULL,
  `voucher` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `advance_id` (`advance_id`),
  KEY `currency_id` (`currency_id`),
  KEY `user_advance_ibfk_3` (`voucher`),
  CONSTRAINT `user_advance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_advance_ibfk_2` FOREIGN KEY (`advance_id`) REFERENCES `advance_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_advance_ibfk_3` FOREIGN KEY (`voucher`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_advance_ibfk_4` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: user_deduction
DROP TABLE IF EXISTS `user_deduction`;
CREATE TABLE `user_deduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `deduction_id` int(11) NOT NULL,
  `deduction_type` tinyint(4) NOT NULL,
  `deduction_value` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deduction_id` (`deduction_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `user_deduction_ibfk_1` FOREIGN KEY (`deduction_id`) REFERENCES `deduction_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_deduction_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: user_family
DROP TABLE IF EXISTS `user_family`;
CREATE TABLE `user_family` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `identity_id` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `gender` tinyint(4) NOT NULL,
  `relationship_list_id` int(11) NOT NULL,
  `medical_list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `relationship_list_id` (`relationship_list_id`),
  KEY `medical_list_id` (`medical_list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: user_provision
DROP TABLE IF EXISTS `user_provision`;
CREATE TABLE `user_provision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provision_id` int(11) NOT NULL,
  `provision_value` float NOT NULL,
  `currency_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `provision_id` (`provision_id`),
  KEY `currency_id` (`currency_id`),
  CONSTRAINT `user_provision_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_provision_ibfk_2` FOREIGN KEY (`provision_id`) REFERENCES `provision_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_provision_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: vacation_planner
DROP TABLE IF EXISTS `vacation_planner`;
CREATE TABLE `vacation_planner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vacation_planner_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Table: violation_data
DROP TABLE IF EXISTS `violation_data`;
CREATE TABLE `violation_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `salary_year` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salary_month` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `month` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `year` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `day` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `deduct_type` int(11) NOT NULL DEFAULT '1' COMMENT '1 غياب\n2- تاخير\n3- عدم توقيع حضور\n4- عدم توقيع انصراف\n',
  `deduct_value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `deduct_currency` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deduct_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `is_active` tinyint(1) DEFAULT '1',
  `deactive_reasone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `job_time` int(11) DEFAULT NULL,
  `closed` tinyint(1) DEFAULT NULL,
  `posted` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_emp_day_type` (`emp_id`,`year`,`month`,`day`,`deduct_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Table: violation_group
DROP TABLE IF EXISTS `violation_group`;
CREATE TABLE `violation_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `description_en` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `violation_group` VALUES ('1', 'المدير التنفيذي', 'Executive Manager', 'القيام بأداء جميع المهمات و المسئوليات المنوطة بالمدير التنفيذي ، و المشاركة الفعالة فى تحديد و صياغة الأهداف، و كذلك تخطيط و تنظيم سير العمل بالمنشأة بما يضمن تحقيق الأهداف المحددة', 'To perform all the tasks and responsibilities of the Executive manager , and active participation in the identification and formulation of objectives, as well as planning and organizing workflow facility to ensure the achievement of the targets set', '1');
INSERT INTO `violation_group` VALUES ('2', 'مخالفات حكومية ', 'مخالفات حكومية ', 'انتهاء الاقامة والجوازات والهروب ', '', '1');

-- Table: violation_setting
DROP TABLE IF EXISTS `violation_setting`;
CREATE TABLE `violation_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `max_employee_reply_period` tinyint(4) NOT NULL,
  `absense_deduct_count` int(11) DEFAULT '1' COMMENT '1 يوم\n2 يوم ونصف\n3 يومان',
  `max_allowd_forget_finger` int(11) DEFAULT '0' COMMENT 'عدد مرات نسيان البصمة\nالافتراضي 0 غير مسموح',
  `forget_finger_deduct` int(11) DEFAULT '1' COMMENT '1 نصف يوم\n2 فترة\n3 يوم كامل',
  `violation_calculation_day` int(11) DEFAULT '31',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `violation_setting` VALUES ('1', '3', '1', '0', '1', '31');

-- Table: violation_type
DROP TABLE IF EXISTS `violation_type`;
CREATE TABLE `violation_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `forgiven_period` tinyint(4) NOT NULL,
  `description` text NOT NULL,
  `violation_group_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `violation_group_id` (`violation_group_id`),
  CONSTRAINT `violation_type_ibfk_1` FOREIGN KEY (`violation_group_id`) REFERENCES `violation_group` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `violation_type` VALUES ('1', 'التدخين أثناء العمل', 'Smoking in-house', '3', '', '1', '1');

-- Table: work_schedule
DROP TABLE IF EXISTS `work_schedule`;
CREATE TABLE `work_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_time_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `day` varchar(200) NOT NULL,
  `tow_date` tinyint(4) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_hour` time NOT NULL,
  `allowed_early_time` time NOT NULL,
  `allowed_late_time` time NOT NULL,
  `deduct_late_hours_type` tinyint(4) NOT NULL,
  `allowed_early_end_time` time NOT NULL,
  `allowed_out_end_time` time NOT NULL,
  `count_extra_hours` tinyint(4) NOT NULL,
  `Max_Allowd_late_time` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_time_id` (`job_time_id`),
  CONSTRAINT `work_schedule_ibfk_1` FOREIGN KEY (`job_time_id`) REFERENCES `job_time` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `work_schedule` VALUES ('1', '1', 'فتره دوام كامل', '1', '0', '08:00:00', '17:00:00', '08:00:00', '00:04:00', '00:04:00', '0', '00:04:00', '00:04:00', '0', NULL);
INSERT INTO `work_schedule` VALUES ('2', '1', 'فترة دوام كامل', '2,3,4,5,6', '0', '08:00:00', '17:00:00', '00:05:40', '08:00:00', '20:10:00', '1', '17:10:00', '17:00:00', '1', NULL);

-- Table: work_schedule_logs
DROP TABLE IF EXISTS `work_schedule_logs`;
CREATE TABLE `work_schedule_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(250) DEFAULT NULL,
  `employee_name` varchar(250) DEFAULT NULL,
  `is_manual` bit(1) DEFAULT b'0',
  `manual_emp_id` int(11) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `type` enum('IN','OUT') DEFAULT NULL,
  `ip_address` varchar(250) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `finger_device_no` varchar(12) DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `no` int(11) DEFAULT '0',
  `device_name` varchar(200) DEFAULT NULL,
  `device_no` varchar(5) DEFAULT NULL,
  `jobplace_id` int(11) DEFAULT NULL,
  `posted` bit(1) DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET FOREIGN_KEY_CHECKS=1;
