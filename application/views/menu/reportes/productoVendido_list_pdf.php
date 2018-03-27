<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
        font-size: 9px;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 10px;
    }

    h4, h5 {
        margin: 0px;
    }

    table tfoot tr td {
        font-weight: bold;
    }
</style>
<h4 style="text-align: center;">Reporte de productos más vendidos</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
        <td>&nbsp;</td>
    </tr>
    </tfoot>
</table>
