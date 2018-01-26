<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Categorias</li>
    <li><a href="">Agregar y Editar Categorias</a></li>
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
        <table class="table table-striped dataTable table-bordered" id="example">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tipo de Categoria</th>
              <th>Descripción</th>
              <th class="desktop">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($categoria) > 0)
            {
              foreach ($categoria as $categorias)
              {?>
                <tr>
                  <td><?= $categorias['id'] ?></td>
                  <td><?= $categorias['tipo'] ?></td>
                  <td><?= $categorias['valor'] ?></td>
                  <td class="center">
                    <div class="btn-group">
                      <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                            title="Editar" data-original-title="fa fa-comment-o"
                            href="#" onclick="editar(' . $categorias['id'] . ');">'; ?>
                            <i class="fa fa-edit"></i></a>
                      <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                          title="Eliminar" data-original-title="fa fa-comment-o"
                          onclick="borrar(' . $categorias['id'] . ');">'; ?>
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


<script type="text/javascript">

    function borrar(id, nom) {
        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }

    function editar(id) {
        $("#agregar").load('<?= $ruta ?>categorias/form/' + id);
        $('#agregar').modal('show');
    }

    function agregar() {
        $("#agregar").load('<?= $ruta ?>categorias/form');
        $('#agregar').modal('show');
    }

    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>categorias'

            })
        },
        guardar: function () {

            if ($("#valor").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#tipo_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar una cuenta</h4>', {
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
        App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrar', 'formeliminar');
    }
</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>categorias/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Categoria</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la categoria seleccionado?</p>
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

<script>$(function () {
        TablesDatatables.init();
    });</script>
