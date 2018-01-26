<style>
    
    @media print {
        html, body
        {
            width:100%;
            margin: 0;
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
    <table style="border: 0px;"
           cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase;">
                Venta Nro:
                <?= $venta->serie_documento != null ? $venta->serie_documento . ' - ' : '' ?>
                <?= sumCod($venta->venta_id, 6) ?>
            </td>
            <td style="text-transform: uppercase; text-align: right;">
                Fecha: <?= date('d/m/Y h:i a', strtotime($venta->venta_fecha)) ?></td>
        </tr>

        <tr>
            <td style="text-transform: uppercase;">Cliente: <?= $venta->cliente_nombre ?></td>
            <td style="text-transform: uppercase; text-align: right;">Vendedor: <?= $venta->vendedor_nombre ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Ubicaci&oacute;n: <?= $venta->local_nombre ?></td>
            <td style="text-transform: uppercase; text-align: right;">
                Tipo de Pago:
                <?= $venta->condicion_nombre?>
            </td>
        </tr>
    </table>


    <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Cantidad</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; width: 50%;">Producto</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Origen</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Precio</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Subtotal
            </td>
        </tr>
        <?php foreach ($venta->detalles as $detalle): ?>
            <tr>
                <td><?= number_format($detalle->cantidad, 0) . " " . $detalle->unidad_abr ?></td>
                <td><?= $detalle->producto_nombre ?></td>
                <td><?= $detalle->origen ?></td>
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
        </tbody>

    </table>
    <br>
    <div style="text-align: right">
        Total a Pagar:
        <?= $venta->moneda_simbolo . ' ' . $venta->total ?>
    </div>
</div>
<script>
    this.print();
</script>