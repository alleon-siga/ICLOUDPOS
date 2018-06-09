<?php $term = diccionarioTermino() ?>
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
            <td style="text-transform: uppercase;"><?= $term[1]->valor ?>: <?= $identificacion->config_value ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Ubicaci&oacute;n: <?= $venta->local_nombre ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; text-align: left;">
                Direcci&oacute;n: <?= $venta->local_direccion ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; text-align: left;">
                T&eacute;lefono: <?= valueOption('EMPRESA_TELEFONO') ?></td>
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
            <td style="text-transform: uppercase;"><?= ($venta->tipo_cliente == '1') ? $term[1]->valor : $term[0]->valor ?>
                : <?= $venta->ruc ?></td>
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
                    <?= $venta->comprobante_nombre ?><br>
                    NCF: <?= $venta->comprobante ?><br>
                    <div style="text-align: right; text-transform: capitalize;">
                        V&aacute;lido hasta: <?= date('d/m/Y', strtotime($venta->fecha_venc)) ?>
                    </div>
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
        <?php $i = 0; ?>
        <?php foreach ($venta->detalles as $detalle): ?>
            <tr>
                <td colspan="3"
                    style="<?= $i++ != 0 ? 'border-top: 1px dashed #0b0b0b;' : '' ?>"><?= $detalle->producto_nombre ?></td>
            </tr>
            <tr>
                <td><?= number_format($detalle->cantidad, 0) . " " . $detalle->unidad_abr ?></td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $detalle->precio ?></td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $detalle->importe ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3">
                <hr style="color: #0b0b0b;">
            </td>
        </tr>
        <!--<tr>
            <td colspan="2">Subtotal:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->subtotal ?></td>
        </tr>-->
        <?php if ($venta->descuento > 0): ?>
            <tr>
                <td colspan="2">Descuento:</td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . number_format($venta->descuento, 2) ?></td>
            </tr>
        <?php endif; ?>
        <!--<tr>
            <td colspan="2"><?= $term[2]->valor ?>:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->impuesto ?></td>
        </tr>-->
        <tr>
            <td colspan="2">Total a Pagar:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->total ?></td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="2"
            ">Pagado:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->venta_pagado ?></td>
        </tr>
        <tr>
            <td colspan="2">Vuelto:</td>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $venta->venta_vuelto ?></td>
        </tr>

        </tbody>

    </table>
    <br>
    <div>
        SON:
        <span style="text-transform: uppercase;"><?= $totalLetras; ?></span>
    </div>
    <?php if (count($venta->cuotas) > 0): ?>
        <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td colspan="3" style="text-align: center; border-top: 1px solid #000000;">CUOTAS Y VENCIMIENTOS</td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">LETRA</td>
                <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">VENCE</td>
                <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;text-align: right;">MONTO
                </td>
            </tr>
            <?php foreach ($venta->cuotas as $cuota): ?>
                <tr>
                    <td><?= $cuota->nro_letra ?></td>
                    <td><?= date('d/m/Y', strtotime($cuota->fecha_vencimiento)) ?></td>
                    <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . number_format($cuota->monto, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" style="border-top: 1px solid #000000;">INICIAL</td>
                <td style="border-top: 1px solid #000000; text-align: right;"><?= $venta->moneda_simbolo . ' ' . number_format($venta->inicial, 2) ?></td>
            </tr>
            <tr>
                <td colspan="2">DEUDA PENDIENTE</td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . number_format($venta->total - $venta->inicial, 2) ?></td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>


    <?= $venta->nota != NULL ? '<br><span style="text-decoration: underline;">NOTA:</span><br>' . $venta->nota . '<br>' : '' ?>

    <?php
    $hoy = date('Ymd');
    $vence = date('Ymd', strtotime(str_replace('/', '-', valueOption('FECHA_VENTA_PROMO', date('Ymd')))));

    if ($hoy < $vence) {
        echo '<br><div style="border-top: 2px dashed #0b0b0b; padding-top: 5px;">';
        echo valueOption('VENTA_PROMO', '') . '</div><br>';
    }
    ?>

    <div style="text-transform: uppercase; border-top: 1px dashed #0b0b0b; text-align: center;">
        GRACIAS POR LA COMPRA
    </div>
    <div style="text-transform: uppercase; border-top: 1px dashed #0b0b0b; text-align: center;">
        CANJEAR POR BOLETA O FACTURA
    </div>
    <br>

    <?php if (SERVER_NAME == SERVER_CRDIGITAL): ?>
        <style>
            .crdigital td {
                border: 1px solid #000000;;
            }
        </style>
        <table class="crdigital" cellspacing="0" cellpadding="5">
            <tr>
                <td style="height: 100px; text-align: center; vertical-align: bottom;">
                    -------------------------<br>
                    p. CR DIGITALL SCRL
                </td>
                <td style="padding-left:5px; width: 40%; text-align: left; vertical-align: bottom; position: relative;">
                    <div style="position: absolute; top: -14px; width: 100%; text-align: center;">GARANTE</div>
                    Frima: -------------------------<br><br>
                    Nombre: <br><br>
                    Domicilio: <br>

                </td>
                <td style="text-align: center; vertical-align: bottom;">
                    -------------------------<br>
                    COMPRADOR
                </td>
                <td style="text-align: center; vertical-align: bottom;">
                    -------------------------<br>
                    COMPRADOR
                </td>
            </tr>
        </table>
        <div style="font-size: 8px;">-	El Garante es fiador solidario con el deudor y renuncia expresamente al beneficio de su excusión.
            <br>-	El Comprador y Garante aceptan voluntariamente que ante la falta de pago. C.R DIGITALL S.C.R.L o el tenedor legitimado realice el llenado de una de las letras por el total de la deuda impaga más intereses y gastos administrativos, para su protesto y cobro judicial respectivo (Artº 10 Ley 27287) Ley de Títulos y Valores.
            <br>-	El Comprador acepta que el bien materia del crédito será de propiedad de C.R DIGITALL S.C.R.L, hasta la cancelación total de la deuda y por esta razón se promete a conservar en buenas condiciones el bien adquirido, por lo que ante su devolución por falta de pago pagara una penalidad ascendente al menoscabo del bien que será merituado por C.R DIGITALL S.C.R.L
            <br>-	La Garantía del bien solamente cubre defectos de fábrica y no los daños provocados por el uso inapropiado del mismo.
        </div>
    <?php endif; ?>

</div>
<script>
    this.print();
</script>