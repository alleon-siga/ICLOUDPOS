-- estos campos son editados asi, para que solo guarde por defecto 2 decimales, y no tener que estar formateando las cantidades a cada momento
ALTER TABLE `producto_costo_unitario`
  CHANGE `costo` `costo` DECIMAL(18,2) NOT NULL;
ALTER TABLE `producto_costo_unitario`
  CHANGE `contable_costo` `contable_costo` DECIMAL(18,2) DEFAULT 0  NULL;
