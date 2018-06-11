<style>
    .row {
        width: 100%;
        display: table;
        clear: both;
        font-size: 9px;
    }

    .row .col {
        margin: 0;
        padding: 0;
        float: left;
        position: relative;
    }

    table {
        width: 100%;
    }

    table td, table th {
        font-size: 9px;
        text-transform: uppercase;
    }

    .header2 td {
        border-bottom: 1px dotted #000;
    }

    .producto_detalles_block {
        border: 1px solid #000;
        border-radius: 4px;
    }

    .producto_detalles th {
        border-bottom: 1px solid #000;
        border-right: 1px solid #000;
    }

    .producto_detalles td {
        border-bottom: 1px dotted #000;
        border-right: 1px solid #000;
    }

    .td_productos td {
        height: 35px;
    }

    .crdigital td {
        border: 1px solid #000000;;
    }
</style>

<div style="text-transform: uppercase;">
    <div class="row">
        <div class="col" style="width: 40%; font-size: 8px;">
            <img style="height: 95px;" align="middle"
                 src="<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", 'logo.jpg')) ?>">
            <div style="text-align: center;">Telefono: <?= valueOption('EMPRESA_TELEFONO', '') ?></div>
            <div><?= $venta->local_direccion ?></div>
        </div>
        <div class="col" style="width: 30%;">
            <div style="border: 1px solid #000; margin: 5px; width: 50%; float: right; padding: 5px;"
                 id="header1_2_span">
                Fact.: <span style="border: 1px solid #000;">    </span>
                Bol. Vta.: <span style="border: 1px solid #000;">    </span>
                <br><br>Nro: <sub>...........................................................</sub>
            </div>
        </div>
        <div class="col" style="width: 25%; border: 1px solid #000; border-radius: 8px; float: right; padding: 10px;">
            <div style="text-align: center; font-size: 14px; border-bottom: 1px solid #000; padding-bottom: 12px;">RUC
            </div>
            <div style="text-align: center; font-size: 14px; border-bottom: 1px solid #000; padding-bottom: 12px; padding-top: 12px;">
                PEDIDO COMPRA-VENTA
            </div>
            <div style="text-align: center; font-size: 14px; padding-top: 15px;">
                No. <?= sumCod($venta->venta_id, 8) ?></div>
        </div>
    </div>

    <table class="header2" cellpadding="4">
        <tr>
            <td><strong>Fecha:</strong> <?= date('d/m/Y h:i a', strtotime($venta->venta_fecha)) ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Comprador:</strong> <?= $venta->cliente_nombre ?></td>
            <td><strong>Ident.:</strong> <?= $venta->identificacion ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Conyuge:</strong></td>
            <td><strong>Ident.:</strong></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Domicilio:</strong> <?= $venta->cliente_direccion ?></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Dir. de Cobranza:</strong></td>
        </tr>
        <tr>
            <td><strong>Modalidad:</strong> <?= $venta->condicion_nombre ?> </td>
            <td><strong>Tel./Cel.:</strong> <?= $venta->cliente_telefono ?> </td>
            <td><strong>RUC:</strong></td>
        </tr>
    </table>
    <br>
    <div class="producto_detalles_block">
        <table class="producto_detalles" cellpadding="3" cellspacing="0">
            <tr>
                <th>C&oacute;digo</th>
                <th>Cant.</th>
                <th>Unidad</th>
                <th>Descripci&oacute;n</th>
                <th>P. Unitario</th>
                <th>Total</th>
            </tr>

            <?php foreach ($venta->detalles as $detalle): ?>
                <tr class="td_productos">
                    <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                    <td><?= number_format($detalle->cantidad, 0) ?></td>
                    <td><?= $detalle->unidad_abr ?></td>
                    <td><?= $detalle->producto_nombre ?></td>
                    <td style="text-align: right; white-space: nowrap;"><?= $venta->moneda_simbolo . ' ' . $detalle->precio ?></td>
                    <td style="text-align: right; white-space: nowrap;"><?= $venta->moneda_simbolo . ' ' . $detalle->importe ?></td>
                </tr>
            <?php endforeach; ?>
            <?php for($i = count($venta->detalles); $i < 13; $i++):?>
                <tr class="td_productos">
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                </tr>
            <?php endfor;?>
            <tr>
                <td colspan="6" style="text-align: center; border: 1px solid #000; border-left: 0px;">CONDICIONES DE
                    PAGO
                </td>
            </tr>
            <tr>
                <td colspan="6" style="border: 1px solid #000; border-left: 0px; border-top: 0px;">Inicial:</td>
            </tr>

            <tr>
                <td colspan="5" style="border-bottom: 0; height: 100px; vertical-align: bottom;"> SON:
                    <span style="text-transform: uppercase;"><?= $totalLetras; ?></span>
                </td>
                <td style="border-bottom: 0; white-space: nowrap; vertical-align: bottom;">
                    <?= $venta->moneda_simbolo . ' ' . $venta->total ?>
                </td>
            </tr>
        </table>
    </div>
    <br>
    <table class="crdigital" cellspacing="0" cellpadding="5">
        <tr>
            <td style="height: 100px; text-align: center; vertical-align: bottom;">
                --------------------------------<br>
                p. CR DIGITALL SCRL
            </td>
            <td style="padding-left:5px; width: 40%; text-align: left; vertical-align: bottom; position: relative;">
                <div>GARANTE</div>
                <br><br>
                Frima: --------------------------------<br><br>
                Nombre: <br><br>
                Domicilio: <br>

            </td>
            <td style="text-align: center; vertical-align: bottom;">
                --------------------------------<br>
                COMPRADOR
            </td>
            <td style="text-align: center; vertical-align: bottom;">
                --------------------------------<br>
                COMPRADOR
            </td>
        </tr>
    </table>
    <div style="font-size: 8px; text-transform: none;">- El Garante es fiador solidario con el deudor y renuncia
        expresamente al beneficio
        de su excusión.
        <br>- El Comprador y Garante aceptan voluntariamente que ante la falta de pago. C.R DIGITALL S.C.R.L o el
        tenedor legitimado realice el llenado de una de las letras por el total de la deuda impaga más intereses y
        gastos administrativos, para su protesto y cobro judicial respectivo (Artº 10 Ley 27287) Ley de Títulos y
        Valores.
        <br>- El Comprador acepta que el bien materia del crédito será de propiedad de C.R DIGITALL S.C.R.L, hasta
        la cancelación total de la deuda y por esta razón se promete a conservar en buenas condiciones el bien
        adquirido, por lo que ante su devolución por falta de pago pagara una penalidad ascendente al menoscabo del
        bien que será merituado por C.R DIGITALL S.C.R.L
        <br>- La Garantía del bien solamente cubre defectos de fábrica y no los daños provocados por el uso
        inapropiado del mismo.
    </div>
</div>