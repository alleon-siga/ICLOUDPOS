CREATE TABLE IF NOT EXISTS `ingreso_contable` (
  `id_ingreso` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `fecha_registro` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `int_Proveedor_id` BIGINT(20) DEFAULT NULL,
  `nUsuCodigo` BIGINT(20) DEFAULT NULL,
  `local_id` BIGINT(20) DEFAULT NULL,
  `tipo_documento` VARCHAR(45) DEFAULT NULL,
  `documento_serie` CHAR(8) DEFAULT NULL,
  `documento_numero` CHAR(20) DEFAULT NULL,
  `ingreso_status` VARCHAR(45) DEFAULT NULL,
  `fecha_emision` TIMESTAMP NULL DEFAULT NULL,
  `tipo_ingreso` VARCHAR(45) DEFAULT NULL,
  `impuesto_ingreso` DOUBLE DEFAULT NULL,
  `sub_total_ingreso` DOUBLE DEFAULT NULL,
  `total_ingreso` DOUBLE DEFAULT NULL,
  `pago` VARCHAR(45) DEFAULT NULL,
  `ingreso_observacion` TEXT,
  `id_moneda` INT(11) DEFAULT NULL,
  `tasa_cambio` DECIMAL(4,2) DEFAULT NULL,
  `factura_ingreso_id` BIGINT(20) DEFAULT NULL,
  `facturado` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`id_ingreso`)
) DEFAULT CHARSET=latin1;

ALTER TABLE `ingreso_contable`
  ADD FOREIGN KEY (`int_Proveedor_id`) REFERENCES `proveedor`(`id_proveedor`),
  ADD FOREIGN KEY (`nUsuCodigo`) REFERENCES `usuario`(`nUsuCodigo`),
  ADD FOREIGN KEY (`local_id`) REFERENCES `local`(`int_local_id`),
  ADD FOREIGN KEY (`id_moneda`) REFERENCES `moneda`(`id_moneda`);

CREATE TABLE IF NOT EXISTS `detalleingreso_contable` (
  `id_detalle_ingreso` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ingreso` bigint(20) DEFAULT NULL,
  `id_producto` bigint(20) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio` decimal(18,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `unidad_medida` bigint(20) DEFAULT NULL,
  `total_detalle` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`id_detalle_ingreso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `detalleingreso_contable`
  ADD FOREIGN KEY (`id_ingreso`) REFERENCES `ingreso`(`id_ingreso`),
  ADD FOREIGN KEY (`id_producto`) REFERENCES `producto`(`producto_id`),
  ADD FOREIGN KEY (`unidad_medida`) REFERENCES `unidades`(`id_unidad`);
