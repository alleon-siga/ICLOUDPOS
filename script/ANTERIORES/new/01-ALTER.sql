CREATE TABLE IF NOT EXISTS `movimiento_historico` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) NOT NULL,
  `local_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `cantidad` bigint(20) NOT NULL,
  `old_cantidad` bigint(20) DEFAULT NULL,
  `date` datetime NOT NULL,
  `tipo_movimiento` varchar(45) NOT NULL,
  `tipo_operacion` varchar(45) NOT NULL,
  `referencia_valor` varchar(100) DEFAULT NULL,
  `referencia_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;