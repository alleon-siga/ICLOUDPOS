<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered table-condensed table-hover" id="tablaresultado">
        <thead>
        <tr>
            <th><?= getCodigoNombre() ?></th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Grupo</th>
            <th>Unidad</th>
            <th>Moneda</th>
            <th>Precio de venta</th>
            <th>Costo de compra</th>
            <th>Stock Actual</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php

        $total=0;
        foreach ($productos as $producto) {
            ?>
            <tr>
                <td><?= getCodigoValue($producto['producto_id'], $producto['producto_codigo_interno']) ?></td>
                <td><?= $producto['producto_nombre'] ?></td>
                <td><?= $producto['nombre_marca'] ?></td>
                <td><?= $producto['nombre_grupo'] ?></td>
                <td><?= $producto['nombre_unidad'] ?></td>
                <td><?= $moneda_nombre ?></td>
                <td><?php
                    if (isset($operacion)) {
                        $precio = $producto['precio'];
                        $string = ' $precio$operacion$tasa_soles ';
                        eval("\$string = \"$string\";");
                        eval("\$result = ($string);");
                        echo number_format($result, 2);
                    } else {
                        echo number_format($producto['precio'], 2);
                    }
                    ?>
                </td>
                <td>

                    <?php
                    if (isset($operacion)) {
                        $precio = $producto['costo'];
                        $string = ' $precio$operacion$tasa_soles ';
                        eval("\$string = \"$string\";");
                        eval("\$result = ($string);");

                        echo number_format($result, 2);
                    } else {
                        echo number_format($producto['costo'], 2);
                    }
                    ?>
                </td>
                <td><?=  number_format($producto['stock'],2) ?></td>
                <td><?php $subtotal = $producto['stock'] * $producto['costo'];


                    if (isset($operacion)) {
                        $precio = $subtotal;
                        $string = ' $precio$operacion$tasa_soles ';
                        eval("\$string = \"$string\";");
                        eval("\$result = ($string);");

                        echo number_format($result, 2);
                    } else {
                        echo number_format($subtotal, 2);
                    }

                    $total = $subtotal + $total;
                    ?></td>

            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-md-10">
        <a target="_blank" href="<?= $ruta; ?>inventario/pdf_valorizacion/<?php if (isset($local)) echo $local; else echo 0; ?>/<?php if (isset($marca)) echo $marca; else echo 0; ?>
/<?php if (isset($grupo)) echo $grupo; else echo 0; ?>/<?php if (isset($linea)) echo $linea; else echo 0; ?>
/<?php if (isset($familia)) echo $familia; else echo 0; ?>/<?php if (isset($moneda)) echo $moneda; else echo 0; ?>/<?php if (isset($usar)) echo $usar; else echo 0; ?>" class="btn  btn-default btn-lg" data-toggle="tooltip"
           title="Exportar a PDF"
           data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

        <a target="_blank" href="<?= $ruta; ?>inventario/excel_valorizacion/<?php if (isset($local)) echo $local; else echo 0; ?>/<?php if (isset($marca)) echo $marca; else echo 0; ?>
/<?php if (isset($grupo)) echo $grupo; else echo 0; ?>/<?php if (isset($linea)) echo $linea; else echo 0; ?>
/<?php if (isset($familia)) echo $familia; else echo 0; ?>/<?php if (isset($moneda)) echo $moneda; else echo 0; ?>/<?php if (isset($usar)) echo $usar; else echo 0; ?>" class="btn btn-default btn-lg" data-toggle="tooltip"
           title="Exportar a Excel"
           data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
    </div>
    <div class="col-md-2"><label>Total:</label> <?php
        $simbolo=$moneda_simbolo;
        if (isset($operacion)) {
            $precio = $total;
            $string = ' $precio$operacion$tasa_soles ';
            //   echo $string. "<br>";
            eval("\$string = \"$string\";");
            eval("\$string = \"$string\";");
            eval("\$result = ($string);");

            echo $simbolo." ".number_format($result, 2);
        } else {
            echo $simbolo." ".number_format($total, 2);
        } ?></div>
</div>






    <div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
    </div>

    <script type="text/javascript">
        $(function () {

            TablesDatatables.init();

        });
    </script>