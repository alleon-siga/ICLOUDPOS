CREATE  TABLE `ajuste` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT ,
  `usuario_id` BIGINT(20) NOT NULL ,
  `local_id` BIGINT(20) NOT NULL ,
  `moneda_id` BIGINT(20) NOT NULL ,
  `fecha` DATETIME NOT NULL ,
  `operacion` VARCHAR(5) NOT NULL ,
  `io` VARCHAR(2) NOT NULL ,
  `documento` VARCHAR(5) NOT NULL ,
  `serie` VARCHAR(45) NULL ,
  `numero` VARCHAR(45) NULL ,
  `estado` VARCHAR(45) NOT NULL ,
  `total_importe` DECIMAL(18,2) NULL DEFAULT 0.00 ,
  `tasa_cambio` FLOAT NULL DEFAULT 0.00 ,
  `operacion_otros` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) );

CREATE  TABLE `ajuste_detalle` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT ,
  `ajuste_id` BIGINT(20) NOT NULL ,
  `producto_id` BIGINT(20) NOT NULL ,
  `unidad_id` BIGINT(20) NOT NULL ,
  `cantidad` DECIMAL(18,2) NOT NULL ,
  `costo_unitario` FLOAT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) );

