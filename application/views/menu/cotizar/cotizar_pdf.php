<?php $term = diccionarioTermino() ?>
<style type="text/css">
    table {
        width: 100%;
        /*border: 0px;*/
        font-size: 11px;
    }

    thead, th {
        border: #111 0.5px solid;
    }

</style>


<table>
    <tr>
        <td><img height="100" src="<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", '')) ?>"></td>
        <td style="height: 100px; text-align: center; width: 50%;"><?= valueOption('COTIZACION_INFORMACION', '') ?></td>
        <td style="text-align: center; border: 1px solid #000; vertical-align: middle; font-size: 1.5em;">
            # COTIZACION <?= sumCod($cotizar->id) ?><br><br><?= date('d/m/Y') ?>
        </td>
    </tr>
</table>

<br>

<table>
    <tr>
        <td>NOMBRE DEL CLIENTE: <?= $cotizar->cliente_nombre ?></td>
    </tr>
    <tr>
        <td>DIRECCI&Oacute;N: <?= $cotizar->cliente_direccion == "" ? '-' : $cotizar->cliente_direccion ?></td>
    </tr>
    <tr>
        <td><?= $tipo_cliente == '2' ? $term[1]->valor : $term[0]->valor ?>: <?= $cotizar->ruc ?></td>
    </tr>
    <tr>
        <td>TEL&Eacute;FONO: <?= $cotizar->telefono == "" ? '-' : $cotizar->telefono ?></td>
    </tr>
</table>

<br>

<table>
    <tr>
        <td>LUGAR DE ENTREGA: <?= $cotizar->lugar_entrega ?></td>
        <td>CONTACTO: <?= valueOption('EMPRESA_CONTACTO', '-') ?></td>
    </tr>
    <tr>
        <td>FECHA DE ENTREGA: <?= date('d/m/Y', strtotime($cotizar->fecha_entrega)) ?></td>
        <td>CORREO: <?= valueOption('EMPRESA_CORREO', '-') ?></td>
    </tr>
    <tr>
        <td>FORMA DE PAGO: <?= $cotizar->condicion_nombre ?></td>
        <td>N<sup>o</sup> CELULAR: <?= valueOption('EMPRESA_TELEFONO', '-') ?></td>
    </tr>
</table>

<br>

<table cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th><?= getCodigoNombre() ?></th>
        <th>DESCRIPCI&Oacute;N PRODUCTO</th>
        <th>Cantidad</th>
        <th>UND</th>
        <th>P. UNIT</th>
        <th>TOTAL</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($cotizar->detalles as $detalle): ?>
        <tr>
            <td style="border: #111 0.5px solid;"><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
            <td style="border: #111 0.5px solid;"><?= $detalle->producto_nombre ?></td>
            <td style="border: #111 0.5px solid; text-align: center;"><?= $detalle->cantidad ?></td>
            <td style="border: #111 0.5px solid; text-align: center;"><?= $detalle->unidad_nombre ?></td>
            <td style="border: #111 0.5px solid; text-align: center;"><?= $cotizar->moneda_simbolo . " " . $detalle->precio ?></td>
            <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->moneda_simbolo . " " . number_format($detalle->importe, 2) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php for ($i = 0; $i < (20 - count($cotizar->detalles)); $i++): ?>
        <tr>
            <td style="border: #111 0.5px solid; color: #fff;">-</td>
            <td style="border: #111 0.5px solid;"></td>
            <td style="border: #111 0.5px solid; text-align: center;"></td>
            <td style="border: #111 0.5px solid; text-align: center;"></td>
            <td style="border: #111 0.5px solid; text-align: center;"></td>
            <td style="border: #111 0.5px solid; text-align: center;"></td>
        </tr>
    <?php endfor; ?>
    <?php if ($cotizar->documento_id == 1): ?>
        <tr>
            <td colspan="4"></td>
            <th style="text-align: left;">SUBTOTAL</th>
            <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->subtotal ?></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <th style="text-align: left;">IMPUESTOS</th>
            <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->impuesto ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td colspan="4"></td>
        <th style="text-align: left;">DESCUENTO</th>
        <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->moneda_simbolo . " " . number_format($cotizar->descuento, 2) ?></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <th style="text-align: left;">TOTAL</th>
        <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->moneda_simbolo . " " . $cotizar->total ?></td>
    </tr>
    </tbody>
</table>
<br>
<div style="text-align: center;">
    <?= valueOption('COTIZACION_CONDICION', '') ?>
</div>
<br>
<div style="text-align: center; font-size: 9px;">
    <?= valueOption('COTIZACION_PIE_PAGINA', 'fsfd') ?>
</div>




