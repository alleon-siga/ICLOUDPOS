<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class local extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('distrito/distrito_model');
            $this->load->model('ciudad/ciudad_model');
            $this->load->model('estado/estado_model');
            $this->load->model('pais/pais_model');
            $this->load->model('local/local_model');
            $this->load->model('usuario/usuario_model');
            $this->load->model('correlativos/correlativos_model');
            $this->load->model('cajas/cajas_model');
        } else {
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

        $data["locales"] = $this->local_model->get_my_locals(0);

        $data['dialog_correlativo'] = $this->load->view('menu/local/dialog_correlativo', array(
            'documentos' => $this->db->get('documentos')->result()
        ), true);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/local/local', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_notas_correlativo($venta_id)
    {
        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        header('Content-Type: application/json');

        if ($venta->id_documento == '1') {
            $data['correlativos'] = $this->db->get_where('correlativos', array(
                'id_local' => $venta->local_id,
                'id_documento' => 9
            ))->row();
        } elseif ($venta->id_documento == '3') {
            $data['correlativos'] = $this->db->get_where('correlativos', array(
                'id_local' => $venta->local_id,
                'id_documento' => 8
            ))->row();
        } elseif ($venta->id_documento == '6') {
            $data['correlativos'] = $this->db->get_where('correlativos', array(
                'id_local' => $venta->local_id,
                'id_documento' => 2
            ))->row();
        }

        echo json_encode($data);
    }

    function get_correlativos($local_id)
    {

        header('Content-Type: application/json');
        echo json_encode(array(
            'correlativos' => $this->correlativos_model->get_by(array(
                'id_local' => $local_id
            ))));
    }

    function save_correlativos($local_id)
    {
        $correlativos = json_decode($this->input->post('correlativos'));

        $this->cajas_model->sync_cajas();
        header('Content-Type: application/json');
        if ($this->correlativos_model->save_correlativos($local_id, $correlativos))
            echo json_encode(array('success' => '1'));
        else
            echo json_encode(array('success' => '0'));
    }

    function form($id = FALSE)
    {
        $data = array();
        $data['distritos'] = $this->distrito_model->get_all();
        $data['ciudades'] = $this->ciudad_model->get_all();
        $data['estados'] = $this->estado_model->get_all();
        $data['paises'] = $this->pais_model->get_all();
        if ($id != FALSE) {
            $data['local'] = $this->local_model->get_by('int_local_id', $id);
            $data['sdistrito'] = $this->distrito_model->get_by('id', $data['local']['distrito_id']);
            $data['sciudad'] = $this->ciudad_model->get_by('ciudad_id', $data['sdistrito']['ciudad_id']);
            $data['sestado'] = $this->estado_model->get_by('estados_id', $data['sciudad']['estado_id']);
            $data['spais'] = $this->pais_model->get_by('id_pais', $data['sestado']['pais_id']);
        }
        $this->load->view('menu/local/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $local = array(
            'local_nombre' => $this->input->post('local_nombre'),
            'local_status' => '1',
            'principal' => $this->input->post('principal'),
            'distrito_id' => $this->input->post('distrito_id'),
            'direccion' => $this->input->post('direccion'),
            'telefono' => $this->input->post('telefono'),
        );

        if (empty($id)) {
            //$this->db->trans_start();
            $resultado = $this->local_model->insertar($local);
        } else {
            $local['int_local_id'] = $id;
            $resultado = $this->local_model->update($local);
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
        $this->cajas_model->sync_cajas();
        echo json_encode($json);

    }

    function activar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $status = $this->input->post('status');

        $local = array(
            'int_local_id' => $id,
            'local_nombre' => $nombre,
            'local_status' => $status

        );

        if ($this->local_model->verifLocal($local) != true) {


            $data['resultado'] = $this->local_model->update($local);

            if ($data['resultado'] != FALSE)
                $json['success'] = '1';
            else
                $json['success'] = '0';

            header('Content-Type: application/json');
            echo json_encode($json);
        }
    }
}