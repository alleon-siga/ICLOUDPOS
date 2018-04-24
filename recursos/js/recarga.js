var ruta = $("#base_url").val();

$(document).ready(function(){
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

                        template += '<option value="' + data.clientes[i].id_cliente + '" data-ruc="' + data.clientes[i].ruc + '">' + data.clientes[i].razon_social + '</option>';
                    }
                    $("#cliente_id").html(template);

                    $("#cliente_id").val(selected).trigger("chosen:updated");
                    $("#cliente_id").change();
                }
            });
        }
    });

    $("#terminar_venta").on('click', function (e) {
        e.preventDefault();
        $("#dialog_venta_contado").html($("#loading").html());
        $('#dialog_venta_contado').modal('show');
        $("#dialog_venta_contado").load(ruta + 'venta_new/dialog_venta_contado', function(){
            let num = parseFloat($('#total_importe').val());
            $('#vc_total_pagar').attr('value', num.toFixed(2));
            $('#vc_importe2').attr('value', $('#vc_importe').val());
            $('#vc_vuelto2').attr('value', $('#vc_vuelto').val());
            $("#contado_tipo_pago").attr('value', $('#tipo_pago').val());
        }); 
    });    

    $('.date-picker').datepicker({
        weekStart: 1,
        format: 'dd/mm/yyyy'
    });
});

function save_venta_contado(imprimir){
    /*if (isNaN(parseFloat($('#vc_importe').val()))) {
        show_msg('warning', '<h4>Error. </h4><p>El importe tiene que ser numerico.</p>');
        setTimeout(function () {
            $("#vc_importe").trigger('focus');
        }, 500);
        return false;
    }

    if ($("#vc_forma_pago").val() == '3' && $("#vc_vuelto").val() < 0) {
        show_msg('warning', '<h4>Error. </h4><p>El importe no puede ser menor que el total a pagar. Recomendamos una venta al Cr&eacute;dito.</p>');
        setTimeout(function () {
            $("#vc_importe").trigger('focus');
        }, 500);
        return false;
    }
    if ($("#vc_forma_pago").val() != '3' && $("#vc_num_oper").val() == '') {
        show_msg('warning', '<h4>Error. </h4><p>El campo Operaci&oacute;n # es obligatorio.</p>');
        setTimeout(function () {
            $("#vc_num_oper").trigger('focus');
        }, 500);
        return false;
    }
    if (($("#vc_forma_pago").val() == '4' || $("#vc_forma_pago").val() == '8' || $("#vc_forma_pago").val() == '9' || $("#vc_forma_pago").val() == '7') && $("#vc_banco_id").val() == '') {
        show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar un Banco</p>');
        setTimeout(function () {
            $("#vc_banco_id").trigger('focus');
        }, 500);
        return false;
    }*/

    $.ajax({
        url: ruta + 'venta_new/save_recarga/',
        type: 'POST',
        dataType: 'json',
        data: $('#frmRecarga').serialize(),
        success: function (data) {
            if (data.success == '1') {
                show_msg('success', '<h4>Correcto. </h4><p>La venta numero ' + data.venta.venta_id + ' se ha pagado con exito.</p>');
                if (imprimir == '1') {
                    $("#dialog_venta_imprimir").html('');
                    $("#dialog_venta_imprimir").modal('show');

                    $.ajax({
                        url: ruta + 'venta_new/get_venta_previa',
                        type: 'POST',
                        data: {'venta_id': data.venta.venta_id},

                        success: function (data) {
                            $("#dialog_venta_imprimir").html(data);
                            $("#loading_save_venta").modal('hide');
                        }
                    });
                }
            }else{
                if (data.msg)
                    show_msg('danger', '<h4>Error. </h4><p>' + data.msg + '</p>');
                else
                    show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');
            }
        },
        error: function (data) {
            show_msg('danger', '<h4>Error. </h4><p>Ha ocurrido un error insperado al guardar la venta.</p>');
        },
        complete: function (data) {
            $('.save_venta_contado').removeAttr('disabled');
        }
    });

    /*$("#loading_save_venta").modal('show');
    $("#dialog_venta_contado").modal('hide');
    $('.save_venta_contado').attr('disabled', 'disabled');*/
}