DROP TABLE IF EXISTS `rf_course_type`;

DROP TABLE IF EXISTS `el_s4s`;
CREATE TABLE `el_s4s` (
	s4s_id					int(11) AUTO_INCREMENT NOT NULL,
	pp_name					varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	pp_description			varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	company_id				int(11) NOT NULL DEFAULT 0,
	branch_id				int(11) NOT NULL DEFAULT 0,
	department_id			int(11) NOT NULL DEFAULT 0,
	position_id				int(11) NOT NULL DEFAULT 0,
	is_active				int(11) NOT NULL DEFAULT 0,
	insert_timestamp		timestamp DEFAULT CURRENT_TIMESTAMP,
	update_timestamp		timestamp DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`s4s_id`),
	KEY `pp_name` (`pp_name`),	
	KEY `company_id` (`company_id`),	
	KEY `branch_id` (`branch_id`),	
	KEY `department_id` (`department_id`),	
	KEY `position_id` (`position_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- table for contents
DROP TABLE IF EXISTS `el_s4s_asset`;
CREATE TABLE `el_s4s_asset` (
	s4s_asset_id			int(11) AUTO_INCREMENT NOT NULL,
	s4s_id					int(11) NOT NULL DEFAULT 0,
	asset_filename			text COLLATE utf8_unicode_ci,
	asset_description		varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
	file_type				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	insert_timestamp		timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`s4s_asset_id`),
	KEY `s4s_id` (`s4s_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



