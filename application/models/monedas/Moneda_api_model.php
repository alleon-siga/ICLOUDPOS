<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Moneda_api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $result = $this->db->select('*')
            ->from('moneda')
            ->where('status_moneda', 1)
            ->get()->result();

        return $result;
    }
}
