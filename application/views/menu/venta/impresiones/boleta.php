<div style="margin: 0; padding: 0;">
    <?php if ($section == 'header'): ?>
        <table style="border: 0px; font-size: 16px; width: 100%; font-family: Tahoma; font-weight: bold;"
               cellpadding="1" cellspacing="0">
            <tr>
                <td style="text-transform: uppercase;">
                    Venta Nro:
                    <?= $venta->serie_documento != null ? $venta->serie_documento . ' - ' : '' ?>
                    <?= sumCod($venta->venta_id, 6) ?> - {PAGENO} BOLETA
                </td>
                <td style="text-transform: uppercase; text-align: right;">
                    Fecha: <?= date('d/m/Y h:i a', strtotime($venta->venta_fecha)) ?></td>
            </tr>

            <tr>
                <td style="text-transform: uppercase;">Documento: Nota de Venta</td>
                <td style="text-transform: uppercase; text-align: right;">Tipo de
                    Pago: <?= $venta->condicion_nombre ?></td>
            </tr>

            <tr>
                <td style="text-transform: uppercase;">Empresa: <?= $venta->local_nombre ?></td>
                <td style="text-transform: uppercase; text-align: right;">
                    Ubicaci&oacute;n: <?= $venta->local_direccion ?></td>
            </tr>

            <tr>
                <td style="text-transform: uppercase;">Cliente: <?= $venta->cliente_nombre ?></td>
                <td style="text-transform: uppercase; text-align: right;">Vendedor: <?= $venta->vendedor_nombre ?></td>
            </tr>
        </table>
    <?php elseif ($section == 'body'): ?>

        <style>

            table {
                border: 0px;
                width: 100%;
                border: 1px solid #000000;
                font-family: Tahoma;
            }

            table tbody td {
                font-size: 16px;
                text-transform: uppercase;
                font-weight: bold;
                border-bottom: 1px solid #000000;
                padding: 2px;
            }

            table thead td {
                font-size: 16px;
                text-transform: uppercase;
                font-weight: bold;
                border-bottom: 1px solid #000000;
                padding: 3px 2px;
            }

        </style>
        <table cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <td style="width: 60%;">Producto</td>
                <td>Cantidad</td>
                <td style="text-align: right">Precio</td>
                <td style="text-align: right">Subtotal</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($venta->detalles as $detalle): ?>
                <tr>
                    <td><?= $detalle->producto_nombre ?></td>
                    <td><?= $detalle->producto_cualidad=="PESABLE"?$detalle->cantidad:number_format($detalle->cantidad,0) . " " . $detalle->unidad_abr ?></td>
                    <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $detalle->precio ?></td>
                    <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . number_format($detalle->importe, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
        <table style="border: 0px; font-size: 16px; width: 100%; font-family: Tahoma; font-weight: bold;"
               cellpadding="1" cellspacing="0">
            <tr>
                <td style="text-transform: uppercase;">Pagado: <?= $venta->venta_pagado ?></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;">Vuelto: <?= $venta->venta_vuelto ?></td>
            </tr>
        </table>
    <?php elseif ($section == 'footer'): ?>
        <table
            style="border: 0px; font-size: 16px; text-transform: uppercase; width: 100%; font-family: Tahoma; font-weight: bold;">
            <tr>
                <td style="text-transform: uppercase;">Total Deuda:
                    <?= $venta->credito_pendiente != null ? $venta->moneda_simbolo . ' ' . $venta->credito_pendiente : $venta->moneda_simbolo . ' 0' ?>
                </td>
                <td style="text-transform: uppercase; text-align: right;">
                    Total a Pagar:
                    <?= $venta->moneda_simbolo . ' ' . $venta->total ?>
                </td>
                <td style="text-transform: uppercase; text-align: right;">
                    Total a Pagar Con Letra:
                    <?= $totalLetras; ?>
                </td>
            </tr>
        </table>
    <?php endif; ?>
</div>