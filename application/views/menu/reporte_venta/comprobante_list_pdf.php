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
<h4 style="text-align: center;">Reporte de ventas por comprobante e impuestos</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
