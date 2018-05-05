<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=TraspasoDeAlmacen.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="8">TRASPASO DE ALMACEN
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
            <th>Nombre Prod.</th>
            <th>UM</th>
            <th>Cantidad</th>
            <th>Almacen Origen</th>
            <th>Almacen Destino</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <?php //if($local=="TODOS"){?>
                <!--<th>Local</th>-->
            <?php //} ?>
        </tr>
    </thead>
    <tbody id="columnas">

    <?php

    foreach ($movimientos as $arreglo): ?>
        <tr>
            <td style="text-align: center"><?= $arreglo->id ?></span></td>
            <td style="text-align: center"><?= $arreglo->producto_nombre ?></td>
            <td style="text-align: center"><?= $arreglo->um ?></td>
            <td style="text-align: center"><?= $arreglo->cantidad ?></td>
            <td style="text-align: center"><?php  if($arreglo->io=="2"){ echo $arreglo->local_nombre; }else{ echo $arreglo->ref_val; } ?></td>
            <td style="text-align: center"><?php  if($arreglo->io=="1"){ echo $arreglo->local_nombre; }else{ echo $arreglo->ref_val; } ?> </td>
            <td style="text-align: center"><?= $arreglo->nombre ?></td>
            <td style="text-align: center"><?= date('d-m-Y H:i', strtotime($arreglo->fecha)) ?></td>
            <?php //if($local=="TODOS"){?>
                <!--<td style="text-align: center;"><?php //echo $arreglo->localuno; ?></td>-->
            <?php //} ?> </tr>
    <?php endforeach; ?>


    </tbody>
</table>