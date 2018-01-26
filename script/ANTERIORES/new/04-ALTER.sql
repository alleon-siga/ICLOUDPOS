CREATE TABLE IF NOT EXISTS `correlativos` (
  `id_local` int(11) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `correlativo` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `venta_devolucion` (
  `id_devolucion` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) DEFAULT NULL,
  `id_producto` bigint(20) DEFAULT NULL,
  `precio` decimal(18,2) DEFAULT NULL,
  `cantidad` decimal(18,3) NOT NULL DEFAULT '0.000',
  `unidad_medida` bigint(20) DEFAULT NULL,
  `detalle_importe` decimal(18,2) DEFAULT NULL,
  `detalle_costo_promedio` decimal(18,2) DEFAULT '0.00',
  `detalle_utilidad` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`id_devolucion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


ALTER TABLE `venta` 
ADD COLUMN `id_documento` INT(11) NOT NULL AFTER `tasa_cambio`,
ADD COLUMN `correlativo` BIGINT(20) NULL AFTER `id_documento`,
ADD COLUMN `dni_garante` VARCHAR(20) NULL AFTER `correlativo`,
ADD COLUMN `inicial` DECIMAL(6,2) NULL AFTER `dni_garante`;