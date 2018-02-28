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
    <li>Reportes</li>
    <li><a href="">Entrada y Salida</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
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
                        <label class="control-label">Movimiento:</label>
                        <select id="marca_id" name="marca_id" class="form-control">
                            <option value="0">Todos</option>
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= $marca->id_marca ?>"><?= $marca->nombre_marca ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <label class="control-label">Grupo:</label>
                        <select id="grupo_id" name="grupo_id" class="form-control">
                            <option value="0">Todos</option>
                            <?php foreach ($grupos as $grupo): ?>
                                <option value="<?= $grupo->id_grupo ?>"><?= $grupo->nombre_grupo ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <label class="control-label">Familia:</label>
                        <select id="familia_id" name="familia_id" class="form-control">
                            <option value="0">Todos</option>
                            <?php foreach ($familias as $familia): ?>
                                <option value="<?= $familia->id_familia ?>"><?= $familia->nombre_familia ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <label class="control-label">Linea:</label>
                        <select id="linea_id" name="linea_id" class="form-control">
                            <option value="0">Todos</option>
                            <?php foreach ($lineas as $linea): ?>
                                <option value="<?= $linea->id_linea ?>"><?= $linea->nombre_linea ?></option>
                            <?php endforeach; ?>
                        </select>
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

                    <div class="row" style="display: none;">
                        <label class="control-label">Stock:</label>
                        <select id="con_stock" name="con_stock" class="form-control">
                            <option value="0">Todos</option>
                            <option value="1" selected="">Con Stock</option>
                            <option value="2">Sin Stock</option>
                        </select>
                    </div>

                </div>
            </div>
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <select name="local_id" id="local_id" class='form-control'>
                            <?php foreach ($locales as $local): ?>
                                <option <?= $local->local_id == $local->local_defecto ? 'selected="selected"' : '' ?>
                                        value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;" name="daterange" value="<?= date('01/m/Y')?> - <?= date('d/m/Y')?>" />

                    </div>
                    <div class="col-md-1">

                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-offset-3 col-md-2 text-right">
                        <button type="button" id="new_cotizacion" class="btn btn-primary">Nueva Cotizacion</button>
                    </div>



                </div>
            </div>
            <br>



            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list" class="block">


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

                    get_reporte();

                    $("#btn_buscar").on("click", function () {
                        get_reporte();
                    });

                });

                function get_reporte() {

                    $("#historial_list").html($("#loading").html());

                    var date_range = $("#date_range").val();
                    var local_id = $("#local_id").val();


                    $.ajax({
                        url: '<?= base_url()?>cotizar/get_cotizaciones',
                        data: {
                            'date_range': date_range,
                            'local_id': local_id
                        },
                        type: 'POST',
                        success: function (data) {
                            $("#historial_list").html(data);

                            $('#exportar_pdf').attr('href', $('#exportar_pdf').attr('data-href') + estado + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);
                            $('#exportar_excel').attr('href', $('#exportar_excel').attr('data-href') + estado + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);

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
