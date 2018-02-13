ALTER TABLE `cotizacion`
ADD COLUMN `fecha_entrega` DATETIME NULL AFTER `periodo_per`,
ADD COLUMN `lugar_entrega` VARCHAR(200) NULL AFTER `fecha_entrega`;
