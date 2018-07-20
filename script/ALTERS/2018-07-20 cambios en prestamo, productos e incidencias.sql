/* nuevos campos */
ALTER TABLE ingreso_credito
ADD COLUMN capital decimal(18, 0) NULL AFTER numero_cuotas,
ADD COLUMN interes decimal(18, 0) NULL AFTER capital,
ADD COLUMN comision decimal(18, 0) NULL AFTER interes;

ALTER TABLE ingreso_credito_cuotas 
ADD COLUMN capital decimal(18, 0) NULL AFTER monto,
ADD COLUMN interes decimal(18, 0) NULL AFTER capital,
ADD COLUMN comision decimal(18, 0) NULL AFTER interes;

/* para estados de resultados */
INSERT INTO tipos_gasto(nombre_tipos_gasto, status_tipos_gasto, tipo_tipos_gasto, id_grupo_gastos) VALUES('INTERES','1', '0', '3');
INSERT INTO tipos_gasto(nombre_tipos_gasto, status_tipos_gasto, tipo_tipos_gasto, id_grupo_gastos) VALUES('COMISION','1', '0', '3');
	
/* PARA PRESTAMO BANCARIO */
ALTER TABLE `ingreso`
ADD COLUMN `id_gastos`  int NULL COMMENT 'CODIGO UNICO DE TABLA GASTOS' AFTER `tipo_impuesto`,
ADD COLUMN `int_usuario_id`  int NULL COMMENT 'CODIGO UNICO DE USUARIO PARA GASTOS' AFTER `id_gastos`;

/* para configuracion ventas */
INSERT INTO configuraciones VALUES('57', 'NOMBRE_PRODUCTO', '["0","0","0","0","0","0"]');

/* PARA CORREGIR EL ERROR DE AGREGAR PRODUCTO CAMPO LINEA Y COMENTARIOS */
ALTER TABLE `lineas`
MODIFY COLUMN `id_linea`  bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Codigo unico' FIRST ,
MODIFY COLUMN `nombre_linea`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '' COMMENT 'Nombre de linea' AFTER `id_linea`,
MODIFY COLUMN `estatus_linea`  tinyint(1) NULL DEFAULT 1 COMMENT '1 = Activo, 0 = Inactivo' AFTER `nombre_linea`,
ADD PRIMARY KEY (`id_linea`);
