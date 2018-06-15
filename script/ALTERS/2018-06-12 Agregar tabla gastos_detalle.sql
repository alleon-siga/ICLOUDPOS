CREATE TABLE `gastos_detalle` (
	`id`  int NOT NULL AUTO_INCREMENT ,
	`id_gastos`  int NULL ,
	`descripcion`  varchar(100) NULL ,
	`cantidad`  int NULL ,
	`precio`  decimal(18,2) NULL ,
	`impuesto`  decimal(18,2) NULL ,
	`subtotal`  decimal(18,2) NULL ,
	`total`  decimal(18,2) NULL ,
	PRIMARY KEY (`id`)
);
