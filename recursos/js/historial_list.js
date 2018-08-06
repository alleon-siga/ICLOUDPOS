$(function () {
    TablesDatatables.init(1);
    $("#dialog_cotizar_detalle").on("hide.bs.modal", function(){
        get_cotizaciones();
    })
});

function cotizar(id) {
    $("#dialog_cotizar_detalle").html($("#loading").html());
    $("#dialog_cotizar_detalle").modal('show');

    $.ajax({
        url: ruta + 'cotizar/get_cotizar_validar/',
        type: 'POST',
        data: {'id': id},

        success: function (data) {
            $("#dialog_cotizar_detalle").html(data);
        },
        error: function () {
            alert('Error inesperado')
        }
    });
}


function ver(id) {
    $("#dialog_cotizar_detalle").html($("#loading").html());
    $("#dialog_cotizar_detalle").modal('show');

    $.ajax({
        url: ruta + 'cotizar/get_cotizar_detalle/',
        type: 'POST',
        data: {'id': id},

        success: function (data) {
            $("#dialog_cotizar_detalle").html(data);
        },
        error: function () {
            alert('Error inesperado')
        }
    });
}

function exportar_pdf(id, tp) {
    var win = window.open(ruta + 'cotizar/exportar_pdf/' + id + '/' + tp, '_blank');
    win.focus();
}


function anular(id) {
    if (!window.confirm("Estas seguro de eliminar esta cotizacion"))
        return false;

    $("#confirm_venta_text").html($("#loading").html());

    $.ajax({
        url: ruta + 'cotizar/eliminar',
        type: 'POST',
        data: {'id': id},

        success: function (data) {
            $.bootstrapGrowl('<h4>Correcto.</h4> <p>Cotizacion anulada con exito.</p>', {
                type: 'success',
                delay: 5000,
                allow_dismiss: true
            });
            get_cotizaciones();
        },
        error: function () {
            alert('Error inesperado');
        }
    });
}

function enviar_correo(idCotizacion, tipo_cliente){
    $("#correoModal").html($("#loading").html());
    $("#correoModal").load(ruta + 'cotizar/modalEnviarCotizacion/' + idCotizacion + '/' + tipo_cliente);
    $('#correoModal').modal('show');
}

function whatsApp(){
    mensaje('warning', '<h4>Opci&oacute;n no disponible para esta versi&oacute;n</h4>')
}

function messenger(){
    mensaje('warning', '<h4>Opci&oacute;n no disponible para esta versi&oacute;n</h4>')
}