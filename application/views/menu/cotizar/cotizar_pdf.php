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
        <td></td>
        <td style="height: 100px; text-align: center; width: 50%;"><?= valueOption('COTIZACION_INFORMACION', '')?></td>
        <td style="text-align: right;"><?= date('d/m/Y', strtotime($cotizar->fecha)) ?></td>
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
        <td>RUC: <?= $cotizar->ruc ?></td>
    </tr>
    <tr>
        <td>TEL&Eacute;FONO: <?= $cotizar->telefono == "" ? '-' : $cotizar->telefono ?></td>
    </tr>
</table>

<br>

<table>
    <tr>
        <td>LUGAR DE ENTREGA: -</td>
        <td>CONTACTO: <?= valueOption('EMPRESA_CONTACTO', '-')?></td>
    </tr>
    <tr>
        <td>FECHA DE ENTREGA: -</td>
        <td>CORREO: <?= valueOption('EMPRESA_CORREO', '-')?></td>
    </tr>
    <tr>
        <td>FORMA DE PAGO: <?= $cotizar->condicion_nombre ?></td>
        <td>N<sup>o</sup> CELULAR: <?= valueOption('EMPRESA_TELEFONO', '-')?></td>
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
        <th>% DTO</th>
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
            <td style="border: #111 0.5px solid; text-align: center;"><?= $detalle->precio ?></td>
            <td style="border: #111 0.5px solid; text-align: center;"></td>
            <td style="border: #111 0.5px solid; text-align: right;"><?= number_format($detalle->importe, 2) ?></td>
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
        <td style="border: #111 0.5px solid; text-align: right;"></td>
    </tr>
    <?php endfor; ?>
    <tr>
        <td colspan="5"></td>
        <th style="text-align: left;">SUBTOTAL</th>
        <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->subtotal ?></td>
    </tr>
    <tr>
        <td colspan="5"></td>
        <th style="text-align: left;">IMPUESTOS</th>
        <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->impuesto ?></td>
    </tr>
    <tr>
        <td colspan="5"></td>
        <th style="text-align: left;">DESCUENTOS</th>
        <td style="border: #111 0.5px solid; text-align: right;"></td>
    </tr>
    <tr>
        <td colspan="5"></td>
        <th style="text-align: left;">TOTAL</th>
        <td style="border: #111 0.5px solid; text-align: right;"><?= $cotizar->total ?></td>
    </tr>
    </tbody>
</table>
<br>
<div style="text-align: center;">
    CONDICIONES: <?= valueOption('COTIZACION_CONDICION', '')?>
</div>
<br>
<div style="text-align: center; font-size: 9px;">
   <?= valueOption('COTIZACION_PIE_PAGINA', 'fsfd')?>
</div>




