<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class monedas extends MY_Controller
{

    private $monedas = array();

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('monedas/monedas_model');
        }else{
            redirect(base_url(), 'refresh');
        }

    }

    function form($id = FALSE)
    {

        $data = array();
        if ($id != FALSE) {
            $data['monedas'] = $this->monedas_model->get_by('id_moneda', $id);
            $data['moneda_defecto'] = $this->db->get_where('moneda', array('id_moneda' => MONEDA_DEFECTO))->row();
        }
        $this->load->view('menu/monedas/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $monedas = array(
            'nombre' => $this->input->post('nombre_moneda'),
            'simbolo' => $this->input->post('simbolo'),
            'pais' => $this->input->post('pais'),
            'tasa_soles' => $this->input->post('tasa_soles'),
            'ope_tasa' => '*',
            'status_moneda' => $this->input->post('status_moneda'),
        );

        if (empty($id)) {
            $resultado = $this->monedas_model->insertar($monedas);
        } else {
            $monedas['id_moneda'] = $id;
            if($id == MONEDA_DEFECTO){
                $monedas['ope_tasa'] = '/';
                $monedas['status_moneda'] = '1';
            }
            $resultado = $this->monedas_model->update($monedas);
        }

        if ($resultado == TRUE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        if ($resultado === NOMBRE_EXISTE) {
            //  $this->session->set_flashdata('error', NOMBRE_EXISTE);
            $json['error'] = NOMBRE_EXISTE;
        }

        echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');

        $moneda = array(
            'id_moneda' => $id,
            'status_moneda' => 0
        );

        $data['resultado'] = $this->monedas_model->update_status($moneda);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar esta moneda';
        }

        echo json_encode($json);
    }


    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

//    	$data['monedas'] = $this->monedas_model->get_all();
        $data['monedas'] = $this->db->get('moneda')->result();
        $data['moneda_defecto'] = $this->db->get_where('moneda', array('id_moneda' => MONEDA_DEFECTO))->row();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/monedas/monedas', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

}