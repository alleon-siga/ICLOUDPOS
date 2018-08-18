ALTER TABLE `venta`
ADD COLUMN `latitud`  varchar(12) NULL COMMENT 'Latitud de donde se registro la venta' AFTER `nota_facturada`,
ADD COLUMN `longitud`  varchar(12) NULL COMMENT 'Longitud de donde se registro la venta' AFTER `latitud`,
ADD COLUMN `plataforma`  int NULL COMMENT 'Campo para identificar donde se realiza la venta 0 = web; 1 = app' AFTER `longitud`;
