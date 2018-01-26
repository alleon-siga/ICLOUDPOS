---este alter lo que hace es agregar los campos paara guardar la descripcion de las imagenes del producto
ALTER TABLE `producto`
    ADD COLUMN `producto_titulo_imagen` VARCHAR(100) NULL AFTER `producto_modelo`,
  ADD COLUMN `producto_descripcion_img` TEXT NULL AFTER `producto_titulo_imagen`;
INSERT INTO columnas (nombre_columna,nombre_join,nombre_mostrar,tabla,mostrar,activo) VALUES
('producto_titulo_imagen','producto_titulo_imagen','Titulo Imagen','producto','0','1');
INSERT INTO columnas (nombre_columna,nombre_join,nombre_mostrar,tabla,mostrar,activo) VALUES
('producto_descripcion_img','producto_descripcion_img','Descripcion Imagen','producto','0','1')