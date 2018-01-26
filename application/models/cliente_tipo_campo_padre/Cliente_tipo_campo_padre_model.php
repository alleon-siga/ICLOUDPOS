<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_tipo_campo_padre_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {

        $query = $this->db->get('cliente_tipo_campo_padre');
        return $query->result_array();
    }




}