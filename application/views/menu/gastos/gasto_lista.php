<?php $md = get_moneda_defecto() ?>
<br>
<div class="row">
    <div class="col-md-10"></div>
    <div class="col-md-2">
        <label>Total: <?= $moneda->simbolo ?> <span
                    id="total_list"><?= number_format($gastos_totales->total, 2) ?></span></label>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered tableStyle" id="example">
        <thead>
        <tr>
            <th>ID</th>
            <th width="10%">Local</th>
            <th width="5%">Fecha</th>
            <th width="5%">Tipo de Gasto</th>
            <th width="5%">Documento</th>
            <th width="10%">Persona Afectada</th>
            <th width="15%">Descripci&oacute;n</th>
            <th width="5%">Subtotal</th>
            <th width="5%">Impuesto</th>
            <th width="10%">Total</th>
            <th width="5%">Usuario</th>
            <th width="5%">F. Registro</th>
            <th width="5%">Condici&oacute;n</th>
            <th width="5%">Estado</th>
            <th width="10%" class="desktop">Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($gastoss) > 0) {

            foreach ($gastoss as $gastos) {
                ?>
                <tr style="<?= $gastos['status_gastos'] == 0 ? 'color: #0000FF;' : '' ?>">

                    <td class="center"><?= $gastos['id_gastos'] ?></td>
                    <td style="white-space: normal;"><?= $gastos['local_nombre'] ?></td>
                    <td style="white-space: normal;">
                        <span style="display: none;"><?= date("YmdHis", strtotime($gastos['fecha'])) ?></span><?= date("d/m/Y", strtotime($gastos['fecha'])) ?>
                    </td>
                    <td style="white-space: normal;"><?= $gastos['nombre_tipos_gasto'] ?></td>
                    <td style="white-space: normal;"><?= $gastos['des_doc'] ?></td>
                    <td style="white-space: normal;"><?= $gastos['proveedor_id'] != NULL ? $gastos['proveedor_nombre'] : $gastos['trabajador'] ?></td>
                    <td style="white-space: normal;"><?= $gastos['descripcion'] ?></td>
                    <td style="white-space: normal;"><?= $gastos['simbolo'] . ' ' . number_format($gastos['subtotal'], 2) ?></td>
                    <td style="white-space: normal;"><?= $gastos['simbolo'] . ' ' . number_format($gastos['impuesto'], 2) ?></td>
                    <td style="white-space: normal;"><?= $gastos['simbolo'] . ' ' . number_format($gastos['total'], 2) ?></td>
                    <td style="white-space: normal;"><?= $gastos['responsable'] ?></td>
                    <td style="white-space: normal;"><?= date("d/m/Y", strtotime($gastos['fecha_registro'])) ?></td>
                    <td style="white-space: normal;"><?= $gastos['nombre_condiciones'] ?></td>
                    <td style="white-space: normal;"><?= $gastos['status_gastos'] == 1 ? 'Pendiente' : 'Confirmado' ?></td>
                    <td class="center" style="white-space: nowrap;">
                        <div class="btn-group">
                        <?php if ($gastos['status_gastos'] == 1): ?>
                            <?php if($gastos['nombre_tipos_gasto']!='PRESTAMO BANCARIO'){  ?>
                            <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                                                title="Editar" data-original-title="fa fa-comment-o"
                                                href="#" onclick="editar(' . $gastos['id_gastos'] . ');">'; ?>
                            <i class="fa fa-edit"></i>
                            </a>
                        <?php } ?>
                            <?php echo '<a class="btn btn-danger" data-toggle="tooltip"
                                 title="Eliminar" data-original-title="fa fa-comment-o"
                                 onclick="borrar(' . $gastos['id_gastos'] . ');">'; ?>
                            <i class="fa fa-trash-o"></i>
                            </a>
                        <?php endif; ?>
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
   class="btn  btn-danger btn-md" data-toggle="tooltip" title="Exportar a Pdf"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

<a id="exportar_excel" target="_blank"
   href="#"
   class="btn  btn-success btn-md" data-toggle="tooltip" title="Exportar a Excel"
   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<br><br>
<script>
    $(function () {
        TablesDatatables.init(2);

        $('#exportar_excel').on('click', function (e) {
            e.preventDefault();
            exportar_excel();
        });

        $('#exportar_pdf').on('click', function (e) {
            e.preventDefault();
            exportar_pdf();
        });
    });


    function exportar_pdf() {

        var data = {
            'local_id': $('#local_id').val(),
            'tipo_gasto': $('#tipo_gasto_id').val(),
            'mes': $('#mes').val(),
            'fecha': $('#date_range').val(),
            'persona_gasto': $("#persona_gasto_filter").val(),
            'proveedor': $("#proveedor_filter").val(),
            'usuario': $("#usuario_filter").val(),
            'moneda_id': $('#moneda_id').val(),
            'estado_id': $('#estado_id').val()
        };

        var win = window.open('<?= base_url()?>gastos/historial_pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'local_id': $('#local_id').val(),
            'tipo_gasto': $('#tipo_gasto_id').val(),
            'mes': $('#mes').val(),
            'fecha': $('#date_range').val(),
            'persona_gasto': $("#persona_gasto_filter").val(),
            'proveedor': $("#proveedor_filter").val(),
            'usuario': $("#usuario_filter").val(),
            'moneda_id': $('#moneda_id').val(),
            'estado_id': $('#estado_id').val()
        };

        var win = window.open('<?= base_url()?>gastos/historial_excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }
</script>