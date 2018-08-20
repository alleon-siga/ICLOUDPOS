DROP TABLE IF EXISTS `usuario_facturador`;
CREATE TABLE `usuario_facturador` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `username` varchar(18) NOT NULL,
  `var_usuario_clave` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `id_local` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO usuario_facturador(nombre, username, var_usuario_clave, id_local) VALUES('Facturador', 'facturador', 'b867d9cd482834bbf35e785855f416d5', '1');
