<?php $ruta = base_url(); ?>

<table class="table table-striped dataTable table-bordered no-footer" id="tablaresultado">
    <thead>
    <tr>
        <th><?php echo getCodigoNombre() ?></th>
        <th>Nombre </th>
        <th>Stock M&iacute;nimo </th>
        <th>Existencia</th>
        <th>Fracci&oacute;n</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($inventarios) > 0) {
        foreach ($inventarios as $inventario) {
            ?>
            <tr>
                <td class="center"><?php echo getCodigoValue(sumCod($inventario->producto_id), $inventario->producto_codigo_interno) ?></td>
                <td class="center"><?= $inventario->producto_nombre ?></td>
                <td class="center"><?= $inventario->producto_stockminimo ?></td>
                <td><?= $inventario->cantidad ?></td>
                    <td><?= $inventario->fraccion ?></td>

            </tr>
        <?php }
    } ?>
    </tbody>
</table>




<div class="btn-group">
    <a href="<?= $ruta?>inventario/pdf/<?= $tipo_reporte ?>/<?= $local ?>" id=""
       value="<?= $ruta?>inventario/pdf/<?= $tipo_reporte?>/<?= $local ?>"
       class="btn  btn-default btn-lg pdf" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
    <a href="<?= $ruta?>inventario/excel/<?= $tipo_reporte?>/<?= $local ?>"
       value="<?= $ruta?>inventario/excel/<?= $tipo_reporte?>/<?= $local ?>" class="btn btn-default btn-lg excel"
       data-toggle="tooltip" title="Exportar a Excel"
       data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
</div>

<div class="modal fades" id="verajuste" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<script type="text/javascript">
$(document).ready(function () {
TablesDatatables.init();

});
</script>
