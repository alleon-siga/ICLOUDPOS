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

        #qr_image {
            width: 3cm;
        }
    }
</style>
<div>
    <table style="border: 0px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase; text-align: center;"><?= valueOption('EMPRESA_NOMBRE', '') ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">R.U.C.: <?= $emisor->ruc ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Contacto: <?= valueOption('EMPRESA_CONTACTO', '') ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; text-align: left;">
                T&eacute;lefono: <?= valueOption('EMPRESA_TELEFONO', '') ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase; text-align: left;">
                Correo: <?= valueOption('EMPRESA_CORREO', '') ?></td>
        </tr>

        <tr>
            <td style="text-transform: uppercase; text-align: left;">
                Direcci&oacute;n: <?= $facturacion->direccion ?></td>
        </tr>
    </table>
    <hr>
    <table style="border: 0px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase; text-align: center;">
                <?php
                if ($facturacion->documento_tipo == '01') echo 'FACTURA ELECTR&Oacute;NICA';
                if ($facturacion->documento_tipo == '03') echo 'BOLETA  ELECTR&Oacute;NICA';
                if ($facturacion->documento_tipo == '07') echo 'NOTA DE CR&Eacute;DITO ELECTR&Oacute;NICA';
                if ($facturacion->documento_tipo == '08') echo 'NOTA DE D&Eacute;BITO ELECTR&Oacute;NICA';
                ?>
            </td>
        </tr>
    </table>
    <hr>
    <table style="border: 0px;"
           cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase;">
                Nro: <?= $facturacion->documento_numero_ceros ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Fecha: <?= date('d/m/Y', strtotime($facturacion->fecha)) ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Raz&oacute;n Social: <?= $facturacion->cliente_nombre ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Identificaci&oacute;n: <?= $facturacion->cliente_identificacion ?></td>
        </tr>

        <tr>
            <td style="text-transform: uppercase;">Guia de Remisi&oacute;n: <span>-</span></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Moneda: <span><?= $emisor->moneda_letra ?></span>
            </td>
        </tr>
        <?php if ($facturacion->documento_tipo == '07' || $facturacion->documento_tipo == '08'): ?>
            <tr>
                <td style="text-transform: uppercase;">Comprobante Afectado:
                    <span><?= $facturacion->documento_mod_numero_ceros ?></span></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;">Motivo: <span><?= $facturacion->motivo_nota ?></span>
            </tr>
        <?php endif; ?>
    </table>
<?php 
    if($facturacion->estado_comprobante==3){
        ?>
    <center>
    <span style="text-align:center;">___________________________________________________</span><br>
    <span style="text-align:center;">TICKET ANULADO</span><br>
    <span style="text-align:center;">¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯</span>
    </center>
        <?php
    }
    ?>

    <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Cantidad</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Precio</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Subtotal
            </td>
        </tr>
        <?php $i = 0; ?>
        <?php foreach ($facturacion->detalles as $detalle): ?>
            <tr>
                <td colspan="3"
                    style="<?= $i++ != 0 ? 'border-top: 1px dashed #0b0b0b;' : '' ?>"><?= $detalle->producto_descripcion ?></td>
            </tr>
            <tr>
                <td><?= number_format($detalle->cantidad, 3) . " " . $detalle->um ?></td>
                <td style="text-align: right"><?= $emisor->moneda_simbolo . ' ' . number_format($detalle->precio, 2) ?></td>
                <td style="text-align: right"><?= $emisor->moneda_simbolo . ' ' . number_format($detalle->cantidad * $detalle->precio, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3">
                <hr style="color: #0b0b0b;">
            </td>
        </tr>
        <tr>
            <td colspan="2">Subtotal:</td>
            <td style="text-align: right;"><?= $emisor->moneda_simbolo . ' ' . number_format($facturacion->subtotal, 2) ?></td>
        </tr>
        <tr>
            <td colspan="2">Impuesto:</td>
            <td style="text-align: right;"><?= $emisor->moneda_simbolo . ' ' . number_format($facturacion->impuesto, 2) ?></td>
        </tr>
        <tr>
            <td colspan="2">Total a Pagar:</td>
            <td style="text-align: right;"><?= $emisor->moneda_simbolo . ' ' . number_format($facturacion->total, 2) ?></td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
        <!--        <tr>-->
        <!--            <td colspan="2">Pagado:</td>-->
        <!--            <td style="text-align: right;">-->
        <? //= $venta->moneda_simbolo . ' ' . $venta->venta_pagado ?><!--</td>-->
        <!--        </tr>-->
        <!--        <tr>-->
        <!--            <td colspan="2">Vuelto:</td>-->
        <!--            <td style="text-align: right;">-->
        <? //= $venta->moneda_simbolo . ' ' . $venta->venta_vuelto ?><!--</td>-->
        <!--        </tr>-->

        </tbody>

    </table>
    <br>
    <div>
        <?php
        $n = $facturacion->total;
        $aux = (string)$n;
        $decimal = substr($aux, strpos($aux, "."));
        ?>
        SON: <?= $facturacion->total_letra . ' ' . $emisor->moneda_letra . ' ' . str_replace('.', '', $decimal) . '/100' ?>
    </div>
    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="border: 0; padding: 5px; text-align: center;">
                <img id="qr_image"
                     src="<?= base_url('recursos/qr/' . $emisor->ruc . '/' . $facturacion->documento_tipo . '-' . $facturacion->documento_numero . '.png') ?>">
            </td>
        </tr>
        <tr>
            <td style="border: 0;">
                Autorizado mediante Resoluci&oacute;n Nro: 0180050000804/SUNAT.
                Representac&oacute;n impresa del comprobante de venta electr&oacute;nica.<br>
                HASH: <?= $facturacion->hash_cpe ?>
            </td>
        </tr>
    </table>
    <p style="font-size: 11px;">Consulte su comprobante
        aqui: <?= base_url() . 'facturacion/consulta/' . md5($facturacion->id) ?></p>
</div>
<script>
    this.print();
</script>