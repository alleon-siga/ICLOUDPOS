ALTER TABLE facturacion ADD descuento DECIMAL(18,2) DEFAULT 0 NULL;

ALTER TABLE venta ADD nota_facturada INT DEFAULT 0 NULL;

CREATE UNIQUE INDEX facturacion_documento_tipo_documento_numero_uindex ON facturacion (documento_tipo, documento_numero);

ALTER TABLE facturacion_resumen MODIFY fecha DATE NOT NULL;
CREATE UNIQUE INDEX facturacion_resumen_fecha_correlativo_uindex ON facturacion_resumen (fecha, correlativo);
ALTER TABLE facturacion_resumen MODIFY fecha_ref DATE NOT NULL;

ALTER TABLE facturacion MODIFY fecha DATE NOT NULL;