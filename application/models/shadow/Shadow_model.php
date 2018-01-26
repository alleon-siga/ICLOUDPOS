<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class shadow_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('venta_new/venta_new_model', 'venta');
    }

    function save_venta_contable($venta_id, $detalles_productos)
    {

        $this->db->where('venta_id', $venta_id);
        $this->db->delete('venta_contable_detalle');
        $venta_detalle = array();
        foreach ($detalles_productos as $producto) {

            //preparo el detalle de la venta
            $producto_detalle = array(
                'venta_id' => $venta_id,
                'producto_id' => $producto->producto_id,
                'precio' => $producto->precio,
                'cantidad' => $producto->cantidad,
                'unidad_id' => $producto->unidad_id
            );
            array_push($venta_detalle, $producto_detalle);
        }

        //inserto los detalles de la venta
        $this->db->insert_batch('venta_contable_detalle', $venta_detalle);
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

    public function get_precio_contable($producto_id, $moneda_id)
    {
        $query = $this->db->get_where('producto_costo_unitario', array(
            'producto_id' => $producto_id,
            'moneda_id' => $moneda_id
        ))->row();

        return $query != null ? $query->contable_costo + ($query->contable_costo * valueOption("COSTO_AUMENTO", '5') / 100) : 0;
    }

    function get_venta_contable_detalle($venta_id)
    {
        $venta = $this->venta->get_ventas(array('venta_id' => $venta_id));

        //$venta->venta_documentos = $this->db->get_where('venta_documento', array('venta_id' => $venta_id))->result();

        $venta->detalles = $this->db->select('
            venta_contable_detalle.id as detalle_id,
            venta_contable_detalle.producto_id as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            venta_contable_detalle.precio as precio,
            venta_contable_detalle.cantidad as cantidad,
            venta_contable_detalle.unidad_id as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            (venta_contable_detalle.cantidad * venta_contable_detalle.precio) as importe
            ')
            ->from('venta_contable_detalle')
            ->join('producto', 'producto.producto_id=venta_contable_detalle.producto_id')
            ->join('unidades', 'unidades.id_unidad=venta_contable_detalle.unidad_id')
            ->where('venta_contable_detalle.venta_id', $venta->venta_id)
            ->get()->result();

        return $venta;
    }


}
