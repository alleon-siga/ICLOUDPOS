<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Proveedor</li>
    <li><a href="">Calendario Cuentas x Pagar</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Ubicacion:</label>
                    <select name="local_id" id="local_id" class='cho form-control'>
                        <option value="0">TODOS</option>
                        <?php if (count($locales) > 0): ?>
                            <?php foreach ($locales as $local): ?>
                                <option
                                    value="<?= $local['int_local_id']; ?>"
                                    <?= $local['int_local_id'] == $this->session->userdata('id_local') ? 'selected' : ''?>>
                                    <?= $local['local_nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else : ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Proveedor:</label>
                    <select name="proveedor" id="proveedor" class='cho form-control'>
                        <option value="0">TODOS</option>
                        <?php if (count($lstproveedor) > 0): ?>
                            <?php foreach ($lstproveedor as $cl): ?>
                                <option
                                        value="<?php echo $cl['id_proveedor']; ?>"><?php echo $cl['proveedor_nombre']; ?></option>
                            <?php endforeach; ?>
                        <?php else : ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Moneda:</label>
                    <select name="moneda" id="moneda" class='cho form-control'>
                        <?php foreach ($monedas as $moneda): ?>
                            <option value="<?= $moneda->id_moneda ?>"
                                    data-simbolo="<?= $moneda->simbolo ?>"><?= $moneda->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Tipo:</label>
                    <select name="tipo" id="tipo" class='cho form-control'>
                        <option value="0">TODOS</option>
                        <option value="COMPRA">COMPRA</option>
                        <option value="GASTO">GASTO</option>
                    </select>
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
            <br>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <script src='<?= $ruta ?>recursos/js/fullcalendar.min.js'></script>
            <script src='<?= $ruta ?>recursos/js/es.js'></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                $(document).ready(function () {
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

                    $("#btn_buscar, .btn_buscar").on("click", function () {
                        getReporte();
                    });
                });

                function getReporte() {
                    $("#historial_list").html($("#loading").html());

                    var data = {
                        'local_id': $("#local_id").val(),
                        'proveedor': $("#proveedor").val(),
                        'moneda': $("#moneda").val(),
                        'tipo': $("#tipo").val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>proveedor/calendarioCuentasPagar/filter',
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
