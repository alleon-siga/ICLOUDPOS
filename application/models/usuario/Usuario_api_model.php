<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_venta_user($id)
    {
        $result = $this->db->select('*')
            ->from('usuario')
            ->where('nUsuCodigo', $id)
            ->where('grupo', 8)
            ->get()->row();

        return $result;
    }
}