<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Gastos del d&iacute;a</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Moneda</label>
                    <select name="moneda_id" id="moneda_id" class='ctrl form-control'>
                        <?php foreach ($monedas as $moneda): ?>
                            <option value="<?= $moneda->id_moneda ?>"
                                    data-simbolo="<?= $moneda->simbolo ?>"
                                <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $moneda->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Caja</label>
                    <select id="caja_select" name="caja_select" class="ctrl form-control">
                        <?php foreach ($cajas as $caja): ?>
                            <option 
                                    value="<?= $caja->id ?>"
                                    data-moneda_id="<?= $caja->moneda_id ?>"
                                    data-simbolo="<?= $caja->simbolo ?>">
                                <?= $caja->local_nombre ?>
                                <?= ' | ' . $caja->nombre ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Fecha Operaci&oacute;n</label>
                    <input type="text" id="fecha" class="form-control" readonly style="cursor: pointer;" name="fecha" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-md-1">
                    <div style="padding-top: 30px;"></div>
                    <button id="btn_buscar" class="btn btn-default">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            <br>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list">

                    </div>
                </div>
            </div>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                $(document).ready(function () {
                    //CONFIGURACIONES INICIALES
                    App.sidebar('close-sidebar');
                    
                    $('input[name="fecha"]').daterangepicker({
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

                    $('.ctrl').chosen();

                    $("#btn_buscar, .btn_buscar").on("click", function () {
                        getReporte();
                    });

                    $('.chosen-container').css('width', '100%');
                });

                function getReporte() {
                    $("#historial_list").html($("#loading").html());

                    var data = {
                        'caja_id': $("#caja_select").val(),
                        'fecha': $("#fecha").val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>reporte_caja/gastosDia/filter',
                        data: data,
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
            </script>
