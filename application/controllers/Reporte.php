<?php

class Reporte extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('local/local_model');
        }else{
            redirect(base_url(), 'refresh');
        }
    }

    function productoVendido()
    {
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();


        $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/productoVendido', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }
}