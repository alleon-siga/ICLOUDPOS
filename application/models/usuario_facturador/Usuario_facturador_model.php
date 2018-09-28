<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class usuario_facturador_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('usuario_facturador');
        return $query->result();
    }

    function select_all_user($grupo = false)
    {
        $this->db->select('');
        $this->db->from('usuario_facturador');
        $this->db->where('usuario_facturador.deleted', 0);
        $query = $this->db->get();
        return $query->result();
    }

    function buscar_id($id)
    {
        $this->db->select('*');
        $this->db->from('usuario_facturador');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function insertar($usu)
    {
        $nombre = $this->input->post('username');
        $validar_nombre = sizeof($this->get_by('username', $nombre));

        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert('usuario_facturador', $usu);
            $this->db->trans_complete();
            return true;
        } else {
            return USERNAME_EXISTE;
        }
    }

    function update($usu)
    {
        $produc_exite = $this->get_by('username', $usu['username']);
        $validar_nombre = sizeof($produc_exite);

        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite[0]->id == $usu['id']))) {
            $this->db->trans_start();
            $this->db->where('usuario_facturador.id', $usu['id']);
            if ($this->db->update('usuario_facturador', $usu)) {
                $this->db->trans_complete();
                $this->db->trans_off();
                return true;
            } else {
                $this->db->trans_complete();
                $this->db->trans_off();
                return false;
            }
        } else {
            return USERNAME_EXISTE;
        }
    }

    function update_estatus($usu)
    {
        $produc_exite = $this->get_by('username', $usu['username']);
        $validar_nombre = sizeof($produc_exite);

        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite[0]->id == $usu['id']))) {
            $this->db->where('id', $usu['id']);
            if ($this->db->update('usuario_facturador', $usu)) {
                return true;
            } else {
                return false;
            }
        } else {
            return USERNAME_EXISTE;
        }
    }
}
