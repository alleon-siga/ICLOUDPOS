<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class gastos extends MY_Controller {

    function __construct() {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('gastos/gastos_model');
            $this->load->model('tiposdegasto/tipos_gasto_model');
            $this->load->model('local/local_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('cajas/cajas_model');
            $this->load->model('metodosdepago/metodos_pago_model');
            $this->load->model('proveedor/proveedor_model');
            $this->load->model('impuesto/impuestos_model');
            $this->load->model('condicionespago/condiciones_pago_model');
            $this->load->model('ingreso/ingreso_model');
            $this->load->model('documentos/documentos_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    /** carga cuando listas los proveedores */
    function index() {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['tipos_gastos'] = $this->tipos_gasto_model->get_all();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["usuarios"] = $this->db->get_where('usuario', array('activo' => 1))->result();
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/gastos/gastos', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function lista_gasto() {

        $date_range = explode(" - ", $this->input->post('fecha'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);

        $params = array(
            'fecha_ini' => date('Y-m-d H:i:s', strtotime($fecha_ini . ' 00:00:00')),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime($fecha_fin . ' 23:59:59')),
            'persona_gasto' => $this->input->post('persona_gasto'),
            'id_moneda' => $this->input->post('moneda_id'),
            'status_gastos' => $this->input->post('estado_id'),
        );

        $tipo_gasto = $this->input->post('tipo_gasto');
        if ($tipo_gasto != "-")
            $params['tipo_gasto'] = $tipo_gasto;

        $persona_gasto = $this->input->post('persona_gasto');
        if ($persona_gasto == 1) {
            $proveedor = $proveedor = $this->input->post('proveedor');
            if ($proveedor != "-")
                $params['proveedor'] = $proveedor;
        }
        if ($persona_gasto == 2) {
            $usuario = $usuario = $this->input->post('usuario');
            if ($usuario != "-")
                $params['usuario'] = $usuario;
        }

        $local_id = $this->input->post('local_id');
        if ($local_id != "0") {
            $params['local_id'] = $local_id;
        }

        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['id_moneda']))->row();
        $data['gastoss'] = $this->gastos_model->get_all($params);
        $data['gastos_totales'] = $this->gastos_model->get_totales_gasto($params);

        $this->load->view('menu/gastos/gasto_lista', $data);
    }

    function form($id = FALSE) {

        $data = array();
        $data['gastos'] = array();
        $data["tipo_pagos"] = $this->condiciones_pago_model->get_all();
        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data['tiposdegasto'] = $this->tipos_gasto_model->get_all();
        $data["metodo_pago"] = $this->metodos_pago_model->get_all();
        $data['local'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data["monedas"] = $this->monedas_model->get_all();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["usuarios"] = $this->db->get_where('usuario', array('activo' => 1))->result();
        $data["documentos"] = $this->db->get_where('documentos', array('gastos' => 1))->result();
        $data['cuentas'] = $this->db->select('caja_desglose.*, caja.local_id, caja.moneda_id, moneda.nombre AS moneda_nombre, moneda.simbolo,banco.banco_status as banco')
                        ->from('caja_desglose')
                        ->join('caja', 'caja.id = caja_desglose.caja_id')
                        ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                        ->join('banco', 'banco.cuenta_id = caja_desglose.id', 'left')
                        ->where('moneda.status_moneda', 1)
                        ->get()->result();

        if ($id != FALSE) {
            $data['gastos'] = $this->gastos_model->get_by('id_gastos', $id);
        }
        $this->load->view('menu/gastos/form', $data);
    }

    function guardar() {
        $id = $this->input->post('gastos_id');

        $persona_gasto = $this->input->post('persona_gasto');
        if ($persona_gasto == 1) {
            $proveedor = $this->input->post('proveedor');
            $usuario = NULL;
        } elseif ($persona_gasto == 2) {
            $proveedor = NULL;
            $usuario = $this->input->post('usuario');
        }
        $status_gastos = '1'; //pendiente
        $tipo_gasto = $this->db->get_where('tipos_gasto', array('id_tipos_gasto' => $this->input->post('tipo_gasto')))->row();
        if ($this->input->post('tipo_pago') == '2' && $tipo_gasto->nombre_tipos_gasto != 'PRESTAMO BANCARIO') { //Si es al credito y es diferente a prestamo bancario
            $status_gastos = '0'; //confirmado
        }

        $cuenta = $this->db->join('caja', 'caja.id = caja_desglose.caja_id')
                        ->get_where('caja_desglose', array('caja_desglose.id' => $this->input->post('cuenta_id')))->row();

        $tasa_cambio = 0;
        if (isset($cuenta->moneda_id) && $cuenta->moneda_id!='') {
            if ($cuenta->moneda_id != 1029) {
                $tasa = $this->monedas_model->get_by('id_moneda', $cuenta->moneda_id);
                $tasa_cambio = $tasa['tasa_soles'];
            }
            $idmoneda=$cuenta->moneda_id;
        } else {
            if ($this->input->post('tipo_moneda') != 1029) {
                $tasa = $this->monedas_model->get_by('id_moneda', $this->input->post('tipo_moneda'));
                $tasa_cambio = $tasa['tasa_soles'];
            }
            $idmoneda=$this->input->post('tipo_moneda');
        }

        $gastos = array(
            'id_gastos' => $id,
            'fecha' => date('Y-m-d', strtotime($this->input->post('fecha'))) . " " . date("H:i:s"),
            'fecha_registro' => date('Y-m-d H:i:s'),
            'descripcion' => $this->input->post('descripcion'),
            'total' => $this->input->post('total'),
            'tipo_gasto' => $this->input->post('tipo_gasto'),
            'local_id' => $this->input->post('filter_local_id'),
            'status_gastos' => $status_gastos,
            'gasto_usuario' => $this->session->userdata('nUsuCodigo'),
            'metodo_pago' => $this->input->post('metodo_pago'),
            'cuenta_id' => $this->input->post('cuenta_id'),
            'proveedor_id' => $proveedor,
            'usuario_id' => $usuario,
            'responsable_id' => $this->session->userdata('nUsuCodigo'),
            'gravable' => $this->input->post('gravable'),
            'id_documento' => $this->input->post('cboDocumento'),
            'serie' => $this->input->post('doc_serie'),
            'numero' => $this->input->post('doc_numero'),
            'id_impuesto' => $this->input->post('id_impuesto'),
            'subtotal' => $this->input->post('subtotal'),
            'impuesto' => $this->input->post('impuesto'),
            'moneda_id' => $idmoneda,
            'tipo_pago' => $this->input->post('tipo_pago'),
            'c_tasa_interes' => $this->input->post('c_tasa_interes'),
            'capital' => $this->input->post('c_precio_contado'),
            'tasa_cambio' => $tasa_cambio
        );

        $detalle = array();
        if (!empty($this->input->post('txtDesc')[0])) {
            for ($x = 0; $x < count($this->input->post('txtDesc')); $x++) {
                $detalle[$x] = array(
                    'id' => $this->input->post('txtId')[$x],
                    'descripcion' => $this->input->post('txtDesc')[$x],
                    'cantidad' => $this->input->post('txtCant')[$x],
                    'precio' => $this->input->post('txtPrec')[$x],
                    'impuesto' => $this->input->post('txtImp')[$x],
                    'subtotal' => $this->input->post('txtSub')[$x],
                    'total' => $this->input->post('txtTot')[$x]
                );
            }
        }

        //Cuando es al credito
        if ($this->input->post('tipo_pago') == '2') {
            if ($this->input->post('persona_gasto') == '1') { //Proveedor
                $cboProveedor = $this->input->post('proveedor', true);
                $cboUsuario = '0';
            } else { //Trabajador
                $cboUsuario = $this->input->post('usuario', true);
                $cboProveedor = '0';
            }

            if ($this->input->post('gravable') == '0') { //no
                $tipo_impuesto = '3';
            } else { //si
                $tipo_impuesto = '1';
            }

            if ($tipo_gasto->nombre_tipos_gasto == 'PRESTAMO BANCARIO') {
                $status = 'PENDIENTE';
            } else {
                $status = 'COMPLETADO';
            }
            $doc = $this->documentos_model->get_by('id_doc', $this->input->post('cboDocumento', true));

            $comp_cab_pie = array(
                'fecReg' => date("Y-m-d H:i:s"),
                'fecEmision' => date('Y-m-d H:i:s', strtotime($this->input->post('fecha', true))),
                'doc_serie' => $this->input->post('doc_serie', true),
                'doc_numero' => $this->input->post('doc_numero', true),
                'cboTipDoc' => $doc->des_doc,
                'cboProveedor' => $cboProveedor,
                'subTotal' => $this->input->post('subtotal', true),
                'montoigv' => $this->input->post('impuesto', true),
                'totApagar' => $this->input->post('total', true),
                'tipo_ingreso' => 'GASTO',
                'pago' => 'CREDITO',
                'local_id' => $this->input->post('filter_local_id', true),
                'ingreso_observacion' => $this->input->post('descripcion', true),
                'id_moneda' => $idmoneda,
                'tasa_cambio' => NULL,
                'status' => $status,
                'facturar' => '0',
                'tipo_impuesto' => $tipo_impuesto,
                'cboUsuario' => $cboUsuario,
                'metodo_pago' => $this->input->post('metodo_pago')
            );
            $credito['c_inicial'] = $this->input->post('c_saldo_inicial') != '' ? $this->input->post('c_saldo_inicial') : 0;
            $credito['c_precio_contado'] = $this->input->post('c_precio_contado'); //capital
            $credito['c_tasa_interes'] = $this->input->post('c_tasa_interes'); //interes
            $credito['c_comision'] = $this->input->post('c_comision'); //comision
            $credito['c_precio_credito'] = $this->input->post('c_precio_credito');  //total de cuota
            $credito['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
            $credito['c_fecha_giro'] = $this->input->post('c_fecha_giro');
            $credito['c_periodo_gracia'] = $this->input->post('c_periodo_gracia');
            $cuotas = json_decode($this->input->post('cuotas', true));
        }

        if (empty($id)) {
            $resultado = $this->gastos_model->insertar($gastos, $detalle);
        } else {
            $resultado = $this->gastos_model->update($gastos, $detalle);
        }

        if ($this->input->post('tipo_pago') == '2') {
            $comp_cab_pie['id_gastos'] = $resultado;
            $resultado = $this->ingreso_model->insertar_compra($comp_cab_pie, null, $credito, $cuotas);
        }

        if ($resultado != FALSE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);
    }

    function eliminar() {
        $id = $this->input->post('id');


        $this->db->where('ref_id', $id);
        $this->db->where('tipo', 'GASTOS');
        $this->db->where('IO', 2);
        $this->db->where('estado', 0);
        $this->db->delete('caja_pendiente');

        $this->db->where('id_gastos', $id);
        $this->db->where('status_gastos', 1);
        $this->db->delete('gastos');

        $json['success'] = 'Se ha eliminado exitosamente';

        echo json_encode($json);
    }

    function historial_pdf() {
        $get = json_decode($this->input->get('data'));
        $date_range = explode(" - ", $get->fecha);
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);

        $params = array(
            'fecha_ini' => date('Y-m-d H:i:s', strtotime($fecha_ini . ' 00:00:00')),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime($fecha_fin . ' 23:59:59')),
            'persona_gasto' => $get->persona_gasto,
            'id_moneda' => $get->moneda_id,
            'status_gastos' => $get->estado_id,
        );

        $tipo_gasto = $get->tipo_gasto;
        if ($tipo_gasto != "-")
            $params['tipo_gasto'] = $tipo_gasto;

        $persona_gasto = $get->persona_gasto;
        if ($persona_gasto == 1) {
            $proveedor = $proveedor = $get->proveedor;
            if ($proveedor != "-")
                $params['proveedor'] = $proveedor;
        }
        if ($persona_gasto == 2) {
            $usuario = $usuario = $get->usuario;
            if ($usuario != "-")
                $params['usuario'] = $usuario;
        }
        $local_id = $get->local_id;
        if ($local_id != "0") {
            $params['local_id'] = $local_id;
        }

        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['id_moneda']))->row();
        $data['gastoss'] = $this->gastos_model->get_all($params);
        $data['gastos_totales'] = $this->gastos_model->get_totales_gasto($params);

        $data['fecha_ini'] = $params['fecha_ini'];
        $data['fecha_fin'] = $params['fecha_fin'];

        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/gastos/gasto_lista_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function historial_excel() {
        $get = json_decode($this->input->get('data'));
        $date_range = explode(" - ", $get->fecha);
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);

        $params = array(
            'fecha_ini' => date('Y-m-d H:i:s', strtotime($fecha_ini . ' 00:00:00')),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime($fecha_fin . ' 23:59:59')),
            'persona_gasto' => $get->persona_gasto,
            'id_moneda' => $get->moneda_id,
            'status_gastos' => $get->estado_id,
        );

        $tipo_gasto = $get->tipo_gasto;
        if ($tipo_gasto != "-")
            $params['tipo_gasto'] = $tipo_gasto;

        $persona_gasto = $get->persona_gasto;
        if ($persona_gasto == 1) {
            $proveedor = $proveedor = $get->proveedor;
            if ($proveedor != "-")
                $params['proveedor'] = $proveedor;
        }
        if ($persona_gasto == 2) {
            $usuario = $usuario = $get->usuario;
            if ($usuario != "-")
                $params['usuario'] = $usuario;
        }
        $local_id = $get->local_id;
        if ($local_id != "0") {
            $params['local_id'] = $local_id;
        }
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['id_moneda']))->row();
        $data['gastoss'] = $this->gastos_model->get_all($params);
        $data['gastos_totales'] = $this->gastos_model->get_totales_gasto($params);

        $data['fecha_ini'] = $params['fecha_ini'];
        $data['fecha_fin'] = $params['fecha_fin'];

        echo $this->load->view('menu/gastos/gasto_lista_excel', $data, true);
    }

    function detalle($id = '') {
        $data['detalles'] = $this->gastos_model->get_detalle('id_gastos', $id);
        echo $this->load->view('menu/gastos/detalle', $data, true);
    }

    function editarDetalle() {
        $detalle = array();
        if (!empty($this->input->post('txtDesc')[0])) {
            for ($x = 0; $x < count($this->input->post('txtDesc')); $x++) {
                $detalle[$x] = array(
                    'id' => $this->input->post('txtId')[$x],
                    'descripcion' => $this->input->post('txtDesc')[$x],
                    'cantidad' => $this->input->post('txtCant')[$x],
                    'precio' => $this->input->post('txtPrec')[$x],
                    'impuesto' => $this->input->post('txtImp')[$x],
                    'subtotal' => $this->input->post('txtSub')[$x],
                    'total' => $this->input->post('txtTot')[$x]
                );
                $resultado = $this->gastos_model->editarDetalle($detalle);
            }
        }

        if ($resultado != FALSE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);
    }

    function deleteDetalle($id) {
        $resultado = $this->gastos_model->deleteDetalle($id);
        if ($resultado != FALSE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);
    }

    function dialog_gasto_credito() {
        echo $this->load->view('menu/gastos/dialog_gasto_credito', array(), true);
    }

    function dialog_gasto_prestamo() {
        echo $this->load->view('menu/gastos/dialog_gasto_prestamo', array(), true);
    }

}
