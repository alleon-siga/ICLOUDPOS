INSERT INTO `metodos_pago` (`id_metodo`, `nombre_metodo`, `status_metodo`, `tipo_metodo`) VALUES (7, 'TARJETA', 1, 'CAJA');

UPDATE `metodos_pago` SET `status_metodo`=0 WHERE `id_metodo`='6';
