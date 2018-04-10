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
                <label class="control-label">Proveedor:</label>
                <select id="proveedor_id" name="proveedor_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?= $proveedor->id_proveedor ?>"><?= $proveedor->proveedor_nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Tipo de Documento:</label>
                <select id="tipo_documento" name="tipo_documento" class="form-control">
                    <option value="0">Todos</option>
                    <option value="NOTA DE PEDIDO">NOTA DE PEDIDO</option>
                    <option value="FACTURA">FACTURA</option>
                    <option value="BOLETA DE VENTA">BOLETA DE VENTA</option>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Dias de Atraso:</label>
                <select id="atraso" class="form-control">
                    <option value="0">Todos</option>
                    <option value="1">Menor que 7 Dias</option>
                    <option value="2">Entre 8 y 15 Dias</option>
                    <option value="3">Entre 16 y 30 Dias</option>
                    <option value="4">Mayor que 30 Dias</option>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Deudas:</label>
                <br>
                <div class="col-md-6">
                    <select id="dif_deuda" class="form-control">
                        <option value="1">Mayor</option>
                        <option value="2">Menor</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input id="dif_deuda_value" type="number" class="form-control" value="0">
                </div>

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
            <br>
            <input type="checkbox" id="mostrar_detalles">
            <label for="mostrar_detalles"
                   class="control-label"
                   style="cursor: pointer;">
                Mostrar Detalles
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
        $("#proveedor_id, #tipo_documento, #desglose").chosen();

        $("#charm").tcharm({
            'position': 'right',
            'display': false,
            'top': '50px'
        });

        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });

//        $("#incluir_fecha").on('change', function () {
//            filter_cobranzas();
//        });


        $("#btn_filter_reset").on('click', function () {
            $('#proveedor_id').val('0').trigger('chosen:updated');
            $('#tipo_documento').val('0').trigger('chosen:updated');
            $('#atraso').val('0');
            $('#dif_deuda').val('1');
            $('#dif_deuda_value').val('0');
            filter_cobranzas();
        });

        $("#mostrar_detalles").on('change', function () {
            if ($(this).prop('checked'))
                $('.tabla_detalles').show();
            else
                $('.tabla_detalles').hide();
        });


    });

    function filter_cobranzas() {
        $("#charm").tcharm('hide');
        var data = {
            'fecha_ini': $("#fecha_ini").val(),
            'fecha_fin': $("#fecha_fin").val(),
            'proveedor_id': $("#proveedor_id").val(),
            'tipo_documento': $("#tipo_documento").val(),
            'atraso': $("#atraso").val(),
            'dif_deuda': $("#dif_deuda").val(),
            'dif_deuda_value': $("#dif_deuda_value").val()
        };

        if ($("#incluir_fecha").prop('checked'))
            data.fecha_flag = 1;
        else
            data.fecha_flag = 0;

        if($(this).prop('checked'))
            data.mostrar_detalles = 1;
        else
            data.mostrar_detalles = 0;

        $.ajax({
            url: '<?php echo base_url('reporte_compra/cuentas/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            }
        });
    }


</script>