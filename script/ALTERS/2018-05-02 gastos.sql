ALTER TABLE `gastos`
ADD COLUMN `gravable`  char(1) NULL DEFAULT 0 COMMENT 'GRAVABLE 1=SI Y 0=NO' AFTER `motivo_eliminar`,
ADD COLUMN `id_documento`  int NULL COMMENT 'DOCUMENTO' AFTER `gravable`,
ADD COLUMN `serie`  varchar(255) NULL COMMENT 'NUMERO DE SERIE' AFTER `id_documento`,
ADD COLUMN `numero`  varchar(255) NULL COMMENT 'NUMERO' AFTER `serie`;

INSERT INTO documentos VALUES('7', 'RECIBO DE CAJA', '1', 'RC');

ALTER TABLE `documentos`
ADD COLUMN `compras`  char(1) NULL DEFAULT 0 COMMENT '0=NO, 1=SI' AFTER `abr_doc`,
ADD COLUMN `ventas`  char(1) NULL DEFAULT 0 COMMENT '0=NO, 1=SI' AFTER `compras`,
ADD COLUMN `gastos`  char(1) NULL DEFAULT 0 COMMENT '0=NO, 1=SI' AFTER `ventas`;

UPDATE documentos set gastos=1 where id_doc in(1,3,6,7);
UPDATE documentos set compras=1 where id_doc in(1,3,6);
UPDATE documentos set ventas=1 where id_doc in(1,3,6);

-- NEWLEVEL_INVENTARIO2017
truncate opcion;

INSERT INTO `opcion` VALUES ('1', null, 'inventario', 'Inventario');
INSERT INTO `opcion` VALUES ('2', null, 'ingresos', 'Compras');
INSERT INTO `opcion` VALUES ('3', null, 'ventas', 'Ventas');
INSERT INTO `opcion` VALUES ('4', null, 'clientespadre', 'Clientes');
INSERT INTO `opcion` VALUES ('5', null, 'proveedores', 'Proveedores');
INSERT INTO `opcion` VALUES ('6', null, 'cajas', 'Caja y Bancos');
INSERT INTO `opcion` VALUES ('7', null, 'reportes', 'Reportes');
INSERT INTO `opcion` VALUES ('8', null, 'opciones', 'Configuraciones');
INSERT INTO `opcion` VALUES ('9', null, 'dashboard', 'Dashboard');
INSERT INTO `opcion` VALUES ('101', '1', 'productos', 'Productos');
INSERT INTO `opcion` VALUES ('102', '1', 'stock', 'Stock Productos');
INSERT INTO `opcion` VALUES ('103', '1', 'traspaso', 'Traspasos de Almacen');
INSERT INTO `opcion` VALUES ('104', '1', 'ajusteinventario', 'Entradas & Salidas');
INSERT INTO `opcion` VALUES ('105', '1', 'listaprecios', 'Stock & Precios');
INSERT INTO `opcion` VALUES ('106', '1', 'movimientoinventario', 'Kardex');
INSERT INTO `opcion` VALUES ('107', '1', 'categorizacion', 'Categorizacion');
INSERT INTO `opcion` VALUES ('108', '1', 'marcas', 'Marcas');
INSERT INTO `opcion` VALUES ('109', '1', 'gruposproductos', 'Grupos');
INSERT INTO `opcion` VALUES ('110', '1', 'familias', 'Familias');
INSERT INTO `opcion` VALUES ('111', '1', 'lineas', 'Lineas');
INSERT INTO `opcion` VALUES ('112', '1', 'categorias', 'Categorias');
INSERT INTO `opcion` VALUES ('116', '1', 'ingresocalzado', 'Ingreso Calzado');
INSERT INTO `opcion` VALUES ('117', '1', 'plantillaproducto', 'Plantilla Producto');
INSERT INTO `opcion` VALUES ('118', '1', 'seriescalzado', 'Serie Calzado');
INSERT INTO `opcion` VALUES ('119', '1', 'reportecalzado', 'Reporte Calzado');
INSERT INTO `opcion` VALUES ('201', '2', 'registraringreo', 'Registrar Compras');
INSERT INTO `opcion` VALUES ('203', '2', 'consultarcompras', 'Consultar Compras');
INSERT INTO `opcion` VALUES ('204', '2', 'devolucioningreso', 'Anulacion Compras');
INSERT INTO `opcion` VALUES ('301', '3', 'generarventa', 'Realizar Venta');
INSERT INTO `opcion` VALUES ('302', '3', 'cobraencaja', 'Cobrar en Caja');
INSERT INTO `opcion` VALUES ('303', '3', 'cotizaciones', 'Cotizaciones');
INSERT INTO `opcion` VALUES ('304', '3', 'historialventas', 'Registro de Ventas');
INSERT INTO `opcion` VALUES ('305', '3', 'anularventa', 'Anular & Devolver');
INSERT INTO `opcion` VALUES ('306', '3', 'comprobantes', 'Comprobantes');
INSERT INTO `opcion` VALUES ('307', '3', 'configurarventa', 'Configurar venta');
INSERT INTO `opcion` VALUES ('308', '3', 'generarRecarga', 'Recarga');
INSERT INTO `opcion` VALUES ('401', '4', 'clientes', 'Registrar clientes');
INSERT INTO `opcion` VALUES ('402', '4', 'gruposcliente', 'Grupos de clientes');
INSERT INTO `opcion` VALUES ('403', '4', 'cuentasporcobrar', 'Cuentas x cobrar');
INSERT INTO `opcion` VALUES ('404', '4', 'estadocuenta', 'Estado de cuenta');
INSERT INTO `opcion` VALUES ('501', '5', 'proveedor', 'Registrar proveedores');
INSERT INTO `opcion` VALUES ('502', '5', 'cuentasporpagar', 'Cuentas x pagar');
INSERT INTO `opcion` VALUES ('601', '6', 'cajaybancos', 'Caja y bancos');
INSERT INTO `opcion` VALUES ('602', '6', 'gastos', 'Gastos');
INSERT INTO `opcion` VALUES ('604', '6', 'tiposgasto', 'Tipo gasto');
INSERT INTO `opcion` VALUES ('605', '6', 'regmonedas', 'Monedas');
INSERT INTO `opcion` VALUES ('606', '6', 'bancos', 'Bancos');
INSERT INTO `opcion` VALUES ('607', '6', 'cuadrecaja', 'Corte de caja');
INSERT INTO `opcion` VALUES ('701', '7', 'resumenventas', 'Resumen de ventas');
INSERT INTO `opcion` VALUES ('702', '7', 'valorizacioneinventario', 'Valorización inventario');
INSERT INTO `opcion` VALUES ('703', '7', 'entradasysalidas', 'Entradas & Salidas');
INSERT INTO `opcion` VALUES ('704', '7', 'ingresodetallado', 'Ingreso detallado');
INSERT INTO `opcion` VALUES ('705', '7', 'comisionxvendedor', 'Comision por vendedor');
INSERT INTO `opcion` VALUES ('706', '7', 'ventaxcomprobante', 'Ventas x comprobantes');
INSERT INTO `opcion` VALUES ('707', '7', 'productovendido', 'Productos + vendidos');
INSERT INTO `opcion` VALUES ('708', '7', 'ventaSucursal', 'Ventas x sucursal');
INSERT INTO `opcion` VALUES ('709', '7', 'ventaEmpleado', 'Ventas x empleado');
INSERT INTO `opcion` VALUES ('710', '7', 'stockventas', 'Stock y ventas');
INSERT INTO `opcion` VALUES ('711', '7', 'pagoproveedores', 'Pago a proveedores');
INSERT INTO `opcion` VALUES ('712', '7', 'margenutilidad', 'Margen de utilidad');
INSERT INTO `opcion` VALUES ('713', '7', 'hojaColecta', 'Reporte hoja de colecta');
INSERT INTO `opcion` VALUES ('714', '7', 'recargaDia', 'Recargas del día');
INSERT INTO `opcion` VALUES ('715', '7', 'recargaCobranza', 'Cobranza del día');
INSERT INTO `opcion` VALUES ('716', '7', 'recargaCuentasC', 'Cuentas por cobrar');
INSERT INTO `opcion` VALUES ('801', '8', 'opcionesgenerales', 'Parametros de configuracion');
INSERT INTO `opcion` VALUES ('802', '8', 'locales', 'Locales');
INSERT INTO `opcion` VALUES ('803', '8', 'usuariospadre', 'Usuarios');
INSERT INTO `opcion` VALUES ('804', '8', 'usuarios', 'Registrar Usuarios');
INSERT INTO `opcion` VALUES ('805', '8', 'gruposusuarios', 'Perfiles');
INSERT INTO `opcion` VALUES ('806', '8', 'region', 'Ubigeo');
INSERT INTO `opcion` VALUES ('807', '8', 'pais', 'Paises');
INSERT INTO `opcion` VALUES ('808', '8', 'estado', 'Departamento');
INSERT INTO `opcion` VALUES ('809', '8', 'ciudad', 'Provincia');
INSERT INTO `opcion` VALUES ('810', '8', 'distrito', 'Distrito');
INSERT INTO `opcion` VALUES ('812', '8', 'unidadesmedida', 'Unidad de medida');
INSERT INTO `opcion` VALUES ('813', '8', 'impuestos', 'Impuestos');
INSERT INTO `opcion` VALUES ('901', '9', 'reporteVentas', 'Reporte semanal de salidas');
INSERT INTO `opcion` VALUES ('902', '9', 'reporteCompras', 'Reporte semanal de compras');

CREATE TABLE `recarga` (
  `rec_cod` int(11) NOT NULL AUTO_INCREMENT,
  `id_venta` int(11) DEFAULT NULL,
  `rec_trans` varchar(255) DEFAULT NULL,
  `rec_nro` varchar(9) DEFAULT NULL,
  `rec_ope`  int DEFAULT NULL COMMENT 'Se enlaza con tabla diccionario_termino en el grupo 3',
  `rec_pob`  int DEFAULT NULL COMMENT 'Codigo de centro de poblado',
  PRIMARY KEY (`rec_cod`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `diccionario_termino`
ADD COLUMN `grupo`  int NULL AFTER `activo`;

update diccionario_termino set grupo=1 where id in(1,2);
update diccionario_termino set grupo=2 where id in(3);

INSERT INTO `diccionario_termino` VALUES ('4', 'operador', 'BITEL', '9', '1', '3');
