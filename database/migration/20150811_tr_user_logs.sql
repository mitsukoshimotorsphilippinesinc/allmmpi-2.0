DROP TABLE IF EXISTS `tr_user_logs`;
CREATE TABLE `tr_user_logs` (
  `user_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `origin` varchar(10) COLLATE utf8_unicode_ci DEFAULT '',
  `module_id` int(2) NOT NULL DEFAULT 0,
  `table_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `details_before` text COLLATE utf8_unicode_ci,
  `details_after` text COLLATE utf8_unicode_ci,
  `remarks` text COLLATE utf8_unicode_ci,
  `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_log_id`),
  KEY `user_id` (`user_id`),
  KEY `module_name` (`module_name`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

