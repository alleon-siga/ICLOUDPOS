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
        <td colspan="5"></td>
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
        <th>ID</th>
        <th>Nombre Prod.</th>
        <th>UM</th>
        <th>Cantidad</th>
        <th>Almacen Origen</th>
        <th>Almacen Destino</th>
        <th>Usuario</th>
        <th>Fecha</th>
        <th >Hora</th>
        <?php if($local=="TODOS"){?>
            <th>Local</th>
        <?php } ?>
    </tr>
    </thead>
    <tbody id="columnas">

    <?php

    foreach ($movimientos as $arreglo): ?>
        <tr>
            <td style="text-align: center"><span
                    style="display: none"><?= date('YmdHi', strtotime($arreglo->date)) ?></span><?= $arreglo->producto_id ?></span></td>
            <td style="text-align: center"><?= $arreglo->producto_nombre ?></td>
            <td style="text-align: center"><?= $arreglo->um ?></td>
            <td style="text-align: center"><?= $arreglo->cantidad ?></td>
            <td style="text-align: center"><?php  if($arreglo->operacion=="ENTRADA"){ echo $arreglo->localreferencia;} else{ echo $arreglo->localuno; }  ?></td>
            <td style="text-align: center"><?php  if($arreglo->operacion=="ENTRADA"){ echo $arreglo->localuno;} else{ echo $arreglo->localreferencia; }  ?> </td>
            <td style="text-align: center"><?= $arreglo->encargado ?></td>
            <td style="text-align: center"><?= date('d-m-Y', strtotime($arreglo->date)) ?></td>
            <td style="text-align: center"><?= date('H:i', strtotime($arreglo->date)) ?></td>
            <?php if($local=="TODOS"){?>
                <td style="text-align: center;"><?php echo $arreglo->localuno; ?></td>
            <?php } ?> </tr>
    <?php endforeach; ?>


    </tbody>
</table>