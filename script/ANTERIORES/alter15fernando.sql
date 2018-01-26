ALTER TABLE `pagos_ingreso`
  ADD COLUMN `pagoingreso_usuario` BIGINT NULL AFTER `pagoingreso_restante`,
  ADD FOREIGN KEY (`pagoingreso_usuario`) REFERENCES `usuario`(`nUsuCodigo`);
ALTER TABLE `gastos`
  ADD COLUMN `gasto_usuario` BIGINT NULL AFTER `status_gastos`,
  ADD FOREIGN KEY (`gasto_usuario`) REFERENCES `usuario`(`nUsuCodigo`);
