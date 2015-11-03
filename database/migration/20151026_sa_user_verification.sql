DROP TABLE IF EXISTS `sa_user_verification`;
CREATE TABLE `sa_user_verification` (
  `id_number` 						varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `email_code` 						varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile_code` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `change_password_code` 			varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `change_password_original` 		varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `email_verification_timestamp` 	timestamp NULL DEFAULT NULL,
  `mobile_verification_timestamp` 	timestamp NULL DEFAULT NULL,
  `insert_timestamp` 				timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_number`),
  KEY `user_id` (`id_number`,`email_code`),
  KEY `user_id_2` (`id_number`,`mobile_code`),
  KEY `user_id_3` (`id_number`,`change_password_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `rf_notification_content`;
CREATE TABLE `rf_notification_content` (
  `notification_content_id` 		int(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_number`	 					int(20) NOT NULL DEFAULT '0',
  `title` 							varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `slug` 							varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `body` 							text COLLATE utf8_unicode_ci NOT NULL,
  `is_active` 						tinyint(2) NOT NULL DEFAULT '0',
  `content_type` 					varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `updated_timestamp` 				datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `insert_timestamp` 				timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_content_id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


