UPDATE opcion SET cOpcionNombre="Cargos" WHERE nOpcionClase=50 AND cOpcionNombre="Grupos";
ALTER TABLE `ajusteinventario`
  ADD COLUMN `usuario_encargado` BIGINT(20) NULL AFTER `local_id`,
  ADD FOREIGN KEY (`usuario_encargado`) REFERENCES `usuario`(`nUsuCodigo`);
ALTER TABLE `ingreso`
  ADD COLUMN `ingreso_observacion` TEXT NULL AFTER `pago`;


INSERT INTO columnas VALUES('producto_estado','producto_estado', 'Estado','producto',1,1,NULL);