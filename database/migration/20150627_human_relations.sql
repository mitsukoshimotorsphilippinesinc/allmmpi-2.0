DROP TABLE IF EXISTS `rf_company`;
CREATE TABLE `rf_company` (
	`company_id`				int(11) NOT NULL AUTO_INCREMENT,	
	`company_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`contact_person`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`philhealth`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`sss`						varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`tin`						varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`is_branch`					tinyint(2) NOT NULL DEFAULT 0,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`company_id`),
	KEY `company_name` (`company_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_company` (company_name, address_street, address_city, contact_number, contact_person, tin, sss, philhealth, is_active, is_branch)
(SELECT 
Name, 
CASE WHEN AddStreet = '---' THEN NULL ELSE AddStreet END, 
CASE WHEN AddCity = '---' THEN NULL ELSE AddCity END, 
CASE WHEN ContactNumber = '---' THEN NULL ELSE ContactNumber END, 
CASE WHEN ContactPerson = '---' THEN NULL ELSE ContactPerson END, 
CASE WHEN TinNo = '---' THEN NULL ELSE TinNo END, 
CASE WHEN SSSNo = '---' THEN NULL ELSE SSSNo END, 
CASE WHEN PhilhealthNo = '---' THEN NULL ELSE PhilhealthNo END, 
 CASE WHEN VoidCompany = 'NO' THEN 1 ELSE 0 END, 
CASE WHEN Location = 'Branch' THEN 1 ELSE 0 END 
FROM HRDataBase.Company);

DROP TABLE IF EXISTS `rf_agency`;
CREATE TABLE `rf_agency` (
	`agency_id`					int(11) NOT NULL AUTO_INCREMENT,	
	`agency_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,		
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`contact_person`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`remarks`					text COLLATE utf8_unicode_ci,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`agency_id`),
	KEY `agency_name` (`agency_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_agency`(
`agency_name`,
`address_street`,
`address_city`,
`contact_number`,
`contact_person`,
`remarks`,
`is_active`,
`update_timestamp`,
`insert_timestamp`
) (SELECT 
`Name`, 
CASE WHEN `AddStreet` = '---' THEN NULL ELSE `AddStreet` END, 
CASE WHEN `AddCity` = '---' THEN NULL ELSE `AddCity` END, 
CASE WHEN `ContactPerson` = '---' THEN NULL ELSE `ContactPerson` END, 
CASE WHEN `ContactNumber` = '---' THEN NULL ELSE `ContactNumber` END, 
CASE WHEN `Remarks` = '---' THEN NULL ELSE `Remarks` END, 
`Active`, 
now(), 
`DateEnter` 
FROM 
HRDataBase.Agency);

DROP TABLE IF EXISTS `rf_department`;
CREATE TABLE `rf_department` (
	`department_id`				int(11) NOT NULL AUTO_INCREMENT,	
	`department_name`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`manager_id_number`			varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`basic_pay`					decimal(10,2) NOT NULL DEFAULT 0.00,
	`basic_cola`				decimal(10,2) NOT NULL DEFAULT 0.00,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`department_id`),
	KEY `department_name` (`department_name`),
	KEY `manager_id_number` (`manager_id_number`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- NOTE: Change COLLATION OF HRDataBase.Department
INSERT INTO `rf_department`(
`department_name`,
`contact_number`,
`manager_id_number`,
`basic_pay`,
`basic_cola`,
`is_active`,
`update_timestamp`,
`insert_timestamp`
) 
(SELECT 
`Name`, 
CASE WHEN `ContactNumber` = '---' THEN NULL ELSE `ContactNumber` END, 
CASE WHEN Head = '---' THEN NULL ELSE (SELECT IDNo FROM HRDataBase.Personal_Information WHERE CompleteName LIKE CONCAT('%', a.Head, '%') ORDER BY DateBeg LIMIT 1) END,
`MinBasic`,
`MinCola`, 
CASE WHEN `VoidDepartment` = 'NO' THEN 1 ELSE 0 END, 
now(), 
`DateEnter` 
FROM 
HRDataBase.Department a ORDER BY DepartmentCode);

DROP TABLE IF EXISTS `rf_branch`;
CREATE TABLE `rf_branch` (
	`branch_id`					int(11) NOT NULL AUTO_INCREMENT,	
	`company_id`				int(11) NOT NULL DEFAULT 0,	
	`branch_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`area_manager_id_number`	varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`area_manager_name`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`branch_manager_id_number`	varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`branch_manager_name`		varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`cashier_id_number`			varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`cashier_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`atm`						varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`etps`						varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
	`basic_pay`					decimal(10,2) NOT NULL DEFAULT 0.00,
	`basic_cola`				decimal(10,2) NOT NULL DEFAULT 0.00,
	`payroll_region`			varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`monthly_ceiling`			decimal(10,2) NOT NULL DEFAULT 0.00,
	`daily_ceiling`				decimal(10,2) NOT NULL DEFAULT 0.00,
	`opening_time`				timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`closing_time`				timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`branch_id`),
	KEY `company_id` (`company_id`),
	KEY `branch_name` (`branch_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO rf_branch(company_id, branch_name, address_street, address_city, contact_number, atm, etps, basic_pay, basic_cola, payroll_region, monthly_ceiling, daily_ceiling, opening_time, closing_time, is_active, area_manager_name, branch_manager_name, cashier_name)
SELECT
	CompanyCode,
	Name,
	CASE WHEN AddStreet = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN AddCity = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN ContactNumber = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN ATM = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN ETPS = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN MinBasic = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN MinCola = '---' THEN NULL ELSE AddStreet END,
	CASE WHEN PayRollRegion = '---' THEN NULL ELSE AddStreet END,
	MonthlyCeiling,
	DailyCeiling,
	STR_TO_DATE(concat('0000-00-00 ', substring(timeOpen, 11)), '%Y-%m-%d %h:%i%s'),
	STR_TO_DATE(concat('0000-00-00 ', substring(timeClose, 11)) , '%Y-%m-%d %h:%i%s'),
	CASE WHEN VoidBranch = 'NO' THEN 1 ELSE 0 END,
	CASE WHEN AreaManager = '---' THEN NULL ELSE AreaManager END,
	CASE WHEN BranchManager = '---' THEN NULL ELSE BranchManager END,
	CASE WHEN Cashier = '---' THEN NULL ELSE Cashier END
FROM
	HRDataBase.Branch ORDER BY BranchCode;
	
DROP TABLE IF EXISTS `rf_employment_requirement`;
CREATE TABLE `rf_employment_requirement` (
	`employment_requirement_id`	int(11) NOT NULL AUTO_INCREMENT,	
	`requirement_name`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`description`				text COLLATE utf8_unicode_ci,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,	
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`employment_requirement_id`),
	KEY `requirement_name` (`requirement_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('2x2 ID Picture/s', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('1x1 ID Picture/s', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('NBI Clearance', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Transcript of Records', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('College Diploma', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('SSS', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('TIN', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('BIR 2x', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Clearance', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Birth Certificate', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Dependent''s Birth Certificate', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Marriage Contract', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Drivers License', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('X-ray', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('CBC', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Urinalysis', 1);
INSERT INTO `rf_employment_requirement`(`requirement_name`,`is_active`) VALUES ('Fecalysis', 1);


DROP TABLE IF EXISTS `pm_employee_requirement`;
CREATE TABLE `pm_employment_requirement` (
	`employee_requirement_id`	int(11) NOT NULL AUTO_INCREMENT,
	`personal_information_id`	int(11) NOT NULL DEFAULT 0,
	`id_number`					varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`requirement_id`			int(11) NOT NULL DEFAULT 0,
	`status`					varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`update_timestamp`			timestamp DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`employee_requirement_id`),
	KEY `personal_information_id` (`personal_information_id`),
	KEY `id_number` (`id_number`),
	KEY `requirement_id` (`requirement_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `rf_employment_status`;
CREATE TABLE `rf_employment_status` (
	`employment_status_id`		int(11) NOT NULL AUTO_INCREMENT,	
	`status_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,	
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`employment_status_id`),
	KEY `status_name` (`status_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Contractual 1', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Contractual 2', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Contractual 3', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Contractual 4', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Contractual 5', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Contractual 6', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Probationary 3', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Probationary 5', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Probationary 6', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Regular', '1');
INSERT INTO `rf_employment_status`(`status_name`, `is_active`) VALUES ('Trainee', '1');

DROP TABLE IF EXISTS `rf_job_grade_level`;
CREATE TABLE `rf_job_grade_level` (
	`job_grade_level_id`		int(11) NOT NULL AUTO_INCREMENT,	
	`grade_level_name`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`job_grade_level_id`),
	KEY `grade_level_name` (`grade_level_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO rf_job_grade_level(`grade_level_name`, `is_active`) VALUES ('Full Pledge', '1');
INSERT INTO rf_job_grade_level(`grade_level_name`, `is_active`) VALUES ('Trainee', '1');

DROP TABLE IF EXISTS `rf_memo_subject`;
CREATE TABLE `rf_memo_subject` (
	`memo_subject_id`			int(11) NOT NULL AUTO_INCREMENT,	
	`subject_name`				varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
	`description`				text COLLATE utf8_unicode_ci,	
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`memo_subject_id`),
	KEY `subject_name` (`subject_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tmp_distinct_memo_subject`;
CREATE TABLE tmp_distinct_memo_subject AS
SELECT REPLACE(subject, '"', '') AS memo_sbject FROM HRDataBase.Employee_Memo GROUP BY 1 ORDER BY 1;

INSERT INTO rf_memo_subject(subject_name, is_active)
(SELECT memo_sbject, '1' FROM tmp_distinct_memo_subject);

DROP TABLE IF EXISTS `rf_position`;
CREATE TABLE `rf_position` (
	`position_id`				int(11) NOT NULL AUTO_INCREMENT,	
	`position_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`parent_position_id`		int(11) NOT NULL DEFAULT 0,
	`is_active`					tinyint(2) NOT NULL DEFAULT 0,	
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`position_id`),
	KEY `parent_position_id` (`parent_position_id`),
	KEY `position_name` (`position_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `rf_position` VALUES ('1', 'AC', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('2', 'AC / MA', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('3', 'Account Counselor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('4', 'Account Counselor / Driver', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('5', 'Account Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('6', 'Accountant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('7', 'Accounting Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('8', 'Accounting Team Leader', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('9', 'Accunt Counselor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('10', 'Acting Caretaker', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('11', 'Acting Leadman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('12', 'Acting Leadman//Maintenance', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('13', 'Acting Officer in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('14', 'Acting OIC', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('15', 'Acting Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('16', 'Acting VP for HR and Administration', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('17', 'Administrative Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('18', 'Adminstrative Officer', '17', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('19', 'Area Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('20', 'Area Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('21', 'Area Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('22', 'Area Manager 1', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('23', 'Area Manager 2', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('24', 'AS 400 Administrator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('25', 'Assember', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('26', 'Assembler', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('27', 'Assembler - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('28', 'Assembler / Engine', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('29', 'Assembler / Main Line', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('30', 'Assembler / Quality Control', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('31', 'Assembler / Sub - Assy', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('32', 'Assistant Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('33', 'Assistant Liaison', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('34', 'Assistant Liaison Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('35', 'Assistant Liaison Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('36', 'Assistant Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('37', 'Assistant Production Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('38', 'Assistant Repo in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('39', 'Associate', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('40', 'Asst. Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('41', 'Audit Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('42', 'Audit Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('43', 'Auditor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('44', 'Bookkeeper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('45', 'Branch Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('46', 'Branch Manager   1', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('47', 'Branch Manager   1 (Trainee)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('48', 'Branch Manager (Trainee)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('49', 'Branch Manager 1', '47', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('50', 'Branch Manager 1  (Trainee)', '47', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('51', 'Branch Manager 1 - Trainee', '47', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('52', 'Branch Manager 1 (Trainee)', '47', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('53', 'Branch Manager 2', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('54', 'Caretaker', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('55', 'Caretaker - Trainee', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('56', 'Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('57', 'Cashier  (Trainee)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('58', 'Cashier (Trainee)', '57', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('59', 'Cashier / MA', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('60', 'Chief Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('61', 'CI / Collector', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('62', 'CIC Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('63', 'CKD', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('64', 'CKD - Forklift Operator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('65', 'CKD - Quality Control Leadman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('66', 'CKD - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('67', 'CKD Leadman/MSD', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('68', 'CKD Production Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('69', 'CKD Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('70', 'Claim Requestor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('71', 'Collection Area Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('72', 'Collection Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('73', 'Company Consultant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('74', 'Company Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('75', 'Consultant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('76', 'Corporate and Internet Sales Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('77', 'Corporate Secretary', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('78', 'Corporate Treasurer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('79', 'Counter Salesman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('80', 'Credit & Collection Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('81', 'Credit & Collection Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('82', 'Credit and Collection Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('83', 'Credit and Collection Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('84', 'Credit and Collection Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('85', 'Credit Investigator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('86', 'Credit Superivsor (Trainee)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('87', 'Credit Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('88', 'Credit Supervisor - Trainee', '86', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('89', 'Credit Supervisor (Trainee)', '86', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('90', 'Delivery Driver and Warehousing', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('91', 'Delivery Helper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('92', 'Documentation Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('93', 'Driver', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('94', 'Driver / Helper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('95', 'Driver / Liaison Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('96', 'Driver / Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('97', 'Driver/Helper', '94', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('98', 'Electrician', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('99', 'Encoder', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('100', 'Endurance Testing - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('101', 'Engine 1', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('102', 'Executive Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('103', 'Field Associate', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('104', 'Field Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('105', 'Forklift Driver', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('106', 'Forklift Operator', '105', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('107', 'Forklipt Operator', '105', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('108', 'Forllipt Operator', '105', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('109', 'General Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('110', 'Graphic Artist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('111', 'Graphic Designer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('112', 'Head Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('113', 'Head Marketing', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('114', 'Head Task Force', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('115', 'Helper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('116', 'Helper Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('117', 'HR Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('118', 'HR Associate', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('119', 'HR Business Partner', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('120', 'HR Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('121', 'HR Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('122', 'HR Specialist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('123', 'HR Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('124', 'HRD Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('125', 'HRD Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('126', 'Human Resource Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('127', 'Industrial Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('128', 'Industrial Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('129', 'Internal Auditor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('130', 'Inventory Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('131', 'Inventory in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('132', 'Inventory Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('133', 'IT Expert Analyst', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('134', 'IT Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('135', 'IT Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('136', 'IT Technician', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('137', 'Janitor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('138', 'Janitress', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('139', 'Jr. Programmer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('140', 'Junior Area Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('141', 'Junior LTO - Supervisor (Documentation)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('142', 'Leadman - Stencil Section', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('143', 'Legal Affairs Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('144', 'Legal Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('145', 'Legal Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('146', 'Liaison Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('147', 'Liaison Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('148', 'Liaison Officer / MA', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('149', 'Liaison Officer II', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('150', 'Liaison Officer Region VI', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('151', 'Liaison Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('152', 'Liaison Staff / AC', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('153', 'Liaison Staff / Marketing Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('154', 'Liaison Staff/ Messenger', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('155', 'Liason Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('156', 'Logistics Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('157', 'LTO Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('158', 'LTO Supervisor (Budget)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('159', 'LTO Supervisor (ORCR)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('160', 'MA', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('161', 'MA / Liaison Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('162', 'MA / Partsman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('163', 'MA/Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('164', 'MA/Partsman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('165', 'Machinist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('166', 'Main Line', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('167', 'Main Line - Leadman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('168', 'Main Line - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('169', 'Main Line 1 - Assembler', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('170', 'Main Line 2 - Assembler', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('171', 'Main Line 2 - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('172', 'Main Line Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('173', 'Maintenance', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('174', 'Maintenance - Leadman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('175', 'Maintenance Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('176', 'Maintenance Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('177', 'Management Team', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('178', 'Management Team Representative', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('179', 'Management Trainee', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('180', 'Management Trainee - Account Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('181', 'Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('182', 'Managing Director', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('183', 'Marekting Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('184', 'Marketing and Store Development Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('185', 'Marketing Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('186', 'Marketing Assistant / Liaison', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('187', 'Marketing Assistant / Liaison Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('188', 'Marketing Assistant/ Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('189', 'Marketing Assistant/Liaison', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('190', 'Marketing Assitant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('191', 'Marketing Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('192', 'Marketing Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('193', 'Marketing Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('194', 'Marketing Services Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('195', 'Marketing Services Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('196', 'Marketing Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('197', 'Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('198', 'Mechanic / Watchman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('199', 'Messenger', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('200', 'Mindanao Office Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('201', 'MSD Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('202', 'MSD Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('203', 'MSD UNLOADER', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('204', 'MSD/CKD - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('205', 'Office Assistant', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('206', 'Officer in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('207', 'Officer in Charge I', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('208', 'Officer in Charge II', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('209', 'OJT', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('210', 'OJT - Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('211', 'Operation Field Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('212', 'Operation Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('213', 'Operations Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('214', 'Operations Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('215', 'Orchard Realty Property Administrator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('216', 'Owner', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('217', 'Painter', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('218', 'Parts Controller', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('219', 'Partsman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('220', 'Partswoman / Liaison', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('221', 'Plant / Production Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('222', 'Plant Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('223', 'President', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('224', 'Product Developement Specialist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('225', 'Production  Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('226', 'Production  Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('227', 'Production Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('228', 'Production Reworks Leadman - Trainee', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('229', 'Production Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('230', 'Production Staff / Sub engine', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('231', 'Production Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('232', 'Programmer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('233', 'Project Encoder', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('234', 'Project Engineer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('235', 'QC Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('236', 'Quality Check', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('237', 'Quality Control', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('238', 'Quality Control - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('239', 'Quality Control Custodian', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('240', 'Quality Control Service', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('241', 'Recon Leadman Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('242', 'Recon Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('243', 'Recon Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('244', 'Recruitment Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('245', 'Regional Liaison Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('246', 'Regional Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('247', 'Regional Manager - Trainee', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('248', 'Regional Manager (Trainee)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('249', 'Regional Manager Trainee', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('250', 'Regional Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('251', 'Remedial Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('252', 'Repo in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('253', 'Rework Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('254', 'Reworks', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('255', 'Reworks - Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('256', 'Reworks - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('257', 'Sales & Marketing  Services Specialist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('258', 'Sales and Collection Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('259', 'Sales and Marketing Director', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('260', 'Sales and POA Director', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('261', 'Sales Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('262', 'Sales Executive', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('263', 'Sales Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('264', 'Sales Staff (North Area)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('265', 'Sales Staff (Noth Area)', '264', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('266', 'Sales Staff (Visayas Area)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('267', 'Secretary', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('268', 'Secretary (Mindanao Area)', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('269', 'Secretary / MA', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('270', 'Security Guard', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('271', 'Senior Account Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('272', 'Senior Account Manager / OIC', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('273', 'Senior Accounting Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('274', 'Senior Branch Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('275', 'Senior Branch Manager - Trainee', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('276', 'Service Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('277', 'Service Specialist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('278', 'Shop Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('279', 'Showroom Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('280', 'Spare Parts  Delivery', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('281', 'Spare Parts Cashier', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('282', 'Spare Parts Sales and Collections Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('283', 'Spareparts Auditor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('284', 'Spareparts Credit and Collection Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('285', 'Spareparts Express Delivery', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('286', 'Spareparts in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('287', 'Spareparts Staff / Counter Salesman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('288', 'Special Project Manager', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('289', 'Sr. Programmer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('290', 'Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('291', 'Stencil', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('292', 'Stencil and Encoding', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('293', 'Stockman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('294', 'Sub Assy Frame - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('295', 'Sub Assy Line - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('296', 'Sub Assy Line 2', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('297', 'Sub Assy Line Staff', '295', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('298', 'Sub Engine', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('299', 'Sub Engine 2', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('300', 'Sub Main Leadman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('301', 'Sub Mainline', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('302', 'Sub-Assy', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('303', 'Sub-Assy Frame', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('304', 'Sub-Assy Frame - Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('305', 'Sub-Assy Frame 1', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('306', 'Sub-Assy Leadman/Support Group', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('307', 'Sub-Assy Line', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('308', 'Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('309', 'Task Force', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('310', 'Task Force Mindanao', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('311', 'Team Leader', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('312', 'Technician', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('313', 'Temporary Mechanic', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('314', 'Tool Keeper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('315', 'Training Coordinator', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('316', 'Training Head', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('317', 'Training Officer', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('318', 'Training Specialist', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('319', 'Truck Helper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('320', 'Warehouse - Driver', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('321', 'Warehouse Assistant/Delivery Helper', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('322', 'Warehouse Encoder', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('323', 'Warehouse Staff', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('324', 'Warehouse Supervisor', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('325', 'Warehouseman', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('326', 'Warranty in Charge', '0', '1', '2015-06-19 13:24:22');
INSERT INTO `rf_position` VALUES ('327', 'Welder', '0', '1', '2015-06-19 13:24:22');

DROP TABLE IF EXISTS `pm_character_reference`;
CREATE TABLE `pm_character_reference` (
	`character_reference_id`	int(11) NOT NULL AUTO_INCREMENT,
	`personal_information_id`	int(11) NOT NULL DEFAULT 0,	
	`complete_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`company_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`position`					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`relationship`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`character_reference_id`),
	KEY `personal_information_id` (`personal_information_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--DROP TABLE IF EXISTS `pm_personal_information`;
--CREATE TABLE `pm_personal_information` (
--	`personal_information_id`	int(11) NOT NULL AUTO_INCREMENT,	
--	`complete_name`				varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`last_name`					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`suffix_name`				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`first_name`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`middle_name`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`personal_email_address`	varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`phone_number`				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,	
--	`mobile_number`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,	
--	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`is_rent`					tinyint(2) NOT NULL DEFAULT 0,	
--	`months_of_stay`			int(11) NOT NULL DEFAULT 0,	
--	`monthly_rental`			decimal(10,2) NOT NULL DEFAULT 0.00,	
--	`birthdate` 				date DEFAULT NULL,
--	`birthplace` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`gender` 					varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`nationality` 				varchar(50) COLLATE utf8_unicode_ci DEFAULT 'FILIPINO',
--	`religion` 					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`marital_status` 			varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,	
--	`pagibig` 					varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,	
--	`philhealth` 				varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,	
--	`sss` 						varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--	`tin` 						varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,		
--	`image` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,		
--	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
--	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
--	PRIMARY KEY (`personal_information_id`),
--	KEY `complete_name` (`complete_name`),
--	KEY `middle_name` (`middle_name`),
--	KEY `last_name` (`last_name`),
--	KEY `first_name` (`first_name`)
--)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
---- GET last Name, First Name, Middle Name, Contact Number
--INSERT INTO pm_personal_information(
--`complete_name`,
--`last_name`,
--`first_name`,
--`middle_name`,
--`mobile_number`,
--`image`
--)
--(SELECT 
--`CompleteName`, 
--`SurName`, 
--`FirstName`, 
--`MiddleName`, 
--`CellNo`,
--`Picture` 
--FROM
--HRDataBase.Personal_Information 
--GROUP BY CompleteName ORDER BY IDNO * 1);

-- get latest set of character reference per employee
CREATE TABLE `tmp_distinct_character_reference`
AS 
SELECT a.*
FROM HRDataBase.`Character_Reference` a 
JOIN
     (SELECT employee, name as pangalan, MAX(idno) AS maxdt
		FROM HRDataBase.`Character_Reference`
		GROUP BY employee, name
     ) asum
     ON 
	 asum.employee = a.employee 
	 AND 
		a.idno = asum.maxdt
	AND 
		a.Name = pangalan
ORDER BY employee, idno DESC;

-- NOTE: Change COLLATION of tmp table

INSERT pm_character_reference(personal_information_id, complete_name, company_name, position, relationship, contact_number)
(SELECT
(SELECT personal_information_id FROM pm_personal_information WHERE complete_name = a.employee),
Name, 
CASE WHEN Company IN ('','-','--', '---') THEN NULL ELSE Company END, 
CASE WHEN EmpPosition IN ('','-','--','---') THEN NULL ELSE EmpPosition END, 
CASE WHEN Relationship IN ('','-','--','---') THEN NULL ELSE Relationship END, 
CASE WHEN ContactNos IN ('','-','--','---') THEN NULL ELSE ContactNos END
FROM tmp_distinct_character_reference a);

DROP TABLE IF EXISTS `pm_family_member`;
CREATE TABLE `pm_family_member` (
	`family_member_id`			int(11) NOT NULL AUTO_INCREMENT,	
	`personal_information_id`	int(11) NOT NULL DEFAULT 0,
	`complete_name`				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`relation`					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`birthdate`					date NOT NULL,
	`age`						int(3) NOT NULL DEFAULT 0,
	`status`					varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL, 
	`occupation`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL, 
	`company_address`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`family_member_id`),
	KEY `personal_information_id` (`personal_information_id`),
	KEY `complete_name` (`complete_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- get latest set of family members per employee
CREATE TABLE `tmp_distinct_family_member`
AS 
SELECT a.*
FROM HRDataBase.`Family_Members` a 
JOIN
     (SELECT employee, name as pangalan, MAX(idno) AS maxdt
		FROM HRDataBase.`Family_Members`
		GROUP BY employee, name
     ) asum
     ON 
	 asum.employee = a.employee 
	 AND 
		a.idno = asum.maxdt
	AND 
		a.Name = pangalan
ORDER BY employee, idno DESC;

-- NOTE: Change COLLATION of tmp table

INSERT pm_family_member(personal_information_id, complete_name, relation, address_street, address_city, contact_number, birthdate, age, status, occupation, company_address)
(SELECT
(SELECT personal_information_id FROM pm_personal_information WHERE complete_name = a.employee),
Name, 
CASE WHEN Relation IN ('','-','--', '---') THEN NULL ELSE Relation END, 
CASE WHEN AddStreet IN ('','-','--','---') THEN NULL ELSE AddStreet END, 
CASE WHEN AddCity IN ('','-','--','---') THEN NULL ELSE AddCity END, 
CASE WHEN PhoneNo IN ('','-','--','---') THEN NULL ELSE PhoneNo END,
CASE WHEN BirthDate IN ('','-','--','---') THEN NULL ELSE BirthDate END,
CASE WHEN Age IN ('','-','--','---') THEN NULL ELSE Age END,
CASE WHEN Status IN ('','-','--','---') THEN NULL ELSE Status END,
CASE WHEN OccSchool IN ('','-','--','---') THEN NULL ELSE OccSchool END,
CASE WHEN Address IN ('','-','--','---') THEN NULL ELSE Address END
FROM tmp_distinct_family_member a);

DROP TABLE IF EXISTS `pm_personal_information`;
CREATE TABLE `pm_personal_information` (
	`personal_information_id`	int(11) NOT NULL AUTO_INCREMENT,	
	`complete_name`				varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
	`last_name`					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`suffix_name`				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`first_name`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`middle_name`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`personal_email_address`	varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`phone_number`				varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`mobile_number`				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`is_rent`					tinyint(2) NOT NULL DEFAULT 0,	
	`months_of_stay`			int(11) NOT NULL DEFAULT 0,	
	`monthly_rental`			decimal(10,2) NOT NULL DEFAULT 0.00,	
	`birthdate` 				date DEFAULT NULL,
	`birthplace` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`gender` 					varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
	`nationality` 				varchar(50) COLLATE utf8_unicode_ci DEFAULT 'FILIPINO',
	`religion` 					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`marital_status` 			varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`pagibig` 					varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`philhealth` 				varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,	
	`sss` 						varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
	`tin` 						varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,		
	`image_filename` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,		
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`personal_information_id`),
	KEY `complete_name` (`complete_name`),
	KEY `middle_name` (`middle_name`),
	KEY `last_name` (`last_name`),
	KEY `first_name` (`first_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- GET last Name, First Name, Middle Name, Contact Number
INSERT INTO pm_personal_information(
`complete_name`,
`last_name`,
`first_name`,
`middle_name`,
`mobile_number`,
`image_filename`
)
(SELECT 
`CompleteName`, 
`SurName`, 
`FirstName`, 
`MiddleName`, 
`CellNo`,
`Picture` 
FROM
HRDataBase.Personal_Information 
GROUP BY CompleteName ORDER BY IDNO * 1);

--CREATE TEMP TABLE IN HRDataBase
CREATE TABLE `tmp_distinct_latest_other_information`
AS SELECT a.*
FROM `Other_Information` a 
JOIN
     (SELECT employee, MAX(idno) AS maxdt
		FROM Other_Information
		GROUP BY employee
     ) asum
     ON 
	 asum.employee = a.employee 
	 AND 
	 a.idno = asum.maxdt
ORDER BY employee, idno DESC;
-- NOTE: CHANGE COLLATION BEFORE UPDATING pm_personal_information


-- UPDATE other details
UPDATE  pm_personal_information a
        INNER JOIN
        (
            SELECT  
				`Employee`,
				`BirthDate`, 
				`BirthPlace`, 
				`Gender`, 
				CASE WHEN `CivilStatus` LIKE 'ME%' THEN 'Married' ELSE 'Single' END as CivilStatus, 
				`Religion`,
				`PagIbig`, 
				`PhilHealth`,
				`SSS`,
				`TIN` 
            FROM    HRDataBase.tmp_distinct_latest_other_information
            GROUP   BY employee
        ) b ON  b.employee = a.complete_name
SET     a.birthdate = b.BirthDate,
        a.birthplace = b.BirthPlace,
				a.`gender` = b.Gender,
				a.`religion` = b.Religion,
				a.`marital_status` = b.CivilStatus,
				a.`pagibig` =  b.PagIbig,
				a.`philhealth` = b.PhilHealth,
				a.`sss` = b.SSS,
				a.`tin` = b.TIN;

--CREATE TEMP TABLE IN HRDataBase
CREATE TABLE `tmp_distinct_latest_address`
AS SELECT a.*
FROM `Personal_Address` a 
JOIN
     (SELECT employee, MAX(idno) AS maxdt
		FROM `Personal_Address`
		GROUP BY employee
     ) asum
     ON 
	 asum.employee = a.employee 
	 AND 
	 a.idno = asum.maxdt
ORDER BY employee, idno DESC;

-- NOTE: CHANGE COLLATION BEFORE UPDATING pm_personal_information

-- UPDATE other details
UPDATE  pm_personal_information a
        INNER JOIN
        (
            SELECT  
				`Employee`,
				`AddStreet`,
				`AddCity`, 
				`AddProv`,  
				CASE WHEN `RentOwn` LIKE '%Rent%' THEN 1 ELSE 0 END as rent_own, 
				`TelNo`,
				(replace(LengthOfStay,' Months', '') * 1) AS los, 
				`MonthlyRental`
            FROM   HRDataBase.tmp_distinct_latest_address
            GROUP BY employee
        ) b ON  b.employee = a.complete_name
		SET 	a.`address_street` = b.AddStreet,
				a.`address_city` = b.AddCity,
				a.`address_province` = b.AddProv,
				a.`is_rent` =  b.rent_own,
				a.`phone_number` = b.TelNo,
				a.`months_of_stay` = b.los,
				a.`monthly_rental` = b.MonthlyRental;
				
DROP TABLE IF EXISTS `pm_work_experience`;
CREATE TABLE `pm_work_experience` (
	`work_experience_id`		int(11) NOT NULL AUTO_INCREMENT,	
	`personal_information_id`	int(11) NOT NULL DEFAULT 0,
	`employer`					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`contact_number`			varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`nature_of_business`		varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`position`					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`start_date` 				date DEFAULT NULL,
	`end_date` 					date DEFAULT NULL,	
	`covered_period`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`immediate_head`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`reason_for_leaving`		text COLLATE utf8_unicode_ci,
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`work_experience_id`),
	KEY `personal_information_id` (`personal_information_id`),
	KEY `employer` (`employer`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
	
CREATE TABLE `tmp_distinct_work_experience`
AS 
SELECT a.*
FROM HRDataBase.`Work_Experience` a 
JOIN
     (SELECT employee, employer as pangalan, MAX(idno) AS maxdt
		FROM HRDataBase.`Work_Experience`
		GROUP BY employee, employer
     ) asum
     ON 
	 asum.employee = a.employee 
	 AND 
		a.idno = asum.maxdt
	AND 
		a.Employer = pangalan
ORDER BY employee, idno DESC;

-- NOTE: CHANGE COLLATION tmp_distinct_work_experience

INSERT pm_work_experience(personal_information_id, employer, address_street, address_city, contact_number, nature_of_business, position, covered_period, immediate_head, reason_for_leaving)
(SELECT
(SELECT personal_information_id FROM pm_personal_information WHERE complete_name = a.employee),
Employer, 
CASE WHEN AddStreet IN ('','-','--', '---') THEN NULL ELSE AddStreet END, 
CASE WHEN AddCity IN ('','-','--', '---') THEN NULL ELSE AddCity END, 
CASE WHEN TelNo IN ('','-','--','---') THEN NULL ELSE TelNo END, 
CASE WHEN NatureOFBusiness IN ('','-','--','---') THEN NULL ELSE NatureOFBusiness END, 
CASE WHEN EmpPosition IN ('','-','--','---') THEN NULL ELSE EmpPosition END,
CASE WHEN PeriodCovered IN ('','-','--','---') THEN NULL ELSE PeriodCovered END,
CASE WHEN Supervisor IN ('','-','--','---') THEN NULL ELSE Supervisor END,
CASE WHEN ReasonForLeaving IN ('','-','--','---') THEN NULL ELSE ReasonForLeaving END
FROM tmp_distinct_work_experience a);	
	
DROP TABLE IF EXISTS `pm_educational_background`;
CREATE TABLE `pm_educational_background` (
	`educational_background_id` int(11) NOT NULL AUTO_INCREMENT,
	`personal_information_id` 	int(11) NOT NULL DEFAULT 0,	
	`school_name` 				varchar(200) DEFAULT NULL,
	`address_street` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_city` 				varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_province` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_country` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`address_zip_code` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`course` 					varchar(50) COLLATE utf8_unicode_ci DEFAULT 'N/A',
	`start_year` 				varchar(50) COLLATE utf8_unicode_ci DEFAULT 'N/A',
	`end_year` 					varchar(50) COLLATE utf8_unicode_ci DEFAULT 'N/A',
	`degree_acquired` 			varchar(100) COLLATE utf8_unicode_ci DEFAULT 'N/A',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`educational_background_id`),
	KEY `personal_information_id` (`personal_information_id`),
	KEY `course` (`course`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- get distinct employee's educational bg
CREATE TABLE `tmp_distinct_educational_background`
AS SELECT a.*
FROM HRDataBase.Educational_Background a 
JOIN
     (SELECT employee, MAX(idno) AS maxdt
		FROM HRDataBase.Educational_Background
		GROUP BY employee
     ) asum
     ON 
	 asum.employee = a.employee 
	 AND 
	 a.idno = asum.maxdt
ORDER BY employee, idno DESC;

-- NOTE: Change COLLATION OF FIELDS for tmp_distinct_educational_background
INSERT INTO `pm_educational_background`(personal_information_id, school_name, address_street, address_city, course, start_year, end_year, degree_acquired)
(
SELECT 
(SELECT personal_information_id FROM pm_personal_information WHERE complete_name = a.employee),
Institution, 
CASE WHEN AddStreet IN ('','-','--', '---') THEN NULL ELSE AddStreet END, 
AddCity, 
CASE WHEN Course IN ('','-','--','---') THEN 'N/A' ELSE Course END, 
substring(PeriodCovered,1, 4), 
substring(PeriodCovered,6,4), 
CASE WHEN DegreeAcquired IN ('', '-', '---') THEN 'N/A' ELSE DegreeAcquired END
FROM tmp_distinct_educational_background a);


DROP TABLE IF EXISTS `pm_employment_information`;
CREATE TABLE `pm_employment_information` (
	`employment_information_id`	int(11) NOT NULL AUTO_INCREMENT,
	`personal_information_id`	int(11) NOT NULL DEFAULT 0,
	`complete_name`				varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
	`id_number`					varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`username`					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`password`					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`original_password`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`company_email_address`		varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`company_id`				int(11) NOT NULL DEFAULT 0,
	`department_id`				int(11) NOT NULL DEFAULT 0,
	`branch_id`					int(11) NOT NULL DEFAULT 0,
	`job_grade_level_id`		int(11) NOT NULL DEFAULT 0,
	`position_id`				int(11) NOT NULL DEFAULT 0,
	`employment_status_id`		int(11) NOT NULL DEFAULT 0,	
	`is_employed`				tinyint(2) NOT NULL DEFAULT 0,
	`paycode` 					varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`atm` 						varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`is_create_atm` 			tinyint(2) NOT NULL DEFAULT 0,
	`salary_basis` 				varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`salary_rate` 				decimal(10, 2) DEFAULT 0.00,
	`allowance_rate` 			decimal(10, 2) DEFAULT 0.00,
	`agency_id`					int(11) NOT NULL DEFAULT 0,
	`update_timestamp`			timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`employment_information_id`),
	KEY `personal_information_id` (`personal_information_id`),
	KEY `id_number` (`id_number`),
	KEY `username` (`username`),
	KEY `password` (`password`),
	KEY `company_email_address` (`company_email_address`),
	KEY `company_id` (`company_id`),
	KEY `department_id` (`department_id`),
	KEY `branch_id` (`branch_id`),
	KEY `paycode` (`paycode`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO pm_employment_information (`id_number`, `complete_name`, `username`, `password`, `original_password`,`paycode`, `atm`, `is_create_atm`)
SELECT
a.IDNo, 
a.Employee,
CONCAT(lower(trim(replace(b.first_name, ' ', ''))), lower(trim(replace(b.last_name, ' ', '')))) as username,
MD5('mitsukoshimotors') as `password`,
'mitsukoshimotors' as `orig_password`,
#lower(trim(replace(b.first_name, ' ', ''))),
a.PayCode,
a.ATM,
CASE WHEN a.CreateATM = 'YES' THEN 1 ELSE 0 END AS CreateATM
FROM
HRDataBase.Other_Information a
LEFT JOIN pm_personal_information b ON b.complete_name = a.Employee;

-- personal_information_id
UPDATE  pm_employment_information a
INNER JOIN
(
	SELECT  
		complete_name, `personal_information_id`
	FROM   pm_personal_information
	GROUP BY personal_information_id
) b ON  b.complete_name = a.complete_name
SET a.`personal_information_id` = b.personal_information_id;

-- company_id
UPDATE  pm_employment_information a
INNER JOIN
(
	SELECT  
		x.IDNo, y.company_id
	FROM   HRDataBase.Personal_Information x
	LEFT JOIN rf_company y on x.Company = y.company_name
	GROUP BY x.IDNo
) b ON  b.IDno = a.id_number
SET 	a.`company_id` = b.company_id;

-- position_id
UPDATE  pm_employment_information a
INNER JOIN
(
	SELECT  
		x.IDNo, CASE WHEN y.parent_position_id = 0 THEN y.position_id ELSE y.parent_position_id END AS position_id
	FROM   HRDataBase.Personal_Information x
	LEFT JOIN rf_position y on x.EmpPosition = y.position_name
	GROUP BY x.IDNo
) b ON  b.IDno = a.id_number
SET 	a.`position_id` = b.position_id;

-- department_id
UPDATE  pm_employment_information a
INNER JOIN
(
	SELECT  
		x.IDNo, y.department_id
	FROM   HRDataBase.Personal_Information x
	LEFT JOIN rf_department y on x.DeptBranch = y.department_name
	GROUP BY x.IDNo
) b ON  b.IDno = a.id_number
SET 	a.`department_id` = b.department_id;

-- job_grade_level_id
UPDATE  pm_employment_information a
INNER JOIN
(
	SELECT  
		x.IDNo, y.job_grade_level_id
	FROM   HRDataBase.Personal_Information x
	LEFT JOIN rf_job_grade_level y on x.JobGradeLevel = y.grade_level_name
	GROUP BY x.IDNo
) b ON  b.IDno = a.id_number
SET 	a.`job_grade_level_id` = b.job_grade_level_id;


-- is_employed
-- branch_id
-- salary_rate
-- allowance_rate`




DROP TABLE IF EXISTS `pm_employee_memo`;
CREATE TABLE `pm_employee_memo` (
	`employee_memo_id`			int(11) NOT NULL AUTO_INCREMENT,	
	`personal_information_id`	int(11) NOT NULL DEFAULT 0,
	`id_number`					int(11) NOT NULL DEFAULT 0,
	`reference_number`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`memo_subject_id`			varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`remarks` 					varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`action_taken` 				text COLLATE utf8_unicode_ci,
	`filed_by` 					int(11) NOT NULL DEFAULT 0,
	`insert_timestamp`			timestamp DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`employee_memo_id`),
	KEY `personal_information_id` (`personal_information_id`),
	KEY `id_number` (`id_number`),
	KEY `reference_number` (`reference_number`),
	KEY `memo_subject_id` (`memo_subject_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- NOTE: Change COLLATION of HRDataBase.Employee_Memo

INSERT INTO pm_employee_memo (personal_information_id, id_number, reference_number, memo_subject_id, action_taken, remarks, insert_timestamp)
(
SELECT
(SELECT personal_information_id FROM pm_personal_information WHERE complete_name = a.employee),
idno,
refno,
(SELECT memo_subject_id FROM rf_memo_subject WHERE subject_name = trim(a.subject)),
actiontaken,
particulars,
eventdate
FROM 
HRDataBase.Employee_Memo a
);

DROP VIEW IF EXISTS `pm_employment_information_view`;
CREATE VIEW `pm_employment_information_view` 
AS 
SELECT 
`a`.`employment_information_id` AS `employment_information_id`,
`b`.`personal_information_id` AS `personal_information_id`,
`a`.`id_number` AS `id_number`,
`b`.`complete_name` AS `complete_name`,
`b`.`last_name` AS `last_name`,
`b`.`first_name` AS `first_name`,
`b`.`middle_name` AS `middle_name`,
`b`.`personal_email_address` AS `personal_email_address`,
`a`.`company_email_address` AS `company_email_address`,
`b`.`phone_number` AS `phone_number`,
`b`.`mobile_number` AS `mobile_number`,
`a`.`company_id` AS `company_id`,
`a`.`department_id` AS `department_id`,
`a`.`branch_id` AS `branch_id`,
`a`.`job_grade_level_id` AS `job_grade_level_id`,
`a`.`position_id` AS `position_id`,
`a`.`employment_status_id` AS `employment_status_id`,
`a`.`is_employed` AS `is_employed`,
`a`.`paycode` AS `paycode`,
`a`.`atm` AS `atm`,
`b`.`birthdate` AS `birthdate`,
`b`.`birthplace` AS `birthplace`,
`b`.`gender` AS `gender`,
`b`.`nationality` AS `nationality`,
`b`.`religion` AS `religion`,
`b`.`marital_status` AS `marital_status`,
`b`.`pagibig` AS `pagibig`,
`b`.`philhealth` AS `philhealth`,
`b`.`sss` AS `sss`,
`b`.`tin` AS `tin`,
`b`.`image_filename` AS `image_filename`
FROM (`pm_employment_information` `a` LEFT JOIN `pm_personal_information` `b` ON((`a`.`personal_information_id` = `b`.`personal_information_id`)));

