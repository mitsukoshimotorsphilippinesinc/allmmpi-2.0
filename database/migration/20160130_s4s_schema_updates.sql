ALTER TABLE human_relations.el_s4s ADD COLUMN `document_sequence` INT(11) NOT NULL DEFAULT 0 AFTER pp_description;

--------------
-- SYMLINK S4S
--------------
-- LOCAL
ln -s /var/www/allmmpi/webroot_admin/assets/media/s4s /var/www/allmmpi/webroot_site/assets/media/