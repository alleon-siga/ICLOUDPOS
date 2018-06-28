<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=TraspasoDeAlmacen.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="9">TRASPASO DE ALMACEN
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
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
            <th>Id</th>
            <th>Tipo</th>
            <th>Nombre Prod.</th>
            <th>UM</th>
            <th>Cantidad</th>
            <th>Almacen Origen</th>
            <th>Almacen Destino</th>
            <th>Usuario</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody id="columnas">

    <?php

    foreach ($movimientos as $arreglo): ?>
        <tr>
            <td style="text-align: center"><?= $arreglo->id ?></span></td>
            <td style="text-align: center"><?= $arreglo->ref_id ?></td>
            <td style="text-align: center"><?= $arreglo->producto_nombre ?></td>
            <td style="text-align: center"><?= $arreglo->um ?></td>
            <td style="text-align: center"><?= $arreglo->cantidad ?></td>
            <td style="text-align: center"><?= $arreglo->origen ?></td>
            <td style="text-align: center"><?= $arreglo->destino ?> </td>
            <td style="text-align: center"><?= $arreglo->username ?></td>
            <td style="text-align: center"><?= date('d-m-Y H:i', strtotime($arreglo->fecha)) ?></td>
    <?php endforeach; ?>


    </tbody>
</table>