<?php $ruta = base_url(); ?>
<style>
    #tabla_lista_precios_filter {
        display: none;
    }

    #tabla_lista_precios_wrapper .row_table {
        display: none;
    }


</style>
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
    <li>Productos</li>
    <li><a href="">Listado de Precios y Stock</a></li>
</ul>
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

                <div class="col-md-3">
                    <div class="input-group">
                        <input
                                id="buscar_filter"
                                type="text" class="form-control"
                                placeholder="">
                        <a id="btn_filter" href="#" class="input-group-addon">
                            <i class="fa fa-search"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-1">
                </div>

                <div class="col-md-2">
                    <div clas="row">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Limite: </label>

                        </div>
                        <div class="col-md-8">
                            <select id="max_rows" class="form-control">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="0">Todos</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <label style="padding: 5px;" class="control-label badge label-danger panel-admin-text">STOCK =
                        0</label>

                    <label style="padding: 5px;" class="control-label badge label-warning panel-admin-text">STOCK <
                        MINIMO</label>

                    <label style="padding: 5px;" class="control-label badge label-success panel-admin-text">STOCK >
                        MINIMO</label>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-primary tcharm-trigger form-control">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-default exportar_excel form-control">
                        <i class="fa fa-file-excel-o"></i>
                    </button>
                </div>

            </div>

            <div class="row" id="loading" style="display: none; margin-top: 20px;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
            <div class="table-responsive">
                <div class="content">

                    <div class="content-loader" id="listaprecios_list" style="display: none;">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ver_imagenes_producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div style="display: none;">
    <?php foreach ($locales as $local): ?>
        <input type="hidden" class="locales" data-id="<?= $local['int_local_id'] ?>"
               value="<?= $local['local_nombre'] ?>">
    <?php endforeach; ?>
</div>

<script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
<script>

    var xhrPool = [];
    var xhrPool_details = [];

    function abortPool() {
        for (var i = 0; i < xhrPool.length; i++) {
            xhrPool[i].abort();
            xhrPool.splice(i, 1);
        }
    }

    function abortDetails() {
        for (var i = 0; i < xhrPool_details.length; i++) {
            xhrPool_details[i].abort();
            xhrPool_details.splice(i, 1);
            $("#buscar_filter").keyup();
        }
    }

    $(document).ready(function () {

        $('.exportar_excel').on('click', function () {
            var win = window.open('<?= base_url()?>producto/exportar_precios_excel', '_blank');
            win.focus();
        });

        //find_producto(true);

        $("select").chosen();
        $("#charm").tcharm({
            'position': 'right',
            'display': false,
            'top': '50px'
        });

        $('.btn_buscar').on('click', function () {
            find_producto(true);
        });

        $("#btn_filter_reset").on('click', function () {
            $('#marca_id').val('0').trigger('chosen:updated');
            $('#grupo_id').val('0').trigger('chosen:updated');
            $('#familia_id').val('0').trigger('chosen:updated');
            $('#linea_id').val('0').trigger('chosen:updated');
            $('#proveedor_id').val('0').trigger('chosen:updated');
            $('#con_stock').val('0').trigger('chosen:updated');
            $("#listaprecios_list").html('');
            $("#listaprecios_list").hide();
            $("#charm").tcharm('hide');
            find_producto();
        });


        setTimeout(function () {
            $("#buscar_filter").trigger('focus');

        }, 3);

        $("#buscar_filter").on('keyup', function () {
            find_producto();
        });

        $("#max_rows").on('change', function () {
            find_producto();
        });

        $("#btn_filter").on('click', function () {
            find_producto();
        });

    });

    function find_producto(by_button) {

        if ($("#buscar_filter").val().length >= 3 || by_button == true) {
            $("#listaprecios_list").html('');
            $("#listaprecios_list").hide();
            $("#charm").tcharm('hide');
            $("#loading").show();

            var data = {
                'filter': $("#buscar_filter").val(),
                'max': $("#max_rows").val(),
                'marca_id': $("#marca_id").val(),
                'grupo_id': $("#grupo_id").val(),
                'familia_id': $("#familia_id").val(),
                'linea_id': $("#linea_id").val(),
                'proveedor_id': $("#proveedor_id").val(),
                'con_stock': $("#con_stock").val(),
            };

            $.ajax({
                url: '<?= $ruta . "producto/listaprecios_list" ?> ',
                type: 'POST',
                data: data,
                beforeSend: function (jqXHR) {
                    abortDetails();
                    abortPool();
                    xhrPool.push(jqXHR);
                },
                success: function (data) {

                    $("#listaprecios_list").html(data);

                    var max = $('.tr_list').length;
                    var period = 20;
                    var ids = [];
                    var counter = 0;

                    $('.tr_list').each(function () {
                        var id = $(this).attr('data-id');
                        ids.push(id);
                        counter++;
                    });

                    if (ids.length != 0) {
                        $.ajax({
                            url: '<?= $ruta . "producto/listaprecios_list_detalles" ?> ',
                            type: 'POST',
                            headers: {
                                Accept: 'application/json'
                            },
                            data: {'ids': JSON.stringify(ids), 'stock': $("#con_stock").val()},
                            beforeSend: function (jqXHR) {
                                xhrPool_details.push(jqXHR);
                            },
                            success: function (data) {
                                console.log(data.detalles);

                                for (var i = 0; i < data.detalles.length; i++) {
                                    set_embalajes(data.detalles[i].id, data.detalles[i].embalajes);

                                    set_precios(data.detalles[i].id, data.detalles[i].unidades_y_precios)

                                    set_stock_local(data.detalles[i].id, data.detalles[i].stock_por_local, data.detalles[i].total_stock);
                                }
                            },
                            complete: function (data) {
                                $("#loading").hide();
                                $("#listaprecios_list").show();
                                set_dataTable(ids.length);
                            }
                        });
                    }
                    else {
                        $("#loading").hide();
                        $("#listaprecios_list").show();
                    }
                }

            });

        }
    }

    function set_embalajes(id, embalajes) {
        var template = '';
        for (var i = 0; i < embalajes.length; i++) {
            template += '<div class="row">';
            template += '<div class="col-md-8">' + embalajes[i].nombre_unidad + ':</div>';
            template += '<div class="col-md-4">' + embalajes[i].unidades + '</div>';
            template += '</div>';

            if (i == 0) {
                $("#embalaje_" + id).attr('data-unidad_max', embalajes[i].abreviatura);
                $("#embalaje_" + id).attr('data-unidad_max_unidades', embalajes[i].unidades);
            }
            if (i == embalajes.length - 1)
                $("#embalaje_" + id).attr('data-unidad_min', embalajes[i].abreviatura);
        }
        $("#embalaje_" + id).html(template);
    }

    function set_precios(id, unidades) {
        var precio_unitario = '';
        var precio_venta = '';
        for (var i = 0; i < unidades.length; i++) {
            if (unidades[i].id_precio == '3') {
                precio_unitario += '<div class="row">';
                precio_unitario += '<div class="col-md-8">' + unidades[i].nombre_unidad + ':</div>';
                precio_unitario += '<div class="col-md-4">' + unidades[i].precio + '</div>';
                precio_unitario += '</div>';
            }
            else if (unidades[i].id_precio == '1') {
                precio_venta += '<div class="row">';
                precio_venta += '<div class="col-md-8">' + unidades[i].nombre_unidad + ':</div>';
                precio_venta += '<div class="col-md-4">' + unidades[i].precio + '</div>';
                precio_venta += '</div>';
            }
        }

        $("#precio_unitario_" + id).html(precio_unitario);
        $("#precio_venta_" + id).html(precio_venta);
    }

    function set_stock_local(id, stock_local, total_stock) {
        var stock_min = parseFloat($("#tr_id_" + id).attr('data-stock_min'));
        var no_stock_template = '<label class="control-label badge label-danger">Sin Stock</label>';
        var type_badge = 'success';
        var template = '<table class="table table-bordered no-footer">';
        template += '<tr>';
        template += '<td style="background-color: #ADADAD; color: #fff; padding: 0;text-align: center;">Local</td>';
        template += '<td style="background-color: #ADADAD; color: #fff; padding: 0;text-align: center;">Stock x Local</td>';
        template += '<td style="background-color: #ADADAD; color: #fff; padding: 0;text-align: center;">Totales Locales</td>';
        template += '<td style="background-color: #ADADAD; color: #fff; padding: 0;text-align: center;">Total Minimo</td>';
        template += '</tr>';

        var total_locales = $(".locales").length;
        var flag = true;

        $(".locales").each(function () {
            template += '<tr>';

            template += '<td>' + $(this).val() + '</td>';


            var stock = no_stock_template;
            for (var i = 0; i < stock_local.length; i++) {
                //verifico si existe algun stock de la base de datos
                if (stock_local[i].id_local == $(this).attr('data-id')) {

                    //defino si el stock esta por debajo del minimo
                    if (stock_local[i].cantidad != null && stock_local[i].fraccion != null) {
                        var unit = parseFloat($("#embalaje_" + id).attr('data-unidad_max_unidades'));
                        var stock_actual = (parseFloat(stock_local[i].cantidad) * unit) + parseFloat(stock_local[i].fraccion);
                        if (stock_actual <= stock_min)
                            type_badge = 'warning';
                        else
                            type_badge = 'success';
                    }

                    //preparo el las cantidades por almacenes para mostrarlas
                    if (stock_local[i].cantidad != null && stock_local[i].fraccion != null) {
                        if (stock_local[i].cantidad != 0) {
                            stock = stock_local[i].cantidad + ' ' + $("#embalaje_" + id).attr('data-unidad_max');
                            if (stock_local[i].fraccion != 0)
                                stock += ' y ' + stock_local[i].fraccion + ' ' + $("#embalaje_" + id).attr('data-unidad_min');
                        }
                        else if (stock_local[i].fraccion != 0) {
                            stock = stock_local[i].fraccion + ' ' + $("#embalaje_" + id).attr('data-unidad_min');
                        }

                        //si tengo un stock diferente de 0 muestro el resultado, sino muestro Sin Stock
                        if (stock_local[i].cantidad != 0 || stock_local[i].fraccion != 0)
                            stock = '<label class="control-label badge label-' + type_badge + '">' + stock + '</label>';
                    }
                }
            }
            template += '<td>' + stock + '</td>';

            if (flag) {
                //defino si el stock esta por debajo del minimo
                if (total_stock.suma_cantidad != null && total_stock.suma_fraccion != null) {
                    var unit = parseFloat($("#embalaje_" + id).attr('data-unidad_max_unidades'));
                    var stock_actual = (parseFloat(total_stock.suma_cantidad) * unit) + parseFloat(total_stock.suma_fraccion);
                    if (stock_actual <= stock_min)
                        type_badge = 'warning';
                    else
                        type_badge = 'success';
                }

                //preparo para mostrar todos los stock en formato mayor y menor
                var total = no_stock_template;
                if (total_stock.suma_cantidad != null && total_stock.suma_fraccion != null) {
                    if (total_stock.suma_cantidad != 0) {
                        total = total_stock.suma_cantidad + ' ' + $("#embalaje_" + id).attr('data-unidad_max');
                        if (total_stock.suma_fraccion != 0)
                            total += ' y ' + total_stock.suma_fraccion + ' ' + $("#embalaje_" + id).attr('data-unidad_min');
                    }
                    else {
                        total = total_stock.suma_fraccion + ' ' + $("#embalaje_" + id).attr('data-unidad_min');
                    }

                    total = '<label class="control-label badge label-' + type_badge + '">' + total + '</label>';
                }
                template += '<td rowspan="' + total_locales + '">' + total + '</td>';

                //preparo para mostrar el total del stock minimo
                var total_minimo = no_stock_template;
                if (total_stock.suma_cantidad != null && total_stock.suma_fraccion != null) {
                    var unidades = parseFloat($("#embalaje_" + id).attr('data-unidad_max_unidades'));

                    total_minimo = (parseFloat(total_stock.suma_cantidad) * unidades) + parseFloat(total_stock.suma_fraccion);
                    total_minimo = total_minimo + ' ' + $("#embalaje_" + id).attr('data-unidad_min');

                    total_minimo = '<label class="control-label badge label-' + type_badge + '">' + total_minimo + '</label>';
                }


                template += '<td rowspan="' + total_locales + '">' + total_minimo + '</td>';
                flag = false;
            }

            template += '</tr>';
        });

        template += '</table>';

        $("#detalle_" + id).html(template);
    }

    function set_dataTable(limit) {
        App.datatables();

        $("#tabla_lista_precios").dataTable({
            retrieve: true,
            "iDisplayLength": limit,
            "aLengthMenu": false,
            "order": [[1, "desc"]],
            "scrollX": false,
            "scrollY": false,
            "scrollCollapse": false,
            "dom": '<"row_table"<"pull-left"f><"pull-right"l>>rt<"row_table"<"pull-left"i><"pull-right"p>>',
        });
    }

</script>
