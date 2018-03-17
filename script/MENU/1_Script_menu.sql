
-- SCRIPT PRODUCTOS

DELETE FROM `opcion` WHERE `nOpcion`='113';
UPDATE `opcion` SET `cOpcionDescripcion`='ingresocalzado', `cOpcionNombre`='Ingreso Calzado' WHERE `nOpcion`='116';
UPDATE `opcion` SET `cOpcionDescripcion`='plantillaproducto', `cOpcionNombre`='Plantilla Producto' WHERE `nOpcion`='117';
UPDATE `opcion` SET `cOpcionDescripcion`='seriescalzado', `cOpcionNombre`='Serie Calzado' WHERE `nOpcion`='118';
UPDATE `opcion` SET `cOpcionDescripcion`='reportecalzado', `cOpcionNombre`='Reporte Calzado' WHERE `nOpcion`='119';

-- SCRIPT COMPRAS

DELETE FROM `opcion` WHERE `nOpcion`='205';
DELETE FROM `opcion` WHERE `nOpcion`='206';


-- SCRIPT VENTAS
UPDATE `opcion` SET `cOpcionDescripcion`='cobraencaja', `cOpcionNombre`='Cobrar en Caja' WHERE `nOpcion`='302';
UPDATE `opcion` SET `cOpcionNombre`='Realizar Venta' WHERE `nOpcion`='301';
UPDATE `opcion` SET `cOpcionDescripcion`='cotizaciones', `cOpcionNombre`='Cotizaciones' WHERE `nOpcion`='303';
UPDATE `opcion` SET `cOpcionDescripcion`='historialventas', `cOpcionNombre`='Registro de Ventas' WHERE `nOpcion`='304';
INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('305', '3', 'anularventa', 'Anular & Devolver');
INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('306', '3', 'comprobantes', 'Comprobantes');
INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('307', '3', 'configurarventa', 'Configurar Venta');


-- SCRIPT CUADRE DE CAJA
DELETE FROM `opcion` WHERE `nOpcion`='603';


-- SCRIPT REPORTES
UPDATE `opcion` SET `cOpcionDescripcion`='resumenventas', `cOpcionNombre`='Resumen de ventas' WHERE `nOpcion`='701';
UPDATE `opcion` SET `cOpcionDescripcion`='valorizacioneinventario', `cOpcionNombre`='Valorización inventario' WHERE `nOpcion`='702';
UPDATE `opcion` SET `cOpcionDescripcion`='ingresodetallado', `cOpcionNombre`='Ingreso Detallado' WHERE `nOpcion`='703';
UPDATE `opcion` SET `cOpcionDescripcion`='estadodecuenta', `cOpcionNombre`='Estado de Cuenta' WHERE `nOpcion`='704';
UPDATE `opcion` SET `cOpcionDescripcion`='comisionxvendedor', `cOpcionNombre`='Comision por vendedor' WHERE `nOpcion`='705';
UPDATE `opcion` SET `cOpcionDescripcion`='ventaxcomprobante', `cOpcionNombre`='Ventas por comprobantes' WHERE `nOpcion`='706';
DELETE FROM `opcion` WHERE `nOpcion`='707';

-- SCRIPT OPCIONES
INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('813', '8', 'impuestos', 'Impuestos');

INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('707', '7', 'productovendido', 'Productos más vendidos');
INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('708', '7', 'ventaSucursal', 'Ventas por sucursal');
INSERT INTO `opcion` (`nOpcion`, `nOpcionClase`, `cOpcionDescripcion`, `cOpcionNombre`) VALUES ('709', '7', 'ventaEmpleado', 'Ventas por empleado');

wfHu6~Skek4E