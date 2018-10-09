INSERT INTO configuraciones(config_key, config_value) VALUES('REDONDEO_VENTAS', 'SI');

ALTER TABLE `detalle_venta`
MODIFY COLUMN `precio`  decimal(18,4) NULL DEFAULT 0.00 AFTER `id_producto`,
MODIFY COLUMN `precio_venta`  decimal(18,4) NULL DEFAULT 0.00 AFTER `impuesto_porciento`;