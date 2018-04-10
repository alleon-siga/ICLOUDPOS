<?php $ruta = base_url(); ?>
<?php $term = diccionarioTermino() ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Clientes</li>
    <li><a href="">Agregar y editar Clientes</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->


    <a class="btn btn-primary" onclick="agregar();">
        <i class="fa fa-plus "> Nuevo</i>
    </a>
    <br>

    <div class="table-responsive">
        <table class="table table-striped dataTable" id="example">
            <thead>
            <tr>
                <th style="text-align: center">ID</th>
                <th style="text-align: center">Tipo</th>
                <th style="text-align: center"><?= $term[0]->valor.' / '.$term[1]->valor ?></th>
                <th style="text-align: center">Raz&oacute;n Social o Nombre</th>
                <th style="text-align: center">Direccion</th>
                <th style="text-align: center">Teléfono</th>
                <th style="text-align: center">correo</th>
                <th class="desktop">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($clientes) > 0) {
                foreach ($clientes as $cliente) {
                    ?>
                    <tr>
                        <td class="center"><?= $cliente['id_cliente'] ?></td>
                        <td><?= $cliente['tipo_cliente'] == '1' ? 'Empresa' : 'Persona'?></td>
                        <td><?= $cliente['ruc'] == '2' ? $term[1]->valor.':' : $term[0]->valor.':'?> <?= $cliente['identificacion'] ?></td>
                        <td><?= $cliente['razon_social'] ?></td>                        
                        <td><?= $cliente['direccion'] ?></td>
                        <td><?= $cliente['telefono1'] ?></td>
                        <td><?= $cliente['email'] ?></td>
                        <!--  <td><?php //if($cliente['categoria_precio']!=null){ echo  $cliente['nombre_precio']; }?></td> -->
                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Editar" data-original-title="fa fa-comment-o"
                                            href="#" onclick="editar(' . $cliente['id_cliente'] . ');">'; ?>
                                <i class="fa fa-edit"></i>
                                </a>
                                <?php if($cliente['razon_social']!='Cliente Frecuente') {
                                    ?>
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Eliminar" data-original-title="fa fa-comment-o"
                                       onclick="borrar(<?= $cliente['id_cliente'] ?>,'<?= $cliente['razon_social'] ?>')";
                                        >
                                        <i class="fa fa-trash"></i>
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
    <br>
    <a href="<?= $ruta ?>cliente/pdf" class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
    <a href="<?= $ruta ?>cliente/excel" class="btn btn-default btn-lg" data-toggle="tooltip"
       title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
            class="fa fa-file-excel-o fa-fw"></i></a>
</div>




<script src="<?php echo $ruta; ?>recursos/js/Validacion.js?<?php echo date("Hms"); ?>"></script>
<script type="text/javascript">
    function borrar(id, nom) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
        $("#identificacion_borrar").attr('value', identificacion);
    }


    function editar(id) {
        $('#load_div').show()

        $("#agregar").load('<?= $ruta ?>cliente/form/' + id);
        $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
        setTimeout(function () {
                    //$(".alert-danger").css('display','none');
            $('#load_div').hide()
            }, 500)


    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>cliente/form');
        $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});

    }


    var cliente = {
        ajaxgrupo: function () {

            return $.ajax({
                url: '<?= base_url()?>cliente'

            })
        },
        guardar: function () {
            if ($("#razon_social").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la raz&oacute;n social</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#identificacion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la identificaci&oacute;n</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#grupo_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el cliente</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#id_pais").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el pais</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#estado_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el estado</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#ciudad_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la ciudad</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if (isNaN($("#identificacion").val())) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar solo numeros</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#identificacion").focus();
                $(this).prop('disabled', true);

                return false;
            }

            App.formSubmitAjax($("#formagregar").attr('action'), this.ajaxgrupo, 'agregar', 'formagregar');

        }


    }
    function eliminar() {

        App.formSubmitAjax($("#formeliminar").attr('action'), cliente.ajaxgrupo, 'borrar', 'formeliminar');
    }
</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>cliente/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Cliente</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el Cliente seleccionado?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <input type="hidden" name="nombre" id="nom_borrar">
                    <input type="hidden" name="identificacion" id="identificacion_borrar">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function () {
        TablesDatatables.init();
    });</script>