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
}
