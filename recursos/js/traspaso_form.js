var lst_producto = [];
var datos_globales = [];

$(document).ready(function () {
    // este codigo es para que al abrir un modal encima de otro modal no se pierda el scroll
    $('.modal').on("hidden.bs.modal", function (e) {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) - 10);
            $('body').addClass('modal-open');
        }
    }).on("show.bs.modal", function (e) {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop.in').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) + 10);
            $(this).css('z-index', parseInt($('.modal-backdrop.in').first().css('z-index')) + 10);
        }
    });
    //APLICANDO BUSQUEDA AL SELECT DEL PRODUCTO
    setTimeout(function(){
        jQuery('#select_prodc').chosen({search_contains: true});
        $('select').chosen();
    }, 500);

    $('#select_prodc').on("change", function (e) {
        $("#loading").show();
        if ($('#select_prodc').val() != "") {
            e.preventDefault();
            $.ajax({
                url: ruta + 'traspaso/form_buscar',
                type: 'POST',
                dataType: 'json',
                data: {'producto_id': $("#select_prodc").val(), 'local_id': $("#localform1").val()},
                success: function (data) {
                    datos_globales = data;
                    var cantidad_prod = data.cantidad_prod;
                    var um = data.um;
                    var prod_und = data.stock_actual;
                    //MUESTRA EL STOCK ACTUAL DEL PRODUCTO
                    $("#mostrar_nombres").html('');
                    $("#mostrar_nombres").append('<label>' + cantidad_prod["cantidad"] + ' ' + um["nombre_unidad"] + ' / ' + cantidad_prod["fraccion"] + ' ' + um["nombre_fraccion"] + '</label>');
                    //GENERA LA TABLA DEACUERDO A LAS UNIDADES
                    var tabla = '<table class="table block table-striped table-bordered" style="width: 70%">';
                    tabla += '<tr>';
                    for(let x=0; x<prod_und.length; x++){
                        tabla += '<td align="center">';
                        tabla += '<input type="number" name="cantidad" id="cantidad_'+(x+1)+'" required="true" class="form-control" value="">';
                        tabla += '<h6>'+ prod_und[x]['nombre_unidad'] + '(' + prod_und[x]['unidades'] + ' ' + prod_und[x]['abreviatura'] + ')' + '</h6>';
                        tabla += '</td>';
                    }
                    tabla += '<td>';
                    tabla += '<a class="btn btn-primary" data-placement="bottom" style="margin-top:-2.2%;cursor: pointer;" onclick="agregarProducto();">Agregar</a>';
                    tabla += '</td>';
                    tabla += '</tr>';                    
                    tabla += '</table>';
                    $('#mostrar_input').html(tabla);

                    setTimeout(function () {
                        $("#loading").hide();
                        $("#abrir_info").show();
                    }, 1);
                },
                error: function (data) {
                    $("#loading").hide();
                    mensaje('warning', '<h4>Ocurrio un error al buscar el producto</p>');
                }
            });
        } else {
            mensaje('warning', '<h4>Datos incompletos</h4> <p>Debe seleccionar un producto</p>');
        }
    });
});

function cancelarcerrar() {
    $("#confirmarcerrar").modal('show');
}

function preguntar() {
    if (lst_producto.length > 0) {
        $('#MsjPreg').modal('show');
    } else {
        //$("#cantidad").focus();
        mensaje('warning', '<h4>Datos incompletos</h4> <p>Debe seleccionar un producto</p>');
    }
}

function cambiarlocal() {
    if (lst_producto.length > 0) {
        mostrar_advertencia();
    } else {
        local_actual=$("#localform1").val();
        $("#abrir_info").hide();
        productos_porlocal_almacen(); //RELLENA EL SELECT DEL PRODUCTO
    }
}

//este metodo agrega y edita la tabla de los productos
function agregarProducto(){
    if ($('#select_prodc').val() == "") {
        $("#cantidad_1").focus();
        mensaje('warning', '<h4>Datos incompletos</h4> <p>Debe seleccionar un producto</p>');
    }

    var n=0;
    for(let x=0; x<datos_globales.stock_actual.length; x++){
        var cantidad = $("#cantidad_"+(x+1)).val();
        if (cantidad == "" || cantidad < 1 || isNaN(cantidad)) {
            n++;
        }
    }

    if(datos_globales.stock_actual.length==n){
        mensaje('warning', '<h4>Debe ingresar una cantidad válida.</h4>');
    }

    var cantidad = $("#cantidad").val() == "" ? 0 : parseInt($("#cantidad").val());
    var fraccion = $("#fraccion").val() == "" ? 0 : parseInt($("#fraccion").val());
    var suma_en_entrada = ((parseInt(cantidad) * datos_globales['stock_actual']['max_unidades']) + parseInt(fraccion));

    if (suma_en_entrada > datos_globales['stock_minimo']) {
        mensaje('warning', '<h4>Ha ingresado una cantidad mayor a el stock actual</h4>');
    }

    $("#mostrar_nombres").html('');
    var index = get_index_producto($("#select_prodc").val(), $("#localform1").val());
    if (index == -1) {
        var producto = {};
        producto.local_id = $("#localform1").val();
        producto.local_nombre = $("#localform1 option:selected").text();
        producto.index = lst_producto.length;
        producto.producto_nombre = $("#select_prodc option:selected").text();
        producto.producto_id = $("#select_prodc").val();
        producto.cantidad = $("#cantidad").val() == "" ? '0' : $("#cantidad").val();
        producto.fraccion = $("#fraccion").val() == "" ? '0' : $("#fraccion").val();
        lst_producto.push(producto);

    } else {
        lst_producto[index].cantidad = $("#cantidad").val() == "" ? '0' : $("#cantidad").val();
        lst_producto[index].fraccion = $("#fraccion").val() == "" ? '0' : $("#fraccion").val();
    }
    $("#cantidad").val('');
    $("#fraccion").val('');
    $("#cantidad").focus();
    $("#abrir_info").hide();
    $("#select_prodc").val("").trigger("chosen:updated");
    updateView();
}