;
var ruta = $("#base_url").val();
var lst_producto = [];
var operaciones = [];

$(document).ready(function () {
    $("#agregarproveedor").load(ruta + 'proveedor/form');
    $("#agregarmarca").load(ruta + 'marca/form');
    $("#agregargrupo").load(ruta + 'grupo/form');
    $("#agregarfamilia").load(ruta + 'familia/form');
    $("#agregarlinea").load(ruta + 'linea/form');

    $('#tipo_operacion option').each(function (key, val) {
        var item = $(val);
        if (item.val() != '') {
            operaciones.push({
                id: item.val(),
                nombre: item.text()
            });
        }
    });

    $('#tipo_operacion').html('');

    $('#refresh_productos').on('click', function () {
        $("#loading_save_venta").modal('show');
        $.ajax({
            url: ruta + 'venta_new/refresh_productos',
            type: 'GET',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                var select_producto = $('#producto_id');
                var productos = data.productos;

                select_producto.chosen('destroy');
                select_producto.html('<option value=""></option>');
                $('#close_add_producto').trigger('click');

                for (var i = 0; i < productos.length; i++) {
                    var temp = '<option value="' + productos[i].producto_id + '" data-impuesto="' + productos[i].porcentaje_impuesto + '">';
                    if ($('#producto_what_codigo').val() == 'AUTO')
                        temp += productos[i].producto_id + ' - ' + productos[i].producto_nombre;
                    else
                        temp += productos[i].codigo + ' - ' + productos[i].producto_nombre;

                    if ($('#barra_activa').val() == 1 && productos[i].barra != "")
                        temp += ' CB: ' + productos[i].barra;

                    temp += '</option>';

                    select_producto.append(temp);
                }

                $('#producto_id').chosen({
                    search_contains: true
                });
                $('.chosen-container').css('width', '100%');
            },
            complete: function (data) {
                $("#loading_save_venta").modal('hide');
            },
            error: function (data) {
                alert('not');
            }
        });
    });
    //CONFIGURACIONES INICIALES
    App.sidebar('close-sidebar');

    $('.date-picker').datepicker({format: 'dd/mm/yyyy'});
    $('.date-picker').css('cursor', 'pointer');

    $('#producto_id, #local_id, #moneda_id, #tipo_documento').chosen({
        search_contains: true
    });
    $('.chosen-container').css('width', '100%');


    $("#local_text").html($("#local_id option:selected").text());

    var ctrlPressed = false;
    var tecla_ctrl = 17;
    var tecla_enter = 13;

    var F6 = 117;

    $(document).off('keyup');
    $(document).off('keydown');

    $(document).keydown(function (e) {

        if (e.keyCode == tecla_ctrl) {
            $('.help-key, .help-key-side').show();
            ctrlPressed = true;
        }

        if (e.keyCode == F6) {
            e.preventDefault();
        }
    });

    $(document).keyup(function (e) {
        if (e.keyCode == tecla_ctrl) {
            $('.help-key, .help-key-side').hide();
            ctrlPressed = false;
        }

        if ($('.block_producto_unidades').css('display') != 'none')
            if (ctrlPressed && e.keyCode == tecla_enter) {
                e.stopImmediatePropagation();
                $("#add_producto").trigger('click');
            }

        if (e.keyCode == F6 && $(".modal").is(":visible") == false) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $("#terminar_ajuste").click();
        }

        // if (e.keyCode == F6 && $("#dialog_cotizar").is(":visible") == true) {
        //     e.preventDefault();
        //     e.stopImmediatePropagation();
        //     $('.save_venta_contado[data-imprimir="1"]').first().click();
        // }
    });

    // EVENTOS FUNCIONALES

    $("#local_id").on('change', function () {
        $("#local_text").html($("#local_id option:selected").text());
    });

    $("#producto_id").on('change', function (e) {
        get_productos_unidades('change');
    });

    $("#moneda_id").on('change', function () {
        var tasa = $('#moneda_id option:selected').attr('data-tasa');
        var simbolo = $('#moneda_id option:selected').attr('data-simbolo');
        var nombre = $('#moneda_id option:selected').attr('data-nombre');

        $("#tasa").val(tasa);
        $('.tipo_moneda').html(simbolo);

        if ($(this).val() != $('#MONEDA_DEFECTO_ID').val()) {
            $('#block_tasa').show();
            $("#tasa").trigger('focus');
        }
        else {
            $('#block_tasa').hide();
        }
        $("#moneda_text").html(nombre);
        refresh_right_panel();

    });

    $("#tasa").on('keyup', function () {
        refresh_right_panel();
    });

    $("#tasa").on('focus', function () {
        $(this).select();
    });

    $('#tipo_movimiento').on('change', function () {
        var tipo_movimiento = $(this);
        var tipo_operacion = $('#tipo_operacion');
        $('#movimiento_text').html($("#tipo_movimiento option:selected").text());

        $('#otros_valor_block').hide();
        $('#otros_val').val('');
        tipo_operacion.html('<option></option>');

        if (tipo_movimiento.val() == 1) {
            for (var i = 0; i < operaciones.length; i++) {
                if (operaciones[i].id == '09' || operaciones[i].id == '99' || operaciones[i].id == '16') {
                    tipo_operacion.append('<option value="' + operaciones[i].id + '">' + operaciones[i].nombre + '</option>')
                }
            }
        }
        if (tipo_movimiento.val() == 2) {
            for (var i = 0; i < operaciones.length; i++) {
                if (operaciones[i].id == '07' || operaciones[i].id == '12' || operaciones[i].id == '13' || operaciones[i].id == '14' || operaciones[i].id == '15' || operaciones[i].id == '09' || operaciones[i].id == '99') {
                    tipo_operacion.append('<option value="' + operaciones[i].id + '">' + operaciones[i].nombre + '</option>')
                }
            }
        }
    });

    $("#tipo_operacion").on('change', function () {
        $("#operacion_text").html($("#tipo_operacion option:selected").text());
        var oper = $(this).val();

        $('#otros_valor_block').hide();
        $('#otros_val').val('');

        if (oper == '99') {
            $('#otros_valor_block').show();
        }


    });

    $("#tipo_documento").on('change', function () {
        $("#documento_text").html($("#tipo_documento option:selected").text());
        if ($(this).val() == '09') {
            var local_id = $("#local_id").val();
            $.ajax({
                url: ruta + 'ajuste/getGuiaRemision/' + local_id,
                dataType: 'json',
                success: function (data) {
                    $('#serie_doc').attr('value', data.serie);
                    $('#numero_doc').attr('value', data.correlativo);
                }
            })
        } else {
            $('#serie_doc').attr('value', '');
            $('#numero_doc').attr('value', '');
        }
    });


    $("#add_producto").on('click', function () {
        var total = parseFloat($('#total_minimo').val());
        var stock = parseFloat($('#stock_actual').attr('data-stock'));

        if (total <= 0) {
            show_msg('warning', '<h4>Error. </h4><p>Total no puede ser cero.</p>');
            $('.cantidad-input[data-index="0"]').first().trigger('focus');
            return false;
        }

        if ($('#tipo_movimiento').val() == 2) {
            if (total > stock) {
                show_msg('warning', '<h4>Error. </h4><p>Stock Insuficiente.</p>');
                $('.cantidad-input[data-index="0"]').first().trigger('focus');
                return false;
            }
        }

        add_producto();
    });

    $("#close_add_producto").on('click', function () {
        $("#producto_id").val("").trigger("chosen:updated");
        $("#producto_id").change();
    });

    $("#add_todos").on('click', function () {
        var index = $('.cantidad-input').length - 1;
        $('.cantidad-input').val('0');
        $('.cantidad-input[data-index="' + index + '"]').first().val($('#stock_actual').attr('data-stock'));
        refresh_totals();
    });

    $("#stock_actual").on('click', function () {
        var stock = $(this);
        var stock_total = $("#stock_total");

        var temp = stock.html();
        stock.html(stock.attr('data-template'));
        stock.attr('data-template', temp);

        var temp = stock_total.html();
        stock_total.html(stock_total.attr('data-template'));
        stock_total.attr('data-template', temp);

    });

    $("#tabla_vista").on('click', function () {
        update_view(get_active_view());
    });

    //EVENTOS DEL PANEL INFERIOR
    $("#terminar_ajuste").click('on', function (e) {

        if (lst_producto.length == 0) {
            show_msg('warning', '<h4>Error. </h4><p>Debe agregar al menos un producto para realizar el ajuste.</p>');
            return false;
        }

        save_ajuste();
    });

    $("#reiniciar_ajuste").on('click', function () {
        $('#confirm_venta_text').html('Si reinicias el ajuste perderas todos los productos agregados. Estas seguro?');
        $('#confirm_venta_button').attr('onclick', 'reset_ajuste();');
        $('#dialog_venta_confirm').modal('show');
    });

    $("#cancelar_ajuste").on('click', function () {
        $('#confirm_venta_text').html('Si cancelas el ajuste perderas todos los cambios realizados. Estas seguro?');
        $('#confirm_venta_button').attr('onclick', 'cancel_ajuste();');
        $('#dialog_venta_confirm').modal('show');
    });

    $('#confirm_venta_imprimir').on('click', function(){
        var datos = {
            'ajuste_id': $('#hdAjusteId').val(),
            'serie': $('#hdSerie').val(),
            'numero': $('#hdNumero').val(),
            'operacion': $('#hdOperacion').val()
        };

        $.ajax({
            url: ruta + 'impresion/get_guia_remision',
            data: datos,
            type: 'POST',
            success: function (data) {
                console.log(data);
                $.ajax({
                    url: 'http://localhost:8080',
                    method: 'POST',
                    data: {
                        documento: 'guia',
                        dataset: data
                    },
                    success: function (data) {
                        show_msg('success', 'La guia de remisi&oacute;on se esta imprimiendo');
                    },
                    error: function (data) {
                        alert('Error de impresion')
                    },
                    complete: function (data) {
                    }

                })
            }
        });
    });
});

//FUNCIONES DE MANEJO DE LOS AJUSTES

function prepare_detalles_productos() {
    var productos = [];

    for (var i = 0; i < lst_producto.length; i++) {

        var cantidades = {};
        for (var j = 0; j < lst_producto[i].detalles.length; j++) {
            if (cantidades[lst_producto[i].detalles[j].unidad] == undefined)
                cantidades[lst_producto[i].detalles[j].unidad] = lst_producto[i].detalles[j].cantidad;
            else
                cantidades[lst_producto[i].detalles[j].unidad] += lst_producto[i].detalles[j].cantidad;
        }

        var costos = {};
        for (var j = 0; j < lst_producto[i].detalles.length; j++) {
            if (costos[lst_producto[i].detalles[j].unidad] == undefined)
                costos[lst_producto[i].detalles[j].unidad] = lst_producto[i].detalles[j].unidades;
        }

        for (var unidad in cantidades) {
            if (cantidades[unidad] != 0) {
                var producto = {};
                producto.id_producto = lst_producto[i].producto_id;
                producto.costo = costos[unidad] * lst_producto[i].costo_unitario;
                producto.unidad_medida = unidad;
                producto.cantidad = cantidades[unidad];
                producto.detalle_importe = producto.cantidad * producto.costo;
                producto.costo_unitario = lst_producto[i].costo_unitario;
                productos.push(producto);
            }
        }

    }

    return JSON.stringify(productos);

}

function save_ajuste() {

    $("#loading_save_venta").modal('show');
    var tipo_movimiento = $('#tipo_movimiento').val();
    var tipo_documento = $('#tipo_documento').val();
    var form = $('#form_venta').serialize();
    var detalles_productos = prepare_detalles_productos();

    $.ajax({
        url: ruta + 'ajuste/save_ajuste',
        type: 'POST',
        dataType: 'json',
        data: form + '&detalles_productos=' + detalles_productos,
        success: function (data) {

            if (data.success == '1') {
                show_msg('success', '<h4>Correcto. </h4><p>El ajuste se ha guardado con exito.</p>');
                $.ajax({
                    url: ruta + 'ajuste',
                    success: function (datos) {
                        $("#loading_save_venta").modal('hide');
                        $(".modal-backdrop").remove();
                        $('#page-content').html(datos);
                        $('#producto_id').trigger('chosen:open');

                        if(tipo_movimiento=='2' && tipo_documento=='09'){ //Salida y guia de remision
                            $("#dialog_venta_imprimir").modal('show');
                            $('#hdAjusteId').attr('value', data.ajuste.id);
                            $('#hdSerie').attr('value', data.ajuste.serie);
                            $('#hdNumero').attr('value', data.ajuste.numero);
                            $('#hdOperacion').attr('value', data.ajuste.operacion);
                        }
                    }
                });
            } else {
                show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');
            }
        },
        error: function (data) {

        },
        complete: function (data) {

        }
    });
}

//FUNCIONES INTERNAS

//funcion para agregar los productos de la venta
function add_producto() {

    var producto_id = $("#producto_id").val();
    var local_id = $("#local_id").val();

    var index = get_index_producto(producto_id);

    if (index == -1) {
        //AGREGO EL PRODUCTO E INICIALIZO SUS VALORES
        var producto = {};
        producto.index = lst_producto.length;
        producto.producto_id = producto_id;
        producto.producto_nombre = encodeURIComponent($("#producto_id option:selected").text());
        producto.costo_unitario = isNaN(parseFloat($("#costo_unitario").val())) ? 0 : parseFloat($("#costo_unitario").val());

        producto.um_min = $("#um_minimo").html().trim();
        producto.um_min_abr = $("#um_minimo").attr('data-abr');

        producto.total_local = {};
        producto.detalles = [];

        $("#local_id option").each(function () {
            var local = $(this);
            if (local.val() == local_id)
                producto.total_local['local' + local.val()] = parseFloat($("#total_minimo").val());
            else
                producto.total_local['local' + local.val()] = 0;

            $(".cantidad-input").each(function () {
                var input = $(this);
                var detalle = {};

                detalle.local_id = local.val();
                detalle.local_nombre = encodeURIComponent(local.text());
                if (local.val() == local_id)
                    detalle.cantidad = parseFloat(input.val());
                else
                    detalle.cantidad = parseFloat(0);
                detalle.unidad = input.attr('data-unidad_id');
                detalle.unidad_nombre = input.attr('data-unidad_nombre');
                detalle.unidad_abr = input.attr('data-unidad_abr');
                detalle.unidades = input.attr('data-unidades');
                detalle.orden = input.attr('data-orden');

                producto.detalles.push(detalle);

            });

        });

        producto.total_minimo = 0;
        for (var local_index in producto.total_local)
            producto.total_minimo += parseFloat(producto.total_local[local_index]);

        producto.subtotal = parseFloat(producto.total_minimo * producto.costo_unitario);

        lst_producto.push(producto);
    }
    else {
        //EDITO LA INFORMACION DETALLADA DEL PRODUCTO
        lst_producto[index].costo_unitario = isNaN(parseFloat($("#costo_unitario").val())) ? 0 : parseFloat($("#costo_unitario").val());
        lst_producto[index].total_local['local' + local_id] = parseFloat($("#total_minimo").val());
        lst_producto[index].total_minimo = 0;
        for (var local_index in lst_producto[index].total_local)
            lst_producto[index].total_minimo += parseFloat(lst_producto[index].total_local[local_index]);

        lst_producto[index].subtotal = parseFloat(lst_producto[index].total_minimo * lst_producto[index].costo_unitario);

        $(".cantidad-input").each(function () {
            var input = $(this);

            for (var i = 0; i < lst_producto[index].detalles.length; i++) {
                if (lst_producto[index].detalles[i].local_id == local_id && lst_producto[index].detalles[i].unidad == input.attr('data-unidad_id')) {
                    lst_producto[index].detalles[i].cantidad = parseFloat(input.val());
                }
            }

        });
    }

    $("#producto_id").val("").trigger("chosen:updated");
    $("#producto_id").change();


    update_view(get_active_view());

    refresh_right_panel();

    setTimeout(function () {
        $('#producto_id').trigger('chosen:open');
        setTimeout(function () {
            $('#producto_id_chosen .chosen-search input').trigger('focus');

        }, 5)
    }, 500);

}

//edita un producto en la tabla
function edit_producto(producto_id) {
    $("#producto_id").val(producto_id).trigger("chosen:updated");
    $("#producto_id").change();
}

//elimina un producto de la tabla
function delete_producto(item) {

    lst_producto.splice(item, 1);

    for (var i = 0; i < lst_producto.length; i++) {
        lst_producto[i].index = i;
    }
    update_view(get_active_view());
    refresh_right_panel();
    $("#producto_id").val("").trigger("chosen:updated");
    $("#producto_id").change();
}

//funcion para mostrar las tabla de los productos agregados
function update_view(type) {

    $("#body_productos").html('');
    if (lst_producto.length == 0)
        $("#head_productos").html('');
    else {
        switch (type) {
            case 'detalle': {
                $("#head_productos").html('<tr>' +
                    '<th>#</th>' +
                    '<th>Producto</th>' +
                    '<th>Detalles</th>' +
                    '<th>Acciones</th>' +
                    '</tr>');

                for (var i = 0; i < lst_producto.length; i++) {
                    addTable(lst_producto[i], type);
                }
                break;
            }
            case 'general': {
                $('#table_producto').css('white-space', 'nowrap');
                $("#head_productos").html('<tr>' +
                    '<th>#</th>' +
                    '<th>Producto</th>' +
                    '<th>Total Minimo</th>' +
                    '<th>Costo Unitario</th>' +
                    '<th>Subtotal</th>' +
                    '<th>Acciones</th>' +
                    '</tr>');

                for (var i = 0; i < lst_producto.length; i++) {
                    addTable(lst_producto[i], type);
                }
                break;
            }
        }
    }
}

//añade un elemento a la tabla, tiene sus variaciones dependiendo del tipo de vista
function addTable(producto, type) {
    var template = '<tr>';

    template += '<td>' + (producto.index + 1) + '</td>';
    template += '<td style="white-space: pre-wrap;">' + decodeURIComponent(producto.producto_nombre).trim() + '</td>';
    if (type == 'general') {
        template += '<td style="text-align: center;">' + producto.total_minimo + ' (' + producto.um_min + ')</td>';
        template += '<td>' + producto.costo_unitario + '</td>';
        template += '<td>' + parseFloat(producto.subtotal).toFixed(2) + '</td>';
    }
    if (type == 'detalle') {
        template += '<td style="text-align: center; width: 400px;">';

        template += '<div class="row" style="margin: 0;">';
        template += '<div class="col-sm-4" style="background-color: #ADADAD; color: #fff; padding: 0;">Cantidad</div>';
        template += '<div class="col-sm-4" style="background-color: #ADADAD; color: #fff;">UM</div>';
        template += '<div class="col-sm-4" style="background-color: #ADADAD; color: #fff;">Unidades</div>';
        template += '</div>';

        var det = detalles_sort(producto.detalles);
        for (var i = 0; i < det.length; i++) {
            template += '<div class="row" style="margin: 0;">';
            if (det[i].cantidad != 0) {
                template += '<div class="col-sm-4" style="border: solid 1px #e2e2e2;">' + det[i].cantidad + '</div>';
                template += '<div class="col-sm-4" style="border: solid 1px #e2e2e2;">' + det[i].unidad_abr + '</div>';
                template += '<div class="col-sm-4" style="border: solid 1px #e2e2e2;">' + det[i].unidades + ' ' + producto.um_min_abr + '</div>';
                template += '</div>';
            }
        }

        template += '</td>';
    }

    template += '<td style="text-align: center;">';

    template += '<div class="btn-group"><a class="btn btn-default" data-toggle="tooltip" title="Editar cantidad" data-original-title="Editar cantidad" onclick="edit_producto(' + producto.producto_id + ');">';
    template += '<i class="fa fa-edit"></i></a>';
    template += '</div>';

    template += '<div style="margin-left: 10px;" class="btn-group"><a class="btn btn-danger" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="delete_producto(' + producto.index + ');">';
    template += '<i class="fa fa-trash-o"></i></a>';
    template += '</div>';
    template += '</td>';

    template += '</tr>';

    $("#body_productos").append(template);
}

//devuelve la vista activa
function get_active_view() {
    if ($("#tabla_vista").prop('checked'))
        return 'detalle';
    else
        return 'general';
}

//reinicio la venta, solo elimino los productos agregados pero mantengo el estado de la venta
function reset_ajuste() {
    lst_producto = [];
    update_view();
    refresh_right_panel();
    $('#dialog_venta_confirm').modal('hide');
}

//cancelo la venta, estado inicial y si la venta es en espera se elimina
function cancel_ajuste() {
    $('#dialog_venta_confirm').modal('hide');
    $("#loading_save_venta").modal('show');
    $.ajax({
        url: ruta + 'ajuste',
        success: function (data) {
            $("#loading_save_venta").modal('hide');
            $(".modal-backdrop").remove();
            $('#page-content').html(data);
        }
    });
}

//funcion para refrescar los totales cuando ocurren eventos
function refresh_totals() {
    var cantidad_input = $('.cantidad-input');

    var data_total = 0;
    var importe_total = 0;
    cantidad_input.each(function () {
        var input = $(this);
        if (input.val() != 0) {
            data_total += parseFloat(input.val() * input.attr('data-unidades'));
            importe_total += parseFloat($("#costo_unitario").val() * input.val() * input.attr('data-unidades'));
        }
    });

    importe_total = isNaN(importe_total) ? 0 : importe_total;
    $("#total_minimo").val(data_total);
    $("#importe").val(parseFloat(importe_total).toFixed(2));
}

//function para refrescar el panel derecho
function refresh_right_panel() {

    if (lst_producto.length > 0) {
        $("#moneda_block_input").hide();
        $("#moneda_block_text").show();
        $("#tasa").attr('readonly', 'readonly');

        $("#local_block_input").hide();
        $("#local_block_text").show();

        $("#operacion_block_input").hide();
        $("#operacion_block_text").show();

        $("#documento_block_input").hide();
        $("#documento_block_text").show();

        $("#movimiento_block_input").hide();
        $("#movimiento_block_text").show();

    } else {
        $("#moneda_block_text").hide();
        $("#moneda_block_input").show();
        $("#tasa").removeAttr('readonly');

        $("#local_block_input").show();
        $("#local_block_text").hide();

        $("#operacion_block_input").show();
        $("#operacion_block_text").hide();

        $("#documento_block_input").show();
        $("#documento_block_text").hide();

        $("#movimiento_block_input").show();
        $("#movimiento_block_text").hide();
    }


    var total = 0;
    var tipo_moneda = $('#moneda_id option:selected').attr('data-tasa');
    var tasa = $('#tasa').val();
    var operacion = $('#moneda_id option:selected').attr('data-oper');

    for (var i = 0; i < lst_producto.length; i++) {
        total += lst_producto[i].subtotal;
    }

    total_importe = total;


    $("#total_importe").val(parseFloat(total_importe).toFixed(2));
    $("#total_producto").val(lst_producto.length);


}

//actualizo la informacion del stock
function set_stock_info() {

    $("#stock_actual").html('Calculando Stock...');
    $("#stock_total").html('Calculando Stock...');

    var producto_id = $("#producto_id").val();
    var stock_total_minimo = 0;
    for (var i = 0; i < lst_producto.length; i++)
        if (lst_producto[i].producto_id == producto_id)
            stock_total_minimo += lst_producto[i].total_minimo;
    $.ajax({
        url: ruta + 'ajuste/set_stock',
        type: 'POST',
        headers: {
            Accept: 'application/json'
        },
        data: {
            'stock_minimo': $("#total_minimo").val(),
            'stock_total_minimo': stock_total_minimo,
            'producto_id': producto_id,
            'local_id': $("#local_id").val(),
            'IO': $('#tipo_movimiento').val()
        },
        success: function (data) {
            if (data.stock_actual.max_um_id != data.stock_actual.min_um_id)
                $("#stock_actual").html(data.stock_actual.cantidad + ' ' + data.stock_actual.max_um_nombre + ' / ' + data.stock_actual.fraccion + ' ' + data.stock_actual.min_um_nombre);
            else
                $("#stock_actual").html(data.stock_actual.cantidad + ' ' + data.stock_actual.max_um_nombre);

            if (data.stock_total.max_um_id != data.stock_total.min_um_id)
                $("#stock_total").html(data.stock_total.cantidad + ' ' + data.stock_total.max_um_nombre + ' / ' + data.stock_total.fraccion + ' ' + data.stock_total.min_um_nombre);
            else
                $("#stock_total").html(data.stock_total.cantidad + ' ' + data.stock_total.max_um_nombre);

            $("#stock_actual").attr('data-stock', data.stock_minimo);
            $("#stock_total").attr('data-stock', data.stock_total_minimo);

            $("#stock_actual").attr('data-template', data.stock_minimo_left + ' ' + data.stock_actual.min_um_nombre);
            $("#stock_total").attr('data-template', data.stock_total_minimo_left + ' ' + data.stock_total.min_um_nombre);

            var popover_stock = $("#popover_stock");
            popover_stock.html('');
            $("#local_id option").each(function () {
                var local = $(this);

                var template = '<div class="row">';
                template += '<div class="col-md-6">' + local.text() + '</div>';
                template += '<div class="col-md-6">11 CAJA / 136 UNIDAD</div>';
                template += '</div>';

                popover_stock.append(template);
            });

        }
    });

}

//creo el template de las unidades
function create_unidades_template(index, unidad, unidad_minima) {

    if (unidad_minima.id_unidad == unidad.id_unidad)
        unidad.unidades = 1;

    var template = '<div class="col-md-3">';
    template += '<div>';
    template += '<input type="number" class="input-square input-mini form-control text-center cantidad-input" ';
    template += 'id="cantidad_' + unidad.id_unidad + '" ';
    template += 'data-unidades="' + unidad.unidades + '" ';
    template += 'data-unidad_id="' + unidad.id_unidad + '" ';
    template += 'data-unidad_nombre="' + unidad.nombre_unidad + '" ';
    template += 'data-unidad_abr="' + unidad.abr + '" ';
    template += 'data-orden="' + unidad.orden + '" ';
    template += 'data-cualidad="' + unidad.producto_cualidad + '" ';
    template += 'data-index="' + index + '" ';
    template += 'onkeydown="return soloDecimal(this, event);">';
    template += '</div>';

    template += '<h6>' + unidad.nombre_unidad + ' (' + unidad.unidades + ' ' + unidad_minima.abr + ')</h6>';


    template += '</div>';

    return template;
}

//preparo los valores de las unidades
function prepare_unidades_value(producto_id, local_id, unidad) {

    var cantidad = $("#cantidad_" + unidad.id_unidad);
    var cant = get_value_producto(producto_id, local_id, unidad.id_unidad, -1);
    if (cant == -1) {
        cantidad.attr('value', 0);
        cantidad.attr('data-value', 0);
    }
    else {
        cantidad.attr('value', cant);
        cantidad.attr('data-value', cant);
    }

    if (unidad.producto_cualidad == "MEDIBLE") {
        cantidad.attr('min', '0');
        cantidad.attr('step', '1');
    } else {
        cantidad.attr('min', '0.0');
        cantidad.attr('step', '0.1');

    }
}

//suscribo eventos a las unidades
function prepare_unidades_events() {

    var cantidad_input = $('.cantidad-input');
    //selecciona la cantidad cuando haces focus
    cantidad_input.on('focus', function () {
        $(this).select();
    });

    //implementacion para poder navegar con las flechas
    var letra_left = 37, letra_right = 39;
    var max_index = cantidad_input.length - 1;

    cantidad_input.keydown(function (e) {
        var input = $(this);
        var index = input.attr('data-index');

        switch (e.keyCode) {
            //Moverse a travez de las unidades con la flecha de izquierda y derecha
            case letra_right: {
                e.preventDefault();
                var next = 0;
                if (index < max_index)
                    next = ++index;
                $('.cantidad-input[data-index="' + next + '"]').first().trigger('focus');
                break;
            }
            case letra_left: {
                e.preventDefault();
                var prev = max_index;
                if (index > 0)
                    prev = --index;
                $('.cantidad-input[data-index="' + prev + '"]').first().trigger('focus');
                break;
            }
        }

    });

    //calculo del total y el importe cuando hay cambios en las cantidades
    cantidad_input.bind('keyup change click mouseleave', function () {
        var item = $(this);
        if (item.val() != item.attr('data-value')) {
            item.attr('data-value', item.val());
            refresh_totals();
        }


    });
}


//devuelve el indice del producto el el array lst_producto definido pro sus parametros
function get_index_producto(producto_id) {
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id) {
            return lst_producto[i].index;
        }
    }

    return -1;
}

//devuelve el valor del producto en el array lst_producto definido por sus parametros
function get_value_producto(producto_id, local_id, um_id, defecto) {
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id) {
            for (var j = 0; j < lst_producto[i].detalles.length; j++) {
                if (lst_producto[i].detalles[j].local_id == local_id && lst_producto[i].detalles[j].unidad == um_id)
                    return lst_producto[i].detalles[j].cantidad;
            }
        }
    }
    if (defecto != undefined)
        return defecto;
    else return 0;
}

//funcion para organizar la unidades de medida
function detalles_sort(detalles) {

    detalles.sort(function (a, b) {
        return parseInt(a.orden) - parseInt(b.orden);
    });

    return detalles;

}

function show_msg(type, msg) {
    $.bootstrapGrowl(msg, {
        type: type,
        delay: 5000,
        allow_dismiss: true
    });
}

function agregarProducto() {
    $('#producto_id').val(0);
    $("#producto_id").trigger('chosen:updated');
    var a = get_productos_unidades('click');
    if (a != false) {
        $("#productomodal").load(ruta + 'producto/agregar', function () {
            $('#btnGuardar').removeAttr("onclick");
            $('#btnGuardar').attr("onclick", "confirm_save('ajuste')");
        });
        $('#productomodal').modal('show');
    }
}

function agregarfamilia() {
    $("#formagregarfamilia").trigger("reset");
    $('#agregarfamilia').modal('show');
    setTimeout(function () {
        $('#confirmar_boton_familia').removeAttr("onclick");
        $('#confirmar_boton_familia').attr("onclick", "guardar_familia('producto')");

    }, 10);
}

function agregarmarca() {
    $("#formagregarmarca").trigger("reset");
    $('#agregarmarca').modal('show');
    setTimeout(function () {
        $('#confirmar_boton_marca').removeAttr("onclick");
        $('#confirmar_boton_marca').attr("onclick", "guardar_marca('producto')");

    }, 10);
}

function agregargrupo() {
    $("#formagregargrupo").trigger("reset");
    $('#agregargrupo').modal('show');
    setTimeout(function () {
        $('#confirmar_boton_grupo').removeAttr("onclick");
        $('#confirmar_boton_grupo').attr("onclick", "guardar_grupo('producto')");
    }, 10);
}

function agregarproveedor() {
    $("#formagregarproveedor").trigger("reset");
    $('#agregarproveedor').modal('show');
    setTimeout(function () {
        $('#confirmar_boton_proveedor').removeAttr("onclick");
        $('#confirmar_boton_proveedor').attr("onclick", "guardar_proveedor('producto')");
    }, 10);
}

function agregarlinea() {
    $("#formagregarlinea").trigger("reset");
    $('#agregarlinea').modal('show');
    setTimeout(function () {
        $('#confirmar_boton_linea').removeAttr("onclick");
        $('#confirmar_boton_linea').attr("onclick", "guardar_linea('producto')");
    }, 10);
}

function update_producto(id, nombre, impuesto, producto_id) {
    $('#producto_id').append('<option value="' + producto_id + '">' + id + ' - ' + nombre + '</option>');
    $('#producto_id').val(producto_id);
    $("#producto_id").trigger('chosen:updated');
    get_productos_unidades('click');
}

function getproductosbylocal() {
    $("#cargando_modal").modal({
        show: true,
        backdrop: 'static'
    });
    $.ajax({
        url: '<?= $ruta?>producto/getbylocal',
        data: {'local': $("#locales").val()},
        type: 'post',
        success: function (data) {

            $("#productostable").html(data);
            $("#cargando_modal").modal('hide');
        }
    })
}

function get_productos_unidades(evento) {
    //e.preventDefault();

    if ($("#tipo_operacion").val() == "") {
        show_msg('warning', '<h4>Error. </h4><p>Debe configurar la operacion correctamente. Por favo reviselos.</p>');
        $("#producto_id").val("").trigger('chosen:update');
        return false;
    }

    if ($("#tipo_movimiento").val() == "") {
        show_msg('warning', '<h4>Error. </h4><p>Debe configurar el movimiento correctamente. Por favo reviselos.</p>');
        $("#producto_id").val("").trigger('chosen:update');
        return false;
    }

    if ($("#tipo_documento").val() == "") {
        show_msg('warning', '<h4>Error. </h4><p>Debe configurar el documento correctamente. Por favo reviselos.</p>');
        $("#producto_id").val("").trigger('chosen:update');
        return false;
    }

    $(".block_producto_unidades").hide();

    if (evento == 'change') {
        if ($('#producto_id').val() == "") {
            return false;
        }
    }

    var producto_id = $('#producto_id').val();
    var local_id = $("#local_id").val();
    var moneda_id = $("#moneda_id").val();

    if ($('#producto_id').val() != null) {
        $("#loading").show();
        $.ajax({
            url: ruta + 'ajuste/get_productos_unidades',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {'producto_id': producto_id, 'moneda_id': moneda_id},
            success: function (data) {
                var form = $("#producto_form");
                form.html('');

                if (data.unidades.length > 0) {

                    var unidad_minima = data.unidades[data.unidades.length - 1];
                    $("#um_minimo").html(unidad_minima.nombre_unidad);
                    $("#um_minimo").attr('data-abr', unidad_minima.abr);

                    var index = 0;
                    for (var i = 0; i < data.unidades.length; i++) {

                        if (data.unidades[i].presentacion == '1')
                            form.append(create_unidades_template(index++, data.unidades[i], unidad_minima));

                        prepare_unidades_value(producto_id, local_id, data.unidades[i]);
                    }


                    //Este ciclo es para los datos iniciales del total y el importe
                    var total = 0;
                    $(".cantidad-input").each(function () {
                        var input = $(this);
                        if (input.val() != 0) {
                            total += parseFloat(input.val() * input.attr('data-unidades'));
                        }
                    });
                    $("#total_minimo").val(total);
                    console.log(data);
                    $('#costo_unitario').val(data.costo.costo);
                    set_stock_info();


                    //SUSCRIBOS EVENTOS
                    prepare_unidades_events();

                    refresh_right_panel();
                    refresh_totals();
                }
            },
            complete: function (data) {
                $("#loading").hide();
                $(".block_producto_unidades").show();
                $('.cantidad-input[data-index="0"]').first().trigger('focus');
                $(".modal-backdrop").remove();
            },
            error: function (data) {
                alert('Ha ocurrido un Error Inesperado.');
            }
        });
    }
    return true;
}