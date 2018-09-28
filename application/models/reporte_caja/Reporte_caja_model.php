<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporte_caja_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('unidades/unidades_model');
        $this->load->database();
    }

    function getEstadoResultado($params)
    {
        // Ventas
        $this->db->select("
        SUM(
            IF (
                v.id_moneda = 1029,
                dv.detalle_importe / (
                    (dv.impuesto_porciento / 100) + 1
                ),
                (
                    dv.detalle_importe / (
                        (dv.impuesto_porciento / 100) + 1
                    )
                ) * v.tasa_cambio
            )
        ) AS detalle_importe,
        SUM(
            IF (
                v.id_moneda = 1029,
                dv.detalle_costo_ultimo / (
                    (dv.impuesto_porciento / 100) + 1
                ) * dv.cantidad,
                (
                    dv.detalle_costo_ultimo / (
                        (dv.impuesto_porciento / 100) + 1
                    ) * dv.cantidad
                ) * v.tasa_cambio
            )
        ) AS costo_venta, m.simbolo, v.tasa_cambio");
        $this->db->from('venta v');
        $this->db->join('detalle_venta dv', 'v.venta_id = dv.id_venta');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        $this->db->where("v.venta_status='COMPLETADO'");
        if($params['local_id']>0){
            $this->db->where('v.local_id = '.$params['local_id']);
        }
        if($params['mes'] != '' && $params['year'] != ''){
            $this->db->where('YEAR(v.fecha) = '.$params['year'].' AND MONTH(v.fecha) = '.$params['mes']);
        }
        $ventas = $this->db->get()->row();

        //Grupo de gasto
        $this->db->select('id_grupo_gastos, nom_grupo_gastos');
        $this->db->from('grupo_gastos');
        $grupos = $this->db->get()->result_array();

        $x=0;
        foreach ($grupos as $grupo){
            //Tipo de gasto
            $this->db->select('id_tipos_gasto, nombre_tipos_gasto');
            $this->db->from('tipos_gasto');
            $this->db->where('status_tipos_gasto', '1');
            $this->db->where('id_grupo_gastos', $grupo['id_grupo_gastos']);
            $this->db->where("nombre_tipos_gasto != 'PRESTAMO BANCARIO'");
            $tipo_gastos = $this->db->get()->result_array();

            $a = 0;
            $totSubtotal = 0;
            foreach ($tipo_gastos as $tipo_gasto) {
                //Sumas los gastos deacuerdo al tipo y grupo
                $this->db->select('SUM(IF(id_moneda = 1029, subtotal, subtotal * tasa_cambio)) AS subtotal');
                $this->db->from('gastos');
                $this->db->where('status_gastos', '0'); //Gasto confirmado
                $this->db->where('tipo_gasto', $tipo_gasto['id_tipos_gasto']);
                if($params['local_id']>0){
                    $this->db->where('local_id = '.$params['local_id']);
                }
                if($params['mes'] != '' && $params['year'] != ''){
                    $this->db->where('YEAR(fecha) = '.$params['year'].' AND MONTH(fecha) = '.$params['mes']);
                }
                $suma = $this->db->get()->row_array();

                $tipo_gastos[$a]['suma'] = $suma['subtotal'];

                //Prestamo bancario
                if($tipo_gasto['nombre_tipos_gasto']=='INTERES' || $tipo_gasto['nombre_tipos_gasto']=='COMISION'){
                    if($tipo_gasto['nombre_tipos_gasto'] == 'INTERES'){
                        $this->db->select('SUM(IF(id_moneda = 1029, interes, interes * tasa_cambio)) AS subtotal');
                    }else{
                        $this->db->select('SUM(IF(id_moneda = 1029, comision, comision * tasa_cambio)) AS subtotal');
                    }
                    $this->db->from('ingreso i');
                    $this->db->join('ingreso_credito ic', 'i.id_ingreso = ic.ingreso_id');
                    $this->db->where("i.tipo_ingreso='GASTO' AND tipo_documento='CRONOGRAMA DE PAGOS'");
                    if($params['local_id']>0){
                        $this->db->where('local_id = '.$params['local_id']);
                    }
                    if($params['mes'] != '' && $params['year'] != ''){
                        $this->db->where('YEAR(fecha_emision) = '.$params['year'].' AND MONTH(fecha_emision) = '.$params['mes']);
                    }
                    $suma = $this->db->get()->row_array();
                    $tipo_gastos[$a]['suma'] += $suma['subtotal'];
                }

                $totSubtotal += $tipo_gastos[$a]['suma'];
                $a++;
            }
            $grupos[$x]['nom'] = $tipo_gastos;
            $grupos[$x]['suma'] = $totSubtotal;
            $x++;
        }

        $datos['simbolo'] = $ventas->simbolo;
        $datos['ventas'] = $ventas->detalle_importe;
        $datos['costo'] = $ventas->costo_venta;
        $datos['margen_bruto'] = $datos['ventas'] - $datos['costo'];
        $datos['gastos'] = $grupos;
        //utilidad operativa = margen bruto - gasto de venta - gasto administrativo - planilla - gastos de servicio
        $datos['utilidad'] = $datos['margen_bruto'] - $grupos[0]['suma'] - $grupos[1]['suma'] - $grupos[3]['suma'] - $grupos[5]['suma'];
        //UTILIDAD ANTES DE IMPUESTOS = utilidad operativa - gasto financiero
        $datos['utilidad_si'] = $datos['utilidad'] - $grupos[2]['suma'];
        //IMPUESTO A LA RENTA  = UTILIDAD ANTES DE IMPUESTOS * 0.3
        $datos['impuesto'] = $datos['utilidad_si'] * 0.3;
        //UTILIDAD NETA = UTILIDAD ANTES DE IMPUESTOS - IMPUESTO A LA RENTA
        $datos['utilidad_neta'] = $datos['utilidad_si'] - $datos['impuesto'];
        return $datos;
    }

    function getGastosDia($params)
    {
        $where = "v.venta_status='COMPLETADO' AND ";
        if($params['local_id']>0){
            $where .= "v.local_id = ".$params['local_id'];
        }
        if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            if(!empty($where)){
                $where .= " AND ";
            }
            $where .= "v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'";
        }

        $query = "SELECT v.venta_id, DATE_FORMAT(v.fecha, '%d/%m/%Y') AS fecha, pr.proveedor_nombre, p.producto_nombre, u.nombre_unidad, SUM(up.unidades * dv.cantidad) AS cantidad, dv.detalle_costo_promedio, dv.detalle_importe, l.local_nombre, dv.detalle_costo_ultimo, dv.impuesto_porciento, v.tipo_impuesto
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
            INNER JOIN unidades u ON u.id_unidad=up2.id_unidad";
        if(!empty($where)){
            $query .= " WHERE ". $where;
        }
        $query .= " GROUP BY v.venta_id, dv.id_detalle ORDER BY v.venta_id";
        return $this->db->query($query)->result();
    }
}