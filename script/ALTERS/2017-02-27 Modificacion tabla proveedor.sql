ALTER TABLE `proveedor` CHANGE COLUMN `proveedor_nrofax` `proveedor_ruc` VARCHAR(20) NULL DEFAULT ''
AFTER `id_proveedor` , CHANGE COLUMN `proveedor_direccion2` `proveedor_contacto` TEXT NULL DEFAULT NULL  AFTER `proveedor_telefono1` ;
