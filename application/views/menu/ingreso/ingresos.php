<?php $ruta = base_url(); ?>
<input id="precio_base" type="hidden" value="<?= valueOption('PRECIO_INGRESO', 'COSTO') ?>">
<input id="producto_cualidad" type="hidden">
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
                                                <option value="">Seleccione</option>
                                                <?php

                                                if (count($locales) > 0) {

                                                    foreach ($locales as $local) {
                                                        ?>
                                                        <option
                                                            value="<?= $local['int_local_id'] ?>"
                                                            <?php if (isset($ingreso->id_ingreso) and $ingreso->local_id == $local['int_local_id']) {
                                                                echo "selected";
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
                                                <option value="<?= DONACION ?>"
                                                    <?php if (isset($ingreso->tipo_ingreso) and $ingreso->tipo_ingreso == DONACION)
                                                        echo "selected"; ?>><?= DONACION ?></option>

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
                                                    <?php if (valueOption('ACTIVAR_SHADOW') == 1 || valueOption('ACTIVAR_FACTURACION_INGRESO') == 1): ?>
                                                        <option value="<?= BOLETAVENTA ?>"
                                                            <?php if (isset($ingreso->tipo_documento) and $ingreso->tipo_documento == BOLETAVENTA)
                                                                echo "selected"; ?>><?= BOLETAVENTA ?></option>
                                                        <option value="<?= FACTURA ?>"
                                                            <?php if (isset($ingreso->tipo_documento) and $ingreso->tipo_documento == FACTURA)
                                                                echo "selected"; ?>><?= FACTURA ?></option>
                                                    <?php endif; ?>
                                                    <option
                                                        value="<?= NOTAVENTA ?>" <?php if (isset($ingreso->tipo_documento) and $ingreso->tipo_documento == NOTAVENTA)
                                                        echo "selected"; ?>><?= NOTAVENTA ?></option>
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
                                                   maxlength="3"
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
                                        <div class="controls">
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
                                        <div class="controls">
                                            <select name="cboProducto" id="cboProducto"
                                                    class='cho form-control'
                                                    required="true">
                                                <option value="">Seleccione</option>
                                                <?php if (count($lstProducto) > 0): ?>
                                                    <?php foreach ($lstProducto as $pd): ?>
                                                        <option
                                                            value="<?php echo $pd['producto_id']; ?>">

                                                            <?php $barra = $barra_activa->activo == 1 && $pd['producto_codigo_barra'] != "" ? "CB: ".$pd['producto_codigo_barra'] : ""?>
                                                            <?php echo getCodigoValue(sumCod($pd['producto_id']), $pd['producto_codigo_interno']) . ' - ' . $pd['producto_nombre'].' '.$barra; ?></option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                <?php endif; ?>
                                            </select>
                                            <input type="hidden" id="hiden_local">
                                        </div>
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

                                        <input type="checkbox"
                                               id="with_igv" value="1"
                                        /> <label for="with_igv" class="control-label">Considerar
                                            IGV</label>

                                    </div>
                                </div>
                            <?php endif ?>

                        </div>
                    </div>
                    <!-- END FILA DE TOTALES ************************************************************-->

                    <br>
                </div>

            </div>


            <div class="block-options">

                <div class="form-actions">

                    <button class="btn" id="btnGuardar"
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
                <button type="button" id="botonconfirmar" class="btn btn-primary" onclick="guardaringreso()"
                >
                    F6 Confirmar
                </button>
                <button type="button" class="btn btn-default" id="cerrar_confirmar" onclick="cerrar_confirmar()">
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
                        aria-hidden="true">&times;</button>
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

<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>


<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>


<script>

    $(function () {

        $("select").chosen({width: '100%'});
        $("#fecEmision").datepicker({format: 'dd-mm-yyyy'});
        //TablesDatatables.init();

    });</script>


<script>

    var ruta = '<?php echo $ruta; ?>';

</script>
