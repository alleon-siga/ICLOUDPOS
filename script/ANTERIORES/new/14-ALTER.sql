--AGREGUE CAMPO PARA PODER ORGANIZAR LOS PRECIOS

ALTER TABLE `precios`
ADD COLUMN `orden` INT NULL DEFAULT 0 AFTER `estatus_precio`;

UPDATE `precios` SET `orden`='1' WHERE `id_precio`='3';
UPDATE `precios` SET `orden`='111' WHERE `id_precio`='2';
UPDATE `precios` SET `orden`='11' WHERE `id_precio`='1';

