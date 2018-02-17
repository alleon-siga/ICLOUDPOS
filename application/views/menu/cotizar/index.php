<input type="hidden" id="sc" value="<?= valueOption('ACTIVAR_SHADOW') ?>">
<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Cotizar Venta</a></li>
    <label id="save_venta_load" style="font-size: 12px; float: right; display: none;"
           class="control-label badge label-primary">Cotizando la Venta...</label>
</ul>
<?php $md = get_moneda_defecto() ?>
<form id="form_venta" method="POST" action="<?= base_url('cotizar/save') ?>">
    <div class="block">

        <!--CAMPOS HIDDEN PARA GUARDAR OPCIONES NECESARIAS-->
        <input type="hidden" id="incorporar_igv" value="<?= valueOption('INCORPORAR_IGV') ?>">
        <input type="hidden" id="moneda_simbolo" value="<?= $md->simbolo ?>">

        <div class="row">

            <!-- SECCION IZQUIERDA -->
            <div class="col-md-9 block-section">

                <!-- SELECCION DEL LOCAL DE LA VENTA -->
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Local:</label>
                    </div>
                    <div class="col-md-3">
                        <select name="local_venta_id" id="local_venta_id" class='form-control'>
                            <?php foreach ($locales as $local): ?>
                                <option <?= $local->local_id == $local->local_defecto ? 'selected="selected"' : '' ?>
                                        value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <div class="help-key badge label-success" style="display: none;">1</div>
                            <select name="cliente_id" id="cliente_id" class='form-control'>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option
                                            value="<?php echo $cliente['id_cliente']; ?>"
                                            data-ruc="<?= $cliente['ruc'] ?>"><?php echo $cliente['razon_social']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a id="cliente_new" href="#" class="input-group-addon btn-default">
                                <i class="fa fa-plus-circle"></i>
                            </a>
                        </div>

                    </div>
                </div>

                <hr class="hr-margin-10">

                <!-- SELECCION DEL LOCAL Y EL PRODUCTO PARA VENDER -->
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Producto:</label>
                    </div>

                    <div class="col-md-3">

                    </div>

                    <div class="col-md-7">
                        <div class="help-key badge label-success" style="display: none;">3</div>
                        <select name="producto_id" id="producto_id" class='form-control'
                                data-placeholder="Seleccione el Producto">
                            <option value=""></option>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->producto_id ?>">
                                    <?php $barra = $barra_activa->activo == 1 && $producto->barra != "" ? "CB: " . $producto->barra : "" ?>
                                    <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre . " " . $barra ?>
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
                                <label class="control-label">TOTAL MINIMO:</label>
                            </div>

                            <div class="col-md-4">
                                <label id="stock_actual" data-view="1" style="font-size: 15px; cursor: pointer;"
                                       class="control-label badge label-info"></label>

                            </div>


                            <div class="col-md-2">
                                <label class="control-label">TOTAL STOCK:</label>
                            </div>

                            <div class="col-md-4">

                                <label id="popover_stock" class="control-label badge label-info"
                                       style="width: 200% !important; font-size: 15px; cursor: pointer; display:none; float: left; position: absolute; z-index: 3000;">

                                </label>
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
                        <!-- SECCION DE TIPO PRECIOS -->
                        <div class="row">
                            <div class="col-md-2 venta_input">
                                <label class="control-label panel-admin-text">Precio Unitario:</label>
                                <div style="display: none;">
                                    <!--<div class="help-key badge label-success" style="display: none;">4</div>-->
                                    <select name="precio_id" id="precio_id" class='form-control'>
                                        <?php foreach ($precios as $precio): ?>
                                            <option <?= $precio['id_precio'] == 3 ? 'selected="selected"' : '' ?>
                                                    value="<?= $precio['id_precio'] ?>">
                                                <?= $precio['nombre_precio'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8 row" id="loading_precio" style="display: none;">
                                <div class="col-md-12 text-center">
                                    <div class="loading-icon"></div>
                                </div>
                            </div>
                            <div id="producto_precio" class="col-md-8 row text-center venta_input">

                            </div>

                            <div class="col-md-2">

                            </div>
                        </div>

                        <hr class="hr-margin-10">

                        <!-- SECCION DE PRECIO UNITARIO E IMPORTE -->
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label panel-admin-text">Precio U. Venta:</label>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                                    <input type="text" style="text-align: right;"
                                           class='form-control'
                                           data-index="0"
                                           name="precio_unitario" id="precio_unitario" value="0.00"
                                           onkeydown="return soloDecimal4(this, event);" readonly>
                                    <a id="editar_pu" data-estado="0" href="#" class="input-group-addon"><i
                                                class="fa fa-edit"></i></a>
                                </div>
                                <h6 id="precio_unitario_um"
                                    style="text-align: center; margin-bottom: 0; margin-top: 2px;"></h6>
                            </div>

                            <div class="col-md-1 text-right" style="padding-right: 2px;">
                                <label class="control-label panel-admin-text">SubTotal:</label>
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
                                    Agregar <span class="help-key-side badge label-success"
                                                  style="display: none;">[Enter]</span></button>
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
                        <label class="control-label" id="moneda_text"><?= $monedas[0]['nombre']?></label>
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

                <!--SUBTOTAL-->
                <div id="block_subtotal" style="display:none;" class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Sub-Total:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                            <input type="text" style="text-align: right;"
                                   class='form-control'
                                   name="subtotal" id="subtotal" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);" readonly>
                        </div>
                    </div>
                </div>

                <!--IMPUESTO-->
                <div id="block_impuesto" style="display:none;" class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Impuesto:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                            <input type="text" style="text-align: right;"
                                   class='form-control'
                                   name="impuesto" id="impuesto" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);" readonly>
                        </div>
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
                            <input type="text" style="text-align: right; background: #FFC000"
                                   class='form-control'
                                   name="total_importe" id="total_importe" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);" readonly>
                        </div>
                    </div>
                </div>

                <!--TIPO DE PAGO-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Pago:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="help-key badge label-success" style="display: none;">6</div>
                        <select name="tipo_pago" id="tipo_pago" class='form-control'>
                            <?php foreach ($tipo_pagos as $pago): ?>
                                <option
                                        value="<?= $pago['id_condiciones'] ?>"><?= $pago['nombre_condiciones'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="block_credito_periodo" style="display:none;" class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Cr&eacute;dito Periodo:</label>
                    </div>

                    <div class="col-md-7">
                        <select id="c_pago_periodo" name="c_pago_periodo" class='form-control'>
                            <option value="1">Diario</option>
                            <option value="2">Interdiario</option>
                            <option value="3">Semanal</option>
                            <option value="4" selected>Mensual</option>
                            <option value="5">Personalizado</option>
                        </select>
                    </div>
                </div>

                <div id="block_periodo_per" style="display:none;" class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Dias:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" style="text-align: right;"
                               class='form-control'
                               name="periodo_per" id="periodo_per" value="30"
                               onkeydown="return soloDecimal4(this, event);">
                    </div>
                </div>

                <!--TIPO DE DOCUMENTO-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Documento:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="help-key badge label-success" style="display: none;">7</div>
                        <select name="tipo_documento" id="tipo_documento" class="form-control">
                            <?php $facturacion = valueOption('ACTIVAR_FACTURACION_VENTA') ?>
                            <?php $shadow_stock = valueOption('ACTIVAR_SHADOW') ?>
                            <?php foreach ($tipo_documentos as $key => $value): ?>

                                <?php if (($facturacion == 1 || $shadow_stock == 1) && ($value->id_doc == 1 || $value->id_doc == 3 || $value->id_doc == 6)): ?>

                                    <option <?= $value->id_doc == 3 ? 'selected="selected"' : '' ?>
                                            value="<?= $value->id_doc ?>"><?= $value->des_doc ?></option>

                                <?php elseif (($facturacion == 0 || $shadow_stock == 0) && $value->id_doc == 6): ?>

                                    <option <?= $value->id_doc == 6 ? 'selected="selected"' : '' ?>
                                            value="<?= $value->id_doc ?>"><?= $value->des_doc ?></option>

                                <?php endif; ?>

                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <!--FECHA DE LA VENTA-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Fecha Vencimiento:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control date-picker" name="fecha_venta" id="fecha_venta"
                               value="<?= date('d/m/Y') ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Fecha Entrega:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control date-picker" name="fecha_entrega" id="fecha_entrega"
                               value="<?= date('d/m/Y') ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Lugar Entrega:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="lugar_entrega" id="lugar_entrega" value="">
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
        <!--DIALOGOS DE LA COTIZACION-->


        <div class="modal fade" id="dialog_cotizar" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
             aria-hidden="true">

            <!-- TERMINAR VENTA CONTADO -->

            <?php echo isset($dialog_cotizar) ? $dialog_cotizar : '' ?>

        </div>

    </div>
</form>


<div class="block">


    <div class="form-actions">

        <button class="btn" id="terminar_cotizar" type="button"><i
                    class="fa fa-save fa-3x text-info fa-fw"></i> <br>F6
            Cotizar
        </button>

        <!--<button type="button" class="btn" id="abrir_ventas"><i
                class="fa fa-folder-open-o fa-3x text-info fa-fw"></i><br>Abrir
        </button>-->

        <button type="button" class="btn" id="reiniciar_cotizar"><i class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
        </button>
        <button class="btn" type="button" id="cancelar_cotizar"><i
                    class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar
        </button>
    </div>

</div>

<div class="modal fade" id="dialog_new_cliente" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

</div>

<div class="modal fade" id="dialog_new_garante" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

</div>


<div class="modal fade" id="dialog_cotizar_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmaci&oacute;n</h4>
            </div>

            <div class="modal-body ">
                <h5 id="confirm_cotizar_text">Estas Seguro?</h5>
            </div>

            <div class="modal-footer">
                <button id="confirm_cotizar_button" type="button" class="btn btn-primary">
                    Aceptar
                </button>

                <button type="button" class="btn btn-danger" onclick="$('#dialog_cotizar_confirm').modal('hide');">
                    Cancelar
                </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>


</div>


<script src="<?php echo base_url('recursos/js/pages/tablesDatatables.js') ?>"></script>
<script src="<?php echo base_url('recursos/js/Validacion.js') ?>"></script>
<script src="<?php echo base_url('recursos/js/cotizar.js') ?>"></script>
<script>

</script>
