<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporte_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getProductoVendido($params){
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $query = "
            SELECT 
                dv.id_producto AS id_producto,
                p.producto_nombre AS producto_nombre,
                SUM(up.unidades * dv.cantidad) AS ventas, 
                (
                    SELECT SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion) AS und
                    FROM producto_almacen pa
                    WHERE pa.id_producto=dv.id_producto
                ) AS stock,
                u.nombre_unidad AS nombre_unidad
            FROM 
                detalle_venta AS dv
                INNER JOIN 
                    venta v ON v.venta_id=dv.id_venta
                INNER JOIN 
                    producto p ON dv.id_producto=p.producto_id
                INNER JOIN 
                    unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                INNER JOIN 
                    unidades u ON up.id_unidad=u.id_unidad
            WHERE 
                v.venta_status='COMPLETADO'
                AND v.local_id = ".$params['local_id']."
                AND v.id_moneda = ".$params['moneda_id']."
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                $search
            GROUP BY 
                dv.id_producto
            ORDER BY 
                id_producto
        ";

        return $this->db->query($query)->result();
    }

    function getVentaSucursal($params){
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $query = "
            SELECT 
                dv.id_producto AS id_producto, 
                p.producto_nombre AS producto_nombre, 
                SUM(up.unidades * dv.cantidad) AS ventas, 
                (
                    SELECT SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion) AS und
                    FROM producto_almacen pa
                    WHERE pa.id_producto=dv.id_producto
                ) AS stock,
                u.nombre_unidad AS nombre_unidad
            FROM 
                detalle_venta dv
                INNER JOIN 
                    venta v ON v.venta_id=dv.id_venta
                INNER JOIN
                    producto p ON dv.id_producto=p.producto_id
                INNER JOIN
                    unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                INNER JOIN 
                    unidades u ON up.id_unidad=u.id_unidad
                INNER JOIN 
                    `local` l ON v.local_id=l.int_local_id
                WHERE 
                    v.venta_status='COMPLETADO'
                    AND v.local_id = ".$params['local_id']."
                    AND v.id_moneda = ".$params['moneda_id']."
                    AND v.fecha >= '".$params['fecha_ini']."'
                    AND v.fecha <= '".$params['fecha_fin']."'
                    $search
                GROUP BY
                    dv.id_producto
                ORDER BY 
                    dv.id_producto
        ";

        return $this->db->query($query)->result();
    }

    function getVentaEmpleado($params){
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $tipo = $params['tipo'];
        $query = "
            SELECT 
                v.id_vendedor AS id_vendedor,
                u.nombre AS nombre,
                $tipo AS tipo,
                SUM(up.unidades * dv.cantidad) AS cantidad, 
                v.total AS total,
                (
                    SELECT COUNT(*) FROM venta WHERE venta_status='ANULADO' AND id_vendedor=u.nUsuCodigo
                ) AS anulado
            FROM 
                venta v
                INNER JOIN 
                    detalle_venta dv ON v.venta_id=dv.id_venta
                INNER JOIN 
                    usuario u ON v.id_vendedor=u.nUsuCodigo
                INNER JOIN 
                    producto p ON p.producto_id = dv.id_producto
                INNER JOIN
                    unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                WHERE
                    v.venta_status='COMPLETADO'
                    AND v.local_id = ".$params['local_id']."
                    AND v.id_moneda = ".$params['moneda_id']."
                    AND v.fecha >= '".$params['fecha_ini']."'
                    AND v.fecha <= '".$params['fecha_fin']."'
                    $search
                GROUP BY
                v.id_vendedor
        ";

        return $this->db->query($query)->result();
    }
}