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
/*Table structure for table `cliente_tipo_campo` */

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
