<style type="text/css">
    .tableStyle td {
        font-size: 9px !important;
    }

    .table > tbody > tr:hover {
        color: #333 !important;
    }

    .tableStyle th {
        font-size: 10px !important;
    }

    .input_table, .input_table_readonly {
        width: 100%;
        height: 25px;
        font-size: 10px !important;
        border: 1px solid #DEDEDE;
        text-align: center;
    }

    .input_table_readonly {
        background-color: #DEDEDE;
    }

    .table > tbody > tr > td {
        padding: 1px !important;
    }

    .table > thead > tr > th {
        padding: 4px 1px !important;
    }

    .panel_button {
        padding: 3px 5px;
    }

    .panel_button:hover {
        border: 1px solid #DEDEDE;
    }
</style>
<?php $md = get_moneda_defecto()?>
<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li>
        <a href="">Ingreso Calzado</a>
    </li>
</ul>

<div class="block">
    <div class="row-fluid">
        <form id="frmCompra" class='form-horizontal'>

            <div class="row">

                <div class="col-md-3">
                    <label class="control-label">Ubicaci&oacute;n:</label>
                    <select name="local_id" id="local_id" class="form-control">
                        <?php foreach ($locales as $local): ?>
                            <option value="<?= $local->local_id ?>"
                                <?= $local->local_defecto == $local->local_id ? 'selected' : '' ?>>
                                <?= $local->local_nombre ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="control-label">Proveedor:</label>
                    <select name="proveedor_id" id="proveedor_id" class="form-control">
                        <option value="">Seleccione</option>
                        <?php foreach ($proveedores as $p): ?>
                            <option value="<?= $p->id_proveedor ?>"><?= $p->proveedor_nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="control-label">Condici&oacute;n:</label>
                    <select name="pago" id="pago" class="form-control">
                        <option value="CONTADO">CONTADO</option>
                        <option value="CREDITO">CR&Eacute;DITO</option>
                    </select>
                </div>


                <div class="col-md-2">
                    <label class="control-label">Moneda:</label>
                    <select name="moneda" id="moneda" class="form-control">
                        <?php foreach ($monedas as $mon): ?>
                            <option
                                <?= $mon['id_moneda'] == 1029 ? 'selected' : '' ?>
                                    value="<?= $mon['id_moneda'] ?>"
                                    data-tasa="<? echo $mon['tasa_soles'] ?>"
                                    data-oper="<? echo $mon['ope_tasa'] ?>"
                                    data-simbolo="<? echo $mon['simbolo'] ?>"><?= $mon['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2" id="tasa_block" style="display: none;">
                    <label class="control-label">Tasa:</label>
                    <div class="input-group">
                        <div style="min-width: 10px;" class="input-group-addon"><?= $md->simbolo ?></div>
                        <input type="text" id="tasa" name="tasa" value="" class='form-control'>

                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-3">
                    <label class="control-label">Documento:</label>
                    <select name="documento" id="documento" class="form-control">
                        <option value="">Seleccione</option>
                        <option value="FACTURA">FACTURA</option>
                        <option value="BOLETA DE VENTA">BOLETA DE VENTA</option>
                        <option value="NOTA DE PEDIDO">NOTA DE PEDIDO</option>
                        <option value="GUIA DE REMISION">GUIA DE REMISION</option>
                    </select>
                </div>


                <div class="col-md-3">
                    <label class="control-label">Serie - N&uacute;mero:</label>
                    <div class="row">
                        <div class="col-md-4">
                            <input name="serie_doc" id="serie_doc" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <input name="numero" id="numero" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="control-label">Fecha de Emisi&oacute;n:</label>
                    <input name="fecha_emision" id="fecha_emision" class="form-control" readonly
                           value="<?= date('d-m-Y') ?>">
                </div>

                <div class="col-md-2">
                    <label class="control-label">Costo:</label>
                    <div class="input-group">
                        <input type="text" id="costo" name="costo" value="<?= valueOption("INGRESO_COSTO", '0') ?>"
                               class='form-control'>
                        <div style="min-width: 10px;" class="input-group-addon">%</div>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="control-label">Utilidad:</label>
                    <div class="input-group">
                        <input type="text" id="utilidad" name="utilidad"
                               value="<?= valueOption("INGRESO_UTILIDAD", '0') ?>" class='form-control'>
                        <div style="min-width: 10px;" class="input-group-addon">%</div>
                    </div>
                </div>


            </div>

            <hr style="margin-bottom: 10px; margin-top: 10px;">


            <div class="row">
                <div class="col-md-1">
                    <label class="control-label">Serie:</label>
                    <br>
                    <br>
                    <span id="rango_text" style="float: left; position: fixed;"></span>
                </div>
                <div class="col-md-1">
                    <select name="serie" id="serie" class="form-control">
                        <option value="">-</option>
                        <?php foreach ($series as $s): ?>
                            <option value="<?= $s->serie ?>"
                                    data-rango="<?= $s->rango ?>"><?= $s->serie ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-1"></div>

                <div class="col-md-1">
                    <label class="control-label">Plantilla:</label>
                </div>
                <div class="col-md-4">
                    <select name="plantilla" id="plantilla" class="form-control">
                        <option value="">Seleccione</option>
                        <?php foreach ($plantillas as $p): ?>
                            <option value="<?= $p->id ?>"><?= $p->nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-1">
                    <a id="add_producto" class="btn btn-default">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>

                <div class="col-md-3" style="text-align: right; padding: 0px;">
                    <button class="btn panel_button" id="save_ingreso" type="button" style="margin-right: 10px;">
                        <i class="fa fa-save fa-3x text-info fa-fw"></i><br>Guardar
                    </button>

                    <button class="btn panel_button" id="reset_ingreso" type="button">
                        <i class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
                    </button>
                </div>
            </div>

            <hr style="margin-bottom: 5px; margin-top: 5px;">
            <div class="row">
                <div class="col-md-6">
                    <input type="checkbox" name="igv" id="igv"> <label for="igv" class="control-label"
                                                                       style="cursor: pointer">Incluir IGV</label>
                </div>
                <div class="col-md-2">
                    <label>Subtotal: <span class="tipo_moneda"><?= $md->simbolo ?></span> <span
                                id="subtotal">0.00</span></label>
                </div>
                <div class="col-md-2">
                    <label>IGV: <span class="tipo_moneda"><?= $md->simbolo ?></span> <span id="impuesto">0.00</span></label>
                </div>
                <div class="col-md-2">
                    <label>Total: <span class="tipo_moneda"><?= $md->simbolo ?></span> <span id="total">0.00</span></label>
                </div>
            </div>
            <div class="block" id="tables_template">

            </div>

        </form>
    </div>
</div>

<script type="text/javascript">
    var my_tables = [];

    $(function () {

        $("select").chosen();
        $("#fecha_emision").datepicker({
            format: 'dd-mm-yyyy'
        });

        $("#serie").on('change', function () {
            var rango = $("#serie option:selected").attr('data-rango');
            if (rango != undefined) {
                $("#rango_text").html("Rango: " + rango.split('|').join(', '));
            }
            else{
                $("#rango_text").html("");
            }
        });


        $("#add_producto").on('click', function () {
            var serie = $("#serie").val();
            var tallas = $("#serie option:selected").attr('data-rango');
            var plantilla_id = $("#plantilla").val();
            var plantilla_nombre = $("#plantilla option:selected").text();

            //validaciones basicas
            if (serie == "") {
                show_msg('warning', 'Error. Seleccione una serie');
                return false;
            }

            if (plantilla_id == "") {
                show_msg('warning', 'Error. Seleccione una plantilla');
                return false;
            }


            //Proceso de agregar la combinacion de serie y plantilla
            if (my_tables[serie] == undefined) {
                my_tables[serie] = {
                    serie: serie,
                    tallas: tallas.split('|'),
                    table_data: []
                };
            }

            //valida si existe una combinacion de plantilla y serie
            for (var i = 0; i < my_tables[serie].table_data.length; i++) {
                if (my_tables[serie].table_data[i].plantilla_id == plantilla_id) {
                    show_msg('warning', 'Error. Esta combinacion de plantilla y serie ya existe.');
                    return false;
                }
            }


            //Proceso de agregar las cantidades segun su rango
            var cantidades = [];
            for (var i = 0; i < my_tables[serie].tallas.length; i++) {
                cantidades[my_tables[serie].tallas[i]] = 0;
            }

            //Proceso para agregar los datos de las tablas segun su serie
            my_tables[serie].table_data.push({
                plantilla_id: plantilla_id,
                nombre: plantilla_nombre,
                cantidades: cantidades,
                total: parseInt(0),
                costo_unitario: parseFloat(0),
                importe: parseFloat(0),
                costo_total: 0,
                utilidad: 0,
                precio_min: 0,
                precio_max: 0
            });

            $("#serie").val("").trigger('chosen:updated');
            $("#plantilla").val("").trigger('chosen:updated');

            //Aqui hago el render las tablas
            $("#tables_template").html('');
            for (var key in my_tables) {
                $("#tables_template").append(render_template(my_tables[key]));
            }

            //Asigno los eventos del html rendereado
            set_events();

        });


        //evento que recalcula todos los valores de la tabla 
        //cuando se detectan cambios externos
        $("#costo, #utilidad").on('keyup', function () {
            refresh_screen();
        });

        $("#igv").on('change', function () {
            refresh_screen();
        });

        $("#moneda").on('change', function () {
            var data_tasa = $("#moneda option:selected").attr('data-tasa');

            if ($("#moneda").val() != 1029) {
                $("#tasa").val(data_tasa);
                $("#tasa_block").show();

            }
            else {
                $("#tasa_block").hide();
                $("#tasa").val(0);
            }

            refresh_screen();
        });


        //evento para guardar el ingreso
        $("#save_ingreso").on('click', function () {

            var plantillas = prepare_plantilla();
            if (plantillas == false) {
                show_msg('warning', 'Error. No tiene ningun producto agregado');
                return false;
            }

            if ($("#proveedor_id").val() == "") {
                show_msg('warning', 'Error. Seleccione un proveedor');
                return false;
            }

            if ($("#documento").val() == "") {
                show_msg('warning', 'Error. Seleccione un Documento');
                return false;
            }

            if ($("#serie_doc").val() == "" && $("#numero").val() == "") {
                show_msg('warning', 'Error. Ingrese la serie-numero del documento');
                return false;
            }


            if (confirm('Deseas guardar la compra')) {

                var params = {
                    costo: isNaN(parseFloat($("#costo").val())) ? 0 : parseFloat($("#costo").val()),
                    utilidad: isNaN(parseFloat($("#utilidad").val())) ? 0 : parseFloat($("#utilidad").val()),
                    plantillas: plantillas,
                    proveedor_id: $("#proveedor_id").val(),
                    local_id: $("#local_id").val(),
                    fecha_emision: $("#fecha_emision").val(),
                    tipo_documento: $("#documento").val(),
                    documento_serie: $("#serie_doc").val(),
                    documento_numero: $("#numero").val(),
                    pago: $("#pago").val(),
                    moneda_id: $("#moneda").val(),
                    tasa: $("#tasa").val(),
                    impuesto: $("#impuesto").html(),
                    subtotal: $("#subtotal").html(),
                    total: $("#total").html()
                };


                $("#save_ingreso").attr('disabled', 'disabled');
                $("#reset_ingreso").attr('disabled', 'disabled');
                $("#cargando_modal").modal('show');
                $.ajax({
                    url: '<?= base_url()?>ingreso_calzado/save',
                    type: 'POST',
                    dataType: 'json',
                    data: params,
                    success: function (data) {

                        if (data.success == '1') {
                            show_msg('success', 'Ingreso guardado con exito.');
                            $.ajax({
                                url: '<?= base_url()?>ingreso_calzado',
                                success: function (data) {
                                    $('#page-content').html(data);
                                    $("#cargando_modal").modal('hide');
                                    $(".modal-backdrop").remove();
                                }
                            });
                        } else {
                            show_msg('warning', 'Error al realizar la operacion.');
                        }

                    },
                    error: function () {
                        show_msg('danger', 'Error al realizar la operacion');
                    },
                    complete: function () {
                        $("#save_ingreso").removeAttr('disabled');
                        $("#reset_ingreso").removeAttr('disabled');
                        $("#cargando_modal").modal('hide');
                    }
                });

            }
        });

        $("#reset_ingreso").on('click', function () {
            if (confirm('Deseas reiniciar la compra')) {
                $.ajax({
                    url: '<?=base_url()?>' + '/ingreso_calzado',
                    success: function (data) {
                        $('#page-content').html(data);
                        $(".modal-backdrop").remove();
                    }
                });
            }
        });

    });


    /** FUNCIONES INTERNAS */

    function prepare_plantilla() {
        var result = [];

        for (var key in my_tables) {
            for (var i = 0; i < my_tables[key].table_data.length; i++) {
                for (var j = 0; j < my_tables[key].tallas.length; j++) {
                    var cantidad = my_tables[key].table_data[i].cantidades[my_tables[key].tallas[j]];

                    if (cantidad > 0) {
                        var temp = {
                            'id': my_tables[key].table_data[i].plantilla_id + '-' + key + '-' + my_tables[key].tallas[j],
                            'nombre': my_tables[key].table_data[i].nombre + ' TALLA ' + my_tables[key].tallas[j],
                            'cantidad': cantidad,
                            'costo_unitario': my_tables[key].table_data[i].costo_unitario,
                            'precio_min': formatPrice(my_tables[key].table_data[i].precio_min),
                            'precio_max': formatPrice(my_tables[key].table_data[i].precio_max)
                        };
                        result.push(temp);
                    }
                }
            }
        }

        if (result.length > 0)
            return JSON.stringify(result);
        else
            return false;
    }

    function refresh_screen() {
        var total_temp = parseFloat(0);
        for (var key in my_tables) {
            for (var i = 0; i < my_tables[key].table_data.length; i++)
                total_temp += parseFloat(refresh_row(key, i));
        }

        var simbolo = $("#moneda option:selected").attr('data-simbolo');
        var moneda = $("#moneda").val();
        var tasa = isNaN(parseFloat($("#tasa").val())) ? 1 : parseFloat($("#tasa").val());
        var total, subtotal, impuesto;

        if (moneda != 1029) {
            if (moneda == 1030) {
                total_temp = total_temp / tasa;
            }
        }

        if ($("#igv").prop('checked')) {
            subtotal = parseFloat(total_temp);
            total = parseFloat(subtotal + (subtotal * 0.18));
            impuesto = parseFloat(subtotal * 0.18);
        } else {
            total = parseFloat(total_temp);
            subtotal = parseFloat(total - (total * 0.18));
            impuesto = parseFloat(total * 0.18);
        }

        $(".tipo_moneda").html(simbolo);
        $("#total").html(total.toFixed(2));
        $("#subtotal").html(subtotal.toFixed(2));
        $("#impuesto").html(impuesto.toFixed(2));
    }

    function refresh_row(serie, index) {
        var td_id = serie + '_' + index;
        var total = 0;
        var costo = isNaN(parseFloat($("#costo").val())) ? 0 : parseFloat($("#costo").val());
        var utilidad = isNaN(parseFloat($("#utilidad").val())) ? 0 : parseFloat($("#utilidad").val());

        for (var key in my_tables[serie].table_data[index].cantidades) {
            total = parseInt(total) + parseInt(my_tables[serie].table_data[index].cantidades[key]);
        }

        var costo_unitario = my_tables[serie].table_data[index].costo_unitario;
        var importe = parseFloat(total * costo_unitario);
        var costo_total = parseFloat((costo_unitario * costo / 100) + costo_unitario);
        var utilidad = parseFloat((costo_total * utilidad / 100));

        my_tables[serie].table_data[index].total = parseInt(total);
        my_tables[serie].table_data[index].importe = importe;
        my_tables[serie].table_data[index].costo_total = costo_total;
        my_tables[serie].table_data[index].utilidad = utilidad;
        my_tables[serie].table_data[index].precio_min = parseFloat(utilidad + costo_total);

        $("#total_" + td_id).val(total);
        $("#importe_" + td_id).val(formatPrice(importe));
        $("#costo_total_" + td_id).val(costo_total.toFixed(2));
        $("#utilidad_" + td_id).val(utilidad.toFixed(2));
        $("#precio_min_" + td_id).val(formatPrice(parseFloat(utilidad + costo_total)));

        return importe;
    }

    function delete_row(serie, index) {
        my_tables[serie].table_data.splice(index, 1);

        if (my_tables[serie].table_data.length == 0) {
            delete my_tables[serie];
        }

        //Aqui hago el render las tablas
        $("#tables_template").html('');
        for (var key in my_tables) {
            $("#tables_template").append(render_template(my_tables[key]));
        }

        //Asigno los eventos del html rendereado
        set_events();
    }

    function set_events() {
        $(".input_table").on('focus', function () {
            $(this).select();
        });

        $(".input_cantidad").on('keyup', function () {
            var input = $(this);
            var serie = input.attr('data-serie');
            var index = input.attr('data-index');
            var talla = input.attr('data-talla');

            var cantidad = isNaN(parseInt(input.val())) ? 0 : parseInt(input.val());
            my_tables[serie].table_data[index].cantidades[talla] = cantidad;
            refresh_screen();
        });

        $(".input_costo_unitario").on('keyup', function () {
            var input = $(this);
            var serie = input.attr('data-serie');
            var index = input.attr('data-index');

            var costo_unitario = isNaN(parseFloat(input.val())) ? 0 : parseFloat(input.val());

            my_tables[serie].table_data[index].costo_unitario = costo_unitario;
            refresh_screen();
        });

        $(".input_precio_max").on('keyup', function () {
            var input = $(this);
            var serie = input.attr('data-serie');
            var index = input.attr('data-index');

            var precio_max = isNaN(parseFloat(input.val())) ? 0 : parseFloat(input.val());
            my_tables[serie].table_data[index].precio_max = precio_max;
        });
    }

    function render_template(table) {

        var template = '<div id="block_' + table.serie + '" class="row">';
        template += '<table id="tabla_' + table.serie + '" cellpadding="0" cellspacing="0" class="table table-striped table-bordered tableStyle">';
        template += '<thead><tr>';
        template += '<th style="width: 250px;">Plantilla</th>';
        template += '<th style="width: 20px;">Serie</th>';
        for (var i = 0; i < table.tallas.length; i++) {
            template += '<th style="width: 35px;">' + table.tallas[i] + '</th>';
        }
        template += '<th style="width: 50px;">Total</th>';
        template += '<th style="width: 50px;">Costo Unit.</th>';
        template += '<th style="width: 50px;">Importe</th>';
        template += '<th style="width: 50px;">Costo Total</th>';
        template += '<th style="width: 50px;">Utilidad</th>';
        template += '<th style="width: 50px;">Precio Min.</th>';
        template += '<th style="width: 50px;">Precio Max</th>';
        template += '<th style="width: 30px;"></th>';
        template += '</tr></thead>';

        template += '<tbody>';
        for (var i = 0; i < table.table_data.length; i++) {
            var td_id = table.serie + '_' + i;
            template += '<tr id="tr_' + td_id + '">';
            template += '<td>' + table.table_data[i].nombre + '</td>';
            template += '<td style="text-align: center;">' + table.serie + '</td>';
            for (var j = 0; j < table.tallas.length; j++) {
                template += '<td><input';
                template += ' id="input_' + table.serie + '_' + i + '_' + table.tallas[j] + '"';
                template += ' data-talla="' + table.tallas[j] + '" ';
                template += ' data-serie="' + table.serie + '" ';
                template += ' data-index="' + i + '" ';
                template += ' value="' + table.table_data[i].cantidades[table.tallas[j]] + '" ';
                template += ' type="text" class="input_table input_cantidad"></td>';
            }

            template += '<td><input type="text" value="' + table.table_data[i].total + '" id="total_' + td_id + '" class="input_table_readonly" readonly></td>';

            template += '<td><input';
            template += ' id="costo_unitario_' + table.serie + '_' + i + '"';
            template += ' data-serie="' + table.serie + '" ';
            template += ' data-index="' + i + '" ';
            template += ' value="' + formatPrice(table.table_data[i].costo_unitario) + '" ';
            template += ' type="text" class="input_table input_costo_unitario"></td>';


            template += '<td><input type="text" value="' + formatPrice(table.table_data[i].importe) + '" id="importe_' + td_id + '" class="input_table_readonly" readonly></td>';
            template += '<td><input type="text" value="' + table.table_data[i].costo_total.toFixed(2) + '" id="costo_total_' + td_id + '" class="input_table_readonly" readonly></td>';
            template += '<td><input type="text" value="' + table.table_data[i].utilidad.toFixed(2) + '" id="utilidad_' + td_id + '" class="input_table_readonly" readonly></td>';
            template += '<td><input type="text" value="' + formatPrice(table.table_data[i].precio_min) + '" id="precio_min_' + td_id + '" class="input_table_readonly" readonly></td>';

            template += '<td><input';
            template += ' id="precio_max_' + table.serie + '_' + i + '"';
            template += ' data-serie="' + table.serie + '" ';
            template += ' data-index="' + i + '" ';
            template += ' value="' + formatPrice(table.table_data[i].precio_max) + '" ';
            template += ' type="text" class="input_table input_precio_max"></td>';

            template += '<td style="text-align: center;"><a href="#" onclick="delete_row(\'' + table.serie + '\', ' + i + ')" class="btn btn-xs btn-danger">';
            template += '<i class="fa fa-remove"></i>';
            template += '</a></td>';

            template += '</tr>';
        }
        template += '</tbody>';
        template += '</table></div>';

        return template;

    }
</script>