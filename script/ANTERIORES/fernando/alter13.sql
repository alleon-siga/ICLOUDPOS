--esto es para tener un historial del precio de venta por cada producto y unidad que se registre en ingresos
--se hizo para poder sacar el reporte de INGRESO DETALLE
ALTER TABLE `detalleingreso`
  ADD COLUMN `precio_venta` DECIMAL(18,2) DEFAULT 0.00  NULL AFTER `total_detalle`;
  ----este insert es para que los usuarios de Ceo Aplication grupos usuarios, tengan acceso a este reporte
  INSERT INTO opcion_grupo VALUES('1','75','1')

