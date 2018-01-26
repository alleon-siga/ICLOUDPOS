ALTER TABLE `credito_cuotas`   
  ADD COLUMN `id_credito_cuota` BIGINT NOT NULL AUTO_INCREMENT FIRST, 
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`id_credito_cuota`);

CREATE TABLE IF NOT EXISTS `credito_cuotas_abono`(
  `abono_id` BIGINT NOT NULL AUTO_INCREMENT,
  `credito_cuota_id` BIGINT,
  `monto_abono` DECIMAL(18,2),
  `fecha_abono` DATETIME NOT NULL,
  `tipo_pago` BIGINT,
  `monto_restante` DECIMAL(18,2),
  `usuario_pago` BIGINT,
  PRIMARY KEY (`abono_id`),
  FOREIGN KEY (`credito_cuota_id`) REFERENCES `credito_cuotas`(`id_credito_cuota`),
  FOREIGN KEY (`tipo_pago`) REFERENCES `metodos_pago`(`id_metodo`),
  FOREIGN KEY (`usuario_pago`) REFERENCES `usuario`(`nUsuCodigo`)
);
ALTER TABLE `credito_cuotas`   
  DROP COLUMN `fecha_pago`;
  ALTER TABLE `credito_cuotas`
  ADD COLUMN `ultimo_pago` DATETIME NULL AFTER `ispagado`;

