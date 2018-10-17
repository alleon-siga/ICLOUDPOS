<?php

function formatPrice($price, $min = 10)
{

    $price = explode('.', number_format($price, 2, '.', ''));
    $entero = $price[0];
    $fraccion = $price[1];

    for ($i = 0; $i <= 100; $i = $i + $min) {
        if ($i < $fraccion && $i + $min > $fraccion) {
            if (($i + $min - $fraccion) <= ($fraccion - $i))
                $fraccion = $i + $min;
            else if (($i + $min - $fraccion) > ($fraccion - $i))
                $fraccion = $i;

            if ($fraccion == 100) {
                $fraccion = 0;
                $entero = $entero + 1;
            }
        }
    }

    return number_format($entero . '.' . $fraccion, 2, '.', '');
}

function get_moneda_defecto()
{
    $CI =& get_instance();
    return $CI->db->get_where('moneda', array('id_moneda' => MONEDA_DEFECTO))->row();
}

function diff_date($ini, $fin)
{
    // $datetime1 = new DateTime($ini);
    $datetime1 = DateTime::createFromFormat('d/m/Y', $ini);
    $datetime2 = DateTime::createFromFormat('d/m/Y', $fin);
    $interval = $datetime1->diff($datetime2);
    return $interval->format('%a');
}

function get_tipo_doc($cod)
{
    switch ($cod) {
        case 3: {
            return array('code' => $cod, 'value' => 'Boleta de Venta');
        }
        case 1: {
            return array('code' => $cod, 'value' => 'Factura');
        }
        case 7: {
            return array('code' => $cod, 'value' => 'Nota de Cr&eacute;dito');
        }
        case 8: {
            return array('code' => $cod, 'value' => 'Nota de D&eacute;bito');
        }
        case 20: {
            return array('code' => $cod, 'value' => 'Comprobante de Retenci&oacute;n');
        }
        case 31: {
            return array('code' => $cod, 'value' => 'Gu&iacute;a de Remisi&oacute;n - Transportista');
        }
        case 9: {
            return array('code' => $cod, 'value' => 'Gu&iacute;a de Remisi&oacute;n');
        }
        case -2: {
            return array('code' => $cod, 'value' => 'Nota de Venta');
        }
        case -3: {
            return array('code' => $cod, 'value' => 'Control Interno');
        }
        default: {
            return array('code' => 0, 'value' => 'Otros');
        }
    }
}


function get_tipo_operacion($cod)
{
    switch ($cod) {
        case 2: {
            return array('code' => $cod, 'value' => 'COMPRA');
        }
        case 1: {
            return array('code' => $cod, 'value' => 'VENTA');
        }
        case 5: {
            return array('code' => $cod, 'value' => 'DEVOLUCI&Oacute;N RECIBIDA');
        }
        case 6: {
            return array('code' => $cod, 'value' => 'DEVOLUCI&Oacute;N ENTREGADA');
        }
        case 7: {
            return array('code' => $cod, 'value' => 'PROMOCI&Oacute;N');
        }
        case 9: {
            return array('code' => $cod, 'value' => 'DONACI&Oacute;N');
        }
        case 11: {
            return array('code' => $cod, 'value' => 'TRANSFERENCIA ENTRE ALMACENES');
        }
        case 12: {
            return array('code' => $cod, 'value' => 'RETIRO');
        }
        case 13: {
            return array('code' => $cod, 'value' => 'MERMAS');
        }
        case 14: {
            return array('code' => $cod, 'value' => 'DESMEDROS');
        }
        case 15: {
            return array('code' => $cod, 'value' => 'DESTRUCCION');
        }
        case 16: {
            return array('code' => $cod, 'value' => 'SALDO INICIAL');
        }
        default: {
            return array('code' => 0, 'value' => 'Otros');
        }
    }
}

function get_sunat_documento($code = false)
{
    $array = array(
        '00' => 'Control Interno',
        '07' => 'Nota de Credito',
        '09' => 'Guia de Remision'
    );

    if ($code == false) {
        return $array;
    } else {
        return $array[$code];
    }
}

function get_sunat_operacion($code = false)
{
    $array = array(
        '07' => 'Promocion',
        '09' => 'Donacion',
        '12' => 'Retiro',
        '13' => 'Mermas',
        '14' => 'Desmedros',
        '15' => 'Destruccion',
        '16' => 'Saldo Inicial',
        '99' => 'Otros',
    );

    if ($code === false) {
        return $array;
    } else {
        return $array[$code];
    }
}

function validOption($config_value, $value, $default = 'NO')
{
    $CI =& get_instance();
    if ($CI->session->userdata($config_value) == NULL) {
        return $value == $default ? true : false;
    }
    return $CI->session->userdata($config_value) == $value ? true : false;
}

function validOptionDB($config_value, $value, $default = 'NO')
{
    $CI =& get_instance();
    $CI->load->model('opciones/opciones_model');
    $config = $CI->opciones_model->get_opcion($config_value);
    $config = isset($config[0]['config_value']) ? $config[0]['config_value'] : $default;
    return $config == $value ? true : false;
}

function valueOption($config_value, $default = 'NO')
{
    $CI =& get_instance();
    if ($CI->session->userdata($config_value) == NULL) {
        return $default;
    }
    return $CI->session->userdata($config_value);
}

function valueOptionDB($config_value, $default = 'NO')
{
    $CI =& get_instance();
    $CI->load->model('opciones/opciones_model');
    $config = $CI->opciones_model->get_opcion($config_value);
    $config = isset($config[0]['config_value']) ? $config[0]['config_value'] : $default;
    return $config;
}

function isContableActivo()
{
    $CI =& get_instance();
    $cont = $CI->session->userdata('CONTABLE_COSTO') == 'SI' ? true : false;
    return $cont;
}

function isVentaActivo()
{
    $CI =& get_instance();
    $venta = $CI->session->userdata('VENTAS_COBRAR') == 'SI' ? true : false;
    return $venta;
}

function cantidad_ventas_cobrar()
{
    $CI =& get_instance();
    $CI->db->where('venta_status', 'COBRO');
    $CI->db->from('venta');
    return $CI->db->count_all_results();
}

function sumCod($cod, $length = 4)
{
    $len = $length;

    if ($len < count(str_split($cod))) $len++;

    $temp = array_reverse(str_split($cod));
    $result = array();

    $n = 0;
    for ($i = $len - 1; $i >= 0; $i--) {
        if (isset($temp[$n]))
            $result[] = $temp[$n++];
        else
            $result[] = '0';
    }
    return implode(array_reverse($result));
}

function getCodigo()
{
    $CI =& get_instance();
    $CI->load->model('opciones/opciones_model');

    $codigo = $CI->opciones_model->get_opcion("CODIGO_DEFAULT");
    $codigo = isset($codigo[0]['config_value']) ? $codigo[0]['config_value'] : "AUTO";

    return $codigo;
}

function getCodigoNombre()
{
    $codigo = getCodigo();

    if ($codigo == "AUTO")
        return "ID";
    elseif ($codigo == "INTERNO")
        return "Código";
}

function getCodigoValue($id, $interno)
{
    $codigo = getCodigo();

    if ($codigo == "AUTO")
        return $id;
    elseif ($codigo == "INTERNO")
        return $interno;
}

function canShowCodigo()
{
    $CI =& get_instance();
    $CI->load->model('columnas/columnas_model');
    $codigo = getCodigo();

    if ($codigo == "AUTO")
        $col = $CI->columnas_model->getColumn('producto_id');
    elseif ($codigo == "INTERNO")
        $col = $CI->columnas_model->getColumn('producto_codigo_interno');

    return $col->mostrar;
}

function getValorUnico()
{
    $CI =& get_instance();
    $CI->load->model('opciones/opciones_model');

    $codigo = $CI->opciones_model->get_opcion("VALOR_UNICO");
    $codigo = isset($codigo[0]['config_value']) ? $codigo[0]['config_value'] : "NOMBRE";

    return $codigo;
}

function getProductoSerie()
{
    $CI =& get_instance();
    $CI->load->model('opciones/opciones_model');

    $codigo = $CI->opciones_model->get_opcion("PRODUCTO_SERIE");
    $codigo = isset($codigo[0]['config_value']) ? $codigo[0]['config_value'] : "NO";

    return $codigo;
}


function get_imagen_producto($id)
{

    $result = array();
    $dir = './uploads/' . $id . '/';
    if (!is_dir($dir)) return array();
    $temp = scandir($dir);
    foreach ($temp as $img) {
        if (is_file($dir . $img))
            $result[] = $img;
    }
    natsort($result);

    return $result;
}

function get_total_minimas($producto_id, $cantidad, $fraccion)
{

    $CI =& get_instance();
    $CI->load->model('unidades/unidades_model');
    $old_cantidad_min = $CI->unidades_model->convert_minimo_um($producto_id, $cantidad, $fraccion);

    return $old_cantidad_min;
}

function get_cantidad_total_stock($producto_id, $old_cantidad_min)
{

    $CI =& get_instance();
    $CI->load->model('unidades/unidades_model');
    $cantidad_total = $CI->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min);

    return $cantidad_total;
}

function last_day($year, $mes)
{
    return date("d", (mktime(0, 0, 0, $mes + 1, 1, $year) - 1));
}

function get_tipo_credito($id)
{
    $credito = array(
        1 => 'Diario',
        2 => 'Interdiario',
        3 => 'Semanal',
        4 => 'Menusal',
        5 => 'Personalizado'
    );

    return $credito[$id];
}

function getMes($num)
{
    switch ($num) {
        case 1: {
            return 'Enero';
        }
        case 1: {
            return 'Enero';
        }
        case 2: {
            return 'Febrero';
        }
        case 3: {
            return 'Marzo';
        }
        case 4: {
            return 'Abril';
        }
        case 5: {
            return 'Mayo';
        }
        case 6: {
            return 'Junio';
        }
        case 7: {
            return 'Julio';
        }
        case 8: {
            return 'Agosto';
        }
        case 9: {
            return 'Septiembre';
        }
        case 10: {
            return 'Octubre';
        }
        case 11: {
            return 'Noviembre';
        }
        case 12: {
            return 'Diciembre';
        }
    }
}

function numtoletras($xcifra, $moneda_nombre = 'Soles')
{
    $xarray = array(0 => "Cero",
        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
    );
//
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false)) {
        if ($xpos_punto == 0) {
            $xcifra = "0" . $xcifra;
            $xpos_punto = strpos($xcifra, ".");
        }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
    }

    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for ($xz = 0; $xz < 3; $xz++) {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0;
        $xlimite = 6; // inicializo el contador de centenas xi y establezco el l�mite a 6 d�gitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While
        while ($xexit) {
            if ($xi == $xlimite) { // si ya lleg� al l�mite m�ximo de enteros
                break; // termina el ciclo
            }

            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d�gitos)
            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                switch ($xy) {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres d�gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                        } else {
                            $key = (int)substr($xaux, 0, 3);
                            if (TRUE === array_key_exists($key, $xarray)) {  // busco si la centena es n�mero redondo (100, 200, 300, 400, etc..)
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Mill�n, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100)
                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                            } else { // entra aqu� si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                $key = (int)substr($xaux, 0, 1) * 100;
                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " " . $xcadena . " " . $xseek;
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // checa las decenas (con la misma l�gica que las centenas)
                        if (substr($xaux, 1, 2) < 10) {

                        } else {
                            $key = (int)substr($xaux, 1, 2);
                            if (TRUE === array_key_exists($key, $xarray)) {
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3;
                            } else {
                                $key = (int)substr($xaux, 1, 1) * 10;
                                $xseek = $xarray[$key];
                                if (20 == substr($xaux, 1, 1) * 10)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // checa las unidades
                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                        } else {
                            $key = (int)substr($xaux, 2, 1);
                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = subfijo($xaux);
                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                        } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                } // END SWITCH
            } // END FOR
            $xi = $xi + 3;
        } // ENDDO

        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
            $xcadena .= " DE";

        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
            $xcadena .= " DE";

        // ----------- esta l�nea la puedes cambiar de acuerdo a tus necesidades o a tu pa�s -------
        if (trim($xaux) != "") {
            switch ($xz) {
                case 0:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena .= "UN BILLON ";
                    else
                        $xcadena .= " BILLONES ";
                    break;
                case 1:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena .= "UN MILLON ";
                    else
                        $xcadena .= " MILLONES ";
                    break;
                case 2:
                    if ($xcifra < 1) {
                        $xcadena = "CERO " . strtoupper($moneda_nombre) . " $xdecimales/100 ";
                    }
                    if ($xcifra >= 1 && $xcifra < 2) {
                        $xcadena = "UN " . strtoupper($moneda_nombre) . " $xdecimales/100  ";
                    }
                    if ($xcifra >= 2) {
                        $xcadena .= " " . strtoupper($moneda_nombre) . " $xdecimales/100  "; //
                    }
                    break;
            } // endswitch ($xz)
        } // ENDIF (trim($xaux) != "")
        // ------------------      en este caso, para M�xico se usa esta leyenda     ----------------
        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
    } // ENDFOR ($xz)
    return trim($xcadena);
}

// END FUNCTION

function subfijo($xx)
{ // esta funci�n regresa un subfijo para la cifra
    $xx = trim($xx);
    $xstrlen = strlen($xx);
    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
        $xsub = "";
    //
    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
        $xsub = "MIL";
    //
    return $xsub;
}

function diccionarioTermino()
{
    $CI =& get_instance();
    return $CI->db->get_where('diccionario_termino', array('activo' => '1'))->result();
}

//Preparo el flashdata inicial y se lo asigno al $data.
// Nota: esto debe ir al principio de los controllers para no sobrescribir lo que se agrega despues
function _prepareFlashData()
{
    $data = array();
    $CI =& get_instance();
    $CI->load->model('local/local_model');

    if ($CI->session->flashdata('success') != FALSE) {
        $data['success'] = $CI->session->flashdata('success');
    }
    if ($CI->session->flashdata('error') != FALSE) {
        $data['error'] = $CI->session->flashdata('error');
    }
    if ($CI->session->userdata('esSuper') == 1) {
        $data['locales'] = $CI->local_model->get_all();
    } else {
        $usu = $CI->session->userdata('nUsuCodigo');
        $data['locales'] = $CI->local_model->get_all_usu($usu);
    }

    return $data;
}