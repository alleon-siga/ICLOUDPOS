<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class facturacion_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        require_once(APPPATH . 'libraries/Facturador/Facturador.php');
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
            if ($result->documento_tipo == '07' || $result->documento_tipo == '08') {
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

    function crearXml($id)
    {
        log_message('debug', 'Facturacion Electronica. creando comprobante ' . $id);

        $emisor = $this->db
            ->join('estados', 'estados.estados_id = facturacion_emisor.departamento_id')
            ->join('ciudades', 'ciudades.ciudad_id = facturacion_emisor.provincia_id')
            ->join('distrito', 'distrito.id = facturacion_emisor.distrito_id')
            ->get('facturacion_emisor')->row();

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

        $comprobante = $this->db->get_where('facturacion', array('id' => $id))->row();
        $comprobante_detalle = $this->db->get_where('facturacion_detalle', array('facturacion_id' => $id))->result();

        $cabecera = array(
            'FECHA_EMISION' => date('Y-m-d', strtotime($comprobante->fecha)),
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
                'PRECIO_VALOR' => $d->precio,
                'PRECIO_VENTA' => $d->precio,
                'TIPO_PRECIO' => '01',
                'DETALLE_TRIBUTO_IGV' => $d->impuesto,
                'TIPO_TRIBUTO_IGV' => '10',
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
    }

    function facturarVenta($venta_id)
    {
        $this->load->model('venta_new/venta_new_model');
        log_message('debug', 'Facturacion Electronica. Guardando venta ' . $venta_id);
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

        foreach ($venta->detalles as $d) {

            if ($d->afectacion_impuesto == OP_GRAVABLE)
                $total_gravadas += $d->cantidad * $d->precio;

            if ($d->afectacion_impuesto == OP_EXONERADA)
                $total_exoneradas += $d->cantidad * $d->precio;

            if ($d->afectacion_impuesto == OP_INAFECTA)
                $total_inafectas += $d->cantidad * $d->precio;
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
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,
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
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue(sumCod($d->producto_id, 4), $d->producto_codigo_interno),
                'producto_descripcion' => $d->producto_nombre,
                'um' => $d->unidad_abr,
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->crearXml($facturacion_id);

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

        $total_gravadas = 0;
        $total_exoneradas = 0;
        $total_inafectas = 0;

        foreach ($venta->detalles as $d) {

            if ($d->afectacion_impuesto == OP_GRAVABLE)
                $total_gravadas += $d->cantidad * $d->precio;

            if ($d->afectacion_impuesto == OP_EXONERADA)
                $total_exoneradas += $d->cantidad * $d->precio;

            if ($d->afectacion_impuesto == OP_INAFECTA)
                $total_inafectas += $d->cantidad * $d->precio;
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
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,
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
            if ($d->afectacion_impuesto == OP_GRAVABLE) {
                $factor = (100 + $d->impuesto_porciento) / 100;
                if ($venta->tipo_impuesto == 1) {
                    $impuesto = ($d->cantidad * $d->precio) - (($d->cantidad * $d->precio) / $factor);
                } elseif ($venta->tipo_impuesto == 2) {
                    $impuesto = (($d->cantidad * $d->precio) * $factor) - ($d->cantidad * $d->precio);
                }
            }

            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue($d->producto_id, $d->producto_codigo_interno),
                'producto_descripcion' => $d->producto_nombre,
                'um' => $d->unidad_abr,
                'cantidad' => $d->cantidad,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->crearXml($facturacion_id);

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

        $total_gravadas = 0;
        $total_exoneradas = 0;
        $total_inafectas = 0;

        foreach ($devoluciones as $d) {
            $producto = $this->db->get_where('producto', array('producto_id' => $d->producto_id))->row();

            if ($producto->producto_afectacion_impuesto == OP_GRAVABLE)
                $total_gravadas += $d->devolver * $d->precio;

            if ($producto->producto_afectacion_impuesto == OP_EXONERADA)
                $total_exoneradas += $d->devolver * $d->precio;

            if ($producto->producto_afectacion_impuesto == OP_INAFECTA)
                $total_inafectas += $d->devolver * $d->precio;
        }

        $impuesto = 0;
        $total = 0;
        foreach ($devoluciones as $d) {
            $total += $d->devolver * $d->precio;
        }


        if ($venta->tipo_impuesto == 1) {
            foreach ($devoluciones as $d) {
                $producto = $this->db->get_where('producto', array('producto_id' => $d->producto_id))->row();
                if ($producto->producto_afectacion_impuesto == OP_GRAVABLE) {
                    $factor = (100 + $d->impuesto_porciento) / 100;
                    $impuesto += ($d->devolver * $d->precio) - (($d->devolver * $d->precio) / $factor);
                }
            }
            $subtotal = $total - $impuesto;
        } elseif ($venta->tipo_impuesto == 2) {
            $subtotal = $total;
            foreach ($devoluciones as $d) {
                $producto = $this->db->get_where('producto', array('producto_id' => $d->producto_id))->row();
                if ($producto->producto_afectacion_impuesto == OP_GRAVABLE) {
                    $factor = (100 + $d->impuesto_porciento) / 100;
                    $impuesto += (($d->devolver * $d->precio) * $factor) - ($d->devolver * $d->precio);
                }
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
            'total_gravadas' => $total_gravadas * $cambio_dolar,
            'total_exoneradas' => $total_exoneradas * $cambio_dolar,
            'total_inafectas' => $total_inafectas * $cambio_dolar,

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

            $unidad = $this->db->get_where('unidades', array('id_unidad' => $d->unidad_id))->row();
            $this->db->insert('facturacion_detalle', array(
                'facturacion_id' => $facturacion_id,
                'producto_codigo' => getCodigoValue($producto->producto_id, $producto->producto_codigo_interno),
                'producto_descripcion' => $producto->producto_nombre,
                'um' => $unidad->abreviatura,
                'cantidad' => $d->devolver,
                'precio' => $d->precio * $cambio_dolar,
                'impuesto' => $impuesto * $cambio_dolar
            ));
        }

        return $this->crearXml($facturacion_id);

    }
}
