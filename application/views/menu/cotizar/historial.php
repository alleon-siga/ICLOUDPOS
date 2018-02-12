<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Cotizaciones</a></li>
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
                        <select name="local_id" id="local_id" class='form-control'>
                            <?php foreach ($locales as $local): ?>
                                <option <?= $local->local_id == $local->local_defecto ? 'selected="selected"' : '' ?>
                                        value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;" name="daterange" value="<?= date('01/m/Y')?> - <?= date('d/m/Y')?>" />

                    </div>
                    <div class="col-md-1">

                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-offset-3 col-md-2 text-right">
                        <button type="button" id="new_cotizacion" class="btn btn-primary">Nueva Cotizacion</button>
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


            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">

                $(function () {

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

                    var date_range = $("#date_range").val();
                    var local_id = $("#local_id").val();


                    $.ajax({
                        url: '<?= base_url()?>cotizar/get_cotizaciones',
                        data: {
                            'date_range': date_range,
                            'local_id': local_id
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


            </script>
