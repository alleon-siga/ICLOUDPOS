<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=cobranza_dia.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<h4 style="text-align: center; margin: 0;">Reporte cobranzas del d&iacute;a</h4>
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
            <th># Venta</th>
            <th>Cliente</th>
            <th>Tienda</th>
            <th>Usuario</th>
            <th>Operador</th>
            <th># Recarga</th>
            <th># Transacci&oacute;n</th>
            <th>Fecha recarga</th>
            <th>Monto Recarga</th>
            <th>Fecha pago</th>
            <th>Monto pagado</th>
            <th>Pendiente pago</th>
            <th>Local</th>
            <th>Condici&oacute;n</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
    <?php $suma = $montoRecarga = $pendientePago = 0;  ?>
    <?php foreach ($lists as $list): ?>
    <?php
        $fechaRecarga = $list->fecha;
        $debe = 0;
        $color = 'b-default';
        $estado = 'Cancelado';
        $fechaPago = $list->fecha;
        $montoPagado = $list->total;
        if($list->condicion_pago == 2){
            $debe = $list->monto_restante;
            if($debe == 0 && !empty($debe)){ //cuando no hay deuda
                $color = 'b-default';
                $estado = 'Cancelado';
                $fechaPago = $list->fecha_abono;
                $montoPagado = $list->monto_abono;
            }elseif(empty($debe)){ //cuando no hay ningun pago
                $debe = $list->total;
                $color = 'b-warning';
                $estado = 'Debe';
                $fechaPago = '';
                $montoPagado = 0;
            }else{ //cuando hay deuda
                $color = 'b-warning';
                $estado = 'Debe';
                $fechaPago = $list->fecha_abono;
                $montoPagado = $list->monto_abono;
            }
        }
        if($estado=='Cancelado'){
            $colorFila = "#9fa8da";
        }else{
            $colorFila = "#81d4fa";
        }
    ?>
        <tr style="background-color: <?= $colorFila ?> !important">
            <td><?= $list->venta_id ?></td>
            <td><?= utf8_decode($list->razon_social) ?></td>
            <td><?= utf8_decode($list->nota) ?></td>
            <td><?= utf8_decode($list->nombre) ?></td>
            <td><?= $list->valor ?></td>
            <td><?= $list->rec_nro ?></td>
            <td><?= $list->rec_trans ?></td>
            <td><?= date('d/m/Y H:i', strtotime($fechaRecarga)) ?></td>
            <td style="text-align: right;"><?= $md->simbolo ?> <?= number_format($list->total, 2) ?></td>
            <td><?php if(!empty($fechaPago)) echo date('d/m/Y H:i', strtotime($fechaPago)) ?></td>
            <td style="text-align: right;"><?= $md->simbolo.' '.number_format($montoPagado, 2); ?></td>
            <td style="text-align: right;">
                <label style="margin-bottom: 0px;" class="control-label badge <?= $color ?>">
                    <?= $md->simbolo.' '.number_format($debe, 2); ?>
                </label>
            </td>
            <td><?= utf8_decode($list->local_nombre) ?></td>
            <td><?= utf8_decode($list->condicion) ?></td>
            <td><?= $estado ?></td>
        </tr>     
        <?php 
            if(!empty($list->monto_abono)){
                $suma += $list->monto_abono;
            }elseif($list->condicion_pago==1){
                $suma += $list->total;
            }
            $montoRecarga += $list->total;
            $pendientePago += $debe;
        ?>
    <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: right;">TOTAL</td>
            <td style="text-align: right;"><?= $md->simbolo ?> <?= number_format($montoRecarga, 2) ?></td>
            <td></td>
            <td style="text-align: right;">
                <label style="margin-bottom: 0px;" class="control-label badge b-default">
                    <?= $md->simbolo ?> <?= number_format($suma, 2) ?>
                </label>
            </td>
            <td style="text-align: right; color: red;">
                <label style="margin-bottom: 0px;" class="control-label badge b-warning">
                    <?= $md->simbolo ?> <?= number_format($pendientePago, 2) ?>
                </label>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
