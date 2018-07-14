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