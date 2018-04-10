<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rcliente_estado_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_estado_cuenta($params = array())
    {
        $this->db->select("
            cliente.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            usuario.nombre as vendedor_nombre, 
            SUM(venta.total) as subtotal_venta,
            SUM(credito.dec_credito_montodebito) as subtotal_pago
        ")
            ->from('cliente')
            ->join('venta', 'venta.id_cliente = cliente.id_cliente')
            ->join('credito', 'credito.id_venta = venta.venta_id', 'LEFT')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->where('cliente.cliente_status', 1)
            ->where('venta.venta_status', "COMPLETADO")
            ->group_by('cliente.id_cliente');

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where('credito.var_credito_estado', "PagoCancelado");
                    break;
                }
                case 2: {
                    $this->db->where('credito.var_credito_estado', "PagoPendiente");
                    break;
                }
            }
        }

        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $this->db->where('cliente.id_cliente', $params['cliente_id']);

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $this->db->where('usuario.nUsuCodigo', $params['vendedor_id']);

        $clientes = $this->db->get()->result();

        foreach ($clientes as $cliente) {
            $cliente->cobranzas = $this->get_cobranzas_by_cliente($cliente->cliente_id, $params);

            $this->db->select("
                SUM(credito_cuotas_abono.monto_abono) as monto,
            ")
                ->from('credito_cuotas_abono')
                ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                ->join('venta', 'venta.venta_id = credito_cuotas.id_venta')
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.id_cliente', $cliente->cliente_id);

            if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
                $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
                $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
            }

            if (isset($params['estado']) && $params['estado'] != 0) {
                switch ($params['estado']) {
                    case 1: {
                        $this->db->where('credito.var_credito_estado', "PagoCancelado");
                        break;
                    }
                    case 2: {
                        $this->db->where('credito.var_credito_estado', "PagoPendiente");
                        break;
                    }
                }
            }
            $pagado_pendientes = $this->db->get()->row();

            $cliente->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;
            $cliente->subtotal_pago -= $cliente->pagado_pendientes;
        }

        return $clientes;

    }

    function get_cobranzas_by_cliente($cliente_id, $params)
    {
        $this->db->select("
            venta.venta_id as venta_id,
            documentos.des_doc as documento_nombre, 
            venta.serie as documento_serie, 
            venta.numero as documento_numero, 
            venta.fecha as fecha_venta, 
            venta.total as total_deuda, 
            credito.dec_credito_montodebito as actual,
            (venta.total - credito.dec_credito_montodebito)  as credito,
            venta.venta_status as venta_estado,
            DATEDIFF(CURDATE(), (venta.fecha)) as atraso
        ")
            ->from('venta')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documentos', 'documentos.id_doc = venta.id_documento')
            ->where('venta.venta_status', "COMPLETADO")
            ->where('venta.id_cliente', $cliente_id);

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where('credito.var_credito_estado', "PagoCancelado");
                    break;
                }
                case 2: {
                    $this->db->where('credito.var_credito_estado', "PagoPendiente");
                    break;
                }
            }
        }


        $cobranzas = $this->db->get()->result();

        foreach ($cobranzas as $cobranza) {
            $pagado_pendientes = $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE')
                ->group_by('credito_id')
                ->get()->row();

            $cobranza->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;
            $cobranza->actual = $cobranza->actual - $cobranza->pagado_pendientes;
            $cobranza->credito = $cobranza->credito + $cobranza->pagado_pendientes;

            $cobranza->detalles = $this->db->select("
                historial_pagos_clientes.historial_fecha as fecha,
                historial_pagos_clientes.historial_monto as monto,
                metodos_pago.nombre_metodo as tipo_pago_nombre,
                historial_pagos_clientes.historial_estatus as estado,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'CONFIRMADO')
                ->order_by('historial_pagos_clientes.historial_fecha', 'ASC ')
                ->get()->result();

            $pagado_pendientes = $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE')
                ->group_by('credito_id')
                ->get()->row();

            $cobranza->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;

            $pagado_confirmado = $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'CONFIRMADO')
                ->group_by('credito_id')
                ->get()->row();

            $cobranza->pagado_confirmados = isset($pagado_confirmado->monto) ? $pagado_confirmado->monto : 0;
        }

        return $cobranzas;
    }

}
