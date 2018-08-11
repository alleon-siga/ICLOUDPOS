ALTER TABLE `producto_costo_unitario`
ADD COLUMN `tipo_impuesto_compra`  tinyint NULL COMMENT 'tipo de impuesto de la compra: 1=Incluye impuesto, 2=Agregar impuesto, 3=No considerar impuesto' AFTER `contable_activo`;

ALTER TABLE `detalle_venta`
ADD COLUMN `tipo_impuesto_compra`  tinyint NULL COMMENT 'tipo de impuesto de la compra: 1=Incluye impuesto, 2=Agregar impuesto, 3=No considerar impuesto' AFTER `precio_venta`;

