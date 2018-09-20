<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporte extends MY_Controller
{
	function __construct()
    {
        parent::__construct();
        $this->load->model('producto/producto_model');
        $this->load->model('facturador/facturador_model');
        $this->load->model('monedas/monedas_model');
        $this->load->model('reporte_shadow/reporte_shadow_model');
        if ($this->facturador_model->verify_session()) {
            
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function reporte($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['moneda_id'] = $this->input->post('moneda_id');
                if(!empty($this->input->post('fecha'))){
                    $date_range = explode(" - ", $this->input->post('fecha'));
                    $params['fecha_ini'] = date('Y-m-d 00:00:00', strtotime(str_replace("/", "-", $date_range[0])));
                    $params['fecha_fin'] = date('Y-m-d 23:59:59', strtotime(str_replace("/", "-", $date_range[1])));
                }
                $data['lists'] = $this->reporte_shadow_model->getReporte($params);

                $this->load->view('facturador/reporte/reporte_list', $data);
                break;
            }
            case 'pdf': {
                /*$params = json_decode($this->input->get('data'));
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
                break;*/
            }
            case 'excel': {
                /*$params = json_decode($this->input->get('data'));
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
                break;*/
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
}