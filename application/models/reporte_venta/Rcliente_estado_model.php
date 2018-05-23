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
            SUM(IF(credito.tasa_interes > 0, venta.total + ((venta.total - venta.inicial) * (credito.tasa_interes / 100)), venta.total)) as subtotal_venta,
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
                    $this->db->where("(venta.condicion_pago = 1 OR credito.var_credito_estado = 'PagoCancelado')");
                    break;
                }
                case 2: {
                    $this->db->where("(venta.condicion_pago = 2 AND credito.var_credito_estado = 'PagoPendiente')");
                    break;
                }
            }
        }

        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $this->db->where_in('cliente.id_cliente', explode(",", $params['cliente_id']));

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $this->db->where('usuario.nUsuCodigo', $params['vendedor_id']);

        if (isset($params['moneda_id']))
            $this->db->where('venta.id_moneda', $params['moneda_id']);

        if (isset($params['local_id']))
            $this->db->where('venta.local_id', $params['local_id']);

        $clientes = $this->db->get()->result();

        foreach ($clientes as $cliente) {
            $cliente->cobranzas = $this->get_cobranzas_by_cliente($cliente->cliente_id, $params);

            $this->db->select("
                SUM(credito_cuotas_abono.monto_abono) as monto,
            ")
                ->from('credito_cuotas_abono')
                ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                ->join('venta', 'venta.venta_id = credito_cuotas.id_venta')
                ->join('credito', 'credito.id_venta = venta.venta_id')
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.id_cliente', $cliente->cliente_id);

           $this->_filtro_totales($params);
            $pagado_pendientes = $this->db->get()->row();

            $cliente->subtotal_pago = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;

            $this->db->select("
                SUM(venta.total) as monto,
            ")
                ->from('venta')
                ->join('credito', 'credito.id_venta = venta.venta_id', 'LEFT')
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.condicion_pago', "1")
                ->where('venta.id_cliente', $cliente->cliente_id);

            $this->_filtro_totales($params);
            $pagado_pendientes = $this->db->get()->row();

            $cliente->subtotal_pago += isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;


            $this->db->select("
                SUM(venta.inicial) as monto,
            ")
                ->from('venta')
                ->join('credito', 'credito.id_venta = venta.venta_id')
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.condicion_pago', "2")
                ->where('venta.id_cliente', $cliente->cliente_id);

            $this->_filtro_totales($params);
            $pagado_pendientes = $this->db->get()->row();

            $cliente->subtotal_pago += isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;
        }



        return $clientes;

    }

    function _filtro_totales($params){
        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['moneda_id']))
            $this->db->where('venta.id_moneda', $params['moneda_id']);

        if (isset($params['local_id']))
            $this->db->where('venta.local_id', $params['local_id']);

        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where("(venta.condicion_pago = 1 OR credito.var_credito_estado = 'PagoCancelado')");
                    break;
                }
                case 2: {
                    $this->db->where("(venta.condicion_pago = 2 AND credito.var_credito_estado = 'PagoPendiente')");
                    break;
                }
            }
        }
    }

    function get_cobranzas_by_cliente($cliente_id, $params)
    {
        $this->db->select("
            venta.venta_id as venta_id,
            documentos.des_doc as documento_nombre,
            documentos.id_doc as documento_id,
            venta.serie as documento_serie,
            venta.numero as documento_numero,
            venta.fecha as fecha_venta,
            IF(credito.tasa_interes > 0, venta.total + ((venta.total - venta.inicial) * (credito.tasa_interes / 100)), venta.total) as total_deuda,
            IF(credito.tasa_interes > 0, (venta.total - venta.inicial) * (credito.tasa_interes / 100), 0) as total_interes,
            venta.condicion_pago as condicion_pago,
            condiciones_pago.nombre_condiciones as condicion_pago_nombre,
            credito.dec_credito_montodebito as actual,
            IFNULL(venta.inicial, 0) as inicial,
            (venta.total - credito.dec_credito_montodebito) as credito,
            credito.var_credito_estado as credito_estado,
            credito.tasa_interes as tasa_interes,
            venta.venta_status as venta_estado,
            DATEDIFF(CURDATE(), (venta.fecha)) as atraso
        ")
            ->from('venta')
            ->join('credito', 'credito.id_venta = venta.venta_id', 'LEFT')
            ->join('condiciones_pago', 'condiciones_pago.id_condiciones = venta.condicion_pago')
            ->join('documentos', 'documentos.id_doc = venta.id_documento')
            ->where('venta.venta_status', "COMPLETADO")
            ->where('venta.id_cliente', $cliente_id);

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['moneda_id']))
            $this->db->where('venta.id_moneda', $params['moneda_id']);

        if (isset($params['local_id']))
            $this->db->where('venta.local_id', $params['local_id']);

        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where("(venta.condicion_pago = 1 OR credito.var_credito_estado = 'PagoCancelado')");
                    break;
                }
                case 2: {
                    $this->db->where("(venta.condicion_pago = 2 AND credito.var_credito_estado = 'PagoPendiente')");
                    break;
                }
            }
        }


        $cobranzas = $this->db->get()->result();


        foreach ($cobranzas as $cobranza) {
            $pagado_pendientes = $this->db->select("
                SUM(credito_cuotas_abono.monto_abono) as monto,
            ")
                ->from('credito_cuotas_abono')
                ->join('metodos_pago', 'metodos_pago.id_metodo = credito_cuotas_abono.tipo_pago')
                ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                ->where('credito_cuotas.id_venta', $cobranza->venta_id)
                ->where('credito_cuotas.ispagado', 0)
                ->group_by('credito_cuotas.id_venta')
                ->get()->row();

            $cobranza->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;
            $cobranza->actual = $cobranza->actual - $cobranza->pagado_pendientes;
            $cobranza->credito = $cobranza->credito + $cobranza->pagado_pendientes;

            if ($cobranza->condicion_pago == 1) {
                $temp = new stdClass();
                $temp->letra = 'PAGO CONTADO';
                $temp->fecha = $cobranza->fecha_venta;
                $temp->monto = $cobranza->total_deuda;
                $temp->tipo_pago_nombre = '<span style="color: #e67e22;">INDEFINIDO</span>';

                $caja = $this->db->join('metodos_pago', 'metodos_pago.id_metodo = caja_movimiento.medio_pago')
                    ->get_where('caja_movimiento', array(
                        'movimiento' => 'INGRESO',
                        'operacion' => 'VENTA',
                        'ref_id' => $cobranza->venta_id
                    ))->row();

                if($caja != NULL){
                    $temp->tipo_pago_nombre = $caja->nombre_metodo;
                }


                $cobranza->detalles = array($temp);
            } else {
                $result_detalles = array();
                if ($cobranza->inicial > 0) {
                    $temp = new stdClass();
                    $temp->letra = 'PAGO INICIAL';
                    $temp->fecha = $cobranza->fecha_venta;
                    $temp->monto = $cobranza->inicial;
                    $temp->tipo_pago_nombre = '<span style="color: #e67e22;">INDEFINIDO</span>';

                    $caja = $this->db->join('metodos_pago', 'metodos_pago.id_metodo = caja_movimiento.medio_pago')
                        ->get_where('caja_movimiento', array(
                            'movimiento' => 'INGRESO',
                            'operacion' => 'VENTA',
                            'ref_id' => $cobranza->venta_id
                        ))->row();

                    if($caja != NULL){
                        $temp->tipo_pago_nombre = $caja->nombre_metodo;
                    }

                    $result_detalles = array($temp);
                }


                $detalles = $this->db->select("
                    credito_cuotas.nro_letra as letra,
                    credito_cuotas_abono.fecha_abono as fecha,
                    credito_cuotas_abono.monto_abono as monto,
                    metodos_pago.nombre_metodo as tipo_pago_nombre,
                    ")
                    ->from('credito_cuotas_abono')
                    ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                    ->join('metodos_pago', 'metodos_pago.id_metodo = credito_cuotas_abono.tipo_pago')
                    ->where('credito_cuotas.id_venta', $cobranza->venta_id)
                    ->order_by('credito_cuotas_abono.fecha_abono', 'ASC ')
                    ->get()->result();

                foreach ($detalles as $d) {
                    $result_detalles[] = $d;
                }

                $cobranza->detalles = $result_detalles;
            }


            $pagado_pendientes = $this->db->select("
                SUM(credito_cuotas_abono.monto_abono) as monto,
            ")
                ->from('credito_cuotas_abono')
                ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                ->where('credito_cuotas.id_venta', $cobranza->venta_id)
                ->where('credito_cuotas.ispagado', 0)
                ->group_by('credito_cuotas.id_venta')
                ->get()->row();

            $cobranza->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;

            $pagado_confirmado = $this->db->select("
                SUM(credito_cuotas_abono.monto_abono) as monto,
            ")
                ->from('credito_cuotas_abono')
                ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                ->where('credito_cuotas.id_venta', $cobranza->venta_id)
                ->where('credito_cuotas.ispagado', 0)
                ->group_by('credito_cuotas.id_venta')
                ->get()->row();

            $cobranza->pagado_confirmados = isset($pagado_confirmado->monto) ? $pagado_confirmado->monto : 0;
        }

        return $cobranzas;
    }

}
