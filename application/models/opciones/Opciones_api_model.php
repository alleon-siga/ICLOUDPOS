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

    public function get_app_access($usuario_id)
    {
        $grupo = $this->db->select('*')
                    ->from('usuario')
                    ->where('nUsuCodigo', $usuario_id)
                    ->get()->row();

        $result = $this->db->select('*')
            ->from('opcion_grupo')
            ->where('grupo', $grupo->grupo)
            ->where('opcion', 11)
            ->get()->row();

        return $result;
    }

    public function get_opcion_ventas($usuario_id)
    {
        $grupo = $this->db->select('*')
            ->from('usuario')
            ->where('nUsuCodigo', $usuario_id)
            ->get()->row();

        $result = $this->db->select('*')
            ->from('opcion_grupo')
            ->where('grupo', $grupo->grupo)
            ->where('opcion', 1101)
            ->get()->result();

        return $result;
    }

    public function get_opcion_registros($usuario_id)
    {
        $grupo = $this->db->select('*')
            ->from('usuario')
            ->where('nUsuCodigo', $usuario_id)
            ->get()->row();

        $result = $this->db->select('*')
            ->from('opcion_grupo')
            ->where('grupo', $grupo->grupo)
            ->where('opcion', 1102)
            ->get()->result();

        return $result;
    }

    public function get_opcion_clientes($usuario_id)
    {
        $grupo = $this->db->select('*')
            ->from('usuario')
            ->where('nUsuCodigo', $usuario_id)
            ->get()->row();

        $result = $this->db->select('*')
            ->from('opcion_grupo')
            ->where('grupo', $grupo->grupo)
            ->where('opcion', 1103)
            ->get()->result();

        return $result;
    }
}
