DROP TABLE IF EXISTS `is_request_detail`;
CREATE TABLE `is_request_detail` (
  `request_detail_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `module_id` 					int(11) NOT NULL DEFAULT '0',
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
  KEY `module_id` (`module_id`),
  KEY `request_summary_id` (`request_summary_id`),
  KEY `module_request_summary_id` (`module_id`, `request_summary_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


