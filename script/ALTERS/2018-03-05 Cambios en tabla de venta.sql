ALTER TABLE `venta`
ADD COLUMN `fecha_facturacion` DATETIME NULL AFTER `fecha`,
ADD COLUMN `serie` VARCHAR(45) NULL AFTER `fecha_facturacion`,
ADD COLUMN `numero` VARCHAR(45) NULL AFTER `serie`,
ADD COLUMN `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP AFTER `inicial`;

ALTER TABLE `documentos` ADD COLUMN `estado` BIT NULL  AFTER `des_doc` ;

ALTER TABLE `detalleingreso`
ADD COLUMN `impuesto_id` INT NULL AFTER `precio_venta`,
ADD COLUMN `impuesto_porciento` DECIMAL(18,2) NULL AFTER `impuesto_id`;

ALTER TABLE `detalle_venta`
ADD COLUMN `impuesto_id` INT NULL AFTER `detalle_utilidad`,
ADD COLUMN `impuesto_porciento` DECIMAL(18,2) NULL AFTER `impuesto_id`,
ADD COLUMN `descuento` DECIMAL(18,2) NULL DEFAULT 0 AFTER `impuesto_porciento`;


