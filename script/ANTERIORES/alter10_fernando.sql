ALTER TABLE `producto`
  ADD COLUMN `producto_costo_unitario` FLOAT(20,2) NULL AFTER `producto_estado`;
ALTER TABLE `historial_cronograma`
  ADD COLUMN `monto_restante` FLOAT(20,2) NULL AFTER `historialcrono_tipopago`;
ALTER TABLE `pagos_ingreso`
  ADD COLUMN `pagoingreso_restante` FLOAT(22,2) NULL AFTER `pagoingreso_monto`;



