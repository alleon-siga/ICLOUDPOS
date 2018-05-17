<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Producto_api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_productos_fav($str_producto)
    {
        $result = $this->db->select('producto_id, producto_codigo_interno as codigo, producto_nombre, producto_codigo_barra as barra')
            ->from('producto')
            ->join('unidades_has_precio', 'unidades_has_precio.id_producto = producto.producto_id')
            ->where('producto.producto_id', $str_producto)
            ->where('producto.producto_estatus', '1')
            ->where('producto.producto_estado', '1')
            ->where('unidades_has_precio.id_precio', 3)
            ->group_by('producto.producto_id')
            ->get()->result_array();

        return $result;
    }

    function get_productos_listall($str_producto)
    {
        $result = $this->db->select('producto_id, producto_codigo_interno as codigo, producto_nombre, producto_codigo_barra as barra')
            ->from('producto')
            ->join('unidades_has_precio', 'unidades_has_precio.id_producto = producto.producto_id')
            ->like('producto_nombre', $str_producto)
            ->or_like('producto_codigo_interno', $str_producto)
            ->where('producto.producto_estatus', '1')
            ->where('producto.producto_estado', '1')
            ->where('unidades_has_precio.id_precio', 3)
            ->group_by('producto.producto_id')
            ->limit(500)
            ->get()->result_array();

        return $result;
    }

    function get_productos_unidprec($id_producto, $id_precio)
    {
        $result = $this->db->select('unidades_has_precio.precio as precio, unidades_has_producto.*, unidades.nombre_unidad, producto.producto_cualidad, unidades.abreviatura as abr, unidades.presentacion')
            ->from('producto')
            ->join('unidades_has_producto', 'producto.producto_id = unidades_has_producto.producto_id')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->join('unidades_has_precio', 'unidades_has_precio.id_producto = producto.producto_id')
            ->where('producto.producto_id', $id_producto)
            ->where('unidades_has_precio.id_precio', $id_precio)
            ->where('unidades_has_precio.id_unidad = unidades.id_unidad')
            ->group_by('id_unidad')
            ->order_by('orden', 'ASC')->get()->result();

        return $result;
    }

    function get_producto_by($id_producto, $id_local)
    {
        $result = $this->db->select('*')
            ->from('producto_almacen')
            ->where('id_producto', $id_producto)
            ->where('id_local', $id_local)
            ->get()->result_row();

        return $result;
    }
}