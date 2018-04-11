<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cotizar extends MY_Controller
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
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function historial()
    {
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/cotizar/historial', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_cotizaciones($action = "")
    {
        $estado = 'PENDIENTE';
        $local_id = $this->input->post('local_id');
        $moneda_id = $this->input->post('moneda_id');
        $date_range = explode(" - ", $this->input->post('date_range'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        $params = array(
            'estado' => $estado,
            'local_id' => $local_id,
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin,
            'moneda_id' => $moneda_id
        );

        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();

        $data['cotizaciones'] = $this->cotizar_model->get_cotizaciones($params);
        $data['cotizaciones_totales'] = $this->cotizar_model->get_cotizaciones_totales($params);

        $this->load->view('menu/cotizar/historial_list', $data);
    }

    function get_cotizar_detalle()
    {
        $id = $this->input->post('id');
        $data['cotizar'] = $this->cotizar_model->get_cotizar_detalle($id);
        $this->load->view('menu/cotizar/historial_list_detalle', $data);
    }

    function get_cotizar_validar()
    {
        $id = $this->input->post('id');

        $data['cotizar'] = $this->cotizar_model->get_cotizar_validar($id);
        $this->load->view('menu/cotizar/historial_cotizar_detalle', $data);
    }

    function exportar_pdf($id, $tipoCliente){
        $data['tipo_cliente'] = $tipoCliente;
        $data['cotizar'] = $this->cotizar_model->get_cotizar_detalle($id);

        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/cotizar/cotizar_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    function eliminar(){
        $id = $this->input->post('id');

        $this->db->where('id', $id);
        $this->db->update('cotizacion', array('estado'=>'ANULADO'));

        echo 'ok';
    }



    function index($local = "")
    {
        $local_id = $local == "" ? $this->session->userdata('id_local') : $local;

        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['productos'] = $this->producto_model->get_productos_list();
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data["clientes"] = $this->cliente_model->get_all();
        $data["monedas"] = $this->monedas_model->get_all();
        $data["tipo_pagos"] = $this->condiciones_pago_model->get_all();
        $data['tipo_documentos'] = $this->documentos_model->get_documentos();
        $data['precios'] = $this->precios_model->get_all_by('mostrar_precio', '1', array('campo' => 'orden', 'tipo' => 'ASC'));


        $data['dialog_cotizar'] = $this->load->view('menu/cotizar/dialog_cotizar', array(), true);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/cotizar/index', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function save_cotizar()
    {
        header('Content-Type: application/json');

        $cotizar['fecha'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('fecha_venta'))));
        $cotizar['fecha_entrega'] = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('fecha_entrega'))));
        $cotizar['local_id'] = $this->input->post('local_venta_id');
        $cotizar['cliente_id'] = $this->input->post('cliente_id');
        $cotizar['vendedor_id'] = $this->session->userdata('nUsuCodigo');
        $cotizar['documento_id'] = $this->input->post('tipo_documento');
        $cotizar['tipo_impuesto'] = $this->input->post('tipo_impuesto');
        $cotizar['tipo_pago_id'] = $this->input->post('tipo_pago');
        $cotizar['moneda_id'] = $this->input->post('moneda_id');
        $cotizar['estado'] = 'PENDIENTE';
        $cotizar['impuesto'] = $this->input->post('impuesto');
        $cotizar['subtotal'] = $this->input->post('subtotal');
        $cotizar['total'] = $this->input->post('total_importe');
        $cotizar['tasa_cambio'] = $this->input->post('tasa');
        $cotizar['credito_periodo'] = $this->input->post('c_pago_periodo');
        $cotizar['periodo_per'] = $this->input->post('periodo_per');
        $cotizar['lugar_entrega'] = $this->input->post('lugar_entrega');

        $detalles_productos = json_decode($this->input->post('detalles_productos', true));

        $id = $this->cotizar_model->save($cotizar, $detalles_productos);
        if ($id != FALSE) {
            $data['success'] = 1;
            $data['id'] = $id;
        } else {
            $data['success'] = 0;
            $data['msg'] = 'El cotizacion no ha podido ser guardada';
        }

        echo json_encode($data);

    }

    function set_stock()
    {

        $producto_id = $this->input->post('producto_id');

        $all_cantidad = $this->db->join('local', 'local.int_local_id = producto_almacen.id_local')
            ->where(array('id_producto' => $producto_id, 'local_status' => '1'))
            ->get('producto_almacen')->result();
        $all_cantidad_min = 0;
        foreach ($all_cantidad as $cantidad) {
            $temp = $cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $cantidad->cantidad, $cantidad->fraccion) : 0;
            $all_cantidad_min += $temp;
        }
        $data['stock_total'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $all_cantidad_min);

        $data['stock_total_minimo'] = $all_cantidad_min;

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

    function editarCotizacion()
    {
        $action = $this->input->post('action');
        $id = $this->input->post('identify');
        if($action=='edit'){
            $this->cotizar_model->editarCotizacion($id);
        }elseif($action=='delete'){
            $this->cotizar_model->eliminarCotizacion($id);
        }
        echo json_encode($action);
    }
}