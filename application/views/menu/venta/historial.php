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


                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Estado:</label>
                    </div>
                    <div class="col-md-3">
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
                                <i id="caja_class" class="fa fa-search"></i> <span id="total_caja" class="badge label-primary"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <br>

            <div class="row" style="display: <?= $venta_action == 'caja' ? 'none' : 'block' ?>;">

                <div class="col-md-1">
                    <label class="control-label panel-admin-text">Periodo:</label>
                </div>

                <div class="col-md-2">
                    <select
                            id="mes"
                            class="form-control filter-input" name="mes">
                        <option value="01" <?= date('m') == '01' ? 'selected' : '' ?>>Enero</option>
                        <option value="02" <?= date('m') == '02' ? 'selected' : '' ?>>Febrero</option>
                        <option value="03" <?= date('m') == '03' ? 'selected' : '' ?>>Marzo</option>
                        <option value="04" <?= date('m') == '04' ? 'selected' : '' ?>>Abril</option>
                        <option value="05" <?= date('m') == '05' ? 'selected' : '' ?>>Mayo</option>
                        <option value="06" <?= date('m') == '06' ? 'selected' : '' ?>>Junio</option>
                        <option value="07" <?= date('m') == '07' ? 'selected' : '' ?>>Julio</option>
                        <option value="08" <?= date('m') == '08' ? 'selected' : '' ?>>Agosto</option>
                        <option value="09" <?= date('m') == '09' ? 'selected' : '' ?>>Septiembre</option>
                        <option value="10" <?= date('m') == '10' ? 'selected' : '' ?>>Octubre</option>
                        <option value="11" <?= date('m') == '11' ? 'selected' : '' ?>>Noviembre</option>
                        <option value="12" <?= date('m') == '12' ? 'selected' : '' ?>>Diciembre</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
                </div>

                <div class="col-md-2">

                </div>


                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Rango de Dias</label>
                </div>
                <div class="col-md-1">
                    <input type="number" min="1" id="dia_min" name="dia_min" value="1" class="form-control">
                </div>

                <div class="col-md-1">
                    <input type="number" min="1" id="dia_max" name="dia_max" value="31" class="form-control">
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

            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">

                $(function () {

                    <?php if($venta_action == 'caja'):?>
                    var myVar = setInterval(get_pendientes, 2000);

                    function get_pendientes() {
                        if ($('#venta_action').val() == 'caja') {
                            var local_id = $("#venta_local").val();
                            var estado = $("#venta_estado").val();
                            var mes = $("#mes").val();
                            var year = $("#year").val();
                            var dia_min = $("#dia_min").val();
                            var dia_max = $("#dia_max").val();


                            $.ajax({
                                url: '<?= base_url()?>venta_new/get_pendientes',
                                data: {
                                    'local_id': local_id,
                                    'mes': mes,
                                    'year': year,
                                    'dia_min': dia_min,
                                    'dia_max': dia_max,
                                    'estado': estado
                                },
                                type: 'POST',
                                success: function (data) {
                                    $('#total_caja').val(data);
                                    var caja_r = parseInt(data);
                                    var caja_actual = parseInt($('#tabla_caja > tbody > tr').length)

                                    if(caja_actual < caja_r){
                                        $('#caja_class').removeClass('fa-search');
                                        $('#caja_class').addClass('fa-refresh');
                                        $('#total_caja').html(caja_r - caja_actual);
                                    }
                                    else{
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
                    var mes = $("#mes").val();
                    var year = $("#year").val();
                    var dia_min = $("#dia_min").val();
                    var dia_max = $("#dia_max").val();


                    $.ajax({
                        url: '<?= base_url()?>venta_new/get_ventas/<?=$venta_action?>',
                        data: {
                            'local_id': local_id,
                            'mes': mes,
                            'year': year,
                            'dia_min': dia_min,
                            'dia_max': dia_max,
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
