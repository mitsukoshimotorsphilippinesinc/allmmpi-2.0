ALTER TABLE rf_department ADD COLUMN `url` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL AFTER `basic_cola`;
UPDATE rf_department SET url = 'spare_parts' WHERE department_name = 'Spare Parts';
