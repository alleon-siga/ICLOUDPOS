<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class banco_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_all()
    {
        return $this->db->select('banco.*, moneda.*, caja_desglose.saldo as saldo, caja_desglose.descripcion as descripcion, caja.moneda_id as moneda_id')
            ->from('banco')
            ->join('caja_desglose', 'caja_desglose.id = banco.cuenta_id', 'left')
            ->join('caja', 'caja.id = caja_desglose.caja_id', 'left')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id', 'left')
            ->where('banco_status', 1)
            ->get()->result_array();
    }
    function get_all_do()
    {
        return $this->db->select('banco.*, moneda.*, caja_desglose.saldo as saldo, caja_desglose.descripcion as descripcion, caja.moneda_id as moneda_id')
            ->from('banco')
            ->join('caja_desglose', 'caja_desglose.id = banco.cuenta_id', 'left')
            ->join('caja', 'caja.id = caja_desglose.caja_id', 'left')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id', 'left')
            ->where('banco_status', 1)
            ->where('caja.moneda_id', 1030)
            ->get()->result_array();
    }
    function get_all_in_object()
    {
        return $this->db->select('banco.*, moneda.*, caja_desglose.saldo as saldo, caja_desglose.descripcion as descripcion, caja.moneda_id as moneda_id')
            ->from('banco')
            ->join('caja_desglose', 'caja_desglose.id = banco.cuenta_id', 'left')
            ->join('caja', 'caja.id = caja_desglose.caja_id', 'left')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id', 'left')
            ->where('banco_status', 1)
            ->get()->result();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('banco');
        return $query->row_array();
    }

    function insertar($banco)
    {

        $this->db->trans_start();
        $this->db->insert('banco', $banco);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($banco)
    {

        $this->db->trans_start();
        $this->db->where('banco_id', $banco['banco_id']);
        $this->db->update('banco', $banco);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }
    function buscarNumeroOperacion($data = array())
    {
        if ($data['pago_id'] != 3) {
            $this->db->where('pago_data', $data['num_oper']);
            $this->db->where('historial_tipopago', $data['pago_id']);
            $resultado=$this->db->get('historial_pagos_clientes');
            return $resultado->num_rows() ;
        }
        else
            return 0;

    }
}
