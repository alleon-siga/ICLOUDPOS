CREATE TABLE IF NOT EXISTS `tarjeta_pago` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`));

INSERT INTO `tarjeta_pago` (`nombre`) VALUES ('Visa');
INSERT INTO `tarjeta_pago` (`nombre`) VALUES ('Mastercard');

CREATE TABLE IF NOT EXISTS `venta_tarjeta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `venta_id` BIGINT(20) NOT NULL,
  `tarjeta_pago_id` INT NOT NULL,
  `numero` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`));

