<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class Pedidos extends REST_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->model('venta_new/venta_new_model', 'venta');
        $this->load->model('inventario/inventario_model');
        $this->load->model('usuario/usuario_api_model');
        $this->load->model('correlativos/correlativos_model');
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

    public function ultimos_post()
    {
        $estado = $this->input->post('estado');
        $id_usuario = $this->input->post('id_usuario');

        $data = array();
        if ($estado == "COMPLETADO") {
            $id_cliente = $this->input->post('id_cliente');
            $fecha_ini = $this->input->post('fecha_ini');
            $fecha_fin = $this->input->post('fecha_fin');

            $where['estado'] = $estado;

            if ($id_cliente) {
                $where['id_cliente'] = $id_cliente;
            }

            if ($fecha_ini) {
                $where['fecha_ini'] = date('Y-m-d', strtotime($fecha_ini));
                $where['fecha_fin'] = date('Y-m-d', strtotime($fecha_fin));
            }

            $res = $this->usuario_api_model->get_venta_user($id_usuario);
            if (empty($res)) {
                $data['today']['count'] = count($this->db->get_where('venta', array('fecha >=' => date('Y-m-d 00:00:00'),
                    'fecha <=' => date('Y-m-d 23:59:59')))->result());

            } else {
                $where['usuarios_id'] = $id_usuario;

                $data['today']['count'] = count($this->db->get_where('venta', array('fecha >=' => date('Y-m-d 00:00:00'),
                    'fecha <=' => date('Y-m-d 23:59:59'), 'id_vendedor' => $id_usuario))->result());
            }

        } else {
            $where = array('estado' => $estado);
        }

        $ventas = $this->venta->get_ventas($where, "caja");

        $data['ventas'] = array();
        foreach ($ventas as $venta) {
            $v = $this->venta->get_venta_detalle($venta->venta_id);

            $data['ventas'][] = $v;
        }

       $this->response($data, 200);
    }

    public function last_venta_get()
    {
        $data = array();
        $last_id = $this->venta->get_last_id();

        if ($last_id) {
            $data['last'] = $last_id;
            $this->response($data, 200);

        } else {
            $data['last'] = array();
            $this->response($data, 200);
        }
    }

    public function next_corr_post()
    {
        $local_id = $this->input->post('local_id');
        $doc_id = $this->input->post('doc_id');

        $data = array();
        $correlativo = $this->correlativos_model->get_correlativo($local_id, $doc_id);

        if ($correlativo) {
            $data['next_corr'] = $correlativo->serie . '-' . sumCod($correlativo->correlativo, 6);
            $this->response($data, 200);

        } else {
            $data['next_corr'] = "";
            $this->response($data, 200);
        }
    }

    public function fact_elect_post()
    {
        $venta_id = $this->input->post('venta_id');

        $fact = $this->db->get_where('facturacion', array('ref_id' => $venta_id))->row();
        $data['doc_nro'] = $fact->documento_numero;
        $data['hash'] = $fact->hash_cpe;
        $data['url_code'] = md5($fact->id);
        $data['tipo_doc'] = $fact->documento_tipo;
        $data['doc_nro_fixed'] = str_replace('-', '|', $fact->documento_numero);
        $data['total_tributo_igv'] = $fact->impuesto;
        $data['total_venta'] = $fact->total;
        $data['fecha_emision'] = $fact->fecha;
        $data['clie_tipo_ident'] = $fact->total > 700 ? $fact->cliente_tipo : "-";
        $data['clie_nro_doc'] = $fact->total > 700 ? $fact->cliente_identificacion : "-";
        $data['gravadas'] = $fact->total_gravadas;
        $data['exoneradas'] = $fact->total_exoneradas;
        $data['inafectas'] = $fact->total_inafectas;
        $data['subtotal'] = $fact->subtotal;
        $data['impuesto'] = $fact->impuesto;

        $fact_emisor = $this->db->get('facturacion_emisor')->row();
        $data['ruc'] = $fact_emisor->ruc;
        $data['emp_nombre'] = $fact_emisor->razon_social;
        $data['emp_direccion'] = $fact_emisor->direccion;

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
        //$venta['tipo_impuesto'] = $this->input->post('tipo_impuesto');

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
        $venta['c_tasa_interes'] = $this->input->post('c_tasa_interes');
        $venta['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
        $venta['c_fecha_giro'] = $this->input->post('c_fecha_giro');
        $venta['c_periodo_gracia'] = $this->input->post('c_periodo_gracia');

        $venta['caja_total_pagar'] = $this->input->post('caja_total_pagar');
        $venta['dni_garante'] = $this->input->post('c_garante') != "null" ? $this->input->post('c_garante') : null;
        $venta['tipo_impuesto'] = 1;
        $venta['comprobante_id'] = 0;
        $venta['venta_nota'] = "";
        $venta['fact_elect'] = $this->input->post('fact_elect');
        $venta['latitud'] = $this->input->post('latitud') != "null" ? $this->input->post('latitud') : null;
        $venta['longitud'] = $this->input->post('longitud') != "null" ? $this->input->post('longitud') : null;
        $venta['plataforma'] = 1;

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
            $venta_id = 0;
            if ($venta['condicion_pago'] == '1') {
                $venta_id = $this->venta->save_venta_contado($venta, $detalles_productos, $traspasos);

            } elseif ($venta['condicion_pago'] == '2') {
                $venta_id = $this->venta->save_venta_credito($venta, $detalles_productos, $traspasos, $cuotas);
            }

            if ($venta_id) {
                $data['success'] = '1';

                if ($venta['venta_status'] == 'COMPLETADO' && $venta['fact_elect'] == '1' &&
                    $venta['condicion_pago'] == '1' && $venta['id_documento'] != '6') {

                    $fact = $this->db->get_where('facturacion', array('ref_id' => $venta_id))->row();
                    $data['doc_nro'] = $fact->documento_numero;
                    $data['hash'] = $fact->hash_cpe;
                    $data['url_code'] = md5($fact->id);
                    $data['tipo_doc'] = $fact->documento_tipo;
                    $data['doc_nro_fixed'] = str_replace('-', '|', $fact->documento_numero);
                    $data['total_tributo_igv'] = $fact->impuesto;
                    $data['total_venta'] = $fact->total;
                    $data['fecha_emision'] = $fact->fecha;
                    $data['clie_tipo_ident'] = $fact->total > 700 ? $fact->cliente_tipo : "-";
                    $data['clie_nro_doc'] = $fact->total > 700 ? $fact->cliente_identificacion : "-";
                    $data['gravadas'] = $fact->total_gravadas;
                    $data['exoneradas'] = $fact->total_exoneradas;
                    $data['inafectas'] = $fact->total_inafectas;
                    $data['subtotal'] = $fact->subtotal;
                    $data['impuesto'] = $fact->impuesto;

                    $fact_emisor = $this->db->get('facturacion_emisor')->row();
                    $data['ruc'] = $fact_emisor->ruc;
                    $data['emp_nombre'] = $fact_emisor->razon_social;
                    $data['emp_direccion'] = $fact_emisor->direccion;
                }

            } else
                $data['success'] = '0';

        } else {
            $data['success'] = '3';
            $data['sin_stock'] = json_encode($sin_stock);
        }

        $this->response($data, 200);
    }

    public function facturar_venta_post()
    {
        // Obtengo los parametros enviados
        $venta_id = $this->input->post('venta_id');
        $doc_id = $this->input->post('doc_id');
        $id_usuario = $this->input->post('id_usuario');

        // Valido que los parametros esten correctos
        $venta_id = $venta_id != "" && is_numeric($venta_id) ? $venta_id : false;
        $doc_id = $doc_id != "" && is_numeric($doc_id) && ($doc_id == 1 || $doc_id == 3 || $doc_id == 6) ? $doc_id : false;

        $data['success'] = '1';
        if ($venta_id == false || $doc_id == false) {
            $data['success'] = '0';
        }

        // Comienzo el proceso de facturacion de la venta
        $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

        // Hago validaciones de logica del negocio para evitar conflictos
        if ($data['venta']->venta_status != 'COMPLETADO' || $data['venta']->serie != NULL || $data['venta']->numero != NULL) {
            $data['success'] = '2';
        }

        if ($data['success'] == '1') {
            $this->db->trans_begin();
            $this->venta->facturar_venta($venta_id, $doc_id, $id_usuario);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();

                $data['success'] = '3';
            }
            $this->db->trans_commit();
        }

        $this->response($data, 200);
    }

    public function anular_post()
    {
        $venta_id = $this->input->post('venta_id');
        $numero = $this->input->post('numero');
        $serie = $this->input->post('serie');
        $id_usuario = $this->input->post('id_usuario');

        $venta = $this->venta->anular_venta($venta_id, $serie, $numero, 3, 0, 01, $id_usuario);

        if ($venta) {
            $result['response'] = 'success';
        } else {
            $result['response'] = 'failed';
        }

        $this->response($result, 200);
    }
}