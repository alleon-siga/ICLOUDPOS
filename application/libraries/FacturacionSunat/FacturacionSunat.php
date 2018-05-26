<?php
require_once __DIR__ . '/lib/Colecciones.php';
require_once __DIR__ . '/lib/Validacion.php';
require_once __DIR__ . '/lib/Certificado.php';
require_once __DIR__ . '/lib/SunatWS.php';

define('ARCHIVOS_PATH', __DIR__ . '/archivos/');

class FacturacionSunat
{

    public function __construct()
    {
        $this->qr_path = ARCHIVOS_PATH . 'qr/';
    }

    // Proceso y envio el documento a la SUNAT
    /*=============================================================================================

    $doc => tipo de documento

    $data_comprobante = array(
	        'TOTAL_GRAVADAS' => "0",
	        'TOTAL_INAFECTA' => "0",
	        'TOTAL_EXONERADAS' => "0",
	        'TOTAL_GRATUITAS' => "0",
	        'TOTAL_PERCEPCIONES' => "0",
	        'TOTAL_RETENCIONES' => "0",
	        'TOTAL_DETRACCIONES' => "0",
	        'TOTAL_BONIFICACIONES' => "0",
	        'TOTAL_DESCUENTO' => "0",
	        'SUB_TOTAL' => "0",
	        'TOTAL_IGV' => "0",
	        'TOTAL_ISC' => "0",
	        'TOTAL_OTR_IMP' => "0",
	        'TOTAL' => "0",
	        'TOTAL_LETRAS' => "",
	        //==============================================
	        'NRO_GUIA_REMISION' => "",
	        'COD_GUIA_REMISION' => "",
	        'NRO_OTR_COMPROBANTE' => "",
	        'COD_OTR_COMPROBANTE' => "",
	        //==============================================
	        'TIPO_COMPROBANTE_MODIFICA' => "",
	        'NRO_DOCUMENTO_MODIFICA' => "",
	        'COD_TIPO_MOTIVO' => "",
	        'DESCRIPCION_MOTIVO' => "",
	        //===============================================
	        'NRO_COMPROBANTE' => "",
	        'FECHA_DOCUMENTO' => "",
	        'COD_TIPO_DOCUMENTO' => "",
	        'COD_MONEDA' => "",
	        //==================================================
	        'TIPO_DOCUMENTO_CLIENTE' => "", //RUC
	        'NRO_DOCUMENTO_CLIENTE' => "",
	        'RAZON_SOCIAL_CLIENTE' => "",
	        'DIRECCION_CLIENTE' => "",
	        'CIUDAD_CLIENTE' => "",
	        'COD_PAIS_CLIENTE' => "",
	        //===============================================
	        'TIPO_DOCUMENTO_EMPRESA' => "", //RUC
	        'NRO_DOCUMENTO_EMPRESA' => "",
	        'NOMBRE_COMERCIAL_EMPRESA' => "",
	        'RAZON_SOCIAL_EMPRESA' => "",
	        'CODIGO_UBIGEO_EMPRESA' => "",
	        'DIRECCION_EMPRESA' => "",
	        'DEPARTAMENTO_EMPRESA' => "",
	        'PROVINCIA_EMPRESA' => "",
	        'DISTRITO_EMPRESA' => "",
	        'CODIGO_PAIS_EMPRESA' => "",
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
	        'EMISOR_RUC' => $emisor['ruc'],
	        'EMISOR_USUARIO_SOL' => "",
	        'EMISOR_PASS_SOL' => ""
	    );

        stdClass() collection
        $items_detalle = array(
            'txt_item'=> "0",
            'txt_unidad_medida'=> "",
            'txt_cantidad'=> "0",
            'txt_precio'=> "0",
            'txt_importe'=> "0",
            'txt_precio_tipo_codigo'=> "01",
            'txt_igv'=> "0",
            'txt_isc'=> "0",
            'txt_cod_tipo_operacion'=> "10",
            'txt_codigo'=> "",
            'txt_descripcion'=> ""
        );

        $tipo_proceso
        1 => produccion
        2 => homologacion
        3 => beta

    ================================================================================================*/
    public function procesarDocumento($doc, $data_comprobante, $items_detalle, $tipo_proceso)
    {
        $sunat_ws = new SunatWS();
        $certificado = new Certificado();
        $val = new Validacion();
        $resp = null;
        $flag = false;

        $rutas = $this->set_rutas(
            $data_comprobante['NRO_DOCUMENTO_EMPRESA'] . '-' . $data_comprobante['COD_TIPO_DOCUMENTO'] . '-' . $data_comprobante['NRO_COMPROBANTE'],
            $data_comprobante['NRO_DOCUMENTO_EMPRESA'],
            $data_comprobante['CERTIFICADO_PASS'],
            $tipo_proceso
        );

        switch ($doc) {
            case TIPO_COMPROBANTE::$FACTURA: {
                $validacion = $val->validarVenta($data_comprobante);
                if ($validacion['respuesta'] == 'ok')
                    $resp = $sunat_ws->crearXmlFB($data_comprobante, $items_detalle, $rutas['ruta_xml']);
                else
                    return $validacion;
                $flag = true;
                break;
            }
            case TIPO_COMPROBANTE::$BOLETA: {
                if ($data_comprobante['TOTAL'] < 700) {
                    $data_comprobante['TIPO_DOCUMENTO_CLIENTE'] = '-';
                    $data_comprobante['NRO_DOCUMENTO_CLIENTE'] = '-';
                    $data_comprobante['RAZON_SOCIAL_CLIENTE'] = '-';
                    $data_comprobante['DIRECCION_CLIENTE'] = '-';
                }
                $validacion = $val->validarVenta($data_comprobante);
                if ($validacion['respuesta'] == 'ok')
                    $resp = $sunat_ws->crearXmlFB($data_comprobante, $items_detalle, $rutas['ruta_xml']);
                else
                    return $validacion;
                $flag = true;
                break;
            }
            case TIPO_COMPROBANTE::$NOTA_CREDITO: {
                if ($data_comprobante['TOTAL'] < 700 && $data_comprobante['TIPO_COMPROBANTE_MODIFICA'] == TIPO_COMPROBANTE::$BOLETA) {
                    $data_comprobante['TIPO_DOCUMENTO_CLIENTE'] = '-';
                    $data_comprobante['NRO_DOCUMENTO_CLIENTE'] = '-';
                    $data_comprobante['RAZON_SOCIAL_CLIENTE'] = '-';
                    $data_comprobante['DIRECCION_CLIENTE'] = '-';
                }
                $validacion = $val->validarNotaCredito($data_comprobante);
                $resp = $sunat_ws->crearXmlNotaCredito($data_comprobante, $items_detalle, $rutas['ruta_xml']);
                $flag = true;
                break;
            }
            case TIPO_COMPROBANTE::$NOTA_DEBITO: {
                $resp = $sunat_ws->crearXmlNotaDebito($data_comprobante, $items_detalle, $rutas['ruta_xml']);
                $flag = true;
                break;
            }
        }

        if ($flag == true) {
            $flg_firma = "1";
            $resp_firma = $certificado->firmarXml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);

            if ($resp_firma['respuesta'] == 'error') {
                return $resp_firma;
            }

            $resp_envio = $sunat_ws->enviarDocumento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
            if ($resp_envio['respuesta'] == 'error') {
                return $resp_envio;
            }

            $text_qr =
                $data_comprobante['NRO_DOCUMENTO_EMPRESA'] . '|' .
                $data_comprobante['COD_TIPO_DOCUMENTO'] . '|' .
                str_replace('-', '|', $data_comprobante['NRO_COMPROBANTE']) . '|' .
                $data_comprobante['TOTAL_IGV'] . '|' .
                $data_comprobante['TOTAL'] . '|' .
                $data_comprobante['FECHA_DOCUMENTO'] . '|' .
                $data_comprobante['TIPO_DOCUMENTO_CLIENTE'] . '|' .
                $data_comprobante['NRO_DOCUMENTO_CLIENTE'] . '|';

            $name_qr =
                $data_comprobante['COD_TIPO_DOCUMENTO'] . '-' .
                $data_comprobante['NRO_COMPROBANTE'] . '.png';


            $this->generar_png_qr($data_comprobante['NRO_DOCUMENTO_EMPRESA'], $text_qr, $name_qr);

            $resp['respuesta'] = 'ok';
            $resp['hash_cpe'] = $resp_firma['hash_cpe'];
            $resp['hash_cdr'] = $resp_envio['hash_cdr'];
            $resp['cod_sunat'] = $resp_envio['cod_sunat'];
            $resp['msj_sunat'] = $resp_envio['mensaje'];
            return $resp;
        } else {
            return array(
                'respuesta' => 'error',
                'msg_validacion' => 'Tipo de documento no valido'
            );
        }

    }

    /*RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |*/
    private function generar_png_qr($ruc, $string, $name)
    {
        require_once __DIR__ . '/lib/phpqrcode/qrlib.php';
        $ruta = $this->qr_path . $ruc;
        if (!is_dir($ruta)) {
            mkdir($ruta);
        }
        $ruta .= '/' . $name;

        QRcode::png($string, $ruta, 'Q', 15, 0);
    }

    // Configura las rutas de archivos
    private function set_rutas($nombre, $ruc, $cert_pass, $tp)
    {
        $tipo_proceso = '';
        $ruta_ws = '';
        $ruta_firma_pass = $cert_pass;

        switch ($tp) {
            case 3: {
                $tipo_proceso = 'beta';
                $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
                $ruta_firma = ARCHIVOS_PATH . 'certificados/' . $tipo_proceso . '/' . $ruc . '.pfx';
                if (!file_exists($ruta_firma)) {
                    $ruta_firma = ARCHIVOS_PATH . 'certificados/' . $tipo_proceso . '/firmabeta.pfx';
                    $ruta_firma_pass = '123456';
                }
                break;
            }
            case 2: {
                $tipo_proceso = 'homologacion';
                $ruta_firma = ARCHIVOS_PATH . 'certificados/' . $tipo_proceso . '/' . $ruc . '.pfx';
                $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
                break;
            }
            case 1: {
                $tipo_proceso = 'produccion';
                $ruta_firma = ARCHIVOS_PATH . 'certificados/' . $tipo_proceso . '/' . $ruc . '.pfx';
                $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
                break;
            }
        }

        if (!is_dir(ARCHIVOS_PATH . 'xmls/' . $tipo_proceso . '/' . $ruc . '/')) {
            mkdir(ARCHIVOS_PATH . 'xmls/' . $tipo_proceso . '/' . $ruc . '/');
        }

        return array(
            'nombre_archivo' => $nombre,
            'ruta_xml' => ARCHIVOS_PATH . 'xmls/' . $tipo_proceso . '/' . $ruc . '/' . $nombre,
            'ruta_cdr' => ARCHIVOS_PATH . 'xmls/' . $tipo_proceso . '/' . $ruc . '/',
            'ruta_firma' => $ruta_firma,
            'pass_firma' => $ruta_firma_pass,
            'ruta_ws' => $ruta_ws
        );
    }
}