UPDATE  documento_venta SET nombre_tipo_documento="1" WHERE nombre_tipo_documento="FACTURA";
UPDATE  documento_venta SET nombre_tipo_documento="2" WHERE nombre_tipo_documento="NOTA CREDITO";
UPDATE  documento_venta SET nombre_tipo_documento="3" WHERE nombre_tipo_documento="BOLETA DE VENTA";
UPDATE  documento_venta SET nombre_tipo_documento="4" WHERE nombre_tipo_documento="PUNTO REMISION";
UPDATE  documento_venta SET nombre_tipo_documento="5" WHERE nombre_tipo_documento="PEDIDO COMPRA-VENTA";
UPDATE  documento_venta SET nombre_tipo_documento="6" WHERE nombre_tipo_documento="NOTA DE PEDIDO";
UPDATE  credito SET var_credito_estado="PagoPendiente" WHERE var_credito_estado="Debito";