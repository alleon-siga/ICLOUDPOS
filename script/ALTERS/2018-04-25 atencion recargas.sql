CREATE TABLE `recarga` (
  `rec_cod` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) DEFAULT NULL,
  `rec_trans` varchar(255) DEFAULT NULL,
  `rec_nro` varchar(9) DEFAULT NULL,
  `rec_ope`  int DEFAULT NULL COMMENT 'Se enlaza con tabla diccionario_termino en el grupo 3',
  `rec_pob`  int DEFAULT NULL COMMENT 'Codigo de centro de poblado',
  PRIMARY KEY (`rec_cod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `diccionario_termino`
ADD COLUMN `grupo`  int NULL AFTER `activo`;

INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(308,3,'generarRecarga', 'Recargas');
INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(713,7,'hojaColecta', 'Hoja de colecta');
INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(714,7,'pagosRecarga', 'Pagos recargas');

-- CREAR EL PRODUCTO "RECARGA VIRTUAL"


update diccionario_termino set grupo=1 where id in(1,2);
update diccionario_termino set grupo=2 where id in(3);

INSERT INTO `diccionario_termino` VALUES ('4', 'operador', 'BITEL', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('5', 'operador', 'CLARO', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('6', 'operador', 'MOVISTAR', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('7', 'cliente', 'PUNTO DE VENTA', null, '1', '4');
INSERT INTO `diccionario_termino` VALUES ('8', 'cliente', 'CLIENTE FINAL', null, '1', '4');
INSERT INTO `diccionario_termino` VALUES ('9', 'operador', 'ENTEL', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('10', 'centro_poblado', 'aaa', '45', '1', '5');