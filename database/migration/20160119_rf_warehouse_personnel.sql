DROP TABLE IF EXISTS `rf_warehouse_personnel`;
CREATE TABLE `rf_warehouse_personnel` (  
  `warehouse_id` 			INT(11) NOT NULL DEFAULT 0,
  `id_number` 				VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` 				tinyint(2) NOT NULL DEFAULT '0',
  `insert_timestamp` 		timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_id`, `id_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO rf_warehouse_personnel(warehouse_id, id_number, is_active) VALUES (1, '1503108', 1);
