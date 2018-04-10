<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rproveedor_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_estado_pago($params = array())
    {
        $this->db->select("
            proveedor.id_proveedor as proveedor_id, 
            proveedor.proveedor_nombre as proveedor_nombre,
            SUM(ingreso.total_ingreso) as subtotal_compra
        ")
            ->from('proveedor')
            ->join('ingreso', 'ingreso.int_Proveedor_id = proveedor.id_proveedor')
            ->where('ingreso.ingreso_status', 'COMPLETADO')
            ->where('proveedor.proveedor_status', 1)
            ->group_by('proveedor.id_proveedor');

        $this->aplicar_filtros($params);


        $proveedores = $this->db->get()->result();

        unset($params['proveedor_id']);

        foreach ($proveedores as $proveedor) {
            $this->db->select('SUM(pagos_ingreso.pagoingreso_monto) as monto_pagado')
                ->from('pagos_ingreso')
                ->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id = pagos_ingreso.pagoingreso_ingreso_id')
                ->join('ingreso', 'ingreso_credito_cuotas.ingreso_id = ingreso.id_ingreso')
                ->where('ingreso.int_Proveedor_id', $proveedor->proveedor_id);

            $this->aplicar_filtros($params);

            $pagado = $this->db->get()->row();

            $this->db->select('SUM(ingreso.total_ingreso) as monto_pagado')
                ->from('ingreso')
                ->where('ingreso.int_Proveedor_id', $proveedor->proveedor_id)
                ->where('ingreso.pago', 'CONTADO')
                ->where('ingreso.ingreso_status', 'COMPLETADO');

            $this->aplicar_filtros($params);


            $pagado_contado = $this->db->get()->row();
            $proveedor->subtotal_pagado = 0;
            
            if (isset($params['estado']) && ($params['estado'] == 2 || $params['estado'] == 0))
                $proveedor->subtotal_pagado += $pagado->monto_pagado != null ? $pagado->monto_pagado : 0;

            $proveedor->subtotal_pagado += $pagado_contado->monto_pagado != null ? $pagado_contado->monto_pagado : 0;

            $proveedor->pagos = $this->get_pagos_by_proveedor($proveedor->proveedor_id, $params);
        }

        return $proveedores;

    }

    function get_pagos_by_proveedor($proveedor_id, $params)
    {
        $this->db->select("
            ingreso.id_ingreso as ingreso_id,
            ingreso.tipo_documento as documento_nombre, 
            ingreso.documento_serie as documento_serie, 
            ingreso.documento_numero as documento_numero, 
            proveedor.proveedor_nombre as proveedor_nombre, 
            ingreso.fecha_emision as fecha_emision, 
            ingreso.total_ingreso as total, 
            ingreso.pago as pago
        ")
            ->from('ingreso')
            ->join('proveedor', 'ingreso.int_Proveedor_id = proveedor.id_proveedor')
            ->where('ingreso.ingreso_status', 'COMPLETADO')
            ->where('ingreso.int_Proveedor_id', $proveedor_id);

        $this->aplicar_filtros($params);

        $this->db->order_by('ingreso.fecha_emision', 'ASC');

        $pagos = $this->db->get()->result();

        foreach ($pagos as $pago) {
            $pagado = $this->db->select('SUM(pagos_ingreso.pagoingreso_monto) as monto_pagado')
                ->from('pagos_ingreso')
                ->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id = pagos_ingreso.pagoingreso_ingreso_id')
                ->where('ingreso_credito_cuotas.ingreso_id', $pago->ingreso_id)
                ->get()->row();


            if ($pago->pago == 'CREDITO') {
                $pago->pagado = $pagado->monto_pagado != null ? $pagado->monto_pagado : 0;
                $pago->cuenta_pagar = $pago->total - $pago->pagado;
            } else {
                $pago->pagado = $pago->total;
                $pago->cuenta_pagar = 0;
            }

            $pago->detalles = $this->db->select("
                pagos_ingreso.pagoingreso_fecha as fecha,
                pagos_ingreso.pagoingreso_monto as monto,
                pagos_ingreso.medio_pago_id as pago_id,
                metodos_pago.nombre_metodo as pago_nombre,
                banco.banco_nombre as banco_nombre,
                pagos_ingreso.operacion as operacion
            ")
                ->from('pagos_ingreso')
                ->join('metodos_pago', 'metodos_pago.id_metodo = pagos_ingreso.medio_pago_id', 'left')
                ->join('banco', 'banco.banco_id = pagos_ingreso.banco_id', 'left')
                ->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id = pagos_ingreso.pagoingreso_ingreso_id')
                ->where('ingreso_credito_cuotas.ingreso_id', $pago->ingreso_id)
                ->get()->result();
        }

        return $pagos;
    }

    function aplicar_filtros($params)
    {
        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('ingreso.fecha_emision >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('ingreso.fecha_emision <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['proveedor_id']) && $params['proveedor_id'] != 0)
            $this->db->where('ingreso.int_Proveedor_id', $params['proveedor_id']);

        if (isset($params['tipo_documento']) && $params['tipo_documento'] != '0')
            $this->db->where('ingreso.tipo_documento', $params['tipo_documento']);

        if (isset($params['moneda_id']))
            $this->db->where('ingreso.id_moneda', $params['moneda_id']);


        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where("ingreso.pago = 'CONTADO'");
                    break;
                }
                case 2: {
                    $this->db->where("ingreso.pago = 'CREDITO' AND (SELECT 
                                            COUNT(*)
                                        FROM
                                            ingreso_credito
                                        WHERE
                                            ingreso.id_ingreso = ingreso_id AND 
                                            estado = 'PENDIENTE') > 0");
                    
                    break;
                }
            }
        }
    }

}