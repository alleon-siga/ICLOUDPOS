<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cliente_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $result = $this->db->select('*')
            ->from('cliente')
            ->join('grupos_cliente', 'grupos_cliente.id_grupos_cliente=cliente.grupo_id')
            ->join('ciudades c', 'c.ciudad_id=cliente.ciudad_id', 'left')
            ->where('cliente_status', 1)
            ->get()->result();

        return $result;
    }

    function get_grupos_all()
    {
        $result = $this->db->select('*')
            ->from('grupos_cliente')
            ->where('status_grupos_cliente', 1)
            ->get()->result();

        return $result;
    }

    function get_estados()
    {
        $result = $this->db->select('*')
            ->from('estados')
            ->order_by('estados_nombre', 'ASC')
            ->get()->result();

        return $result;
    }

    function get_ciudades()
    {
        $result = $this->db->select('*')
            ->from('ciudades')
            ->order_by('ciudad_nombre', 'ASC')
            ->get()->result();

        return $result;
    }

    function get_distritos()
    {
        $result = $this->db->select('*')
            ->from('distrito')
            ->order_by('nombre', 'ASC')
            ->get()->result();

        return $result;
    }

    function insertar($cliente)
    {
        $validar_rs = $this->validarRazonSocial($cliente);

        if (sizeof($validar_rs) < 1) {
            $this->db->insert('cliente', $cliente);
            return $this->db->insert_id();

        } else {
            return -1;
        }
    }

    function validarRazonSocial($cliente)
    {
        $result = $this->db->select('*')
            ->from('cliente')
            ->where('razon_social', $cliente['razon_social'])
            ->get()->result();

        return $result;
    }
}