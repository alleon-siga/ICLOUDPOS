<?php $md = get_moneda_defecto() ?>
<form name="formagregar" action="<?= base_url() ?>gastos/guardar" method="post" id="formagregar">
    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($gastos['id_gastos'])) echo $gastos['id_gastos']; ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Gasto</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Fecha</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="fecha" id="fecha" required="true" readonly style="cursor: pointer;"
                                   class="input-small input-datepicker form-control"
                                   value="<?= isset($gastos['fecha']) ? $gastos['fecha'] : date('d-m-Y'); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Local</label>
                        </div>
                        <div class="col-md-9">
                            <select name="filter_local_id" id="filter_local_id" required="true"
                                    class="select_chosen form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($local as $local): ?>
                                    <option
                                            value="<?php echo $local->local_id ?>" <?= $local->local_id == $this->session->userdata('id_local') ? 'selected' : '' ?>><?= $local->local_nombre ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Tipo de Gasto</label>
                        </div>
                        <div class="col-md-9">
                            <select name="tipo_gasto" id="tipo_gasto" required="true"
                                    class="select_chosen form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($tiposdegasto as $gasto): ?>
                                    <option
                                            value="<?php echo $gasto['id_tipos_gasto'] ?>" <?php if (isset($gastos['tipo_gasto']) and $gastos['tipo_gasto'] == $gasto['id_tipos_gasto']) echo 'selected' ?>><?= $gasto['nombre_tipos_gasto'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Persona afectada</label>
                        </div>
                        <div class="col-md-9">
                            <select name="persona_gasto" id="persona_gasto" required="true"
                                    class="select_chosen form-control">
                                <option value="">Seleccione</option>
                                <option value="1" <?= isset($gastos['proveedor_id']) && $gastos['proveedor_id'] != NULL ? 'selected' : '' ?>>
                                    Proveedor
                                </option>
                                <option value="2" <?= isset($gastos['usuario_id']) && $gastos['usuario_id'] != NULL ? 'selected' : '' ?>>
                                    Trabajador
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="proveedor_block" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Proveedor</label>
                        </div>
                        <div class="col-md-7">
                            <select name="proveedor" id="proveedor" required="true" class="form-control">
                                <option value="">Seleccione</option>   
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option
                                            value="<?php echo $proveedor->id_proveedor ?>"
                                        <?php if (isset($gastos['proveedor_id']) and $gastos['proveedor_id'] == $proveedor->id_proveedor) echo 'selected' ?>>
                                        <?= $proveedor->proveedor_nombre ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-default" data-toggle="tooltip"
                               title="Agregar Proveedor" data-original-title="Agregar Proveedor"
                               href="#" onclick="agregarproveedor()">
                                <i class="hi hi-plus-sign"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row" id="usuario_block" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Trabajador</label>
                        </div>
                        <div class="col-md-9">

                            <select name="usuario" id="usuario" required="true" class="form-control">
                                <option value="">Seleccione</option>
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
                <hr>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Gravable</label>
                        </div>
                        <div class="col-md-9">
                            <?php
                                if(!isset($gastos['gravable'])){
                                    $gravable = 0;
                                }else{
                                    $gravable = $gastos['gravable'];
                                }
                            ?>
                            <select name="gravable" id="gravable" class="select_chosen form-control">
                                <option value="0" <?php if($gravable == '0'){ echo "selected"; } ?>>NO</option>
                                <option value="1" <?php if($gravable == '1'){ echo "selected"; } ?>>SI</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Documento</label>
                        </div>
                        <div class="col-md-9">
                            <select name="cboDocumento" id="cboDocumento" class="form-control">
                            <?php foreach ($documentos as $documento) { ?>
                                <option value="<?= $documento->id_doc ?>"><?= $documento->des_doc ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">No. Documento</label>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="doc_serie" id="doc_serie" value="" class="form-control">
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="doc_numero" id="doc_numero" value="" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Cuenta</label>
                        </div>
                        <div class="col-md-9">
                            <select class="form-control select_chosen" id="cuenta_id" name="cuenta_id">
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Descripci&oacute;n</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="descripcion" id="descripcion" required="true" class="form-control"
                                   value="<?php if (isset($gastos['descripcion'])) echo $gastos['descripcion']; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Total</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-group">
                                <div class="input-group-addon"><?= $md->simbolo ?></div>
                                <input type="number" name="total" id="total" required="true" class="form-control"
                                       value="<?php if (isset($gastos['total'])) echo $gastos['total']; ?>"
                                       onkeydown="return soloDecimal(event);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">F6 Confirmar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
</form>
<script>
    $("#fecha").datepicker({
        format: 'dd-mm-yyyy'
    });

    var cuentas = [];
    <?php foreach ($cuentas as $cuenta):?>
    cuentas.push({
        id: <?= $cuenta->id ?>,
        local_id: <?= $cuenta->local_id ?>,
        moneda_nombre: '<?= $cuenta->moneda_nombre ?>',
        descripion: '<?= $cuenta->descripcion ?>'
    });
    <?php endforeach;?>

    $(document).ready(function () {
        $(document).off('keyup');
        $(document).off('keydown');

        var F6 = 117;

        var disabled_save = false;
        $(document).keydown(function (e) {
            if (e.keyCode == F6) {
                e.preventDefault();
            }
        });

        $(document).keyup(function (e) {
            if (e.keyCode == F6 && $("#agregar").is(":visible") == true) {
                e.preventDefault();
                e.stopImmediatePropagation();
                grupo.guardar();
            }
        });
        //$("#proveedor").chosen();

        setTimeout(function () {
            $(".select_chosen").chosen();
            $('#filter_local_id').trigger('change');
        }, 500);

        get_persona_gasto();

        $("#persona_gasto").on('change', function () {
            get_persona_gasto();
        });

        $('#filter_local_id').on('change', function () {
            $('#cuenta_id').chosen('destroy');
            var cuenta_select = $('#cuenta_id');

            cuenta_select.html('<option value="">Seleccione</option>');

            if ($(this).val() != "") {
                for (var i = 0; i < cuentas.length; i++) {
                    if (cuentas[i].local_id == $(this).val()) {
                        cuenta_select.append('<option value="' + cuentas[i].id + '">' + cuentas[i].descripion + ' | ' + cuentas[i].moneda_nombre + '</option>');
                    }
                }
            }

            cuenta_select.chosen();
        });

    });

    function get_persona_gasto() {

        if ($('#persona_gasto').val() == '') {
            $('#proveedor_block').hide();
            $('#usuario_block').hide();
            $("#proveedor").val("");
            $("#usuario").val("");
        }
        if ($('#persona_gasto').val() == '1') {
            $("#proveedor").val("");
            $('#proveedor_block').show();
            $('#usuario_block').hide();
            $("#proveedor").chosen();
        }
        if ($('#persona_gasto').val() == '2') {
            $("#usuario").val("");
            $('#proveedor_block').hide();
            $('#usuario_block').show();
            $("#usuario").chosen();
        }

    }

    function update_proveedor(id, nombre) {
        $('#proveedor').append('<option value="' + id + '">' + nombre + '</option>');
        $('#proveedor').val(id)
        $("#proveedor").trigger('chosen:updated');
    }
</script>