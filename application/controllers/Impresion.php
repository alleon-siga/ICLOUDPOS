<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Impresion extends MY_Controller
{


    function __construct()
    {
        parent::__construct();
        $this->login_model->verify_session();

    }

    function index()
    {
        $data['mensaje'] = 'Hola desde el server';
        header('Content-Type: application/json');
        echo json_encode($data);
    }

}