<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Reporte-Valorizacion-Inventario.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
    <table >
        <tr>
            <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
                colspan="5"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
            </td>
        </tr>
        <tr>
            <td width="12%" colspan="10" style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
                >Valorizacion de inventario</td>
            <td width="12%">&nbsp;&nbsp;</td>

            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
        </tr>

        <tr>
            <td width="18%" colspan="5" style="font-weight: bold;">Fecha Emisi&oacute;n: <?php echo date("d-m-Y H:i:s"); ?></td>
            <td width="25%"></td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="12%">&nbsp;&nbsp;</td>
            <td width="7%">&nbsp;&nbsp;</td>
            <td width="5%">&nbsp;&nbsp;</td>
            <td width="5%">&nbsp;&nbsp;</td>
            <td width="5%">&nbsp;&nbsp;</td>


        </tr>
        <tr>
            <td colspan="8"></td>
        </tr>
    </table>


    <table border="1" bordercolor="#FFFFFF" cellpadding="1" cellspacing="0">
    <?php
$html = "<tr>";
 for ($i = 0; $i < count($columna); $i++) {

     $html .= "<th>".$columna[$i]."</th>";
 }
$html .= "</tr>";


if (isset($productos)) {
    $total = 0;
    foreach ($productos as $producto) {


        $subtotal = $producto['stock'] * $producto['producto_costo_unitario'];

        $total = $subtotal + $total;


        $precio = $producto['precio'];
        $producto_costo_unitario = $producto['producto_costo_unitario'];
        if (isset($operacion)) {
            $string = '$precio$operacion$tasa_soles ';
            eval("\$string = \"$string\";");
            eval("\$precio = ($string);");

            $string = '$producto_costo_unitario$operacion$tasa_soles ';
            eval("\$string = \"$string\";");;
            eval("\$producto_costo_unitario = ($string);");

            $string = '$subtotal$operacion$tasa_soles ';
            eval("\$string = \"$string\";");
            eval("\$subtotal = ($string);");
        }

        $html .= " <tr>";
        $html .= " <td>".$producto['producto_id']."</td>";
        $html .= " <td>".utf8_decode($producto['producto_nombre'])."</td>";
        $html .= " <td>".utf8_decode($producto['nombre_marca'])."</td>";
        $html .= " <td>".utf8_decode($producto['nombre_grupo'])."</td>";
        $html .= " <td>".$producto['nombre_unidad']."</td>";
        $html .= " <td>".$moneda_nombre."</td>";
        $html .= " <td>".number_format($precio,2,'.',',')."</td>";
        $html .= " <td>".number_format($producto_costo_unitario, 2,'.',',')."</td>";
        $html .= " <td>".number_format($producto['stock'],2,'.',',')."</td>";
        $html .= " <td>". number_format($subtotal, 2,'.',',')."</td>";
        $html .= " </tr>";

    }
}


$html .= "</table>";
$html .='<table><tr><td colspan="9" align="right"><b>Total:</b></td><td>';

$simbolo=$moneda_simbolo;
if (isset($operacion)) {
    $precio = $total;
    $string = ' $precio$operacion$tasa_soles ';
    //   echo $string. "<br>";
    eval("\$string = \"$string\";");
    eval("\$string = \"$string\";");
    eval("\$result = ($string);");

    $html .= $simbolo." ".number_format($result, 2);
} else {
    $html .= $simbolo." ".number_format($total, 2);
} 
$html .= "</td></tr></table>";

echo $html;
?>