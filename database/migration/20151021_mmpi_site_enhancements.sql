-- add is_email_verified
ALTER TABLE human_relations.pm_employment_information ADD COLUMN `is_personal_email_verified` tinyint(2) NOT NULL DEFAULT 0 AFTER `personal_email_address`;

-- add is_mobile_number_verified
ALTER TABLE human_relations.pm_employment_information ADD COLUMN `is_mobile_number_verified` tinyint(2) NOT NULL DEFAULT 0 AFTER `mobile_number`;
