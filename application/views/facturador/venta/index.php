<link rel="stylesheet" href="<?php echo base_url('recursos/js/autocomplete/jquery-ui.min.css') ?>">
<style>
    .ui-autocomplete-loading {
        background: white url("<?php echo base_url('recursos/js/autocomplete/images/ui-anim_basic_16x16.gif') ?>") right center no-repeat;
    }

    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }
    /* IE 6 doesn't support max-height
     * we use height instead, but this forces the menu to always be this tall
     */
    * html .ui-autocomplete {
        height: 300px;
    }
    table thead {
        background-color: #2d2d2d !important;
        color: white !important;
    }
    table tr:hover {
        font-weight: bold !important;
    }
</style>
<input type="hidden" id="sc" value="<?= valueOption('ACTIVAR_SHADOW') ?>">
<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Realizar Venta</a></li>
    <label id="save_venta_load" style="font-size: 12px; float: right; display: none;"
           class="control-label badge label-primary">Guardando la Venta...</label>
</ul>
<?php $md = get_moneda_defecto() ?>
<form id="form_venta" method="POST" action="<?= base_url('venta_new/save') ?>">
    <input type="hidden" name="venta_id" id="venta_id" value="<?= $venta->venta_id ?>">
    <div class="block">
        <!--CAMPOS HIDDEN PARA GUARDAR OPCIONES NECESARIAS-->
        <input type="hidden" id="generar_facturacion" value="<?= valueOption('ACTIVAR_FACTURACION_VENTA') ?>">
        <input type="hidden" id="generar_shadow_stock" value="<?= valueOption('ACTIVAR_SHADOW') ?>">
        <input type="hidden" id="incorporar_igv" value="<?= valueOption('INCORPORAR_IGV') ?>">
        <input type="hidden" id="moneda_simbolo" value="<?= $md->simbolo ?>">
        <input type="hidden" id="barra_activa" value="<?= $barra_activa->activo ?>">
        <input type="hidden" id="producto_what_codigo" value="<?= getCodigo() ?>">
        <div class="row">
            <!-- SECCION IZQUIERDA -->
            <div class="col-md-9 block-section">
                <!-- SELECCION DEL LOCAL DE LA VENTA -->
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Local de Venta:</label>
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
                                        data-ruc="<?= $cliente['ruc'] ?>"
                                        <?= $cliente['id_cliente'] == 1 ? 'selected' : '' ?>
                                        ><?php echo $cliente['razon_social']; ?></option>
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
                        <div class="help-key badge label-success" style="display: none;">2</div>
                        <select name="local_id" id="local_id" class='form-control'>
                            <?php foreach ($locales as $local): ?>
                                <option <?= $local->local_id == $local->local_defecto ? 'selected="selected"' : '' ?>
                                    value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="producto_complete">
                    </div>
                    <div class="col-md-7" style="display: none;">
                        <div class="input-group">
                            <div class="help-key badge label-success" style="display: none;">3</div>
                            <select name="producto_id" id="producto_id" class='form-control'>
                                <option value=""></option>
                            </select>
                            <a id="refresh_productos" href="#" class="input-group-addon btn-default">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
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

                            <div class="col-md-4">
                                <button type="button" id="add_todos" class="btn btn-xs btn-success">+ Todos</button>
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

                                <?php if (validOption('ACTIVAR_SHADOW', 1)): ?>
                                    <label id="stock_contable" style="font-size: 15px; cursor: pointer;"
                                           class="control-label badge"></label>
                                       <?php endif; ?>

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
                                <label class="control-label panel-admin-text">
                                    <a href="#" id="precioUnitario">Precio Unitario:</a>
                                </label>
                                <br>
                                <?php if ($this->session->userdata('grupo') == '2' || $this->session->userdata('grupo') == '9') { //Administrador y gerente ?>
                                    <label class="control-label panel-admin-text">
                                        <a href="#" id="costoUnitario" style="color: red;">Costo Unitario:</a>
                                    </label>
                                <?php } ?>
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

                            <div class="col-md-8">
                                <label id="popover_precioUnitario" class="control-label badge label-info"
                                       style="width: 50% !important; font-size: 15px; cursor: pointer; display:none; float: left; position: absolute; z-index: 3000;"></label>
                                <label id="popover_costoUnitario" class="control-label badge label-info"
                                       style="width: 50% !important; font-size: 15px; cursor: pointer; display:none; float: left; position: absolute; z-index: 3000; background-color: #FF7B7B"></label>

                            </div>
                            <div id="producto_precio" class="col-md-8 row text-center venta_input">
                            </div>
                            <div class="col-md-2 text-center">
                                <input type="checkbox" name="chkCostoContable" data-costocontable="" id="chkCostoContable" value="" style="margin-top:15px;">
                            </div>
                        </div>
                        <hr class="hr-margin-10">
                        <!-- SECCION DE PRECIO UNITARIO E IMPORTE -->
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label panel-admin-text">Precio U. Venta:</label>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda" style="padding: 0px; min-width: 25px;"
                                         style="padding: 0px; min-width: 25px;"><?= $md->simbolo ?></div>
                                    <input type="text" style="text-align: right;"
                                           class='form-control'
                                           data-index="0"
                                           name="precio_unitario" id="precio_unitario" value="0.00"
                                           onkeydown="return soloDecimal4(this, event);" readonly>
                                    <a id="editar_pu" data-estado="0" href="#" class="input-group-addon"
                                       style="padding: 0px; min-width: 25px;">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </div>                                
                                <h6 id="precio_unitario_um" style="text-align: center; margin-bottom: 0; margin-top: 2px;"></h6>
                            </div>
                            <div class="col-md-1 text-right" style="padding-right: 2px;">
                                <label class="control-label panel-admin-text">Descuento:</label>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <input type="text"
                                           class='form-control'
                                           name="descuento" id="descuento" value=""
                                           style="text-align: right; background-color: #ce8483 !important; color: #9c3428 !important; font-weight: bold;"
                                           onkeydown="return soloDecimal4(this, event);">
                                    <div class="input-group-addon">%</div>
                                </div>
                            </div>
                            <div class="col-md-1 text-right" style="padding-right: 2px;">
                                <label class="control-label panel-admin-text">SubTotal:</label>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group">
                                    <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                                    <input type="text" style="text-align: right;" 
                                           data-sub = "0.00"
                                           class='form-control'
                                           name="importe" id="importe" value="0.00"
                                           onkeydown="return soloDecimal4(this, event);" readonly>
                                    <a id="editar_su" data-estado="0" href="#" class="input-group-addon" style="padding: 0px; min-width: 25px;"><i class="fa fa-edit"></i></a>
                                </div>
                                <h6 id="subtotal_um" style="text-align: center; margin-bottom: 0; margin-top: 2px;"></h6>
                            </div>
                            <div class="col-md-2 text-right">                                
                                <div class="col-md-12 text-right" style="padding-left: 0px;width: 80px;padding-right: 0px;">
                                    <button type="button" id="add_producto" class="btn btn-primary" >
                                        Agregar <span class="help-key-side badge label-success"
                                                      style="display: none;">[Enter]</span></button>
                                </div>


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
                                <input type="checkbox" id="aplicarCosteo"> <b>Aplicar</b>&nbsp;
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

                <!--FECHA DE LA VENTA-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Fecha:</label>
                    </div>

                    <div class="col-md-7">
                        <input type="text" class="form-control date-picker" name="fecha_venta" id="fecha_venta"
                               value="<?= date('d/m/Y') ?>" readonly>
                    </div>
                </div>
                <?php if (isset($usuarios)) { ?>
                    <div class="row">
                        <div class="col-md-5 label-title">
                            <label class="control-label">Vendedor:</label>
                        </div>
                        <div class="col-md-7">                       
                            <select name="vendedor_id" id="vendedor_id" class='form-control'>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option <?= $usuario->id == $this->session->userdata('id') ? 'selected="selected"' : '' ?>
                                        value="<?= $usuario->id ?>"><?= $usuario->username ?></option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php } else { ?>
                    <input type="hidden" name="vendedor_id" id="vendedor_id" value="<?= $this->session->userdata('id') ?>">
                <?php } ?>
                <!--SELECCION MONEDA-->
                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Moneda:</label>
                    </div>
                    <div class="col-md-7" id="moneda_block_text" style="display: none;">
                        <label class="control-label" id="moneda_text"><?= $monedas[0]['nombre'] ?></label>
                    </div>
                    <div class="col-md-7" id="moneda_block_input" style="display: block;">
                        <div class="help-key badge label-success" style="display: none;">5</div>
                        <select name="moneda_id" id="moneda_id" class='form-control'>
                            <?php foreach ($monedas as $moneda): ?>
                                <option
                                    data-tasa="<?php echo $moneda['tasa_soles'] ?>"
                                    data-nombre="<?php echo $moneda['nombre'] ?>"
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
                <div id="block_subtotal" class="row">
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

                <div id="block_subtotal" class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Descuento:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="input-group">
                            <div class="input-group-addon tipo_moneda"><?= $md->simbolo ?></div>
                            <input type="text"
                                   style="text-align: right; background-color: #ce8483 !important; color: #9c3428 !important;"
                                   class='form-control'
                                   name="total_descuento" id="total_descuento" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);" readonly>
                        </div>
                    </div>
                </div>

                <!--IMPUESTO-->
                <div id="block_impuesto" class="row">
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
                            <?php //foreach ($tipo_pagos as $pago): ?>
                            <option
                                value="<?= $tipo_pagos['id_condiciones'] ?>"><?= $tipo_pagos['nombre_condiciones'] ?></option>
                                <?php //endforeach; ?>
                        </select>
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
                            <?php foreach ($tipo_documentos as $key => $value): ?>
                                <option <?= $value->id_doc == $comprobantes_default->config_value ? 'selected="selected"' : '' ?>
                                    value="<?= $value->id_doc ?>"><?= $value->des_doc ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" id="COMPROBANTE" value="<?= valueOption('COMPROBANTE', 0) ?>">
                <div class="row" style="display: <?= validOption('COMPROBANTE', '1') ? 'block' : 'none' ?>">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Comprobante:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="help-key badge label-success" style="display: none;">6</div>
                        <select name="comprobante_id" id="comprobante_id" class='form-control'>
                            <option value="">Seleccione</option>
                            <?php foreach ($comprobantes as $comprobante): ?>
                                <option
                                    value="<?= $comprobante->id ?>"><?= $comprobante->nombre ?></option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!--ESTADO DE LA VENTA-->
                <div class="row" style="display: none;">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Estado:</label>
                    </div>

                    <div class="col-md-7">
                        <div class="help-key badge label-success" style="display: none;">8</div>
                        <select name="venta_estado" id="venta_estado" class="form-control">
                                <option value="COMPLETADO">COMPLETADO</option>
                           
                        </select>
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

                <div class="row">
                    <div class="col-md-5 label-title">
                        <label class="control-label">Impuesto:</label>
                    </div>

                    <div class="col-md-7">
                        <select name="tipo_impuesto" id="tipo_impuesto" class="form-control">
                            <option value="1">Incluye impuesto</option>
                            <option value="2">Agregar impuesto</option>
                            <?php if ($comprobantes_default->config_value == '6'): ?>
                                <option value="3">No considerar impuesto</option>
                            <?php endif; ?>                            
                        </select>
                    </div>
                </div>

                <div class="row">
                    <button type="button" class="btn btn-primary col-md-12 text-center add_nota">
                        <i class="fa fa-plus"></i>
                        Agregar Nota de Venta
                    </button>
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
        <!--DIALOGOS DE LA VENTA-->

        <div class="modal fade" id="dialog_venta_caja" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
             aria-hidden="true">

            <!-- TERMINAR VENTA EN CAJA-->

            <?php echo isset($dialog_venta_caja) ? $dialog_venta_caja : '' ?>

        </div>


        <div class="modal fade" id="dialog_venta_credito" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
             aria-hidden="true">

            <!-- TERMINAR VENTA CONTADO -->

            <?php echo isset($dialog_venta_credito) ? $dialog_venta_credito : '' ?>

        </div>

    </div>
    <div class="modal fade" id="dialog_venta_contado" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">
        <!-- TERMINAR VENTA CONTADO -->
        <?php echo isset($dialog_venta_contado) ? $dialog_venta_contado : '' ?>
    </div>
    <div class="modal fade" id="dialog_venta_nota" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Nota a la Venta</h4>
                </div>
                <div class="modal-body ">
                    <label class="control-label">Nota:</label>
                    <textarea type="text" name="venta_nota" rows="5" id="venta_nota"
                              class='form-control textarea-editor'>
                    </textarea>
                </div>
                <div class="modal-footer">
                    <button onclick="$('#dialog_venta_nota').modal('hide');" type="button" class="btn btn-primary">
                        Aceptar
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
</form>
<iframe style="display: block;" id="imprimir_frame_venta" src="" frameborder="YES" height="0" width="0"
        border="0" scrolling=no>
</iframe>
<div class="block">
    <div class="form-actions">
        <button class="btn" id="terminar_venta" type="button"><i
                class="fa fa-save fa-3x text-info fa-fw"></i> <br>F6
            Guardar
        </button>
        <button type="button" class="btn" id="reiniciar_venta"><i class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
        </button>
        <button class="btn" type="button" id="cancelar_venta"><i
                class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar
        </button>
    </div>
</div>
<div class="modal fade" id="dialog_venta_imprimir" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">
</div>
<div class="modal fade" id="dialog_new_cliente" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">
</div>
<div class="modal fade" id="dialog_new_garante" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">
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
<script src="<?php echo base_url('recursos/js/autocomplete/jquery-ui.min.js') ?>"></script>
<script src="<?php echo base_url('recursos/js/venta_shadow.js') ?>"></script>
<script>
                    var venta = [];
                    $(function () {
<?php if ($venta != NULL): ?>
                            venta.local_id = <?= $venta->local_id ?>;
                            venta.cliente_id = <?= $venta->cliente_id ?>;
                            venta.documento_id = <?= $venta->documento_id ?>;
                            venta.tipo_impuesto = <?= $venta->tipo_impuesto ?>;
                            venta.condicion_id = <?= $venta->condicion_id ?>;
                            venta.moneda_id = <?= $venta->moneda_id ?>;
                            venta.moneda_tasa = <?= $venta->moneda_tasa ?>;
                            venta.detalles = [];

    <?php foreach ($venta->detalles as $detalle): ?>
                                var temp = {
                                    producto_id: <?= $detalle->producto_id ?>,
                                    impuesto: <?= $detalle->impuesto ?>,
                                    afectacion_impuesto: <?= $detalle->afectacion_impuesto ?>,
                                    producto_nombre: '<?= $detalle->producto_nombre ?>',
                                    precio: <?= $detalle->precio ?>,
                                    precio_venta: <?= $detalle->precio_venta ?>,
                                    um_min: '<?= $detalle->um_min ?>',
                                    um_min_abr: '<?= $detalle->um_min_abr ?>',
                                    total_min: <?= $detalle->total_min ?>,
                                    precio_comp: <?= $detalle->precio_comp ?>,
                                    contable_costo: <?= $detalle->contable_costo ?>,
                                    real_costo: <?= $detalle->real_costo ?>,
                                    unidades: []
                                };
        <?php foreach ($detalle->unidades as $unidad): ?>
                                    var uni = {
                                        unidad_id: <?= $unidad->unidad_id ?>,
                                        unidad_nombre: '<?= $unidad->unidad_nombre ?>',
                                        unidad_abr: '<?= $unidad->unidad_abr ?>',
                                        cantidad: <?= $unidad->cantidad ?>,
                                        unidades: <?= $unidad->unidades ?>,
                                        orden: <?= $unidad->orden ?>
                                    };
                                    temp.unidades.push(uni);
        <?php endforeach; ?>
                                venta.detalles.push(temp);
    <?php endforeach; ?>

                            $("#cliente_id").val(venta.cliente_id).trigger("chosen:updated");
                            $("#cliente_id").change();

                            $("#tipo_pago").val(venta.condicion_id).trigger("chosen:updated");
                            $("#tipo_pago").change();

                            $("#moneda_id").val(venta.moneda_id).trigger("chosen:updated");
                            $("#moneda_id").change();

                            $("#local_venta_id").val(venta.local_id).trigger("chosen:updated");
                            $("#local_venta_id").change();

                            $("#local_id").val(venta.local_id).trigger("chosen:updated");
                            $("#local_id").change();

                            $('#tipo_impuesto').val(venta.tipo_impuesto);

                            $('#tasa').val(venta.moneda_tasa);

                            for (var i = 0; i < venta.detalles.length; i++) {
                                var prod = venta.detalles[i];
                                add_producto_from_venta(
                                        prod.producto_id,
                                        prod.producto_nombre,
                                        prod.precio,
                                        prod.precio_venta,
                                        prod.um_min,
                                        prod.um_min_abr,
                                        prod.total_min,
                                        prod.unidades,
                                        prod.impuesto,
                                        prod.afectacion_impuesto,
                                        prod.precio_comp,
                                        prod.contable_costo,
                                        prod.real_costo
                                        );
                            }

<?php endif; ?>
                    });
                    function add_producto_from_venta(producto_id, producto_nombre, precio, precio_venta, um_min, um_min_abr, total_min, unidades, impuesto, afectacion_impuesto, precio_comp, contable_costo, real_costo) {

                        var local_id = $("#local_id").val();
                        var precio_id = $("#precio_id").val();
                        //AGREGO EL PRODUCTO E INICIALIZO SUS VALORES
                        var producto = {};
                        producto.index = lst_producto.length;
                        producto.producto_id = producto_id;
                        producto.producto_impuesto = impuesto;
                        producto.afectacion_impuesto = afectacion_impuesto;
                        producto.producto_nombre = encodeURIComponent(producto_nombre);
                        producto.precio_id = precio_id;
                        producto.precio_unitario = parseFloat(precio_venta);
                        producto.precio_descuento = parseFloat(precio);
                        producto.descuento = parseFloat(0);

                        producto.um_min = um_min;
                        producto.um_min_abr = um_min_abr;

                        producto.total_local = {};
                        producto.detalles = [];

                        producto.total_local['local' + local_id] = parseFloat(total_min);


                        for (var i = 0; i < unidades.length; i++) {
                            var input = unidades[i];
                            var detalle = {};

                            detalle.local_id = local_id;
                            detalle.local_nombre = encodeURIComponent($('#local_id option:selected').text());
                            detalle.cantidad = parseFloat(input.cantidad);
                            detalle.unidad = input.unidad_id;
                            detalle.unidad_nombre = input.unidad_nombre;
                            detalle.unidad_abr = input.unidad_abr;
                            detalle.unidades = input.unidades;
                            detalle.orden = input.orden;

                            producto.detalles.push(detalle);
                        }


                        producto.total_minimo = 0;
                        for (var local_index in producto.total_local)
                            producto.total_minimo += parseFloat(producto.total_local[local_index]);

                        producto.subtotal = parseFloat(producto.total_minimo * producto.precio_descuento);
                        producto.precio_comp = parseFloat(precio_comp);
                        producto.contable_costo = parseFloat(contable_costo);
                        producto.real_costo = parseFloat(real_costo);
                        producto.precio_unitario_bk = parseFloat(precio_venta);
                        producto.subtotal_bk = parseFloat(producto.total_minimo * producto.precio_descuento);
                        lst_producto.push(producto);
                        update_view(get_active_view());
                        refresh_right_panel();

                    }

</script>
