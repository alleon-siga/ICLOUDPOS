<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Impresion extends MY_Controller
{


    function __construct()
    {
        parent::__construct();
        $this->login_model->verify_session();

        $this->load->model('impresion/impresion_model');

    }

    function get_venta($id)
    {
        echo $this->impresion_model->create_xml($this->impresion_model->getVenta($id));
    }

}
