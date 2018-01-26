-- CAMBIOS EN LA TABLA LOCAL

ALTER TABLE `local`
ADD COLUMN `principal` TINYINT(1) NOT NULL DEFAULT 0 AFTER `local_status`,
ADD COLUMN `distrito_id` INT NULL AFTER `principal`,
ADD COLUMN `direccion` VARCHAR(100) NULL AFTER `distrito_id`,
ADD COLUMN `telefono` VARCHAR(100) NULL AFTER `direccion`;


-- BORRADO DE VALORES EN CONFIGURACIONES

DELETE FROM `configuraciones` WHERE `config_id`='2';
DELETE FROM `configuraciones` WHERE `config_id`='3';


-- CREACION DE LA TABLA DISTRITO PARA EL COMPLETAMIENTO DE LA ESTRUCTURA DE LOCALIZACION

CREATE TABLE `distrito` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ciudad_id` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));


-- MODIFICACION EN LA TABLA CORRELATIVOS

ALTER TABLE `correlativos`
CHANGE COLUMN `correlativo` `correlativo` BIGINT(20) NULL DEFAULT 0 ,
ADD COLUMN `serie` VARCHAR(45) NULL AFTER `id_documento`;

DELETE FROM `correlativos`;

ALTER TABLE `correlativos`
CHANGE COLUMN `correlativo` `correlativo` BIGINT(20) NOT NULL DEFAULT 1 ,
ADD PRIMARY KEY (`id_local`, `id_documento`);


-- ELIMINACION DE LA TABLA DOCUMENTO_VENTA y gotoxy_imp

DROP TABLE `documento_venta`;

DROP TABLE `gotoxy_imp`;


-- CAMBIO EN LAS TABLA VENTAS

ALTER TABLE `venta`
DROP COLUMN `correlativo`,
CHANGE COLUMN `local_id` `local_id` BIGINT(20) NULL AFTER `venta_id`,
CHANGE COLUMN `id_documento` `id_documento` INT(11) NOT NULL AFTER `local_id`,
CHANGE COLUMN `condicion_pago` `condicion_pago` BIGINT(20) NULL DEFAULT NULL AFTER `id_vendedor`,
CHANGE COLUMN `id_moneda` `id_moneda` INT(11) NULL DEFAULT NULL AFTER `condicion_pago`,
CHANGE COLUMN `venta_status` `venta_status` VARCHAR(45) NULL AFTER `id_moneda`,
CHANGE COLUMN `numero_documento` `numero_documento` VARCHAR(20) NULL DEFAULT NULL ;




