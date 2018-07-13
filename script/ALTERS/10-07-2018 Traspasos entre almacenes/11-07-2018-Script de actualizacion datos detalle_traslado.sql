UPDATE traspaso_detalle, traspaso
SET traspaso_detalle.local_origen = traspaso.local_origen
WHERE traspaso.id = traspaso_detalle.traspaso_id