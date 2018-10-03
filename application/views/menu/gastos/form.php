<style type="text/css">
    .form-group{
        margin-bottom: 2px !important;
    }
</style>
<form name="formagregar" action="<?= base_url() ?>gastos/guardar" method="post" id="formagregar" class="form-horizontal">
    <input type="hidden" name="gastos_id" id="id" required="true" value="<?php if (isset($gastos['id_gastos'])) echo $gastos['id_gastos']; ?>">
    <input type="hidden" name="cuotas" id="cuotas" value="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Gasto</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-md-3">Local</label>
                    <div class="col-md-4">
                        <select name="filter_local_id" id="filter_local_id" required="true"
                                class="select_chosen form-control">
                            <option value="">Seleccione</option>
                            
                            <?php
                            echo $this->session->userdata('id_local');
                                foreach ($local as $local):
                                    $selected = "";    
                                    if(isset($gastos['local_id'])){
                                        if($local->local_id == $gastos['local_id']){
                                            $selected = "selected";
                                        }
                                    }elseif($local->local_id == $this->session->userdata('id_local')){
                                        $selected = "selected";
                                    }
                            ?>
                                <option value="<?php echo $local->local_id ?>" <?= $selected ?>><?= $local->local_nombre ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <label class="control-label col-md-1">Fecha</label>
                    <div class="col-md-4">
                        <input type="text" name="fecha" id="fecha" required="true" readonly style="cursor: pointer;"
                               class="input-small input-datepicker form-control"
                               value="<?= isset($gastos['fecha']) ? date('d-m-Y', strtotime($gastos['fecha'])) : date('d-m-Y'); ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Tipo de Gasto</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <select name="tipo_gasto" id="tipo_gasto" required="true"
                                    class="select_chosen form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($tiposdegasto as $gasto): ?>
                                    <option
                                            value="<?php echo $gasto['id_tipos_gasto'] ?>" <?php if (isset($gastos['tipo_gasto']) and $gastos['tipo_gasto'] == $gasto['id_tipos_gasto']) echo 'selected' ?>><?= $gasto['nombre_tipos_gasto'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <a class="input-group-addon btn-default" data-toggle="tooltip" title="Agregar Tipo de Gasto" data-original-title="Agregar Tipo de Gasto" href="#" onclick="agregarTipoGasto()">
                                <i class="hi hi-plus-sign"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Persona afectada</label>
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
                <div class="form-group" id="proveedor_block" <?= isset($gastos['proveedor_id']) && $gastos['proveedor_id'] != NULL ? 'style="display: block;"' : 'style="display: none;"' ?>>
                    <label class="control-label col-md-3">Proveedor</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <select name="proveedor" id="proveedor" required="true" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <option value="<?php echo $proveedor->id_proveedor ?>" <?php if (isset($gastos['proveedor_id']) and $gastos['proveedor_id'] == $proveedor->id_proveedor) echo 'selected' ?>><?= $proveedor->proveedor_nombre ?></option>
                                <?php endforeach ?>
                            </select>
                            <a class="input-group-addon btn-default" data-toggle="tooltip"
                               title="Agregar Proveedor" data-original-title="Agregar Proveedor"
                               href="#" onclick="agregarproveedor()">
                                <i class="hi hi-plus-sign"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="usuario_block" <?= isset($gastos['usuario_id']) && $gastos['usuario_id'] != NULL ? 'style="display: block;"' : 'style="display: none;"' ?>>
                    <label class="control-label col-md-3">Trabajador</label>
                    <div class="col-md-9">
                        <select name="usuario" id="usuario" required="true" class="form-control">
                            <option value="">Seleccione</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario->nUsuCodigo ?>" <?php if (isset($gastos['usuario_id']) and $gastos['usuario_id'] == $usuario->nUsuCodigo) echo 'selected' ?>><?= $usuario->nombre ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label class="control-label col-md-3">Condici&oacute;n</label>
                    <div class="col-md-9">
                        <select name="tipo_pago" id="tipo_pago" class='form-control'>
                            <?php
                                foreach ($tipo_pagos as $pago):
                                    $selected = "";    
                                    if(isset($gastos['condicion_pago'])){
                                        if($gastos['condicion_pago'] == $pago['id_condiciones']){
                                            $selected = "selected";
                                        }
                                    }
                            ?>
                                <option
                                        value="<?= $pago['id_condiciones'] ?>" <?= $selected ?>><?= $pago['nombre_condiciones'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Gravable</label>
                    <div class="col-md-9">
                        <?php
                            if(!isset($gastos['gravable'])){
                                $gravable = 0;
                            }else{
                                $gravable = $gastos['gravable'];
                            }
                        ?>
                        <select name="gravable" id="gravable" class="form-control">
                            <option value="0" <?php if($gravable == '0'){ echo "selected"; } ?>>NO</option>
                            <option value="1" <?php if($gravable == '1'){ echo "selected"; } ?>>SI</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Documento</label>
                    <div class="col-md-4">
                        <select name="cboDocumento" id="cboDocumento" class="form-control">
                        <?php foreach ($documentos as $documento) { ?>
                            <option value="<?= $documento->id_doc ?>" <?php if (isset($gastos['id_documento']) and $gastos['id_documento'] == $documento->id_doc) echo 'selected' ?>><?= $documento->des_doc ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" placeholder="Serie" name="doc_serie" id="doc_serie" value="<?php if (isset($gastos['serie'])) echo $gastos['serie']; ?>" class="form-control" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <input type="text" placeholder="Numero" name="doc_numero" id="doc_numero" value="<?php if (isset($gastos['numero'])) echo $gastos['numero']; ?>" class="form-control" autocomplete="off">
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label class="control-label col-md-3">Medio de Pago</label>
                    <div class="col-md-9">
                        <select class="form-control select_chosen" id="metodo_pago" name="metodo_pago">
                            <option value="" selected="">Seleccione</option>
                            <?php foreach ($metodo_pago as $metodopago): ?>
                                    <option value="<?php echo $metodopago['id_metodo'] ?>" <?php if (isset($gastos['id_metodo']) and $gastos['id_metodo'] == $metodopago['id_metodo']) echo 'selected' ?> data-metodo="<?= $metodopago['tipo_metodo']?>"><?= $metodopago['nombre_metodo'] ?></option>
                                <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Cuenta</label>
                    <div class="col-md-9">
                        <select class="form-control select_chosen" id="cuenta_id" name="cuenta_id">
                            <option value="">Seleccione</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Descripci&oacute;n</label>
                    <div class="col-md-6">
                        <textarea name="descripcion" id="descripcion" required="true" class="form-control"><?php if (isset($gastos['descripcion'])) echo $gastos['descripcion']; ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <a href="javascript:<?php if(isset($gastos['id_gastos'])){ echo "editarDetalle(".$gastos['id_gastos'].")"; }else{ echo "agregarDetalle()"; } ?>">Agregar detalle</a>
                    </div>
                </div>
                <?php
                    $display = '';
                    if(!isset($gastos['gravable'])){
                        $display = 'style="display: none;"';
                    }elseif($gastos['gravable']=='0'){
                        $display = 'style="display: none;"';
                    }
                ?>
                <div class="form-group" id="idImp" <?= $display ?>>
                    <div class="col-md-3">
                        <select name="id_impuesto" id="id_impuesto" class='form-control'>
                            <option value="">Seleccione</option>
                            <?php if (count($impuestos) > 0): ?>
                                <?php 
                                    foreach ($impuestos as $impuesto):
                                        $selected = '';
                                        if(isset($gastos['id_impuesto'])){ 
                                            if($gastos['id_impuesto']==$impuesto['id_impuesto']){ 
                                                $selected = 'selected="selected"';
                                            }
                                        }else{
                                            if($impuesto['id_impuesto']=='1'){
                                                $selected = 'selected="selected"';
                                            }
                                        }
                                ?>
                                    <option value="<?php echo $impuesto['id_impuesto']; ?>" data-impuesto="<?= $impuesto['porcentaje_impuesto'] ?>" <?= $selected ?>>
                                        <?php echo $impuesto['nombre_impuesto']; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="input-group-addon idMoneda" id="idMoneda"></div>
                            <input type="number" name="impuesto" id="impuesto" class="form-control" value="<?php if (isset($gastos['impuesto'])) echo $gastos['impuesto']; ?>" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-5" id="idSt" <?= $display ?>>
                        <div class="input-group">
                            <div class="input-group-addon idMoneda" id="idMoneda"></div>
                            <input readonly="readonly" type="number" name="subtotal" id="subtotal" required="true" class="form-control"
                                   value="<?php if (isset($gastos['subtotal'])) echo $gastos['subtotal']; ?>"
                                   onkeydown="return soloDecimal(event);">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Total</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <div class="input-group-addon idMoneda" id="idMoneda"></div>
                            <input type="number" name="total" id="total" required="true" class="form-control"
                                   value="<?php if (isset($gastos['total'])) echo $gastos['total']; ?>"
                                   onkeydown="return soloDecimal(event);" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="grupo.guardar()">F6 Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false"></div>
    <div class="modal fade" id="dialog_gasto_prestamo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>    
    <div class="modal fade" id="dialog_gasto_credito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
</form>
<script>
    var cuentas = [];
    <?php foreach ($cuentas as $cuenta):?>
    cuentas.push({
        id: <?= $cuenta->id ?>,
        local_id: <?= $cuenta->local_id ?>,   
        moneda_nombre: '<?= $cuenta->moneda_nombre ?>',
        simbolo: '<?= $cuenta->simbolo ?>',
        descripion: '<?= $cuenta->descripcion ?>',
        banco: '<?= $cuenta->banco ?>'
    });
    <?php endforeach;?>

    var url = '<?= base_url() ?>';
    var caja_desglose_id = '<?php echo (isset($gastos['caja_desglose_id']))? $gastos['caja_desglose_id'] : ""; ?>';
</script>
<script src="<?= base_url() ?>recursos/js/gastos_form.js"></script>