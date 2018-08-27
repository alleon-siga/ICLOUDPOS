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