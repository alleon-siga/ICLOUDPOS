<?php

require_once "./FacturacionSunat.php";

$facturacion = new FacturacionSunat();


$data_comprobante = array(
    'TOTAL_GRAVADAS' => "84.75",
    'TOTAL_INAFECTA' => "0",
    'TOTAL_EXONERADAS' => "0",
    'TOTAL_GRATUITAS' => "0",
    'TOTAL_PERCEPCIONES' => "0",
    'TOTAL_RETENCIONES' => "0",
    'TOTAL_DETRACCIONES' => "0",
    'TOTAL_BONIFICACIONES' => "0",
    'TOTAL_DESCUENTO' => "0",

    'SUB_TOTAL' => "84.75",
    'TOTAL_IGV' => "15.25",
    'TOTAL_ISC' => "0",
    'TOTAL_OTR_IMP' => "0",

    'TOTAL' => "100",
    'TOTAL_LETRAS' => "CIEN",
    //==============================================
    'NRO_GUIA_REMISION' => "",
    'COD_GUIA_REMISION' => "",
    'NRO_OTR_COMPROBANTE' => "",
    'COD_OTR_COMPROBANTE' => "",
    //==============================================
    'TIPO_COMPROBANTE_MODIFICA' => "01",
    'NRO_DOCUMENTO_MODIFICA' => "F001-523369",
    'COD_TIPO_MOTIVO' => "01",
    'DESCRIPCION_MOTIVO' => "ANULACION DE LA OPERACION",
    //===============================================
    'NRO_COMPROBANTE' => "F011-56632",
    'FECHA_DOCUMENTO' => date("Y-m-d"),
    'COD_TIPO_DOCUMENTO' => "08",
    'COD_MONEDA' => "PEN",
    //==================================================
    'TIPO_DOCUMENTO_CLIENTE' => "6", //RUC
    'NRO_DOCUMENTO_CLIENTE' => "15602744393",
    'RAZON_SOCIAL_CLIENTE' => "MARTIN MARTINEZ ANTONIO",
    'DIRECCION_CLIENTE' => "Jiron Junin 485, Jr. Junin N. 455 D. 700 - 1",
    'CIUDAD_CLIENTE' => "LIMA",
    'COD_PAIS_CLIENTE' => "PE",
    //===============================================
    'TIPO_DOCUMENTO_EMPRESA' => "6", //RUC
    'NRO_DOCUMENTO_EMPRESA' => "20100066603",
    'NOMBRE_COMERCIAL_EMPRESA' => "CREV PERU COMERCIAL",
    'RAZON_SOCIAL_EMPRESA' => "CREVPERU S.A.",
    'CODIGO_UBIGEO_EMPRESA' => "070104",
    'DIRECCION_EMPRESA' => "PSJ HUAMPANI",
    'DEPARTAMENTO_EMPRESA' => "LIMA",
    'PROVINCIA_EMPRESA' => "LIMA",
    'DISTRITO_EMPRESA' => "CHACLAYO",
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
    'EMISOR_RUC' => "20100066603",
    'EMISOR_USUARIO_SOL' => "MODDATOS",
    'EMISOR_PASS_SOL' => "moddatos",
    'CERTIFICADO_PASS' => "123456"
);

$items_detalle = array();

$detalle = new stdClass();
$detalle->txt_item = "1";
$detalle->txt_unidad_medida = "NIU";
$detalle->txt_cantidad = "4";
$detalle->txt_precio = "25.00";
$detalle->txt_importe = "100.00";
$detalle->txt_precio_tipo_codigo = "01";
$detalle->txt_igv = "15.25";
$detalle->txt_isc = "0";
$detalle->txt_cod_tipo_operacion = "10";
$detalle->txt_codigo = "256636";
$detalle->txt_descripcion = "PAPA AMARILLA";

$items_detalle[] = $detalle;

var_dump($facturacion->procesarDocumento(TIPO_COMPROBANTE::$NOTA_DEBITO, $data_comprobante, $items_detalle, 3));

