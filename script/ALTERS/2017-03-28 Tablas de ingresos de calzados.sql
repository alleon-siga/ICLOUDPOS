CREATE DATABASE  IF NOT EXISTS `icloudpos_dev` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `icloudpos_dev`;
-- MySQL dump 10.13  Distrib 5.5.54, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: icloudpos_dev
-- ------------------------------------------------------
-- Server version	5.5.54-0ubuntu0.12.04.1

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
-- Table structure for table `pl_serie`
--

DROP TABLE IF EXISTS `pl_serie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_serie` (
  `serie` varchar(45) NOT NULL,
  `rango` varchar(255) NOT NULL,
  `estado` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pl_serie`
--

LOCK TABLES `pl_serie` WRITE;
/*!40000 ALTER TABLE `pl_serie` DISABLE KEYS */;
INSERT INTO `pl_serie` VALUES ('A','16|17|18|19|20',1),('B','26|28|30|32|34',1),('C','X|XL|XXL',1),('D','M|S|L',1);
/*!40000 ALTER TABLE `pl_serie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pl_producto`
--

DROP TABLE IF EXISTS `pl_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pl_producto`
--

LOCK TABLES `pl_producto` WRITE;
/*!40000 ALTER TABLE `pl_producto` DISABLE KEYS */;
INSERT INTO `pl_producto` VALUES (1,'PIBE','PLANTILLA PIBE'),(2,'TENIS','tenis deportivos');
/*!40000 ALTER TABLE `pl_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pl_tipo`
--

DROP TABLE IF EXISTS `pl_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(45) NOT NULL,
  `estado` tinyint(4) DEFAULT '1',
  `orden` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pl_tipo`
--

LOCK TABLES `pl_tipo` WRITE;
/*!40000 ALTER TABLE `pl_tipo` DISABLE KEYS */;
INSERT INTO `pl_tipo` VALUES (1,'MARCA',1,0),(2,'GRUPO',1,0),(3,'FAMILIA',1,0),(4,'LINEA',1,0),(5,'COLOR',1,0),(6,'MATERIAL',1,0),(7,'PLANTA',1,0),(8,'PROVEEDOR',1,0);
/*!40000 ALTER TABLE `pl_tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pl_producto_propiedad`
--

DROP TABLE IF EXISTS `pl_producto_propiedad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_producto_propiedad` (
  `pl_producto_id` int(11) NOT NULL,
  `pl_propiedad_id` int(11) NOT NULL,
  `estado` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`pl_producto_id`,`pl_propiedad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pl_producto_propiedad`
--

LOCK TABLES `pl_producto_propiedad` WRITE;
/*!40000 ALTER TABLE `pl_producto_propiedad` DISABLE KEYS */;
INSERT INTO `pl_producto_propiedad` VALUES (1,2,1),(1,4,1),(1,5,1),(1,7,1),(1,9,1),(1,12,1),(1,13,1),(1,14,1),(2,3,1),(2,4,1),(2,5,1),(2,7,1),(2,10,1),(2,12,1),(2,13,1),(2,14,1);
/*!40000 ALTER TABLE `pl_producto_propiedad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pl_propiedad`
--

DROP TABLE IF EXISTS `pl_propiedad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pl_propiedad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pl_tipo_id` int(11) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `estado` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pl_propiedad`
--

LOCK TABLES `pl_propiedad` WRITE;
/*!40000 ALTER TABLE `pl_propiedad` DISABLE KEYS */;
INSERT INTO `pl_propiedad` VALUES (1,1,'ADIDAS',1),(2,1,'NIKE',1),(3,1,'CONVERSE',1),(4,2,'CALZADOS',1),(5,3,'HOMBRES',1),(6,3,'MUJERES',1),(7,4,'DEPORTIVOS',1),(8,4,'SOCIAL',1),(9,5,'NEGROS',1),(10,5,'BLANCOS',1),(11,5,'CARMELITA',1),(12,6,'CUERO',1),(13,7,'GOMA',1),(14,8,'NIKE',1),(15,8,'GARLEY',1);
/*!40000 ALTER TABLE `pl_propiedad` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-28 20:40:43
