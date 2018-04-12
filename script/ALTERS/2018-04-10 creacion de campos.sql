ALTER TABLE `venta_devolucion`
ADD COLUMN `serie`  varchar(45) NULL AFTER `detalle_utilidad`,
ADD COLUMN `numero`  varchar(45) NULL AFTER `serie`;

CREATE TABLE `diccionario_termino` (
`id`  int(11) NOT NULL AUTO_INCREMENT,
`tipo`  varchar(45) NULL ,
`valor`  varchar(45) NULL ,
`longitud`  varchar(45) NULL ,
PRIMARY KEY (`id`)
);

ALTER TABLE `diccionario_termino`
ADD COLUMN `activo`  char(1) NULL DEFAULT '1' AFTER `longitud`;

INSERT INTO diccionario_termino(tipo, valor, longitud) VALUES('Identificacion persona','DNI','8');
INSERT INTO diccionario_termino(tipo, valor, longitud) VALUES('Identificacion empresa','RUC','11');
INSERT INTO diccionario_termino(tipo, valor, longitud) VALUES('Impuesto','IGV','2');

ALTER TABLE `comprobantes`
ADD COLUMN `fecha_venc`  date NULL AFTER `estado`;