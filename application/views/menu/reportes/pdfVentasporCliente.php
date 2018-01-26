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
            colspan="8" >VENTAS POR CLIENTE</td>
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

    <thead><tr>
        <th>N&uacute;mero de Venta</th>
        <th>Fecha</th>
        <th>Vendedor</th>
        <th>Raz&oacute;n Social</th>
        <th>Condiciones de Pago</th>
        <th>Estado</th>
        <th>Local</th>
        <th>Sub total</th>
        <th>Impuesto</th>
        <th>Total</th></tr>
    </thead>
    <tbody>
    <?php if (count($clientes) > 0){
        foreach ($clientes as $cliente) { ?>
            <tr>
                <td >  <?= $cliente->venta_id ?>  </td>
                <td ><?= date('d-m-Y H:i:s', strtotime($cliente->fecha)) ?> </td>
                <td ><?=  $cliente->username ?></td>
                <td ><?=  $cliente->razon_social ?></td>
                <td><?=  $cliente->nombre_condiciones ?></td>
                <td><?=  $cliente->venta_status ?></td>
                <td><?=  $cliente->local_nombre ?></td>
                <td><?=  $cliente->simbolo." ".$cliente->subtotal ?></td>
                <td><?=  $cliente->simbolo." ".$cliente->total_impuesto ?></td>
                <td><?= $cliente->simbolo." ".$cliente->total ?></td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>