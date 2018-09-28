<?php $ruta = base_url(); ?>
<?php $term = diccionarioTermino() ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Clientes</li>
    <li><a href="">Agregar y editar Clientes</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="row">
        <div class="col-md-7">
            <a class="btn btn-primary" onclick="agregar();">
                <i class="fa fa-plus "> Nuevo</i>
            </a>
        </div>
        <div class="col-md-5 text-right">
            <a class="btn btn-warning" href="<?= $ruta ?>recursos/plantillas_datos/cliente.csv">
                <i class="fa fa-download"></i> Plantilla
            </a>
            <a class="btn btn-default" onclick="importar()">
                <i class="fa fa-download"></i> Importar
            </a>
        </div>
    </div>
    <br>
    <div class="table-responsive">
        <table class="table table-striped dataTable tableStyle" id="example">
            <thead>
            <tr>
                <th style="text-align: center">ID</th>
                <th style="text-align: center">Tipo</th>
                <th width="10%" style="text-align: center"><?= $term[0]->valor.' / '.$term[1]->valor ?></th>
                <th width="20%" style="text-align: center">Raz&oacute;n Social o Nombre</th>
                <th width="40%" style="text-align: center">Direccion</th>
                <th width="10%" style="text-align: center">Teléfono</th>
                <th width="10%" style="text-align: center">correo</th>
                <th width="10%" class="desktop">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($clientes) > 0) {
                foreach ($clientes as $cliente) {
                    ?>
                    <tr>
                        <td class="center"><?= $cliente['id_cliente'] ?></td>
                        <td><?= $cliente['tipo_cliente'] == '1' ? 'Empresa' : 'Persona'?></td>
                        <td style="white-space: normal;"><?= $cliente['ruc'] == '2' ? $term[1]->valor.':' : $term[0]->valor.':'?> <?= $cliente['identificacion'] ?></td>
                        <td style="white-space: normal;"><?= $cliente['razon_social'] ?></td>                        
                        <td style="white-space: normal;"><?= $cliente['direccion'] ?></td>
                        <td style="white-space: normal;"><?= $cliente['telefono1'] ?></td>
                        <td style="white-space: normal;"><?= $cliente['email'] ?></td>
                        <!--  <td><?php //if($cliente['categoria_precio']!=null){ echo  $cliente['nombre_precio']; }?></td> -->
                        <td class="center" style="white-space: nowrap;">
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
    <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
        <i class="fa fa-file-excel-o fa-fw"></i>
    </button>
    <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
        <i class="fa fa-file-pdf-o fa-fw"></i>
    </button>
    <br><br>
</div>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js?<?php echo date("Hms"); ?>"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#formImportar').on('submit', function(e){
            e.preventDefault();

            var file = $('#file').val();
            var allowedExtensions = /(.csv)$/i; /* /(.jpg|.jpeg|.png|.gif)$/i; */
            var error = false;

            if(file==''){
                mensaje('warning', 'Debe seleccionar el archivo');
                error = true;
            }else if(!allowedExtensions.exec(file)){
                mensaje('warning', 'Debe seleccionar un archivo v&aacute;lido, s&oacute;lo se permite archivo con extesion .csv');
                error = true;
            }

            if(error==false){
                var f = $(this);
                var formData = new FormData(document.getElementById('formImportar'));
                $.ajax({
                    url: '<?= $ruta ?>cliente/importar',
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        $("#barloadermodal").modal('hide');
                        if(data.error==true){
                            mensaje('warning', data.mensaje);
                        }else{
                            mensaje('success', data.mensaje);
                        }
                        $('#resumen').css('display','block');
                    },
                    beforeSend: function(){
                        $('#barloadermodal').modal('show');
                    }
                });
            }
        });
    });

    $('#exportar_excel').on('click', function () {
        location.href = "<?= $ruta ?>cliente/excel";
    });

    $("#exportar_pdf").on('click', function () {
        location.href = "<?= $ruta ?>cliente/pdf";
    });

    function borrar(id, nom) {
        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
        $("#identificacion_borrar").attr('value', identificacion);
    }

    function importar() {
        $('#importar').modal('show');
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
<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        </div>
    </form>
</div>
<div class="modal fade" id="importar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form name="formImportar" id="formImportar" method="post" action="<?= $ruta ?>cliente/importar" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Importar Cliente</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">Seleccione archivo</div>
                        <div class="col-md-9">
                            <input name="file" id="file" type="file" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="resumen" style="display: none;">
                            <button type="button" id="descargar" class="btn btn-warning">Descargar resumen</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="importar" class="btn btn-primary">Importar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function () {
        TablesDatatables.init();

        $('#descargar').on('click', function(){
            var win = window.open('<?= $ruta ?>recursos/plantillas_datos/logs.txt', '_blank');
            win.focus();
        });
    });
</script>