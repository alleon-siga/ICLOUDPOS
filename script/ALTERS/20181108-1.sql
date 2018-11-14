UPDATE `venta`
SET `nro_guia` = NULL;

ALTER TABLE `venta`
  CHANGE COLUMN `nro_guia` `nro_guia` VARCHAR(100) NULL DEFAULT NULL
COMMENT 'Correlativo de la guia de remision';

ALTER TABLE `facturacion_resumen_comprobantes`
  ADD COLUMN `estado` INT(11) NOT NULL
  AFTER `resumen_id`,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`comprobante_id`, `resumen_id`, `estado`);

UPDATE `facturacion_resumen_comprobantes`
SET `estado` = 1;


