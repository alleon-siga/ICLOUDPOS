<?php

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 5/10/2018
 * Time: 12:12 PM
 */
class Validacion
{
    public function check_is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // Elimino todos los caracteres no permitidos por SUNAT
    public function limpiar_caracteres($cadena)
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

    public function validarVenta($data_comprobante)
    {
        if (!$this->indetificacion($data_comprobante['TIPO_DOCUMENTO_EMPRESA'], $data_comprobante['NRO_DOCUMENTO_EMPRESA'])) {
            return array(
                'respuesta' => 'error',
                'msg_validacion' => 'El numero de documento del emisor no es valido'
            );
        }

        if ($data_comprobante['RAZON_SOCIAL_EMPRESA'] == '') {
            return array(
                'respuesta' => 'error',
                'msg_validacion' => 'La razon social del emisor es obligatoria'
            );
        }

        if (!$this->numero_comprobante($data_comprobante['COD_TIPO_DOCUMENTO'], $data_comprobante['NRO_COMPROBANTE'])) {
            return array(
                'respuesta' => 'error',
                'msg_validacion' => 'El numero de documento no es valido'
            );
        }

        if (!$this->fecha($data_comprobante['FECHA_DOCUMENTO'])) {
            return array(
                'respuesta' => 'error',
                'msg_validacion' => 'La fecha del documento no es valida'
            );
        }

        if ($data_comprobante['COD_TIPO_DOCUMENTO'] == TIPO_COMPROBANTE::$BOLETA) {
            if ($data_comprobante['TOTAL'] > 700) {
                if (!$this->indetificacion($data_comprobante['TIPO_DOCUMENTO_CLIENTE'], $data_comprobante['NRO_DOCUMENTO_CLIENTE'])) {
                    return array(
                        'respuesta' => 'error',
                        'msg_validacion' => 'El numero de documento del cliente no es valido'
                    );
                }
            } else {
                $data_comprobante['TIPO_DOCUMENTO_CLIENTE'] = TIPO_IDENTIDAD::$DOC_TRIB_NO_DOM_SIN_RUC;
                $data_comprobante['NRO_DOCUMENTO_CLIENTE'] = '';
                $data_comprobante['RAZON_SOCIAL_CLIENTE'] = '';
                $data_comprobante['DIRECCION_CLIENTE'] = '';
            }
        }


        return array('respuesta' => 'ok');
    }

    public function validarNotaCredito($data_comprobante)
    {

        $venta = $this->validarVenta($data_comprobante);
        if ($venta['respuesta'] == 'ok') {
            if ($data_comprobante['TIPO_COMPROBANTE_MODIFICA'] == '') {
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'El tipo de comprobante que modifica es requerido'
                );
            }
            if ($data_comprobante['NRO_DOCUMENTO_MODIFICA'] == '') {
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'El numero de comprobante que modifica es requerido'
                );
            }
            if ($data_comprobante['COD_TIPO_MOTIVO'] == '') {
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'El tipo de motivo de comprobante que modifica es requerido'
                );
            }
            if ($data_comprobante['DESCRIPCION_MOTIVO'] == '') {
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'la descripcion del motivo del comprobante que modifica es requerido'
                );
            }

            if (!$this->numero_comprobante($data_comprobante['TIPO_COMPROBANTE_MODIFICA'], $data_comprobante['NRO_DOCUMENTO_MODIFICA'])) {
                return array(
                    'respuesta' => 'error',
                    'msg_validacion' => 'El numero de documento que modifica no es valido'
                );
            }

            return array('respuesta' => 'ok');
        } else
            return $venta;

    }

    protected function numero_comprobante($tipo_comprobante, $numero)
    {
        switch ($tipo_comprobante) {
            case TIPO_COMPROBANTE::$FACTURA : {
                return preg_match('/^(F)([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
            case TIPO_COMPROBANTE::$BOLETA : {
                return preg_match('/^(B)([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
            case TIPO_COMPROBANTE::$NOTA_CREDITO : {
                return preg_match('/^([BF])([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
            case TIPO_COMPROBANTE::$NOTA_DEBITO : {
                return preg_match('/^([BF]{1})([0-9A-Z]{3})(-)([0-9]{1,8})$/', $numero) == 0 ? false : true;
            }
        }
    }

    protected function fecha($fecha)
    {
        return preg_match('/^(2[0-9]{3})(-)(0[1-9]|1[0-2])(-)([0-2][0-9]|3[0-1])$/', $fecha) == 0 ? false : true;
    }

    protected function indetificacion($tipo, $identificacion)
    {
        switch ($tipo) {
            case TIPO_IDENTIDAD::$DNI : {
                return preg_match('/^([0-9]{8})$/', $identificacion) == 0 ? false : true;
            }
            case TIPO_IDENTIDAD::$RUC : {
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