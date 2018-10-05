ALTER TABLE `producto_costo_unitario`
ADD COLUMN `porcentaje_utilidad`  decimal(18,2) NULL DEFAULT 0 COMMENT 'porcentaje de utilidad' AFTER `tipo_impuesto_compra`,
ADD COLUMN `tipo_cambio`  decimal(18,2) NULL DEFAULT 0 COMMENT 'tipo de cambio' AFTER `porcentaje_utilidad`;