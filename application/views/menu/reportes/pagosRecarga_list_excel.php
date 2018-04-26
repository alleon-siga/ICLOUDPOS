<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=recarga_virtual.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<h4 style="text-align: center; margin: 0;">Reporte recargas virtuales</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
    Hora: <?= date('H:i:s') ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th># Venta</th>
            <th>Cliente</th>
            <th>Tienda</th>
            <th>Operador</th>
            <th># Recarga</th>
            <th># Transacci&oacute;n</th>
            <th>Fecha recarga</th>
            <th>Monto Recarga</th>
            <?php if($condicion_pago==2 || $condicion_pago==0){ ?><th>Fecha pago</th><?php } ?>
            <?php if($condicion_pago==2 || $condicion_pago==0){ ?><th>Monto pagado</th><?php } ?>
            <th>Pendiente pago</th>
            <th>Local</th>
            <th>Condici&oacute;n</th>
            <?php if($estado_pago==0){ ?><th>Estado</th><?php } ?>
        </tr>
    </thead>
   <tbody>
    <?php $suma = 0;  ?>    
    <?php foreach ($lists as $list): ?>
        <tr>
            <td><?= $list->venta_id ?></td>
            <td><?= utf8_decode($list->razon_social) ?></td>
            <td><?= utf8_decode($list->nota) ?></td>
            <td><?= $list->valor ?></td>
            <td><?= $list->rec_nro ?></td>
            <td><?= $list->rec_trans ?></td>
            <td><?= date('d/m/Y H:i', strtotime($list->fecha)) ?></td>
            <td style="text-align: right;">
                <?= $md->simbolo ?> <?= number_format($list->total, 2) ?>
            </td>
            <?php if($condicion_pago==2 || $condicion_pago==0){ ?>
                <td>
                <?php if(!empty($list->fecha_abono)){ ?>
                    <?php echo date('d/m/Y H:i', strtotime($list->fecha_abono)); ?>
                <?php }else{ ?>
                    <?php echo date('d/m/Y H:i', strtotime($list->fecha)); ?>
                <?php } ?>
                </td>
            <?php } ?>
            <?php if($condicion_pago==2 || $condicion_pago==0){ ?>
                <td style="text-align: right;">
                    <?php if(!empty($list->monto_abono)){ ?>
                        <?php echo $md->simbolo.' '.number_format($list->monto_abono, 2); ?>
                    <?php }else{ ?>
                        <?php echo $md->simbolo.' '.number_format($list->total, 2); ?>
                    <?php } ?>
                </td>
            <?php } ?>
            <td style="text-align: right;">
                <label style="margin-bottom: 0px;">
                <?php if(!empty($list->monto_abono)){ ?>
                    <?= number_format($list->total - $list->monto_abono, 2) ?>
                <?php }else{ ?>
                    <?= number_format($list->total - $list->total, 2) ?>
                <?php } ?>
                </label>
            </td>
            <td><?= utf8_decode($list->local_nombre) ?></td>
            <td><?= utf8_decode($list->condicion) ?></td>
            <?php if($estado_pago==0){ ?>
                <?php if($list->ispagado == 1 OR $list->ispagado==''){ ?>
                    <td>Cancelado</td>
                <?php }elseif($list->ispagado == 0) { ?>
                    <td>Debe</td>
                <?php } ?>
            <?php } ?>
        </tr>
        <?php if(!empty($list->monto_abono)){ ?>
            <?= $suma += $list->monto_abono ?>
        <?php }else{ ?>
            <?= $suma += $list->total ?>
        <?php } ?>
    <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9" style="text-align: right;">TOTAL</td>
            <td style="text-align: right;"><?= $md->simbolo ?> <?= number_format($suma, 2) ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
