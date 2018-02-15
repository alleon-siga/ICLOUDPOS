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
                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
                    </div>
                    <div class="col-md-3">
                        <?php if (isset($locales)): ?>
                            <select id="venta_local" class="form-control filter-input">
                                <?php foreach ($locales as $local): ?>
                                    <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                            value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-1">

                    </div>

                    <div class="col-md-3" style="display: <?= $venta_action != 'caja' ? 'block' : 'none' ?>">
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                               name="daterange" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>

                    </div>


                    <div class="col-md-1" style="display: none;">
                        <label class="control-label panel-admin-text">Estado:</label>
                    </div>
                    <div class="col-md-3" style="display: none;">
                        <select
                                id="venta_estado" <?= $venta_action == 'caja' ? 'disabled' : '' ?>
                                class="form-control filter-input" name="venta_estado">
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


                            $.ajax({
                                url: '<?= base_url()?>venta_new/get_pendientes',
                                data: {
                                    'local_id': local_id,
                                    'estado': estado
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

                    $("#year, #dia_min, #dia_max").bind('keyup change click', function () {
                        $("#historial_list").html('');
                    });

                    $(".filter-input").bind('keyup change click', function () {
                        $("#historial_list").html('');
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


                    $.ajax({
                        url: '<?= base_url()?>venta_new/get_ventas/<?=$venta_action?>',
                        data: {
                            'local_id': local_id,
                            'fecha': fecha,
                            'estado': estado
                        },
                        type: 'POST',
                        success: function (data) {
                            $("#historial_list").html(data);

                            $('#exportar_pdf').attr('href', $('#exportar_pdf').attr('data-href') + local_id + '/' + estado + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);
                            $('#exportar_excel').attr('href', $('#exportar_excel').attr('data-href') + local_id + '/' + estado + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);

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


                function generar_reporte_excel() {

                    document.getElementById("frmExcel").submit();
                }

                function generar_reporte_pdf() {
                    document.getElementById("frmPDF").submit();
                }


            </script>
