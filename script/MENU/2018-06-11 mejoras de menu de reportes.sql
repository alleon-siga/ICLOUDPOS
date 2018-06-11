DELETE FROM opcion_grupo where Opcion>=700 and Opcion like '7%';

DELETE FROM opcion where nOpcionClase=7;

INSERT INTO opcion VALUES('701','7','inventariopadre','Inventario');
INSERT INTO opcion VALUES('702','7','valorizacioneinventario','Valorización inventario');
INSERT INTO opcion VALUES('703','7','entradasysalidas','Entradas & Salidas');
INSERT INTO opcion VALUES('704','7','stockventas','Stock y ventas');
INSERT INTO opcion VALUES('705','7','ingresodetallado','Ingreso detallado');
INSERT INTO opcion VALUES('706','7','ventapadre','Venta');
INSERT INTO opcion VALUES('707','7','resumenventas','Resumen de ventas');
INSERT INTO opcion VALUES('708','7','comisionxvendedor','Comision por vendedor');
INSERT INTO opcion VALUES('709','7','ventaxcomprobante','Ventas x comprobantes');
INSERT INTO opcion VALUES('710','7','productovendido','Productos + vendidos');
INSERT INTO opcion VALUES('711','7','ventaSucursal','Ventas x sucursal');
INSERT INTO opcion VALUES('712','7','ventaEmpleado','Ventas x empleado');
INSERT INTO opcion VALUES('713','7','margenutilidad','Margen de utilidad');
INSERT INTO opcion VALUES('714','7','utilidadProducto','Utilidades por producto');
INSERT INTO opcion VALUES('715','7','hojaColecta','Reporte hoja de colecta');
INSERT INTO opcion VALUES('716','7','recargaDia','Recargas del día');
INSERT INTO opcion VALUES('717','7','recargaCobranza','Cobranza del día');
INSERT INTO opcion VALUES('718','7','recargaCuentasC','Cuentas por cobrar');
INSERT INTO opcion VALUES('719','7','comprapadre','Compra');
INSERT INTO opcion VALUES('720','7','pagoproveedores','Pago a proveedores');
INSERT INTO opcion VALUES('721','7','cajapadre','Caja');
INSERT INTO opcion VALUES('722','7','gastosDia','Gastos del dia');
