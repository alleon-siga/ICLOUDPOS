<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_inventario extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('reporte_inventario/reporte_inventario_model');
            $this->load->model('producto/producto_model');
            $this->load->model('local/local_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function verificaInventario($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $data['inconsistencia'] = $this->input->post('inconsistencia');
                $data['lists'] = $this->reporte_inventario_model->getVerificaInventario($params);
                $this->load->view('menu/reporte_inventario/verificaInventario_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $input = array(
                    'local_id' => $params->local_id,
                    'producto_id' => $params->producto_id
                );
                $data['lists'] = $this->reporte_inventario_model->getVerificaInventario($input);
                $data['inconsistencia'] = $params->inconsistencia;
                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/reporte_inventario/verificaInventario_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $input = array(
                    'local_id' => $params->local_id,
                    'producto_id' => $params->producto_id
                );
                $data['lists'] = $this->reporte_inventario_model->getVerificaInventario($input);
                $data['inconsistencia'] = $params->inconsistencia;
                echo $this->load->view('menu/reporte_inventario/verificaInventario_list_excel', $data, true);
                break;
            }
            default: {
                $data['locales'] = $this->local_model->get_all();
                $data["productos"] = $this->producto_model->get_productos_list();
                $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_inventario/verificaInventario', $data, true);
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