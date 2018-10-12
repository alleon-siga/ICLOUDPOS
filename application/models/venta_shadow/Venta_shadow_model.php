<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class venta_shadow_model extends CI_Model {

    private $table = 'venta_shadow';

    function __construct() {
        parent::__construct();
        $this->load->database();

        $this->load->model('correlativos/correlativos_model');
        $this->load->model('historico/historico_model');
        $this->load->model('kardex/kardex_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('traspaso/traspaso_model');
        $this->load->model('cajas/cajas_model');
        $this->load->model('cajas/cajas_mov_model');
        $this->load->model('comprobante/comprobante_model');
        $this->load->model('producto/producto_model');
        $this->load->model('facturacion/facturacion_model');
    }

    function get_ventas($where = array()) {
        $this->db->select('
            vs.id as id,
            vs.venta_id as id_venta,
            vs.local_id as local_id,
            l.local_nombre as local_nombre,
            l.direccion as local_direccion,
            vs.id_documento as documento_id,
            d.abr_doc as documento_abr,
            d.des_doc as docuemento_nombre,
            vs.id_cliente as cliente_id,
            c.identificacion as ruc,
            c.razon_social as cliente_nombre,
            c.ruc as cliente_tipo_identificacion,
            c.direccion as cliente_direccion,
            c.telefono1 as cliente_telefono,
            vs.id_vendedor as vendedor_id,
            us.username as vendedor_nombre,
            cp.nombre_condiciones as condicion_nombre,
            vs.condicion_pago as condicion_id,
            vs.id_moneda as moneda_id,
            m.nombre as moneda_nombre,
            m.simbolo as moneda_simbolo,
            vs.venta_status as venta_estado,
            vs.fecha as venta_fecha,
            vs.fecha_facturacion as fecha_facturacion,
            vs.serie as serie,
            vs.numero as numero,
            vs.subtotal as subtotal,
            vs.total_impuesto as impuesto,
            vs.total as total,
            vs.pagado as venta_pagado,
            vs.vuelto as venta_vuelto,
            vs.tasa_cambio as moneda_tasa,
            vs.tipo_impuesto as tipo_impuesto,
            vs.nota as nota,
            vs.nro_guia as nro_guia,
            vs.created_at as venta_creado,
            vs.nota_facturada as nota_facturada_shadow,
            (select SUM(dv.cantidad) from venta_shadow_detalle as dv where dv.id_venta_shadow = vs.id) as total_bultos
            ')
                ->from('venta_shadow as vs')
                ->join('documentos d', 'd.id_doc=vs.id_documento', 'left')
                ->join('cliente c', 'c.id_cliente=vs.id_cliente', 'left')
                ->join('usuario us', 'us.nUsuCodigo=vs.id_vendedor', 'left')
                ->join('condiciones_pago cp', 'cp.id_condiciones=vs.condicion_pago', 'left')
                ->join('moneda m', 'm.id_moneda=vs.id_moneda', 'left')
                ->join('local l', 'l.int_local_id=vs.local_id', 'left')
                ->order_by('vs.fecha', 'desc');

        $this->db->where('vs.id', $where['id']);
        $venta = $this->db->get()->row();
        return $venta;
    }

    function get_ventas_totales($where = array(), $action = '') {
        $this->db->select('
            SUM(venta.total) as total,
            SUM(venta.total_impuesto) as impuesto,
            SUM(venta.subtotal) as subtotal
            ')
                ->from('venta');


        if (isset($where['venta_id'])) {
            $this->db->where('venta.venta_id', $where['venta_id']);
            return $this->db->get()->row();
        }

        if (isset($where['local_id']))
            $this->db->where('venta.local_id', $where['local_id']);

        if (isset($where['moneda_id']))
            $this->db->where('venta.id_moneda', $where['moneda_id']);

        if (isset($where['condicion_id']) && $where['condicion_id'] != "")
            $this->db->where('venta.condicion_pago', $where['condicion_id']);

        if (isset($where['id_cliente']) && $where['id_cliente'] != "")
            $this->db->where('venta.id_cliente', $where['id_cliente']);

        if (isset($where['estado']))
            if ($where['estado'] != "")
                $this->db->where('venta.venta_status', $where['estado']);
            else
                $this->db->where('venta.venta_status = "COMPLETADO"');

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('venta.fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('venta.fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('venta.fecha >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min']);
            $this->db->where('venta.fecha <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day);
        }
        if (isset($where['usuarios_id']) && !empty($where['usuarios_id'])) {
            $this->db->where('venta.id_vendedor', $where['usuarios_id']);
        }
        return $this->db->get()->row();
    }

    function get_last_id() {
        $last_id = $this->db->select('venta_id')
                        ->from('venta')
                        ->order_by('venta_id', "desc")
                        ->limit(1)
                        ->get()->row();

        return $last_id;
    }

    function get_venta_detalle($id_shadow) {
        $venta = $this->get_ventas(array('id' => $id_shadow));

        $venta->detalles = $this->db->select('
            vsd.id as id,
            vsd.id_producto as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            producto.producto_descripcion as producto_descripcion,
            producto.producto_cualidad as producto_cualidad,
            vsd.precio as precio,
            vsd.precio_venta as precio_venta,
            vsd.cantidad as cantidad,
            vsd.unidad_medida as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            vsd.detalle_importe as importe,
            vsd.afectacion_impuesto as afectacion_impuesto,
            vsd.impuesto_porciento as impuesto_porciento
            ')
                        ->from('venta_shadow_detalle as vsd')
                        ->join('producto', 'producto.producto_id=vsd.id_producto')
                        ->join('unidades', 'unidades.id_unidad=vsd.unidad_medida')
                        ->where('vsd.id_venta_shadow', $venta->id)
                        ->group_by('vsd.id')
                        ->get()->result();

        $venta->descuento = 0;
        $x = 0;
        foreach ($venta->detalles as $detalle) {
            if ($detalle->precio < $detalle->precio_venta) {
                $venta->descuento += ($detalle->precio_venta * $detalle->cantidad) - $detalle->importe;
            }
            $venta->detalles[$x]->cantidad_und = $this->unidades_model->get_cantidad_und_max($detalle->producto_id);
            $venta->detalles[$x]->simbolo_und = $this->unidades_model->get_um_min_by_producto_abr($detalle->producto_id);
            $x++;
        }
        return $venta;
    }

    //ls=lista en shadow
    function get_ventas_ls($where = array(), $action = '') {
        $this->db->select('
           venta.venta_id as venta_id,
            venta.comprobante_id as comprobante_id,
            venta.fecha as venta_fecha,
            venta.created_at as venta_creado,
            venta.pagado as venta_pagado,
            venta.vuelto as venta_vuelto,
            venta.local_id as local_id,
            local.local_nombre as local_nombre,
            local.direccion as local_direccion,
            venta.id_documento as documento_id,
            documentos.des_doc as documento_nombre,
            documentos.abr_doc as documento_abr,
            correlativos.serie as serie_documento,
            venta.factura_impresa as factura_impresa,
            venta.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            cliente.identificacion as ruc,
            cliente.ruc as cliente_tipo_identificacion,
            cliente.direccion as cliente_direccion,
            cliente.telefono1 as cliente_telefono,
            venta.id_vendedor as vendedor_id,
            usuario.username as vendedor_nombre,
            venta.condicion_pago as condicion_id,
            condiciones_pago.nombre_condiciones as condicion_nombre,
            venta.venta_status as venta_estado,
            venta.id_moneda as moneda_id,
            venta.tasa_cambio as moneda_tasa,
            moneda.nombre as moneda_nombre,
            moneda.simbolo as moneda_simbolo,
            venta.total as total,
            venta.inicial as inicial,
            venta.total_impuesto as impuesto,
            venta.subtotal as subtotal,
            credito.dec_credito_montodebito as credito_pagado,
            credito.dec_credito_montocuota as credito_pendiente,
            credito.var_credito_estado as credito_estado,
            credito.tasa_interes as tasa_interes,
            credito.periodo_gracia as periodo_gracia,
            venta.serie as serie,
            venta.numero as numero,
            venta.fecha_facturacion as fecha_facturacion,
            venta.nota as nota,
            venta.dni_garante as nombre_caja,
            venta.tipo_impuesto as tipo_impuesto,
            cliente.tipo_cliente as tipo_cliente,
            venta.dni_garante as nombre_vd,
            CASE WHEN venta_shadow.venta_id > 0 THEN "SI" ELSE
            CASE WHEN IsNULL(venta_shadow.venta_id) THEN "NO" END  END as comprobante_shadow ,
	    CASE WHEN COUNT(venta_shadow.venta_id) > 0 THEN  COUNT(venta_shadow.venta_id) ELSE
	    CASE WHEN COUNT(venta_shadow.venta_id) = 0 THEN "" END END as convertidos,
            venta_shadow.serie as vs_serie,
            (select SUM(detalle_venta.cantidad) from detalle_venta
            where detalle_venta.id_venta=venta.venta_id) as total_bultos
            ', FALSE)
                ->from('venta')
                ->join('documentos', 'venta.id_documento=documentos.id_doc')
                ->join('condiciones_pago', 'venta.condicion_pago=condiciones_pago.id_condiciones')
                ->join('cliente', 'venta.id_cliente=cliente.id_cliente')
                ->join('usuario', 'venta.id_vendedor=usuario.nUsuCodigo')
                ->join('moneda', 'venta.id_moneda=moneda.id_moneda')
                ->join('venta_shadow', 'venta.venta_id=venta_shadow.venta_id', 'left')
                ->join('correlativos', 'venta.id_documento=correlativos.id_documento and venta.local_id=correlativos.id_local', 'left')
                ->join('local', 'venta.local_id=local.int_local_id')
                ->join('credito', 'venta.venta_id=credito.id_venta', 'left')
                ->where('venta.venta_status!="ANULADO"')
                ->group_by('venta.venta_id');

        if (isset($where['venta_id'])) {
            $this->db->where('venta.venta_id', $where['venta_id']);
            $venta = $this->db->get()->row();
            $venta->comprobante_nombre = '';
            $venta->comprobante = '';
            if ($venta->comprobante_id > 0) {
                $comprobante = $this->db->join('comprobante_ventas', 'comprobante_ventas.comprobante_id = comprobantes.id')
                                ->get_where('comprobantes', array(
                                    'comprobante_id' => $venta->comprobante_id,
                                    'venta_id' => $venta->venta_id
                                ))->row();
                if ($comprobante != NULL) {
                    $venta->comprobante_nombre = $comprobante->nombre;
                    $venta->fecha_venc = $comprobante->fecha_venc;
                    $venta->comprobante = $comprobante->serie . sumCod($comprobante->numero, $comprobante->longitud);
                }
            }
            return $venta;
        }

        if (isset($where['local_id']))
            $this->db->where('venta.local_id', $where['local_id']);

        if (isset($where['moneda_id']))
            $this->db->where('venta.id_moneda', $where['moneda_id']);

        if (isset($where['condicion_id']) && $where['condicion_id'] != "")
            $this->db->where('venta.condicion_pago', $where['condicion_id']);

        if (isset($where['id_cliente']) && $where['id_cliente'] != "")
            $this->db->where('venta.id_cliente', $where['id_cliente']);

        if (isset($where['estado']))
            if ($action == '')
                $this->db->where('(venta.venta_status = "COMPLETADO" OR venta.venta_status = "ANULADO")');
            else if ($action == 'anular')
                $this->db->where('venta.venta_status = "COMPLETADO"');
            else if ($where['estado'] != "")
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

        if (isset($where['usuarios_id']) && !empty($where['usuarios_id'])) {
            $this->db->where('venta.id_vendedor', $where['usuarios_id']);
        }
        if (isset($where['estado_fac']) && $where['estado_fac']=="") {
            
        }elseif(!empty($where['estado_fac']==0)){
            $this->db->where('venta_shadow.serie =""');
        } elseif (!empty($where['estado_fac']==1)) {
            $this->db->where('venta_shadow.serie >"0"');
        }
        if (isset($where['id_documento']) && !empty($where['id_documento'])) {
            $this->db->where('venta.id_documento', $where['id_documento']);
        }

        $ventas = $this->db->get()->result();

        return $ventas;
    }

    function get_venta_traspaso($id) {
        $this->db->select('c.serie, v.venta_id, v.fecha, cl.tipo_cliente, cl.razon_social, us.username, cl.identificacion, t.id');
        $this->db->from('traspaso t');
        $this->db->join('venta v', 't.ref_id = v.venta_id');
        $this->db->join('cliente cl', 'v.id_cliente = cl.id_cliente');
        $this->db->join('usuario us', 'v.id_vendedor=us.nUsuCodigo');
        $this->db->join('correlativos c', 'v.id_documento=c.id_documento and v.local_id=c.id_local', 'left');
        $this->db->where('t.id', $id);
        return $this->db->get()->row();
    }

    function get_venta_detalle_traspaso($id, $local_origen) {
        $this->db->select('l.local_nombre, k.ref_val, p.producto_nombre, cantidad, nombre_unidad');
        $this->db->from('traspaso_detalle d');
        $this->db->join('kardex k', 'd.kardex_id = k.id');
        $this->db->join('producto p', 'k.producto_id = p.producto_id');
        $this->db->join('unidades u', 'k.unidad_id = u.id_unidad');
        $this->db->join('local l', 'k.local_id = l.int_local_id');
        $this->db->where('d.traspaso_id', $id);
        $this->db->where('d.local_origen', $local_origen);
        return $this->db->get()->result();
    }

    function get_traspaso_local($id) {
        $this->db->select('local_origen');
        $this->db->distinct();
        $this->db->from('traspaso_detalle');
        $this->db->where('traspaso_id', $id);
        $result = $this->db->get()->result();
        return $result;
    }

    function get_venta_facturar($venta_id) {
        $venta = $this->get_ventas(array('venta_id' => $venta_id));

        $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, $venta->documento_id);
        $venta->next_correlativo = $correlativo->serie . ' - ' . sumCod($correlativo->correlativo, 6);

        $venta->comprobante = 0;
        $venta->comprobante_nombre = '';
        if ($venta->comprobante_id > 0) {
            $comprobante = $this->comprobante_model->get_comprobantes($venta->comprobante_id);
            $cv = $this->db
                    ->order_by('id', 'desc')
                    ->get_where('comprobante_ventas', array(
                        'comprobante_id' => $comprobante->id))
                    ->row();

            if ($cv == NULL) {
                $next_comprobante = $comprobante->desde;
            } else {
                $next_comprobante = $cv->numero + 1;
            }

            $venta->comprobante = $comprobante->serie . sumCod($next_comprobante, $comprobante->longitud);
            $venta->comprobante_nombre = $comprobante->nombre;
        }

        return $venta;
    }

    function getDocumentoNumero() {
        $id_doc = $this->input->post('iddoc');
        $local_id = $this->input->post('local_id');

        $correlativo = $this->correlativos_model->get_correlativo($local_id, $id_doc);
        return $correlativo->serie . ' - ' . sumCod($correlativo->correlativo, 6);
    }

    function facturar_venta($id) {
        $venta = $this->db->get_where('venta_shadow', array('id' => $id))->row();

        $iddoc = $venta->id_documento;
        $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, $iddoc);
        $update_venta['fecha_facturacion'] = $venta->fecha;
        $update_venta['serie'] = $correlativo->serie;
        $update_venta['numero'] = $correlativo->correlativo;


        $this->correlativos_model->sumar_correlativo($venta->local_id, $iddoc);

        $this->db->where('id', $id);
        $this->db->update('venta_shadow', $update_venta);


        //TODO hacer el registro de kardex

        if (valueOptionDB('FACTURACION', 0) == 1) {
            $resp = $this->facturacion_model->facturarVenta_shadow($id);
        }
    }

    function save_venta_contado($venta, $productos) {
        $cuenta_id = $this->cajas_model->get_cuenta_id(array(
            'moneda_id' => $venta['id_moneda'],
            'local_id' => $venta['local_id']));

        if ($cuenta_id == NULL) {
            $this->error = 'No existe una cuenta para este local';
            return false;
        }

        //preparo la venta
        $venta_contado = array(
            'venta_id' => $venta['venta_id'],
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
            'dni_garante' => null,
            'inicial' => null,
            'tipo_impuesto' => $venta['tipo_impuesto'],
            'comprobante_id' => $venta['comprobante_id'],
            'nota' => $venta['venta_nota'],
            'dni_garante' => $venta['dni_garante']
        );

        //inserto la venta
        $this->db->insert('venta_shadow', $venta_contado);
        $id_venta_shadow = $this->db->insert_id();

        $this->save_producto_detalles($id_venta_shadow, $venta['id_documento'], $venta['local_id'], $productos, $venta['id_usuario']);

        $this->recalc_totales($id_venta_shadow);
        return $id_venta_shadow;
    }

    function cerrar_venta($venta_id, $correlativos = array()) {
        $venta = $this->get_ventas(array('venta_id' => $venta_id));
        $corr = $this->correlativos_model->get_correlativo($venta->local_id, $venta->documento_id);
        $next_correlativo = 1;
        $referencias = "";
        $doc = 'FA';
        if ($venta->documento_id == 3)
            $doc = "BO";
        $count = 0;
        foreach ($correlativos as $correlativo) {
            $this->db->insert('venta_documento', array(
                'venta_id' => $venta_id,
                'numero_documento' => $correlativo
            ));
            $next_correlativo = $correlativo;

            $referencias .= $doc . " " . $corr->serie . "-" . sumCod($correlativo, 6);
            if (++$count != count($correlativos))
                $referencias .= ", ";
        }
        $this->correlativos_model->update_correlativo($venta->local_id, $venta->documento_id, array(
            'correlativo' => ++$next_correlativo
        ));

        $this->db->where('venta_id', $venta_id);
        $this->db->update('venta', array(
            'factura_impresa' => '2',
            'venta_status' => 'CERRADA'
        ));

        $this->db->where('io', 2);
        $this->db->where('operacion', 1);
        $this->db->where('ref_id', $venta_id);
        $this->db->update('kardex', array(
            'serie' => $corr->serie,
            'numero' => sumCod($correlativo, 6),
            'ref_val' => $referencias
        ));
    }

    private
            function save_producto_detalles($id_venta_shadow, $doc_id, $local_id, $productos, $id_usuario) {
        //Preparo los detalles de la venta para insertarlo y sus historicos
        $venta = $this->get_ventas(array('id' => $id_venta_shadow));
        $cantidades = array();
        $venta_detalle = array();
        $venta_contable_detalle = array();
        $precio = array(); //precio unitario de venta
        $ArrfectImp = array(); //Afectacion de impuesto
        $impPorciento = array(); //Impuesto porciento
        foreach ($productos as $producto) {

            //preparo los datos para el historico
            if (!isset($cantidades[$producto->id_producto]))
                $cantidades[$producto->id_producto] = 0;

            $cantidades[$producto->id_producto] += $this->unidades_model->convert_minimo_by_um(
                    $producto->id_producto, $producto->unidad_medida, $producto->cantidad
            );

            $p = $this->db
                            ->join('impuestos', 'impuestos.id_impuesto=producto.producto_impuesto')
                            ->get_where('producto', array('producto_id' => $producto->id_producto))->row();

            $costo_u = $this->db->get_where('producto_costo_unitario', array(
                        'producto_id' => $producto->id_producto,
                        'moneda_id' => $venta->moneda_id
                    ))->row();
            $prod = $this->db->get_where('producto', array('producto_id' => $producto->id_producto))->row();

            //preparo el detalle de la venta
            $producto_detalle = array(
                'id_venta_shadow' => $id_venta_shadow,
                'id_producto' => $producto->id_producto,
                'precio' => $producto->precio,
                'cantidad' => $producto->cantidad,
                'unidad_medida' => $producto->unidad_medida,
                'detalle_importe' => $producto->detalle_importe,
                'detalle_costo_promedio' => $producto->aplishadow == 0 ? empty($producto->aplishadowitem) ? $producto->real_costo : $producto->contable_costo : $producto->contable_costo,
                'detalle_costo_ultimo' => $producto->aplishadow == 0 ? empty($producto->aplishadowitem) ? $producto->real_costo : $producto->contable_costo : $producto->contable_costo,
                'detalle_utilidad' => $producto->aplishadow == 0 ? empty($producto->aplishadowitem) ? $producto->precio - $producto->real_costo : $producto->precio - $producto->contable_costo : $producto->precio - $producto->contable_costo,
                'impuesto_id' => $p->id_impuesto,
                'afectacion_impuesto' => $prod->producto_afectacion_impuesto,
                'impuesto_porciento' => $p->porcentaje_impuesto,
                'precio_venta' => $producto->precio_venta,
                'tipo_impuesto_compra' => $costo_u->tipo_impuesto_compra
            );
            array_push($venta_detalle, $producto_detalle);

            $precio[$producto->id_producto] = $this->unidades_model->get_maximo_costo($producto->id_producto, $producto->unidad_medida, $producto->precio);
            $ArrfectImp[$producto->id_producto] = $prod->producto_afectacion_impuesto;
            $impPorciento[$producto->id_producto] = $p->porcentaje_impuesto;
        }

        //inserto los detalles de la venta
        $this->db->insert_batch('venta_shadow_detalle', $venta_detalle);
    }

    function save_producto_detalles_contable($id_venta_shadow, $doc_id, $local_id, $productos, $id_usuario) {
        //Preparo los detalles de la venta para insertarlo y sus historicos
        $venta = $this->get_ventas(array('id' => $id_venta_shadow));
        $cantidades = array();
        $venta_detalle = array();
        $venta_contable_detalle = array();
        $precio = array(); //precio unitario de venta
        $ArrfectImp = array(); //Afectacion de impuesto
        $impPorciento = array(); //Impuesto porciento
        foreach ($productos as $producto) {

            //preparo los datos para el historico
            if (!isset($cantidades[$producto->id_producto]))
                $cantidades[$producto->id_producto] = 0;

            $cantidades[$producto->id_producto] += $this->unidades_model->convert_minimo_by_um(
                    $producto->id_producto, $producto->unidad_medida, $producto->cantidad
            );

            $p = $this->db
                            ->join('impuestos', 'impuestos.id_impuesto=producto.producto_impuesto')
                            ->get_where('producto', array('producto_id' => $producto->id_producto))->row();

            $costo_u = $this->db->get_where('producto_costo_unitario', array(
                        'producto_id' => $producto->id_producto,
                        'moneda_id' => $venta->moneda_id
                    ))->row();
            $prod = $this->db->get_where('producto', array('producto_id' => $producto->id_producto))->row();

            //preparo el detalle de la venta
            $producto_detalle = array(
                'id_venta_shadow' => $id_venta_shadow,
                'id_producto' => $producto->id_producto,
                'precio' => $producto->precio,
                'cantidad' => $producto->cantidad,
                'unidad_medida' => $producto->unidad_medida,
                'detalle_importe' => $producto->detalle_importe,
                'detalle_costo_promedio' => $this->producto_model->get_costo_promedio($producto->id_producto, $producto->unidad_medida),
                'detalle_costo_ultimo' => $costo_u != NULL ? $costo_u->contable_costo : 0,
                'detalle_utilidad' => 0,
                'impuesto_id' => $p->id_impuesto,
                'afectacion_impuesto' => $prod->producto_afectacion_impuesto,
                'impuesto_porciento' => $p->porcentaje_impuesto,
                'precio_venta' => $producto->precio_venta,
                'tipo_impuesto_compra' => $costo_u->tipo_impuesto_compra
            );
            array_push($venta_detalle, $producto_detalle);

            $precio[$producto->id_producto] = $this->unidades_model->get_maximo_costo($producto->id_producto, $producto->unidad_medida, $producto->precio);
            $ArrfectImp[$producto->id_producto] = $prod->producto_afectacion_impuesto;
            $impPorciento[$producto->id_producto] = $p->porcentaje_impuesto;
        }

        //inserto los detalles de la venta
        $this->db->insert_batch('venta_shadow_detalle', $venta_detalle);
    }

    public
            function get_next_id() {
        $next_id = $this->db->select_max('venta_id')->get('venta')->row();
        return sumCod($next_id->venta_id + 1, 6);
    }

    public
            function anular_venta($venta_id, $serie, $numero, $metodo_pago, $cuenta_id, $motivo, $id_usuario = false) {
        $venta = $this->get_venta_detalle($venta_id);
        $cantidades = array();
        $afectacion_impuesto = array();
        $precio = array();
        $impuesto_porciento = array();
        foreach ($venta->detalles as $detalle) {

            if (!isset($cantidades[$detalle->producto_id]))
                $cantidades[$detalle->producto_id] = 0;


            $cantidades[$detalle->producto_id] += $this->unidades_model->convert_minimo_by_um(
                    $detalle->producto_id, $detalle->unidad_id, $detalle->cantidad
            );
            $afectacion_impuesto[$detalle->producto_id] = $detalle->afectacion_impuesto;
            $precio[$detalle->producto_id] = $this->unidades_model->get_maximo_costo($detalle->producto_id, $detalle->unidad_id, $detalle->precio);
            $impuesto_porciento[$detalle->producto_id] = $detalle->impuesto_porciento;
        }
        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                        'id_producto' => $key,
                        'id_local' => $venta->local_id
                    ))->row();

            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min + $value);

            $this->db->where('io', 2);
            $this->db->where('operacion', 1);
            $this->db->where('ref_id', $venta_id);
            $referencias = $this->db->get('kardex')->row();

            if (!isset($referencias->ref_val))
                $referencias->ref_val == "";

            $costo = 0;
            if ($afectacion_impuesto[$key] == '1') {
                if ($venta->tipo_impuesto == 1) { //incluye impuesto
                    $costo = $precio[$key] / (($impuesto_porciento[$key] / 100) + 1);
                } else { //agrega impuesto
                    $costo = $precio[$key];
                }
            } else {
                $costo = $precio[$key];
            }

            if ($venta->moneda_tasa > 0) {
                $costo = $costo * $venta->moneda_tasa;
            }

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
                'ref_val' => $referencias->ref_val,
                'usuario_id' => $id_usuario == false ? $this->session->userdata('nUsuCodigo') : $id_usuario,
                'costo' => $costo,
                'moneda_id' => $venta->moneda_id
            );
            $this->kardex_model->set_kardex($values);

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
        }

        $venta_status = $venta->venta_estado;

        $this->db->where('venta_id', $venta_id);
        $this->db->update('venta', array(
            'venta_status' => 'ANULADO'
        ));

        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

        $total = $venta->total;
        if ($venta->condicion_pago == 2) {
            $total = $venta->inicial > 0 ? $venta->inicial : 0;

            $cobranzas = $this->db->select_sum('credito_cuotas_abono.monto_abono', 'total')
                            ->from('credito_cuotas_abono')
                            ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                            ->where('credito_cuotas.id_venta', $venta->venta_id)
                            ->get()->row();

            $total += $cobranzas->total;
        }


        if ($total > 0 && $venta_status != 'CAJA') {
            $caja_desglose = array(
                'monto' => $total,
                'tipo' => 'VENTA_ANULADA',
                'IO' => 2,
                'ref_id' => $venta_id,
                'moneda_id' => $venta->id_moneda,
                'local_id' => $venta->local_id,
                'id_usuario' => $id_usuario == false ? $this->session->userdata('nUsuCodigo') : $id_usuario,
                'ref_val' => $metodo_pago
            );

            $caja_desglose['cuenta_id'] = $cuenta_id;

            $this->cajas_model->save_pendiente($caja_desglose);
        }

        if (valueOptionDB('FACTURACION', 0) == 1 && ($venta->id_documento == 1 || $venta->id_documento == 3) && $venta->numero != null) {
            $facturacion = $this->db->get_where('facturacion', array(
                        'ref_id' => $venta_id,
                        'documento_tipo' => sumCod($venta->id_documento, 2)
                    ))->row();

            if ($facturacion != null) {
                if ($facturacion->estado == 3 || $facturacion->estado == 2) {
                    $resp = $this->facturacion_model->anularVenta($venta_id, $serie . '-' . $numero, $motivo);
                } else {
                    $this->db->where('id', $facturacion->id);
                    $this->db->delete('facturacion');
                }
            }
        }

        if ($venta->id_documento == '1') {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 9);
        } elseif ($venta->id_documento == '3') {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 8);
        } elseif ($venta->id_documento == '6') {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 2);
        }


        return $venta_id;
    }

    private function recalc_totales($venta_id) {
        $venta = $this->db->get_where('venta_shadow', array('id' => $venta_id))->row();
        $detalles = $this->db->get_where('venta_shadow_detalle', array('id_venta_shadow' => $venta_id))->result();

        $impuesto = 0;
        $subtotal = 0;
        $total = 0;
        foreach ($detalles as $d) {
            $total += $d->cantidad * $d->precio;
        }

        if ($venta->tipo_impuesto == 1) {
            foreach ($detalles as $d) {
                if ($d->afectacion_impuesto == OP_GRAVABLE) {
                    $factor = (100 + $d->impuesto_porciento) / 100;
                    $impuesto += ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                }
            }
            $subtotal = $total - $impuesto;
        } elseif ($venta->tipo_impuesto == 2) {
            $subtotal = $total;
            foreach ($detalles as $d) {
                if ($d->afectacion_impuesto == OP_GRAVABLE) {
                    $factor = (100 + $d->impuesto_porciento) / 100;
                    $impuesto += (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }
            $total = $subtotal + $impuesto;
        } else {
            $subtotal = $total;
        }

        $this->db->where('id', $venta_id);
        $this->db->update('venta_shadow', array(
            'total' => $total,
            'subtotal' => $subtotal,
            'total_impuesto' => $impuesto,
        ));

        return $total;
    }

    public
            function devolver_venta($venta_id, $total_importe, $devoluciones, $serie, $numero, $metodo_pago, $cuenta_id, $motivo, $id_usuario = false) {
        $venta = $this->get_venta_detalle($venta_id);

        $cantidades = array();
        $afectacion_impuesto = array();
        $precio = array();
        $impuesto_porciento = array();
        foreach ($devoluciones as $detalle) {

            if (!isset($cantidades[$detalle->producto_id]))
                $cantidades[$detalle->producto_id] = 0;

            $cantidades[$detalle->producto_id] += $this->unidades_model->convert_minimo_by_um(
                    $detalle->producto_id, $detalle->unidad_id, $detalle->devolver
            );
            $precio[$detalle->producto_id] = $this->unidades_model->get_maximo_costo($detalle->producto_id, $detalle->unidad_id, $detalle->precio);
            $detalle_temp = $this->db->get_where('detalle_venta', array('id_detalle' => $detalle->detalle_id))->row();
            $detalle->impuesto_porciento = $detalle_temp->impuesto_porciento;
            $impuesto_porciento[$detalle->producto_id] = $detalle->impuesto_porciento;
            $afectacion_impuesto[$detalle->producto_id] = $detalle_temp->afectacion_impuesto;

            if ($detalle->new_cantidad == 0) {
                $this->db->where('id_detalle', $detalle->detalle_id);
                $this->db->delete('detalle_venta');
            } else {
                $this->db->where('id_detalle', $detalle->detalle_id);
                $this->db->update('detalle_venta', array(
                    'cantidad' => $detalle->new_cantidad,
                    'detalle_importe' => $detalle->new_importe
                ));
            }
            //Guardando en tabla venta_devolucion
            /* $this->db->insert('venta_devolucion', array(
              'id_venta' => $venta_id,
              'id_producto' => $detalle->producto_id,
              'precio' => $detalle->precio,
              'cantidad' => $detalle->devolver,
              'unidad_medida' => $detalle->unidad_id,
              'detalle_importe' => $detalle->devolver * $detalle->precio,
              'serie' => $serie,
              'numero' => $numero
              )); */
        }

        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                        'id_producto' => $key,
                        'id_local' => $venta->local_id
                    ))->row();

            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min + $value);

            /* $this->historico_model->set_historico(array(
              'producto_id' => $key,
              'local_id' => $venta->local_id,
              'cantidad' => $value,
              'cantidad_actual' => $this->unidades_model->convert_minimo_um($key, $result['cantidad'], $result['fraccion']),
              'tipo_movimiento' => "DEVOLUCION",
              'tipo_operacion' => 'ENTRADA',
              'referencia_valor' => 'Devolucion de Ventas',
              'referencia_id' => $venta_id
              )); */

            $this->db->where('io', 2);
            $this->db->where('operacion', 1);
            $this->db->where('ref_id', $venta_id);
            $referencias = $this->db->get('kardex')->row();

            if (!isset($referencias->ref_val))
                $referencias->ref_val == "";

            $costo = 0;
            if ($afectacion_impuesto[$key] == '1') {
                if ($venta->tipo_impuesto == 1) { //incluye impuesto
                    $costo = $precio[$key] / (($impuesto_porciento[$key] / 100) + 1);
                } else { //agrega impuesto
                    $costo = $precio[$key];
                }
            } else {
                $costo = $precio[$key];
            }

            if ($venta->moneda_tasa > 0) {
                $costo = $costo * $venta->moneda_tasa;
            }

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
                'ref_val' => $referencias->ref_val,
                'usuario_id' => $id_usuario == false ? $this->session->userdata('nUsuCodigo') : $id_usuario,
                'costo' => $costo,
                'moneda_id' => $venta->moneda_id
            );
            $this->kardex_model->set_kardex($values);

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
        }

        $this->cajas_model->save_pendiente(array(
            'monto' => $venta->total - $this->recalc_totales($venta->venta_id),
            'tipo' => 'VENTA_DEVUELTA',
            'cuenta_id' => $cuenta_id,
            'IO' => 2,
            'ref_id' => $venta_id,
            'moneda_id' => $venta->moneda_id,
            'local_id' => $venta->local_id,
            'ref_val' => $metodo_pago,
            'id_usuario' => $id_usuario == false ? $this->session->userdata('nUsuCodigo') : $id_usuario
        ));

        if (valueOptionDB('FACTURACION', 0) == 1 && ($venta->documento_id == 1 || $venta->documento_id == 3) && $venta->numero != null) {
            $facturacion = $this->db->get_where('facturacion', array(
                        'ref_id' => $venta_id,
                        'documento_tipo' => sumCod($venta->documento_id, 2)
                    ))->row();

            if ($facturacion != null) {
                if ($facturacion->estado == 3 || $facturacion->estado == 2) {
                    $resp = $this->facturacion_model->devolverVenta($venta_id, $devoluciones, $serie . '-' . $numero, $motivo);
                } else {
                    $this->db->where('id', $facturacion->id);
                    $this->db->delete('facturacion');
                }
            }
        }

        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        header('Content-Type: application/json');

        if ($venta->id_documento == '1') {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 9);
        } elseif ($venta->id_documento == '3') {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 8);
        } elseif ($venta->id_documento == '6') {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 2);
        }
    }

    public
            function imprimir_pedido($data) {
        $this->load->library('mpdf53/mpdf');

        $mpdf = new mPDF('utf-8', array('225', '93'));
        $mpdf->SetTopMargin('0');


        $mpdf->SetJS('this.print();');

        $data['section'] = 'body';
        $mpdf->WriteHTML($this->load->view('menu/venta/impresiones/nota_pedido', $data, true));

        $nombre_archivo = utf8_decode('Nota de Venta.pdf');
        $mpdf->Output($nombre_archivo, 'I');
    }

    public
            function imprimir_boleta($data) {
        $this->load->library('mpdf53/mpdf');

        $mpdf = new mPDF('utf-8', array('225', '209'));

        $mpdf->SetTopMargin('32');

        $data['section'] = 'header';
        $mpdf->SetHTMLHeader($this->load->view('menu/venta/impresiones/boleta', $data, true));

        $data['section'] = 'footer';
        $mpdf->SetHTMLFooter($this->load->view('menu/venta/impresiones/boleta', $data, true));

        $mpdf->SetJS('this.print();');

        $data['section'] = 'body';
        $mpdf->WriteHTML($this->load->view('menu/venta/impresiones/boleta', $data, true));

        $nombre_archivo = utf8_decode('Boleta.pdf');
        $mpdf->Output($nombre_archivo, 'I');
    }

    public
            function imprimir_factura($data) {
        $this->load->library('mpdf53/mpdf');

        $mpdf = new mPDF('utf-8', array('225', '93'));

        $mpdf->SetTopMargin('32');

        $data['section'] = 'header';
        $mpdf->SetHTMLHeader($this->load->view('menu/venta/impresiones/factura', $data, true));

        $data['section'] = 'footer';
        $mpdf->SetHTMLFooter($this->load->view('menu/venta/impresiones/factura', $data, true));

        $mpdf->SetJS('this.print();');

        $data['section'] = 'body';
        $mpdf->WriteHTML($this->load->view('menu/venta/impresiones/factura', $data, true));

        $nombre_archivo = utf8_decode('Factura.pdf');
        $mpdf->Output($nombre_archivo, 'I');
    }

    public function ultimasVentas($venta) {
        $this->db->select('date(v.fecha) AS fecha, dv.precio, dv.cantidad, u.nombre_unidad, venta_id, m.simbolo');
        $this->db->from('detalle_venta dv');
        $this->db->join('venta v', 'v.venta_id=dv.id_venta');
        $this->db->join('unidades u', 'dv.unidad_medida=u.id_unidad');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        $this->db->where('id_producto', $venta['id_producto']);
        $this->db->where('id_cliente', $venta['id_cliente']);
        $this->db->order_by('v.fecha DESC');
        return $this->db->get()->result();
    }

    public function ultimasCompras($id) {
        $this->db->select('DATE(i.fecha_registro) AS fecha,pcu.costo AS precio,d.cantidad, m.simbolo, d.id_producto, d.unidad_medida');
        $this->db->from('detalleingreso d');
        $this->db->join('ingreso i', 'd.id_ingreso=i.id_ingreso');
        $this->db->join('moneda m', 'i.id_moneda = m.id_moneda');
        $this->db->join('producto_costo_unitario pcu', 'd.id_producto = pcu.producto_id AND i.id_moneda = pcu.moneda_id');
        $this->db->where('id_producto', $id['id_producto']);
        $this->db->order_by('i.fecha_registro DESC');
        $this->db->limit(10);
        $datos = $this->db->get()->result();
        $x = 0;
        foreach ($datos as $dato) {
            $cantidad = $this->unidades_model->convert_minimo_by_um($datos[$x]->id_producto, $datos[$x]->unidad_medida, $datos[$x]->cantidad);
            $datos[$x]->cantidad = $cantidad;
            $datos[$x]->nombre_unidad = $this->unidades_model->get_um_min_by_producto($datos[$x]->id_producto);
            $x++;
        }
        return $datos;
    }

    function prepare_venta($id) {

        $venta = $this->get_ventas(array('venta_id' => $id));

        $venta->detalles = $this->db->select('
            dv.id_detalle as detalle_id,
            v.local_id AS local_id,
            local.local_nombre AS local_nombre,
            dv.id_producto as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            dv.precio as precio,
            dv.precio_venta as precio_venta,
            dv.cantidad as cantidad,
            (SELECT cantidad FROM producto_almacen WHERE id_producto = dv.id_producto AND id_local = v.local_id LIMIT 1) AS cantidad_almacen,
            (SELECT fraccion FROM producto_almacen WHERE id_producto = dv.id_producto AND id_local = v.local_id LIMIT 1) AS fraccion_almacen,
            dv.unidad_medida as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            SUM(dv.precio * dv.cantidad) as importe,
            dv.impuesto_porciento as impuesto,
            IFNULL(dv.afectacion_impuesto, 0) as afectacion_impuesto,
            dv.precio_venta as precio_venta,
            pcu.contable_costo
            ')
                        ->from('detalle_venta as dv')
                        ->join('venta as v', 'dv.id_venta = v.venta_id')
                        ->join('local', 'local.int_local_id=v.local_id')
                        ->join('producto', 'producto.producto_id=dv.id_producto')
                        ->join('unidades', 'unidades.id_unidad=dv.unidad_medida')
                        ->join('producto_costo_unitario pcu', 'dv.id_producto = pcu.producto_id AND v.id_moneda = pcu.moneda_id')
                        ->where('dv.id_venta', $venta->venta_id)
                        ->group_by('dv.id_detalle')
                        ->get()->result();

        $result = array();

        foreach ($venta->detalles as $detalle) {

            if (!isset($result[$detalle->producto_id])) {
                $result[$detalle->producto_id] = new stdClass();
                $result[$detalle->producto_id]->producto_nombre = $detalle->producto_nombre;
                $result[$detalle->producto_id]->producto_id = $detalle->producto_id;
                $result[$detalle->producto_id]->impuesto = $detalle->impuesto;
                $result[$detalle->producto_id]->afectacion_impuesto = $detalle->afectacion_impuesto;
                $result[$detalle->producto_id]->precio = $detalle->precio;
                $result[$detalle->producto_id]->precio_venta = $detalle->precio_venta;
                $result[$detalle->producto_id]->um_min = $this->unidades_model->get_um_min_by_producto($detalle->producto_id);
                $result[$detalle->producto_id]->um_min_abr = $this->unidades_model->get_um_min_by_producto_abr($detalle->producto_id);
                $result[$detalle->producto_id]->total_min = 0;
                $result[$detalle->producto_id]->unidades = array();
                $unidades = $this->unidades_model->get_unidades_precios($detalle->producto_id, 3);
                foreach ($unidades as $unidad) {
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad] = new stdClass();
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidad_id = $unidad->id_unidad;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidad_nombre = $unidad->nombre_unidad;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidad_abr = $unidad->abr;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->cantidad = 0;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidades = $unidad->unidades;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->orden = $unidad->orden;
                }
            }
            $result[$detalle->producto_id]->unidades[$detalle->unidad_id]->cantidad = $detalle->cantidad;
            $result[$detalle->producto_id]->total_min += $this->unidades_model->convert_minimo_by_um($detalle->producto_id, $detalle->unidad_id, $detalle->cantidad);
        }

        $venta->detalles = $result;
        return $venta;
    }

    function get_venta_detalle_convertido($venta_id) {
        $query = "SELECT doc.des_doc AS vdoc,cl.razon_social AS vnom ,vs.local_id as local_id,
            c.tipo_cliente AS vclien,vs.id AS id_shadow,m.nombre AS vmon,v.condicion_pago AS vcon,
                v.serie AS vser, v.numero AS vnum,
                v.venta_status AS vven,v.fecha AS vfecha,cp.nombre_condiciones AS vcon, v.tasa_cambio AS vtasa,
                @i := @i + 1 AS contador,
                c.razon_social,v.total AS vtotal,
                d.abr_doc,
                vs.fecha,
                vs.venta_id,
		vs.comprobante_id as comprobante_id,
                vs.id_documento as documento_id,
                m.simbolo as moneda,
                vs.subtotal,
                vs.total,
                vs.serie as serie_fac,
                vs.numero as numero_fac
                FROM venta_shadow AS vs
                CROSS JOIN (SELECT @i := 0) r
                JOIN documentos AS d
                ON d.id_doc=vs.id_documento
                JOIN cliente AS c
                ON c.id_cliente=vs.id_cliente
                JOIN venta AS v
                ON v.venta_id=vs.venta_id
                JOIN documentos AS doc
                ON doc.id_doc=v.id_documento
                JOIN cliente AS cl
                ON cl.id_cliente=v.id_cliente
                JOIN condiciones_pago AS cp
                ON cp.id_condiciones=v.condicion_pago
                JOIN moneda as m ON
                m.id_moneda=vs.id_moneda
                WHERE vs.venta_id='" . $venta_id . "'";

        return $this->db->query($query)->result();
    }

    function get_venta_convertido($id) {
        $this->db->select('vs.id,vs.venta_id');
        $this->db->from('venta_shadow as vs');
        $this->db->where('vs.id', $id);
        return $this->db->get()->result();
    }

    function remove_ventaconvertida_shadow($id_shadow) {
        $this->db->where("id", $id_shadow);
        $this->db->delete("venta_shadow");

        return $this->db->affected_rows();
    }

}
