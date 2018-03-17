<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=venta_sucursal.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de ventas por sucursal</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('m/d/Y', strtotime($fecha_ini)) ?>
    al <?= date('m/d/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<table border="1">
    <thead>
    <tr>
        <th rowspan="2"><?= getCodigoNombre() ?></th>
        <th rowspan="2">Nombre</th>
        <th rowspan="2" style="vertical-align: middle;">Unidad</th>
        <?php foreach ($locales as $local): ?>
        <th colspan="3"><?= $local['local_nombre'] ?></th>
        <?php endforeach ?>    
    </tr>
    <tr>
    <?php for($x=1; $x<=count($locales); $x++){ ?>
        <th>Vendida</th>
        <th>Stock actual</th>
        <th>% de avance</th>
    <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php $ventas = array(); $stock = array(); ?>
    <?php for($x=1; $x<=count($locales); $x++){ ?>
    <?php
        $ventas[$x] = 0;
        $stock[$x] = 0;
    ?>
    <?php } ?>
    <?php foreach ($lists as $list): ?>
        <tr>
            <td><?= getCodigoValue($list['producto_id'], $list['producto_codigo_interno']) ?></td>
            <td><?= $list['producto_nombre'] ?></td>
            <td><?= $list['nombre_unidad']; ?></td>
            <?php for($x=1; $x<=count($locales); $x++){ ?>
            <?php
                $ventas[$x] += $list['cantVend'.$x];
                $stock[$x] += $list['stock'.$x];

                $cantVend = $list['cantVend'.$x];
                $stockAct = $list['stock'.$x];
                $porcAvance = number_format(($stockAct==0)? '0':($cantVend/$stockAct)*100,2);
            ?>
            <td style="text-align: right;"><?= empty($list['cantVend'.$x])? '0': $list['cantVend'.$x]; ?></td>
            <td style="text-align: right;"><?= empty($list['stock'.$x])? '0': $list['stock'.$x]; ?></td>
            <td style="text-align: right;"><?= $porcAvance; ?> %</td>
            <?php } ?>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">TOTALES</td>
        <?php for($x=1; $x<=count($locales); $x++){ ?>
        <td style="text-align: right;"><?= $ventas[$x] ?></td>
        <td style="text-align: right;"><?= $stock[$x] ?></td>
        <td style="text-align: right;"><?= ($stock[$x]==0)? '0.00' : number_format(($ventas[$x]/$stock[$x])*100,2) ?> %</td>
        <?php } ?>
    </tr>
    </tfoot>
</table>
