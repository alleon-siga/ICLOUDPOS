<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=comision_vendedores.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<style type="text/css">
    h4, h5 {
        margin: 0px;
    }
</style>
<h4 style="text-align: center;">Reporte de ventas por comprobante e impuestos</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Vendedor</th>
        <th>Total Venta</th>
        <th>Comision</th>
        <th>Importe Comision</th>
    </tr>
    </thead>
    <tbody>
    <?php $total_venta = $imp_com = 0; ?>
    <?php foreach ($lists as $list): ?>
        <tr>
            <td><?= $list->vendedor_id ?></td>
            <td><?= $list->vendedor_nombre ?></td>
            <td><?= $moneda->simbolo.' '.number_format($list->total_venta, 2) ?></td>
            <td><?= number_format($list->comision, 2) ?></td>
            <td><?= $moneda->simbolo.' '.number_format($list->importe_comision, 2) ?></td>
        </tr>
    <?php $total_venta += $list->total_venta; ?>
    <?php $imp_com += $list->importe_comision; ?>        
    <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total:</td>
            <td><?= $moneda->simbolo.' '.number_format($total_venta, 2); ?></td>
            <td></td>
            <td><?= $moneda->simbolo.' '.number_format($imp_com, 2); ?></td>
        </tr>
    </tfoot>    
</table>
