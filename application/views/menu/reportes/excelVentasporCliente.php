<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=VentasPorCliente.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="10">VENTAS POR CLIENTE
        </td>
    </tr>
    <tr>
        <td colspan="10"></td>
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
        <td></td>
        <td style="font-weight: bold">Fecha Emisi&oacute;n:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
</table>
<table border="1">

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