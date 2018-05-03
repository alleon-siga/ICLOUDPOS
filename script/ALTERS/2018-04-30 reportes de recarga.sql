
TRUNCATE TABLE `recarga`;

CREATE TABLE `recarga` (
  `rec_cod` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) DEFAULT NULL,
  `rec_trans` varchar(255) DEFAULT NULL,
  `rec_nro` varchar(9) DEFAULT NULL,
  `rec_ope`  int DEFAULT NULL COMMENT 'Se enlaza con tabla diccionario_termino en el grupo 3',
  `rec_pob`  int DEFAULT NULL COMMENT 'Codigo de centro de poblado',
  PRIMARY KEY (`rec_cod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(714,7,'pagosRecarga', 'Pagos recargas');
UPDATE opcion SET cOpcionDescripcion='recargaDia', cOpcionNombre='Recargas del día' where nOpcion=714;
INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(715,7,'recargaCobranza', 'Cobranza del día');
INSERT INTO opcion(nOpcion, nOpcionClase, cOpcionDescripcion, cOpcionNombre) VALUES(716,7,'recargaCuentasC', 'Cuentas por cobrar');

update diccionario_termino set grupo=1 where id in(1,2);
update diccionario_termino set grupo=2 where id in(3);

INSERT INTO `diccionario_termino` VALUES ('4', 'operador', 'BITEL', '9', '1', '3');