<?php $ruta = base_url(); ?>

<input type="hidden" name="venta_action" id="venta_action" value="<?= $venta_action ?>">
<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">
            <?= $venta_action == 'anular' ? 'Anular & Devolver Venta' : '' ?>
            <?= $venta_action == 'caja' ? 'Ventas por Cobrar' : '' ?>
            <?= $venta_action == '' ? 'Historial de Ventas' : '' ?>
        </a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">

            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <?php if (isset($locales)): ?>
                            <label class="control-label panel-admin-text">Ubicaci&oacute;n</label>
                            <select id="venta_local" class="form-control filter-input">
                                <?php foreach ($locales as $local): ?>
                                    <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                            value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-3" style="display: <?= $venta_action != 'caja' ? 'block' : 'none' ?>">
                        <label class="control-label panel-admin-text">Fecha</label>
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                               name="daterange" value="<?= date('d/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                    </div>

                    <div class="col-md-2" style="display: <?= $venta_action != 'caja' ? 'block' : 'none' ?>">
                        <label class="control-label panel-admin-text">Tipo de Pago</label>
                        <select name="condicion_pago_id" id="condicion_pago_id" class='cho form-control'>
                            <option value="">Todos</option>
                            <?php foreach ($condiciones_pagos as $condicion): ?>
                                <option value="<?= $condicion->id_condiciones ?>">
                                    <?= $condicion->nombre_condiciones ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Moneda</label>
                        <select name="moneda_id" id="moneda_id" class='cho form-control'>
                            <?php foreach ($monedas as $moneda): ?>
                                <option value="<?= $moneda->id_moneda ?>"
                                        data-simbolo="<?= $moneda->simbolo ?>"
                                    <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $moneda->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="col-md-1" style="display: none;">
                        <label class="control-label panel-admin-text">Estado:</label>
                    </div>
                    <div class="col-md-3" style="display: none;">
                        <select
                                id="venta_estado" <?= $venta_action == 'caja' ? 'disabled' : '' ?>
                                class="form-control filter-input" name="venta_estado">
                            <option value="">TODOS</option>
                            <option value="COMPLETADO">COMPLETADO</option>
                            <?php if (validOption('ACTIVAR_SHADOW', 1) || validOption('ACTIVAR_FACTURACION_VENTA', 1)): ?>
                                <option value="CERRADA">CERRADA</option>
                            <?php endif; ?>
                            <?php if ($venta_action == 'caja'): ?>
                                <option selected value="CAJA">CAJA</option>
                            <?php endif; ?>
                            <?php if ($venta_action != 'anular'): ?>
                                <option value="ANULADO">ANULADO</option>
                            <?php endif; ?>
                            <!--<option value="DEVUELTO">DEVUELTO</option>-->
                        </select>

                    </div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">
                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>
                        <?php if ($venta_action != 'caja'): ?>
                            <button id="btn_buscar" class="btn btn-default">
                                <i class="fa fa-search"></i>
                            </button>
                        <?php else: ?>
                            <button id="btn_buscar" class="btn btn-default">
                                <i id="caja_class" class="fa fa-search"></i> <span id="total_caja"
                                                                                   class="badge label-primary"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <br>


            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list" class="block">


                    </div>

                </div>
            </div>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
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

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Serie</label>
                                    <input type="text" id="documento_serie" class="form-control">
                                </div>
                                <div class="col-md-5">
                                    <label>Numero</label>
                                    <input type="text" id="documento_numero" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="confirm_venta_button" type="button" class="btn btn-primary">
                                Aceptar
                            </button>

                            <button type="button" class="btn btn-danger"
                                    onclick="$('#dialog_venta_confirm').modal('hide');">
                                Cancelar
                            </button>

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>


            </div>

            <div class="modal fade" id="dialog_venta_contado" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
                 aria-hidden="true">

                <!-- TERMINAR VENTA CONTADO -->

                <?php echo isset($dialog_venta_contado) ? $dialog_venta_contado : '' ?>

            </div>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">

                $(function () {

                    $(document).off('keyup');
                    $(document).off('keydown');

                    $(document).on('keydown', function (e) {
                        if (e.keyCode == 117) {
                            e.preventDefault();
                        }
                    });

                    $(document).on('keyup', function (e) {
                        if (e.keyCode == 117 && $("#dialog_venta_contado").is(":visible") == true) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            $('.save_venta_contado[data-imprimir="1"]').first().click();
                        }
                    });



                    <?php if($venta_action != 'caja'):?>
                    $('input[name="daterange"]').daterangepicker({
                        "locale": {
                            "format": "DD/MM/YYYY",
                            "separator": " - ",
                            "applyLabel": "Aplicar",
                            "cancelLabel": "Cancelar",
                            "fromLabel": "De",
                            "toLabel": "A",
                            "customRangeLabel": "Personalizado",
                            "daysOfWeek": [
                                "Do",
                                "Lu",
                                "Ma",
                                "Mi",
                                "Ju",
                                "Vi",
                                "Sa"
                            ],
                            "monthNames": [
                                "Enero",
                                "Febrero",
                                "Marzo",
                                "Abril",
                                "Mayo",
                                "Junio",
                                "Julio",
                                "Agosto",
                                "Septiembre",
                                "Octubre",
                                "Noviembre",
                                "Diciembre"
                            ],
                            "firstDay": 1
                        }
                    });
                    <?php endif;?>

                    <?php if($venta_action == 'caja'):?>
                    var myVar = setInterval(get_pendientes, 2000);

                    function get_pendientes() {
                        if ($('#venta_action').val() == 'caja') {
                            var local_id = $("#venta_local").val();
                            var estado = $("#venta_estado").val();
                            var moneda_id = $("#moneda_id").val();


                            $.ajax({
                                url: '<?= base_url()?>venta_new/get_pendientes',
                                data: {
                                    'local_id': local_id,
                                    'estado': estado,
                                    'moneda_id': moneda_id
                                },
                                type: 'POST',
                                success: function (data) {
                                    $('#total_caja').val(data);
                                    var caja_r = parseInt(data);
                                    var caja_actual = parseInt($('#tabla_caja > tbody > tr').length)

                                    if (caja_actual < caja_r) {
                                        $('#caja_class').removeClass('fa-search');
                                        $('#caja_class').addClass('fa-refresh');
                                        $('#total_caja').html(caja_r - caja_actual);
                                    }
                                    else {
                                        $('#caja_class').removeClass('fa-refresh');
                                        $('#caja_class').addClass('fa-search');
                                        $('#total_caja').html('');
                                    }
                                },
                                error: function () {

                                }
                            });
                        } else {
                            clearInterval(myVar);
                        }
                    }

                    <?php endif;?>

                    $('select').chosen();

                    get_ventas();

                    $("#btn_buscar").on("click", function () {
                        get_ventas();
                    });

                    $('#vc_forma_pago').chosen({
                        search_contains: true
                    });
                    $('.chosen-container').css('width', '100%');

                });

                function get_ventas() {
                    $("#historial_list").html($("#loading").html());

                    var local_id = $("#venta_local").val();
                    var estado = $("#venta_estado").val();
                    var fecha = $('#date_range').val();
                    var moneda_id = $("#moneda_id").val();
                    var condicion_pago_id = $("#condicion_pago_id").val();


                    $.ajax({
                        url: '<?= base_url()?>venta_new/get_ventas/<?=$venta_action?>',
                        data: {
                            'local_id': local_id,
                            'fecha': fecha,
                            'estado': estado,
                            'moneda_id': moneda_id,
                            'condicion_pago_id': condicion_pago_id
                        },
                        type: 'POST',
                        success: function (data) {
                            $("#historial_list").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#historial_list").html('');
                        }
                    });

                }

                function facturar(venta_id) {

                    $("#dialog_venta_facturar").html($("#loading").html());
                    $("#dialog_venta_facturar").modal('show');

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/get_venta_facturar/' . $venta_action; ?>',
                        type: 'POST',
                        data: {'venta_id': venta_id},

                        success: function (data) {
                            $("#dialog_venta_facturar").html(data);
                        },
                        error: function () {
                            alert('asd')
                        }
                    });
                }

                function cobrar(venta_id) {

                    $("#dialog_venta_detalle").html($("#loading").html());
                    $("#dialog_venta_detalle").modal('show');

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/get_venta_cobro/' . $venta_action; ?>',
                        type: 'POST',
                        data: {'venta_id': venta_id},
                        headers: {
                            Accept: 'application/json'
                        },

                        success: function (data) {
                            $("#caja_venta_id").val(venta_id);
                            if (data.venta.condicion_id == '1')
                                $("#vc_total_pagar").val(formatPrice(data.venta.total));
                            else if (data.venta.condicion_id == '2')
                                $("#vc_total_pagar").val(formatPrice(data.venta.inicial));

                            $("#vc_importe").val(formatPrice($("#vc_total_pagar").val()));
                            $("#vc_vuelto").val(0);
                            $("#vc_num_oper").val('');

                            //le paso el tipo de pago contado pq es un cobro en caja y simplemente lo trata como un importe contado
                            $('#contado_tipo_pago').val('1');

                            $("#dialog_venta_detalle").modal('hide');
                            $("#dialog_venta_contado").modal('show');

                            setTimeout(function () {
                                $("#vc_forma_pago").val('3').trigger("chosen:updated");
                                $("#vc_forma_pago").change();
                            }, 500);


                        }
                    });
                }

                function save_venta_credito() {
                    return false;
                }

                function save_venta_contado(imprimir) {

                    if (isNaN(parseFloat($('#vc_importe').val()))) {
                        show_msg('warning', '<h4>Error. </h4><p>El importe tiene que ser numerico.</p>');
                        setTimeout(function () {
                            $("#vc_importe").trigger('focus');
                        }, 500);
                        return false;
                    }

                    if ($("#vc_forma_pago").val() == '3' && $("#vc_vuelto").val() < 0) {
                        show_msg('warning', '<h4>Error. </h4><p>El importe no puede ser menor que el total a pagar. Recomendamos una venta al Cr&eacute;dito.</p>');
                        setTimeout(function () {
                            $("#vc_importe").trigger('focus');
                        }, 500);
                        return false;
                    }
                    if ($("#vc_forma_pago").val() != '3' && $("#vc_num_oper").val() == '') {
                        show_msg('warning', '<h4>Error. </h4><p>El campo Operaci&oacute;n # es obligatorio.</p>');
                        setTimeout(function () {
                            $("#vc_num_oper").trigger('focus');
                        }, 500);
                        return false;
                    }
                    if (($("#vc_forma_pago").val() == '4' || $("#vc_forma_pago").val() == '8' || $("#vc_forma_pago").val() == '9' || $("#vc_forma_pago").val() == '7') && $("#vc_banco_id").val() == '') {
                        show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar un Banco</p>');
                        setTimeout(function () {
                            $("#vc_banco_id").trigger('focus');
                        }, 500);
                        return false;
                    }


                    var data = {
                        'venta_id': $("#caja_venta_id").val(),
                        'tipo_pago': $("#vc_forma_pago").val(),
                        'importe': $("#vc_importe").val(),
                        'vuelto': $("#vc_vuelto").val(),
                        'num_oper': $("#vc_num_oper").val(),
                        'tarjeta': $("#vc_tipo_tarjeta").val(),
                        'banco': $("#vc_banco_id").val()
                    };

                    $("#loading_save_venta").modal('show');
                    $("#dialog_venta_contado").modal('hide');
                    $('.save_venta_contado').attr('disabled', 'disabled');

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/save_venta_caja/'; ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data) {

                            if (data.success == '1') {
                                show_msg('success', '<h4>Correcto. </h4><p>La venta numero ' + data.venta.venta_id + ' se ha pagado con exito.</p>');
                                if (imprimir == '1') {
                                    $("#dialog_venta_imprimir").html('');
                                    $("#dialog_venta_imprimir").modal('show');

                                    $.ajax({
                                        url: '<?php echo $ruta . 'venta_new/get_venta_previa'; ?>',
                                        type: 'POST',
                                        data: {'venta_id': data.venta.venta_id},

                                        success: function (data) {
                                            $("#dialog_venta_imprimir").html(data);
                                            $("#loading_save_venta").modal('hide');
                                        }
                                    });
                                } else {
                                    get_ventas();
                                }
                            }
                            else {
                                if (data.msg)
                                    show_msg('danger', '<h4>Error. </h4><p>' + data.msg + '</p>');
                                else
                                    show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');

                            }
                        },
                        error: function (data) {

                            show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');
                        },
                        complete: function (data) {
                            $('.save_venta_contado').removeAttr('disabled');
                        }
                    });
                }


                function previa(venta_id) {

                    $("#dialog_venta_imprimir").html($("#loading").html());
                    $("#dialog_venta_imprimir").modal('show');

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/get_venta_previa'; ?>',
                        type: 'POST',
                        data: {'venta_id': venta_id},

                        success: function (data) {
                            $("#dialog_venta_imprimir").html(data);
                        }
                    });
                }

                function cerrar_venta(venta_id) {

                    $("#dialog_venta_cerrar").html($("#loading").html());
                    $("#dialog_venta_cerrar").modal('show');

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/cerrar_venta'; ?>',
                        type: 'POST',
                        data: {'venta_id': venta_id},

                        success: function (data) {
                            $("#dialog_venta_cerrar").html(data);
                        }
                    });
                }


                function anular(venta_id, venta) {

                    $('#confirm_venta_text').attr('data-venta', venta);
                    $('#confirm_venta_text').html('Estas seguro que deseas anular la venta ' + $('#confirm_venta_text').attr('data-venta') + '?');
                    $('#confirm_venta_button').attr('onclick', 'anular_venta("' + venta_id + '");');

                    $("#documento_serie").val("");
                    $("#documento_numero").val("");

                    $('#dialog_venta_confirm').modal('show');
                }

                function anular_venta(venta_id) {

                    if ($("#documento_serie").val() == "" || $("#documento_numero").val() == "") {
                        show_msg('warning', 'Complete la serie y numero del documento');
                        return false;
                    }

                    $("#confirm_venta_text").html($("#loading").html());

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/anular_venta'; ?>',
                        type: 'POST',
                        data: {
                            'venta_id': venta_id,
                            'serie': $("#documento_serie").val(),
                            'numero': $("#documento_numero").val()
                        },

                        success: function (data) {
                            $('#dialog_venta_confirm').modal('hide');
                            $.bootstrapGrowl('<h4>Correcto.</h4> <p>Venta anulada con exito.</p>', {
                                type: 'success',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            get_ventas();
                        },
                        error: function () {
                            $('#confirm_venta_text').html('Estas seguro que deseas anular la venta ' + $('#confirm_venta_text').attr('data-venta') + '?');
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                        }
                    });
                }

                function devolver(venta_id) {

                    $("#dialog_venta_detalle").html($("#loading").html());
                    $("#dialog_venta_detalle").modal('show');

                    $.ajax({
                        url: '<?php echo $ruta . 'venta_new/devolver_detalle'; ?>',
                        type: 'POST',
                        data: {'venta_id': venta_id},

                        success: function (data) {
                            $("#dialog_venta_detalle").html(data);
                        }
                    });
                }


            </script>
