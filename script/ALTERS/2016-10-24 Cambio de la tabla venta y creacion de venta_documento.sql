ALTER TABLE `venta`
CHANGE COLUMN `numero_documento` `factura_impresa` TINYINT(2) NULL DEFAULT 0 ;


CREATE TABLE `venta_documento` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `venta_id` BIGINT(20) NOT NULL,
  `numero_documento` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));
