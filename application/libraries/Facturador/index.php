<?php
require_once 'Facturador.php';

$emisor = array(
    'NRO_DOCUMENTO' => '20490961195',
    'RAZON_SOCIAL' => 'C.R DIGITALL SOCIEDAD COMERCIAL DE RESPONSABILIDAD LIMITADA - C.R. DIGITALL S.C.R.L.',
    'NOMBRE_COMERCIAL' => '-',
    'DIRECCION' => '-',
    'UBIGEO' => '150701',
    'URBANIZACION' => '-',
    'DISTRITO' => 'PUENTE PIEDRA',
    'PROVINCIA' => 'LIMA',
    'DEPARTAMENTO' => 'LIMA',
    'PAIS_CODIGO' => 'PE',
    'CERT_PASS' => 'VpXksgpen5fkFtAw',
    'SOL_USER' => 'FACTEL01',
    'SOL_PASS' => 'Vrp8Yf3a',
    'ENV' => 'BETA'
);

$facturador = new Facturador($emisor);

$cabecera = array(
    'FECHA_EMISION' => date('Y-m-d'),
    'TIPO_DOCUMENTO' => '08',
    'NUMERO_DOCUMENTO' => 'BN01-1',

    'NOTA_NUMERO_DOCUMENTO' => 'B001-500',
    'NOTA_TIPO_DOCUMENTO' => '03',
    'NOTA_MOTIVO_CODIGO' => '01',
    'NOTA_MOTIVO_DESCRIPCION' => 'Anulacion de la operacion',

    'CLIENTE_NRO_DOCUMENTO' => '12345678',
    'CLIENTE_TIPO_IDENTIDAD' => '1',
    'CLIENTE_NOMBRE' => 'ANTONIO MARTIN MARTINEZ',

    'CODIGO_MONEDA' => 'PEN',
    'TOTAL_GRAVADAS' => '84.73',
    'TOTAL_INAFECTAS' => '0.00',
    'TOTAL_EXONERADAS' => '0.00',
    'TOTAL_GRATUITAS' => '0.00',
    'TOTAL_DESCUENTOS' => '0.00',

    'TOTAL_TRIBUTO_IGV' => '15.10',
    'TOTAL_TRIBUTO_ISC' => '0.00',
    'TOTAL_TRIBUTO_OTROS' => '0.00',

    'TOTAL_DESCUENTO_GLOBAL' => '0.00',
    'TOTAL_OTROS_CARGOS' => '0.00',
    'TOTAL_VENTA' => '100.00',
    'TOTAL_VENTA_LETRAS' => 'CIEN SOLES 00/100',

    //Referencia al documento de Guia de remision. Opcional
    'GUIA_REMISION_NRO' => '0001-5263',
    'GUIA_REMISION_CODIGO' => '09',
);

$detalles = array();

$detalles[] = array(
    'CODIGO' => 'ASD',
    'CANTIDAD' => '8',
    'UNIDAD_MEDIDA' => "NIU",
    'DESCRIPCION' => 'LAPTOP ACER',
    'PRECIO_VALOR' => '12.65',
    'PRECIO_VENTA' => '15.87',
    'TIPO_PRECIO' => '01',
    'DETALLE_TRIBUTO_IGV' => '3.25',
    'TIPO_TRIBUTO_IGV' => '10',
);

$detalles[] = array(
    'CODIGO' => 'sdfsdf',
    'CANTIDAD' => '2',
    'UNIDAD_MEDIDA' => "NIU",
    'DESCRIPCION' => 'LINTERNA F8Y',
    'PRECIO_VALOR' => '12.65',
    'PRECIO_VENTA' => '15.87',
    'TIPO_PRECIO' => '01',
    'DETALLE_TRIBUTO_IGV' => '3.25',
    'TIPO_TRIBUTO_IGV' => '10',
);

//var_dump($facturador->crearComprobante(TIPO_COMPROBANTE::$NOTA_DEBITO, $cabecera, $detalles));
//
//var_dump($reponse = $facturador->enviarComprobante(TIPO_COMPROBANTE::$FACTURA, array(
//    'NUMERO_DOCUMENTO' => 'F001-500'
//)));


$cb = array(
    'FECHA_EMISION' => date('Y-m-d'),
    'FECHA_REFERENCIA' => '2018-06-15',
    'NUMERO_DOCUMENTO' => '4'
);

$cb_detalles = array();

$cb_detalles[] = array(
    'TIPO_DOCUMENTO' => '03',
    'DOCUMENTO_BAJA_SERIE' => 'BZZ2',
    'DOCUMENTO_BAJA_NUMERO' => '1',
    'BAJA_DESCRIPCION' => 'Error del sistema'
);

var_dump($facturador->enviarBaja($cb, $cb_detalles));

