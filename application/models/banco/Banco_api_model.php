<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banco_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
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
}
