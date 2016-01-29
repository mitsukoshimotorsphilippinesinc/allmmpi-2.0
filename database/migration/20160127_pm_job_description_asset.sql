DROP TABLE IF EXISTS `pm_job_description_asset`;
CREATE TABLE `pm_job_description_asset` (
  `job_description_asset_id` 	int(11) NOT NULL AUTO_INCREMENT,
  `position_id`					int(11) NOT NULL DEFAULT 0,	
  `asset_filename` 				text COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n/a',
  `is_active` 					tinyint(2) NOT NULL DEFAULT '0',
  `insert_timestamp` 			timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`job_description_asset_id`),
  KEY `position_id` (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

---------------
-- BASH COMMAND
-- SYMLINK webroot_admin to webroot_site
---------------

-- LOCAL
ln -s /var/www/allmmpi/webroot_admin/assets/media/jd /var/www/allmmpi/webroot_site/assets/media/

-- PRODUCTION
ln -s /var/www/html/allmmpi/webroot_admin/assets/media/jd /var/www/html/allmmpi/webroot_site/assets/media/
