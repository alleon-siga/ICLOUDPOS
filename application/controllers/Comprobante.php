<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comprobante extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('comprobante/comprobante_model');
            $this->load->library('Pdf');
            $this->load->library('phpExcel/PHPExcel.php');
        } else {
            redirect(base_url(), 'refresh');
        }
    }


    function index()
    {
        $data['comprobantes'] = $this->comprobante_model->get_comprobantes();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/comprobante/comprobante', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        if ($id != FALSE) {
            $data['comprobante'] = $this->comprobante_model->get_comprobantes($id);
        }


        $this->load->view('menu/comprobante/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');
        $comprobante = array(
            'nombre' => $this->input->post('nombre'),
            'serie' => $this->input->post('serie'),
            'desde' => $this->input->post('desde'),
            'hasta' => $this->input->post('hasta'),
            'longitud' => $this->input->post('longitud'),
            'estado' => $this->input->post('estado'),
            'num_actual' => $this->input->post('actual'),
            'fecha_venc' => date('Y-m-d', strtotime($this->input->post('fecha_venc')))
        );

        if (empty($id)) {
            $resultado = $this->comprobante_model->save($comprobante);
        } else {
            $comprobante['id'] = $id;
            $resultado = $this->comprobante_model->save($comprobante);
        }

        if ($resultado != FALSE) {
            $json['success'] = 'Solicitud procesada con exito';

        } else {
            $json['error'] = $this->comprobante_model->error;
        }

        echo json_encode($json);
    }

    function eliminar()
    {
        $id = $this->input->post('id');
        $json = array();
        echo json_encode($json);
    }


}