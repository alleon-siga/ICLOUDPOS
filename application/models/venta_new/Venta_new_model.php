<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_new_model extends CI_Model
{

    private $table = 'venta';

    function __construct()
    {
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

    function get_ventas($where = array(), $action = '')
    {
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

        if (isset($where['id_documento']) && !empty($where['id_documento'])) {
            $this->db->where('venta.id_documento', $where['id_documento']);
        }

        $ventas = $this->db->get()->result();

        return $ventas;
    }

    //ls=lista en shadow
    function get_ventas_ls($where = array(), $action = '')
    {
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

        if (isset($where['id_documento']) && !empty($where['id_documento'])) {
            $this->db->where('venta.id_documento', $where['id_documento']);
        }

        $ventas = $this->db->get()->result();

        return $ventas;
    }

    function get_ventas_totales($where = array(), $action = '')
    {
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

    function get_last_id()
    {
        $last_id = $this->db->select('venta_id')
            ->from('venta')
            ->order_by('venta_id', "desc")
            ->limit(1)
            ->get()->row();

        return $last_id;
    }

    function get_venta_detalle($venta_id)
    {
        $venta = $this->get_ventas(array('venta_id' => $venta_id));
        $venta->cuotas = array();
        if ($venta->condicion_id == 2) {
            $venta->cuotas = $this->db->get_where('credito_cuotas', array('id_venta' => $venta_id))->result();
        }
        $venta->venta_documentos = $this->db->get_where('venta_documento', array('venta_id' => $venta_id))->result();

        $venta->detalles = $this->db->select("
            detalle_venta.id_detalle as detalle_id,
            detalle_venta.id_producto as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            producto.producto_descripcion as producto_descripcion,
            producto.producto_cualidad as producto_cualidad,
            detalle_venta.precio as precio,
            detalle_venta.precio_venta as precio_venta,
            detalle_venta.cantidad as cantidad,
            detalle_venta.cantidad_devuelta as cantidad_devuelta,
            detalle_venta.unidad_medida as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            detalle_venta.detalle_importe as importe,
            detalle_venta.afectacion_impuesto as afectacion_impuesto,
            detalle_venta.impuesto_porciento as impuesto_porciento
            ")
            ->from('detalle_venta')
            ->join('producto', 'producto.producto_id=detalle_venta.id_producto')
            ->join('unidades', 'unidades.id_unidad=detalle_venta.unidad_medida')
            ->where('detalle_venta.id_venta', $venta->venta_id)
            ->group_by('detalle_venta.id_detalle')
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

    function get_venta_detalle_convertido($venta_id)
    {
        $query = "SELECT doc.des_doc AS vdoc,cl.razon_social AS vnom ,c.tipo_cliente AS vclien,vs.id AS id_shadow,v.id_moneda AS vmon,v.condicion_pago AS vcon,
                v.serie AS vser, v.numero AS vnum,
                v.venta_status AS vven,v.fecha AS vfecha,cp.nombre_condiciones AS vcon, v.tasa_cambio AS vtasa,
                @i := @i + 1 AS contador,
                c.razon_social,v.total AS vtotal,
                d.abr_doc,
                vs.fecha,
                CASE WHEN vs.id_moneda='1029' THEN 'S/.' ELSE
                CASE WHEN vs.id_moneda='1030' THEN '$' END END AS moneda,
                vs.subtotal,
                vs.total
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
                WHERE vs.venta_id='" . $venta_id . "'";

        return $this->db->query($query)->result();
    }

    function remove_ventaconvertida_shadow($id_shadow)
    {
        $this->db->where('id', $id_shadow);
        $this->db->delete('venta_shadow');
        $this->db->affected_rows();
        return $this->db->affected_rows();
    }

    function get_venta_traspaso($id)
    {
        $this->db->select('c.serie, v.venta_id, v.fecha, cl.tipo_cliente, cl.razon_social, us.username, cl.identificacion, t.id');
        $this->db->from('traspaso t');
        $this->db->join('venta v', 't.ref_id = v.venta_id');
        $this->db->join('cliente cl', 'v.id_cliente = cl.id_cliente');
        $this->db->join('usuario us', 'v.id_vendedor=us.nUsuCodigo');
        $this->db->join('correlativos c', 'v.id_documento=c.id_documento and v.local_id=c.id_local', 'left');
        $this->db->where('t.id', $id);
        return $this->db->get()->row();
    }

    function get_venta_detalle_traspaso($id, $local_origen)
    {
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

    function get_traspaso_local($id)
    {
        $this->db->select('local_origen');
        $this->db->distinct();
        $this->db->from('traspaso_detalle');
        $this->db->where('traspaso_id', $id);
        $result = $this->db->get()->result();
        return $result;
    }

    function get_venta_facturar($venta_id)
    {
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

    function getDocumentoNumero()
    {
        $id_doc = $this->input->post('iddoc');
        $local_id = $this->input->post('local_id');

        $correlativo = $this->correlativos_model->get_correlativo($local_id, $id_doc);
        return $correlativo->serie . ' - ' . sumCod($correlativo->correlativo, 6);
    }

    function facturar_venta($venta_id, $iddoc = '')
    {
        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        $iddoc = $iddoc == '' ? $venta->id_documento : $iddoc;
        $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, $iddoc);
        $update_venta['fecha_facturacion'] = date('Y-m-d H:i:s');
        $update_venta['serie'] = $correlativo->serie;
        $update_venta['numero'] = $correlativo->correlativo;
        if ($iddoc != '')
            $update_venta['id_documento'] = $iddoc;
        $this->correlativos_model->sumar_correlativo($venta->local_id, $iddoc);

        // Hago la facturacion de comprobantes
        if ($venta->comprobante_id > 0) {
            $this->comprobante_model->facturar($venta->venta_id, $venta->comprobante_id);
        }

        if ($iddoc != 6) { //Si es diferente a la nota de venta
            //Correlativo para la guia de remision
            $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 4);
            $this->correlativos_model->sumar_correlativo($venta->local_id, 4);
            $update_venta['nro_guia'] = $correlativo->correlativo;
        }

        $this->db->where('venta_id', $venta_id);
        $this->db->update('venta', $update_venta);


        $this->db->where('io', 2);
        $this->db->where('operacion', 1);
        $this->db->where('ref_id', $venta_id);
        $this->db->update('kardex', array(
            'tipo' => $iddoc,
            'serie' => $update_venta['serie'],
            'numero' => sumCod($update_venta['numero'], 8)
        ));

        if (valueOptionDB('FACTURACION', 0) == 1 && ($iddoc == 1 || $iddoc == 3)) {
            $resp = $this->facturacion_model->facturarVenta($venta_id);
        }

    }

    function save_venta_caja($venta)
    {

        $venta_actual = $this->db->get_where('venta', array('venta_id' => $venta['venta_id']))->row();

        $moneda_id = $venta_actual->id_moneda;

        if ($venta['tipo_pago'] == 4 || $venta['tipo_pago'] == 8 || $venta['tipo_pago'] == 9 || $venta['tipo_pago'] == 7) {
            $banco = $this->db->get_where('banco', array('banco_id' => $venta['banco_id']))->row();
            $cuenta_id = $banco->cuenta_id;
        } else {
            $cuenta_id = $this->cajas_model->get_cuenta_id(array(
                'moneda_id' => $moneda_id,
                'local_id' => $venta_actual->local_id));
        }

        if ($cuenta_id == NULL) {
            $this->error = 'No existe una cuenta para este local';
            return false;
        }

        $cuenta_old = $this->cajas_model->get_cuenta($cuenta_id);

        $venta_total = $venta_actual->total;
        if ($venta_actual->condicion_pago == 2)
            $venta_total = $venta_actual->inicial;

        $this->cajas_model->update_saldo($cuenta_id, $venta_total);

        $this->cajas_mov_model->save_mov(array(
            'caja_desglose_id' => $cuenta_id,
            'usuario_id' => $venta['id_usuario'],
            'fecha_mov' => date('Y-m-d H:i:s'),
            'movimiento' => 'INGRESO',
            'operacion' => 'VENTA',
            'medio_pago' => $venta['tipo_pago'],
            'saldo' => $venta_total,
            'saldo_old' => $cuenta_old->saldo,
            'ref_id' => $venta_actual->venta_id,
            'ref_val' => $venta['num_oper'],
        ));


        //guardo la relacion del modo de pago
        if ($venta_actual->condicion_pago == 1 || ($venta_actual->condicion_pago == 2 && $venta_actual->inicial > 0)) {

            if ($venta['tipo_pago'] != 7) {
                $contado = array(
                    'id_venta' => $venta_actual->venta_id,
                    'status' => 'PagoCancelado',
                    'montopagado' => $venta_total
                );
                $this->db->insert('contado', $contado);
            } elseif ($venta['tipo_pago'] == 7) {
                $tarjeta = array(
                    'venta_id' => $venta_actual->venta_id,
                    'tarjeta_pago_id' => $venta['tarjeta'],
                    'numero' => $venta['num_oper']
                );
                $this->db->insert('venta_tarjeta', $tarjeta);
            }
        }

        $update_venta = array(
            'pagado' => $venta['importe'],
            'vuelto' => $venta['vuelto'],
            'venta_status' => 'COMPLETADO'
        );

        if ($venta_actual->condicion_pago == 1) {
            $correlativo = $this->correlativos_model->get_correlativo($venta_actual->local_id, $venta_actual->id_documento);
            $update_venta['fecha_facturacion'] = $venta_actual->fecha;
            $update_venta['serie'] = $correlativo->serie;
            $update_venta['numero'] = $correlativo->correlativo;
            $this->correlativos_model->sumar_correlativo($venta_actual->local_id, $venta_actual->id_documento);

            // Hago la facturacion de comprobantes
            if ($venta_actual->comprobante_id > 0)
                $this->comprobante_model->facturar($venta_actual->venta_id, $venta_actual->comprobante_id);
        }

        $this->db->where('venta_id', $venta['venta_id']);
        $this->db->update('venta', $update_venta);


        if ($venta_actual->condicion_pago == 1) {

            $this->db->where('io', 2);
            $this->db->where('operacion', 1);
            $this->db->where('ref_id', $venta['venta_id']);
            $this->db->update('kardex', array(
                'serie' => $update_venta['serie'],
                'numero' => sumCod($update_venta['numero'], 8)
            ));

            if (valueOptionDB('FACTURACION', 0) == 1 && ($venta_actual->id_documento == 1 || $venta_actual->id_documento == 3)) {
                $resp = $this->facturacion_model->facturarVenta($venta['venta_id']);
            }
        }

        return true;
    }

    function save_venta_contado($venta, $productos, $traspasos = array())
    {
        if ($venta['venta_status'] != 'CAJA') {
            if ($venta['vc_forma_pago'] == 4 || $venta['vc_forma_pago'] == 8 || $venta['vc_forma_pago'] == 9 || $venta['vc_forma_pago'] == 7) {
                $banco = $this->db->get_where('banco', array('banco_id' => $venta['vc_banco_id']))->row();
                $cuenta_id = $banco->cuenta_id;
            } else {
                $cuenta_id = $this->cajas_model->get_cuenta_id(array(
                    'moneda_id' => $venta['id_moneda'],
                    'local_id' => $venta['local_id']));
            }

            if ($cuenta_id == NULL) {
                $this->error = 'No existe una cuenta para este local';
                return false;
            }
        }

        if (sizeof($traspasos) > 0) {
            $this->save_traspasos($traspasos, $venta['id_usuario'], $venta['condicion_pago']);
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
            'dni_garante' => null,
            'inicial' => null,
            'tipo_impuesto' => $venta['tipo_impuesto'],
            'comprobante_id' => $venta['comprobante_id'],
            'nota' => $venta['venta_nota'],
            'dni_garante' => $venta['dni_garante']
        );

        if ($venta['venta_status'] == 'CAJA') {
            $venta_contado['total'] = $venta['total_importe'];
        } else {
            $correlativo = $this->correlativos_model->get_correlativo($venta['local_id'], $venta['id_documento']);
            $venta_contado['fecha_facturacion'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s")));
            $venta_contado['serie'] = $correlativo->serie;
            $venta_contado['numero'] = $correlativo->correlativo;

            $this->correlativos_model->sumar_correlativo($venta['local_id'], $venta['id_documento']);
            if ($venta['id_documento'] != 6) { //Si es diferente a la nota de venta
                //Correlativo para la guia de remision
                $correlativo = $this->correlativos_model->get_correlativo($venta['local_id'], 4);
                $this->correlativos_model->sumar_correlativo($venta['local_id'], 4);
                $venta_contado['nro_guia'] = $correlativo->correlativo;
            }
        }

        //inserto la venta
        $this->db->insert('venta', $venta_contado);
        $venta_id = $this->db->insert_id();

        if ($venta['venta_status'] != 'CAJA') {
            // Hago la facturacion de comprobantes
            if (validOption('COMPROBANTE', 1))
                $this->comprobante_model->facturar($venta_id, $venta['comprobante_id']);

            $cuenta_old = $this->cajas_model->get_cuenta($cuenta_id);

            $this->cajas_model->update_saldo($cuenta_id, $venta_contado['total']);

            $this->cajas_mov_model->save_mov(array(
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

        $this->correlativos_model->update_nota_pedido($venta['local_id'], $venta_id);

        $this->save_producto_detalles($venta_id, $venta['id_documento'], $venta['local_id'], $productos, $venta['id_usuario']);

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

        $this->recalc_totales($venta_id);

        if (valueOptionDB('FACTURACION', 0) == 1 && ($venta['id_documento'] == 1 || $venta['id_documento'] == 3)) {
            if ($venta['venta_status'] != 'CAJA') {
                $resp = $this->facturacion_model->facturarVenta($venta_id);
            }
        } elseif (valueOptionDB('FACTURACION', 0) == 1) {
            log_message('debug', 'Facturacion electronica. Documento erroneo. Doc: ' . $venta['id_documento']);
        }

        return $venta_id;

    }

    function save_venta_credito($venta, $productos, $traspasos = array(), $cuotas)
    {

        if ($venta['venta_status'] != 'CAJA' && $venta['c_inicial'] > 0) {
            if ($venta['vc_forma_pago'] == 4 || $venta['vc_forma_pago'] == 8 || $venta['vc_forma_pago'] == 9 || $venta['vc_forma_pago'] == 7) {
                $banco = $this->db->get_where('banco', array('banco_id' => $venta['vc_banco_id']))->row();
                $cuenta_id = $banco->cuenta_id;
            } else {
                $cuenta_id = $this->cajas_model->get_cuenta_id(array(
                    'moneda_id' => $venta['id_moneda'],
                    'local_id' => $venta['local_id']));
            }

            if ($cuenta_id == NULL) {
                $this->error = 'No existe una cuenta para este local';
                return false;
            }
        }

        if (sizeof($traspasos) > 0) {
            $this->save_traspasos($traspasos, $venta['id_usuario'], $venta['condicion_pago']);
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
            'total' => $venta['c_precio_credito'] + $venta['c_inicial'],
            'pagado' => $venta['vc_importe'],
            'vuelto' => $venta['vc_vuelto'],
            'tasa_cambio' => $venta['tasa_cambio'],
            'dni_garante' => $venta['c_dni_garante'],
            'inicial' => $venta['c_inicial'],
            'tipo_impuesto' => $venta['tipo_impuesto'],
            'comprobante_id' => $venta['comprobante_id'],
            'nota' => $venta['venta_nota'],
            'dni_garante' => $venta['dni_garante']
        );

        //inserto la venta
        $this->db->insert('venta', $venta_contado);
        $venta_id = $this->db->insert_id();

        if ($venta['venta_status'] != 'CAJA' && $venta_contado['inicial'] > 0) {


            $cuenta_old = $this->cajas_model->get_cuenta($cuenta_id);

            $this->cajas_model->update_saldo($cuenta_id, $venta_contado['inicial']);

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $cuenta_id,
                'usuario_id' => $venta['id_usuario'],
                'fecha_mov' => date('Y-m-d H:i:s'),
                'movimiento' => 'INGRESO',
                'operacion' => 'VENTA',
                'medio_pago' => $venta['vc_forma_pago'],
                'saldo' => valueOptionDB('REDONDEO_VENTAS', 0) == 1 ? formatPrice($venta_contado['inicial']) : $venta_contado['inicial'],
                'saldo_old' => $cuenta_old->saldo,
                'ref_id' => $venta_id,
                'ref_val' => $venta['vc_num_oper'],
            ));
        }


        $this->correlativos_model->update_nota_pedido($venta['local_id'], $venta_id);

        $this->save_producto_detalles($venta_id, $venta['id_documento'], $venta['local_id'], $productos, $venta['id_usuario']);

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

        $this->recalc_totales($venta_id);

        return $venta_id;

    }

    function cerrar_venta($venta_id, $correlativos = array())
    {
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
    function save_producto_detalles($venta_id, $doc_id, $local_id, $productos, $id_usuario)
    {
        //Preparo los detalles de la venta para insertarlo y sus historicos
        $venta = $this->get_ventas(array('venta_id' => $venta_id));
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
                $producto->id_producto,
                $producto->unidad_medida,
                $producto->cantidad
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
                'id_venta' => $venta_id,
                'id_producto' => $producto->id_producto,
                'precio' => $producto->precio,
                'cantidad' => $producto->cantidad,
                'unidad_medida' => $producto->unidad_medida,
                'detalle_importe' => $producto->detalle_importe,
                'detalle_costo_promedio' => $this->producto_model->get_costo_promedio($producto->id_producto, $producto->unidad_medida),
                'detalle_costo_ultimo' => $costo_u != NULL ? $costo_u->costo : 0,
                'detalle_utilidad' => 0,
                'impuesto_id' => $p->id_impuesto,
                'afectacion_impuesto' => $prod->producto_afectacion_impuesto,
                'impuesto_porciento' => $p->porcentaje_impuesto,
                'precio_venta' => $producto->precio_venta,
                'tipo_impuesto_compra' => $costo_u->tipo_impuesto_compra,
                'cantidad_devuelta' => 0
            );
            array_push($venta_detalle, $producto_detalle);

            if (validOption('ACTIVAR_SHADOW', 1) && $doc_id != 6) {
                //preparo el detalle de la venta contable cuando el shadow stock esta activo
                $producto_detalle = array(
                    'venta_id' => $venta_id,
                    'producto_id' => $producto->id_producto,
                    'unidad_id' => $producto->unidad_medida,
                    'precio' => $producto->precio,
                    'cantidad' => $producto->cantidad
                );
                array_push($venta_contable_detalle, $producto_detalle);
            }
            $precio[$producto->id_producto] = $this->unidades_model->get_maximo_costo($producto->id_producto, $producto->unidad_medida, $producto->precio);
            $ArrfectImp[$producto->id_producto] = $prod->producto_afectacion_impuesto;
            $impPorciento[$producto->id_producto] = $p->porcentaje_impuesto;
        }

        //inserto los detalles de la venta
        $this->db->insert_batch('detalle_venta', $venta_detalle);

        if (validOption('ACTIVAR_SHADOW', 1) && $doc_id != 6)
            $this->db->insert_batch('venta_contable_detalle', $venta_contable_detalle);


        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $local_id,
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min - $value);

            //CREAR EL HISTORICO DE LA VENTA *************************************
            /*$this->historico_model->set_historico(array(
                'producto_id' => $key,
                'local_id' => $local_id,
                'usuario_id' => $this->session->userdata('nUsuCodigo'),
                'cantidad' => $value,
                'cantidad_actual' => $this->unidades_model->convert_minimo_um($key, $result['cantidad'], $result['fraccion']),
                'tipo_movimiento' => "VENTA",
                'tipo_operacion' => 'SALIDA',
                'referencia_valor' => 'Se realizo una Venta',
                'referencia_id' => $venta_id
            ));*/

            $tipo = 0;
            if ($venta->documento_id == 1)
                $tipo = 1;
            if ($venta->documento_id == 3)
                $tipo = 3;
            if ($venta->documento_id == 6)
                $tipo = -2;

            $costo = 0;
            if ($ArrfectImp[$key] == '1') {
                if ($venta->tipo_impuesto == 1) { //incluye impuesto
                    $costo = $precio[$key] / (($impPorciento[$key] / 100) + 1);
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
                'local_id' => $local_id,
                'producto_id' => $key,
                'cantidad' => $value,
                'io' => 2,
                'tipo' => $tipo,
                'operacion' => 1,
                'serie' => $venta->serie != null ? $venta->serie : '-',
                'numero' => $venta->numero != null ? sumCod($venta->numero, 8) : '-',
                'ref_id' => $venta->venta_id,
                'usuario_id' => $id_usuario,
                'costo' => $costo,
                'moneda_id' => $venta->moneda_id
            );
            $this->kardex_model->set_kardex($values);

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

    private
    function save_traspasos($traspasos, $id_usuario, $condicion_pago)
    {
        $next_id = $this->db->select_max('venta_id')->get('venta')->row();
        //Guardo en tabla traspaso
        $values = array(
            'ref_id' => $next_id->venta_id + 1,
            'usuario_id' => $id_usuario,
            'local_destino' => $traspasos[0]->parent_local,
            'fecha' => date('Y-m-d H:i:s'),
            'motivo' => ($condicion_pago == '1') ? 'VENTA AL CONTADO' : 'VENTA AL CREDITO'
        );
        $this->db->insert('traspaso', $values);
        $idTraspaso = $this->db->insert_id();
        //Hago los traspasos en caso de haber
        foreach ($traspasos as $traspaso) {
            $orden_max = $this->db->select_max('orden', 'orden')
                ->where('producto_id', $traspaso->id_producto)->get('unidades_has_producto')->row();

            $minima_unidad = $this->db->select('id_unidad as um_id')
                ->where('producto_id', $traspaso->id_producto)
                ->where('orden', $orden_max->orden)
                ->get('unidades_has_producto')->row();

            $result = $this->traspaso_model->traspasar_productos($traspaso->id_producto, $traspaso->local_id, $traspaso->parent_local, $id_usuario, array(
                'um_id' => $minima_unidad->um_id,
                'cantidad' => $traspaso->cantidad,
                'venta_id' => $next_id->venta_id + 1,
                'traspaso_id'=>$idTraspaso
            ));
            //aqui guarda los detalles de traspaso
            $this->db->insert("traspaso_detalle", array(
                'traspaso_id' => $idTraspaso,
                'local_origen' => $traspaso->local_id,
                'kardex_id' => $result["entrada_id"]
            ));
        }
    }

    public
    function get_next_id()
    {
        $next_id = $this->db->select_max('venta_id')->get('venta')->row();
        return sumCod($next_id->venta_id + 1, 8);
    }

    public function anular_venta($venta_id, $metodo_pago, $cuenta_id, $motivo, $id_usuario = false)
    {
        $venta = $this->get_venta_detalle($venta_id);

        $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 2);
        $serie = $correlativo->serie;
        $numero = $correlativo->correlativo;

        $cantidades = array();
        $afectacion_impuesto = array();
        $precio = array();
        $impuesto_porciento = array();

        if ($venta->documento_id == 1 || $venta->documento_id == 3) {
            if (valueOptionDB('FACTURACION', 0) == 1) {
                $facturacion = $this->db->order_by('id', 'desc')->get_where('facturacion', array(
                    'documento_tipo' => '0' . $venta->documento_id,
                    'ref_id' => $venta->venta_id
                ))->row();

                if ($facturacion != NULL) {
                    if ($facturacion->estado == 3) {
                        if ($venta->documento_id == 1) {
                            $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 9);
                            $serie = $correlativo->serie;
                            $numero = $correlativo->correlativo;
                        } elseif ($venta->documento_id == 3) {
                            $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 8);
                            $serie = $correlativo->serie;
                            $numero = $correlativo->correlativo;
                        }
                    }
                }
            }
        }

        foreach ($venta->detalles as $detalle) {

            if (!isset($cantidades[$detalle->producto_id]))
                $cantidades[$detalle->producto_id] = 0;


            $cantidades[$detalle->producto_id] += $this->unidades_model->convert_minimo_by_um(
                $detalle->producto_id,
                $detalle->unidad_id,
                $detalle->cantidad
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

        //Al anular, la cantidad devuelta queda igual a la cantidad
        $this->db->query("UPDATE detalle_venta SET cantidad_devuelta = cantidad WHERE id_venta =" . $venta_id);

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

        $updated_correlativo = false;
        if (valueOptionDB('FACTURACION', 0) == 1 && ($venta->id_documento == 1 || $venta->id_documento == 3) && $venta->numero != null) {
            $facturacion = $this->db->get_where('facturacion', array(
                'ref_id' => $venta_id,
                'documento_tipo' => sumCod($venta->id_documento, 2)
            ))->row();

            if ($facturacion != null) {
                if ($facturacion->estado == 3) {
                    $resp = $this->facturacion_model->anularVenta($venta_id, $serie . '-' . $numero, $motivo);
                    $updated_correlativo = true;
                    if ($venta->id_documento == 1) {
                        $this->correlativos_model->sumar_correlativo($venta->local_id, 9);
                    } elseif ($venta->id_documento == 1) {
                        $this->correlativos_model->sumar_correlativo($venta->local_id, 8);
                    }
                }
            }
        }

        if ($updated_correlativo == false) {
            $this->correlativos_model->sumar_correlativo($venta->local_id, 2);
        }


        return $venta_id;
    }

    public function anular_venta_caja($venta_id, $metodo_pago, $cuenta_id, $motivo, $id_usuario = false)
    {
        $venta = $this->get_venta_detalle($venta_id);

        $cantidades = array();
        $afectacion_impuesto = array();
        $precio = array();
        $impuesto_porciento = array();


        foreach ($venta->detalles as $detalle) {

            if (!isset($cantidades[$detalle->producto_id]))
                $cantidades[$detalle->producto_id] = 0;


            $cantidades[$detalle->producto_id] += $this->unidades_model->convert_minimo_by_um(
                $detalle->producto_id,
                $detalle->unidad_id,
                $detalle->cantidad
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

        //Al anular, la cantidad devuelta queda igual a la cantidad
        $this->db->query("UPDATE detalle_venta SET cantidad_devuelta = cantidad WHERE id_venta =" . $venta_id);

        return $venta_id;
    }

    private function recalc_totales($venta_id)
    {
        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        $detalles = $this->db->get_where('detalle_venta', array('id_venta' => $venta_id))->result();

        $impuesto = 0;
        $subtotal = 0;
        $total = 0;
        foreach ($detalles as $d) {
            $total += ($d->cantidad - $d->cantidad_devuelta) * $d->precio;
        }


        if ($venta->tipo_impuesto == 1) {
            foreach ($detalles as $d) {
                if ($d->afectacion_impuesto == OP_GRAVABLE) {
                    $factor = (100 + $d->impuesto_porciento) / 100;
                    $impuesto += (($d->cantidad - $d->cantidad_devuelta) * $d->precio) - ((($d->cantidad - $d->cantidad_devuelta) * $d->precio) / $factor);
                }
            }
            $subtotal = $total - $impuesto;
        } elseif ($venta->tipo_impuesto == 2) {
            $subtotal = $total;
            foreach ($detalles as $d) {
                if ($d->afectacion_impuesto == OP_GRAVABLE) {
                    $factor = (100 + $d->impuesto_porciento) / 100;
                    $impuesto += ((($d->cantidad - $d->cantidad_devuelta) * $d->precio) * $factor) - (($d->cantidad - $d->cantidad_devuelta) * $d->precio);
                }
            }
            $total = $subtotal + $impuesto;
        } else {
            $subtotal = $total;
        }

        $this->db->where('venta_id', $venta_id);
        $this->db->update('venta', array(
            'total' => $total,
            'subtotal' => $subtotal,
            'total_impuesto' => $impuesto,
        ));

        return $total;
    }

    public
    function devolver_venta($venta_id, $total_importe, $devoluciones, $serie, $numero, $metodo_pago, $cuenta_id, $motivo, $id_usuario = false)
    {
        $venta = $this->get_venta_detalle($venta_id);

        $cantidades = array();
        $afectacion_impuesto = array();
        $precio = array();
        $impuesto_porciento = array();
        foreach ($devoluciones as $detalle) {

            if (!isset($cantidades[$detalle->producto_id]))
                $cantidades[$detalle->producto_id] = 0;

            $cantidades[$detalle->producto_id] += $this->unidades_model->convert_minimo_by_um(
                $detalle->producto_id,
                $detalle->unidad_id,
                $detalle->devolver
            );
            $precio[$detalle->producto_id] = $this->unidades_model->get_maximo_costo($detalle->producto_id, $detalle->unidad_id, $detalle->precio);
            $detalle_temp = $this->db->get_where('detalle_venta', array('id_detalle' => $detalle->detalle_id))->row();
            $detalle->impuesto_porciento = $detalle_temp->impuesto_porciento;
            $impuesto_porciento[$detalle->producto_id] = $detalle->impuesto_porciento;
            $afectacion_impuesto[$detalle->producto_id] = $detalle_temp->afectacion_impuesto;

            $this->db->where('id_detalle', $detalle->detalle_id);
            $this->db->select('cantidad_devuelta');
            $this->db->from('detalle_venta');
            $cantidadD = $this->db->get()->row();

            $this->db->where('id_detalle', $detalle->detalle_id);
            $this->db->update('detalle_venta', array(
                'cantidad_devuelta' => $cantidadD->cantidad_devuelta + $detalle->devolver,
                'detalle_importe' => $detalle->new_importe
            ));
            //Guardando en tabla venta_devolucion
            /*$this->db->insert('venta_devolucion', array(
                'id_venta' => $venta_id,
                'id_producto' => $detalle->producto_id,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->devolver,
                'unidad_medida' => $detalle->unidad_id,
                'detalle_importe' => $detalle->devolver * $detalle->precio,
                'serie' => $serie,
                'numero' => $numero
            ));*/
        }

        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                'id_producto' => $key,
                'id_local' => $venta->local_id
            ))->row();

            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min + $value);

            /*$this->historico_model->set_historico(array(
                'producto_id' => $key,
                'local_id' => $venta->local_id,
                'cantidad' => $value,
                'cantidad_actual' => $this->unidades_model->convert_minimo_um($key, $result['cantidad'], $result['fraccion']),
                'tipo_movimiento' => "DEVOLUCION",
                'tipo_operacion' => 'ENTRADA',
                'referencia_valor' => 'Devolucion de Ventas',
                'referencia_id' => $venta_id
            ));*/

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
    function imprimir_pedido($data)
    {
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
    function imprimir_boleta($data)
    {
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
    function imprimir_factura($data)
    {
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

    public function save_recarga($venta)
    {
        $data = array(
            'local_id' => $venta['local_id'],
            'id_documento' => $venta['id_documento'],
            'id_cliente' => $venta['id_cliente'],
            'id_vendedor' => $venta['id_usuario'],
            'condicion_pago' => $venta['condicion_pago'],
            'id_moneda' => $venta['id_moneda'],
            'venta_status' => $venta['venta_status'],
            'fecha' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s"))),
            'factura_impresa' => 0,
            'subtotal' => number_format($venta['total_importe'] / 1.18, 2),
            'total_impuesto' => number_format($venta['total_importe'] - ($venta['total_importe'] / 1.18), 2),
            'total' => $venta['total_importe'],
            'pagado' => $venta['vc_importe'],
            'vuelto' => $venta['vc_vuelto'],
            'tasa_cambio' => 0,
            'dni_garante' => null,
            'inicial' => 0,
            'tipo_impuesto' => 1,
            'comprobante_id' => 0,
            'nota' => null,
            'dni_garante' => null,
        );
        if ($venta['condicion_pago'] == '1') {
            $correlativo = $this->correlativos_model->get_correlativo($venta['local_id'], $venta['id_documento']);
            $data['fecha_facturacion'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s")));
            $data['serie'] = $correlativo->serie;
            $data['numero'] = $correlativo->correlativo;
            $this->correlativos_model->sumar_correlativo($venta['local_id'], $venta['id_documento']);
        }
        //inserto la venta
        $this->db->insert('venta', $data);
        $venta_id = $this->db->insert_id();

        $product = $this->db->get_where('producto', array('producto_nombre' => 'RECARGA VIRTUAL'))->row();
        $data = array(
            'id_venta' => $venta_id,
            'id_producto' => $product->producto_id,
            'precio' => number_format($venta['total_importe'], 2),
            'cantidad' => 1,
            'unidad_medida' => 1,
            'detalle_importe' => number_format($venta['total_importe'], 2),
            'impuesto_id' => 1,
            'impuesto_porciento' => 18.00,
            'precio_venta' => number_format($venta['total_importe'], 2)
        );
        //inserto en detalle
        $this->db->insert('detalle_venta', $data);

        if ($venta['condicion_pago'] == 2) { //Al credito
            $data = array(
                'id_venta' => $venta_id,
                'int_credito_nrocuota' => 1,
                'dec_credito_montocuota' => number_format($venta['total_importe'], 2),
                'var_credito_estado' => 'PagoPendiente',
                'id_moneda' => $venta['id_moneda'],
                'tasa_cambio' => 0
            );
            $this->db->insert('credito', $data);

            $data = array(
                'nro_letra' => '1 / 1',
                'fecha_giro' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s"))),
                'fecha_vencimiento' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $venta['fecha_venta']) . date(" H:i:s"))),
                'monto' => number_format($venta['total_importe'], 2),
                'isgiro' => '0',
                'id_venta' => $venta_id,
                'ispagado' => '0'
            );
            $this->db->insert('credito_cuotas', $data);
        } else {
            if ($venta['vc_forma_pago'] == 4 || $venta['vc_forma_pago'] == 8 || $venta['vc_forma_pago'] == 9 || $venta['vc_forma_pago'] == 7) {
                $banco = $this->db->get_where('banco', array('banco_id' => $venta['vc_banco_id']))->row();
                $cuenta_id = $banco->cuenta_id;
            } else {
                $cuenta_id = $this->cajas_model->get_cuenta_id(array(
                    'moneda_id' => $venta['id_moneda'],
                    'local_id' => $venta['local_id']));
            }

            if ($cuenta_id == NULL) {
                $this->error = 'No existe una cuenta para este local';
                return false;
            }

            $cuenta_old = $this->cajas_model->get_cuenta($cuenta_id);

            $this->cajas_model->update_saldo($cuenta_id, $venta['total_importe']);

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $cuenta_id,
                'usuario_id' => $venta['id_usuario'],
                'fecha_mov' => date('Y-m-d H:i:s'),
                'movimiento' => 'INGRESO',
                'operacion' => 'VENTA',
                'medio_pago' => $venta['vc_forma_pago'],
                'saldo' => $venta['total_importe'],
                'saldo_old' => $cuenta_old->saldo,
                'ref_id' => $venta_id,
                'ref_val' => $venta['vc_num_oper']
            ));
        }

        $data = array(
            'id_venta' => $venta_id,
            'rec_trans' => $venta['cod_tran'],
            'rec_nro' => $venta['rec_nro'],
            'rec_ope' => $venta['rec_ope'],
            'rec_pob' => $venta['rec_pob']
        );
        //inserto la recarga
        $this->db->insert('recarga', $data);
        //Actualizo cliente
        $update['nota'] = $venta['nota'];
        $update['telefono1'] = $venta['telefono1'];
        $this->db->where('id_cliente', $venta['id_cliente']);
        $this->db->update('cliente', $update);
        return $venta_id;
    }

    public function ultimasVentas($venta)
    {
        $this->db->select('date(v.fecha) AS fecha, dv.precio, u.nombre_unidad, venta_id, m.simbolo, dv.unidad_medida, dv.id_producto');
        $this->db->from('detalle_venta dv');
        $this->db->join('venta v', 'v.venta_id=dv.id_venta');
        $this->db->join('unidades u', 'dv.unidad_medida=u.id_unidad');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        $this->db->where('id_producto', $venta['id_producto']);
        $this->db->where('id_cliente', $venta['id_cliente']);
        $this->db->order_by('v.fecha DESC');
        $this->db->limit(10);
        $datos = $this->db->get()->result();
        $x = 0;
        foreach ($datos as $dato) {
            $datos[$x]->precio = number_format($this->unidades_model->get_maximo_costo($datos[$x]->id_producto, $datos[$x]->unidad_medida, $datos[$x]->precio), 2);
            $datos[$x]->nombre_unidad = $this->unidades_model->get_um_min_by_producto($datos[$x]->id_producto);
            $x++;
        }
        return $datos;
    }

    public function ultimasCompras($id)
    {
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

    function prepare_venta($id)
    {

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

            dv.precio_venta as precio_venta,
            pcu.contable_costo as contable_costo,
            pcu.porcentaje_utilidad as porcentaje_utilidad,
            pcu.costo as real_costo
            ')
            ->from('detalle_venta as dv')
            ->join('venta as v', 'dv.id_venta = v.venta_id')
            ->join('local', 'local.int_local_id=v.local_id')
            ->join('producto', 'producto.producto_id=dv.id_producto')
            ->join('producto_costo_unitario', 'producto_costo_unitario.producto_id=dv.id_producto')
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


            $result[$detalle->producto_id]->contable_costo = $detalle->contable_costo;
            $result[$detalle->producto_id]->real_costo = $detalle->real_costo;
            $result[$detalle->producto_id]->precio_comp = (($detalle->porcentaje_utilidad / 100) * $detalle->contable_costo) + $detalle->contable_costo;

        }

        $venta->detalles = $result;
        return $venta;
    }

    function verificarAnulacion($venta_id)
    {
        $this->db->select('COUNT(*) AS numReg');
        $this->db->from('kardex AS k');
        $this->db->where("k.io = 2 AND k.tipo = 7 AND k.operacion = 5 AND k.ref_id = $venta_id");
        return $this->db->get()->row();
    }
}
