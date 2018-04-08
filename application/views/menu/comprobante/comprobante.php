<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Marcas</li>
    <li><a href="">Agregar y editar Comprobantes</a></li>
</ul>

<div class="block">
    <a class="btn btn-primary" onclick="nuevo();">
        <i class="fa fa-plus "> Nuevo Comprobante</i>
    </a>
    <br>
    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Nombre</th>
                <th>Serie</th>
                <th>Desde</th>
                <th>Hasta</th>
                <th>Longitud</th>
                <th>Actual</th>
                <th>Estado</th>
                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($comprobantes) > 0): ?>
                <?php foreach ($comprobantes as $comprobante): ?>
                    <tr>
                        <td><?= $comprobante->id ?></td>
                        <td><?= $comprobante->nombre ?></td>
                        <td><?= $comprobante->serie ?></td>
                        <td><?= $comprobante->desde ?></td>
                        <td><?= $comprobante->hasta ?></td>
                        <td><?= $comprobante->longitud ?></td>
                        <td><?= $comprobante->num_actual ?></td>
                        <td><?= $comprobante->estado == 1 ? 'Activo' : 'Inactivo' ?></td>
                        <td>
                            <div class="btn-group">
                                <a
                                        class="btn btn-default"
                                        data-toggle="tooltip"
                                        title="Editar"
                                        href="#"
                                        onclick="editar(<?= $comprobante->id ?>);">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a
                                        class="btn btn-danger"
                                        data-toggle="tooltip"
                                        title="Eliminar"
                                        href="#"
                                        onclick="eliminar(<?= $comprobante->id ?>);">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </div>

                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script type="text/javascript">

    $(function () {
        TablesDatatables.init();
    });

    function editar(id) {
        $("#form_modal").load('<?= $ruta ?>comprobante/form/' + id, function () {
            $('#form_modal').modal('show');
        });
    }

    function nuevo() {

        $("#form_modal").load('<?= $ruta ?>comprobante/form', function () {
            $('#form_modal').modal('show');
        });
    }


    function eliminar(id) {
        if (!window.confirm('Estas seguro de eliminar este registro'))
            return false;

        $.ajax({
            url: "<?= $ruta ?>comprobante/eliminar",
            type: "POST",
            dataType: "json",
            data: {'id': id},
            success: function (data) {

            }
        });
    }
</script>