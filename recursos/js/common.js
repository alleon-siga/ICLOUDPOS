/**
 * Created by Jhainey on 10/10/2015.
 */

function get_numero_mes(mes) {
    switch (mes) {
        case 0:
        {
            return '01';
        }
        case 1:
        {
            return '02';
        }
        case 2:
        {
            return '03';
        }
        case 3:
        {
            return '04';
        }
        case 4:
        {
            return '05';
        }
        case 5:
        {
            return '06';
        }
        case 6:
        {
            return '07';
        }
        case 7:
        {
            return '08';
        }
        case 8:
        {
            return '09';
        }
        case 9:
        {
            return '10';
        }
        case 10:
        {
            return '11';
        }
        case 11:
        {
            return '12';
        }
    }
}

function get_numero_dia(dia) {
    switch (dia) {
        case 1:
        {
            return '01';
        }
        case 2:
        {
            return '02';
        }
        case 3:
        {
            return '03';
        }
        case 4:
        {
            return '04';
        }
        case 5:
        {
            return '05';
        }
        case 6:
        {
            return '06';
        }
        case 7:
        {
            return '07';
        }
        case 8:
        {
            return '08';
        }
        case 9:
        {
            return '09';
        }
        case 10:
        {
            return '10';
        }
        default:{
            return dia;
        }
    }
}

function roundPrice(price, fixed, prec) {

    var p = "e+3";
    var b = "e-3";
    if (prec != undefined) {
        p = "e+" + prec;
        b = "e-" + prec;
    }

    var r = +(Math.round(price + p) + b);
    if (fixed >= 0)
        return r.toFixed(fixed);
    else
        return r.toFixed(2);
}

function formatPrice(price, min) {
    if (min == undefined)
        min = 10;
    var r = +(Math.round(price + "e+4") + "e-4");
    var round = r.toFixed(2).split('.');
    var entero = round[0];
    var fraccion = round[1]

    for (var i = 0; i <= 100; i = i + min) {
        if (i < fraccion && i + min > fraccion) {
            if ((i + min - fraccion) <= (fraccion - i))
                fraccion = i + min;
            else if ((i + min - fraccion) > (fraccion - i))
                fraccion = i;

            if (fraccion == 100) {
                fraccion = 0;
                entero = parseInt(entero) + 1;
            }
        }
    }
    return parseFloat(entero + '.' + fraccion).toFixed(2);
}

function mensaje(growlType, msj){
    $.bootstrapGrowl(msj, {
        type: growlType,
        delay: 2500,
        allow_dismiss: true
    });
    return false;
}

var region = {

    actualizarestados: function () {




        $.ajax({
            url: baseurl + 'estados/get_by_pais',
            type: 'POST',
            data: {'pais_id': $("#id_pais").val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].estados_id + '">' + data[i].estados_nombre + '</option>';

                    }

                    $("#estado_id").html(options);
                }
            }
        })
    },


    actualizardistritos: function () {

        $.ajax({
            url: baseurl + 'ciudad/get_by_estado',
            type: 'POST',
            data: {'estado_id': $("#estado_id").val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].ciudad_id + '">' + data[i].ciudad_nombre + '</option>';
                    }


                    $("#ciudad_id").html(options);
                    $("#ciudad_id").trigger("chosen:updated");


                }
            }
        })
    },
    actualizardistritos_dinamico: function (cont) {

        $("#ciudad_id"+cont).html('<option value="">Seleccione </option>');
        $("#distrito_id"+cont).html('<option value="">Seleccione </option>');
        $.ajax({
            url: baseurl + 'ciudad/get_by_estado',
            type: 'POST',
            data: {'estado_id': $("#estado_id"+cont).val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].ciudad_id + '">' + data[i].ciudad_nombre + '</option>';
                    }

                    $("#ciudad_id"+cont).html(options);
                    $("#ciudad_id").trigger("chosen:updated");
                }
            }
        })
    },

    actualizarbarrio: function () {

        $.ajax({
            url: baseurl + 'distrito/get_by_ciudad',
            type: 'POST',
            data: {'ciudad_id': $("#ciudad_id").val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].id + '">' + data[i].nombre + '</option>';
                    }


                    $("#distrito_id").html(options);
                    $("#distrito_id").trigger("chosen:updated");
                }
            }
        })
    },
    actualizarbarrio_dinamico: function (cont) {
        $("#distrito_id"+cont).html('<option value="">Seleccione </option>');
        $.ajax({
            url: baseurl + 'distrito/get_by_ciudad',
            type: 'POST',
            data: {'ciudad_id': $("#ciudad_id"+cont).val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].id + '">' + data[i].nombre + '</option>';
                    }

                    $("#distrito_id"+cont).html(options);
                    $("#distrito_id").trigger("chosen:updated");
                }
            }
        })
    }


};
