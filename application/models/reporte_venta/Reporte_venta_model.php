<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte_venta_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getVentasComprobantes($params)
    {

        $query = "
            SELECT 
                v.venta_id AS venta_id,
                v.id_cliente AS cliente_id,
                c.identificacion AS identificacion,
                c.razon_social AS cliente_nombre,
                v.id_documento AS documento_id,
                d.des_doc AS documento_nombre,
                v.serie AS serie,
                v.numero AS numero,
                comp.nombre AS comprobante_nombre,
                CONCAT(comp.serie, comp_v.numero) AS comprobante_numero,
                v.total_impuesto AS impuesto,
                v.total AS total,
                DATE(v.fecha) AS fecha
            FROM
                venta AS v
                    JOIN
                documentos AS d ON d.id_doc = v.id_documento
                    JOIN
                cliente AS c ON c.id_cliente = v.id_cliente
                    JOIN
                comprobante_ventas AS comp_v ON comp_v.venta_id = v.venta_id
                    JOIN
                comprobantes AS comp ON comp.id = comp_v.comprobante_id
            WHERE
                comp.id = " . $params['comprobante_id'] . " AND v.local_id = " . $params['local_id'] . "
                    AND v.id_moneda = " . $params['moneda_id'] . "
                    AND v.fecha_facturacion >= '".$params['fecha_ini']."'
                    AND v.fecha_facturacion <= '".$params['fecha_fin']."'
        ";

        return $this->db->query($query)->result();
    }

    function getVendedoresComision($params)
    {
        $where_usuario = '';
        if(!empty($params['usuarios_id'])){
            $where_usuario = "AND v.id_vendedor = '".$params['usuarios_id']."'";
        }
        $query = "
                SELECT
                    v.id_vendedor AS vendedor_id,
                    u.nombre AS vendedor_nombre,
                    SUM(v.total) AS total_venta,
                    IFNULL(u.porcentaje_comision, 0) AS comision,
                    IFNULL((SUM(v.total) * u.porcentaje_comision) / 100,0) AS importe_comision
                FROM
                    venta v
                INNER JOIN usuario u ON v.id_vendedor = u.nUsuCodigo
                WHERE
                    v.id_moneda = " . $params['moneda_id'] . " 
                AND v.local_id = " . $params['local_id'] . "
                AND v.venta_status = 'COMPLETADO' 
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                $where_usuario
                GROUP BY
                    v.id_vendedor;
            ";

        return $this->db->query($query)->result();
    }

    function getMargenUtilidad($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = $local_id = '';
        $local_id .= ($params['local_id']>0)? " AND v.local_id=".$params['local_id'] : "";
        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id.$local_id;

        $query = "SELECT
                    l.local_nombre,
                    p.producto_nombre,
                    u.nombre_unidad,
                    SUM(up.unidades * dv.cantidad) AS cantidad,
                    dv.detalle_costo_promedio,
                    SUM(
                        IF(
                            v.tipo_impuesto = '2',
                            dv.detalle_importe * (
                                (dv.impuesto_porciento / 100) + 1
                            ),
                            dv.detalle_importe
                        )
                    ) AS detalle_importe,
                    AVG(
                        IF (
                            v.tipo_impuesto = '1',
                            (
                                (dv.detalle_importe / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1)
                            ),
                            (
                                IF(
                                    v.tipo_impuesto = '2',
                                    (
                                        ((dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1)) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1)
                                    ),
                                    (
                                        dv.detalle_importe / (up.unidades * dv.cantidad)
                                    )
                                )
                            )
                        )
                    ) AS costoVentaSi,
                    AVG(
                        IF(
                            v.tipo_impuesto = '1',
                            (
                                dv.precio - 
                                (
                                    dv.precio / (
                                        (dv.impuesto_porciento / 100) + 1
                                    )
                                )
                            ),
                            IF(
                                v.tipo_impuesto = '2',
                                (
                                    dv.precio *  (
                                        (dv.impuesto_porciento / 100) + 1
                                    ) - 
                                    dv.precio
                                ),
                                '0'
                            )
                        )
                    )
                    AS impVenta,
                    AVG(
                        IF (
                            v.tipo_impuesto = '3',
                        IF (
                            v.tipo_impuesto = '3',
                            (
                                IF (
                                    v.tipo_impuesto = '2',
                                    dv.detalle_importe * (
                                        (dv.impuesto_porciento / 100) + 1
                                    ),
                                    dv.detalle_importe
                                ) / (up.unidades * dv.cantidad)
                            ),
                            (
                                IF (
                                    v.tipo_impuesto = '2',
                                    dv.detalle_importe * (
                                        (dv.impuesto_porciento / 100) + 1
                                    ),
                                    dv.detalle_importe
                                ) / (up.unidades * dv.cantidad)
                            ) / (
                                (dv.impuesto_porciento / 100) + 1
                            )
                        ),
                        (
                            IF (
                                v.tipo_impuesto = '3',
                                (
                                    IF (
                                        v.tipo_impuesto = '2',
                                        dv.detalle_importe * (
                                            (dv.impuesto_porciento / 100) + 1
                                        ),
                                        dv.detalle_importe
                                    ) / (up.unidades * dv.cantidad)
                                ),
                                (
                                    IF (
                                        v.tipo_impuesto = '2',
                                        dv.detalle_importe * (
                                            (dv.impuesto_porciento / 100) + 1
                                        ),
                                        dv.detalle_importe
                                    ) / (up.unidades * dv.cantidad)
                                ) / (
                                    (dv.impuesto_porciento / 100) + 1
                                )
                            )
                        ) * (
                            (dv.impuesto_porciento / 100) + 1
                        )
                        )
                    ) AS costoVenta,
                    AVG(dv.detalle_costo_ultimo) AS detalle_costo_ultimo,
                    AVG(
                        IF (
                            dv.tipo_impuesto_compra = '1',
                            (
                                dv.detalle_costo_ultimo / (
                                    (dv.impuesto_porciento / 100) + 1
                                )
                            ),
                            dv.detalle_costo_ultimo
                        )
                    ) AS costoCompraSi,
                    AVG(
                        IF(
                            dv.tipo_impuesto_compra = '1',
                            (
                                dv.detalle_costo_ultimo - 
                                (
                                    dv.detalle_costo_ultimo / (
                                        (dv.impuesto_porciento / 100) + 1
                                    )
                                )
                            ),
                            IF(
                                dv.tipo_impuesto_compra = '2',
                                (
                                    dv.detalle_costo_ultimo *  (
                                        (dv.impuesto_porciento / 100) + 1
                                    ) - 
                                    dv.detalle_costo_ultimo
                                ),
                                '0'
                            )
                        )
                    )
                    AS impCompra,
                    AVG(
                        IF(
                            dv.tipo_impuesto_compra = '1',
                            (
                                (
                                    dv.detalle_costo_ultimo / (
                                        (dv.impuesto_porciento / 100) + 1
                                    )
                                ) +
                                (
                                    dv.detalle_costo_ultimo - 
                                    (
                                        dv.detalle_costo_ultimo / (
                                            (dv.impuesto_porciento / 100) + 1
                                        )
                                    )
                                )
                            ),
                            (
                                IF(
                                    dv.tipo_impuesto_compra = '2',
                                    (
                                        dv.detalle_costo_ultimo * 
                                        (
                                            (dv.impuesto_porciento / 100) + 1
                                        )
                                    ),
                                    (
                                        dv.detalle_costo_ultimo
                                    )
                                )
                            )
                        )
                    ) AS costoCompraImp,
                    dv.impuesto_porciento,
                    v.tipo_impuesto
                FROM
                    detalle_venta dv
                INNER JOIN venta v ON v.venta_id = dv.id_venta
                INNER JOIN `local` l ON v.local_id = l.int_local_id
                INNER JOIN producto p ON p.producto_id = dv.id_producto
                INNER JOIN unidades_has_producto up ON dv.id_producto = up.producto_id
                AND dv.unidad_medida = up.id_unidad
                INNER JOIN unidades_has_producto up2 ON dv.id_producto = up2.producto_id
                AND (
                    SELECT
                        id_unidad
                    FROM
                        unidades_has_producto
                    WHERE
                        unidades_has_producto.producto_id = dv.id_producto
                    ORDER BY
                        orden DESC
                    LIMIT 1
                ) = up2.id_unidad
                INNER JOIN unidades u ON u.id_unidad = up2.id_unidad
                INNER JOIN producto_costo_unitario pcu ON p.producto_id = pcu.producto_id
                AND v.id_moneda = pcu.moneda_id
                AND activo = 1
            WHERE 
                v.venta_status='COMPLETADO'
                AND v.id_moneda = ".$params['moneda_id']."
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                $search";

        $query .= " GROUP BY p.producto_id";
        return $this->db->query($query)->result();
    }

    function getUtilidadProducto($params)
    {
        $where = "v.venta_status='COMPLETADO'";
        if($params['local_id']>0){
            $where .= " AND v.local_id = ".$params['local_id'];
        }
        if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            if(!empty($where)){
                $where .= " AND ";
            }
            $where .= "v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'";
        }

        $query = "SELECT v.venta_id, DATE_FORMAT(v.fecha, '%d/%m/%Y') AS fecha, pr.proveedor_nombre, p.producto_nombre, u.nombre_unidad, SUM(up.unidades * dv.cantidad) AS cantidad, dv.detalle_costo_promedio, dv.detalle_importe, l.local_nombre, dv.detalle_costo_ultimo, dv.impuesto_porciento, v.tipo_impuesto, dv.tipo_impuesto_compra, v.id_moneda
            FROM detalle_venta dv
            INNER JOIN venta v ON v.venta_id=dv.id_venta 
            INNER JOIN producto p ON p.producto_id=dv.id_producto 
            INNER JOIN `local` l ON v.local_id = l.int_local_id
            LEFT JOIN proveedor pr ON p.producto_proveedor=pr.id_proveedor
            INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id 
            AND dv.unidad_medida=up.id_unidad
            INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
            AND (
                select id_unidad from unidades_has_producto 
                where unidades_has_producto.producto_id = dv.id_producto
                ORDER BY orden DESC LIMIT 1
            ) = up2.id_unidad 
            INNER JOIN unidades u ON u.id_unidad=up2.id_unidad
            INNER JOIN producto_costo_unitario pcu ON  p.producto_id = pcu.producto_id AND v.id_moneda = pcu.moneda_id AND activo=1 ";
        if(!empty($where)){
            $query .= " WHERE ". $where;
        }
        $query .= " GROUP BY v.venta_id, dv.id_detalle ORDER BY v.venta_id";
        return $this->db->query($query)->result();
    }    
}
