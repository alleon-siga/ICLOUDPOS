<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

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
            $this->load->model('diccionario_termino/diccionario_termino_model');
            $this->load->model('clientesgrupos/clientes_grupos_model');
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
        $data['condiciones_pagos'] = $this->db->get_where('condiciones_pago', array('status_condiciones' => 1))->result();

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
        $condicion_pago_id = $this->input->post('condicion_pago_id');

        $date_range = explode(" - ", $this->input->post('fecha'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        if ($action != 'caja') {
            $params = array(
                'local_id' => $local_id,
                'estado' => $estado,
                'condicion_id' => $condicion_pago_id,
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
        $params['usuarios_id'] = $this->input->post('usuarios_id');
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

        $data['ventas'] = $this->venta->get_ventas($params, 'caja');

        echo count($data['ventas']);
    }

    function get_venta_detalle($action = "")
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);
        $data['venta_action'] = $action;
        $data['detalle'] = 'venta';
        $this->db->select('serie, numero, fecha, nombre');
        $this->db->from('kardex');
        $this->db->join('usuario', 'kardex.usuario_id = usuario.nUsuCodigo');
        $this->db->where(array('ref_id' => $venta_id, 'io' => 2, 'tipo' => 7, 'operacion' => 5));
        $this->db->group_by('serie, numero');
        $data['kardex'] = $this->db->get()->result();
        /*$data['kardex'] = $this->db->get_where('kardex', array(
            'ref_id' => $venta_id,
            'io' => 2,
            'tipo' => 7,
            'operacion' => 5
        ))->result();*/
        $this->load->view('menu/venta/historial_list_detalle', $data);
    }

    function get_venta_facturar($action = "")
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_facturar($venta_id);
        $data['comprobante'] = $this->documentos_model->get_documentosBy('id_doc IN(1,3,6)');
        $data['venta_action'] = $action;
        $data['detalle'] = 'venta';
        $this->load->view('menu/venta/historial_list_facturar', $data);
    }

    function getDocumentoNumero(){
        $num = $this->venta->getDocumentoNumero();
        echo $num;
    }

    function facturar_venta()
    {
        $venta_id = $this->input->post('venta_id');
        $this->venta->facturar_venta($venta_id);
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
        $data['tipo_documentos'] = $this->db->get_where('documentos', array('ventas' => 1))->result();
        $data['precios'] = $this->precios_model->get_all_by('mostrar_precio', '1', array('campo' => 'orden', 'tipo' => 'ASC'));
        $data['comprobantes'] = $this->db->get_where('comprobantes', array('estado' => 1))->result();


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
        $venta['id_usuario'] = $this->session->userdata('nUsuCodigo');
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

        $venta['caja_total_pagar'] = $this->input->post('caja_total_pagar');
        $venta['dni_garante'] = $this->input->post('caja_nombre');
        $venta['comprobante_id'] = $this->input->post('comprobante_id') != "" ? $this->input->post('comprobante_id') : 0;
        $venta['venta_nota'] = $this->input->post('venta_nota');

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
            } else{
                if(isset($this->venta->error))
                    $data['msg'] = $this->venta->error;
                $data['success'] = '0';
            }

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
        $venta['id_usuario'] = $this->session->userdata('nUsuCodigo');
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
        } else{
            if(isset($this->venta->error))
                $data['msg'] = $this->venta->error;
            $data['success'] = '0';
        }


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
            'COBRAR_CAJA',
            'COTIZACION_INFORMACION',
            'COTIZACION_CONDICION',
            'COTIZACION_PIE_PAGINA',
            'COMPROBANTE'
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

    function ofertas($action = 'get')
    {
        $this->load->model('opciones/opciones_model');
        $keys = array(
            'FECHA_VENTA_PROMO',
            'VENTA_PROMO'
        );

        if ($action == 'get') {
            $data['configuraciones'] = $this->opciones_model->get_opciones($keys);
            $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/ofertas', $data, true);

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

    function historial_pdf()
    {
        $params = json_decode($this->input->get('data'));

        $date_range = explode(" - ", $params->fecha);
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        $condition = array(
            'local_id' => $params->local_id,
            'condicion_id' => $params->condicion_pago_id,
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin,
            'moneda_id' => $params->moneda_id
        );
        $data = $condition;

        $local = $this->db->get_where('local', array('int_local_id' => $condition['local_id']))->row();
        $data['local_nombre'] = $local->local_nombre;
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $condition['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($condition);

        $data['venta_totales'] = $this->venta->get_ventas_totales($condition);
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/venta/historial_list_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function imprimir($venta_id, $tipo_impresion)
    {
        $venta_temp = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        $moneda = $this->db->get_where('moneda', array('id_moneda' => $venta_temp->id_moneda))->row();
        if ($tipo_impresion == 'PEDIDO') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            $data['identificacion'] = $this->db->get_where('configuraciones', array('config_key' =>'EMPRESA_IDENTIFICACION'))->row();
            $total = $data['venta']->total;
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);
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
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);
            $this->load->view('menu/venta/impresiones/pedido_almacen', $data);
            //$this->venta->imprimir_pedido($data);

        } elseif ($tipo_impresion == 'DOCUMENTO' || $tipo_impresion == 'SC') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            if ($tipo_impresion == 'SC')
                $data['venta'] = $this->shadow_model->get_venta_contable_detalle($venta_id);
            $total = $data['venta']->total;
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);
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

    function historial_excel()
    {

        $params = json_decode($this->input->get('data'));

        $date_range = explode(" - ", $params->fecha);
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        $condition = array(
            'local_id' => $params->local_id,
            'condicion_id' => (isset($params->condicion_pago_id))? $params->condicion_pago_id : '',
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin,
            'moneda_id' => $params->moneda_id
        );
        $data = $condition;

        $local = $this->db->get_where('local', array('int_local_id' => $condition['local_id']))->row();
        $data['local_nombre'] = $local->local_nombre;
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $condition['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($condition);

        $data['venta_totales'] = $this->venta->get_ventas_totales($condition);

        echo $this->load->view('menu/venta/historial_list_excel', $data, true);
    }

    function recarga()
    {
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data["clientes"] = $this->cliente_model->get_all();
        $data['operadore'] = $this->diccionario_termino_model->get_all_operador();
        $data['poblados'] = $this->clientes_grupos_model->get_all();
        $data['monedas'] = $this->monedas_model->get_monedas_activas();
        $data['condPagos'] = $this->condiciones_pago_model->get_all();
        $data["documentos"] = $this->db->get_where('documentos', array('ventas' => 1))->result();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/recarga', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function save_recarga()
    {
        $venta['local_id'] = $this->input->post('local_venta_id');
        $venta['id_cliente'] = $this->input->post('cliente_id');
        $venta['rec_ope'] = $this->input->post('operador_id');
        $venta['fecha_venta'] = $this->input->post('fecha_venta');
        $venta['id_moneda'] = $this->input->post('moneda_id');
        $venta['total_importe'] = $this->input->post('total_importe');
        $venta['condicion_pago'] = $this->input->post('tipo_pago');
        $venta['rec_nro'] = $this->input->post('nro_recarga');
        $venta['cod_tran'] = $this->input->post('cod_tran');
        $venta['id_usuario'] = $this->session->userdata('nUsuCodigo');
        $venta['vc_importe'] = $this->input->post('vc_importe2');
        $venta['vc_vuelto'] = $this->input->post('vc_vuelto2');
        $venta['rec_pob'] = $this->input->post('poblado_id');
        $venta['nota'] = $this->input->post('tienda');
        $venta['vc_forma_pago'] = $this->input->post('vc_forma_pago2');
        $venta['vc_banco_id'] = $this->input->post('vc_banco_id2');
        $venta['vc_num_oper'] = $this->input->post('vc_num_oper2');
        $venta['telefono1'] = $this->input->post('nro_recarga');
        $venta['venta_status'] = 'COMPLETADO';
        $venta['id_documento'] = $this->input->post('cboDocumento');
        $venta_id = false;
        if($venta['condicion_pago']==2 && $venta['id_cliente']==1){
            $this->venta->error = 'El Cliente frecuente no tiene credito.';
        }else{
            $venta_id = $this->venta->save_recarga($venta);    
        }

        if($venta_id) {
            $data['success'] = '1';
            $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        }else{
            if(isset($this->venta->error)){
                $data['msg'] = $this->venta->error;
            }
            $data['success'] = '0';
        }
        echo json_encode($data);
    }   

    function dialog_venta_contado()
    {
        $this->load->view('menu/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_all(),
            'bancos' => $this->db->get_where('banco', array('banco_status' => 1))->result()
        ));
    }

    function getCliente()
    {
        $id = $this->input->post('id');
        $datos = $this->cliente_model->get_by('id_cliente', $id);
        echo json_encode($datos);
    }

    function ultimasVentas()
    {
        $venta['id_producto'] = $this->input->post('id_producto');
        $venta['id_cliente'] = $this->input->post('id_cliente');
        $data = $this->venta->ultimasVentas($venta);
        echo json_encode($data);
    }
}