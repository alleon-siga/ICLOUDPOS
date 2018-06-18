<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<input id="base_url" type="hidden" value="<?= $ruta ?>">

<ul class="breadcrumb breadcrumb-top">
    <li>Compras</li>
    <li><a href="">Consultar Compras </a></li>
</ul>

<div class="block">

    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <?php if (isset($locales)): ?>
                    <select id="local_id" class="form-control">
                        <?php foreach ($locales as $local): ?>
                            <option <?php if ($this->session->userdata('id_local') == $local->local_id) echo "selected"; ?>
                                    value="<?= $local->local_id; ?>"> <?= $local->local_nombre ?> </option>
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


    <div id="tabla">

    </div>

    <div class="modal fade" id="ver_compra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

    </div>


    <div class="modal fade" id="ingresomodal" style="width: 85%; overflow: auto;
      margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
    </div>

    <div id="load_div" style="display: none;">
        <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
            <div class="col-md-12 text-center">
                <div class="loading-icon"></div>
            </div>
        </div>
    </div>

</div>
<script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
<script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
<script type="text/javascript">

    $(document).ready(function () {

        $('#estado').on('change', function () {
            if ($(this).val() == 'PENDIENTE') {
                $('#moneda_block').hide();
            }
            else {
                $('#moneda_block').show();
            }
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

        $('select').chosen();

        get_ingresos();

        $("#btn_buscar").on('click', function () {
            get_ingresos();
        });
    });

    function get_ingresos() {
        $('#tabla').html($('#load_div').html());
        var data = {
            'local_id': $('#local_id').val(),
            'estado': $('#estado').val(),
            'fecha': $('#date_range').val(),
            'moneda_id': $('#moneda_id').val()
        };

        $.ajax({
            url: '<?= base_url()?>ingresos/lista_compra/filter',
            data: data,
            type: 'POST',
            success: function (data) {
                $("#tabla").html(data);
            },
            error: function () {
                $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                    type: 'danger',
                    delay: 5000,
                    allow_dismiss: true
                });
                $("#tabla").html('');
            }
        });
    }
</script>