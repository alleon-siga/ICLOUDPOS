ALTER TABLE `venta`
  ADD COLUMN `motivo` VARCHAR(500) NULL AFTER `plataforma`;


ALTER TABLE `facturacion`
  ADD COLUMN `estado_comprobante` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1 => Nuevo\n2 => Modificado\n3 => Anulado o dado de baja' AFTER `descuento`,
  CHANGE COLUMN `fecha` `fecha` DATETIME NOT NULL ,
  ADD COLUMN `fecha_cdr` DATETIME NULL AFTER `fecha`;

--
-- Table structure for table `facturacion_baja`
--

DROP TABLE IF EXISTS `facturacion_baja`;
CREATE TABLE `facturacion_baja` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha_emision` date NOT NULL,
  `correlativo` int(11) NOT NULL,
  `estado` tinyint(4) NOT NULL,
  `nota` varchar(500) DEFAULT NULL,
  `sunat_codigo` varchar(45) DEFAULT NULL,
  `hash_cpe` varchar(500) DEFAULT NULL,
  `hash_cdr` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `facturacion_baja_comprobantes`;
CREATE TABLE `facturacion_baja_comprobantes` (
  `comprobante_id` bigint(20) NOT NULL,
  `baja_id` bigint(20) NOT NULL,
  `motivo` varchar(250) NOT NULL,
  PRIMARY KEY (`comprobante_id`,`baja_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;