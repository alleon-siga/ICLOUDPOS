ALTER TABLE `venta`
DROP COLUMN `facturacion_nota`,
DROP COLUMN `facturacion`;

ALTER TABLE `facturacion_emisor`
ADD COLUMN `env` VARCHAR(45) NOT NULL AFTER `moneda`;
