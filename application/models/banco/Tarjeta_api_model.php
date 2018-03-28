<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tarjeta_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        return $this->db->select('*')
            ->from('tarjeta_pago')
            ->get()->result_array();
    }
}
