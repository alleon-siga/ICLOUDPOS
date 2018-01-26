ALTER TABLE `bdinventario`.`venta`   
  ADD COLUMN `pagado` DECIMAL(18,2) DEFAULT 0  NULL AFTER `total`,
  ADD COLUMN `vuelto` DECIMAL(18,2) DEFAULT 0  NULL AFTER `pagado`;
