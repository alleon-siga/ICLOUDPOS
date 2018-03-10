<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_new extends MY_Controller
{


    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('venta_new/venta_new_model', 'venta');
            $this->load->model('local/local_model');
            $this->load->model('producto/producto_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('condicionespago/condiciones_pago_model');
            $this->load->model('documentos/documentos_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('precio/precios_model');
            $this->load->model('correlativos/correlativos_model');
            $this->load->model('cotizar/cotizar_model');
            $this->load->model('metodosdepago/metodos_pago_model');
        } else {
            redirect(base_url(), 'refresh');
        }
        if (validOption('ACTIVAR_SHADOW', 1))
            $this->load->model('shadow/shadow_model');
    }

    function historial($action = "")
    {
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $data['venta_action'] = $action;
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();

        $data['dialog_venta_contado'] = $this->load->view('menu/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_all(),
            'bancos' => $this->db->get_where('banco', array('banco_status' => 1))->result()
        ), true);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/historial', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_ventas($action = "")
    {
        $local_id = $this->input->post('local_id');
        $estado = $this->input->post('estado');

        $date_range = explode(" - ", $this->input->post('fecha'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        if ($action != 'caja') {
            $params = array(
                'local_id' => $local_id,
                'estado' => $estado,
                'fecha_ini' => $fecha_ini,
                'fecha_fin' => $fecha_fin
            );
        } else {
            $params = array(
                'local_id' => $local_id,
                'estado' => $estado
            );
        }

        $params['moneda_id'] = $this->input->post('moneda_id');
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($params, $action);


        $data['venta_totales'] = $this->venta->get_ventas_totales($params, $action);

        $data['venta_action'] = $action;
        if ($action != 'caja')
            $this->load->view('menu/venta/historial_list', $data);
        else
            $this->load->view('menu/venta/caja_list', $data);
    }

    function get_pendientes()
    {
        $local_id = $this->input->post('local_id');
        $estado = $this->input->post('estado');

        $params = array(
            'local_id' => $local_id,
            'estado' => $estado
        );

        $data['ventas'] = $this->venta->get_ventas($params);

        echo count($data['ventas']);
    }

    function get_venta_detalle($action = "")
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);
        $data['venta_action'] = $action;
        $data['detalle'] = 'venta';
        $this->load->view('menu/venta/historial_list_detalle', $data);
    }

    function get_venta_previa()
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        $data['venta_action'] = 'imprimir';
        $data['detalle'] = 'venta';

        $data['dialog_detalle'] = $this->load->view('menu/venta/historial_list_detalle', $data, true);

        $this->load->view('menu/venta/dialog_venta_previa', $data);
    }

    function refresh_productos()
    {
        $data['productos'] = $this->producto_model->get_productos_list();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function index($local = "", $cot_id = FALSE)
    {

        $local_id = $local == "" || $local == '-' ? $this->session->userdata('id_local') : $local;


        $data['cotizacion'] = $cot_id != FALSE ? $this->cotizar_model->prepare_cotizacion($cot_id) : NULL;

        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['productos'] = $this->producto_model->get_productos_list();
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data["clientes"] = $this->cliente_model->get_all();
        $data["monedas"] = $this->monedas_model->get_all();
        $data["tipo_pagos"] = $this->condiciones_pago_model->get_all();
        $data['tipo_documentos'] = $this->documentos_model->get_documentos();
        $data['precios'] = $this->precios_model->get_all_by('mostrar_precio', '1', array('campo' => 'orden', 'tipo' => 'ASC'));


        $data['dialog_venta_contado'] = $this->load->view('menu/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_all(),
            'bancos' => $this->db->get_where('banco', array('banco_status' => 1))->result()
        ), true);

        $data['dialog_venta_credito'] = $this->load->view('menu/venta/dialog_venta_credito', array(
            'garantes' => $this->db->get('garante')->result()
        ), true);

        $data['dialog_venta_caja'] = $this->load->view('menu/venta/dialog_venta_caja', array(
            'next_id' => $this->venta->get_next_id()
        ), true);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/index', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function save_venta()
    {

        $venta['local_id'] = $this->input->post('local_venta_id');
        $venta['id_documento'] = $this->input->post('tipo_documento');
        $venta['id_cliente'] = $this->input->post('cliente_id');
        $venta['condicion_pago'] = $this->input->post('tipo_pago');
        $venta['id_moneda'] = $this->input->post('moneda_id');
        $venta['tasa_cambio'] = $this->input->post('tasa');

        $venta['venta_status'] = $this->input->post('venta_estado');
        $venta['fecha_venta'] = $this->input->post('fecha_venta');
        $venta['tipo_impuesto'] = $this->input->post('tipo_impuesto');

        $venta['subtotal'] = $this->input->post('subtotal');
        $venta['impuesto'] = $this->input->post('impuesto');
        $venta['total_importe'] = $this->input->post('total_importe');

        $venta['vc_total_pagar'] = $this->input->post('vc_total_pagar');
        $venta['vc_importe'] = $this->input->post('vc_importe');
        $venta['vc_vuelto'] = $this->input->post('vc_vuelto');
        $venta['vc_forma_pago'] = $this->input->post('vc_forma_pago');
        $venta['vc_num_oper'] = $this->input->post('vc_num_oper');
        $venta['vc_tipo_tarjeta'] = $this->input->post('vc_tipo_tarjeta');
        $venta['vc_banco_id'] = $this->input->post('vc_banco_id');


        $venta['c_dni_garante'] = $this->input->post('c_garante');
        $venta['c_inicial'] = $this->input->post('c_saldo_inicial') != '' ? $this->input->post('c_saldo_inicial') : 0;
        $venta['c_precio_contado'] = $this->input->post('c_precio_contado');
        $venta['c_precio_credito'] = $this->input->post('c_precio_credito');
        $venta['c_tasa_interes'] = $this->input->post('c_tasa_interes') != '' ? $this->input->post('c_tasa_interes') : 0;
        $venta['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
        $venta['c_fecha_giro'] = $this->input->post('c_fecha_giro');
        $venta['c_periodo_gracia'] = $this->input->post('c_periodo_gracia');

        $venta['caja_total_pagar'] = $this->input->post('caja_total_pagar');;

        $detalles_productos = json_decode($this->input->post('detalles_productos', true));
        $traspasos = json_decode($this->input->post('traspasos', true));
        $cuotas = json_decode($this->input->post('cuotas', true));

        $validar_detalle = array();
        foreach ($detalles_productos as $d) {
            $validar_detalle[] = array(
                'producto_id' => $d->id_producto,
                'local_id' => $venta['local_id'],
                'unidad_id' => $d->unidad_medida,
                'cantidad' => $d->cantidad
            );
        }

        $sin_stock = $this->inventario_model->check_stock($validar_detalle);

        if (count($sin_stock) == 0) {

            if ($venta['condicion_pago'] == '1') {
                $venta_id = $this->venta->save_venta_contado($venta, $detalles_productos, $traspasos);
            } elseif ($venta['condicion_pago'] == '2') {
                $venta_id = $this->venta->save_venta_credito($venta, $detalles_productos, $traspasos, $cuotas);
            }

            if ($venta_id) {
//                $cot_id = $this->input->post('cot_id');
//                if ($cot_id != "-1") {
//                    $this->db->where('id', $cot_id);
//                    $this->db->update('cotizacion', array('estado' => 'COMPLETADO'));
//                }
                $data['success'] = '1';
                $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
            } else
                $data['success'] = '0';

        } else {
            $data['success'] = "3";
            $data['sin_stock'] = json_encode($sin_stock);
        }


        echo json_encode($data);

    }

    function save_venta_contable()
    {
        if (validOption('ACTIVAR_SHADOW', 1)) {
            $venta_id = $this->input->post('venta_id', true);
            $detalles_productos = json_decode($this->input->post('detalles_productos', true));
            $this->shadow_model->save_venta_contable($venta_id, $detalles_productos);

            $data['success'] = '1';
            echo json_encode($data);
        }
    }

    function set_stock()
    {
        $stock_minimo = $this->input->post('stock_minimo');
        $stock_total_minimo = $this->input->post('stock_total_minimo');
        $producto_id = $this->input->post('producto_id');
        $local_id = $this->input->post('local_id');

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();
        $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
        $data['stock_actual'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min - $stock_minimo);

        $all_cantidad = $this->db->join('local', 'local.int_local_id = producto_almacen.id_local')
            ->where(array('id_producto' => $producto_id, 'local_status' => '1'))
            ->get('producto_almacen')->result();
        $all_cantidad_min = 0;
        foreach ($all_cantidad as $cantidad) {
            $temp = $cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $cantidad->cantidad, $cantidad->fraccion) : 0;
            $all_cantidad_min += $temp;
        }
        $data['stock_total'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $all_cantidad_min - $stock_total_minimo);

        $data['stock_minimo'] = $old_cantidad_min;
        $data['stock_total_minimo'] = $all_cantidad_min;

        $data['stock_minimo_left'] = $old_cantidad_min - $stock_minimo;
        $data['stock_total_minimo_left'] = $all_cantidad_min - $stock_total_minimo;

        if (validOption('ACTIVAR_SHADOW', 1)) {
            $data['shadow'] = $this->shadow_model->get_stock($producto_id);
        }


        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function set_stock_desglose()
    {
        $locales = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $producto_id = $this->input->post('producto_id');


        foreach ($locales as $local) {
            $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local->local_id))->row();
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
            $data['locales'][] = $local->local_nombre;
            $data['stock_desgloses'][] = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_productos_unidades($moneda_id = '')
    {
        $producto_id = $this->input->post('producto_id');
        $precio_id = $this->input->post('precio_id');

        $data['unidades'] = $this->unidades_model->get_unidades_precios($producto_id, $precio_id);

        $data['moneda'] = $this->unidades_model->get_moneda_default($producto_id);

        if (validOption('ACTIVAR_SHADOW', 1)) {
            if ($moneda_id != '')
                $data['precio_contable'] = $this->shadow_model->get_precio_contable($producto_id, $moneda_id);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_productos_precios()
    {
        $producto_id = $this->input->post('producto_id');
        $precio_id = $this->input->post('precio_id');

        $data['unidades'] = $this->unidades_model->get_unidades_precios($producto_id, $precio_id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function update_cliente()
    {
        $data['clientes'] = $data["clientes"] = $this->cliente_model->get_all();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_contable_detalle()
    {
        if (validOption('ACTIVAR_SHADOW', 1)) {
            $venta_id = $this->input->post('venta_id');
            $data['venta'] = $this->shadow_model->get_venta_contable_detalle($venta_id);
            $data['productos'] = $this->producto_model->get_productos_list();
            $this->load->view('menu/venta/dialog_contable_detalle', $data);
        }
    }

    function cerrar_venta()
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        $data['correlativo'] = $this->correlativos_model->get_correlativo($data['venta']->local_id, $data['venta']->documento_id);

        $data['dialog_detalle'] = $this->load->view('menu/venta/historial_list_detalle', $data, true);
        $this->load->view('menu/venta/dialog_venta_cerrar', $data);
    }

    function cerrar_venta_save()
    {
        $venta_id = $this->input->post('venta_id');
        $correlativo_inicial = $this->input->post('correlativo_inicial');
        $cantidad_correlativo = $this->input->post('cantidad_correlativo');

        $correlativos = array();
        for ($i = 0; $i < $cantidad_correlativo; $i++)
            $correlativos[$i] = $correlativo_inicial++;

        $this->venta->cerrar_venta($venta_id, $correlativos);

        $data['success'] = '1';
        echo json_encode($data);
    }

    function anular_venta()
    {
        $venta_id = $this->input->post('venta_id');
        $numero = $this->input->post('numero');
        $serie = $this->input->post('serie');
        $this->venta->anular_venta($venta_id, $serie, $numero);
    }

    function get_venta_cobro()
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function save_venta_caja()
    {

        $venta['venta_id'] = $this->input->post('venta_id');
        $venta['tipo_pago'] = $this->input->post('tipo_pago');
        $venta['importe'] = $this->input->post('importe');
        $venta['vuelto'] = $this->input->post('vuelto');
        $venta['tarjeta'] = $this->input->post('tarjeta');
        $venta['num_oper'] = $this->input->post('num_oper');
        $venta['banco_id'] = $this->input->post('banco');


        $result = $this->venta->save_venta_caja($venta);

        if ($result) {
            $data['success'] = '1';
            $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta['venta_id']))->row();
        } else
            $data['success'] = '0';


        echo json_encode($data);

    }

    function devolver_detalle()
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);
        $data['detalle'] = 'devolver';
        $this->load->view('menu/venta/historial_list_detalle', $data);
    }

    function devolver_venta()
    {
        $venta_id = $this->input->post('venta_id');
        $total_importe = $this->input->post('total_importe');
        $devoluciones = json_decode($this->input->post('devoluciones'));
        $numero = $this->input->post('numero');
        $serie = $this->input->post('serie');
        $this->venta->devolver_venta($venta_id, $total_importe, $devoluciones, $serie, $numero);
    }

    function opciones($action = 'get')
    {
        $this->load->model('opciones/opciones_model');
        $keys = array(
            'CREDITO_INICIAL',
            'CREDITO_TASA',
            'CREDITO_CUOTAS',
            'VISTA_CREDITO',
            'COSTO_AUMENTO',
            'INCORPORAR_IGV',
            'COBRAR_CAJA',
            'COTIZACION_INFORMACION',
            'COTIZACION_CONDICION',
            'COTIZACION_PIE_PAGINA'
        );

        if ($action == 'get') {
            $data['configuraciones'] = $this->opciones_model->get_opciones($keys);
            $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/opciones', $data, true);

            if ($this->input->is_ajax_request()) {
                echo $dataCuerpo['cuerpo'];
            } else {
                $this->load->view('menu/template', $dataCuerpo);
            }
        } elseif ($action == 'save') {

            $configuraciones = array();
            foreach ($keys as $key) {
                $configuraciones[] = array(
                    'config_key' => $key,
                    'config_value' => $this->input->post($key)
                );
            }

            $result = $this->opciones_model->guardar_configuracion($configuraciones);
            $configuraciones = $this->opciones_model->get_opciones($keys);

            if (count($configuraciones) > 0) {
                foreach ($configuraciones as $configuracion) {
                    $data[$configuracion['config_key']] = $configuracion['config_value'];
                }
                $this->session->set_userdata($data);
            }

            if ($result)
                $json['success'] = 'Las configuraciones se han guardado exitosamente';
            else
                $json['error'] = 'Ha ocurido un error al guardar las configuraciones';

            echo json_encode($json);
        }
    }

    function historial_pdf($local_id, $estado, $mes, $year, $dia_min, $dia_max)
    {
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4-L');

        $data['ventas'] = $this->venta->get_ventas(array(
            'local_id' => $local_id,
            'estado' => $estado,
            'mes' => $mes,
            'year' => $year,
            'dia_min' => $dia_min,
            'dia_max' => $dia_max,
        ));

        $data['local'] = $this->local_model->get_by('int_local_id', $local_id);
        $data['estado'] = $estado;
        $data['fecha_ini'] = $dia_min . '/' . $mes . '/' . $year;
        $data['fecha_fin'] = $dia_max . '/' . $mes . '/' . $year;


        $mpdf->WriteHTML($this->load->view('menu/venta/historial_list_pdf', $data, true));
        $nombre_archivo = utf8_decode('Historial ventas ' . $data['fecha_ini'] . ' : ' . $data['fecha_fin'] . '.pdf');
        $mpdf->Output($nombre_archivo, 'I');
    }

    function imprimir($venta_id, $tipo_impresion)
    {
        if ($tipo_impresion == 'PEDIDO') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            $total = $data['venta']->total;
            $data['totalLetras'] = $this->numLetras($total);
            $this->load->view('menu/venta/impresiones/nota_pedido', $data);
            //$this->venta->imprimir_pedido($data);
        } elseif ($tipo_impresion == 'ALMACEN') {
            $pedido = $this->venta->get_venta_detalle($venta_id);
            $detalles = array();
            foreach ($pedido->detalles as $venta) {
                $detalles[] = $venta;
                $venta->origen = $pedido->local_nombre;

                $kardexs = $this->db->get_where('kardex', array(
                    'ref_id' => $pedido->venta_id,
                    'io' => 1,
                    'tipo' => -1,
                    'operacion' => 11,
                    'producto_id' => $venta->producto_id,
                    'unidad_id' => $venta->unidad_id
                ))->result();


                foreach ($kardexs as $kardex) {
                    $venta->cantidad -= $kardex->cantidad;
                    $venta_temp = clone $venta;
                    $venta_temp->cantidad = $kardex->cantidad;
                    $venta_temp->origen = $kardex->ref_val;
                    $venta_temp->importe = number_format($venta_temp->cantidad * $venta_temp->precio, 2);
                    $detalles[] = $venta_temp;

                }

                $venta->importe = number_format($venta->cantidad * $venta->precio, 2);
            }

            $pedido->detalles = $detalles;
            $data['venta'] = $pedido;
            $total = $data['venta']->total;
            $data['totalLetras'] = $this->numLetras($total);
            $this->load->view('menu/venta/impresiones/pedido_almacen', $data);
            //$this->venta->imprimir_pedido($data);

        } elseif ($tipo_impresion == 'DOCUMENTO' || $tipo_impresion == 'SC') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            if ($tipo_impresion == 'SC')
                $data['venta'] = $this->shadow_model->get_venta_contable_detalle($venta_id);
            $total = $data['venta']->total;
            $data['totalLetras'] = $this->numLetras($total);
            $this->db->where('venta_id', $venta_id);
            $this->db->update('venta', array('factura_impresa' => '1'));

            if ($data['venta']->documento_id == 1) {
                //$this->load->view('menu/venta/impresiones/factura', $data);
                $this->venta->imprimir_factura($data);
            } elseif ($data['venta']->documento_id == 3) {
                //$this->load->view('menu/venta/impresiones/boleta', $data);
                $this->venta->imprimir_boleta($data);
            }
        }

    }

    function numLetras($num)
    {
        $fem = false;
        $dec = true;
        $matuni[2] = "dos";
        $matuni[3] = "tres";
        $matuni[4] = "cuatro";
        $matuni[5] = "cinco";
        $matuni[6] = "seis";
        $matuni[7] = "siete";
        $matuni[8] = "ocho";
        $matuni[9] = "nueve";
        $matuni[10] = "diez";
        $matuni[11] = "once";
        $matuni[12] = "doce";
        $matuni[13] = "trece";
        $matuni[14] = "catorce";
        $matuni[15] = "quince";
        $matuni[16] = "dieciseis";
        $matuni[17] = "diecisiete";
        $matuni[18] = "dieciocho";
        $matuni[19] = "diecinueve";
        $matuni[20] = "veinte";
        $matunisub[2] = "dos";
        $matunisub[3] = "tres";
        $matunisub[4] = "cuatro";
        $matunisub[5] = "quin";
        $matunisub[6] = "seis";
        $matunisub[7] = "sete";
        $matunisub[8] = "ocho";
        $matunisub[9] = "nove";

        $matdec[2] = "veint";
        $matdec[3] = "treinta";
        $matdec[4] = "cuarenta";
        $matdec[5] = "cincuenta";
        $matdec[6] = "sesenta";
        $matdec[7] = "setenta";
        $matdec[8] = "ochenta";
        $matdec[9] = "noventa";
        $matsub[3] = 'mill';
        $matsub[5] = 'bill';
        $matsub[7] = 'mill';
        $matsub[9] = 'trill';
        $matsub[11] = 'mill';
        $matsub[13] = 'bill';
        $matsub[15] = 'mill';
        $matmil[4] = 'millones';
        $matmil[6] = 'billones';
        $matmil[7] = 'de billones';
        $matmil[8] = 'millones de billones';
        $matmil[10] = 'trillones';
        $matmil[11] = 'de trillones';
        $matmil[12] = 'millones de trillones';
        $matmil[13] = 'de trillones';
        $matmil[14] = 'billones de trillones';
        $matmil[15] = 'de billones de trillones';
        $matmil[16] = 'millones de billones de trillones';

        //Zi hack
        $float = explode('.', $num);
        $num = $float[0];

        $num = trim((string)@$num);
        if ($num[0] == '-') {
            $neg = 'menos ';
            $num = substr($num, 1);
        } else
            $neg = '';
        while ($num[0] == '0') $num = substr($num, 1);
        if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
        $zeros = true;
        $punt = false;
        $ent = '';
        $fra = '';
        for ($c = 0; $c < strlen($num); $c++) {
            $n = $num[$c];
            if (!(strpos(".,'''", $n) === false)) {
                if ($punt) break;
                else {
                    $punt = true;
                    continue;
                }

            } elseif (!(strpos('0123456789', $n) === false)) {
                if ($punt) {
                    if ($n != '0') $zeros = false;
                    $fra .= $n;
                } else

                    $ent .= $n;
            } else

                break;

        }
        $ent = '     ' . $ent;
        if ($dec and $fra and !$zeros) {
            $fin = ' coma';
            for ($n = 0; $n < strlen($fra); $n++) {
                if (($s = $fra[$n]) == '0')
                    $fin .= ' cero';
                elseif ($s == '1')
                    $fin .= $fem ? ' una' : ' un';
                else
                    $fin .= ' ' . $matuni[$s];
            }
        } else
            $fin = '';
        if ((int)$ent === 0) return 'Cero ' . $fin;
        $tex = '';
        $sub = 0;
        $mils = 0;
        $neutro = false;
        while (($num = substr($ent, -3)) != '   ') {
            $ent = substr($ent, 0, -3);
            if (++$sub < 3 and $fem) {
                $matuni[1] = 'una';
                $subcent = 'as';
            } else {
                $matuni[1] = $neutro ? 'un' : 'uno';
                $subcent = 'os';
            }
            $t = '';
            $n2 = substr($num, 1);
            if ($n2 == '00') {
            } elseif ($n2 < 21)
                $t = ' ' . $matuni[(int)$n2];
            elseif ($n2 < 30) {
                $n3 = $num[2];
                if ($n3 != 0) $t = 'i' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            } else {
                $n3 = $num[2];
                if ($n3 != 0) $t = ' y ' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }
            $n = $num[0];
            if ($n == 1) {
                $t = ' ciento' . $t;
            } elseif ($n == 5) {
                $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
            } elseif ($n != 0) {
                $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
            }
            if ($sub == 1) {
            } elseif (!isset($matsub[$sub])) {
                if ($num == 1) {
                    $t = ' mil';
                } elseif ($num > 1) {
                    $t .= ' mil';
                }
            } elseif ($num == 1) {
                $t .= ' ' . $matsub[$sub] . '?n';
            } elseif ($num > 1) {
                $t .= ' ' . $matsub[$sub] . 'ones';
            }
            if ($num == '000') $mils++;
            elseif ($mils != 0) {
                if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
                $mils = 0;
            }
            $neutro = true;
            $tex = $t . $tex;
        }
        $tex = $neg . substr($tex, 1) . $fin;
        $end_num = $tex;
        return $end_num;
    }


    function imprimir_html()
    {

        $venta_id = $this->input->post('venta_id');
        $tipo_impresion = $this->input->post('tipo_impresion');

        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        if ($tipo_impresion == 'PEDIDO') {
            $documento = 'boleta';

            $this->load->view('menu/venta/impresiones/' . $documento, $data);

        }

    }

    function historial_excel($local_id, $estado, $mes, $year, $dia_min, $dia_max)
    {

        $this->load->library('phpExcel/PHPExcel.php');

        $data['ventas'] = $this->venta->get_ventas(array(
            'local_id' => $local_id,
            'estado' => $estado,
            'mes' => $mes,
            'year' => $year,
            'dia_min' => $dia_min,
            'dia_max' => $dia_max,
        ));

        $local = $this->local_model->get_by('int_local_id', $local_id);
        $fecha_ini = $dia_min . '/' . $mes . '/' . $year;
        $fecha_fin = $dia_max . '/' . $mes . '/' . $year;

        $this->phpexcel->getProperties()
            ->setTitle("ventas")
            ->setSubject("ventas")
            ->setDescription("ventas")
            ->setKeywords("ventas")
            ->setCategory("ventas");

        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 1, "Ubicacion");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 1, $local['local_nombre']);

        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 1, "Estado");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 1, $estado);

        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 1, "Fecha");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, 1, $fecha_ini . ' a ' . $fecha_fin);


        $i = 0;

        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Fecha");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Doc");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Num Doc");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "RUC - DNI");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Cliente");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Vendedor");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Condicion");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Moneda");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Tip. Cam.");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "SubTotal");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "IGV");
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($i++, 3, "Total");

        $row = 4;
        foreach ($data['ventas'] as $venta) {
            $col = 0;
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, date('d/m/Y H:i:s', strtotime($venta->venta_fecha)));
            $doc = '';
            if ($venta->documento_id == 1) $doc = "FA";
            if ($venta->documento_id == 2) $doc = "NC";
            if ($venta->documento_id == 3) $doc = "BO";
            if ($venta->documento_id == 4) $doc = "GR";
            if ($venta->documento_id == 5) $doc = "PCV";
            if ($venta->documento_id == 6) $doc = "NP";
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $doc);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, sumCod($venta->venta_id, 4));
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->ruc);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->cliente_nombre);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->vendedor_nombre);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->condicion_nombre);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->moneda_nombre);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->moneda_tasa);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->subtotal);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->impuesto);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col++, $row, $venta->total);

            $row++;
        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="RegistroVentas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}