$(function () {
    //CONFIGURACIONES INICIALES
    App.sidebar('close-sidebar');

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
    get_ventas();

    $("#btn_buscar").on("click", function () {
        get_ventas();
    });

    $('.chosen-container').css('width', '100%');
});

function get_ventas() {
    $("#historial_list").html($("#loading").html());

    var local_id = $("#venta_local").val();
    var estado = $("#venta_estado").val();
    var fecha = $('#date_range').val();
    var moneda_id = $("#moneda_id").val();
    var condicion_pago_id = $("#condicion_pago_id").val();

    $.ajax({
        url: $('#ruta').val() + 'facturador/venta/get_ventas/',
        data: {
            'local_id': local_id,
            'fecha': fecha,
            'estado': estado,
            'moneda_id': moneda_id,
            'condicion_pago_id': condicion_pago_id
        },
        type: 'POST',
        success: function (data) {
            $("#historial_list").html(data);
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