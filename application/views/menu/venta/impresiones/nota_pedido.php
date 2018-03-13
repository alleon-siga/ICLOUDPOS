<style>
    @media print {
        html, body {
            width: 100%;
            margin: 0;
            font-size: 9pt;
        }

        table {
            border: 0px;
            width: 100%;
            font-family: Verdana, Arial, sans-serif;
        }

        table tbody td {
            font-size: 8pt;
            text-transform: uppercase;
            padding: 2px;
        }

        table thead td {
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: bold;

            padding: 3px 2px;
        }
    }
</style>
<div>
    <table style="border: 0px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase; text-align: center;"><?= valueOption('EMPRESA_NOMBRE', '') ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Ubicaci&oacute;n: <?= $venta->local_nombre ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; text-align: left;">
                Direcci&oacute;n: <?= $venta->local_direccion ?></td>
        </tr>
    </table>
    <hr>
    <table style="border: 0px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase; text-align: center;">NOTA DE PEDIDO</td>
        </tr>
    </table>
    <hr>
    <table style="border: 0px;"
           cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase;">
                Venta Nro:
                <?= $venta->serie_documento != null ? $venta->serie_documento . ' - ' : '' ?>
                <?= sumCod($venta->venta_id, 6) ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Fecha: <?= date('d/m/Y h:i a', strtotime($venta->venta_fecha)) ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Identificaci&oacute;n Cliente: <?= $venta->ruc ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Cliente: <?= $venta->cliente_nombre ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Vendedor: <?= $venta->vendedor_nombre ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Tipo de Pago:
                <?= $venta->condicion_nombre ?>
            </td>
        </tr>
        <?php if ($venta->comprobante_id > 0): ?>
            <tr>
                <td style="text-transform: uppercase; text-align: center; border-top: 1px solid #0b0b0b;">
                    Nro Comprobante: <?= $venta->comprobante_nombre ?><br>
                    <?= $venta->comprobante ?>
                </td>
            </tr>
        <?php endif; ?>
    </table>


    <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Cantidad</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Precio</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Subtotal
            </td>
        </tr>
        <?php foreach ($venta->detalles as $detalle): ?>
            <tr>
                <td colspan="3"><?= $detalle->producto_nombre ?></td>
            </tr>
            <tr>
                <td><?= number_format($detalle->cantidad, 0) . " " . $detalle->unidad_abr ?></td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $detalle->precio ?></td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $detalle->importe ?></td>
            </tr>
        <?php endforeach; ?>
        <!--
        <?php for ($i = 0; $i < 20; $i++): ?>
            <tr>
                <td>asdasd</td>
                <td>asdsad</td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' ?></td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' ?></td>
            </tr>
        <?php endfor; ?>
        -->
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="2">Total a Pagar:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->total ?></td>
        </tr>
        <tr>
            <td colspan="2">Pagado:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->venta_pagado ?></td>
        </tr>
        <tr>
            <td colspan="2">Vuelto:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->venta_vuelto ?></td>
        </tr>
        <?php if ($venta->descuento > 0): ?>
            <tr>
                <td colspan="2">Descuento:</td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . number_format($venta->descuento, 2) ?></td>
            </tr>
        <?php endif; ?>
        </tbody>

    </table>
    <br>
    <div>
        SON:
        <span style="text-transform: uppercase;"><?= $totalLetras; ?></span>
    </div>
    <br>
    <div style="text-transform: uppercase; border-top: 1px dotted #0b0b0b; text-align: center;">
        GRACIAS POR LA COMPRA
    </div>
    <div style="text-transform: uppercase; border-top: 1px dotted #0b0b0b; text-align: center;">
        CANJEAR POR BOLETA O FACTURA
    </div>
</div>
<script>
    this.print();
</script>