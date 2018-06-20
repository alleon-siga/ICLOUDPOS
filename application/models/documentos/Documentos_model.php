<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class documentos_model extends CI_Model {

 
    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_documentos(){
    	$q = "SELECT * from documentos";
    	$result = $this->db->query($q);
    	foreach ($result->result() as $row)
		{
		  $filas[] = $row;
		}
		return $filas;
    }

    public function get_documentosBy($where){
        $this->db->select('id_doc, des_doc');
        $this->db->from('documentos');
        $this->db->where($where);
        return $this->db->get()->result();
    }

    function get_by($campo, $valor)
    {
        $this->db->select('des_doc');
        $this->db->where($campo, $valor);
        $query = $this->db->get('documentos');
        return $query->row();
    }
}