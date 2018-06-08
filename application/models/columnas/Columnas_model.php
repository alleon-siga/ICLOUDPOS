<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class columnas_model extends CI_Model
{

    private $table = 'columnas';


    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getColumn($name){
        return $this->db->get_where($this->table, array('nombre_columna' => $name))->row(0);
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->order_by('orden', 'asc')->get($this->table);
        $query = $query->result();
        $data = array();
        foreach ($query as $q) {
            if ($q->nombre_columna == "producto_id")
                $data[] = $q;
        }
        foreach ($query as $q) {
            if ($q->nombre_columna == "producto_codigo_interno")
                $data[] = $q;
        }
        foreach ($query as $q) {
            if($q->nombre_columna != "producto_id" && $q->nombre_columna != "producto_codigo_interno")
                $data[] = $q;
        }
        return $data;
    }

    function  insert($columnas_id)
    {

        $this->db->trans_start();

        if($columnas_id!=false) {
            foreach ($columnas_id as $id) {
                $mostrar = $this->input->post('mostrar_' . $id) == 'on' ? 1 : 0;
                $activo = $this->input->post('activo_' . $id) == 'on' ? 1 : 0;
                $columna = array(
                    "mostrar" => $mostrar,
                    "activo" => $activo
                );
                $this->db->where('id_columna', $id);
                $this->db->update('columnas', $columna);

            }

        }
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }


}
