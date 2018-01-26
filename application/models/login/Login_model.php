<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class login_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('opciones/opciones_model');
        $this->load->library('session');
    }

    function verificar_usuario($data)
    {

        $query = $this->db->where('username', $data['username']);
        $query = $this->db->where('var_usuario_clave', $data['password']);
        $query = $this->db->where('usuario.activo', 1);
        $query = $this->db->where('usuario.deleted', 0);
        $query = $this->db->join('local', 'local.int_local_id=usuario.id_local', 'left');
        $query = $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo', 'left');
        $query = $this->db->get('usuario');
        //return $query->row();
        return $query->row_array();
        /*$query = "select * from usuario where cUsuarioIDName='".$data['username']."' and cUsuarioClave='".$data['password']."'";
        $result = $this->db->query($query);
        return $result->result();*/
    }

    function traer_datos_sesion($condicion)
    {

        $this->db->where($condicion);
        $this->db->join('local', 'local.int_local_id=usuario.id_local');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        $query = $this->db->get('usuario');
        return $query->row_array();

    }

    public function verify_session()
    {
        /*if(!$this->input->is_ajax_request()) {
            if ($this->session->userdata('nUsuCodigo')) {
                //$this->refresh_session($this->session->userdata('nUsuCodigo'));
                $this->session->sess_regenerate();
            } else {
                $this->session->sess_destroy();
                redirect('', 'refresh');
            }
        }*/
    }

    public function refresh_session($user_id)
    {
        $condicion = array(
            'nUsuCodigo' => $user_id
        );

        $rs = $this->traer_datos_sesion($condicion);

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
        }

    }

}
