ALTER TABLE `traspaso` DROP COLUMN `local_origen` ;

ALTER TABLE `traspaso_detalle` ADD COLUMN `local_origen` INT(11) NULL  AFTER `traspaso_id` ;