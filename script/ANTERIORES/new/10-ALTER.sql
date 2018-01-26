ALTER TABLE `producto_costo_unitario`
ADD COLUMN `contable_costo` FLOAT NULL DEFAULT 0 AFTER `activo`,
ADD COLUMN `contable_activo` VARCHAR(2) NULL DEFAULT 0 AFTER `contable_costo`;


