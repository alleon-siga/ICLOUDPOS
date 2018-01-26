<?php $md = get_moneda_defecto()?>
<ul class="breadcrumb breadcrumb-top">
    <li>Ajuste de Inventario</li>
    <li><a href="">Operaciones de Entrada/Salida</a></li>
    <label id="save_venta_load" style="font-size: 12px; float: right; display: none;"
           class="control-label badge label-primary">Guardando el Ajuste...</label>
</ul>

<form id="form_venta" method="POST" action="<?= base_url('ajuste/save_ajuste') ?>">
    <div class="block">

        <!--CAMPOS HIDDEN PARA GUARDAR OPCIONES NECESARIAS-->
        <input type="hidden" id="moneda_simbolo" value="<?= $md->simbolo ?>">

        <div class="row">

            <!-- SECCION IZQUIERDA -->
            <div class="col-md-9 block-section">

                <!-- SELECCION DEL LOCAL Y EL PRODUCTO PARA VENDER -->

                <div class="row">
                    <div class="col-md-2">

                    </div>

                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Almacen:</label>
                    </div>

                    <div class="col-md-3" id="local_block_text" style="display: none;">
                        <label class="control-label" id="local_text"></label>
                    </div>
                    <div class="col-md-3" id="local_block_input">
                        <div class="help-key badge label-success" style="display: none;">2</div>
                        <select name="local_id" id="local_id" class='form-control'>
                            <?php foreach ($locales as $local): ?>
                                <option <?= $local->local_id == $local->local_defecto && $this->session->userdata('esSuper') != 1 ? 'selected="selected"' : '' ?>
                                    value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-7">
                        <div class="help-key badge label-success" style="display: none;">3</div>
                        <select name="producto_id" id="producto_id" class='form-control'
                                data-placeholder="Seleccione el Producto">
                            <option value=""></option>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->producto_id ?>">
                                    <?php $barra = $barra_activa->activo == 1 && $producto->barra != "" ? "CB: ".$producto->barra : ""?>
                                    <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre . " ". $barra ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!--SECCION COMPLETA DE LA AGREGACION DE PRODUCTOS-->
                <div class="row" id="loading" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="loading-icon"></div>
                    </div>
                </div>

                <div class="row block_producto_unidades" style="display: none;">
                    <div class="col-md-12">
                        <hr class="hr-margin-10">

                        <!-- SECCION DE LA CANTIDAD EN STOCK -->
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">STOCK:</label>
                            </div>

                            <div class="col-md-5">
                                <button type="button" id="add_todos" class="btn btn-xs btn-success">+ Todos</button>
                                <label id="stock_actual" data-view="1" style="font-size: 15px; cursor: pointer;"
                                       class="control-label badge label-info"></label>
                            </div>


                            <div class="col-md-2">
                                <label class="control-label">TOTAL STOCK:</label>
                            </div>

                            <div class="col-md-3">
                                <div id="popover_stock" class="popover" style="margin-bottom: 10px; display: none;">

                                </div>
                                <label id="stock_total" style="font-size: 15px; cursor: pointer;"
                                       class="control-label badge label-default"></label>


                                <!--CERRAR VENTANA DE AGREGAR PRODUCTOS-->
                                <a style="float: right;" class="badge label-danger" id="close_add_producto">x</a>
                            </div>

                        </div>


                        <br>
                        <!-- DESGLOSE DE LOS PRODUCTOS -->
                        <div class="row">
                            <div class="col-md-2">

                            </div>

                            <div id="producto_form" class="col-md-8 row text-center venta_input">

                            </div>

                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label panel-admin-text">TOTAL:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input id="total_minimo" type="text" class="form-control text-center" value="0"
                                               readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div id="um_minimo" class="col-md-8 text-center"></div>
                                </div>
                            </div>
                        </div>

                        <br>

                        <hr class="hr-margin-10">

                        <!-- SECCION DE COSTO UNITARIO E IMPORTE -->
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label panel-admin-text">Costo Unitario:</label>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                                    <input type="text" style="text-align: right;"
                                           class='form-control'
                                           data-index="0"
                                           name="costo_unitario" id="costo_unitario" value="0.00"
                                           onkeydown="return soloDecimal4(this, event);">
                                </div>
                                <h6 id="costo_unitario_um"
                                    style="text-align: center; margin-bottom: 0; margin-top: 2px;"></h6>
                            </div>

                            <div class="col-md-1 text-right" style="padding-right: 2px;">
                                <label class="control-label panel-admin-text">Importe:</label>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                                    <input type="text" style="text-align: right;"
                                           class='form-control'
                                           name="importe" id="importe" value="0.00"
                                           onkeydown="return soloDecimal4(this, event);" readonly>
                                </div>
                            </div>

                            <div class="col-md-3 text-right">

                                <button type="button" id="add_producto" class="btn btn-primary">
                                    Agregar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--FIN DE LA SECCION COMPLETA DE LA AGREGACION DE PRODUCTOS-->

                <hr class="hr-margin-10">

                <!--TABLAS DE LOS PRODUCTOS AGREGADOS-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-content box-nomargin">
                                            <span style="float: right; margin-bottom: 5px;">
                                                <input type="checkbox" id="tabla_vista"> <b>Mostrar Detalles</b>
                                            </span>
                            <table
                                class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort"
                                data-nosort="0">
                                <thead id="head_productos"></thead>
                                <tbody id="body_productos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>


            <!--SECCION DERECHA-->
            <div class="col-md-3 block block-section venta-right venta_input">

                <!--SELECCION MONEDA-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Moneda:</label>
                    </div>
                    <div class="col-md-7" id="moneda_block_text" style="display: none;">
                        <label class="control-label" id="moneda_text">Soles</label>
                    </div>
                    <div class="col-md-7" id="moneda_block_input" style="display: block;">
                        <div class="help-key badge label-success" style="display: none;">5</div>
                        <select name="moneda_id" id="moneda_id" class='form-control'>
                            <?php foreach ($monedas as $moneda): ?>
                                <option
                                    data-tasa="<?php echo $moneda['tasa_soles'] ?>"
                                    data-simbolo="<?php echo $moneda['simbolo'] ?>"
                                    data-oper="<?php echo $moneda['ope_tasa'] ?>"
                                    value="<?= $moneda['id_moneda'] ?>"><?= $moneda['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!--SELECCION TASA DE LA MONEDA-->
                <div id="block_tasa" style="display:none;" class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Tipo Cambio:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <div class="input-group-addon"><?= $md->simbolo ?></div>
                            <input type="text" style="text-align: right;"
                                   class='form-control'
                                   name="tasa" id="tasa" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);">
                            <a id="refresh_tasa" href="#" class="input-group-addon" style="display: none;"><i
                                    class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                </div>

                <!--TIPO DE MOVIMIENTO-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Movimiento:</label>
                    </div>

                    <div class="col-md-7" id="movimiento_block_text" style="display: none;">
                        <label class="control-label" id="movimiento_text">Entrada</label>
                    </div>
                    <div class="col-md-7" id="movimiento_block_input">
                        <select name="tipo_movimiento" id="tipo_movimiento" data-placeholder="Seleccione" class='form-control'>
                                <option value=""></option>
                                <option value="1">Entrada</option>
                                <option value="2">Salida</option>
                        </select>
                    </div>
                </div>

                <!--TIPO DE OPERACION-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Operacion:</label>
                    </div>

                    <div class="col-md-7" id="operacion_block_text" style="display: none;">
                        <label class="control-label" id="operacion_text"></label>
                    </div>
                    <div class="col-md-7" id="operacion_block_input">
                        <select name="tipo_operacion" id="tipo_operacion"
                        data-placeholder="Selecciona" class='form-control'>
                            <option value=""></option>
                            <?php foreach (get_sunat_operacion() as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>




                <hr class="hr-margin-10">
                <!--TIPO DE DOCUMENTO-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Documento:</label>
                    </div>

                    <div class="col-md-7" id="documento_block_text" style="display: none;">
                        <label class="control-label" id="documento_text"></label>
                    </div>
                    <div class="col-md-7" id="documento_block_input">
                        <select name="tipo_documento" id="tipo_documento" class='form-control' data-placeholder="Selecciona">
                            <option value=""></option>
                            <?php foreach (get_sunat_documento() as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>

                <!--SERIE Y NUMERO-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Serie:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="serie_doc" id="serie_doc">
                    </div>
                </div>

                <!--SERIE Y NUMERO-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Numero:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="numero_doc" id="numero_doc">
                    </div>
                </div>

                <hr class="hr-margin-10">

                <!--FECHA DEL AJUSTE-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Fecha:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control date-picker" name="fecha_venta" id="fecha_venta"
                               value="<?= date('d/m/Y') ?>" readonly>
                    </div>
                </div>

                <!--TOTAL DE PRODUCTOS-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Total Productos:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="total_producto" id="total_producto" value="0"
                               readonly>
                    </div>
                </div>

                <!--TOTAL DEL IMPORTE-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Total:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                            <input type="text" style="text-align: right;"
                                   class='form-control'
                                   name="total_importe" id="total_importe" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);" readonly>
                        </div>
                    </div>
                </div>


            </div>

        </div>


        <div class="modal fade" id="loading_save_venta" tabindex="-1" role="dialog" style="top: 50px;"
             aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
             aria-hidden="true">
            <div class="row" id="loading">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>


        </div>


    </div>
</form>


<div class="block">


    <div class="form-actions">

        <button class="btn" id="terminar_ajuste" type="button"><i
                class="fa fa-save fa-3x text-info fa-fw"></i> <br>Guardar
        </button>



        <button type="button" class="btn" id="reiniciar_ajuste"><i class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
        </button>
        <button class="btn" type="button" id="cancelar_ajuste"><i
                class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar
        </button>
    </div>

</div>




<div class="modal fade" id="dialog_venta_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmaci&oacute;n</h4>
            </div>

            <div class="modal-body ">
                <h5 id="confirm_venta_text">Estas Seguro?</h5>
            </div>

            <div class="modal-footer">
                <button id="confirm_venta_button" type="button" class="btn btn-primary">
                    Aceptar
                </button>

                <button type="button" class="btn btn-danger" onclick="$('#dialog_venta_confirm').modal('hide');">
                    Cancelar
                </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>


</div>


<script src="<?php echo base_url('recursos/js/pages/tablesDatatables.js') ?>"></script>
<script src="<?php echo base_url('recursos/js/Validacion.js') ?>"></script>
<script src="<?php echo base_url('recursos/js/ajuste.js') ?>"></script>
<script>

</script>
