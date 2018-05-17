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

    function get_nota_credito()
    {
        $param['id'] = $this->input->post('venta_id');
        $param['serie'] = $this->input->post('serie');
        $param['numero'] = $this->input->post('numero');
        echo $this->impresion_model->createXmlNotaCredito($this->impresion_model->getVentaNotaCredito($param['id']));
    }
}
