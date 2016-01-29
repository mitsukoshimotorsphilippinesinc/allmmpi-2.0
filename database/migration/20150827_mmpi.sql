DROP TABLE IF EXISTS `rf_setting`;
CREATE TABLE `rf_setting` (  
  `slug` 						varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` 						text COLLATE utf8_unicode_ci NOT NULL,
  `default` 					text COLLATE utf8_unicode_ci NOT NULL,  
  `department_id` 				int(11) NOT NULL DEFAULT 0,  
  `is_locked` 					tinyint(4) NOT NULL DEFAULT '0',
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`slug`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_setting`(`slug`, `value`, `default`, `department_id`, `is_locked`)
VALUES ('series_per_booklet', '50', '50', '33', '1');


DROP TABLE IF EXISTS `rf_job_type`;
CREATE TABLE `rf_job_type` (
  `job_type_id` 				int(11) NOT NULL AUTO_INCREMENT,
  `job_code` 					varchar(16) NOT NULL DEFAULT '',
  `department_id` 				int(11) NOT NULL DEFAULT 0,
  `module_id`					int(11) NOT NULL DEFAULT 0,
  `submodule_id`				int(11) NOT NULL DEFAULT 0,
  `scripts` 					text,
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;

INSERT INTO rf_job_type(job_code, scripts, department_id, module_id, submodule_id)
VALUES('generate_booklet', '/jobs/dpr/generate_booklet', 33, 2, 0);

DROP TABLE IF EXISTS `et_job`;	
CREATE TABLE `et_job` (
  `job_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  `parameters` text,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING',
  `exceptions` text,
  `processing_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completed_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;

DROP VIEW IF EXISTS `et_job_view`;
CREATE VIEW `et_job_view` 
AS select 
`a`.`job_id` AS `job_id`,
`a`.`job_type_id` AS `job_type_id`,
`b`.`job_code` AS `job_code`,
`b`.`department_id` AS `department_id`,
`b`.`module_id` AS `module_id`,
`b`.`submodule_id` AS `submodule_id`,
`b`.`scripts` AS `scripts`,
`a`.`parameters` AS `parameters`,
`a`.`status` AS `status`,
`a`.`exceptions` AS `exceptions`,
`a`.`processing_timestamp` AS `processing_timestamp`,
`a`.`completed_timestamp` AS `completed_timestamp`,
`a`.`insert_timestamp` AS `insert_timestamp` 
FROM (`et_job` `a` 
LEFT JOIN `rf_job_type` `b` ON((`a`.`job_type_id` = `b`.`job_type_id`)));

--------
-- USERS
--------
DROP TABLE IF EXISTS `sa_user`;
CREATE TABLE `sa_user` (
  `user_id` 						int(11) unsigned NOT NULL AUTO_INCREMENT,
  `employment_information_id` 		int(11) NOT NULL DEFAULT 0,
  `id_number` 						varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` 						varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` 						varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `original_password` 				varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `designation` 					varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'User',
  `role_id` 						int(11) NOT NULL,
  `default_page` 					varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '/admin',
  `last_login` 						int(11) unsigned NOT NULL DEFAULT '0',
  `login_hash` 						varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `insert_timestamp` 				timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `employment_information_id` (`employment_information_id`),
  KEY `id_number` (`id_number`),
  KEY `IS_USERS_IS_USER_ROLES_FK` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- POPULATE TABLE BASED ON human_relations.pm_employment_information
INSERT INTO `sa_user`(`employment_information_id`, `id_number`, `username`, `password`, `original_password`)
(SELECT `employment_information_id`, `id_number`, `username`, `password`, `original_password` FROM human_relations.pm_employment_information);

DROP TABLE IF EXISTS `sa_user_privilege`;
CREATE TABLE `sa_user_privilege` (
  `user_id` 			int(11) unsigned NOT NULL,
  `privilege_id` 		int(11) unsigned NOT NULL,
  `insert_timestamp` 	timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`privilege_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sa_user_privilege`(`user_id`, `privilege_id`) 
VALUES('1', '1');


DROP TABLE IF EXISTS `sa_privilege`;
CREATE TABLE `sa_privilege` (
  `privilege_id` 			int(11) unsigned NOT NULL AUTO_INCREMENT,
  `privilege_code` 			varchar(65) COLLATE utf8_unicode_ci NOT NULL,
  `privilege_description` 	text COLLATE utf8_unicode_ci NOT NULL,
  `privilege_uri` 			text COLLATE utf8_unicode_ci NOT NULL,
  `system_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menu_code` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`privilege_id`),
  KEY `privilege_code` (`privilege_code`),
  KEY `system_code` (`system_code`),
  KEY `menu_code` (`menu_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sa_privilege`(`privilege_code`, `privilege_description`, `privilege_uri`, `system_code`)
VALUES('DPR', 'DPR Booklet Monitoring', '["dpr"]', 'dpr]');



DROP TABLE IF EXISTS `sa_navigation`;
CREATE TABLE `sa_navigation` (
  `navigation_id` 				int(11) unsigned NOT NULL AUTO_INCREMENT,
  `department_id`				int(11) NOT NULL DEFAULT 0,
  `name` 						varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` 						varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` 						varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `priority_order` 				tinyint(4) NOT NULL DEFAULT '0',
  `parent_id` 					int(11) unsigned NOT NULL,
  `is_active` 					tinyint(4) NOT NULL DEFAULT '0',
  `type` 						enum('MODULE','SUBMODULE') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SUBMODULE',
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`navigation_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 1
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'main_dashboard', 'Main Dashboard', '/dpr', '0', '0', '1', 'MODULE');

-- 2
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'form_request', 'Form Request', '/dpr/form_request', '0', '0', '1', 'MODULE');

-- 3 and 4
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'accountable_forms', 'Accountable Forms', '/dpr/form_request/accountables', '1', '2', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'non_accountable_forms', 'Non-Accountable Forms', '/dpr/form_request/non_accountables', '2', '2', '1', 'SUBMODULE');

-- 5
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'delivery', 'Delivery', '/dpr/delivery', '0', '0', '1', 'MODULE');

-- 6
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'inventory', 'Inventory', '/dpr/inventory', '0', '0', '1', 'MODULE');

-- 7 and 8
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'main', 'Main', '/dpr/inventory/main', '1', '6', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'branch', 'Branch', '/dpr/inventory/branch', '2', '6', '1', 'SUBMODULE');

-- 9
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'maintenance', 'Maintenance', '/dpr/maintenance', '0', '0', '1', 'MODULE');

-- 10 and 11
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'branch_rack_location', 'Branch Rack Location', '/dpr/maintenance/branch_rack_location', '1', '9', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(33, 'form_types', 'Form Types', '/dpr/maintenance/form_types', '2', '9', '1', 'SUBMODULE');


-- SPARE PARTS
-- 12
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'main_dashboard', 'Main Dashboard', '/spare_parts', '0', '0', '1', 'MODULE');

-- 13
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'warehouse_request', 'Warehouse Request', '/spare_parts/warehouse_request', '0', '0', '1', 'MODULE');

-- 14, 15, 16, 17
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'dashboard', 'Dashboard', '/spare_parts/warehouse_request', '1', '13', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'listing', 'Request List', '/spare_parts/warehouse_request/listing', '2', '13', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'approval', 'Approval', '/spare_parts/warehouse_request/approval', '3', '13', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'reports', 'Reports', '/spare_parts/warehouse_request/reports', '4', '13', '1', 'SUBMODULE');

-- 18
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'warehouse_claim', 'Warehouse Claim Parts', '/spare_parts/warehouse_claim', '0', '0', '1', 'MODULE');

-- 19, 20, 21, 22
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'dashboard', 'Dashboard', '/spare_parts/warehouse_claim', '1', '18', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'listing', 'Request List', '/spare_parts/warehouse_claim/listing', '2', '18', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'approval', 'Approval', '/spare_parts/warehouse_claim/approval', '3', '18', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'reports', 'Reports', '/spare_parts/warehouse_claim/reports', '4', '18', '1', 'SUBMODULE');

-- 23
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'salary_deduction', 'Salary Deduction', '/spare_parts/salary_deduction', '0', '0', '1', 'MODULE');

-- 24, 25, 26, 27
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'dashboard', 'Dashboard', '/spare_parts/salary_deduction', '1', '23', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'listing', 'Request List', '/spare_parts/salary_deduction/listing', '2', '23', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'approval', 'Approval', '/spare_parts/salary_deduction/approval', '3', '23', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'reports', 'Reports', '/spare_parts/salary_deduction/reports', '4', '23', '1', 'SUBMODULE');

-- 28
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'service_unit', 'Service Unit', '/spare_parts/service_unit', '0', '0', '1', 'MODULE');

-- 29, 30, 31, 32
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'dashboard', 'Dashboard', '/spare_parts/service_unit', '1', '28', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'listing', 'Request List', '/spare_parts/service_unit/listing', '2', '28', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'approval', 'Approval', '/spare_parts/service_unit/approval', '3', '28', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'reports', 'Reports', '/spare_parts/service_unit/reports', '4', '28', '1', 'SUBMODULE');

-- 33
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'dealer request', 'Dealer Request', '/spare_parts/dealer_request', '0', '0', '1', 'MODULE');

-- 34, 35, 36, 37
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'dashboard', 'Dashboard', '/spare_parts/dealer_request', '1', '33', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'listing', 'Request List', '/spare_parts/dealer_request/listing', '2', '33', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'approval', 'Approval', '/spare_parts/dealer_request/approval', '3', '33', '1', 'SUBMODULE');
INSERT INTO `sa_navigation` (`department_id`, `name`, `title`, `url`, `priority_order`, `parent_id`, `is_active`, `type`)
VALUES(1, 'reports', 'Reports', '/spare_parts/dealer_request/reports', '4', '33', '1', 'SUBMODULE');


DROP VIEW IF EXISTS `sa_user_privilege_view`;
CREATE VIEW `sa_user_privilege_view` AS 
SELECT 
`a`.`user_id` AS `user_id`,
`a`.`privilege_id` AS `privilege_id`,
`b`.`privilege_code` AS `privilege_code`,
`b`.`privilege_description` AS `privilege_description`,
`b`.`privilege_uri` AS `privilege_uri`,
`b`.`system_code` AS `system_code`,
`a`.`insert_timestamp` AS `insert_timestamp` 
FROM 
(`sa_user_privilege` `a` LEFT JOIN `sa_privilege` `b`  ON ((`a`.`privilege_id` = `b`.`privilege_id`)));


ALTER TABLE `sa_user` ADD COLUMN `is_active` tinyint(2) NOT NULL DEFAULT 0 AFTER `default_page`;
UPDATE `sa_user` SET `is_active` = 1;
