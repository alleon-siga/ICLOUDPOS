<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_super_user($id)
    {
        $this->db->where('nUsuCodigo', $id);
        $this->db->where('esSuper', 1);
        $query = $this->db->get('usuario');

        return $query->row();
    }
}