<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=IngresoDetallado.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="9">REPORTE DETALLADO DE INGRESOS
        </td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>

    <tr>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>


        <td style="font-weight: bold;">Fecha Emision:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table border="1">
    <thead>
    <tr>

        <th style="text-align: center;">ID </th>
        <th style="text-align: center;">Tipo Doc.</th>
        <th style="text-align: center;">Num Doc.</th>
        <th style="text-align: center;">Fecha Ingreso</th>
        <th style="text-align: center;">Hora</th>
        <th style="text-align: center;">Forma Pago</th>
        <th style="text-align: center;">Importe Pendiente</th>
        <th style="text-align: center;">Proveedor</th>
        <th style="text-align: center;">Cod. Producto </th>
        <th style="text-align: center;">Producto</th>
        <th style="text-align: center;">Cantidad</th>
        <th style="text-align: center;">UM</th>
        <th style="text-align: center;">P.U. Compra</th>
        <th style="text-align: center;">Sub Total</th>
        <th style="text-align: center;">P.U Venta</th>
        <th style="text-align: center;">Usuario</th>
        <?php  if(isset($locales) and count($locales)>1){ ?>
            <th style="text-align: center;">Locales</th>

        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php if (count($ingresos) > 0): ?>

        <?php
        foreach

        ($ingresos as $ingreso): ?>

            <tr>

                <td style="text-align: center;"><?= $ingreso->id_ingreso ?></td>
                <td style="text-align: center;"><?php

                    if($ingreso->tipo_documento=="FACTURA") echo "FA";
                    if($ingreso->tipo_documento==2) echo "NC";
                    if($ingreso->tipo_documento=="BOLETA DE VENTA" || $ingreso->tipo_documento==3 ) echo "BO";
                    if($ingreso->tipo_documento==4) echo "GR";
                    if($ingreso->tipo_documento==5) echo "PCV";
                    if($ingreso->tipo_documento=="NOTA DE PEDIDO" || $ingreso->tipo_documento==6) echo "NP";
                    ?></td>
                <td style="text-align: center;"><?= $ingreso->documento_serie."-".$ingreso->documento_numero ?></td>
                <td style="text-align: center;"><?= date('d-m-Y', strtotime($ingreso->fecha_registro)) ?></td>
                <td style="text-align: center;"><?= date('H:i', strtotime($ingreso->fecha_registro)) ?></td>
                <td style="text-align: center;"><?= $ingreso->pago ?></td>
                <td style="text-align: center;"><?= $ingreso->restante==null? $moneda_local->simbolo." 0.00" : $moneda_local->simbolo." ".$ingreso->restante ?></td>
                <td style="text-align: center;"><?= $ingreso->proveedor_nombre ?></td>
                <td style="text-align: center;"><?= getCodigoValue($ingreso->id_producto,$ingreso->producto_codigo_interno) ?></td>
                <td style="text-align: left;"><?= $ingreso->producto_nombre ?></td>
                <td style="text-align: center;"><?= number_format($ingreso->cantidad) ?></td>
                <td style="text-align: center;"><?= $ingreso->nombre_unidad ?></td>
                <td style="text-align: right;">
                    <?php
                    $subtotal_dolares=false;
                    if($ingreso->nombre=="Dolares"){
                        echo $moneda_local->simbolo." ".number_format($ingreso->precio*$ingreso->tasa_cambio,2);
                        $subtotal_dolares=$ingreso->total_detalle*$ingreso->tasa_cambio;
                    }else{
                        echo  $moneda_local->simbolo." ".number_format($ingreso->precio,2);

                    }  ?>
                </td>
                <td style="text-align: right;"><?= $subtotal_dolares==false ? $moneda_local->simbolo." ".number_format($ingreso->total_detalle,2) : $moneda_local->simbolo." ".number_format($subtotal_dolares,2) ?></td>
                <td style="text-align: right;"><?= $ingreso->precio_venta==null?  $moneda_local->simbolo." 0.00" :  $moneda_local->simbolo." ".number_format($ingreso->precio_venta,2) ?></td>
                <td style="text-align: center;"><?= $ingreso->username ?></td>


                <?php  if(isset($locales) and count($locales)>1){ ?>
                    <td style="text-align: center;"><?= $ingreso->local_nombre ?></td>
                <?php } ?>

            </tr>
        <?php endforeach ?>
    <?php endif; ?>

    </tbody>
</table>