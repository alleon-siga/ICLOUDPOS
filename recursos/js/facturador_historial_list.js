$(function () {
    TablesDatatables.init(1);

    $('#exportar_excel').on('click', function (e) {
        e.preventDefault();
        exportar_excel();
    });

    $("#exportar_pdf").on('click', function (e) {
        e.preventDefault();
        exportar_pdf();
    });
});

function exportar_pdf() {

    var data = {
        'local_id': $("#venta_local").val(),
        'esatdo': $("#venta_estado").val(),
        'fecha': $("#date_range").val(),
        'moneda_id': $("#moneda_id").val(),
        'condicion_pago_id': $("#condicion_pago_id").val()
    };

    var win = window.open($('#ruta').val() + 'facturador/venta/historial_pdf?data=' + JSON.stringify(data), '_blank');
    win.focus();
}

function exportar_excel() {
    var data = {
        'local_id': $("#venta_local").val(),
        'esatdo': $("#venta_estado").val(),
        'fecha': $("#date_range").val(),
        'moneda_id': $("#moneda_id").val(),
        'condicion_pago_id': $("#condicion_pago_id").val()
    };

    var win = window.open($('#ruta').val() + 'facturador/venta/historial_excel?data=' + JSON.stringify(data), '_blank');
    win.focus();
}
function mostrar(id) {
    $("#remove_ventaconvertida_shadow").html($("#loading").html());
    $('#remove_ventaconvertida_shadow').modal('show');
    $.ajax({
        url: $('#ruta').val() + 'facturador/venta/get_venta_convertido/',
        type: 'POST',
        data: {'id': id},
        success: function (data) {
            $('#remove_ventaconvertida_shadow').html(data);
        },
        error: function (resp) {
            alert("resp")
        }
    });
}
function sendsunat(venta_id) {
    if ($("#ValFacturacion").val() == 1) {
        $("#dialog_sunat_shadow_masivo").html($("#loading").html());
        $('#dialog_sunat_shadow_masivo').modal('show');
        $.ajax({
            url: $('#ruta').val() + 'facturador/venta/get_ventas_shadow/',
            type: 'POST',
            data: {'id': venta_id},
            success: function (data) {
                $('#dialog_sunat_shadow_masivo').html(data);
            },
            error: function (resp) {
                alert("resp")
            }
        });
    } else {
        $.bootstrapGrowl('<p>No Tiene Configurado Facturacion Electronica</p>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });
    }
}
function ver(venta_id) {

    $("#dialog_venta_detalle").html($("#loading").html());
    $("#dialog_venta_detalle").modal('show');

    $.ajax({
        url: $('#ruta').val() + 'facturador/venta/get_venta_detalle/',
        type: 'POST',
        data: {'venta_id': venta_id},

        success: function (data) {
            $("#dialog_venta_detalle").html(data);
        },
        error: function () {
            alert('asd')
        }
    });
}
function info(id_shadow) {

    $("#dialog_venta_detalle_sahdow").html($("#loading").html());
    $("#dialog_venta_detalle_sahdow").modal('show');

    $.ajax({
        url: $('#ruta').val() + 'facturador/venta/get_venta_detalle_shadow/',
        type: 'POST',
        data: {'id_shadow': id_shadow},

        success: function (data) {
            $("#dialog_venta_detalle_sahdow").html(data);
        },
        error: function () {
            alert('asd')
        }
    });
}
function calculartotalcontable() {
    var totalcontable = 0;
    $(".total_co").each(function () {
        totalcontable += parseFloat($(this).html()) || 0;
        $("#total_c").text(parseFloat(totalcontable).toFixed(2));

    });
}
function caltulartotal() {
    $("#total_r_c").text(document.getElementById("total_r").innerHTML - document.getElementById("total_c").innerHTML).toFixed(2);
}
function detalle(venta_id) {

    $("#dialog_venta_detalle_convertidos").html($("#loading").html());
    $("#dialog_venta_detalle_convertidos").modal('show');

    $.ajax({
        url: $('#ruta').val() + 'facturador/venta/get_venta_detalle_convertido/',
        type: 'POST',
        data: {'venta_id': venta_id},

        success: function (data) {
            $("#dialog_venta_detalle_convertidos").html(data);
        },
        error: function () {
            alert('asd')
        }
    });
}
function shadow(venta_id)
{
    $('#barloadermodal').modal('show');
    $.ajax({
        url: $('#ruta').val() + 'facturador/venta/shadow/' + venta_id,
        success: function (data) {
            $('#page-content').html(data);
            $('#barloadermodal').modal('hide');
            $(".modal-backdrop").remove();
        }
    });
}