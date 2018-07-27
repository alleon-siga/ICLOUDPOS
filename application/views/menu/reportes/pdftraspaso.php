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
            <th><?php echo getCodigoNombre() ?></th>
            <th>Tipo</th>
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
            <td style="text-align: center"><?= $arreglo->id ?></td>
            <td style="text-align: center"><?= $arreglo->ref_id; ?></td>
            <td>
            <?php 
                if(count($origen)>0){
                    foreach($origen as $row){
                        $arr = array();
                        foreach ($row as $value) {
                            if($value->traspaso_id == $arreglo->id){
                                $arr[] = $value->origen;
                            }
                        }
                        echo implode(" / ", $arr);
                    }
                } 
            ?>        
            </td>
            <td style="text-align: center"><?= $arreglo->destino; ?></td>
            <td style="text-align: center"><?= $arreglo->username ?></td>
            <td style="text-align: center"><?= date('d-m-Y H:i', strtotime($arreglo->fecha)) ?></td>
            <td style="text-align: center">
                <a class="btn btn-default" data-toggle="tooltip" style="margin-right: 5px;" title="Ver" data-original-title="Ver" href="#" onclick="ver('<?= $arreglo->id ?>');"><i class="fa fa-search"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>