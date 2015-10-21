DROP TABLE IF EXISTS `am_employee_login_ad`;
CREATE TABLE `am_employee_login_ad` ( 
	`employee_login_ad_id` 		int(11) UNSIGNED AUTO_INCREMENT NOT NULL,
	`ad_name` 					varchar(100) NULL,
	`description` 				varchar(100) NOT NULL,
	`priority_id` 				int(2) NOT NULL DEFAULT '0',
	`image_filename` 			varchar(100) NULL,
	`is_active` 				tinyint(1) NOT NULL DEFAULT '0',
	`user_id` 					int(4) NOT NULL DEFAULT '0',
	`insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY ( `employee_login_ad_id` ) )
COLLATE = utf8_unicode_ci
ENGINE = InnoDB;

DROP TABLE IF EXISTS `am_alert_message`;
CREATE TABLE `am_alert_message` ( 
	`alert_message_id` 			Int( 11 ) UNSIGNED AUTO_INCREMENT NOT NULL,
	`title` 					VarChar( 32 ) NULL,
	`content` 					Text NULL,
	`is_visible` 				TinyInt( 1 ) UNSIGNED NOT NULL,
	`employee_only` 			TinyInt( 1 ) NOT NULL DEFAULT '1',
	`start_timestamp` 			Timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`end_timestamp` 			Timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp` 			Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY ( `alert_message_id` ) )
COLLATE = utf8_unicode_ci
ENGINE = InnoDB;

DROP TABLE IF EXISTS `am_announcement`;
CREATE TABLE `am_announcement` ( 
	`announcement_id` 			Int( 11 ) AUTO_INCREMENT NOT NULL,
	`title`						VarChar( 1024 ) NULL,
	`body` 						Text NULL,
	`is_published` 				TinyInt( 4 ) NOT NULL DEFAULT '0',
	`user_id` 					Int( 11 ) NOT NULL DEFAULT '0',
	`update_timestamp` 			Timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp` 			Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY ( `announcement_id` ) )
COLLATE = utf8_unicode_ci
ENGINE = InnoDB;

CREATE INDEX `is_published` USING BTREE ON `am_announcement`( `is_published` );
CREATE INDEX `user_id` USING BTREE ON `am_announcement`( `user_id` );

-- add e_login_hash to sa_user
ALTER TABLE sa_user ADD COLUMN `e_login_hash` varchar(255) NOT NULL AFTER `login_hash`;

DROP TABLE IF EXISTS `am_media_upload`;
CREATE TABLE `am_media_upload` ( 
	`media_upload_id` 			Int( 11 ) AUTO_INCREMENT NOT NULL,
	`title` 					VarChar( 1024 ) NOT NULL DEFAULT 'insert title here',
	`description` 				Text NULL,
	`body` 						Text NULL,
	`is_display` 				TinyInt( 4 ) NOT NULL DEFAULT '0',
	`user_id` 					Int( 11 ) NOT NULL DEFAULT '0',
	`update_timestamp` 			Timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp` 			Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY ( `media_upload_id` ) )
COLLATE = utf8_unicode_ci
ENGINE = InnoDB;

CREATE INDEX `is_published` USING BTREE ON `am_media_upload`( `is_display` );
CREATE INDEX `user_id` USING BTREE ON `am_media_upload`( `user_id` );


DROP TABLE IF EXISTS `am_announcement_message`;
CREATE TABLE `am_announcement_message` (
	`announcement_message_id`	int(11) AUTO_INCREMENT NOT NULL,
	`from_id_number`			varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n/a',
	`announcement_id`			int(11) NOT NULL DEFAULT 0,
	`message`					text COLLATE utf8_unicode_ci,	
	`insert_timestamp` 			Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`announcement_message_id`)
)
COLLATE = utf8_unicode_ci
ENGINE = InnoDB;

CREATE INDEX `id_number` USING BTREE ON `am_announcement_message`( `id_number` );
CREATE INDEX `announcement_id` USING BTREE ON `am_announcement_message`( `announcement_id` );

ALTER TABLE `am_announcement_message` ADD COLUMN `is_removed` tinyint(2) NOT NULL DEFAULT 0 AFTER `message`;
ALTER TABLE `am_announcement_message` ADD COLUMN `to_id_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n/a' AFTER `from_id_number`;
