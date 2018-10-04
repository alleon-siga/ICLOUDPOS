$("#fecha").datepicker({
    format: 'dd-mm-yyyy'
});


$(document).ready(function () {
    
    function bloqueo(bl) {
        document.getElementById('f6guardar').disabled = bl;
        document.getElementById("myFieldset").disabled = bl;
        document.getElementById("myFieldset1").disabled = bl;
        document.getElementById("myFieldset2").disabled = bl;
    }
    if ($('#tipo_gasto option:selected').text() == 'PRESTAMO BANCARIO') {
        controles(true);
    }

    $("#detalleModal").load(url + 'gastos/detalle');
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
        if (e.keyCode == F6 && $("#agregar").is(":visible") == true) {
            e.preventDefault();
            e.stopImmediatePropagation();
            grupo.guardar();
        }
    });
    //$("#proveedor").chosen();

    setTimeout(function () {
        $(".select_chosen").chosen();
        $('#filter_local_id').trigger('change');
    }, 500);
    get_persona_gasto();
    $("#persona_gasto").on('change', function () {
        get_persona_gasto();
    });
    $('#filter_local_id').on('change', function () {
        $('#cuenta_id').chosen('destroy');
        var cuenta_select = $('#cuenta_id');
        cuenta_select.html('<option value="">Seleccione</option>');
        if ($(this).val() != "") {
            var slt;
            for (var i = 0; i < cuentas.length; i++) {
                if (cuentas[i].local_id == $(this).val()) {
                    slt = "";
                    if (cuentas[i].id == caja_desglose_id) {
                        slt = "selected";
                    }
                    cuenta_select.append('<option data-moneda="' + cuentas[i].simbolo + '" value="' + cuentas[i].id + '" ' + slt + '>' + cuentas[i].descripion + ' | ' + cuentas[i].moneda_nombre + '</option>');
                }
            }
            $('.idMoneda').text($('#cuenta_id').find(':selected').data('moneda'));
        }

        cuenta_select.chosen();
    });
    $('#tipo_pago').on('change', function () {
        var opt = $('#tipo_pago').find(':selected').val();
        if (opt === '2') {
            $('#dispago').hide();
            $('#discuenta').hide();
            $('#selmoneda').show();
        } else if (opt === '1') {
            $('#dispago').show();
            $('#discuenta').show();
            $('#selmoneda').hide();
        }
    });
    $('#metodo_pago').on('change', function () {
//        if ($('#tipo_moneda').find(':selected').data('idmonto') != undefined) {
        $('#cuenta_id').chosen('destroy');
        var cuenta_select = $('#cuenta_id');
        cuenta_select.html('<option value="">Seleccione</option>');
        if ($('#metodo_pago').find(':selected').data('metodo') == "BANCO") {
            var slt;
            for (var i = 0; i < cuentas.length; i++) {
                if (cuentas[i].banco == 1) {

                    slt = "";
                    if (cuentas[i].id == caja_desglose_id) {
                        slt = "selected";
                    }

                    cuenta_select.append('<option data-moneda="' + cuentas[i].simbolo + '" value="' + cuentas[i].id + '" ' + slt + '>' + cuentas[i].descripion + ' | ' + cuentas[i].moneda_nombre + '</option>');
                }
                $('#cuenta_id').chosen('destroy');
            }
            $('.idMoneda').text($('#cuenta_id').find(':selected').data('moneda'));
        } else if ($('#metodo_pago').find(':selected').data('metodo') == "CAJA") {
            var slt;
            for (var i = 0; i < cuentas.length; i++) {
                if (cuentas[i].banco == '') {
                    slt = "";
                    if (cuentas[i].id == caja_desglose_id) {
                        slt = "selected";
                    }
                    cuenta_select.append('<option data-moneda="' + cuentas[i].simbolo + '" value="' + cuentas[i].id + '" ' + slt + '>' + cuentas[i].descripion + ' | ' + cuentas[i].moneda_nombre + '</option>');
                }
                $('#cuenta_id').chosen('destroy');
            }
            $('.idMoneda').text($('#cuenta_id').find(':selected').data('moneda'));
        } else {
            cuenta_select.html('<option value="">Seleccione</option>');
            $('#cuenta_id').chosen('destroy');
        }
//        } else if ($('#tipo_moneda').find(':selected').data('idmonto') == undefined) {
//            var growlType = 'warning';
//            $.bootstrapGrowl('<h4>Debe seleccionar la moneda</h4>', {
//                type: growlType,
//                delay: 2500,
//                allow_dismiss: true
//            });
//            $('#metodo_pago').val('');
//            $("#metodo_pago").trigger('chosen:updated');
//        }
    });
    $('#cuenta_id').on('change', function () {
        $('.idMoneda').text($(this).find(':selected').data('moneda'));
    });
    $('#tipo_moneda').on('change', function () {
        $('#tipo_moneda').chosen('destroy');
        $('.idMoneda').text($(this).find(':selected').data('moneda'));
    });
    $('#cboDocumento').on('change', function () {
        if ($(this).val() == '1') {
            $('#gravable').val('1');
            $('#idSt').show();
            $('#idImp').show();
        } else if ($(this).val() >= '2') {
            $('#gravable').val('0');
            $('#idSt').hide();
            $('#idImp').hide();
            $('#id_impuesto').val(1);
            $("#id_impuesto").trigger('chosen:updated');
            $('#subtotal').attr('value', '0');
            $('#impuesto').attr('value', '0');
        } else if ($(this).val() == '') {
            $('#gravable').val('');
        }
    });
    $('#gravable').on('change', function () {
        var growlType = 'warning';
        if ($('#filter_local_id').val() == '') {
            $.bootstrapGrowl('<h4> Seleccione el local</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            $('#gravable').val(0);
            $("#gravable").trigger('chosen:updated');
        } else {
            if ($(this).val() == '1') {
                $('#idSt').show();
                $('#idImp').show();
                $('#cboDocumento').val('1');
            } else if ($(this).val() == '') {
                $('#cboDocumento').val('');
                $('#idSt').hide();
                $('#idImp').hide();
                $('#id_impuesto').val(1);
                $("#id_impuesto").trigger('chosen:updated');
                $('#subtotal').attr('value', '0');
                $('#impuesto').attr('value', '0');
            }
        }
    });
    $('#total').keyup(function (e) {
        var impuesto = (($('#id_impuesto option:selected').attr('data-impuesto') / 100) + 1);
        var total = $('#total').val();
        $('#subtotal').attr('value', parseFloat(total / impuesto).toFixed(2));
        $('#impuesto').attr('value', parseFloat(total - (total / impuesto)).toFixed(2));
    });
    $('#total').click(function (e) {
        var impuesto = (($('#id_impuesto option:selected').attr('data-impuesto') / 100) + 1);
        var total = $('#total').val();
        $('#subtotal').attr('value', parseFloat(total / impuesto).toFixed(2));
        $('#impuesto').attr('value', parseFloat(total - (total / impuesto)).toFixed(2));
    });
    $('#id_impuesto').on('change', function () {
        var impuesto = (($('#id_impuesto option:selected').attr('data-impuesto') / 100) + 1);
        var total = $('#total').val();
        $('#subtotal').attr('value', parseFloat(total / impuesto).toFixed(2));
        $('#impuesto').attr('value', parseFloat(total - (total / impuesto)).toFixed(2));
    });
    $('#tipo_gasto').on('change', function () {
        if ($('#tipo_gasto option:selected').text() == 'PRESTAMO BANCARIO') {
            $('#tipo_pago').val(2);
            $('#tipo_pago').trigger('chosen:updated');
            $('#descripcion').text('Prestamo bancario');
            $('#selmoneda').show();
            controles(true);
        } else {
            $('#tipo_pago').val(1);
            $('#tipo_pago').trigger('chosen:updated');
            $('#descripcion').text('');
            $('#selmoneda').hide();
            controles(false);
        }
    });
});
function get_persona_gasto() {

    if ($('#persona_gasto').val() == '') {
        $('#proveedor_block').hide();
        $('#usuario_block').hide();
        $("#proveedor").val("");
        $("#usuario").val("");
    }
    if ($('#persona_gasto').val() == '1' && $('#id').val() == '') {
        $("#proveedor").val("");
        $('#proveedor_block').show();
        $('#usuario_block').hide();
        $("#proveedor").chosen();
    }
    if ($('#persona_gasto').val() == '2' && $('#id').val() == '') {
        $("#usuario").val("");
        $('#proveedor_block').hide();
        $('#usuario_block').show();
        $("#usuario").chosen();
    }
    if ($('#persona_gasto').val() == '1' && $('#id').val() != '') {
        $('#usuario_block').hide();
        $("#usuario").hide();
        $('#proveedor_block').show();
        $('#proveedor').show();
    }
    if ($('#persona_gasto').val() == '2' && $('#id').val() != '') {
        $('#proveedor').hide();
        $('#proveedor_block').hide();
        $('#usuario_block').show();
        $("#usuario").show();
    }
}

function update_proveedor(id, nombre) {
    $('#proveedor').append('<option value="' + id + '">' + nombre + '</option>');
    $('#proveedor').val(id)
    $("#proveedor").trigger('chosen:updated');
}

function agregarDetalle() {
    if ($("#cuenta_id").val() == '') {
        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe seleccionar una cuenta</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });
        $(this).prop('disabled', true);
        return false;
    }

    if ($('#gravable').val() == '0') {
        impuesto = 0;
    } else {
        impuesto = $('#id_impuesto option:selected').attr('data-impuesto');
    }

    $('.impuesto').attr('value', impuesto);
    $('#detalleModal').modal('show');
}

function editarDetalle(id) {
    $('#load_div').show();
    $.ajax({
        url: url + 'gastos/detalle/' + id,
        type: 'POST',
        success: function (data) {
            $("#detalleModal").html(data);
            $('#load_div').hide();
            $('#detalleModal').modal('show');
        },
        error: function () {
            alert('Error');
        }
    });
}
document.getElementById('cbxactser').onchange = function () {
    document.getElementById('doc_serie').disabled = this.checked;
    document.getElementById('doc_numero').disabled = this.checked;
};
function controles(a) {
    $('#tipo_pago').prop('disabled', a);
    $('#gravable').prop('disabled', a);
    $('#cboDocumento').prop('disabled', a);
    $('#doc_serie').prop('disabled', a);
    $('#doc_numero').prop('disabled', a);
}