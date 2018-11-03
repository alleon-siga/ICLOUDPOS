<style>
    #body {
        color: #212121;
        text-transform: uppercase;
        /*font-family: "Arial Black", arial-black;*/
    }

    #header, #header_1 {
        width: 100%;
        display: table;
        clear: both;
    }

    #header .col-caja {
        margin: 0;
        padding-left: 40%;
        width: 30%;
        float: left;
        position: relative;
    }
    #header .col {
        margin: 0;
        padding: 0;
        width: 30%;
        float: left;
        position: relative;
    }
    #emisor_logo {
        height: 95px;
    }

    #emisor_ruc {
        padding-top: 20px;
        font-size: 18px;
        text-align: center;
    }

    #tipo_dcumento {
        padding-top: 20px;
        font-size: 15px;
        text-align: center;
    }

    #numero_documento {
        padding-top: 10px;
        font-size: 15px;
        text-align: center;
    }

    #emisor_nombre_comercial, #emisor_razon_social, #emisor_telefono, #emisor_correo {
    }

    #emisor_nombre_comercial {
        padding-top: 5px;
        font-size: 11px;
    }

    #emisor_razon_social {
        font-size: 11px;
    }

    #emisor_telefono, #emisor_correo {
        font-size: 10px;
    }

    #emisor_direccion {
        font-size: 10px;
    }

    #header_1 div {
        margin: 0;
        padding: 0;
        float: left;
        position: relative;
    }

    /* HEADER 1 */

    #header_1 {
        margin-top: 20px;
    }

    #table_header {
        padding: 6px;
        border: 1px solid #000;
        width: 100%;
        font-size: 9px;
    }

    #table_header tr th {
        text-transform: uppercase;
        text-align: left;
        width: 20%;
    }

    #table_header tr td {
        text-transform: uppercase;
    }

    /* SECCION DE PRODUCTOS */

    #producto_detalles {
        width: 100%;
        margin-top: 20px;
        border: 1px solid #000;
        font-size: 9px;
    }

    .td-data td {
        text-transform: uppercase;
        font-size: 9px;
    }

    #producto_detalles thead tr {
        padding-top: 1px;
        padding-bottom: 1px;
    }

    #producto_detalles th {
        text-transform: uppercase;
        font-size: 9px;
        padding: 4px 4px;
        border: 1px solid #000;
        color: #212121;
    }

    #producto_detalles td {
        text-transform: uppercase;
        padding: 4px 4px;
        border-left: 1px solid #000;
        border-right: 1px solid #000;
        color: #212121;
    }

    #qr_image {
        width: 2cm;
    }
</style>

<div id="body">
    <div id="header">
        <div class="col" style="display: table; clear: both;">
            <div>
                <img id="emisor_logo" align="middle"
                     src="<?= base_url('recursos/img/logo/' . valueOptionDB("EMPRESA_LOGO", 'logo.jpg')) ?>">
            </div>
            <div>
                <div id="emisor_nombre_comercial"><?= $emisor->nombre_comercial!="-" && $emisor->nombre_comercial!=""?$emisor->nombre_comercial:"" ?></div>
                <div id="emisor_razon_social"><?= $emisor->razon_social!="-" && $emisor->razon_social!=""?$emisor->razon_social:"" ?></div>
                <div id="emisor_direccion"><?= $emisor->direccion!="-" && $emisor->direccion!=""?$emisor->direccion:"" ?></div>
                <div id="emisor_telefono"><?= valueOption('EMPRESA_TELEFONO', '')!="-" && valueOption('EMPRESA_TELEFONO', '')!=""?valueOption('EMPRESA_TELEFONO', ''):""  ?></div>
            </div>
        </div>
        <div class="col-caja">
            <div style="border: 1px solid #000; padding-bottom: 15px; font-weight: bold;">
                <div id="emisor_ruc">
                    R.U.C. <?= $emisor->ruc ?>
                </div>
                <div id="tipo_dcumento">
                    <?php
                    if ($facturacion->documento_tipo == '01')
                        echo 'FACTURA ELECTR&Oacute;NICA';
                    if ($facturacion->documento_tipo == '03')
                        echo 'BOLETA ELECTR&Oacute;NICA';
                    if ($facturacion->documento_tipo == '07')
                        echo 'NOTA DE CR&Eacute;DITO ELECTR&Oacute;NICA';
                    if ($facturacion->documento_tipo == '08')
                        echo 'NOTA DE D&Eacute;BITO ELECTR&Oacute;NICA';
                    ?>
                </div>
                <div id="numero_documento">
<?= $facturacion->documento_numero_ceros ?>
                </div>
            </div>
        </div>
    </div>

    <div id="header_1">
        <table id="table_header" cellspacing="0" cellpadding="3">
            <tr>
                <th>Raz&oacute;n Social:</th>
                <td><?= $facturacion->cliente_nombre ?></td>
                <th>Fecha de Emisi&oacute;n:</th>
                <td><?= date('d/m/Y', strtotime($facturacion->fecha)) ?></td>
            </tr>
            <tr>
                <th>Identificaci&oacute;n:</th>
                <td><?= $facturacion->cliente_identificacion ?></td>
                <th>Guia de Remisi&oacute;n:</th>
                <td>-</td>
            </tr>
            <tr>
                <th>Direcci&oacute;n:</th>
                <td><?= $facturacion->cliente_direccion ?></td>
                <th>Moneda:</th>
                <td><?= $emisor->moneda_letra ?></td>
            </tr>
<?php if ($facturacion->documento_tipo == '07' || $facturacion->documento_tipo == '08'): ?>
                <tr>
                    <th>Comprobante Afectado:</th>
                    <td><?= $facturacion->documento_mod_numero_ceros ?></td>
                    <th>Motivo:</th>
                    <td><?= $emisor->motivo_nota ?></td>
                </tr>
<?php endif; ?>
                <tr>
                <th>Vendedor:</th>
                <td><?= $facturacion->username ?></td>
<!--                <th>Forma de Pago:</th>
                <td><?= $emisor->moneda_letra ?></td>-->
            </tr>
        </table>
    </div>

    <table id="producto_detalles" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>C&oacute;digo</th>
                <th>Cantidad</th>
                <th>UM</th>
                <th>Descripci&oacute;n</th>                
                <th style="white-space: nowrap;">P.U.</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($facturacion->detalles as $detalle): ?>
                <tr class="td-data">
                    <td><?= $detalle->producto_codigo ?></td>
                    <td><?= number_format($detalle->cantidad, 0) ?></td>
                    <td><?= $detalle->um ?></td>
                    <td style="width: 50%;"><?= $detalle->producto_descripcion ?></td>
                    <td style="white-space: nowrap; text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($detalle->precio, 2) ?></td>
                    <td style="white-space: nowrap; text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($detalle->precio * $detalle->cantidad, 2) ?></td>
                </tr>
                    <?php endforeach; ?>

            
            <tr>
                <td colspan="4" style="border-top: 1px solid #000;" rowspan="6">
                    <?php
                    if ($facturacion->estado_comprobante == 3) {
                        ?>
                        <img src="recursos/img/anulado.png" style="margin-left: 15%;width: 300px;"> 
                        <?php
                    }
                    ?>
                 </td>
                <th style="text-align: left;">Gravadas</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->total_gravadas, 2) ?></th>
            </tr>
            <tr>
                <th style="text-align: left;">Inafectas</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->total_inafectas, 2) ?></th>
            </tr>
            <tr>
                <th style="text-align: left;">Exoneradas</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->total_exoneradas, 2) ?></th>
            </tr>
            <tr>
                <th style="text-align: left;">Gratuitas</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format(0, 2) ?></th>
            </tr>
            <tr>
                <th style="text-align: left;">Descuento</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format(0, 2) ?></th>
            </tr>
            <tr>
                <th style="text-align: left;">Subtotal</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->subtotal, 2) ?></th>
            </tr>
            <tr>
                <td rowspan="2" colspan="4" style="border: 1px solid #000;">
                    <?php
                    $n = $facturacion->total;
                    $aux = (string) $n;
                    $decimal = substr($aux, strpos($aux, "."));
                    ?>
                    SON: <?= $facturacion->total_letra . ' ' . $emisor->moneda_letra . ' ' . str_replace('.', '', $decimal) . '/100' ?>
                </td>
                <th style="text-align: left;">IGV</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->impuesto, 2) ?></th>
            </tr>
            <tr>

                <th style="text-align: left;">Total</th>
                <th style="text-align: right;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->total, 2) ?></th>
            </tr>
        </tbody>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tr>
            <td style="border: 0; padding: 5px; width: 2.5cm;">
                <img id="qr_image"
                     src="<?= base_url('recursos/qr/' . $emisor->ruc . '/' . $facturacion->documento_tipo . '-' . $facturacion->documento_numero . '.png') ?>">
            </td>
            <td style="border: 0; font-size: 9px;">
                Autorizado mediante Resoluci&oacute;n Nro: 0180050000804/SUNAT.
                Representac&oacute;n impresa del comprobante de venta electr&oacute;nica.<br><br>
                HASH: <?= $facturacion->hash_cpe ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p style="font-size: 9px;">Consulte su comprobante
                    aqui: <?= base_url() . 'facturacion/consulta/' . md5($facturacion->id) ?></p>
            </td>
        </tr>
    </table>
</div>
