<?php $ruta = base_url(); ?>
<style>
    [data-toggle="buttons"] > .btn > input[type="radio"],
    [data-toggle="buttons"] > .btn > input[type="checkbox"] {
        display:inline;
        position:absolute;
        left:-9999px;
    }

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
    label.active{
        background-color: #A2E279 !important;
        border-color: #96DF69 !important;
    }
</style>
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Stock y ventas</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
<link rel="stylesheet" href="<?= base_url('recursos/css/bootstrap-datepicker.min.css') ?>">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
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
                            <label class="control-label">Marca:</label>
                            <select id="marca_id" name="marca_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= $marca->id_marca ?>"><?= $marca->nombre_marca ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Grupo:</label>
                            <select id="grupo_id" name="grupo_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($grupos as $grupo): ?>
                                    <option value="<?= $grupo->id_grupo ?>"><?= $grupo->nombre_grupo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Familia:</label>
                            <select id="familia_id" name="familia_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($familias as $familia): ?>
                                    <option value="<?= $familia->id_familia ?>"><?= $familia->nombre_familia ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Linea:</label>
                            <select id="linea_id" name="linea_id" class="form-control ctrl">
                                <option value="0">Todos</option>
                                <?php foreach ($lineas as $linea): ?>
                                    <option value="<?= $linea->id_linea ?>"><?= $linea->nombre_linea ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <label class="control-label">Producto:</label>
                            <div id="divSelect">
                                <select id="producto_id" name="producto_id" multiple="multiple">
                                 <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto->producto_id ?>"
                                                data-impuesto="<?= $producto->porcentaje_impuesto ?>">
                                            <?php $barra = $barra_activa->activo == 1 && $producto->barra != "" ? "CB: " . $producto->barra : "" ?>
                                            <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre . " " . $barra ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <input type="hidden" id="tipo_periodo" value="">
                    <div class="row">
                    <?php
                        $fechaF = date("d/m/Y");
                        $fechaI = date("d/m/Y", strtotime("-7 days"));
                    ?>
                        <div class="col-md-6">
                            <label class="control-label">Desde:</label> <span id="fecha_ini_value"><?= $fechaI ?></span>
                            <div id="fecha_ini"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">Hasta:</label> <span id="fecha_fin_value"><?= $fechaF ?></span>
                            <div id="fecha_fin"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label">Periodo:</label>
                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                                <label class="btn btn-default active">
                                    <input type="radio" name="options" id="day" autocomplete="off" checked> Dia
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="options" id="month" autocomplete="off"> Mes
                                </label>
                                <label class="btn btn-default">
                                    <input type="radio" name="options" id="year" autocomplete="off"> A&ntilde;o
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <select id="tipo" name="tipo" class="form-control filter-input">
                                <option value="1">Cantidad</option>
                                <option value="2" selected="">Importe</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="row">
                        <div class="col-md-12">
                        <?php if (isset($locales)): ?>
                            <select id="local_id" multiple="multiple">
                                <?php foreach ($locales as $local): ?>
                                    <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                            value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <button id="btn_buscar" class="btn btn-default">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary tcharm-trigger form-control">
                        <i class="fa fa-plus"></i>
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
            <script src="<?= base_url('recursos/js/Validacion.js') ?>"></script>
            <script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
            <script src="<?= base_url('recursos/js/bootstrap.min.js') ?>"></script>
            <script src="<?= base_url('recursos/js/bootstrap-datepicker.min.js') ?>"></script>
            <script src="<?= base_url('recursos/js/bootstrap-datepicker.es.min.js') ?>"></script>
            <script src="<?= base_url('recursos/js/moment.min.js') ?>"></script>
            <script src="<?= base_url('recursos/js/multiple-select.js') ?>"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                var bnt_save = '<input type="button" id="guardar" class="btn btn-sm btn-success" value="Guardar">';

                // Filtro en select
                $("#producto_id, #local_id").multipleSelect({
                    filter: true,
                    width: '100%'
                });

                $(function () {

                    select_day();
                    set_datepicker_events();

                    $("#day, #month, #year").on('change', function () {
                        $('#fecha_ini, #fecha_fin').datepicker('destroy');
                        $('#fecha_ini_value, #fecha_fin_value').html('');

                        $('.include_days').hide();

                        if ($(this).attr('id') == 'month'){
                            select_month();
                        }else if ($(this).attr('id') == 'year'){
                            select_year();
                        }else {
                            $('.include_days').show();
                            select_day();
                        }
                        set_datepicker_events();
                    });
                });

                $('document').ready(function(){

                    $(".select-chosen").chosen({
                        search_contains: true
                    });

                    $("#charm").tcharm({
                        'position': 'right',
                        'display': false,
                        'top': '50px'
                    });

                    $('.ctrl').chosen();

                    getReporte();

                    $("#btn_buscar, .btn_buscar").on("click", function () {
                        getReporte();
                    });

                    $('.chosen-container').css('width', '100%');

                    $("#btn_filter_reset").on('click', function () {
                        $('#marca_id').val('0').trigger('chosen:updated');
                        $('#grupo_id').val('0').trigger('chosen:updated');
                        $('#familia_id').val('0').trigger('chosen:updated');
                        $('#linea_id').val('0').trigger('chosen:updated');
                        $('#producto_id').multipleSelect('uncheckAll');
                        $("#charm").tcharm('hide');
                        getReporte();
                        filtro();
                    });

                    $('#marca_id, #grupo_id, #familia_id, #linea_id').on('change', function(){
                        filtro();
                    });
                });

                function getReporte() {
                    $("#historial_list").html($("#loading").html());

                    var th = [];

                    var ini = $('#fecha_ini_value').html();
                    var fin = $('#fecha_fin_value').html();

                    if (ini != "" && fin != "") {
                        if ($('#year').prop('checked')) {
                            th = generar_rango_year(ini, fin);
                        }
                        else if ($('#month').prop('checked')) {
                            th = generar_rango_month(ini, fin);
                        }
                        else if ($('#day').prop('checked')) {
                            th = generar_rango_day(ini, fin);
                        }
                    }else{
                        alert("Seleccione un rango de fecha");
                        return;
                    }

                    if (th.length > 0) {
                        var params = {
                            'producto_id': $("#producto_id").val(),
                            'grupo_id': $("#grupo_id").val(),
                            'marca_id': $("#marca_id").val(),
                            'linea_id': $("#linea_id").val(),
                            'familia_id': $("#familia_id").val(),
                            'local_id': JSON.stringify($("#local_id").val()),
                            'rangos': JSON.stringify(th.slice(1)),
                            'tipo_periodo': $('#tipo_periodo').val(),
                            'tipo': $("#tipo").val()
                        };

                        $.ajax({
                            url: '<?= base_url()?>reporte/stockVentas/filter',
                            type: 'POST',
                            data: params,
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
                        $("#charm").tcharm('hide');
                    }
                    else {
                        alert("El rango de fecha no es valido");
                        return;
                    }
                }

                function filtro(){
                    var data = {
                        'grupo_id': $("#grupo_id").val(),
                        'marca_id': $("#marca_id").val(),
                        'linea_id': $("#linea_id").val(),
                        'familia_id': $("#familia_id").val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>reporte/selectProducto',
                        data: data,
                        type: 'POST',
                        success: function (data) {
                            $("#divSelect").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#divSelect").html('');
                        }
                    });
                }

                function select_day() {
                    if ($('#day').prop('checked')) {
                        $('#fecha_ini, #fecha_fin').datepicker({
                            language: "es",
                            format: 'dd/mm/yyyy',
                            startView: 0,
                            minViewMode: 0,
                            maxViewMode: 0
                        });
                    }
                }

                function select_month() {
                    $('#fecha_ini, #fecha_fin').datepicker({
                        language: "es",
                        format: 'mm/yyyy',
                        startView: 1,
                        minViewMode: 1,
                        maxViewMode: 2
                    });

                }

                function select_year() {
                    $('#fecha_ini, #fecha_fin').datepicker({
                        language: "es",
                        format: 'yyyy',
                        startView: 2,
                        minViewMode: 2,
                        maxViewMode: 2
                    });
                }

                function set_datepicker_events() {
                    $('#fecha_ini').on('changeDate', function () {
                        $('#fecha_ini_value').html(
                            $('#fecha_ini').datepicker('getFormattedDate')
                        );
                    });

                    $('#fecha_fin').on('changeDate', function () {
                        $('#fecha_fin_value').html(
                            $('#fecha_fin').datepicker('getFormattedDate')
                        );
                    });
                }

                function generar_rango_year(ini, fin) {
                    var data_head = [];

                    if (parseInt(ini) <= parseInt(fin)) {
                        data_head.push(bnt_save);
                        for (var inicial = parseInt(ini); inicial <= parseInt(fin); inicial++) {
                            data_head.push(inicial);
                        }
                    }
                    $('#tipo_periodo').val(3);
                    return data_head;
                }

                function generar_rango_month(ini, fin) {
                    var data_head = [];
                    var fecha = ini.split('/');
                    var fecha_ini = new Date(fecha[1], fecha[0] - 1, 1);

                    var fecha = fin.split('/');
                    var fecha_fin = new Date(fecha[1], fecha[0] - 1, 1);

                    var fecha_counter = moment(fecha_ini).format('YYYYMMDD');
                    fecha_fin = moment(fecha_fin).format('YYYYMMDD');

                    if (parseInt(fecha_counter) > parseInt(fecha_fin))
                        return [];

                    data_head.push(bnt_save);
                    while (parseInt(fecha_counter) <= parseInt(fecha_fin)) {
                        data_head.push(moment(fecha_ini).format('MM/YYYY'));
                        fecha_ini.setMonth(fecha_ini.getMonth() + 1);
                        fecha_counter = moment(fecha_ini).format('YYYYMMDD');
                    }
                    $('#tipo_periodo').val(2);
                    return data_head;
                }

                function generar_rango_day(ini, fin) {
                    var data_head = [];
                    var fecha = ini.split('/');
                    var fecha_ini = new Date(fecha[2], fecha[1] - 1, fecha[0]);

                    var fecha = fin.split('/');
                    var fecha_fin = new Date(fecha[2], fecha[1] - 1, fecha[0]);

                    var fecha_counter = moment(fecha_ini).format('YYYYMMDD');
                    fecha_fin = moment(fecha_fin).format('YYYYMMDD');

                    if (parseInt(fecha_counter) > parseInt(fecha_fin))
                        return [];

                    data_head.push(bnt_save);
                    while (parseInt(fecha_counter) <= parseInt(fecha_fin)) {
                        data_head.push(moment(fecha_ini).format('DD/MM/YYYY'));
                        fecha_ini.setDate(fecha_ini.getDate() + 1);
                        fecha_counter = moment(fecha_ini).format('YYYYMMDD');
                    }
                    $('#tipo_periodo').val(1);
                    return data_head;
                }
            </script>
