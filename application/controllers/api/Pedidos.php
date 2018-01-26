<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class Pedidos extends REST_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('venta_new/venta_new_api_model', 'venta');
        $this->load->model('inventario/inventario_api_model');
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

    function ultimos_post()
    {
        $estado = $this->input->post('estado');

        $params = array('estado' => $estado);

        $ventas = $this->venta->get_ventas($params);

        $data = array();
        foreach ($ventas as $venta) {
            $v = $this->venta->get_venta_detalle($venta->venta_id);

            $data['ventas'][] = $v;
        }

        $this->response($data, 200);
    }

    public function save_post()
    {
        $venta['id_usuario'] = $this->input->post('id_usuario');
        $venta['local_id'] = $this->input->post('local_venta_id');
        $venta['id_documento'] = $this->input->post('tipo_documento');
        $venta['id_cliente'] = $this->input->post('cliente_id');
        $venta['condicion_pago'] = $this->input->post('tipo_pago');
        $venta['id_moneda'] = $this->input->post('moneda_id');
        $venta['tasa_cambio'] = $this->input->post('tasa');

        $venta['venta_status'] = $this->input->post('venta_estado');
        $venta['fecha_venta'] = $this->input->post('fecha_venta');

        $venta['subtotal'] = $this->input->post('subtotal');
        $venta['impuesto'] = $this->input->post('impuesto');
        $venta['total_importe'] = $this->input->post('total_importe');

        $venta['vc_total_pagar'] = $this->input->post('vc_total_pagar');
        $venta['vc_importe'] = $this->input->post('vc_importe');
        $venta['vc_vuelto'] = $this->input->post('vc_vuelto');
        $venta['vc_forma_pago'] = $this->input->post('vc_forma_pago');
        $venta['vc_num_oper'] = $this->input->post('vc_num_oper');
        $venta['vc_tipo_tarjeta'] = $this->input->post('vc_tipo_tarjeta');

        $venta['c_dni_garante'] = null;
        $venta['c_inicial'] = $this->input->post('c_saldo_inicial') != '' ? $this->input->post('c_saldo_inicial') : 0;
        $venta['c_precio_contado'] = $this->input->post('c_precio_contado');
        $venta['c_precio_credito'] = $this->input->post('c_precio_credito');
        $venta['c_tasa_interes'] = $this->input->post('c_tasa_interes');
        $venta['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
        $venta['c_fecha_giro'] = $this->input->post('c_fecha_giro');

        $venta['caja_total_pagar'] = $this->input->post('caja_total_pagar');

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

        $sin_stock = $this->inventario_api_model->check_stock($validar_detalle);

        if (count($sin_stock) == 0) {
            $venta_id = 0;
            if ($venta['condicion_pago'] == '1') {
                $venta_id = $this->venta->save_venta_contado($venta, $detalles_productos, $traspasos);

            } elseif ($venta['condicion_pago'] == '2') {
                $venta_id = $this->venta->save_venta_credito($venta, $detalles_productos, $traspasos, $cuotas);
            }

            if ($venta_id) {
                $data['success'] = '1';
                //$data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
            } else
                $data['success'] = '0';

        } else {
            $data['success'] = '3';
            $data['sin_stock'] = json_encode($sin_stock);
        }

        $this->response($data, 200);
    }

    function anular_post()
    {
        $venta_id = $this->input->post('venta_id');
        $numero = $this->input->post('numero');
        $serie = $this->input->post('serie');
        $id_usuario = $this->input->post('id_usuario');

        $venta = $this->venta->anular_venta($venta_id, $serie, $numero, $id_usuario);

        if ($venta) {
            $result['response'] = 'success';
        } else {
            $result['response'] = 'failed';
        }

        $this->response($result, 200);
    }
}