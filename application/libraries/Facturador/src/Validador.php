<?php

namespace Facturador;

class Validador
{

    public static function prepareEmisor($emisor)
    {
        if (isset($emisor['RAZON_SOCIAL']))
            $emisor['RAZON_SOCIAL'] = self::limpiar_caracteres($emisor['RAZON_SOCIAL']);

        if (isset($emisor['NOMBRE_COMERCIAL']))
            $emisor['NOMBRE_COMERCIAL'] = self::limpiar_caracteres($emisor['NOMBRE_COMERCIAL']);

        if (isset($emisor['DIRECCION']))
            $emisor['DIRECCION'] = self::limpiar_caracteres($emisor['DIRECCION']);

        if (isset($emisor['URBANIZACION']))
            $emisor['URBANIZACION'] = self::limpiar_caracteres($emisor['URBANIZACION']);

        if (isset($emisor['DISTRITO']))
            $emisor['DISTRITO'] = self::limpiar_caracteres($emisor['DISTRITO']);

        if (isset($emisor['PROVINCIA']))
            $emisor['PROVINCIA'] = self::limpiar_caracteres($emisor['PROVINCIA']);

        if (isset($emisor['DEPARTAMENTO']))
            $emisor['DEPARTAMENTO'] = self::limpiar_caracteres($emisor['DEPARTAMENTO']);

        return $emisor;
    }

    public static function prepareCabecera($cabecera)
    {
        $cabecera['CLIENTE_NOMBRE'] = self::limpiar_caracteres($cabecera['CLIENTE_NOMBRE']);

        if (isset($cabecera['TOTAL_GRAVADAS']))
            $cabecera['TOTAL_GRAVADAS'] = number_format($cabecera['TOTAL_GRAVADAS'], 2, '.', '');

        if (isset($cabecera['TOTAL_INAFECTAS']))
            $cabecera['TOTAL_INAFECTAS'] = number_format($cabecera['TOTAL_INAFECTAS'], 2, '.', '');

        if (isset($cabecera['TOTAL_EXONERADAS']))
            $cabecera['TOTAL_EXONERADAS'] = number_format($cabecera['TOTAL_EXONERADAS'], 2, '.', '');

        if (isset($cabecera['TOTAL_GRATUITAS']))
            $cabecera['TOTAL_GRATUITAS'] = number_format($cabecera['TOTAL_GRATUITAS'], 2, '.', '');

        if (isset($cabecera['TOTAL_DESCUENTOS']))
            $cabecera['TOTAL_DESCUENTOS'] = number_format($cabecera['TOTAL_DESCUENTOS'], 2, '.', '');

        if (isset($cabecera['TOTAL_TRIBUTO_IGV']))
            $cabecera['TOTAL_TRIBUTO_IGV'] = number_format($cabecera['TOTAL_TRIBUTO_IGV'], 2, '.', '');

        if (isset($cabecera['TOTAL_TRIBUTO_ISC']))
            $cabecera['TOTAL_TRIBUTO_ISC'] = number_format($cabecera['TOTAL_TRIBUTO_ISC'], 2, '.', '');

        if (isset($cabecera['TOTAL_TRIBUTO_OTROS']))
            $cabecera['TOTAL_TRIBUTO_OTROS'] = number_format($cabecera['TOTAL_TRIBUTO_OTROS'], 2, '.', '');

        if (isset($cabecera['TOTAL_DESCUENTO_GLOBAL']))
            $cabecera['TOTAL_DESCUENTO_GLOBAL'] = number_format($cabecera['TOTAL_DESCUENTO_GLOBAL'], 2, '.', '');

        if (isset($cabecera['TOTAL_OTROS_CARGOS']))
            $cabecera['TOTAL_OTROS_CARGOS'] = number_format($cabecera['TOTAL_OTROS_CARGOS'], 2, '.', '');

        if (isset($cabecera['TOTAL_VENTA']))
            $cabecera['TOTAL_VENTA'] = number_format($cabecera['TOTAL_VENTA'], 2, '.', '');

        if ($cabecera['TIPO_DOCUMENTO'] == \TIPO_COMPROBANTE::$BOLETA) {
            if ($cabecera['TOTAL_VENTA'] <= 700) {
                $cabecera['CLIENTE_NRO_DOCUMENTO'] = '-';
                $cabecera['CLIENTE_TIPO_IDENTIDAD'] = '-';
                $cabecera['CLIENTE_NOMBRE'] = '-';
            }
        }

        if (isset($cabecera['NOTA_TIPO_DOCUMENTO'])) {
            if ($cabecera['NOTA_TIPO_DOCUMENTO'] == \TIPO_COMPROBANTE::$BOLETA) {
                if ($cabecera['TOTAL_VENTA'] <= 700) {
                    $cabecera['CLIENTE_NRO_DOCUMENTO'] = '-';
                    $cabecera['CLIENTE_TIPO_IDENTIDAD'] = '-';
                    $cabecera['CLIENTE_NOMBRE'] = '-';
                }
            }
        }

        return $cabecera;
    }

    public static function prepareDetallesRC($detalles)
    {
        for ($i = 0; $i < count($detalles); $i++) {
            $detalles[$i]['CLIENTE_NOMBRE'] = self::limpiar_caracteres($detalles[$i]['CLIENTE_NOMBRE']);

            if (isset($detalles[$i]['TOTAL_GRAVADAS']))
                $detalles[$i]['TOTAL_GRAVADAS'] = number_format($detalles[$i]['TOTAL_GRAVADAS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_INAFECTAS']))
                $detalles[$i]['TOTAL_INAFECTAS'] = number_format($detalles[$i]['TOTAL_INAFECTAS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_EXONERADAS']))
                $detalles[$i]['TOTAL_EXONERADAS'] = number_format($detalles[$i]['TOTAL_EXONERADAS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_GRATUITAS']))
                $detalles[$i]['TOTAL_GRATUITAS'] = number_format($detalles[$i]['TOTAL_GRATUITAS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_DESCUENTOS']))
                $detalles[$i]['TOTAL_DESCUENTOS'] = number_format($detalles[$i]['TOTAL_DESCUENTOS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_TRIBUTO_IGV']))
                $detalles[$i]['TOTAL_TRIBUTO_IGV'] = number_format($detalles[$i]['TOTAL_TRIBUTO_IGV'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_TRIBUTO_ISC']))
                $detalles[$i]['TOTAL_TRIBUTO_ISC'] = number_format($detalles[$i]['TOTAL_TRIBUTO_ISC'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_TRIBUTO_OTROS']))
                $detalles[$i]['TOTAL_TRIBUTO_OTROS'] = number_format($detalles[$i]['TOTAL_TRIBUTO_OTROS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_DESCUENTO_GLOBAL']))
                $detalles[$i]['TOTAL_DESCUENTO_GLOBAL'] = number_format($detalles[$i]['TOTAL_DESCUENTO_GLOBAL'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_OTROS_CARGOS']))
                $detalles[$i]['TOTAL_OTROS_CARGOS'] = number_format($detalles[$i]['TOTAL_OTROS_CARGOS'], 2, '.', '');

            if (isset($detalles[$i]['TOTAL_VENTA']))
                $detalles[$i]['TOTAL_VENTA'] = number_format($detalles[$i]['TOTAL_VENTA'], 2, '.', '');

            if ($detalles[$i]['TIPO_DOCUMENTO'] == \TIPO_COMPROBANTE::$BOLETA) {
                if ($detalles[$i]['TOTAL_VENTA'] <= 700) {
                    $detalles[$i]['CLIENTE_NRO_DOCUMENTO'] = '-';
                    $detalles[$i]['CLIENTE_TIPO_IDENTIDAD'] = '-';
                    $detalles[$i]['CLIENTE_NOMBRE'] = '-';
                }
            }

            if (isset($detalles[$i]['NOTA_TIPO_DOCUMENTO'])) {
                if ($detalles[$i]['NOTA_TIPO_DOCUMENTO'] == \TIPO_COMPROBANTE::$BOLETA) {
                    if ($detalles[$i]['TOTAL_VENTA'] <= 700) {
                        $detalles[$i]['CLIENTE_NRO_DOCUMENTO'] = '-';
                        $detalles[$i]['CLIENTE_TIPO_IDENTIDAD'] = '-';
                        $detalles[$i]['CLIENTE_NOMBRE'] = '-';
                    }
                }
            }
        }


        return $detalles;
    }

    public static function prepareDetalles($detalle)
    {
        for ($i = 0; $i < count($detalle); $i++) {

            $detalle[$i]['DESCRIPCION'] = self::limpiar_caracteres($detalle[$i]['DESCRIPCION']);

            if (isset($detalle[$i]['CODIGO']))
                $detalle[$i]['CODIGO'] = self::limpiar_caracteres($detalle[$i]['CODIGO']);

            if (isset($detalle[$i]['CANTIDAD']))
                $detalle[$i]['CANTIDAD'] = number_format($detalle[$i]['CANTIDAD'], 3, '.', '');

            if (isset($detalle[$i]['PRECIO_VALOR']))
                $detalle[$i]['PRECIO_VALOR'] = number_format($detalle[$i]['PRECIO_VALOR'], 2, '.', '');

            if (isset($detalle[$i]['PRECIO_VENTA']))
                $detalle[$i]['PRECIO_VENTA'] = number_format($detalle[$i]['PRECIO_VENTA'], 2, '.', '');

            if (isset($detalle[$i]['DETALLE_TRIBUTO_IGV']))
                $detalle[$i]['DETALLE_TRIBUTO_IGV'] = number_format($detalle[$i]['DETALLE_TRIBUTO_IGV'], 2, '.', '');

            if (isset($detalle[$i]['DETALLE_TRIBUTO_ISC']))
                $detalle[$i]['DETALLE_TRIBUTO_ISC'] = number_format($detalle[$i]['DETALLE_TRIBUTO_ISC'], 2, '.', '');
        }

        return $detalle;
    }

    public static function validarComprobante($tipo, $cabecera, $detalles)
    {
        $resp['CODIGO'] = '-2';
        switch ($tipo) {
            case \TIPO_COMPROBANTE::$FACTURA : {

                if (!self::fecha($cabecera['FECHA_EMISION'])) {
                    $resp['MENSAJE'] = 'Fecha de emision no valida';
                    return $resp;
                }

                if (!self::numero_comprobante($tipo, $cabecera['NUMERO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Numero de comprobante no valido';
                    return $resp;
                }

                if (!self::identificacion($cabecera['CLIENTE_TIPO_IDENTIDAD'], $cabecera['CLIENTE_NRO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Identificacion del cliente no valida';
                    return $resp;
                }

                if (!isset($cabecera['CLIENTE_NOMBRE']) && $cabecera['CLIENTE_NOMBRE'] = '') {
                    $resp['MENSAJE'] = 'Nombre del cliente es requerido';
                    return $resp;
                }

                if (!isset($cabecera['TOTAL_VENTA']) && $cabecera['TOTAL_VENTA'] = '' && $cabecera['TOTAL_VENTA'] > 0) {
                    $resp['MENSAJE'] = 'Total de la venta es requerido';
                    return $resp;
                }

                if (!isset($cabecera['CODIGO_MONEDA']) && $cabecera['CODIGO_MONEDA'] = '') {
                    $resp['MENSAJE'] = 'Codigo de moneda es requerido';
                    return $resp;
                }

                //VALIDAR GRAVABAS, EXONERADAS E INAFECTAS. Se debera informar al menos uno de estos

                return self::validar_detalles($detalles);
            }

            case \TIPO_COMPROBANTE::$BOLETA : {

                if (!self::fecha($cabecera['FECHA_EMISION'])) {
                    $resp['MENSAJE'] = 'Fecha de emision no valida';
                    return $resp;
                }

                if (!self::numero_comprobante($tipo, $cabecera['NUMERO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Numero de comprobante no valido';
                    return $resp;
                }

                if (!isset($cabecera['TOTAL_VENTA']) && $cabecera['TOTAL_VENTA'] = '' && $cabecera['TOTAL_VENTA'] > 0) {
                    $resp['MENSAJE'] = 'Total de la venta es requerido';
                    return $resp;
                }

                if ($cabecera['TOTAL_VENTA'] > 700) {
                    if (!self::identificacion($cabecera['CLIENTE_TIPO_IDENTIDAD'], $cabecera['CLIENTE_NRO_DOCUMENTO'])) {
                        $resp['MENSAJE'] = 'Identificacion del cliente no valida';
                        return $resp;
                    }

                    if (!isset($cabecera['CLIENTE_NOMBRE']) && $cabecera['CLIENTE_NOMBRE'] = '') {
                        $resp['MENSAJE'] = 'Nombre del cliente es requerido';
                        return $resp;
                    }
                }

                if (!isset($cabecera['CODIGO_MONEDA']) && $cabecera['CODIGO_MONEDA'] = '') {
                    $resp['MENSAJE'] = 'Codigo de moneda es requerido';
                    return $resp;
                }

                //VALIDAR GRAVABAS, EXONERADAS E INAFECTAS. Se debera informar al menos uno de estos

                return self::validar_detalles($detalles);
            }

            case \TIPO_COMPROBANTE::$NOTA_CREDITO : {

                if (!self::fecha($cabecera['FECHA_EMISION'])) {
                    $resp['MENSAJE'] = 'Fecha de emision no valida';
                    return $resp;
                }

                if (!self::numero_comprobante($cabecera['NOTA_TIPO_DOCUMENTO'], $cabecera['NUMERO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Numero de comprobante no valido';
                    return $resp;
                }

                if (!self::numero_comprobante($cabecera['NOTA_TIPO_DOCUMENTO'], $cabecera['NOTA_NUMERO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Numero de comprobante que afecta no valido';
                    return $resp;
                }

                if ($cabecera['TOTAL_VENTA'] > 700 && $cabecera['NOTA_TIPO_DOCUMENTO'] == \TIPO_COMPROBANTE::$BOLETA) {
                    if (!self::identificacion($cabecera['CLIENTE_TIPO_IDENTIDAD'], $cabecera['CLIENTE_NRO_DOCUMENTO'])) {
                        $resp['MENSAJE'] = 'Identificacion del cliente no valida';
                        return $resp;
                    }

                    if (!isset($cabecera['CLIENTE_NOMBRE']) && $cabecera['CLIENTE_NOMBRE'] = '') {
                        $resp['MENSAJE'] = 'Nombre del cliente es requerido';
                        return $resp;
                    }
                }

                if (!isset($cabecera['TOTAL_VENTA']) && $cabecera['TOTAL_VENTA'] = '' && $cabecera['TOTAL_VENTA'] > 0) {
                    $resp['MENSAJE'] = 'Total de la venta es requerido';
                    return $resp;
                }

                if (!isset($cabecera['CODIGO_MONEDA']) && $cabecera['CODIGO_MONEDA'] = '') {
                    $resp['MENSAJE'] = 'Codigo de moneda es requerido';
                    return $resp;
                }

                //VALIDAR GRAVABAS, EXONERADAS E INAFECTAS. Se debera informar al menos uno de estos

                return self::validar_detalles($detalles);
            }

            case \TIPO_COMPROBANTE::$NOTA_DEBITO : {

                if (!self::fecha($cabecera['FECHA_EMISION'])) {
                    $resp['MENSAJE'] = 'Fecha de emision no valida';
                    return $resp;
                }

                if (!self::numero_comprobante($cabecera['NOTA_TIPO_DOCUMENTO'], $cabecera['NUMERO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Numero de comprobante no valido';
                    return $resp;
                }

                if (!self::numero_comprobante($cabecera['NOTA_TIPO_DOCUMENTO'], $cabecera['NOTA_NUMERO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Numero de comprobante que afecta no valido';
                    return $resp;
                }

                if (!self::identificacion($cabecera['CLIENTE_TIPO_IDENTIDAD'], $cabecera['CLIENTE_NRO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Identificacion del cliente no valida';
                    return $resp;
                }

                if (!isset($cabecera['CLIENTE_NOMBRE']) && $cabecera['CLIENTE_NOMBRE'] = '') {
                    $resp['MENSAJE'] = 'Nombre del cliente es requerido';
                    return $resp;
                }

                if (!isset($cabecera['TOTAL_VENTA']) && $cabecera['TOTAL_VENTA'] = '' && $cabecera['TOTAL_VENTA'] > 0) {
                    $resp['MENSAJE'] = 'Total de la venta es requerido';
                    return $resp;
                }

                if (!isset($cabecera['CODIGO_MONEDA']) && $cabecera['CODIGO_MONEDA'] = '') {
                    $resp['MENSAJE'] = 'Codigo de moneda es requerido';
                    return $resp;
                }

                //VALIDAR GRAVABAS, EXONERADAS E INAFECTAS. Se debera informar al menos uno de estos

                return self::validar_detalles($detalles);
            }
        }
    }

    public static function validarRC($cabecera, $detalles)
    {
        $resp['CODIGO'] = '-2';
        if (!self::fecha($cabecera['FECHA_EMISION'])) {
            $resp['MENSAJE'] = 'Fecha de emision no valida';
            return $resp;
        }

        if (!self::fecha($cabecera['FECHA_REFERENCIA'])) {
            $resp['MENSAJE'] = 'Fecha de referencia no valida';
            return $resp;
        }

        if (!isset($cabecera['CORRELATIVO']) && $cabecera['CORRELATIVO'] = '') {
            $resp['MENSAJE'] = 'Correlativo es requerido';
            return $resp;
        }

        foreach ($detalles as $detalle) {
            if (!self::fecha($detalle['FECHA_EMISION'])) {
                $resp['MENSAJE'] = 'Fecha de emision no valida';
                return $resp;
            }

            if (!self::numero_comprobante($detalle['TIPO_DOCUMENTO'], $detalle['NUMERO_DOCUMENTO'])) {
                $resp['MENSAJE'] = 'Numero de comprobante no valido';
                return $resp;
            }

            if (!isset($detalle['TOTAL_VENTA']) && $detalle['TOTAL_VENTA'] = '' && $detalle['TOTAL_VENTA'] > 0) {
                $resp['MENSAJE'] = 'Total de la venta es requerido';
                return $resp;
            }

            if ($detalle['TOTAL_VENTA'] > 700) {
                if (!self::identificacion($detalle['CLIENTE_TIPO_IDENTIDAD'], $detalle['CLIENTE_NRO_DOCUMENTO'])) {
                    $resp['MENSAJE'] = 'Identificacion del cliente no valida';
                    return $resp;
                }

                if (!isset($detalle['CLIENTE_NOMBRE']) && $detalle['CLIENTE_NOMBRE'] = '') {
                    $resp['MENSAJE'] = 'Nombre del cliente es requerido';
                    return $resp;
                }
            }

            if (!isset($detalle['CODIGO_MONEDA']) && $detalle['CODIGO_MONEDA'] = '') {
                $resp['MENSAJE'] = 'Codigo de moneda es requerido';
                return $resp;
            }

            //VALIDAR GRAVABAS, EXONERADAS E INAFECTAS. Se debera informar al menos uno de estos
        }

        return FALSE;

    }

    protected static function validar_detalles($detalles)
    {
        foreach ($detalles as $detalle) {
            if (!isset($detalle['UNIDAD_MEDIDA']) && $detalle['UNIDAD_MEDIDA'] != '') {
                $resp['MENSAJE'] = 'Unidad de medida del item es requerida';
                return $resp;
            }

            if (!isset($detalle['CANTIDAD']) && $detalle['CANTIDAD'] != '' && $detalle['CANTIDAD'] > 0) {
                $resp['MENSAJE'] = 'Cantidad del item es requerida';
                return $resp;
            }

            if (!isset($detalle['DESCRIPCION']) && $detalle['DESCRIPCION'] != '') {
                $resp['MENSAJE'] = 'Descripcion del item es requerida';
                return $resp;
            }

            if (!isset($detalle['PRECIO_VALOR']) && $detalle['PRECIO_VALOR'] != '' && $detalle['PRECIO_VALOR'] > 0) {
                $resp['MENSAJE'] = 'Precio valor unitario es requerido';
                return $resp;
            }

            if (!isset($detalle['PRECIO_VENTA']) && $detalle['PRECIO_VENTA'] != '' && $detalle['PRECIO_VENTA'] >= 0) {
                $resp['MENSAJE'] = 'Precio de venta unitario es requerido';
                return $resp;
            }

            //Catalogo No. 16
            if (!isset($detalle['TIPO_PRECIO']) && ($detalle['TIPO_PRECIO'] != '01' || $detalle['TIPO_PRECIO'] != '02')) {
                $resp['MENSAJE'] = 'Tipo de precio de venta unitario no valido';
                return $resp;
            }

            if (!isset($detalle['DETALLE_TRIBUTO_IGV']) && $detalle['DETALLE_TRIBUTO_IGV'] != '' && $detalle['DETALLE_TRIBUTO_IGV'] > 0) {
                $resp['MENSAJE'] = 'Afectacion al IGV es requerido';
                return $resp;
            }

            //Catalogo No. 07
            if (!isset($detalle['TIPO_TRIBUTO_IGV']) && $detalle['TIPO_TRIBUTO_IGV'] != '') {
                $resp['MENSAJE'] = 'Tipo de precio de venta unitario no valido';
                return $resp;
            }
        }

        return FALSE;
    }

    // Elimino todos los caracteres no permitidos por SUNAT
    protected static function limpiar_caracteres($cadena)
    {
        $cadena = str_replace("'", "", $cadena);
        $cadena = str_replace("#", "", $cadena);
        $cadena = str_replace("$", "", $cadena);
        $cadena = str_replace("%", "", $cadena);
        $cadena = str_replace("&", "", $cadena);
        $cadena = str_replace("'", "", $cadena);
        $cadena = str_replace("(", "", $cadena);
        $cadena = str_replace(")", "", $cadena);
        $cadena = str_replace("*", "", $cadena);
        $cadena = str_replace("+", "", $cadena);
        $cadena = str_replace("-", "", $cadena);
        $cadena = str_replace(".", "", $cadena);
        $cadena = str_replace("/", "", $cadena);
        $cadena = str_replace("<", "", $cadena);
        $cadena = str_replace("=", "", $cadena);
        $cadena = str_replace(">", "", $cadena);
        $cadena = str_replace("?", "", $cadena);
        $cadena = str_replace("@", "", $cadena);
        $cadena = str_replace("[", "", $cadena);
        $cadena = str_replace("\\", "", $cadena);
        $cadena = str_replace("]", "", $cadena);
        $cadena = str_replace("^", "", $cadena);
        $cadena = str_replace("_", "", $cadena);
        $cadena = str_replace("`", "", $cadena);
        $cadena = str_replace("{", "", $cadena);
        $cadena = str_replace("|", "", $cadena);
        $cadena = str_replace("}", "", $cadena);
        $cadena = str_replace("~", "", $cadena);
        $cadena = str_replace("¡", "", $cadena);
        $cadena = str_replace("¢", "", $cadena);
        $cadena = str_replace("£", "", $cadena);
        $cadena = str_replace("¤", "", $cadena);
        $cadena = str_replace("¥", "", $cadena);
        $cadena = str_replace("¦", "", $cadena);
        $cadena = str_replace("§", "", $cadena);
        $cadena = str_replace("¨", "", $cadena);
        $cadena = str_replace("©", "", $cadena);
        $cadena = str_replace("ª", "", $cadena);
        $cadena = str_replace("«", "", $cadena);
        $cadena = str_replace("¬", "", $cadena);
        $cadena = str_replace("®", "", $cadena);
        $cadena = str_replace("°", "", $cadena);
        $cadena = str_replace("±", "", $cadena);
        $cadena = str_replace("²", "", $cadena);
        $cadena = str_replace("³", "", $cadena);
        $cadena = str_replace("´", "", $cadena);
        $cadena = str_replace("µ", "", $cadena);
        $cadena = str_replace("¶", "", $cadena);
        $cadena = str_replace("·", "", $cadena);
        $cadena = str_replace("¸", "", $cadena);
        $cadena = str_replace("¹", "", $cadena);
        $cadena = str_replace("º", "", $cadena);
        $cadena = str_replace("»", "", $cadena);
        $cadena = str_replace("¼", "", $cadena);
        $cadena = str_replace("½", "", $cadena);
        $cadena = str_replace("¾", "", $cadena);
        $cadena = str_replace("¿", "", $cadena);
        $cadena = str_replace("À", "A", $cadena);
        $cadena = str_replace("Á", "A", $cadena);
        $cadena = str_replace("Â", "A", $cadena);
        $cadena = str_replace("Ã", "A", $cadena);
        $cadena = str_replace("Ä", "A", $cadena);
        $cadena = str_replace("Å", "A", $cadena);
        $cadena = str_replace("Æ", "", $cadena);
        $cadena = str_replace("Ç", "", $cadena);
        $cadena = str_replace("È", "E", $cadena);
        $cadena = str_replace("É", "E", $cadena);
        $cadena = str_replace("Ê", "E", $cadena);
        $cadena = str_replace("Ë", "E", $cadena);
        $cadena = str_replace("Ì", "I", $cadena);
        $cadena = str_replace("Í", "I", $cadena);
        $cadena = str_replace("Î", "I", $cadena);
        $cadena = str_replace("Ï", "I", $cadena);
        $cadena = str_replace("Ð", "", $cadena);
        $cadena = str_replace("Ñ", "N", $cadena);
        $cadena = str_replace("Ò", "O", $cadena);
        $cadena = str_replace("Ó", "O", $cadena);
        $cadena = str_replace("Ô", "O", $cadena);
        $cadena = str_replace("Õ", "O", $cadena);
        $cadena = str_replace("Ö", "O", $cadena);
        $cadena = str_replace("×", "", $cadena);
        $cadena = str_replace("Ø", "", $cadena);
        $cadena = str_replace("Ù", "U", $cadena);
        $cadena = str_replace("Ú", "U", $cadena);
        $cadena = str_replace("Û", "U", $cadena);
        $cadena = str_replace("Ü", "U", $cadena);
        $cadena = str_replace("Ý", "Y", $cadena);
        $cadena = str_replace("Þ", "", $cadena);
        $cadena = str_replace("ß", "", $cadena);
        $cadena = str_replace("à", "a", $cadena);
        $cadena = str_replace("á", "a", $cadena);
        $cadena = str_replace("â", "a", $cadena);
        $cadena = str_replace("ã", "a", $cadena);
        $cadena = str_replace("ä", "a", $cadena);
        $cadena = str_replace("å", "a", $cadena);
        $cadena = str_replace("æ", "", $cadena);
        $cadena = str_replace("ç", "", $cadena);
        $cadena = str_replace("è", "e", $cadena);
        $cadena = str_replace("é", "e", $cadena);
        $cadena = str_replace("ê", "e", $cadena);
        $cadena = str_replace("ë", "e", $cadena);
        $cadena = str_replace("ì", "i", $cadena);
        $cadena = str_replace("í", "i", $cadena);
        $cadena = str_replace("î", "i", $cadena);
        $cadena = str_replace("ï", "i", $cadena);
        $cadena = str_replace("ð", "o", $cadena);
        $cadena = str_replace("ñ", "n", $cadena);
        $cadena = str_replace("ò", "o", $cadena);
        $cadena = str_replace("ó", "o", $cadena);
        $cadena = str_replace("ô", "o", $cadena);
        $cadena = str_replace("õ", "o", $cadena);
        $cadena = str_replace("ö", "o", $cadena);
        $cadena = str_replace("÷", "", $cadena);
        $cadena = str_replace("ø", "", $cadena);
        $cadena = str_replace("ù", "u", $cadena);
        $cadena = str_replace("ú", "u", $cadena);
        $cadena = str_replace("û", "u", $cadena);
        $cadena = str_replace("ü", "u", $cadena);
        $cadena = str_replace("ý", "y", $cadena);
        $cadena = str_replace("þ", "", $cadena);
        $cadena = str_replace("ÿ", "", $cadena);
        $cadena = str_replace("Œ", "", $cadena);
        $cadena = str_replace("œ", "", $cadena);
        $cadena = str_replace("Š", "", $cadena);
        $cadena = str_replace("š", "", $cadena);
        $cadena = str_replace("Ÿ", "", $cadena);
        $cadena = str_replace("ƒ", "", $cadena);
        $cadena = str_replace("–", "", $cadena);
        $cadena = str_replace("—", "", $cadena);
        $cadena = str_replace("‘", "", $cadena);
        $cadena = str_replace("’", "", $cadena);
        $cadena = str_replace("‚", "", $cadena);
        $cadena = str_replace("“", "", $cadena);
        $cadena = str_replace("”", "", $cadena);
        $cadena = str_replace("„", "", $cadena);
        $cadena = str_replace("†", "", $cadena);
        $cadena = str_replace("‡", "", $cadena);
        $cadena = str_replace("•", "", $cadena);
        $cadena = str_replace("…", "", $cadena);
        $cadena = str_replace("‰", "", $cadena);
        $cadena = str_replace("€", "", $cadena);
        $cadena = str_replace("™", "", $cadena);

        return $cadena;
    }

    protected static function numero_comprobante($tipo_comprobante, $numero)
    {
        switch ($tipo_comprobante) {
            case \TIPO_COMPROBANTE::$FACTURA : {
                return preg_match('/^(F)([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
            case \TIPO_COMPROBANTE::$BOLETA : {
                return preg_match('/^(B)([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
            case \TIPO_COMPROBANTE::$NOTA_CREDITO : {
                return preg_match('/^([BF])([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
            case \TIPO_COMPROBANTE::$NOTA_DEBITO : {
                return preg_match('/^([BF]{1})([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
        }
    }

    protected static function fecha($fecha)
    {
        return preg_match('/^(2[0-9]{3})(-)(0[1-9]|1[0-2])(-)([0-2][0-9]|3[0-1])$/', $fecha) == 0 ? false : true;
    }

    protected static function identificacion($tipo, $identificacion)
    {
        switch ($tipo) {
            case \TIPO_IDENTIDAD::$DNI : {
                return preg_match('/^([0-9]{8})$/', $identificacion) == 0 ? false : true;
            }
            case \TIPO_IDENTIDAD::$RUC : {
                return preg_match('/^([0-9]{11})$/', $identificacion) == 0 ? false : true;
            }
            default: {
                return preg_match('/^([0-9A-Z]{15})$/', $identificacion) == 0 ? false : true;
            }
        }
    }

    protected function validarNumero($numero, $max = 12, $max_decimal = 2)
    {
        return $numero == 0 || preg_match('/^([0-9]{1,' . $max . '})(.)([0-9]{1,' . $max_decimal . '})$/', $numero) == 0 ? false : true;
    }
}