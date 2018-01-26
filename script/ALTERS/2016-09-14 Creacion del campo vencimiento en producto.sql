ALTER TABLE `producto`
ADD COLUMN `producto_vencimiento` DATETIME NULL DEFAULT NULL AFTER `producto_descripcion`;

INSERT INTO `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`, `id_columna`) VALUES ('producto_vencimiento', 'producto_vencimiento', 'Fecha de Vencimiento', 'producto', '0', '0', '66');

