<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=hoja_colecta.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<h4 style="text-align: center; margin: 0;">Reporte hoja de colecta</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    Hora: <?= date('H:i:s') ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th># Venta</th>
            <th>Fecha</th>
            <th>Local</th>
            <th>Cliente</th>
            <th># Comprobante</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio unitario</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $venta_id_temp = '';
        $suma = $total = 0;
        foreach ($lists as $list):
            if($venta_id_temp!=$list->local_nombre && !empty($venta_id_temp)){
    ?>
        <tr>
            <td style="text-align: right; font-weight: bold;" colspan="8">TOTAL <?= $venta_id_temp ?></td>
            <td style="text-align: right; font-weight: bold;"><?= $list->simbolo ?> <?= number_format($suma, 2) ?></td>
        </tr>
    <?php
                $suma = 0;
            }
    ?>
        <tr>
            <td><?= $list->venta_id ?></td>
            <td><?= date('d/m/Y H:i', strtotime($list->fecha)) ?></td>
            <td><?= utf8_decode($list->local_nombre) ?></td>
            <td><?= utf8_decode($list->razon_social) ?></td>
            <td><?= $list->abr_doc . ' ' . $list->serie . '-' . sumCod($list->numero, 6) ?></td>
            <td><?= utf8_decode($list->producto_nombre) ?></td>
            <td><?= $list->cantidad ?></td>
            <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->precio, 2) ?></td>
            <td style="text-align: right;"><?= $list->simbolo ?> <?= number_format($list->detalle_importe, 2) ?></td>
        </tr>
    <?php
            $suma += $list->detalle_importe;
            $venta_id_temp = $list->local_nombre;
            $total += $list->detalle_importe;
        endforeach;
    ?>
        <tr>
            <td style="text-align: right; font-weight: bold;" colspan="8">TOTAL <?= $list->local_nombre ?></td>
            <td style="text-align: right; font-weight: bold;"><?= !empty($list->simbolo)? $list->simbolo : $md->simbolo ?> <?= number_format($suma, 2) ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right; font-weight: bold;" colspan="8">TOTAL GENERAL</td>
            <td style="text-align: right; font-weight: bold;"><?= !empty($list->simbolo)? $list->simbolo : $md->simbolo ?> <?= number_format($total, 2) ?></td>
        </tr>
    </tfoot>
</table>
