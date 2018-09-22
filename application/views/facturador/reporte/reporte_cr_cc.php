<?php $ruta = base_url(); ?>
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
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Costos Reales Vs Contables</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
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
                    <label class="control-label panel-admin-text">Fecha</label>
                    <input type="text" id="fecha" class="form-control" readonly style="cursor: pointer;" name="fecha" value="<?= date('01/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                </div>
                
                <div class="col-md-1">
                    <div style="padding-top: 30px;"></div>
                    <button id="btn_buscar" class="btn btn-default">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
                <div class="col-md-3">
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
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
            <script src="<?php echo $ruta; ?>recursos/js/multiple-select.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                $("#producto_id").multipleSelect({
                    filter: true,
                    width: '100%'
                });
                $(document).ready(function () {
                    //CONFIGURACIONES INICIALES
                    
                    $("#charm").tcharm({
                        'position': 'right',
                        'display': false,
                        'top': '50px'
                    });
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
                    $("#historial_cr_list").html($("#loading").html());

                    var data = {
                        'caja_id': $("#caja_select").val(),
                        'fecha': $("#fecha").val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>facturador/reporte/reporte/filter',
                        data: data,
                        type: 'POST',
                        success: function (data) {
                            $("#historial_cr_list").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#historial_cr_list").html('');
                        }
                    });
                    $("#charm").tcharm('hide');
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
            </script>
