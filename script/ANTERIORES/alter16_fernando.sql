ALTER TABLE `historial_cronograma`
  ADD COLUMN `historialcrono_usuario` BIGINT NULL AFTER `monto_restante`,
  ADD FOREIGN KEY (`historialcrono_usuario`) REFERENCES `usuario`(`nUsuCodigo`);
