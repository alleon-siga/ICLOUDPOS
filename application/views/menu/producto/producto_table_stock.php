<?php
/**
 * Created by PhpStorm.
 * User: Jhainey
 * Date: 09/07/2015
 * Time: 15:01
 */
?>
<table class='table table-striped dataTable table-bordered' id="table">
    <thead>
    <tr>
        <?php if (canShowCodigo()): ?>
            <th><?php echo getCodigoNombre() ?></th>
        <?php endif; ?>
        <?php foreach ($columnas as $col): ?>
            <?php
            if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                echo " <th>" . $col->nombre_mostrar . "</th>";
            }

            ?>
        <?php endforeach; ?>
        <th>UM</th>
        <th>Cantidad</th>
        <th>Fracci&oacute;n</th>
        <th>Estado</th>


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
                <?php echo $pd['nombre_unidad']; ?>

            </td>
            <td id="cantidad_prod_<?php echo $pd['producto_id']?>">
                <?php echo $pd['cantidad']; ?>

            </td>
            <td id="fraccion_prod_<?php echo $pd['producto_id']?>">
                <?php if ($pd['fraccion'] != null) {
                    echo $pd['fraccion'];
                    if ($pd['nombre_fraccion'] != "") {
                        echo " " . $pd['nombre_fraccion'];
                    }
                } ?>

            </td>

            <td>
                <?php if ($pd['producto_estado'] == 0) {
                    echo "INACTIVO";
                } else {
                    echo "ACTIVO";
                } ?>

            </td>


        </tr>

    <?php endforeach; ?>


    </tbody>
</table>


<script>
    $(function () {
        TablesDatatables.init();
        var t = setTimeout(function () { $(window).resize(); }, 400);

        $("#tbody").selectable({
            stop: function () {

                var id = $("#tbody tr.ui-selected").attr('id');


            }
        });
    });
</script>
