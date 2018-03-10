ALTER TABLE `ingreso`
ADD COLUMN `tipo_impuesto` TINYINT(1) NULL AFTER `facturado`;

ALTER TABLE `venta`
ADD COLUMN `tipo_impuesto` TINYINT(1) NULL AFTER `dni_garante`;

