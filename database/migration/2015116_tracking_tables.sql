-- SPARE_PARTS
DROP TABLE IF EXISTS `tr_admin_log`;
CREATE TABLE `tr_admin_log` (
  `admin_log_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `id_number` 				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `origin` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT 'mmpi',
  `module_name` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `table_name` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `details_before` 			text COLLATE utf8_unicode_ci,
  `details_after` 			text COLLATE utf8_unicode_ci,
  `remarks` 				text COLLATE utf8_unicode_ci,
  `insert_timestamp` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_log_id`),
  KEY `id_number` (`id_number`),
  KEY `module_name` (`module_name`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- MMPI
DROP TABLE IF EXISTS `tr_user_log`;
CREATE TABLE `tr_user_log` (
  `user_log_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `id_number` 				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_name` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `table_name` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `details_before` 			text COLLATE utf8_unicode_ci,
  `details_after` 			text COLLATE utf8_unicode_ci,
  `remarks` 				text COLLATE utf8_unicode_ci,
  `insert_timestamp` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_log_id`),
  KEY `id_number` (`id_number`),
  KEY `module_name` (`module_name`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tr_s4s_acceptance`;
CREATE TABLE `tr_s4s_acceptance` (
  `s4s_acceptance_id` 		int(11) NOT NULL AUTO_INCREMENT,
  `id_number` 				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `s4s_id` 					int(11) DEFAULT NULL,
  `is_accepted`				tinyint(2) NOT NULL DEFAULT 0,    
  `insert_timestamp` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`s4s_acceptance_id`),
  KEY `id_number` (`id_number`),
  KEY `s4s_id` (`s4s_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


