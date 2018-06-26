<?php $ruta = base_url(); ?>
<?php $term = diccionarioTermino() ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Proveedor</li>
    <li><a href="">Agregar y editar Proveedor</a></li>
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
    <a class="btn btn-primary" onclick="agregar();">
        <i class="fa fa-plus "> Nuevo</i>
    </a>
    <br>
    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered tableStyle" id="example">
            <thead>
            <tr>
                <th>ID</th>
                <th width="5%"><?= $term[0]->valor.' / '.$term[1]->valor ?></th>
                <th width="30%">Razón Social</th>
                <th width="30%">Direcci&oacute;n Fiscal</th>
                <th width="10%">Tel&eacute;fono</th>
                <th width="5%">Correo</th>
                <th width="10%">Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($proveedores) > 0) {
                foreach ($proveedores as $proveedor) {
                    ?>
                    <tr id=<?= $proveedor['id_proveedor'] ?>>
                        <td class="center"><?= $proveedor['id_proveedor'] ?></td>
                        <td style="white-space: normal;"><?= $proveedor['proveedor_ruc'] ?></td>
                        <td style="white-space: normal;"><?= $proveedor['proveedor_nombre'] ?></td>
                        <td style="white-space: normal;"><?= $proveedor['proveedor_direccion1'] ?></td>
                        <td style="white-space: normal;"><?= $proveedor['proveedor_telefono1'] ?></td>
                        <td style="white-space: normal;"><?= $proveedor['proveedor_email'] ?></td>
                        <td style="white-space: nowrap;">
                            <div class="btn-group">
                                <?php
                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Editar" data-original-title="fa fa-comment-o"
                                            href="#" onclick="editar(' . $proveedor['id_proveedor'] . ');">'; ?>
                                <i class="fa fa-edit"></i>
                                </a>
                                <?php echo '<a class="btn btn-danger" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $proveedor['id_proveedor'] . ',\'' . $proveedor['proveedor_nombre'] . '\');">'; ?>
                                <i class="fa fa-trash-o"></i>
                                </a>
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
    $(document).ready(function () {
        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });
    });
    function borrar(id, nom) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }


    function editar(id) {

        $("#agregarproveedor").load('<?= $ruta ?>proveedor/form/'+id);
        $('#agregarproveedor').modal('show');
    }

    function agregar() {

        $("#agregarproveedor").load('<?= $ruta ?>proveedor/form');
        $('#agregarproveedor').modal('show');
    }


    function eliminar(){

  $('#load_div').show()
        $.ajax({
            url: "<?= $ruta ?>proveedor/eliminar",
            type: "POST",
            dataType: "json",
            data: {'id':$("#id_borrar").val(), 'nombre':$("#nom_borrar").val()},
            success: function(data) {
                setTimeout(function () {
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

    function exportar_pdf() {
        var data = {
        };

        var win = window.open('<?= base_url()?>proveedor/index/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
        };
        var win = window.open('<?= base_url()?>proveedor/index/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }    
</script>

<div class="modal fade" id="agregarproveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>proveedor/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Proveedor</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el Proveedor seleccionado?</p>
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
<script src="<?php echo $ruta?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function(){ TablesDatatables.init(); });</script>