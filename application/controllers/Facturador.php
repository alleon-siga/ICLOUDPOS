<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Facturador extends MY_Controller
{
	function __construct()
    {
		parent::__construct();
        $this->load->model('facturador/facturador_model');
    }

    function index()
    {
        if($this->session->userdata('id')){
            redirect('facturador/principal', 'refresh');
        }
        $this->load->view('facturador/login');
    }

    function principal()
    {
        $dataCuerpo['cuerpo'] = $this->load->view('facturador/principal', NULL, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('facturador/template', $dataCuerpo);
        }
    }

    function validar_login()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('user', 'user', 'required');
            $this->form_validation->set_rules('pw', 'pw', 'required');
            if ($this->form_validation->run() == false):
                echo validation_errors();
            else:
                $password = md5($this->input->post('pw', true));
                $data = array(
                    'username' => $this->input->post('user', true),
                    'password' => $password
                );
                $rs = $this->facturador_model->verificar_usuario($data);

                if ($rs) {
                    $this->session->set_userdata($rs);
                    echo "ok";
                } else {
                    echo "no ok";
                }
            endif;
        }
    }    
}