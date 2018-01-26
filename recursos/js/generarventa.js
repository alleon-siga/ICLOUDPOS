var lst_producto = [];

var cargar_venta_credito_p=false;
var cargar_venta_contado_p=false;
var cargar_existencia_producto_p=false;
var cargar_venta_caja_p=false;
var cargar_garante_p=false;

/**CACHE DE ELEMENTOS DEL DOM**/
var cache = {};
var ruta = VAR.base
var paso_existencia_pro=false;

/*esta variable es para que no se presionen bastantes veces tanto el  f6 y el boton de guardar y no realicen bastantes ventas*/
var parar_guardar=false;

$(document).ready(function () {

    App.sidebar('close-sidebar');

    /*cargamos el modal de existencia producto*/
    if(cargar_existencia_producto_p==false){
        cargar_venta_existencia_producto()
    }

    $("#mvisualizarVenta").on('show.bs.modal', function () {
        setTimeout(function () {
            $("#imprimir").focus();
        }, 50);
    });

    $("#generarventa").on('hidden.bs.modal', function () {
        $("#selectproductos").trigger('chosen:activate');
    });

    $("#mvisualizarVenta").on('hidden.bs.modal', function () {
        //$("#selectproductos").trigger('chosen:activate');

    });

    $("#btnRealizarVentaAndView").on('blur', function () {
        $("#btnRealizarVentaAndView").removeClass('btn-primary');
        $("#btnRealizarVentaAndView").addClass('btn-default');
    });

    $("#btnRealizarVentaAndView").on('focus', function () {
        $("#btnRealizarVentaAndView").removeClass('btn-default');
        $("#btnRealizarVentaAndView").addClass('btn-primary');
    });
    $("body").on("click", "#cliente_nuevo", function () {
        if ($("#cliente_nuevo").is(':checked')) {
            $("#divcantpagar").removeClass('hide');
            $("#divmto_ini_res").removeClass('hide');
        } else {
            $("#divcantpagar").addClass('hide');
            $("#divmto_ini_res").addClass('hide');
        }
    });

    $(".enter").keypress(function (event) {

        if (event.keyCode == 13) {
            currentBoxNumber = parseInt($(this).attr("enter-id"));
            nextBoxNumber = currentBoxNumber + parseInt(1);
            id = $('[enter-id=' + nextBoxNumber + ']').attr("id");

            if ($('[enter-id=' + nextBoxNumber + ']') != null) {
                $('#' + id).trigger('chosen:activate');
            }
        }
    });
    /*********************************************************/


    jQuery('#selectproductos').chosen({search_contains: true});



    //
    $(".closeformgarante").on('click', function () {
        $("#formgarante").modal('hide');
    });
    $(".closegenerarventa").on('click', function () {
        $("#generarventa").modal('hide');
        $("#generarventa1").modal('hide');
    });
    $(".closemodificarcantidad").on('click', function () {
        $("#modificarcantidad").modal('hide');
    });
    $("#cancelar").on('click', function (data) {

        if ($("#ventamodal").length > 0) {
            $("#ventamodal").modal('hide');
        }
        else if (confirm("Si cancelas perderas los cambios de la venta actual. Estas seguro?")) {
            $.ajax({
                url: ruta + 'principal',
                success: function (data) {
                    $('#page-content').html(data);
                }

            })
        }
    });


    $("#reiniciar").on('click', function (data) {
        if ($("#ventamodal").length > 0) {
            return false;
        }
        else if (confirm("Si reinicias perderas los cambios de la venta actual. Estas seguro?")) {
            $.ajax({
                url: ruta + 'venta/generarventados',
                success: function (data) {
                    $('#base_contenido').html(data);
                }

            })
        }
    });

    ajaxventa = function () {
        return $.ajax({
            url: ruta + 'venta/generarventados'

        })
    };

    $('body').off('keydown');
    $('body').on('keydown', function (e) {

        // console.log(e.keyCode);

        if (e.keyCode == 117) {
            e.preventDefault();
            if ($("#generarventa").is(":visible") || $("#generarventa1").is(":visible")) {
                hacerventa(1);

            } else {
                $("#terminarventa").click();
            }

        }

        if (e.keyCode == 27) {

            if ($("#seleccionunidades").is(":visible")) {

                $("#seleccionunidades").modal('hidde');
            }

            /* Daniel Contreras Octubre 2015 - Para que vuelva el enfoque a buscar producto */
            if ($("#generarventa").is(":visible")) {
                $("#generarventa").modal('hidde');
            }
            if ($("#mvisualizarVenta").is(":visible")) {
                $("#mvisualizarVenta").modal('hidde');
            }
        }

        if (e.keyCode == 40) {

            if ($("#seleccionunidades").is(":visible")) {

                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("#agregarproducto_salir").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {

                    var next = parseInt($(".ui-selected").attr('tabindex'));
                    var len = jQuery("#preciostbody tr").length;

                    next = next + 1;
                    if (next == len) {
                        next = 0;
                    }

                    selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(" + next + ")"));

                    return 0;
                }

            }
        }

        if (e.keyCode == 38) {

            if ($("#seleccionunidades").is(":visible")) {
                var next = parseInt($(".ui-selected").attr('tabindex'));
                var len = parseInt(jQuery("#preciostbody tr").length);
                if (next == 0) {
                    next = len - 1;
                } else {
                    next = next - 1;
                }

                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("#agregarproducto_salir").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {


                    selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(" + next + ")"));

                    return 0;
                }

            }
        }

        /* Daniel Contreras Octubre 2015 - Permite la selección de la tabla con enter y cambia al siguiente input */
        if (e.keyCode == 13) {

            if ($("#mvisualizarVenta").is(":visible")) {
                $("#imprimir").click();
            }

            if ($("#btnRealizarVentaAndView").is(':focus')) {
                //  $("#btnRealizarVentaAndView").click();
                return 0;
            }

            if ($("#seleccionunidades").is(":visible")) {


                //Se debe agregar ID en el anchor: cerrarproducto
                if ($('#cerrarproducto').is(':focus')) {
                    //alert(14);
                    $('#cerrarproducto').click();
                    return 0;
                }


                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("#agregarproducto_salir").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {

                    //alert(11);
                    $("#cantidad").removeAttr('readonly');
                    $("#cantidad").focus();
                    return 0;

                }

                if ($("#cantidad").is(':focus')) {
                    //alert(12);
                    $("#cantidad").blur();
                    $('#agregarproducto_salir').focus();
                    return 0;
                }

                if ($('#agregarproducto_salir').is(':focus')) {
                    //alert(13);
                    $("#cantidad").blur();
                    //$('#agregarproducto').click();

                }


            }
        }


        if (e.keyCode == 9) {

            if ($("#generarventa").is(":visible")) {
                e.stopPropagation();
                e.preventDefault();
                if ($("#importe").is(':focus')) {

                    $("#importe").blur();

                    setTimeout(function () {
                        $("#btnRealizarVentaAndView").focus();
                    }, 5);
                    return false;
                }

                if ($("#btnRealizarVentaAndView").is(':focus')) {

                    $("#btnRealizarVentaAndView").blur();


                    setTimeout(function () {
                        $("#importe").focus();

                    }, 5);
                    return false;
                }
            }

            if ($("#seleccionunidades").is(":visible")) {
                e.stopPropagation();
                e.preventDefault();

                if ($("#precios_chosen .chosen-search input").is(':focus')) {

                    //console.log('focus para unidades');
                    $("#precios_chosen .chosen-search input").blur();
                    if ($(".ui-selected").length == 0) {
                        selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(0)"));
                    }
                    return false;
                }


                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("agregarproducto_salir").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {
                    // console.log('focus para cantidad');
                    $("#cantidad").removeAttr('readonly');
                    setTimeout(function () {

                        setTimeout(function () {
                            $("#cantidad").focus();
                        }, 5);

                        return false;
                    }, 5)


                }

                if ($("#cantidad").is(':focus')) {
                    //console.log('focus para agregarproducto');
                    $("#cantidad").blur();

                    setTimeout(function () {
                        $("agregarproducto_salir").focus();
                    }, 5);
                    return false;
                }

                if ($("agregarproducto_salir").is(':focus')) {
                    // console.log('focus para lo primero');
                    $("agregarproducto_salir").blur();

                    setTimeout(function () {
                        $("#precios_chosen .chosen-search input").focus();
                    }, 5);
                    return false;
                }

            }
        }
        handleF();

    });


    $("#cantidad").focus(function () {
        $("#cantidad").select();
    });

    $("select").chosen({
        width: "100%"

    });

    // $(".chosen-container").css('width', '100%');


    // setTimeout(function () {
    //     $("#selectproductos").trigger('chosen:close');
    //     // $("#selectproductos_chosen .chosen-drop .chosen-search input").focus();
    // }, 50);


    activarText_ModoPago();

    $("#lstTabla").hide();


    /***
     * Esta funcion se activa al hacer click en el boton guardar y levanta el modal para ingresar el importe y calcular el vuelto
     */
    $("#terminarventa").on('click', function () {
        if(cargar_venta_credito_p==false){
            cargar_venta_credito()
        }
        re_calcularTotales();


        if ($("#totalproductos").val() == 0) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe agregar al menos un producto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (!$("#tipo_documento").val()) {
            var growlType = 'danger';

            $.bootstrapGrowl('<h4>Error</h4> <p>Es obligatorio elegir un tipo de documento, por favor, elija uno</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return;
        }

        if ($("#venta_status").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar el estado de la venta</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        //desabilite el modo de pago de credito
        var modopago = $("#cboModPag").val();
        //var modopago = "1";
        var dias = $("#diascondicionpago" + modopago).val();
        $("#diascondicionpagoinput").val(dias);
        //console.log("Dias:",dias);
        if (dias < 1) {
            if ($("#venta_status").val() == "COMPLETADO") {


                $("#generarventa").modal('show');
            }
            else if ($("#venta_status").val() == "COBRO") {
                $("#importe").val($("#totApagar2").val());

                if(cargar_venta_caja_p==false){
                    cargar_venta_caja()
                }

                generarventa_caja();

                $("#generarventa_caja").modal('show');
            }
            else {
                setTimeout(function () {
                    document.getElementById('montxcuota').value = $("#totApagar").val();
                    document.getElementById('nrocuota').value = 1;

                }, 5);
                hacerventa(0);
            }
        } else {
            /*aqui se despliega la modal de venta a credito*/

            if ($("#venta_status").val() == "COBRO") {
                $("#importe").val($("#totApagar2").val());

                if(cargar_venta_caja_p==false){
                    cargar_venta_caja()
                }
                generarventa_caja();
                $("#generarventa_caja").modal('show');
            }
            else {



                $("#generarventa1").modal('show');

                $("#precio_contado").val($("#totApagar").val());

                var mto = parseFloat($("#mtoporcen").text().split('%')[0]);
                //console.log(mto);
                $("#inicial").val($("#totApagar").val() * mto / 100);
                $("#precio_credito").val('0');
                $("#inicial").attr('disabled', 'disabled');
                $("#mto_ini_res").val($("#inicial").val());
            }
        }


    });
    $("body").on("change", "#inicial", function () {
        $("#mto_ini_res").val($("#inicial").val());
    });
    $("#porcentaje").on('click', function () {
        var porcentaje = 40;
        var precio = $("#precio_contado").val();

        if ($("#porcentaje").is(':checked')) {
            $("#inicial").attr('disabled', 'disabled');
            $("#inicial").val(parseFloat(precio) * 0.40);
        } else {
            $("#inicial").removeAttr('disabled');
            $("#inicial").focus();
        }
    });

    $("#refrescarstock").on('click', function () {
        refrescarstock();
    });

    $("#abrirventas").on('click', function () {
        if ($("#ventamodal").length > 0) {
            return false;
        }
        buscarventasabiertas();
    });


    $("body").on("click", ".cerrar_impresiones", function () {

        $.ajax({
            url: ruta + 'venta/generarventados',
            type: "post",
            success: function (data) {
                $('#base_contenido').html("");
                $('#base_contenido').html(data);
                /*esto quita despues de dos segundos que se cierra la impresion de venta credito los background negros*/
                $("body").removeClass("modal-open");
                setTimeout(function () {
                    console.log($(".modal-backdrop").remove());
                    $(".modal-backdrop .fade .in").remove();
                    $("#selectproductos_chosen").focus();
                }, 1000)
            }

        })
    });
    $("#importe").focus(function () {
        $(this).select();
    });

    $('#generarventa').on('shown.bs.modal', function (e) {


        if ($("#pago_cuenta_saved").val()!=undefined && $("#pago_cuenta_saved").val() != "") {
            $("#importe").val($("#pago_cuenta_saved").val());
            $("#vuelto").val(parseFloat($("#pago_cuenta_saved").val()) - parseFloat($("#totApagar2").val()));
        } else {
            $("#vuelto").val(0);
            $("#importe").val($("#totApagar2").val(), 0);
        }
        $("#importe").select();
        document.getElementById("importe").focus();

    });
    $('#generarventa').on('hidden.bs.modal', function (e) {
        $("#importe").val(0, 0);
        $("#vuelto").val(0, 0);
        $("#pago_cuenta_saved").val("");
    });

    $("#pago_cuenta").focus(function () {
        $(this).select();
    });

    $("#pago_cuenta").keyup(function () {
        $("#deuda_restante").val(parseFloat($("#precio_contado").val() - $("#pago_cuenta").val()).toFixed(2));
    });

    $('#generarventa1').on('shown.bs.modal', function (e) {
        $("#pago_cuenta").val(0);
        $("#deuda_restante").val(parseFloat($("#precio_contado").val()).toFixed(2));
        document.getElementById("pago_cuenta").focus();
    });

    $('#generarventa1').on('hidden.bs.modal', function (e) {
        document.getElementById("pago_cuenta").focus();
    });


    $('#seleccionunidades').on('hidden.bs.modal', function (e) {

        paso_existencia_pro=false;
        if ($('#valor_defecto').val() == "NOMBRE") {
            setTimeout(function () {
                //$("#selectproductos").trigger('chosen:open');
                // $("#selectproductos_chosen .chosen-drop .chosen-search input").focus();
            }, 50);

        } else {
            setTimeout(function () {
                $("#barra").focus();
            }, 5);
        }

        clearFields();
    });

    $('#mvisualizarVenta').on('hidden.bs.modal', function (e) {
        $("#generarventa").modal('hide');
        var growlType = 'success';

        $.bootstrapGrowl('<h4>Felicidades</h4> <p>La venta se ha guardado</p>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });
        $(this).prop('disabled', true);
        ajaxventa().success(function (data) {
            $('#base_contenido').html(data);

        })
    });


    var timerid;

    /****se valida el codigo de barra***/
    $("#barra").on("input", function (e) {

        var value = $("#barra").val();

        if ($(this).data("lastval") != value) {
            $(this).data("lastval", value);


            clearTimeout(timerid);
            timerid = setTimeout(function () {

                $.ajax({
                    type: "POST",
                    url: ruta + 'producto/validar_codigo_de_barra',
                    dataType: 'JSON',
                    headers: {
                        Accept: 'application/json'
                    },
                    data: {'codigo': value, 'id_cliente': $("#id_cliente").val()},

                    error: function () {
                        var growlType = 'danger';
                        $.bootstrapGrowl('<h4>Error</h4> <p><h5>Ha ocurrido un error</h5></p>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                        return false;
                    },
                    success: function (data) {


                        if (data.success == true) {

                            // console.log(data)
                            if (data.precios_normal.length > 0) {

                                var precios_normal = data.precios_normal;
                                var opciones = '';
                                if (data.precios_cliente) {
                                    var precios_cliente = data.precios_cliente;
                                }
                                var i = 0;


                                // $("#precios").val('');
                                //   alert(precios_normal.length);
                                $("#precios").html('<option value="">Seleccione</option>');
                                for (i = 0; i < precios_normal.length; i++) {

                                    if (precios_normal[i]['id_precio'] != '3') {
                                        if (precios_cliente['categoria_precio'] == precios_normal[i]['id_precio']) {
                                            opciones += '<option value="'
                                            + precios_normal[i]['id_precio']
                                            + '" selected>'
                                            + precios_normal[i]['nombre_precio']
                                            + '</option>';

                                        } else {

                                            if (precios_normal[i]['nombre_precio'] == "Precio Venta") {
                                                opciones += '<option value="'
                                                + precios_normal[i]['id_precio']
                                                + '" selected>'
                                                + precios_normal[i]['nombre_precio']
                                                + '</option>';
                                            } else {

                                                opciones += '<option value="'
                                                + precios_normal[i]['id_precio']
                                                + '" >'
                                                + precios_normal[i]['nombre_precio']
                                                + '</option>';

                                            }
                                        }
                                    }
                                }
                                $("#precios").append(opciones);
                                $("#precios").trigger("chosen:updated");


                            }
                            setTimeout(function () {
                                $("#poner_id_producto").attr('value', data.producto);
                                $("#poner_nombre_producto").attr('value', data.nombre);
                                // $("#selectproductos_chosen .chosen-drop .chosen-search input").focus();
                            }, 1);

                            $("#barra").val("");
                            var id = data.producto;


                            $("#seleccionunidades").modal('show');

                            if ($("#stockhidden" + id).length > 0) {

                                var val = parseFloat($("#stockhidden" + id).val());
                                var maxima = parseInt(val / parseInt(data.maxima_unidades));
                                var fraccion = parseFloat(val % parseInt(data.maxima_unidades));
                                if (data.unidad_maxima != data.unidad_minima)
                                    $("#stock").text(maxima + " " + data.unidad_maxima + "/" + fraccion + " " + data.unidad_minima);
                                else
                                    $("#stock").text(maxima + " " + data.unidad_maxima + "/" + fraccion);
                                var nueva_cantidad = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);

                                $("#stockhidden" + id).attr('value', nueva_cantidad);

                            } else {

                                $("#inentariocontainer").append('<input id="stockhidden' + id + '" type="hiden" value="0"/>');
                                if (data.unidad_maxima != data.unidad_minima)
                                    $("#stock").text(data.existencia_unidad + " " + data.unidad_maxima + "/" + data.existencia_fraccion + " " + data.unidad_minima);
                                else
                                    $("#stock").text(data.existencia_unidad + " " + data.unidad_maxima + "/" + data.existencia_fraccion);
                                var nueva_cantidad = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);

                                $("#stockhidden" + id).attr('value', nueva_cantidad);


                            }

                            $("#nombreproduto").text(data.nombre);

                            $("#producto_cualidad").attr('value', data.cualidad);
                            if (data.producto_cualidad == "MEDIBLE") {
                                $("#cantidad").attr('min', '1');
                                $("#cantidad").attr('step', '1');
                                $("#cantidad").attr('value', '1');

                            } else {
                                $("#cantidad").attr('min', '0.1');
                                $("#cantidad").attr('step', '0.1');
                                $("#cantidad").attr('value', '0.0');

                            }


                            cambiarnombreprecio_codigo_barra(data.producto);
                            setTimeout(function () {

                                // $("#precios").trigger('chosen:open');
                                //

                                //$("#precios_chosen .chosen-search input").focus();

                                selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(0)"));


                            }, 500);


                        } else {

                            var growlType = 'danger';
                            $.bootstrapGrowl('<h4>Ha ingresado un codigo de barra errado o el producto no esta disponible</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });
                            return false;

                        }
                    }

                });

            }, 800);

        }
    });


    validar_default(ventas_credito);

});



function closeseleccionunidades(){
    $("#seleccionunidades").modal('hide');

    if ($('#valor_defecto').val() == "NOMBRE") {
        setTimeout(function () {
            $("#selectproductos").trigger('chosen:open');
            // $("#selectproductos_chosen .chosen-drop .chosen-search input").focus();
        }, 50);

    } else {
        setTimeout(function () {
            $("#barra").focus();
        }, 5);
    }
}


function validar_default(v) {


    /*if (ventas_credito > ya) {
     ya++;*/

    if ($('#valor_defecto').val() == "NOMBRE") {

        if ($("#devolver").val() == 'true') {
            $("#selectproductos").attr("disabled");
            $("#selectproductos").chosen({
                disable_search: true
            });
        } else {

            //setTimeout(function () {
            //$('#selectproductos').trigger('chosen:activate');
            $('#selectproductos').trigger('chosen:open');
            //$('#selectproductos').trigger('chosen:open');
            //}, 1500);
            setTimeout(function () {
                $('#selectproductos').trigger('chosen:activate');
            }, 250);

        }


    } else {
        setTimeout(function () {
            $("#barra").focus();
        }, 5);
    }
    // }

}


function cerrarmodal_ventana_imp_cre(){
    $("#cargando_modal").modal({
        show: true,
        backdrop: 'static'
    });
    $.ajax({
        url: ruta + 'venta/generarventados',
        success: function (data) {
            $('#base_contenido').html("");
            $('#base_contenido').html(data);
            $("#cargando_modal").modal('hide');
            /*esto quita despues de dos segundos que se cierra la impresion de venta credito los background negros*/
            $("body").removeClass("modal-open");
            setTimeout(function () {
                console.log($(".modal-backdrop").remove());
                $(".modal-backdrop .fade .in").remove();
                $("#selectproductos_chosen").focus();
            }, 1000)

        }

    })
}

function selectSelectableElement(selectableContainer, elementToSelect) {
    // add unselecting class to all elements in the styleboard canvas except current one
    jQuery("tr", selectableContainer).each(function () {

        if (this != elementToSelect[0])
            jQuery(this).removeClass("ui-selected");
    });

    // add ui-selecting class to the element to select
    elementToSelect.addClass("ui-selected");

    $("#cantidad").val(1);
    selectableContainer.selectable('refresh');
    // trigger the mouse stop event (this will select all .ui-selecting elements, and deselect all .ui-unselecting elements)
    selectableContainer.data("selectable")._mouseStop(null);
}

function handleF() {
    $(document).on('keydown', function (e) {

        //   console.log(e.keyCode);

        if (e.keyCode == 116) {

            e.preventDefault();
            e.stopPropagation();
            // $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
            return false;
        }


        if (e.keyCode == 114) {
            e.preventDefault();
            e.stopPropagation();
            if ($(".modal").is(":visible")) {
                return false;
            }


            $('#barloadermodal').modal('show');

            $.ajax({
                url: ruta + 'venta/generarventados',
                success: function (data) {

                    if (data.error == undefined) {

                        $('#base_contenido').html(data);


                    } else {

                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }


                    $('#barloadermodal').modal('hide');

                },
                error: function (response) {
                    $('#barloadermodal').modal('hide');
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                }
            })
        }


        if (e.keyCode == 113) {

            e.preventDefault();
            e.stopPropagation();

            if ($(".modal").is(":visible")) {
                return false;
            }
            $('#barloadermodal').modal('show');

            $.ajax({
                url: ruta + 'producto/stock',
                success: function (data) {

                    if (data.error == undefined) {

                        $('#page-content').html(data);


                    } else {

                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }


                    $('#barloadermodal').modal('hide');

                },
                error: function (response) {
                    $('#barloadermodal').modal('hide');
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                }
            })

        }

        if (e.keyCode == 115) {

            e.preventDefault();
            e.stopPropagation();

            if ($(".modal").is(":visible")) {
                return false;
            }
            $('#barloadermodal').modal('show');

            $.ajax({
                url: ruta + 'producto/listaprecios',
                success: function (data) {

                    if (data.error == undefined) {

                        $('#page-content').html(data);


                    } else {

                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                    }


                    $('#barloadermodal').modal('hide');

                },
                error: function (response) {
                    $('#barloadermodal').modal('hide');
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                }
            })

        }
    });


}

/*DONE*/
function refrescarstock() {

    //console.log(lst_producto)
    $("#cargando_modal").modal({
        show: true,
        backdrop: 'static'
    });
    var productos_id = [];
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].default_precio != 0)
            productos_id.push({id: lst_producto[i].id_producto});
    }

    $.ajax({
        url: ruta + 'inventario/get_refresh_stock',
        data: {ids: JSON.stringify(productos_id)},
        type: 'post',
        dataType: 'json',
        success: function (data) {

            for (var i = 0; i < data.result.length; i++) {
                for (var j = 0; j < lst_producto.length; j++)
                    if (data.result[i].id_producto == lst_producto[j].id_producto && data.result[i].id_unidad == lst_producto[j].unidad_medida && data.result[i].id_precio == lst_producto[j].default_precio) {
                        var result = saveCantidadEdit(
                            lst_producto[j].count,
                            parseFloat(lst_producto[j].cantidad),
                            lst_producto[j].porcentaje_impuesto,
                            lst_producto[j].unidades,
                            lst_producto[j].producto_cualidad,
                            lst_producto[j].unidad_medida,
                            lst_producto[j].id_producto,
                            data.result[i].precio);
                    }

            }
            $('#cargando_modal').modal('hide');
        },
        error: function () {

            $('#cargando_modal').modal('hide');
        }
    })
}


function cerrar_venta() {

    $("#venta_terminada").modal("hide");
    setTimeout(function () {
        ajaxventa().success(function (data) {
            if ($("#ventamodal").length > 0) {
                $("#ventamodal").modal('hide');
            } else {
                $('#base_contenido').html(data);
            }
        })
    }, 1);
}

function hacerventa(imprimir) {

    if(parar_guardar==false) {
        parar_guardar=true
        // console.log(lst_producto);
        if ($("#id_cliente").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar el cliente</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false;
        }

        if ($("#tipo_documento").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar el tipo de documento</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false;
        }


        if ($("#cboModPag").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar el modo de pago</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false;
        }


        if ($("#venta_status").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar el status de la venta</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false;
        }


        if ($("#tbodyproductos tr[id^='producto']").length == 0) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar al menos un producto</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false;
        }


        var dias = $('#diascondicionpagoinput').val();

        var importe = parseFloat($('#importe').val());
        var totap = parseFloat($('#totApagar').val());
        var nroct = parseFloat($('#nrocuota').val());
        var vuelto = importe - totap;

        var devolver = $('#devolver').val();

        var estatus = $('#venta_status').val();


        if (dias < 1) {

            if (estatus == "COMPLETADO" || estatus == "COBRO") {
                if (totap >= 0 && vuelto >= 0) {
                    if (importe > 0 && importe >= totap) {

                        var miJSON = JSON.stringify(lst_producto);
                        var nom_doc = $("#cboTipDoc option:selected").html();
                        $.ajax({
                            type: 'POST',
                            data: $('#frmVenta').serialize() + '&lst_producto=' + miJSON + '&devolver=' + devolver,
                            dataType: 'json',
                            url: ruta + 'venta/registrar_venta',
                            success: function (data) {
                                $("#generarventa").modal('hide');

                                if (data.msj == 'guardo') {
                                    if ($("#ventamodal").is(":visible")) {
                                        $("#generarventa").modal('hide');

                                    }


                                    if (imprimir == 0) {

                                        //ImprimirDocumento(data.idventa, $('#tipo_documento').val());
                                        if (estatus != "COBRO") {
                                            var growlType = 'success';

                                            $.bootstrapGrowl('<h4>Felicidades</h4> <p>La venta ' + data.idventa + ' se ha guardado</p>', {
                                                type: growlType,
                                                delay: 2500,
                                                allow_dismiss: true
                                            });

                                            setTimeout(function () {
                                                ajaxventa().success(function (data) {
                                                    if ($("#ventamodal").length > 0) {
                                                        $("#ventamodal").modal('hide');
                                                    } else {
                                                        $('#base_contenido').html(data);
                                                    }
                                                })
                                            }, 1);

                                        } else {

                                            $("#num_venta").html(data.idventa);
                                            $("#num_doc").html(data.numero_doc);
                                            $("#nombre_cliente").html($("#id_cliente option:selected").text());
                                            $("#generarventa_caja").modal('hide');
                                            $("#venta_terminada").modal("show");

                                        }


                                    }
                                    else {
                                        cargaData_Impresion(data.idventa, data.devolver);
                                        //ImprimirDocumento(data.idventa, $('#tipo_documento').val());

                                    }


                                } else {
                                    var growlType = 'warning';

                                    $.bootstrapGrowl('<h4>Error</h4> <p>Ha ocurrido un error al guardar la venta</p>', {
                                        type: growlType,
                                        delay: 2500,
                                        allow_dismiss: true
                                    });

                                    $(this).prop('disabled', true);
                                    return false;
                                }
                            }
                        });
                        return false;
                    } else {


                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>No se admite</h4> <p> Importe Menor a Total Apagar</p>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);
                        return false;
                    }
                } else {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>No se admite</h4> <p> Importe Menor a Total Apagar</p>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);
                    return false;
                }
            } else {
                //si la venta es en estatus en espera
                var miJSON = JSON.stringify(lst_producto);
                var nom_doc = $("#cboTipDoc option:selected").html();
                $.ajax({
                    type: 'POST',
                    data: $('#frmVenta').serialize() + '&lst_producto=' + miJSON + '&devolver=' + devolver,
                    dataType: 'json',
                    url: ruta + 'venta/registrar_venta',
                    success: function (data) {
                        $("#generarventa").modal('hide');

                        if (data.msj == 'guardo') {
                            if ($("#ventamodal").is(":visible")) {
                                $("#generarventa").modal('hide');

                            }

                            // alert("asd")
                            if (imprimir == 0) {

                                //ImprimirDocumento(data.idventa, $('#tipo_documento').val());
                                if (estatus != 'COBRO') {
                                    var growlType = 'success';

                                    $.bootstrapGrowl('<h4>Felicidades</h4> <p>La venta ' + data.idventa + ' se ha guardado</p>', {
                                        type: growlType,
                                        delay: 2500,
                                        allow_dismiss: true
                                    });


                                    setTimeout(function () {
                                        ajaxventa().success(function (data) {
                                            if ($("#ventamodal").length > 0) {
                                                $("#ventamodal").modal('hide');
                                            } else {
                                                $('#base_contenido').html(data);
                                            }
                                        })
                                    }, 1);
                                } else {

                                    $("#num_venta").html(data.idventa);
                                    $("#num_doc").html(data.numero_doc);
                                    $("#nombre_cliente").html($("#id_cliente option:selected").text());
                                    $("#generarventa_caja").modal('hide');
                                    $("#venta_terminada").modal("show");

                                }
                                if (data.devolver) {
                                    $('body').removeClass('modal-open');
                                    $('.modal-backdrop').remove();
                                    $(".menulink").each(function () {
                                        if ($(this).text() == 'Devolución de Ventas') {
                                            $(this).click();

                                        }
                                    })
                                }
                            }
                            else {
                                if (data.devolver) {
                                    $('body').removeClass('modal-open');
                                    $('.modal-backdrop').remove();
                                    $(".menulink").each(function () {
                                        if ($(this).text() == 'Devolución de Ventas') {
                                            $(this).click();

                                        }
                                    })
                                }
                                cargaData_Impresion(data.idventa, data.devolver);
                                ImprimirDocumento(data.idventa, $('#tipo_documento').val());
                            }


                        } else {
                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>Error</h4> <p>Ha ocurrido un error al guardar la venta</p>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $(this).prop('disabled', true);
                            return false;
                        }
                    }
                });
                return false;

            }

        } else {
            if (totap >= 0) {
                if (nroct > 0) {
                    var credito_vista = $("#credito_vista").attr('data-vista');
                    $("#montxcuota").val($("#totApagar").val());
                    console.log('paso por el credito');
                    var miJSON = JSON.stringify(lst_producto);
                    if (credito_vista == "AVANZADO") {
                        LoadCuotas();
                        var miCuotas = JSON.stringify(lst_cuotas);
                    }
                    else {
                        var miCuotas = JSON.stringify([]);
                    }
                    $.ajax({
                        type: 'POST',
                        data: $('#frmVenta').serialize() + '&lst_producto=' + miJSON + '&devolver=' + devolver + '&lst_cuotas=' + miCuotas,
                        dataType: 'json',
                        url: ruta + 'venta/registrar_venta',
                        success: function (data) {

                            if (data.msj == 'guardo') {

                                if ($("#generarventa").is(":visible")) {
                                    $("#generarventa").modal('hide');

                                }

                                if ($("#ventamodal").is(":visible")) {
                                    $("#ventamodal").modal('hide');
                                }


                                if (imprimir == 0) {


                                    if (estatus != "COBRO") {
                                        console.log("paso por imprimir 0");
                                        if (credito_vista == 'AVANZADO') {
                                            ImprimirDocumento(data.idventa, $('#tipo_documento').val());
                                        }

                                        var growlType = 'success';

                                        $.bootstrapGrowl('<h4>Felicidades</h4> <p>La venta ' + data.idventa + ' se ha guardado</p>', {
                                            type: growlType,
                                            delay: 2500,
                                            allow_dismiss: true
                                        });

                                        $.ajax({
                                            url: ruta + 'venta/generarventados',
                                            success: function (data) {
                                                $('.modal-backdrop').remove();
                                                $('#base_contenido').html(data);
                                            }

                                        })

                                    } else {

                                        $("#num_venta").html(data.idventa);
                                        $("#num_doc").html(data.numero_doc);
                                        $("#nombre_cliente").html($("#id_cliente option:selected").text());
                                        $("#generarventa_caja").modal('hide');
                                        $("#venta_terminada").modal("show");
                                    }


                                }
                                else {


                                    // ajaxventa().success(function (data) {$('body').removeClass('modal-open');

                                    if (data.devolver) {
                                        $('body').removeClass('modal-open');
                                        $('.modal-backdrop').remove();
                                        $(".menulink").each(function () {
                                            if ($(this).text() == 'Devolución de Ventas') {
                                                $(this).click();

                                            }
                                        })
                                    } else {

                                        if (estatus != "COBRO") {
                                            $.bootstrapGrowl('<h4>Felicidades</h4> <p>La venta ' + data.idventa + ' se ha guardado</p>', {
                                                type: 'success',
                                                delay: 2500,
                                                allow_dismiss: true
                                            });
                                            if (credito_vista == 'AVANZADO') {
                                                cargaData_Impresion_credito(data.idventa, data.devolver);
                                            }
                                            else {
                                                cargaData_Impresion(data.idventa, data.devolver);
                                                //ImprimirDocumento(data.idventa, $('#tipo_documento').val());
                                            }

                                            ventas_credito++;
                                            $('.modal-backdrop').remove();
                                            $("#generarventa1").modal('hide');
                                        } else {

                                            $("#num_venta").html(data.idventa);
                                            $("#num_doc").html(data.numero_doc);
                                            $("#nombre_cliente").html($("#id_cliente option:selected").text());
                                            $("#generarventa_caja").modal('hide');
                                            $("#venta_terminada").modal("show");
                                        }
                                    }

                                    // })

                                }

                            } else {

                                var growlType = 'error';

                                $.bootstrapGrowl('<h4>Error</h4> <p> Ha ocurrido un error al guardar la venta</p>', {
                                    type: growlType,
                                    delay: 2500,
                                    allow_dismiss: true
                                });

                                $(this).prop('disabled', true);
                                return false;
                            }
                        }
                    });
                    return false;
                } else {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Faltan Datos</h4> <p> Ingrese el numero de cuotas</p>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);
                    return false;

                }
            }
        }

    }
}


/* busca el nombre precio pero pasandole el id del producto enviado por codigo de barra*/

function cambiarnombreprecio_codigo_barra(id) {


    $("#cantidad").val(0);
    $("#cantidad").attr('readonly', true);

    var tr = '';
    $("#preciostbody").html(tr);
    $("#tituloprecio").html($("#precios :selected").text());
    $.ajax({
        url: ruta + 'producto/preciosporproducto',
        data: {'producto': id, 'precio': $("#precios").val()},
        type: 'POST',
        dataType: "json",
        success: function (data) {

            for (var i = 0; i < data.length; i++) {
                tr = "<tr tabindex='" + i + "' id='" + data[i].id_unidad + "'>" +

                "<td>" +

                "<input type='hidden' name='unidadnombre' id='unidadnombre" + data[i].id_unidad + "' value='" + data[i].nombre_unidad + "'/>" + data[i].nombre_unidad + "</td>" +
                "<input type='hidden' name='unidades' id='unidades" + data[i].id_unidad + "' value='" + data[i].unidades + "'/>" +
                "<td>" + data[i].unidades + "</td>";
                //  console.log(data[i].porcentaje_impuesto);
                // console.log($("#tituloprecio").text());
                if (data[i].nombre_precio == $("#tituloprecio").text()) {
                    tr += "<td><input type='hidden' name='unidadprecio' id='unidadprecio" + data[i].id_unidad + "' value='" + data[i].precio + "'/>" +
                    "<input type='hidden' name='porcentaje_impuesto' id='porcentaje_impuesto" + data[i].id_unidad + "' value='" + data[i].porcentaje_impuesto + "'/>" +
                    "<input type='hidden' name='unidaddescuento' id='unidaddescuento" + data[i].id_unidad + "' value='" + data[i].descuento_precio + "'/>" + data[i].precio + "</td>";
                }
                tr += "</tr>";
                $("#preciostbody").append(tr);


            }


            $("#preciostbody").selectable({
                stop: function () {

                    var id = $("#preciostbody tr.ui-selected").attr('id');
                    $("#cantidad").removeAttr('readonly');
                    $("#cantidad").val(1);
                    $("#cantidad").blur();
                    $("#precios_chosen .chosen-search input").blur();
                    $("agregarproducto_salir").blur();

                }
            });

        }
    })

}


/**
 * Actualiza la tabla de precios y unidades cuando seleciono un precio del select
 */
function cambiarnombreprecio() {
    $("#cantidad").val(0);
    $("#cantidad").attr('readonly', true);

    var tr = '';
    $("#preciostbody").html(tr);
    $("#tituloprecio").html($("#precios :selected").text());
    $.ajax({
        url: ruta + 'producto/preciosporproducto',
        data: {'producto': $("#selectproductos").val(), 'precio': $("#precios").val()},
        type: 'POST',
        dataType: "json",
        success: function (data) {

            var last = data.length - 1;
            for (var i = 0; i < data.length; i++) {
                if (last == i)
                    data[i].unidades = 1;

                tr = "<tr tabindex='" + i + "' id='" + data[i].id_unidad + "'>" +

                "<td>" +

                "<input type='hidden' name='unidadnombre' id='unidadnombre" + data[i].id_unidad + "' value='" + data[i].nombre_unidad + "'/>" + data[i].nombre_unidad + "</td>" +
                "<input type='hidden' name='unidades' id='unidades" + data[i].id_unidad + "' value='" + data[i].unidades + "'/>" +
                "<td>" + data[i].unidades + "</td>";
                //  console.log(data[i].porcentaje_impuesto);
                // console.log($("#tituloprecio").text());
                if (data[i].nombre_precio == $("#tituloprecio").text()) {
                    tr += "<td><input type='hidden' name='unidadprecio' id='unidadprecio" + data[i].id_unidad + "' value='" + data[i].precio + "'/>" +
                    "<input type='hidden' name='porcentaje_impuesto' id='porcentaje_impuesto" + data[i].id_unidad + "' value='" + data[i].porcentaje_impuesto + "'/>" +
                    "<input type='hidden' name='unidaddescuento' id='unidaddescuento" + data[i].id_unidad + "' value='" + data[i].descuento_precio + "'/>" + data[i].precio + "</td>";
                }
                tr += "</tr>";
                $("#preciostbody").append(tr);


            }


            /* Daniel Contreras Octubre 2015 - Al momento de seleccionar en #precios con enter, se hace el cambio de focus a la tabla */
            setTimeout(function () {
                selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(0)"));
            }, 1);

            $("#preciostbody").selectable({
                stop: function () {

                    var id = $("#preciostbody tr.ui-selected").attr('id');
                    $("#cantidad").removeAttr('readonly');
                    $("#cantidad").val(1);
                    $("#cantidad").blur();
                    $("#precios_chosen .chosen-search input").blur();
                    $("agregarproducto_salir").blur();

                }
            });


        }
    })


}

function add_producto(elemento){

    var price = 0;
    if ($("#price_check").prop('checked') && $("#price_new").val() > 0) {
        price = $("#price_new").val();
    }

    var close = $(elemento).attr('data-close');
    agregarProducto($("#poner_id_producto").val(), $("#poner_nombre_producto").val(), price, close);

    $("#price_check").prop('checked', false);
    $("#price_new").val('');
    $("#show_price_new").hide();

}




function agregarProducto(id_producto_CB, nombre_producto, new_price, close) {
    var unidad_id = $("#preciostbody tr.ui-selected").attr('id');

    if (id_producto_CB == "") {
        var producto_id = $('#selectproductos').val();
        var producto_nombre = $('#selectproductos option:selected').attr('data-producto');

    } else {
        var producto_id = id_producto_CB;
        var producto_nombre = nombre_producto;

    }

    setTimeout(function () {
        $("#poner_id_producto").attr('value', '');
        $("#poner_nombre_producto").attr('value', '');
    }, 1);


    var unidad_nombre = $('#unidadnombre' + unidad_id).val();
    var cantidad = parseFloat($('#cantidad').val());
    var precio = $('#unidadprecio' + unidad_id).val();
    if (new_price != 0) {
        $("#default_precio").val('0');
        precio = new_price;
    }
    else
        $("#default_precio").val($('#precios').val());

    var precio_id = $('#precios').val();
    var porcentaje_impuesto = $('#porcentaje_impuesto' + unidad_id).val();
    var unidaddescuento = $('#unidaddescuento' + unidad_id).val();
    var unidades = parseFloat($('#unidades' + unidad_id).val());
    var subtotal = precio * cantidad;

    var stockhidden = $("#stockhidden_local_" + $("#select_local").val());

    var cantidad_total = (parseFloat(stockhidden.val()) - unidades * cantidad);


    if (cantidad_total < 0) {
        $.bootstrapGrowl('<h4>Stock Insuficiente!</h4> <p>' + producto_nombre + '</p>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });

        $(this).prop('disabled', true);
        $('#cerrarproducto').focus();
        return false;
    }

    var producto_cualidad = $("#producto_cualidad").val();


    if (producto_cualidad == "MEDIBLE")
        cantidadminima = 1;
    else
        cantidadminima = 0.1;

    if (cantidad == '' || isNaN(cantidad) || cantidad < cantidadminima) {
        var growlType = 'danger';

        $.bootstrapGrowl('<h4>Datos incompletos:</h4> <p>Ingrese una cantidad mayor a ' + cantidadminima + ' y menor al stock</p>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });

        $(this).prop('disabled', true);
        return false;

    }

    for (var i = 0; i < lst_producto.length; i++) {
        if (producto_id == lst_producto[i].id_producto && unidad_id == lst_producto[i].unidad_medida && lst_producto[i].local == $("#select_local").val()) {
            lst_producto[i].default_precio = $("#default_precio").val();
            var result = saveCantidadEdit(lst_producto[i].count, parseFloat(lst_producto[i].cantidad + cantidad), porcentaje_impuesto, unidades, producto_cualidad, unidad_id, producto_id, precio);
            if (!result) return false;
            if (close == '1') {
                clearFields();
                $('#seleccionunidades').modal('toggle');
            } else $("#select_local").change();
            return false;
        }
    }

    addProductoToArray(producto_id, encodeURIComponent(producto_nombre), unidad_id, unidad_nombre, cantidad, precio, subtotal, unidades, producto_cualidad, porcentaje_impuesto, $("#default_precio").val(), $("#select_local").val(), $("#select_local option:selected").text());

    update_view();

    if (close == '1') {
        clearFields();
        $('#seleccionunidades').modal('toggle');
    }
    else $("#select_local").change();
}

function generarventa_caja() {

    $("#tbody_caja").html('');

    $("#total_pagar_caja").html($("#totApagar").val());
    var prod_copy = JSON.parse(JSON.stringify(lst_producto));
    var new_view = [];
    for (var i = 0; i < lst_producto.length; i++) {
        if (lst_producto[i].local == $("#id_local").val()) {
            new_view.push(JSON.parse(JSON.stringify(lst_producto[i])));
            delete prod_copy[i];
        }
    }

    var prod_copy2 = JSON.parse(JSON.stringify(prod_copy));
    for (var i = 0; i < new_view.length; i++) {
        for (var j = 0; j < prod_copy.length; j++) {
            if (prod_copy[j] != undefined)
                if (new_view[i].id_producto == prod_copy[j].id_producto && new_view[i].unidad_medida == prod_copy[j].unidad_medida) {
                    new_view[i].cantidad += prod_copy[j].cantidad;
                    new_view[i].subtotal = parseFloat(new_view[i].cantidad * new_view[i].precio);
                    delete prod_copy2[j];
                }
        }
    }

    for (var i = 0; i < prod_copy2.length; i++) {
        if (prod_copy2[i] != undefined)
            new_view.push(JSON.parse(JSON.stringify(prod_copy2[i])))
    }

    for (var i = 0; i < new_view.length; i++) {
        var cont = i + 1;
        var tr = '<tr>';
        tr += '<td>' + cont + '</td>';
        tr += '<td>' + decodeURIComponent(new_view[i].nombre) + '</td>';
        tr += '<td>' + new_view[i].unidad_nombre + '</td>';
        tr += '<td>' + new_view[i].cantidad + '</td>';
        tr += '<td>' + new_view[i].precio + '</td>';
        tr += "<td>" + parseFloat(Math.ceil(new_view[i].subtotal.toFixed(10) * 10) / 10) + "</td>";
        tr += "</tr>";

        $("#tbody_caja").append(tr);
    }
}

$("#desglose").click(function () {
    update_view();
});

function update_view() {
    if ($("#desglose").prop('checked') != undefined) {
        if ($("#desglose").prop('checked')) {
            change_view('detalle');
        }
        else {
            change_view('general');
        }
    }
    else {
        change_view('defecto');
    }
}

function change_view(num_view) {


    $("#subTotal").val(0.00);
    $("#montoigv").val(0.00);
    $("#totApagar").val(0.00);
    $("#totApagar2").val(0.00);

    $("#totales_totApagar").val(0);
    $("#totales_montoigv").val(0);
    $("#totales_subTotal").val(0);

    $("#tbodyproductos").html('');

    switch (num_view) {
        case 'detalle':
        {
            $("#theadproductos").html('<tr style="background-color: #B1AEAE;color:#fff !important;">' +
            '<th>#</th>' +
            '<th>Nombre</th>' +
            '<th>Ubicaci&oacute;n</th>' +
            '<th>UM</th>' +
            '<th>Cantidad</th>' +
            '<th>Precio</th>' +
            '<th>Subtotal</th>' +
            '<th>Opciones</th>' +
            '</tr>');

            var new_view = lst_producto;

            for (var i = 0; i < new_view.length; i++) {
                calculatotales(new_view[i].id_producto, new_view[i].nombre, new_view[i].unidad_nombre, new_view[i].cantidad, new_view[i].precio, new_view[i].cantidad * new_view[i].precio, new_view[i].porcentaje_impuesto, new_view[i].count, new_view[i].unidades, new_view[i].producto_cualidad, new_view[i].unidad_medida, new_view[i].local_nombre);
            }

            $("#totalproductos").val(new_view.length);
            break;
        }
        case 'general':
        {

            $("#theadproductos").html('<tr style="background-color: #B1AEAE;color:#fff !important;">' +
            '<th>#</th>' +
            '<th>Nombre</th>' +
            '<th>UM</th>' +
            '<th>Cantidad</th>' +
            '<th>Precio</th>' +
            '<th>Subtotal</th>' +
            '</tr>');

            var count = 0;
            var prod_copy = JSON.parse(JSON.stringify(lst_producto));
            var new_view = [];
            var to_del = [];
            for (var i = 0; i < lst_producto.length; i++) {
                if (lst_producto[i].local == $("#id_local").val()) {
                    new_view.push(JSON.parse(JSON.stringify(lst_producto[i])));
                    delete prod_copy[i];
                }
            }


            var prod_copy2 = JSON.parse(JSON.stringify(prod_copy));
            for (var i = 0; i < new_view.length; i++) {
                for (var j = 0; j < prod_copy.length; j++) {
                    if (prod_copy[j] != undefined)
                        if (new_view[i].id_producto == prod_copy[j].id_producto && new_view[i].unidad_medida == prod_copy[j].unidad_medida) {
                            new_view[i].cantidad += prod_copy[j].cantidad;
                            new_view[i].subtotal = parseFloat(new_view[i].cantidad * new_view[i].precio);
                            delete prod_copy2[j];
                        }
                }
            }


            for (var i = 0; i < prod_copy2.length; i++) {
                if (prod_copy2[i] != undefined)
                    new_view.push(JSON.parse(JSON.stringify(prod_copy2[i])))
            }

            for (var i = 0; i < new_view.length; i++) {
                calculatotales(new_view[i].id_producto, new_view[i].nombre, new_view[i].unidad_nombre, new_view[i].cantidad, new_view[i].precio, new_view[i].subtotal, new_view[i].porcentaje_impuesto, i, new_view[i].unidades, new_view[i].producto_cualidad, new_view[i].unidad_medida, "");
            }

            $("#totalproductos").val(new_view.length);

            break;
        }
        case 'defecto':
        {

            $("#theadproductos").html('<tr style="background-color: #B1AEAE;color:#fff !important;">' +
            '<th>#</th>' +
            '<th>Nombre</th>' +
            '<th>UM</th>' +
            '<th>Cantidad</th>' +
            '<th>Precio</th>' +
            '<th>Subtotal</th>' +
            '<th>Opciones</th>' +
            '</tr>');

            var new_view = lst_producto;
            for (var i = 0; i < new_view.length; i++) {
                calculatotales(new_view[i].id_producto, new_view[i].nombre, new_view[i].unidad_nombre, new_view[i].cantidad, new_view[i].precio, new_view[i].cantidad * new_view[i].precio, new_view[i].porcentaje_impuesto, new_view[i].count, new_view[i].unidades, new_view[i].producto_cualidad, new_view[i].unidad_medida, -1);
            }

            $("#totalproductos").val(new_view.length);

            break;
        }

    }


}

function calculatotales(producto_id, producto_nombre, unidad_nombre, cantidad, precio, subtotal, porcentaje_impuesto, count, unidades, cualidad, unidad_id, local_nombre) {

    var cont = parseInt(count) + 1;
    var tr = '<tr id="producto' + count + '">';
    tr += '<td>' + cont + '</td>';

    tr += '<td>' + decodeURIComponent(producto_nombre) + '</td>';
    if (local_nombre != "" && local_nombre != -1)
        tr += '<td>' + local_nombre + '</td>';
    tr += '<td>' + unidad_nombre + '</td>';
    tr += '<td id="prod_cantidad_' + count + '">' + cantidad + '</td>';
    tr += '<td id="prod_precio_' + count + '">' + precio + '</td>';
    tr += "<td>" + parseFloat(Math.ceil(subtotal.toFixed(10) * 10) / 10) + "</td>";

    if (local_nombre != "" || local_nombre == -1) {
        tr += '<td class="actions">';

        tr += '<div class="btn-group" style="padding: 3px;">';
        tr += '<a id="prod_edit_' + count + '" style="padding: 5px 10px; border-radius: 4px;" title="Editar" href="#" onclick="editCantidad(' + count + ', ' + cantidad + ', ' + porcentaje_impuesto + ', ' + unidades + ', \'' + cualidad + '\', ' + unidad_id + ', ' + producto_id + ')" class="bt btn-default">';
        tr += '<i class="fa fa-edit"></i></a>';
        tr += '</div>';


        if ($("#serie_activa").val() == "SI") {
            tr += '<div class="btn-group" style=" padding: 3px;">';
            tr += '<a style="margin-left: 3px; padding: 5px 10px; border-radius: 4px;" title="Seleccionar Series" href="#" onclick="alert(\'Esta funcion no ha sido implementada.\')" class="bt btn-default">';
            tr += '<i class="fa fa-barcode"></i></a>';
            tr += '</div>';
        }

        tr += '<div style="margin-left: 3px; padding: 3px;" class="btn-group">';
        tr += '<a style="padding: 5px 10px; border-radius: 4px;" title="Eliminar" href="#" onclick="deleteproducto(' + count + ', ' + porcentaje_impuesto + ', \'' + cualidad + '\')" class="bt btn-danger">';
        tr += '<i class="fa fa-trash-o"></i></a>';
        tr += '</div>';

        tr += '</td>';
    }
    tr += "</tr>";

    $("#tbodyproductos").append(tr);


    $("#preciostbody").html('');
    $("#cantidad").val('');

    //TODO CALCULAR SI EL PRECIO TIENE DESCUENTOS(NO SE SABE SI VA)
    var impuesto = parseFloat(Math.ceil((subtotal * (porcentaje_impuesto / 100)) * 10) / 10);

    var nuevoimpuesto = parseFloat(impuesto) + parseFloat($("#totales_montoigv").val());
    var nuevototal = (parseFloat(subtotal) + parseFloat($("#totales_totApagar").val())).toFixed(2);
    var nuevosubtotal = parseFloat(parseFloat(subtotal) - parseFloat(impuesto)) + parseFloat($("#totales_subTotal").val());

    if(cargar_venta_contado_p==false){
        cargar_venta_contado()
    }
    document.getElementById('montxcuota').value = parseFloat(Math.ceil(nuevosubtotal * 10) / 10).toFixed(2);
    document.getElementById('totApagar').value = parseFloat(Math.ceil(nuevototal * 10) / 10).toFixed(2);
    document.getElementById('totApagar2').value = parseFloat(Math.ceil(nuevototal * 10) / 10).toFixed(2);
    document.getElementById('montoigv').value = parseFloat(Math.ceil(nuevoimpuesto * 10) / 10).toFixed(2);
    document.getElementById('subTotal').value = parseFloat(Math.ceil(nuevosubtotal * 10) / 10).toFixed(2);

    /*ESTOS SON INPUT QUE ESTAN EN generarVenta.php en el DIV de Total Productos*/
    $("#totales_totApagar").val(parseFloat(Math.ceil(nuevototal * 10) / 10).toFixed(2));
    $("#totales_montoigv").val(parseFloat(Math.ceil(nuevoimpuesto * 10) / 10).toFixed(2));
    $("#totales_subTotal").val(parseFloat(Math.ceil(nuevosubtotal * 10) / 10).toFixed(2));

    re_calcularTotales();
}


function addProductoToArray(producto_id, producto_nombre, unidad_id, unidad_nombre, cantidad, precio, subtotal, unidades, producto_cualidad, porcentaje_impuesto, default_precio, local, local_nombre) {
    var producto = {};

    producto.id_producto = producto_id;
    producto.nombre = producto_nombre;

    producto.local_nombre = local_nombre;
    producto.local = local;
    producto.default_precio = default_precio;
    producto.precio = precio;
    producto.cantidad = parseFloat(cantidad);
    producto.unidad_medida = unidad_id;
    producto.unidad_nombre = unidad_nombre;
    producto.detalle_importe = subtotal;
    producto.unidades = unidades;
    producto.producto_cualidad = producto_cualidad;
    producto.porcentaje_impuesto = porcentaje_impuesto;
    producto.count = countproducto;
    producto.subtotal = subtotal;


    if ($("#serie_activa").val() == "SI")
        producto.series = {};

    lst_producto.push(producto);
    countproducto++;
    $("#totalproductos").val(countproducto);
}

function clearFields() {
    $("#selectproductos").val('').trigger("chosen:updated");
    $('#selectproductos').trigger('chosen:open');
    $("#precios").val(1).trigger("chosen:updated");

}

function clearAllfields() {
    $("#selectproductos").trigger('chosen:open');

    setTimeout(function () {
        $("#selectproductos_chosen .chosen-drop .chosen-search input").focus();
    }, 10);
}

function editCantidad(count, cantidad, porcentaje_impuesto, unidades, cualidad, unidad_id, producto_id) {

    if ($("#devolver_activo").val() == "1")
        $("#devolver_activo").attr('data-cantidad', cantidad);

    if ($("#prod_select").val() != undefined) {
        $.bootstrapGrowl('<h4>Ya esta modificando un producto.</h4> <p>Debe finalizar primero la modificacion del otro producto.</p>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });
        return false;
    }

    var precio = $("#prod_precio_" + count).html().trim();
    $("#prod_precio_" + count).html('<input style="width: 80px; height: 23px;" type="number" id="prod_prec_' + count + '" value="' + precio + '" class="form-control input-sm" onkeydown="return soloDecimal3(this, event);">');
    $("#prod_cantidad_" + count).html('<input style="width: 80px; height: 23px;" type="number" id="prod_cant_' + count + '" value="' + cantidad + '" class="form-control input-sm" onkeydown="return soloDecimal3(this, event);">');
    $("#prod_cantidad_" + count).append('<input type="hidden" id="prod_select" value="' + count + '">');
    $("#prod_cant_" + count).focus();

    $("#prod_edit_" + count).removeClass('btn-default');
    $("#prod_edit_" + count).addClass('btn-primary');
    $("#prod_edit_" + count).attr('onclick', 'saveCantidadEdit(' + count + ', ' + cantidad + ',' + porcentaje_impuesto + ', ' + unidades + ', "' + cualidad + '", ' + unidad_id + ', ' + producto_id + ')')
}

function saveCantidadEdit(count, cantidad, porcentaje_impuesto, unidades, cualidad, unidad_id, producto_id, precio) {

    if ($("#prod_cant_" + count).val() != undefined) {
        var newcantidad = parseFloat($("#prod_cant_" + count).val());
        var newprecio = parseFloat($("#prod_prec_" + count).val());
    }
    else {
        var newcantidad = cantidad;
        var newprecio = parseFloat(precio);
    }

    $("#cantidadedit").val(newcantidad);
    $("#precioedit").val(newprecio);
    var producto_cualidad = cualidad;

    var stockhidden = $("#stockhidden_local_" + lst_producto[count].local);
    var cantidad_total = (parseFloat(stockhidden.val()) - unidades * newcantidad);

    if ($("#devolver_activo").val() == "1") {
        var old = parseFloat($("#devolver_activo").attr('data-cantidad'));
        if (newcantidad > old) {
            $.bootstrapGrowl('<h4>La cantidad de devolucion no puede ser mayor que la actual (' + old + ').</h4> ', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

    }

    if (cantidad_total < 0) {
        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Stock Insuficiente!</h4>  <p>' + lst_producto[count].nombre + '</p>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });
        return false;
    }


    if (producto_cualidad == "MEDIBLE") {
        cantidadminima = 1;

    } else {
        cantidadminima = 0.1;

    }

    if (newcantidad == '' || isNaN(newcantidad) || newcantidad < cantidadminima) {
        var growlType = 'danger';

        $.bootstrapGrowl('<h4>Datos incompletos:</h4> <p>Ingrese una cantidad mayor a ' + cantidadminima + ' y menor al stock</p>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });
        return false;
    }


    var lista_vieja = lst_producto;

    $("#subTotal").val(0.00);
    $("#montoigv").val(0.00);
    $("#totApagar").val(0.00);
    $("#totApagar2").val(0.00);

    $("#totales_totApagar").val(0);
    $("#totales_montoigv").val(0);
    $("#totales_subTotal").val(0);

    countproducto = 0;

    $("#tbodyproductos").html('');


    lst_producto = [];

    jQuery.each(lista_vieja, function (i, value) {

        if (value["count"] == count) {
            newcantidad = $("#cantidadedit").val();
            newprecio = $("#precioedit").val();
            var subtotal = newprecio * newcantidad;

            addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, newcantidad, newprecio, subtotal, value.unidades, cualidad, porcentaje_impuesto, value.default_precio, value.local, value.local_nombre);

            $.bootstrapGrowl('<h4>Modificacion ejecutada correctamente</h4>', {
                type: 'success',
                delay: 2500,
                allow_dismiss: true
            });

        } else {
            addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, value.precio, value.subtotal, value.unidades, cualidad, porcentaje_impuesto, value.default_precio, value.local, value.local_nombre);

        }
    });

    update_view();


    return true;

}

function deleteproducto(count, porcentaje_impuesto, cualidad) {

    var lista_vieja = lst_producto;
    var productoeliminar;

    $("#subTotal").val(0.00);
    $("#montoigv").val(0.00);
    $("#totApagar").val(0.00);
    $("#totApagar2").val(0.00);

    $("#totales_totApagar").val(0);
    $("#totales_montoigv").val(0);
    $("#totales_subTotal").val(0);

    countproducto = 0;

    $("#tbodyproductos").html('');

    lst_producto = [];
    jQuery.each(lista_vieja, function (i, value) {

        if (value["count"] === count) {
            eliminar = i;
            productoeliminar = value;

        } else {

            addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, value.precio, value.subtotal, value.unidades, cualidad, porcentaje_impuesto, value.default_precio, value.local, value.local_nombre);
        }
    });

    update_view();

    lista_vieja.splice(eliminar, 1);

    $("#totalproductos").val(countproducto);
}

$("#select_local").change(function (e) {
    buscarProducto();
});

$("#selectproductos").change(function (e) {

    if(paso_existencia_pro==false) {
        paso_existencia_pro=true
        $("#select_local").val($("#id_local").val());
        $("#select_local").trigger("chosen:updated");
        buscarProducto();
    }
});

/*estos 4 funciones son creadas para cargar las vistas en su momento indicado, ya que al inicio se cargaban al momento de darle click a realizar
 * venta, y relentizaba el proceso*/
function cargar_venta_caja(){

    $.ajax({
        type: 'POST',
        cache: false,
        async: false,
        url: ruta + 'venta/load_dialog_venta_caja',
        success: function (data) {
            $("#generarventa_caja").html(data);
            cargar_venta_caja_p=true;
        }
    })


}

function cargar_venta_existencia_producto(){

    $.ajax({
        type: 'POST',
        cache: false,
        async: false,
            url: ruta + 'venta/load_dialog_existencia_producto',
        success: function (data) {
            $("#seleccionunidades").html(data);
            cargar_existencia_producto_p=true;
        }
    })


}

function cargar_venta_contado(){

    $.ajax({
        type: 'POST',
        cache: false,
        async: false,
        url: ruta + 'venta/load_dialog_terminar_venta_contado',
        success: function (data) {
            $("#generarventa").html(data);
        }
    })

    cargar_venta_contado_p=true;
}

function cargar_venta_credito(){

    if(cargar_garante_p==false){
        cargar_garante()
    }

    $.ajax({
        cache: false,
        async: false,
        type: 'POST',
        url: ruta + 'venta/load_dialog_terminar_venta_credito',
        success: function (data) {
            $("#generarventa1").html(data);
            cargar_venta_credito_p=true;
        }
    })


}

function cargar_garante(){

    $.ajax({
        type: 'POST',
        url: ruta + 'venta/load_dialog_nuevo_garante',
        success: function (data) {
            $("#formgarante").html(data);
            cargar_garante_p=true;
        }
    })

}

/**
 * Levanta el modal para seleccionar los precios del producto
 */
function buscarProducto() {
    $('#cargando_modal').modal('show')

    if(cargar_venta_contado_p==false){
        cargar_venta_contado()
    }


    /*estas variables cargar_existencia_producto_p y cargar_venta_contado_p son para verificar si ya se cargo la vista o no*/





    $("#ubicacion_actual").html($("#id_local option:selected").text());
    $("#ubicacion_actual").attr('data-local', $("#id_local").val());

    var id = $("#selectproductos").val();

    if ($("#id_cliente").val() == "") {

        var growlType = 'danger';

        $.bootstrapGrowl('<h4>Por favor seleccione</h4> <p>Un Cliente</p>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        });

        return false;

    }
    if (id == '') {
        return false;
    }


    setTimeout(function () {
        $.ajax({
            type: 'POST',
            data: {'producto': id, 'id_cliente': $("#id_cliente").val(), 'local': $("#select_local").val()},
            dataType: "json",
            url: ruta + 'inventario/get_existencia_producto',
            success: function (data) {
                $('#cargando_modal').modal('hide')
                if (data.precios_normal.length > 0) {

                    var precios_normal = data.precios_normal;
                    var opciones = '';
                    if (data.precios_cliente) {
                        var precios_cliente = data.precios_cliente;
                    }
                    var i = 0;


                    $("#precios").html('<option value="">Seleccione<option>');
                    for (i = 0; i < precios_normal.length; i++) {

                        if (precios_normal[i]['id_precio'] != 3) {
                            if (precios_cliente['categoria_precio'] == precios_normal[i]['id_precio']) {
                                opciones += '<option value="'
                                + precios_normal[i]['id_precio']
                                + '" selected>'
                                + precios_normal[i]['nombre_precio']
                                + '</option>';

                            } else {

                                if (precios_normal[i]['nombre_precio'] == "Precio Venta") {
                                    opciones += '<option value="'
                                    + precios_normal[i]['id_precio']
                                    + '" selected>'
                                    + precios_normal[i]['nombre_precio']
                                    + '</option>';
                                } else {

                                    opciones += '<option value="'
                                    + precios_normal[i]['id_precio']
                                    + '" >'
                                    + precios_normal[i]['nombre_precio']
                                    + '</option>';
                                }


                            }
                        }

                    }
                    $("#precios").append(opciones);
                    $("#precios").trigger("chosen:updated");
                    $("#precios").trigger("chosen:activate");


                }



                if (data.minimo_um == null)
                    var stock = 0;
                else
                    var stock = data.minimo_um;

                if ($("#stockhidden_local_" + $("#select_local").val()).val() == undefined)
                    $("#inentariocontainer").append('<input type="hidden" id="stockhidden_local_' + $("#select_local").val() + '" value="' + stock + '">');
                else
                    $("#stockhidden_local_" + $("#select_local").val()).val(stock);

                var agregado = 0;

                for (var i = 0; i < lst_producto.length; i++) {
                    if (lst_producto[i].id_producto == id && lst_producto[i].local == $("#select_local").val()) {
                        agregado += parseFloat(lst_producto[i].unidades * lst_producto[i].cantidad);
                    }
                }

                if (agregado == data.minimo_um || data.minimo_um == null)
                    $("#stock").text("Sin Stock");
                else if (data.um.max_um_id == data.um.min_um_id)
                    $("#stock").text(data.um.cantidad - agregado + ' ' + data.um.max_um_nombre);
                else {
                    var my_minimo = (data.um.cantidad * data.um.max_unidades + data.um.fraccion) - agregado;
                    cantidad = parseInt(my_minimo / data.um.max_unidades);
                    fraccion = parseInt(my_minimo % data.um.max_unidades);
                    $("#stock").text(cantidad + ' ' + data.um.max_um_nombre + ' / ' + fraccion + ' ' + data.um.min_um_nombre);
                }


                $("#nombreproduto").text($("#selectproductos option:selected").text());

                $("#producto_cualidad").attr('value', data.cualidad);
                if (data.producto_cualidad == "MEDIBLE") {
                    $("#cantidad").attr('min', '1');
                    $("#cantidad").attr('step', '1');
                    $("#cantidad").attr('value', '1');

                } else {
                    $("#cantidad").attr('min', '0.1');
                    $("#cantidad").attr('step', '0.1');
                    $("#cantidad").attr('value', '0.0');

                }



                $("#seleccionunidades").modal('show');

                cambiarnombreprecio();
                setTimeout(function () {
                    $("#precios_chosen .chosen-search input").blur();
                    selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(0)"));


                }, 1);
            }
        });
    }, 500);


}

function change_moneda() {
    $("#moneda_tasa").val($("#monedas option:selected").attr('data-tasa'));
    re_calcularTotales();
}

function re_calcularTotales() {
    $(document).ready(function () {

        var totApagar = $("#totales_totApagar").val();
        var totSubPagar = $("#totales_subTotal").val();
        var totMtoImp = $("#totales_montoigv").val();
        //var moneda = $("#monedas").val();
        var moneda_simbolo = $("#monedas option:selected").attr('data-simbolo');
        var moneda_operador = $("#monedas option:selected").attr('data-oper');

        var tasa = $("#moneda_tasa").val();

        $('#update_moneda').hide();

        if (tasa != 0) {
            $("#block_tasa").show();
            $("#moneda_tasa_div").show();
            $("#moneda_tasa_confirm").val(tasa);
        } else {
            $("#block_tasa").hide();
            $("#moneda_tasa_div").hide();
            $("#moneda_tasa_confirm").val(tasa);
        }
        if (totApagar > 0) {

            if (tasa != 0) {
                if (moneda_operador == '/') {
                    totApagar = parseFloat(totApagar / tasa).toFixed(1);
                    totSubPagar = parseFloat(totSubPagar / tasa).toFixed(1);
                    totMtoImp = parseFloat(totMtoImp / tasa).toFixed(1);
                }
                else if (moneda_operador == '*') {
                    totApagar = parseFloat(totApagar * tasa).toFixed(1);
                    totSubPagar = parseFloat(totSubPagar * tasa).toFixed(1);
                    totMtoImp = parseFloat(totMtoImp * tasa).toFixed(1);
                }
            }

            $("#lblSim1").text(moneda_simbolo);
            $("#lblSim2").text(moneda_simbolo);
            $("#lblSim3").text(moneda_simbolo);
            $("#lblSim4").text(moneda_simbolo);
            $("#lblSim5").text(moneda_simbolo);
            $("#lblSim6").text(moneda_simbolo);
            $("#lblSim10").text(moneda_simbolo);
            $("#lblSim11").text(moneda_simbolo);
            $("#lblSim12").text(moneda_simbolo);

            $("#hdTasaSoles").val(tasa);

            $("#totApagar").val(totApagar);
            $("#totApagar2").val(totApagar);
            $("#subTotal").val(totSubPagar);
            $("#montoigv").val(totMtoImp);

        }

        $("#totApagar").val(totApagar);
        $("#totApagar2").val(totApagar);
        $("#subTotal").val(totSubPagar);
        $("#montoigv").val(totMtoImp);

    });
}


$(document).ready(function () {
    $("#formgarante,#generarventa,#mvisualizarVenta").on("hidden.bs.modal", function () {

        $("body").removeClass("modal-open")
    })
});

function activarText_ModoPago() {
    var modopago = $("#cboModPag").val();
    var dias = $("#diascondicionpago" + modopago).val();

    $("#diascondicionpagoinput").val(dias);
    //console.log(dias);
    if (dias < 1) {
        $("#montxcuotadiv").hide();
        $("#numero_cuotadiv").hide();
        $("#fec_primerpagodiv").hide();
        $("#importediv").show();
        $("#vueltodiv").show();
    } else if (dias > 1) {

        document.getElementById('nrocuota').value = 1;
        document.getElementById('montxcuota').value = "";

        $("#numero_cuotadiv").show();
        $("#montxcuotadiv").show();
        $("#fec_primerpagodiv").show();
        $("#importediv").hide();
        $("#vueltodiv").hide();
    }
}

function calcular_monto_cuota() {
    var totalApagar = $('#totApagar').val();
    var nro = $('#nrocuota').val();
    document.getElementById('montxcuota').value = parseFloat(Math.ceil((totalApagar / nro) * 10) / 10);
}

function PrintElem(elem) {
    Popup($(elem).html());
}

function Popup(data) {
    var mywindow = window.open('', 'ventanaimpresion', 'height=0,width=0');
    mywindow.document.write('<html><head><title></title>');
    /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10

    mywindow.print();
    mywindow.close();

    return true;
}


function ImprimirFactura() {

    /*$("#ventanaimpresion").printThis({
     importCSS: true,
     debug: false,
     printContainer: true,
     pageTitle: "",
     removeInline: false
     loadCSS: "<?= base_url()?>recursos/css/page.css"
     });*/

    Popup($("#ventanaimpresion").html());

    //$('#ventanaimpresion').printElement();

    setTimeout(function () {
        $("#ventanaimpresion").modal('hide');
    }, 2000);
}

/*$("#ventanaimpresion").on('show.bs.modal', function () {
 ImprimirFactura();
 })*/


function ImprimirDocumento(id_venta, tipo_doc) {

    $.ajax({
        url: ruta + 'venta/cargarDatosFactura',
        type: 'POST',
        data: "idventa=" + id_venta,

        success: function (data) {

            $("#ventanaimpresion").html(data);
            //$("#ventanaimpresion").modal('show');
            ImprimirFactura();
        }
    });
}


function cargaData_Impresion(id_venta, devuelto) {

    //refrescarstock();
    $.ajax({
        url: ruta + 'venta/verVenta',
        type: 'POST',
        data: "idventa=" + id_venta,

        success: function (data) {

            $("#mvisualizarVenta").html(data);
            $("#mvisualizarVenta").modal('show');
            if (devuelto) {
                $(".div_nota_credito").removeClass("hide");
                $("#mvisualizarVenta").find('.modal-footer').find('.btn').addClass('pull-left');
            }
        }
    });
}

function cargaData_Impresion_credito(id_venta) {

    //refrescarstock();
    //$("#mvisualizarVentaCredito").modal('show');
    $.ajax({
        url: ruta + 'venta/verVentaVentana',
        type: 'POST',
        data: "idventa=" + id_venta,

        success: function (data) {

            $("#mvisualizarVentaCredito").html(data);
            $("#mvisualizarVentaCredito").modal('show');
            $("#generarventa1").modal('hide');

        }
    });

}


function buscarventasabiertas() {

    $.ajax({
        url: ruta + 'venta/get_ventas_por_status',
        type: 'POST',
        data: {'estatus': 'EN ESPERA'},

        success: function (data) {

            $("#ventasabiertas").html(data);
        }
    });
    $("#ventasabiertas").modal('show');
}


function agregarCliente() {


    $("#agregarcliente").load(ruta + 'cliente/form');
    $('#agregarcliente').modal('show');

}


var cliente = {

    guardar: function () {

        if ($("#razon_social").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar la raz&oacute;n social</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#identificacion").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar la identificaci&oacute;n</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#grupo_id").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el grupo</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#pais_id").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el pais</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }


        if ($("#estados_id").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el estado</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }


        if ($("#ciudad_id").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar la ciudad</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if (isNaN($("#identificacion").val())) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar solo numeros</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            $("#identificacion").focus();
            $(this).prop('disabled', true);

            return false;
        }
        $("#barloadermodal").modal({
            show: true,
            backdrop: 'static'
        });
        $.ajax({
            url: ruta + 'cliente/guardar',
            type: 'POST',
            data: $("#formagregar").serialize(),
            dataType: 'json',
            success: function (data1) {
                $('#barloadermodal').modal('hide');
                if (data1.error == undefined) {
                    $("#agregarcliente").modal('hide');
                    var growlType = 'success';

                    $.bootstrapGrowl('<h4>Se ha agregado el cliente</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });


                    $.ajax({
                        url: ruta + '/cliente/get_all_ajax',
                        headers: {
                            Accept: 'application/json'
                        },
                        success: function (data) {
                            if (data != 'undefined') {
                                var options = '<option value="">Seleccione</option>';
                                for (var i = 0; i < data.clientes.length; i++) {


                                    var selected = '';
                                    if (data1.cliente == data.clientes[i].id_cliente) {
                                        selected = 'selected';
                                    }
                                    options += '<option ' + selected + ' ' + 'value="' + data.clientes[i].id_cliente + '">' + data.clientes[i].razon_social + '</option>';

                                }

                                $("#id_cliente").html(options);
                                $("#id_cliente").trigger("chosen:updated");
                            }
                        }

                    })

                }
                else {

                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>' + data1.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);


                    /*$("#errorspan").text(data.error);
                     $("#error").css('display','block');*/

                }


            }
        })
    }


};
function mostrar_error(mensaje) {
    $("#mostrar_errores").modal("show");
    $(".error_ventana").html("<div class='alert'>" + mensaje + "</div>");
}