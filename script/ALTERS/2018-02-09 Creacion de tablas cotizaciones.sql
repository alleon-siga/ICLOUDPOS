CREATE TABLE `cotizacion` (
  `id` BIGINT(20) NOT NULL,
  `fecha` DATETIME NOT NULL,
  `cliente_id` BIGINT(20) NOT NULL,
  `vendedor_id` BIGINT(20) NOT NULL,
  `documento_id` BIGINT(20) NOT NULL,
  `tipo_pago_id` BIGINT(20) NOT NULL,
  `moneda_id` BIGINT(20) NOT NULL,
  `estado` VARCHAR(45) NOT NULL,
  `impuesto` DECIMAL(18,2) NOT NULL,
  `subtotal` DECIMAL(18,2) NOT NULL,
  `total` DECIMAL(18,2) NOT NULL,
  `tasa_cambio` FLOAT NULL DEFAULT 0,
  `credito_periodo` VARCHAR(45) NULL,
  `periodo_per` INT NULL,
  PRIMARY KEY (`id`));


CREATE TABLE `cotizacion_detalles` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `cotizacion_id` BIGINT(20) NOT NULL,
  `producto_id` BIGINT(20) NOT NULL,
  `unidad_id` BIGINT(20) NOT NULL,
  `precio` DECIMAL(18,2) NOT NULL,
  `cantidad` DECIMAL(18,2) NOT NULL,
  PRIMARY KEY (`id`));
