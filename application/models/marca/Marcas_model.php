<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class marcas_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_marcas()
    {
        $query = $this->db->where('estatus_marca', 1);
        $this->db->order_by('nombre_marca', 'asc');
        $query = $this->db->get('marcas');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('marcas');
        return $query->row_array();
    }

    function set_marcas()
    {
        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_marca', $nombre));

        if ($validar_nombre < 1) {

            $query_marca = array(

                'nombre_marca' => $this->input->post('nombre')

            );

            $this->db->trans_start();
            $this->db->insert('marcas', $query_marca);
            $id = $this->db->insert_id();
            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return $id;
        } else {
            return NOMBRE_EXISTE;

        }

    }

    function update_marcas($grupo)
    {


        $produc_exite = $this->get_by('nombre_marca', $grupo['nombre_marca']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_marca'] == $grupo ['id_marca']))) {

            $this->db->trans_start();
            $this->db->where('id_marca', $grupo['id_marca']);
            $this->db->update('marcas', $grupo);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;

        }
    }

    function verifProdMarca($marca)
    {
        $this->db->where('producto_marca', $marca['id_marca']);
        $this->db->from('producto');

        if ($this->db->count_all_results() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
