<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class diccionario_termino_model extends CI_Model {
    private $table = 'diccionario_termino';
    function __construct() 
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all_operador()
    {
    	$this->db->select('id, valor');
    	$this->db->from('diccionario_termino');
    	$this->db->where('grupo=3 AND activo=1');
    	return $this->db->get()->result();
    }

    function get_all_poblado()
    {
        $this->db->select('id, valor');
        $this->db->from('diccionario_termino');
        $this->db->where('grupo=5 AND activo=1');
        return $this->db->get()->result();
    }
 }