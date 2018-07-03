<?php $ruta = base_url(); ?>
<input id="precio_base" type="hidden" value="<?= valueOption('PRECIO_INGRESO', 'COSTO') ?>">
<!--<input id="producto_cualidad" type="hidden">-->
<input id="producto_serie_activo" value="<?php echo getProductoSerie() ?>" type="hidden">
<input id="base_url" type="hidden" value="<?= $ruta ?>">

<script src="<?php echo $ruta; ?>recursos/js/ingresos.js?<?= date('His'); ?>"></script>
<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li>
        <a href=""><?php if ($costos === 'true') {

                if ($facturar == "SI") {

                    echo "Facturar Ingreso";
                }

                if (isset($ingreso->id_ingreso) and $facturar == "NO") {

                    echo "Valorizar Documento";
                }

                if (!isset($ingreso->id_ingreso)) {

                    echo "Formulario De Ingresos ";
                }

                ?><?php } else { ?> Registro de Existencia <?php } ?></a>
    </li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable"
             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">X
            </button>
            <h4><i class="icon fa fa-ban"></i> Error</h4>
            <?php echo isset($error) ? $error : '' ?></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">X
            </button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <?php echo isset($success) ? $success : '' ?>
        </div>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">

    <div class="row-fluid">
        <form id="frmCompra" class='form-horizontal' style="margin-top: 3%">
            <div class="box-content">
                <input id="facturar" name="facturar" type="hidden"
                       value="<?php if (isset($facturar)) echo $facturar; ?>">
                <input id="costos" name="costos" type="hidden" value="<?= $costos ?>">
                <input id="ingreso_id" name="id_ingreso" type="hidden"
                       value="<?php if (isset($ingreso->id_ingreso)) echo $ingreso->id_ingreso; ?>">
                <div class="block-section">
                    <div class="force-margin">

                        <!-- Empiezan lo campos de los formularios-->
                        <!-- FILA 1 ************************************************************-->
                        <div class="section-border">
                            <span class="section-text-header">Datos del Ingreso</span>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <div class="col-md-2">
                                            <label for="fecEnt" class="control-label">Local:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="controls">

                                                <select name="local" id="local" class='cho form-control'
                                                        required="true" <?php if (isset($ingreso->id_ingreso) and $facturar == "NO") echo 'disabled' ?>>
                                                    <?php

                                                    if (count($locales) > 0) {
                                                        foreach ($locales as $local) {
                                                            ?>
                                                            <option
                                                                    value="<?= $local['int_local_id'] ?>"
                                                                <?php if (isset($ingreso->id_ingreso) and $ingreso->local_id == $local['int_local_id']) {
                                                                    echo "selected";
                                                                } else {
                                                                    if ($local['int_local_id'] == $this->session->userdata('id_local')) echo 'selected';
                                                                } ?> >
                                                                <?= $local['local_nombre'] ?></option>


                                                        <?php }
                                                    } ?>
                                                </select>
                                                <input type="hidden" name="local" id="local_hidden"
                                                       value="<?php if (isset($ingreso->local_id)) echo $ingreso->local_id ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($costos === 'true') { ?>
                                        <div class="control-group">
                                            <div class="col-md-2">
                                                <label class="control-label">Pago:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="controls">
                                                    <select name="pago" id="pago" class='cho form-control'
                                                            required="true">
                                                        <option value="" selected>Seleccione</option>
                                                        <option
                                                                value="CONTADO" <?php if (isset($ingreso->pago) and $ingreso->pago == "CONTADO") echo "selected" ?>>
                                                            CONTADO
                                                        </option>
                                                        <option
                                                                value="CREDITO" <?php if (isset($ingreso->pago) and $ingreso->pago == "CREDITO") echo "selected" ?>>
                                                            CREDITO
                                                        </option>
                                                    </select>
                                                </div>

                                            </div>

                                        </div>
                                        <br><br><br>
                                    <?php } ?>

                                    <?php if ($costos === 'true') { ?>
                                        <div class="control-group">
                                            <div class="col-md-2">
                                                <label for="fecEnt" class="control-label">Fecha
                                                    Emisi&oacute;n:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="controls">
                                                    <div class="input-append">
                                                        <input type="text" placeholder="día-mes-año"
                                                               name="fecEmision"
                                                               value="<?php if (isset($ingreso->fecha_emision) and $ingreso->fecha_emision != null)
                                                                   echo date("d-m-Y", strtotime($ingreso->fecha_emision)); else echo date('d-m-Y'); ?>"
                                                               id="fecEmision"
                                                               class='input-small datepick required form-control'
                                                               required="true" readonly>
                                                        <span class="add-on"><i class="icon-calendar"></i></span>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    <?php } else { ?>


                                    <?php } ?>

                                    <div class="control-group">
                                        <div class="col-md-2">
                                            <label for="fecEnt" class="control-label">Motivo del Ingreso:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="controls">
                                                <select name="tipo_ingreso" id="" class='cho form-control'
                                                        required="true">
                                                    <option
                                                            value="<?= COMPRA ?>" <?php if (isset($ingreso->tipo_ingreso) and $ingreso->tipo_ingreso == COMPRA)
                                                        echo "selected"; ?>><?= COMPRA ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($costos === 'true') {

                                        echo "<br><br><br>";
                                    } ?>

                                    <?php if ($costos === 'true'): ?>
                                        <div class="control-group">
                                            <div class="col-md-2">
                                                <label for="cboTipDoc" class="control-label">Tipo Documento:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="controls">
                                                    <select name="cboTipDoc" id="cboTipDoc" class='cho form-control'
                                                            required="true">
                                                    <?php foreach ($documentos as $documento) { ?>
                                                        <option value="<?= $documento->des_doc ?>" 
                                                        <?php if (isset($ingreso->tipo_documento) && $ingreso->tipo_documento == $documento->des_doc) echo "selected"; ?>>
                                                        <?= $documento->des_doc ?>
                                                        </option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($costos === 'true'): ?>
                                        <div class="control-group">
                                            <div class="col-md-2">
                                                <label class="control-label">Documento:</label>
                                            </div>


                                            <div class="col-md-1">
                                                <input type="text" class='input-mini required form-control'
                                                       name="doc_serie" id="doc_serie" autofocus="autofocus"
                                                       required="true"
                                                       maxlength="8"
                                                       value="<?php if (isset($ingreso->documento_serie) and
                                                           $ingreso->documento_serie != null and $ingreso->documento_serie != 0
                                                       ) echo $ingreso->documento_serie; ?>">
                                            </div>

                                            <div class="col-md-3">
                                                <input type="text" class='input-medium required form-control'
                                                       name="doc_numero" id="doc_numero" required="true"
                                                       value="<?php if (isset($ingreso->documento_numero)
                                                           and
                                                           $ingreso->documento_numero != null and $ingreso->documento_numero != 0
                                                       ) echo $ingreso->documento_numero; ?>"
                                                       maxlength="20">
                                            </div>

                                        </div>
                                        <br><br><br>
                                    <?php endif ?>

                                    <!-- END FILA 3 ************************************************************-->


                                    <!-- FILA 4 ************************************************************-->
                                    <?php if ($costos === 'false') {

                                        echo "<br><br><br>";
                                    } ?>

                                    <div class="control-group">
                                        <div class="col-md-2">
                                            <label for="Proveedor" class="control-label">Proveedor:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <select name="cboProveedor" id="cboProveedor"
                                                            class='cho form-control' required="true" required="true">
                                                        <?php if (count($lstProveedor) > 0): ?>
                                                            <?php foreach ($lstProveedor as $pv): ?>
                                                                <option
                                                                        value="<?php echo $pv->id_proveedor; ?>"
                                                                    <?php if (isset($ingreso->id_proveedor) and $ingreso->id_proveedor == $pv->id_proveedor)
                                                                        echo "selected"; ?>><?php echo $pv->proveedor_nombre; ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2" style="padding-left:0;">
                                                    <a class="btn btn-default" data-toggle="tooltip"
                                                    title="Agregar Proveedor" data-original-title="Agregar Proveedor"
                                                    href="#" onclick="agregarproveedor()">
                                                    <i class="hi hi-plus-sign"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>                              
                                    </div>
                                    <?php if ($costos === 'true'): ?>
                                        <div class="control-group">
                                            <div class="col-md-2">
                                                <label for="fecEnt"
                                                       class="control-label">Observaci&oacute;n:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="controls">
                                                    <input type="text" placeholder="Observaciones"
                                                           name="observacion"
                                                           id=""
                                                           class='form-control'
                                                           value="<?php if (isset($ingreso->ingreso_observacion) and
                                                               $ingreso->ingreso_observacion != null and $ingreso->ingreso_observacion != 0
                                                           ) echo $ingreso->ingreso_observacion; ?>"
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                        <br><br><br>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                        <!-- END FILA 4 ************************************************************-->

                        <!-- FILA DE LA MONEDA ************************************************************-->
                        <?php if (count($monedas) == 1): ?>
                            <script>
                                $("#config_moneda").click();
                            </script>
                        <?php endif; ?>
                        <?php if ($costos === 'true'): ?>
                            <div class="section-border"
                                 style="display: <?php echo count($monedas) == 1 ? 'none' : 'block' ?>">
                                <span class="section-text-header">Configure primero la moneda a usar para realizar los ingresos</span>
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="col-md-2"></div>
                                        <div class="control-group">
                                            <div class="col-md-2 text-right">
                                                <label for="" class="control-label">Moneda:</label>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="controls">
                                                    <select class="form-control" id="monedas" name="monedas">
                                                        <?php foreach ($monedas as $mon) { ?>
                                                            <option
                                                                <?php if (isset($ingreso->id_moneda) and $ingreso->id_moneda == $mon['id_moneda']) {
                                                                    echo "selected";
                                                                } ?>
                                                                    value="<?= $mon['id_moneda'] ?>"
                                                                    data-tasa="<? echo $mon['tasa_soles'] ?>"
                                                                    data-oper="<? echo $mon['ope_tasa'] ?>"
                                                                    data-simbolo="<? echo $mon['simbolo'] ?>"><?= $mon['nombre'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <div class="col-md-1 text-right"><label for=""
                                                                                    class="control-label">Tasa:</label>
                                            </div>

                                            <div class="col-md-1">
                                                <input type="text" name="tasa_id" id="tasa_id"
                                                       onkeydown="return soloDecimal4(this, event);"
                                                       value="<?php if (isset($ingreso->tasa_soles)) {
                                                           echo $ingreso->tasa_soles;
                                                       } ?>" class='form-control'>

                                                <input type="hidden" name="moneda_id" id="moneda_id"
                                                       value="<?php if (isset($ingreso->id_moneda)) {
                                                           echo $ingreso->id_moneda;
                                                       } ?>">
                                            </div>

                                        </div>


                                        <div class="col-md-2">
                                            <a id="config_moneda" data-action="1" class="btn btn-primary"
                                               data-placement="bottom"
                                               style="margin-top:-2.2%;cursor: pointer;">Confirmar</a>
                                        </div>

                                        <br><br>

                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- END FILA DE LA MONEDA ************************************************************-->


                        <!-- FILA DE SELECIONAR EL PRODUCTO ************************************************************-->
                        <div class="section-border">

                            <span class="section-text-header">Agregue sus Productos</span>

                            <div class="row">
                                <div class="col-md-12">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="control-group">

                                        <div class="col-md-3 text-right">
                                            <label class="control-label">Seleccione el Producto:</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <select name="cboProducto" id="cboProducto"
                                                        class='cho form-control'
                                                        required="true">
                                                    <option value="">Seleccione</option>
                                                    <?php if (count($lstProducto) > 0): ?>
                                                        <?php foreach ($lstProducto as $pd): ?>
                                                            <option
                                                                    value="<?php echo $pd['producto_id']; ?>"
                                                                    data-impuesto="<?= $pd['porcentaje_impuesto'] ?>">

                                                                <?php $barra = $barra_activa->activo == 1 && $pd['producto_codigo_barra'] != "" ? "CB: " . $pd['producto_codigo_barra'] : "" ?>
                                                                <?php echo getCodigoValue(sumCod($pd['producto_id']), $pd['producto_codigo_interno']) . ' - ' . $pd['producto_nombre'] . ' ' . $barra; ?></option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                    <?php endif; ?>
                                                </select>
                                                <a class="input-group-addon btn-warning" data-toggle="tooltip" title="Agregar Producto" data-original-title="Agregar Producto" href="#" onclick="nuevoProducto()">
                                                    <i class="hi hi-plus-sign"></i>
                                                </a>
                                                <a id="refresh_productos" href="#" class="input-group-addon btn-default">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                                <input type="hidden" id="hiden_local">
                                            </div>
                                        </div>

                                        <div class="col-md-3 text-right">
                                            <button id="agregar_gasto" type="button" class="btn btn-info">
                                              <i class="fa fa-plus"></i>  Agregar Gasto
                                            </button>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="col-md-2"></div>
                                    </div>
                                </div>
                            </div>
                            <br>


                            <!-- END FILA DE SELECIONAR EL PRODUCTO ************************************************************-->

                            <!-- FILA OCULTA DE IMPUESTO ************************************************************-->
                            <?php if ($costos === 'true'): ?>


                                <div class="control-group" style="display: none;">
                                    <div class="col-md-2"><label class="control-label">Impuesto:</label></div>
                                    <div class="col-md-4">
                                        <select name="impuestos" id="impuestos" class='cho form-control'
                                                required="true" style='visibility: hidden'>
                                            <option value="0">Seleccione</option>
                                            <?php if (count($impuestos) > 0) { ?>
                                                <?php foreach ($impuestos as $impuesto) { ?>
                                                    <option
                                                            value="<?php echo $impuesto['porcentaje_impuesto']; ?>" <?php if (strtoupper($impuesto['nombre_impuesto']) == "IGV") echo 'selected' ?>><?php echo $impuesto['nombre_impuesto'] ?></option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                            <?php endif ?>
                            <!-- END FILA OCULTA DE IMPUESTO ************************************************************-->


                            <!-- FILA PARA AGREGAR PRODUCTOS ************************************************************-->
                            <div class="row" id="loading" style="display: none;">
                                <div class="col-md-12 text-center">
                                    <div class="loading-icon"></div>
                                </div>
                            </div>

                            <div class="row" style="display: none;" id="mostrar_totales">
                                <div id="producto_form" class="col-md-10 text-center"></div>
                                <div class="col-md-2">
                                    <div class="row">
                                        <div class="col-md-4"><label class="control-label">TOTAL:</div>
                                        <div class="col-md-8"><input id="total_unidades" type="text"
                                                                     class="form-control text-center" value="0"
                                                                     readonly></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div id="um_minimo" class="col-md-8 text-center">Unidades</div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">

                                <div class="col-md-12 form_div" style="display: none;">
                                    <div class="col-md-2 text-right"><label class="control-label">Costo
                                            Unitario:</label></div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-addon tipo_tasa"></div>
                                            <input type="text" style="text-align: right;"
                                                   class='form-control'
                                                   name="precio" id="precio" value="0.00"
                                                   onkeydown="return soloDecimal4(this, event);"
                                                <?= !validOption('PRECIO_INGRESO', 'COSTO', 'IMPORTE') ? 'readonly' : '' ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-right"><label class="control-label">SubTotal:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-addon tipo_tasa"></div>
                                            <input type="text" style="text-align: right;"
                                                   class='form-control' <?php if (isset($ingreso->id_ingreso) and $facturar == "SI") { ?>
                                                value="<?= $ingreso->sub_total_ingreso ?>"
                                            <?php } else {
                                                echo !validOption('PRECIO_INGRESO', 'IMPORTE', 'IMPORTE') ? 'readonly' : '';
                                            } ?>
                                                   name="total_precio" id="total_precio" value="0.00"
                                                   onkeydown="return soloDecimal4(this, event);">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-10"></div>
                                <div class="col-md-2 text-right" id="botonconfirmar" style="display: none">
                                    <a class="btn btn-primary" data-placement="bottom"
                                       style="margin-top:-2.2%;cursor: pointer;"
                                       onclick="agregarProducto();">Agregar</a><br>
                                    <span style="color: #999999; font-size: 9px;">[Ctrl + Enter]</span>
                                </div>
                            </div>
                        </div>
                        <!-- END FILA PARA AGREGAR PRODUCTOS ************************************************************-->

                        <!-- FILA PARA VER LOS DETALLES DE LOS PRODUCTOS ************************************************************-->
                        <div class="section-border">
                            <span class="section-text-header">Detalle de los Productos</span>
                            <div class="row-fluid">
                                <div id="producto_min_unidad" style="display: none;"></div>
                                <div class="span12">
                                    <div class="box">
                                        <div class="box-head">

                                        </div>
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
                        </div>
                        <!-- END FILA PARA VER LOS DETALLES DE LOS PRODUCTOS ************************************************************-->

                        <!-- FILA DE TOTALES ************************************************************-->

                        <div class="section-border"
                             style="display: <?php echo $costos === 'true' ? 'block' : 'none' ?>">
                            <span class="section-text-header">Totales</span>
                            <div class="row">

                                <div class="control-group"
                                     style=" <?php if ($costos === 'false') echo 'display:none' ?>">
                                    <div class="col-md-3">
                                        <label for="subTotal" class="control-label">SubTotal:</label>

                                        <div class="controls">
                                            <div class="input-prepend input-append">
                                                <div class="input-group">
                                                    <div class="input-group-addon tipo_tasa"></div>
                                                    <input style="text-align: right;" type="text"
                                                           class='input-square input-small form-control'
                                                           name="subTotal"
                                                           id="subTotal" <?php if (isset($ingreso->id_ingreso) and $facturar == "SI") { ?>
                                                        value="<?= $ingreso->sub_total_ingreso ?>"
                                                    <?php } else {
                                                        echo "readonly";
                                                    } ?>/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="control-group"
                                     style=" <?php if ($costos === 'false') echo 'display:none' ?>">
                                    <div class="col-md-3">
                                        <label for="montoigv" class="control-label">Total Impuesto:</label>

                                        <div class="controls">
                                            <div class="input-prepend input-append">
                                                <div class="input-group">
                                                    <div class="input-group-addon tipo_tasa"></div>
                                                    <input style="text-align: right;" type="text"
                                                           class='input-square input-small form-control'
                                                           name="montoigv"
                                                           id="montoigv" <?php if (isset($ingreso->id_ingreso) and $facturar == "SI") { ?>
                                                        value="<?= $ingreso->sub_total_ingreso ?>"
                                                    <?php } else {
                                                        echo "readonly";
                                                    } ?>/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="control-group"
                                     style=" <?php if ($costos === 'false') echo 'display:none' ?>">
                                    <div class="col-md-3">
                                        <label class="control-label">Total a Pagar:</label>

                                        <div class="controls">
                                            <div class="input-prepend input-append">
                                                <div class="input-group">
                                                    <div class="input-group-addon tipo_tasa"></div>
                                                    <input style="text-align: right;" type="text"
                                                           class='input-square input-small form-control'
                                                           name="totApagar"
                                                           id="totApagar" <?php if (isset($ingreso->id_ingreso) and $facturar == "SI") { ?>
                                                        value="<?= $ingreso->total_ingreso ?>"
                                                    <?php } else {
                                                        echo "readonly";
                                                    } ?>/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($costos === 'true'): ?>
                                    <div class="control-group">
                                        <div class="col-md-3">
                                            <label class="control-label">Calculo de Impuesto:</label>
                                            <select id="tipo_impuesto" name="tipo_impuesto" class="form-control">
                                                <option value="1">Incluye impuesto</option>
                                                <option value="2">Agregar impuesto</option>
                                                <option value="3">No considerar impuesto</option>
                                            </select>

                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                        <!-- END FILA DE TOTALES ************************************************************-->

                        <br>
                    </div>

                </div>


                <div class="block-options">

                    <div class="form-actions">

                        <button class="btn" id="btnGuardarCompra"
                                type="button"><i
                                    class="fa fa-save fa-3x text-info fa-fw"></i> <br>F6 Guardar
                        </button>
                        <!-- <button type="button" class="btn"><i class="fa fa-folder-open-o fa-3x text-info"></i><br>Abrir </button>-->
                        <?php if (!isset($ingreso->id_ingreso)) { ?>
                            <button class="btn" id="reiniciar"
                                    onclick="confirmDialog('reiniciar_res(<?= $costos ?>);');"><i
                                        class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
                            </button>
                        <?php } ?>
                        <button class="btn" type="button" onclick="confirmDialog('cancelarIngreso();');"><i
                                    class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="dialog_compra_credito" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
                 aria-hidden="true">

                <?php echo isset($dialog_compra_credito) ? $dialog_compra_credito : '' ?>

            </div>

        </form>
    </div>


</div>

<div class="modal fade" id="confirmarmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
                <p>Est&aacute; seguro que desea registrar el ingreso de los productos seleccionados?</p>
                <input type="hidden" name="id" id="id_borrar">

            </div>
            <div class="modal-footer">
                <button type="button" id="botonconfirmar_save" class="btn btn-primary" onclick="guardaringreso()">
                    F6 Confirmar
                </button>
                <button type="button" class="btn btn-danger" id="cerrar_confirmar" onclick="cerrar_confirmar()">
                    Cancelar
                </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>


<div class="modal fade" id="producto_serie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Agregar Numeros de Series</h4>
            </div>
            <div id="producto_serie_body" class="modal-body">


            </div>

            <div class="modal-footer">
                <button type="button" id="submitcolumnas" class="btn btn-primary" onclick="save_serie_listaProducto();">
                    Confirmar
                </button>
                <input type="button" id="cerrar_numero_series" class="btn btn-default" value="Cancelar"/>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>


<div class="modal fade" id="modificarcantidad" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodificarcantidad" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Editar cantidad</h4> <h5 id="nombreproduto2"></h5>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">Unidad:</div>
                        <div class="col-md-10">
                            <select name="unidadedit" id="unidadedit" class='cho form-control'>


                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">Cantidad:</div>
                        <div class="col-md-10">
                            <input type="number" id="cantidadedit" class="form-control"
                                   onkeydown="return soloDecimal3(this, event);">
                        </div>
                    </div>
                </div>
                <?php if ($costos === 'true'): ?>
                    <div class="row">
                        <div class="form-group">

                            <div class="col-md-2">Total:</div>
                            <div class="col-md-10">
                                <input type="number" id="totaledit" class="form-control"
                                       onkeydown="return soloDecimal3(this, event);">
                            </div>
                        </div>
                    </div>
                <?php endif ?>


            </div>

            <div class="modal-footer">

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" type="button" id="guardarcantidad"><i
                                    class="fa fa-save"></i>Guardar
                        </button>

                        <button class="btn btn-default closemodificarcantidad" type="button"><i
                                    class="fa fa-close"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade conf" id="confirm_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="confirmDialog(false);" data-dismiss="conf"
                        aria-hidden="true">&times;
                </button>
                <h4 id="confirm_title" class="modal-title">Confirmaci&oacute;n</h4>
            </div>
            <div class="modal-body">

                <p id="confirm_msg" style="text-align: justify;">Si continuas perderas todos los cambios realizados.
                    Estas Seguro?</p>

                <div id="confirm_function" style="display: none;" data-function="0"></div>
            </div>

            <div class="modal-footer">
                <button type="button" id="confirm_ok" class="btn btn-primary">
                    Confirmar
                </button>
                <button type="button" id="confirm_no" class="btn btn-warning" onclick="confirmDialog(false);">
                    Cancelar
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<div class="modal fade" id="loading_save_compra" tabindex="-1" role="dialog" style="top: 50px;"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">
    <div class="row" id="loading">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="productomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="agregarproveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="agregarmarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="agregarfamilia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>
<div class="modal fade" id="agregarlinea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<div class="modal fade" id="dialog_gastos_modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

</div>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    var ruta = '<?php echo $ruta; ?>';

    $(function () {
        $("select").chosen({width: '100%'});
        $("#fecEmision").datepicker({format: 'dd-mm-yyyy'});
        //TablesDatatables.init();
        $("#agregarproveedor").load(ruta + 'proveedor/form');
        $("#agregarmarca").load(ruta + 'marca/form');
        $("#agregargrupo").load(ruta + 'grupo/form');
        $("#agregarfamilia").load(ruta + 'familia/form');
        $("#agregarlinea").load(ruta + 'linea/form');
    });

    function agregarfamilia() {
        $("#formagregarfamilia").trigger("reset");
        $('#agregarfamilia').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_familia').removeAttr("onclick");
            $('#confirmar_boton_familia').attr("onclick", "guardar_familia('producto')");

        }, 10);
    }

    function agregarmarca() {
        $("#formagregarmarca").trigger("reset");
        $('#agregarmarca').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_marca').removeAttr("onclick");
            $('#confirmar_boton_marca').attr("onclick", "guardar_marca('producto')");

        }, 10);
    }

    function agregargrupo() {
        $("#formagregargrupo").trigger("reset");
        $('#agregargrupo').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_grupo').removeAttr("onclick");
            $('#confirmar_boton_grupo').attr("onclick", "guardar_grupo('producto')");
        }, 10);
    }

    function agregarproveedor() {
        $("#formagregarproveedor").trigger("reset");
        $('#agregarproveedor').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_proveedor').removeAttr("onclick");
            $('#confirmar_boton_proveedor').attr("onclick", "guardar_proveedor('producto')");
        }, 10);
    }

    function agregarlinea() {
        $("#formagregarlinea").trigger("reset");
        $('#agregarlinea').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_linea').removeAttr("onclick");
            $('#confirmar_boton_linea').attr("onclick", "guardar_linea('producto')");
        }, 10);
    }

    function update_proveedor(id, nombre) {
        $('#cboProveedor').append('<option value="' + id + '">' + nombre + '</option>');
        $('#cboProveedor').val(id)
        $("#cboProveedor").trigger('chosen:updated');
    }
</script>
