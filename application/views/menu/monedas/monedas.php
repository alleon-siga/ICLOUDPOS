<?php $ruta = base_url(); ?>

<style>
    table tr {
        text-align: center;
    }
</style>
<ul class="breadcrumb breadcrumb-top">
    <li>Monedas</li>
    <li><a href="">Agregar y editar Monedas</a></li>
</ul>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">
    <!-- Progress Bars Wizard Title -->


    <a class="btn btn-primary" onclick="agregar();" style="display: none;">
        <i class="fa fa-plus "> Nueva</i>
    </a>
    <br>

    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Nombre</th>
                <th>Simbolo</th>
                <th>Pais</th>
                <th>Tasa <?= $moneda_defecto->nombre ?></th>
                <th>Estado</th>

                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($monedas) > 0) {

                foreach ($monedas as $moneda) {
                    ?>
                    <tr>

                        <td class="center"><?= $moneda->id_moneda ?></td>
                        <td><?= $moneda->nombre ?></td>
                        <td><?= $moneda->simbolo ?></td>
                        <td><?= $moneda->pais ?></td>
                        <td><?= $moneda->id_moneda != MONEDA_DEFECTO ? $moneda_defecto->simbolo . ' ' . $moneda->tasa_soles : '-' ?></td>
                        <td><?= $moneda->status_moneda == 1 ? 'ACTIVA' : 'INACTIVA' ?></td>


                        <td class="center">
                            <div class="btn-group">
                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Editar" data-original-title="fa fa-comment-o"
                                   href="#" onclick="editar(<?= $moneda->id_moneda ?>);">

                                    <i class="fa fa-edit"></i>
                                </a>

                                <?php
                                if ($moneda->id_moneda != MONEDA_DEFECTO && FALSE) {
                                    echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $moneda->id_moneda . ',\'' . $moneda->nombre . '\');">';

                                    ?>
                                    <i class="fa fa-trash-o"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>

            </tbody>
        </table>
    </div>
</div>


<!-- Modales for Messages -->
<div class="modal hide" id="mOK">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">
        </button>
        <h3>Notificaci&oacute;n</h3>
    </div>
    <div class="modal-body">
        <p>Registro Exitosa</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-dismiss="modal"
           onclick="javascript:window.location.reload();">Close</a>
    </div>
</div>

</div>
</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

    function borrar(id, nom) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }


    function editar(id) {

        $("#agregar").load('<?= $ruta ?>monedas/form/' + id);
        $('#agregar').modal('show');
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>monedas/form');
        $('#agregar').modal('show');
    }

    var marca = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>monedas'

            })
        },
        guardar: function () {
            if ($("#nombre_moneda").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el nombre de moneda</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            if ($("#simbolo").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el simbolo de la moneda</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            if ($("#tasa_soles").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la tasa de soles</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            if ($("#pais").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el pais procedente de la moneda</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            if ($("#operacion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la operacion de la conversion</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            App.formSubmitAjax($("#formagregar").attr('action'), this.ajaxgrupo, 'agregar', 'formagregar');
        }
    }
    function eliminar() {

        App.formSubmitAjax($("#formeliminar").attr('action'), marca.ajaxgrupo, 'borrar', 'formeliminar');

    }
</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>monedas/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar M&eacute;todo de Pago</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la moneda seleccionada?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <input type="hidden" name="nombre" id="nom_borrar">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>
<!-- /.modal-dialog -->
</div>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function () {
        TablesDatatables.init(0, 'asc');
    });</script>