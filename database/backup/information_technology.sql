/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50627
Source Host           : localhost:3306
Source Database       : information_technology

Target Server Type    : MYSQL
Target Server Version : 50627
File Encoding         : 65001

Date: 2016-01-25 11:04:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `es_expense`
-- ----------------------------
DROP TABLE IF EXISTS `es_expense`;
CREATE TABLE `es_expense` (
  `expense_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `particulars` text COLLATE utf8_unicode_ci,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `expense_signatory_id` int(11) NOT NULL DEFAULT '0',
  `authority_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approval_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requested_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_id`),
  KEY `branch_id` (`branch_id`),
  KEY `department_id` (`department_id`),
  KEY `requested_by` (`requested_by`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of es_expense
-- ----------------------------
INSERT INTO `es_expense` VALUES ('2', 'REFORMAT PC', '77', '0', '300.00', '2', '', '123456789', '2015-12-29 00:00:00', '1506057', '1503108', '2015-12-29 13:30:21');
INSERT INTO `es_expense` VALUES ('3', '45 RJ45', '0', '5', '100.00', '1', '', '123123123', '2015-12-29 00:00:00', '0808061', '1503108', '2015-12-29 14:08:47');

-- ----------------------------
-- Table structure for `es_expense_detail`
-- ----------------------------
DROP TABLE IF EXISTS `es_expense_detail`;
CREATE TABLE `es_expense_detail` (
  `expense_detail_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_summary_id` int(11) NOT NULL DEFAULT '0',
  `repair_hardware_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_detail_id`),
  KEY `expense_summary_id` (`expense_summary_id`),
  KEY `repair_hardware_id` (`repair_hardware_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of es_expense_detail
-- ----------------------------
INSERT INTO `es_expense_detail` VALUES ('1', '1', '3', '2', '700.50', 'acer brand', '2016-01-11 01:19:46');
INSERT INTO `es_expense_detail` VALUES ('2', '2', '1', '1', '5000.00', 'service desktop', '2016-01-11 01:44:54');
INSERT INTO `es_expense_detail` VALUES ('3', '3', '5', '1', '400.00', '', '2016-01-11 01:49:01');
INSERT INTO `es_expense_detail` VALUES ('4', '3', '7', '1', '250.00', 'test', '2016-01-11 03:22:07');
INSERT INTO `es_expense_detail` VALUES ('5', '3', '6', '1', '45.00', '', '2016-01-11 03:24:24');
INSERT INTO `es_expense_detail` VALUES ('6', '3', '3', '1', '34.00', 'fdadfa', '2016-01-11 03:29:45');
INSERT INTO `es_expense_detail` VALUES ('7', '6', '5', '1', '888.00', '888 <br/><strong>\n										#APPROVED_AMOUNT: 888<br/>\n										#APPROVAL_NUMBER: 888<br/>\n										#AUTHORITY_NUMBER: 888\n									</strong>', '2016-01-18 15:30:13');
INSERT INTO `es_expense_detail` VALUES ('8', '6', '5', '1', '999.00', '999<table class=\'table bordered table-condensed\'>\n									<thead></thead>\n									<tbody>\n										<tr>\n											<td>APPROVED AMOUNT</td><td>999</td>\n										</tr>\n										<tr>\n											<td', '2016-01-18 16:10:53');
INSERT INTO `es_expense_detail` VALUES ('9', '6', '5', '1', '123.00', '123<table class=\'span4 table table-bordered table-condensed table-striped\'>\n									<thead></thead>\n									<tbody>\n										<tr>\n											<td style=\'text-align:right;\'>APPROVED BY</td><td></td>\n', '2016-01-18 16:20:19');
INSERT INTO `es_expense_detail` VALUES ('10', '6', '5', '1', '456.00', '456 <br/><strong>\n										#APPROVED_BY: <br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 456<br/>\n										#APPROVAL_NUMBER: 456<br/>\n										#AUTHORITY_NUMBER: 456\n			', '2016-01-18 16:46:21');
INSERT INTO `es_expense_detail` VALUES ('11', '6', '5', '1', '456.00', '456 <br/><strong>\n										#APPROVED_BY: <br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 456<br/>\n										#APPROVAL_NUMBER: 456<br/>\n										#AUTHORITY_NUMBER: 456\n			', '2016-01-18 16:47:41');
INSERT INTO `es_expense_detail` VALUES ('12', '6', '5', '1', '8978.00', '978 <br/><strong>\n										#APPROVED_BY: 1<br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 8978<br/>\n										#APPROVAL_NUMBER: 7897<br/>\n										#AUTHORITY_NUMBER: 787\n', '2016-01-18 16:48:40');
INSERT INTO `es_expense_detail` VALUES ('13', '6', '5', '1', '99999999.99', '7867 <br/><strong>\n										#APPROVED_BY: Ngan, Suzelle<br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 5,785,768,567.00<br/>\n										#APPROVAL_NUMBER: 867<br/>\n									', '2016-01-18 16:49:54');

-- ----------------------------
-- Table structure for `es_expense_signatory`
-- ----------------------------
DROP TABLE IF EXISTS `es_expense_signatory`;
CREATE TABLE `es_expense_signatory` (
  `expense_signatory_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `complete_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '0',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_signatory_id`),
  KEY `id_number` (`id_number`),
  KEY `complete_name` (`complete_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of es_expense_signatory
-- ----------------------------
INSERT INTO `es_expense_signatory` VALUES ('1', '', 'Tanoja, Jenny', '1', '2015-12-29 08:12:10');
INSERT INTO `es_expense_signatory` VALUES ('2', null, 'Ngan, Suzelle', '1', '2015-12-29 08:12:10');

-- ----------------------------
-- Table structure for `es_expense_summary`
-- ----------------------------
DROP TABLE IF EXISTS `es_expense_summary`;
CREATE TABLE `es_expense_summary` (
  `expense_summary_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_series` int(2) NOT NULL DEFAULT '0',
  `expense_number` int(11) NOT NULL DEFAULT '0',
  `expense_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `id_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` int(11) NOT NULL DEFAULT '0',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `authority_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approval_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requested_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `repair_summary_id` int(11) NOT NULL DEFAULT '0',
  `date_approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_summary_id`),
  KEY `expense_series` (`expense_series`),
  KEY `expense_number` (`expense_number`),
  KEY `expense_code` (`expense_code`),
  KEY `branch_id` (`branch_id`),
  KEY `id_number` (`id_number`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of es_expense_summary
-- ----------------------------
INSERT INTO `es_expense_summary` VALUES ('1', '16', '1', '16-00001', '77', null, '0', '2', '312', '123', '1304122', '14162', '0', '2016-01-01 00:00:00', '2016-01-11 01:19:46');
INSERT INTO `es_expense_summary` VALUES ('2', '16', '2', '16-00002', '0', '1503108', '5', '1', '34234', '4564564', '1503081', '1503108', '0', '2016-01-11 00:00:00', '2016-01-11 01:44:53');
INSERT INTO `es_expense_summary` VALUES ('3', '16', '3', '16-00003', '0', '0808061', '5', '1', '34534', '756756', '603018', '1503108', '0', '2016-01-11 00:00:00', '2016-01-11 01:49:01');
INSERT INTO `es_expense_summary` VALUES ('6', '16', '4', '16-00004', '0', '1503108', '5', '2', '00', '000', '1503108', '1503108', '4', '2016-01-18 00:00:00', '2016-01-18 14:24:13');

-- ----------------------------
-- Table structure for `rf_department_module`
-- ----------------------------
DROP TABLE IF EXISTS `rf_department_module`;
CREATE TABLE `rf_department_module` (
  `department_module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N/A',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `module_code` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NON',
  `segment_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '0',
  `signatory_id_number` int(11) NOT NULL DEFAULT '0',
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_module_id`),
  KEY `module_name` (`module_name`),
  KEY `department_id` (`department_id`),
  KEY `module_code` (`module_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rf_department_module
-- ----------------------------
INSERT INTO `rf_department_module` VALUES ('1', 'Maintenance', '5', 'NON', 'maintenance', '1', '0', '0000-00-00 00:00:00', '2015-12-08 11:23:35');
INSERT INTO `rf_department_module` VALUES ('2', 'Repairs', '5', 'NON', 'repairs', '1', '0', '0000-00-00 00:00:00', '2015-12-08 13:22:30');
INSERT INTO `rf_department_module` VALUES ('3', 'Expenses', '5', 'NON', 'expenses', '1', '0', '0000-00-00 00:00:00', '2015-12-19 11:33:22');

-- ----------------------------
-- Table structure for `rf_department_module_submodule`
-- ----------------------------
DROP TABLE IF EXISTS `rf_department_module_submodule`;
CREATE TABLE `rf_department_module_submodule` (
  `department_module_submodule_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_module_id` int(11) NOT NULL DEFAULT '0',
  `submodule_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `submodule_url` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priority_order` int(2) NOT NULL DEFAULT '0',
  `is_active` tinyint(2) NOT NULL DEFAULT '0',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_module_submodule_id`),
  KEY `department_module_id` (`department_module_id`),
  KEY `submodule_name` (`submodule_name`),
  KEY `submodule_url` (`submodule_url`),
  KEY `priority_order` (`priority_order`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rf_department_module_submodule
-- ----------------------------
INSERT INTO `rf_department_module_submodule` VALUES ('1', '1', 'Repair Hardware', '/repair_hardware', '1', '1', '2015-12-08 11:29:30');
INSERT INTO `rf_department_module_submodule` VALUES ('2', '2', 'List of Repairs', '/listing', '1', '1', '2015-12-08 13:23:06');

-- ----------------------------
-- Table structure for `rf_repair_hardware`
-- ----------------------------
DROP TABLE IF EXISTS `rf_repair_hardware`;
CREATE TABLE `rf_repair_hardware` (
  `repair_hardware_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `repair_hardware_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`repair_hardware_id`),
  KEY `repair_hardware_name` (`repair_hardware_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rf_repair_hardware
-- ----------------------------
INSERT INTO `rf_repair_hardware` VALUES ('1', 'CPU', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('2', 'PRINTER', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('3', 'SCANNER', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('4', 'MONITOR', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('5', 'KEYBOARD', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('6', 'MOUSE', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('7', 'AVR', null, '2015-12-08 14:21:22');
INSERT INTO `rf_repair_hardware` VALUES ('8', 'CARD READER', 'CARD READER', '2015-12-14 08:45:58');

-- ----------------------------
-- Table structure for `rf_repair_status`
-- ----------------------------
DROP TABLE IF EXISTS `rf_repair_status`;
CREATE TABLE `rf_repair_status` (
  `repair_status_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `repair_status` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priority_order` int(2) unsigned NOT NULL DEFAULT '99',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`repair_status_id`),
  KEY `repair_status` (`repair_status`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rf_repair_status
-- ----------------------------
INSERT INTO `rf_repair_status` VALUES ('1', 'REPORTED CONCERN', '1', '2016-01-04 10:16:14');
INSERT INTO `rf_repair_status` VALUES ('2', 'RECEIVED FROM BRANCH', '13', '2016-01-04 10:16:14');
INSERT INTO `rf_repair_status` VALUES ('3', 'CHECKING', '2', '2016-01-04 10:16:14');
INSERT INTO `rf_repair_status` VALUES ('4', 'TROUBLESHOOTING', '3', '2016-01-04 10:16:14');
INSERT INTO `rf_repair_status` VALUES ('5', 'FOR REPLACEMENT', '4', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('6', 'FOR APPROVAL', '5', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('7', 'FOR P.O.', '6', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('8', 'TESTING / BURN-IN', '7', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('9', 'COMPLETED', '8', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('10', 'FOR DELIVERY', '9', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('11', 'IN TRANSIT', '10', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('12', 'RECEIVED BY BRANCH', '11', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('13', 'FOR BACKLOAD', '12', '2016-01-04 10:16:15');
INSERT INTO `rf_repair_status` VALUES ('14', 'CLOSED', '14', '2016-01-04 10:16:15');

-- ----------------------------
-- Table structure for `rs_repair_detail`
-- ----------------------------
DROP TABLE IF EXISTS `rs_repair_detail`;
CREATE TABLE `rs_repair_detail` (
  `repair_detail_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `repair_summary_id` int(11) NOT NULL DEFAULT '0',
  `repair_hardware_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `description` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `peripherals` text COLLATE utf8_unicode_ci,
  `tr_number_out` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `current_status_id` tinyint(2) NOT NULL DEFAULT '1',
  `date_completed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`repair_detail_id`),
  KEY `repair_summary_id` (`repair_summary_id`),
  KEY `repair_hardware_id` (`repair_hardware_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rs_repair_detail
-- ----------------------------
INSERT INTO `rs_repair_detail` VALUES ('1', '1', '1', '1', 'fsdfsd', 'sdfsdf', '', '11', '0000-00-00 00:00:00', '2015-12-11 16:10:03');
INSERT INTO `rs_repair_detail` VALUES ('2', '1', '3', '1', 'dfsdfs', 'dsfsdfsd', '123123', '9', '0000-00-00 00:00:00', '2015-12-11 16:10:13');
INSERT INTO `rs_repair_detail` VALUES ('3', '2', '5', '1', 'a4tech', 'kulang', '', '11', '0000-00-00 00:00:00', '2015-12-11 16:42:37');
INSERT INTO `rs_repair_detail` VALUES ('4', '2', '1', '1', 'nec', 'no power', '', '11', '0000-00-00 00:00:00', '2015-12-11 16:43:00');
INSERT INTO `rs_repair_detail` VALUES ('5', '3', '3', '1', 'dfsdfs', 'sdfsdf', '', '14', '0000-00-00 00:00:00', '2015-12-12 08:39:45');
INSERT INTO `rs_repair_detail` VALUES ('6', '4', '5', '1', '24234234', 'fasdf asdfasdfad', '', '7', '0000-00-00 00:00:00', '2015-12-12 09:15:01');
INSERT INTO `rs_repair_detail` VALUES ('7', '1', '8', '1', '', 'none', null, '1', '0000-00-00 00:00:00', '2015-12-14 09:51:05');
INSERT INTO `rs_repair_detail` VALUES ('10', '5', '3', '1', 'HP', '1 power cable', '', '6', '0000-00-00 00:00:00', '2015-12-14 15:31:51');
INSERT INTO `rs_repair_detail` VALUES ('11', '5', '1', '1', '', '', '', '6', '0000-00-00 00:00:00', '2015-12-14 15:32:31');
INSERT INTO `rs_repair_detail` VALUES ('12', '6', '5', '1', 'cd r king brand', '', '', '8', '0000-00-00 00:00:00', '2015-12-17 13:36:27');
INSERT INTO `rs_repair_detail` VALUES ('13', '7', '1', '1', 'nec', 'cable', null, '1', '0000-00-00 00:00:00', '2015-12-17 14:11:07');
INSERT INTO `rs_repair_detail` VALUES ('14', '8', '8', '1', 'cd r king', '', null, '1', '0000-00-00 00:00:00', '2015-12-17 14:45:09');
INSERT INTO `rs_repair_detail` VALUES ('15', '9', '7', '1', '', '', null, '1', '0000-00-00 00:00:00', '2015-12-17 14:52:51');
INSERT INTO `rs_repair_detail` VALUES ('16', '10', '1', '1', 'nec', 'cable', '', '5', '0000-00-00 00:00:00', '2015-12-17 22:29:57');
INSERT INTO `rs_repair_detail` VALUES ('17', '11', '6', '1', '', '', '', '1', '0000-00-00 00:00:00', '2015-12-29 16:08:55');
INSERT INTO `rs_repair_detail` VALUES ('18', '12', '6', '1', 'not working', 'a4tech', '', '14', '0000-00-00 00:00:00', '2016-01-04 09:29:36');
INSERT INTO `rs_repair_detail` VALUES ('19', '13', '4', '1', 'Samsung 14\" LCD', '1 power cord and adaptor\n1 socket adaptor', null, '1', '0000-00-00 00:00:00', '2016-01-14 14:57:23');

-- ----------------------------
-- Table structure for `rs_repair_remark`
-- ----------------------------
DROP TABLE IF EXISTS `rs_repair_remark`;
CREATE TABLE `rs_repair_remark` (
  `repair_remark_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `repair_detail_id` int(11) NOT NULL DEFAULT '0',
  `repair_status_id` int(11) NOT NULL DEFAULT '0',
  `proposed_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `approved_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `date_approved` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_branch_expense` tinyint(2) NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8_unicode_ci,
  `created_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`repair_remark_id`),
  KEY `repair_detail_id` (`repair_detail_id`),
  KEY `repair_status_id` (`repair_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rs_repair_remark
-- ----------------------------
INSERT INTO `rs_repair_remark` VALUES ('1', '1', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'TEst', '1503108', '2015-12-11 16:38:39');
INSERT INTO `rs_repair_remark` VALUES ('2', '2', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'asdfads fadsf asd', '1503108', '2015-12-11 16:38:43');
INSERT INTO `rs_repair_remark` VALUES ('3', '1', '2', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test 2', '1503108', '2015-12-11 16:39:16');
INSERT INTO `rs_repair_remark` VALUES ('4', '2', '2', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'fsdfs fsfasdfa', '1503108', '2015-12-11 16:39:25');
INSERT INTO `rs_repair_remark` VALUES ('5', '3', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'now', '1503108', '2015-12-11 16:43:51');
INSERT INTO `rs_repair_remark` VALUES ('6', '3', '2', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'sira nga', '1503108', '2015-12-11 16:44:00');
INSERT INTO `rs_repair_remark` VALUES ('7', '4', '7', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test test', '1503108', '2015-12-11 16:44:38');
INSERT INTO `rs_repair_remark` VALUES ('8', '3', '3', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'df adfad fasdf asdfasdf asdfasd fasdf asdfa sdfasd fasdf asdf asdf asdf asdfa sdfasd fasdf asd fasd fasd fasdf', '1503108', '2015-12-11 16:48:49');
INSERT INTO `rs_repair_remark` VALUES ('9', '1', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'adfasdfad', '1503108', '2015-12-11 17:33:16');
INSERT INTO `rs_repair_remark` VALUES ('10', '2', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'dfadf asdfasd', '1503108', '2015-12-11 17:33:26');
INSERT INTO `rs_repair_remark` VALUES ('11', '1', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'fsf', '1503108', '2015-12-11 17:38:42');
INSERT INTO `rs_repair_remark` VALUES ('12', '1', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'dfadsfa sdfasd', '1503108', '2015-12-11 17:39:06');
INSERT INTO `rs_repair_remark` VALUES ('13', '2', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'dfasdf asdfasd', '1503108', '2015-12-11 17:39:12');
INSERT INTO `rs_repair_remark` VALUES ('14', '1', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'aadfasdf sadfasdf', '1503108', '2015-12-11 17:40:40');
INSERT INTO `rs_repair_remark` VALUES ('15', '2', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'fasdf asdfasdf', '1503108', '2015-12-14 14:37:28');
INSERT INTO `rs_repair_remark` VALUES ('16', '1', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'fadfas dasd fa <strong>#TR_NUMBER_OUT: 2123123</strong>', '1503108', '2015-12-14 14:47:05');
INSERT INTO `rs_repair_remark` VALUES ('17', '11', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test', '1503108', '2015-12-14 15:34:28');
INSERT INTO `rs_repair_remark` VALUES ('18', '11', '3', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'not working', '1503108', '2015-12-14 15:35:55');
INSERT INTO `rs_repair_remark` VALUES ('19', '3', '11', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', '', '1503108', '2015-12-15 08:10:43');
INSERT INTO `rs_repair_remark` VALUES ('20', '4', '11', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', '', '1503108', '2015-12-15 08:10:50');
INSERT INTO `rs_repair_remark` VALUES ('21', '2', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'Via LBC <strong>#TR_NUMBER_OUT: 666777</strong>', '1503108', '2015-12-15 08:56:38');
INSERT INTO `rs_repair_remark` VALUES ('22', '1', '11', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'received', '1503108', '2015-12-17 10:52:38');
INSERT INTO `rs_repair_remark` VALUES ('23', '2', '11', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'received', '1503108', '2015-12-17 10:52:53');
INSERT INTO `rs_repair_remark` VALUES ('24', '2', '11', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'received', '1503108', '2015-12-17 10:53:18');
INSERT INTO `rs_repair_remark` VALUES ('25', '2', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test <strong>#TR_NUMBER_OUT: 123123</strong>', '1503108', '2015-12-17 13:17:21');
INSERT INTO `rs_repair_remark` VALUES ('26', '12', '4', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'missing letters a,b,c - unrepairable', '1503108', '2015-12-17 13:37:43');
INSERT INTO `rs_repair_remark` VALUES ('27', '12', '5', '500.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'mahal dito', '1503108', '2015-12-17 13:40:17');
INSERT INTO `rs_repair_remark` VALUES ('28', '12', '6', '0.00', '300.00', '0', '0000-00-00 00:00:00', '0', 'ok na', '1503108', '2015-12-17 13:41:43');
INSERT INTO `rs_repair_remark` VALUES ('29', '12', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'ayos na', '1503108', '2015-12-17 13:41:59');
INSERT INTO `rs_repair_remark` VALUES ('30', '14', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'adfadf adfasdfadsfasdfadfad fasdf adfasd fasdf ad fadf adf adfa dfa dsfadf ads', '1503108', '2015-12-17 14:45:09');
INSERT INTO `rs_repair_remark` VALUES ('31', '15', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test', '1503108', '2015-12-17 14:52:52');
INSERT INTO `rs_repair_remark` VALUES ('32', '10', '6', '0.00', '12312.23', '0', '0000-00-00 00:00:00', '0', 'sdfsdf', '1503108', '2015-12-17 16:23:21');
INSERT INTO `rs_repair_remark` VALUES ('33', '11', '6', '0.00', '123.23', '0', '0000-00-00 00:00:00', '0', 'sdfsdfs', '1503108', '2015-12-17 16:23:35');
INSERT INTO `rs_repair_remark` VALUES ('34', '11', '6', '0.00', '333.56', '0', '0000-00-00 00:00:00', '0', 'sfdfsd', '1503108', '2015-12-17 16:49:20');
INSERT INTO `rs_repair_remark` VALUES ('35', '16', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'sira', '1503108', '2015-12-17 22:29:57');
INSERT INTO `rs_repair_remark` VALUES ('36', '16', '5', '5000.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'ok <strong>#PROPOSED_AMOUNT: 5000</strong>', '1503108', '2015-12-17 22:32:11');
INSERT INTO `rs_repair_remark` VALUES ('37', '17', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'defective mouse - for replacement', '1503108', '2015-12-29 16:08:55');
INSERT INTO `rs_repair_remark` VALUES ('38', '17', '5', '500.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'canvass price / logitech mouse <strong>#PROPOSED_AMOUNT: 500</strong>', '1503108', '2015-12-29 16:21:28');
INSERT INTO `rs_repair_remark` VALUES ('39', '17', '6', '0.00', '500.00', '0', '0000-00-00 00:00:00', '1', 'go go go <br/><strong>\n										#APPROVED_AMOUNT: 500<br/>\n										#APPROVAL_NUMBER: 123<br/>\n										#AUTHORITY_NUMBER: \n									</strong>', '1503108', '2015-12-29 16:22:09');
INSERT INTO `rs_repair_remark` VALUES ('40', '17', '8', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'bought', '1503108', '2015-12-29 16:22:36');
INSERT INTO `rs_repair_remark` VALUES ('41', '17', '12', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'defective mouse', '1503108', '2015-12-29 16:23:08');
INSERT INTO `rs_repair_remark` VALUES ('42', '17', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'sn#123123234 <strong>#TR_NUMBER_IN: 56789</strong>', '1503108', '2015-12-29 16:23:45');
INSERT INTO `rs_repair_remark` VALUES ('43', '18', '1', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'mouse not working', '1503108', '2016-01-04 09:29:36');
INSERT INTO `rs_repair_remark` VALUES ('44', '18', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test completed', '1503108', '2016-01-04 09:56:29');
INSERT INTO `rs_repair_remark` VALUES ('45', '18', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'rwerwer wer', '1503108', '2016-01-04 09:59:15');
INSERT INTO `rs_repair_remark` VALUES ('46', '18', '13', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'fg fgsdfsg', '1503108', '2016-01-04 10:02:30');
INSERT INTO `rs_repair_remark` VALUES ('47', '18', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'dfadfa ', '1503108', '2016-01-04 10:02:48');
INSERT INTO `rs_repair_remark` VALUES ('48', '18', '13', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'dfadf adsfadfad', '1503108', '2016-01-04 10:03:41');
INSERT INTO `rs_repair_remark` VALUES ('49', '18', '2', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'ok <strong>#TR_NUMBER_IN: dfasdfa</strong>', '1503108', '2016-01-04 10:07:23');
INSERT INTO `rs_repair_remark` VALUES ('50', '18', '14', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'done', '1503108', '2016-01-04 10:17:51');
INSERT INTO `rs_repair_remark` VALUES ('51', '5', '9', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'test', '1503108', '2016-01-11 01:58:40');
INSERT INTO `rs_repair_remark` VALUES ('52', '5', '2', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'fadfadfa <strong>#TR_NUMBER_IN: 234234</strong>', '1503108', '2016-01-11 01:59:06');
INSERT INTO `rs_repair_remark` VALUES ('53', '5', '14', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'done', '1503108', '2016-01-11 01:59:15');
INSERT INTO `rs_repair_remark` VALUES ('54', '19', '2', '0.00', '0.00', '0', '0000-00-00 00:00:00', '0', 'No power', '1503108', '2016-01-14 14:57:23');
INSERT INTO `rs_repair_remark` VALUES ('71', '6', '7', '0.00', '0.00', '2', '2016-01-18 00:00:00', '0', '000 <br/><strong>\n										#APPROVED_AMOUNT: 000<br/>\n										#APPROVAL_NUMBER: 000<br/>\n										#AUTHORITY_NUMBER: 00\n									</strong>', '1503108', '2016-01-18 14:24:12');
INSERT INTO `rs_repair_remark` VALUES ('72', '6', '7', '0.00', '1111.00', '1', '2016-01-18 00:00:00', '0', '111 <br/><strong>\n										#APPROVED_AMOUNT: 1111<br/>\n										#APPROVAL_NUMBER: 1111<br/>\n										#AUTHORITY_NUMBER: 111\n									</strong>', '1503108', '2016-01-18 15:22:06');
INSERT INTO `rs_repair_remark` VALUES ('73', '6', '7', '0.00', '222.00', '2', '2016-01-18 00:00:00', '0', '222 <br/><strong>\n										#APPROVED_AMOUNT: 222<br/>\n										#APPROVAL_NUMBER: 222<br/>\n										#AUTHORITY_NUMBER: 222\n									</strong>', '1503108', '2016-01-18 15:23:11');
INSERT INTO `rs_repair_remark` VALUES ('74', '6', '7', '0.00', '333.00', '2', '2016-01-18 00:00:00', '0', '333 <br/><strong>\n										#APPROVED_AMOUNT: 333<br/>\n										#APPROVAL_NUMBER: 333<br/>\n										#AUTHORITY_NUMBER: 333\n									</strong>', '1503108', '2016-01-18 15:24:40');
INSERT INTO `rs_repair_remark` VALUES ('75', '6', '7', '0.00', '444.00', '2', '2016-01-18 00:00:00', '0', '444 <br/><strong>\n										#APPROVED_AMOUNT: 444<br/>\n										#APPROVAL_NUMBER: 444<br/>\n										#AUTHORITY_NUMBER: 444\n									</strong>', '1503108', '2016-01-18 15:25:27');
INSERT INTO `rs_repair_remark` VALUES ('76', '6', '7', '0.00', '555.00', '1', '2016-01-18 00:00:00', '0', '555 <br/><strong>\n										#APPROVED_AMOUNT: 555<br/>\n										#APPROVAL_NUMBER: 555<br/>\n										#AUTHORITY_NUMBER: 555\n									</strong>', '1503108', '2016-01-18 15:26:47');
INSERT INTO `rs_repair_remark` VALUES ('77', '6', '7', '0.00', '666.00', '1', '2016-01-18 00:00:00', '0', '6666 <br/><strong>\n										#APPROVED_AMOUNT: 666<br/>\n										#APPROVAL_NUMBER: 6666<br/>\n										#AUTHORITY_NUMBER: 6666\n									</strong>', '1503108', '2016-01-18 15:28:28');
INSERT INTO `rs_repair_remark` VALUES ('78', '6', '7', '0.00', '777.00', '2', '2016-01-18 00:00:00', '0', '777 <br/><strong>\n										#APPROVED_AMOUNT: 777<br/>\n										#APPROVAL_NUMBER: 777<br/>\n										#AUTHORITY_NUMBER: 777\n									</strong>', '1503108', '2016-01-18 15:28:57');
INSERT INTO `rs_repair_remark` VALUES ('79', '6', '7', '0.00', '888.00', '1', '2016-01-18 00:00:00', '0', '888 <br/><strong>\n										#APPROVED_AMOUNT: 888<br/>\n										#APPROVAL_NUMBER: 888<br/>\n										#AUTHORITY_NUMBER: 888\n									</strong>', '1503108', '2016-01-18 15:30:13');
INSERT INTO `rs_repair_remark` VALUES ('80', '6', '7', '0.00', '999.00', '1', '2016-01-18 00:00:00', '0', '999<table class=\'table bordered table-condensed\'>\n									<thead></thead>\n									<tbody>\n										<tr>\n											<td>APPROVED AMOUNT</td><td>999</td>\n										</tr>\n										<tr>\n											<td>APPROVAL NUMBER</td><td>999</td>\n										</tr>\n										<tr>\n											<td>AUTHORITY NUMBER</td><td>999</td>\n										</tr>\n									</tbody>\n								 </table>', '1503108', '2016-01-18 16:10:53');
INSERT INTO `rs_repair_remark` VALUES ('81', '6', '7', '0.00', '123.00', '1', '2016-01-18 00:00:00', '0', '123<table class=\'span4 table table-bordered table-condensed table-striped\'>\n									<thead></thead>\n									<tbody>\n										<tr>\n											<td style=\'text-align:right;\'>APPROVED BY</td><td></td>\n										</tr>\n										<tr>\n											<td style=\'text-align:right;\'>APPROVED AMOUNT</td><td>123.00</td>\n										</tr>\n										<tr>\n											<td style=\'text-align:right;\'>APPROVAL NUMBER</td><td>123</td>\n										</tr>\n										<tr>\n											<td style=\'text-align:right;\'>AUTHORITY NUMBER</td><td>123</td>\n										</tr>\n										<tr>\n											<td style=\'text-align:right;\'>DATE APPROVED</td><td>2016-01-18</td>\n										</tr>\n									</tbody>\n								 </table>', '1503108', '2016-01-18 16:20:19');
INSERT INTO `rs_repair_remark` VALUES ('82', '6', '7', '0.00', '456.00', '2', '2016-01-18 00:00:00', '0', '456 <br/><strong>\n										#APPROVED_BY: <br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 456<br/>\n										#APPROVAL_NUMBER: 456<br/>\n										#AUTHORITY_NUMBER: 456\n									</strong>', '1503108', '2016-01-18 16:46:20');
INSERT INTO `rs_repair_remark` VALUES ('83', '6', '7', '0.00', '456.00', '2', '2016-01-18 00:00:00', '0', '456 <br/><strong>\n										#APPROVED_BY: <br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 456<br/>\n										#APPROVAL_NUMBER: 456<br/>\n										#AUTHORITY_NUMBER: 456\n									</strong>', '1503108', '2016-01-18 16:47:41');
INSERT INTO `rs_repair_remark` VALUES ('84', '6', '7', '0.00', '8978.00', '1', '2016-01-18 00:00:00', '0', '978 <br/><strong>\n										#APPROVED_BY: 1<br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 8978<br/>\n										#APPROVAL_NUMBER: 7897<br/>\n										#AUTHORITY_NUMBER: 787\n									</strong>', '1503108', '2016-01-18 16:48:40');
INSERT INTO `rs_repair_remark` VALUES ('85', '6', '7', '0.00', '99999999.99', '2', '2016-01-18 00:00:00', '0', '7867 <br/><strong>\n										#APPROVED_BY: Ngan, Suzelle<br/>\n										#DATE_APPROVED: 2016-01-18<br/>\n										#APPROVED_AMOUNT: 5,785,768,567.00<br/>\n										#APPROVAL_NUMBER: 867<br/>\n										#AUTHORITY_NUMBER: 878\n									</strong>', '1503108', '2016-01-18 16:49:54');

-- ----------------------------
-- Table structure for `rs_repair_summary`
-- ----------------------------
DROP TABLE IF EXISTS `rs_repair_summary`;
CREATE TABLE `rs_repair_summary` (
  `repair_summary_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `repair_series` int(2) NOT NULL DEFAULT '0',
  `repair_number` int(11) NOT NULL DEFAULT '0',
  `repair_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `id_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` int(11) NOT NULL DEFAULT '0',
  `received_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tr_number_in` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reported_concern` text COLLATE utf8_unicode_ci,
  `overall_status` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'OPEN',
  `date_received` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`repair_summary_id`),
  KEY `repair_series` (`repair_series`),
  KEY `repair_number` (`repair_number`),
  KEY `repair_code` (`repair_code`),
  KEY `branch_id` (`branch_id`),
  KEY `received_by` (`received_by`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of rs_repair_summary
-- ----------------------------
INSERT INTO `rs_repair_summary` VALUES ('1', '15', '1', '15-00001', '0', '1503108', '5', '1503108', '123123', 'fadsfsd fasdfasdf', 'COMPLETED', '0000-00-00 00:00:00', '2015-12-11 16:10:03');
INSERT INTO `rs_repair_summary` VALUES ('2', '15', '2', '15-00002', '77', null, '0', '1503108', '123123', 'sira', 'CLOSED', '0000-00-00 00:00:00', '2015-12-11 16:42:37');
INSERT INTO `rs_repair_summary` VALUES ('3', '15', '3', '15-00003', '0', '1503108', '5', '1503108', '123123', 'afdfasdfa', 'CLOSED', '2015-12-12 00:00:00', '2015-12-12 08:39:45');
INSERT INTO `rs_repair_summary` VALUES ('4', '15', '4', '15-00004', '0', '1503108', '5', '1503108', '', '', 'OPEN', '2015-12-12 00:00:00', '2015-12-12 09:15:00');
INSERT INTO `rs_repair_summary` VALUES ('5', '15', '5', '15-00005', '0', '1503108', '5', '1503108', '12345', '', 'OPEN', '2015-12-14 00:00:00', '2015-12-14 15:31:51');
INSERT INTO `rs_repair_summary` VALUES ('6', '15', '6', '15-00006', '58', null, '0', '1503108', '', 'Defective Keyboard reported last oct 1, 2015', 'COMPLETED', '2015-12-17 00:00:00', '2015-12-17 13:36:27');
INSERT INTO `rs_repair_summary` VALUES ('7', '15', '7', '15-00007', '77', null, '0', '1503108', '123123', 'sira', 'OPEN', '2015-12-17 00:00:00', '2015-12-17 14:11:07');
INSERT INTO `rs_repair_summary` VALUES ('8', '15', '8', '15-00008', '77', null, '0', '1503108', '444444', 'adfadf adfasdfadsfasdfadfad fasdf adfasd fasdf ad fadf adf adfa dfa dsfadf ads', 'OPEN', '2015-12-17 00:00:00', '2015-12-17 14:45:09');
INSERT INTO `rs_repair_summary` VALUES ('9', '15', '9', '15-00009', '228', null, '0', '1503108', '66666666', 'test', 'OPEN', '2015-12-17 00:00:00', '2015-12-17 14:52:51');
INSERT INTO `rs_repair_summary` VALUES ('10', '15', '10', '15-00010', '77', null, '0', '1503108', '12123123', 'sira', 'OPEN', '2015-12-17 00:00:00', '2015-12-17 22:29:57');
INSERT INTO `rs_repair_summary` VALUES ('11', '15', '11', '15-00011', '77', null, '0', '1503108', '', 'defective mouse - for replacement', 'CLOSED', '2015-12-29 00:00:00', '2015-12-29 16:08:55');
INSERT INTO `rs_repair_summary` VALUES ('12', '16', '1', '16-00001', '77', null, '0', '1503108', '', 'mouse not working', 'CLOSED', '2016-01-04 00:00:00', '2016-01-04 09:29:35');
INSERT INTO `rs_repair_summary` VALUES ('13', '16', '2', '16-00002', '14', null, '0', '1503108', '898989', 'No power', 'OPEN', '2016-01-14 00:00:00', '2016-01-14 14:57:23');

-- ----------------------------
-- Table structure for `tr_admin_log`
-- ----------------------------
DROP TABLE IF EXISTS `tr_admin_log`;
CREATE TABLE `tr_admin_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `origin` varchar(100) COLLATE utf8_unicode_ci DEFAULT 'mmpi',
  `module_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `table_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `details_before` text COLLATE utf8_unicode_ci,
  `details_after` text COLLATE utf8_unicode_ci,
  `remarks` text COLLATE utf8_unicode_ci,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `id_number` (`id_number`),
  KEY `module_name` (`module_name`),
  KEY `action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tr_admin_log
-- ----------------------------
INSERT INTO `tr_admin_log` VALUES ('1', '1503108', 'mmpi', 'MAINTENANCE-REPAIRS-HARDWARE', 'rf_repair_hardware', 'INSERT', '{\"id\":0,\"details\":[]}', '{\"id\":9,\"details\":{\"repair_details\":{\"repair_hardware_id\":\"9\",\"repair_hardware_name\":\"CARD READER\",\"description\":\"CARD READER\",\"insert_timestamp\":\"2015-12-14 08:50:16\"}}}', '', '2015-12-14 08:50:16');
INSERT INTO `tr_admin_log` VALUES ('2', '1503108', 'mmpi', 'REPAIRS-DEALERS', 'rs_repair_detail', 'DELETE', '{\"id\":false,\"details\":{\"repair_details\":null}}', '{\"id\":false,\"details\":[]}', '', '2016-01-11 03:20:25');

-- ----------------------------
-- View structure for `es_expense_view`
-- ----------------------------
DROP VIEW IF EXISTS `es_expense_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `es_expense_view` AS select `a`.`expense_id` AS `expense_id`,`a`.`particulars` AS `particulars`,`a`.`branch_id` AS `branch_id`,`b`.`branch_name` AS `branch_name`,`a`.`department_id` AS `department_id`,`c`.`department_name` AS `department_name`,`a`.`amount` AS `amount`,`a`.`expense_signatory_id` AS `expense_signatory_id`,`d`.`complete_name` AS `expense_signatory_name`,`a`.`authority_number` AS `authority_number`,`a`.`approval_number` AS `approval_number`,`a`.`date_approved` AS `date_approved`,`a`.`requested_by` AS `requested_by`,`e`.`complete_name` AS `requester_name`,`a`.`created_by` AS `created_by`,`a`.`insert_timestamp` AS `insert_timestamp` from ((((`information_technology`.`es_expense` `a` left join `human_relations`.`rf_branch` `b` on((`a`.`branch_id` = `b`.`branch_id`))) left join `human_relations`.`rf_department` `c` on((`a`.`department_id` = `c`.`department_id`))) left join `information_technology`.`es_expense_signatory` `d` on((`a`.`expense_signatory_id` = `d`.`expense_signatory_id`))) left join `human_relations`.`pm_employment_information_view` `e` on((`a`.`requested_by` = `e`.`id_number`)));
