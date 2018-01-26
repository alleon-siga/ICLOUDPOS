<style type="text/css">
    table{
        width: 100%;
        border-color: #111 1px solid;
    }
    thead , th{
        background: #585858;
        /* #e7e6e6*/
        border-color: #111 1px solid;
        color:#fff;
    }
    tbody tr{
        border-color: #111 1px solid;
    }
</style>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="8" >REPORTE DETALLADO DE INGRESOS</td>
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
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
        <td width="20%">&nbsp;&nbsp;</td>
        <td width="18%">&nbsp;&nbsp;</td>
        <td width="10%" style="font-weight: bold;text-align: center;"></td>
        <td width="10%" style="text-align: center;"></td>
        <td width="5%" style="font-weight: bold; text-align: center;"></td>
        <td width="14%" style="text-align: center;"></td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="7%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("Y-m-d H:i:s");?></td>
    </tr>
    <tr>
        <td colspan="8" ></td>
    </tr>
</table>
<table>
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