<?php $ruta = base_url(); ?>

<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li><a href="">Reporte de Ingreso</a></li>
</ul>
<div class="block">
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable"
                 style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                <h4><i class="icon fa fa-ban"></i> Error</h4>
                <?php echo isset($error) ? $error : '' ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-success alert-dismissable"
                 style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
                <?php echo isset($success) ? $success : '' ?>
            </div>
        </div>
    </div>
    <?php
    echo validation_errors('<div class="alert alert-danger alert-dismissable">', "</div>");
    ?>
    <!-- Progress Bars Wizard Title -->


    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <?php if (isset($locales)): ?>
                    <select id="local_id" class="form-control">
                        <?php foreach ($locales as $local): ?>
                            <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                    value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

            </div>
            <div class="col-md-3">
                <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                       name="daterange" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>

            </div>

            <div class="col-md-2" id="moneda_block">
                <select
                        id="moneda_id"
                        class="form-control" name="moneda_id">
                    <?php foreach ($monedas as $moneda): ?>
                        <option value="<?= $moneda->id_moneda ?>"
                            <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>>
                            <?= $moneda->nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            </div>

            <div class="col-md-2"></div>
            <!--            <div class="col-md-2">-->
            <!--                <select-->
            <!--                        id="estado"-->
            <!--                        class="form-control" name="estado">-->
            <!--                    <option value="COMPLETADO">COMPLETADO</option>-->
            <!--                    <option value="PENDIENTE">PENDIENTE</option>-->
            <!--                </select>-->
            <!---->
            <!--            </div>-->


            <div class="col-md-1">
                <button id="btn_buscar" class="btn btn-default">
                    <i class="fa fa-search"></i> Buscar
                </button>
            </div>
        </div>
    </div>

    <br>
    <input type="hidden" name="anular" id="anular" value=1>

    <div id="tabla">


    </div>

    <br>

</div>

<div id="load_div" style="display: none;">
    <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<!-- /.modal-dialog -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
<script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
<script type="text/javascript">
    $(function () {
        elajax();
        TablesDatatables.init();
        $(".fecha").datepicker({
            format: 'dd-mm-yyyy'
        });
        $("#btn_buscar").on("click", function () {

            elajax();

        });

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

    });

    function elajax() {
        $('#tabla').html($('#load_div').html());
        var anular = 0;
        if ($("#anular").length > 0) {

            var anular = 1;
        }
        // $("#hidden_consul").remove();
        var d = {
            'id_local': $('#local_id').val(),
            'fecha': $('#date_range').val(),
            'moneda_id': $('#moneda_id').val(),
            'anular': anular
        };
        $.ajax({
            url: '<?= base_url()?>ingresos/get_ingresos_devolucion',
            data: d,
            type: 'POST',
            success: function (data) {
                if (data.length > 0)
                    $("#tabla").html(data);
                $("#tablaresult").dataTable();
            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
    }
</script>
