ALTER TABLE `cotizacion`
ADD COLUMN `tipo_impuesto` TINYINT(1) NULL AFTER `created_at`;

ALTER TABLE `cotizacion_detalles`
ADD COLUMN `impuesto` DECIMAL(18,2) NOT NULL DEFAULT 0 AFTER `cantidad`;

