ALTER TABLE `producto`
ADD COLUMN `producto_nombre_original`  varchar(100) NULL COMMENT 'NOMBRE ORIGINAL DE NOMBRE DE PRODUCTO' AFTER `producto_descripcion_img`;