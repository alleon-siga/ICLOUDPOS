;
var ruta = $("#base_url").val();
var lst_producto = [];

$(document).ready(function () {

    $(document).off('keyup');
    $(document).off('keydown');

    //CONFIGURACIONES INICIALES
    App.sidebar('close-sidebar');

    $('.date-picker').datepicker({format: 'dd/mm/yyyy'});
    $('.date-picker').css('cursor', 'pointer');

    $('#producto_id, #local_id, #local_venta_id, #cliente_id, #moneda_id, #precio_id, #c_garante, #c_pago_periodo').chosen({
        search_contains: true
    });
    $('.chosen-container').css('width', '100%');

    //tecla_3 para mostrar los productos
    select_productos(51);


    //EVENTOS DEL TECLADO
    //CNTRL + 0 al 9 = Para seleccionar los select los numero son de 0(48) - 9(57)
    var ctrlPressed = false;
    var tecla_ctrl = 17;
    var tecla_espacio = 32;
    var tecla_enter = 13;
    var letra_up = 38, letra_down = 40;
    var F6 = 117;

    var disabled_save = false;
    $(document).keydown(function (e) {

        if (e.keyCode == tecla_ctrl) {
            $('.help-key, .help-key-side').show();
            ctrlPressed = true;
        }

        if (ctrlPressed && (e.keyCode >= 48 || e.keyCode <= 57)) {
            e.preventDefault();
            select_productos(e.keyCode);
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

        if ($('.chosen-container-active').length == 0 && $('.chosen-with-drop').length == 0)
            if ($('.block_producto_unidades').css('display') != 'none')
                if (!ctrlPressed && (e.keyCode == tecla_espacio)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    var max_index = parseInt($('.precio-input').length - 1);
                    var index = $('#precio_unitario').attr('data-index');
                    var next = 0;
                    if (index < max_index)
                        next = ++index;

                    $('.precio-input[data-index="' + next + '"]').first().click();
                }

        if (e.keyCode == F6 && $(".modal").is(":visible") == false) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $("#terminar_cotizar").click();
        }

        if (e.keyCode == F6 && $("#dialog_cotizar").is(":visible") == true) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $('.save_venta_contado[data-imprimir="1"]').first().click();
        }
    });


    // EVENTOS FUNCIONALES


    $("#producto_id").on('change', function (e) {

        e.preventDefault();

        $(".block_producto_unidades").hide();

        if ($(this).val() == "") {
            return false;
        }

        var producto_id = $(this).val();
        var precio_id = $("#precio_id").val();

        $("#loading").show();

        $.ajax({
            url: ruta + 'cotizar/get_productos_unidades',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {'producto_id': producto_id, 'precio_id': precio_id},
            success: function (data) {
                var form = $("#producto_form");
                var form_precio = $("#producto_precio");
                form.html('');
                form_precio.html('');


                var unidad_minima = data.unidades[data.unidades.length - 1];
                $("#um_minimo").html(unidad_minima.nombre_unidad);
                $("#um_minimo").attr('data-abr', unidad_minima.abr);

                var index = 0;
                for (var i = 0; i < data.unidades.length; i++) {

                    if (data.unidades[i].presentacion == '1')
                        form.append(create_unidades_template(index++, data.unidades[i], unidad_minima));

                    form_precio.append(create_precio_template(i, data.unidades[i]));
                    prepare_unidades_value(producto_id, data.unidades[i]);
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
                set_stock_info();


                //SUSCRIBOS EVENTOS
                prepare_unidades_events();
                prepare_precio_events();

                prepare_precio_value(producto_id, unidad_minima);

                refresh_right_panel();
                refresh_totals();
            },
            complete: function (data) {
                $("#loading").hide();
                $(".block_producto_unidades").show();

                $('.cantidad-input[data-index="0"]').first().trigger('focus');
            },
            error: function (data) {
                alert('not');
            }
        });

    });

    //Esta funcion esta desabilitda por el momento
    $("#precio_id").on('change', function () {

        var producto_id = $("#producto_id").val();
        var precio_id = $(this).val();

        $("#producto_precio").hide();
        $("#loading_precio").show();
        $.ajax({
            url: ruta + 'cotizar/get_productos_precios',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {'producto_id': producto_id, 'precio_id': precio_id},
            success: function (data) {
                var form_precio = $("#producto_precio");
                form_precio.html('');

                var unidad_minima = data.unidades[data.unidades.length - 1];
                for (var i = 0; i < data.unidades.length; i++) {
                    form_precio.append(create_precio_template(i, data.unidades[i]));
                }

                prepare_precio_value(producto_id, unidad_minima);


            },
            complete: function (data) {
                $("#loading_precio").hide();
                $("#producto_precio").show();
                $('.cantidad-input[data-index="0"]').first().trigger('focus');
            },
            error: function (data) {
                alert('not');
            }
        });
    });

    $("#editar_pu").on('click', function (e) {
        e.preventDefault();
        var edit_pu = $("#editar_pu");
        var pu = $("#precio_unitario");


        if (edit_pu.attr('data-estado') == '0') {
            pu.removeAttr('readonly');
            pu.trigger("focus");
            edit_pu.attr('data-estado', '1');
            edit_pu.html('<i class="fa fa-check"></i>');
        }
        else {
            pu.attr('readonly', 'readonly');
            edit_pu.attr('data-estado', '0');
            edit_pu.html('<i class="fa fa-edit"></i>');
            var flag = false;
            $(".precio-input").removeClass('precio-selected');
            $('.precio-input').each(function () {
                var input = $(this);

                if (input.val() == $("#precio_unitario").val()) {
                    flag = true;
                    $("#precio_unitario").attr('data-index', input.attr('data-index'));
                    $("#precio_unitario_um").html(input.attr('data-unidad_nombre'));
                    input.addClass('precio-selected');
                }
            });

            if (flag == false) {
                $("#precio_unitario_um").html('<span style="color: #f39c12;">Personalizado</span>');
            }

            refresh_totals();
        }
    });

    $("#precio_unitario").on('focus', function () {
        $(this).select();
    });

    $("#precio_unitario").on('keyup', function (e) {
        if (e.keyCode == tecla_enter) {
            $("#editar_pu").click();
            $('#add_producto').trigger("focus");
        }
        else
            refresh_totals();
    });

    $("#moneda_id").on('change', function () {
        var tasa = $('#moneda_id option:selected').attr('data-tasa');
        var simbolo = $('#moneda_id option:selected').attr('data-simbolo');

        $("#tasa").val(tasa);
        $('.tipo_moneda').html(simbolo);

        if ($('#moneda_id option:selected').val() != $('#MONEDA_DEFECTO_ID').val()) {
            $('#block_tasa').show();
            $("#tasa").trigger('focus');
        }
        else {
            $('#block_tasa').hide();
        }
        $("#moneda_text").html($('#moneda_id option:selected').text());
        refresh_right_panel();

    });

    $("#tasa").on('keyup', function () {
        refresh_right_panel();
    });

    $("#tasa").on('focus', function () {
        $(this).select();
    });

    $("#cliente_id").on('change', function () {

        if ($("#tipo_documento").val() == 1 && $("#cliente_id option:selected").attr('data-ruc') != 2) {
            show_msg('warning', '<h4>Error. </h4><p>El Cliente no tiene ruc para realizar venta en factura.</p>');
            select_productos(55);
        }

    });

    $("#tipo_pago").on('change', function () {

        if ($(this).val() == '2') {
            $('#block_credito_periodo').show();
        }
        else {
            $('#block_credito_periodo').hide();
        }

    });

    $("#c_pago_periodo").on('change', function () {
        if ($(this).val() == '5') {
            $('#block_periodo_per').show();
        }
        else {
            $('#block_periodo_per').hide();
        }

    });

    $("#tipo_documento").on('change', function () {

        if ($(this).val() == '1') {
            $('#block_impuesto').show();
            $('#block_subtotal').show();

            if ($("#tipo_documento").val() == 1 && $("#cliente_id option:selected").attr('data-ruc') != 2) {
                show_msg('warning', '<h4>Error. </h4><p>El Cliente no tiene ruc para realizar venta en factura.</p>');
                select_productos(49);
            }
        }
        else {
            $('#block_impuesto').hide();
            $('#block_subtotal').hide();
        }

        refresh_right_panel();
    });

    $("#add_producto").on('click', function () {
        var total = parseFloat($('#total_minimo').val());

        if (total <= 0) {
            show_msg('warning', '<h4>Error. </h4><p>Inserte una cantidad para realizar la cotizacion.</p>');
            $('.cantidad-input[data-index="0"]').first().trigger('focus');
            return false;
        }
        else if ($("#editar_pu").attr('data-estado') == '1') {
            show_msg('warning', '<h4>Error. </h4><p>Por favor debe confirmar el Precio Unitario de Venta.</p>');
            $('#precio_unitario').trigger('focus');
            return false;
        }

        add_producto();
    });

    $("#close_add_producto").on('click', function () {
        $("#producto_id").val("").trigger("chosen:updated");
        $("#producto_id").change();
    });

    $("#tabla_vista").on('click', function () {
        update_view(get_active_view());
    });

    //EVENTOS DEL PANEL INFERIOR
    $("#terminar_cotizar").click('on', function (e) {

        if (lst_producto.length == 0) {
            show_msg('warning', '<h4>Error. </h4><p>Debe agregar al menos un producto para realizar la venta.</p>');
            select_productos(51);
            return false;
        }

        end_cotizar();
    });

    $("#dialog_venta_imprimir").on('hidden.bs.modal', function () {
        $("#loading_save_venta").modal('show');
        $.ajax({
            url: ruta + 'cotizar',
            success: function (data) {
                $('#page-content').html(data);
                $("#loading_save_venta").modal('hide');
                $(".modal-backdrop").remove();
            }
        });
    });

    $("#cliente_new").on('click', function (e) {
        e.preventDefault();
        $('#dialog_new_cliente').attr('data-id', '');
        $("#dialog_new_cliente").html($("#loading").html());
        $('#dialog_new_cliente').modal('show');
        $("#dialog_new_cliente").load(ruta + 'cliente/form/' + '-1');
    });

    $("#dialog_new_cliente").on('hidden.bs.modal', function () {

        var dni = $('#dialog_new_cliente').attr('data-id');

        if (dni != '') {
            $.ajax({
                headers: {
                    Accept: 'application/json'
                },
                url: ruta + 'venta_new/update_cliente',
                success: function (data) {
                    var selected = 1;
                    var template = '';
                    for (var i = 0; i < data.clientes.length; i++) {
                        if (dni == data.clientes[i].id_cliente)
                            selected = data.clientes[i].id_cliente;

                        template += '<option value="' + data.clientes[i].id_cliente + '">' + data.clientes[i].razon_social + '</option>';
                    }
                    $("#cliente_id").html(template);

                    $("#cliente_id").val(selected).trigger("chosen:updated");
                    $("#cliente_id").change();
                }
            });
        }
    });

    $("#reiniciar_cotizar").on('click', function () {
        $('#confirm_cotizar_text').html('Si reinicias la cotizacion perderas todos los productos agregados. Estas seguro?');
        $('#confirm_cotizar_button').attr('onclick', 'reset_cotizar();');
        $('#dialog_cotizar_confirm').modal('show');
    });

    $("#cancelar_cotizar").on('click', function () {
        $('#confirm_cotizar_text').html('Si cancelas la cotizacion perderas todos los cambios realizados. Estas seguro?');
        $('#confirm_cotizar_button').attr('onclick', 'cancel_cotizar();');
        $('#dialog_cotizar_confirm').modal('show');
    });

    $("#stock_total").on('mousemove', function () {
        $("#stock_total").hide();
        $("#popover_stock").show();
    });

    $("#popover_stock").on('mouseleave', function () {
        $("#popover_stock").hide();
        $("#stock_total").show();
    });

});

//FUNCIONES DE MANEJO DE LAS VENTAS

//dependiendo de la configuracion lanza el cuadro de dialogo para realizar la venta
function end_cotizar() {

    $("#vc_total_pagar").val(formatPrice($("#total_importe").val()));
    $("#dialog_cotizar").modal('show');

}

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

        var precios = {};
        for (var j = 0; j < lst_producto[i].detalles.length; j++) {
            if (precios[lst_producto[i].detalles[j].unidad] == undefined)
                precios[lst_producto[i].detalles[j].unidad] = lst_producto[i].detalles[j].unidades;
        }

        for (var unidad in cantidades) {
            if (cantidades[unidad] != 0) {
                var producto = {};
                producto.id_producto = lst_producto[i].producto_id;
                producto.precio = precios[unidad] * lst_producto[i].precio_unitario;
                producto.unidad_medida = unidad;
                producto.cantidad = cantidades[unidad];
                producto.detalle_importe = producto.cantidad * producto.precio;
                productos.push(producto);
            }
        }

    }

    return JSON.stringify(productos);

}

function save_cotizar(imprimir) {

    $("#loading_save_venta").modal('show');
    $("#dialog_cotizar").modal('hide');
    $('.save_venta_contado').attr('disabled', 'disabled');

    var form = $('#form_venta').serialize();
    var detalles_productos = prepare_detalles_productos();

    $.ajax({
        url: ruta + 'cotizar/save_cotizar',
        type: 'POST',
        dataType: 'json',
        data: form + '&detalles_productos=' + detalles_productos,
        success: function (data) {

            if (data.success == '1') {
                show_msg('success', '<h4>Correcto. </h4><p>La cotizacion se ha guardado con exito.</p>');
                cancel_cotizar();
            }
            else {
                show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la cotizacion.</p>');
                $("#dialog_cotizar").modal('show');
            }
        },
        error: function (data) {
            alert('Error inesperado');
        },
        complete: function (data) {
            $('.save_venta_contado').removeAttr('disabled');
            $("#loading_save_venta").modal('hide');
        }
    });
}


//FUNCIONES INTERNAS

//funcion para agregar los productos de la venta
function add_producto() {

    var producto_id = $("#producto_id").val();
    var precio_id = $("#precio_id").val();


    var index = get_index_producto(producto_id);

    if (index == -1) {
        //AGREGO EL PRODUCTO E INICIALIZO SUS VALORES
        var producto = {};
        producto.index = lst_producto.length;
        producto.producto_id = producto_id;
        producto.producto_nombre = encodeURIComponent($("#producto_id option:selected").text());
        producto.precio_id = precio_id;
        producto.precio_unitario = parseFloat($("#precio_unitario").val());

        producto.um_min = $("#um_minimo").html().trim();
        producto.um_min_abr = $("#um_minimo").attr('data-abr');

        producto.detalles = [];
        producto.total_minimo = 0;


        $(".cantidad-input").each(function () {
            var input = $(this);
            var detalle = {};

            detalle.cantidad = isNaN(parseFloat(input.val())) ? 0 : parseFloat(input.val());
            detalle.unidad = input.attr('data-unidad_id');
            detalle.unidad_nombre = input.attr('data-unidad_nombre');
            detalle.unidad_abr = input.attr('data-unidad_abr');
            detalle.unidades = input.attr('data-unidades');
            detalle.orden = input.attr('data-orden');

            producto.total_minimo += parseFloat(detalle.cantidad * detalle.unidades);

            producto.detalles.push(detalle);

        });

        producto.subtotal = parseFloat(producto.total_minimo * producto.precio_unitario);

        lst_producto.push(producto);
    }
    else {
        //EDITO LA INFORMACION DETALLADA DEL PRODUCTO
        lst_producto[index].precio_id = precio_id;
        lst_producto[index].precio_unitario = parseFloat($("#precio_unitario").val());
        lst_producto[index].total_minimo = 0;

        $(".cantidad-input").each(function () {
            var input = $(this);
            for (var i = 0; i < lst_producto[index].detalles.length; i++) {
                if (lst_producto[index].detalles[i].unidad == input.attr('data-unidad_id')) {
                    lst_producto[index].detalles[i].cantidad = isNaN(parseFloat(input.val())) ? 0 : parseFloat(input.val());
                    lst_producto[index].total_minimo += parseFloat(lst_producto[index].detalles[i].cantidad * lst_producto[index].detalles[i].unidades);
                }
            }

        });

        lst_producto[index].subtotal = parseFloat(lst_producto[index].total_minimo * lst_producto[index].precio_unitario);
    }


    $("#producto_id").val("").trigger("chosen:updated");
    $("#producto_id").change();

    update_view(get_active_view());

    refresh_right_panel();

    setTimeout(function () {
        select_productos(51);
    }, 5);

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
                    '<th>Precio Unitario</th>' +
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
    template += '<td>' + decodeURIComponent(producto.producto_nombre) + '</td>';
    if (type == 'general') {
        template += '<td style="text-align: center;">' + producto.total_minimo + ' (' + producto.um_min + ')</td>';
        template += '<td>' + producto.precio_unitario + '</td>';
        template += '<td>' + parseFloat(producto.subtotal).toFixed(2) + '</td>';
    }
    if (type == 'detalle') {
        template += '<td style="text-align: center; width: 400px;">';

        template += '<div class="row" style="margin: 0;">';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff; padding: 0;">Cantidad</div>';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff;">UM</div>';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff;">Unidades</div>';
        template += '</div>';

        var det = detalles_sort(producto.detalles);
        for (var i = 0; i < det.length; i++) {
            template += '<div class="row" style="margin: 0;">';
            if (det[i].cantidad != 0) {
                template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2;">' + det[i].cantidad + '</div>';
                template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2;">' + det[i].unidad_abr + '</div>';
                template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2;">' + det[i].unidades + ' ' + producto.um_min_abr + '</div>';
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
function reset_cotizar() {
    lst_producto = [];
    update_view();
    refresh_right_panel();
    $('#dialog_cotizar_confirm').modal('hide');
}

//cancelo la venta, estado inicial y si la venta es en espera se elimina
function cancel_cotizar() {
    $('#dialog_cotizar_confirm').modal('hide');
    $("#loading_save_venta").modal('show');
    $.ajax({
        url: ruta + 'cotizar/historial',
        success: function (data) {
            $('#page-content').html(data);
            $("#loading_save_venta").modal('hide');
            $(".modal-backdrop").remove();
        }
    });
}

//funcion dependiendo de la tecla muestra el select
function select_productos(tecla) {
    var id = "";
    switch (tecla) {
        case 49: {
            id = '#cliente_id';
            break;
        }
        case 51: {
            id = '#producto_id';
            break;
        }
        /*case 52:
         {
         if ($('.block_producto_unidades').css('display') != 'none')
         id = '#precio_id';
         break;
         }*/
        case 53: {
            id = '#moneda_id';
            break;
        }

    }


    if (id != "") {
        $(id).trigger('chosen:open');
        setTimeout(function () {
            $(id + '_chosen .chosen-search input').trigger('focus');
        }, 500);
    }
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

            if ($("#precio_id").val() == '3')
                importe_total += parseFloat($("#precio_unitario").val() * input.val() * input.attr('data-unidades'));
            else
                importe_total += parseFloat($("#precio_" + input.attr('data-unidad_id')).val() * input.val());
        }
    });

    $("#total_minimo").val(data_total);
    $("#importe").val(parseFloat(importe_total).toFixed(2));
}

//function para refrescar el panel derecho
function refresh_right_panel() {

    if (lst_producto.length > 0) {
        $("#moneda_block_input").hide();
        $("#moneda_block_text").show();
        $("#tasa").attr('readonly', 'readonly');
    } else {
        $("#moneda_block_text").hide();
        $("#moneda_block_input").show();
        $("#tasa").removeAttr('readonly');
    }


    var total = 0;
    var tipo_moneda = $('#moneda_id option:selected').attr('data-tasa');
    var tasa = $('#tasa').val();
    var operacion = $('#moneda_id option:selected').attr('data-oper');

    for (var i = 0; i < lst_producto.length; i++) {
        total += lst_producto[i].subtotal;
    }

    if ($('#moneda_id option:selected').val() != $('#MONEDA_DEFECTO_ID').val()) {

        $('.precio-input').each(function () {
            $(this).val(parseFloat($(this).attr('data-value') / tasa).toFixed(3));
        });

    } else {
        $('.precio-input').each(function () {
            $(this).val(parseFloat($(this).attr('data-value')));
        });
    }

    var index = $('.precio-input').length - 1;
    $('.precio-input[data-index="' + index + '"]').first().trigger('click');

    var subtotal = 0, impuesto = 0, total_importe = 0;
    if ($("#incorporar_igv").val() == '1') {
        subtotal = total;
        impuesto = (total * 18) / 100;
        total_importe = subtotal + impuesto;
    }
    else {
        total_importe = total;
        impuesto = (total * 18) / 100;
        subtotal = total - impuesto;
    }


    $("#total_importe").val(parseFloat(total_importe).toFixed(2));
    $("#subtotal").val(parseFloat(subtotal).toFixed(2));
    $("#impuesto").val(parseFloat(impuesto).toFixed(2));
    $("#total_producto").val(lst_producto.length);


}

//actualizo la informacion del stock
function set_stock_info() {

    $("#stock_actual").html('Calculando Stock...');
    $("#stock_total").html('Calculando Stock...');


    var producto_id = $("#producto_id").val();

    $.ajax({
        url: ruta + 'cotizar/set_stock',
        type: 'POST',
        headers: {
            Accept: 'application/json'
        },
        data: {
            'producto_id': producto_id
        },
        success: function (data) {
            $("#stock_actual").html(data.stock_total_minimo + ' ' + data.stock_total.min_um_nombre);

            if (data.stock_total.max_um_id != data.stock_total.min_um_id)
                $("#stock_total").html(data.stock_total.cantidad + ' ' + data.stock_total.max_um_nombre + ' / ' + data.stock_total.fraccion + ' ' + data.stock_total.min_um_nombre);
            else
                $("#stock_total").html(data.stock_total.cantidad + ' ' + data.stock_total.max_um_nombre);


            $("#stock_total").attr('data-stock', data.stock_total_minimo);

            $.ajax({
                url: ruta + 'cotizar/set_stock_desglose',
                type: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                data: {
                    'producto_id': producto_id
                },
                success: function (data) {

                    var popover_stock = $("#popover_stock");
                    popover_stock.html('');
                    for (var i = 0; i < data.stock_desgloses.length; i++) {
                        var stock_text = '';
                        if (data.stock_desgloses[i].max_um_id != data.stock_desgloses[i].min_um_id)
                            stock_text = data.stock_desgloses[i].cantidad + ' ' + data.stock_desgloses[i].max_um_abrev + ' / ' + data.stock_desgloses[i].fraccion + ' ' + data.stock_desgloses[i].min_um_abrev;
                        else
                            stock_text = data.stock_desgloses[i].cantidad + ' ' + data.stock_desgloses[i].max_um_abrev;

                        var template = '<div class="row" style="margin-bottom: 3px;">';
                        template += '<div class="col-md-6" style="text-align: left;">' + data.locales[i] + '</div>';
                        template += '<div class="col-md-6" style="text-align: left;">' + stock_text + '</div>';
                        template += '</div>';


                        popover_stock.append(template);
                    }
                }
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
function prepare_unidades_value(producto_id, unidad) {

    var cantidad = $("#cantidad_" + unidad.id_unidad);
    var cant = get_value_producto(producto_id, unidad.id_unidad, -1);
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

//creo el template para mostrar los precios
function create_precio_template(index, unidad) {
    var simbolo = $('#moneda_id option:selected').attr('data-simbolo');
    var template = '<div class="col-md-3">';
    template += '<div class="input-group">';
    template += '<div class="input-group-addon tipo_moneda">' + simbolo + '</div>';
    template += '<input type="button" class="form-control btn text-right precio-input" ';
    template += 'style="cursor: pointer" ';
    template += 'id="precio_' + unidad.id_unidad + '" ';
    template += 'value="' + unidad.precio + '" ';
    template += 'data-value="' + unidad.precio + '" ';
    template += 'data-unidad_id="' + unidad.id_unidad + '" ';
    template += 'data-unidad_nombre="' + unidad.nombre_unidad + '" ';
    template += 'data-index="' + index + '" ';
    template += 'onkeydown="return soloDecimal(this, event);">';
    template += '<a href="#" class="input-group-addon add_precio" data-precio="precio_' + unidad.id_unidad + '"><i class="fa fa-check"></i></a>';
    template += '</div>';

    template += '<h6>' + unidad.nombre_unidad + '</h6>';

    template += '</div>';

    return template;
}

//preparo los valos iniciales de precio
function prepare_precio_value(producto_id, unidad_minima) {
    var precio = get_precio_producto(producto_id);

    $(".precio-input").removeClass('precio-selected');

    if (precio == -1) {
        $("#precio_unitario").val($('#precio_' + unidad_minima.id_unidad).val());
        $("#precio_unitario").attr('data-index', parseInt($('.precio-input').length - 1));
        $("#precio_unitario_um").html(unidad_minima.nombre_unidad);
        $('#precio_' + unidad_minima.id_unidad).addClass('precio-selected');
    }
    else {
        $("#precio_unitario").val(precio);
        $('.precio-input').each(function () {
            var input = $(this);

            if (input.val() == precio) {
                $("#precio_unitario").attr('data-index', input.attr('data-index'));
                $("#precio_unitario_um").html(input.attr('data-unidad_nombre'));
                input.addClass('precio-selected');
            }
        });
    }
}

//preparo los eventos de los precios
function prepare_precio_events() {

    var precio_input = $('.precio-input');


    precio_input.on('click', function (e) {
        e.preventDefault();
        $(".precio-input").removeClass('precio-selected');
        $('#precio_unitario').val($(this).val());
        $("#precio_unitario").attr('data-index', parseInt($(this).attr('data-index')));
        $("#precio_unitario_um").html($(this).attr('data-unidad_nombre'));
        $(this).addClass('precio-selected');
        refresh_totals();
    });

    $('.add_precio').on('click', function (e) {
        e.preventDefault();
        $('#precio_unitario').val($("#" + $(this).attr('data-precio')).val());
        $("#" + $(this).attr('data-precio')).click();
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
function get_value_producto(producto_id, um_id, defecto) {
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id) {
            for (var j = 0; j < lst_producto[i].detalles.length; j++) {
                if (lst_producto[i].detalles[j].unidad == um_id)
                    return lst_producto[i].detalles[j].cantidad;
            }
        }
    }

    if (defecto != undefined)
        return defecto;
    else return 0;
}

function get_precio_producto(producto_id) {
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id) {
            return lst_producto[i].precio_unitario;
        }
    }

    return -1;
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