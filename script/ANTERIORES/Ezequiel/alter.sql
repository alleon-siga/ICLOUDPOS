  ALTER TABLE cliente DROP FOREIGN KEY cliente_fk_2;
 ALTER TABLE cliente DROP FOREIGN KEY cliente_ibfk_1;
 ALTER TABLE cliente DROP INDEX cliente_fk_2_idx;
 ALTER TABLE `cliente` DROP ciudad_id;



 ALTER TABLE `cliente`
  DROP `direccion`,
  DROP `email`,
  DROP `identificacion`,
  DROP `pagina_web`,
  DROP `telefono1`,
  DROP `telefono2`,
  DROP `nota`,
  DROP `categoria_precio`;

  ALTER TABLE `cliente`  ADD `tipo_cliente` VARCHAR(20) NOT NULL  AFTER `cliente_status`,  ADD `dni` VARCHAR(225) NOT NULL  AFTER `tipo_cliente`,  ADD `nombres` VARCHAR(255) NOT NULL  AFTER `dni`,  ADD `apellido_materno` VARCHAR(255) NOT NULL  AFTER `nombres`,  ADD `apellido_paterno` VARCHAR(255) NOT NULL  AFTER `apellido_materno`,  ADD `genero` CHAR(1) NOT NULL  AFTER `apellido_paterno`,  ADD `direccion_maps` TEXT NOT NULL  AFTER `genero`,  ADD `latitud` VARCHAR(255) NOT NULL  AFTER `direccion_maps`,  ADD `longitud` VARCHAR(255) NOT NULL  AFTER `latitud`,  ADD `ruc` VARCHAR(45) NOT NULL  AFTER `longitud`,  ADD `direccion_sunat` TEXT NOT NULL  AFTER `ruc`,  ADD `direccion_envio` TINYINT(1) NOT NULL  AFTER `direccion_sunat`,  ADD `direccion_facturacion` TINYINT(1) NOT NULL  AFTER `direccion_envio`,  ADD `representante_apellido_pat` VARCHAR(255) NOT NULL  AFTER `direccion_facturacion`,  ADD `representante_apellido_mat` VARCHAR(255) NOT NULL  AFTER `representante_apellido_pat`,  ADD `representante_genero` VARCHAR(255) NOT NULL  AFTER `representante_apellido_mat`,  ADD `vendedor_a` BIGINT(20) NULL  AFTER `representante_genero`,  ADD `representante_nombre` VARCHAR(255) NOT NULL  AFTER `vendedor_a`,  ADD `representante_dni` VARCHAR(255) NOT NULL  AFTER `representante_nombre`;

  UPDATE `cliente` SET `vendedor_a` = '1' WHERE `cliente`.`id_cliente` = 1;
  ALTER TABLE `cliente` ADD INDEX  (`vendedor_a`);
 ALTER TABLE `cliente` ADD CONSTRAINT `cliente_ibfk_2` FOREIGN KEY (`vendedor_a`) REFERENCES `usuario`(`nUsuCodigo`) ON DELETE RESTRICT ON UPDATE RESTRICT;