<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajuste extends MY_Controller
{


    function __construct()
    {
        parent::__construct();
        $this->login_model->verify_session();

        $this->load->model('local/local_model');
        $this->load->model('producto/producto_model');
        $this->load->model('monedas/monedas_model');
        $this->load->model('condicionespago/condiciones_pago_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('precio/precios_model');
        $this->load->model('ajuste/ajuste_model');
    }

    function index($local = "")
    {
        $local_id = $local == "" ? $this->session->userdata('id_local') : $local;

        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['productos'] = $this->producto_model->get_productos_list();
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data["monedas"] = $this->monedas_model->get_all();
        $data["tipo_pagos"] = $this->condiciones_pago_model->get_all();
        $data['precios'] = $this->precios_model->get_all_by('mostrar_precio', '1', array('campo' => 'orden', 'tipo' => 'ASC'));




        $dataCuerpo['cuerpo'] = $this->load->view('menu/ajuste/index', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function save_ajuste()
    {

        $ajuste['usuario_id'] = $this->session->userdata('nUsuCodigo');
        $ajuste['local_id'] = $this->input->post('local_id');
        $ajuste['moneda_id'] = $this->input->post('moneda_id');
        $ajuste['tasa_cambio'] = $this->input->post('tasa');
        $ajuste['fecha'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->input->post('fecha_venta')) . date(" H:i:s")));
        
        $ajuste['operacion'] = $this->input->post('tipo_operacion');
        $ajuste['io'] = $this->input->post('tipo_movimiento');
        
        $ajuste['documento'] = $this->input->post('tipo_documento');
        $ajuste['serie'] = $this->input->post('serie_doc');
        $ajuste['numero'] = $this->input->post('numero_doc');
        
        $ajuste['estado'] = '1';
        $ajuste['total_importe'] = $this->input->post('total_importe');

        $ajuste['operacion_otros'] = $this->input->post('operacion_otros');

        $detalles_productos = json_decode($this->input->post('detalles_productos', true));

        
        $ajuste_id = $this->ajuste_model->save_ajuste($ajuste, $detalles_productos);
        

        if ($ajuste_id) {
            $data['success'] = '1';
            $data['ajuste'] = $this->db->get_where('ajuste', array('id' => $ajuste_id))->row();
        } else
            $data['success'] = '0';


        echo json_encode($data);

    }

    function set_stock()
    {
        $stock_minimo = $this->input->post('stock_minimo');
        $stock_total_minimo = $this->input->post('stock_total_minimo');
        $producto_id = $this->input->post('producto_id');
        $local_id = $this->input->post('local_id');
        $io = $this->input->post('IO');

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();
        $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
        
        $io_cantidad_min = $old_cantidad_min - $stock_minimo;
        if($io == 1)
            $io_cantidad_min = $old_cantidad_min + $stock_minimo;
        
        $data['stock_actual'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $io_cantidad_min);

        $all_cantidad = $this->db->join('local', 'local.int_local_id = producto_almacen.id_local')
            ->where(array('id_producto' => $producto_id, 'local_status' => '1'))
            ->get('producto_almacen')->result();
        $all_cantidad_min = 0;
        foreach ($all_cantidad as $cantidad) {
            $temp = $cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $cantidad->cantidad, $cantidad->fraccion) : 0;
            $all_cantidad_min += $temp;
        }

        $io_cantidad_total_min = $all_cantidad_min - $stock_total_minimo;
        if($io == 1)
            $io_cantidad_total_min = $all_cantidad_min + $stock_total_minimo;

        $data['stock_total'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $io_cantidad_total_min);

        $data['stock_minimo'] = $old_cantidad_min;
        $data['stock_total_minimo'] = $all_cantidad_min;

        $data['io'] = $io;

        if($io == 1){
            $data['stock_minimo_left'] = $old_cantidad_min + $stock_minimo;
            $data['stock_total_minimo_left'] = $all_cantidad_min + $stock_total_minimo;
        }
        elseif($io == 2){
            $data['stock_minimo_left'] = $old_cantidad_min - $stock_minimo;
            $data['stock_total_minimo_left'] = $all_cantidad_min - $stock_total_minimo;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_productos_unidades()
    {
        $producto_id = $this->input->post('producto_id');
        $moneda_id = $this->input->post('moneda_id');

        $data['unidades'] = $this->unidades_model->get_unidades_precios($producto_id, 3);

        $data['moneda'] = $this->unidades_model->get_moneda_default($producto_id);

        $data['costo'] = $this->db->get_where('producto_costo_unitario', array(
            'producto_id' => $producto_id,
            'moneda_id' => $moneda_id
        ))->row();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

}
