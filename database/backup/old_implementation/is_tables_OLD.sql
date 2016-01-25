/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50627
Source Host           : localhost:3306
Source Database       : spare_parts

Target Server Type    : MYSQL
Target Server Version : 50627
File Encoding         : 65001

Date: 2016-01-19 13:50:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `is_dealer_request_detail_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_dealer_request_detail_OLD`;
CREATE TABLE `is_dealer_request_detail_OLD` (
  `dealer_request_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `dealer_request_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `srp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dealer_request_detail_id`),
  KEY `dealer_request_id` (`dealer_request_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_dealer_request_detail_OLD
-- ----------------------------

-- ----------------------------
-- Table structure for `is_dealer_request_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_dealer_request_OLD`;
CREATE TABLE `is_dealer_request_OLD` (
  `dealer_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_series` int(2) NOT NULL DEFAULT '0',
  `request_number` int(11) NOT NULL DEFAULT '0',
  `request_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dealer_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) NOT NULL DEFAULT '0',
  `chassis` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `engine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `purchase_order_number` int(11) NOT NULL DEFAULT '0',
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_approved_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `warehouse_received_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_receive_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remarks` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`dealer_request_id`),
  KEY `agent_id` (`agent_id`),
  KEY `dealer_id` (`dealer_id`),
  KEY `purchase_order_number` (`purchase_order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_dealer_request_OLD
-- ----------------------------

-- ----------------------------
-- Table structure for `is_free_of_charge_detail_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_free_of_charge_detail_OLD`;
CREATE TABLE `is_free_of_charge_detail_OLD` (
  `free_of_charge_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `free_of_charge_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `srp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`free_of_charge_detail_id`),
  KEY `warehouse_request_id` (`free_of_charge_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_free_of_charge_detail_OLD
-- ----------------------------

-- ----------------------------
-- Table structure for `is_free_of_charge_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_free_of_charge_OLD`;
CREATE TABLE `is_free_of_charge_OLD` (
  `free_of_charge_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_series` int(2) NOT NULL DEFAULT '0',
  `request_number` int(11) NOT NULL DEFAULT '0',
  `request_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` int(11) NOT NULL DEFAULT '0',
  `motorcycle_brand_model_id` int(11) NOT NULL DEFAULT '0',
  `engine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chassis` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_approved_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mtr_number` int(11) NOT NULL DEFAULT '0',
  `warehouse_received_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_receive_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`free_of_charge_id`),
  KEY `id_number` (`id_number`),
  KEY `motorcycle_brand_model_id` (`motorcycle_brand_model_id`),
  KEY `engine` (`engine`),
  KEY `chassis` (`chassis`),
  KEY `mtr_number` (`mtr_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_free_of_charge_OLD
-- ----------------------------

-- ----------------------------
-- Table structure for `is_salary_deduction_detail_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_salary_deduction_detail_OLD`;
CREATE TABLE `is_salary_deduction_detail_OLD` (
  `salary_deduction_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `salary_deduction_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `srp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`salary_deduction_detail_id`),
  KEY `salary_deduction_id` (`salary_deduction_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_salary_deduction_detail_OLD
-- ----------------------------
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('1', '1', '125', '2200.00', '10.00', '0.00', '5.00', '0.00', '9900.00', 'COMPLETED', null, '2015-09-17 15:34:44', '2015-09-17 15:26:03');
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('2', '2', '14', '550.00', '0.00', '0.00', '1.00', '0.00', '550.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-05 11:06:16');
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('3', '2', '135', '480.00', '0.00', '0.00', '2.00', '0.00', '960.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-05 11:06:52');
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('4', '3', '117', '420.00', '0.00', '0.00', '2.00', '0.00', '840.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-05 11:27:08');
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('5', '3', '21', '900.00', '0.00', '0.00', '1.00', '0.00', '900.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-05 11:27:24');
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('6', '4', '14', '550.00', '0.00', '0.00', '1.00', '0.00', '550.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-05 15:15:19');
INSERT INTO `is_salary_deduction_detail_OLD` VALUES ('7', '4', '107', '65.00', '0.00', '0.00', '2.00', '0.00', '130.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-05 15:15:35');

-- ----------------------------
-- Table structure for `is_salary_deduction_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_salary_deduction_OLD`;
CREATE TABLE `is_salary_deduction_OLD` (
  `salary_deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_series` int(2) NOT NULL DEFAULT '0',
  `request_number` int(11) NOT NULL DEFAULT '0',
  `request_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` int(11) NOT NULL DEFAULT '0',
  `motorcycle_brand_model_id` int(11) NOT NULL DEFAULT '0',
  `engine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chassis` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_approved_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mtr_number` int(11) NOT NULL DEFAULT '0',
  `warehouse_received_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_receive_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`salary_deduction_id`),
  KEY `id_number` (`id_number`),
  KEY `motorcycle_brand_model_id` (`motorcycle_brand_model_id`),
  KEY `engine` (`engine`),
  KEY `chassis` (`chassis`),
  KEY `mtr_number` (`mtr_number`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_salary_deduction_OLD
-- ----------------------------
INSERT INTO `is_salary_deduction_OLD` VALUES ('1', '15', '1', 'SD15-00001', '603018', '0', '', '', 'COMPLETED', null, '0', '0', '0000-00-00 00:00:00', '224', '2015-09-17 16:33:13', '82456', '0', '0000-00-00 00:00:00', '2015-09-17 15:26:03', '2015-09-17 16:34:06');
INSERT INTO `is_salary_deduction_OLD` VALUES ('2', '15', '2', 'SD15-00002', '1503108', '0', '', '', 'FORWARDED', null, '0', '0', '0000-00-00 00:00:00', '14162', '2015-11-05 11:08:02', '0', '0', '0000-00-00 00:00:00', '2015-11-05 11:06:15', '0000-00-00 00:00:00');
INSERT INTO `is_salary_deduction_OLD` VALUES ('3', '15', '3', 'SD15-00003', '1503081', '0', '', '', 'FORWARDED', null, '0', '0', '0000-00-00 00:00:00', '14162', '2015-11-05 11:28:07', '0', '0', '0000-00-00 00:00:00', '2015-11-05 11:27:08', '0000-00-00 00:00:00');
INSERT INTO `is_salary_deduction_OLD` VALUES ('4', '15', '4', 'SD15-00004', '1503108', '0', '', '', 'PENDING', null, '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '2015-11-05 15:15:18', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for `is_service_unit_detail_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_service_unit_detail_OLD`;
CREATE TABLE `is_service_unit_detail_OLD` (
  `service_unit_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_unit_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `srp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_unit_detail_id`),
  KEY `warehouse_request_id` (`service_unit_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_service_unit_detail_OLD
-- ----------------------------

-- ----------------------------
-- Table structure for `is_service_unit_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_service_unit_OLD`;
CREATE TABLE `is_service_unit_OLD` (
  `service_unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_series` int(2) NOT NULL DEFAULT '0',
  `request_number` int(11) NOT NULL DEFAULT '0',
  `request_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` int(11) NOT NULL DEFAULT '0',
  `motorcycle_brand_model_id` int(11) NOT NULL DEFAULT '0',
  `engine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chassis` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_approved_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mtr_number` int(11) NOT NULL DEFAULT '0',
  `warehouse_received_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_receive_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`service_unit_id`),
  KEY `id_number` (`id_number`),
  KEY `motorcycle_brand_model_id` (`motorcycle_brand_model_id`),
  KEY `engine` (`engine`),
  KEY `chassis` (`chassis`),
  KEY `mtr_number` (`mtr_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_service_unit_OLD
-- ----------------------------

-- ----------------------------
-- Table structure for `is_warehouse_claim_detail_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_warehouse_claim_detail_OLD`;
CREATE TABLE `is_warehouse_claim_detail_OLD` (
  `warehouse_claim_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_claim_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `srp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_claim_detail_id`),
  KEY `warehouse_request_id` (`warehouse_claim_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_warehouse_claim_detail_OLD
-- ----------------------------
INSERT INTO `is_warehouse_claim_detail_OLD` VALUES ('1', '1', '90', '200.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-09-17 15:05:17');
INSERT INTO `is_warehouse_claim_detail_OLD` VALUES ('2', '2', '62', '15.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-09-17 15:40:20');

-- ----------------------------
-- Table structure for `is_warehouse_claim_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_warehouse_claim_OLD`;
CREATE TABLE `is_warehouse_claim_OLD` (
  `warehouse_claim_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_series` int(2) NOT NULL DEFAULT '0',
  `request_number` int(11) NOT NULL DEFAULT '0',
  `request_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` int(11) NOT NULL DEFAULT '0',
  `motorcycle_brand_model_id` int(11) NOT NULL DEFAULT '0',
  `engine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chassis` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_approved_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mtr_number` int(11) NOT NULL DEFAULT '0',
  `warehouse_received_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_receive_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_claim_id`),
  KEY `id_number` (`id_number`),
  KEY `motorcycle_brand_model_id` (`motorcycle_brand_model_id`),
  KEY `engine` (`engine`),
  KEY `chassis` (`chassis`),
  KEY `mtr_number` (`mtr_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_warehouse_claim_OLD
-- ----------------------------
INSERT INTO `is_warehouse_claim_OLD` VALUES ('1', '15', '1', 'WR15-00001', '1503108', '65', '23423', '234234', 'FORWARDED', null, '1', '708075', '0000-00-00 00:00:00', '224', '2015-09-17 16:06:16', '0', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2015-09-17 15:05:17');
INSERT INTO `is_warehouse_claim_OLD` VALUES ('2', '15', '2', 'WR15-00002', '9710001', '173', '', '', 'PENDING', '[{\"datetime\":\"2015-09-17 16:39:13\",\"message\":\"prf#19085\"}]', '2', '1304122', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2015-09-17 15:40:20');

-- ----------------------------
-- Table structure for `is_warehouse_request_detail_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_warehouse_request_detail_OLD`;
CREATE TABLE `is_warehouse_request_detail_OLD` (
  `warehouse_request_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_request_id` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `srp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_request_detail_id`),
  KEY `warehouse_request_id` (`warehouse_request_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_warehouse_request_detail_OLD
-- ----------------------------
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('8', '6', '64', '1090.00', '100.00', '0.00', '100.00', '0.00', '0.00', 'COMPLETED', null, '2015-09-17 15:19:59', '2015-09-17 15:14:47');
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('9', '6', '83', '340.00', '100.00', '0.00', '250.00', '0.00', '0.00', 'COMPLETED', null, '2015-09-17 15:19:59', '2015-09-17 15:15:22');
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('10', '7', '36', '560.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-06 10:18:05');
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('11', '8', '36', '560.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-06 10:18:11');
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('12', '9', '49', '550.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-06 16:43:32');
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('13', '10', '49', '550.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-06 16:43:35');
INSERT INTO `is_warehouse_request_detail_OLD` VALUES ('14', '11', '23', '300.00', '100.00', '0.00', '1.00', '0.00', '0.00', 'PENDING', null, '0000-00-00 00:00:00', '2015-11-06 16:45:51');

-- ----------------------------
-- Table structure for `is_warehouse_request_OLD`
-- ----------------------------
DROP TABLE IF EXISTS `is_warehouse_request_OLD`;
CREATE TABLE `is_warehouse_request_OLD` (
  `warehouse_request_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_series` int(2) NOT NULL DEFAULT '0',
  `request_number` int(11) NOT NULL DEFAULT '0',
  `request_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` int(11) NOT NULL DEFAULT '0',
  `motorcycle_brand_model_id` int(11) NOT NULL DEFAULT '0',
  `engine` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chassis` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text COLLATE utf8_unicode_ci,
  `warehouse_id` int(11) NOT NULL DEFAULT '0',
  `warehouse_approved_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `approved_by` int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mtr_number` int(11) NOT NULL DEFAULT '0',
  `warehouse_received_by` int(11) NOT NULL DEFAULT '0',
  `warehouse_receive_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`warehouse_request_id`),
  KEY `id_number` (`id_number`),
  KEY `motorcycle_brand_model_id` (`motorcycle_brand_model_id`),
  KEY `engine` (`engine`),
  KEY `chassis` (`chassis`),
  KEY `mtr_number` (`mtr_number`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of is_warehouse_request_OLD
-- ----------------------------
INSERT INTO `is_warehouse_request_OLD` VALUES ('6', '15', '1', 'WP15-00001', '1211002', '190', '', '', 'COMPLETED-R', null, '1', '708075', '0000-00-00 00:00:00', '224', '2015-09-17 16:17:40', '82485', '0', '0000-00-00 00:00:00', '2015-09-17 15:14:47', '2015-09-17 16:58:56');
INSERT INTO `is_warehouse_request_OLD` VALUES ('7', '15', '2', 'WP15-00002', '1503108', '0', '', '', 'PENDING', null, '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '2015-11-06 10:18:05', '0000-00-00 00:00:00');
INSERT INTO `is_warehouse_request_OLD` VALUES ('8', '15', '3', 'WP15-00001', '1503108', '0', '', '', 'PENDING', null, '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '2015-11-06 10:18:11', '0000-00-00 00:00:00');
INSERT INTO `is_warehouse_request_OLD` VALUES ('9', '15', '4', 'WP15-00002', '1503108', '0', '', '', 'PENDING', null, '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '2015-11-06 16:43:32', '0000-00-00 00:00:00');
INSERT INTO `is_warehouse_request_OLD` VALUES ('10', '15', '5', 'WP15-00003', '1503108', '0', '', '', 'PENDING', null, '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '2015-11-06 16:43:34', '0000-00-00 00:00:00');
INSERT INTO `is_warehouse_request_OLD` VALUES ('11', '15', '6', 'WP15-00006', '1503108', '0', '', '', 'PENDING', null, '0', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0', '0000-00-00 00:00:00', '2015-11-06 16:45:51', '0000-00-00 00:00:00');
