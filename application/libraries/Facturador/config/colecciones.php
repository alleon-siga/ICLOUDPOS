<?php

class TIPO_COMPROBANTE
{
    public static $FACTURA = '01';
    public static $BOLETA = '03';
    public static $NOTA_CREDITO = '07';
    public static $NOTA_DEBITO = '08';
    public static $PERCEPCION = '40';
    public static $RETENCION = '20';
}

class TIPO_RESUMEN
{
    public static $COMUNICACION_BAJA = 'RA';
    public static $RESUMEN_DIARIO = 'RC';
    public static $RESUMEN_REVERSION = 'RR';
}

class GUIA_REMISION
{
    public static $TRANSPORTISTA = '09';
    public static $REMITENTE = '31';
}

class TIPO_LOTES
{
    public static $LOTES_FACTURA_NOTAS = 'LT';
}

class TIPO_IDENTIDAD
{
    public static $DOC_TRIB_NO_DOM_SIN_RUC = '0';
    public static $DNI = '1';
    public static $CARNET_EXTRANGERIA = '4';
    public static $RUC = '6';
    public static $PASAPORTE = '7';
    public static $DIPLOMATICO = 'A';
}

class TIPO_NOTA_CREDITO
{
    public static $ANULACION_OPERACION = '01';
    public static $ANULACION_ERROR_RUC = '02';
    public static $CORRECCION_ERROR_DESCRIPCION = '03';
    public static $DESCUENTO_GLOBAL = '04';
    public static $DESCUENTO_ITEM = '05';
    public static $DEVOLUCION_TOTAL = '06';
    public static $DEVOLUCION_ITEM = '07';
    public static $BONIFICACION = '08';
    public static $DISMINUCION_VALOR = '09';

    public static function get($codigo = false)
    {
        $result = array(
            '01' => 'Anulacion de la operacion',
            '02' => 'Anulacion por error en el RUC',
            '03' => 'Correccion por error en la descripcion',
            '04' => 'Descuento global',
            '05' => 'Descuento por item',
            '06' => 'Devolucion total',
            '07' => 'Devolucion por item',
            '08' => 'Bonificacion',
            '09' => 'Disminucion en el valor'
        );

        if ($codigo == false)
            return $result;

        if (isset($result[$codigo]))
            return $result[$codigo];

        return '';
    }

}