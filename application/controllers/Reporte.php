<?php

class Reporte extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('local/local_model');
            $this->load->model('producto/producto_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('reporte/reporte_model');
        }else{
            redirect(base_url(), 'refresh');
        }
    }

    function productoVendido($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['lists'] = $this->reporte_model->getProductoVendido($params);

                $this->load->view('menu/reportes/productoVendido_list', $data);
                break;
            }
            case 'grafico': {
                $params['local_id'] = $this->input->post('local_id');
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['lists'] = $this->reporte_model->getProductoVendido($params);
                echo json_encode($data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getProductoVendido($input);

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reportes/productoVendido_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getProductoVendido($input);

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                echo $this->load->view('menu/reportes/productoVendido_list_excel', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }
                $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                $data["productos"] = $this->producto_model->get_productos_list();
                $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/productoVendido', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function selectProducto()
    {
        $params['marca_id'] = $this->input->post('marca_id');
        $params['grupo_id'] = $this->input->post('grupo_id');
        $params['familia_id'] = $this->input->post('familia_id');
        $params['linea_id'] = $this->input->post('linea_id');
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data["productos"] = $this->producto_model->get_productos_list2($params);
        $this->load->view('menu/reportes/selectProducto', $data);
    }

    function ventaSucursal($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['lists'] = $this->reporte_model->getVentaSucursal($params);
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }
                $this->load->view('menu/reportes/ventaSucursal_list', $data);
                break;
            }
            case 'grafico': {
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['lists'] = $this->reporte_model->getVentaSucursal($params);
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }
                echo json_encode($data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getVentaSucursal($input);
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reportes/ventaSucursal_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getVentaSucursal($input);
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                echo $this->load->view('menu/reportes/ventaSucursal_list_excel', $data, true);
                break;
            }
            default: {
                $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                $data["productos"] = $this->producto_model->get_productos_list();
                $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/ventaSucursal', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function ventaEmpleado($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['moneda_id'] = $this->input->post('moneda_id');
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $params['tipo'] = $this->input->post('tipo');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();

                $data['lists'] = $this->reporte_model->getVentaEmpleado($params);

                $this->load->view('menu/reportes/ventaEmpleado_list', $data);
                break;
            }
            case 'grafico': {
                $params['local_id'] = $this->input->post('local_id');
                $params['moneda_id'] = $this->input->post('moneda_id');
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $params['tipo'] = $this->input->post('tipo');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();

                $data['lists'] = $this->reporte_model->getVentaEmpleado($params);
                echo json_encode($data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'tipo' => $params->tipo,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getVentaEmpleado($input);
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reportes/ventaEmpleado_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'tipo' => $params->tipo,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getVentaEmpleado($input);
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                echo $this->load->view('menu/reportes/ventaEmpleado_list_excel', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }
                $data["monedas"] = $this->monedas_model->get_all();
                $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                $data["productos"] = $this->producto_model->get_productos_list();
                $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/ventaEmpleado', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function margenUtilidad($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['moneda_id'] = $this->input->post('moneda_id');
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['lists'] = $this->reporte_model->getMargenUtilidad($params);

                $this->load->view('menu/reportes/margenUtilidad_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getMargenUtilidad($input);

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reportes/productoVendido_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );

                $data['lists'] = $this->reporte_model->getMargenUtilidad($input);

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                echo $this->load->view('menu/reportes/productoVendido_list_excel', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }
                $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
                $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                $data["productos"] = $this->producto_model->get_productos_list();
                $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/margenUtilidad', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }
}