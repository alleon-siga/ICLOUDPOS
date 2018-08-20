<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Producto extends MY_Controller
{
	function __construct()
    {
        parent::__construct();
        $this->load->model('producto/producto_model');
        $this->load->model('facturador/facturador_model');
        $this->load->model('monedas/monedas_model');
        if ($this->facturador_model->verify_session()) {
            
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function costeo($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['marca_id'] = $this->input->post('marca_id');
                $params['grupo_id'] = $this->input->post('grupo_id');
                $params['familia_id'] = $this->input->post('familia_id');
                $params['linea_id'] = $this->input->post('linea_id');
                $params['producto_id'] = $this->input->post('producto_id');
                $data['lists'] = $this->producto_model->getCosteo($params);
                $data['moneda'] = $this->monedas_model->get_by('id_moneda', '1030');
                $this->load->view('facturador/producto/costeo_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $input = array(
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id
                );

                $data['lists'] = $this->producto_model->getCosteo($input);

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4-L', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('facturador/producto/costeo_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $input = array(
                    'marca_id' => $params->marca_id,
                    'grupo_id' => $params->grupo_id,
                    'familia_id' => $params->familia_id,
                    'linea_id' => $params->linea_id,
                    'producto_id' => $params->producto_id
                );

                $data['lists'] = $this->producto_model->getCosteo($input);
                echo $this->load->view('facturador/producto/costeo_list_excel', $data, true);
                break;
            }
            default: {
                $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
                $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
                $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
                $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
                $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
                $data["productos"] = $this->producto_model->get_productos_list();
                $dataCuerpo['cuerpo'] = $this->load->view('facturador/producto/costeo', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('facturador/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function editarCosteo()
    {
        $data = json_decode($this->input->post('detalle', true));
        $rpta = $this->producto_model->editarCosteo($data);
        if ($rpta != FALSE) {
            $data['success'] = 1;
            $data['msg'] = 'Actualizado correctamente';
        } else {
            $data['success'] = 0;
            $data['msg'] = 'No pudo ser guardada';
        }
        echo json_encode($data);
    }
}