-- OLD
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `is_item_view` AS select `a`.`item_id` AS `item_id`,`a`.`sku` AS `sku`,`b`.`model_name` AS `model_name`,`b`.`brand_name` AS `brand_name`,`b`.`description` AS `description`,`b`.`part_number` AS `part_number`,`b`.`unit` AS `unit`,`b`.`stock_limit` AS `stock_limit`,`b`.`srp` AS `srp`,`b`.`is_active` AS `is_active`,`a`.`good_quantity` AS `good_quantity`,`a`.`bad_quantity` AS `bad_quantity`,`c`.`warehouse_name` AS `warehouse_name`,`a`.`rack_location` AS `rack_location`,`b`.`image_filename` AS `image_filename` from ((`is_item` `a` left join `rf_spare_part` `b` on((`a`.`sku` = `b`.`sku`))) left join `rf_warehouse` `c` on((`a`.`warehouse_id` = `c`.`warehouse_id`)))



-- NEW
DROP VIEW IF EXISTS `is_item_view`;
CREATE  VIEW `is_item_view` AS 
select `a`.`item_id` AS `item_id`,
`a`.`spare_part_id` AS `spare_part_id`,
`a`.`sku` AS `sku`,
`b`.`model_name` AS `model_name`,
`b`.`brand_name` AS `brand_name`,`b`.`description` AS `description`,`b`.`part_number` AS `part_number`,`b`.`unit` AS `unit`,
`b`.`stock_limit` AS `stock_limit`,`b`.`srp` AS `srp`,`b`.`is_active` AS `is_active`,`a`.`good_quantity` AS `good_quantity`,`a`.`bad_quantity` AS `bad_quantity`,`c`.`warehouse_name` AS `warehouse_name`,
`a`.`rack_location` AS `rack_location`,`b`.`image_filename` AS `image_filename` 
from ((`is_item` `a` left join `rf_spare_part` `b` on((`a`.`spare_part_id` = `b`.`spare_part_id`))) left join `rf_warehouse` `c` on((`a`.`warehouse_id` = `c`.`warehouse_id`)))

-- UPDATE spare_part_id FIELD
update is_item a set a.spare_part_id = (select b.spare_part_id from rf_spare_part b where a.sku = b.sku)
