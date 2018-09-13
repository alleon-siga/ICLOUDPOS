<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class usuario_facturador_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('usuario_facturador');
        return $query->result();
    }
}
