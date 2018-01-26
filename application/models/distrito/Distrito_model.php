<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class distrito_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

    function get_all()
    {
       /* $query = $this->db->where('ciudad_status', 1); */
        $query = $this->db->select('*');
        $query = $this->db->from('distrito');
        $query = $this->db->join('ciudades', 'ciudades.ciudad_id=distrito.ciudad_id');
        $query = $this->db->join('estados', 'estados.estados_id=ciudades.estado_id');
        $query = $this->db->join('pais', 'estados.pais_id=pais.id_pais');
        $query = $this->db->get();
        return $query->result_array();
    }



    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('distrito');
        return $query->row_array();
    }
   function get_all_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('distrito');
        return $query->result_array();
    }

    function insertar($ciudad)
    {

        $this->db->trans_start();
        $this->db->insert('distrito', $ciudad);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($distrito)
    {

        $this->db->trans_start();
        $this->db->where('id', $distrito['id']);
        $this->db->update('distrito', $distrito);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }


}
