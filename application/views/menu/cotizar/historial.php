<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Cotizaciones</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">

            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <button type="button" id="new_cotizacion" class="btn btn-primary">Nueva Cotizacion</button>
                    </div>

                    <div class="col-md-1">

                    </div>


                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Estado:</label>
                    </div>
                    <div class="col-md-3">
                        <select
                                id="cotizacion_estado"
                                class="form-control filter-input" name="cotizacion_estado">
                            <option value="PENDIENTE">PENDIENTE</option>
                            <option value="COMPLETADO">COMPLETADO</option>
                        </select>

                    </div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">

                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>

                </div>
            </div>
            <br>

            <div class="row">

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


            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">

                $(function () {

                    $('select').chosen();

                    get_cotizaciones();

                    $("#new_cotizacion").on('click', function () {
                        $('#barloadermodal').modal('show');
                        $.ajax({
                            url: '<?= base_url()?>cotizar',
                            success: function (data) {
                                $('#page-content').html(data);
                                $('#barloadermodal').modal('hide');
                                $(".modal-backdrop").remove();
                            }
                        });
                    });

                    $("#btn_buscar").on("click", function () {
                        get_cotizaciones();
                    });

                    $("#year, #dia_min, #dia_max").bind('keyup change click', function () {
                        $("#historial_list").html('');
                    });

                    $(".filter-input").bind('keyup change click', function () {
                        $("#historial_list").html('');
                    });


                });

                function get_cotizaciones() {

                    $("#historial_list").html($("#loading").html());

                    var estado = $("#cotizacion_estado").val();
                    var mes = $("#mes").val();
                    var year = $("#year").val();
                    var dia_min = $("#dia_min").val();
                    var dia_max = $("#dia_max").val();


                    $.ajax({
                        url: '<?= base_url()?>cotizar/get_cotizaciones',
                        data: {
                            'mes': mes,
                            'year': year,
                            'dia_min': dia_min,
                            'dia_max': dia_max,
                            'estado': estado
                        },
                        type: 'POST',
                        success: function (data) {
                            $("#historial_list").html(data);

                            $('#exportar_pdf').attr('href', $('#exportar_pdf').attr('data-href') + estado + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);
                            $('#exportar_excel').attr('href', $('#exportar_excel').attr('data-href') + estado + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);

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
