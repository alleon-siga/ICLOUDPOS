ALTER TABLE `facturacion_detalle`
  ADD COLUMN `tipo_precio` VARCHAR(45) NOT NULL DEFAULT '01' AFTER `impuesto`;

ALTER TABLE `facturacion_detalle`
  ADD COLUMN `tipo_tributo` VARCHAR(45) NOT NULL DEFAULT '10' AFTER `tipo_precio`;

