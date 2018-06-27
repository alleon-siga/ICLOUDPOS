CREATE TABLE `traspaso` (
`id`  int NOT NULL AUTO_INCREMENT ,
`ref_id`  int NULL ,
`usuario_id`  int NULL ,
`local_origen`  int NULL ,
`local_destino`  int NULL ,
`fecha`  datetime NULL ,
`motivo`  varchar(255) NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `traspaso_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traspaso_id` int(11) DEFAULT NULL,
  `kardex_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `traspaso_id` (`traspaso_id`),
  CONSTRAINT `traspaso_detalle_ibfk_1` FOREIGN KEY (`traspaso_id`) REFERENCES `traspaso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;