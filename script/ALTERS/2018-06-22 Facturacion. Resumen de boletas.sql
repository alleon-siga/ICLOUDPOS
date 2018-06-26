CREATE TABLE `facturacion_resumen` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fecha` DATETIME NOT NULL,
  `fecha_ref` DATETIME NOT NULL,
  `correlativo` INT NOT NULL,
  `estado` TINYINT(4) NOT NULL,
  `nota` VARCHAR(500) NOT NULL,
  `sunat_codigo` VARCHAR(45) NOT NULL,
  `hash_cpe` VARCHAR(500) NULL,
  `hash_cdr` VARCHAR(500) NULL,
  `ticket` varchar(150) NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `facturacion_resumen_comprobantes` (
  `comprobante_id` INT NOT NULL,
  `resumen_id` INT NOT NULL,
  PRIMARY KEY (`comprobante_id`));
