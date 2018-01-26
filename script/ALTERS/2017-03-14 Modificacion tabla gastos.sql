
ALTER TABLE `gastos` 
ADD COLUMN `fecha_registro` DATETIME NULL  AFTER `id_gastos` ,
ADD COLUMN `proveedor_id` BIGINT(20) NULL  AFTER `tasa_cambio` , 
ADD COLUMN `usuario_id` BIGINT(20) NULL  AFTER `proveedor_id` ,
ADD COLUMN `responsable_id` BIGINT(20) NULL  AFTER `usuario_id` ,
ADD COLUMN `motivo_eliminar` VARCHAR(100) NULL  AFTER `responsable_id` ;


