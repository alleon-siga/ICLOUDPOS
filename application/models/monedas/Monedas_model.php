<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class monedas_model extends CI_Model
{

    private $tabla = 'moneda';

    function __construct()
    {
        parent::__construct();
    }

    function g()
    {
        return true;
    }

    function get_all()
    {

        $query = $this->db->get('vw_monedas_cajas');
        return $query->result_array();
    }

    function get_moneda_default()
    {
        return $this->db->get_where($this->tabla, array('id_moneda' => MONEDA_DEFECTO, 'status_moneda' => 1))->row();
    }

    function get_monedas_activas()
    {
        return $this->db->get_where($this->tabla, array('status_moneda' => 1))->result();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('moneda');
        return $query->row_array();
    }

    function insertar($moneda)
    {

        $nombre = $this->input->post('nombre_moneda');
        $validar_nombre = sizeof($this->get_by('nombre', $nombre));

        if ($validar_nombre < 1) {


            $this->db->trans_start();
            $this->db->insert('moneda', $moneda);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update_status($moneda)
    {
        $this->db->where('id_moneda', $moneda['id_moneda']);
        $this->db->update('moneda', $moneda);
        return TRUE;
    }

    function update($moneda)
    {
        $produc_exite = $this->get_by('nombre', $moneda['nombre']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_moneda'] == $moneda ['id_moneda']))) {

            $this->db->trans_start();
            $this->db->where('id_moneda', $moneda['id_moneda']);
            $this->db->update('moneda', $moneda);

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
}
