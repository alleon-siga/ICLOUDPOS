<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=pago_pendiente.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Lista de cuentas por cobrar</h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th># Venta</th>
            <th class='tip' title="Fecha Venta">Fecha Venta</th>
            <th># Comprobante</th>
            <th>Cliente</th>
            <th class='tip' title="Monto Credito Solicitado">Importe Venta</th>
            <th class='tip' title="Monto Cancelado">Importe Abonado</th>
            <th class='tip' title="Monto Cancelado">Pendiente de pago</th>
            <th class='tip' title="Monto Cancelado">Cuotas</th>

            <th class='tip' title="Total" tool># Cuotas Atrasado</th>
            <?php if ($local == "TODOS") { ?>
                <th>Local</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php if (count($pago_pendiente) > 0): ?>
        <?php foreach ($pago_pendiente as $v): ?>
            <tr>
                <td><?php echo $v->Venta_id; ?></td>
                <td style="text-align: center;"><span
                            style="display: none"><?= date('YmdHis', strtotime($v->FechaReg)) ?></span><?php echo date("d/m/Y", strtotime($v->FechaReg)) ?>
                </td>
                <td style="text-align: center;">
                    <?php
                    $doc = '';
                    if ($v->TipoDocumento == 1) $doc = "FA";
                    if ($v->TipoDocumento == 2) $doc = "NC";
                    if ($v->TipoDocumento == 3) $doc = "BO";
                    if ($v->TipoDocumento == 4) $doc = "GR";
                    if ($v->TipoDocumento == 5) $doc = "PCV";
                    if ($v->TipoDocumento == 6) $doc = "NV";

                    if ($v->correlativo != '')
                        echo $doc . ' ' . $v->serie . '-' . sumCod($v->correlativo, 6);
                    else
                        echo '<span style="color: #0000FF">NO EMITIDO</span>';
                    ?>
                </td>
                <td><?php echo $v->Cliente; ?></td>
                <td style="text-align: right;"><?php echo $v->Simbolo . ' ' . number_format($v->MontoTotal, 2) ?></td>
                <td style="text-align: right;"><?php echo $v->Simbolo . ' ' . number_format($v->MontoCancelado, 2) ?></td>
                <td style="text-align: right;"><?php echo $v->Simbolo . ' ' . number_format($v->MontoTotal - $v->MontoCancelado, 2) ?></td>
                <td style="text-align: center;"><?= $v->nro_cuotas ?></td>
                <td style="text-align: center;"><?= $v->cuotas_atrasadas ?></td>
                <?php if ($local == "TODOS") { ?>
                    <td style="text-align: center;"><?php echo $v->local; ?></td>
                <?php } ?>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
    <?php endif; ?>
    </tbody>
</table>