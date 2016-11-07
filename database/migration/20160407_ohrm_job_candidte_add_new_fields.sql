ALTER TABLE `ohrm_job_candidate` ADD COLUMN `date_of_availability` date DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `expected_salary` decimal(10, 2) DEFAULT 0.00;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `nick_name` varchar(100) DEFAULT '';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `street` varchar(100) DEFAULT '';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `city_code` varchar(100) DEFAULT '';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `coun_code` varchar(100) DEFAULT '';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `provin_code` varchar(100) DEFAULT '';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `zipcode` varchar(20) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `hm_telephone` varchar(50) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `nation_code` int(4) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `gender` smallint(6) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `height` decimal(4,2) DEFAULT '0.00';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `weight` decimal(4,2) DEFAULT '0.00';
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `marital_status` varchar(20) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `birthday` date DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `religion` varchar(50) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `pagibig_number` varchar(50) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `philhealth_number` varchar(50) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `sss_number` varchar(50) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `tin` varchar(50) DEFAULT NULL;
ALTER TABLE `ohrm_job_candidate` ADD COLUMN `insert_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;


-- add data group
INSERT INTO `ohrm_data_group` (`name`, `description`, `can_read`, `can_create`, `can_update`, `can_delete`)
VALUES ('photograph - recruitment', 'Recruitment - Candidate Photograph', 1, 1, 1, 1);




