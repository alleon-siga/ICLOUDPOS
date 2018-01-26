INSERT INTO `opcion` (`nOpcion`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('75', 'cajas', 'Cajas');


UPDATE `opcion` SET `nOpcionClase`='75' WHERE `nOpcion`='68';
UPDATE `opcion` SET `nOpcionClase`='75' WHERE `nOpcion`='69';
UPDATE `opcion` SET `nOpcionClase`='75' WHERE `nOpcion`='70';


INSERT INTO `opcion_grupo` (`grupo`, `Opcion`, `var_opcion_usuario_estado`) VALUES ('1', '75', '1');
INSERT INTO `opcion_grupo` (`grupo`, `Opcion`, `var_opcion_usuario_estado`) VALUES ('2', '75', '1');
INSERT INTO `opcion_grupo` (`grupo`, `Opcion`, `var_opcion_usuario_estado`) VALUES ('3', '75', '1');
INSERT INTO `opcion_grupo` (`grupo`, `Opcion`, `var_opcion_usuario_estado`) VALUES ('4', '75', '1');


DELETE FROM `opcion_grupo` WHERE `grupo`='1' and`Opcion`='71';
DELETE FROM `opcion_grupo` WHERE `grupo`='3' and`Opcion`='71';
DELETE FROM `opcion_grupo` WHERE `grupo`='4' and`Opcion`='71';

DELETE FROM `opcion` WHERE `nOpcion`='71';
