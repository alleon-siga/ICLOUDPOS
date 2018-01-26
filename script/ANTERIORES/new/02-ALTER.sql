ALTER TABLE `producto` ADD `producto_codigo_interno` VARCHAR( 50 ) NULL AFTER `producto_id`;

INSERT INTO `columnas` (`nombre_columna`, `nombre_join`, `nombre_mostrar`, `tabla`, `mostrar`, `activo`) VALUES ('producto_codigo_interno', 'producto_codigo_interno', 'Código Interno', 'producto', '1', '1');