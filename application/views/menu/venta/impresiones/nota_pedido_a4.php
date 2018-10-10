<?php $term = diccionarioTermino() ?>
<style>
    body {
        color: #212121;
        text-transform: uppercase;
        font-family: sans-serif !important;
    }

    #header, #header_1 {
        width: 100%;
        display: table;
        clear: both;
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
        font-size: 14px;
        text-align: center;
    }

    #tipo_dcumento {
        padding-top: 10px;
        font-size: 14px;
        text-align: center;
    }

    #numero_documento {
        padding-top: 10px;
        font-size: 14px;
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
        text-decoration: underline;
    }

    #emisor_telefono, #emisor_correo {
        font-size: 11px;
    }

    #emisor_direccion {
        font-size: 12px;
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
        border: 0px solid #000;
        width: 100%;
        font-size: 9px;
    }

    #table_header tr th {
        text-transform: uppercase;
        text-align: left;
        width: 12%;
    }

    #table_header tr td {
        text-transform: uppercase;
    }

    /* SECCION DE PRODUCTOS */

    #producto_detalles {
        width: 100%;
        margin-top: 20px;
        border: 0px solid #000;
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
        border: 0px solid #000;
        color: #212121;
    }

    #producto_detalles td {
        text-transform: uppercase;
        padding: 4px 4px;
        border-left: 0px solid #000;
        border-right: 0px solid #000;
        color: #212121;
    }

    #qr_image {
        width: 2cm;
    }
</style>
<div id="body">
    <div id="header">
        <div class="col" style="display: table; clear: both; width: 70%">
            <div>
                <table>
                    <tr>
                        <td>
                            <img id="emisor_logo" align="middle"
                                 src="<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", 'logo.jpg')) ?>">
                        </td>
                        <td>
                            <div id="emisor_direccion"><?= $venta->local_nombre ?></div>
                            <div id="emisor_nombre_comercial"><?= $venta->local_direccion ?></div>
                            <div id="emisor_nombre_comercial"><?= $term[1]->valor ?>
                                : <?= $identificacion->config_value ?></div>
                            <div id="emisor_telefono">T&eacute;lefono: <?= valueOption('EMPRESA_TELEFONO') ?></div>
                        </td>
                    </tr>
                </table>
            </div>
            <div>

            </div>
        </div>
        <div class="col">
            <div style="border: 1px solid #000; padding-bottom: 15px; font-weight: bold;">
                <div id="emisor_ruc"><?= valueOption('EMPRESA_NOMBRE', '') ?></div>
                <div id="tipo_dcumento">NOTA DE VENTA</div>
                <div id="numero_documento">
                    Venta Nro:
                    <?= $venta->serie_documento != null ? $venta->serie_documento . ' - ' : '' ?>
                    <?= sumCod($venta->venta_id, 6) ?>
                </div>
            </div>
        </div>
    </div>

    <div id="header_1">
        <table id="table_header" cellspacing="0" cellpadding="3">
            <tr>
                <th>Cliente:</th>
                <td><?= $venta->cliente_nombre ?></td>
                <th>Fecha:</th>
                <td><?= date('d/m/Y', strtotime($venta->venta_fecha)) ?></td>
            </tr>
            <tr>
                <th><?= ($venta->tipo_cliente == '1') ? $term[1]->valor : $term[0]->valor ?>:</th>
                <td><?= $venta->ruc ?></td>
                <th>Vendedor:</th>
                <td><?= $venta->vendedor_nombre ?></td>
            </tr>
            <tr>
                <th>Vendedor:</th>
                <td><?= $venta->vendedor_nombre ?></td>
                <th>Tipo de Pago:</th>
                <td><?= $venta->condicion_nombre ?></td>
            </tr>
            <?php if ($venta->comprobante_id > 0): ?>
                <tr>
                    <th><?= $venta->comprobante_nombre ?><br> NCF: <?= $venta->comprobante ?></th>
                    <td>V&aacute;lido hasta: <?= date('d/m/Y', strtotime($venta->fecha_venc)) ?></td>
                    <th></th>
                    <td></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <table id="producto_detalles" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th style="border-top: #ccc 1px solid;">Cantidad</th>
            <th style="border-top: #ccc 1px solid;">Descripci&oacute;n</th>
            <th style="border-top: #ccc 1px solid;">UM</th>
            <th style="border-top: #ccc 1px solid;">Precio</th>
            <th style="border-top: #ccc 1px solid;">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($venta->detalles as $detalle):
            if (($i % 2) == 0) {
                $color = "#fff";
            } else {
                $color = "#F3F3F3";
            }
            ?>
            <tr class="td-data">
                <td style="background-color: <?= $color ?>"><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad : number_format($detalle->cantidad, 0) ?></td>
                <td style="background-color: <?= $color ?>; width: 50%;"><?= $detalle->producto_nombre ?></td>
                <td style="background-color: <?= $color ?>"><?= $detalle->unidad_abr ?></td>
                <td style="background-color: <?= $color ?>; white-space: nowrap; text-align: right;"><?= $venta->moneda_simbolo . ' ' . $detalle->precio ?></td>
                <td style="background-color: <?= $color ?>; white-space: nowrap; text-align: right;">
                    <?= $venta->moneda_simbolo . ' ' . number_format($detalle->importe, 2) ?>
                </td>
            </tr>
            <?php
            $i++;
        endforeach;
        ?>
        </tbody>
    </table>

    <table id="producto_detalles" cellspacing="0" cellpadding="0">
        <tr>
            <td width="70%">
                <?= $venta->nota ?>
            </td>
            <td valign="top" align="right" width="30%">
                <table cellspacing="0" cellpadding="0" border="0">
                    <?php if ($venta->descuento > 0): ?>
                        <tr>
                            <td style="text-align: left;" width="50%">Descuento</td>
                            <td style="text-align: right;white-space: nowrap;"
                                width="50%"><?= $venta->moneda_simbolo . ' ' . number_format($venta->descuento, 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (valueOptionDB('REDONDEO_VENTAS', 1) == 1): ?>
                        <tr>
                            <td style="text-align: left;" width="50%">Total de la venta</td>
                            <td style="text-align: right;white-space: nowrap;"
                                width="50%"><?= $venta->moneda_simbolo . ' ' . number_format($venta->total, 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="text-align: left;" width="50%">Total a Pagar</td>
                        <?php $total = valueOptionDB('REDONDEO_VENTAS', 1) == 1 ? formatPrice($venta->total) : $venta->total ?>
                        <td style="text-align: right;white-space: nowrap;"
                            width="50%"><?= $venta->moneda_simbolo . ' ' . $total ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Pagado</td>
                        <td style="text-align: right;white-space: nowrap;"><?= $venta->moneda_simbolo . ' ' . $venta->venta_pagado ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;">Vuelto</td>
                        <td style="text-align: right;white-space: nowrap;"><?= $venta->moneda_simbolo . ' ' . $venta->venta_vuelto ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <?php if (count($venta->cuotas) > 0): ?>
        <table id="producto_detalles" cellspacing="0" cellpadding="0" width="50%">
            <thead>
            <tr>
                <th style="border-top: #ccc 1px solid; text-align: left;">Letra</th>
                <th style="border-top: #ccc 1px solid; text-align: left;">Vence</th>
                <th style="border-top: #ccc 1px solid; text-align: left;">Monto</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            foreach ($venta->cuotas as $cuota):
                if (($i % 2) == 0) {
                    $color = "#fff";
                } else {
                    $color = "#F3F3F3";
                }
                ?>
                <tr>
                    <td style="background-color: <?= $color ?>"><?= $cuota->nro_letra ?></td>
                    <td style="background-color: <?= $color ?>"><?= date('d/m/Y', strtotime($cuota->fecha_vencimiento)) ?></td>
                    <td style="background-color: <?= $color ?>"><?= $venta->moneda_simbolo . ' ' . number_format($cuota->monto, 2) ?></td>
                </tr>
                <?php
                $i++;
            endforeach;
            ?>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="right">Pago Inicial</td>
                <?php $inicial = valueOptionDB('REDONDEO_VENTAS', 1) == 1 ? formatPrice($venta->inicial) : $venta->inicial ?>
                <td><?= $venta->moneda_simbolo . ' ' . $inicial ?></td>
            </tr>
            <tr>
                <td colspan="2" align="right">Deuda Pendiente</td>
                <td><?= $venta->moneda_simbolo . ' ' . number_format($venta->total - $venta->inicial, 2) ?></td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>
    <hr style="border-color:#A4A5A7;">
    <table id="table_header" cellspacing="0" cellpadding="3" border="0">
        <tr>
            <td width="60%">
                <table border="0">
                    <tr>
                        <th style="width: 5%;">SON:</th>
                        <td><span style="text-transform: uppercase;"><?= $totalLetras; ?></span></td>
                    </tr>
                    <tr>
                        <td style="" colspan="2">
                            <?php
                            $hoy = date('Ymd');
                            $vence = date('Ymd', strtotime(str_replace('/', '-', valueOption('FECHA_VENTA_PROMO', date('Ymd')))));

                            if ($hoy < $vence) {
                                echo valueOption('VENTA_PROMO', '') . '</div><br>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="40%">
                <table border="0">
                    <tr>
                        <td width="60%">Emitido a trav&eacute;s de</td>
                        <td width="40%"><img style="height: 35px" src="<?= EMITIDO_ATRAVEZDE ?>"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;" colspan="2">GRACIAS POR LA COMPRA</td>
        </tr>
        <tr>
            <td style="text-align: center;" colspan="2">CANJEAR POR BOLETA O FACTURA</td>
        </tr>
    </table>
</div>