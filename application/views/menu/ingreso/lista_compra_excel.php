<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=consultar_compras.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<?php $term = diccionarioTermino() ?>
<h4 style="text-align: center; margin: 0;">Consultar compras</h4>
<?php if(isset($fecha_ini) && isset($fecha_fin)): ?>
<h4 style="text-align: center; margin: 0;">
    Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    Hora: <?= date('H:i:s') ?>
</h4>
<?php endif; ?>
<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Doc</th>
            <th>Doc</th>
            <th>Num Doc</th>
            <th><?= $term[0]->valor.' / '.$term[1]->valor ?> Provedor</th>
            <th>Proveedor</th>
            <th>Tipo Pago</th>
            <?php if ($md->id_moneda != $moneda->id_moneda): ?>
                <th>Tipo Cambio</th>
            <?php endif; ?>
            <th>SubTotal</th>
            <th>Impuesto</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Usuario</th>
            <th>fec Registro</th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($ingresos as $ingreso):
?>
        <tr <?= $ingreso->estado == 'ANULADO' ? 'style="color: red;"' : '' ?>>
            <td><?= $ingreso->id ?></td>
            <td><?= date('d/m/Y', strtotime($ingreso->fecha_emision)) ?></td>
            <td><?= $ingreso->documento ?></td>
            <td><?= $ingreso->documento_numero ?></td>
            <td><?= $ingreso->proveedor_ruc ?></td>
            <td><?= $ingreso->proveedor_nombre ?></td>
            <td><?= $ingreso->tipo_pago ?></td>
            <?php if ($md->id_moneda != $moneda->id_moneda): ?>
                <td><?= $ingreso->tasa ?></td>
            <?php endif; ?>
            <td><?= $moneda->simbolo.' '.number_format($ingreso->subtotal, 2) ?></td>
            <td><?= $moneda->simbolo.' '.number_format($ingreso->impuesto, 2) ?></td>
            <td><?= $moneda->simbolo.' '.number_format($ingreso->total, 2) ?></td>
            <td><?= $ingreso->estado ?></td>
            <td><?= $ingreso->usuario_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($ingreso->fecha_registro)) ?></td>
        </tr>
<?php
    endforeach;
?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">TOTALES</td>
            <td style="text-align: right;"><?= $moneda->simbolo.' '.number_format($ingreso_totales->subtotal, 2) ?></span></td>
            <td style="text-align: right;"><?= $moneda->simbolo.' '.number_format($ingreso_totales->impuesto, 2) ?></span></td>
            <td style="text-align: right;"><?= $moneda->simbolo.' '.number_format($ingreso_totales->total, 2) ?></span></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
