<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ingresosYventas_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }


    function get_ventas_ingresos($data){
        $this->db->select('*');
        $this->db->from('movimiento_historico');
        $this->db->join('producto','producto.producto_id=movimiento_historico.producto_id');
        $this->db->where($data);
        $query = $this->db->get();
        return $query->result_array();

    }


}