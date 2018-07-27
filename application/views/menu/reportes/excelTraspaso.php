<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=TraspasoDeAlmacen.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="6">TRASPASO DE ALMACEN
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="font-weight: bold;">Fecha Emision:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
</table>
<table border="1">
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
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>