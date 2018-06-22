CREATE TABLE `grupo_gastos` (
	`id_grupo_gastos`  int NOT NULL AUTO_INCREMENT ,
	`nom_grupo_gastos`  varchar(255) NULL ,
	PRIMARY KEY (`id_grupo_gastos`)
);

INSERT INTO `grupo_gastos` VALUES ('1', 'GASTO DE VENTA');
INSERT INTO `grupo_gastos` VALUES ('2', 'GASTO ADMINISTRATIVO');
INSERT INTO `grupo_gastos` VALUES ('3', 'GASTO FINANCIERO');

INSERT INTO opcion VALUES('723', '7', 'estadoresultado', 'Estado de Resultado');

ALTER TABLE `tipos_gasto`
ADD COLUMN `id_grupo_gastos`  int NULL AFTER `tipo_tipos_gasto`;

INSERT INTO tipos_gasto VALUES(11, 'GASTOS DE MARKETING', '1', NULL, 1);

UPDATE tipos_gasto SET id_grupo_gastos=1 WHERE id_tipos_gasto IN(5,11,7,2);
UPDATE tipos_gasto SET id_grupo_gastos=2 WHERE id_tipos_gasto IN(3, 6, 10, 1, 4);
UPDATE tipos_gasto SET id_grupo_gastos=3 WHERE id_tipos_gasto IN(8, 9);

UPDATE detalle_venta SET impuesto_porciento='18.00'