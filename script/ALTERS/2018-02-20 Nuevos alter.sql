ALTER TABLE `credito`
ADD COLUMN `periodo_gracia` INT(10) NULL DEFAULT 0 AFTER `fecha_cancelado`;

ALTER TABLE `credito_cuotas`
ADD COLUMN `numero_unico` VARCHAR(100) NULL AFTER `nro_letra`;

INSERT INTO `metodos_pago` (`nombre_metodo`, `status_metodo`, `tipo_metodo`) VALUES ('LETRA', '1', 'BANCO');
INSERT INTO `metodos_pago` (`nombre_metodo`, `status_metodo`, `tipo_metodo`) VALUES ('TRANSFERENCIA BANCARIA', '1', 'BANCO');

CREATE TABLE `ingreso_credito` (
  `ingreso_id` BIGINT(20) NOT NULL,
  `numero_cuotas` INT NOT NULL,
  `monto_cuota` DECIMAL(18,2) NOT NULL,
  `monto_debito` DECIMAL(18,2) NOT NULL DEFAULT 0,
  `estado` VARCHAR(45) NOT NULL,
  `inicial` DECIMAL(18,2) NOT NULL,
  `periodo_gracia` INT NULL,
  `ultima_fecha_pago` DATETIME NULL,
  PRIMARY KEY (`ingreso_id`),
  UNIQUE INDEX `ingreso_id_UNIQUE` (`ingreso_id` ASC));

CREATE TABLE `ingreso_credito_cuotas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ingreso_id` BIGINT(20) NOT NULL,
  `monto` DECIMAL(18,2) NOT NULL,
  `letra` VARCHAR(45) NOT NULL,
  `fecha_vencimiento` DATETIME NOT NULL,
  `pagado` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_cancelada` DATETIME NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `pagos_ingreso`
  DROP COLUMN `tasa_cambio`,
  DROP COLUMN `id_moneda`;

ALTER TABLE `pagos_ingreso`
  ADD COLUMN `estado` VARCHAR(45) NULL AFTER `operacion`;





