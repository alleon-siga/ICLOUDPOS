<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=stock.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">STOCK DE PRODUCTOS</h4>
<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?php echo empty($local["direccion"])? 'TODOS' : $local["direccion"] ?></h5>
<h5 style="margin: 0;">UBICACION: <?php echo empty($local["local_nombre"])? 'TODOS' : $local["local_nombre"] ?></h5>
<table cellpadding="5">
    <thead>
        <tr>
            <?php if (canShowCodigo()): ?>
                <th><?php echo utf8_decode(getCodigoNombre()) ?></th>
            <?php endif; ?>
            <?php foreach ($columnas as $col): ?>
                <?php
                if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                    echo " <th>" . utf8_decode($col->nombre_mostrar) . "</th>";
                }

                ?>
            <?php endforeach; ?>
            <th>UM</th>
            <th>Cantidad</th>
        <?php if($unidadMinima==0){ ?>
            <th>Fracci&oacute;n</th>
        <?php } ?>
            <th>Estado</th>
            <?php if($local_selected == false && $detalle_checked == 1):?>
                <th>Ubicaci&oacute;n</th>
            <?php endif;?>
        </tr>
    </thead>
    <tbody id="tbody">

    <?php foreach ($lstProducto as $pd):

        ?>

        <tr id="<?= $pd['producto_id'] ?>">
            <?php if (canShowCodigo()): ?>
                <td><?php echo getCodigoValue(sumCod($pd['producto_id']), $pd['producto_codigo_interno']) ?></td>
            <?php endif; ?>
            <?php foreach ($columnas as $col): ?>
                <?php if (array_key_exists($col->nombre_columna, $pd) and $col->mostrar == TRUE) {
                    if ($col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                        echo "<td>";
                        if ($col->nombre_columna == 'producto_vencimiento')
                            echo $pd[$col->nombre_join] != null ? date('d-m-Y', strtotime($pd[$col->nombre_join])) : '';
                        else
                            echo $pd[$col->nombre_join];
                        echo "</td>";
                    }
                } ?>
            <?php endforeach; ?>
            <td>
        <?php if($unidadMinima == 0){ ?>
            <?php echo $pd['nombre_unidad']; ?>
        <?php }else{ ?>
            <?php echo !empty($pd['nombre_fraccion'])? $pd['nombre_fraccion'] : $pd['nombre_unidad']; ?>
        <?php } ?>
            </td>
            <td id="cantidad_prod_<?php echo $pd['producto_id'] ?>">
        <?php if($unidadMinima == 0){ ?>
            <?php echo $pd['cantidad']; ?>
        <?php }else{ ?>
            <?php echo ($pd['unidades'] * $pd['cantidad']) + $pd['fraccion']; ?>
        <?php } ?>
            </td>
            <?php if($unidadMinima == 0){ ?>
            <td>
                <?php if ($pd['fraccion'] != null) {
                    echo $pd['fraccion'];
                    if ($pd['nombre_fraccion'] != "") {
                        echo " " . $pd['nombre_fraccion'];
                    }
                } ?>
            </td>
            <?php } ?>
            <td>
                <?php if ($pd['producto_estado'] == 0) {
                    echo "INACTIVO";
                } else {
                    echo "ACTIVO";
                } ?>

            </td>
            <?php if($local_selected == false && $detalle_checked == 1):?>
                <td><?=$pd['local_nombre']?></td>
            <?php endif;?>

        </tr>

    <?php endforeach; ?>


    </tbody>
</table>
