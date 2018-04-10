<style>
    .tcharm {
        background-color: #fff;
        border: 1px solid #dae8e7;
        width: 300px;
        padding: 0 20px;
        overflow-y: auto;
    }

    .tcharm-header {
        text-align: center;
    }

    .tcharm-body .row {
        margin: 20px 3px;
    }

    .tcharm-close {
        text-decoration: none !important;
        color: #333333;
        padding: 3px;
        border: 1px solid #fff;
        float: left;
    }

    .tcharm-close:hover {
        background-color: #dae8e7;
        color: #333333;
    }
</style>
<link rel="stylesheet" href="<?= base_url() ?>recursos/js/datepicker-range/daterangepicker.css">
<form id="form_filter">
    <div id="charm" class="tcharm">
        <div class="tcharm-header">

            <h3><a href="#" class="fa fa-arrow-right tcharm-close"></a> <span>Filtros Avanzados</span></h3>
        </div>

        <div class="tcharm-body">

            <div class="row">
                <div class="col-md-4" style="text-align: center;">
                    <button type="button" class="btn btn-default btn_buscar">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <button id="btn_filter_reset" type="button" class="btn btn-warning">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <button type="button" class="btn btn-danger tcharm-trigger">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>

            </div>

            <div class="row">
                <label class="control-label">Vendedor:</label>
                <select id="vendedor_id" name="vendedor_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($vendedores as $vendedor): ?>
                        <option value="<?= $vendedor->nUsuCodigo ?>"><?= $vendedor->nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Cliente:</label>
                <select id="cliente_id" name="cliente_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option
                                value="<?= $cliente->id_cliente ?>"
                            <?= $cliente->id_cliente == $cliente_id ? 'selected' : '' ?>>

                            <?= $cliente->razon_social ?>

                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Estado:</label>
                <select id="estado" class="form-control">
                    <option value="0">Todos</option>
                    <option value="1">Cancelados</option>
                    <option value="2">Pendientes</option>
                </select>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <label class="control-label" style="padding-top: 8px;">Fecha de Venta:</label>
        </div>

        <div class="col-md-3">
            <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                   name="daterange" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>

        </div>

        <div class="col-md-3">
            <input type="checkbox" id="incluir_fecha" checked>
            <label for="incluir_fecha"
                   class="control-label"
                   style="cursor: pointer;">
                Incluir Filtro de Fecha
            </label>
        </div>

        <div class="col-md-2">
            <select name="moneda_id" id="moneda_id" class='cho form-control'>
                <?php foreach ($monedas as $moneda): ?>
                    <option value="<?= $moneda->id_moneda ?>"
                            data-simbolo="<?= $moneda->simbolo ?>"
                        <?= $moneda->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $moneda->nombre ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-default form-control btn_buscar">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-primary tcharm-trigger form-control">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
</form>
<script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
<script src="<?php echo base_url(); ?>recursos/js/datepicker-range/moment.min.js"></script>
<script src="<?php echo base_url(); ?>recursos/js/datepicker-range/daterangepicker.js"></script>

<script>

    $(document).ready(function () {

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

        $("#vendedor_id, #cliente_id").chosen();

        $("#charm").tcharm({
            'position': 'right',
            'display': false,
            'top': '50px'
        });


        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });


        $("#mostrar_detalles").on('change', function () {
            if ($(this).prop('checked'))
                $('.tabla_detalles').show();
            else
                $('.tabla_detalles').hide();
        });

        $("#btn_filter_reset").on('click', function () {
            $('#vendedor_id').val('0').trigger('chosen:updated');
            $('#vendedor_id').change();
            $('#estado').val('0');
            filter_cobranzas();
        });

    });

    function filter_cobranzas() {
        $('#barloadermodal').modal('show');
        $("#charm").tcharm('hide');
        var data = {
            'fecha': $('#date_range').val(),
            'vendedor_id': $("#vendedor_id").val(),
            'cliente_id': $("#cliente_id").val(),
            'moneda_id': $("#moneda_id").val(),
            'estado': $("#estado").val()
        };

        if ($("#incluir_fecha").prop('checked'))
            data.fecha_flag = 1;
        else
            data.fecha_flag = 0;

        $.ajax({
            url: '<?php echo base_url('reporte/cliente_estado/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            },
            complete: function () {
                $('#barloadermodal').modal('hide');
            }
        });
    }


</script>