<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=MovimientoInventario.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="10">REPORTE MOVIMIENTO DE INVENTARIO
        </td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>

    <tr>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>

        <td style="font-weight: bold;">Fecha Emision:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table border="1">
    <thead>
    <tr>

        <th>Fecha y Hora</th>
        <th>Movimiento</th>
        <th>Tipo</th>
        <th>N&uacute;mero</th>
        <th>Referencia</th>
        <th>Encargado</th>
        <th>UM</th>
        <th>Entrada</th>
        <th>Salida</th>
        <?php if ($local == "TODOS"): ?>
            <th>Local</th>
        <?php endif; ?>
        <th>Importe</th>
    </tr>
    </thead>
    <tbody id="columnas">

    <?php

    foreach ($movimientos as $arreglo): ?>
        <tr>
            <td style="text-align: center"><?= date('d-m-Y H:i', strtotime($arreglo->date)) ?></td>
            <td style="text-align: center"><?= $arreglo->operacion ?></td>
            <td style="text-align: center"><?= $arreglo->tipo ?></td>
            <td style="text-align: center"><?= $arreglo->numero ?></td>
            <td style="text-align: center"><?php
                if ($arreglo->tipo == "TRASPASO") {

                    if ($arreglo->operacion == "SALIDA") {

                        echo "Local de origen: " . $arreglo->localuno . ", local de destino: " . $arreglo->localreferencia;
                    } else {

                        echo "Local de origen: " . $arreglo->localreferencia . ", local de destino: " . $arreglo->localuno;
                    }

                } else {
                    echo $arreglo->referencia;
                }
                ?></td>
            <td style="text-align: center"><?= $arreglo->encargado ?></td>
            <td style="text-align: center"><?= $arreglo->um ?></td>
            <?php if ($arreglo->operacion == "ENTRADA"): ?>
                <td style="text-align: center"><?= $arreglo->cantidad ?></td>
                <td style="text-align: center"></td>
            <?php elseif ($arreglo->operacion == "SALIDA"): ?>
                <td></td>
                <td style="text-align: center"><?= $arreglo->cantidad ?></td>
            <?php endif; ?>
            <?php if ($local == "TODOS"): ?>
                <td style="text-align: center"><?= $arreglo->local_nombre ?></td>
            <?php endif; ?>
            <td style="text-align: right"><?= $arreglo->importe != "" ? $arreglo->simbolo . " " . number_format($arreglo->importe, 2) : $arreglo->importe ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>