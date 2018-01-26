ALTER TABLE `producto_series`
ADD COLUMN `estado` VARCHAR(2) NULL DEFAULT '1' AFTER `local_id`;