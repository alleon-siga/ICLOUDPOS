var lst_producto = [];
var tablaListaCompras;
var contador_productos = 0;
var montoTotal = 0;
var ruta = $("#base_url").val();

$(document).ready(function () {

    $(document).off('keyup');
    $(document).off('keydown');

    var ctrlPressed = false;
    var tecla_ctrl = 17;
    var tecla_enter = 13;

    $(document).keydown(function (e) {

        if (e.keyCode == tecla_ctrl) {
            ctrlPressed = true;
        }
    });

    $(document).keyup(function (e) {

        if (e.keyCode == tecla_ctrl) {
            ctrlPressed = false;
        }

        if (ctrlPressed && e.keyCode == tecla_enter && $("#cboProducto").val() != "") {
            e.preventDefault();
            e.stopImmediatePropagation();
            agregarProducto();
        }
    });

    /*esto hace que los nombres de los productos se puedan buscar facilmente*/
    jQuery('#cboProducto').chosen({search_contains: true});

    $("#cerrar_numero_series").on('click', function () {
        $("#producto_serie").modal('hide');
    });

    /*esta funcion va a verificar si existe un ingreso, para traer su detalle y mostrarlo en pantalla*/
    if ($("#ingreso_id").val() != "") {
        buscardetalle()
    }

    $("#local").on('change', function () {

        if ($(this).val() != "") {

            $("#local_hidden").val($(this).val())
        } else {
            $("#local_hidden").val()
        }

    });
    $("#local_hidden").val($('#local').val());

    $("select").chosen({width: '100%'}).trigger("chosen:updated");
    $("#fecEmision").datepicker({format: 'dd-mm-yyyy'});


    updateMonedas();
    $('body').off('keydown');
    $('body').on('keydown', function (e) {
        if (e.keyCode == 117) {
            e.preventDefault();
            if ($(".modal").is(":visible") == false) {
                /*dependiendo de el valor de costo, se llama a la funcion que valida los campos*/
                if ($("#costos").val() == 'true') {
                    validar_ingreso()
                } else {
                    validar_registro_existencia()
                }
            }
            else {
                guardaringreso();
            }


        }
    });

    $(".closemodificarcantidad").on('click', function () {
        $("#modificarcantidad").modal('hide');
    });

    $("#config_moneda").click(function (e) {
        e.preventDefault();
        var tasa_val = $("#monedas option:selected").attr('data-tasa');

        $("#monedas").attr('disabled', true).trigger("chosen:updated");

        if ($(this).attr('data-action') == "1") {
            var tasa = parseFloat($("#tasa_id").val());

            if ((!isNaN(tasa) && tasa > 0) || $("#monedas option:selected").val() == $('#MONEDA_DEFECTO_ID').val()) {



                /*si es una facturacion, dejo editar el campo*/
                if ($("#facturar").val() == "SI") {
                    $("#tasa_id").prop('readonly', false);

                    $("#cboProducto").attr('disabled', false).trigger("chosen:updated");
                } else {

                    if ($("#ingreso_id").val() != "") {

                        $("#cboProducto").attr('disabled', true).trigger("chosen:updated");
                    } else {

                        $("#cboProducto").attr('disabled', false).trigger("chosen:updated");
                    }

                    $("#tasa_id").prop('readonly', true);
                }


                $(this).attr('data-action', "0");
                $(this).removeClass('btn-primary');
                $(this).addClass('btn-warning');
                $(this).html('Cambiar Moneda');
            }
            else {
                $.bootstrapGrowl('<h4>Debe escribir una tasa válida.</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        } else {

            $("#reiniciar").click();
        }

    });


    $("#monedas").change(function (e) {
        updateMonedas();
    });

    function updateMonedas() {

        /*esto lo hago para saber si voy a bloquear el select de productos o no*/
        if ($("#costos").val() == "true" && $("#facturar").val() == "NO") {
            $("#cboProducto").prop('disabled', true).trigger("chosen:updated");
        } else {
            $("#cboProducto").prop('disabled', false).trigger("chosen:updated");
        }


        var tasa_val = $("#monedas option:selected").attr('data-tasa');
        var tasa_simbolo = $("#monedas option:selected").attr('data-simbolo');

        $('.tipo_moneda').html(tasa_simbolo);
        $(".tipo_tasa").html(tasa_simbolo);

        if ($("#monedas option:selected").val() == $('#MONEDA_DEFECTO_ID').val())

            if ($("#facturar").val() == "SI") {
                $("#tasa_id").prop('disabled', false);
            } else {
                $("#tasa_id").prop('disabled', true);
            }
        else
            $("#tasa_id").prop('disabled', false);

        $("#tasa_id").val(tasa_val);
        $("#moneda_id").val($("#monedas").val());

    }

    function calcTasa(val) {
        var tasa_val = $("#tasa_id").val().trim() != "0.00" ? parseFloat($("#tasa_id").val().trim()) : 1;
        var tasa_oper = $("#monedas option:selected").attr('data-oper');

        if (tasa_oper == "/") {
            return (parseFloat(val) / tasa_val).toFixed(2);
        }
        else if (tasa_oper == "*") {
            return (parseFloat(val) * tasa_val).toFixed(2);
        }
        else return parseFloat(val).toFixed(2);
    }

    $("#btnGuardar").click(function () {
        if ($("#costos").val() == 'false') {
            validar_registro_existencia()
        } else {
            validar_ingreso()
        }
    });

    $("#precio").mouseenter(function () {
        $("#precio").attr('title', 'Ultimo Costo ingresado del producto')

    });


    var f = new Date();
    // document.getElementById('fecIni').value = "01/01/2010";
    // document.getElementById('fecFin').value = (f.getMonth() + 1) + "/" + f.getDate() + "/" + f.getFullYear();
    // document.getElementById( f.getDate() + "-"+'fecEmision').value = (f.getMonth() + 1) + "-"  + f.getFullYear();

    $("#ec_excel").hide();
    $("#ec_pdf").hide();

    $("#cboProducto").chosen({
        placeholder: "Seleccione el producto",
        allowClear: true,
        search_contains: true
    });


    $('#cboProducto').on("change", function (e) {
        e.preventDefault();
        $(".form_div").hide();

        if ($(this).val() == "") {
            return false;
        }

        var producto_id = $(this).val();

        $("#loading").show();
        $.ajax({
            url: ruta + 'ingresos/get_unidades_has_producto',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {'id_producto': $(this).val(), 'moneda_id': $("#monedas").val()},
            success: function (data) {

                var form = $("#producto_form");
                form.html('');
                for (var i = 0; i < data.unidades.length; i++) {
                    var template = '<div class="col-md-2">';

                    var cost = get_costo_producto(producto_id, data.unidades[i].id_unidad, -1);
                    if (cost == -1) {
                        cost = data.unidades[i].costo;

                        var oper = $("#monedas option:selected").attr('data-oper');
                        var tasa = $("#monedas option:selected").attr('data-tasa');
                        var tasa_per = $("#tasa_id").val();

                        /*if (tasa != '0.00' && tasa != undefined) {

                         if (oper == "/") {
                         cost = ((parseFloat(cost) * parseFloat(tasa)) / parseFloat(tasa_per)).toFixed(2);
                         }
                         else if (oper == "*") {
                         cost = ((parseFloat(cost) / parseFloat(tasa)) * parseFloat(tasa_per)).toFixed(2);
                         }
                         else return parseFloat(cost).toFixed(2);
                         }*/

                    }

                    var cantidad_unidades = data.unidades[i].unidades;
                    if ((i + 1) == data.unidades.length) {
                        cantidad_unidades = 1;
                        data.unidades[i].unidades = cantidad_unidades;
                        $("#um_minimo").html(data.unidades[i].nombre_unidad);
                    }

                    template += '<div>';
                    template += '<input type="number" class="input-square input-mini form-control text-center cantidad-input" ';
                    template += 'id="cantidad_' + data.unidades[i].id_unidad + '" ';
                    template += 'data-costo="' + cost + '" ';
                    template += 'data-unidades="' + data.unidades[i].unidades + '" ';
                    template += 'data-unidad_id="' + data.unidades[i].id_unidad + '" ';
                    template += 'data-unidad_nombre="' + data.unidades[i].nombre_unidad + '" ';
                    template += 'data-minimo="0" ';
                    template += 'onkeydown="return soloDecimal(this, event);">';
                    template += '</div>';

                    template += '<h5>' + data.unidades[i].nombre_unidad + '</h5>';


                    template += '<h6>' + cantidad_unidades + ' ' + data.unidades[data.unidades.length - 1].nombre_unidad + '</h6>';

                    template += '</div>';


                    form.append(template);

                    var cantidad = $("#cantidad_" + data.unidades[i].id_unidad);
                    var cant = get_value_producto(producto_id, data.unidades[i].id_unidad, -1);
                    if (cant == -1) {
                        cantidad.attr('value', '0');
                        cantidad.attr('data-value', '0');
                    }
                    else {
                        cantidad.attr('value', cant);
                        cantidad.attr('data-value', cant);
                    }

                    if ((i + 1) == data.unidades.length)
                        cantidad.attr('min', '1');

                    if (data.unidades[i].producto_cualidad == "MEDIBLE") {
                        cantidad.attr('min', '0');
                        cantidad.attr('step', '1');


                    } else {
                        cantidad.attr('min', '0.0');
                        cantidad.attr('step', '0.1');

                    }
                }

                $(".cantidad-input").attr('data-um-minimo', data.unidades[data.unidades.length - 1].nombre_unidad)

                //estructuro la cofiguracion inicial, el costo unitario de la unidad menor
                var unidad_minima = $("#cantidad_" + data.unidades[data.unidades.length - 1].id_unidad);
                unidad_minima.attr('data-minimo', '1');
                var costo = unidad_minima.attr('data-costo');
                $("#precio").val(parseFloat(costo).toFixed(3));


                //Este ciclo es para los datos iniciales del total y el importe
                var total = 0;
                $(".cantidad-input").each(function () {
                    var input = $(this);
                    if (input.val() != 0) {
                        total += parseFloat(input.val() * input.attr('data-unidades'));
                    }
                });
                $("#total_unidades").val(total);
                $("#total_precio").val(parseFloat($("#total_unidades").val() * costo).toFixed(2));


                //AGREGO LOS EVENTOS
                $(".cantidad-input").bind('keyup change click mouseleave', function () {
                    var item = $(this);
                    if (item.val() != item.attr('data-value')) {
                        item.attr('data-value', item.val());
                        var data_total = 0;
                        $(".cantidad-input").each(function () {
                            var input = $(this);
                            if (input.val() != 0) {
                                data_total += parseFloat(input.val() * input.attr('data-unidades'));
                            }
                        });

                        $("#total_unidades").val(data_total);

                        if ($("#precio_base").val() == 'COSTO')
                            $("#precio").keyup();
                        else if ($("#precio_base").val() == 'IMPORTE')
                            $("#total_precio").keyup();


                        //$("#total_precio").val(parseFloat($("#total_unidades").val() * $("#precio").val()).toFixed(2));
                    }
                });

                $("#precio").keyup(function () {
                    $("#total_precio").val(roundPrice(parseFloat($("#total_unidades").val() * $("#precio").val())));
                });


                $("#total_precio").keyup(function () {
                    var total = $("#total_unidades").val();
                    if (total > 0 && total != "")
                        $("#precio").val(roundPrice(parseFloat($("#total_precio").val() / total), 3));
                    else
                        $("#precio").val('0');
                });


            },
            complete: function (data) {
                $("#loading").hide();
                $("#botonconfirmar").show()
                /*si costos es igual a true es porque es un ingreso normal, tambien entrara, cuando se este valorizando el ingreso*/
                if ($("#costos").val() == 'true' && $("#ingreso_id").val() != "") {
                    setTimeout(function () {

                        $("#acomodar_boton_confirmar").remove();
                        $("#mostrar_totales").show();
                        $(".cantidad-input").prop('readonly', true);
                        $(".form_div").show();

                    }, 10);
                }

                if ($("#costos").val() == 'true' && $("#ingreso_id").val() != "" && $("#facturar").val() != "NO") {
                    setTimeout(function () {

                        $("#acomodar_boton_confirmar").remove();
                        $("#mostrar_totales").show();
                        $(".cantidad-input").prop('readonly', false);
                        $(".form_div").show();

                    }, 10);
                }

                if ($("#costos").val() == 'true' && $("#ingreso_id").val() == "") {
                    $(".cantidad-input").prop('readonly', false);
                    $("#acomodar_boton_confirmar").remove();
                    $("#mostrar_totales").show();
                    $(".form_div").show();
                }
                if ($("#costos").val() == 'false') {
                    $(".cantidad-input").prop('readonly', false);
                    $(".form_div").append('<div class="col-md-10" id="acomodar_boton_confirmar"> </div>');
                    $("#mostrar_totales").show();
                }

            }
        })


    });


    $("#cboProveedor").chosen({
        placeholder: "Seleccione el producto",
        allowClear: true,
        search_contains: true
    });


    $("#impuestos").chosen({
        placeholder: "Seleccione el impuesto",
        allowClear: true,
        search_contains: true
    });
    tablaListaCompras = $('#tbLista').dataTable({
        "aoColumns": [
            {"sWidth": "15%", "mDataProp": "nroDocumento"},
            {"sWidth": "15%", "mDataProp": "Documento"},
            {"sWidth": "15%", "mDataProp": "FecRegistro"},
            {"sWidth": "15%", "mDataProp": "FecEmision"},
            {"sWidth": "15%", "mDataProp": "RazonSocial"},
            {"sWidth": "15%", "mDataProp": "Responsable"}
        ],
        "fnCreatedRow": function (nRow, aData, iDisplayIndex) {
        },
        "aaSorting": [[0, 'asc'], [1, 'asc']],
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ registros por página"
        }
    });


    $("#btnBuscar").click(function (e) {
        e.preventDefault();
        document.getElementById('fecIni1').value = $("#fecIni").val();
        document.getElementById('fecFin1').value = $("#fecFin").val();
        document.getElementById('fecIni2').value = $("#fecIni").val();
        document.getElementById('fecFin2').value = $("#fecFin").val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: $('#frmBuscar').serialize(),
            url: ruta + 'ingresos/lst_reg_ingreso',
            success: function (data) {
                tablaListaCompras.fnAddData(data);
            }
        });
    });

    $("#tipo_impuesto").on('change', function () {
        calcular_pago();
    });

    $("#tabla_vista").click(function () {
        updateView(get_type_view());
    });

    //Sección Proveedor

});

/*****************************aqui termina el document. ready****/



function buscardetalle() {
    /*este metodo busca a ver si hay delallesde el ingreso*/
    $.ajax({
        type: 'POST',
        data: {'idingreso': $("#ingreso_id").val(), 'facturar': $("#facturar").val()},
        url: ruta + 'ingresos/get_detalle_ingresos',
        dataType: 'json',
        success: function (data) {
            if (data.detalles) {

                var detalles = data.detalles

                for (var i = 0; i < detalles.length; i++) {
                    var producto = {};
                    producto.index = lst_producto.length;
                    producto.producto_id = detalles[i]['id_producto'];
                    producto.producto_nombre = encodeURIComponent(detalles[i]['producto_nombre']);
                    producto.cantidad = parseFloat(detalles[i]['cantidad']);
                    producto.costo_unitario = detalles[i]['precio'];
                    producto.importe = detalles[i]['total_detalle'];

                    producto.viene_bd = true;

                    producto.unidad = detalles[i]['unidad_medida'];
                    producto.unidad_nombre = detalles[i]['nombre_unidad'];

                    //estas propiedades son para calculos internos
                    producto.unidades = detalles[i]['unidades'];
                    producto.minimo = "0";
                    producto.um_min = data.um_min[detalles[i]['id_producto']];

                    if ($("#producto_serie_activo").val() == "SI") {
                        producto.series = [];
                    }

                    lst_producto.push(producto);
                }


                $("#cboProducto").val("").trigger("chosen:updated");
                $("#cboProducto").change();
                updateView(get_type_view());


            }
            else {
                $("#botonconfirmar").removeClass('disabled');
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Este Ingreso no tiene detalles</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                })

            }


        },
        error: function (data) {
            $("#barloadermodal").modal('hide');

            var growlType = 'warning';
            $.bootstrapGrowl('<h4> Ha ocurrido un error al buscar el detalle</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

        }
    });


}

function confirmDialog(func, title, msg) {

    if (func != false) {
        $("#confirm_ok").attr('onclick', func + ';' + "$('#confirm_dialog').modal('hide');");
        if (title != undefined)
            $("#confirm_title").html(title);
        else
            $("#confirm_title").html('Confirmaci&oacute;n');

        if (msg != undefined)
            $("#confirm_msg").html(msg);
        else
            $("#confirm_msg").html('Si continuas perderas todos los cambios realizados. Estas Seguro?');

        $('#confirm_dialog').modal('show');
    }
    else {
        $('#confirm_dialog').modal('hide');
    }


}

function cancelarIngreso() {
    /*con esto evito que se uede la pantalla en gris*/
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();

    $.ajax({
        url: ruta + 'principal',
        success: function (data) {
            $('#page-content').html(data);
        }
    });
}


function validar_ingreso() {
    /*esta uncionvalida todos los campos cuando es un ingreso normal*/

    if ($("#fecEmision").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe seleccionar una fecha</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#doc_serie").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un número de documento</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#doc_numero").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un número de documento</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#local").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un local</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }


    if ($("#cboTipDoc").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un tipo de documento</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#cboProveedor").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un proveedor</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#body_productos tr").length < 1) {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar al menos un producto</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#impuestos").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un impuesto</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#pago").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un tipo de pago</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#monedas").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar seleccionar una moneda</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if (($('#moneda_id').val() != $('#MONEDA_DEFECTO_ID').val() && $("#tasa_id").val() == "") || $("#tasa_id").val() < 0) {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar una tasa válida</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#pago").val() == 'CONTADO')
        $("#confirmarmodal").modal('show');
    else {
        credito_init(formatPrice($("#totApagar").val()), 'COMPLETADO');
        refresh_credito_window();
        $('#dialog_compra_credito').modal('show');
    }
}


function validar_registro_existencia() {
    /*function que valida los campos, cuando es un registro de existencia*/


    if ($("#local").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un local</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }


    if ($("#doc_serie").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un número de documento</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#doc_numero").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un número de documento</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#pago").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar un tipo de pago</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#monedas").val() == "") {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar seleccionar una moneda</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#tasa_id").val() == "" || $("#tasa_id").val() < 0) {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar una tasa válida</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }

    if ($("#body_productos tr").length < 1) {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe ingresar al menos un producto</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        $(this).prop('disabled', true);
        return false;
    }
    $("#confirmarmodal").modal('show');


}


function guardaringreso() {
    // console.log($('#frmCompra').serialize());
    // return false;
    /*esta funcion carga el modal que indica que esta procesando, y ejecuta la funcion de guardar*/
    $("#botonconfirmar").addClass('disabled');
    $("#btn_compra_credito").addClass('disabled');
    $("#barloadermodal").modal('show');

    accionGuardar();
}


//FUNCIONES PARA TRABAJAR CON LOS PRODUCTOS
//este metodo agrega y edita la tabla de los productos
function agregarProducto() {

    if ($("#cboProducto").val() == "") {
        $.bootstrapGrowl('<h4>Debe seleccionar un Producto</h4>', {
            type: 'warning',
            delay: 5000,
            allow_dismiss: true
        });
        return false;

    }

    if ($("#costos") == 'false') {
        //VALIDACIONES
        if ($("#total_unidades").val() <= 0) {
            $.bootstrapGrowl('<h4>El total no puede ser 0</h4>', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#precio").val() <= 0) {
            $.bootstrapGrowl('<h4>El costo unitario no puede ser 0</h4>', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });
            return false;
        }
    }

    /*esto es una bandera para validar si al menos una cantidad es mayor que 0*/
    var pasar = false;

    //AGREGO EL PRODUCTO A lst_producto
    $(".cantidad-input").each(function () {
        var input = $(this);

        var index = get_index_producto($("#cboProducto").val(), input.attr('data-unidad_id'));

        if (index == -1) {
            if (input.attr('data-minimo') == '1') {
                $('#producto_min_unidad').attr('data-' + $("#cboProducto").val(), input.attr('data-unidad_nombre'));
                $('#producto_min_unidad').attr('data-precio-' + $("#cboProducto").val(), parseFloat($("#total_precio").val() / $("#total_unidades").val() * input.attr('data-unidades')));
            }
            if (input.val() > 0) {
                pasar = true;
                var producto = {};
                producto.index = lst_producto.length;
                producto.producto_id = $("#cboProducto").val();
                producto.producto_nombre = encodeURIComponent($("#cboProducto option:selected").text());
                producto.producto_impuesto = $("#cboProducto option:selected").attr('data-impuesto');
                producto.cantidad = input.val();
                /*si costos es igual a false, es porque es unregistro de existencia, por lo tanto
                 * no lleva importe, ni costo unitario*/
                if ($("#costos").val() == 'false') {
                    producto.costo_unitario = 0.00;
                    producto.importe = 0.00;
                } else {
                    producto.costo_unitario = parseFloat($("#total_precio").val() / $("#total_unidades").val() * input.attr('data-unidades'));
                    producto.importe = parseFloat(producto.cantidad * producto.costo_unitario);
                }

                producto.unidad = input.attr('data-unidad_id');
                producto.unidad_nombre = input.attr('data-unidad_nombre');

                //estas propiedades son para calculos internos
                producto.unidades = input.attr('data-unidades');
                producto.minimo = input.attr('data-minimo');
                producto.um_min = input.attr('data-um-minimo');


                if ($("#producto_serie_activo").val() == "SI") {
                    producto.series = [];
                }

                lst_producto.push(producto);
            }
        }
        else {
            if (input.val() > 0) {
                lst_producto[index].cantidad = input.val();

                /*si costos es igual a false, es porque es unregistro de existencia, por lo tanto
                 * no lleva importe, ni costo unitario*/
                if ($("#costos").val() == 'false') {
                    lst_producto[index].costo_unitario = 0.00;
                    lst_producto[index].importe = 0.00;
                } else {


                    lst_producto[index].costo_unitario = parseFloat($("#total_precio").val() / $("#total_unidades").val() * input.attr('data-unidades'));
                    lst_producto[index].importe = parseFloat(lst_producto[index].cantidad * lst_producto[index].costo_unitario);
                }

                if ($("#producto_serie_activo").val() == "SI") {
                    lst_producto[index].series = [];
                }
                pasar = true;
            }
            else if (input.val() == 0) {

                lst_producto.splice(index, 1);

                for (var i = 0; i < lst_producto.length; i++) {
                    lst_producto[i].index = i;
                }
            }
        }
    });


    if (pasar == true) {
        $("#hiden_local").val($("#cboProducto").val())
        $("#cboProducto").val("").trigger("chosen:updated");
        $("#cboProducto").change();


        /*para que desaparesca la pantalla de las unidades y el costo unitario*/
        setTimeout(function () {

            $("#mostrar_totales").css('display', 'none');
            $(".form_div").css('display', 'none');
            $("#botonconfirmar").css('display', 'none');

        }, 10);

        updateView(get_type_view());
    } else {

        $.bootstrapGrowl('<h4>Debe ingresar una cantidad mayor a 0</h4>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });

        return false;
    }

}

//aqui selecciono el producto con sus valores para editarlos de la misma forma que se insertan
function editProducto(producto_id, um_id) {

    /*valido si ya registraron la moneda*/
    if ($("#config_moneda").attr('data-action') == "1") {
        $.bootstrapGrowl('<h4>Debe configurar una moneda.</h4>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });

    } else {

        $("#cboProducto").val(producto_id).trigger("chosen:updated");
        $("#cboProducto").change();
        //revisar porque no hace el focus
        if (um_id != undefined) {
            setTimeout(function () {
                $("#cantidad_" + um_id).focus();
            }, 300);
        }

    }
}

//dependiendo de la vista selecionada realiza el borrado de la tabla
//si la vista es detalle borra por el indice y si es general por el producto_id
//depende en como tengas la vista configurada
function deleteProducto(item, type) {

    if (type == 'detalle') {
        lst_producto.splice(item, 1);

        for (var i = 0; i < lst_producto.length; i++) {
            lst_producto[i].index = i;
        }
    }
    else if (type == 'general') {
        var new_list = [];
        for (var i = 0; i < lst_producto.length; i++) {
            if (lst_producto[i].producto_id != item)
                new_list.push(JSON.parse(JSON.stringify(lst_producto[i])));
        }
        lst_producto = new_list;
    }

    updateView(get_type_view());
}

//funcion interna para sacar el indice del listado dependiendo de sus parametros
function get_index_producto(producto_id, um_id) {

    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id && lst_producto[i].unidad == um_id) {
            return lst_producto[i].index;
        }
    }

    return -1;
}

//funcion interna para sacar la cantidad del listado dependiendo de sus parametros
function get_value_producto(producto_id, um_id, defecto) {
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id && lst_producto[i].unidad == um_id) {
            return lst_producto[i].cantidad;
        }
    }
    if (defecto != undefined)
        return defecto;
    else return 0;
}

//funcion interna para sacar el costo unitario del listado dependiendo de sus parametros
function get_costo_producto(producto_id, um_id, defecto) {
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].producto_id == producto_id && lst_producto[i].unidad == um_id) {
            if (lst_producto[i].costo_unitario != 0)
                return lst_producto[i].costo_unitario;
        }
    }
    if (defecto != undefined)
        return defecto;
    else return 0;
}

//devuelve la vista seleccionada
function get_type_view() {
    if ($("#tabla_vista").prop('checked'))
        return 'detalle';
    else
        return 'general';
}

//refresca la tabla con la vista seleccionada
function updateView(type) {


    $("#body_productos").html('');

    $("#head_productos").html('<tr>' +
        '<th>#</th>' +
        '<th>Producto</th>' +
        '<th>Unidad de Medida</th>' +
        '<th>Cantidad</th>' +
        '<th>Precio U.</th>' +
        '<th>Subtotal</th>' +
        '<th>Opciones</th>' +
        '</tr>');

    switch (type) {
        case 'detalle': {
            for (var i = 0; i < lst_producto.length; i++) {
                addTable(lst_producto[i], type);
            }
            break;
        }
        case 'general': {

            var new_view = [];
            var y = 0;
            for (y = 0; y < lst_producto.length; y++) {
                var index = get_index_array(new_view, lst_producto[y]);


                if (index == -1) {
                    new_view.push(JSON.parse(JSON.stringify(lst_producto[y])));
                    var last_index = new_view.length - 1;
                    new_view[last_index].cantidad = parseFloat(new_view[last_index].cantidad) * parseFloat(new_view[last_index].unidades);
                    new_view[last_index].costo_unitario = lst_producto[y].importe / new_view[last_index].cantidad;
                }
                else {
                    new_view[index].cantidad = parseFloat(new_view[index].cantidad) + parseFloat(lst_producto[y].cantidad) * parseFloat(lst_producto[y].unidades);
                    if ($("#costos").val() == 'false') {
                        new_view[index].importe = 0.00;
                    } else {
                        new_view[index].importe = parseFloat(new_view[index].importe) + parseFloat(lst_producto[y].importe);
                        new_view[index].importe = parseFloat(new_view[index].importe);
                    }
                }
            }


            for (var i = 0; i < new_view.length; i++) {
                new_view[i].index = i;
                new_view[i].unidad_nombre = new_view[i].um_min;


                /*aqui entra solo cuando es registro de existencia*/
                if ($("#costos").val() == 'false') {
                    new_view[i].costo_unitario = 0.00;
                } else {

                    if ($("#ingreso_id").val() != "") {

                        /*Aqui entra solo cuando existe n ingreso*/

                        if ($("#hiden_local").val() == new_view[i].producto_id && $("#precio").val() != "" && $("#precio").val() > 0) {
                            /*entra aqui solo cuando el producto ya tiene un valor, en el campo costo unitario*/

                            new_view[i].costo_unitario = $("#precio").val();
                        } else {
                            /*aqui entra en el caso de un producto sin costo unitario, cuando se vaya a valorizar*/


                            new_view[i].costo_unitario = new_view[i].costo_unitario

                        }

                    } else {

                        if ($("#hiden_local").val() == new_view[i].producto_id && $("#precio").val() != "" && $("#precio").val() > 0) {

                            new_view[i].costo_unitario = $("#precio").val()
                        } else {

                        }
                    }

                }


                addTable(new_view[i], type);
            }

            break;
        }
    }

    calcular_pago();
}

function get_index_array(array, element) {

    for (var i = 0; i < array.length; i++) {
        if (array[i].producto_id == element.producto_id) {
            return i;
        }
    }

    return -1;
}

//añade un elemento a la tabla, tiene sus variaciones dependiendo del tipo de vista
function addTable(producto, type) {
    var template = '<tr>';

    template += '<td>' + (producto.index + 1) + '</td>';
    template += '<td>' + decodeURIComponent(producto.producto_nombre) + '</td>';
    template += '<td style="text-align: center;">' + producto.unidad_nombre + '</td>';
    template += '<td style="text-align: center;">' + producto.cantidad + '</td>';
    template += '<td style="text-align: right;">' + parseFloat(producto.costo_unitario).toFixed(3) + '</td>';
    template += '<td style="text-align: right;">' + parseFloat(producto.importe).toFixed(2) + '</td>';

    template += '<td class="actions" style="text-align: center;">';
    template += '<div class="btn-group"><a class="btn btn-default" data-toggle="tooltip" title="Editar cantidad" data-original-title="Editar cantidad" onclick="editProducto(' + producto.producto_id + ',' + producto.unidad + ');">';
    template += '<i class="fa fa-edit"></i></a>';
    template += '</div>';

    if ($("#producto_serie_activo").val() == "SI") {
        var item = producto.index;
        if (type == 'general')
            item = producto.producto_id;
        template += '<div style="margin-left: 10px;" class="btn-group"><a id="class_ps_' + item + '" class="btn btn-primary" data-toggle="tooltip" title="Agregar Serie" data-original-title="Agregar Serie" onclick="add_serie_listaProducto(' + item + ', \'' + type + '\');">';
        template += '<i class="fa fa-barcode"></i></a>';
        template += '</div>';
    }

    /*el boton eliminar solo aparecera, cuando no exista un ingreso, o se vaya a facturar el ingreso*/
    if ($("#ingreso_id").val() == "" || $("#facturar").val() == "SI") {
        var item = producto.index;
        if (type == 'general')
            item = producto.producto_id;
        var delete_string = "deleteProducto(" + item + ", '" + type + "');";
        template += '<div style="margin-left: 10px;" class="btn-group"><a class="btn btn-danger" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="confirmDialog(`' + delete_string + '`, `Confirmaci&oacute;n`, `Estas seguro de eliminar este elemento?`)">';
        template += '<i class="fa fa-trash-o"></i></a>';
        template += '</div>';
    }
    template += '</td>';
    template += '</tr>';

    $("#body_productos").append(template);
}

//calcula los totales del pago
function calcular_pago() {
    var costos = $("#costos").val();

    var total_importe = 0;
    for (var i = 0; i < lst_producto.length; i++) {
        total_importe = parseFloat(total_importe) + parseFloat(lst_producto[i].importe);
    }

    var total = 0;
    var impuesto = 0;
    var sub_total = 0;
    var igv = parseFloat($("#impuestos").val());

    if (costos === 'false') {

    }
    else {

        if ($("#tipo_impuesto").val() == 1) {
            total = parseFloat(total_importe);
            for (var i = 0; i < lst_producto.length; i++) {
                var factor = parseFloat((parseFloat(lst_producto[i].producto_impuesto) + 100) / 100);
                impuesto += parseFloat(lst_producto[i].importe - (lst_producto[i].importe / factor));
            }
            sub_total = parseFloat(total_importe - impuesto);
        }
        else if ($("#tipo_impuesto").val() == 2) {
            sub_total = parseFloat(total_importe);
            for (var i = 0; i < lst_producto.length; i++) {
                var factor = parseFloat((parseFloat(lst_producto[i].producto_impuesto) + 100) / 100);
                impuesto += parseFloat((lst_producto[i].importe * factor) - lst_producto[i].importe);
            }
            total = parseFloat(sub_total + impuesto);
        }
        else {
            total = parseFloat(total_importe);
            sub_total = total;
            impuesto = parseFloat(0);
        }
    }


    $('#totApagar').val(formatPrice(total));
    $('#montoigv').val(impuesto.toFixed(2));
    $('#subTotal').val(sub_total.toFixed(2));
}

//FUNCIONES PARA TRABAJAR CON LAS SERIES DE LOS PRODUCTOS
function add_serie_listaProducto(index, type) {
    var html = '';
    var ps_body = $("#producto_serie_body");

    if (type == 'detalle') {
        html += '<input type="hidden" id="val_index" value="' + index + '">';
        html += '<h4>Nuevas Series de Producto: ' + lst_producto[index].unidad_nombre + '</h4>';

        var n = 1;
        for (var i = 0; i < lst_producto[index].cantidad; i++) {
            var val = lst_producto[index].series[i];
            if (val == undefined) val = "";
            html += '<div class="row">';
            html += '<div class="control-group">';
            html += '<div class="col-md-6">';
            html += '<label class="control-label">Serie del Producto ' + (i + 1) + ':</label>';
            html += '</div>';

            html += '<div class="col-md-6">';
            html += '<input type="text" class="form-control serie-number" data-id="' + n++ + '" value="' + val + '" id="ps_' + (i + 1) + '"/>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        //Aqui muestro las series del producto pero en caso de que tenga otro UM
        for (var j = 0; j < lst_producto.length; j++) {
            if (lst_producto[j].producto_id == lst_producto[index].producto_id && j != index) {
                html += '<h4>Otras Series del Producto en el Ingreso: ' + lst_producto[j].unidad_nombre + '</h4>';
                for (var i = 0; i < lst_producto[j].cantidad; i++) {
                    var val = lst_producto[j].series[i];
                    if (val == undefined) val = "";
                    html += '<div class="row">';
                    html += '<div class="control-group">';
                    html += '<div class="col-md-6">';
                    html += '<label class="control-label">Serie del Producto ' + (i + 1) + ':</label>';
                    html += '</div>';

                    html += '<div class="col-md-6">';
                    html += '<input type="text" readonly class="form-control serie-number" data-id="' + n++ + '" value="' + val + '" id="ps_' + (i + 1) + '"/>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                }
            }
        }
    }
    else if (type == 'general') {
        html = mostrar_series_general(index);
    }
    html += '<div id="list_series"></div>';
    ps_body.html(html);

    if (type == 'detalle')
        getListSeries(lst_producto[index].producto_id);
    else if (type == 'general')
        getListSeries(index);

    $("#producto_serie").modal({show: true, keyboard: false, backdrop: 'static'});
}

function mostrar_series_general(index) {
    var producto_id = index;
    var html = '<input type="hidden" id="val_index" value="' + producto_id + '">';
    var n = 1;

    //Aqui muestro las series del producto pero en caso de que tenga otro UM
    for (var j = 0; j < lst_producto.length; j++) {
        if (lst_producto[j].producto_id == producto_id) {
            html += '<h4>Nuevas Series de Producto: ' + lst_producto[j].unidad_nombre + '</h4>';
            for (var i = 0; i < lst_producto[j].cantidad; i++) {
                var val = lst_producto[j].series[i];
                if (val == undefined) val = "";
                html += '<div class="row">';
                html += '<div class="control-group">';
                html += '<div class="col-md-6">';
                html += '<label class="control-label">Serie del Producto ' + (i + 1) + ':</label>';
                html += '</div>';

                html += '<div class="col-md-6">';
                html += '<input type="text" class="form-control serie-number" data-id="' + n + '" value="' + val + '" id="ps_' + (n++) + '"/>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
        }
    }
    return html;
}

function save_serie_listaProducto() {
    if (validateSerie() == true) {
        if (get_type_view() == 'detalle') {
            var index = $("#val_index").val();
            for (var i = 0; i < lst_producto[index].cantidad; i++) {
                lst_producto[index].series[i] = $("#ps_" + (i + 1)).val();
            }
        }
        else if (get_type_view() == 'general') {
            var n = 1;
            var producto_id = $("#val_index").val();
            for (var j = 0; j < lst_producto.length; j++) {
                if (lst_producto[j].producto_id == producto_id) {
                    for (var i = 0; i < lst_producto[j].cantidad; i++) {
                        lst_producto[j].series[i] = $("#ps_" + (n++)).val();
                    }
                }
            }
        }

        $("#producto_serie").modal('hide');
    }
    else {
        $.bootstrapGrowl('<h4>Los numeros de Serie no pueden coincidir</h4>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });
    }
}

function getListSeries(prod_id) {
    //$("#list_series").html('esto es un test' + prod_id);
    $("#list_series").load(ruta + 'ingresos/get_series/' + prod_id);
}

function validateSerie() {
    var series = $(".serie-number");
    var temp = series;
    var flag = true;
    var error = [];

    series.each(function () {
        var item = $(this);
        temp.each(function () {
            var item2 = $(this);
            if (item.attr('data-id') != item2.attr('data-id') && item.val().trim() != "")
                if (item.val().trim() == item2.val().trim()) {
                    error.push({item: item});
                    flag = false;
                }

        });
        item.css('border', '1px solid green');
    });

    for (var i = 0; i < error.length; i++)
        error[i].item.css('border', '1px solid red');

    return flag;
}

function checkProductoSerie(index) {
    for (var i = 0; i < lst_producto[index].cantidad; i++) {
        if (lst_producto[index].series[i] == undefined || lst_producto[index].series[i] == "")
            return false;
    }
    return true;
}


function reiniciar_res(costos) {


    $.ajax({
        url: ruta + 'ingresos?costos=' + costos,
        success: function (data) {
            $('#page-content').html(data);
            $("#monedas").attr('disabled', false).trigger("chosen:updated");
        }

    })

}


function accionGuardar() {


    //var miJSON = JSON.stringify(lst_producto);
    var miJSON = [];

    for (var i = 0; i < lst_producto.length; i++) {
        miJSON.push({
            index: lst_producto[i].index,
            producto_id: lst_producto[i].producto_id,
            cantidad: lst_producto[i].cantidad,
            costo_unitario: lst_producto[i].costo_unitario,
            importe: lst_producto[i].importe,
            unidad: lst_producto[i].unidad,
            unidades: lst_producto[i].unidades,
            minimo: lst_producto[i].minimo
        });
    }
    miJSON = JSON.stringify(miJSON);

    var cuotas = [];
    if ($('#pago').val() == 'CREDITO')
        cuotas = prepare_cuotas();

    $.ajax({
        type: 'POST',
        data: $('#frmCompra').serialize() + '&lst_producto=' + miJSON + '&cuotas=' + cuotas,
        url: ruta + 'ingresos/registrar_ingreso',
        dataType: 'json',
        success: function (data) {


            if (data.success && data.error == undefined) {

                $("#confirmarmodal").modal('hide');
                $("#dialog_compra_credito").modal('hide');
                if ($("#ingresomodal").length > 0) {
                    $("#ingresomodal").modal('hide');
                }
                var growlType = 'success';
                $.bootstrapGrowl('<h4>Se ha registrado el ingreso</h4> Número de ingreso: ' + data.id, {
                    type: growlType,
                    delay: 5000,
                    allow_dismiss: true
                });

                $('body').removeClass('modal-open');

                if ($("#ingreso_id").val() == '') {
                    $.ajax({
                        url: ruta + 'ingresos?costos=' + $("#costos").val(),
                        success: function (data2) {
                            $('.modal-backdrop').remove();
                            $('#page-content').html(data2);
                        }

                    })
                } else {
                    $.ajax({
                        url: ruta + 'ingresos/consultar',
                        success: function (data2) {
                            $('.modal-backdrop').remove();
                            $('#page-content').html(data2);

                        }
                    })
                }


            }
            else {
                $("#botonconfirmar").removeClass('disabled');
                $("#btn_compra_credito").removeClass('disabled');
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                })

            }
            $("#barloadermodal").modal('hide');
            $('.modal-backdrop').remove();
        },
        error: function (data) {
            $("#barloadermodal").modal('hide');


            var growlType = 'warning';
            $.bootstrapGrowl('<h4> Ha ocurrido un error al registrar el ingreso</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

        }
    });

}


function cerrar_confirmar() {

    $("#confirmarmodal").modal('hide')
}


function generar_reporte_excel() {
    document.getElementById("frmExcel").submit();
}

function generar_reporte_pdf() {
    document.getElementById("frmPDF").submit();
}

