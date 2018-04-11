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
                <label class="control-label">Ubicaci&oacute;n:</label>
                <select id="local_id" class="form-control filter-input">
                    <?php foreach ($locales as $local): ?>
                        <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Moneda:</label>
                <select id="moneda_id" name="moneda_id" class="form-control">
                    <?php foreach ($monedas as $m): ?>
                        <option value="<?= $m->id_moneda ?>"
                            <?= $m->id_moneda == MONEDA_DEFECTO ? 'selected' : '' ?>><?= $m->nombre ?></option>
                    <?php endforeach; ?>
                </select>
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
        <div class="col-md-2">
            <input id="fecha_ini" type="text" class="form-control input-datepicker" value="<?= date('01-m-Y') ?>"
                   style="cursor: pointer;" readonly>
        </div>


        <div class="col-md-2">
            <input id="fecha_fin" type="text" class="form-control input-datepicker" value="<?= date('d-m-Y') ?>"
                   style="cursor: pointer;" readonly>
        </div>

        <div class="col-md-3">
            <input type="checkbox" id="incluir_fecha" checked>
            <label for="incluir_fecha"
                   class="control-label"
                   style="cursor: pointer;">
                Incluir Filtro de Fecha
            </label>
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

<script>

    $(document).ready(function () {

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
            'fecha_ini': $('#fecha_ini').val(),
            'fecha_fin': $('#fecha_fin').val(),
            'vendedor_id': $("#vendedor_id").val(),
            'cliente_id': $("#cliente_id").val(),
            'moneda_id': $("#moneda_id").val(),
            'local_id': $("#local_id").val(),
            'estado': $("#estado").val()
        };

        if ($("#incluir_fecha").prop('checked'))
            data.fecha_flag = 1;
        else
            data.fecha_flag = 0;

        $.ajax({
            url: '<?php echo base_url('reporte_ventas/cliente_estado/filter')?>',
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