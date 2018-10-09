

ALTER TABLE `traspaso_detalle` ADD COLUMN `local_origen` INT(11) NULL  AFTER `traspaso_id` ;

UPDATE traspaso_detalle, traspaso
SET traspaso_detalle.local_origen = traspaso.local_origen
WHERE traspaso.id = traspaso_detalle.traspaso_id

ALTER TABLE `traspaso` DROP COLUMN `local_origen` ;