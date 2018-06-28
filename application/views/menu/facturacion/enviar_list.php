<?php if ($emisor == NULL): ?>
    <h4 class="alert alert-danger text-center">Emisor no configurado</h4>
<?php else: ?>

    <?php if (count($boletas) == 0 && count($facturas) == 0): ?>
        <?php if ($estado == 1): ?>
            <h4 class="alert alert-info text-center">No tienes comprobantes por emitir en el
                dia <?= str_replace('-', '/', $fecha) ?></h4>
        <?php elseif ($estado == 2): ?>
            <h4 class="alert alert-info text-center">No hay comprobantes pendientes a respuesta</h4>
        <?php endif; ?>
    <?php else: ?>

        <?php $ruta = base_url(); ?>
        <?php $md = get_moneda_defecto() ?>
        <div class="row">
            <div class="col-md-6">
                <?php if ($estado == 1): ?>
                    <h4><strong>N&uacute;mero del resumen:</strong> <span
                                id="resumen_numero"><?= $resumen_numero ?></span>
                    </h4>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-right">
                <?php if ($estado == 1): ?>
                    <button id="enviar_sunat" type="button" class="btn btn-primary emitir_sunat">
                        <i class="fa fa-mail-forward"></i> EMITIR COMPROBANTES A SUNAT
                    </button>
                <?php elseif ($estado == 2): ?>
                    <button id="actualizar_sunat" type="button" class="btn btn-warning emitir_sunat">
                        <i class="fa fa-refresh"></i> ACTUALIZAR COMPROBANTES ENVIADOS
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h3>Resumen de Boletas y Notas Asociadas</h3>

                <div class="table-responsive">
                    <table class='table table-striped dataTable table-bordered no-footer tableStyle'
                           style="overflow:scroll">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Numero</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($boletas) > 0): ?>

                            <?php foreach ($boletas as $f): ?>
                                <tr>
                                    <td><?= $f->id ?></td>
                                    <td><?php
                                        if ($f->documento_tipo == '01') echo 'FACTURA';
                                        if ($f->documento_tipo == '03') echo 'BOLETA';
                                        if ($f->documento_tipo == '07') echo 'NOTA DE CREDITO';
                                        if ($f->documento_tipo == '08') echo 'NOTA DE DEBITO';
                                        ?></td>
                                    <td><?= $f->documento_numero ?>
                                    </td>
                                    <td style="white-space: nowrap;">

                                        <?php
                                        $estado = '';
                                        $estado_class = '';
                                        if ($f->estado == 0) {
                                            $estado_class = 'label-warning';
                                            $estado = 'NO GENERADO';
                                        } elseif ($f->estado == 1) {
                                            $estado_class = 'label-info';
                                            $estado = 'GENERADO';
                                        } elseif ($f->estado == 2) {
                                            $estado_class = 'label-warning';
                                            $estado = 'ENVIADO';
                                        } elseif ($f->estado == 3) {
                                            $estado_class = 'label-success';
                                            $estado = 'ACEPTADO';
                                        } elseif ($f->estado == 4) {
                                            $estado_class = 'label-danger';
                                            $estado = 'RECHAZADO';
                                        }

                                        ?>
                                        <div
                                                title="Descripci&oacute;n del Estado" data-content="<?= $f->nota ?>"
                                                data-toggle="popover"
                                                class="label <?= $estado_class ?>"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            <?= $estado ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($f->total, 2) ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" data-toggle="tooltip"
                                           style="margin-right: 5px;"
                                           title="Ver Detalles" data-original-title="Ver Detalles"
                                           href="#"
                                           onclick="ver('<?= $f->id ?>');">
                                            <i class="fa fa-list"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>

                        </tbody>
                    </table>

                </div>
            </div>

            <div class="col-md-6">
                <h3>Facturas y Notas Asociadas</h3>
                <div class="table-responsive">
                    <table class='table table-striped dataTable table-bordered no-footer tableStyle'
                           style="overflow:scroll">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Numero</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($facturas) > 0): ?>

                            <?php foreach ($facturas as $f): ?>
                                <tr>
                                    <td><?= $f->id ?></td>
                                    <td><?php
                                        if ($f->documento_tipo == '01') echo 'FACTURA';
                                        if ($f->documento_tipo == '03') echo 'BOLETA';
                                        if ($f->documento_tipo == '07') echo 'NOTA DE CREDITO';
                                        if ($f->documento_tipo == '08') echo 'NOTA DE DEBITO';
                                        ?></td>
                                    <td><?= $f->documento_numero ?>
                                    </td>
                                    <td style="white-space: nowrap;">

                                        <?php
                                        $estado = '';
                                        $estado_class = '';
                                        if ($f->estado == 0) {
                                            $estado_class = 'label-warning';
                                            $estado = 'NO GENERADO';
                                        } elseif ($f->estado == 1) {
                                            $estado_class = 'label-info';
                                            $estado = 'GENERADO';
                                        } elseif ($f->estado == 2) {
                                            $estado_class = 'label-warning';
                                            $estado = 'ENVIADO';
                                        } elseif ($f->estado == 3) {
                                            $estado_class = 'label-success';
                                            $estado = 'ACEPTADO';
                                        } elseif ($f->estado == 4) {
                                            $estado_class = 'label-danger';
                                            $estado = 'RECHAZADO';
                                        }

                                        ?>
                                        <div
                                                title="Descripci&oacute;n del Estado" data-content="<?= $f->nota ?>"
                                                data-toggle="popover"
                                                class="label <?= $estado_class ?>"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            <?= $estado ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($f->total, 2) ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" data-toggle="tooltip"
                                           style="margin-right: 5px;"
                                           title="Ver Detalles" data-original-title="Ver Detalles"
                                           href="#"
                                           onclick="ver('<?= $f->id ?>');">
                                            <i class="fa fa-list"></i>
                                        </a>
                                        <?php if ($f->estado == 1): ?>
                                            <a class="btn btn-sm btn-default" data-toggle="tooltip" style="margin-right: 5px;"
                                               title="Emitir comprobante a SUNAT" data-original-title="Emitir comprobante a SUNAT"
                                               href="#"
                                               onclick="emitir_by_id('<?= $f->id ?>');">
                                                <i class="fa fa-mail-forward"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($f->estado == 2): ?>
                                            <a class="btn btn-sm btn-warning" data-toggle="tooltip" style="margin-right: 5px;"
                                               title="Actualizar estado" data-original-title="Actualizar estado"
                                               href="#"
                                               onclick="emitir_by_id('<?= $f->id ?>');">
                                                <i class="fa fa-refresh"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>


        <div class="modal fade" id="dialog_venta_detalle" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
             aria-hidden="true">

        </div>


        <script type="text/javascript">
            var total = 0;
            var index_p = 0;
            var res_pendiente_total = 0;
            $(function () {

                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover({
                    trigger: 'hover'
                });

                $('#exportar_excel').on('click', function (e) {
                    e.preventDefault();
                    exportar_excel();
                });

                $("#exportar_pdf").on('click', function (e) {
                    e.preventDefault();
                    exportar_pdf();
                });

                TablesDatatables.init(0);

                $('.emitir_sunat').on('click', function () {

                    var fecha = $('#date_range').val();
                    var estado = $('#estado').val();
                    var msg = 'Emitiendo comprobantes a la sunat del dia ' + fecha;

                    var mensaje_estado = $('#mensaje_estado');
                    var mensaje_box = $('#mensaje_box');

                    mensaje_estado.html(msg);
                    mensaje_box.html('<div>- ' + msg + '</div>');

                    $('#btn_cerrar').attr('disabled', 'disabled');
                    $('#resumen_modal').modal('show');

                    $.ajax({
                        url: '<?php echo $ruta . 'facturacion/get_comprobantes'; ?>',
                        type: 'POST',
                        data: {fecha: fecha, estado: estado, local_id: $('#local_id').val()},
                        headers: {
                            Accept: 'application/json'
                        },
                        success: function (data) {
                            res_pendiente_total = data.resumen_pendiente.length;
                            total = data.facturas.length + (data.boletas.length > 0 && estado == 1 ? 2 : 1) + res_pendiente_total;
                            set_progess(++index_p, total);

                            var actualizar = function () {
                                if (res_pendiente_total > 0) {
                                    msg = 'Actualizando resumenes pendientes';
                                    mensaje_estado.html(msg);
                                    mensaje_box.append('<div>- ' + msg + '</div>');

                                    actualizar_resumen(data.resumen_pendiente, 0, function (err) {
                                        if (err == false) {
                                            if (data.boletas.length > 0 && estado == 1)
                                                emitir_resumen(fecha);
                                            else {
                                                msg = 'Emision terminada';
                                                mensaje_estado.html(msg);
                                                mensaje_box.append('<div>- ' + msg + '</div>');

                                                $('#btn_cerrar').removeAttr('disabled');
                                            }
                                        }
                                    });
                                }
                                else {
                                    if (data.boletas.length > 0 && estado == 1)
                                        emitir_resumen(fecha);
                                    else {
                                        msg = 'Emision terminada';
                                        mensaje_estado.html(msg);
                                        mensaje_box.append('<div>- ' + msg + '</div>');

                                        $('#btn_cerrar').removeAttr('disabled');
                                    }
                                }
                            };

                            if (data.facturas != undefined && data.facturas.length > 0) {
                                emitir(data.facturas, 0, function (err) {
                                    if (err == false) {
                                        actualizar();
                                    }

                                });
                            }
                            else {
                                actualizar();
                            }
                        },
                        error: function () {
                            alert('Ha ocurrido un error interno.');
                            $('#btn_cerrar').removeAttr('disabled');
                        }
                    });

                });

            });


            function ver(id) {

                $("#dialog_venta_detalle").html($("#loading").html());
                $("#dialog_venta_detalle").modal('show');

                $.ajax({
                    url: '<?php echo $ruta . 'facturacion/get_facturacion_detalle'; ?>',
                    type: 'POST',
                    data: {'id': id},

                    success: function (data) {
                        $("#dialog_venta_detalle").html(data);
                    },
                    error: function () {
                        alert('Error inesperado')
                    }
                });
            }

            function emitir_resumen(fecha) {
                var msg = 'Emitiendo resumen diario ' + $('#resumen_numero').html();

                var mensaje_estado = $('#mensaje_estado');
                var mensaje_box = $('#mensaje_box');

                mensaje_estado.html(msg);
                mensaje_box.append('<div>- ' + msg + '</div>');
                $.ajax({
                    url: '<?php echo $ruta . 'facturacion/emitir_resumen'; ?>',
                    type: 'POST',
                    data: {fecha: fecha, estado: $('#estado').val(), local_id: $('#local_id').val()},

                    success: function (data) {
                        set_progess(++index_p, total);
                        if (data.resp.CODIGO == 0) {
                            mensaje_estado.html(data.resp.MENSAJE);
                            mensaje_box.append('<div class="text-success">- ' + data.resp.MENSAJE + '</div>');
                        }
                        else {
                            mensaje_estado.html(data.resp.MENSAJE);
                            mensaje_box.append('<div class="text-danger">- ' + data.resp.MENSAJE + '</div>');
                        }

                        msg = 'Emision terminada';
                        mensaje_estado.html(msg);
                        mensaje_box.append('<div>- ' + msg + '</div>');

                        $('#btn_cerrar').removeAttr('disabled');

                    },
                    error: function () {
                        alert('Ha ocurrido un error interno.');
                        $('#btn_cerrar').removeAttr('disabled');
                    }
                });

            }


            function emitir(comprobantes, index, cb) {
                if (index < comprobantes.length) {
                    var msg = 'Emitiendo comprobante ' + comprobantes[index].documento_numero;

                    var mensaje_estado = $('#mensaje_estado');
                    var mensaje_box = $('#mensaje_box');

                    mensaje_estado.html(msg);
                    mensaje_box.append('<div>- ' + msg + '</div>');
                    $.ajax({
                        url: '<?php echo $ruta . 'facturacion/emitir_comprobante'; ?>',
                        type: 'POST',
                        data: {'id': comprobantes[index].id},

                        success: function (data) {
                            set_progess(++index_p, total);
                            if (data.facturacion.estado == 3) {
                                mensaje_estado.html(data.facturacion.nota);
                                mensaje_box.append('<div class="text-success">- ' + data.facturacion.nota + '</div>');
                            }
                            else {
                                mensaje_estado.html(data.facturacion.nota);
                                mensaje_box.append('<div class="text-danger">- ' + data.facturacion.nota + '</div>');
                            }

                            emitir(comprobantes, ++index, cb)
                        },
                        error: function () {
                            alert('Ha ocurrido un error interno.');
                            $('#btn_cerrar').removeAttr('disabled');
                            cb(true);
                        }
                    });

                }
                else {
                    cb(false);
                }
            }

            function actualizar_resumen(comprobantes, index, cb) {
                if (index < comprobantes.length) {
                    var msg = 'Actualizando Resumen ' + comprobantes[index].numero;
                    comprobantes[index].documento_numero;

                    var mensaje_estado = $('#mensaje_estado');
                    var mensaje_box = $('#mensaje_box');

                    mensaje_estado.html(msg);
                    mensaje_box.append('<div>- ' + msg + '</div>');
                    $.ajax({
                        url: '<?php echo $ruta . 'facturacion/actualizar_resumen'; ?>',
                        type: 'POST',
                        data: {'id': comprobantes[index].id},

                        success: function (data) {
                            set_progess(++index_p, total);
                            if (data.resumen.estado != 4) {
                                mensaje_estado.html(data.resumen.nota);
                                mensaje_box.append('<div class="text-success">- ' + data.resumen.nota + '</div>');
                            }
                            else {
                                mensaje_estado.html(data.resumen.nota);
                                mensaje_box.append('<div class="text-danger">- ' + data.resumen.nota + '</div>');
                            }

                            actualizar_resumen(comprobantes, ++index, cb)
                        },
                        error: function () {
                            alert('Ha ocurrido un error interno.');
                            $('#btn_cerrar').removeAttr('disabled');
                            cb(true);
                        }
                    });

                }
                else {
                    cb(false);
                }
            }

            function emitir_by_id(id) {

                $("#barloadermodal").modal('show');

                $.ajax({
                    url: '<?php echo $ruta . 'facturacion/emitir_comprobante'; ?>',
                    type: 'POST',
                    data: {'id': id},

                    success: function (data) {

                        if (data.facturacion.estado == 3) {
                            show_msg('success', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota);
                        }
                        else {
                            show_msg('danger', '<h4>Facturacion Electronica:</h4> ' + data.facturacion.nota);
                        }

                        $("#barloadermodal").modal('hide');
                        get_facturacion();
                    },
                    error: function () {
                        alert('Error inesperado');
                        $("#barloadermodal").modal('hide');
                    }
                });
            }

            function set_progess(index, total) {
                var p = $('#mensaje_progress');
                var val = (index * 100 / total).toFixed(0);
                p.html(val + '%');
                p.attr('aria-valuenow', val);
                p.css('width', val + '%');
            }
        </script>
    <?php endif; ?>
<?php endif; ?>