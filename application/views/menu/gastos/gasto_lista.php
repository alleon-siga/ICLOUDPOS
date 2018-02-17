<?php $md = get_moneda_defecto()?>
<br>
    <div class="row">
        <div class="col-md-10"></div>
        <div class="col-md-2">
            <label>Total: <?= $md->simbolo ?> <span id="total"><?=number_format($gastos_totales->total, 2)?></span></label>
        </div>
    </div>
<div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Local</th>
                <th>Fecha</th>
                <th>Tipo de Gasto</th>
                <th>Persona Afectada</th>
                <th>Descripci&oacute;n</th>
                <th>Moneda</th>
                <th>Total</th>
                <th>Usuario</th>
                <th>Fecha Registro</th>
                <th>Estado</th>

                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($gastoss) > 0) {

                foreach ($gastoss as $gastos) {
                    ?>
                    <tr style="<?= $gastos['status_gastos'] == 0 ? 'color: red;': ''?>">

                        <td class="center"><?= $gastos['id_gastos'] ?></td>
                        <td><?= $gastos['local_nombre'] ?></td>
                        <td><span style="display: none;"><?= date("YmdHis", strtotime($gastos['fecha'] ))?></span><?= date("d/m/Y", strtotime($gastos['fecha'] ))?></td>
                        <td><?= $gastos['nombre_tipos_gasto'] ?></td>
                        <td><?= $gastos['proveedor_id'] != NULL ? $gastos['proveedor_nombre'] : $gastos['trabajador'] ?></td>
                        <td><?= $gastos['descripcion'] ?></td>
                        <td><?= $gastos['moneda_nombre'] ?></td>
                        <td><?= number_format($gastos['total'], 2) ?></td>
                        <td><?= $gastos['responsable'] ?></td>
                        <td><?= date("d/m/Y", strtotime($gastos['fecha_registro'] ))?></td>
                        <td><?= $gastos['status_gastos'] == 1 ? 'Activado' : 'Eliminado' ?></td>

                        <td class="center">
                            <div class="btn-group">
                            <?php if ($gastos['status_gastos'] == 1):?>
                                <?php  echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $gastos['id_gastos'] . ');">'; ?>
                                <i class="fa fa-trash-o"></i>
                                </a>
                            <?php endif;?>
                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>

            </tbody>
        </table>
    </div>

    <a id="exportar_pdf" target="_blank"
   href="#"
   data-href="<?= base_url() ?>gastos/historial_pdf/"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

    <script>$(function () {
        TablesDatatables.init(2);
    });</script>