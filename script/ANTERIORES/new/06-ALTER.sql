CREATE TABLE `producto_costo_unitario` (
  `producto_id` BIGINT(20) NOT NULL,
  `moneda_id` INT NOT NULL,
  `costo` FLOAT NOT NULL,
  `activo` VARCHAR(2) NULL);