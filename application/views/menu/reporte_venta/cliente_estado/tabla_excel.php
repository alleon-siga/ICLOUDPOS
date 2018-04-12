<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=estado_cuenta_cliente.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center;">Reporte de Estado de Cuenta del Cliente</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<?php
$total_venta = 0;
$total_pago = 0;
$total_saldo = 0; ?>
<?php if (count($clientes) == 0 && isset($form_filter)) echo '<br><h3>No hay resultados para mostrar.</h3>' ?>
<?php foreach ($clientes as $cliente): ?>
    <table border="1">
        <tbody>
        <tr>
            <th>Cliente</th>
            <td colspan="4"><?= $cliente->cliente_nombre ?></td>
            <th>Total Vendido</th>
            <td colspan="2"><?= $moneda->simbolo ?> <span><?= number_format($cliente->subtotal_venta, 2) ?></span></td>
        </tr>
        <tr>
            <th>Ubicaci&oacute;n</th>
            <td colspan="4"><?= $local->local_nombre ?></td>
            <th>Total Pagado</th>
            <td colspan="2"><?= $moneda->simbolo ?> <span><?= number_format($cliente->subtotal_pago, 2) ?></span></td>
        </tr>
        <tr>
            <th>Vendedor</th>
            <td colspan="4"><?= $cliente->vendedor_nombre ?></td>
            <th>Total Saldo</th>
            <td colspan="2">
                <label style="margin-bottom: 0px;"
                       class="control-label badge <?= $cliente->subtotal_venta - $cliente->subtotal_pago > 0 ? 'b-warning' : 'b-default' ?>">
                    <?= $moneda->simbolo ?>
                    <?= number_format($cliente->subtotal_venta - $cliente->subtotal_pago, 2) ?>
                </label>
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Descripci&oacute;n</th>
            <th>Venta</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th></th>
        </tr>
        <?php $actual_desglose = 0 ?>
        <?php $total_venta += number_format($cliente->subtotal_venta, 2, '.', ''); ?>
        <?php $total_pago += number_format($cliente->subtotal_pago, 2, '.', ''); ?>
        <?php $total_saldo += number_format($cliente->subtotal_venta - $cliente->subtotal_pago, 2, '.', ''); ?>
        <?php foreach ($cliente->cobranzas as $cobranza): ?>

            <tr style="background-color: #dae8e7; font-weight: bold;">
                <td><?= date('d/m/Y', strtotime($cobranza->fecha_venta)) ?></td>
                <td>
                    <?php
                    $doc = 'NE ';
                    if ($cobranza->documento_id == 3) $doc = 'BO ';
                    if ($cobranza->documento_id == 1) $doc = 'FA ';
                    ?>

                    <?= $cobranza->documento_numero != null ?
                        $doc . $cobranza->documento_serie . ' - ' . sumCod($cobranza->documento_numero, 6) :
                        '<span style="color: blue;">NO FACTURADO</span>' ?>
                    (# Vnt: <?= $cobranza->venta_id ?>)
                </td>
                <td><?= $cobranza->condicion_pago_nombre ?></td>
                <td>
                    <?= $moneda->simbolo . ' ' . number_format($cobranza->total_deuda, 2) ?>
                </td>
                <td></td>
                <?php $actual_desglose += $cobranza->total_deuda; ?>
                <td><?= $moneda->simbolo . ' ' . number_format($actual_desglose, 2) ?></td>
                <td><?= $cobranza->condicion_pago == 1 || $cobranza->credito_estado == 'PagoCancelado' ? 'Cancelado' : 'Pendiente' ?></td>
                <td>
                    <a href="#" class="btn btn-default show_detalle" data-id="<?= $cobranza->venta_id ?>">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
            </tr>

            <? foreach ($cobranza->detalles as $detalle): ?>
                <tr>
                    <td style="text-align: right;"><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                    <td><?= $detalle->letra ?></td>
                    <td><?= $detalle->tipo_pago_nombre ?></td>
                    <td></td>
                    <td><?= $moneda->simbolo . ' ' . number_format($detalle->monto, 2) ?></td>
                    <?php $actual_desglose -= $detalle->monto; ?>
                    <td><?= $moneda->simbolo . ' ' . number_format($actual_desglose, 2) ?></td>
                    <td colspan="2">

                        <?= $detalle->letra == 'PAGO INICIAL' && $cobranza->total_interes > 0
                            ? '<span style="font-weight: bold;">INTERES:</span> ' . $moneda->simbolo . ' ' . number_format($cobranza->total_interes, 2)
                            . ' (' . $cobranza->tasa_interes . '%)'
                            : '' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
<?php endforeach; ?>
