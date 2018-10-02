<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class venta extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('venta_new/venta_new_model', 'venta');
            $this->load->model('local/local_model');
            $this->load->model('documentos/documentos_model');
            $this->load->model('venta_contable_detalle/venta_contable_detalle_model', 'venta_contable');
            $this->load->model('usuario_facturador/usuario_facturador_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('condicionespago/condiciones_pago_model');
            $this->load->model('precio/precios_model');
            $this->load->model('metodosdepago/metodos_pago_model');
            $this->load->model('venta_shadow/venta_shadow_model');
            $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function historial()
    {
        $data['locales'] = $this->local_model->get_all();
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $data['condiciones_pagos'] = $this->db->get_where('condiciones_pago', array('status_condiciones' => 1))->result();
        $dataCuerpo['cuerpo'] = $this->load->view('facturador/venta/historial', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('facturador/template', $dataCuerpo);
        }
    }

    function get_ventas()
    {
        $params['local_id'] = $this->input->post('local_id');
        $params['estado'] = $this->input->post('estado');
        $params['condicion_id'] = $this->input->post('condicion_pago_id');
        $date_range = explode(" - ", $this->input->post('fecha'));
        $params['fecha_ini'] = str_replace("/", "-", $date_range[0]);
        $params['fecha_fin'] = str_replace("/", "-", $date_range[1]);
        $params['moneda_id'] = $this->input->post('moneda_id');
        $params['usuarios_id'] = $this->input->post('usuarios_id');
        $params['id_documento'] = 6;
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($params, 'venta');
        $data['venta_totales'] = $this->venta->get_ventas_totales($params, 'venta');
        $this->load->view('facturador/venta/historial_list', $data);
    }

    function get_venta_detalle()
    {
        $venta_id = $this->input->post('venta_id');
        $datos = $this->venta->get_venta_detalle($venta_id);
        $x = 0;
        foreach($datos->detalles as $dato){
            $datos->detalles[$x]->cantidad = $this->unidades_model->convert_minimo_by_um($dato->producto_id, $dato->unidad_id, $dato->cantidad);
            $datos->detalles[$x]->unidad_nombre = $this->unidades_model->get_um_min_by_producto($dato->producto_id);
            $unidades = $this->unidades_model->get_by('nombre_unidad', $datos->detalles[$x]->unidad_nombre);
            $datos->detalles[$x]->unidad_id_min = $unidades['id_unidad'];
            $datos->detalles[$x]->precio = $this->unidades_model->get_maximo_costo($dato->producto_id, $dato->unidad_id, $datos->detalles[$x]->precio);
            $contable_costo = $this->venta_contable->getCosto($venta_id, $dato->unidad_id);
            $datos->detalles[$x]->contable_costo = $contable_costo->contable_costo;
            $x++;
        }
        $data['venta'] = $datos;
        $this->load->view('facturador/venta/historial_list_detalle', $data);
    }
    function get_venta_detalle_convertido()
    {
        $venta_id = $this->input->post('venta_id');
        $datos = $this->venta->get_venta_detalle_convertido($venta_id);
        
        $data['venta'] = $datos;
        $this->load->view('facturador/venta/historial_list_detalle_convertidos', $data);
    }
    function remove_ventaconvertida_shadow()
    {
        $id_shadow = $this->input->post('id_shadow');
        $datos = $this->venta->remove_ventaconvertida_shadow($id_shadow);
        
        $data['shadow_v'] = $datos;
        $this->load->view('facturador/venta/remove_ventaconvertida_shadow', $data);
    }
    /*function editarVentaContable()
    {
        $action = $this->input->post('action');
        $arr = explode('_', $this->input->post('identify'));
        $params['venta_id'] = $arr[0];
        $params['producto_id'] = $arr[1];
        $params['unidad_id'] = $arr[2];
        $params['cantidad'] = $this->input->post('Cantidad');
        $params['precio'] = $this->input->post('Precio');

        if($action=='edit'){
            $this->venta_contable->editar($params);
        }elseif($action=='delete'){
            $this->venta_contable->eliminar($params);
        }
        echo json_encode($action);
    }*/

    function shadow($id_venta)
    {
        $data['venta'] = $this->venta->prepare_venta($id_venta);
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('id_local'));
        $data['productos'] = array();
        $data['usuarios'] = $this->usuario_facturador_model->get_by('activo', '1');
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data["clientes"] = $this->cliente_model->get_all();
        $data["monedas"] = $this->monedas_model->get_all();
        $data["tipo_pagos"] = $this->condiciones_pago_model->get_by('id_condiciones', '1');
        $data['tipo_documentos'] = $this->db->get_where('documentos', "id_doc IN(1,3)")->result();
        $data['precios'] = $this->precios_model->get_all_by('mostrar_precio', '1', array('campo' => 'orden', 'tipo' => 'ASC'));
        $data['comprobantes'] = $this->db->get_where('comprobantes', array('estado' => 1))->result();
        $data['comprobantes_default'] = $this->db->get_where('configuraciones', array('config_id' => '55'))->row();

        $data['dialog_venta_contado'] = $this->load->view('facturador/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_by('id_metodo', '3'),
            'bancos' => $this->db->get_where('banco', array('banco_status' => 1))->result()
        ), true);

        $dataCuerpo['cuerpo'] = $this->load->view('facturador/venta/index', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('facturador/template', $dataCuerpo);
        }        
    }

    function set_stock_desglose()
    {
        $locales = $this->local_model->get_local_by_user($this->session->userdata('id'));
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

    function set_stock()
    {
        $stock_minimo = $this->input->post('stock_minimo');
        $stock_total_minimo = $this->input->post('stock_total_minimo');
        $producto_id = $this->input->post('producto_id');
        $local_id = $this->input->post('local_id');

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();
        $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
        $data['stock_actual'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min - $stock_minimo);

        $locales = $this->local_model->get_local_by_user($this->session->userdata('id'));
        $all_cantidad_min = 0;
        foreach ($locales as $local) {
            $cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local->local_id))->row();
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

    function save_venta_shadow()
    {
        $venta['venta_id'] = $this->input->post('venta_id');
        $venta['local_id'] = $this->input->post('local_venta_id');
        $venta['id_documento'] = '0'.$this->input->post('tipo_documento');
        $venta['id_cliente'] = $this->input->post('cliente_id');
        $venta['id_usuario'] = $this->input->post('vendedor_id');
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
        $venta['c_precio_contado'] = $this->input->post('c_precio_contado');
        $venta['c_precio_credito'] = $this->input->post('c_precio_credito');
        $venta['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
        $venta['c_fecha_giro'] = $this->input->post('c_fecha_giro');
        $venta['c_periodo_gracia'] = $this->input->post('c_periodo_gracia');

        $venta['caja_total_pagar'] = $this->input->post('caja_total_pagar');
        $venta['dni_garante'] = $this->input->post('caja_nombre');
        $venta['comprobante_id'] = $this->input->post('comprobante_id') != "" ? $this->input->post('comprobante_id') : 0;
        $venta['venta_nota'] = $this->input->post('venta_nota');

        $detalles_productos = json_decode($this->input->post('detalles_productos', true));

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

        if (count($sin_stock) == 0 || count($sin_stock) != 0) {
            if ($venta['condicion_pago'] == '1') {
                $id_venta_shadow = $this->venta_shadow_model->save_venta_contado($venta, $detalles_productos);
            }
            if ($id_venta_shadow) {
                $data['success'] = '1';
                $data['venta'] = $this->db->get_where('venta_shadow', array('id' => $id_venta_shadow))->row();
            } else {
                if (isset($this->venta->error))
                    $data['msg'] = $this->venta->error;
                $data['success'] = '0';
            }
        }
        echo json_encode($data);
    }

    function getCostoUnitarioVenta()
    {
        $param['moneda_id'] = $this->input->post('moneda_id');
        $param['producto_id'] = $this->input->post('producto_id');
        $data = $this->producto_costo_unitario_model->getCostoUnitarioVenta($param);
        echo json_encode($data);
    }
}