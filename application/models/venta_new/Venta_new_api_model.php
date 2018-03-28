<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Venta_new_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('correlativos/correlativos_api_model');
        $this->load->model('kardex/kardex_api_model');
        $this->load->model('unidades/unidades_api_model');
        $this->load->model('traspaso/traspaso_api_model');
        $this->load->model('cajas/cajas_api_model');
        $this->load->model('cajas/cajas_mov_api_model');
    }

    function get_ventas($where = array())
    {
        $this->db->select('
            venta.venta_id as venta_id,
            venta.fecha as venta_fecha,
            venta.local_id as local_id,
            local.local_nombre as local_nombre,
            venta.id_documento as documento_id,
            documentos.des_doc as documento_nombre,
            correlativos.serie as serie_documento,
            venta.factura_impresa as factura_impresa,
            venta.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            cliente.identificacion as ruc,
            venta.id_vendedor as vendedor_id,
            usuario.nombre as vendedor_nombre,
            venta.condicion_pago as condicion_id,
            condiciones_pago.nombre_condiciones as condicion_nombre,
            venta.venta_status as venta_estado,
            venta.id_moneda as moneda_id,
            moneda.tasa_soles as moneda_tasa,
            moneda.nombre as moneda_nombre,
            moneda.simbolo as moneda_simbolo,
            venta.total as total,
            venta.inicial as inicial,
            venta.total_impuesto as impuesto,
            venta.subtotal as subtotal,
            credito.dec_credito_montodebito as credito_pagado,
            credito.dec_credito_montocuota as credito_pendiente,
            credito.var_credito_estado as credito_estado,
            venta.dni_garante as nombre_vd,
            (select SUM(detalle_venta.cantidad) from detalle_venta
            where detalle_venta.id_venta=venta.venta_id) as total_bultos
            ')

            ->from('venta')
            ->join('documentos', 'venta.id_documento=documentos.id_doc')
            ->join('condiciones_pago', 'venta.condicion_pago=condiciones_pago.id_condiciones')
            ->join('cliente', 'venta.id_cliente=cliente.id_cliente')
            ->join('usuario', 'venta.id_vendedor=usuario.nUsuCodigo')
            ->join('moneda', 'venta.id_moneda=moneda.id_moneda')
            ->join('correlativos', 'venta.id_documento=correlativos.id_documento and venta.local_id=correlativos.id_local', 'left')
            ->join('local', 'venta.local_id=local.int_local_id')
            ->join('credito', 'venta.venta_id=credito.id_venta', 'left')
            ->order_by('venta.fecha', 'desc');

        if (isset($where['venta_id'])) {
            $this->db->where('venta.venta_id', $where['venta_id']);
            return $this->db->get()->row();
        }

        if (isset($where['local_id']))
            $this->db->where('venta.local_id', $where['local_id']);

        if (isset($where['estado']))
            $this->db->where('venta.venta_status', $where['estado']);

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('venta.fecha >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min'] . " 00:00:00");
            $this->db->where('venta.fecha <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day . " 23:59:59");
        }

        return $this->db->get()->result();
    }

    function get_last_id()
    {
        $last_id = $this->db->select('venta_id')->order_by('venta_id',"desc")->limit(1)->get('venta')->row();

        return $last_id;
    }

    function get_venta_detalle($venta_id)
    {
        $venta = $this->get_ventas(array('venta_id' => $venta_id));

        $venta->venta_documentos = $this->db->get_where('venta_documento', array('venta_id' => $venta_id))->result();

        $venta->detalles = $this->db->select('
            detalle_venta.id_detalle as detalle_id,
            detalle_venta.id_producto as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            detalle_venta.precio as precio,
            detalle_venta.cantidad as cantidad,
            detalle_venta.unidad_medida as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            detalle_venta.detalle_importe as importe            ')
            ->from('detalle_venta')
            ->join('producto', 'producto.producto_id=detalle_venta.id_producto')
            ->join('unidades', 'unidades.id_unidad=detalle_venta.unidad_medida')
            ->where('detalle_venta.id_venta', $venta->venta_id)
            ->get()->result();

        return $venta;
    }

    function save_venta_contado($venta, $productos, $traspasos = array())
    {
        if (sizeof($traspasos) > 0) {
            $this->save_traspasos($traspasos, $venta['id_usuario']);
        }

        //preparo la venta
        $venta_contado = array(
            'local_id' => $venta['local_id'],
            'id_documento' => $venta['id_documento'],
            'id_cliente' => $venta['id_cliente'],
            'id_vendedor' => $venta['id_usuario'],
            'condicion_pago' => $venta['condicion_pago'],
            'id_moneda' => $venta['id_moneda'],
            'venta_status' => $venta['venta_status'],
            'fecha' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s"))),
            'factura_impresa' => 0,
            'subtotal' => $venta['subtotal'],
            'total_impuesto' => $venta['impuesto'],
            'total' => $venta['vc_total_pagar'],
            'pagado' => $venta['vc_importe'],
            'vuelto' => $venta['vc_vuelto'],
            'tasa_cambio' => $venta['tasa_cambio'],
            'dni_garante' => $venta['dni_garante'],
            'inicial' => null,
            'tipo_impuesto' => $venta['tipo_impuesto'],
            'comprobante_id' => $venta['comprobante_id'],
            'nota' => $venta['venta_nota']
        );

        if ($venta['venta_status'] == 'CAJA') {
            $venta_contado['total'] = $venta['total_importe'];
        } else {
            $correlativo = $this->correlativos_api_model->get_correlativo($venta['local_id'], $venta['id_documento']);
            $venta_contado['fecha_facturacion'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s")));
            $venta_contado['serie'] = $correlativo->serie;
            $venta_contado['numero'] = $correlativo->correlativo;

            $this->correlativos_api_model->sumar_correlativo($venta['local_id'], $venta['id_documento']);
        }

        //inserto la venta
        $this->db->insert('venta', $venta_contado);
        $venta_id = $this->db->insert_id();

        if ($venta['venta_status'] != 'CAJA') {
            $moneda_id = $venta_contado['id_moneda'];

            // Hago la facturacion de comprobantes
            if (validOption('COMPROBANTE', 1))
                $this->comprobante_api_model->facturar($venta_id, $venta['comprobante_id']);

            if ($venta['vc_forma_pago'] == 4 || $venta['vc_forma_pago'] == 8 || $venta['vc_forma_pago'] == 9) {
                $banco = $this->db->get_where('banco', array('banco_id' => $venta['vc_banco_id']))->row();
                $cuenta_id = $banco->cuenta_id;
            } else {
                $cuenta_id = $this->cajas_api_model->get_cuenta_id(array(
                    'moneda_id' => $moneda_id,
                    'local_id' => $venta_contado['local_id']));
            }

            $cuenta_old = $this->cajas_api_model->get_cuenta($cuenta_id);

            $this->cajas_api_model->update_saldo($cuenta_id, $venta_contado['total']);

            $this->cajas_mov_api_model->save_mov(array(
                'caja_desglose_id' => $cuenta_id,
                'usuario_id' => $venta['id_usuario'],
                'fecha_mov' => date('Y-m-d H:i:s'),
                'movimiento' => 'INGRESO',
                'operacion' => 'VENTA',
                'medio_pago' => $venta['vc_forma_pago'],
                'saldo' => $venta_contado['total'],
                'saldo_old' => $cuenta_old->saldo,
                'ref_id' => $venta_id,
                'ref_val' => $venta['vc_num_oper']
            ));
        }

        $this->correlativos_api_model->update_nota_pedido($venta['local_id'], $venta_id);

        $this->save_producto_detalles($venta_id, $venta['local_id'], $productos, $venta['id_usuario']);

        if ($venta['venta_status'] == 'COMPLETADO') {
            //guardo la relacion del modo de pago
            if ($venta['vc_forma_pago'] != 7) {
                $contado = array(
                    'id_venta' => $venta_id,
                    'status' => 'PagoCancelado',
                    'montopagado' => $venta['vc_total_pagar']
                );
                $this->db->insert('contado', $contado);
            } elseif ($venta['vc_forma_pago'] == 7) {
                $tarjeta = array(
                    'venta_id' => $venta_id,
                    'tarjeta_pago_id' => $venta['vc_tipo_tarjeta'],
                    'numero' => $venta['vc_num_oper']
                );
                $this->db->insert('venta_tarjeta', $tarjeta);
            }
        }

        return $venta_id;

    }

    function save_venta_credito($venta, $productos, $traspasos = array(), $cuotas)
    {
        if (sizeof($traspasos) > 0) {
            $this->save_traspasos($traspasos , $venta['id_usuario']);
        }

        if ($venta['venta_status'] == 'CAJA' && $venta['c_inicial'] == 0)
            $venta['venta_status'] = 'COMPLETADO';

        $imp = (100 + IMPUESTO) / 100;
        $venta['subtotal'] = ($venta['c_precio_credito'] + $venta['c_inicial']) / $imp;
        $venta['impuesto'] = ($venta['c_precio_credito'] + $venta['c_inicial']) - $venta['subtotal'];

        //preparo la venta
        $venta_contado = array(
            'local_id' => $venta['local_id'],
            'id_documento' => $venta['id_documento'],
            'id_cliente' => $venta['id_cliente'],
            'id_vendedor' => $venta['id_usuario'],
            'condicion_pago' => $venta['condicion_pago'],
            'id_moneda' => $venta['id_moneda'],
            'venta_status' => $venta['venta_status'],
            'fecha' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s"))),
            'factura_impresa' => 0,
            'subtotal' => $venta['subtotal'],
            'total_impuesto' => $venta['impuesto'],
            'total' => $venta['c_precio_credito'],
            'pagado' => 0,
            'vuelto' => 0,
            'tasa_cambio' => $venta['tasa_cambio'],
            'dni_garante' => null,
            'inicial' => $venta['c_inicial'],
            'tipo_impuesto' => $venta['tipo_impuesto'],
            'comprobante_id' => $venta['comprobante_id'],
            'nota' => $venta['venta_nota'],
        );


        //inserto la venta
        $this->db->insert('venta', $venta_contado);
        $venta_id = $this->db->insert_id();

        if ($venta['venta_status'] != 'CAJA' && $venta_contado['inicial'] > 0) {
            $moneda_id = $venta_contado['id_moneda'];

            if ($venta['vc_forma_pago'] == 4 || $venta['vc_forma_pago'] == 8 || $venta['vc_forma_pago'] == 9) {
                $banco = $this->db->get_where('banco', array('banco_id' => $venta['vc_banco_id']))->row();
                $cuenta_id = $banco->cuenta_id;
            } else {
                $cuenta_id = $this->cajas_api_model->get_cuenta_id(array(
                    'moneda_id' => $moneda_id,
                    'local_id' => $venta_contado['local_id']));
            }

            $cuenta_old = $this->cajas_api_model->get_cuenta($cuenta_id);

            $this->cajas_api_model->update_saldo($cuenta_id, $venta_contado['inicial']);

            $this->cajas_mov_api_model->save_mov(array(
                'caja_desglose_id' => $cuenta_id,
                'usuario_id' => $venta['id_usuario'],
                'fecha_mov' => date('Y-m-d H:i:s'),
                'movimiento' => 'INGRESO',
                'operacion' => 'VENTA',
                'medio_pago' => $venta['vc_forma_pago'],
                'saldo' => $venta_contado['inicial'],
                'saldo_old' => $cuenta_old->saldo,
                'ref_id' => $venta_id,
                'ref_val' => $venta['vc_num_oper']
            ));
        }

        $this->correlativos_api_model->update_nota_pedido($venta['local_id'], $venta_id);

        $this->save_producto_detalles($venta_id, $venta['local_id'], $productos, $venta['id_usuario']);

        $this->db->insert('credito', array(
            'id_venta' => $venta_id,
            'int_credito_nrocuota' => $venta['c_numero_cuotas'],
            'dec_credito_montocuota' => $venta['c_precio_credito'],
            'var_credito_estado' => 'PagoPendiente',
            'dec_credito_montodebito' => 0.00,
            'id_moneda' => $venta['id_moneda'],
            'tasa_cambio' => $venta['tasa_cambio'],
            'periodo_gracia' => $venta['c_periodo_gracia'],
            'tasa_interes' => $venta['c_tasa_interes']
        ));

        foreach ($cuotas as $cuota) {
            $this->db->insert('credito_cuotas', array(
                'id_venta' => $venta_id,
                'nro_letra' => $cuota->letra,
                'fecha_giro' => date('Y-m-d', strtotime(str_replace('/', '-', $venta['c_fecha_giro']))),
                'fecha_vencimiento' => date('Y-m-d', strtotime(str_replace('/', '-', $cuota->fecha))),
                'monto' => $cuota->monto,
                'ispagado' => 0,
                'isgiro' => 0
            ));
        }

        if ($venta['venta_status'] == 'COMPLETADO' && $venta_contado['inicial'] > 0) {
            //guardo la relacion del modo de pago
            if ($venta['vc_forma_pago'] != 7) {
                $contado = array(
                    'id_venta' => $venta_id,
                    'status' => 'PagoCancelado',
                    'montopagado' => $venta_contado['inicial']
                );
                $this->db->insert('contado', $contado);
            } elseif ($venta['vc_forma_pago'] == 7) {
                $tarjeta = array(
                    'venta_id' => $venta_id,
                    'tarjeta_pago_id' => $venta['vc_tipo_tarjeta'],
                    'numero' => $venta['vc_num_oper']
                );
                $this->db->insert('venta_tarjeta', $tarjeta);
            }
        }

        return $venta_id;
    }

    function save_producto_detalles($venta_id, $local_id, $productos, $id_usuario)
    {
        //Preparo los detalles de la venta para insertarlo y sus historicos
        $cantidades = array();
        $venta_detalle = array();
        $venta_contable_detalle = array();
        foreach ($productos as $producto) {
            //preparo los datos para el historico
            if (!isset($cantidades[$producto->id_producto]))
                $cantidades[$producto->id_producto] = 0;

            $cantidades[$producto->id_producto] += $this->unidades_api_model->convert_minimo_by_um(
                $producto->id_producto,
                $producto->unidad_medida,
                $producto->cantidad
            );

            $p = $this->db
                ->join('impuestos', 'impuestos.id_impuesto=producto.producto_impuesto')
                ->get_where('producto', array('producto_id' => $producto->id_producto))->row();

            //preparo el detalle de la venta
            $producto_detalle = array(
                'id_venta' => $venta_id,
                'id_producto' => $producto->id_producto,
                'precio' => $producto->precio,
                'cantidad' => $producto->cantidad,
                'unidad_medida' => $producto->unidad_medida,
                'detalle_importe' => $producto->detalle_importe,
                'detalle_costo_promedio' => 0,
                'detalle_utilidad' => 0,
                'impuesto_id' => $p->id_impuesto,
                'impuesto_porciento' => $p->porcentaje_impuesto,
                'precio_venta' => $producto->precio
            );
            array_push($venta_detalle, $producto_detalle);
        }

        //inserto los detalles de la venta
        $this->db->insert_batch('detalle_venta', $venta_detalle);

        $venta = $this->get_ventas(array('venta_id' => $venta_id));
        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $local_id,
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_api_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_api_model->get_cantidad_fraccion($key, $old_cantidad_min - $value, $local_id);

            $tipo = 0;
            if ($venta->documento_id == 1)
                $tipo = 1;
            if ($venta->documento_id == 3)
                $tipo = 3;
            if ($venta->documento_id == 6)
                $tipo = -2;

            $values = array(
                'local_id' => $local_id,
                'producto_id' => $key,
                'cantidad' => $value,
                'io' => 2,
                'tipo' => $tipo,
                'operacion' => 1,
                'serie' => '-',
                'numero' => '-',
                'ref_id' => $venta->venta_id
            );
            $this->kardex_api_model->set_kardex($values, $id_usuario);

            if ($old_cantidad != NULL) {
                //Actualizo el almacen
                $this->db->where(array(
                    'id_local' => $local_id,
                    'id_producto' => $key
                ));
                $this->db->update('producto_almacen', array(
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']
                ));
            } else {
                $this->db->insert('producto_almacen', array(
                    'id_producto' => $key,
                    'id_local' => $local_id,
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']
                ));
            }
        }
    }

    function save_traspasos($traspasos, $id_usuario)
    {
        //Hago los traspasos en caso de haber
        foreach ($traspasos as $traspaso) {
            $orden_max = $this->db->select_max('orden', 'orden')
                ->where('producto_id', $traspaso->id_producto)->get('unidades_has_producto')->row();

            $minima_unidad = $this->db->select('id_unidad as um_id')
                ->where('producto_id', $traspaso->id_producto)
                ->where('orden', $orden_max->orden)
                ->get('unidades_has_producto')->row();

            $next_id = $this->db->select_max('venta_id')->get('venta')->row();
            $this->traspaso_api_model->traspasar_productos($traspaso->id_producto, $traspaso->local_id, $traspaso->parent_local, $id_usuario, array(
                'um_id' => $minima_unidad->um_id,
                'cantidad' => $traspaso->cantidad,
                'venta_id' => $next_id->venta_id + 1
            ));
        }
    }

    function anular_venta($venta_id, $serie, $numero, $id_usuario)
    {
        $venta = $this->get_venta_detalle($venta_id);

        $cantidades = array();
        foreach ($venta->detalles as $detalle) {
            if (!isset($cantidades[$detalle->producto_id]))
                $cantidades[$detalle->producto_id] = 0;

            $cantidades[$detalle->producto_id] += $this->unidades_api_model->convert_minimo_by_um(
                $detalle->producto_id,
                $detalle->unidad_id,
                $detalle->cantidad
            );
        }

        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                'id_producto' => $key,
                'id_local' => $venta->local_id
            ))->row();

            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_api_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_api_model->get_cantidad_fraccion($key, $old_cantidad_min + $value, $venta->local_id);

            $this->db->where('io', 2);
            $this->db->where('operacion', 1);
            $this->db->where('ref_id', $venta_id);
            $referencias = $this->db->get('kardex')->row();

            if (!isset($referencias->ref_val))
                $referencias->ref_val == "";

            $values = array(
                'local_id' => $venta->local_id,
                'producto_id' => $key,
                'cantidad' => $value * -1,
                'io' => 2,
                'tipo' => 7,
                'operacion' => 5,
                'serie' => $serie,
                'numero' => $numero,
                'ref_id' => $venta->venta_id,
                'ref_val' => $referencias->ref_val
            );

            $this->kardex_api_model->set_kardex($values, $id_usuario);

            if ($old_cantidad != NULL) {
                $this->db->where('id_producto', $key);
                $this->db->where('id_local', $venta->local_id);
                $this->db->update('producto_almacen', array(
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']
                ));
            } else {
                $this->db->insert('producto_almacen', array(
                    'id_producto' => $key,
                    'id_local' => $venta->local_id,
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']
                ));
            }

            if ($venta->condicion_id == '2') {
                $this->db->where('id_venta', $venta_id);
                $this->db->delete('credito');

                $this->db->where('id_venta', $venta_id);
                $this->db->delete('credito_cuotas');
            }

            $this->db->where('venta_id', $venta_id);
            $this->db->update('venta', array('venta_status' => 'ANULADO'));

            $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

            $moneda_id = $venta->id_moneda;

            $this->cajas_api_model->save_pendiente(array(
                'monto' => $venta->total,
                'tipo' => 'VENTA_ANULADA',
                'IO' => 2,
                'ref_id' => $venta_id,
                'moneda_id' => $moneda_id,
                'local_id' => $venta->local_id
            ), $id_usuario);
        }

        return $venta_id;
    }
}
