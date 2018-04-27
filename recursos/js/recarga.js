var ruta = $("#base_url").val();

$(document).ready(function(){
    $(document).off('keyup');
    $(document).off('keydown');

    var F6 = 117;

    var disabled_save = false;
    $(document).keydown(function (e) {
        if (e.keyCode == F6) {
            e.preventDefault();
        }
    });

    $(document).keyup(function (e) {
        if (e.keyCode == F6 && $("#dialog_venta_contado").is(":visible") == false) {
            terminar_venta();
        }
        if (e.keyCode == F6 && $("#dialog_venta_contado").is(":visible") == true) {
            e.preventDefault();
            e.stopImmediatePropagation();
            save_venta_contado('1');
        }
    });
            
    $('.ctrl').chosen();
    $('.chosen-container').css('width', '100%');
    
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

    $('#reiniciar_venta').on('click', function(){
        document.frmRecarga.reset();
    });

    $("#terminar_venta").on('click', function (e) {
        e.preventDefault();
        terminar_venta();
    });    

    $('.date-picker').datepicker({
        weekStart: 1,
        format: 'dd/mm/yyyy'
    });

    $('#cliente_id').on('change', function(){
        let id = $(this).val();
        $.ajax({
            url: ruta + 'venta_new/getCliente/',
            type: 'post',
            data: { id: id },
            headers: {
                Accept: 'application/json'
            },
            success: function(data){
                var data = JSON.parse(data);
                $('#tienda').val(data.nota);
                $('#nro_recarga').val(data.telefono1);
                $('#poblado_id').val(data.id_grupos_cliente);
                $('#cod_tran').focus();
            }
        });
    });
});

function save_venta_contado(imprimir){
    if (isNaN(parseFloat($('#vc_importe').val()))) {
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
    }

    $("#loading_save_venta").modal('show');
    $("#dialog_venta_contado").modal('hide');
    $('.save_venta_contado').attr('disabled', 'disabled');

    $("#vc_num_oper2").attr('value', $('#vc_num_oper').val());
    $("#vc_forma_pago2").attr('value', $('#vc_forma_pago').val());
    $("#vc_banco_id2").attr('value', $('#vc_banco_id').val());

    $.ajax({
        url: ruta + 'venta_new/save_recarga/',
        type: 'POST',
        dataType: 'json',
        data: $('#frmRecarga').serialize(),
        success: function (data) {
            if (data.success == '1') {
                show_msg('success', '<h4>Imprimiendo. </h4><p>La venta numero ' + data.venta.venta_id + ' se ha pagado con exito.</p>');
                if (imprimir == '1') {
                    let url = ruta + 'venta_new/imprimir/' + data.venta.venta_id + '/PEDIDO';
                    $("#imprimir_frame").attr('src', url);
                    document.frmRecarga.reset();
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
}

function save_venta_credito(imprimir){

    $.ajax({
        url: ruta + 'venta_new/save_recarga/',
        type: 'POST',
        dataType: 'json',
        data: $('#frmRecarga').serialize(),
        success: function (data) {
            if (data.success == '1') {
                show_msg('success', '<h4>Imprimiendo. </h4><p>La venta numero ' + data.venta.venta_id + ' se ha pagado con exito.</p>');
                if (imprimir == '1') {
                    let url = ruta + 'venta_new/imprimir/' + data.venta.venta_id + '/PEDIDO';
                    $("#imprimir_frame").attr('src', url);
                    document.frmRecarga.reset();
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
}

function terminar_venta(){
    var importe = $('#total_importe').val();
    var nro_recarga = $('#nro_recarga').val();
    var cod_tran = $('#cod_tran').val();
    if($.trim(importe)==''){
        show_msg('warning', '<h4>Advertencia. </h4><p>El importe es requerido</p>');
        setTimeout(function () {
            $("#total_importe").trigger('focus');
        }, 500);
        return false;
    }
    if(parseFloat(importe) <= 0){
        show_msg('warning', '<h4>Advertencia. </h4><p>El importe es inválido</p>');
        setTimeout(function () {
            $("#total_importe").trigger('focus');
        }, 500);
        return false;
    }
    if(isNaN(parseFloat(importe))){
        show_msg('warning', '<h4>Advertencia. </h4><p>El importe tiene que ser numérico</p>');
        setTimeout(function () {
            $("#total_importe").trigger('focus');
        }, 500);
        return false;
    }
    if($.trim(nro_recarga)==''){
        show_msg('warning', '<h4>Advertencia. </h4><p>El número de recarga es requerido</p>');
        setTimeout(function () {
            $("#nro_recarga").trigger('focus');
        }, 500);
        return false;
    }
    if($.trim(cod_tran)==''){
        show_msg('warning', '<h4>Advertencia. </h4><p>El codigo de transacción es requerido</p>');
        setTimeout(function () {
            $("#cod_tran").trigger('focus');
        }, 500);
        return false;
    }

    if($('#tipo_pago').val()==1){
        $("#dialog_venta_contado").html($("#loading").html());
        $('#dialog_venta_contado').modal('show');

        $("#dialog_venta_contado").load(ruta + 'venta_new/dialog_venta_contado', function(){
            let num = parseFloat($('#total_importe').val());
            $('#vc_total_pagar').attr('value', num.toFixed(2));
            $('#vc_importe2').attr('value', $('#vc_importe').val());
            $('#vc_vuelto2').attr('value', $('#vc_vuelto').val());
            $('#contado_tipo_pago').attr('value', $('#tipo_pago').val());
            if($('#tipo_pago').val()==1){
                $('#vc_importe').attr('value', num.toFixed(2));
            }else{
                $('#vc_importe').attr('value', 0);
            }
        });
    }else{
        save_venta_credito(1);
    }   
}