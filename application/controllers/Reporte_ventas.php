<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_ventas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('reporte_venta/reporte_venta_model');
            $this->load->model('local/local_model');
        } else {
            redirect(base_url(), 'refresh');
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


}