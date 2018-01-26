<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_tipo_campo_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_by($where)
    {

        $this->db->where($where);
        $query = $this->db->get('cliente_tipo_campo');
        return $query->result_array();
    }

    function get_by_with_padre($where)
    {
        $this->db->select('*');
        $this->db->from('cliente_tipo_campo');
        $this->db->join('cliente_tipo_campo_padre','cliente_tipo_campo_padre.tipo_campo_padre_id=cliente_tipo_campo.padre_id');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }



}