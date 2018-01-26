-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 22-10-2015 a las 11:09:07
-- Versión del servidor: 5.5.34
-- Versión de PHP: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `bdinventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajustedetalle`
--

CREATE TABLE IF NOT EXISTS `ajustedetalle` (
  `id_ajustedetalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ajusteinventario` bigint(20) DEFAULT NULL,
  `cantidad_detalle` float DEFAULT NULL,
  `fraccion_detalle` float DEFAULT NULL,
  `old_cantidad` float DEFAULT NULL,
  `old_fraccion` float DEFAULT NULL,
  `id_inventario` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_ajustedetalle`),
  KEY `fk_1_idx` (`id_ajusteinventario`),
  KEY `fk_ajustedetalle_2_idx` (`id_inventario`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=896 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajusteinventario`
--

CREATE TABLE IF NOT EXISTS `ajusteinventario` (
  `id_ajusteinventario` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  `local_id` bigint(20) DEFAULT NULL,
  `usuario_encargado` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_ajusteinventario`),
  KEY `ajusteinventario_fk_1_idx` (`local_id`),
  KEY `usuario_encargado` (`usuario_encargado`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=180 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE IF NOT EXISTS `ciudades` (
  `ciudad_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ciudad_nombre` varchar(45) DEFAULT NULL,
  `estado_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ciudad_id`),
  KEY `ciudad_pk_1_idx` (`estado_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `ciudad_id` bigint(20) DEFAULT NULL,
  `descuento` float DEFAULT NULL,
  `direccion` text,
  `email` varchar(250) DEFAULT NULL,
  `exento_impuesto` tinyint(1) DEFAULT '0',
  `grupo_id` bigint(20) DEFAULT NULL,
  `limite_credito` decimal(10,0) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `identificacion` varchar(45) DEFAULT NULL,
  `pagina_web` varchar(255) DEFAULT NULL,
  `telefono1` varchar(45) DEFAULT NULL,
  `telefono2` varchar(45) DEFAULT NULL,
  `nota` text,
  `cliente_status` tinyint(1) DEFAULT '1',
  `categoria_precio` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  KEY `cliente_fk_1_idx` (`grupo_id`),
  KEY `cliente_fk_2_idx` (`ciudad_id`),
  KEY `categoria_precio` (`categoria_precio`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `columnas`
--

CREATE TABLE IF NOT EXISTS `columnas` (
  `nombre_columna` varchar(255) NOT NULL,
  `nombre_join` varchar(45) NOT NULL,
  `nombre_mostrar` varchar(255) NOT NULL,
  `tabla` varchar(45) DEFAULT NULL,
  `mostrar` tinyint(1) DEFAULT '1',
  `activo` tinyint(1) DEFAULT '1',
  `id_columna` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_columna`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condiciones_pago`
--

CREATE TABLE IF NOT EXISTS `condiciones_pago` (
  `id_condiciones` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_condiciones` varchar(255) NOT NULL,
  `status_condiciones` tinyint(1) DEFAULT '1',
  `dias` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_condiciones`,`nombre_condiciones`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuraciones`
--

CREATE TABLE IF NOT EXISTS `configuraciones` (
  `config_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(255) DEFAULT NULL,
  `config_value` varchar(255) DEFAULT NULL,
  KEY `config_id` (`config_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contado`
--

CREATE TABLE IF NOT EXISTS `contado` (
  `id_venta` bigint(20) NOT NULL,
  `status` varchar(13) NOT NULL,
  `montopagado` decimal(18,2) NOT NULL,
  PRIMARY KEY (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credito`
--

CREATE TABLE IF NOT EXISTS `credito` (
  `id_venta` bigint(20) NOT NULL,
  `int_credito_nrocuota` int(11) NOT NULL,
  `dec_credito_montocuota` decimal(18,2) NOT NULL,
  `var_credito_estado` varchar(20) NOT NULL,
  `dec_credito_montodebito` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cronogramapago`
--

CREATE TABLE IF NOT EXISTS `cronogramapago` (
  `nCPagoCodigo` bigint(20) NOT NULL AUTO_INCREMENT,
  `int_cronpago_nrocuota` int(11) NOT NULL,
  `dat_cronpago_fecinicio` date NOT NULL,
  `dat_cronpago_fecpago` date NOT NULL,
  `dec_cronpago_pagocuota` decimal(18,2) NOT NULL,
  `dec_cronpago_pagorecibido` decimal(18,2) DEFAULT '0.00',
  `nVenCodigo` bigint(20) NOT NULL,
  PRIMARY KEY (`nCPagoCodigo`),
  KEY `cronogramapago_venta_idx` (`nVenCodigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleingreso`
--

CREATE TABLE IF NOT EXISTS `detalleingreso` (
  `id_detalle_ingreso` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ingreso` bigint(20) DEFAULT NULL,
  `id_producto` bigint(20) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio` decimal(18,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `unidad_medida` bigint(20) DEFAULT NULL,
  `total_detalle` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`id_detalle_ingreso`),
  KEY `DetalleOrdenCompraFKOrdenCompra_idx` (`id_ingreso`),
  KEY `fk_detalle_ingreso2_idx` (`id_producto`),
  KEY `fk_detalle_ingreso3_idx` (`unidad_medida`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) DEFAULT NULL,
  `id_producto` bigint(20) DEFAULT NULL,
  `precio` decimal(18,2) DEFAULT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `unidad_medida` bigint(20) DEFAULT NULL,
  `detalle_importe` decimal(18,2) DEFAULT NULL,
  `detalle_costo_promedio` decimal(18,2) DEFAULT '0.00',
  `detalle_utilidad` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`id_detalle`),
  KEY `R_9` (`id_venta`),
  KEY `transaccion_ibfk_2_idx` (`precio`),
  KEY `transaccion_ibfk_3_idx` (`unidad_medida`),
  KEY `transaccion_ibfk_4_idx` (`id_producto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=744 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_venta`
--

CREATE TABLE IF NOT EXISTS `documento_venta` (
  `id_tipo_documento` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_tipo_documento` varchar(45) NOT NULL,
  `documento_Serie` varchar(20) NOT NULL,
  `documento_Numero` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_documento`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1740 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE IF NOT EXISTS `estados` (
  `estados_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `estados_nombre` varchar(45) DEFAULT NULL,
  `pais_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`estados_id`),
  KEY `estado_fk_1_idx` (`pais_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familia`
--

CREATE TABLE IF NOT EXISTS `familia` (
  `id_familia` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_familia` varchar(50) DEFAULT NULL,
  `estatus_familia` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_familia`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE IF NOT EXISTS `gastos` (
  `id_gastos` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT NULL,
  `descripcion` text,
  `total` decimal(10,0) DEFAULT NULL,
  `tipo_gasto` bigint(20) DEFAULT NULL,
  `local_id` bigint(20) DEFAULT NULL,
  `status_gastos` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_gastos`),
  KEY `tipos_gasto_fk1_idx` (`tipo_gasto`),
  KEY `tipos_gasto_fk2_idx` (`local_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE IF NOT EXISTS `grupos` (
  `id_grupo` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_grupo` varchar(45) DEFAULT NULL,
  `estatus_grupo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_cliente`
--

CREATE TABLE IF NOT EXISTS `grupos_cliente` (
  `id_grupos_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_grupos_cliente` varchar(255) DEFAULT NULL,
  `status_grupos_cliente` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_grupos_cliente`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_usuarios`
--

CREATE TABLE IF NOT EXISTS `grupos_usuarios` (
  `id_grupos_usuarios` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_grupos_usuarios` varchar(45) DEFAULT NULL,
  `status_grupos_usuarios` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_grupos_usuarios`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_cronograma`
--

CREATE TABLE IF NOT EXISTS `historial_cronograma` (
  `historialcrono_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cronogramapago_id` bigint(20) DEFAULT NULL,
  `historialcrono_fecha` datetime DEFAULT NULL,
  `historialcrono_monto` float(20,2) DEFAULT NULL,
  `historialcrono_tipopago` bigint(20) DEFAULT NULL,
  `monto_restante` float(20,2) DEFAULT NULL,
  PRIMARY KEY (`historialcrono_id`),
  KEY `cronogramapago_id` (`cronogramapago_id`),
  KEY `historialcrono_tipopago` (`historialcrono_tipopago`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuestos`
--

CREATE TABLE IF NOT EXISTS `impuestos` (
  `id_impuesto` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_impuesto` varchar(45) DEFAULT NULL,
  `porcentaje_impuesto` float DEFAULT NULL,
  `estatus_impuesto` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_impuesto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingreso`
--

CREATE TABLE IF NOT EXISTS `ingreso` (
  `id_ingreso` bigint(20) NOT NULL AUTO_INCREMENT,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `int_Proveedor_id` bigint(20) DEFAULT NULL,
  `nUsuCodigo` bigint(20) DEFAULT NULL,
  `local_id` bigint(20) DEFAULT NULL,
  `tipo_documento` varchar(45) DEFAULT NULL,
  `documento_serie` char(8) DEFAULT NULL,
  `documento_numero` char(20) DEFAULT NULL,
  `ingreso_status` varchar(45) DEFAULT NULL,
  `fecha_emision` timestamp NULL DEFAULT NULL,
  `tipo_ingreso` varchar(45) DEFAULT NULL,
  `impuesto_ingreso` double DEFAULT NULL,
  `sub_total_ingreso` double DEFAULT NULL,
  `total_ingreso` double DEFAULT NULL,
  `pago` varchar(45) DEFAULT NULL,
  `ingreso_observacion` text,
  PRIMARY KEY (`id_ingreso`),
  KEY `fk_OrdenCompra_personal1_idx` (`nUsuCodigo`),
  KEY `fk_OrdenCompra_proveedor_idx` (`int_Proveedor_id`),
  KEY `fk_ingreso_local_idx` (`local_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE IF NOT EXISTS `inventario` (
  `id_inventario` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_producto` bigint(20) DEFAULT NULL,
  `cantidad` float DEFAULT NULL,
  `fraccion` float DEFAULT NULL,
  `id_local` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_inventario`),
  KEY `fk_inventario_1_idx` (`id_producto`),
  KEY `fk_inventario_2_idx` (`id_local`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=849 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas`
--

CREATE TABLE IF NOT EXISTS `lineas` (
  `id_linea` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_linea` varchar(50) DEFAULT NULL,
  `estatus_linea` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_linea`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=148 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `local`
--

CREATE TABLE IF NOT EXISTS `local` (
  `int_local_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `local_nombre` varchar(45) NOT NULL,
  `local_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`int_local_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE IF NOT EXISTS `marcas` (
  `id_marca` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_marca` varchar(45) DEFAULT NULL,
  `estatus_marca` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_marca`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=184 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE IF NOT EXISTS `metodos_pago` (
  `id_metodo` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_metodo` varchar(255) DEFAULT NULL,
  `status_metodo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_metodo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcion`
--

CREATE TABLE IF NOT EXISTS `opcion` (
  `nOpcion` bigint(20) NOT NULL AUTO_INCREMENT,
  `nOpcionClase` bigint(20) DEFAULT NULL,
  `cOpcionDescripcion` varchar(100) DEFAULT NULL,
  `cOpcionNombre` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`nOpcion`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcion_grupo`
--

CREATE TABLE IF NOT EXISTS `opcion_grupo` (
  `grupo` bigint(20) NOT NULL,
  `Opcion` bigint(20) NOT NULL,
  `var_opcion_usuario_estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`grupo`,`Opcion`),
  KEY `nopcionUsuarioFKUsuario_idx` (`grupo`),
  KEY `nopcionUsuarioFKOpcion_idx` (`Opcion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_ingreso`
--

CREATE TABLE IF NOT EXISTS `pagos_ingreso` (
  `pagoingreso_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pagoingreso_ingreso_id` bigint(20) DEFAULT NULL,
  `pagoingreso_fecha` datetime DEFAULT NULL,
  `pagoingreso_monto` float(22,2) DEFAULT NULL,
  `pagoingreso_restante` float(22,2) DEFAULT NULL,
  PRIMARY KEY (`pagoingreso_id`),
  KEY `pagoingreso_ingreso_id` (`pagoingreso_ingreso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pais`
--

CREATE TABLE IF NOT EXISTS `pais` (
  `id_pais` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_pais` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pais`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios`
--

CREATE TABLE IF NOT EXISTS `precios` (
  `id_precio` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_precio` varchar(45) DEFAULT NULL,
  `descuento_precio` double DEFAULT NULL,
  `mostrar_precio` tinyint(1) DEFAULT '0',
  `estatus_precio` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_precio`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE IF NOT EXISTS `producto` (
  `producto_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `producto_codigo_barra` varchar(255) DEFAULT NULL,
  `producto_nombre` varchar(100) DEFAULT NULL,
  `producto_descripcion` varchar(500) DEFAULT NULL,
  `producto_marca` bigint(20) DEFAULT NULL,
  `producto_linea` bigint(20) DEFAULT NULL,
  `producto_familia` bigint(20) DEFAULT NULL,
  `produto_grupo` bigint(20) DEFAULT NULL,
  `producto_proveedor` bigint(20) DEFAULT NULL,
  `producto_stockminimo` decimal(18,2) DEFAULT NULL,
  `producto_impuesto` bigint(20) DEFAULT NULL,
  `producto_estatus` tinyint(1) DEFAULT '1',
  `producto_largo` float DEFAULT NULL,
  `producto_ancho` float DEFAULT NULL,
  `producto_alto` float DEFAULT NULL,
  `producto_peso` float DEFAULT NULL,
  `producto_nota` text,
  `producto_cualidad` varchar(255) DEFAULT NULL,
  `producto_estado` tinyint(1) DEFAULT '1',
  `producto_costo_unitario` float(20,2) DEFAULT NULL,
  PRIMARY KEY (`producto_id`),
  KEY `R_19` (`producto_linea`),
  KEY `producto_fk_1_idx` (`producto_marca`),
  KEY `producto_fk_3_idx` (`producto_familia`),
  KEY `producto_fk_4_idx` (`produto_grupo`),
  KEY `producto_fk_5_idx` (`producto_proveedor`),
  KEY `producto_fk_6_idx` (`producto_impuesto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=524 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proforma`
--

CREATE TABLE IF NOT EXISTS `proforma` (
  `nProfCOdigo` bigint(20) NOT NULL AUTO_INCREMENT,
  `nProvCodigo` bigint(20) NOT NULL,
  `nProCodigo` bigint(20) NOT NULL,
  `cProfSerie` char(6) NOT NULL,
  `nCantidad` int(11) NOT NULL,
  PRIMARY KEY (`nProfCOdigo`),
  KEY `ProveedorFKProforma_idx` (`nProvCodigo`),
  KEY `ProductoFKProforma_idx` (`nProCodigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` bigint(20) NOT NULL AUTO_INCREMENT,
  `proveedor_nombre` varchar(50) NOT NULL,
  `proveedor_direccion1` text NOT NULL,
  `proveedor_nrofax` varchar(20) DEFAULT '',
  `proveedor_paginaweb` varchar(50) DEFAULT '',
  `proveedor_email` varchar(50) DEFAULT '',
  `proveedor_telefono1` varchar(12) NOT NULL,
  `proveedor_telefono2` varchar(12) NOT NULL,
  `proveedor_status` tinyint(1) NOT NULL DEFAULT '1',
  `proveedor_observacion` varchar(500) DEFAULT '',
  `proveedor_direccion2` text,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_gasto`
--

CREATE TABLE IF NOT EXISTS `tipos_gasto` (
  `id_tipos_gasto` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_tipos_gasto` varchar(255) DEFAULT NULL,
  `status_tipos_gasto` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_tipos_gasto`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE IF NOT EXISTS `unidades` (
  `id_unidad` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_unidad` varchar(45) DEFAULT NULL,
  `estatus_unidad` tinyint(1) DEFAULT '1',
  `abreviatura` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_has_precio`
--

CREATE TABLE IF NOT EXISTS `unidades_has_precio` (
  `id_precio` bigint(20) NOT NULL,
  `id_unidad` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `precio` double DEFAULT NULL,
  PRIMARY KEY (`id_precio`,`id_unidad`,`id_producto`),
  KEY `fk_precios_has_unidades_has_producto_unidades_has_producto1_idx` (`id_unidad`),
  KEY `fk_precios_has_unidades_has_producto_precios1_idx` (`id_precio`),
  KEY `fk_precios_has_unidades_has_producto_unidades_has_producto3_idx` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_has_producto`
--

CREATE TABLE IF NOT EXISTS `unidades_has_producto` (
  `id_unidad` bigint(20) NOT NULL,
  `producto_id` bigint(20) NOT NULL,
  `unidades` float DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_unidad`,`producto_id`),
  KEY `fk_unidades_has_producto_producto1_idx` (`producto_id`),
  KEY `fk_unidades_has_producto_unidades1_idx` (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `nUsuCodigo` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(18) NOT NULL,
  `var_usuario_clave` varchar(50) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `nombre` varchar(255) DEFAULT NULL,
  `grupo` bigint(20) DEFAULT NULL,
  `id_local` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `identificacion` int(45) DEFAULT NULL,
  PRIMARY KEY (`nUsuCodigo`),
  KEY `grupo` (`grupo`),
  KEY `id_local` (`id_local`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE IF NOT EXISTS `venta` (
  `venta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cliente` bigint(20) NOT NULL,
  `id_vendedor` bigint(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `numero_documento` bigint(20) DEFAULT NULL,
  `venta_status` varchar(45) DEFAULT NULL,
  `condicion_pago` bigint(20) DEFAULT NULL,
  `local_id` bigint(20) DEFAULT NULL,
  `subtotal` decimal(18,2) DEFAULT NULL,
  `total_impuesto` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `pagado` decimal(18,2) DEFAULT '0.00',
  `vuelto` decimal(18,2) DEFAULT '0.00',
  PRIMARY KEY (`venta_id`),
  KEY `venta_tipodocumento_idx` (`numero_documento`),
  KEY `ventafklocal_idx` (`local_id`),
  KEY `ventafkpersonal_idx` (`id_vendedor`),
  KEY `ventacondicionpagofk_idx` (`condicion_pago`),
  KEY `ventaclientefk_idx` (`id_cliente`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=219 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_anular`
--

CREATE TABLE IF NOT EXISTS `venta_anular` (
  `nVenAnularCodigo` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) NOT NULL,
  `var_venanular_descripcion` text NOT NULL,
  `nUsuCodigo` bigint(20) NOT NULL,
  `dat_fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nVenAnularCodigo`),
  KEY `VentaFKVenta_Anular_idx` (`id_venta`),
  KEY `UsuarioFKVenta_Anular_idx` (`nUsuCodigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_consulta_estadocuenta_venta`
--
CREATE TABLE IF NOT EXISTS `v_consulta_estadocuenta_venta` (
`Venta_Id` bigint(20)
,`Cliente_Id` bigint(20)
,`Cliente` varchar(255)
,`Personal` varchar(255)
,`FechaReg` date
,`MontoTotal` decimal(18,2)
,`MontoCancelado` decimal(40,2)
,`SaldoPendiente` decimal(41,2)
,`NroVenta` varchar(41)
,`TipoDocumento` varchar(45)
,`Estado` varchar(20)
,`FormaPago` varchar(255)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_consulta_pagospendientes_venta`
--
CREATE TABLE IF NOT EXISTS `v_consulta_pagospendientes_venta` (
`Venta_id` bigint(20)
,`Cliente_Id` bigint(20)
,`Cliente` varchar(255)
,`Personal` varchar(255)
,`FechaReg` date
,`FechaVenc` varchar(10)
,`MontoTotal` decimal(18,2)
,`MontoCancelado` decimal(40,2)
,`SaldoPendiente` decimal(41,2)
,`NroVenta` varchar(41)
,`TipoDocumento` varchar(45)
,`Estado` varchar(20)
,`FormaPago` varchar(255)
);
-- --------------------------------------------------------

--
-- Estructura para la vista `v_consulta_estadocuenta_venta`
--
DROP TABLE IF EXISTS `v_consulta_estadocuenta_venta`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_consulta_estadocuenta_venta` AS (select `v`.`venta_id` AS `Venta_Id`,`c`.`id_cliente` AS `Cliente_Id`,`c`.`razon_social` AS `Cliente`,`p`.`nombre` AS `Personal`,cast(`v`.`fecha` as date) AS `FechaReg`,`v`.`total` AS `MontoTotal`,ifnull((select sum(`cp`.`dec_cronpago_pagorecibido`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)),0.00) AS `MontoCancelado`,(`v`.`total` - ifnull((select sum(`cp`.`dec_cronpago_pagorecibido`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)),0.00)) AS `SaldoPendiente`,concat(`d`.`documento_Serie`,'-',`d`.`documento_Numero`) AS `NroVenta`,`d`.`nombre_tipo_documento` AS `TipoDocumento`,ifnull((select `cd`.`var_credito_estado` from `credito` `cd` where (`cd`.`id_venta` = `v`.`venta_id`)),'') AS `Estado`,`cond`.`nombre_condiciones` AS `FormaPago` from ((((`venta` `v` join `cliente` `c` on((`c`.`id_cliente` = `v`.`id_cliente`))) join `documento_venta` `d` on((`d`.`id_tipo_documento` = `v`.`numero_documento`))) join `usuario` `p` on((`p`.`nUsuCodigo` = `v`.`id_vendedor`))) join `condiciones_pago` `cond` on((`cond`.`id_condiciones` = `v`.`condicion_pago`))) where (((select `condiciones_pago`.`dias` from `condiciones_pago` where (`v`.`condicion_pago` = `condiciones_pago`.`id_condiciones`)) > 0) and ((select sum(`cp`.`dec_cronpago_pagorecibido`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)) < `v`.`total`) and (`v`.`venta_status` = 'COMPLETADO')));

-- --------------------------------------------------------

--
-- Estructura para la vista `v_consulta_pagospendientes_venta`
--
DROP TABLE IF EXISTS `v_consulta_pagospendientes_venta`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_consulta_pagospendientes_venta` AS (select `v`.`venta_id` AS `Venta_id`,`c`.`id_cliente` AS `Cliente_Id`,`c`.`razon_social` AS `Cliente`,`p`.`nombre` AS `Personal`,cast(`v`.`fecha` as date) AS `FechaReg`,ifnull((select max(`cp`.`dat_cronpago_fecpago`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)),'') AS `FechaVenc`,`v`.`total` AS `MontoTotal`,ifnull((select sum(`cp`.`dec_cronpago_pagorecibido`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)),0.00) AS `MontoCancelado`,(`v`.`total` - ifnull((select sum(`cp`.`dec_cronpago_pagorecibido`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)),0.00)) AS `SaldoPendiente`,concat(`d`.`documento_Serie`,'-',`d`.`documento_Numero`) AS `NroVenta`,`d`.`nombre_tipo_documento` AS `TipoDocumento`,ifnull((select `cd`.`var_credito_estado` from `credito` `cd` where (`cd`.`id_venta` = `v`.`venta_id`)),'') AS `Estado`,`cond`.`nombre_condiciones` AS `FormaPago` from ((((`venta` `v` join `cliente` `c` on((`c`.`id_cliente` = `v`.`id_cliente`))) join `documento_venta` `d` on((`d`.`id_tipo_documento` = `v`.`numero_documento`))) join `usuario` `p` on((`p`.`nUsuCodigo` = `v`.`id_vendedor`))) join `condiciones_pago` `cond` on((`cond`.`id_condiciones` = `v`.`condicion_pago`))) where (((select `condiciones_pago`.`dias` from `condiciones_pago` where (`v`.`condicion_pago` = `condiciones_pago`.`id_condiciones`)) > 0) and ((select sum(`cp`.`dec_cronpago_pagorecibido`) from `cronogramapago` `cp` where (`cp`.`nVenCodigo` = `v`.`venta_id`)) < `v`.`total`) and (`v`.`venta_status` = 'COMPLETADO')));

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ajustedetalle`
--
ALTER TABLE `ajustedetalle`
  ADD CONSTRAINT `fk_ajustedetalle_1` FOREIGN KEY (`id_ajusteinventario`) REFERENCES `ajusteinventario` (`id_ajusteinventario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ajustedetalle_2` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id_inventario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `ajusteinventario`
--
ALTER TABLE `ajusteinventario`
  ADD CONSTRAINT `ajusteinventario_fk_1` FOREIGN KEY (`local_id`) REFERENCES `local` (`int_local_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ajusteinventario_ibfk_1` FOREIGN KEY (`usuario_encargado`) REFERENCES `usuario` (`nUsuCodigo`);

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `ciudad_pk_1` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`estados_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_fk_1` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_cliente` (`id_grupos_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cliente_fk_2` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`ciudad_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`categoria_precio`) REFERENCES `precios` (`id_precio`);

--
-- Filtros para la tabla `contado`
--
ALTER TABLE `contado`
  ADD CONSTRAINT `contado_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`venta_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `credito`
--
ALTER TABLE `credito`
  ADD CONSTRAINT `credito_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`venta_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cronogramapago`
--
ALTER TABLE `cronogramapago`
  ADD CONSTRAINT `cronogramapago_ibfk_1` FOREIGN KEY (`nVenCodigo`) REFERENCES `venta` (`venta_id`);

--
-- Filtros para la tabla `detalleingreso`
--
ALTER TABLE `detalleingreso`
  ADD CONSTRAINT `DetalleOrdenCompraFKOrdenCompra` FOREIGN KEY (`id_ingreso`) REFERENCES `ingreso` (`id_ingreso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_detalle_ingreso2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`producto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_detalle_ingreso3` FOREIGN KEY (`unidad_medida`) REFERENCES `unidades` (`id_unidad`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `transaccion_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`venta_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `transaccion_ibfk_3` FOREIGN KEY (`unidad_medida`) REFERENCES `unidades` (`id_unidad`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `transaccion_ibfk_4` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`producto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `estados`
--
ALTER TABLE `estados`
  ADD CONSTRAINT `estado_fk_1` FOREIGN KEY (`pais_id`) REFERENCES `pais` (`id_pais`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `tipos_gasto_fk1` FOREIGN KEY (`tipo_gasto`) REFERENCES `tipos_gasto` (`id_tipos_gasto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tipos_gasto_fk2` FOREIGN KEY (`local_id`) REFERENCES `local` (`int_local_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `historial_cronograma`
--
ALTER TABLE `historial_cronograma`
  ADD CONSTRAINT `historial_cronograma_ibfk_1` FOREIGN KEY (`cronogramapago_id`) REFERENCES `cronogramapago` (`nCPagoCodigo`),
  ADD CONSTRAINT `historial_cronograma_ibfk_2` FOREIGN KEY (`historialcrono_tipopago`) REFERENCES `metodos_pago` (`id_metodo`);

--
-- Filtros para la tabla `ingreso`
--
ALTER TABLE `ingreso`
  ADD CONSTRAINT `fk_ingreso_local` FOREIGN KEY (`local_id`) REFERENCES `local` (`int_local_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_OrdenCompra_proveedor` FOREIGN KEY (`int_Proveedor_id`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ingreso_ibfk_1` FOREIGN KEY (`nUsuCodigo`) REFERENCES `usuario` (`nUsuCodigo`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `fk_inventario_1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`producto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_inventario_2` FOREIGN KEY (`id_local`) REFERENCES `local` (`int_local_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `opcion_grupo`
--
ALTER TABLE `opcion_grupo`
  ADD CONSTRAINT `nopcionUsuarioFKOpcion` FOREIGN KEY (`Opcion`) REFERENCES `opcion` (`nOpcion`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `opcion_grupo_ibfk_1` FOREIGN KEY (`grupo`) REFERENCES `grupos_usuarios` (`id_grupos_usuarios`);

--
-- Filtros para la tabla `pagos_ingreso`
--
ALTER TABLE `pagos_ingreso`
  ADD CONSTRAINT `pagos_ingreso_ibfk_1` FOREIGN KEY (`pagoingreso_ingreso_id`) REFERENCES `ingreso` (`id_ingreso`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_fk_1` FOREIGN KEY (`producto_marca`) REFERENCES `marcas` (`id_marca`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `producto_fk_2` FOREIGN KEY (`producto_linea`) REFERENCES `lineas` (`id_linea`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `producto_fk_3` FOREIGN KEY (`producto_familia`) REFERENCES `familia` (`id_familia`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `producto_fk_4` FOREIGN KEY (`produto_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `producto_fk_5` FOREIGN KEY (`producto_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `producto_fk_6` FOREIGN KEY (`producto_impuesto`) REFERENCES `impuestos` (`id_impuesto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `proforma`
--
ALTER TABLE `proforma`
  ADD CONSTRAINT `ProductoFKProforma` FOREIGN KEY (`nProCodigo`) REFERENCES `producto` (`producto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ProveedorFKProforma` FOREIGN KEY (`nProvCodigo`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `unidades_has_precio`
--
ALTER TABLE `unidades_has_precio`
  ADD CONSTRAINT `fk_precios_has_unidades_has_producto_precios1` FOREIGN KEY (`id_precio`) REFERENCES `precios` (`id_precio`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_precios_has_unidades_has_producto_unidades_has_producto2` FOREIGN KEY (`id_unidad`) REFERENCES `unidades` (`id_unidad`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_precios_has_unidades_has_producto_unidades_has_producto3` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`producto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `unidades_has_producto`
--
ALTER TABLE `unidades_has_producto`
  ADD CONSTRAINT `fk_unidades_has_producto_producto1` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`producto_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `unidades_has_producto_ibfk_1` FOREIGN KEY (`id_unidad`) REFERENCES `unidades` (`id_unidad`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`grupo`) REFERENCES `grupos_usuarios` (`id_grupos_usuarios`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_local`) REFERENCES `local` (`int_local_id`);

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `ventaclientefk` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ventafklocal` FOREIGN KEY (`local_id`) REFERENCES `local` (`int_local_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `ventafkpersonal` FOREIGN KEY (`id_vendedor`) REFERENCES `usuario` (`nUsuCodigo`),
  ADD CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`numero_documento`) REFERENCES `documento_venta` (`id_tipo_documento`),
  ADD CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`condicion_pago`) REFERENCES `condiciones_pago` (`id_condiciones`);

--
-- Filtros para la tabla `venta_anular`
--
ALTER TABLE `venta_anular`
  ADD CONSTRAINT `UsuarioFKVenta_Anular` FOREIGN KEY (`nUsuCodigo`) REFERENCES `usuario` (`nUsuCodigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `VentaFKVenta_Anular` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`venta_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
