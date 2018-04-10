<style>
    .totales {
        width: 100%;
        text-align: right;
    }

    .totales tr td {
        padding: 5px 0;
        font-weight: bold;
    }
</style>
<input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
<?php if ($detalle == 'venta'): ?>
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalles de la Venta <?= $venta_action == 'caja' ? 'a Cobrar' : '' ?></h3>
            </div>
            <div class="modal-body">
                <div class="row-fluid force-margin">
                    <?php if ($venta->condicion_id == '1'): ?>
                        <div class="row-fluid">
                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                                <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label
                                            class="control-label">Documento:</label>
                                </div>
                                <div
                                        class="col-md-3">
                                    <?php
                                    $doc = '';
                                    if ($venta->documento_id == 1) $doc = "FA";
                                    if ($venta->documento_id == 2) $doc = "NC";
                                    if ($venta->documento_id == 3) $doc = "BO";
                                    if ($venta->documento_id == 4) $doc = "GR";
                                    if ($venta->documento_id == 5) $doc = "PCV";
                                    if ($venta->documento_id == 6) $doc = "NP";
                                    if ($venta->numero != '')
                                        echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                                    else
                                        echo '<span style="color: #0000FF">NO FACTURADO</span>';
                                    ?>
                                </div>
                            </div>

                            <hr class="hr-margin-5">

                            <?php if ($venta->comprobante_id > 0): ?>
                                <div class="row">
                                    <div class="col-md-2"><label class="control-label">Comprobante:</label></div>
                                    <div class="col-md-3"><?= $venta->comprobante_nombre ?></div>

                                    <div class="col-md-1"></div>

                                    <div class="col-md-2"><label
                                                class="control-label">Comp. Nro.:</label></div>
                                    <div
                                            class="col-md-3"><?= $venta->comprobante ?></div>
                                </div>

                                <hr class="hr-margin-5">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                                <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_nombre ?></div>
                            </div>

                            <hr class="hr-margin-5">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                                <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                                <div class="col-md-3"><?= $venta->condicion_nombre ?></div>
                            </div>

                            <hr class="hr-margin-5">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                                <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_tasa ?></div>
                            </div>

                            <hr class="hr-margin-5">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Estado:</label></div>
                                <div class="col-md-3"><?= $venta->venta_estado ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Venta Total:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->total ?></div>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th><?= getCodigoNombre() ?></th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>UM</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($venta->detalles as $detalle): ?>
                                    <tr>
                                        <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                        <td><?= $detalle->producto_nombre ?></td>
                                        <td><?= $detalle->cantidad ?></td>
                                        <td><?= $detalle->unidad_nombre ?></td>
                                        <td style="text-align: right"><?= $detalle->precio ?></td>
                                        <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>


                    <?php if ($venta->condicion_id == '2'): ?>
                        <div class="row-fluid">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                                <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label
                                            class="control-label">Documento:</label>
                                </div>
                                <div
                                        class="col-md-3">
                                    <?php
                                    $doc = '';
                                    if ($venta->documento_id == 1) $doc = "FA";
                                    if ($venta->documento_id == 2) $doc = "NC";
                                    if ($venta->documento_id == 3) $doc = "BO";
                                    if ($venta->documento_id == 4) $doc = "GR";
                                    if ($venta->documento_id == 5) $doc = "PCV";
                                    if ($venta->documento_id == 6) $doc = "NP";
                                    if ($venta->numero != '')
                                        echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                                    else
                                        echo '<span style="color: #0000FF">NO FACTURADO</span>';
                                    ?>
                                </div>
                            </div>

                            <hr class="hr-margin-5">

                            <?php if ($venta->comprobante_id > 0): ?>
                                <div class="row">
                                    <div class="col-md-2"><label class="control-label">Comprobante:</label></div>
                                    <div class="col-md-3"><?= $venta->comprobante_nombre ?></div>

                                    <div class="col-md-1"></div>

                                    <div class="col-md-2"><label
                                                class="control-label">Comp. Nro.:</label></div>
                                    <div
                                            class="col-md-3"><?= $venta->comprobante ?></div>
                                </div>

                                <hr class="hr-margin-5">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                                <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_nombre ?></div>
                            </div>

                            <hr class="hr-margin-5">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                                <div class="col-md-3"><?= $venta->condicion_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Importe de Deuda:</label></div>
                                <div
                                        class="col-md-3">
                                    <?= $venta->moneda_simbolo ?> <?= $venta_action == 'caja' ? $venta->total : $venta->credito_pendiente ?>
                                </div>
                            </div>

                            <hr class="hr-margin-5">
                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                                <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Importe Inicial:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->inicial ?></div>
                            </div>

                            <hr class="hr-margin-5">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                                <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_tasa ?></div>
                            </div>

                            <hr class="hr-margin-5">

                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Estado:</label></div>
                                <div class="col-md-3"><?= $venta->venta_estado ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label class="control-label">Venta Total:</label></div>
                                <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->total ?></div>
                            </div>

                            <hr class="hr-margin-5">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    Dias de Gracia: <?= $venta->periodo_gracia ?> /
                                    Numero de Cuotas: <?= count($venta->cuotas) ?> /
                                    Tasa de Interes: <?= $venta->tasa_interes ?>%
                                </div>

                            </div>
                            <br>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th><?= getCodigoNombre() ?></th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>UM</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($venta->detalles as $detalle): ?>
                                    <tr>
                                        <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                        <td><?= $detalle->producto_nombre ?></td>
                                        <td><?= $detalle->cantidad ?></td>
                                        <td><?= $detalle->unidad_nombre ?></td>
                                        <td style="text-align: right"><?= $detalle->precio ?></td>
                                        <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <br>
                    <div class="row">
                        <div class="col-md-8">
                            <?php if (count($kardex) > 0): ?>
                                <h4>Anulaciones</h4>
                                <?php foreach ($kardex as $k): ?>
                                    <h5><a href="javascript:ver_nc('<?= $venta->venta_id ?>','<?= $venta->local_id ?>')"><?= 'NC ' . $k->serie . ' - ' . $k->numero ?></a></h5>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if ($venta->condicion_id == '2'): ?>
                                <h4>Cuotas y Vencimientos</h4>
                                <table class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th>Letra</th>
                                        <th>Vence</th>
                                        <th>Monto</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($venta->cuotas as $cuota): ?>
                                        <tr>
                                            <td><?= $cuota->nro_letra ?></td>
                                            <td><?= date('d/m/Y', strtotime($cuota->fecha_vencimiento)) ?></td>
                                            <td><?= $venta->moneda_simbolo . ' ' . number_format($cuota->monto, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif ?>

                            <?php if ($venta->nota != NULL): ?>
                                <h4>Notas:</h4>
                                <?= $venta->nota ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-right">
                            <table class="totales">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->subtotal, 2) ?></label></td>
                                </tr>
                                <tr>
                                    <td>Descuento:</td>
                                    <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->descuento, 2) ?></label></td>
                                </tr>
                                <tr>
                                    <td>Impuesto:</td>
                                    <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->impuesto, 2) ?></label></td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></label></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer" align="right">
                <div class="row">
                    <div class="text-right">
                        <div class="col-md-12">
                            <input type="button" class='btn btn-default' value="Cerrar"
                                   data-dismiss="modal">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php elseif ($detalle == 'devolver'): ?>
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Devolver Venta <span
                            id="venta_numero"><?= sumCod($venta->venta_id, 6) ?></span></h3>
                <input type="hidden" id="tipo_impuesto" value="<?= $venta->tipo_impuesto ?>">
            </div>
            <div class="modal-body">
                <div class="row-fluid force-margin">

                    <div class="row-fluid">
                        <div class="row" style="font-size: 15px;">
                            <div class="col-md-2"><label class="control-label">Total Pagado:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo ?> <span
                                        id="total_pagado"
                                        data-documento="<?= $venta->documento_id ?>"
                                        data-subtotal="<?= $venta->subtotal ?>">
                                    <?= $venta->total ?>
                                </span></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-3"><label class="control-label">Total Devolver:</label></div>
                            <div id="total_devolver_text" class="col-md-3"><?= $venta->moneda_simbolo ?> <span
                                        id="total_devolver">0.00</span></div>
                        </div>

                        <hr class="hr-margin-5">

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th><?= getCodigoNombre() ?></th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Devolver</th>
                                <th>UM</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($venta->detalles as $detalle): ?>
                                <tr class="producto_detalles_list"
                                    data-id="<?= $detalle->detalle_id ?>"
                                    data-producto_id="<?= $detalle->producto_id ?>"
                                    data-unidad_id="<?= $detalle->unidad_id ?>">
                                    <td id="producto_codigo_<?= $detalle->detalle_id ?>"><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                    <td id="producto_nombre_<?= $detalle->detalle_id ?>"><?= $detalle->producto_nombre ?></td>
                                    <td id="cantidad_<?= $detalle->detalle_id ?>"
                                        data-cantidad="<?= $detalle->cantidad ?>"><?= $detalle->cantidad ?></td>
                                    <td style="width: 150px;">
                                        <input class="form-control devolver_input"
                                               id="cantidad_devuelta_<?= $detalle->detalle_id ?>"
                                               data-id="<?= $detalle->detalle_id ?>"
                                               type="number"
                                               style="text-align: center;"
                                               min="0"
                                               max="<?= $detalle->cantidad ?>"
                                               data-impuesto="<?= $detalle->impuesto_porciento ?>"
                                               value="0">
                                    </td>
                                    <td id="unidad_nombre_<?= $detalle->detalle_id ?>"><?= $detalle->unidad_nombre ?></td>
                                    <td id="precio_<?= $detalle->detalle_id ?>" style="text-align: right">
                                        <?= $detalle->precio ?>
                                    </td>
                                    <td style="text-align: right; width: 150px;"><?= $venta->moneda_simbolo ?>
                                        <span id="subtotal_<?= $detalle->detalle_id ?>" class="subtotales">
                                            <?= $detalle->importe ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

            <div class="modal-footer" align="right">
                <div class="row">
                    <div class="text-right">

                        <div class="col-md-12">
                            <input id="devolver_venta_button" type="button" class='btn btn-primary' value="Devolver">

                            <input type="button" class='btn btn-danger' value="Cancelar"
                                   data-dismiss="modal">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        $('.devolver_input').bind('keyup change click mouseleave', function () {
            var id = $(this).attr('data-id');
            var impuesto = parseFloat($(this).attr('data-impuesto'));
            var tipo_impuesto = $('#tipo_impuesto').val();
            var devolver = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());
            var cantidad = isNaN(parseFloat($('#cantidad_' + id).attr('data-cantidad'))) ? 0 : parseFloat($('#cantidad_' + id).attr('data-cantidad'));
            var precio = parseFloat($('#precio_' + id).html().trim());

            var factor = parseFloat((100 + impuesto) / 100);

            var cantidad_td = $('#cantidad_' + id);
            var subtotal_td = $('#subtotal_' + id);

            cantidad_td.html(parseFloat(cantidad - devolver).toFixed(2));
            subtotal_td.html(parseFloat((cantidad - devolver) * precio).toFixed(2));

            var subtotales = 0;
            $('.subtotales').each(function () {
                subtotales += parseFloat($(this).html())
            });

            if (tipo_impuesto == 2) {
                subtotales = subtotales * factor;
            }

            var total_devolver = parseFloat($('#total_pagado').html() - subtotales);

            $('#total_devolver').html(formatPrice(total_devolver));
        });

        $('.devolver_input').on('focus', function () {
            $(this).select();
        });

        $('#devolver_venta_button').on('click', function () {

            if (!validar_venta())
                return false;

            var template = '<h3>Devoluvi&oacute;n de la Venta ' + $('#venta_numero').html().trim() + '</h3>';
            template += '<hr class="hr-margin-10">';
            template += '<h4><label>Productos Devueltos:</label></h4>';
            $('.producto_detalles_list').each(function () {
                var id = $(this).attr('data-id');
                var producto_codigo = $('#producto_codigo_' + id).html().trim();
                var producto_nombre = $('#producto_nombre_' + id).html().trim();
                var unidad_nombre = $('#unidad_nombre_' + id).html().trim();
                var cantidad_devuelta = $('#cantidad_devuelta_' + id).val();

                if (cantidad_devuelta != 0 && cantidad_devuelta != "") {
                    template += '<div class="row">';
                    template += '<div class="col-md-8">' + producto_codigo + ' - ' + producto_nombre + '</div>';
                    template += '<div class="col-md-4">' + cantidad_devuelta + ' ' + unidad_nombre + '</div>';
                    template += '</div>';
                    template += '<hr class="hr-margin-5">';
                }
            });
            template += '<hr class="hr-margin-10">';
            template += '<h4><label>Total a devolver:</label> ' + $('#total_devolver_text').html().trim() + '</h4>';

            $('#confirm_venta_text').html(template);
            $('#confirm_venta_button').attr('onclick', 'devolver_venta();');

            $("#documento_serie").val("");
            $("#documento_numero").val("");

            $('#dialog_venta_confirm').modal('show');
        });

        function validar_venta() {
            var flag = true;
            var n = 0;
            $('.producto_detalles_list').each(function () {
                var id = $(this).attr('data-id');
                var cantidad = parseFloat($('#cantidad_' + id).html());
                var old_cantidad = parseFloat($('#cantidad_' + id).attr('data-cantidad'));

                if (cantidad < 0) {
                    $.bootstrapGrowl('<h4>Error.</h4> <p>No puede hacer una devoluci&oacute;n mayor a la cantidad.</p>', {
                        type: 'warning',
                        delay: 5000,
                        allow_dismiss: true
                    });
                    $('#cantidad_devuelta_' + id).trigger('focus');
                    flag = false;
                    return false;
                }

                if (cantidad == old_cantidad)
                    n++;

            });
            if (n == $('.producto_detalles_list').length) {
                $.bootstrapGrowl('<h4>Error.</h4> <p>Por favor devuelva una cantidad.</p>', {
                    type: 'warning',
                    delay: 5000,
                    allow_dismiss: true
                });
                $('#cantidad_devuelta_' + id).trigger('focus');
                return false;
            }

            var total_importe = parseFloat($("#total_pagado").html()) - parseFloat($("#total_devolver").html());
            if (total_importe == 0) {
                $.bootstrapGrowl('<h4>Error.</h4> <p>No puede devolver la totalidad de la venta. Realice una anulacion.</p>', {
                    type: 'warning',
                    delay: 5000,
                    allow_dismiss: true
                });
                return false;
            }
            return flag;
        }

        function devolver_venta() {
            if ($("#documento_serie").val() == "" || $("#documento_numero").val() == "") {
                show_msg('warning', 'Complete la serie y numero del documento');
                return false;
            }

            $("#confirm_venta_text").html($("#loading").html());

            var venta_id = $("#venta_id").val();

            var total_importe = parseFloat($("#total_pagado").html()) - parseFloat($("#total_devolver").html());
            if ($("#total_pagado").attr('data-documento') == '1') {
                var subtotales = 0;
                $('.subtotales').each(function () {
                    subtotales += parseFloat($(this).html())
                });
                total_importe = parseFloat($("#total_pagado").attr('data-subtotal')) - subtotales;
            }

            var devoluciones = prepare_devolucion();

            $.ajax({
                url: '<?php echo base_url() . 'venta_new/devolver_venta'; ?>',
                type: 'POST',
                data: {
                    'venta_id': venta_id,
                    'total_importe': total_importe,
                    'devoluciones': devoluciones,
                    'serie': $("#documento_serie").val(),
                    'numero': $("#documento_numero").val()
                },

                success: function () {
                    $('#dialog_venta_confirm').modal('hide');
                    $('#dialog_venta_detalle').modal('hide');
                    $(".modal-backdrop").remove();
                    $.bootstrapGrowl('<h4>Correcto.</h4> <p>Venta devuelta con exito.</p>', {
                        type: 'success',
                        delay: 5000,
                        allow_dismiss: true
                    });
                    get_ventas();
                },
                error: function () {

                    $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                        type: 'danger',
                        delay: 5000,
                        allow_dismiss: true
                    });
                    $('#dialog_venta_confirm').modal('show');

                    $('#devolver_venta_button').click();
                }
            });
        }

        function prepare_devolucion() {
            var devoluciones = [];

            $('.producto_detalles_list').each(function () {
                var id = $(this).attr('data-id');
                var devolver = isNaN(parseFloat($('#cantidad_devuelta_' + id).val())) ? 0 : parseFloat($('#cantidad_devuelta_' + id).val());
                var devolucion = {};

                if (devolver != 0) {
                    devolucion.detalle_id = id;
                    devolucion.producto_id = $(this).attr('data-producto_id');
                    devolucion.unidad_id = $(this).attr('data-unidad_id');
                    devolucion.devolver = devolver;
                    devolucion.new_cantidad = parseFloat($('#cantidad_' + id).html());
                    devolucion.new_importe = parseFloat($('#subtotal_' + id).html());
                    devolucion.precio = parseFloat($('#precio_' + id).html());
                    devoluciones.push(devolucion);
                }
            });

            return JSON.stringify(devoluciones);
        }
    </script>
<?php endif; ?>