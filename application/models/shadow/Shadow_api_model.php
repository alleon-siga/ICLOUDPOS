<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shadow_api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_stock($producto_id)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        return $this->db->select('
            shadow_stock.stock as stock_min,
            unidades.id_unidad as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr')
            ->from('shadow_stock')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id = shadow_stock.producto_id')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->where('shadow_stock.producto_id', $producto_id)
            ->where('unidades_has_producto.orden', $orden_max->orden)
            ->get()->row();
    }
}
