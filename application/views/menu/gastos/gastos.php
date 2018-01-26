<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Gastos</li>
    <li><a href="">Agregar y editar Gastos</a></li>
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
        <i class="fa fa-plus "> Nueva</i>
    </a>
    <br> <br>

    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
            <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
                <?php if (isset($locales)): ?>
                    <select id="local_id" class="form-control select_chosen">
                        <?php foreach ($locales as $local): ?>
                            <option <?php if ($this->session->userdata('id_local') == $local->local_id) echo "selected"; ?>
                                value="<?= $local->local_id; ?>"> <?= $local->local_nombre ?> </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

            </div>
            <div class="col-md-1"></div>

            <div class="col-md-4">
                <label class="control-label panel-admin-text">Persona Afectada:</label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="persona_gasto_filter" id="persona_gasto_filter" required="true" class="select_chosen form-control">
                            <option value="1">Proveedor</option>
                            <option value="2">Trabajador</option>
                        </select>
                    </div>  
                    <div class="col-md-6" id="proveedor_block_filter">
                        <select name="proveedor_filter" id="proveedor_filter" required="true" class="select_chosen form-control">
                                <option value="-">TODOS</option>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option
                                        value="<?php echo $proveedor->id_proveedor ?>" 
                                        <?php if (isset($gastos['proveedor_id']) and $gastos['proveedor_id'] == $proveedor->id_proveedor) echo 'selected' ?>>
                                        <?= $proveedor->proveedor_nombre ?>   
                                        </option>
                                <?php endforeach ?>
                            </select>
                    </div> 

                    <div class="col-md-6" id="usuario_block_filter" style="display: none;">
                            <select name="usuario_filter" id="usuario_filter" required="true" class="form-control">
                                <option value="-">TODOS</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option
                                        value="<?php echo $usuario->nUsuCodigo ?>" 
                                        <?php if (isset($gastos['usuario_id']) and $gastos['usuario_id'] == $usuario->nUsuCodigo) echo 'selected' ?>>
                                        <?= $usuario->nombre ?>   
                                        </option>
                                <?php endforeach ?>
                            </select>

                        </div>  
                </div>
            </div>

            <div class="col-md-1"></div>

            <div class="col-md-3">
            <label class="control-label panel-admin-text">Tipo de Gasto:</label>
                <select
                    id="tipo_gasto_id"
                    class="form-control select_chosen" name="tipo_gasto_id">
                    <option value="-">TODOS</option>
                    <?php foreach ($tipos_gastos as $gasto): ?>
                                    <option
                                        value="<?php echo $gasto['id_tipos_gasto'] ?>" <?php if (isset($gastos['tipo_gasto']) and $gastos['tipo_gasto'] == $gasto['id_tipos_gasto']) echo 'selected' ?>><?= $gasto['nombre_tipos_gasto'] ?></option>
                                <?php endforeach ?>
                </select>

            </div>

            

        </div>
    </div>
        <br>

        <div class="row">

            <div class="col-md-1">
                <label class="control-label panel-admin-text">Periodo:</label>
            </div>

            <div class="col-md-2">
                <select
                    id="mes"
                    class="form-control filter-input select_chosen" name="mes">
                    <option value="01" <?= date('m')=='01' ? 'selected' : ''?>>Enero</option>
                    <option value="02" <?= date('m')=='02' ? 'selected' : ''?>>Febrero</option>
                    <option value="03" <?= date('m')=='03' ? 'selected' : ''?>>Marzo</option>
                    <option value="04" <?= date('m')=='04' ? 'selected' : ''?>>Abril</option>
                    <option value="05" <?= date('m')=='05' ? 'selected' : ''?>>Mayo</option>
                    <option value="06" <?= date('m')=='06' ? 'selected' : ''?>>Junio</option>
                    <option value="07" <?= date('m')=='07' ? 'selected' : ''?>>Julio</option>
                    <option value="08" <?= date('m')=='08' ? 'selected' : ''?>>Agosto</option>
                    <option value="09" <?= date('m')=='09' ? 'selected' : ''?>>Septiembre</option>
                    <option value="10" <?= date('m')=='10' ? 'selected' : ''?>>Octubre</option>
                    <option value="11" <?= date('m')=='11' ? 'selected' : ''?>>Noviembre</option>
                    <option value="12" <?= date('m')=='12' ? 'selected' : ''?>>Diciembre</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="number" id="year" name="year" value="<?=date('Y')?>" class="form-control">
            </div>

            <div class="col-md-1">
               
            </div>


            <div class="col-md-2">
                <label class="control-label panel-admin-text">Rango de Dias</label>
            </div>
            <div class="col-md-1">
                <input type="number" min="1" id="dia_min" name="dia_min" value="<?=date('d')?>" class="form-control">
            </div>

            <div class="col-md-1">
                <input type="number" min="1" id="dia_max" name="dia_max" value="<?=date('d')?>" class="form-control">
            </div>

            <div class="col-md-2">
               
            </div>

            <div class="col-md-1">
                <button id="btn_buscar" class="btn btn-default btn-lg">
                    <i class="fa fa-search"></i>
                </button>
            </div>

        </div>
            <br>

    <div id="tabla_lista">
        
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

<div id="load_div" style="display: none;">
        <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
            <div class="col-md-12 text-center">
                <div class="loading-icon"></div>
            </div>
        </div>
    </div>




<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
        get_gastos();

        $(".select_chosen").chosen();

        $("#local_id, #tipo_gasto_id, #mes").on('change', function(){
            $("#tabla_lista").html('');
        });

        $("#year, #dia_min, #dia_max").bind('keyup change click', function(){
            $("#tabla_lista").html('');
        });

        $("#btn_buscar").on('click', function(){
            get_gastos();
        });

        $("#persona_gasto_filter").on('change', function(){
            get_persona_gasto_filter();
        });
    });

    function get_gastos(){
        $('#tabla_lista').html($('#load_div').html());
            var data = {
                'local_id': $('#local_id').val(),
                'tipo_gasto': $('#tipo_gasto_id').val(),
                'mes': $('#mes').val(),
                'year': $('#year').val(),
                'dia_min': $('#dia_min').val(),
                'dia_max': $('#dia_max').val(),
                'persona_gasto': $("#persona_gasto_filter").val(),
                'proveedor': $("#proveedor_filter").val(),
                'usuario': $("#usuario_filter").val()
            };

            $.ajax({
            url: '<?= base_url()?>gastos/lista_gasto',
            data: data,
            type: 'POST',
            success: function (datos) {

                $("#tabla_lista").html(datos);

                $('#exportar_pdf').attr('href', $('#exportar_pdf').attr('data-href') + data.local_id + '/' + data.tipo_gasto + '/' + data.mes + '/' + data.year + '/' + data.dia_min + '/' + data.dia_max + '/' + data.persona_gasto + '/' + data.proveedor + '/' + data.usuario);
            },
            error: function () {
                $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                    type: 'danger',
                    delay: 5000,
                    allow_dismiss: true
                });
                $("#tabla_lista").html('');
            }
        });
    }

    function get_persona_gasto_filter(){
        if($('#persona_gasto_filter').val() == ''){
                $('#proveedor_block_filter').hide();
                $('#usuario_block_filter').hide();
                $("#proveedor_filter").val("-");
                $("#usuario_filter").val("-");
            }
            if($('#persona_gasto_filter').val() == '1'){
                $("#proveedor_filter").val("-");
                $('#proveedor_block_filter').show();
                $('#usuario_block_filter').hide();
            }
            if($('#persona_gasto_filter').val() == '2'){
                $("#usuario_filter").val("-");
                $('#proveedor_block_filter').hide();
                $('#usuario_block_filter').show();
            }

            $("#usuario_filter").chosen();
            $("#proveedor_filter").chosen();
    }

    function borrar(id, nom) {
        $("#motivo").val('');
        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
    }


    function editar(id) {

        $("#agregar").load('<?= $ruta ?>gastos/form/' + id);
        $('#agregar').modal('show');
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>gastos/form');
        $('#agregar').modal('show');
    }


    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>gastos'

            })
        },
        guardar: function () {
            if ($("#fecha").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la fecha</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#descripcion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la descripcion</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#descripcion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el monto gastado</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#tipo_gasto").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el tipo de gasto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#persona_gasto").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la persona afectada</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }

            if ($("#persona_gasto").val() == '1' && $("#proveedor").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el proveedor</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }

            if ($("#persona_gasto").val() == '2' && $("#usuario").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el trabajador</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }


            if ($("#local_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el local</h4>', {
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

        if ($("#motivo").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar un motivo</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                return false;
            }

        App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrar', 'formeliminar');
    }


</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>gastos/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Gasto</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el Gasto seleccionado?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Motivo: </label>
                        </div>

                        <div class="col-md-8">
                            <input type="text" name="motivo" id="motivo" required="true" class="form-control"
                                   value="">
                        </div>
                    </div>
                    
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
