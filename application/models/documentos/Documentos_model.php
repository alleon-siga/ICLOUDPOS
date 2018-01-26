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
}