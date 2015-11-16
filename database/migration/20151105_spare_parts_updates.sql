DROP TABLE IF EXISTS `is_request_summary`;
CREATE TABLE `is_request_summary` (
  `request_summary_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `department_module_id` 		int(11) NOT NULL DEFAULT '0',
  `request_series` 				int(2) NOT NULL DEFAULT '0',
  `request_number` 				int(11) NOT NULL DEFAULT '0',
  `request_code` 				varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` 					int(11) NOT NULL DEFAULT '0',
  `motorcycle_brand_model_id` 	int(11) NOT NULL DEFAULT '0',
  `engine` 						varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chassis` 					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` 						varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` 					text COLLATE utf8_unicode_ci,
  `warehouse_id` 				int(11) NOT NULL DEFAULT '0',
  `approved_by` 				int(11) NOT NULL DEFAULT '0',
  `approve_timestamp` 			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cross_reference_type` 		varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cross_reference_number` 		int(11) NOT NULL DEFAULT '0', 
  `branch_id` 					int(11) NOT NULL DEFAULT '0',	
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_timestamp` 			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`request_summary_id`),
  KEY `department_module_id` (`department_module_id`),
  KEY `engine` (`engine`),
  KEY `chassis` (`chassis`),
  KEY `warehouse_id` (`warehouse_id`),
  KEY `branch_id` (`branch_id`),
  KEY `cross_reference` (`cross_reference_type`, `cross_reference_number`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


	DROP TABLE IF EXISTS `is_request_detail`;
CREATE TABLE `is_request_detail` (
  `request_detail_id` 			int(11) NOT NULL AUTO_INCREMENT,  
  `request_summary_id` 			int(11) NOT NULL DEFAULT '0',
  `item_id` 					int(11) NOT NULL DEFAULT '0',
  `srp` 						decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` 					decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` 			decimal(10,2) NOT NULL DEFAULT '0.00',
  `good_quantity` 				decimal(10,2) NOT NULL DEFAULT '0.00',
  `bad_quantity` 				decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` 				decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` 						varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` 					text COLLATE utf8_unicode_ci,
  `update_timestamp` 			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_detail_id`),
  KEY `request_summary_id` (`request_summary_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


------------------------


CREATE TABLE `is_branch_transaction_summary` (
  `branch_transaction_summary_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `htr_year` int(2) NOT NULL,
  `htr_series` int(11) NOT NULL,
  `htr_code` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `cross_reference_number` int(15) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '0',
  `status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PENDING',
  `remarks` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`branch_transaction_summary_id`),
  KEY `transaction_type` (`transaction_type`),
  KEY `transaction_number` (`htr_code`),
  KEY `branch_warehouse_id` (`branch_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `is_deal`
CREATE TABLE `is_dealer_request` (
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

ALTER TABLE `rf_spare_part` ADD COLUMN `motorcycle_brand_id` int(11) NOT NULL DEFAULT 0 AFTER `part_number`;
ALTER TABLE `rf_spare_part` ADD COLUMN `motorcycle_model_id` int(11) NOT NULL DEFAULT 0 AFTER `motorcycle_brand_id`;

UPDATE rf_spare_part a, warehouse.rf_motorcycle_brand b SET a.motorcycle_brand_id = b.motorcycle_brand_id
WHERE a.brand_name = b.brand_name;


