<?php
function get_moneda_defecto()
{
    $CI =& get_instance();
    return $CI->db->get_where('moneda', array('id_moneda' => MONEDA_DEFECTO))->row();
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
        $array[$code];
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

    if ($code == false) {
        return $array;
    } else {
        $array[$code];
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
