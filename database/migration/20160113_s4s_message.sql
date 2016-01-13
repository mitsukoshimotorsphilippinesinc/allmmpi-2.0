DROP TABLE IF EXISTS `el_s4s_message`;
CREATE TABLE `el_s4s_message` (
  `s4s_message_id` 			int(11) NOT NULL AUTO_INCREMENT,
  `from_id_number` 			varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n/a',
  `to_id_number` 			varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n/a',
  `s4s_id` 					int(11) NOT NULL DEFAULT '0',
  `message` 				text COLLATE utf8_unicode_ci,
  `is_removed` 				tinyint(2) NOT NULL DEFAULT '0',
  `insert_timestamp` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`s4s_message_id`),
  KEY `id_number` (`from_id_number`),
  KEY `s4s_id` (`s4s_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ADD id_number COLUMN TO am_announcement TABLE
ALTER TABLE mmpi.am_announcement ADD COLUMN id_number varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL AFTER user_id;


-- INSERT TO rf_setting
INSERT INTO mmpi.rf_setting(`slug`, `value`, `default`, `department_id`, `is_locked`)
VALUES ('s4s_comment_message_removal', 'This message has been removed by Admin.', 'This message has been removed by Admin.', 32, 1);
