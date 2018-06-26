<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class grupo_gastos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->get('grupo_gastos');
        return $query->result_array();
    }
}
