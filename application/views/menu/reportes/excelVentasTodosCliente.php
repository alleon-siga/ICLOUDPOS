<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=VentasTodosClientes.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="8">VENTAS POR CLIENTE
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


        <td style="font-weight: bold">Fecha Emisi&oacute;n:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
</table>
<table border="1">

    <thead><tr>
        <th>N&uacute;mero de Venta</th>
        <th>Fecha</th>
        <th>Vendedor</th>
        <th>Cliente</th>
        <th>Estado</th>
        <th>Sub total</th>
        <th>Impuesto</th>
        <th>Total</th></tr>
    </thead>
    <tbody>
    <?php if (count($total) > 0){
        foreach ($total as $rows) { ?>
            <tr>
                <td >  <?= $rows->venta_id ?>  </td>
                <td ><?= date('d-m-Y H:i:s', strtotime($rows->fecha)) ?> </td>
                <td ><?=  $rows->username ?></td>
                <td ><?=  $rows->razon_social ?></td>
                <td><?=  $rows->venta_status ?></td>
                <td><?=  $rows->simbolo." ".$rows->sub_total ?></td>
                <td><?=  $rows->simbolo." ".$rows->impuesto ?></td>
                <td><?= $rows->simbolo." ".$rows->totalizado ?></td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>