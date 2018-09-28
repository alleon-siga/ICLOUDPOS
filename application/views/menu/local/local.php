<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Locales</li>
    <li><a href="">Agregar y editar Local</a></li>
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


    <a class="btn btn-default" onclick="agregar();">
        <i class="fa fa-plus "> Nuevo Local</i>
    </a>
    <br>

    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th width="10%">Nombre</th>
                <th width="5%">Estado</th>
                <th width="20%">Principal</th>
                <th width="20%">Localizaci&oacute;n</th>
                <th width="20%">Direcci&oacute;n</th>
                <th width="10%">Telef&oacute;no</th>
                <th class="desktop" width="15%">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($locales) > 0) {

                foreach ($locales as $local) {
                    ?>
                    <tr id=<?= $local->int_local_id ?>>

                        <td class="center" style="white-space: normal;"><?= $local->int_local_id ?></td>
                        <td style="white-space: normal;"><?= $local->local_nombre ?></td>
                        <td style="white-space: normal;"><?= $local->local_status == 1 ? 'Activado' : 'Desactivado' ?></td>
                        <td style="white-space: normal;"><?= $local->principal == 1 ? 'SI' : 'NO' ?></td>
                        <td style="white-space: normal;"><?= $local->distrito_id != null ? $local->pais . ' / ' . $local->estado . ' / ' . $local->ciudad . ' / ' . $local->distrito : 'Sin Localizaci&oacute;n' ?></td>
                        <td style="white-space: normal;"><?= ($local->direccion != null)? $local->direccion : 'Sin Direcci&oacute;n' ?></td>
                        <td style="white-space: normal;"><?= $local->telefono ?></td>
                        <td class="center" style="white-space: normal;">
                            <div class="btn-group acciones_<?= $local->int_local_id ?>">
                                <a href="#" style="margin-right: 5px;"
                                   class="btn btn-primary"
                                   data-toggle="tooltip"
                                   title="Correlativos"
                                   data-original-title="fa fa-comment-o"
                                   onclick="correlativos('<?= $local->int_local_id ?>',  '<?= $local->local_nombre ?>')">
                                    <i class="fa fa-list-ol"></i></a>
                                <a href="#" style="margin-right: 5px;"
                                   class="btn btn-default"
                                   data-toggle="tooltip"
                                   title="Editar"
                                   data-original-title="fa fa-comment-o"
                                   onclick="editar('<?= $local->int_local_id ?>')">
                                    <i class="fa fa-edit"></i></a>
                                <a href="#"
                                   class="btn btn-danger eliminar_<?= $local->int_local_id ?>"
                                   data-toggle="tooltip"
                                   title="Eliminar"
                                   data-original-title="fa fa-comment-o"
                                   onclick="borrar('<?= $local->int_local_id ?>',  '<?= $local->local_nombre ?>');">
                                    <i class="fa fa-trash-o"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="dialog_correlativo" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

    <!-- TERMINAR VENTA CONTADO -->

    <?php echo isset($dialog_correlativo) ? $dialog_correlativo : '' ?>

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

    function correlativos(local_id, local_nombre) {

        $.ajax({
            url: '<?= $ruta ?>local/get_correlativos/' + local_id,
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {

                $("#local_nombre_almacen").html(local_nombre);
                $("#local_nombre_almacen").attr('local-id', local_id);

                $('.docs').each(function () {
                    var id = $(this).attr('data-id');

                    $("#serie_" + id).val('001');
                    $("#next_" + id).val(1);

                    if (id == 6) {
                        $("#next_" + id).attr('readonly', 'readonly');
                    }
                });


                for (var i = 0; i < data.correlativos.length; i++) {
                    $("#serie_" + data.correlativos[i].id_documento).val(data.correlativos[i].serie);
                    $("#next_" + data.correlativos[i].id_documento).val(data.correlativos[i].correlativo);

                }

                $("#dialog_correlativo").modal('show');
            }
        });


    }

    function save_correlativos() {
        var correlativos = [];
        $('.docs').each(function () {
            var id = $(this).attr('data-id');
            var correlativo = {};

            correlativo.id_local = $("#local_nombre_almacen").attr('local-id');
            correlativo.id_documento = id;
            correlativo.serie = $("#serie_" + id).val();
            correlativo.correlativo = $("#next_" + id).val();

            correlativos.push(correlativo);
        });

        for (var i = 0; i < correlativos.length; i++) {
            if(correlativos[i].serie.length != 3){
                show_msg('warning', 'La series tiene que tener 3 caracteres alfanumericos');
                return false;
            }

            if(!Number.isInteger(parseFloat(correlativos[i].correlativo))){
                show_msg('warning', 'Los correlativos tienen que ser numerico');
                return false;
            }

            if(parseFloat(correlativos[i].correlativo) <= 0){
                show_msg('warning', 'Los correlativos no pueden ser negativos');
                return false;
            }

            if(correlativos[i].correlativo.length > 8){
                show_msg('warning', 'Los correlativos no pueden ser mayor que 8 caracteres numericos');
                return false;
            }
        }

        $('#load_div').show()

        console.log(JSON.stringify(correlativos))

        $.ajax({
            url: '<?= $ruta ?>local/save_correlativos/' + $("#local_nombre_almacen").attr('local-id'),
            type: 'POST',
            data: {'correlativos': JSON.stringify(correlativos)},
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data.success == '1') {
                    $.bootstrapGrowl('<h4>Correlativos Guardados</h4>', {
                        type: 'success',
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $("#dialog_correlativo").modal('hide');
                }
                else {
                    $.bootstrapGrowl('<h4>Ocurrio un Problema al Guardar</h4>', {
                        type: 'danger',
                        delay: 2500,
                        allow_dismiss: true
                    });
                }
                setTimeout(function () {
                    $('#load_div').hide()
                }, 2000)

            }
        });
    }

    function borrar(id, nom) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }


    function editar(id) {

        $("#agregar").load('<?= $ruta ?>local/form/' + id);
        $('#agregar').modal('show');
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>local/form');
        $('#agregar').modal('show');
    }

    var grupo = {

        ajaxgrupo: function () {
            $('#load_div').show()
            return $.ajax({
                url: '<?= base_url()?>local'

            })

            setTimeout(function () {
                $('#load_div').hide()
            }, 2000)

        },
        guardar: function () {
            if ($("#local_nombre").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);


                return false;
            }
            App.formSubmitAjax($("#formagregar").attr('action'), this.ajaxgrupo, 'agregar', 'formagregar');
            setTimeout(function () {
                $('#load_div').hide()
            }, 2000)
        }
    }

    function eliminar() {


        $('#load_div').show()
        $.ajax({
            url: "<?= $ruta ?>local/eliminar",
            type: "POST",
            dataType: "json",
            data: {'id': $("#id_borrar").val(), 'nombre': $("#nom_borrar").val()},
            success: function (data) {
                setTimeout(function () {
                    $('#load_div').hide()
                }, 2000)
                $('#borrar').modal('toggle')
                if (data != '') {


                    $.bootstrapGrowl('<h4>' + data[Object.keys(data)] + '</h4>', {
                        type: Object.keys(data),
                        delay: 2500,
                        allow_dismiss: true
                    });
                    if (Object.keys(data) == 'success') {
                        $('.eliminar_' + $("#id_borrar").val()).remove()
                        $('.acciones_' + $("#id_borrar").val()).append('<a onclick="activar_local(' + $("#id_borrar").val() + ');" href="#" data-original-title="Activar" title="Activar" data-toggle="tooltip" class="btn btn-default btn-default btn-default activar_' + $("#id_borrar").val() + '"><i class="fa fa-edit"></i></a>')

                    } else {
                        return false

                    }


                }
            }
        });

    }
</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>local/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Local</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el Local seleccionado?</p>
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
        TablesDatatables.init();
    });</script>