<div id="base_contenido">
    <?php $md = get_moneda_defecto()?>
<?php $ruta = base_url(); ?>

    <script>

        var VAR = new Object;
        VAR.base = '<?php echo $ruta; ?>';

    </script>


    <script src="<?php echo $ruta; ?>recursos/js/generarventa.js"></script>
<div class="row ">

    <div class="col-md-8">
        <div class="table-responsive block">
            <?php if (count($locales) > 1): ?>
                <span style="float: right; margin-bottom: 5px;"><input type="checkbox" id="desglose"
                                                                       checked="checked"> <b>Mostrar
                        Detalles</b></span>
            <?php endif; ?>
            <div id="" class="table-responsive" style="height: 400px;    overflow-y: auto;">
                <table class="table dataTable dataTables_filter table-bordered">
                    <thead id="theadproductos">

                    </thead>
                    <tbody id="tbodyproductos">

                    <?php
                    $countproductos = 0;

                    foreach ($venta as $ven) {

                        ?>
                        <script type="text/javascript">

                            var nombre = "<?php echo $ven["nombre"]; ?>";

                            calculatotales(<?php echo $ven['producto_id'] ?>, encodeURIComponent(nombre), '<?php echo $ven["nombre_unidad"] ?>', <?php echo $ven['cantidad'] ?>, <?php echo $ven['preciounitario'] ?>, <?php echo $ven['importe'] ?>, <?php echo $ven['porcentaje_impuesto'] ?>, <?php echo $countproductos?>, <?php echo $ven['unidades'] ?>, '<?php echo $ven["producto_cualidad"]?>', <?php echo $ven['id_unidad'] ?>,'<?php echo $ven['local_nombre'] ?>');
                            addProductoToArray(<?php echo $ven['producto_id'] ?>, encodeURIComponent(nombre), <?php echo $ven['id_unidad'] ?>, '<?php echo $ven["nombre_unidad"] ?>', <?php echo $ven['cantidad'] ?>, <?php echo $ven['preciounitario'] ?>, <?php echo $ven['importe'] ?>, <?php echo $ven['unidades'] ?>, '<?php echo $ven["producto_cualidad"]?>', <?php echo $ven['porcentaje_impuesto'] ?>);

                        </script>
                        <?php $countproductos++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4 block panel-venta-left">
        <div class="row">
            <div class="form-group">
                <div class="col-md-5">
                    <label for="lblMoneda" class="control-label panel-admin-text">Moneda:</label>
                </div>
                <div class="col-md-7">
                    <div>
                        <?php if (isset($devolver)): ?>
                            <input type="text" id="monedas" name="monedas_nombre" readonly
                                   value="<?php echo $moneda['nombre'] ?>" class="form-control">
                            <input type="hidden" id="monedas" name="monedas"
                                   value="<?php echo $venta[0]['id_moneda'] ?>">
                        <?php else: ?>

                            <select class="form-control" id="monedas"
                                    name="monedas" <?php echo isset($devolver) ? "readonly" : NULL ?>
                                    onchange="change_moneda();">
                                <?php foreach ($monedas as $mon) { ?>
                                    <option
                                        data-tasa="<?php echo $mon['tasa_soles'] ?>"
                                        data-simbolo="<?php echo $mon['simbolo'] ?>"
                                        data-oper="<?php echo $mon['ope_tasa'] ?>"
                                        value="<?= $mon['id_moneda'] ?>"><?= $mon['nombre'] ?></option>
                                <?php } ?>
                            </select>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="block_tasa" style="display:none;" class="row">
            <div class="form-group">
                <div class="col-md-5">
                    <label for="lblMoneda" class="control-label panel-admin-text">Tasa de la moneda:</label>
                </div>
                <div class="col-md-7">
                    <div>
                        <?php if (isset($devolver)): ?>
                            <input type="text" id="monedas" name="monedas_nombre" readonly
                                   value="<?php echo $moneda['nombre'] ?>" class="form-control">
                            <input type="hidden" id="monedas" name="monedas"
                                   value="<?php echo $venta[0]['id_moneda'] ?>">
                        <?php else: ?>
                            <div class="input-prepend input-append input-group">
                                <label class="input-group-addon"><?= $md->simbolo ?></label>
                                <input type="text" value="0" id="moneda_tasa"
                                       onkeypress="$('#update_moneda').show();"
                                       class="form-control" onkeydown="return soloDecimal3(this, event);">
                                <a id="update_moneda" onclick="re_calcularTotales();" style="display: none;"
                                   class="input-group-addon" href="#"><i class="fa fa-refresh"></i></a>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="subtotal_show" style="display: none;">
            <div class="form-group">
                <div class="col-md-5">
                    <label for="subTotal" class="control-label panel-admin-text">Sub-Total:</label>
                </div>

                <div class="col-md-7">
                    <div class="input-prepend input-append input-group">
                        <label id="lblSim1" class="input-group-addon"><?= $md->simbolo ?></label>
                        <input type="text"
                               class='input-square input-small form-control'
                               name="subTotal" id="subTotal" readonly value="0.00">
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="impuesto_show" style="display: none;">
            <div class="form-group">
                <div class="col-md-5">
                    <label for="montoigv" class="control-label panel-admin-text">Impuesto:</label>
                </div>
                <div class="col-md-7">

                    <div class="input-prepend input-append input-group">

                        <label id="lblSim2" class="input-group-addon"><?= $md->simbolo ?></label>
                        <input type="text" class='input-square input-small form-control' name="montoigv"
                               id="montoigv" readonly value="0.00">

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-md-5">
                    <label class="control-label panel-admin-text">Total:</label>
                </div>
                <div class="col-md-7">
                    <div class="input-prepend input-append input-group">
                        <label id="lblSim3" class="input-group-addon"><?= $md->simbolo ?></label><input style="font-size: 14px;
    font-weight: bolder;" type="text"
                                                                                                  class='input-square input-small form-control'
                                                                                                  name="totApagar"
                                                                                                  id="totApagar"
                                                                                                  readonly
                                                                                                  value="0.00">
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="condiciones_pago_div"
             style="display: <?php echo isVentaActivo() ? "none;" : 'block;' ?>">
            <div class="form-group">
                <div class="col-md-5">
                    <label class="control-label panel-admin-text">Pago</label>

                </div>
                <div class="col-md-7">


                    <?php if (isset($devolver)) { ?>
                        <input type="hidden" name="condicion_pago"
                               value="<?php echo $venta[0]['id_condiciones'] ?>">
                    <?php } ?>
                    <select name="condicion_pago" id="cboModPag"
                            onchange="activarText_ModoPago()" <?php if (isset($devolver)) echo 'disabled' ?>
                            class="form-control">
                        <?php if (count($condiciones_pago) > 0): ?>

                            <?php foreach ($condiciones_pago as $lc): ?>

                                <option
                                    value="<?php echo $lc['id_condiciones']; ?>"
                                    <?php if ((isset($venta[0]['id_condiciones']) and $venta[0]['id_condiciones'] == $lc['id_condiciones']) OR (!isset($venta[0]['id_condiciones']) and $lc['dias'] == 0)) echo 'selected' ?>>
                                    <?php echo $lc['nombre_condiciones'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (count($condiciones_pago) > 0): ?>

                        <?php foreach ($condiciones_pago as $lc): ?>
                            <input type="hidden" id="diascondicionpago<?= $lc['id_condiciones']; ?>"
                                   value="<?= $lc['dias']; ?>"/>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="form-group">
                <div class="col-md-5">

                    <label for="tipo_documento" class="control-label panel-admin-text">Tipo Documento:</label>
                </div>
                <div class="col-md-7">

                    <?php if (isset($devolver)) { ?>
                        <input type="hidden" name="tipo_documento"
                               value="<?php echo $venta[0]['nombre_tipo_documento'] ?>">
                    <?php } ?>
                    <select name="tipo_documento" id="tipo_documento"
                            class="form-control" <?php if (isset($devolver)) echo 'disabled' ?>>
                        <?php foreach ($tipos_documento as $key => $value): ?>
                            <?php $gf = $this->session->userdata('GENERAR_FACTURACION') == "SI" ? "SI" : "NO" ?>
                            <?php if ($gf == "SI" and ($value->id_doc == 1 or $value->id_doc == 3 or $value->id_doc == 6)): ?>
                                <option
                                    <?php echo $value->id_doc == 3 ? 'selected="selected"' : '' ?>
                                    value="<?php echo $value->id_doc ?>" <?php if (isset($venta[0]['nombre_tipo_documento'])
                                    and $venta[0]['nombre_tipo_documento'] == $value->id_doc
                                ): echo "selected"; endif; ?> ><?php echo $value->des_doc ?></option>

                            <?php elseif ($gf == "NO" and $value->id_doc == 6): ?>
                                <option
                                    <?php echo $value->id_doc == 6 ? 'selected="selected"' : '' ?>
                                    value="<?php echo $value->id_doc ?>" <?php if (isset($venta[0]['nombre_tipo_documento'])
                                    and $venta[0]['nombre_tipo_documento'] == $value->id_doc
                                ): echo "selected"; endif; ?> ><?php echo $value->des_doc ?></option>

                            <?php endif; ?>
                        <?php endforeach ?>
                        <!--  <option
                                    value="<?= BOLETAVENTA ?>" <?php if (isset($venta[0]['nombre_tipo_documento']) and $venta[0]['nombre_tipo_documento'] == BOLETAVENTA) echo 'selected' ?>><?= BOLETAVENTA ?></option>
                                <option
                                    value="<?= NOTAVENTA ?>" <?php if ((isset($venta[0]['nombre_tipo_documento']) and $venta[0]['nombre_tipo_documento'] == NOTAVENTA) OR !isset($venta[0]['nombre_tipo_documento'])) echo 'selected' ?>><?= NOTAVENTA ?></option>
                                <option
                                    value="<?= FACTURA ?>" <?php if (isset($venta[0]['nombre_tipo_documento']) and $venta[0]['nombre_tipo_documento'] == FACTURA) echo 'selected' ?>><?= FACTURA ?></option> -->
                    </select>

                </div>
            </div>

        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-md-5">
                    <label class="control-label panel-admin-text">Estado:</label>

                </div>
                <div class="col-md-7">

                    <?php if (isset($devolver)) { ?>
                        <input type="hidden" id="venta_status" name="venta_status"
                               value="<?php echo $venta[0]['venta_status'] ?>">
                    <?php } ?>
                    <select
                        name="venta_status" <?php if (!isset($devolver)): echo 'id="venta_status"'; endif; ?> <?php if (isset($devolver)) echo 'disabled' ?>
                        class="form-control">


                        <?php if (isVentaActivo()): ?>
                            <option
                                selected="selected"
                                value="COBRO">
                                COBRAR EN CAJA
                            </option>
                        <?php else: ?>
                            <option
                                value="<?= COMPLETADO ?>" <?php if ((isset($venta[0]['venta_status']) && $venta[0]['venta_status'] == COMPLETADO)) echo 'selected' ?>><?= COMPLETADO ?></option>
                        <?php endif;?>
                        <option
                            value="<?= ESPERA ?>" <?php if (isset($venta[0]['venta_status']) && $venta[0]['venta_status'] == ESPERA) echo 'selected' ?>><?= ESPERA ?></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-md-5">

                    <label for="cboTipDoc" class="control-label panel-admin-text">Fecha:</label>
                </div>
                <div class="col-md-7">

                    <input type="text" class="form-control" readonly id="fecha" name="fecha"
                           value="<?= isset($venta[0]['fechaemision']) ? $venta[0]['fechaemision'] : date('d/m/Y') ?>">

                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <div class="col-md-5">
                    <label class="control-label panel-admin-text">Total Productos:</label>

                </div>
                <div class="col-md-7">

                    <input type="hidden" id="totales_totApagar" value="0">
                    <input type="hidden" id="totales_montoigv" value="0">
                    <input type="hidden" id="totales_subTotal" value="0">

                    <input type="text" readonly id="totalproductos" class="form-control" value="0">
                </div>
            </div>
        </div>


    </div>


    <div class="modal fade" id="generarventa1" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">
        <!-- TERMINAR VENTA CREDITO -->

        <?php echo isset($dialog_terminar_venta_credito) ? $dialog_terminar_venta_credito : '' ?>
    </div>


    <div class="modal fade" id="generarventa" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

        <!-- TERMINAR VENTA CONTADO -->

        <?php echo isset($dialog_terminar_venta_contado) ? $dialog_terminar_venta_contado : '' ?>

    </div>

    <div class="modal fade" id="generarventa_caja" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
         aria-hidden="true">

        <!-- TERMINAR VENTA CAJA -->

        <?php echo isset($dialog_venta_caja) ? $dialog_venta_caja : '' ?>

    </div>

    <div class="modal fade" id="venta_terminada" tabindex="-1" role="dialog"
         aria-labelledby="mostrar_errores"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pedido Registrado Correctamente</h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-2"><h3>ID: </h3></div>
                        <div class="col-md-10">
                            <h3><span id="num_venta"></span></h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2"><h3>Venta: </h3></div>
                        <div class="col-md-10">
                            <h3><span id="num_doc"></span></h3>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2"><h3>Cliente: </h3></div>
                        <div class="col-md-10">
                            <h3><span id="nombre_cliente"></span></h3>
                        </div>
                    </div>

                </div>


                <div class="modal-footer">

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-default " onclick="cerrar_venta();" type="button"><i
                                    class="fa fa-close"></i> Ok
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="modal fade" id="formgarante" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <!-- TERMINAR VENTA CONTADO -->

        <?php echo isset($dialog_nuevo_garante) ? $dialog_nuevo_garante : '' ?>
    </div>


    <div class="modal fade bs-example-modal-lg" id="seleccionunidades" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">

        <!-- EXISTENCIA DEL PRODUCTO-->
        <?php echo isset($dialog_existencia_producto) ? $dialog_existencia_producto : '' ?>
    </div>

</div>
</form>
<div class="block-options">

    <div class="form-actions">

        <button class="btn" id="terminarventa" type="button"><i
                class="fa fa-save fa-3x text-info fa-fw"></i> <br>F6
            Guardar
        </button>
        <button type="button" class="btn" id="abrirventas"><i
                class="fa fa-folder-open-o fa-3x text-info fa-fw"></i><br>Abrir
        </button>
        <button type="button" class="btn" id="reiniciar"><i class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
        </button>
        <button class="btn" type="button" id="cancelar"><i
                class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar
        </button>
    </div>
</div>
</div>


<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

<div class="modal fade" id="mvisualizarVentaCredito" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <label>Un Label</label>
    <button>Imprimir</button>
</div>

<div class="modal fade" id="ventasabiertas" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

<div class="modal fade" id="modificarcantidad" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodificarcantidad" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Editar cantidad</h4> <h5 id="nombreproduto2"></h5>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">Cantidad:</div>
                        <div class="col-md-3">
                            <input type="number" id="cantidadedit" class="form-control"
                                   onkeydown="return soloDecimal3(this, event);">
                            <input type="number" id="precioedit" class="form-control"
                                   onkeydown="return soloDecimal3(this, event);">
                        </div>
                    </div>
                </div>

            </div>
            <input type="hidden" value="<?= $this->session->userdata('VENTA_DEFAULT'); ?>" id="valor_defecto">

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


<div class="modal fade" id="mostrar_errores" tabindex="-1" role="dialog"
     aria-labelledby="mostrar_errores"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close " onclick="$('#mostrar_errores').modal('hide')"
                        aria-hidden="true">&times;</button>
                <h4 class="modal-title">A ocurrido un error:</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12 error_ventana">

                    </div>
                </div>

            </div>


            <div class="modal-footer">

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default " onclick="$('#mostrar_errores').modal('hide')" type="button"><i
                                class="fa fa-close"></i> Ok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="agregarcliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="ventanaimpresion" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js?<?php echo date('Hms'); ?>"></script>


<script>
    countproducto = <?= $countproductos?>;

    update_view();

    $("#venta_status").change(function(){
        if($("#venta_status").val() != "COBRO")
            $("#condiciones_pago_div").show();
        else
            $("#condiciones_pago_div").hide();
    });



    $("#tipo_documento").change(function (e) {
        if ($(this).val() == '1') {
            $("#subtotal_show").show();
            $("#impuesto_show").show();
        }
        else {
            $("#subtotal_show").hide();
            $("#impuesto_show").hide();
        }
    });

    <?php if ($show_precio_new == "SI"): ?>
    $("#price_check").click(function (e) {
        if ($("#price_check").prop("checked")) {
            $("#show_price_new").show();
        }
        else {
            $("#show_price_new").hide();
            $("#price_new").val('');
        }

    });

    $(".closeseleccionunidades").click(function (e) {
        $("#price_check").prop('checked', false);
        $("#price_new").val('');
        $("#show_price_new").hide();
    });
    <?php endif;?>

    var id_ubicacion = $("#id_local").val();
    $("#id_local").change(function (e) {
        e.preventDefault();
        if (!confirm("Si cambias la ubicacion perderas los cambios de la venta actual. Estas seguro?")) {
            $("#id_local").val(id_ubicacion);
            $("#id_local").trigger("chosen:updated");
            return false;
        }

        $.ajax({
            type: "GET",
            url: '<?php echo site_url('venta/index')?>' + '/' + $("#id_local").val(),
            //data: 'page=' + url,	//with the page number as a parameter
            dataType: "html",	//expect html to be returned
            success: function (msg) {

                if (parseInt(msg) != 0)	//if no errors
                {

                    $('#page-content').html(msg);	//load the returned html into pageContet
                    // $('#loading').css('visibility', 'hidden');	//and hide the rotating gif
                    //inizializePlugins();
                    //$('#barloadermodal').modal('hide');
                    id_ubicacion = $("#id_local").val();
                }
            }

        });
    });

</script>



</div>