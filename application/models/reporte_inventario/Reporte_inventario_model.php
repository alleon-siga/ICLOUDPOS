<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte_inventario_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getVerificaInventario($params)
    {
        $this->db->select("p.producto_id, p.producto_nombre");
        $this->db->from('producto p');
        $this->db->where('p.producto_estado', '1');
        $this->db->where_in('p.producto_id', $params['producto_id']);
        $datosProd = $this->db->get()->result();
        $x=0;
        foreach($datosProd as $datos){
            $producto_id = $datos->producto_id;

            //total de entrada

            //compra
            $cadsql = "SELECT SUM(d.cantidad * up.unidades) AS cantidad
                FROM detalleingreso d
                INNER JOIN ingreso i ON d.id_ingreso = i.id_ingreso
                INNER JOIN unidades_has_producto up ON d.id_producto=up.producto_id AND d.unidad_medida=up.id_unidad
                INNER JOIN unidades_has_producto up2 ON d.id_producto=up2.producto_id AND (
                    SELECT id_unidad FROM unidades_has_producto WHERE unidades_has_producto.producto_id = d.id_producto  ORDER BY orden DESC LIMIT 1
                ) = up2.id_unidad
                WHERE d.id_producto='".$producto_id."' AND i.ingreso_status='COMPLETADO' AND i.tipo_ingreso='COMPRA' 
                AND i.local_id IN(".implode(",", $params['local_id']).")";
            $cantComIng = $this->db->query($cadsql)->row();
            $datosProd[$x]->compra = $cantComIng->cantidad;

            //entrada
            $cadsql = "SELECT SUM(d.cantidad * up.unidades) AS cantidad
                FROM ajuste_detalle d
                INNER JOIN ajuste a ON d.ajuste_id = a.id
                INNER JOIN unidades_has_producto up ON d.producto_id = up.producto_id AND d.unidad_id=up.id_unidad
                INNER JOIN unidades_has_producto up2 ON d.producto_id = up2.producto_id AND (
                    SELECT id_unidad FROM unidades_has_producto WHERE unidades_has_producto.producto_id = d.producto_id  ORDER BY orden DESC LIMIT 1
                ) = up2.id_unidad
                WHERE d.producto_id='".$producto_id."' AND a.io='1' 
                AND a.local_id IN(".implode(",", $params['local_id']).")";
            $cantComAju = $this->db->query($cadsql)->row();
            $datosProd[$x]->entrada = $cantComAju->cantidad;

            //traspaso
            $cadsql = "SELECT SUM(cantidad) AS cantidad FROM traspaso t
                INNER JOIN traspaso_detalle d ON t.id = d.traspaso_id
                INNER JOIN kardex k ON d.kardex_id = k.id AND k.producto_id = '$producto_id' AND io='1' AND t.local_destino IN(".implode(",", $params['local_id']).")";
            $cantTraspaso = $this->db->query($cadsql)->row();
            $datosProd[$x]->traspasoE = $cantTraspaso->cantidad;

            //kardex
            $cadsql = "SELECT SUM(cantidad) AS cantidad from kardex where producto_id = '$producto_id' and io = '1' AND local_id IN(".implode(",", $params['local_id']).")";
            $kardexE = $this->db->query($cadsql)->row();
            $datosProd[$x]->kardexE = $kardexE->cantidad;

            //total de venta
            $cadsql = "SELECT SUM(d.cantidad * up.unidades) AS cantidad
                FROM detalle_venta d
                INNER JOIN venta v ON d.id_venta = v.venta_id
                INNER JOIN unidades_has_producto up ON d.id_producto = up.producto_id AND d.unidad_medida = up.id_unidad
                INNER JOIN unidades_has_producto up2 ON d.id_producto = up2.producto_id AND (
                    SELECT id_unidad FROM unidades_has_producto WHERE unidades_has_producto.producto_id = d.id_producto  ORDER BY orden DESC LIMIT 1
                ) = up2.id_unidad
                WHERE d.id_producto='".$producto_id."' AND v.venta_status='COMPLETADO' AND v.local_id IN(".implode(",", $params['local_id']).")";
            $cantVenta = $this->db->query($cadsql)->row();
            $datosProd[$x]->venta = $cantVenta->cantidad;

            //salida
            $cadsql = "SELECT SUM(d.cantidad * up.unidades) AS cantidad
                FROM ajuste_detalle d
                INNER JOIN ajuste a ON d.ajuste_id = a.id
                INNER JOIN unidades_has_producto up ON d.producto_id = up.producto_id AND d.unidad_id=up.id_unidad
                INNER JOIN unidades_has_producto up2 ON d.producto_id = up2.producto_id AND (
                    SELECT id_unidad FROM unidades_has_producto WHERE unidades_has_producto.producto_id = d.producto_id  ORDER BY orden DESC LIMIT 1
                ) = up2.id_unidad
                WHERE d.producto_id='".$producto_id."' AND a.io='2' AND a.local_id IN(".implode(",", $params['local_id']).")";
            $cantComAju = $this->db->query($cadsql)->row();
            $datosProd[$x]->salida = $cantComAju->cantidad;

            //traspaso
            $cadsql = "SELECT SUM(cantidad) AS cantidad from kardex where producto_id = '$producto_id' and io = '2' and operacion = '11' AND local_id IN(".implode(",", $params['local_id']).")";
            $cantTraspaso = $this->db->query($cadsql)->row();
            $datosProd[$x]->traspasoS = $cantTraspaso->cantidad;

            //kardex
            $cadsql = "SELECT SUM(cantidad) AS cantidad from kardex where producto_id = '$producto_id' and io = '2' AND local_id IN(".implode(",", $params['local_id']).")";
            $kardexS = $this->db->query($cadsql)->row();
            $datosProd[$x]->kardexS = $kardexS->cantidad;

            //Stock actual
            $this->db->select('cantidad, fraccion');
            $this->db->from("producto_almacen");
            $this->db->where_in("id_local", $params['local_id']);
            $this->db->where("id_producto", $producto_id);
            $datoStock = $this->db->get()->result_array();
            $datosProd[$x]->stock = 0;
            foreach ($datoStock as $stocks) {
                $datosProd[$x]->stock += $this->unidades_model->convert_minimo_um($producto_id, $stocks['cantidad'], $stocks['fraccion']);
            }

            $x++;
        }
        return $datosProd;        
    }
}
