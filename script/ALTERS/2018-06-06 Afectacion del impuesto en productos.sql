ALTER TABLE `producto`
ADD COLUMN `producto_afectacion_impuesto` VARCHAR(2) NULL AFTER `producto_stockminimo`;

UPDATE `producto` SET `producto_afectacion_impuesto`='1';

INSERT INTO `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) VALUES ('producto_afectacion_impuesto', 'producto_afectacion_impuesto', 'Afectacion del Impuesto', 'producto', '0', '1', '67');

ALTER TABLE `columnas`
ADD COLUMN `orden` INT NOT NULL AFTER `id_columna`;

UPDATE `columnas` SET `orden`='1' WHERE `id_columna`='35';
UPDATE `columnas` SET `orden`='2' WHERE `id_columna`='36';
UPDATE `columnas` SET `orden`='3' WHERE `id_columna`='37';
UPDATE `columnas` SET `orden`='4' WHERE `id_columna`='38';
UPDATE `columnas` SET `orden`='5' WHERE `id_columna`='39';
UPDATE `columnas` SET `orden`='6' WHERE `id_columna`='40';
UPDATE `columnas` SET `orden`='7' WHERE `id_columna`='41';
UPDATE `columnas` SET `orden`='8' WHERE `id_columna`='42';
UPDATE `columnas` SET `orden`='9' WHERE `id_columna`='43';
UPDATE `columnas` SET `orden`='10' WHERE `id_columna`='53';
UPDATE `columnas` SET `orden`='11' WHERE `id_columna`='54';
UPDATE `columnas` SET `orden`='13' WHERE `id_columna`='55';
UPDATE `columnas` SET `orden`='14' WHERE `id_columna`='56';
UPDATE `columnas` SET `orden`='15' WHERE `id_columna`='57';
UPDATE `columnas` SET `orden`='16' WHERE `id_columna`='58';
UPDATE `columnas` SET `orden`='17' WHERE `id_columna`='59';
UPDATE `columnas` SET `orden`='17' WHERE `id_columna`='60';
UPDATE `columnas` SET `orden`='18' WHERE `id_columna`='61';
UPDATE `columnas` SET `orden`='19' WHERE `id_columna`='63';
UPDATE `columnas` SET `orden`='20' WHERE `id_columna`='64';
UPDATE `columnas` SET `orden`='21' WHERE `id_columna`='65';
UPDATE `columnas` SET `orden`='22' WHERE `id_columna`='66';
UPDATE `columnas` SET `orden`='12' WHERE `id_columna`='67';


ALTER TABLE `facturacion`
ADD COLUMN `total_gravadas` DECIMAL(18,2) NULL DEFAULT 0 AFTER `cliente_direccion`,
ADD COLUMN `total_exoneradas` DECIMAL(18,2) NULL DEFAULT 0 AFTER `total_gravadas`,
ADD COLUMN `total_inafectas` DECIMAL(18,2) NULL DEFAULT 0 AFTER `total_exoneradas`,
ADD COLUMN `sunat_codigo` VARCHAR(45) NULL DEFAULT NULL AFTER `nota`;


ALTER TABLE `detalle_venta`
ADD COLUMN `afectacion_impuesto` VARCHAR(2) NOT NULL AFTER `impuesto_id`;

INSERT INTO `documentos` (`id_doc`, `des_doc`, `abr_doc`, `compras`, `ventas`, `gastos`) VALUES ('8', 'NC BOLETA', 'NCB', '0', '0', '0');
INSERT INTO `documentos` (`id_doc`, `des_doc`, `abr_doc`, `compras`, `ventas`, `gastos`) VALUES ('9', 'NC FACTURA', 'NCF', '0', '0', '0');


