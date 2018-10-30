<?php $term = diccionarioTermino() ?>
<style type="text/css">
    table {
        width: 100%;
        font-size: 11px;
    }

    thead, th {
        border: #111 0.5px solid;
    }
    body{
        font-size: 12px;
        font-family: sans-serif;
    }
</style>
<?php
    $colores = json_decode(valueOption("COTIZACION_COLOR_FORMATO"));
    $bordes = $colores[0];
    $empresa = $colores[1];
?>
<body>
    <hr style="color:<?= $empresa ?>; height: 5px;">
    <table border="0">
        
        <tr>
            <td colspan="2" align="center"><?= valueOption('COTIZACION_INFORMACION'); ?></td>
        </tr>
        <tr>
            <td width="50%" style="text-align: left;" rowspan="5" valign="middle">
                <img height="120" src="<?= base_url('recursos/img/logo/' . valueOptionDB("EMPRESA_LOGO", 'logo.jpg')) ?>">
            </td>
            <td width="50%" style="font-size: 12px; text-align: right; vertical-align: middle; color:red; font-weight: bold;">
                <br><?= date('d/m/Y') ?>
            </td>
        </tr>
        <tr>
            <td style="text-align: right; vertical-align: middle; font-size: 28px; color:<?= $empresa ?>;">
                Cotizaci&oacute;n Nro: <?= sumCod($cotizar->id) ?>
            </td>
        </tr>
        <tr>
            <td style="text-align:right; text-transform: uppercase; color:<?= $bordes ?> ; font-size: 22px;"><?= valueOption('EMPRESA_NOMBRE'); ?></td>
        </tr>
        <tr>
            <td style="text-align:right; text-transform: uppercase; color: <?= $bordes ?>; font-size: 22px;"><?= $term[1]->valor.': '.valueOption('EMPRESA_IDENTIFICACION'); ?></td>
        </tr>
        <tr>
            <td style="text-align:right; text-transform: uppercase; color: <?= $bordes ?>; font-size: 12px;"><?= valueOption('EMPRESA_DIRECCION'); ?></td>
        </tr>
        <tr>
            <td valign="top">
                <table>
                    <tr>
                        <td style="height: 20px; font-weight: bold;">Datos de contacto</td>
                    </tr>
                    <tr>
                        <td style="height: 20px;">CONTACTO: <?= $cotizar->vendedor_nombre ?></td>
                    </tr>
                    <tr>
                        <td style="height: 20px;">CORREO: <?= valueOption('EMPRESA_CORREO', '-') ?></td>
                    </tr>
                    <tr>
                        <td style="height: 20px;">TEL&Eacute;FONO: <?= valueOption('EMPRESA_TELEFONO') ?></td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table>
                    <tr>
                        <td style="height: 20px; font-weight: bold;">A la atenci&oacute;n de</td>
                    </tr>
                    <tr>
                        <td style="height: 20px;">NOMBRE DEL CLIENTE: <?= $cotizar->cliente_nombre ?></td>
                    </tr>
                    <tr>
                        <td style="height: 20px;"><?= $tipo_cliente == '2' ? $term[1]->valor : $term[0]->valor ?>: <?= $cotizar->ruc ?></td>
                    </tr>
                    <tr>
                        <td style="height: 20px;">TEL&Eacute;FONO: <?= $cotizar->telefono == "" ? '-' : $cotizar->telefono ?></td>
                    </tr>
                    <tr>
                        <td style="height: 20px;">DIRECCI&Oacute;N: <?= $cotizar->cliente_direccion == "" ? '-' : $cotizar->cliente_direccion ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table cellpadding="5" cellspacing="0" border="0">
    <?php
        $hayDescuento = false;
        $columnaPie = 6;
        foreach ($cotizar->detalles as $detalle):
            if($detalle->descuento > 0){
                $hayDescuento = true;
                $columnaPie = 8;
                break;
            }
        endforeach;
    ?>
        <thead>
            <tr>
                <td width="5%" style="border-top: #ccc 1px solid; color:<?= $bordes ?>;"><?= getCodigoNombre() ?></td>
                <td width="45%" style="border-top: #ccc 1px solid; color:<?= $bordes ?>;">Descripci&oacute;n</td>
                <td width="10%" style="text-align: center;border-top: #ccc 1px solid; color:<?= $bordes ?>;">Cantidad</td>
                <td width="10%" style="text-align: center;border-top: #ccc 1px solid; color:<?= $bordes ?>;">Unidad</td>
                <?php if($hayDescuento==true): ?>
                <td width="13%" style="text-align: center;border-top: #ccc 1px solid; color:<?= $bordes ?>;">P. Lista</td>
                <td width="13%" style="text-align: center;border-top: #ccc 1px solid; color:<?= $bordes ?>;">% Desc.</td>
                <?php endif; ?>
                <td width="13%" style="text-align: center;border-top: #ccc 1px solid; color:<?= $bordes ?>;">P.U.</td>                
                <td width="13%" style="text-align: center;border-top: #ccc 1px solid; color:<?= $bordes ?>;">Precio total</td>
            </tr>
        </thead>
        <tbody>
        <?php 
            $i=1;
            foreach ($cotizar->detalles as $detalle):
                if(($i % 2) == 0){
                    $color ="#fff";
                }else{
                    $color ="#F3F3F3";
                }

                $presentacion = '';
                if(valueOption('EMBALAJE_IMPRESION')==1){
                    $presentacion = "(x ".$detalle->cantidad_und.' '.$detalle->simbolo_und.")";
                }
        ?>
            <tr>
                <td style="white-space: normal; background-color: <?= $color ?>"><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                <td style="white-space: normal; background-color: <?= $color ?>"><?= $detalle->producto_nombre.' '.$presentacion ?></td>
                <td style="white-space: normal; text-align: center; background-color: <?= $color ?>"><?= $detalle->cantidad ?></td>
                <td style="white-space: normal; text-align: center; background-color: <?= $color ?>"><?= $detalle->unidad_nombre ?></td>
                <?php if($hayDescuento==true): ?>
                <td style="white-space: normal; text-align: center; background-color: <?= $color ?>"><?= $cotizar->moneda_simbolo . " " . number_format($detalle->precio_venta, 2) ?></td>
                <td style="white-space: normal; text-align: right; background-color: <?= $color ?>"><?= number_format($detalle->descuento, 2) ?> %</td>
                <?php endif; ?>
                <td style="white-space: normal; text-align: right; background-color: <?= $color ?>"><?= $cotizar->moneda_simbolo . " " . number_format($detalle->precio, 2) ?></td>                
                <td style="white-space: normal; text-align: right; background-color: <?= $color ?>"><?= $cotizar->moneda_simbolo . " " . number_format($detalle->importe, 2) ?></td>
            </tr>
        <?php
            endforeach;
        ?>
            <tr>
                <td colspan="<?= $columnaPie ?>" style="border-bottom: #ccc 1px solid; background-color: #F3F3F3;">&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <table border="0">
        <tr>
            <td width="65%">
                <table border="0">
                    <tr>
                        <td rowspan="<?= $rowspan ?>" colspan="4" style="color: #434343;"><?= $cotizar->nota ?></td>
                    </tr>
                </table>
            </td>
            <td valign="top" align="right" width="35%">
                <table border="0" align="right">
                <?php if ($cotizar->documento_id == 1): ?>
                    <tr>
                        <td style="text-align: left; font-weight: bold; width: 160px; height: 25px;">SUBTOTAL</td>
                        <td style="text-align: right;"><?= $cotizar->subtotal ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">IMPUESTOS</td>
                        <td style="text-align: right;"><?= $cotizar->impuesto ?></td>
                    </tr>
                <?php endif; ?>
                    <tr>
                        <td style="text-align: left; font-weight: bold; width: 160px; height: 25px;">DESCUENTO</td>
                        <td style="text-align: right;"><?= $cotizar->moneda_simbolo . " " . number_format($cotizar->descuento, 2) ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">TOTAL</td>
                        <td style="text-align: right;"><?= $cotizar->moneda_simbolo . " " . $cotizar->total ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table width="50%">
        <thead>
            <tr>
                <td style="border-top: #ccc 1px solid; color:<?= $bordes ?>; text-align: center;">Lugar de entrega</td>
                <td style="border-top: #ccc 1px solid; color:<?= $bordes ?>; text-align: center;">Fecha de entrega</td>
                <td style="border-top: #ccc 1px solid; color:<?= $bordes ?>; text-align: center;">Forma de pago</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center"><?= $cotizar->lugar_entrega ?></td>
                <td align="center"><?= date('d/m/Y', strtotime($cotizar->fecha_entrega)) ?></td>
                <td align="center"><?= $cotizar->condicion_nombre ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="border-bottom: #ccc 1px solid; background-color: #F3F3F3;">&nbsp;</td>
            </tr>
        </tfoot>
    </table>
    <br>
    <div style="text-align: left;">
        <?= valueOption('COTIZACION_CONDICION', '') ?>
    </div>
    <br>
    <div style="text-align: center; font-size: 15px; font-weight: bold;">
    Valido hasta el <?= date("d/m/Y", strtotime($cotizar->fecha)) ?>
    </div>
    <br>
    <div style="text-align: center;">
        <?= valueOption('COTIZACION_PIE_PAGINA', 'fsfd') ?>
    </div>
</body>