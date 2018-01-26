/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.34 : Database - newlevel_dev
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `cliente_campo_valor` */

DROP TABLE IF EXISTS `cliente_campo_valor`;

CREATE TABLE `cliente_campo_valor` (
  `id_campo` bigint(20) NOT NULL AUTO_INCREMENT,
  `campo_cliente` bigint(20) NOT NULL,
  `campo_valor` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_campo` bigint(20) DEFAULT NULL,
  `referencia` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

  ALTER TABLE `cliente`  ADD `tipo_cliente` VARCHAR(20) NOT NULL,  ADD `dni` VARCHAR(225) NOT NULL,  ADD `nombres` VARCHAR(255) NOT NULL,  ADD `apellido_materno` VARCHAR(255) NOT NULL,  ADD `apellido_paterno` VARCHAR(255) NOT NULL,  ADD `genero` CHAR(1) NOT NULL  AFTER `apellido_paterno`,  ADD `direccion_maps` TEXT NOT NULL,  ADD `latitud` VARCHAR(255) NOT NULL,  ADD `longitud` VARCHAR(255) NOT NULL,  ADD `ruc` VARCHAR(45) NOT NULL, ADD `representante_apellido_pat` VARCHAR(255) NOT NULL,  ADD `representante_apellido_mat` VARCHAR(255) NOT NULL  AFTER `representante_apellido_pat`,  ADD `representante_genero` VARCHAR(255) NOT NULL  AFTER `representante_apellido_mat`, ADD `representante_nombre` VARCHAR(255) NOT NULL,  ADD `representante_dni` VARCHAR(255) NOT NULL  AFTER `representante_nombre`;

ALTER TABLE `cliente`
  DROP COLUMN `exento_impuesto`,DROP COLUMN `limite_credito`;
ALTER TABLE `cliente`
  ADD COLUMN `agente_retension` BOOLEAN NULL AFTER `representante_dni`;
  ALTER TABLE `cliente`
  ADD COLUMN `agente_retension_valor` DECIMAL(18,2) NULL AFTER `agente_retension`;
  ALTER TABLE `cliente`
  ADD COLUMN `linea_credito` DECIMAL(18,2) NULL AFTER `agente_retension_valor`;
ALTER TABLE `cliente`
  ADD COLUMN `provincia` BIGINT NULL AFTER `direccion`,
  ADD COLUMN `ciudad` BIGINT NULL AFTER `provincia`,
  ADD COLUMN `distrito` BIGINT NULL AFTER `ciudad`;

ALTER TABLE `cliente`   
  DROP COLUMN `email`, 
  DROP COLUMN `identificacion`, 
  DROP COLUMN `pagina_web`, 
  DROP COLUMN `telefono2`, 
  DROP COLUMN `nota`, 
  CHANGE `tipo_cliente` `tipo_cliente` VARCHAR(20) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `dni` `dni` VARCHAR(225) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `nombres` `nombres` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `apellido_materno` `apellido_materno` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `apellido_paterno` `apellido_paterno` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `genero` `genero` CHAR(1) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `direccion_maps` `direccion_maps` TEXT CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `latitud` `latitud` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `longitud` `longitud` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `ruc` `ruc` VARCHAR(45) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `representante_apellido_pat` `representante_apellido_pat` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `representante_apellido_mat` `representante_apellido_mat` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `representante_genero` `representante_genero` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `representante_nombre` `representante_nombre` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL,
  CHANGE `representante_dni` `representante_dni` VARCHAR(255) CHARSET latin1 COLLATE latin1_swedish_ci NULL;

