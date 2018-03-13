CREATE TABLE `comprobantes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `serie` VARCHAR(45) NOT NULL,
  `desde` INT NOT NULL,
  `hasta` INT NOT NULL,
  `longitud` INT NOT NULL,
  `estado` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `comprobante_ventas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `venta_id` BIGINT(20) NOT NULL,
  `comprobante_id` INT NOT NULL,
  `numero` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`));

ALTER TABLE `venta`
ADD COLUMN `comprobante_id` INT NULL DEFAULT 0 AFTER `tipo_impuesto`;
