<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class Productos extends REST_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('producto/producto_api_model');
        $this->load->model('unidades/unidades_api_model');
        $this->load->model('local/local_api_model');
        $this->load->model('api/api_model', 'api');

        $this->very_auth();
    }

    function very_auth()
    {
        // Request Header
        $reqHeader = $this->input->request_headers();

        // Key
        $key = null;
        if (isset($reqHeader['x-api-key'])) {
            $key = $reqHeader['x-api-key'];

        } else if ($key_get = $this->get('x-api-key')) {
            $key = $key_get;

        } else if ($key_post = $this->post('x-api-key')) {
            $key = $key_post;

        } else {
            $key = null;
        }

        // Auth ID
        $auth_id = $this->api->getAuth($key);

        // ID ?
        if (!empty($auth_id)) {
            $this->uid = $auth_id;

        } else {
            $this->uid = null;
        }
    }

    public function index_post() {
        $id_usuario = $this->input->post('id_usuario');
        $str_producto = $this->input->post('str_producto');
        $favorito = $this->input->post('favorito');

        $data = array();
        if ($favorito == "SI") {
            $productos = $this->producto_api_model->get_productos_fav($str_producto);

        } else {
            $productos = $this->producto_api_model->get_productos_listall($str_producto);
        }

        foreach ($productos as $prod) {
            $producto['producto_id'] = $prod['producto_id'];
            $producto['codigo'] = $prod['codigo'];
            $producto['producto_nombre'] = $prod['producto_nombre'];

            $producto['unidad_precio'] = $this->producto_api_model->get_productos_unidprec($prod['producto_id'], 3);

            //$producto['stock_total'] = $this->set_stock($prod['producto_id']);

            $producto['stock_desglose'] = $this->set_stock_desglose($id_usuario, $prod['producto_id']);

            $data['productos'][] = $producto;
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    function set_stock($prod, $stock_tot_min = 0)
    {
        $producto_id = $prod;
        $stock_total_minimo = $stock_tot_min;

        $all_cantidad = $this->db->join('local', 'local.int_local_id = producto_almacen.id_local')
            ->where(array('id_producto' => $producto_id, 'local_status' => '1'))
            ->get('producto_almacen')->result();

        $all_cantidad_min = 0;
        foreach ($all_cantidad as $cantidad) {
            $temp = $cantidad != NULL ? $this->unidades_api_model->convert_minimo_um($producto_id, $cantidad->cantidad, $cantidad->fraccion) : 0;
            $all_cantidad_min += $temp;
        }
        $data = $this->unidades_api_model->get_cantidad_fraccion($producto_id, $all_cantidad_min - $stock_total_minimo, 0);

        return $data;
    }

    function set_stock_desglose($id_usuario, $prod)
    {
        $locales = $this->local_api_model->get_local_by_user($id_usuario);
        $id_producto = $prod;

        $data = array();
        foreach ($locales as $local) {
            $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $id_producto, 'id_local' => $local->local_id))->row();
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_api_model->convert_minimo_um($id_producto, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
            $data['locales'][] = $local;
            $data['stock_desgloses'][] = $this->unidades_api_model->get_cantidad_fraccion($id_producto, $old_cantidad_min, $local->local_id);
        }

        return $data;
    }
}