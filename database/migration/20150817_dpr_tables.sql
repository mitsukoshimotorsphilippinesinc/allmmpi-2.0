DROP TABLE IF EXISTS `rf_printing_press`;
CREATE TABLE `rf_printing_press` (
  `printing_presS_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `complete_name` 				varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `complete_address` 			varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_number` 				int(2) NOT NULL DEFAULT 0,  
  `is_active`					tinyint(2) NOT NULL DEFAULT 0,  
  `is_deleted`					tinyint(2) NOT NULL DEFAULT 0,  
  `remarks` 					text COLLATE utf8_unicode_ci,
  `update_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (printing_presS_id),
  KEY `complete_name` (`complete_name`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_printing_press`(complete_name, complete_address, contact_number)
VALUES ('PRINTING PRESS ONE', '123 BLUMENTRITT COR. ESPANA MANILA', '(02)123-4567');
INSERT INTO `rf_printing_press`(complete_name, complete_address, contact_number)
VALUES ('PRINTING PRESS TWO', '123 QUEZON AVE QUEZON CITY', '(02)321-1111');

DROP TABLE IF EXISTS `rf_form_type`;
CREATE TABLE `rf_form_type` (
	`form_type_id`				int(11) NOT NULL AUTO_INCREMENT,
	`name`						varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`code`						varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
	`description`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`parent_form_id`			int(11) NOT NULL DEFAULT 0,
	`is_accountable`			tinyint(2) NOT NULL DEFAULT 0, 
	`pieces_per_booklet`		tinyint(2) NOT NULL DEFAULT 0, 
	`rack_location`				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,  
	`is_deleted`				tinyint(2) NOT NULL DEFAULT 0,  
	`remarks` 					text COLLATE utf8_unicode_ci,
  `update_timestamp` 			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (form_type_id),
  KEY `name` (`name`),
  KEY `code` (`code`),
  KEY `is_accountable` (`is_accountable`),
  KEY `pieces_per_booklet` (`pieces_per_booklet`),
  KEY `rack_location` (`rack_location`),
  KEY `parent_form_id` (`parent_form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('OFFICIAL RECEIPT', 'OR', 'OFFICIAL RECIPT', '1', 4);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('COLLECTION RECEIPT', 'CR', 'COLLECTION RECIPT', '1', 4);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('DELIVERY RECEIPT', 'DR', 'DELIVERY RECIPT', '1', 4);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('SALES INVOICE', 'SI', 'SALES INVOICE', '1', 5);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('CASH SALES INVOICE', 'CASH SI', 'CASH SALES INVOICE', '1', 4);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('CHARGE SALES INVOICE', 'CHARGE SI', 'CHARGE SALES INVOICE', '1', 4);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('CASH INVOICE', 'CI', 'CASH INVOICE', '1', 4);
INSERT INTO rf_form_type(name, code, description, is_accountable, pieces_per_booklet)
VALUES ('SISP', 'SISP', 'SISP', '1', 4);

DROP TABLE `rf_department_module`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_department_module`(module_name, department_id, segment_name, is_active)
VALUES('Form Request', '33', 'form_request', '1');
INSERT INTO `rf_department_module`(module_name, department_id, segment_name, is_active)
VALUES('Delivery', '33', 'delivery', '1');
INSERT INTO `rf_department_module`(module_name, department_id, segment_name, is_active)
VALUES('Inventory', '33', 'inventory', '1');
INSERT INTO `rf_department_module`(module_name, department_id, segment_name, is_active)
VALUES('Branch Transactions', '33', 'branch_transactions', '1');
INSERT INTO `rf_department_module`(module_name, department_id, segment_name, is_active)
VALUES('Reports', '33', 'reports', '1');
INSERT INTO `rf_department_module`(module_name, department_id, segment_name, is_active)
VALUES('Maintenance', '33', 'maintenance', '1');

DROP TABLE IF EXISTS `rf_department_module_submodule` (
CREATE TABLE `rf_department_module_submodule` (
  `department_module_submodule_id` 	int(11) NOT NULL AUTO_INCREMENT,
  `department_module_id` 			int(11) NOT NULL DEFAULT '0',
  `submodule_name` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `submodule_url` 					varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priority_order` 					int(2) NOT NULL DEFAULT '0',
  `is_active` 						tinyint(2) NOT NULL DEFAULT '0',
  `insert_timestamp` 				timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`department_module_submodule_id`),
  KEY `department_module_id` (`department_module_id`),
  KEY `submodule_name` (`submodule_name`),
  KEY `submodule_url` (`submodule_url`),
  KEY `priority_order` (`priority_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES ('1', 'Accountable Forms', '/accountables', '1', '1');
INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES ('1', 'Non-Accountable Forms', '/non_accountables', '2', '1');

INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES ('1', 'Accountable Forms', '/accountables', '1', '1');
INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES ('1', 'Non-Accountable Forms', '/non_accountables', '2', '1');

DROP TABLE IF EXISTS `rf_branch_rack_location`;
CREATE TABLE `rf_branch_rack_location` (
	`branch_rack_location_id`	int(11) NOT NULL AUTO_INCREMENT,
	`branch_id`					int(11) NOT NULL DEFAULT 0,
	`rack_location`				varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`is_deleted`				tinyint(2) NOT NULL DEFAULT 0,
	`remarks`					text COLLATE utf8_unicode_ci,
	`update_timetamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`branch_rack_location_id`),
	KEY `branch_id` (`branch_id`),
	KEY `rack_location` (`rack_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `is_booklet`;
CREATE TABLE `is_booklet` (
	`booklet_id`				int(11) NOT NULL AUTO_INCREMENT,
	`request_detail_id`			int(11) NOT NULL DEFAULT 0,	
	`branch_id`					int(11) NOT NULL DEFAULT 0,		
	`booklet_code`				varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
	`booklet_series`			int(11) NOT NULL DEFAULT 0,
	`booklet_number`			varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`series_from`				int(11) NOT NULL DEFAULT 0,
	`series_to`				  	int(11) NOT NULL DEFAULT 0,
	`status`					varchar(30) COLLATE utf8_unicode_ci DEFAULT 'IN',
	`receive_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`receive_remarks`			text COLLATE utf8_unicode_ci,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`booklet_id`),
	KEY `branch_id` (`branch_id`),
	KEY `request_detail_id` (`request_detail_id`),	
	KEY `booklet_number` (`booklet_number`),
	KEY `series_from` (`series_from`),
	KEY `series_to` (`series_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ADD INVENTORY SUBMODULES FOR INVENTORY
INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES('3', 'Main', '/main', '1', '1');
INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES('3', 'Branch', '/branch', '2', '1');

-- MAINTENANCE MAINTENANCE
INSERT INTO `rf_department_module_submodule`(department_module_id, submodule_name, submodule_url, priority_order, is_active)
VALUES('6', 'Branch Rack Location', '/branch_rack_location', '1', '1');


-- VIEWS
DROP VIEW IF EXISTS `rf_branch_rack_location_view`;
CREATE VIEW `rf_branch_rack_location_view` 
	AS 
	SELECT
	a.branch_id,
	a.branch_name,
	a.address_street,
	a.address_city,
	a.address_province,
	a.address_country,
	a.address_zip_code,
	a.contact_number,
	a.tin,
	CASE WHEN a.is_active IS NULL THEN 0 ELSE a.is_active END AS is_active_branch,
	b.branch_rack_location_id,
	b.rack_location,
	CASE WHEN b.is_active IS NULL THEN 0 ELSE b.is_active END AS is_active_rack_location,
	b.is_deleted,
	b.remarks, 
	b.insert_timestamp
	FROM human_relations.rf_branch a
	LEFT JOIN dpr.rf_branch_rack_location b 
	ON a.branch_id = b.branch_id;

-- ACTION LOG / TRAIL
DROP TABLE IF EXISTS `at_action_log`;
CREATE TABLE `at_action_log` (
	`action_log_id` 			int(11) NOT NULL AUTO_INCREMENT,
	`id_number` 				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,  
	`module_name` 				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`submodule_name` 			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`table_name` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`action` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`details_before` 			text COLLATE utf8_unicode_ci,
	`details_after` 			text COLLATE utf8_unicode_ci,
	`remarks` 					text COLLATE utf8_unicode_ci,
	`insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`action_log_id`),
	KEY `id_number` (`id_number`),
	KEY `module_name` (`module_name`),
	KEY `table_name` (`table_name`),
	KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE `tr_request_detail`;
CREATE TABLE `tr_request_detail` (
  `request_detail_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `request_summary_id` 			int(11) DEFAULT NULL,
  `branch_id` 					int(11) DEFAULT NULL,
  `last_serial_number` 			int(11) DEFAULT NULL,
  `form_type_id` 				int(11) DEFAULT NULL,
  `quantity` 					int(11) DEFAULT NULL,
  `send_atp` 					timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `receive_atp` 				timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `faxed_to_printer` 			timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `received_from_printer` 		timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `send_for_stamping` 			timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `received_from_stamping` 		timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `date_delivered` 				timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `printing_press_id` 			int(11) DEFAULT NULL,
  `status` 						varchar(30) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` 					text COLLATE utf8_unicode_ci,
  `insert_timestamp` 			timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE `tr_request_summary`;
CREATE TABLE `tr_request_summary` (
  `request_summary_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `request_year` 				int(11) DEFAULT NULL,
  `request_series` 				int(11) DEFAULT NULL,
  `request_code` 				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` 						varchar(15) COLLATE utf8_unicode_ci DEFAULT 'PENDING',
  `remarks` text				COLLATE utf8_unicode_ci,
  `is_accountable` 				tinyint(1) DEFAULT NULL,
  `update_timestamp` 			timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` 			timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_summary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `rf_job_type`;
CREATE TABLE `rf_job_type` (
  `job_type_id` 				int(11) NOT NULL AUTO_INCREMENT,
  `job_code` 					varchar(16) NOT NULL DEFAULT '',
  `scripts` 					text,
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;

INSERT INTO rf_job_type(job_code, scripts)
VALUES('generate_booklet', '/jobs/dpr/generate_booklet');

DROP TABLE IF EXISTS `et_job`;	
CREATE TABLE `et_job` (
  `job_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `parameters` text,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING',
  `exceptions` text,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;

CREATE VIEW `et_job_view` 
AS select 
`a`.`job_id` AS `job_id`,`a`.`job_type_id` AS `job_type_id`,`b`.`job_code` AS `job_code`,`b`.`scripts` AS `scripts`,`a`.`parameters` AS `parameters`,`a`.`status` AS `status`,`a`.`exceptions` AS `exceptions`,`a`.`insert_timestamp` AS `insert_timestamp` 
from (`et_job` `a` left join `rf_job_type` `b` on((`a`.`job_type_id` = `b`.`job_type_id`)))
