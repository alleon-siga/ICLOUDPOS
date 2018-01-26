<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered table-condensed table-hover" id="tablaresultado">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>C&oacute;digo Producto</th>
            <th>Productos</th>
            <th>Cantidad Ingresada</th>
            <th>Precio Compra</th>
            <th>Cantidad Salida</th>
            <th>Precio Salida</th>
            <th>Stock Final</th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach($venta as $iventa) {
            if ($iventa['tipo_operacion'] == 'ENTRADA') {

                ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($iventa['date'])); ?></td>
                    <td><?php echo $iventa['producto_id']; ?></td>
                    <td><?php echo $iventa['producto_nombre']; ?></td>
                    <td><?php echo $iventa['cantidad']; ?></td>
                    <td>Precio Entrada</td>
                    <td></td>
                    <td></td>
                    <td>Stock Entrada</td>


                </tr>
                <?php
            } else {

                ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($iventa['date'])); ?></td>
                    <td><?php echo $iventa['producto_id']; ?></td>
                    <td><?php echo $iventa['producto_nombre']; ?></td>
                    <td></td>
                    <td></td>
                    <td><?php echo $iventa['cantidad']; ?></td>
                    <td>Precio Salida</td>
                    <td>Stock Salida</td>


                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>

<a href="<?= $ruta; ?>ingresosYsalidas/pdf/<?php if (isset($id_producto)) echo $id_producto; else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde; else echo 0; ?>
 /<?php if (isset($fecha_hasta)) echo $fecha_hasta; else echo 0; ?> "
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta; ?>ingresosYsalidas/excel/<?php if (isset($id_producto)) echo $id_producto; else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde; else echo 0; ?>
 /<?php if (isset($fecha_hasta)) echo $fecha_hasta; else echo 0; ?> / <?php if (isset($estatus)) echo $estatus; else echo 0; ?>/0"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

<script type="text/javascript">
    $(function () {

        TablesDatatables.init();

    });
</script>