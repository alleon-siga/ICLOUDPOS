ALTER TABLE `facturacion`
CHANGE COLUMN `nota` `nota` VARCHAR(500) NULL DEFAULT NULL ,
CHANGE COLUMN `cliente_nombre` `cliente_nombre` VARCHAR(150) NOT NULL ,
CHANGE COLUMN `cliente_direccion` `cliente_direccion` VARCHAR(150) NULL DEFAULT NULL ;

