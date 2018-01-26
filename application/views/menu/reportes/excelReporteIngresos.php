<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ExcelReporteIngresos.xls");
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
            ><?php if(isset($local_detalle)){
                echo $local_detalle['local_nombre'];
            }else{
                echo "INGRESOS: ";
            }  ?></td>
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

if(isset($ingresos)) {

    $html .= "<tr>
            <th>ID Ingreso</th>
            <th>Tipo Documento</th>
            <th>Nro Documento</th>
            <th>Fecha Registro</th>
            <th>Fecha Valorizaci&oacute;n</th>
            <th>Proveedor</th>
            <th>Responsable</th>
            <th>Local</th>
            <th>Tipo Pago</th>
            <th>Total</th> ";
           if ($this->session->userdata('FACTURAR_INGRESO') == "SI"){
                /*si la opcion es SI, mostramos esta cabecera*/
               $html .= "<th> Facturaci&oacute;n </th>";
            }
    $html .= "<th>Estado</th></tr>";

    foreach ($ingresos as $ingreso) {
        $html .= " <tr><td align='center'>".$ingreso->id_ingreso."</td>";

        $html .= "<td align='center' >";

        if($ingreso->tipo_documento=="FACTURA") $html .= "FA";
        if($ingreso->tipo_documento==2) $html .= "NC";
        if($ingreso->tipo_documento=="BOLETA DE VENTA") $html .= "BO";
        if($ingreso->tipo_documento==4) $html .= "GR";
        if($ingreso->tipo_documento==5) $html .= "PCV";
        if($ingreso->tipo_documento=="NOTA DE PEDIDO")$html .= "NP";
        $html.="</td>";
        $html.="<td align='center' >".$ingreso->documento_serie ."-".$ingreso->documento_numero. "</td>";

        $html .="</td><td align='center' >" . date('d-m-Y H:i:s', strtotime($ingreso->fecha_registro)) . "</td>";
        $html .= "<td align='center'>";
        if($ingreso->fecha_emision==null){
            $html .= "--";
        }else{
            $html .= date('d-m-Y', strtotime($ingreso->fecha_emision)); }
        $html .= "</td><td align='center'>" . $ingreso->proveedor_nombre . "</td>";
        $html .= "<td align='center'>" . $ingreso->username . "</td><td>" . $ingreso->local_nombre . "</td><td align='center'>" . $ingreso->pago . "</td><td align='center'>";
        if($ingreso->total_ingreso>0.00){
            $html.= $ingreso->simbolo. " ";
        }
        $html.= $ingreso->total_ingreso  . "</td>";

        $pertenece="'INGRESONORMAL'";
        /*color del estatus, la coloco por defecto en danger, que es cuando esta en pendiente*/
        $etiqueta='label-danger';
        $status=PENDIENTE;
        $facturado="PENDIENTE";

        $etiqueta_facturar='label-danger';

        if(($ingreso->ingreso_status=="COMPLETADO" ) and $this->session->userdata('FACTURAR_INGRESO') == "SI" ){
            $status="COMPLETADO";
            $etiqueta='label-success';
        }

        if(($ingreso->ingreso_status=="COMPLETADO" ) and $this->session->userdata('FACTURAR_INGRESO') == "NO" ){
            $status="COMPLETADO";
            $etiqueta='label-success';
        }

        if($ingreso->ingreso_status=="FACTURADO" and $this->session->userdata('FACTURAR_INGRESO') == "SI" ){
            $etiqueta_facturar='label-success';
            $pertenece="'INGRESOCONTABLE'";
            $facturado="FACTURADO";
            $status="COMPLETADO";
            $etiqueta='label-success';
        }

        if($ingreso->ingreso_status=="CERRADO" ){
            $etiqueta_facturar='label-success';
            $facturado="NO APLICA";
            $status="CERRADO";
            $etiqueta='label-success';
        }


        if ($this->session->userdata('FACTURAR_INGRESO') == "SI"){

                         $html.="<td align='center'><label class='label ".$etiqueta_facturar."'>".$facturado."</label> </td>";
                 }
        $html.="<td align='center'><label class='label ".$etiqueta ."'> ".$status." </label></td>";
        $html.="</tr>";



    }



    if(isset($ingresos_cerrados_normales)) {

        foreach ($ingresos_cerrados_normales as $ingreso) {

            $html .= " <tr><td align='center'>".$ingreso->id_ingreso."</td>";

            $html .= "<td align='center' >";

            if($ingreso->tipo_documento=="FACTURA") $html .= "FA";
            if($ingreso->tipo_documento==2) $html .= "NC";
            if($ingreso->tipo_documento=="BOLETA DE VENTA") $html .= "BO";
            if($ingreso->tipo_documento==4) $html .= "GR";
            if($ingreso->tipo_documento==5) $html .= "PCV";
            if($ingreso->tipo_documento=="NOTA DE PEDIDO")$html .= "NP";
            $html.="</td>";
            $html.="<td align='center' >".$ingreso->documento_serie ."-".$ingreso->documento_numero. "</td>";

            $html .="</td><td align='center' >" . date('d-m-Y H:i:s', strtotime($ingreso->fecha_registro)) . "</td>";
            $html .= "<td align='center'>";
            if($ingreso->fecha_emision==null){
                $html .= "--";
            }else{
                $html .= date('d-m-Y', strtotime($ingreso->fecha_emision)); }
            $html .= "</td><td align='center'>" . $ingreso->proveedor_nombre . "</td>";
            $html .= "<td align='center'>" . $ingreso->username . "</td><td>" . $ingreso->local_nombre . "</td><td align='center'>" . $ingreso->pago . "</td><td align='center'>";
            if($ingreso->total_ingreso>0.00){
                $html.= $ingreso->simbolo;
            }
            $html.= $ingreso->total_ingreso  . "</td>";

            $pertenece="'INGRESONORMAL'";
            /*color del estatus, la coloco por defecto en danger, que es cuando esta en pendiente*/
            $etiqueta='label-danger';
            $status=PENDIENTE;
            $facturado="PENDIENTE";

            $etiqueta_facturar='label-danger';

            if(($ingreso->ingreso_status=="COMPLETADO" )and $this->session->userdata('FACTURAR_INGRESO') == "SI" ){
                $status="COMPLETADO";
                $etiqueta='label-success';
            }

            if(($ingreso->ingreso_status=="COMPLETADO" ) and $this->session->userdata('FACTURAR_INGRESO') == "NO" ){
                $status="COMPLETADO";
                $etiqueta='label-success';
            }

            if($ingreso->ingreso_status=="FACTURADO" and $this->session->userdata('FACTURAR_INGRESO') == "SI" ){
                $etiqueta_facturar='label-success';
                $pertenece="'INGRESOCONTABLE'";
                $facturado="FACTURADO";
                $status="COMPLETADO";
                $etiqueta='label-success';
            }

            if($ingreso->ingreso_status=="CERRADO" ){
                $etiqueta_facturar='label-success';
                $facturado="NO APLICA";
                $status="CERRADO";
                $etiqueta='label-success';
            }


            if ($this->session->userdata('FACTURAR_INGRESO') == "SI"){

                $html.="<td align='center'><label class='label ".$etiqueta_facturar."'>".$facturado."</label> </td>";
            }
            $html.="<td align='center'><label class='label ".$etiqueta ."'> ".$status." </label></td>";
            $html.="</tr>";
        }
    }
}elseif(isset($compras)){
    $html .= "<tr><th>ID</th> <th>"; echo getCodigoNombre(); $html.="</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Unidad de Medida</th><th>Sub total</th></tr>";

    foreach ($compras as $compra) {
        $html .= " <tr><td>".  $compra->id_detalle_ingreso ."</td><td>";
                                    echo getCodigoValue(sumCod($compra->id_producto),$compra->producto_codigo_interno);
        $html .= "</td><td >" . $compra->producto_nombre. "</td><td >" . $compra->cantidad . "</td>";
        $html .= "<td >" .$compra->simbolo." ".$compra->precio . "</td><td >".$compra->nombre_unidad."</td><td>".$compra->simbolo." ".number_format($compra->precio*$compra->cantidad,2)."</td></tr>";
    }

}

$html .= "</table>";
echo $html;
?>