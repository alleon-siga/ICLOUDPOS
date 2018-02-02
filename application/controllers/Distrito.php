<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class distrito extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('distrito/distrito_model');
            $this->load->model('ciudad/ciudad_model');
            $this->load->model('estado/estado_model');
            $this->load->model('pais/pais_model');
        }else{
            redirect(base_url(), 'refresh');
        }  
    }


    function index()
    {
        //$data="";

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["distritos"] = $this->distrito_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/distrito/distrito', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        $data['distrito'] = array();
        $data['ciudades'] = $this->ciudad_model->get_all();
        $data['estados'] = $this->estado_model->get_all();
        $data['paises'] = $this->pais_model->get_all();
        if ($id != FALSE) {
            $data['distrito'] = $this->distrito_model->get_by('id', $id);
            $data['sciudad'] = $this->ciudad_model->get_by('ciudad_id',$data['distrito']['ciudad_id']);
            $data['sestado'] = $this->estado_model->get_by('estados_id',$data['sciudad']['estado_id']);
            $data['spais'] = $this->pais_model->get_by('id_pais',$data['sestado']['pais_id']);

        }

        $this->load->view('menu/distrito/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $distrito = array(

            'ciudad_id' => $this->input->post('ciudad_id'),
            'nombre' => $this->input->post('nombre'),
        );

        if (empty($id)) {
            $resultado = $this->distrito_model->insertar($distrito);

        } else {
            $distrito['id'] = $id;
            $resultado = $this->distrito_model->update($distrito);
        }

        if ($resultado == TRUE) {
            $json['success']= 'Solicitud Procesada con exito';
        } else {
            $json['error']= 'Ha ocurrido un error al procesar la solicitud';
        }

       echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $metodospago = array(
            'id_metodo' => $id,
            'nombre_metodo' => $nombre . time(),
            'status_metodo' => 0

        );

        $data['resultado'] = $this->metodos_pago_model->update($metodospago);

        if ($data['resultado'] != FALSE) {

            $this->session->set_flashdata('success', 'Se ha eliminado exitosamente');


        } else {

            $this->session->set_flashdata('error', 'Ha ocurrido un error al eliminar el M&eacute;todos');
        }

        redirect('metodosdepago');
    }

    function get_by_ciudad()
    {
        if ($this->input->is_ajax_request()) {
            $ciudad_id = $this->input->post('ciudad_id');

            $distritos = $this->distrito_model->get_all_by('ciudad_id', $ciudad_id);
            header('Content-Type: application/json');

            echo json_encode($distritos);
        } else {
            redirect(base_url . 'principal');
        }
    }


}
