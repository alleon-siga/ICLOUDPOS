<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
    }
</style>
<h2 style="text-align: center;">Historial de Ventas</h2>

<table border="0">
<tr>
    <td style="border: 0px;">Ubicaci&oacute;n: <?= $local['local_nombre'] ?></td>
    <td style="border: 0px;">Estado: <?= $estado ?></td>
    <td style="border: 0px;">Desde: <?= $fecha_ini ?></td>
    <td style="border: 0px;">Hasta: <?= $fecha_fin ?></td>
</tr>
</table>
<br>
<table cellpadding="3" cellspacing="0">
    <thead>
    <tr>

        <th>Fecha</th>
        <th>Doc</th>
        <th>Num Doc</th>
        <th>RUC - DNI</th>
        <th>Cliente</th>
        <th>Vendedor</th>
        <th>Condici&oacute;n</th>
        <th>Moneda</th>
        <th>Tip. Cam.</th>
        <th>SubTotal</th>
        <th>IGV</th>
        <th>Total <?= $venta_action == 'caja' ? 'a Pagar' : '' ?></th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($ventas) > 0): ?>

        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td>
                    <?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?>
                </td>

                <td style="text-align: center;"><?php
                    if ($venta->documento_id == 1) echo "FA";
                    if ($venta->documento_id == 2) echo "NC";
                    if ($venta->documento_id == 3) echo "BO";
                    if ($venta->documento_id == 4) echo "GR";
                    if ($venta->documento_id == 5) echo "PCV";
                    if ($venta->documento_id == 6) echo "NP";
                    ?>
                </td>
                <td><?= sumCod($venta->venta_id, 4) ?></td>
                <td><?= $venta->ruc?></td>
                <td><?= $venta->cliente_nombre ?></td>
                <td><?= $venta->vendedor_nombre ?></td>
                <td><?= $venta->condicion_nombre ?></td>
                <td><?= $venta->moneda_nombre ?></td>
                <td><?= $venta->moneda_tasa ?></td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->subtotal, 2) ?></td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->impuesto, 2) ?></td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></td>

            </tr>
        <?php endforeach ?>
    <?php endif; ?>

    </tbody>
</table>
