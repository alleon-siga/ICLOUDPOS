<<<<<<< HEAD
/*
SQLyog Ultimate v10.42
MySQL - 5.5.34
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_id','producto_id','ID','producto','1','0','35');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_codigo_barra','producto_codigo_barra','Código de barra','producto','0','0','36');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_nombre','producto_nombre','Nombre','producto','1','0','37');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_descripcion','producto_descripcion','Descripción','producto','0','0','38');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_marca','nombre_marca','Marca','producto','1','1','39');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('produto_grupo','nombre_grupo','Grupo','producto','0','1','40');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_familia','nombre_familia','Familia','producto','1','1','41');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_linea','nombre_linea','Linea','producto','1','1','42');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_proveedor','proveedor_nombre','Proveedor','producto','0','1','43');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_stockminimo','producto_stockminimo','Stock Mínimo','producto','0','1','44');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_impuesto','nombre_impuesto','Impuesto','producto','0','0','45');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_largo','producto_largo','Largo','producto','0','0','46');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_ancho','producto_ancho','Ancho','producto','0','0','47');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_alto','producto_alto','Alto','producto','0','0','48');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_peso','producto_peso','Peso','producto','0','0','49');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_nota','producto_nota','Nota','producto','0','0','50');
insert into `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) values('producto_cualidad','producto_cualidad','Cualidad','producto','0','0','51');
=======
/*
SQLyog Ultimate v10.42 
MySQL - 5.5.34 : Database - bdinventario
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`bdinventario` /*!40100 DEFAULT CHARACTER SET latin1 */;

/*Table structure for table `columnas` */

CREATE TABLE `columnas` (
  `nombre_columna` varchar(255) NOT NULL,
  `nombre_join` varchar(45) NOT NULL,
  `nombre_mostrar` varchar(255) NOT NULL,
  `tabla` varchar(45) DEFAULT NULL,
  `mostrar` tinyint(1) DEFAULT '1',
  `activo` tinyint(1) DEFAULT '1',
  `id_columna` bigint(20) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_columna`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
>>>>>>> 25bfc352afb5e740472c635bd68a58639e6d6cc1
