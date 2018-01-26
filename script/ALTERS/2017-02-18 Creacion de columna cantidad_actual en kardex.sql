ALTER TABLE `movimiento_historico`
ADD COLUMN `cantidad_actual` BIGINT(20) NULL DEFAULT 0 AFTER `old_cantidad`;

