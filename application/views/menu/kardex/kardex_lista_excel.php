<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=kardex.xls");
header("Content-Language: es");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table>
    <thead>
    <tr>
        <td colspan="10">FORMATO 12.1: "REGISTRO DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS- DETALLE DEL INVENTARIO PERMANENTE EN UNIDADES FÍSICAS</td>
    </tr>
    <tr>
        <td colspan="4">PERÍODO:</td>
        <td colspan="6"><?=getMes($mes)?> <?=$year?></td>
    </tr>
    <tr>
        <td colspan="4">EMPRESA:</td>
        <td colspan="6"><?=valueOption('EMPRESA_NOMBRE')?></td>
    </tr>
    <tr>
        <td colspan="4">ESTABLECIMIENTO:</td>
        <td colspan="6"><?=$local->local_nombre?></td>
    </tr>
    <tr>
        <td colspan="4">CÓDIGO DE LA EXISTENCIA:</td>
        <td colspan="6">CODIGO INTERNO DEL PRODUCTO</td>
    </tr>
    <tr>
        <td colspan="4">TIPO:</td>
        <td colspan="6">MERCADERIA</td>
    </tr>
    <tr>
        <td colspan="4">DESCRIPCIÓN::</td>
        <td colspan="6"><?=getCodigoValue(sumCod($producto->producto_id), $producto->producto_codigo_interno)." - ".$producto->producto_nombre?></td>
    </tr>
    <tr>
        <td colspan="4">CÓDIGO DE LA UNIDAD DE MEDIDA:</td>
        <td colspan="6"><?=$unidad?></td>
    </tr>
    <tr>
        <th colspan="10" style="text-align: center;">DOCUMENTO DE TRASLADO, COMPROBANTE DE PAGO, DOCUMENTO INTERNO O SIMILAR</th>
    </tr>
    <tr>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Serie</th>
        <th>Numero</th>
        <th>Tipo de Operacion</th>
        <th>Responsable</th>
        <th>Referencia</th>
        <th>Entradas</th>
        <th>Salidas</th>
        <th>Saldo Final</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($kardex as $k): ?>
        <tr>
            <td><?= date('d/m/Y H:i:s', strtotime($k->fecha)) ?></td>
            <?php $tipo = get_tipo_doc($k->tipo) ?>
            <td><?= $tipo['value'] ?></td>
            <td><?= $k->serie ?></td>
            <td><?= $k->numero ?></td>
            <?php $operacion = get_tipo_operacion($k->operacion) ?>
            <td><?= $operacion['value'] ?></td>
            <td><?= $k->nombre ?></td>
            <td style="white-space: normal;"><?= $k->ref_val ?></td>
            <?php if ($k->io == 1): ?>
                <td style="text-align: right;"><?= $k->cantidad ?></td>
                <td style="text-align: right;"></td>
            <?php elseif ($k->io == 2): ?>
                <td style="text-align: right;"></td>
                <td style="text-align: right;"><?= $k->cantidad ?></td>
            <?php endif; ?>
            <td style="text-align: right;"><?= $k->cantidad_saldo ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
