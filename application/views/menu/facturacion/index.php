<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Facturaci&oacute;n</li>
    <li><a href="">Emitir Facturas</a></li>
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
                            <select id="local_id" class="form-control filter-input">
                                <?php foreach ($locales as $local): ?>
                                    <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                            value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-3">
                        <label class="control-label panel-admin-text">Fecha Facturaci&oacute;n</label>
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                               name="daterange" value="<?= date('d/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                    </div>


                    <div class="col-md-1">

                    </div>
                    <div class="col-md-3">
                        <label class="control-label panel-admin-text">Estado:</label>
                        <select id="estado" class="form-control filter-input" name="estado">
                            <option value="">TODOS</option>
                            <option value="0">PENDIENTES</option>
                            <option value="1">FACTURADO</option>
                        </select>

                    </div>

                    <div class="col-md-1"></div>

                    <div class="col-md-2">
                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>
                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>

                </div>
            </div>
            <br>

            <?php if (!isset($emisor)): ?>
                <h4 class="alert alert-danger text-center">Emisor no configurado</h4>
            <?php endif; ?>

            <div class="row-fluid">
                <div class="span12">
                    <div id="facturacion_list" class="block">


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

                    get_facturacion();

                    $("#btn_buscar").on("click", function () {
                        get_facturacion();
                    });

                });

                function get_facturacion() {
                    <?php if (isset($emisor) != NULL): ?>
                    $("#facturacion_list").html($("#loading").html());

                    var local_id = $("#local_id").val();
                    var estado = $("#estado").val();
                    var fecha = $('#date_range').val();
                    $.ajax({
                        url: '<?= base_url()?>facturacion/emision/filter',
                        data: {
                            'local_id': local_id,
                            'fecha': fecha,
                            'estado': estado
                        },
                        type: 'POST',
                        success: function (data) {
                            $("#facturacion_list").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#facturacion_list").html('');
                        }
                    });
                    <?php endif;?>

                }


            </script>
