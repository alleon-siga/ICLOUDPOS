<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Unidades</li>
    <li><a href="">Agregar y editar Unidades</a></li>
</ul>

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
                <th>Abreviatura</th>
                <th>Presentaci&oacute;n</th>

                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($unidades) > 0) {
                foreach ($unidades as $unidad) {
                    ?>
                    <tr id=<?= $unidad['id_unidad'] ?>>

                        <td class="center"><?= $unidad['id_unidad'] ?></td>
                        <td id=nombre_<?= $unidad['id_unidad'] ?>><?= $unidad['nombre_unidad'] ?></td>
                        <td><?= $unidad['abreviatura'] ?></td>
                        <td><?= $unidad['presentacion']=='1' ? 'SI' : 'NO' ?></td>

                        <td class="center">
                            <div class="btn-group acciones_<?= $unidad['id_unidad'] ?>">
                                <?php

                                echo '<a class="btn btn-default btn-default btn-default editar_'.$unidad['id_unidad'].'" data-toggle="tooltip"
                                            title="Editar" data-original-title="Editar"
                                            href="#" onclick="editargrupo(' . $unidad['id_unidad'] . ');">'; ?>
                                <i class="fa fa-edit"></i>
                                </a>
                                <?php
                                if($unidad['estatus_unidad'] == 1){

                                 echo '<a class="btn btn-default btn-default btn-default eliminar_'.$unidad['id_unidad'].'" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="Eliminar" onclick="borrargrupo(' . $unidad['id_unidad'] . ',\'' . $unidad['nombre_unidad'] . '\');">'; ?>
                                <i class="fa fa-trash-o"></i>
                                </a>
                           <?php }else{

                                ?>
                                <a class="btn btn-default btn-default btn-default activar_<?= $unidad['id_unidad'] ?>" data-toggle="tooltip" title="Activar" data-original-title="Activar" href="#" onclick="activar_unidad(<?= $unidad['id_unidad'] ?>);"><i class="fa fa-edit"></i></a>

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


</div>
</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

    function borrargrupo(id, nom) {

        $('#borrargrupo').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }


    function editargrupo(id) {
        $("#agregargrupo").load('<?= $ruta ?>unidades/form/'+id);
        $('#agregargrupo').modal('show');
    }

    function agregargrupo() {
        $("#agregargrupo").load('<?= $ruta ?>unidades/form');
        $('#agregargrupo').modal('show');
    }


    var grupo = {
        ajaxgrupo : function(){
        $('#load_div').show()
            return  $.ajax({
                url:'<?= base_url()?>unidades'

            })
        },
        guardar : function () {
            if ($("#nombre").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#abreviatura").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el abreviatura</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            App.formSubmitAjax($("#formagregar").attr('action'), this.ajaxgrupo, 'agregargrupo', 'formagregar');

            setTimeout(function () {
               $('#load_div').hide()
            }, 2000)
        }
    }

    function eliminar(){


        $('#load_div').show()
        $.ajax({
            url: "<?= $ruta ?>unidades/eliminar",
            type: "POST",
            dataType: "json",
            data: {'id':$("#id_borrar").val(), 'nombre':$("#nom_borrar").val()},
            success: function(data) {
              setTimeout(function () {
                $('#load_div').hide()
                }, 2000)     
                $('#borrargrupo').modal('toggle')
                    if (data != '') {
                              

                        $.bootstrapGrowl('<h4>'+data[Object.keys(data)]+'</h4>', {
                            type: Object.keys(data),
                            delay: 2500,
                            allow_dismiss: true
                        });
                        if(Object.keys(data) == 'success'){
                            $('.eliminar_'+$("#id_borrar").val()).remove()
                            $('.acciones_'+$("#id_borrar").val()).append('<a onclick="activar_unidad('+$("#id_borrar").val()+');" href="#" data-original-title="Activar" title="Activar" data-toggle="tooltip" class="btn btn-default btn-default btn-default activar_'+$("#id_borrar").val()+'"><i class="fa fa-edit"></i></a>')

                        }else{
                            return false
                        
                        }

                    
                    }
                }
        });

    }


    function activar_unidad(unidad_id){
        var nombre_unidad = $('#nombre_'+unidad_id).html()
        $('#load_div').show()
        $.ajax({
            url: "<?= $ruta ?>unidades/activar",
            type: "POST",
            dataType: "json",
            data: {'id':unidad_id},
            success: function(data) {
                setTimeout(function () {
                    $('#load_div').hide()
                }, 2000)    
                if (data != '') {

                    $.bootstrapGrowl('<h4>'+data[Object.keys(data)]+'</h4>', {
                        type: Object.keys(data),
                        delay: 2500,
                        allow_dismiss: true
                    });
                    if(Object.keys(data) == 'success'){
                        $('.activar_'+unidad_id).remove()
                        $('.acciones_'+unidad_id).append('<a onclick=borrargrupo('+unidad_id+',"'+nombre_unidad+'") data-original-title="Eliminar" title="" data-toggle="tooltip" class="btn btn-default btn-default btn-default eliminar_'+unidad_id+'"><i class="fa fa-trash-o"></i></a>')

                    }else{
                        return false
                    
                    }

                
                }
                }
        });


    }

</script>
<div class="modal fade" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<div class="modal fade" id="borrargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>unidades/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Unidad</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la unidad seleccionada?</p>
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
