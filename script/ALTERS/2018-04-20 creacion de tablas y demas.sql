CREATE TABLE `recarga` (
  `rec_cod` int(11) DEFAULT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `rec_trans` varchar(255) DEFAULT NULL,
  `rec_nro` varchar(9) DEFAULT NULL,
  `rec_ope`  int DEFAULT NULL COMMENT 'Se enlaza con tabla diccionario_termino en el grupo 3'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `recarga`
MODIFY COLUMN `rec_cod`  int(11) NOT NULL AUTO_INCREMENT FIRST ,
ADD PRIMARY KEY (`rec_cod`);

ALTER TABLE `diccionario_termino`
ADD COLUMN `grupo`  int NULL AFTER `activo`;

INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(308,3,'generarRecarga', 'Recarga');
INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(713,7,'pagosRecarga', 'Pagos recarga');

update diccionario_termino set grupo=1 where id in(1,2)
update diccionario_termino set grupo=2 where id in(3)

INSERT INTO `diccionario_termino` VALUES ('4', 'operador', 'CLARO', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('5', 'operador', 'BITEL', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('6', 'operador', 'MOVISTAR', '9', '1', '3');
INSERT INTO `diccionario_termino` VALUES ('7', 'cliente', 'PUNTO DE VENTA', null, '1', '4');
INSERT INTO `diccionario_termino` VALUES ('8', 'cliente', 'CLIENTE FINAL', null, '1', '4');
INSERT INTO `diccionario_termino` VALUES ('9', 'operador', 'ENTEL', '9', '1', '3');