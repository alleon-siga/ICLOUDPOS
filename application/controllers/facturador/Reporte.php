<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reporte extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('producto/producto_model');
        $this->load->model('facturador/facturador_model');
        $this->load->model('monedas/monedas_model');
        $this->load->model('local/local_model');
        $this->load->model('reporte_shadow/reporte_shadow_model');
        $this->load->model('facturacion/facturacion_model');
        if ($this->facturador_model->verify_session()) {
            
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function reporte($action = '') {
        switch ($action) {
            case 'filter': {
                    $params['moneda_id'] = $this->input->post('moneda_id');
                    if (!empty($this->input->post('fecha'))) {
                        $date_range = explode(" - ", $this->input->post('fecha'));
                        $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                        $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                    }
                    $data['lists'] = $this->reporte_shadow_model->getReporte($params);

                    $this->load->view('facturador/reporte/reporte_list', $data);
                    break;
                }
            case 'pdf': {
                    /* $params = json_decode($this->input->get('data'));
                      $date_range = explode(' - ', $params->fecha);
                      $input = array(
                      'local_id' => $params->local_id,
                      'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                      'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                      );
                      $data['lists'] = $this->reporte_shadow_model->getReporte($input);

                      $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                      $data['local_nombre'] = !empty($local->local_nombre)? $local->local_nombre: 'TODOS';
                      $data['local_direccion'] = !empty($local->direccion)? $local->direccion: 'TODOS';

                      $data['fecha_ini'] = $input['fecha_ini'];
                      $data['fecha_fin'] = $input['fecha_fin'];
                      $data['condicion_pago'] = $input['condicion_pago'];
                      $this->load->library('mpdf53/mpdf');
                      $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                      $html = $this->load->view('facturador/reporte/reporte_list_pdf', $data, true);
                      $mpdf->WriteHTML($html);
                      $mpdf->Output();
                      break; */
                }
            case 'excel': {
                    /* $params = json_decode($this->input->get('data'));
                      $date_range = explode(' - ', $params->fecha);
                      $input = array(
                      'local_id' => $params->local_id,
                      'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                      'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                      );
                      $data['lists'] = $this->reporte_shadow_model->getReporte($input);

                      $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                      $data['local_nombre'] = !empty($local->local_nombre)? $local->local_nombre: 'TODOS';
                      $data['local_direccion'] = !empty($local->direccion)? $local->direccion: 'TODOS';
                      echo $this->load->view('facturador/reporte/reporte_list_excel', $data, true);
                      break; */
                }
            default: {
                    $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
                    $dataCuerpo['cuerpo'] = $this->load->view('facturador/reporte/reporte', $data, true);
                    if ($this->input->is_ajax_request()) {
                        echo $dataCuerpo['cuerpo'];
                    } else {
                        $this->load->view('facturador/template', $dataCuerpo);
                    }
                    break;
                }
        }
    }

    //
    function relacion_comprobante($action = '') {
        switch ($action) {
            case 'filter': {
                    $params['tipo_ven']=$this->input->post('tipo_ven');
                    $params['local_id'] = $this->input->post('local_id');
                    $params['fecha_flag'] = $this->input->post('fecha_flag');
                    $date_range = explode(" - ", $this->input->post('fecha'));
                    $params['fecha_ini'] = date('Y-m-d', strtotime(str_replace("/", "-", $date_range[0])));
                    $params['fecha_fin'] = date('Y-m-d', strtotime(str_replace("/", "-", $date_range[1])));
                    $params['doc_id'] = $this->input->post('doc_id');
                    $params['estado_id'] = $this->input->post('estado_id');
                    $data['lists'] = $this->facturacion_model->get_relacion_comprobantes($params);
                    $this->load->view('facturador/reporte/reporte_fac_list', $data);
                    break;
                }
            case 'pdf': {
                    $params = json_decode($this->input->get('data'));
                    $date_range = explode(' - ', $params->fecha);
                    $data = array(
                        'tipo_ven' => $params->tipo_ven,
                        'local_id' => $params->local_id,
                        'doc_id' => $params->doc_id,
                        'estado_id' => $params->estado_id,
                        'fecha_flag' => $params->fecha_flag,
                        'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                        'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                    );
                    $data['lists'] = $this->facturacion_model->get_relacion_comprobantes($data);
                    $local = $this->db->get_where('local', array('int_local_id' => $data['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    $data['fecha_ini'] = $data['fecha_ini'];
                    $data['fecha_fin'] = $data['fecha_fin'];
                    $data['fecha_flag'] = $data['fecha_flag'];
                    $this->load->library('mpdf53/mpdf');
                    $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                    $html = $this->load->view('facturador/reporte/reporte_fac_pdf', $data, true);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output();
                    break;
                }
            case 'excel': {
                    $params = json_decode($this->input->get('data'));
                    $date_range = explode(' - ', $params->fecha);
                    $data = array(                        
                        'tipo_ven' => $params->tipo_ven,
                        'local_id' => $params->local_id,
                        'doc_id' => $params->doc_id,
                        'estado_id' => $params->estado_id,
                        'fecha_flag' => $params->fecha_flag,
                        'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                        'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                    );
                    $data['lists'] = $this->facturacion_model->get_relacion_comprobantes($data);
                    $local = $this->db->get_where('local', array('int_local_id' => $data['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    $data['fecha_ini'] = $data['fecha_ini'];
                    $data['fecha_fin'] = $data['fecha_fin'];
                    $data['fecha_flag'] = $data['fecha_flag'];
                    echo $this->load->view('facturador/reporte/reporte_fac_excel', $data, true);
                    break;
                }
            default: {
                    $data['locales'] = $this->local_model->get_all();
                    $dataCuerpo['cuerpo'] = $this->load->view('facturador/reporte/reporte_fac', $data, true);
                    if ($this->input->is_ajax_request()) {
                        echo $dataCuerpo['cuerpo'];
                    } else {
                        $this->load->view('menu/template', $dataCuerpo);
                    }
                    break;
                }
        }
    }

    function reporte_cg($action = '') {
        switch ($action) {
            case 'filter': {
                    $params['local_id'] = $this->input->post('local_id');
                    $params['marca_id'] = $this->input->post('marca_id');
                    $params['grupo_id'] = $this->input->post('grupo_id');
                    $params['familia_id'] = $this->input->post('familia_id');
                    $params['linea_id'] = $this->input->post('linea_id');
                    $params['producto_id'] = $this->input->post('producto_id');
                    $data['lists'] = $this->reporte_shadow_model->getReporte_cg($params);

                    $this->load->view('facturador/reporte/reporte_cg_list', $data);
                    break;
                }
            case 'pdf': {
                    $params = json_decode($this->input->get('data'));
                    $input = array(
                        'local_id' => $params->local_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_cg($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';

                    $this->load->library('mpdf53/mpdf');
                    $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                    $html = $this->load->view('facturador/reporte/reporte_cg_list_pdf', $data, true);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output();
                    break;
                }
            case 'excel': {
                    $params = json_decode($this->input->get('data'));
                    $input = array(
                        'local_id' => $params->local_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_cg($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    echo $this->load->view('facturador/reporte/reporte_cg_list_excel', $data, true);
                    break;
                }
            default: {
                    $data['locales'] = $this->local_model->get_all();
                    $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                    $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                    $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                    $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                    $data["productos"] = $this->producto_model->get_productos_list();
                    $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                    $dataCuerpo['cuerpo'] = $this->load->view('facturador/reporte/reporte_cg', $data, true);
                    if ($this->input->is_ajax_request()) {
                        echo $dataCuerpo['cuerpo'];
                    } else {
                        $this->load->view('facturador/template', $dataCuerpo);
                    }
                    break;
                }
        }
    }

    function reporte_cv($action = '') {
        switch ($action) {
            case 'filter': {
                    $params['moneda_id'] = $this->input->post('moneda_id');
                    $params['local_id'] = $this->input->post('local_id');
                    $params['marca_id'] = $this->input->post('marca_id');
                    $params['grupo_id'] = $this->input->post('grupo_id');
                    $params['familia_id'] = $this->input->post('familia_id');
                    $params['linea_id'] = $this->input->post('linea_id');
                    $params['producto_id'] = $this->input->post('producto_id');
                    if (!empty($this->input->post('fecha'))) {
                        $date_range = explode(" - ", $this->input->post('fecha'));
                        $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                        $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                    }
                    $data['lists'] = $this->reporte_shadow_model->getReporte_cg($params);

                    $this->load->view('facturador/reporte/reporte_cv_list', $data);
                    break;
                }
            case 'pdf': {
                    $params = json_decode($this->input->get('data'));
                    $input = array(
                        'local_id' => $params->local_id,
                        'moneda_id' => $params->moneda_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id,
                        'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                        'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_cv($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    $data['fecha_ini'] = $input['fecha_ini'];
                    $data['fecha_fin'] = $input['fecha_fin'];
                    $this->load->library('mpdf53/mpdf');
                    $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                    $html = $this->load->view('facturador/reporte/reporte_cv_list_pdf', $data, true);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output();
                    break;
                }
            case 'excel': {
                    $params = json_decode($this->input->get('data'));
                    $input = array(
                        'local_id' => $params->local_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id,
                        'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                        'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_cg($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    echo $this->load->view('facturador/reporte/reporte_cv_list_excel', $data, true);
                    break;
                }
            default: {
                    $data['locales'] = $this->local_model->get_all();
                    $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                    $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                    $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                    $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                    $data["productos"] = $this->producto_model->get_productos_list();
                    $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
                    $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                    $dataCuerpo['cuerpo'] = $this->load->view('facturador/reporte/reporte_cv', $data, true);
                    if ($this->input->is_ajax_request()) {
                        echo $dataCuerpo['cuerpo'];
                    } else {
                        $this->load->view('facturador/template', $dataCuerpo);
                    }
                    break;
                }
        }
    }

    //Obtener el reporte de ventas por documentos (19-10-2018) Carlos Camargo
    function reporte_vp($action = '') {
        switch ($action) {
            case 'filter': {
                    $params['local_id'] = $this->input->post('local_id');
                    $params['estado_cr_id'] = $this->input->post('estado_cr_id');                    
                    $params['marca_id'] = $this->input->post('marca_id');
                    $params['grupo_id'] = $this->input->post('grupo_id');
                    $params['familia_id'] = $this->input->post('familia_id');
                    $params['linea_id'] = $this->input->post('linea_id');
                    $params['producto_id'] = $this->input->post('producto_id');
                    if (!empty($this->input->post('fecha'))) {
                        $date_range = explode(" - ", $this->input->post('fecha'));
                        $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                        $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                    }
                    
                    $data['lists'] = $this->reporte_shadow_model->getReporte_vp($params);

                    $this->load->view('facturador/reporte/reporte_vp_list', $data);
                    break;
                }
            case 'pdf': {
                    $params = json_decode($this->input->get('data'));
                    if (!empty($params->fecha)) {
                        $date_range = explode(" - ", $params->fecha);
                        $params->fecha_ini = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                        $params->fecha_fin = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                    }
                    $input = array(
                        'local_id' => $params->local_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id,
                        'fecha_ini' => $params->fecha_ini,
                        'fecha_fin' => $params->fecha_fin
                       
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_vp($input);
                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    $data['fecha_ini'] = $input['fecha_ini'];
                    $data['fecha_fin'] = $input['fecha_fin'];
                    $this->load->library('mpdf53/mpdf');
                    $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                    $html = $this->load->view('facturador/reporte/reporte_vp_pdf', $data, true);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output();
                    break;
                }
            case 'excel': {
                    $params = json_decode($this->input->get('data'));
                    if (!empty($params->fecha)) {
                        $date_range = explode(" - ", $params->fecha);
                        $params->fecha_ini = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                        $params->fecha_fin = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                    }
                    $input = array(
                        'local_id' => $params->local_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id,
                        'fecha_ini' => $params->fecha_ini,
                        'fecha_fin' => $params->fecha_fin
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_vp($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    $data['fecha_ini'] = $input['fecha_ini'];
                    $data['fecha_fin'] = $input['fecha_fin'];
                    echo $this->load->view('facturador/reporte/reporte_vp_excel', $data, true);
                    break;
                }
            default: {
                    $data['locales'] = $this->local_model->get_all();
                    $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                    $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                    $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                    $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                    $data["productos"] = $this->producto_model->get_productos_list();
                    $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
                    $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                    $dataCuerpo['cuerpo'] = $this->load->view('facturador/reporte/reporte_vp', $data, true);
                    if ($this->input->is_ajax_request()) {
                        echo $dataCuerpo['cuerpo'];
                    } else {
                        $this->load->view('facturador/template', $dataCuerpo);
                    }
                    break;
                }
        }
    }
    //Obtener el reporte de ventas por documentos (22-10-2018) Carlos Camargo
    function reporte_vpr($action = '') {
        switch ($action) {
            case 'filter': {
                    $params['local_id'] = $this->input->post('local_id');
                    $params['moneda_id'] = $this->input->post('moneda_id');
                    $params['estado_cr_id'] = $this->input->post('estado_cr_id');                    
                    $params['marca_id'] = $this->input->post('marca_id');
                    $params['grupo_id'] = $this->input->post('grupo_id');
                    $params['familia_id'] = $this->input->post('familia_id');
                    $params['linea_id'] = $this->input->post('linea_id');
                    $params['producto_id'] = $this->input->post('producto_id');
                    if (!empty($this->input->post('fecha'))) {
                        $date_range = explode(" - ", $this->input->post('fecha'));
                        $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                        $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                    }
                    $data['lists'] = $this->reporte_shadow_model->getReporte_vpr($params);

                    $this->load->view('facturador/reporte/reporte_vpr_list', $data);
                    break;
                }
            case 'pdf': {
                    $params = json_decode($this->input->get('data'));
                    $input = array(
                        'local_id' => $params->local_id,
                        'moneda_id' => $params->moneda_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id,
                        'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                        'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_vpr($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    $data['fecha_ini'] = $input['fecha_ini'];
                    $data['fecha_fin'] = $input['fecha_fin'];
                    $this->load->library('mpdf53/mpdf');
                    $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                    $html = $this->load->view('facturador/reporte/reporte_vpr_pdf', $data, true);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output();
                    break;
                }
            case 'excel': {
                    $params = json_decode($this->input->get('data'));
                    $input = array(
                        'local_id' => $params->local_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'estado_cr_id' => $params->estado_cr_id,
                        'marca_id' => $params->marca_id,
                        'grupo_id' => $params->grupo_id,
                        'familia_id' => $params->familia_id,
                        'linea_id' => $params->linea_id,
                        'producto_id' => $params->producto_id,
                        'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                        'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                    );
                    $data['lists'] = $this->reporte_shadow_model->getReporte_vpr($input);

                    $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                    $data['local_nombre'] = !empty($local->local_nombre) ? $local->local_nombre : 'TODOS';
                    $data['local_direccion'] = !empty($local->direccion) ? $local->direccion : 'TODOS';
                    echo $this->load->view('facturador/reporte/reporte_vpr_excel', $data, true);
                    break;
                }
            default: {
                    $data['locales'] = $this->local_model->get_all();
                    $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                    $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                    $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                    $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                    $data["productos"] = $this->producto_model->get_productos_list();
                    $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
                    $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                    $dataCuerpo['cuerpo'] = $this->load->view('facturador/reporte/reporte_vp', $data, true);
                    if ($this->input->is_ajax_request()) {
                        echo $dataCuerpo['cuerpo'];
                    } else {
                        $this->load->view('facturador/template', $dataCuerpo);
                    }
                    break;
                }
        }
    }
}
