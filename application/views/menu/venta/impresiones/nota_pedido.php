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
            <td style="text-transform: uppercase; text-align: center;">NOTA DE VENTA</td>
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
                <td colspan="3" style="<?= $i++ != 0 ? 'border-top: 1px dashed #0b0b0b;' : '' ?>">
                    <?php
                        $presentacion = '';
                        if(valueOption('EMBALAJE_IMPRESION')==1){
                            $presentacion = "(x ".$detalle->cantidad_und.' '.$detalle->simbolo_und.")";
                        }
                    ?>
                    <?= $detalle->producto_nombre.' '.$presentacion ?>
                </td>
            </tr>
            <tr>
                <td ><?= $detalle->producto_cualidad=="PESABLE"?$detalle->cantidad:number_format($detalle->cantidad,0) . "  " . $detalle->unidad_abr ?></td>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $detalle->precio ?></td>
            <?php
                if(valueOptionDB('REDONDEO_VENTAS', 1)=='1'){ //quiere decir si aplicar redondeo, esto esta en ventas/configuracion
                    $importe = number_format($detalle->importe, 1);
                    $importe = number_format($importe, 2);
                }else{
                    $importe = $detalle->importe;
                }
            ?>
                <td style="text-align: right"><?= $venta->moneda_simbolo . ' ' . $importe ?></td>
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
            <?php
                if(valueOptionDB('REDONDEO_VENTAS', 1)=='1'){ //quiere decir si aplicar redondeo, esto esta en ventas/configuracion
                    $total = number_format($venta->total, 1);
                    $total = number_format($total, 2);
                }else{
                    $total = $venta->total;
                }
            ?>
            <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . $total ?></td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="2">Pagado:</td>
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
                <?php
                    if(valueOptionDB('REDONDEO_VENTAS', 1)=='1'){ //quiere decir si aplicar redondeo, esto esta en ventas/configuracion
                        $inicial = number_format($venta->inicial, 1);
                        $inicial = number_format($inicial, 2);

                        $total = number_format($venta->total, 1);
                        $total = number_format($total, 2);
                    }else{
                        $inicial = $venta->inicial;
                        $total = $venta->total;
                    }
                ?>
                <td style="text-align: right;"><?= $venta->moneda_simbolo . ' ' . number_format($total - $inicial, 2) ?></td>
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

</div>
<script>
    this.print();
</script>