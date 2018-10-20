<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class reporte_shadow_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function getReporte($params) {
        $this->db->select('dv.precio_venta, dv.detalle_costo_ultimo, dv.cantidad, dv.detalle_importe, (dv.cantidad * dv.detalle_costo_ultimo) as detalle_importe_cc, 
            vsd.precio_venta as precio_venta2, vsd.detalle_costo_ultimo as detalle_costo_ultimo2, vsd.cantidad as cantidad2, vsd.detalle_importe as detalle_importe2, (vsd.cantidad * vsd.detalle_costo_ultimo) as detalle_importe_cc2');
        $this->db->from('detalle_venta dv');
        $this->db->join('venta v', 'dv.id_venta = v.venta_id');
        $this->db->join('venta_shadow vs', 'v.venta_id = vs.venta_id');
        $this->db->join('venta_shadow_detalle vsd', 'vs.id = vsd.id_venta_shadow');
        $this->db->where("v.venta_status='COMPLETADO'");
        $this->db->where("DATE(v.fecha) >= '" . $params['fecha_ini'] . "' AND DATE(v.fecha) <= '" . $params['fecha_fin'] . "'");
        if ($params['moneda_id'] > 0) {
            $this->db->where('v.id_moneda = ' . $params['moneda_id']);
        }
        return $this->db->get()->result();
    }

    function getReporte_cg($params) {

        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';
        $marca_id .= ($params['marca_id'] > 0) ? " AND p.producto_marca=" . $params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id'] > 0) ? " AND p.produto_grupo=" . $params['grupo_id'] : "";
        $familia_id .= ($params['familia_id'] > 0) ? " AND p.producto_familia=" . $params['familia_id'] : "";
        $linea_id .= ($params['linea_id'] > 0) ? " AND p.producto_linea=" . $params['linea_id'] : "";
        $producto_id .= ($params['producto_id'] != '') ? " AND p.producto_id IN(" . implode(",", $params['producto_id']) . ")" : "";
        $search = $marca_id . $grupo_id . $familia_id . $linea_id . $producto_id;

        $query = "SELECT 
                p.producto_codigo_interno, p.producto_nombre, p.producto_marca, u.nombre_unidad,
                pcu.costo as costo_real, 
                pcu.contable_costo as contable_costo,
                (pcu.costo / pcu.tipo_cambio) as costo_real_d, 
                (pcu.contable_costo / pcu.tipo_cambio) as costo_contable_d, 
                pcu.tipo_cambio, 
                pcu.porcentaje_utilidad,
                ((pcu.contable_costo * pcu.porcentaje_utilidad) / 100)+pcu.contable_costo as precio_compra_s,
                (((pcu.contable_costo / pcu.tipo_cambio) * pcu.porcentaje_utilidad) / 100)+(pcu.contable_costo / pcu.tipo_cambio) as precio_compra_d
                FROM `producto` as p
                INNER JOIN producto_costo_unitario as pcu
                on
                pcu.producto_id=p.producto_id
                INNER JOIN producto_almacen as pa
                on
                pa.id_producto=p.producto_id
                INNER JOIN `local` as l
                on
                l.int_local_id=pa.id_local
                INNER JOIN unidades_has_producto as uhp
                on
                uhp.producto_id=p.producto_id
                INNER JOIN unidades as u
                on
                u.id_unidad=uhp.id_unidad
                WHERE l.int_local_id=" . $params['local_id'] . ""
                . " $search";
        $query .= "GROUP BY p.producto_codigo_interno";

        return $this->db->query($query)->result();

//        $this->db->select('p.producto_codigo_interno, p.producto_nombre,p.producto_marca, u.nombre_unidad, pcu.costo as costo_real, pcu.contable_costo as contable_costo,
//            (pcu.costo / pcu.tipo_cambio) as costo_real_d, (pcu.contable_costo / pcu.tipo_cambio) as costo_contable_s, pcu.tipo_cambio,
//            pcu.porcentaje_utilidad,((pcu.contable_costo * pcu.porcentaje_utilidad) / 100)+pcu.contable_costo as precio_compra_s,
//            (((pcu.contable_costo / pcu.tipo_cambio) * pcu.porcentaje_utilidad) / 100)+(pcu.contable_costo / pcu.tipo_cambio) as precio_compra_d');
//        $this->db->from('producto p');
//        $this->db->join('producto_costo_unitario pcu', 'pcu.producto_id=p.producto_id');
//        $this->db->join('producto_almacen pa', 'pa.id_producto=p.producto_id');
//        $this->db->join('local l', 'l.int_local_id=pa.id_local');
//        $this->db->join('unidades_has_producto uhp', 'uhp.producto_id=p.producto_id');
//        $this->db->join('unidades u', 'u.id_unidad=uhp.id_unidad');
//        $this->db->where('l.int_local_id = ' . $params['local_id']);
//        $this->db->where($search);
//        $this->db->group_by('p.producto_codigo_interno');
//        return $this->db->get()->result();
    }

    function getReporte_cv($params) {

        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = $local_id = '';
        $local_id .= ($params['local_id'] > 0) ? " AND v.local_id=" . $params['local_id'] : "";
        $marca_id .= ($params['marca_id'] > 0) ? " AND p.producto_marca=" . $params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id'] > 0) ? " AND p.produto_grupo=" . $params['grupo_id'] : "";
        $familia_id .= ($params['familia_id'] > 0) ? " AND p.producto_familia=" . $params['familia_id'] : "";
        $linea_id .= ($params['linea_id'] > 0) ? " AND p.producto_linea=" . $params['linea_id'] : "";
        $producto_id .= ($params['producto_id'] != '') ? " AND p.producto_id IN(" . implode(",", $params['producto_id']) . ")" : "";
        $search = $marca_id . $grupo_id . $familia_id . $linea_id . $producto_id . $local_id;

        $query = "SELECT 
                p.producto_codigo_interno, p.producto_nombre, p.producto_marca, u.nombre_unidad,
                pcu.costo as costo_real, 
                pcu.contable_costo as contable_costo,
                (pcu.costo / pcu.tipo_cambio) as costo_real_d, 
                (pcu.contable_costo / pcu.tipo_cambio) as costo_contable_d, 
                pcu.tipo_cambio, 
                pcu.porcentaje_utilidad,
                ((pcu.contable_costo * pcu.porcentaje_utilidad) / 100)+pcu.contable_costo as precio_compra_s,
                (((pcu.contable_costo / pcu.tipo_cambio) * pcu.porcentaje_utilidad) / 100)+(pcu.contable_costo / pcu.tipo_cambio) as precio_compra_d
                FROM `producto` as p
                INNER JOIN producto_costo_unitario as pcu
                on
                pcu.producto_id=p.producto_id
                INNER JOIN producto_almacen as pa
                on
                pa.id_producto=p.producto_id
                INNER JOIN `local` as l
                on
                l.int_local_id=pa.id_local
                INNER JOIN unidades_has_producto as uhp
                on
                uhp.producto_id=p.producto_id
                INNER JOIN unidades as u
                on
                u.id_unidad=uhp.id_unidad
                WHERE l.int_local_id=" . $params['local_id'] . " "
                . "$search";
        $query .= "GROUP BY p.producto_codigo_interno";

        return $this->db->query($query)->result();
    }

    //Get info de ventas por documento (19-10-2018) Carlos Camargo ('por depurar')
    function getReporte_vd($params) {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id  = $local_id = '';
        $local_id .= ($params['local_id'] > 0) ? " AND f.local_id=" . $params['local_id'] : "";
        $marca_id .= ($params['marca_id'] > 0) ? " AND p.producto_marca=" . $params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id'] > 0) ? " AND p.produto_grupo=" . $params['grupo_id'] : "";
        $familia_id .= ($params['familia_id'] > 0) ? " AND p.producto_familia=" . $params['familia_id'] : "";
        $linea_id .= ($params['linea_id'] > 0) ? " AND p.producto_linea=" . $params['linea_id'] : "";
        $producto_id .= ($params['producto_id'] != '') ? " AND p.producto_id IN(" . implode(",", $params['producto_id']) . ")" : "";
        $search = $marca_id . $grupo_id . $familia_id . $linea_id . $producto_id . $local_id;
        $tipo_reporte='';
        $tipo_reporte.=($params['estado_cr_id'] == 0) ? " AND f.id=vs.id_factura " : "";
        $searcht=$tipo_reporte;

        $query = "SELECT 
                    p.producto_codigo_interno as codigo_producto,
                    p.producto_nombre as nombre_producto,
                    ma.nombre_marca as marca_producto,
                    (SELECT count(f.documento_tipo) from facturacion as f
                    INNER JOIN facturacion_detalle as fd on
                    fd.facturacion_id=f.id
                    INNER JOIN venta_shadow as vs on
                    vs.id_factura=f.id
                    WHERE f.documento_tipo=03 AND fd.producto_codigo=p.producto_codigo_interno $searcht) AS ven_bol,
                    (SELECT count(f.documento_tipo) from facturacion as f
                    INNER JOIN facturacion_detalle as fd on
                    fd.facturacion_id=f.id
                    INNER JOIN venta_shadow as vs on
                    vs.id_factura=f.id
                    WHERE f.documento_tipo=01 AND fd.producto_codigo=p.producto_codigo_interno $searcht) AS ven_fac,";
                    if($params['estado_cr_id'] == 1){
                    $query.="(select COUNT(DISTINCT(venta_id)) as 'NumVentas' 
                    from venta v inner join detalle_venta dv on v.venta_id = dv.id_venta 
                    where dv.id_producto = p.producto_id and
                    v.venta_status = 'COMPLETADO' and id_documento in (6)) AS ven_nv";
                    }else{
                        $query.=" '' AS ven_nv ";
                    }
                    $query.=",
                    ((SELECT count(f.documento_tipo) from facturacion as f
                    INNER JOIN facturacion_detalle as fd on
                    fd.facturacion_id=f.id
                    INNER JOIN venta_shadow as vs on
                    vs.id_factura=f.id
                    WHERE fd.producto_codigo=p.producto_codigo_interno $searcht)";
                    if($params['estado_cr_id'] == 1){
                        $query.=" +(select COUNT(DISTINCT(venta_id)) as 'NumVentas' 
                    from venta v inner join detalle_venta dv on v.venta_id = dv.id_venta 
                    where dv.id_producto = p.producto_id and
                    v.venta_status = 'COMPLETADO' and id_documento in (6))";
                        
                    }else{
                        
                    } $query.=") AS ven_total,
                    (SELECT sum(f.total) from facturacion as f
                    INNER JOIN facturacion_detalle as fd on
                    fd.facturacion_id=f.id
                    INNER JOIN venta_shadow as vs on
                    vs.id_factura=f.id
                    WHERE f.documento_tipo=03 AND fd.producto_codigo=p.producto_codigo_interno $searcht) AS ven_bol_t,
                    (SELECT sum(f.total) from facturacion as f
                    INNER JOIN facturacion_detalle as fd on
                    fd.facturacion_id=f.id
                    INNER JOIN venta_shadow as vs on
                    vs.id_factura=f.id
                    WHERE f.documento_tipo=01 AND fd.producto_codigo=p.producto_codigo_interno $searcht) AS ven_fac_t,";
                    if($params['estado_cr_id'] == 1){
                    $query.="(select SUM(DISTINCT(total)) as 'NumVentas' 
                    from venta v inner join detalle_venta dv on v.venta_id = dv.id_venta 
                    where dv.id_producto = p.producto_id and
                    v.venta_status = 'COMPLETADO' and id_documento in (6)) AS ven_nv_t";
                    }else{
                        $query.=" '' AS ven_nv_t ";
                    }
                    $query.=",
                    ((SELECT sum(f.total) from facturacion as f
                    INNER JOIN facturacion_detalle as fd on
                    fd.facturacion_id=f.id
                    INNER JOIN venta_shadow as vs on
                    vs.id_factura=f.id
                    WHERE  fd.producto_codigo=p.producto_codigo_interno $searcht)";
                    if($params['estado_cr_id'] == 1){
                        $query.=" + (select SUM(DISTINCT(total)) as 'NumVentas' 
                    from venta v inner join detalle_venta dv on v.venta_id = dv.id_venta 
                    where dv.id_producto = p.producto_id and
                    v.venta_status = 'COMPLETADO' and id_documento in (6))) AS ven_tot_t";
                        
                    }else{
                      $query.="+(0)) AS ven_tot_t";  
                    } 
                    $query.="
                    FROM producto AS p
                    LEFT JOIN marcas as ma ON
                    p.producto_marca=ma.id_marca 
                    LEFT JOIN detalle_venta as dv ON
                    p.producto_id=dv.id_producto 
                    LEFT JOIN facturacion as f ON
                    f.ref_id=dv.id_venta 
                    LEFT JOIN venta_shadow as vs ON
                    vs.id_factura=f.id
                    LEFT JOIN venta_shadow_detalle as vsd ON
                    vsd.id_venta_shadow=vs.id 
                    LEFT JOIN `local` as lv ON
                    lv.int_local_id=f.local_id 
                    LEFT JOIN documentos as dov ON
                    dov.id_doc=f.documento_tipo                    
                    WHERE f.estado=3 $search  $searcht";
        $query .= "GROUP BY p.producto_id";

        return $this->db->query($query)->result();
    }

}
