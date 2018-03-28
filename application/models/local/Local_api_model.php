<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Local_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_local_by_user($id)
    {
        if ($id == null)
        {
            $result = $this->db->select(
                'local.int_local_id as local_id,
                local.local_nombre as local_nombre,
                local.direccion, local.telefono,
                local.int_local_id as local_defecto')
                ->from('local')
                ->where('local.local_status', '1')
                ->get()->result();

            return $result;

        } else
        {
            $result = $this->db->select(
                'local.int_local_id as local_id,
                local.local_nombre as local_nombre,
                local.direccion, local.telefono,
                usuario.id_local as local_defecto')
                ->from('usuario_almacen')
                ->join('local', 'usuario_almacen.local_id=local.int_local_id')
                ->join('usuario', 'usuario_almacen.usuario_id=usuario.nUsuCodigo')
                ->where('local.local_status', '1')
                ->where('usuario.nUsuCodigo', $id)
                ->get()->result();

            return $result;
        }
    }
}
