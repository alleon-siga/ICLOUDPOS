ALTER TABLE `venta_shadow` DROP FOREIGN KEY `venta_shadow_ibfk_3`;

ALTER TABLE `venta_shadow`
MODIFY COLUMN `id_documento`  char(2) NOT NULL AFTER `local_id`;
