DROP TABLE IF EXISTS `tr_admin_log`;
CREATE TABLE `tr_admin_log` (
  `log_id` 					int(11) NOT NULL AUTO_INCREMENT,
  `id_number` 				int(11) DEFAULT NULL,
  `origin` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT 'mmpi',
  `module_name` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `table_name` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `details_before` 			text COLLATE utf8_unicode_ci,
  `details_after` 			text COLLATE utf8_unicode_ci,
  `remarks` 				text COLLATE utf8_unicode_ci,
  `insert_timestamp` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `id_number` (`id_number`),
  KEY `module_name` (`module_name`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


