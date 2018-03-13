<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=utilidades_productos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de ventas por comprobante e impuestos</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('m/d/Y', strtotime($fecha_ini)) ?>
    al <?= date('m/d/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
    <tr>
        <th>Identificaci&oacute;n</th>
        <th>Documento</th>
        <th>Cliente</th>
        <th>No. Comprobante</th>
        <th>Tipo Comprobante</th>
        <th>Impuesto</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    $total_impuesto = 0;
    ?>
    <?php foreach ($lists as $list): ?>
        <?php
        $total += $list->total;
        $total_impuesto += $list->impuesto;
        ?>
        <tr>
            <td><?= $list->identificacion ?></td>
            <?php
            $doc = 'NP ';
            if ($list->documento_id == 1) $doc = 'FA ';
            if ($list->documento_id == 3) $doc = 'BO ';
            ?>
            <td><?= $doc . $list->serie . '-' . sumCod($list->numero, 6) ?></td>
            <td><?= $list->cliente_nombre ?></td>
            <td><?= $list->comprobante_numero ?></td>
            <td><?= $list->comprobante_nombre ?></td>
            <td><?= $moneda->simbolo . ' ' . number_format($list->impuesto, 2) ?></td>
            <td><?= $moneda->simbolo . ' ' . number_format($list->total, 2) ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="5">TOTALES</td>
        <td><?= $moneda->simbolo . ' ' . number_format($total_impuesto, 2) ?></td>
        <td><?= $moneda->simbolo . ' ' . number_format($total, 2) ?></td>
    </tr>
    </tfoot>
</table>
