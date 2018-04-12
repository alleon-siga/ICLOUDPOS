<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporte_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getProductoVendido($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";

        if($params['tipo']==1){ // Productos con ventas
            $where = "HAVING ventas IS NOT NULL";
        }elseif($params['tipo']==2){ //Productos sin ventas
            $where = "HAVING ventas IS NULL";
        }else{ //Todos
            $where = "";
        }
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        //Limitar top
        $limit = '';
        if(isset($params['limit'])){
            $limit = "LIMIT 0, ".$params['limit'];
        }
        $query = "SELECT p.producto_id AS producto_id, p.producto_codigo_interno AS producto_codigo_interno, p.producto_nombre AS producto_nombre,
            (
                SELECT SUM(up.unidades * dv.cantidad)
                FROM detalle_venta AS dv
                INNER JOIN venta v ON v.venta_id=dv.id_venta
                INNER JOIN producto p2 ON dv.id_producto=p2.producto_id
                INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
                AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up2.id_unidad
                INNER JOIN unidades u ON up2.id_unidad=u.id_unidad
                WHERE dv.id_producto = p.producto_id AND v.venta_status='COMPLETADO' AND v.local_id = '".$params['local_id']."' AND v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'
            ) AS ventas,
            (
                SELECT SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion)
                FROM producto_almacen pa
                WHERE pa.id_producto=p.producto_id AND pa.id_local = '".$params['local_id']."'
            ) AS stock,
            (
                SELECT u.nombre_unidad
                FROM unidades u, producto p2, unidades_has_producto up, unidades_has_producto up2
                WHERE p2.producto_id = p.producto_id AND p2.producto_id=up.producto_id AND u.id_unidad=up.id_unidad AND p2.producto_id=up2.producto_id AND (SELECT id_unidad FROM unidades_has_producto WHERE unidades_has_producto.producto_id = p2.producto_id  ORDER BY orden DESC LIMIT 1) = up2.id_unidad 
                LIMIT 1
            ) AS nombre_unidad
            FROM producto p
            WHERE p.producto_estado='1' ".$search." ".$where." ORDER BY ventas DESC ".$limit;

        return $this->db->query($query)->result();
    }

    function getVentaSucursal($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $query = "SELECT p.producto_id, p.producto_codigo_interno, p.producto_nombre, u.nombre_unidad";
        
        $sqlLocal = $this->db->select('`l`.`int_local_id` AS `int_local_id`,`l`.`local_nombre` AS `local_nombre`');
        $sqlLocal = $this->db->from('(`local` `l`)');
        $sqlLocal = $this->db->where('`l`.`local_status` = 1');
        $sqlLocal = $this->db->get();
        $x=1;
        foreach ($sqlLocal->result() as $row)
        {
            $local = $row->int_local_id;
            $query .= ",
                (
                    SELECT 
                        IF(SUM(up.unidades * dv.cantidad) IS NULL, '0', SUM(up.unidades * dv.cantidad))
                    FROM venta v
                    INNER JOIN detalle_venta dv ON v.venta_id=dv.id_venta 
                    INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                    WHERE v.venta_status='COMPLETADO' AND v.local_id='$local' AND dv.id_producto=p.producto_id
                ) AS cantVend$x,
                (
                    SELECT 
                        IF(SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion) IS NULL, 0, SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion))
                    FROM producto_almacen pa
                    WHERE pa.id_local='$local' AND pa.id_producto=p.producto_id
                ) AS stock$x,
                (
                    SELECT 
                        SUM(dv.precio * dv.cantidad)
                    FROM venta v
                    INNER JOIN detalle_venta dv ON v.venta_id=dv.id_venta 
                    INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                    WHERE v.venta_status='COMPLETADO' AND v.local_id='$local' AND dv.id_producto=p.producto_id
                ) AS total$x
            ";
            $x++;
        }

        $query .= "
            FROM 
                producto AS p
            INNER JOIN 
                detalle_venta dv ON p.producto_id=dv.id_producto
            INNER JOIN 
                venta v ON v.venta_id=dv.id_venta
            INNER JOIN
                unidades_has_producto up3 ON dv.id_producto=up3.producto_id AND dv.unidad_medida=up3.id_unidad
            INNER JOIN 
                unidades_has_producto up4 ON dv.id_producto=up4.producto_id 
                AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up4.id_unidad 
            INNER JOIN
                unidades u ON up4.id_unidad=u.id_unidad
            WHERE 
                p.producto_estado='1'
                AND v.venta_status='COMPLETADO'
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                AND v.id_moneda = ".$params['moneda_id']."
                $search
            GROUP BY
                dv.id_producto
            ORDER BY
                p.producto_id";

        return $this->db->query($query)->result_array();
    }

    function getVentaEmpleado($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $tipo = $params['tipo'];
        $orden = ($tipo=='1')? "cantidad":"total";
        $local_id = ($params['local_id']>0)? " AND v.local_id = ".$params['local_id'] : "";
        //Limitar top
        $limit = '';
        if(isset($params['limit'])){
            $limit = "LIMIT 0, ".$params['limit'];
        }
        $query = "
            SELECT
                p.producto_id AS producto_id, 
                v.id_vendedor AS id_vendedor, 
                u.nombre AS nombre,
                $tipo AS tipo,
                SUM(up.unidades * dv.cantidad) AS cantidad, 
                SUM(dv.precio * dv.cantidad) AS total,
                (
                    SELECT COUNT(*) FROM venta WHERE venta_status='ANULADO' AND id_vendedor=u.nUsuCodigo
                ) AS anulado
            FROM 
                detalle_venta dv
                INNER JOIN 
                    venta v ON v.venta_id=dv.id_venta
                INNER JOIN 
                    usuario u ON v.id_vendedor=u.nUsuCodigo
                INNER JOIN 
                    producto p ON p.producto_id = dv.id_producto
                INNER JOIN 
                    unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                INNER JOIN 
                    unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
                    AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up2.id_unidad 
                WHERE
                    v.venta_status='COMPLETADO'
                    AND v.id_moneda = ".$params['moneda_id']." 
                    $local_id
                    AND v.fecha >= '".$params['fecha_ini']."'
                    AND v.fecha <= '".$params['fecha_fin']."'
                    $search
            GROUP BY
                v.id_vendedor
            ORDER BY 
                $orden DESC $limit
        ";

        return $this->db->query($query)->result();
    }

    function getMargenUtilidad($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;

        $query = "
            SELECT dv.id_producto, p.producto_codigo_interno,
            p.producto_nombre,  p.producto_codigo_interno, 
            SUM(up.unidades * dv.cantidad) AS cantidad, 
            u.nombre_unidad, 
            di2.precio AS compra,
            i.porcentaje_impuesto,
            dv.precio AS precioUnitario
            FROM detalle_venta dv
            INNER JOIN venta v 
                ON v.venta_id=dv.id_venta
            INNER JOIN producto p 
                ON dv.id_producto = p.producto_id
            INNER JOIN unidades_has_producto up 
                ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
            INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up2.id_unidad 
            INNER JOIN unidades u 
                ON up2.id_unidad=u.id_unidad
            INNER JOIN ingreso ing
                ON ing.local_id = v.local_id AND ing.id_moneda = v.id_moneda
            INNER JOIN detalleingreso di
                ON di.id_producto=p.producto_id AND di.impuesto_id = dv.impuesto_id AND di.id_ingreso = ing.id_ingreso AND di.unidad_medida = dv.unidad_medida
            INNER JOIN detalleingreso di2
                ON di.id_producto=p.producto_id AND di2.impuesto_id = dv.impuesto_id AND di2.id_ingreso = ing.id_ingreso AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = di2.unidad_medida
            INNER JOIN impuestos i ON 
                dv.impuesto_id = i.id_impuesto
            WHERE
                v.venta_status='COMPLETADO'
                AND v.id_moneda = ".$params['moneda_id']."
                AND v.local_id = ".$params['local_id']."
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                $search
            GROUP BY 
                dv.id_producto
        ";

        return $this->db->query($query)->result();
    }

    function getStockVentas($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $tipo = $params['tipo'];
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $query = "SELECT p.producto_id, p.producto_codigo_interno, f.nombre_familia, p.producto_nombre, m.nombre_marca, l.nombre_linea";
        foreach ($params['local_id'] as $local_id)
        {
            $query .= ",
            (
                SELECT 
                    IF(SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion) IS NULL, 0, SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion))
                FROM producto_almacen pa
                WHERE pa.id_local='$local_id' AND pa.id_producto=p.producto_id
            ) AS stock_".$local_id;

            switch ($params['tipo_periodo']) {
                case '1': //dia
                    $rango = $params['rangos'];
                    $ArrayFechaI =explode('/', $rango[0]);
                    $fechaI = $ArrayFechaI[2] ."-".$ArrayFechaI[1] ."-".$ArrayFechaI[0];
                    $fecha_ini = date('Y-m-d 00:00:00', strtotime($fechaI));

                    $ArrayFechaF =explode('/', $rango[count($rango)-1]);
                    $fechaF = $ArrayFechaF[2] ."-".$ArrayFechaF[1] ."-".$ArrayFechaF[0];
                    $fecha_fin = date('Y-m-d 23:59:59', strtotime($fechaF));

                    $where = "AND v.fecha >= '".$fecha_ini."' AND v.fecha <= '".$fecha_fin."'";
                    break;
                case '2': //mes
                    $rango = $params['rangos'];
                    $arr = explode('/', $rango[0]);
                    $where = "AND MONTH(v.fecha)='".$arr[0]."' AND YEAR(v.fecha)='".$arr[1]."'";
                    break;
                case '3': //anio
                    $where = "AND YEAR(v.fecha) IN(".implode(",", $params['rangos']).")";
                    break;
            }
            if($tipo=='1'){ //cantidad
                $select = "IF(SUM(up.unidades * dv.cantidad) IS NULL, '0', SUM(up.unidades * dv.cantidad))";
            }else{ //importe
                $select = "IF(SUM(dv.precio * dv.cantidad) IS NULL, '0', SUM(dv.precio * dv.cantidad))";
            }

            $query .= ",
                (   SELECT $select
                    FROM venta v
                    INNER JOIN detalle_venta dv ON v.venta_id=dv.id_venta 
                    INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                    WHERE v.venta_status='COMPLETADO' AND v.local_id='$local_id' AND dv.id_producto=p.producto_id $where
                ) AS cantVend". $local_id;
        }

        $x=1;
        foreach ($params['rangos'] as $rango)
        {
            switch ($params['tipo_periodo']) {
                case '1': //dia
                    $ArrayFecha =explode('/', $rango);
                    $fecha = $ArrayFecha[2] ."-".$ArrayFecha[1] ."-".$ArrayFecha[0];
                    $fecha_ini = date('Y-m-d 00:00:00', strtotime($fecha));
                    $fecha_fin = date('Y-m-d 23:59:59', strtotime($fecha));
                    $where = "AND v.fecha >= '".$fecha_ini."' AND v.fecha <= '".$fecha_fin."'";
                    break;
                case '2': //mes
                    $arr = explode('/', $rango);
                    $where = "AND MONTH(v.fecha)='".$arr[0]."' AND YEAR(v.fecha)='".$arr[1]."'";
                    break;
                case '3': //anio
                    $where = "AND YEAR(v.fecha)='".$rango."'";
                    break;
            }

            foreach ($params['local_id'] as $local_id){
                if($tipo=='1'){ //cantidad
                    $select = "IF(SUM(up.unidades * dv.cantidad) IS NULL, '0', SUM(up.unidades * dv.cantidad))";
                }else{ //importe
                    $select = "SUM(dv.precio * dv.cantidad)";
                }

                $query .= ", 
                    (   SELECT $select FROM venta v
                        INNER JOIN detalle_venta dv ON v.venta_id=dv.id_venta 
                        INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                        WHERE v.venta_status='COMPLETADO' AND v.local_id='$local_id' AND dv.id_producto=p.producto_id $where
                    ) AS periodo".$x."_".$local_id;
            }
            $x++;
        }

        $query .= "
            FROM 
                producto AS p
            INNER JOIN 
                detalle_venta dv ON p.producto_id=dv.id_producto
            INNER JOIN 
                venta v ON v.venta_id=dv.id_venta
            LEFT JOIN 
                familia f ON p.producto_familia = f.id_familia
            LEFT JOIN 
                marcas m ON p.producto_marca = m.id_marca
            LEFT JOIN 
                lineas l ON p.producto_linea = l.id_linea
            WHERE 
                p.producto_estado='1'
                AND v.venta_status='COMPLETADO'
                $search
            GROUP BY
                dv.id_producto
            ORDER BY
                p.producto_nombre
        ";

        return $this->db->query($query)->result_array();
    }    
}