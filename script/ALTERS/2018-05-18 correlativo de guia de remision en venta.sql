ALTER TABLE `venta`
ADD COLUMN `nro_guia`  int NULL COMMENT 'Correlativo de la guia de remision' AFTER `nota`;

INSERT INTO documentos VALUES('8', 'GUIA DE REMISION', '1', 'GR', '0', '0', '0');