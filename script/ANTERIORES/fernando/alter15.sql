--esto se hace para mostrar la referencia distinta para ajuste salida, y ajuste entrada
UPDATE movimiento_historico SET referencia_valor="Se agregaron mas productos por ajuste" WHERE tipo_movimiento="AJUSTE" AND tipo_operacion="ENTRADA";
UPDATE movimiento_historico SET referencia_valor="Se sacaron alguno(s) producto(s) por ajuste" WHERE tipo_movimiento="AJUSTE" AND tipo_operacion="SALIDA";
UPDATE movimiento_historico SET referencia_valor="Se devolvió un Ingreso" WHERE tipo_movimiento="ANULACION" AND tipo_operacion="SALIDA"