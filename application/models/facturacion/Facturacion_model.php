<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class facturacion_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        require_once(APPPATH . 'libraries/FacturacionSunat/FacturacionSunat.php');
        require APPPATH . 'libraries/Numeroletra.php';
    }

    function get_facturacion($where = array())
    {
        $this->db->select('*')->from('facturacion')
            ->join('local', 'local.int_local_id = facturacion.local_id');

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
            if($result->documento_tipo == '07' || $result->documento_tipo == '08'){
                $numero_cero = explode('-', $result->documento_mod_numero);
                $result->documento_mod_numero_ceros = $numero_cero[0] . '-' . sumCod($numero_cero[1], 8);
            }


            $result->total_letra = Numeroletra::convertir($result->total);

            return $result;
        }

        return $this->db->get()->result();
    }

    function save_emisor($data)
    {
        $this->db->empty_table('facturacion_emisor');

        $this->db->insert('facturacion_emisor', $data);
    }

    function get_emisor()
    {
        $result = $this->db->get('facturacion_emisor')->row();
        $result->moneda_simbolo = 'S/';
        $result->moneda_letra = 'SOLES';
        return $result;
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

    function emitir($id)
    {

        $facturacion = new FacturacionSunat();
        $facturacion->qr_path = './recursos/qr/';

        $emisor = $this->db
            ->join('estados', 'estados.estados_id = facturacion_emisor.departamento_id')
            ->join('ciudades', 'ciudades.ciudad_id = facturacion_emisor.provincia_id')
            ->join('distrito', 'distrito.id = facturacion_emisor.distrito_id')
            ->get('facturacion_emisor')->row();

        $comprobante = $this->db->get_where('facturacion', array('id' => $id))->row();
        $comprobante_detalle = $this->db->get_where('facturacion_detalle', array('facturacion_id' => $id))->result();

        $data_comprobante = array(
            'TOTAL_GRAVADAS' => number_format($comprobante->subtotal, 2, '.', ''),
            'TOTAL_INAFECTA' => "0",
            'TOTAL_EXONERADAS' => "0",
            'TOTAL_GRATUITAS' => "0",
            'TOTAL_PERCEPCIONES' => "0",
            'TOTAL_RETENCIONES' => "0",
            'TOTAL_DETRACCIONES' => "0",
            'TOTAL_BONIFICACIONES' => "0",
            'TOTAL_DESCUENTO' => "0",

            'SUB_TOTAL' => number_format($comprobante->subtotal, 2, '.', ''),
            'TOTAL_IGV' => number_format($comprobante->impuesto, 2, '.', ''),
            'TOTAL_ISC' => "0",
            'TOTAL_OTR_IMP' => "0",

            'TOTAL' => number_format($comprobante->total, 2, '.', ''),
            'TOTAL_LETRAS' => Numeroletra::convertir($comprobante->total),
            //==============================================
            'NRO_GUIA_REMISION' => "",
            'COD_GUIA_REMISION' => "",
            'NRO_OTR_COMPROBANTE' => "",
            'COD_OTR_COMPROBANTE' => "",
            //==============================================
            'TIPO_COMPROBANTE_MODIFICA' => $comprobante->documento_mod_tipo,
            'NRO_DOCUMENTO_MODIFICA' => $comprobante->documento_mod_numero,
            'COD_TIPO_MOTIVO' => $comprobante->documento_mod_motivo,
            'DESCRIPCION_MOTIVO' => TIPO_NOTA_CREDITO::get($comprobante->documento_mod_motivo),
            //===============================================
            'NRO_COMPROBANTE' => $comprobante->documento_numero,
            'FECHA_DOCUMENTO' => date("Y-m-d", strtotime($comprobante->fecha)),
            'COD_TIPO_DOCUMENTO' => $comprobante->documento_tipo,
            'COD_MONEDA' => $emisor->moneda,
            //==================================================
            'TIPO_DOCUMENTO_CLIENTE' => $comprobante->cliente_tipo, //RUC
            'NRO_DOCUMENTO_CLIENTE' => $comprobante->cliente_identificacion,
            'RAZON_SOCIAL_CLIENTE' => $comprobante->cliente_nombre,
            'DIRECCION_CLIENTE' => $comprobante->cliente_direccion,
            'CIUDAD_CLIENTE' => 'LIMA',
            'COD_PAIS_CLIENTE' => "PE",
            //===============================================
            'TIPO_DOCUMENTO_EMPRESA' => TIPO_IDENTIDAD::$RUC, //RUC
            'NRO_DOCUMENTO_EMPRESA' => $emisor->ruc,
            'NOMBRE_COMERCIAL_EMPRESA' => $emisor->nombre_comercial,
            'RAZON_SOCIAL_EMPRESA' => $emisor->razon_social,
            'CODIGO_UBIGEO_EMPRESA' => $emisor->ubigeo,
            'DIRECCION_EMPRESA' => $emisor->direccion,
            'DEPARTAMENTO_EMPRESA' => strtoupper($emisor->estados_nombre),
            'PROVINCIA_EMPRESA' => strtoupper($emisor->ciudad_nombre),
            'DISTRITO_EMPRESA' => strtoupper($emisor->nombre),
            'CODIGO_PAIS_EMPRESA' => "PE",
            //====================INFORMACION PARA ANTICIPO=====================//
            'FLG_ANTICIPO' => "0",
            //====================REGULAR ANTICIPO=====================//
            'FLG_REGU_ANTICIPO' => "0",
            'NRO_COMPROBANTE_REF_ANT' => "",
            'MONEDA_REGU_ANTICIPO' => "",
            'MONTO_REGU_ANTICIPO' => "0",
            'TIPO_DOCUMENTO_EMP_REGU_ANT' => "",
            'NRO_DOCUMENTO_EMP_REGU_ANT' => "",
            //===================CLAVES SOL EMISOR====================//
            'EMISOR_RUC' => $emisor->ruc,
            'EMISOR_USUARIO_SOL' => $emisor->user_sol,
            'EMISOR_PASS_SOL' => $emisor->pass_sol,
            'CERTIFICADO_PASS' => $emisor->pass_sign
        );


        $items_detalle = array();

        $n = 1;
        foreach ($comprobante_detalle as $d) {
            $detalle = new stdClass();

            $detalle->txt_item = $n++;
            $detalle->txt_unidad_medida = $d->um;
            $detalle->txt_cantidad = number_format($d->cantidad, 3, '.', '');
            $detalle->txt_precio = number_format($d->precio, 2, '.', '');
            $detalle->txt_importe = number_format($d->cantidad * $d->precio, 2, '.', '');
            $detalle->txt_precio_tipo_codigo = "01";
            $detalle->txt_igv = number_format($d->impuesto, 2, '.', '');
            $detalle->txt_isc = "0";
            $detalle->txt_cod_tipo_operacion = "10";
            $detalle->txt_codigo = $d->producto_codigo;
            $detalle->txt_descripcion = $d->producto_descripcion;

            $items_detalle[] = $detalle;
        }

        $resp = $facturacion->procesarDocumento($comprobante->documento_tipo, $data_comprobante, $items_detalle, FACTURACION_PROCESO);
        if ($resp['respuesta'] == 'ok') {
            $this->db->where('venta_id', $comprobante->ref_id);
            $this->db->update('venta', array(
                'facturacion' => 1,
                'facturacion_nota' => $resp['msj_sunat']
            ));

            $this->db->where('id', $comprobante->id);
            $this->db->update('facturacion', array(
                'estado' => 1,
                'nota' => $resp['msj_sunat'],
                'hash_cpe' => $resp['hash_cpe'],
                'hash_cdr' => $resp['hash_cdr']
            ));
            return $resp;
        } else {
            $error = 'Error no identificado';

            if (isset($resp['msg_validacion']))
                $error = $resp['msg_validacion'];

            if (isset($resp['msj_sunat']))
                $error = $resp['msj_sunat'];

            if (isset($resp['mensaje']))
                $error = $resp['mensaje'];

            $this->db->where('venta_id', $comprobante->ref_id);
            $this->db->update('venta', array(
                'facturacion' => 0,
                'facturacion_nota' => $error,
            ));

            $this->db->where('id', $comprobante->id);
            $this->db->update('facturacion', array(
                'estado' => 0,
                'nota' => $error,
            ));

            return $resp;
        }
    }

    function facturarVenta($venta_id)
    {
        $this->load->model('venta_new/venta_new_model');

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
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'No se ha podido recuperar el cambio de dolar'
                );
            }
        }


        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => $venta->fecha_facturacion,
            'documento_tipo' => $tipo_doc,
            'documento_numero' => $numero_comprobante,
            'documento_mod_tipo' => '',
            'documento_mod_numero' => '',
            'documento_mod_motivo' => '',
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'subtotal' => $venta->subtotal * $cambio_dolar,
            'impuesto' => $venta->impuesto * $cambio_dolar,
            'total' => $venta->total * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->venta_id,
        ));

        $facturacion_id = $this->db->insert_id();

        foreach ($venta->detalles as $d) {

            $impuesto = 0;
            $factor = (100 + $d->impuesto_porciento) / 100;
            if ($venta->tipo_impuesto == 1) {
                $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
            } elseif ($venta->tipo_impuesto == 2) {
                $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue($d->producto_id, $d->producto_codigo_interno),
                'producto_descripcion' => $d->producto_nombre,
                'um' => "NIU",
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->emitir($facturacion_id);

    }

    function anularVenta($venta_id, $numero, $motivo)
    {
        $this->load->model('venta_new/venta_new_model');

        $venta = $this->venta_new_model->get_venta_detalle($venta_id);

        $tipo_doc = '';
        $numero_comprobante = '';
        if ($venta->documento_id == 3) {
            $numero_comprobante = 'B' . $venta->serie . '-' . $venta->numero;
            $numero = 'B' . $numero;
            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;
        }
        if ($venta->documento_id == 1) {
            $numero_comprobante = 'F' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$FACTURA;
            $numero = 'F' . $numero;
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


        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => date('Y-m-d H:i:s'),
            'documento_tipo' => TIPO_COMPROBANTE::$NOTA_CREDITO,
            'documento_numero' => $numero,
            'documento_mod_tipo' => $tipo_doc,
            'documento_mod_numero' => $numero_comprobante,
            'documento_mod_motivo' => $motivo,
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'subtotal' => $venta->subtotal * $cambio_dolar,
            'impuesto' => $venta->impuesto * $cambio_dolar,
            'total' => $venta->total * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->venta_id,
        ));

        $facturacion_id = $this->db->insert_id();

        foreach ($venta->detalles as $d) {

            $impuesto = 0;
            $factor = (100 + $d->impuesto_porciento) / 100;
            if ($venta->tipo_impuesto == 1) {
                $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
            } elseif ($venta->tipo_impuesto == 2) {
                $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue($d->producto_id, $d->producto_codigo_interno),
                'producto_descripcion' => $d->producto_nombre,
                'um' => "NIU",
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->emitir($facturacion_id);

    }

    function devolverVenta($venta_id, $devoluciones, $numero, $motivo)
    {
        $this->load->model('venta_new/venta_new_model');

        $venta = $this->venta_new_model->get_venta_detalle($venta_id);

        $tipo_doc = '';
        $numero_comprobante = '';
        if ($venta->documento_id == 3) {
            $numero_comprobante = 'B' . $venta->serie . '-' . $venta->numero;
            $numero = 'B' . $numero;
            $tipo_doc = TIPO_COMPROBANTE::$BOLETA;
        }
        if ($venta->documento_id == 1) {
            $numero_comprobante = 'F' . $venta->serie . '-' . $venta->numero;
            $tipo_doc = TIPO_COMPROBANTE::$FACTURA;
            $numero = 'F' . $numero;
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

        $impuesto = 0;
        $subtotal = 0;
        $total = 0;
        foreach ($devoluciones as $d) {
            $total += $d->devolver * $d->precio;
        }


        if ($venta->tipo_impuesto == 1) {
            foreach ($devoluciones as $d) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                $impuesto += ($d->devolver * $d->precio) - (($d->devolver * $d->precio) / $factor);
            }
            $subtotal = $total - $impuesto;
        } elseif ($venta->tipo_impuesto == 2) {
            $subtotal = $total;
            foreach ($devoluciones as $d) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                $impuesto += (($d->devolver * $d->precio) * $factor) - ($d->devolver * $d->precio);
            }
            $total = $subtotal + $impuesto;
        } else {
            $subtotal = $total;
        }


        $this->db->insert('facturacion', array(
            'local_id' => $venta->local_id,
            'fecha' => date('Y-m-d H:i:s'),
            'documento_tipo' => TIPO_COMPROBANTE::$NOTA_CREDITO,
            'documento_numero' => $numero,
            'documento_mod_tipo' => $tipo_doc,
            'documento_mod_numero' => $numero_comprobante,
            'documento_mod_motivo' => $motivo,
            'cliente_tipo' => $tipo_identidad,
            'cliente_identificacion' => $venta->ruc,
            'cliente_nombre' => $venta->cliente_nombre,
            'cliente_direccion' => $venta->cliente_direccion,
            'subtotal' => $subtotal * $cambio_dolar,
            'impuesto' => $impuesto * $cambio_dolar,
            'total' => $total * $cambio_dolar,
            'estado' => 0,
            'nota' => 'No enviado',
            'ref_id' => $venta->venta_id,
        ));

        $facturacion_id = $this->db->insert_id();

        foreach ($devoluciones as $d) {

            $impuesto = 0;
            $factor = (100 + $d->impuesto_porciento) / 100;
            if ($venta->tipo_impuesto == 1) {
                $impuesto = ($d->devolver * $d->precio) - (($d->devolver * $d->precio) / $factor);
            } elseif ($venta->tipo_impuesto == 2) {
                $impuesto = (($d->devolver * $d->precio) * $factor) - ($d->devolver * $d->precio);
            }

            $producto = $this->db->get_where('producto', array('producto_id' => $d->producto_id))->row();


            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue($producto->producto_id, $producto->producto_codigo_interno),
                'producto_descripcion' => $producto->producto_nombre,
                'um' => "NIU",
                'cantidad' => $d->devolver,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->emitir($facturacion_id);

    }
}
