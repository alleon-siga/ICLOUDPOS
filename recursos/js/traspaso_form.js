    var lst_producto = [];
    var datos_globales = [];
    function preguntar() {
        if (lst_producto.length > 0) {
            $('#MsjPreg').modal('show');
        } else {
            var growlType = 'warning';
            $("#cantidad").focus();
            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar un producto</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }
    }

    function IrGuardar() {
        $('#MsjPreg').modal('hide');
        guardar();
    }

    function cambiarlocal() {

        if (lst_producto.length > 0) {

            mostrar_advertencia();
        } else {
            local_actual=$("#localform1").val();
            $("#abrir_info").hide();
            productos_porlocal_almacen();
        }
    }

    function reiniciar_form() {
        local_actual=$("#localform1").val();
        lst_producto = [];
        $("#advertencia").modal('hide');
        $("#abrir_info").hide();
        productos_porlocal_almacen();
        updateView();


        $("#head_productos").html('');
    }

    function cerrartransferir_mercancia() {
        $('#MsjPreg').modal('hide');
    }


    //funcion interna para sacar el indice del listado dependiendo de sus parametros
    function get_index_producto(producto_id, local_id) {
        for (var i = 0; i < lst_producto.length; i++) {
            if (lst_producto[i].producto_id == producto_id && lst_producto[i].local_id == local_id) {
                return lst_producto[i].index;
            }
        }

        return -1;
    }

    function get_producto(producto_id, local_id) {
        for (var i = 0; i < lst_producto.length; i++) {
            if (lst_producto[i].producto_id == producto_id && lst_producto[i].local_id == local_id) {
                return lst_producto[i];
            }
        }

        return null;
    }

    //este metodo agrega y edita la tabla de los productos
    function agregarProducto() {

        var paso = true;
        var paso_fraccion = false;
        if ($('#select_prodc').val() == "") {
            var growlType = 'warning';
            $("#cantidad").focus();
            $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar un producto</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#cantidad").val() == "" || $("#cantidad").val() < 1 || isNaN($("#cantidad").val())) {

            if ($("#fraccion").val() < 1 || isNaN($("#fraccion").val())) {
                paso_fraccion = true;
                paso = false;
                $.bootstrapGrowl('<h4>Debe ingresar una Fracción válida.</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }

            if ($("#fraccion").val() == "" && paso_fraccion == false && parseInt($("#fraccion").val()) > 0) {
                paso = false;
                $.bootstrapGrowl('<h4>Debe ingresar una cantidad válida.</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });

                return false;

            }
        }

        var cantidad = $("#cantidad").val() == "" ? 0 : parseInt($("#cantidad").val());
        var fraccion = $("#fraccion").val() == "" ? 0 : parseInt($("#fraccion").val());
        var suma_en_entrada = ((parseInt(cantidad) * datos_globales['stock_actual']['max_unidades']) + parseInt(fraccion));

        if (suma_en_entrada > datos_globales['stock_minimo']) {
            $.bootstrapGrowl('<h4>Ha ingresado una cantidad mayor a el stock actual</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            return false;
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

    function delete_producto(item) {
        lst_producto.splice(item, 1);

        for (var i = 0; i < lst_producto.length; i++) {
            lst_producto[i].index = i;
        }
        updateView();
        $("#abrir_info").hide();
        $("#select_prodc").val("").trigger("chosen:updated");
    }


    //refresca la tabla con la vista seleccionada
    function updateView() {


        $("#body_productos").html('');

        $("#head_productos").html('<tr>' +
            '<th style="text-align: center">#</th>' +
            '<th style="text-align: center">Producto</th>' +
            '<th style="text-align: center">Cantidad</th>' +
            '<th style="text-align: center">Fraccion</th>' +
            '<th style="text-align: center">Acciones</th>' +
            '</tr>');


        for (var i = 0; i < lst_producto.length; i++) {

            addTable(lst_producto[i]);
        }
    }

    //añade un elemento a la tabla, tiene sus variaciones dependiendo del tipo de vista
    function addTable(producto) {

        var template = '<tr>';

        template += '<td>' + (parseInt(producto.index) + parseFloat(1)) + '</td>';
        template += '<td>' + decodeURIComponent(producto.producto_nombre) + '</td>';
        template += '<td style="text-align: center">' + producto.cantidad + ' </td>';
        template += '<td style="text-align: center">' + producto.fraccion + ' </td>';
        template += '<td style="text-align: center">';
        template += '<div style="margin-left: 10px;" class="btn-group"><a class="btn btn-danger" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="delete_producto(' + producto.index + ');">';
        template += '<i class="fa fa-trash-o"></i></a>';
        template += '</div>';
        template += '</td>';
        template += '</tr>';

        $("#body_productos").append(template);
    }

    $(document).ready(function () {
        $('.input-datepicker').datepicker({weekStart: 1, format: 'dd-mm-yyyy'});

        $("#valor_localform1").val($("#localform1").val());

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

        setTimeout(function(){
            jQuery('#select_prodc').chosen({search_contains: true});
            $('select').chosen();
        }, 500);

        //productos_porlocal_almacen();

        $('#select_prodc').on("change", function (e) {
            $("#cantidad").val('');
            $("#fraccion").val('');
            //$("#cargando_modal").modal('show');
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

                        var producto = get_producto($("#select_prodc").val(), $("#localform1").val());
                        if(producto != null){
                            $("#cantidad").val(producto.cantidad);
                            $("#fraccion").val(producto.fraccion);
                        }

                        var um = data.um;
                        $("#mostrar_nombres").html('');
                        $("#mostrar_nombres").append('<label>' + cantidad_prod["cantidad"] + ' ' + um["nombre_unidad"] + ' / ' + cantidad_prod["fraccion"] + ' ' + um["nombre_fraccion"] + '</label>');
                        $("#cantidad_producto").val(cantidad_prod["cantidad"])
                        setTimeout(function () {
                            $("#loading").hide();
                            $("#abrir_info").show();
                        }, 1)

                    },
                    error: function (data) {
                        $("#loading").hide();
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Ocurrio un error al buscar el producto</p>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        return false;
                    }
                });
            } else {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Datos incompletos</h4> <p>Debe seleccionar un producto</p>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }
        });
    });
    function cancelarcerrar() {
        $("#confirmarcerrar").modal('show');
    }