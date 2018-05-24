ALTER TABLE `venta`
ADD COLUMN `facturacion` TINYINT NULL DEFAULT 0 AFTER `numero`;

ALTER TABLE `venta`
ADD COLUMN `facturacion_nota` VARCHAR(255) NULL AFTER `facturacion`;


-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 10.1.1.3    Database: ip_newlevel
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `facturacion`
--

DROP TABLE IF EXISTS `facturacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturacion` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `local_id` bigint(20) NOT NULL,
  `fecha` datetime NOT NULL,
  `documento_tipo` varchar(45) NOT NULL,
  `documento_numero` varchar(45) NOT NULL,
  `documento_mod_tipo` varchar(45) DEFAULT NULL,
  `documento_mod_numero` varchar(45) DEFAULT NULL,
  `documento_mod_motivo` varchar(45) DEFAULT NULL,
  `cliente_tipo` varchar(45) DEFAULT NULL,
  `cliente_identificacion` varchar(45) DEFAULT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `cliente_direccion` varchar(45) DEFAULT NULL,
  `subtotal` decimal(18,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  `estado` tinyint(4) NOT NULL DEFAULT '0',
  `nota` varchar(150) DEFAULT NULL,
  `hash_cpe` varchar(500) DEFAULT NULL,
  `hash_cdr` varchar(500) DEFAULT NULL,
  `ref_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturacion_detalle`
--

DROP TABLE IF EXISTS `facturacion_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturacion_detalle` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `facturacion_id` bigint(20) NOT NULL,
  `producto_codigo` varchar(45) NOT NULL,
  `producto_descripcion` varchar(150) NOT NULL,
  `um` varchar(45) NOT NULL,
  `cantidad` decimal(18,3) NOT NULL,
  `precio` decimal(18,2) NOT NULL,
  `impuesto` decimal(18,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturacion_emisor`
--

DROP TABLE IF EXISTS `facturacion_emisor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturacion_emisor` (
  `ruc` varchar(15) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `nombre_comercial` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `provincia_id` int(11) DEFAULT NULL,
  `distrito_id` int(11) DEFAULT NULL,
  `ubigeo` varchar(45) DEFAULT NULL,
  `moneda` varchar(45) NOT NULL,
  `user_sol` varchar(45) NOT NULL,
  `pass_sol` varchar(45) NOT NULL,
  `pass_sign` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ruc`),
  UNIQUE KEY `ruc_UNIQUE` (`ruc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-23 20:20:18

