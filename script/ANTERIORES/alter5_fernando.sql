ALTER TABLE `producto`
  ADD COLUMN `producto_condicion` BOOLEAN DEFAULT 1  NULL AFTER `producto_cualidad`;
ALTER TABLE `producto`
  CHANGE `producto_condicion` `producto_estado` TINYINT(1) DEFAULT 1  NULL;