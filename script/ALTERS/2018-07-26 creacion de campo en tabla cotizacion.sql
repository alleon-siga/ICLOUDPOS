ALTER TABLE `cotizacion`
ADD COLUMN `nota`  longtext NULL COMMENT 'Nota de la cotizacion' AFTER `tipo_impuesto`;