DROP TABLE IF EXISTS `rf_department_module_report`;
CREATE TABLE `rf_department_module_report` (
	`module_report_id`				int(11) NOT NULL AUTO_INCREMENT,
	`department_module_id`			int(11) NOT NULL DEFAULT 0,
	`report_name`					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`report_query`					text COLLATE utf8_unicode_ci,
	`report_parameters`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`is_active`						tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`				timestamp DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`				timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`module_report_id`),
	KEY `department_module_id` (`department_module_id`),
	KEY `report_name` (`report_name`),
	KEY `report_query` (`report_query`),
	KEY `report_parameters` (`report_parameters`)	
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--INSERT INTO `rf_department_module_report`(`department_module_id`, `report_name`, `report_query`, `report_parameters`, `is_active`)
--VALUES ('5', 'Pending Requests', 'SELECT ');


DROP TABLE IF EXISTS `rf_department_module_submodule`;
CREATE TABLE `rf_department_module_submodule` (
	`department_module_submodule_id`	int(11) NOT NULL AUTO_INCREMENT,
	`department_module_id`				int(11) NOT NULL DEFAULT 0,
	`submodule_name`					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`submodule_url`						varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
	`priority_order`					int(2) NOT NULL DEFAULT 0,
	`is_active`							tinyint(2) NOT NULL DEFAULT 0,
	`insert_timestamp`					timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`department_module_submodule_id`),
	KEY `department_module_id` (`department_module_id`),
	KEY `submodule_name` (`submodule_name`),
	KEY `submodule_url` (`submodule_url`),
	KEY `priority_order` (`priority_order`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (3, 'Dashboard', '/', 1, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (3, 'Request List', '/listing', 2, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (3, 'For Approval', '/approval', 3, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (3, 'Reports', '/reports', 4, 1);

INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (4, 'Dashboard', '/', 1, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (4, 'Request List', '/listing', 2, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (4, 'For Approval', '/approval', 3, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (4, 'Reports', '/reports', 4, 1);

INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (5, 'Dashboard', '/', 1, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (5, 'Request List', '/listing', 2, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (5, 'For Approval', '/approval', 3, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (5, 'Reports', '/reports', 4, 1);

INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (6, 'Dashboard', '/', 1, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (6, 'Request List', '/listing', 2, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (6, 'For Approval', '/approval', 3, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (6, 'Reports', '/reports', 4, 1);

INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (7, 'Dashboard', '/', 1, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (7, 'Request List', '/listing', 2, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (7, 'For Approval', '/approval', 3, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (7, 'Reports', '/reports', 4, 1);

INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (8, 'Dashboard', '/', 1, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (8, 'Request List', '/listing', 2, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (8, 'For Approval', '/approval', 3, 1);
INSERT INTO `rf_department_module_submodule`(`department_module_id`, `submodule_name`, `submodule_url`, `priority_order`, `is_active`)
VALUES (8, 'Reports', '/reports', 4, 1);