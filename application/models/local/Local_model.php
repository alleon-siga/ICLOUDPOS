<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class local_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_all() {
        $query = $this->db->where('local_status', 1);
        $query = $this->db->get('local');
        return $query->result_array();
    }

    function get_my_locals($status = 1) {

        $this->db->select(
                        'local.*, 
            pais.nombre_pais as pais,
            estados.estados_nombre as estado,
            ciudades.ciudad_nombre as ciudad,
            distrito.nombre as distrito')
                ->from('local')
                ->join('distrito', 'local.distrito_id=distrito.id', 'left')
                ->join('ciudades', 'distrito.ciudad_id=ciudades.ciudad_id', 'left')
                ->join('estados', 'ciudades.estado_id=estados.estados_id', 'left')
                ->join('pais', 'estados.pais_id=pais.id_pais', 'left');
        if ($status == 1)
            $this->db->where('local_status', 1);

        return $this->db->get()->result();
    }

    //funcion nueva que trae los locales de un usuario y su local por defecto
    function get_local_by_user($id) {
        if ($this->session->userdata('esSuper') == '1') {
            return $this->db->select(
                                    'local.int_local_id as local_id, 
            local.local_nombre as local_nombre, 
            local.int_local_id as local_defecto')
                            ->from('local')
                            ->where('local.local_status', '1')->get()->result();
        } else {
            $this->db->select(
                            'local.int_local_id as local_id, 
            local.local_nombre as local_nombre, 
            usuario.id_local as local_defecto')
                    ->from('usuario_almacen')
                    ->join('local', 'usuario_almacen.local_id=local.int_local_id')
                    ->join('usuario', 'usuario_almacen.usuario_id=usuario.nUsuCodigo')
                    ->where('local.local_status', '1');
            return $this->db->where('usuario.nUsuCodigo', $id)->get()->result();
        }
    }

    function get_all_usu($usu) {
        $query = $this->db->select('`l`.`int_local_id` AS `int_local_id`,`l`.`local_nombre` AS `local_nombre`,`ua`.`usuario_id`  AS `usuario_id`');
        $query = $this->db->from('(`local` `l` LEFT JOIN `usuario_almacen` `ua`  ON ((`ua`.`local_id` = `l`.`int_local_id`)))');
        $query = $this->db->where('`l`.`local_status` = 1');
        $query = $this->db->where('usuario_id', $usu);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_all_usu_almacen() {
        $query = $this->db->get('v_usuario_almacen');
        return $query->result_array();
    }

    function get_by($campo, $valor) {
        $this->db->where($campo, $valor);
        $query = $this->db->get('local');
        return $query->row_array();
    }

    function insertar($local) {
        $nombre = $this->input->post('local_nombre');
        $validar_nombre = sizeof($this->get_by('local_nombre', $nombre));

        if ($validar_nombre < 1) {
            //$this->db->trans_start();
            $this->db->insert('local', $local);
            $id = $this->db->insert_id();
            $this->update_principal($id, $local['principal']);
            //$this->db->trans_complete();

            $documentos = $this->db->get('documentos')->result();
            foreach ($documentos as $d) {
                $this->db->insert('correlativos', array(
                    'id_local' => $id,
                    'id_documento' => $d->id_doc,
                    'serie' => '001',
                    'correlativo' => 1
                ));
            }

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update($local) {


        $produc_exite = $this->get_by('local_nombre', $local['local_nombre']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ( $validar_nombre > 0 and ( $produc_exite ['int_local_id'] == $local ['int_local_id']))) {
            $this->db->trans_start();
            $this->db->where('int_local_id', $local['int_local_id']);
            $this->db->update('local', $local);

            if (isset($local['principal']))
                $this->update_principal($local['int_local_id'], $local['principal']);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update_principal($local_id, $principal) {
        if ($principal == '1') {
            $this->db->where('int_local_id !=', $local_id);
            $this->db->update('local', array('principal' => '0'));
        }
    }

    function verifLocal($local) {
        $this->db->where('local_id', $local['int_local_id']);
        $this->db->from('movimiento_historico');

        if ($this->db->count_all_results() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
