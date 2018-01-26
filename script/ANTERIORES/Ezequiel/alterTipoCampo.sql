CREATE TABLE `cliente_tipo_campo` ( `id_tipo` BIGINT(20) NOT NULL AUTO_INCREMENT , `id_cliente` BIGINT(20) NOT NULL , `valor` BIGINT(2) NOT NULL , PRIMARY KEY (`id_tipo`)) ENGINE = MyISAM;


CREATE TABLE `cliente_campo_valor` ( `id_campo` BIGINT(20) NOT NULL AUTO_INCREMENT , `campo_cliente` BIGINT(20) NOT NULL , `campo_valor` VARCHAR(255) NOT NULL , PRIMARY KEY (`id_campo`)) ENGINE = MyISAM;