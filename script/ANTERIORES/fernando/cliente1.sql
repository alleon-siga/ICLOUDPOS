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
/*Table structure for table `cliente_tipo_campo_padre` */

DROP TABLE IF EXISTS `cliente_tipo_campo_padre`;

CREATE TABLE `cliente_tipo_campo_padre` (
  `tipo_campo_padre_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tipo_campo_padre_nombre` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_campo_padre_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`tipo_campo_padre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `cliente_tipo_campo_padre` */

insert  into `cliente_tipo_campo_padre`(`tipo_campo_padre_id`,`tipo_campo_padre_nombre`,`tipo_campo_padre_slug`) values (1,'REPRESENTANTE','representante'),(2,'DIRECCION','direccion'),(3,'RAZON SOCIAL','razon_social'),(4,'TELEFONO/CELULAR','telefono'),(5,'PAGINA WEB','pagina_web'),(6,'CUMPLEAÑOS','cumpleano'),(7,'CORREO','correo');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

DROP TABLE IF EXISTS `cliente_tipo_campo`;

CREATE TABLE `cliente_tipo_campo` (
  `id_tipo` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `padre_id` bigint(20) DEFAULT NULL,
  `input_type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tipo`),
  KEY `padre_id` (`padre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Data for the table `cliente_tipo_campo` */

insert  into `cliente_tipo_campo`(`id_tipo`,`nombre`,`slug`,`padre_id`,`input_type`) values (1,'Pagina web','pagina_web',5,'text'),(2,'Provincia','provincia',2,'select'),(3,'Ciudad','ciudad',2,'select'),(4,'Distrito','distrito',2,'select'),(5,'Direccion Especifica','direccion_especifica',2,'text'),(6,'RUC','ruc',3,'text'),(7,'Razon Social','razon_social',3,'text'),(8,'Direccion','direccion',3,'text'),(9,'Representante','representante',1,'text'),(10,'Telefono/Celular','telefono_celular',4,'text'),(11,'Cumpleaño','cumpleano',6,'text'),(12,'Correo','correo',7,'text');


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
/*ALTER TABLE `cliente`
  ADD COLUMN `direccion` TEXT NULL;*/

  ALTER TABLE `cliente`  ADD `tipo_cliente` VARCHAR(20) NOT NULL, ADD `dni` VARCHAR(225) NOT NULL,  ADD `nombres` VARCHAR(255) NOT NULL,  ADD `apellido_materno` VARCHAR(255) NOT NULL,  ADD `apellido_paterno` VARCHAR(255) NOT NULL,  ADD `genero` CHAR(1) NOT NULL  AFTER `apellido_paterno`,  ADD `direccion_maps` TEXT NOT NULL,  ADD `latitud` VARCHAR(255) NOT NULL,  ADD `longitud` VARCHAR(255) NOT NULL,  ADD `ruc` VARCHAR(45) NOT NULL, ADD `representante_apellido_pat` VARCHAR(255) NOT NULL,  ADD `representante_apellido_mat` VARCHAR(255) NOT NULL  AFTER `representante_apellido_pat`,  ADD `representante_genero` VARCHAR(255) NOT NULL  AFTER `representante_apellido_mat`, ADD `representante_nombre` VARCHAR(255) NOT NULL,  ADD `representante_dni` VARCHAR(255) NOT NULL  AFTER `representante_nombre`;

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


