CREATE TABLE `pagos_ingreso`(  
  `pagoingreso_id` BIGINT NOT NULL AUTO_INCREMENT,
  `pagoingreso_ingreso_id` BIGINT,
  `pagoingreso_fecha` DATETIME,
  `pagoigreso_monto` DOUBLE,
  PRIMARY KEY (`pagoingreso_id`),
  FOREIGN KEY (`pagoingreso_ingreso_id`) REFERENCES `ingreso`(`id_ingreso`)
);

ALTER TABLE `pagos_ingreso`   
  CHANGE `pagoigreso_monto` `pagoingreso_monto` DOUBLE NULL;
