CREATE TABLE IF NOT EXISTS `banco` (
  `banco_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `banco_nombre` varchar(255) DEFAULT NULL,
  `banco_numero_cuenta` varchar(255) DEFAULT NULL,
  `banco_saldo` float DEFAULT NULL,
  `banco_cuenta_contable` varchar(255) DEFAULT NULL,
  `banco_titular` varchar(255) DEFAULT NULL,
  `banco_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`banco_id`)
) DEFAULT CHARSET=latin1;

ALTER TABLE `credito_cuotas_abono`
  ADD COLUMN `banco_id` BIGINT NULL AFTER `usuario_pago`,
  ADD COLUMN `nro_operacion` VARCHAR(45) NULL AFTER `banco_id`;