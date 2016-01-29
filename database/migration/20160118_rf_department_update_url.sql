UPDATE human_relations.rf_department SET url = 'accounting' WHERE LOWER(department_name) = 'accounting';
UPDATE human_relations.rf_department SET url = 'corporate_services' WHERE LOWER(department_name) = 'corporate services';
UPDATE human_relations.rf_department SET url = 'treasury' WHERE LOWER(department_name) = 'treasury';

-- ADD reference_number to el_s4s
ALTER TABLE human_relations.`el_s4s` ADD reference_number VARCHAR(20) COLLATE utf8_unicode_ci DEFAULT NULL AFTER pp_description;

-- UPDATE el_s4s.reference_number
UPDATE el_s4s a SET a.reference_number = (SELECT b.asset_description FROM el_s4s_asset b WHERE a.s4s_id = b.s4s_id);


-- REBUILD add reference_number
DROP VIEW IF EXISTS `el_s4s_position_view`;
CREATE VIEW `el_s4s_position_view` AS 
(select `a`.
`s4s_id` AS `s4s_id`,
`a`.`pp_name` AS `pp_name`,
`a`.`pp_description` AS `pp_description`,
`a`.`reference_number` AS `reference_number`,
`a`.`department_id` AS `department_id`,
`a`.`is_active` AS `is_active_s4s`,
`b`.`position_id` AS `position_id`,
`b`.`parent_position_id` AS `parent_position_id`,
`b`.`position_name` AS `position_name`,
`b`.`is_active` AS `is_active_position`,
`c`.`priority_order` AS `priority_order`,
`c`.`is_active` AS `is_active_s4s_position`,
`c`.`insert_timestamp` AS `insert_timestamp` 
from ((`el_s4s_position` `c` left join `el_s4s` `a` on((`a`.`s4s_id` = `c`.`s4s_id`))) 
left join `rf_position` `b` on((`b`.`position_id` = `c`.`position_id`))));

-- MODIFY approved_by 
ALTER TABLE information_technology.rs_repair_remark MODIFY COLUMN approved_by INT(11) NOT NULL DEFAULT 0;

-- ADD date_approved
ALTER TABLE information_technology.rs_repair_remark ADD COLUMN date_approved datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER approved_by;

-- ADD department_id
ALTER TABLE information_technology.rs_repair_summary ADD COLUMN department_id int(11) NOT NULL DEFAULT '0' AFTER `id_number`;
