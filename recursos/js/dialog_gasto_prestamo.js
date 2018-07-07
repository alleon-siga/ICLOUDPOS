$(document).ready(function () {
    $('#btn_compra_credito').on('click', function () {
        $("#btn_compra_credito").addClass('disabled');
        var cuotas = [];
        cuotas = prepare_cuotas();
        $('#cuotas').attr('value', cuotas);
        $('#total').val($('#c_precio_credito').val());
        $('#agregar').modal('hide');
        App.formSubmitAjax($("#formagregar").attr('action'), get_gastos, 'dialog_gasto_prestamo', 'formagregar');
        $.bootstrapGrowl('<h4>Solicitud procesada con &eacute;xito</h4>', {
            type: 'success',
            delay: 2500,
            allow_dismiss: true
        });
    });

    $("#c_tasa_interes, #c_numero_cuotas, #c_saldo_inicial_por, #c_dia_pago, #c_precio_contado").on('keyup', function () {
        refresh_credito_window(1);
    });

    $('#c_numero_cuotas, #c_rango_min').bind('keyup change click mouseleave', function () {
        var min = isNaN(parseInt($("#c_rango_min").val())) ? 1 : parseInt($("#c_rango_min").val());
        $("#c_rango_max").val(parseInt(min + 4));
        refresh_credito_window(1);
    });

    $("#c_saldo_inicial_por").on('keyup', function () {
        refresh_credito_window(2);
    });

    $("#c_saldo_inicial_por").on('keydown', function (e) {
        var tecla = e.key;
        if (isNaN(parseFloat($(this).val() + tecla)))
            return false;

        if (parseFloat($(this).val() + tecla) > 100 || parseFloat($(this).val() + tecla) < 0)
            return false;

        return soloDecimal($(this), e);
    });

    $("#c_saldo_inicial").on('keydown', function (e) {
        var tecla = e.key;
        if (isNaN(parseFloat($(this).val() + tecla)))
            return false;

        if (parseFloat($(this).val() + tecla) > parseFloat($('#c_precio_contado').val()) || parseFloat($(this).val() + tecla) < 0)
            return false;

        return soloDecimal($(this), e);
    });

    $("#c_numero_cuotas, #c_rango_min").on('keydown', function (e) {
        var tecla = e.key;
        if (isNaN(parseInt($(this).val() + tecla)))
            return false;
        if (parseInt($(this).val() + tecla) > parseInt($(this).attr('max')) || parseInt($(this).val() + tecla) <= 0)
            return false;
        return soloNumeros(e);
    });

    $("#c_dia_pago").on('keydown', function (e) {
        var tecla = e.key;
        if (isNaN(parseInt($(this).val() + tecla)))
            return false;
        if (parseInt($(this).val() + tecla) <= 0)
            return false;
        return soloNumeros(e);
    });

    $("#c_pago_periodo").on('change', function () {
        var pago_periodo = $(this).val();

        $("#c_dia_pago_block").hide();
        $("#table_rango").hide();
        switch (pago_periodo) {
            case '4': {
                var dia = $("#c_fecha_giro").val().split('/');
                $("#c_dia_pago_letra").html("D&iacute;as de Pago:");
                $("#c_dia_pago").val(dia[0]);
                $("#c_dia_pago_block").show();
                break;
            }
            case '5': {
                $("#c_dia_pago_letra").html("Periodos de D&iacute;as:");
                $("#c_dia_pago").val("1");
                $("#c_dia_pago_block").show();
                break;
            }
            case '6': {
                $("#table_rango").show();
                break;
            }
        }
        refresh_credito_window(1);
    });

    $("#c_garante").on('change', function () {
        $("#c_garante_nombre").html($("#c_garante option:selected").attr('data-nombre'));
    });
});

function credito_init(precio_contado) {
    if(isNaN(precio_contado)){
        precio_contado = 0;
    }
    $("#c_precio_contado").val(precio_contado);
    $("#c_tasa_interes").val($("#tasa_interes").val());
    $("#c_saldo_inicial_por").val($("#saldo_porciento").val());
    $("#c_numero_cuotas").attr('max', $("#max_cuotas").val());
    $("#c_numero_cuotas").val($("#numero_cuotas").val());
    $("#c_rango_min").val($("#proyeccion_rango").val());
    if($('#persona_gasto').val()=='1'){
        $('#c_proveedor').val($('#proveedor option:selected').text());    
    }else{
        $('#c_proveedor').val($('#usuario option:selected').text());    
    }
    $('#c_fecha_giro').val($('#fecha').val().replace(/-/g, '/'));        
    //ojo
    setTimeout(function () {
        $("#c_pago_periodo").val('4').trigger('chosen:updated');
        $("#c_pago_periodo").change();
    }, 500);
    //ojo inicializar garante tambien*/
}

function refresh_credito_window(trigger) {
    var capital = isNaN(parseFloat($("#c_precio_contado").val())) ? 0 : parseFloat($("#c_precio_contado").val());
    var interes = isNaN(parseFloat($("#c_tasa_interes").val())) ? 0 : parseFloat($("#c_tasa_interes").val());
    var prestamo = capital + interes;
    $("#c_precio_credito").val(formatPrice(prestamo));
    generar_proyeccion(prestamo);

    if ($('#c_pago_periodo').val() == 6){
        generar_rangos(parseInt($("#c_numero_cuotas").val()));
    }
    generar_cuotas(parseInt($("#c_numero_cuotas").val()), prestamo);
    $('#body_proyeccion_cuotas tr').removeClass('table-selected');
    $('#body_proyeccion_cuotas tr[data-cuota="' + $("#c_numero_cuotas").val() + '"]').addClass('table-selected');
    $("#c_total_deuda").html(formatPrice(prestamo));
}

function generar_proyeccion(saldo) {
    var min = isNaN(parseInt($("#c_rango_min").val())) ? 1 : parseInt($("#c_rango_min").val());
    var max = $("#c_rango_max").val();
    var body = $("#body_proyeccion_cuotas");

    body.html("");
    for (var i = min; i <= max; i++) {
        var template = '<tr class="proyeccion_cuota" data-cuota="' + i + '">';
        template += '<td style="text-align: center;">' + i + '</td>';
        template += '<td style="text-align: right;">' + $('.tipo_moneda').first().html() + ' ' + formatPrice(saldo / i) + '</td>';
        template += '</tr>';

        body.append(template);
    }

    $('.proyeccion_cuota').on('click', function () {
        $("#c_numero_cuotas").val($(this).attr('data-cuota'));
        refresh_credito_window(1);
    });
}

function generar_rangos(numero_cuotas) {
    var body = $("#body_cuotas_rango");
    if ($("#body_cuotas_rango tr").length > numero_cuotas) {
        var counter = 0;
        $("#body_cuotas_rango tr").each(function () {
            if (++counter > numero_cuotas)
                $(this).remove();
        });
    }
    for (var i = 0; i < numero_cuotas; i++) {
        if ($('#c_rango_' + i).html() == undefined) {
            var template = '<tr style="background-color: #39B147 !important">';
            template += '<td style="padding: 0 !important; height: 28px; text-align: center;"><input  id="c_rango_' + i + '" class="c_rango_input" type="text" value="' + (30 * (i + 1)) + '" style="width: 40px;"></td>';
            template += '</tr>';

            body.append(template);
        }
    }

    $('.c_rango_input').off('focus keyup');
    $('.c_rango_input').on('focus', function () {
        $(this).select();
    });
    $('.c_rango_input').on('keyup', function () {
        refresh_credito_window(1);
    });
}

function generar_cuotas(numero_cuotas, saldo) {
    $('#last_fecha_giro').val($("#c_fecha_giro").val());
    var body = $("#body_cuotas");
    var monto = formatPrice(saldo / numero_cuotas);

    body.html("");
    for (var i = 0; i < numero_cuotas; i++) {
        var template = '<tr>';
        template += '<td id="c_cuota_letra_' + i + '">' + (i + 1) + ' / ' + numero_cuotas + '</td>';
        template += '<td style="height: 28px;"><span  id="c_cuota_fecha_' + i + '">' + get_fecha_vencimiento(i, $("#c_pago_periodo").val()) + '</span>'
        template += '</td>';
        template += '<td style="text-align: right;">' + $('.tipo_moneda').first().html() + ' <span id="c_cuota_monto_' + i + '">' + monto + '</span></td>';
        template += '</tr>';
        body.append(template);
    }
}

function get_fecha_vencimiento(index, type) {
    var fecha = $('#last_fecha_giro').val().split('/');
    var next = new Date(fecha[2], fecha[1] - 1, fecha[0]);
    switch (type) {
        case '1': {
            next.setDate(next.getDate() + 1);
            break;
        }
        case '2': {
            next.setDate(next.getDate() + 2);
            break;
        }
        case '3': {
            next.setDate(next.getDate() + 7);
            break;
        }
        case '4': {
            next.setMonth(next.getMonth() + 1);
            var dia_mes = isNaN(parseInt($("#c_dia_pago").val())) ? 1 : parseInt($("#c_dia_pago").val());
            next.setDate(dia_mes);
            break;
        }
        case '5': {
            var dia_mes = isNaN(parseInt($("#c_dia_pago").val())) ? 1 : parseInt($("#c_dia_pago").val());
            next.setDate(next.getDate() + dia_mes);
            break;
        }
        case '6': {
            var fecha_rango = $('#c_fecha_giro').val().split('/');
            var next_rango = new Date(fecha_rango[2], fecha_rango[1] - 1, fecha_rango[0]);
            var dia_mes = isNaN(parseInt($("#c_dia_pago").val())) ? 1 : parseInt($("#c_rango_" + index).val());
            next_rango.setDate(next_rango.getDate() + dia_mes);
            if (next_rango.getDay() == 0) {
                next_rango.setDate(next_rango.getDate() + 1);
            }
            var last_fecha_r = get_numero_dia(next_rango.getDate()) + '/' + get_numero_mes(next_rango.getMonth()) + '/' + next_rango.getFullYear();
            return last_fecha_r;
        }
    }

    if (next.getDay() == 0) {
        next.setDate(next.getDate() + 1);
    }

    var last_fecha = get_numero_dia(next.getDate()) + '/' + get_numero_mes(next.getMonth()) + '/' + next.getFullYear();
    $('#last_fecha_giro').val(last_fecha);

    return last_fecha;
}

function prepare_cuotas() {
    var cuotas = [];
    var numero_coutas = parseInt($("#c_numero_cuotas").val());

    for (var i = 0; i < numero_coutas; i++) {
        var cuota = {};
        cuota.letra = $("#body_cuotas #c_cuota_letra_" + i).html().trim();
        cuota.fecha = $("#body_cuotas #c_cuota_fecha_" + i).html().trim();
        cuota.monto = $("#body_cuotas #c_cuota_monto_" + i).html().trim();
        cuotas.push(cuota);
    }
    return JSON.stringify(cuotas);
}