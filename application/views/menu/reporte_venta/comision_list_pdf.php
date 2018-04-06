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
<h4 style="text-align: center;">Reporte de comisi&oacute;n por vendedores</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
