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
            colspan="8" >VENTAS</td>
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
        <th>NUMERO DE VENTA</th>
        <th>CLIENTE</th>
        <th>VENDEDOR</th>
        <th class='tip'>FECHA</th>
        <th class='tip'>TIPO DE DOCUMENTO</th>
        <th class='tip' >ESTATUS</th>
        <th class='tip' >LOCAL</th>
        <th class='tip' title="Total">CONDICION DE PAGO</th>
        <th>TOTAL IMPUESTO</th>
        <th>TOTAL</th>

    </tr>
    </thead>
    <tbody>
    <?php if (count($ventas) > 0): ?>
        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td style="text-align: center;"><?php echo  $venta->venta_id; ?></td>
                <td style="text-align: center;"><?php echo  $venta->razon_social;  ?></td>
                <td style="text-align: center;"><?php echo  $venta->username;  ?></td>
                <td style="text-align: center;"><?php echo  date('d-m-Y H:i:s', strtotime($venta->fecha)); ?></td>
                <td style="text-align: center;"><?php

                    if($venta->nombre_tipo_documento=="FACTURA") echo "FA";
                    if($venta->nombre_tipo_documento==2) echo "NC";
                    if($venta->nombre_tipo_documento=="BOLETA DE VENTA" || $venta->nombre_tipo_documento==3 ) echo "BO";
                    if($venta->nombre_tipo_documento==4) echo "GR";
                    if($venta->nombre_tipo_documento==5) echo "PCV";
                    if($venta->nombre_tipo_documento=="NOTA DE PEDIDO" || $venta->nombre_tipo_documento==6) echo "NP";
                    ?></td>
                <td style="text-align: center;"><?php echo  $venta->venta_status;  ?></td>
                <td style="text-align: center;"><?php echo  $venta->local_nombre;  ?></td>
                <td style="text-align: center;"><?php echo  $venta->nombre_condiciones; ?></td>
                <td style="text-align: center;"><?php echo  $venta->simbolo." ".$venta->total_impuesto;  ?></td>
                <td style="text-align: center;"><?php echo $venta->simbolo." ".$venta->total;  ?></td>

            </tr>
        <?php endforeach; ?>
    <?php else : ?>
    <?php endif; ?>
    </tbody>
</table>