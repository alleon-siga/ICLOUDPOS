ALTER TABLE `tipos_gasto`
ADD COLUMN `tipo_tipos_gasto`  char(1) NULL COMMENT '0 = Variable, 1 = Fijo' AFTER `status_tipos_gasto`;

ALTER TABLE `gastos`
ADD COLUMN `id_impuesto`  int NULL AFTER `descripcion`,
ADD COLUMN `subtotal`  float NULL AFTER `id_impuesto`,
ADD COLUMN `impuesto`  float NULL AFTER `subtotal`;