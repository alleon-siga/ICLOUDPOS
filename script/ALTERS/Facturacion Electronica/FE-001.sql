
ALTER TABLE `facturacion`
  ADD COLUMN `estado_comprobante` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1 => Nuevo\n2 => Modificado\n3 => Anulado o dado de baja' AFTER `descuento`;


CREATE TABLE `facturacion_baja_comprobantes` (
  `comprobante_id` BIGINT(20) NOT NULL,
  `baja_id` BIGINT(20) NOT NULL,
  `motivo` VARCHAR(250) NOT NULL,
  PRIMARY KEY (`comprobante_id`, `baja_id`));
