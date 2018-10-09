ALTER TABLE `cotizacion_detalles`
ADD COLUMN `descuento`  decimal(18,2) NULL DEFAULT 0 COMMENT 'Porcentaje de descuento' AFTER `precio_venta`;