<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class facturacion_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        require_once(APPPATH . 'libraries/Facturador/Facturador.php');
        require APPPATH . 'libraries/Numeroletra.php';

        $this->load->model('venta_new/venta_new_model');
    }

    function get_facturacion($where = array())
    {
        $this->db->select('facturacion.*, local.*, usuario.username')->from('facturacion')
            ->join('local', 'local.int_local_id = facturacion.local_id')
            ->join('venta', 'venta.venta_id = facturacion.ref_id')
            ->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor');
        if (isset($where['local_id']))
            $this->db->where('facturacion.local_id', $where['local_id']);

        if (isset($where['estado']) && $where['estado'] != "")
            $this->db->where('facturacion.estado', $where['estado']);

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('facturacion.fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('facturacion.fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['id'])) {
            $this->db->where('facturacion.id', $where['id']);
            $result = $this->db->get()->row();

            $result->detalles = $this->db->get_where('facturacion_detalle', array('facturacion_id' => $result->id))->result();
            $result->motivo_nota = $this->get_nota_credito_motivo($result->documento_mod_motivo);

            $numero_cero = explode('-', $result->documento_numero);
            $result->documento_numero_ceros = $numero_cero[0] . '-' . sumCod($numero_cero[1], 8);

            $result->documento_mod_numero_ceros = null;
            if ($result->documento_tipo == '07' || $result->documento_tipo == '08') {
                $numero_cero = explode('-', $result->documento_mod_numero);
                $result->documento_mod_numero_ceros = $numero_cero[0] . '-' . sumCod($numero_cero[1], 8);
            }


            $result->total_letra = Numeroletra::convertir($result->total);

            return $result;
        }

        return $this->db->get()->result();
    }

    function get_comprobantes_baja($where = array())
    {

        $query = "
            SELECT 
                *
            FROM
                facturacion
            WHERE
                (estado = 1 OR estado = 3) AND datediff(now(), fecha) < 3
        ";

        if (isset($where['local_id']))
            $query .= " AND local_id = " . $where['local_id'];

        return $this->db->query($query)->result();
    }

    function get_comprobantes_generados($where = array())
    {
        $this->db->select('*')->from('facturacion')
            ->join('local', 'local.int_local_id = facturacion.local_id');

        if (isset($where['local_id']))
            $this->db->where('facturacion.local_id', $where['local_id']);


        if (isset($where['fecha']) && isset($where['estado'])) {
            if ($where['estado'] == '1') {
                $this->db->where('facturacion.fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha'] . " 00:00:00")));
                $this->db->where('facturacion.fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha'] . " 23:59:59")));
            }
        }

        if (isset($where['estado'])) {
            $this->db->where('facturacion.estado', $where['estado']);
        }

        if ($where['tipo_documento'] == '01') {
            $this->db->where("
        (facturacion.documento_tipo = '01' OR 
        (facturacion.documento_tipo = '07' AND documento_mod_tipo = '01') OR  
        (facturacion.documento_tipo = '08' AND documento_mod_tipo = '01'))");
        } elseif ($where['tipo_documento'] == '03') {
            $this->db->where("
        (facturacion.documento_tipo = '03' OR 
        (facturacion.documento_tipo = '07' AND documento_mod_tipo = '03') OR  
        (facturacion.documento_tipo = '08' AND documento_mod_tipo = '03'))");
        }


        return $this->db->get()->result();
    }

    function get_ventas_emitidas($params)
    {
        $this->db->select('v.*, f.cliente_nombre')
            ->from('venta AS v')
            ->join('facturacion as f', 'v.venta_id = f.ref_id');
        $this->db->where('estado', 3);
        if ($params['local_id'] > 0) {
            $this->db->where('v.local_id', $params['local_id']);
        }
        if (!empty($params['fecha_ini']) && !empty($params['fecha_fin'])) {
            $this->db->where("DATE(v.fecha) >='" . $params['fecha_ini'] . "' AND DATE(v.fecha)<='" . $params['fecha_fin'] . "'");
        }
        if (!empty($params['doc_id'] > 0)) {
            $this->db->where('f.documento_tipo', $params['doc_id']);
        }
        $this->db->group_by('v.venta_id');

        $ventas = $this->db->get()->result();

        foreach ($ventas as $venta) {
            $venta->comprobantes = $this->db->get_where('facturacion', array('ref_id' => $venta->venta_id))->result();
        }

        return $ventas;
    }

    //Se Agrego el campo vfac..identificara de donde viene la venta
    function get_relacion_comprobantes($params)
    {
        $this->db->select('f.fecha as "FecFacturacionElectr", v.fecha as "Fec_Venta", l.local_nombre as "local_nombre", v.venta_id,	f.id,
	CASE 1 
		WHEN f.documento_tipo = "01" THEN "FACTURA" 
		WHEN f.documento_tipo = "03" THEN "BOLETA" 
		WHEN f.documento_tipo = "07" THEN "NOTACREDITO"
	END AS "documento",
	f.documento_mod_tipo,f.documento_mod_numero,f.documento_mod_motivo,
	f.documento_numero,v.numero,f.cliente_identificacion,cliente_nombre,
        IF(f.documento_tipo = "07", f.subtotal * -1, f.subtotal) as subtotal,
        IF(f.documento_tipo = "07", f.impuesto * -1, f.impuesto) as impuesto,
        IF(f.documento_tipo = "07", f.total * -1, f.total) as total,f.nota,
	CASE 1
		WHEN f.estado = 0 THEN "NO GENERADO"
		WHEN f.estado = 1 THEN "GENERADO"
		WHEN f.estado = 2 THEN "ENVIADO"
		WHEN f.estado = 3 THEN "ACEPTADO"
		WHEN f.estado = 4 THEN "RECHAZADO" 
	END AS "Estado",vs.id,
        IF(f.id = vs.id_factura, "SHADOW", "NORMAL") AS "vfac"', false)
            ->from('facturacion AS f')
            ->join('venta AS v', 'f.ref_id = v.venta_id')
            ->join('local AS l', 'v.local_id = l.int_local_id', 'left')
            ->join('venta_shadow AS vs', 'vs.id_factura = f.id', 'left');
        if ($params['local_id'] > 0) {
            $this->db->where('v.local_id', $params['local_id']);
        }
        if (!empty($params['fecha_ini']) && !empty($params['fecha_fin']) && !empty($params['fecha_flag'] == 1)) {
            $this->db->where("DATE(f.fecha) >='" . $params['fecha_ini'] . "' AND DATE(f.fecha)<='" . $params['fecha_fin'] . "'");
        }
        if (!empty($params['doc_id'] > 0)) {
            $this->db->where('f.documento_tipo', $params['doc_id']);
        }
        if (!empty($params['estado_id'] > -1)) {
            $this->db->where('f.estado', $params['estado_id']);
        }
        //TODO Filtros de ventas por shadow (19-10-2018) Camargo Carlos
        if (isset($params['tipo_ven']) && !empty($params['tipo_ven'] == "1")) {
            $this->db->where('vs.id', NULL);
        } else if (isset($params['tipo_ven']) && !empty($params['tipo_ven'] == "0")) {
            $this->db->where('f.id=vs.id_factura');
        } else if (isset($params['tipo_ven']) && !empty($params['tipo_ven'] == "-1")) {

        }

        $this->db->order_by('f.fecha, f.id', 'ASC');
        $ventas = $this->db->get()->result();

        foreach ($ventas as $venta) {
            $venta->comprobantes = $this->db->get_where('facturacion', array('ref_id' => $venta->venta_id))->result();
        }

        return $ventas;
    }

    function save_emisor($data)
    {
        $this->db->empty_table('facturacion_emisor');
        $this->db->insert('facturacion_emisor', $data);
    }

    function get_emisor()
    {
        $result = $this->db->get('facturacion_emisor')->row();
        if ($result != NULL) {
            $result->moneda_simbolo = 'S/';
            $result->moneda_letra = 'SOLES';
            return $result;
        }
        return NULL;
    }

    function getFacturador()
    {
        $emisor = $this->db
            ->join('estados', 'estados.estados_id = facturacion_emisor.departamento_id')
            ->join('ciudades', 'ciudades.ciudad_id = facturacion_emisor.provincia_id')
            ->join('distrito', 'distrito.id = facturacion_emisor.distrito_id')
            ->get('facturacion_emisor')->row();

        if ($emisor == NULL) {
            return FALSE;
        }

        $facturador = new Facturador(array(
            'NRO_DOCUMENTO' => $emisor->ruc,
            'RAZON_SOCIAL' => $emisor->razon_social,
            'NOMBRE_COMERCIAL' => $emisor->nombre_comercial,
            'DIRECCION' => $emisor->direccion,
            'UBIGEO' => $emisor->ubigeo,
            'URBANIZACION' => '-',
            'DISTRITO' => strtoupper($emisor->nombre),
            'PROVINCIA' => strtoupper($emisor->ciudad_nombre),
            'DEPARTAMENTO' => strtoupper($emisor->estados_nombre),
            'PAIS_CODIGO' => 'PE',
            'CERT_PASS' => $emisor->pass_sign,
            'SOL_USER' => $emisor->user_sol,
            'SOL_PASS' => $emisor->pass_sol,
            'ENV' => $emisor->env,
            'PATH_QR' => './recursos/qr/',
        ));

        return $facturador;
    }

    function getEstado($ticket, $data)
    {
        $facturador = $this->getFacturador();
        return $facturador->getEstado($ticket, $data);
    }

    function getEstadoResumen($resumen_id)
    {

        $facturador = $this->getFacturador();

        $resumen = $this->db->get_where('facturacion_resumen', array('id' => $resumen_id))->row();
        if ($resumen != NULL) {

            if ($resumen->estado == 2) {

                $response = $facturador->getEstado($resumen->ticket, array(
                    'FECHA_EMISION' => $resumen->fecha,
                    'CORRELATIVO' => $resumen->correlativo
                ));

                $codigo = $response['CODIGO'];

                if ($codigo == '0') {
                    $estado = 3;
                } elseif ($codigo == '9999' || $codigo == '-3') {
                    $estado = 2;
                } else {
                    $estado = 4;
                }

                $this->db->where('id', $resumen_id);
                $this->db->update('facturacion_resumen', array(
                    'estado' => $estado,
                    'sunat_codigo' => $response['CODIGO'],
                    'hash_cdr' => isset($response['HASH_CDR']) ? $response['HASH_CDR'] : null,
                    'nota' => $response['MENSAJE']
                ));

                $resumen_detalles = $this->db->join('facturacion', 'facturacion.id = facturacion_resumen_comprobantes.comprobante_id')
                    ->get_where('facturacion_resumen_comprobantes', array(
                        'resumen_id' => $resumen_id
                    ))->result();


                foreach ($resumen_detalles as $detalle) {
                    $response_msg = 'El Comprobante numero ' . $detalle->documento_numero .
                        ', ha sido aceptado por el resumen 
                                RC-' . date('Ymd', strtotime($resumen->fecha)) . '-' . $resumen->correlativo;
                    if ($codigo != '0') {
                        $response_msg = $response['MENSAJE'];
                    }

                    if ($estado == 4) {
                        $pre_fact = $this->db->get_where('facturacion', array('id' => $detalle->comprobante_id))->row();
                        $this->venta_new_model->anular_venta_rechazada($pre_fact->ref_id);
                    }

                    $this->db->where('id', $detalle->comprobante_id);
                    $this->db->update('facturacion', array(
                        'estado' => $estado,
                        'nota' => $response_msg,
                        'hash_cdr' => isset($response['HASH_CDR']) ? $response['HASH_CDR'] : null,
                        'fecha_cdr' => isset($response['FECHA_CDR']) ? $response['FECHA_CDR'] : date('Y-m-d H:i:s')
                    ));
                }

                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function getCdr($id)
    {
        $facturador = $this->getFacturador();

        if ($facturador === FALSE) {
            $this->db->where('id', $id);
            $this->db->update('facturacion', array(
                'sunat_codigo' => '-2',
                'hash_cpe' => null,
                'nota' => 'Emisor no configurado',
                'estado' => 0
            ));

            return FALSE;
        }

        $comprobante = $this->db->get_where('facturacion', array('id' => $id))->row();

        return $facturador->getEstadoCDR(array(
            'TIPO_DOCUMENTO' => $comprobante->documento_tipo,
            'NUMERO_DOCUMENTO' => $comprobante->documento_numero,
        ));
    }

    function get_tipo_cambio()
    {
        require_once(APPPATH . 'libraries/TipoCambioSunat/TipoCambioSunat.php');
        $tipo_cambio = new TipoCambioSunat();

        $result = $tipo_cambio->consultarTipoCambio();
        return isset($result[0]) ? $result[0] : null;
    }

    function get_nota_credito_motivo($codigo = false)
    {
        return TIPO_NOTA_CREDITO::get($codigo);
    }

    function emitirXml($id)
    {


        $this->db->trans_begin();

        $facturador = $this->getFacturador();

        if ($facturador === FALSE) {
            $this->db->where('id', $id);
            $this->db->update('facturacion', array(
                'sunat_codigo' => '-2',
                'hash_cpe' => null,
                'nota' => 'Emisor no configurado',
                'estado' => 0
            ));

            return FALSE;
        }

        $pre_fact = $this->db->get_where('facturacion', array('id' => $id))->row();

        if ($pre_fact->hash_cdr != null) {
            $this->db->where('id', $id);
            $this->db->update('facturacion', array(
                'estado' => 3
            ));

            return FALSE;
        }

        $resumen = $this->db->join('facturacion_resumen_comprobantes', 'facturacion_resumen_comprobantes.resumen_id = facturacion_resumen.id')
            ->get_where('facturacion_resumen', array(
                'facturacion_resumen_comprobantes.comprobante_id' => $pre_fact->id
            ))->row();

        if ($resumen == null) {

            if ($pre_fact->estado == 2) {
                $response = $this->getCdr($id);
            } else {
                $this->db->where('id', $id);
                $this->db->update('facturacion', array(
                    'nota' => 'El comprobante esta enviado',
                    'estado' => 2
                ));

                $comprobante = $this->db->get_where('facturacion', array('id' => $id))->row();

                $response = $facturador->enviarComprobante($comprobante->documento_tipo, array(
                    'NUMERO_DOCUMENTO' => $comprobante->documento_numero
                ));
            }


            $codigo = $response['CODIGO'];

            if ($codigo == '0') {
                $estado = 3;
            } elseif ($codigo == '9999' || $codigo == '-3') {
                $estado = 2;
            } else {
                $estado = 4;

                $this->venta_new_model->anular_venta_rechazada($pre_fact->ref_id);
            }

            $this->db->where('id', $id);
            $this->db->update('facturacion', array(
                'sunat_codigo' => $response['CODIGO'],
                'hash_cdr' => isset($response['HASH_CDR']) ? $response['HASH_CDR'] : null,
                'nota' => $response['MENSAJE'],
                'estado' => $estado,
                'fecha_cdr' => isset($response['FECHA_CDR']) ? $response['FECHA_CDR'] : date('Y-m-d H:i:s')
            ));

        } else {
            $this->getEstadoResumen($resumen->id);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    function crearXml($id)
    {

        $facturador = $this->getFacturador();

        if ($facturador === FALSE) {
            $this->db->where('id', $id);
            $this->db->update('facturacion', array(
                'sunat_codigo' => '-2',
                'hash_cpe' => null,
                'nota' => 'Emisor no configurado',
                'estado' => 0
            ));

            return FALSE;
        }

        $comprobante = $this->db->get_where('facturacion', array('id' => $id))->row();
        $comprobante_detalle = $this->db->get_where('facturacion_detalle', array('facturacion_id' => $id))->result();

        $cabecera = array(
            'FECHA_EMISION' => date('Y-m-d', strtotime($comprobante->fecha)),
            'HORA_EMISION' => date('H:i:s', strtotime($comprobante->fecha)),
            'TIPO_DOCUMENTO' => $comprobante->documento_tipo,
            'NUMERO_DOCUMENTO' => $comprobante->documento_numero,
            'NOTA_NUMERO_DOCUMENTO' => $comprobante->documento_mod_numero,
            'NOTA_TIPO_DOCUMENTO' => $comprobante->documento_mod_tipo,
            'NOTA_MOTIVO_CODIGO' => $comprobante->documento_mod_motivo,
            'NOTA_MOTIVO_DESCRIPCION' => TIPO_NOTA_CREDITO::get($comprobante->documento_mod_motivo),
            'CLIENTE_NRO_DOCUMENTO' => $comprobante->cliente_identificacion,
            'CLIENTE_TIPO_IDENTIDAD' => $comprobante->cliente_tipo,
            'CLIENTE_NOMBRE' => $comprobante->cliente_nombre,
            'CODIGO_MONEDA' => 'PEN',
            'TOTAL_GRAVADAS' => $comprobante->total_gravadas,
            'TOTAL_INAFECTAS' => $comprobante->total_inafectas,
            'TOTAL_EXONERADAS' => $comprobante->total_exoneradas,
            'TOTAL_GRATUITAS' => '0.00',
            'TOTAL_DESCUENTOS' => '0.00',
            'TOTAL_TRIBUTO_IGV' => $comprobante->impuesto,
            'TOTAL_TRIBUTO_ISC' => '0.00',
            'TOTAL_TRIBUTO_OTROS' => '0.00',
            'TOTAL_DESCUENTO_GLOBAL' => '0.00',
            'TOTAL_OTROS_CARGOS' => '0.00',
            'SUBTOTAL_VENTA' => $comprobante->subtotal,
            'TOTAL_VENTA' => $comprobante->total,
            'TOTAL_VENTA_LETRAS' => Numeroletra::convertir($comprobante->total)
        );

        $items_detalle = array();

        foreach ($comprobante_detalle as $d) {
            $items_detalle[] = array(
                'CODIGO' => $d->producto_codigo,
                'CANTIDAD' => $d->cantidad,
                'UNIDAD_MEDIDA' => "NIU",
                'DESCRIPCION' => $d->producto_descripcion,
                'PRECIO_VALOR' => $d->precio - ($d->impuesto / $d->cantidad),
                'PRECIO_VENTA' => $d->precio,
                'TIPO_PRECIO' => $d->tipo_precio,
                'DETALLE_TRIBUTO_IGV' => $d->impuesto,
                'TIPO_TRIBUTO_IGV' => $d->tipo_tributo,
            );
        }

        $response = $facturador->crearComprobante($comprobante->documento_tipo, $cabecera, $items_detalle);

        $this->db->where('id', $id);
        $this->db->update('facturacion', array(
            'sunat_codigo' => $response['CODIGO'],
            'hash_cpe' => isset($response['HASH_CPE']) ? $response['HASH_CPE'] : null,
            'nota' => $response['MENSAJE'],
            'estado' => $response['CODIGO'] == 0 ? 1 : 0
        ));

        return TRUE;
    }

    function enviarResumenBoletas($data)
    {
        $data['tipo_documento'] = '03';
        $boletas = $this->get_comprobantes_generados($data);

        $facturador = $this->getFacturador();

        if ($facturador === FALSE) {
            return FALSE;
        }

        $resumen = $this->db->order_by('id', 'desc')->get_where('facturacion_resumen', array(
            'fecha' => date('Y-m-d')
        ))->row();

        if ($resumen != NULL) {
            $correlativo = ($resumen->correlativo + 1);
        } else {
            $correlativo = 1;
        }

        $cabecera = array(
            'FECHA_EMISION' => date('Y-m-d'),
            'FECHA_REFERENCIA' => date('Y-m-d', strtotime($data['fecha'])),
            'CORRELATIVO' => $correlativo,
        );

        $detalles = array();
        foreach ($boletas as $comprobante) {

            $detalles[] = array(
                'FECHA_EMISION' => date('Y-m-d', strtotime($comprobante->fecha)),
                'TIPO_DOCUMENTO' => $comprobante->documento_tipo,
                'NUMERO_DOCUMENTO' => $comprobante->documento_numero,
                'NOTA_NUMERO_DOCUMENTO' => $comprobante->documento_mod_numero,
                'NOTA_TIPO_DOCUMENTO' => $comprobante->documento_mod_tipo,
                'ESTADO_ITEM' => $comprobante->estado_comprobante,
                'CLIENTE_NRO_DOCUMENTO' => $comprobante->cliente_identificacion,
                'CLIENTE_TIPO_IDENTIDAD' => $comprobante->cliente_tipo,
                'CLIENTE_NOMBRE' => $comprobante->cliente_nombre,
                'CODIGO_MONEDA' => 'PEN',
                'TOTAL_GRAVADAS' => $comprobante->total_gravadas,
                'TOTAL_INAFECTAS' => $comprobante->total_inafectas,
                'TOTAL_EXONERADAS' => $comprobante->total_exoneradas,
                'TOTAL_GRATUITAS' => '0.00',
                'TOTAL_DESCUENTOS' => '0.00',
                'TOTAL_TRIBUTO_IGV' => $comprobante->impuesto,
                'TOTAL_TRIBUTO_ISC' => '0.00',
                'TOTAL_TRIBUTO_OTROS' => '0.00',
                'TOTAL_DESCUENTO_GLOBAL' => '0.00',
                'TOTAL_OTROS_CARGOS' => '0.00',
                'SUBTOTAL_VENTA' => $comprobante->total,
                'TOTAL_VENTA' => $comprobante->total
            );
        }

        $response = $facturador->enviarResumen($cabecera, $detalles);

        if ($response['CODIGO'] == 0) {

            $this->db->insert('facturacion_resumen', array(
                'fecha' => date('Y-m-d H:i:s'),
                'fecha_ref' => date('Y-m-d H:i:s', strtotime($data['fecha'] . " " . date('H:i:s'))),
                'correlativo' => $correlativo,
                'estado' => 2,
                'nota' => $response['MENSAJE'],
                'sunat_codigo' => 0,
                'hash_cpe' => $response['HASH_CPE'],
                'hash_cdr' => null,
                'ticket' => $response['TICKET']
            ));
            $resumen_id = $this->db->insert_id();

            foreach ($boletas as $comprobante) {
                $this->db->insert('facturacion_resumen_comprobantes', array(
                    'comprobante_id' => $comprobante->id,
                    'resumen_id' => $resumen_id,
                    'estado' => $comprobante->estado_comprobante
                ));

                $this->db->where('id', $comprobante->id);
                $this->db->update('facturacion', array(
                    'estado' => 2,
                    'nota' => 'El comprobante ha sido enviado con el resumen RC-' . date('Ymd') . '-' .
                        $correlativo . ' y se encuentra pendiente. Numero de ticket ' . $response['TICKET']
                ));
            }
        }

        return $response;
    }

    function facturarVenta($venta_id)
    {

        $venta = $this->venta_new_model->get_venta_detalle($venta_id);

        $tipo_doc = '';
        $numero_comprobante = '';
        if ($venta->documento_id == 3) {
            $numero_comprobante = 'B' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;
        }
        if ($venta->documento_id == 1) {
            $numero_comprobante = 'F' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$FACTURA;
        }

        $tipo_identidad = '';
        if ($venta->cliente_tipo_identificacion == 1)
            $tipo_identidad = TIPO_IDENTIDAD::$DNI;
        if ($venta->cliente_tipo_identificacion == 2)
            $tipo_identidad = TIPO_IDENTIDAD::$RUC;

        $cambio_dolar = 1;
        if ($venta->moneda_id != MONEDA_DEFECTO) {
            $cambio = $this->get_tipo_cambio();
            if ($cambio != null) {
                $cambio_dolar = $cambio->venta;
            } else {
                log_message('error', 'Facturacion. No se ha podido recuperar el cambio de dolar');
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'No se ha podido recuperar el cambio de dolar'
                );
            }
        }

        $total_gravadas = 0;
        $total_exoneradas = 0;
        $total_inafectas = 0;
        $total_impuesto = 0;

        foreach ($venta->detalles as $d) {

            $factor = (100 + $d->impuesto_porciento) / 100;
            $subtotal = $venta->tipo_impuesto == 1 ? ($d->cantidad * $d->precio / $factor) : ($d->cantidad * $d->precio);

            // Sumo los subtotales dependiendo su tipo de operacion
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $total_gravadas += $subtotal;

                // Calculo el total de los impuestos y normalizo el precio al de venta
                if ($venta->tipo_impuesto == 1) {
                    $total_impuesto += ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $total_impuesto += (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }

            if ($d->afectacion_impuesto == OP_EXONERADA) {
                $total_exoneradas += ($d->cantidad * $d->precio);
            }

            if ($d->afectacion_impuesto == OP_INAFECTA) {
                $total_inafectas += ($d->cantidad * $d->precio);
            }
        }

        $subtotal = $total_gravadas + $total_exoneradas + $total_inafectas;

        // Guardo el comprobante electronico
        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => date('Y-m-d H:i:s', strtotime($venta->fecha_facturacion)),
            'documento_tipo' => $tipo_doc,
            'documento_numero' => $numero_comprobante,
            'documento_mod_tipo' => '',
            'documento_mod_numero' => '',
            'documento_mod_motivo' => '',
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,
            'subtotal' => $subtotal * $cambio_dolar,
            'impuesto' => $total_impuesto * $cambio_dolar,
            'total' => ($subtotal + $total_impuesto) * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->venta_id,
        ));

        $facturacion_id = $this->db->insert_id();

        foreach ($venta->detalles as $d) {

            $tipo_tributo = '10';
            // Calculo el impuesto por producto
            $impuesto = 0;
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $tipo_tributo = '10';
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                    $d->precio += ((($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio)) / $d->cantidad;
                }
            }

            if ($d->afectacion_impuesto == OP_EXONERADA) {
                $tipo_tributo = '20';
            }

            if ($d->afectacion_impuesto == OP_INAFECTA) {
                $tipo_tributo = '30';
            }

            //Viene de la configuracion de la venta item VALOR_COMPROBANTE
            if (valueOptionDB('VALOR_COMPROBANTE', 'NOMBRE') == 'NOMBRE') {
                $producto_descripcion = $d->producto_nombre;
            } else {
                $producto_descripcion = $d->producto_descripcion;
                if (empty($producto_descripcion)) {
                    $producto_descripcion = $d->producto_nombre;
                }
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue(sumCod($d->producto_id, 4), $d->producto_codigo_interno),
                'producto_descripcion' => $producto_descripcion,
                'um' => $d->unidad_abr,
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar,
                'tipo_precio' => '01',
                'tipo_tributo' => $tipo_tributo
            ));
        }

        return $this->crearXml($facturacion_id);
    }

    function facturarVenta_shadow($id_shadow)
    {
        $this->load->model('venta_shadow/venta_shadow_model');
        log_message('debug', 'Facturacion Electronica. Guardando venta ' . $id_shadow);
        $venta = $this->venta_shadow_model->get_venta_detalle($id_shadow);

        $tipo_doc = '';
        $numero_comprobante = '';
        if ($venta->documento_id == 3) {
            $numero_comprobante = 'B' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;
        }
        if ($venta->documento_id == 1) {
            $numero_comprobante = 'F' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$FACTURA;
        }

        $tipo_identidad = '';
        if ($venta->cliente_tipo_identificacion == 1)
            $tipo_identidad = TIPO_IDENTIDAD::$DNI;
        if ($venta->cliente_tipo_identificacion == 2)
            $tipo_identidad = TIPO_IDENTIDAD::$RUC;

        $cambio_dolar = 1;
        if ($venta->moneda_id != MONEDA_DEFECTO) {
            $cambio = $this->get_tipo_cambio();
            if ($cambio != null) {
                $cambio_dolar = $cambio->venta;
            } else {
                log_message('error', 'Facturacion error, No se ha podido recuperar el cambio de dolar');
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'No se ha podido recuperar el cambio de dolar'
                );
            }
        }

        $total_gravadas = 0;
        $total_exoneradas = 0;
        $total_inafectas = 0;
        $total_impuesto = 0;

        foreach ($venta->detalles as $d) {

            $factor = (100 + $d->impuesto_porciento) / 100;
            $subtotal = $venta->tipo_impuesto == 1 ? ($d->cantidad * $d->precio / $factor) : ($d->cantidad * $d->precio);

            // Sumo los subtotales dependiendo su tipo de operacion
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $total_gravadas += $subtotal;

                // Calculo el total de los impuestos y normalizo el precio al de venta
                if ($venta->tipo_impuesto == 1) {
                    $total_impuesto += ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $total_impuesto += (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }

            if ($d->afectacion_impuesto == OP_EXONERADA) {
                $total_exoneradas += ($d->cantidad * $d->precio);
            }

            if ($d->afectacion_impuesto == OP_INAFECTA) {
                $total_inafectas += ($d->cantidad * $d->precio);
            }
        }

        $subtotal = $total_gravadas + $total_exoneradas + $total_inafectas;

        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => date('Y-m-d', strtotime($venta->fecha_facturacion)),
            'documento_tipo' => $tipo_doc,
            'documento_numero' => $numero_comprobante,
            'documento_mod_tipo' => '',
            'documento_mod_numero' => '',
            'documento_mod_motivo' => '',
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,
            'subtotal' => $subtotal * $cambio_dolar,
            'impuesto' => $total_impuesto * $cambio_dolar,
            'total' => ($subtotal + $total_impuesto) * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->id_venta,
        ));

        $facturacion_id = $this->db->insert_id();

        $this->db->where("id", $venta->id);
        $this->db->update("venta_shadow", array("id_factura" => $facturacion_id));

        foreach ($venta->detalles as $d) {

            $tipo_tributo = '10';
            // Calculo el impuesto por producto
            $impuesto = 0;
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $tipo_tributo = '10';
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                    $d->precio += ((($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio)) / $d->cantidad;
                }
            }

            if ($d->afectacion_impuesto == OP_EXONERADA) {
                $tipo_tributo = '20';
            }

            if ($d->afectacion_impuesto == OP_INAFECTA) {
                $tipo_tributo = '30';
            }

            //Viene de la configuracion de la venta item VALOR_COMPROBANTE
            if (valueOption('VALOR_COMPROBANTE', 'NOMBRE') == 'NOMBRE') {
                $producto_descripcion = $d->producto_nombre;
            } else {
                $producto_descripcion = $d->producto_descripcion;
                if (empty($producto_descripcion)) {
                    $producto_descripcion = $d->producto_nombre;
                }
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue(sumCod($d->producto_id, 4), $d->producto_codigo_interno),
                'producto_descripcion' => $producto_descripcion,
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar,
                'tipo_precio' => '01',
                'tipo_tributo' => $tipo_tributo
            ));
        }

        return $this->crearXml($facturacion_id);
    }

    function enviarBaja($id)
    {
        $facturador = $this->getFacturador();

        if ($facturador === FALSE) {
            return false;
        }

        $baja = $this->db->get_where('facturacion_baja', array('id' => $id))->row();
        $baja_detalles = $this->db->join('facturacion', 'facturacion.id = facturacion_baja_comprobantes.comprobante_id')
            ->get_where('facturacion_baja_comprobantes', array('baja_id' => $id))->result();

        foreach ($baja_detalles as $detalle) {
            if ($detalle->estado != 3) {
                $this->db->where('baja_id', $id);
                $this->db->where('comprobante_id', $detalle->comprobante_id);
                $this->db->delete('facturacion_baja_comprobantes');

                $this->db->where('id', $detalle->comprobante_id);
                $this->db->update('facturacion', array('estado_comprobante' => 1));
            }
        }

        $baja_detalles = $this->db->join('facturacion', 'facturacion.id = facturacion_baja_comprobantes.comprobante_id')
            ->get_where('facturacion_baja_comprobantes', array('baja_id' => $id))->result();

        if (count($baja_detalles) > 0) {

            $cabecera = array(
                'FECHA_EMISION' => date('Y-m-d', strtotime($baja->fecha_emision)),
                'NUMERO_DOCUMENTO' => $baja->correlativo,
                'FECHA_REFERENCIA' => date('Y-m-d', strtotime($baja_detalles[0]->fecha))
            );

            $detalles = array();

            foreach ($baja_detalles as $bd) {
                $detalles[] = array(
                    'TIPO_DOCUMENTO' => $bd->documento_tipo,
                    'DOCUMENTO_BAJA_SERIE' => explode('-', $bd->documento_numero)[0],
                    'DOCUMENTO_BAJA_NUMERO' => explode('-', $bd->documento_numero)[1],
                    'BAJA_DESCRIPCION' => $bd->motivo,
                );
            }

            $response = $facturador->enviarBaja($cabecera, $detalles);

            return $response;

        } else {
            $this->db->where('id', $id);
            $this->db->delete('facturacion_baja');
            return FALSE;
        }
    }

    function anularComprobante($venta_id, $motivo)
    {
        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        $facturacion = $this->db->get_where('facturacion', array(
            'documento_tipo' => sumCod($venta->id_documento, 2),
            'ref_id' => $venta->venta_id
        ))->row();

        // Solo se pueden anular boletas emitidas y aceptadas
        if ($facturacion->documento_tipo == '03' && $facturacion->estado == 3) {
            $this->db->where('id', $facturacion->id);
            $this->db->update('facturacion', array(
                'estado' => 1,
                'estado_comprobante' => 3,
                'hash_cdr' => null,
                'sunat_codigo' => 0,
                'nota' => 'Este combrobante ha sido anulado'
            ));

            return TRUE;
        }


        if ($facturacion->documento_tipo == '01' && ($facturacion->estado == 1 || $facturacion->estado == 3)) {
            // Si la factura esta en estado generado la emito para luego hacer su posterior comunicacion de baja
            if ($facturacion->estado == 1) {
                if ($this->emitirXml($facturacion->id) === FALSE) {
                    return FALSE;
                }
            }

            $facturacion = $this->db->get_where('facturacion', array('id' => $facturacion->id))->row();
            if ($facturacion->estado == 3) {

                $baja = $this->db->order_by('id', 'desc')->get_where('facturacion_baja', array(
                    'fecha_emision' => date('Y-m-d')
                ))->row();

                if ($baja != NULL) {
                    $correlativo = ($baja->correlativo + 1);
                } else {
                    $correlativo = 1;
                }

                $this->db->insert('facturacion_baja', array(
                    'fecha_emision' => date('Y-m-d'),
                    'correlativo' => $correlativo,
                    'estado' => 0,
                    'nota' => 'No enviado'
                ));

                $baja_id = $this->db->insert_id();

                $this->db->insert('facturacion_baja_comprobantes', array(
                    'comprobante_id' => $facturacion->id,
                    'baja_id' => $baja_id,
                    'motivo' => $motivo
                ));

                $response = $this->enviarBaja($baja_id);

                if ($response !== FALSE) {
                    $this->db->where('id', $baja_id);
                    $this->db->update('facturacion_baja', array(
                        'nota' => $response['MENSAJE'],
                        'sunat_codigo' => $response['CODIGO'],
                        'hash_cpe' => $response['HASH_CPE'],
                        'hash_cdr' => null,
                        'ticket' => $response['TICKET'],
                        'estado' => 2
                    ));

                    $this->db->where('id', $facturacion->id);
                    $this->db->update('facturacion', array('estado_comprobante' => 3));
                } else {
                    return FALSE;
                }

                return TRUE;
            } else {
                if ($facturacion->estado == 4)
                    return TRUE;
                return FALSE;
            }
        }
    }

    function notaCreditoVenta($nc_id)
    {
        $nc = $this->db->get_where('notas_credito', array('id' => $nc_id))->row();
        $nc_detalles = $this->db->get_where('notas_credito_detalle', array('notas_credito_id' => $nc_id))->result();
        $venta = $this->venta_new_model->get_venta_detalle($nc->venta_id);

        $tipo_doc = '';
        $numero_comprobante = '';
        if ($venta->documento_id == 3) {
            $numero_comprobante = 'B' . $venta->serie . '-' . $venta->numero;
            $numero = 'B' . $nc->serie . '-' . intval($nc->numero);
            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;
        }
        if ($venta->documento_id == 1) {
            $numero_comprobante = 'F' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$FACTURA;
            $numero = 'F' . $nc->serie . '-' . intval($nc->numero);
        }

        $tipo_identidad = '';
        if ($venta->cliente_tipo_identificacion == 1)
            $tipo_identidad = TIPO_IDENTIDAD::$DNI;
        if ($venta->cliente_tipo_identificacion == 2)
            $tipo_identidad = TIPO_IDENTIDAD::$RUC;

        $cambio_dolar = 1;
        if ($venta->moneda_id != MONEDA_DEFECTO) {
            $cambio = $this->get_tipo_cambio();
            if ($cambio != null) {
                $cambio_dolar = $cambio->venta;
            } else {
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'No se ha podido recuperar el cambio de dolar'
                );
            }
        }

        $total_gravadas = 0;
        $total_exoneradas = 0;
        $total_inafectas = 0;
        $total_impuesto = 0;

        foreach ($nc_detalles as $nc_detalle) {

            $d = $this->db->get_where('detalle_venta', array('id_detalle' => $nc_detalle->detalle_id))->row();

            $factor = (100 + $d->impuesto_porciento) / 100;
            $subtotal = $venta->tipo_impuesto == 1 ? ($nc_detalle->cantidad * $nc_detalle->precio / $factor) : ($nc_detalle->cantidad * $nc_detalle->precio);

            // Sumo los subtotales dependiendo su tipo de operacion
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $total_gravadas += $subtotal;

                // Calculo el total de los impuestos y normalizo el precio al de venta
                if ($venta->tipo_impuesto == 1) {
                    $total_impuesto += ($nc_detalle->cantidad * $nc_detalle->precio) - (($nc_detalle->cantidad * $nc_detalle->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $total_impuesto += (($nc_detalle->cantidad * $nc_detalle->precio) * $factor) - ($nc_detalle->cantidad * $nc_detalle->precio);
                }
            }

            if ($d->afectacion_impuesto == OP_EXONERADA) {
                $total_exoneradas += $subtotal;
            }

            if ($d->afectacion_impuesto == OP_INAFECTA) {
                $total_inafectas += $subtotal;
            }
        }

        $subtotal = $total_gravadas + $total_exoneradas + $total_inafectas;

        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => date('Y-m-d'),
            'documento_tipo' => TIPO_COMPROBANTE::$NOTA_CREDITO,
            'documento_numero' => $numero,
            'documento_mod_tipo' => $tipo_doc,
            'documento_mod_numero' => $numero_comprobante,
            'documento_mod_motivo' => $nc->motivo_codigo,
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,
            'subtotal' => $subtotal * $cambio_dolar,
            'impuesto' => $total_impuesto * $cambio_dolar,
            'total' => ($subtotal + $total_impuesto) * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->venta_id,
        ));

        $facturacion_id = $this->db->insert_id();

        foreach ($nc_detalles as $nc_detalle) {

            $d = $this->db
                ->join('producto', 'producto.producto_id = detalle_venta.id_producto')
                ->get_where('detalle_venta', array('id_detalle' => $nc_detalle->detalle_id))->row();
            $unidad = $this->db->get_where('unidades', array('id_unidad' => $d->unidad_medida))->row();

            $impuesto = 0;
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $impuesto = ($nc_detalle->cantidad * $nc_detalle->precio) - (($nc_detalle->cantidad * $nc_detalle->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $impuesto = (($nc_detalle->cantidad * $nc_detalle->precio) * $factor) - ($nc_detalle->cantidad * $nc_detalle->precio);
                    $nc_detalle->precio += ((($nc_detalle->cantidad * $nc_detalle->precio) * $factor) - ($nc_detalle->cantidad * $nc_detalle->precio)) / $nc_detalle->cantidad;
                }
            }

            if (valueOptionDB('VALOR_COMPROBANTE', 'NOMBRE') == 'NOMBRE') {
                $producto_descripcion = $d->producto_nombre;
            } else {
                $producto_descripcion = $d->producto_descripcion;
                if (empty($producto_descripcion)) {
                    $producto_descripcion = $d->producto_nombre;
                }
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue($d->producto_id, $d->producto_codigo_interno),
                'producto_descripcion' => $producto_descripcion,
                'um' => $unidad->abreviatura,
                'cantidad' => $nc_detalle->cantidad,
                'precio' => $nc_detalle->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->crearXml($facturacion_id);
    }

    function convertirNotaPedido($venta_id, $tipo_documento, $fecha_facturacion, $descuento)
    {

        if ($tipo_documento == '99') {
            return $this->crearBoletasMultiples($venta_id, $fecha_facturacion, $descuento);
        }

        $this->load->model('correlativos/correlativos_model');
        log_message('debug', 'Facturacion Electronica. Guardando venta ' . $venta_id);
        $venta = $this->venta_new_model->get_venta_detalle($venta_id);

        $tipo_doc = '';
        $numero_comprobante = '';
        if ($tipo_documento == '03') {
            $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 3);
            $this->correlativos_model->sumar_correlativo($venta->local_id, 3);
            $numero_comprobante = 'B' . $correlativo->serie . '-' . $correlativo->correlativo;
            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;
        }
        if ($tipo_documento == '01') {
            $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 1);
            $this->correlativos_model->sumar_correlativo($venta->local_id, 1);
            $numero_comprobante = 'F' . $correlativo->serie . '-' . $correlativo->correlativo;
            $tipo_doc = TIPO_COMPROBANTE::$FACTURA;
        }

        $tipo_identidad = '';
        if ($venta->cliente_tipo_identificacion == 1)
            $tipo_identidad = TIPO_IDENTIDAD::$DNI;
        if ($venta->cliente_tipo_identificacion == 2)
            $tipo_identidad = TIPO_IDENTIDAD::$RUC;

        $cambio_dolar = 1;
        if ($venta->moneda_id != MONEDA_DEFECTO) {
            $cambio = $this->get_tipo_cambio();
            if ($cambio != null) {
                $cambio_dolar = $cambio->venta;
            } else {
                log_message('error', 'Facturacion error, No se ha podido recuperar el cambio de dolar');
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'No se ha podido recuperar el cambio de dolar'
                );
            }
        }

        $descuento = $descuento > 0 ? $descuento : 0;

        $total_gravadas = 0;
        $total_exoneradas = 0;
        $total_inafectas = 0;
        $total_impuesto = 0;
        $total_subtotal = 0;
        $total_venta = 0;

        foreach ($venta->detalles as $d) {
            if ($descuento > 0) {
                $importe = ($d->cantidad * $d->precio);
                $desc = ($importe * $descuento / 100);
                $d->precio = ($importe - $desc) / $d->cantidad;
            }


            $factor = (100 + $d->impuesto_porciento) / 100;
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                if ($venta->tipo_impuesto == 1) {
                    $total_gravadas += $d->cantidad * $d->precio / $factor;
                } else {
                    $total_gravadas += $d->cantidad * $d->precio;
                }
            }

            if ($d->afectacion_impuesto == OP_EXONERADA) {
                if ($venta->tipo_impuesto == 1) {
                    $total_exoneradas += $d->cantidad * $d->precio / $factor;
                } else {
                    $total_exoneradas += $d->cantidad * $d->precio;
                }
            }

            if ($d->afectacion_impuesto == OP_INAFECTA) {
                if ($venta->tipo_impuesto == 1) {
                    $total_inafectas += $d->cantidad * $d->precio / $factor;
                } else {
                    $total_inafectas += $d->cantidad * $d->precio;
                }
            }

            $total_venta += $d->cantidad * $d->precio;

            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $total_impuesto += ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $total_impuesto += (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }
        }

        if ($venta->tipo_impuesto == 1) {
            $total_subtotal = $total_venta - $total_impuesto;
        } elseif ($venta->tipo_impuesto == 2) {
            $total_subtotal = $total_venta;
            $total_venta = $total_subtotal + $total_impuesto;
        }

        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => $fecha_facturacion,
            'documento_tipo' => $tipo_doc,
            'documento_numero' => $numero_comprobante,
            'documento_mod_tipo' => '',
            'documento_mod_numero' => '',
            'documento_mod_motivo' => '',
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,
            'subtotal' => $total_subtotal * $cambio_dolar,
            'impuesto' => $total_impuesto * $cambio_dolar,
            'total' => $total_venta * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->venta_id,
            'descuento' => $descuento
        ));

        $facturacion_id = $this->db->insert_id();

        foreach ($venta->detalles as $d) {

            $impuesto = 0;
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                    $d->precio += (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }

            //Viene de la configuracion de la venta item VALOR_COMPROBANTE
            if (valueOption('VALOR_COMPROBANTE', 'NOMBRE') == 'NOMBRE') {
                $producto_descripcion = $d->producto_nombre;
            } else {
                $producto_descripcion = $d->producto_descripcion;
                if (empty($producto_descripcion)) {
                    $producto_descripcion = $d->producto_nombre;
                }
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue(sumCod($d->producto_id, 4), $d->producto_codigo_interno),
                'producto_descripcion' => $producto_descripcion,
                'um' => $d->unidad_abr,
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        $this->db->where('venta_id', $venta_id);
        $this->db->update('venta', array(
            'nota_facturada' => 1
        ));

        return $this->crearXml($facturacion_id);
    }

    function crearBoletasMultiples($venta_id, $fecha_facturacion, $descuento)
    {
        $this->load->model('correlativos/correlativos_model');
        $this->load->model('facturacion/picado_model');
        log_message('debug', 'Facturacion Electronica. Guardando venta ' . $venta_id);
        $venta = $this->venta_new_model->get_venta_detalle($venta_id);

        $productos = array();

        foreach ($venta->detalles as $detalle) {

            $temp = new stdClass();
            $temp->id = $detalle->producto_id;
            $temp->um_id = $detalle->unidad_id;
            $temp->precio = $detalle->precio;
            $temp->cantidad = $detalle->cantidad;
            $productos[] = $temp;
        }


        $response = $this->picado_model->split($productos);

        if ($response["CODIGO"] == '0') {

            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;

            $tipo_identidad = '';
            if ($venta->cliente_tipo_identificacion == 1)
                $tipo_identidad = TIPO_IDENTIDAD::$DNI;
            if ($venta->cliente_tipo_identificacion == 2)
                $tipo_identidad = TIPO_IDENTIDAD::$RUC;

            $cambio_dolar = 1;
            if ($venta->moneda_id != MONEDA_DEFECTO) {
                $cambio = $this->get_tipo_cambio();
                if ($cambio != null) {
                    $cambio_dolar = $cambio->venta;
                } else {
                    log_message('error', 'Facturacion error, No se ha podido recuperar el cambio de dolar');
                    return array(
                        'respuesta' => 'error',
                        'msg_validacion' => 'No se ha podido recuperar el cambio de dolar'
                    );
                }
            }

            $descuento = $descuento > 0 ? $descuento : 0;

            foreach ($response['BOLETAS'] as $boleta) {

                $correlativo = $this->correlativos_model->get_correlativo($venta->local_id, 3);
                $this->correlativos_model->sumar_correlativo($venta->local_id, 3);
                $numero_comprobante = 'B' . $correlativo->serie . '-' . $correlativo->correlativo;


                $total_gravadas = 0;
                $total_exoneradas = 0;
                $total_inafectas = 0;
                $total_impuesto = 0;
                $total_subtotal = 0;
                $total_venta = 0;


                foreach ($boleta as $boleta_key => $boleta_val) {
                    $detalle_venta = $this->db->get_where('detalle_venta', array(
                        'id_venta' => $venta->venta_id,
                        'id_producto' => $boleta[$boleta_key]['id'],
                        'unidad_medida' => $boleta[$boleta_key]['um_id']
                    ))->row();


                    if ($descuento > 0) {
                        $importe = ($boleta[$boleta_key]['cantidad'] * $boleta[$boleta_key]['precio']);
                        $desc = ($importe * $descuento / 100);
                        $boleta[$boleta_key]['precio'] = ($importe - $desc) / $boleta[$boleta_key]['cantidad'];
                    }

                    if ($detalle_venta->afectacion_impuesto == OP_GRAVABLE)
                        $total_gravadas += $boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad'];

                    if ($detalle_venta->afectacion_impuesto == OP_EXONERADA)
                        $total_exoneradas += $boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad'];

                    if ($detalle_venta->afectacion_impuesto == OP_INAFECTA)
                        $total_inafectas += $boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad'];

                    $total_venta += $boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad'];

                    if ($detalle_venta->afectacion_impuesto == OP_GRAVABLE) {
                        $factor = (100 + $detalle_venta->impuesto_porciento) / 100;
                        if ($venta->tipo_impuesto == 1) {
                            $total_impuesto += ($boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad']) - (($boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad']) / $factor);
                        } elseif ($venta->tipo_impuesto == 2) {
                            $total_impuesto += (($boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad']) * $factor) - ($boleta[$boleta_key]['precio'] * $boleta[$boleta_key]['cantidad']);
                        }
                    }
                }


                if ($venta->tipo_impuesto == 1) {
                    $total_subtotal = $total_venta - $total_impuesto;
                } elseif ($venta->tipo_impuesto == 2) {
                    $total_subtotal = $total_venta;
                    $total_venta = $total_subtotal + $total_impuesto;
                }

                $this->db->insert('facturacion', array(
                    'local_id' => $venta->local_id,
                    'fecha' => $fecha_facturacion,
                    'documento_tipo' => $tipo_doc,
                    'documento_numero' => $numero_comprobante,
                    'documento_mod_tipo' => '',
                    'documento_mod_numero' => '',
                    'documento_mod_motivo' => '',
                    'cliente_tipo' => $tipo_identidad,
                    'cliente_identificacion' => $venta->ruc,
                    'cliente_nombre' => $venta->cliente_nombre,
                    'cliente_direccion' => $venta->cliente_direccion,
                    'total_gravadas' => $total_gravadas * $cambio_dolar,
                    'total_exoneradas' => $total_exoneradas * $cambio_dolar,
                    'total_inafectas' => $total_inafectas * $cambio_dolar,
                    'subtotal' => $total_subtotal * $cambio_dolar,
                    'impuesto' => $total_impuesto * $cambio_dolar,
                    'total' => $total_venta * $cambio_dolar,
                    'estado' => 0,
                    'nota' => 'No enviado',
                    'ref_id' => $venta->venta_id,
                    'descuento' => $descuento
                ));

                $facturacion_id = $this->db->insert_id();

                foreach ($boleta as $detalle) {

                    $detalle_venta = $this->db
                        ->join('producto', 'producto.producto_id = detalle_venta.id_producto')
                        ->get_where('detalle_venta', array(
                            'id_venta' => $venta->venta_id,
                            'producto_id' => $detalle['id'],
                            'unidad_medida' => $detalle['um_id']
                        ))->row();

//                    var_dump($detalle_venta);
//                    return false;


                    $impuesto = 0;
                    if ($detalle_venta->afectacion_impuesto == OP_GRAVABLE) {
                        $factor = (100 + $detalle_venta->impuesto_porciento) / 100;
                        if ($venta->tipo_impuesto == 1) {
                            $impuesto = ($detalle['precio'] * $detalle['cantidad']) - (($detalle['precio'] * $detalle['cantidad']) / $factor);
                        } elseif ($venta->tipo_impuesto == 2) {
                            $impuesto = (($detalle['precio'] * $detalle['cantidad']) * $factor) - ($detalle['precio'] * $detalle['cantidad']);
                        }
                    }

                    $unidad_medida = $this->db->get_where('unidades', array('id_unidad' => $detalle['um_id']))->row();

                    $this->db->insert('facturacion_detalle', array(
                        'facturacion_id' => $facturacion_id,
                        'producto_codigo' => getCodigoValue(sumCod($detalle['id'], 4), $detalle_venta->producto_codigo_interno),
                        'producto_descripcion' => $detalle_venta->producto_nombre,
                        'um' => $unidad_medida->abreviatura,
                        'cantidad' => $detalle['cantidad'],
                        'precio' => $detalle['precio'] * $cambio_dolar,
                        'impuesto' => $impuesto * $cambio_dolar
                    ));
                }

                $this->crearXml($facturacion_id);
            }

            $this->db->where('venta_id', $venta_id);
            $this->db->update('venta', array(
                'nota_facturada' => 1
            ));
        }

        return TRUE;
    }

}
