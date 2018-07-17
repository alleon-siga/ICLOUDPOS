$(document).ready(function () {
    $('.tipo_moneda').html($('#idMoneda').html());
    $('#btn_compra_credito').on('click', function () {
        //Validar que coincida con el total de importe
        var capital = 0;
        $('.monto_a_pagar').each(function () {
            capital += parseFloat($(this).val());
        });

        validar = false;
        if( parseFloat(roundPrice(capital, 2)) != parseFloat(roundPrice($('#c_precio_credito').val(), 2)) ){
            mensaje('warning', '<h4>El cronograma no coindice con el total del cr&eacute;dito</h4>');
            validar = true;
        }else if(parseFloat($('#c_precio_credito').val())<=0){
            mensaje('warning', '<h4>El total de cuota es inv&aacute;lido</h4>');
            validar = true;
        }

        if(validar==false){
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
            //mensaje('success', '<h4>Solicitud procesada con &eacute;xito</h4>');
        }
    });

    $("#c_tasa_interes, #c_numero_cuotas, #c_dia_pago, #c_precio_contado, #c_comision").on('keyup', function () {
        refresh_credito_window(1);
    });

    //$('#c_numero_cuotas, #c_rango_min').bind('keyup change click mouseleave', function () {
    $('#c_numero_cuotas, #c_rango_min').bind('keyup', function () {
        var min = isNaN(parseInt($("#c_rango_min").val())) ? 1 : parseInt($("#c_rango_min").val());
        $("#c_rango_max").val(parseInt(min + 4));
        refresh_credito_window(1);
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
        //refresh_credito_window(1);
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
    var comision = isNaN(parseFloat($("#c_comision").val())) ? 0 : parseFloat($("#c_comision").val());
    var prestamo = capital + interes + comision;
    var b = prestamo.toString().match(/^-?\d+(?:\.\d{0,2})?/)[0];
    $("#c_precio_credito").val(b);
    generar_proyeccion(prestamo);

    if ($('#c_pago_periodo').val() == 6){
        generar_rangos(parseInt($("#c_numero_cuotas").val()));
    }
    generar_cuotas(parseInt($("#c_numero_cuotas").val()), capital, interes, comision, prestamo);
    $('#body_proyeccion_cuotas tr').removeClass('table-selected');
    $('#body_proyeccion_cuotas tr[data-cuota="' + $("#c_numero_cuotas").val() + '"]').addClass('table-selected');
    $("#c_total_deuda").html(b);

    var capital = 0;
    $('.monto_a_pagar').each(function () {
        capital += parseFloat($(this).val());
    });
    $("#c_total_cronograma").html(roundPrice(capital, 2));
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

function generar_cuotas(numero_cuotas, vcapital, vinteres, vcomision, vprestamo) {
    $('#last_fecha_giro').val($("#c_fecha_giro").val());
    var body = $("#body_cuotas");
    var capital = roundPrice(vcapital / numero_cuotas, 2);
    var interes = roundPrice(vinteres / numero_cuotas, 2);
    var comision = roundPrice(vcomision / numero_cuotas, 2);
    var prestamo = roundPrice(vprestamo / numero_cuotas, 2);
    body.html('');
    var saldo = vcapital;
    for (var i = 0; i < numero_cuotas; i++) {
        saldo = roundPrice(saldo - capital);
        var template = '<tr class="sumarFila">';
        template += '<td id="c_cuota_letra_' + i + '">' + (i + 1) + ' / ' + numero_cuotas + '</td>';
        template += '<td>';
        template += '<input style="width:90px;" style="cursor:pointer;" class="form-control fecha_venc" type="text" id="c_cuota_fecha_'+ i +'" name="c_cuota_fecha_'+ i +'" value="' + get_fecha_vencimiento(i, $("#c_pago_periodo").val()) + '" autocomplete="off" readonly>'; //fecha vencimiento
        template += '</td>';
        template += '<td>';
        template += '<input style="width:80px;" class="form-control saldo" onChange="actualizarTotal()" type="text" id="c_saldo_'+ i +'" name="c_saldo_'+ i +'" value="' + saldo + '" onkeydown="return soloDecimal(this, event);" autocomplete="off">'; //saldo
        template += '</td>';
        template += '<td>';
        template += '<input style="width:80px;" class="form-control capital" onChange="actualizarTotal()" type="text" id="c_capital_'+ i +'" name="c_capital_'+ i +'" value="' + capital + '" onkeydown="return soloDecimal(this, event);" autocomplete="off">'; //capital
        template += '</td>';
        template += '<td>';
        template += '<input style="width:80px;" class="form-control interes" onChange="actualizarTotal()" type="text" id="c_interes_'+ i +'" name="c_interes_'+ i +'" value="' + interes + '" onkeydown="return soloDecimal(this, event);" autocomplete="off">'; //interes
        template += '</td>';
        template += '<td>';
        template += '<input style="width:80px;" class="form-control comision" onChange="actualizarTotal()" type="text" id="c_comision_'+ i +'" name="c_comision_'+ i +'" value="' + comision + '" onkeydown="return soloDecimal(this, event);" autocomplete="off">'; //comision
        template += '</td>';
        template += '<td>';
        template += '<input style="width:80px;" class="form-control monto_a_pagar prestamo" type="text" id="c_cuota_monto_'+ i +'" name="c_cuota_monto_'+ i +'" value="' + prestamo + '" onkeydown="return soloDecimal(this, event);" readonly>'; //total cuota
        template += '</td>';
        template += '</tr>';
        body.append(template);
    }
    $(".fecha_venc").datepicker({
        format: 'dd/mm/yyyy'
    });

    $('.sumarFila').on('keyup', function(e){
        //Sumatoria de las filas
        var capital = parseFloat($(this).find('.capital').val());
        var interes = parseFloat($(this).find('.interes').val());
        var comision = parseFloat($(this).find('.comision').val());
        $(this).find('.prestamo').val(roundPrice(capital + interes + comision, 2));

        //Sumatoria por columnas
        capital = interes = comision = 0;
        $('.sumarFila').each(function(){
            capital += parseFloat($(this).find('.capital').val());
            interes += parseFloat($(this).find('.interes').val());
            comision += parseFloat($(this).find('.comision').val());
        });
        $('#c_precio_contado').val(roundPrice(capital, 2));
        $('#c_tasa_interes').val(roundPrice(interes, 2));
        $('#c_comision').val(roundPrice(comision, 2));
        $('#c_precio_credito').val(roundPrice(capital + interes + comision, 2));
        $("#c_total_deuda").html($('#c_precio_credito').val());
    });
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
        cuota.letra = $("#body_cuotas #c_cuota_letra_" + i).html().trim(); //letra
        cuota.fecha = $("#c_cuota_fecha_" + i).val(); //fecha
        cuota.saldo = $("#c_saldo_" + i).val(); //saldo
        cuota.capital = $("#c_capital_" + i).val(); //capital
        cuota.interes = $("#c_interes_" + i).val(); //interes
        cuota.comision = $("#c_comision_" + i).val(); //comision
        cuota.monto = $("#c_cuota_monto_" + i).val(); //total de cuota
        cuotas.push(cuota);
    }
    return JSON.stringify(cuotas);
}

function actualizarTotal(){
    var capital = 0;
    $('.monto_a_pagar').each(function () {
        capital += parseFloat($(this).val());
    });
    $("#c_total_cronograma").html(roundPrice(capital, 2));
}
