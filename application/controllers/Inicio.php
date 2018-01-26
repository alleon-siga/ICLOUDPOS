<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class inicio extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('local/local_model', 'l');
        $this->load->model('opciones/opciones_model');
        $this->load->model('usuario/usuario_model');
        $this->load->library('session');
    }



    //Este metodo es predefinido en todo los controladores porque por este metodo se define que vistas se
    //mostrar�n por defecto
    public function index()
    {
        if($this->session->userdata('nUsuCodigo'))
            redirect('principal', 'refresh');

        //Aqu� estoy llamando a la vista login, para que me muestre la pagina de log�n por defecto
        $data['opciones'] = $this->opciones_model->get_opciones();
        $data["lstLocal"] = $this->l->get_all();//Se cargan la lista de Locales disponibles
        $this->load->view('login', $data);
    }

    public function validarTema()
    {

        $ruta = array('tema' => $this->input->post('ruta'));
        $this->session->set_userdata($ruta);
        var_dump($ruta);
        echo json_encode($ruta);

    }

    public function check_ajax_login()
    {
        if ($this->session->userdata('nUsuCodigo')) {
            //$this->login_model->refresh_session($this->session->userdata('nUsuCodigo'));
            $this->session->sess_regenerate();
        } else {
            $this->session->sess_destroy();
            echo '0';
            exit(0);
        }
    }


    function validar_singuardar()
    {

        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('user', 'user', 'required');
            $this->form_validation->set_rules('pw', 'pw', 'required');
            if ($this->form_validation->run() == false):
                echo validation_errors();
            else:
                $password = md5($this->input->post('pw', true));
                //$password = $this->input->post('pw',true);
                $data = array(
                    'username' => $this->input->post('user', true),
                    'password' => $password

                );

                $rs = $this->login_model->verificar_usuario($data);

                if (count($rs) > 0) {
                    echo "ok";
                } else {
                    echo "no ok";
                }
            endif;
        } else {
            //redirect(base_url().'inicio', 'refresh');
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
                //$password = $this->input->post('pw',true);
                $data = array(
                    'username' => $this->input->post('user', true),
                    'password' => $password

                );
                $rs = $this->login_model->verificar_usuario($data);

                if ($rs) {

                    $this->session->set_userdata($rs);
                    $configuraciones = $this->opciones_model->get_opciones();

                    if ($configuraciones == TRUE) {
                        foreach ($configuraciones as $configuracion) {

                            $clave = $configuracion['config_key'];
                            $data[$clave] = $configuracion['config_value'];

                        }


                    }

                    $this->session->set_userdata($data);
                    echo "ok";
                } else {
                    echo "no ok";
                }
            endif;
        } else {
            //redirect(base_url().'inicio', 'refresh');
        }
    }

}
