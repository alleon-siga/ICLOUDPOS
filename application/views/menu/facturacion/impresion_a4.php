<style>
    #body {
        color: #212121;
        font-family: "Arial Black", arial-black;
    }

    #header, #header_1 {
        width: 100%;
        display: table;
        clear: both;
    }

    #header .col {
        margin: 0;
        padding: 0;
        width: 50%;
        float: left;
        position: relative;
    }

    #emisor_logo {
        height: 100%;
    }

    #emisor_ruc {
        padding-top: 20px;
        font-size: 25px;
        text-align: center;
    }

    #tipo_dcumento {
        padding-top: 5px;
        font-size: 20px;
        text-align: center;
    }

    #numero_documento {
        font-size: 18px;
        text-align: center;
    }

    #emisor_nombre_comercial, #emisor_razon_social, #emisor_telefono, #emisor_correo {
        padding-left: 10px;
    }

    #emisor_nombre_comercial {
        padding-top: 10px;
        font-size: 20px;
    }

    #emisor_razon_social {
        font-size: 12px;
        text-decoration: underline;
    }

    #emisor_telefono, #emisor_correo {
        font-size: 12px;
    }

    #emisor_direccion {
        font-size: 13px;
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

    #header_1 .col1 {
        width: 55%;
    }

    #header_1 .col2 {
        width: 40%;
    }

    .col1 div, .col2 div {
        padding-top: 5px;
        font-weight: bold;
    }

    .col1 span, .col2 span {
        font-weight: normal;
    }

    /* SECCION DE PRODUCTOS */

    #producto_detalles {
        width: 100%;
        margin-top: 20px;
        font-family: "Arial Black", arial-black;
        border: 1px solid #c4c4c4;
    }

    .td-data td {
        font-size: 13px;
    }

    #producto_detalles thead tr {
        background-color: #f0f0f0;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    #producto_detalles th {
        padding: 12px 4px;
        border: 1px solid #c4c4c4;
        color: #212121;
    }

    #producto_detalles td {
        padding: 10px 4px;
        border-left: 1px solid #c4c4c4;
        border-right: 1px solid #c4c4c4;
        color: #212121;
    }

    #qr_image {
        width: 2cm;
    }
</style>

<div id="body">
    <div id="header">
        <div class="col" style="display: table; clear: both;">
            <div style="float: left; width: 24%;">
                <img id="emisor_logo" align="middle"
                     src="<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", 'logo.jpg')) ?>">
            </div>
            <div style="float: left; width: 75%;">
                <div id="emisor_nombre_comercial"><?= valueOption('EMPRESA_NOMBRE', '') ?></div>
                <div id="emisor_razon_social"><?= valueOption('EMPRESA_CONTACTO', '') ?></div>
                <div id="emisor_telefono"><?= valueOption('EMPRESA_TELEFONO', '') ?></div>
                <div id="emisor_correo">
                    Correo: <?= valueOption('EMPRESA_CORREO', '') != '' ? valueOption('EMPRESA_CORREO', '') : '-' ?></div>
            </div>
            <div id="emisor_direccion">Direcci&oacute;n: <?= $facturacion->direccion ?></div>
        </div>
        <div class="col">
            <div style="border: 1px solid #4c4c4c; padding-bottom: 15px; font-weight: bold;">
                <div id="emisor_ruc">
                    R.U.C. <?= $emisor->ruc ?>
                </div>
                <div id="tipo_dcumento">
                    <?php
                    if ($facturacion->documento_tipo == '01') echo 'FACTURA ELECTR&Oacute;NICA';
                    if ($facturacion->documento_tipo == '03') echo 'BOLETA ELECTR&Oacute;NICA';
                    if ($facturacion->documento_tipo == '07') echo 'NOTA DE CR&Eacute;DITO ELECTR&Oacute;NICA';
                    if ($facturacion->documento_tipo == '08') echo 'NOTA DE D&Eacute;BITO ELECTR&Oacute;NICA';
                    ?>
                </div>
                <div id="numero_documento">
                    <?= $facturacion->documento_numero_ceros ?>
                </div>
            </div>
        </div>
    </div>

    <div id="header_1">
        <div class="col1">
            <div>Raz&oacute;n Social: <span><?= $facturacion->cliente_nombre ?></span></div>
            <div>Identificaci&oacute;n: <span><?= $facturacion->cliente_identificacion ?></span></div>
            <div>Direcci&oacute;n: <span><?= $facturacion->cliente_direccion ?></span></div>
            <?php if ($facturacion->documento_tipo == '07' || $facturacion->documento_tipo == '08'): ?>
                <div>Comprobante Afectado: <span><?= $facturacion->documento_mod_numero_ceros ?></span></div>
            <?php endif; ?>
        </div>
        <div class="col2">
            <div>Fecha de Emisi&oacute;n: <span><?= date('d/m/Y', strtotime($facturacion->fecha)) ?></span></div>
            <div>Guia de Remisi&oacute;n: <span>-</span></div>
            <div>Moneda: <span><?= $emisor->moneda_letra ?></span></div>
            <?php if ($facturacion->documento_tipo == '07' || $facturacion->documento_tipo == '08'): ?>
                <div>Motivo: <span><?= $facturacion->motivo_nota ?></span></div>
            <?php endif; ?>
        </div>
    </div>

    <table id="producto_detalles" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>C&oacute;digo</th>
            <th>Descripci&oacute;n</th>
            <th>UM</th>
            <th>Cantidad</th>
            <th style="white-space: nowrap;">Precio</th>
            <th>Importe</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($facturacion->detalles as $detalle): ?>
            <tr class="td-data">
                <td><?= $detalle->producto_codigo ?></td>
                <td style="width: 50%;"><?= $detalle->producto_descripcion ?></td>
                <td><?= $detalle->um ?></td>
                <td><?= number_format($detalle->cantidad, 3) ?></td>
                <td style="white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($detalle->precio, 2) ?></td>
                <td style="white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($detalle->precio * $detalle->cantidad, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" style="border: 1px solid #c4c4c4; padding-top: 25px;">
                <?php
                $n = $facturacion->total;
                $aux = (string)$n;
                $decimal = substr($aux, strpos($aux, "."));
                ?>
                SON: <?= $facturacion->total_letra . ' ' . $emisor->moneda_letra . ' ' . str_replace('.', '', $decimal) . '/100' ?></td>
            <th style="background-color: #f0f0f0; text-align: left;">Subtotal</th>
            <th style="text-align: left;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->subtotal, 2) ?></th>
        </tr>
        <tr>
            <td colspan="4" rowspan="2" style="border: 1px solid #c4c4c4;">
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tr>
                        <td style="border: 0; padding: 5px;">
                            <img id="qr_image"
                                 src="<?= base_url('recursos/qr/' . $emisor->ruc . '/' . $facturacion->documento_tipo . '-' . $facturacion->documento_numero . '.png') ?>">
                        </td>
                        <td style="border: 0;">
                            Autorizado mediante Resolución de
                            Intendencia N° 032-005-0001476/SUNAT Representación
                            impresa de la Boleta Electrónica.<br>
                            HASH: <?= $facturacion->hash_cpe ?>
                        </td>
                    </tr>
                </table>
                <p style="font-size: 10px;">Consulte su comprobante
                    aqui: <?= base_url() . 'facturacion/consulta/' . md5($facturacion->id) ?></p>
            </td>
            <th style="background-color: #f0f0f0; text-align: left;">Impuesto</th>
            <th style="text-align: left;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->impuesto, 2) ?></th>
        </tr>
        <tr>
            <th style="background-color: #f0f0f0; text-align: left;">Total</th>
            <th style="text-align: left;white-space: nowrap;"><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->total, 2) ?></th>
        </tr>
        </tbody>
    </table>
</div>
