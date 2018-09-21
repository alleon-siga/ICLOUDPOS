<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class facturador_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function verificar_usuario($data)
    {
        $query = $this->db->where('username', $data['username']);
        $query = $this->db->where('var_usuario_clave', $data['password']);
        $query = $this->db->where('uf.activo', 1);
        $query = $this->db->where('uf.deleted', 0);
        $query = $this->db->join('local l', 'l.int_local_id=uf.id_local', 'left');
        $query = $this->db->get('usuario_facturador uf');
        return $query->row_array();
    }

    public function verify_session()
    {
        $session = $this->session->userdata('activo');
        if (isset($session) and $session=='1') {
            return true;
        }else{
            return false;
        }
    }
 }