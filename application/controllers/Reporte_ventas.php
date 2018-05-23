<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_ventas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('reporte_venta/reporte_venta_model');
            $this->load->model('local/local_model');
            $this->load->model('usuario/usuario_model');
            $this->load->model('reporte_venta/rcliente_estado_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function cliente_estado($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Estado de Cuenta del Cliente';

        switch ($action) {
            case 'filter': {
                $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => !empty($this->input->post('cliente_id'))? implode(",", $this->input->post('cliente_id')): '',
                    'moneda_id' => $this->input->post('moneda_id'),
                    'local_id' => $this->input->post('local_id'),
                    'estado' => $this->input->post('estado')
                ));

                $data['form_filter'] = true;

                $data['local'] = $this->db->get_where('local', array('int_local_id' => $this->input->post('local_id')))->row();
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $this->input->post('moneda_id')))->row();

                echo $this->load->view('menu/reporte_venta/cliente_estado/tabla', $data, true);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
                    'fecha_ini' => date('Y-m-d', strtotime($params->fecha_ini)),
                    'fecha_fin' => date('Y-m-d', strtotime($params->fecha_fin)),
                    'fecha_flag' => $params->fecha_flag,
                    'vendedor_id' => $params->vendedor_id,
                    'cliente_id' => !empty($params->cliente_id)? implode(",", $params->cliente_id): '',
                    'moneda_id' => $params->moneda_id,
                    'local_id' => $params->local_id,
                    'estado' => $params->estado
                ));

                $data['form_filter'] = true;

                $data['local'] = $this->db->get_where('local', array('int_local_id' => $params->local_id))->row();
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $data['local_nombre'] = $data['local']->local_nombre;
                $data['local_direccion'] = $data['local']->direccion;

                $data['fecha_ini'] = $params->fecha_ini;
                $data['fecha_fin'] = $params->fecha_fin;

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reporte_venta/cliente_estado/tabla_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
                    'fecha_ini' => date('Y-m-d', strtotime($params->fecha_ini)),
                    'fecha_fin' => date('Y-m-d', strtotime($params->fecha_fin)),
                    'fecha_flag' => $params->fecha_flag,
                    'vendedor_id' => $params->vendedor_id,
                    'cliente_id' => !empty($params->cliente_id)? implode(",", $params->cliente_id): '',
                    'moneda_id' => $params->moneda_id,
                    'local_id' => $params->local_id,
                    'estado' => $params->estado
                ));

                $data['form_filter'] = true;

                $data['local'] = $this->db->get_where('local', array('int_local_id' => $params->local_id))->row();
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $data['local_nombre'] = $data['local']->local_nombre;
                $data['local_direccion'] = $data['local']->direccion;

                $data['fecha_ini'] = $params->fecha_ini;
                $data['fecha_fin'] = $params->fecha_fin;

                echo $this->load->view('menu/reporte_venta/cliente_estado/tabla_excel', $data, true);
                break;
            }
            default: {
                $cliente_id = $this->input->get('cliente_id', null);
                if ($cliente_id != null) {
                    $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
                        'fecha_ini' => date('Y-m-01'),
                        'fecha_fin' => date('Y-m-d'),
                        'fecha_flag' => 1,
                        'cliente_id' => $cliente_id,
                        'moneda_id' => MONEDA_DEFECTO,
                        'local_id' => $this->session->userdata('id_local')
                    ));

                } else {
                    $data['clientes'] = array();
                }

                $data['local'] = $this->db->get_where('local', array('int_local_id' => $this->session->userdata('id_local')))->row();
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => MONEDA_DEFECTO))->row();

                if ($this->session->userdata('esSuper') == 1) {
                    $locales = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $locales = $this->local_model->get_all_usu($usu);
                }

                $data['reporte_filtro'] = $this->load->view('menu/reporte_venta/cliente_estado/filtros', array(
                    'vendedores' => $this->db->get_where('usuario', array('deleted' => 0))->result(),
                    'clientes' => $this->db->get_where('cliente', array('cliente_status' => 1))->result(),
                    'monedas' => $this->db->get_where('moneda', array('status_moneda' => 1))->result(),
                    'locales' => $locales,
                    'cliente_id' => $cliente_id
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reporte_venta/cliente_estado/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_venta/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }


    }


    function comprobante($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['moneda_id'] = $this->input->post('moneda_id');
                $params['comprobante_id'] = $this->input->post('comprobante_id');

                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();

                $data['lists'] = $this->reporte_venta_model->getVentasComprobantes($params);

                $this->load->view('menu/reporte_venta/comprobante_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'comprobante_id' => $params->comprobante_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1]))),
                );

                $data['lists'] = $this->reporte_venta_model->getVentasComprobantes($input);
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reporte_venta/comprobante_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'comprobante_id' => $params->comprobante_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1]))),
                );

                $data['lists'] = $this->reporte_venta_model->getVentasComprobantes($input);
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                echo $this->load->view('menu/reporte_venta/comprobante_list_excel', $data, true);
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
                $data['comprobantes'] = $this->db->get_where('comprobantes', array('estado' => 1))->result();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_venta/comprobante', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function comision($action = '')
    {

        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['moneda_id'] = $this->input->post('moneda_id');
                $date_range = explode(" - ", $this->input->post('fecha'));
                $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));

                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();
                $params['usuarios_id'] = $this->input->post('usuarios_id');
                $data['lists'] = $this->reporte_venta_model->getVendedoresComision($params);

                $this->load->view('menu/reporte_venta/comision_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1]))),
                    'usuarios_id' => $params->usuarios_id
                );

                $data['lists'] = $this->reporte_venta_model->getVendedoresComision($input);
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reporte_venta/comision_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'moneda_id' => $params->moneda_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1]))),
                    'usuarios_id' => $params->usuarios_id
                );

                $data['lists'] = $this->reporte_venta_model->getVendedoresComision($input);
                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params->moneda_id))->row();

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];

                echo $this->load->view('menu/reporte_venta/comision_list_excel', $data, true);
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
                $data['usuarios'] = $this->usuario_model->select_all_user();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_venta/comision', $data, true);
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