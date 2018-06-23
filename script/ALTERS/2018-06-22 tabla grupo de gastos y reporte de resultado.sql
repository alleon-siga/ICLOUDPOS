INSERT INTO opcion VALUES('723', '7', 'estadoresultado', 'Estado de Resultados');

CREATE TABLE `grupo_gastos` (
	`id_grupo_gastos`  int NOT NULL AUTO_INCREMENT ,
	`nom_grupo_gastos`  varchar(255) NULL ,
	PRIMARY KEY (`id_grupo_gastos`)
);

INSERT INTO `grupo_gastos` VALUES ('1', 'GASTO DE VENTA');
INSERT INTO `grupo_gastos` VALUES ('2', 'GASTO ADMINISTRATIVO');
INSERT INTO `grupo_gastos` VALUES ('3', 'GASTO FINANCIERO');

ALTER TABLE `tipos_gasto`
ADD COLUMN `id_grupo_gastos`  int NULL AFTER `tipo_tipos_gasto`;