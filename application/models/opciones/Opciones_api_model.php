<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Opciones_api_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_opciones($keys = array())
    {
        $this->db->select('*');
        $this->db->from('configuraciones');
        $query = $this->db->get();
        return $query->result_array();
    }
}
