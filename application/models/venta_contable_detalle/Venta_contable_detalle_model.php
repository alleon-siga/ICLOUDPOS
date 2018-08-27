<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Venta_contable_detalle_model extends CI_Model
{

    private $table = 'venta';

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('correlativos/correlativos_model');
        $this->load->model('historico/historico_model');
        $this->load->model('kardex/kardex_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('traspaso/traspaso_model');
        $this->load->model('cajas/cajas_model');
        $this->load->model('cajas/cajas_mov_model');
        $this->load->model('comprobante/comprobante_model');
        $this->load->model('producto/producto_model');
        $this->load->model('facturacion/facturacion_model');
    }

    public function editar($params)
    {
        $this->db->select('COUNT(*) AS total');
        $this->db->from('venta_contable_detalle');
        $this->db->where('venta_id', $params['venta_id']);
        $this->db->where('producto_id', $params['producto_id']);
        $dato = $this->db->get()->row();

        if($dato->total>0){
            $this->db->where('venta_id', $params['venta_id']);
            $this->db->where('producto_id', $params['producto_id']);
            $this->db->update('venta_contable_detalle', $params);
        }else{
            $this->db->insert('venta_contable_detalle', $params);
        }
    }

    public function eliminar($params)
    {
        $this->db->where('venta_id', $params['venta_id']);
        $this->db->where('producto_id', $params['producto_id']);
        $this->db->delete('venta_contable_detalle');
    }

    public function getCosto($id_venta, $id_unidad)
    {
        $this->db->select('contable_costo');
        $this->db->from('detalle_venta dv');
        $this->db->join('venta v', 'v.venta_id = dv.id_venta');
        $this->db->join('producto_costo_unitario pcu', 'v.id_moneda = pcu.moneda_id AND dv.id_producto = pcu.producto_id');
        $this->db->where('v.venta_id', $id_venta);
        $this->db->where('dv.unidad_medida', $id_unidad);
        return $this->db->get()->row();
    }
}
