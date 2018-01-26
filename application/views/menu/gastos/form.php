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

                            <select name="local_id" id="local_id" required="true" class="select_chosen form-control">
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

                            <select name="tipo_gasto" id="tipo_gasto" required="true" class="select_chosen form-control">
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

                            <select name="persona_gasto" id="persona_gasto" required="true" class="select_chosen form-control">
                                <option value="">Seleccione</option>
                                    <option value="1" <?= isset($gastos['proveedor_id']) && $gastos['proveedor_id'] != NULL ? 'selected':''?>>Proveedor</option>
                                    <option value="2" <?= isset($gastos['usuario_id']) && $gastos['usuario_id'] != NULL ? 'selected':''?>>Trabajador</option>
                            </select>

                        </div>
                    </div>
                </div>

                <div class="row" id="proveedor_block" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Proveedor</label>
                        </div>
                        <div class="col-md-9">

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

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Moneda</label>
                        </div>
                        <div class="col-md-9">
                           <select class="form-control select_chosen" id="monedas" name="monedas">
                                    <?php foreach ($monedas as $mon):?>
                                    <option value="<?= $mon['id_moneda']?>"
                                        <?= isset($gastos['id_moneda']) && $mon['id_moneda'] == $gastos['id_moneda'] ? 'selected':''?>
                                        data-tasa="<?= $mon['tasa_soles']?>">
                                        <?= $mon['nombre']?>
                                    </option>
                                    <?php endforeach;?>
                               </select>
                        </div>
                    </div>
                </div>

                <div class="row" id="tasa_cambio_block" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Tasa</label>
                        </div>
                        <div class="col-md-9">
                            <input type="number" name="tasa_cambio" id="tasa_cambio" required="true" class="form-control"
                                   value="<?php if (isset($gastos['tasa_cambio'])) echo $gastos['tasa_cambio']; ?>">
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
                            <input type="number" name="total" id="total" required="true" class="form-control"
                                   value="<?php if (isset($gastos['total'])) echo $gastos['total']; ?>" onkeydown="return soloDecimal(event);">
                        </div>
                    </div>
                </div>




            </div>

            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>
<script>
    $("#fecha").datepicker({
        format: 'dd-mm-yyyy'
    });


    $(document).ready(function(){
        
        setTimeout(function () {
                $(".select_chosen").chosen();
            }, 500);
        
        get_persona_gasto();
        get_tasa();

        $("#persona_gasto").on('change', function(){
            get_persona_gasto();
        });

        $("#monedas").on('change', function(){
            get_tasa();
        });
    });

    function get_tasa(){
        if($("#monedas").val() != '1029'){
                $("#tasa_cambio").val($("#monedas option:selected").attr('data-tasa'));
                $("#tasa_cambio_block").show();
            }
            else{
                $("#tasa_cambio").val('0');
                $("#tasa_cambio_block").hide();
            }
    }

    function get_persona_gasto(){
        if($('#persona_gasto').val() == ''){
                $('#proveedor_block').hide();
                $('#usuario_block').hide();
                $("#proveedor").val("");
                $("#usuario").val("");
            }
            if($('#persona_gasto').val() == '1'){
                $("#proveedor").val("");
                $('#proveedor_block').show();
                $('#usuario_block').hide();
                $("#proveedor").chosen();
            }
            if($('#persona_gasto').val() == '2'){
                $("#usuario").val("");
                $('#proveedor_block').hide();
                $('#usuario_block').show();
                $("#usuario").chosen();
            }


            
    }
</script>