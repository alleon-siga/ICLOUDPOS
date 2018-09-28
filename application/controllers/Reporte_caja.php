<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_caja extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('reporte_caja/reporte_caja_model');
            $this->load->model('local/local_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function estadoResultado($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['year'] = $this->input->post('year');
                $params['mes'] = $this->input->post('mes');
                $data['lists'] = $this->reporte_caja_model->getEstadoResultado($params);

                $this->load->view('menu/reporte_caja/estadoResultado_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $input = array(
                    'local_id' => $params->local_id,
                    'year' => $params->year,
                    'mes' => $params->mes
                );
                $data['lists'] = $this->reporte_caja_model->getEstadoResultado($input);
                $data['year'] = $input['year'];
                $data['mes'] = $input['mes'];
                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = !empty($local->local_nombre)? $local->local_nombre: 'TODOS';
                $data['local_direccion'] = !empty($local->direccion)? $local->direccion: 'TODOS';
                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reporte_caja/estadoResultado_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $input = array(
                    'local_id' => $params->local_id,
                    'year' => $params->year,
                    'mes' => $params->mes
                );
                $data['lists'] = $this->reporte_caja_model->getEstadoResultado($input);
                $data['year'] = $input['year'];
                $data['mes'] = $input['mes'];
                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = !empty($local->local_nombre)? $local->local_nombre: 'TODOS';
                $data['local_direccion'] = !empty($local->direccion)? $local->direccion: 'TODOS';
                echo $this->load->view('menu/reporte_caja/estadoResultado_list_excel', $data, true);
                break;
            }
            default: {
                $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_caja/estadoResultado', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function gastosDia($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['caja_id'] = $this->input->post('caja_id');
                if(!empty($this->input->post('fecha'))){
                    $date_range = explode(" - ", $this->input->post('fecha'));
                    $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                    $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                }
                $data['lists'] = $this->reporte_model->getGastosDia($params);

                $this->load->view('menu/reporte_caja/gastosDia_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );
                $data['lists'] = $this->reporte_model->getGastosDia($input);

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = !empty($local->local_nombre)? $local->local_nombre: 'TODOS';
                $data['local_direccion'] = !empty($local->direccion)? $local->direccion: 'TODOS';

                $data['fecha_ini'] = $input['fecha_ini'];
                $data['fecha_fin'] = $input['fecha_fin'];
                $data['condicion_pago'] = $input['condicion_pago'];
                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reporte_caja/gastosDia_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $input = array(
                    'local_id' => $params->local_id,
                    'fecha_ini' => date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0]))),
                    'fecha_fin' => date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])))
                );
                $data['lists'] = $this->reporte_model->getGastosDia($input);

                $local = $this->db->get_where('local', array('int_local_id' => $input['local_id']))->row();
                $data['local_nombre'] = !empty($local->local_nombre)? $local->local_nombre: 'TODOS';
                $data['local_direccion'] = !empty($local->direccion)? $local->direccion: 'TODOS';
                echo $this->load->view('menu/reporte_caja/gastosDia_list_excel', $data, true);
                break;
            }
            default: {
                $data['cajas'] = $this->cajas_model->get_caja();
                $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_caja/gastosDia', $data, true);
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