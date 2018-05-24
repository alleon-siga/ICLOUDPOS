ALTER TABLE `venta`
ADD COLUMN `facturacion` TINYINT NULL DEFAULT 0 AFTER `numero`;

ALTER TABLE `venta`
ADD COLUMN `facturacion_nota` VARCHAR(255) NULL AFTER `facturacion`;




