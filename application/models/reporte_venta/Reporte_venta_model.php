<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporte_venta_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getVentasComprobantes($params){

        $query = "
            SELECT 
                v.venta_id AS venta_id,
                c.ruc AS ruc,
                c.razon_social AS cliente_nombre,
                v.id_documento AS documento_id,
                d.des_doc AS documento_nombre,
                v.serie AS serie,
                v.numero AS numero,
                comp.nombre AS comprobante_nombre,
                CONCAT(comp.serie, comp_v.numero) AS comprobante_numero,
                v.total_impuesto AS impuesto,
                v.total AS total
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
                comp.id = ".$params['comprobante_id']." AND v.local_id = ".$params['local_id']."
                    AND v.id_moneda = ".$params['moneda_id']."
                    AND v.fecha_facturacion >= '2018-03-01 00:00:00'
                    AND v.fecha_facturacion <= '2018-03-31 23:59:59'
        ";

        return $this->db->query($query)->result();
    }
}
