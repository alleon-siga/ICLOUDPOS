<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporte_shadow_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getReporte($params)
    {
        $this->db->select('dv.precio_venta, dv.detalle_costo_ultimo, dv.cantidad, dv.detalle_importe, (dv.cantidad * dv.detalle_costo_ultimo) as detalle_importe_cc, 
            vsd.precio_venta as precio_venta2, vsd.detalle_costo_ultimo as detalle_costo_ultimo2, vsd.cantidad as cantidad2, vsd.detalle_importe as detalle_importe2, (vsd.cantidad * vsd.detalle_costo_ultimo) as detalle_importe_cc2');
        $this->db->from('detalle_venta dv');
        $this->db->join('venta v', 'dv.id_venta = v.venta_id');
        $this->db->join('venta_shadow vs', 'v.venta_id = vs.venta_id');
        $this->db->join('venta_shadow_detalle vsd', 'vs.id = vsd.id_venta_shadow');
        $this->db->where("v.venta_status='COMPLETADO'");
        $this->db->where("DATE(v.fecha) >= '".$params['fecha_ini']."' AND DATE(v.fecha) <= '".$params['fecha_fin']."'");
        if($params['moneda_id']>0){
            $this->db->where('v.id_moneda = '.$params['moneda_id']);
        }
        return $this->db->get()->result();
    }
}