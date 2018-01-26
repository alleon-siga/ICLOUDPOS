<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login_api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

    function verificar_usuario($data) {

        $result = $this->db->where('username', $data['username'])
            ->where('var_usuario_clave', $data['password'])
            ->where('usuario.activo', 1)
            ->where('usuario.deleted', 0)
            ->join('local', 'local.int_local_id=usuario.id_local', 'left')
            ->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo', 'left')
            ->get('usuario')->row_array();

        return $result;
    }
}
