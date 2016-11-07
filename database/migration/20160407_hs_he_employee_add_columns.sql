ALTER TABLE `hs_hr_employee` ADD COLUMN `created_by` int(10) DEFAULT NULL;
ALTER TABLE `hs_hr_employee` ADD COLUMN `date_entered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;