$(document).ready(function () {
    $("#agregarproveedor").load(ruta + 'proveedor/form');

    $("#charm").tcharm({
        'position': 'right',
        'display': false,
        'top': '50px'
    });

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

    get_gastos();

    $(".select_chosen").chosen();
    $('.chosen-container').css('width', '100%');

    $("#local_id, #tipo_gasto_id, #moneda_id, #estado_id, #persona_gasto_filter, #proveedor_filter, #usuario_filter").on('change', function () {
        $("#tabla_lista").html('');
    });

    $("#btn_filter_reset").on('click', function () {
        $('#moneda_id').val('1029').trigger('chosen:updated');
        $('#estado_id').val('').trigger('chosen:updated');
        $('#tipo_gasto_id').val('-').trigger('chosen:updated');
        $('#persona_gasto_filter').val('1').trigger('chosen:updated');
        $('#usuario_filter').val('-').trigger('chosen:updated');
        $('#proveedor_filter').val('-').trigger('chosen:updated');
        $("#charm").tcharm('hide');
        get_gastos();
    });

    $("#btn_buscar, .btn_buscar").on('click', function () {
        $("#charm").tcharm('hide');
        get_gastos();
    });

    $("#persona_gasto_filter").on('change', function () {
        get_persona_gasto_filter();
    });
});

function get_gastos() {
    var data = {
        'local_id': $('#local_id').val(),
        'tipo_gasto': $('#tipo_gasto_id').val(),
        'mes': $('#mes').val(),
        'fecha': $('#date_range').val(),
        'persona_gasto': $("#persona_gasto_filter").val(),
        'proveedor': $("#proveedor_filter").val(),
        'usuario': $("#usuario_filter").val(),
        'moneda_id': $('#moneda_id').val(),
        'estado_id': $('#estado_id').val()
    };
    $('#load_div').show();
    $.ajax({
        url: ruta + 'gastos/lista_gasto',
        data: data,
        type: 'POST',
        success: function (datos) {
            $('#load_div').hide();
            $("#tabla_lista").html(datos);
        },
        error: function () {
            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                type: 'danger',
                delay: 5000,
                allow_dismiss: true
            });
            $('#load_div').hide();
            $("#tabla_lista").html('');
        }
    });
}

function get_persona_gasto_filter() {
    if ($('#persona_gasto_filter').val() == '') {
        $('#proveedor_block_filter').hide();
        $('#usuario_block_filter').hide();
        $("#proveedor_filter").val("-");
        $("#usuario_filter").val("-");
    }
    if ($('#persona_gasto_filter').val() == '1') {
        $("#proveedor_filter").val("-");
        $('#proveedor_block_filter').show();
        $('#usuario_block_filter').hide();
    }
    if ($('#persona_gasto_filter').val() == '2') {
        $("#usuario_filter").val("-");
        $('#proveedor_block_filter').hide();
        $('#usuario_block_filter').show();
    }

    $("#usuario_filter").chosen();
    $("#proveedor_filter").chosen();
}

function borrar(id, nom) {
    $("#motivo").val('');
    $('#borrar').modal('show');
    $("#id_borrar").attr('value', id);
}


function editar(id) {
    $('#load_div').show();
    $("#agregar").load(ruta + 'gastos/form/' + id, function(){
        $('#agregar').modal('show');
        $('#load_div').hide();
    });
}

function agregar() {
    $('#load_div').show();
    $("#agregar").load(ruta + 'gastos/form', function(){
        $('#agregar').modal('show');
        $('#load_div').hide();
    });
}

var grupo = {
    ajaxgrupo: function () {
        return $.ajax({
            url: ruta + 'gastos'

        })
    },
    guardar: function () {
        if ($("#fecha").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar la fecha</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#descripcion").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar la descripcion</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#total").val() == '' && $('#tipo_gasto option:selected').text()!='PRESTAMO BANCARIO') { //Si esta vacio y si el tipo de gasto no es prestamo bancario
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar el monto gastado</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#tipo_gasto").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el tipo de gasto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#persona_gasto").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar la persona afectada</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#persona_gasto").val() == '1' && $("#proveedor").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el proveedor</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#persona_gasto").val() == '2' && $("#usuario").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el trabajador</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }


        if ($("#filter_local_id").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el local</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

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

        if ($("#gravable").val() == '1') {
            if ($("#id_impuesto").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el impuesto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
        }

        var impuesto = (($('#id_impuesto option:selected').attr('data-impuesto') / 100) + 1);
        var total = $('#total').val();
        if($('#gravable').val()=='0'){ //no
            $('#subtotal').attr('value', parseFloat(total).toFixed(2));
            $('#impuesto').attr('value', parseFloat(0).toFixed(2));
        }else{ //si
            $('#subtotal').attr('value', parseFloat(total / impuesto).toFixed(2));
            $('#impuesto').attr('value', parseFloat(total - (total / impuesto)).toFixed(2));
        }


        if($('#tipo_pago').val()=='2' && $('#tipo_gasto option:selected').text()!='PRESTAMO BANCARIO'){ //credito y diferente a tipo de gasto prestamo bancario
            $("#dialog_gasto_prestamo").html('');
            $('#load_div').show();
            $.ajax({
                url: url + 'gastos/dialog_gasto_credito/',
                type: 'POST',
                success: function (data) {
                    $("#dialog_gasto_credito").html(data);
                    $('#load_div').hide();
                    $('#dialog_gasto_credito').modal('show');
                    credito_init(formatPrice(total));
                },
                error: function () {
                    alert('Error');
                }
            });
        }else if($('#tipo_gasto option:selected').text()=='PRESTAMO BANCARIO'){ //si es tipo de gasto prestamo bancario
            $("#dialog_gasto_credito").html('');
            $('#load_div').show();
            $.ajax({
                url: url + 'gastos/dialog_gasto_prestamo/',
                type: 'POST',
                success: function (data) {
                    $("#dialog_gasto_prestamo").html(data);
                    $('#load_div').hide();
                    $('#dialog_gasto_prestamo').modal('show');
                    credito_init(formatPrice(total));
                },
                error: function () {
                    alert('Error');
                }
            });
        }else{
            App.formSubmitAjax($("#formagregar").attr('action'), get_gastos, 'agregar', 'formagregar');
            $.bootstrapGrowl('<h4>Solicitud procesada con &eacute;xito</h4>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });         
        }
    }
}
function eliminar() {

    if ($("#motivo").val() == '') {
        var growlType = 'warning';

        $.bootstrapGrowl('<h4>Debe ingresar un motivo</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });

        return false;
    }

    App.formSubmitAjax($("#formeliminar").attr('action'), get_gastos, 'borrar', 'formeliminar');
}

function agregarproveedor() {
    $("#formagregarproveedor").trigger("reset");
    $('#agregarproveedor').modal('show');
    setTimeout(function () {
        $('#confirmar_boton_proveedor').removeAttr("onclick");
        $('#confirmar_boton_proveedor').attr("onclick", "guardar_proveedor('producto')");
    }, 10);
}

function agregarTipoGasto() {
    $("#tipoGastoModal").load(ruta + 'tiposdegasto/form', function(){
        $('#btnGuardarTipoGasto').removeAttr("onclick");
        $('#btnGuardarTipoGasto').attr("onclick", "guardar_tipoGasto()");
        $('#nombre_tipos_gasto').focus();
    });
    $('#tipoGastoModal').modal('show');
}

function updateSelect(id, nombre){
    $('#tipo_gasto_id').append('<option value="' + id + '">' + nombre + '</option>');
    $("#tipo_gasto_id").trigger('chosen:updated');
    $('#tipo_gasto').append('<option value="' + id + '">' + nombre + '</option>');
    $('#tipo_gasto').val(id);
    $("#tipo_gasto").trigger('chosen:updated');
}

function guardar_tipoGasto(){
    if ($("#nombre_tipos_gasto").val() == '') {
        var growlType = 'warning';

        $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });
        return false;
    }

    if ($("#tipo_tipos_gasto").val() == '') {
        var growlType = 'warning';

        $.bootstrapGrowl('<h4>Debe seleccionar el tipo</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });

        $(this).prop('disabled', true);

        return false;
    }

    $('#load_div').show();
    $.ajax({
        url: ruta + 'tiposdegasto/guardar',
        type: 'POST',
        headers: {
            Accept: 'application/json'
        },
        dataType:'json',
        data: $("#formagregar").serialize(),
        success: function (data) {
            var growlType = 'success';
            $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            updateSelect(data.id, data.nombre);
            $('#tipoGastoModal').modal('hide');
            setTimeout(function () {
                $('#load_div').hide()
            }, 2000);
        },
        error: function(data){
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            setTimeout(function () {
                $('#load_div').hide()
            }, 2000)
        }
    });
}