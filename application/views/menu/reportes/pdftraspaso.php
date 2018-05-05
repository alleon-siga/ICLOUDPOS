<style type="text/css">
    table{
        width: 100%;
        border-color: #111 1px solid;
    }
    thead , th{
        background: #585858;
        /* #e7e6e6*/
        border-color: #111 1px solid;
        color:#fff;
    }
    tbody tr{
        border-color: #111 1px solid;
    }
</style>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="8" >TRASPASOS DE ALMACEN</td>
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td width="18%">&nbsp;&nbsp;</td>
        <td width="10%" style="font-weight: bold;text-align: center;"></td>
        <td width="10%" style="text-align: center;"></td>
        <td width="5%" style="font-weight: bold; text-align: center;"></td>
        <td width="14%" style="text-align: center;"></td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="7%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("Y-m-d H:i:s");?></td>
    </tr>
    <tr>
        <td colspan="8" ></td>
    </tr>
</table>
<table>
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
            <td style="text-align: center"><span
                    style="display: none"><?= date('YmdHi', strtotime($arreglo->date)) ?></span><?= $arreglo->id ?></span></td>
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