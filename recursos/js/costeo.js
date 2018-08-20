//CONFIGURACIONES INICIALES
App.sidebar('close-sidebar');
// Filtro en select
$("#producto_id").multipleSelect({
    filter: true,
    width: '100%'
});

$(document).ready(function () {
    $("#charm").tcharm({
        'position': 'right',
        'display': false,
        'top': '50px'
    });

    $('.ctrl').chosen();

    //getReporte();

    $("#btn_buscar, .btn_buscar").on("click", function () {
        getReporte();
    });

    $('.chosen-container').css('width', '100%');

    $("#btn_filter_reset").on('click', function () {
        $('#marca_id').val('0').trigger('chosen:updated');
        $('#grupo_id').val('0').trigger('chosen:updated');
        $('#familia_id').val('0').trigger('chosen:updated');
        $('#linea_id').val('0').trigger('chosen:updated');
        $('#producto_id').multipleSelect('uncheckAll');
        $("#charm").tcharm('hide');
        getReporte();
        filtro();
    });

    $('#marca_id, #grupo_id, #familia_id, #linea_id').on('change', function(){
        filtro();
    });

    $('#btnAplicar').on('click', function(){
        $('.tipo_cambio').val($('#txtAllTipoCambio').val());
    });

    $('#btnSave').on('click', function(){
        var detalle = prepareCosto();
        $('#loading').show();
        $.ajax({
            url: ruta + 'facturador/producto/editarCosteo',
            type: 'POST',
            dataType: 'json',
            data: 'detalle=' + detalle,
            success: function (data) {
                mensaje('success', data.msg);
                $('#loading').hide();
            },
            error: function () {
                mensaje('danger', "Error inesperado");
                $('#loading').hide();
            }
        });
    });
});

function getReporte() {
    $("#historial_list").html($("#loading").html());
    $("#charm").tcharm('hide');

    var data = {
        'producto_id': $("#producto_id").val(),
        'grupo_id': $("#grupo_id").val(),
        'marca_id': $("#marca_id").val(),
        'linea_id': $("#linea_id").val(),
        'familia_id': $("#familia_id").val()
    };

    $.ajax({
        url: ruta + 'facturador/producto/costeo/filter',
        data: data,
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

function filtro(){
    var data = {
        'grupo_id': $("#grupo_id").val(),
        'marca_id': $("#marca_id").val(),
        'linea_id': $("#linea_id").val(),
        'familia_id': $("#familia_id").val()
    };

    $.ajax({
        url: ruta + 'reporte/selectProducto',
        data: data,
        type: 'POST',
        success: function (data) {
            $("#divSelect").html(data);
        },
        error: function () {
            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                type: 'danger',
                delay: 5000,
                allow_dismiss: true
            });
            $("#divSelect").html('');
        }
    });
}