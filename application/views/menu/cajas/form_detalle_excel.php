<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=movimiento_caja.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4>Nombre de la caja: <?= $cuenta->descripcion ?></h4>
<h5>Fecha: <?= $fecha_ini == $fecha_fin ? $fecha_fin : $fecha_ini . ' a ' . $fecha_fin ?></h5>
<h5>Moneda: <?= $cuenta->nombre ?></h5>
<h5>Responsable: <?= $cuenta->usuario_nombre ?></h5>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Operacion</th>
        <th>Usuario</th>
        <th>Forma de Pago</th>
        <th>Numero</th>
        <th>Observacion</th>
        <th>Ingreso</th>
        <th>Egreso</th>
        <th>Saldo</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?php
        $total_ingreso = 0;
        $total_egreso = 0;
        ?>
        <?php $saldo_anterior = isset($cuenta_movimientos[0]) ? $cuenta_movimientos[0]->saldo_old : 0 ?>
        <td colspan="9" style="font-weight: bold;">SALDO ANTERIOR (<?= $fecha_ini ?>)</td>
        <td style="font-weight: bold;"><?= $cuenta->simbolo . ' ' . number_format($saldo_anterior, 2) ?></td>
    </tr>
    <?php foreach ($cuenta_movimientos as $mov): ?>
        <tr>
            <td><?= $mov->id ?></td>
            <td><?= date('d/m/Y H:i', strtotime($mov->created_at)) ?></td>
            <td><?= $mov->operacion_nombre ?></td>
            <td><?= $mov->usuario_nombre ?></td>
            <td><?= $mov->medio_pago_nombre ?></td>
            <td><?= $mov->numero ?></td>
            <td><?= $mov->ref_val ?></td>
            <?php if ($mov->movimiento == 'INGRESO'): ?>
                <?php $saldo_anterior += $mov->saldo ?>
                <?php $total_ingreso += $mov->saldo ?>
                <td><?= $mov->simbolo ?> <?= number_format($mov->saldo, 2) ?></td>
                <td></td>
            <?php elseif ($mov->movimiento == 'EGRESO'): ?>
                <?php $total_egreso += $mov->saldo ?>
                <?php $saldo_anterior -= $mov->saldo ?>
                <td></td>
                <td><?= $mov->simbolo ?> <?= number_format($mov->saldo, 2) ?></td>
            <?php endif; ?>
            <td><?= $mov->simbolo ?> <?= number_format($saldo_anterior, 2, '.', ',') ?></td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="7" style="font-weight: bold;">SALDO FINAL (<?= $fecha_fin ?>)</td>
        <td style="font-weight: bold;"><?= $cuenta->simbolo . ' ' . number_format($total_ingreso, 2) ?></td>
        <td style="font-weight: bold;"><?= $cuenta->simbolo . ' ' . number_format($total_egreso, 2) ?></td>
        <td style="font-weight: bold;"><?= $cuenta->simbolo . ' ' . number_format($saldo_anterior, 2) ?></td>
    </tr>
    </tbody>
</table>
<h4 style="text-align: right;">
    SALDO ANTERIOR - SALDO FINAL: <?= $cuenta->simbolo . ' ' . number_format($total_ingreso - $total_egreso, 2) ?></h4>

