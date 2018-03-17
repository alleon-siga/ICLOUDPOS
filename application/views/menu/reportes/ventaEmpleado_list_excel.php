<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=venta_empleado.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de ventas por empleado</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('m/d/Y', strtotime($fecha_ini)) ?>
    al <?= date('m/d/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
    <tr>
        <th>Id</th>
        <th>Nombre</th>
        <?php if(isset($lists[0]->tipo)){ ?>
        <th><?= ($lists[0]->tipo=='1')? 'Cantidad': 'Total' ?></th>
        <?php } ?>
        <th>Anulado</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $cant = 0;
    $total = 0;
    $anulado = 0;
    ?>
    <?php foreach ($lists as $list): ?>
        <?php
        $cant += $list->cantidad;
        $total += $list->total;
        $anulado += $list->anulado;
        ?>
        <tr>
            <td><?= $list->id_vendedor ?></td>
            <td><?= $list->nombre ?></td>
            <?php if(isset($lists[0]->tipo)){ ?>
                <?php if($list->tipo=='1'){ ?>
                    <td><?= $list->cantidad ?></td>
                <?php }elseif($list->tipo=='2') { ?>
                    <td><?= $moneda->simbolo . ' ' . number_format($list->total,2) ?></td>
                <?php } ?>
            <?php } ?>
            <td><?= $list->anulado ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">TOTALES</td>
            <?php if(isset($lists[0]->tipo)){ ?>
                <?php if($list->tipo=='1'){ ?>
                    <td><?= $cant ?></td>
                <?php }elseif($list->tipo=='2') { ?>
                    <td><?= $moneda->simbolo . ' ' . number_format($total, 2) ?></td>
                <?php } ?>
            <?php } ?>
            <td><?= $anulado ?></td>
        </tr>
    </tfoot>
</table>
