<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Familias</li>
    <li><a href="">Agregar y editar Familias</a></li>
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
<!--
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error"
             style="display:<?php //echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Error</h4>
            <span id="errorspan"><?php //echo isset($error) ? $error : '' ?></div>
    </div>
</div>-->


<div class="block">
    <!-- Progress Bars Wizard Title -->


    <a class="btn btn-primary" onclick="agregargrupo();">
        <i class="fa fa-plus "> Nueva</i>
    </a>
    <br>


    <?php
    echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
    ?>
    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Nombre</th>

                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($familias) > 0) {

                foreach ($familias as $familia) {
                    ?>
                    <tr id=<?= $familia['id_familia'] ?>>

                        <td class="center"><?= $familia['id_familia'] ?></td>
                        <td><?= $familia['nombre_familia'] ?></td>


                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Editar" data-original-title="fa fa-comment-o"
                                            href="#" onclick="editargrupo(' . $familia['id_familia'] . ');">'; ?>
                                <i class="fa fa-edit"></i>
                                </a>
                                <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o" onclick="borrargrupo(' . $familia['id_familia'] . ',\'' . $familia['nombre_familia'] . '\');">'; ?>
                                <i class="fa fa-trash-o"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>

            </tbody>
        </table>
        <br>
        <a href="<?= $ruta ?>familia/pdf" class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
           data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
        <a href="<?= $ruta ?>familia/excel" class="btn btn-default btn-lg" data-toggle="tooltip"
           title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
                class="fa fa-file-excel-o fa-fw"></i></a>
    </div>
</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

    function borrargrupo(id, nom) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }


    function editargrupo(id) {
        $("#agregarfamilia").load('<?= $ruta ?>familia/form/' + id);
        $('#agregarfamilia').modal('show');
    }

    function agregargrupo() {

        $("#agregarfamilia").load('<?= $ruta ?>familia/form');
        $('#agregarfamilia').modal('show');
    }


    function eliminar(){

    $('#load_div').show()
      $.ajax({
            url: "<?= $ruta ?>familia/eliminar",
            type: "POST",
            dataType: "json",
            data: {'id':$("#id_borrar").val(), 'nombre':$("#nom_borrar").val()},
            success: function(data) {
                    setTimeout(function () {
                        //$(".alert-danger").css('display','none');
                        $('#load_div').hide()
                    }, 2000)
                    $('#borrar').modal('toggle')

                    if (data != '') {

                        $.bootstrapGrowl('<h4>'+data[Object.keys(data)]+'</h4>', {
                            type: Object.keys(data),
                            delay: 2500,
                            allow_dismiss: true
                        });
                          if(Object.keys(data) == 'success'){
                            $('#'+$("#id_borrar").val()).remove()

                        }else{
                            return false
                        
                        }

                    
                    }
                }
        });

    }

</script>

<div class="modal fade" id="agregarfamilia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>familia/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Familia</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la familia seleccionada</p>
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