<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('inventario/inventario_model');
        $this->load->model('traspaso/traspaso_model');
    }

    function exe_sql($sql)
    {
        $query_ins = $this->db->query($sql);
        return $query_ins->result_array();
        //$rs_ins = $query_ins->row_array();
    }

    public function get_garante_by_dni($dni)
    {
        $query = $this->db->get_where('garante', array('dni' => $dni), $limit = 1);
        return $query->result_array();
    }

    function cobrar_venta($venta_id, $tipo_pago, $data = array())
    {
        if ($tipo_pago == 1) {
            if ($data['forma_pago'] == 1) {
                $contado = array(
                    'id_venta' => $venta_id,
                    'status' => 'PagoCancelado',
                    'montopagado' => $data['total_pagar']
                );
                $this->db->insert('contado', $contado);
            } elseif ($data['forma_pago'] == 2) {
                $tarjeta = array(
                    'venta_id' => $venta_id,
                    'tarjeta_pago_id' => $data['tipo_tarjeta'],
                    'numero' => $data['num_oper']
                );
                $this->db->insert('venta_tarjeta', $tarjeta);
            }
        } else if ($tipo_pago == 2) {

            $cuotas_maximas_de_local = $this->session->userdata('MAXIMO_CUOTAS_CREDITO');

            /*ESTA VARIABLE SE ENCARGA DE GUARDAR EL TOTAL A PAGAR DEL CLIENTE, YA RESTADO EL TOTAL DE LA VENTA MENOS
            EL PAGO INICIAL, O SUMANDO LOS MONTOS DE LAS DOS CUOTAS, YA QUE SE ESTABA GUARDANDO EL TOTAL AL CONTADO
            EN EL CAMPO dec_credito_montocuota*/
            $monto_a_pagar = 0;

            $cuotas_a_pagar = array();
            $cuotas = $data['cuotas'];
            for ($i = $cuotas_maximas_de_local; $i < count($cuotas); $i++) {

                $monto = explode(' ', $cuotas[$i]->monto);

                if (count($monto) > 1) {

                    $monto = number_format(str_replace(',', '', $monto[1]), 2, '.', '');
                } else {
                    $monto = number_format(str_replace(',', '', $monto[0]), 2, '.', '');
                }
                $cuotas[$i]->monto = $monto;
                $cuotas_a_pagar[] = $cuotas[$i];
                /*SUMO*/
                $monto_a_pagar += $monto;


            }


            $my_venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
            $credito = array(
                'id_venta' => $venta_id,
                'int_credito_nrocuota' => 1,
                'dec_credito_montocuota' => $monto_a_pagar,
                'var_credito_estado' => 'PagoPendiente',
                'dec_credito_montodebito' => 0.0,
                'id_moneda' => $my_venta->id_moneda,
                'tasa_cambio' => $my_venta->tasa_cambio,
            );
            $this->db->insert('credito', $credito);


            /*   Insertar credito-cuotas */

            $index = 1;
            foreach ($cuotas_a_pagar as $cu) {
                if ($cu->fecha_vencimiento and $cu->fecha_giro) {
                    $fecha_vencimiento = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $cu->fecha_vencimiento)));
                    $fecha_giro = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $cu->fecha_giro)));


                    $array_cuotas = array(
                        "nro_letra" => $venta_id . '-' . $index,
                        "fecha_giro" => $fecha_giro,
                        "fecha_vencimiento" => $fecha_vencimiento,
                        "monto" => $cu->monto,
                        "isgiro" => $cu->isgiro,
                        'id_venta' => $venta_id,
                    );


                    $this->db->insert('credito_cuotas', $array_cuotas);
                    $index++;
                }

            }
        }

        $this->db->where('venta_id', $venta_id);
        $this->db->update('venta', array(
            'venta_status' => 'COMPLETADO',
            'pagado' => $data['importe'],
            'vuelto' => $data['vuelto']));
    }


    function insertar_venta($venta_cabecera, $detalle, $cuotas)
    {

        $this->load->model('historico/historico_model');
        $this->load->model('unidades/unidades_model');

        $cantidad_new = array();
        $traspasos = array();
        // var_dump($this->input->post('precio_credito'));
        // print_r($venta_cabecera);
        // die;
        $this->db->trans_start(true);

        $this->db->trans_begin();

        $query_ins = $this->db->query("select (case when (`documento_venta`.`documento_Numero` = '9999999999') then
									   convert( right(concat('0000',(ifnull(`documento_venta`.`documento_Serie`,0) + 1)), 4) using latin1)
									   when (ifnull(`documento_venta`.`documento_Serie`,0) = 0) then convert( right(concat('0000', 1), 4) using latin1)
									   else `documento_venta`.`documento_Serie` end) AS `SERIE`,
									  (case when (`documento_venta`.`documento_Numero` = '9999999999') then right(concat((`documento_venta`.`documento_Numero` + 2)),10)
											else right(concat('0000000000',(`documento_venta`.`documento_Numero` + 1)),10) end) AS `NUMERO`, `documento_venta`.`nombre_tipo_documento` AS `Documento`
									   from `documento_venta` where `documento_venta`.`nombre_tipo_documento` = '" . $venta_cabecera['tipo_documento'] . "' order by `documento_venta`.`documento_Serie`, `documento_venta`.`documento_Numero` desc limit 0,1");
        $rs_ins = $query_ins->row_array();
        //var_dump($rs_ins);die;
        if (empty($rs_ins['SERIE'])) {
            $serie = '0001';
        } else {
            $serie = $rs_ins['SERIE'];
        }
        if (empty($rs_ins['NUMERO'])) {
            $numero = '0000000001';
        } else {
            $len = strlen((string)$venta_cabecera['correlativo']);
            $numero = '';
            for ($i = 0; $i < (6 - $len); $i++) {
                $numero .= '0';
            }
            $numero = $numero . $venta_cabecera['correlativo'];
        }

        $tip_doc = array(
            'nombre_tipo_documento' => $venta_cabecera['tipo_documento'],
            'documento_Serie' => $serie,
            'documento_Numero' => $numero
        );

        $this->db->insert('documento_venta', $tip_doc);
        $id_documento = $this->db->insert_id();
        $moneda = explode('_', $venta_cabecera['id_moneda']);
        $moneda[0];
        $venta = array(
            'fecha' => $venta_cabecera['fecha'],
            'id_cliente' => $venta_cabecera['id_cliente'],
            'id_vendedor' => $venta_cabecera['id_vendedor'],

            'condicion_pago' => $venta_cabecera['condicion_pago'],
            'venta_status' => $venta_cabecera['venta_status'],
            'local_id' => $venta_cabecera['local_id'],
            'subtotal' => $venta_cabecera['subtotal'],
            'total_impuesto' => $venta_cabecera['total_impuesto'],
            'total' => $this->input->post('precio_credito') ? $this->input->post('precio_credito') : $venta_cabecera['total'],
            'numero_documento' => $id_documento,
            'pagado' => $venta_cabecera['importe'],
            'vuelto' => $venta_cabecera['vuelto'] ? $venta_cabecera['vuelto'] : '0.00',
            'id_moneda' => $moneda[0],
            'tasa_cambio' => $venta_cabecera['tasa_cambio'],
            'id_documento' => $venta_cabecera['id_documento'],
            'correlativo' => $venta_cabecera['correlativo'],
            'dni_garante' => $this->input->post("garante") ? $this->input->post("garante") : NULL,
            'inicial' => $this->input->post('mto_ini_res') ? $this->input->post('mto_ini_res') : $venta_cabecera['total']
        );

        if (validOption("VISTA_CREDITO", 'SIMPLE', 'SIMPLE') && $venta['condicion_pago'] == '2') {
            $venta['inicial'] = isset($venta_cabecera['pago_cuenta']) ? $venta_cabecera['pago_cuenta'] : 0;
        }

        $this->db->insert('venta', $venta);

        $venta_id = $this->db->insert_id();

        // Detalle de la Venta
        $array_detalle = array();
        foreach ($detalle as $row) {


            $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
									   FROM inventario where id_producto=' . $row->id_producto . ' and id_local=' . $venta_cabecera['local_id']);
            $inventario_existente = $query->row_array();
            $cantidad_vieja = 0;
            $fraccion_vieja = 0;

            if (isset($inventario_existente['cantidad'])) {
                $cantidad_vieja = $inventario_existente['cantidad'];
            }
            if (isset($inventario_existente['fraccion'])) {
                $fraccion_vieja = $inventario_existente['fraccion'];
            }
            $cantidad_venta = $row->cantidad;
            $unidad_medida_venta = $row->unidad_medida;
            $id_producto = $row->id_producto;
            $precio = $row->precio;
            $importe = $row->detalle_importe;
            /*if ($venta_cabecera['id_moneda'] == "1029"){
            	$importe = $row->detalle_importe;
            }else{
            	$importe = (($row->detalle_importe / $venta_cabecera['tasa_cambio']) - 0.1);
            } */


            //  CALCLOS DE UNDIAD DE MEDIDA

            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

            $unidades_producto = $query->result_array();

            // var_dump($unidades_producto);

            $unidad_maxima = $unidades_producto[0];
            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
            //var_dump($unidad_maxima);
            //var_dump($unidad_minima);
            $unidad_form = 0;
            foreach ($unidades_producto as $up) {
                if ($up['id_unidad'] == $unidad_medida_venta) {
                    $unidad_form = $up;
                }
            }

            $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
            $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

            $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;


            if ($suma_cantidades >= $unidad_maxima['unidades']) {

                $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                $cantidad_nueva = intval($resultado_division);// - $cantidad_vieja;
                $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                $fraccion_nueva = $resto_division;
            } else {
                if ($suma_cantidades < $unidad_maxima['unidades']) {
                    $cantidad_nueva = 0;
                    $fraccion_nueva = +$suma_cantidades;
                } else {
                    $cantidad_nueva = $cantidad_vieja;
                    $fraccion_nueva = +$suma_cantidades;
                }

            }
            //var_dump($unidad_medida_venta);
            //var_dump($unidad_maxima['id_unidad']);

            if ($unidad_medida_venta == $unidad_maxima['id_unidad']) {
                $cantidad_nueva = $cantidad_vieja - $cantidad_venta;
                $fraccion_nueva = $fraccion_vieja;
            }
            if ($unidad_medida_venta == $unidad_minima['id_unidad']) {
                // var_dump($unidad_minima['id_unidad']);
                //   $suma_cantidades= $fraccion_vieja-$cantidad_venta;
                if ($suma_cantidades >= $unidad_maxima['unidades']) {

                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division);// - $cantidad_vieja;
                    $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                    $fraccion_nueva = $resto_division;
                } else {


                    if ($suma_cantidades < $unidad_maxima['unidades']) {
                        $cantidad_nueva = 0;
                        $fraccion_nueva = +$suma_cantidades;
                    } else {

                        if ($cantidad_vieja > 0) {
                            $cantidad_nueva = $cantidad_vieja;
                            $fraccion_nueva = $suma_cantidades;
                        } else {

                            if (count($unidades_producto) > 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $cantidad_venta;
                            } else {

                                $cantidad_nueva = $cantidad_venta;
                                $fraccion_nueva = 0;
                            }
                        }
                    }

                }

            }


            ///busco el costo unitario del producto

            $query_costo_u = $this->db->query("select producto_id,  producto_costo_unitario from producto
  WHERE producto_id=" . $id_producto . " ");
            $costo_unitario = $query_costo_u->row_array();

            if ($costo_unitario['producto_costo_unitario'] == null) {

                $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
AND unidades_has_producto.producto_id=detalleingreso.id_producto
WHERE detalleingreso.id_producto=" . $id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $id_producto . ")  ");

                $result_ingreso = $query_compra->result_array();
                if (count($result_ingreso) > 0) {

                    $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];
                    $promedio_compra = ($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_form['unidades'];
                } else {
                    $promedio_compra = 0;
                }

            } else {
                $promedio_compra = ($costo_unitario['producto_costo_unitario'] / $unidad_maxima['unidades']) * $unidad_form['unidades'];

            }
            // $costo_compra = $promedio_compra * $unidad_form['unidades'];

            // $promedio_venta = ($precio - $costo_compra);
            // $costo_venta = $precio * $unidad_form['unidades'];
            $utilidad = ($precio - $promedio_compra) * $cantidad_venta;

            $detalle_item = array(
                'id_venta' => $venta_id,
                'id_producto' => $id_producto,
                'precio' => $precio,
                'cantidad' => $cantidad_venta,
                'unidad_medida' => $unidad_medida_venta,
                'detalle_costo_promedio' => $promedio_compra,
                'detalle_utilidad' => $utilidad,
                'detalle_importe' => $importe,
            );

            //VOY PREPARANDO LOS DATOS PARA AGREGAR EL HISTORICO Y ACTUALIZAR EL ALMACEN
            if (!isset($cantidad_new[$id_producto]))
                $cantidad_new[$id_producto] = 0;

            if (!isset($traspasos[$id_producto][$row->local]))
                $traspasos[$id_producto][$row->local] = 0;

            $c = $this->unidades_model->convert_minimo_by_um(
                $id_producto,
                $unidad_medida_venta,
                $cantidad_venta);

            $cantidad_new[$id_producto] += $c;
            $traspasos[$id_producto][$row->local] += $c;


            // $this->db->insert('detalle_venta', $detalle_item);
            array_push($array_detalle, $detalle_item);
            if (count($inventario_existente) > 0) {

                $inventario_nuevo = array(

                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );

                $this->update_inventario($inventario_nuevo, array('id_inventario' => $inventario_existente['id_inventario']));


            } else {
                $inventario_nuevo = array(
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );
                $this->db->insert('inventario', $inventario_nuevo);
            }

        }

        foreach ($traspasos as $key => $value) {
            foreach ($traspasos[$key] as $local_id => $local_value) {
                if ($local_id != $venta_cabecera['local_id']) {
                    $result = $this->unidades_model->get_cantidad_fraccion($key, $local_value);
                    $this->traspaso_model->traspasar_productos($key, $local_id, $venta_cabecera['local_id'], array('cantidad' => $result['cantidad'], 'fraccion' => $result['fraccion']));
                }
            }
        }


        foreach ($cantidad_new as $key => $value) {
            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $venta_cabecera['local_id'],
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min - $value);

            if ($old_cantidad != NULL && $result != NULL) {
                //CREAR EL HISTORICO DE LA VENTA *************************************
                $this->historico_model->set_historico(array(
                    'producto_id' => $key,
                    'local_id' => $venta_cabecera['local_id'],
                    'usuario_id' => $this->session->userdata('nUsuCodigo'),
                    'cantidad' => $value,
                    'cantidad_actual' => $this->unidades_model->convert_minimo_um($key, $result['cantidad'], $result['fraccion']),
                    'tipo_movimiento' => "VENTA",
                    'tipo_operacion' => 'SALIDA',
                    'referencia_valor' => 'Se realizo una Venta',
                    'referencia_id' => $venta_id
                ));


                $this->inventario_model->update_producto_almacen($key, $venta_cabecera['local_id'], array(
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']));
            }
        }


        $this->db->insert_batch('detalle_venta', $array_detalle);

        if ($venta_cabecera['venta_status'] != 'COBRO') {
            if ($venta_cabecera['diascondicionpagoinput'] < 1) {
                if ($venta_cabecera['forma_pago'] == 1) {
                    $contado = array(
                        'id_venta' => $venta_id,
                        'status' => 'PagoCancelado',
                        'montopagado' => $venta_cabecera['total']
                    );
                    $this->db->insert('contado', $contado);
                } elseif ($venta_cabecera['forma_pago'] == 2) {
                    $tarjeta = array(
                        'venta_id' => $venta_id,
                        'tarjeta_pago_id' => $venta_cabecera['tipo_tarjeta'],
                        'numero' => $venta_cabecera['num_oper']
                    );
                    $this->db->insert('venta_tarjeta', $tarjeta);
                }
            } else {

                $array_cuotas = array();
                /*como vienen */
                $cuotas_maximas_de_local = $this->session->userdata('MAXIMO_CUOTAS_CREDITO');

                /*ESTA VARIABLE SE ENCARGA DE GUARDAR EL TOTAL A PAGAR DEL CLIENTE, YA RESTADO EL TOTAL DE LA VENTA MENOS
                EL PAGO INICIAL, O SUMANDO LOS MONTOS DE LAS DOS CUOTAS, YA QUE SE ESTABA GUARDANDO EL TOTAL AL CONTADO
                EN EL CAMPO dec_credito_montocuota*/
                $monto_a_pagar = 0;

                $cuotas_a_pagar = array();
                for ($i = $cuotas_maximas_de_local; $i < count($cuotas); $i++) {

                    $monto = explode(' ', $cuotas[$i]->monto);

                    if (count($monto) > 1) {

                        $monto = number_format(str_replace(',', '', $monto[1]), 2, '.', '');
                    } else {
                        $monto = number_format(str_replace(',', '', $monto[0]), 2, '.', '');
                    }
                    $cuotas[$i]->monto = $monto;
                    $cuotas_a_pagar[] = $cuotas[$i];
                    /*SUMO*/
                    $monto_a_pagar += $monto;


                }

                if (validOption("VISTA_CREDITO", 'SIMPLE', 'SIMPLE')) {
                    $monto_a_pagar = $venta['total'];
                    $cuotas = new \stdClass;
                    $cuotas->monto = $monto_a_pagar - $venta['inicial'];
                    $cuotas->isgiro = '1';
                    $cuotas->fecha_vencimiento = date('Y/m/d');
                    $cuotas->fecha_giro = date('Y/m/d');
                    $cuotas_a_pagar[] = $cuotas;
                }


                $moneda = explode("_", $venta_cabecera['id_moneda']);
                $credito = array(
                    'id_venta' => $venta_id,
                    'int_credito_nrocuota' => $venta_cabecera['nrocuota'],
                    'dec_credito_montocuota' => $monto_a_pagar,
                    'var_credito_estado' => 'PagoPendiente',
                    'dec_credito_montodebito' => 0.0,
                    'id_moneda' => $moneda[0],
                    'tasa_cambio' => $venta_cabecera['tasa_cambio'],
                );
                $this->db->insert('credito', $credito);


                /*   Insertar credito-cuotas */

                $last_id_cuota = 0;
                $index = 1;
                foreach ($cuotas_a_pagar as $cu) {
                    if ($cu->fecha_vencimiento and $cu->fecha_giro) {
                        $fecha_vencimiento = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $cu->fecha_vencimiento)));
                        $fecha_giro = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $cu->fecha_giro)));


                        $array_cuotas = array(
                            "nro_letra" => $venta_id . '-' . $index,
                            "fecha_giro" => $fecha_giro,
                            "fecha_vencimiento" => $fecha_vencimiento,
                            "monto" => $cu->monto,
                            "isgiro" => $cu->isgiro,
                            'id_venta' => $venta_id,
                        );


                        $this->db->insert('credito_cuotas', $array_cuotas);
                        $last_id_cuota = $this->db->insert_id();
                        $index++;
                    }

                }


            }
        }
        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {

            $this->db->trans_commit();
            return $venta_id;
        }

        $this->db->trans_off();

    }

    public function get_cantidad_de_documento_by_local_id($local_id, $id_documento)
    {
        $query = $this->db->select("COUNT(*) as cuenta");
        $query = $this->db->where(array('local_id' => $local_id, "id_documento" => $id_documento));
        $query = $this->db->from("venta");
        $query = $this->db->get();

        $result = $query->result_object();
        return $result[0]->cuenta;
    }

    function actualizar_venta($venta_cabecera, $detalle)
    {

        $this->db->trans_start();

        $this->db->trans_begin();

        $venta_id = $venta_cabecera['venta_id'];


        /**********QUITO DEL INVETARIO TODOS LOS ITEMS DE LA VENTA***********/

        //if ($venta_cabecera['devolver'] == 'true') {
        $sql_detalle_ingreso = $this->db->query("SELECT * FROM detalle_venta
                JOIN producto ON producto.producto_id=detalle_venta.id_producto
                LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
                LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
                JOIN venta ON venta.`venta_id`=detalle_venta.`id_venta`
                WHERE detalle_venta.id_venta='$venta_id'");

        $query_detalle_venta = $sql_detalle_ingreso->result_array();


        //  var_dump($sql_detalle_ingreso);
        for ($i = 0; $i < count($query_detalle_venta); $i++) {


            $local = $query_detalle_venta[$i]['local_id'];
            $unidad_maxima = $query_detalle_venta[$i]['unidades'];

            $producto_id = $query_detalle_venta[$i]['producto_id'];
            $unidad = $query_detalle_venta[$i]['unidad_medida'];
            $cantidad_compra = $query_detalle_venta[$i]['cantidad'];

            $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
            $inventario_existente = $sql_inventario->row_array();

            $id_inventario = $inventario_existente['id_inventario'];
            $cantidad_vieja = $inventario_existente['cantidad'];
            $fraccion_vieja = $inventario_existente['fraccion'];

            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id' ORDER BY orden");

            $unidades_producto = $query->result_array();

            foreach ($unidades_producto as $row) {
                if ($row['id_unidad'] == $unidad) {
                    $unidad_form = $row;
                }
            }


            //var_dump($query_detalle_ingreso[$i]['unidades']);
            if ($cantidad_vieja >= 1) {

                $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_venta[$i]['unidades']) + $fraccion_vieja;
            } else {
                $unidades_minimas_inventario = $fraccion_vieja;
            }

            $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;


            $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;


            $cont = 0;
            while ($suma >= $unidad_maxima) {
                $cont++;
                $suma = $suma - $unidad_maxima;
            }
            if ($cont < 1) {
                $cantidad_nueva = 0;
                $fraccion_nueva = $suma;
            } else {
                $cantidad_nueva = $cont;
                $fraccion_nueva = $suma;
            }


            $inventario_nuevo = array(
                'cantidad' => $cantidad_nueva,
                'fraccion' => $fraccion_nueva
            );
            $where = array('id_inventario' => $id_inventario);
            $this->update_inventario($inventario_nuevo, $where);

        }
        // }

        /************************************/


        $venta = array(
            'fecha' => $venta_cabecera['fecha'],
            'id_vendedor' => $venta_cabecera['id_vendedor'],

            'local_id' => $venta_cabecera['local_id'],
            'subtotal' => $venta_cabecera['subtotal'],
            'total_impuesto' => $venta_cabecera['total_impuesto'],
            'total' => $venta_cabecera['total'],
            'pagado' => $venta_cabecera['importe'],
            'vuelto' => $venta_cabecera['vuelto']


        );

        if (!empty($venta_cabecera['id_cliente'])) {
            $venta['id_cliente'] = $venta_cabecera['id_cliente'];
        }
        if (!empty($venta_cabecera['venta_status'])) {
            $venta['venta_status'] = $venta_cabecera['venta_status'];
        }
        if (!empty($venta_cabecera['condicion_pago'])) {
            $venta['condicion_pago'] = $venta_cabecera['condicion_pago'];
        }

        $this->db->where('venta_id', $venta_cabecera['venta_id']);
        $this->db->update('venta', $venta);


        $this->db->where('id_venta', $venta_id);
        $this->db->delete('detalle_venta');


        /***********COMIENZO A SUMAR EL INVENTARIO DE LA VENTA***************/
        $array_detalle = array();
        foreach ($detalle as $row) {


            $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
									   FROM inventario where id_producto=' . $row->id_producto . ' and id_local=' . $venta_cabecera['local_id']);
            $inventario_existente = $query->row_array();
            $cantidad_vieja = 0;
            $fraccion_vieja = 0;

            if (isset($inventario_existente['cantidad'])) {
                $cantidad_vieja = $inventario_existente['cantidad'];
            }
            if (isset($inventario_existente['fraccion'])) {
                $fraccion_vieja = $inventario_existente['fraccion'];
            }
            $cantidad_venta = $row->cantidad;
            $unidad_medida_venta = $row->unidad_medida;
            $id_producto = $row->id_producto;
            $precio = $row->precio;
            $importe = $row->detalle_importe;


            //  CALCLOS DE UNDIAD DE MEDIDA

            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

            $unidades_producto = $query->result_array();

            // var_dump($unidades_producto);

            $unidad_maxima = $unidades_producto[0];
            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
            //var_dump($unidad_maxima);
            //var_dump($unidad_minima);
            $unidad_form = 0;
            foreach ($unidades_producto as $um) {
                if ($um['id_unidad'] == $unidad_medida_venta) {
                    $unidad_form = $um;
                }
            }

            $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
            if ($fraccion_vieja < $total_unidades_minimas) {
                $suma_cantidades = $total_unidades_minimas - $fraccion_vieja;
            } else {
                $suma_cantidades = $fraccion_vieja - $total_unidades_minimas;
            }

            if ($suma_cantidades >= $unidad_maxima['unidades']) {

                $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                $cantidad_nueva = intval($resultado_division) - $cantidad_vieja;
                $resto_division = $suma_cantidades % $unidad_maxima['unidades'];
                $fraccion_nueva = $resto_division;
            } else {
                $cantidad_nueva = $cantidad_vieja;
                $fraccion_nueva = +$suma_cantidades;

            }
            //var_dump($unidad_medida_venta);
            //var_dump($unidad_maxima['id_unidad']);

            if ($unidad_medida_venta == $unidad_maxima['id_unidad']) {
                $cantidad_nueva = $cantidad_vieja - $cantidad_venta;
                $fraccion_nueva = $fraccion_vieja;
            }
            if ($unidad_medida_venta == $unidad_minima['id_unidad']) {
                // var_dump($unidad_minima['id_unidad']);
                //   $suma_cantidades= $fraccion_vieja-$cantidad_venta;
                if ($suma_cantidades >= $unidad_maxima['unidades']) {

                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division) - $cantidad_vieja;
                    $resto_division = $suma_cantidades % $unidad_maxima['unidades'];
                    $fraccion_nueva = $resto_division;
                } else {
                    if ($cantidad_vieja > 0) {
                        $cantidad_nueva = $cantidad_vieja;
                        $fraccion_nueva = $suma_cantidades;
                    } else {

                        if (count($unidades_producto) > 1) {
                            $cantidad_nueva = 0;
                            $fraccion_nueva = $cantidad_venta;
                        } else {

                            $cantidad_nueva = $cantidad_venta;
                            $fraccion_nueva = 0;
                        }
                    }


                }

            }

            /********************CALCULAO DE UTILIDAD****/


            ///busco el costo unitario del producto

            $query_costo_u = $this->db->query("select producto_id,  producto_costo_unitario from producto
  WHERE producto_id=" . $id_producto . " ");
            $costo_unitario = $query_costo_u->row_array();

            if ($costo_unitario['producto_costo_unitario'] == null or $costo_unitario['producto_costo_unitario'] == '0') {

                $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
AND unidades_has_producto.producto_id=detalleingreso.id_producto
WHERE detalleingreso.id_producto=" . $id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $id_producto . ")  ");

                $result_ingreso = $query_compra->result_array();
                if (count($result_ingreso) > 0) {

                    $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];
                    $promedio_compra = ($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_form['unidades'];
                } else {
                    $promedio_compra = 0;
                }

            } else {
                $promedio_compra = ($costo_unitario['producto_costo_unitario'] / $unidad_maxima['unidades']) * $unidad_form['unidades'];

            }

            $utilidad = ($precio - $promedio_compra) * $cantidad_venta;

            $detalle_item = array(
                'id_venta' => $venta_id,
                'id_producto' => $id_producto,
                'precio' => $precio,
                'cantidad' => $cantidad_venta,
                'unidad_medida' => $unidad_medida_venta,
                'detalle_costo_promedio' => $promedio_compra,
                'detalle_utilidad' => $utilidad,
                'detalle_importe' => $importe,
            );

            // $this->db->insert('detalle_venta', $detalle_item);
            array_push($array_detalle, $detalle_item);

            if (count($inventario_existente) > 0) {

                $inventario_nuevo = array(
                    //  'id_inventario' => $inventario_existente['id_inventario'],
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );


                //$this->db->update('inventario', $inventario_nuevo, 'id_inventario');
                $this->update_inventario($inventario_nuevo, array('id_inventario' => $inventario_existente['id_inventario']));


            } else {
                $inventario_nuevo = array(
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );


                $this->db->insert('inventario', $inventario_nuevo);

            }


        }

        $this->db->insert_batch('detalle_venta', $array_detalle);
        if ($venta_cabecera['diascondicionpagoinput'] < 1) {

            if ($venta_cabecera['devolver'] == 'false') {
                $this->db->delete('credito', array('id_venta' => $venta_id));
            }
            $contado = array(
                'id_venta' => $venta_id,
                'status' => 'PagoCancelado',
                'montopagado' => $venta_cabecera['total'],
            );
            $this->db->where('id_venta', $venta_id);

            $select_contado = $this->db->select('*')->from('contado')->where('id_venta', $venta_id)->get()->row_array();
            if (count($select_contado) > 0) {
                $this->db->where('id_venta', $venta_id);
                $this->db->update('contado', $contado);
            } else {
                $this->db->insert('contado', $contado);
            }
        } else {
            if ($venta_cabecera['devolver'] == 'false') {
                $this->db->delete('contado', array('id_venta' => $venta_id));
            }
            $credito = array(
                'id_venta' => $venta_id,
                'int_credito_nrocuota' => $venta_cabecera['nrocuota'],
                'dec_credito_montocuota' => $venta_cabecera['montxcuota'],
                'var_credito_estado' => 'PagoPendiente',
                'dec_credito_montodebito' => 0.0
            );

            $select_contado = $this->db->select('*')->from('credito')->where('id_venta', $venta_id)->get()->row_array();
            if (count($select_contado) > 0) {
                $this->db->where('id_venta', $venta_id);
                $this->db->update('credito', $credito);
            } else {
                $this->db->insert('credito', $credito);;
            }


        }
        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {


            $this->db->trans_commit();
            return $venta_id;

        }

        $this->db->trans_off();

    }


    function insertar_cronogramaPago($lista_cronogramapago)
    {

        $data = array();

        foreach ($lista_cronogramapago as $row) {
            $list_cp = array(
                'dat_cronpago_fecinicio' => date("Y-m-d", strtotime($row->FechaInicio)),
                'dat_cronpago_fecpago' => date("Y-m-d", strtotime($row->FechaFin)),
                'dec_cronpago_pagocuota' => $row->monto_cuota,
                'dec_cronpago_pagorecibido' => 0.0,
                'int_cronpago_nrocuota' => $row->NroCuota,
                'nVenCodigo' => $row->id_venta
            );
            //$this->db->insert('cronogramapago',$list_cp);
            array_push($data, $list_cp);
        }

        if ($this->db->insert_batch('cronogramapago', $data)) {
            return true;
        } else {
            return false;
        }

    }

    function update_cronogramaPago($lista_cronogramapago, $sinsuma = false)
    {

        $this->db->trans_start();


        foreach ($lista_cronogramapago as $row) {

            $venta_id = $row->id_venta;
            $queryCredito = $this->get_cronograma_by_venta($venta_id);
            $total_venta = $queryCredito[0]['dec_cronpago_pagocuota'];


            if (!$sinsuma) {

                if (($queryCredito[0]['dec_cronpago_pagorecibido'] + $row->cuota) >= $total_venta) {
                    $list_cp = array(
                        'dec_cronpago_pagorecibido' => $queryCredito[0]['dec_cronpago_pagocuota']
                    );

                } else {
                    $list_cp = array(
                        'dec_cronpago_pagorecibido' => $queryCredito[0]['dec_cronpago_pagorecibido'] + $row->cuota
                    );

                }
            } else {
                $list_cp = array(
                    'dec_cronpago_pagocuota' => $row->monto_cuota
                );
            }


            $where = array('nVenCodigo' => $venta_id);

            $this->db->where($where);
            $this->db->update('cronogramapago', $list_cp);

        }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {

            return true;
        }

        $this->db->trans_off();

    }

    function updateCredito($lista_cronogramapago)
    {

        $this->db->trans_start(true);

        $this->db->trans_begin();


        foreach ($lista_cronogramapago as $row) {

            $where = array('id_venta' => $row->id_venta);
            $queryCredito = $this->getCredito($where);
            $total_venta = $queryCredito['int_credito_nrocuota'] * $queryCredito['dec_credito_montocuota'];

            if (($queryCredito['dec_credito_montodebito'] + $row->cuota) >= $total_venta) {
                $list_cp = array(
                    'var_credito_estado' => 'PagoCancelado',
                    'dec_credito_montodebito' => $queryCredito['dec_credito_montodebito'] + $row->cuota
                );

            } else {
                $list_cp = array(
                    'dec_credito_montodebito' => $queryCredito['dec_credito_montodebito'] + $row->cuota
                );


            }
            $this->db->where($where);
            $this->db->update('credito', $list_cp);

        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {

            return true;
        }

        $this->db->trans_off();

    }

    function getCredito($where)
    {
        $this->db->where($where);
        $query = $this->db->get('credito');
        return $query->row_array();

    }


    function anular_venta($id, $locales)
    {

        $this->load->model('historico/historico_model');
        $this->load->model('inventario/inventario_model');
        $this->load->model('unidades/unidades_model');

        $this->db->trans_start();


        $data = array();
        $cantidad_historico = 0;

        for ($j = 0; $j < count($id); $j++) {

            $query_detalle_venta = $this->get_detalle_by_venta_id($id[$j]);


            if (count($query_detalle_venta) > 0) {

                for ($i = 0; $i < count($query_detalle_venta); $i++) {


                    $local = $locales[$j];
                    $producto_id = $query_detalle_venta[$i]->id_producto;
                    $unidad = $query_detalle_venta[$i]->unidad_medida;
                    $cantidad_compra = $query_detalle_venta[$i]->cantidad;

                    /* busco la existencia del producto en la tabla producto almacen*/
                    $producto_almacen = $this->inventario_model->get_by_existencia($producto_id, $local);
                    $cantidad_vieja = $producto_almacen['cantidad'];
                    $fraccion_vieja = $producto_almacen['fraccion'];

                    /* convierto todo a unidades minimas de la venta */
                    $unidades_minimas_detalle = $this->unidades_model->convert_minimo_by_um($producto_id, $unidad, $cantidad_compra);

                    /* convierto todo a unidades minimas de lo que hay en inventario actualmente */
                    $unidades_minimas_inventario = $this->unidades_model->convert_minimo_um($producto_id, $cantidad_vieja, $fraccion_vieja);

                    /* total de unidades minimas*/
                    $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;

                    /*obtengo la cantidad y la fraccion final*/
                    $totales = $this->unidades_model->get_cantidad_fraccion($producto_id, $suma);

                    $inventario_nuevo = array(
                        'cantidad' => $totales['cantidad'],
                        'fraccion' => $totales['fraccion']
                    );

                    /*esta variable va sumando todas las unidades minimas del producto*/
                    $cantidad_historico += $unidades_minimas_detalle;


                    if (isset($query_detalle_venta[$i + 1]) && $producto_id == $query_detalle_venta[$i + 1]->id_producto) {

                        //esto es una bandera para que no haga el insert sobre la tabla historico
                        //sabiendo ya que los datos vienen ordenados por producto

                    } else {

                        //HAGO EL REGISTRO DE HISTORICO
                        $this->historico_model->set_historico(array(
                            'producto_id' => $producto_id,
                            'local_id' => $local,
                            'cantidad' => $cantidad_historico,
                            'cantidad_actual' => $this->unidades_model->convert_minimo_um($producto_id, $inventario_nuevo['cantidad'], $inventario_nuevo['fraccion']),
                            'tipo_movimiento' => "ANULACION",
                            'tipo_operacion' => 'ENTRADA',
                            'referencia_valor' => 'Anulacion de Ventas',
                            'referencia_id' => $id[$j]
                        ));
                        /* vuelvo a reiniciar la variable*/
                        $cantidad_historico = 0;
                    }

                    /*actualizo la tabla producto almacen*/
                    $this->inventario_model->update_producto_almacen($producto_id, $local, $inventario_nuevo);


                }

                $arreglo = array(
                    'id_venta' => $id[$j],
                    'var_venanular_descripcion' => $this->input->post('motivo'),
                    'nUsuCodigo' => $this->session->userdata('nUsuCodigo')

                );
                $this->db->insert('venta_anular', $arreglo);

                $fecha = $this->db->get_where('venta', array('venta_id' => $id[$j]))->row();
                $campos = array('venta_status' => 'ANULADO', 'fecha' => $fecha->fecha);

                $condicion = array('venta_id' => $id[$j]);
                $data['resultado'] = $this->update_venta($condicion, $campos);
            }

        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            //  $this->db->trans_rollback();
            return false;
        } else {

            return true;
        }

        $this->db->trans_off();


    }

    function getUtilidades($select, $where, $retorno, $group)
    {

        $this->db->select($select);
        $this->db->from('detalle_venta');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->join('producto', 'producto.producto_id=detalle_venta.id_producto');
        $this->db->join('cliente', 'venta.id_cliente=cliente.id_cliente');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('proveedor', 'producto.producto_proveedor=proveedor.id_proveedor');

        $this->db->where($where);
        if ($group != false) {
            $this->db->group_by($group);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($retorno == "RESULT") {

            return $query->result();

        }
    }


    function getDetalleVenta($retorno, $order, $where)
    {

        $this->db->select('unidades.*,detalle_venta.*,producto.*,venta.fecha,documento_Serie, documento_Numero,venta.venta_status AS estado,local_nombre,
         usuario.nombre, moneda.id_moneda, moneda.nombre as nombre_moneda, moneda.simbolo, moneda.pais, moneda.tasa_soles, moneda.ope_tasa,
         moneda.status_moneda');
        $this->db->from('detalle_venta');
        $this->db->join('producto', 'producto.producto_id=detalle_venta.id_producto');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->join('unidades', 'unidades.id_unidad=detalle_venta.unidad_medida');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento', 'left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor', 'left');
        $this->db->join('moneda', 'venta.id_moneda=moneda.id_moneda', 'left');
        $this->db->where($where);
        $this->db->order_by($order);
        $query = $this->db->get();
        if ($retorno == "ARRAY") {
            return $query->result_array();
        }


    }


    function get_cronograma_by_venta($venta)
    {

        $query = $this->db->where('nVenCodigo', $venta);
        $query = $this->db->get('v_cronogramapago');
        return $query->result_array();

    }

    function codigo_barra_activo()
    {
        $this->db->where('nombre_columna', 'producto_codigo_barra');
        $this->db->where('activo', '1');
        $this->db->from('columnas');
        return $this->db->count_all_results();
    }

    function get_garantes()
    {
        //$query = $this->db->where('nVenCodigo', $venta);
        $query = $this->db->get('garante');
        return $query->result_array();

    }

    function update_inventario($campos, $wheres)
    {
        $this->db->trans_start();
        $this->db->where($wheres);
        $this->db->update('inventario', $campos);
        $this->db->trans_complete();

    }

///////////////////////////////////////////////////////////////////////////////////////////
    function update_venta($condicion, $campos)
    {

        $this->db->trans_start();
        $this->db->where($condicion);
        $this->db->update('venta', $campos);

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }


    function buscar_NroVenta_credito($nroVenta)
    {
        //   $lista = explode("-", $nroVenta);
        $this->db->select('v.venta_id,c.int_credito_nrocuota,c.dec_credito_montocuota,cl.razon_social');
        $this->db->from('venta v');
        $this->db->join('credito c', 'v.venta_id=c.id_venta');
        $this->db->join('cliente cl', 'cl.id_cliente = v.id_cliente');
        $this->db->where('v.venta_id', $nroVenta);

        $query = $this->db->get();

        //  echo $this->db->last_query();
        return $query->result();
    }

    function get_venta_by_status($estatus, $where)
    {
        $this->db->select('venta.*,cliente.*,local.*,condiciones_pago.*,documentos.*,usuario.*,
         moneda.id_moneda, moneda.nombre as nombre_moneda, moneda.simbolo, moneda.pais, moneda.tasa_soles, moneda.ope_tasa,
         moneda.status_moneda');
        $this->db->from('venta');

        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $this->db->join('documentos', 'documentos.id_doc=venta.id_documento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('moneda', 'moneda.id_moneda=venta.id_moneda');
        $this->db->where($where);
        $this->db->where_in('venta_status', $estatus);

        $this->db->order_by('venta.fecha ', 'desc');
        $query = $this->db->get();

        return $query->result();
    }

    function get_venta_para_devoluciones()
    {
        $this->db->select('*');
        $this->db->from('venta');

        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $this->db->join('documentos', 'documentos.id_doc=venta.id_documento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->where("venta_status = 'COMPLETADO' or venta_status='DEVUELTO' or venta_status='COBRO'");
        $this->db->order_by('venta.fecha ', 'desc');
        $query = $this->db->get();

        return $query->result();
    }

    function get_detalle_by_venta_id($venta_id)
    {
        $this->db->select('*');
        $this->db->from("detalle_venta");
        $this->db->where(array('id_venta' => $venta_id));
        $this->db->order_by('id_producto');
        $query = $this->db->get();
        return $query->result();
    }

    function get_ventas_by($condicion)
    {
        $this->db->select('*,usuario.identificacion as usuario_identificacion,cliente.identificacion as cliente_identificacion, cliente.direccion as cliente_direccion, correlativos.serie as serie');
        $this->db->from('venta');
        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('moneda', 'moneda.id_moneda=venta.id_moneda');
        $this->db->join('garante', 'venta.dni_garante=garante.dni', 'left');
        $this->db->join('documentos', 'venta.id_documento=documentos.id_doc');
        $this->db->join('correlativos', 'venta.id_documento=correlativos.id_documento and venta.local_id = correlativos.id_local');
        $this->db->order_by('venta.venta_id', 'desc');
        $this->db->where($condicion);
        $query = $this->db->get();
        //var_dump($this->db->last_query());die;
        return $query->result();
    }


    function get_total_ventas_by_date($condicion)
    {
        $sql = "SELECT SUM(total) as suma FROM `venta` WHERE venta_status = 'COMPLETADO' AND DATE(fecha)='" . $condicion . "'";
        $query = $this->db->query($sql);
        return $query->row_array();

    }


    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $group = false,
                             $order = false, $retorno = false)
    {


        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);


        }

        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);

        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }


    function get_ventas_by_cliente($condicion)
    {
        $this->db->select('SUM(subtotal) AS sub_total,SUM(total_impuesto) AS impuesto,SUM(total) AS totalizado,
         a.*,b.*,c.*,d.*,e.*,f.*,m.*');
        $this->db->from('venta a');
        $this->db->join('cliente b', 'b.id_cliente=a.id_cliente');
        $this->db->join('local c', 'c.int_local_id=a.local_id');
        $this->db->join('condiciones_pago d', 'd.id_condiciones=a.condicion_pago');
        $this->db->join('documento_venta e', 'e.id_tipo_documento=a.id_documento');
        $this->db->join('usuario f', 'f.nUsuCodigo=a.id_vendedor');
        $this->db->join('moneda m', 'm.id_moneda=a.id_moneda');
        $this->db->group_by('a.id_cliente');
        $this->db->where($condicion);
        $query = $this->db->get();
        return $query->result();
    }

//////////////////////////////////////////////////////////////////////////////////////

    function select_rpt_venta($tipo, $fecha, $anio, $mes, $forma)
    {
        $this->db->select('nVenCodigo,
						cliente.var_cliente_nombre,
						usuario.nombre,
						dat_venta_fecregistro,
						dec_venta_montoTotal,
						documento_venta.documento_Serie,
						documento_venta.documento_Numero,
						nombre_numero_documento,
						var_venta_estado,
						l.var_local_nombre,
						ct.var_constante_descripcion as cFormapago');
        $this->db->from('venta');
        $this->db->join('cliente', 'cliente.nCliCodigo=venta.nCliCodigo');
        $this->db->join('documento_venta', 'documento_venta.nTipDocumento=venta.nTipDocumento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.nUsuCodigo');
        $this->db->join('constante ct', 'ct.int_constante_valor=venta.int_venta_formaPago');
        $this->db->join('local l', 'l.int_local_id=venta.int_venta_local');
        $this->db->where('venta.var_venta_estado != ', 2);
        $this->db->where('ct.int_constante_clase', 3);

        switch ($tipo) {
            case 1:
                $this->db->where('venta.int_venta_formaPago', $forma);
                $this->db->where('DATE(venta.dat_venta_fecregistro)', $fecha);
                break;
            case 2:
                $this->db->where('DATE(venta.dat_venta_fecregistro)', $fecha);
                break;
            case 3:
                $this->db->where('venta.int_venta_formaPago', $forma);
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                $this->db->where('MONTH(venta.dat_venta_fecregistro)', $mes);
                break;
            case 4:
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                $this->db->where('MONTH(venta.dat_venta_fecregistro)', $mes);
                break;
            case 5:
                $this->db->where('venta.int_venta_formaPago', $forma);
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                break;
            case 6:
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                break;
        }
        $query = $this->db->get();
        return $query->result();
    }


    function select_ventas_credito()
    {
        $this->db->select('*');
        $this->db->from('venta');
        $this->db->join('credito', 'credito.id_venta=venta.venta_id');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $query = $this->db->get();
        return $query->result();
    }

    function select_venta_estadocuenta($condicion, $order)
    {
        $consulta = $this->db->query("SELECT
  `v`.`venta_id`              AS `Venta_Id`,
  `c`.`id_cliente`            AS `Cliente_Id`,
  `c`.`razon_social`          AS `Cliente`,
  `p`.`nombre`                AS `Personal`,
  CAST(`v`.`fecha` AS DATE)   AS `FechaReg`,
  `v`.`total`                 AS `MontoTotal`,
  `moneda`.`simbolo`          AS `Simbolo`,
  `cd`.`fecha_cancelado`      AS `FechaCancelado`,
   `local`.`local_nombre`     AS `local`,
   maxfecha.maximo  AS fecha_ultimo,
(`v`.`inicial`+IFNULL((SELECT SUM(`cd`.`dec_credito_montodebito`) FROM `credito` `cd` WHERE (`cd`.`id_venta` = `v`.`venta_id`)),0.00)) AS `MontoCancelado`,
  (`v`.`total` -`v`.`inicial`- IFNULL((SELECT SUM(`cd`.`dec_credito_montodebito`) FROM `credito` `cd` WHERE (`cd`.`id_venta` = `v`.`venta_id`)),0.00)) AS `SaldoPendiente`,
  CONCAT(`correlativos`.`serie`,'-',`v`.`venta_id`) AS `NroVenta`,
  `v`.`id_documento` AS `TipoDocumento`,
  IFNULL((SELECT `cd`.`var_credito_estado` FROM `credito` `cd` WHERE (`cd`.`id_venta` = `v`.`venta_id`)),'') AS `Estado`,
  `cond`.`nombre_condiciones` AS `FormaPago`
FROM (((`venta` `v`
      JOIN `cliente` `c`
        ON ((`c`.`id_cliente` = `v`.`id_cliente`)))
    JOIN `usuario` `p`
      ON ((`p`.`nUsuCodigo` = `v`.`id_vendedor`)))
      LEFT JOIN `credito` `cd`
     ON ((`cd`.`id_venta` = `v`.`venta_id`))
   JOIN `condiciones_pago` `cond`
     ON ((`cond`.`id_condiciones` = `v`.`condicion_pago`))
      JOIN `moneda`
     ON ((`moneda`.`id_moneda` = `v`.`id_moneda`))
     JOIN `local` `local`
     ON ((`local`.`int_local_id` = `v`.`local_id`)))
     JOIN `correlativos`
     ON ((`local`.`int_local_id` = `correlativos`.`id_local`) and (`v`.`id_documento` = `correlativos`.`id_documento`))
      LEFT JOIN
    (SELECT id_venta, MAX(ultimo_pago) AS maximo
    FROM credito_cuotas
    GROUP BY id_venta) maxfecha
    ON cd.id_venta = maxfecha.id_venta
WHERE (((SELECT
           `condiciones_pago`.`dias`
         FROM `condiciones_pago`
         WHERE (`v`.`condicion_pago` = `condiciones_pago`.`id_condiciones`)) > 0)
       ) AND " . $condicion . " " . $order . " ");

        // $query = $this->db->get();
        return $consulta->result();
    }


    function estadistica_semanaactual($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFWEEK(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta, venta.`venta_status`
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta` WHERE ";
        $sql .= $condicion;
        $sql .= " and YEARWEEK (fecha)= YEARWEEK(CURDATE()) ";
        if ($group != false) {
            $sql .= $group;
        }
        $sql .= "ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();

    }

    function condicion_pago_semanaactual($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFWEEK(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta, condiciones_pago.`nombre_condiciones`, venta.condicion_pago
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta`
 JOIN condiciones_pago ON condiciones_pago.`id_condiciones`=venta.`condicion_pago`
 WHERE ";
        $sql .= $condicion;
        $sql .= " and YEARWEEK (fecha)= YEARWEEK(CURDATE()) ";

        if ($group != false) {
            $sql .= " GROUP BY `ciclo`,venta.`condicion_pago` ";
        }
        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();
    }

    function estadistica_annoactual($condicion, $group, $fecha)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, MONTHNAME(fecha) AS mes,
MONTH(fecha) AS ciclo, count(venta.venta_id) as numero_venta, venta.`venta_status`
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta` WHERE ";
        $sql .= $condicion;
        $sql .= " and YEAR(fecha)='" . $fecha . "' ";
        if ($group != false) {
            $sql .= $group;
        }

        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();
    }

    function condicion_pago_annoactual($condicion, $group, $fecha)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, MONTHNAME(fecha) AS mes,
MONTH(fecha) AS ciclo, count(venta.venta_id) as numero_venta, condiciones_pago.`nombre_condiciones`, venta.condicion_pago
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta`
 JOIN condiciones_pago ON condiciones_pago.`id_condiciones`=venta.`condicion_pago`
 WHERE ";
        $sql .= $condicion;

        $sql .= " and YEAR(fecha)='" . $fecha . "' ";

        if ($group != false) {
            $sql .= " GROUP BY `ciclo`,venta.`condicion_pago` ";
        }
        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();
    }


    function estadistica_porfecha($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFMONTH(fecha)  AS ciclo,
MONTH(fecha) AS mes,DATE_FORMAT(fecha,'%Y-%m-%d') as fecha,YEAR(fecha) as anio,
count(venta.venta_id) as numero_venta , venta.`venta_status`
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta` WHERE ";
        $sql .= $condicion;
        if ($group != false) {
            $sql .= $group;
        }
        $sql .= " ORDER BY anio, mes, ciclo ASC";


        $sql = $this->db->query($sql);
        return $sql->result_array();

    }

    function condicion_pago_porfecha($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFMONTH(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta, condiciones_pago.`nombre_condiciones`, venta.condicion_pago,
MONTH(fecha) AS mes,DATE_FORMAT(fecha,'%Y-%m-%d') as fecha,YEAR(fecha) as anio
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta`
 JOIN condiciones_pago ON condiciones_pago.`id_condiciones`=venta.`condicion_pago`
 WHERE ";
        $sql .= $condicion;
        if ($group != false) {
            $sql .= $group;
        }
        $sql .= " ORDER BY anio, mes, ciclo ASC";

        $sql = $this->db->query($sql);
        return $sql->result_array();
    }


    function estadistica_mesactual($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFMONTH(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta , venta.`venta_status`
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta` WHERE ";
        $sql .= $condicion;
        $sql .= " AND MONTH (fecha)= MONTH(CURDATE() ) AND YEAR (fecha)=YEAR(CURDATE())";
        if ($group != false) {
            $sql .= $group;
        }
        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();

    }

    function condicion_pago_mesactual($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFMONTH(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta, condiciones_pago.`nombre_condiciones`, venta.condicion_pago
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta`
 JOIN condiciones_pago ON condiciones_pago.`id_condiciones`=venta.`condicion_pago`
 WHERE ";
        $sql .= $condicion;
        $sql .= " AND MONTH (fecha)= MONTH(CURDATE() ) AND YEAR (fecha)=YEAR(CURDATE())";
        if ($group != false) {
            $sql .= " GROUP BY `ciclo`,venta.`condicion_pago` ";
        }
        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();
    }

    function estadistica_mesanterior($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFMONTH(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta, venta.`venta_status`
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta` WHERE ";
        $sql .= $condicion;
        $sql .= " and MONTH(fecha) = MONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) ";
        if ($group != false) {
            $sql .= $group;
        }
        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();

    }

    function condicion_pago_mesanterior($condicion, $group)
    {
        $sql = "SELECT SUM(detalle_importe) AS total_venta, SUM(detalle_utilidad) AS total_utilidad, DAYOFMONTH(fecha)  AS ciclo,
count(venta.venta_id) as numero_venta, condiciones_pago.`nombre_condiciones`, venta.condicion_pago
FROM (`detalle_venta`) JOIN `venta` ON `venta`.`venta_id`=`detalle_venta`.`id_venta`
 JOIN condiciones_pago ON condiciones_pago.`id_condiciones`=venta.`condicion_pago`
 WHERE ";
        $sql .= $condicion;
        $sql .= " and MONTH(fecha) = MONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) ";
        if ($group != false) {
            $sql .= " GROUP BY `ciclo`,venta.`condicion_pago` ";
        }
        $sql .= " ORDER BY `total_utilidad` DESC";

        $sql = $this->db->query($sql);
        return $sql->result_array();
    }

    function obtener_gotoxy($doc)
    {
        if ($doc == 'FACTURA') {

            $where = array(
                "documento" => $doc
            );

            $this->db->select('*');
            $this->db->from('gotoxy_imp');
            $this->db->where($where);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    function get_totales_pagos_pendientes($data)
    {
        $consulta = "
            SELECT
                SUM(v.total) AS total_venta,
                SUM(v.inicial + cd.dec_credito_montodebito) AS total_abonado,
                SUM(v.total - v.inicial - cd.dec_credito_montodebito) AS total_deuda
            FROM
                venta v
                    JOIN
                credito cd ON cd.id_venta = v.venta_id
            WHERE
                cd.var_credito_estado = 'PagoPendiente'
                AND v.venta_status = 'COMPLETADO'
                AND v.condicion_pago = 2
        ";

        if(isset($data['local_id']))
            $consulta .= " AND v.local_id = ".$data['local_id'];

        if(isset($data['cliente_id']))
            $consulta .= " AND v.id_cliente = ".$data['cliente_id'];

        if(isset($data['moneda_id']))
            $consulta .= " AND v.id_moneda = ".$data['moneda_id'];

        if(isset($data['vence_deuda'])){

            $consulta .= " AND
            (SELECT
                DATEDIFF(CURDATE(), (cc.fecha_vencimiento))
            FROM
                credito_cuotas AS cc
            WHERE
                cc.ispagado = 0
                AND cc.id_venta = v.venta_id
            LIMIT 1) = 0
            ";
        }

        return $this->db->query($consulta)->row();
    }

    function get_pagos_pendientes($data)
    {
        $consulta = "
            SELECT
                v.venta_id AS Venta_id,
                c.id_cliente AS Cliente_Id,
                c.razon_social AS Cliente,
                c.ruc AS ruc,
                c.identificacion AS indentificacion,
                p.nombre AS Personal,
                v.fecha AS FechaReg,
                v.total AS MontoTotal,
                moneda.simbolo AS Simbolo,
                local.local_nombre AS local,
                v.inicial + cd.dec_credito_montodebito AS MontoCancelado,
                v.total - v.inicial - cd.dec_credito_montodebito AS SaldoPendiente,
                IFNULL(correlativos.serie, '0000') AS serie,
                IFNULL(correlativos.correlativo, v.venta_id) AS correlativo,
                d.id_doc AS TipoDocumento,
                d.des_doc AS Documento,
                (SELECT
                        DATEDIFF(CURDATE(), (cc.fecha_vencimiento))
                    FROM
                        credito_cuotas AS cc
                    WHERE
                        cc.ispagado = 0
                        AND cc.id_venta = v.venta_id
                    LIMIT 1) AS atraso,
                (SELECT
                        COUNT(*)
                    FROM
                        credito_cuotas AS cc
                    WHERE
                        DATEDIFF(CURDATE(), (cc.fecha_vencimiento)) > 0 AND
                        cc.ispagado = 0 AND cc.id_venta = v.venta_id) AS cuotas_atrasadas,
                (SELECT
                        COUNT(cc.id_credito_cuota)
                    FROM
                        credito_cuotas AS cc
                    WHERE
                        cc.id_venta = v.venta_id) AS nro_cuotas
            FROM
                venta v
                    JOIN
                cliente c ON c.id_cliente = v.id_cliente
                    JOIN
                credito cd ON cd.id_venta = v.venta_id
                    JOIN
                usuario p ON p.nUsuCodigo = v.id_vendedor
                    JOIN
                documentos d ON v.id_documento = d.id_doc
                    JOIN
                moneda ON moneda.id_moneda = v.id_moneda
                    JOIN
                local local ON local.int_local_id = v.local_id
                    LEFT JOIN
                correlativos ON local.int_local_id = correlativos.id_local and v.id_documento = correlativos.id_documento
            WHERE
                cd.var_credito_estado = 'PagoPendiente'
                AND v.venta_status = 'COMPLETADO'
                AND v.condicion_pago = 2
        ";

        if(isset($data['local_id']))
            $consulta .= " AND v.local_id = ".$data['local_id'];

        if(isset($data['cliente_id']))
            $consulta .= " AND v.id_cliente = ".$data['cliente_id'];

        if(isset($data['moneda_id']))
            $consulta .= " AND v.id_moneda = ".$data['moneda_id'];

        if(isset($data['vence_deuda']) && $data['vence_deuda'] == 1){
            $consulta .= " AND
            (SELECT
                DATEDIFF(CURDATE(), (cc.fecha_vencimiento))
            FROM
                credito_cuotas AS cc
            WHERE
                cc.ispagado = 0
                AND cc.id_venta = v.venta_id
            LIMIT 1) = 0
            ";
        }

        $consulta .=  " order by v.fecha desc , v.local_id desc";

        return $this->db->query($consulta)->result();
    }

    function obtener_venta($id_venta)
    {
        $query = $this->db->query("
            select
                v.venta_id,
                v.total as montoTotal,
                v.subtotal as subTotal,
                v.total_impuesto as impuesto,
                v.pagado,
                v.vuelto,
                v.id_moneda,
                pd.producto_nombre as nombre,
                pd.producto_cualidad,
                pd.producto_id as producto_id,
                tr.cantidad as cantidad,
                tr.precio as preciounitario,
                tr.detalle_importe as importe,
                v.fecha as fechaemision,
                cre.int_credito_nrocuota,
                cre.dec_credito_montocuota,
                p.nombre as vendedor,
                p.username as username,
                documentos.des_doc as descripcion,
                IFNULL(correlativos.serie, '0000') AS serie,
                IFNULL(correlativos.correlativo, v.venta_id) AS numero,
                correlativos.id_documento,
                c.razon_social as cliente,
                c.id_cliente as cliente_id,
                cp.id_condiciones,
                c.categoria_precio,
                cp.nombre_condiciones,
                v.venta_status,
                v.inicial,
                u.id_unidad,
                u.nombre_unidad,
                u.abreviatura,
                i.porcentaje_impuesto,
                up.unidades,
                up.orden,
                m.simbolo,
                local.local_nombre,
                local.int_local_id,
                (select
                        config_value
                    from
                        configuraciones
                    where
                        config_key = 'EMPRESA_NOMBRE') as RazonSocialEmpresa,
                (select
                        config_value
                    from
                        configuraciones
                    where
                        config_key = 'EMPRESA_DIRECCION') as DireccionEmpresa,
                (select
                        config_value
                    from
                        configuraciones
                    where
                        config_key = 'EMPRESA_TELEFONO') as TelefonoEmpresa,
                (select
                        abreviatura
                    from
                        unidades_has_producto
                            join
                        unidades ON unidades.id_unidad = unidades_has_producto.id_unidad
                    where
                        unidades_has_producto.producto_id = pd.producto_id
                    order by orden desc
                    limit 1) as unidad_minima
            from
                venta as v
                    inner join
                usuario p ON p.nUsuCodigo = v.id_vendedor
                    left join
                correlativos ON (v.local_id = correlativos.id_local) and (v.id_documento = correlativos.id_documento)
                    inner join
                detalle_venta tr ON tr.id_venta = v.venta_id
                    inner join
                documentos ON documentos.id_doc = v.id_documento
                    inner join
                cliente c ON c.id_cliente = v.id_cliente
                    inner join
                producto pd ON pd.producto_id = tr.id_producto
                    inner join
                condiciones_pago cp ON cp.id_condiciones = v.condicion_pago
                    inner join
                unidades u ON u.id_unidad = tr.unidad_medida
                    inner join
                moneda m ON m.id_moneda = v.id_moneda
                    inner join
                impuestos i ON i.id_impuesto = pd.producto_impuesto
                    inner join
                unidades_has_producto up ON up.producto_id = pd.producto_id and up.id_unidad = tr.unidad_medida
                    left join
                local ON local.int_local_id = v.local_id
                    left join
                credito cre ON cre.id_venta = v.venta_id
            where
                v.venta_id = ".$id_venta."
            order by 1
        ");

        //echo $this->db->last_query();
        return $query->result_array();
    }

    public function get_numero_guia_remision_by_venta_id($venta_id, $local_id)
    {
        $this->db->select("COUNT(*) as cuenta");
        $this->db->from("venta");
        $this->db->where(array("venta_id <" => $venta_id));
        $this->db->where(array("local_id" => $local_id));
        $query = $this->db->get();
        $resultado = $query->result();
        if ($resultado) {
            return $resultado[0]->cuenta;
        }
        return 0;
    }

    public function get_numero_factura_by_venta_id($venta_id, $local_id)
    {
        $this->db->select("COUNT(*) as cuenta");
        $this->db->from("venta");
        $this->db->where(array("venta_id <" => $venta_id));
        $this->db->where(array("local_id" => $local_id));
        $this->db->where(array("id_documento" => 1));
        $query = $this->db->get();
        $resultado = $query->result();
        if ($resultado) {
            return $resultado[0]->cuenta;
        }
        return 0;
    }

    /*public function update_pago_Cuota_Credito($cuota_id)
    {
        $data = array(
            'ispagado' => 1,
            'fecha_pago'=>date('Y-m-d H:i:s')
        );
        $this->db->trans_start(true);
        $this->db->where('nro_letra', $cuota_id);
        $this->db->update('credito_cuotas', $data);
        $this->db->trans_complete();
    }*/

    public function update_pago_total_cuotas($venta_id)
    {
        $query = $this->db->query("UPDATE credito c SET c.var_credito_estado='PagoCancelado' WHERE (SELECT COUNT(*) FROM credito_cuotas cc WHERE cc.ispagado=0 AND cc.id_venta=" . $venta_id . ")=0 AND c.id_venta=" . $venta_id . " ");
    }

    public function update_pago_cuotas_giro($venta_id, $montodescontar)
    {
        $query = $this->db->query("UPDATE credito c SET c.dec_credito_montocuota=c.dec_credito_montocuota-" . $montodescontar . " WHERE c.id_venta=" . $venta_id . " ");
    }
}
