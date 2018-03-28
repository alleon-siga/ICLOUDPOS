<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=producto_vendido.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de productos mas vendidos</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
    <tr>
        <th><?= getCodigoNombre() ?></th>
        <th>Nombre</th>
        <th>Cantidad Vendida</th>
        <th>Stock Actual</th>
        <th>Unidad</th>
        <th>% avance</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $stock = 0;
    $ventas = 0;
    ?>
    <?php foreach ($lists as $list): ?>
        <?php
        $stock += $list->stock;
        $ventas += $list->ventas;
        ?>
        <tr>
            <td><?= getCodigoValue($list->producto_id, $list->producto_codigo_interno) ?></td>
            <td><?= $list->producto_nombre ?></td>
            <td style="text-align: right;"><?= $list->ventas ?></td>
            <td style="text-align: right;"><?= $list->stock ?></td>
            <td><?= $list->nombre_unidad ?></td>
            <td><?= number_format(($list->stock==0)? '0':($list->ventas/$list->stock)*100,2); ?> %</td>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2">TOTALES</td>
        <td style="text-align: right;"><?= $ventas ?></td>
        <td style="text-align: right;"><?= $stock ?></td>
        <td>&nbsp;</td>
        <td></td>
    </tr>
    </tfoot>
</table>
