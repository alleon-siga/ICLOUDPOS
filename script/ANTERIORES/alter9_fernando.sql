ALTER TABLE `cliente`
  DROP COLUMN `codigo_postal`, 
  DROP COLUMN `direccion2`, 
  DROP COLUMN `fax`;
INSERT INTO opcion VALUES (NULL,'30', 'cuentasporpagar','Cuentas por pagar');

INSERT INTO configuraciones VALUES (NULL,'VENTA_DEFAULT','NOMBRE');