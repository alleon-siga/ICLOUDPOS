CREATE TABLE `shadow_stock` (
  `producto_id` BIGINT(20) NOT NULL,
  `stock` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`producto_id`),
  UNIQUE INDEX `producto_id_UNIQUE` (`producto_id` ASC));

CREATE TABLE `venta_contable_detalle` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `venta_id` BIGINT(20) NOT NULL,
  `producto_id` BIGINT(20) NOT NULL,
  `unidad_id` BIGINT(20) NOT NULL,
  `precio` FLOAT NOT NULL DEFAULT 0,
  `cantidad` FLOAT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`));

