ALTER TABLE `venta`
ADD COLUMN `nota` LONGTEXT NULL AFTER `comprobante_id`;


ALTER TABLE `cotizacion_detalles`
ADD COLUMN `precio_venta` DECIMAL(18,2) NULL DEFAULT 0 AFTER `impuesto`;
