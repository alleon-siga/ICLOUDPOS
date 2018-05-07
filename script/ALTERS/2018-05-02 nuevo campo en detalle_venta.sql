ALTER TABLE `detalle_venta`
ADD COLUMN `detalle_costo_ultimo` DECIMAL(18,2) NULL DEFAULT 0 AFTER `detalle_costo_promedio`;
