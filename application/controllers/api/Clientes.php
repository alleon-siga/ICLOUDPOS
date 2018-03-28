<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class Clientes extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('cliente/cliente_api_model');
        $this->load->model('api/api_model', 'api');

        $this->very_auth();
    }

    function very_auth()
    {
        // Request Header
        $reqHeader = $this->input->request_headers();

        // Key
        $key = null;
        if (isset($reqHeader['x-api-key'])) {
            $key = $reqHeader['x-api-key'];

        } else if ($key_get = $this->get('x-api-key')) {
            $key = $key_get;

        } else if ($key_post = $this->post('x-api-key')) {
            $key = $key_post;

        } else {
            $key = null;
        }

        // Auth ID
        $auth_id = $this->api->getAuth($key);

        // ID ?
        if (!empty($auth_id)) {
            $this->uid = $auth_id;

        } else {
            $this->uid = null;
        }
    }

    // All
    public function index_get()
    {
        $data = array();

        $data['clientes'] = $this->cliente_api_model->get_all();

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Cities
    public function cities_get()
    {
        $data = array();

        $data['estados'] = $this->cliente_api_model->get_estados();

        $data['ciudades'] = $this->cliente_api_model->get_ciudades();

        $data['distritos'] = $this->cliente_api_model->get_distritos();

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Save
    public function save_post()
    {
        $post = $this->input->post();

        $id = isset($data['cliente_id']) ? $data['cliente_id'] : null;
        //Datos variables
        $id_usuario     =   $post['id_usuario'];
        $tipo_cliente   =   $post['tipo_cliente'];
        $ruc            =   $post['ruc'];
        $razon_social   =   $post['razon_social'];
        $identificacion =   $post['identificacion'];
        $dni            =   $post['dni'];
        $grupo_id       =   $post['grupo'];
        $direccion      =   $post['direccion'];
        $provincia      =   $post['estado'];
        $ciudad         =   $post['ciudad'];
        $distrito       =   $post['distrito'];
        $correo         =   $post['correo'];
        $telefono       =   $post['telefono'];
        $genero         =   $post['genero'];
        $latitud        =   $post['latitud'];
        $longitud       =   $post['longitud'];

        //Datos fijos
        $cliente_status = 1;
        $agente_retension = 0;

        $cliente = array(
            'tipo_cliente'   => $tipo_cliente,
            'ruc'            => $ruc,
            'razon_social'   => $razon_social,
            'identificacion' => $identificacion,
            'dni'            => $dni,
            'grupo_id'       => $grupo_id,
            'direccion'      => $direccion,
            'provincia'      => $provincia,
            'ciudad'         => $ciudad,
            'distrito'       => $distrito,
            'email'          => $correo,
            'telefono1'      => $telefono,
            'genero'         => $genero,
            'latitud'        => $latitud,
            'longitud'       => $longitud,
            'cliente_status' => $cliente_status,
            'agente_retension' => $agente_retension
        );

        $result = null;
        if (!$id) {
            $result = $this->cliente_api_model->insertar($cliente);

        } else {
            $cliente['id_cliente'] = $id;
            $result = $this->cliente_api_model->update($cliente);
        }

        if ($result > 1) {
            $data['response'] = 'success';

        } else if ($result == -1) {
            $data['response'] = 'existe';

        } else {
            $data['response'] = 'failed';
        }

        $this->response($data, 200);
    }
}