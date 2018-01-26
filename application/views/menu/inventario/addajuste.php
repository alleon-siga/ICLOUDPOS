<?php $ruta = base_url(); ?>
<form name="formagregar" action="<?php echo $ruta; ?>inventario/guardar" method="post" id="formagregar">
    <input id="maximahidden" type="hidden">

    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="cancelarcerrar()" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ajustar Inventario</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Fecha:</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="fecha" id="fecha" required="true" class="form-control" readonly
                               value="<?php echo date("d-m-Y"); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
                    </div>
                    <div class="col-md-6">
                        <select id="locales_in" name="local" style="width:250px" disabled>
                            <option value=""> Seleccione</option>
                            <?php if (count($locales) > 0) {

                                foreach ($locales as $local) {
                                    if ($this->session->userdata('id_local') == $local['int_local_id']) {
                                        ?>
                                        <option selected
                                                value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                    <?php } else {
                                        if ($id_local_sel == $local['int_local_id']) { ?>
                                            <option selected
                                                    value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                        <?php } else { ?>
                                            <option
                                                value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                        <?php }
                                    }
                                }
                            } ?>

                        </select>
                    </div>


                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Descripci&oacute;n:</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="descripcion" id="descripcion" required="true" class="form-control"
                               value="">
                    </div>

                </div>


                <div class="form-group row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Buscar Producto:</label>
                    </div>
                    <div class="col-md-10">


                        <select id="select" style="width: 100%">
                            <option value=""> Seleccione el Producto</option>
                            <?php if (count($productos) > 0) {
                                $i = 0;
                                foreach ($productos as $producto) {
                                    ?>

                                    <option class="opciones" value="<?= $producto['producto_id'] ?>">
                                        <?= getCodigoValue(sumCod($producto['producto_id']), $producto['producto_codigo_interno']) . ' - ' . $producto['producto_nombre'] ?></option>


                                <?php }
                            } ?>

                        </select>
                    </div>

                </div>


                <div class="row" id="loading" style="display: none;">
                    <div class="col-md-12 text-center">
                        <div class="loading-icon"></div>
                    </div>
                </div>

                <div class="row form_div" style="display: none;">

                </div>

                <div class="row form_div" style="display: none;">
                    <div id="producto_form" class="col-md-10  text-center"></div>
                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-md-4"><label class="control-label panel-admin-text">TOTAL:</div>
                            <div class="col-md-8"><input id="total_unidades" type="text"
                                                         class="form-control text-center" value="0"
                                                         readonly></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div id="um_minimo" class="col-md-8 text-center"></div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row form_div" style="display: none;">

                    <div class="col-md-2 text-right"><label class="control-label panel-admin-text">Costo
                            Unitario:</label></div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="input-group-addon tipo_tasa"></div>
                            <input type="text" style="text-align: right;"
                                   class='form-control'
                                   name="precio" id="precio" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);">
                        </div>
                    </div>
                    <div class="col-md-2 text-right tasa_moneda"><label class="control-label panel-admin-text">Moneda
                            Tasa:</label></div>
                    <div class="col-md-3 tasa_moneda">
                        <?php $md = get_moneda_defecto()?>
                        <div class="input-group">
                            <div class="input-group-addon"><?php echo $md->simbolo ?></div>
                            <input type="text" style="text-align: right;"
                                   class='form-control'
                                   name="tasa" id="tasa" value="0.00"
                                   onkeydown="return soloDecimal4(this, event);">
                        </div>
                    </div>


                </div>
                <br>
                <div class="row form_div" style="display: none;">
                    <div class="col-sm-2 text-right"><label class="control-label panel-admin-text">Stock
                            Actual:</label></div>
                    <div class="col-sm-3">
                            <span id="stock_actual" style="padding: 5px;"
                                  class="badge label-info panel-admin-text"></span>
                    </div>
                    <div class="col-sm-5"></div>
                    <div class="col-md-2 text-right" id="botonconfirmar">
                        <a class="btn btn-primary" data-placement="bottom"
                           style="margin-top:-2.2%;cursor: pointer;"
                           onclick="agregarProducto();">Ajustar</a>
                    </div>
                </div>


                <br>

                <div class="table-responsive">
                    <table class="table dataTable" id="tablaresult">
                        <thead id="head_productos">


                        </thead>
                        <tbody id="body_productos">


                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-default" onclick="confirmar_save()">Guardar</button>
                <button type="button" class="btn btn-danger" onclick="cancelarcerrar()">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>


</form>

<div class="modal fade" id="confirmar_ajuste" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="" id="" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirmar Ajustes</h4>
                </div>
                <div class="modal-body">
                    <p id="confirm_msg">Est&aacute;s seguro que desea guardar este ajuste de inventario?</p>

                </div>
                <div class="modal-footer">
                    <button type="button" id="botoneliminar" class="btn btn-primary" onclick="save_ajuste()">
                        Confirmar
                    </button>
                    <button type="button" class="btn btn-danger" onclick="$('#confirmar_ajuste').modal('hide');">
                        Cancelar
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<div class="modal fade" id="confirmarcerrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="" id="" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="hide_modal_cerrar()" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Confirmar</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea cerrar la ventana ?</p>
                    <input type="hidden" name="id" id="id_borrar">

                </div>
                <div class="modal-footer">
                    <button type="button" id="botoneliminar" class="btn btn-primary" onclick="confirmarcerrar()">
                        Confirmar
                    </button>
                    <button type="button" class="btn btn-default" onclick="hide_modal_cerrar()">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<div class="modal fade" id="confirmareliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="" id="" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="cancelareliminar()" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Confirmar</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea quitar de la lista el producto seleccionado?</p>
                    <input type="hidden" name="id_producto_eliminar" id="id_producto_eliminar">

                </div>
                <div class="modal-footer">
                    <button type="button" id="botoneliminar" class="btn btn-primary" onclick="sendeteleproducto()">
                        Confirmar
                    </button>
                    <button type="button" class="btn btn-default" onclick="cancelareliminar()">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<script>

    var lst_producto = [];
    var ruta = '<?php echo $ruta?>';

    $(function () {

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

        $("#fecha").datepicker({format: 'dd-mm-yyyy'});


        $("#select").select2({
            placeholder: "Seleccione el producto",
            allowClear: true,
            search_contains: true
        });
        $("#locales_in").select2({
            placeholder: "Seleccione el local",
            allowClear: true
        });

        $("#locales_in").on('change', function () {

            $("#select").change();
        });


        $('#select').on("change", function (e) {
            e.preventDefault();
            $(".form_div").hide();

            if ($(this).val() == "") {
                return false;
            }

            var producto_id = $(this).val();
            var local_id = $("#locales_in").val();

            $("#loading").show();
            $.ajax({
                url: ruta + 'inventario/get_ajuste_unidades',
                type: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                data: {'producto_id': producto_id, 'local_id': local_id},
                success: function (data) {
                    var existencias = $("#producto_form");
                    existencias.html('');

                    if (data.stock_actual.max_um_id != data.stock_actual.min_um_id)
                        $("#stock_actual").html(data.stock_actual.cantidad + ' ' + data.stock_actual.max_um_nombre + ' / ' + data.stock_actual.fraccion + ' ' + data.stock_actual.min_um_nombre);
                    else
                        $("#stock_actual").html(data.stock_actual.cantidad + ' ' + data.stock_actual.max_um_nombre);

                    $("#stock_actual").attr('data-stock_minimo', data.stock_minimo);

                    $('.tipo_tasa').html(data.moneda.simbolo);
                    if (data.moneda.tasa_soles != 0) {
                        $("#tasa").val(data.moneda.tasa_soles);
                        $('.tasa_moneda').show();
                    }
                    else {
                        $('.tasa_moneda').hide();
                        $("#tasa").val(data.moneda.tasa_soles);
                    }

                    $("#tasa").attr('data-moneda-id', data.moneda.id_moneda);

                    for (var i = 0; i < data.unidades.length; i++) {
                        var template = '<div class="col-md-2">';

                        var cantidad_unidades = data.unidades[i].unidades;
                        if ((i + 1) == data.unidades.length) {
                            cantidad_unidades = 1;
                            data.unidades[i].unidades = cantidad_unidades;
                            $("#um_minimo").html(data.unidades[i].nombre_unidad);
                        }

                        var cost = get_costo_producto(producto_id, data.unidades[i].id_unidad, -1);
                        if (cost == -1)
                            cost = data.unidades[i].costo;


                        template += '<div>';
                        template += '<input type="number" class="input-square input-mini form-control text-center cantidad-input" ';
                        template += 'id="cantidad_' + data.unidades[i].id_unidad + '" ';
                        template += 'data-costo="' + cost + '" ';
                        template += 'data-unidades="' + data.unidades[i].unidades + '" ';
                        template += 'data-unidad_id="' + data.unidades[i].id_unidad + '" ';
                        template += 'data-unidad_nombre="' + data.unidades[i].nombre_unidad + '" ';
                        template += 'data-orden="' + data.unidades[i].orden + '" ';
                        template += 'data-minimo="0" ';
                        template += 'onkeydown="return soloDecimal(this, event);">';
                        template += '</div>';

                        template += '<h5>' + data.unidades[i].nombre_unidad + '</h5>';


                        template += '<h6>' + cantidad_unidades + ' ' + data.unidades[data.unidades.length - 1].nombre_unidad + '</h6>';

                        template += '</div>';


                        existencias.append(template);

                        var cantidad = $("#cantidad_" + data.unidades[i].id_unidad);
                        var cant = get_value_producto(producto_id, local_id, data.unidades[i].id_unidad, -1);
                        if (cant == -1) {
                            cantidad.attr('value', data.unidades[i].cantidad);
                            cantidad.attr('data-value', data.unidades[i].cantidad);
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

                    //estructuro la cofiguracion inicial, el costo unitario de la unidad menor
                    var unidad_minima = $("#cantidad_" + data.unidades[data.unidades.length - 1].id_unidad);
                    unidad_minima.attr('data-minimo', '1');
                    var costo = unidad_minima.attr('data-costo');
                    $("#precio").val(costo);


                    //Este ciclo es para los datos iniciales del total y el importe
                    var total = 0;
                    $(".cantidad-input").each(function () {
                        var input = $(this);
                        if (input.val() != 0) {
                            total += parseFloat(input.val() * input.attr('data-unidades'));
                        }
                    });

                    $("#total_unidades").val(total);
                    $("#total_unidades").attr('data-total-base', total);

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

                        }
                    });


                },
                error: function (data) {
                    alert('algo pasa')
                },
                complete: function (data) {
                    $("#loading").hide();
                    $(".form_div").show();
                },
            });
        });


    });

    function confirmar_save() {
        if (lst_producto.length == 0) {
            $.bootstrapGrowl('<h4>Error. </h4><p>Debe agregar al menos un producto para poder guardar el ajuste.</p>', {
                type: 'danger',
                delay: 5000,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#descripcion").val().trim() == "") {
            $.bootstrapGrowl('<h4>Error. </h4><p>El campo descripci&oacute;n no puede estar vacio.</p>', {
                type: 'danger',
                delay: 5000,
                allow_dismiss: true
            });
            return false;
        }

        $("#confirm_msg").html('Est&aacute;s seguro que desea guardar este ajuste de inventario?');
        $("#confirmar_ajuste").modal('show');
    }
    function save_ajuste() {

        $("#confirm_msg").html($("#loading").html());
        $.ajax({
            type: 'POST',
            data: {
                productos: prepare_producto(lst_producto),
                descripcion: $("#descripcion").val(),
                local_id: $("#locales_in").val()
            },
            url: ruta + 'inventario/guardar',
            dataType: 'json',
            success: function (data) {
                $.bootstrapGrowl('<h4>Operaci&oacute;n realizada con exito.</h4>', {
                    type: 'success',
                    delay: 5000,
                    allow_dismiss: true
                });

                $.ajax({
                    url: '<?= base_url()?>inventario/ajusteinventario_by_local',
                    data: {'id_local': local_id},
                    type: 'POST',
                    success: function (data) {
                        // $("#query_consul").html(data.consulta);

                        $("#tabla").html(data);
                        TablesDatatables.init();
                    }
                });

                $("#confirmar_ajuste").modal('hide');
                $("#agregargrupo").modal('hide');

            },
            error: function (data) {
                $.bootstrapGrowl('<h4>Error. </h4><p>Ocurrio un error en el proceso del ajustes.</p>', {
                    type: 'danger',
                    delay: 5000,
                    allow_dismiss: true
                });
                $("#confirmar_ajuste").modal('hide');
            },

        });


    }

    function prepare_producto() {
        var producto_save = [];

        for (var i = 0; i < lst_producto.length; i++) {
            producto_save.push({
                producto_id: lst_producto[i].producto_id,
                total: lst_producto[i].total_minimo,
                moneda_id: lst_producto[i].moneda_id,
                costo: lst_producto[i].costo_minimo,
                tasa: lst_producto[i].tasa
            });
        }

        return JSON.stringify(producto_save);

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

    //funcion interna para sacar la cantidad del listado dependiendo de sus parametros
    function get_value_producto(producto_id, local_id, um_id, defecto) {
        for (var i = 0; i < lst_producto.length; i++) {
            if (lst_producto[i].producto_id == producto_id && lst_producto[i].local_id == local_id && lst_producto[i].detalles[um_id].unidad == um_id) {
                return lst_producto[i].detalles[um_id].cantidad;
            }
        }
        if (defecto != undefined)
            return defecto;
        else return 0;
    }

    //funcion interna para sacar el costo unitario del listado dependiendo de sus parametros
    function get_costo_producto(producto_id, um_id, defecto) {
        for (var i = 0; i < lst_producto.length; i++) {
            if (lst_producto[i].producto_id == producto_id && lst_producto[i].detalles[um_id].unidad == um_id) {
                return lst_producto[i].detalles[um_id].costo_unitario;
            }
        }
        if (defecto != undefined)
            return defecto;
        else return 0;
    }

    //FUNCIONES PARA TRABAJAR CON LOS PRODUCTOS
    //este metodo agrega y edita la tabla de los productos
    function agregarProducto() {

        if (parseFloat($("#total_unidades").val()) == parseFloat($("#stock_actual").attr('data-stock_minimo'))) {
            $.bootstrapGrowl('<h4>Advertencia. </h4><p>No puede realizar un ajuste con el valor actual en stock.</p>', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });
            return false;
        }

        var index = get_index_producto($("#select").val(), $("#locales_in").val());
        if (index == -1) {
            var producto = {};
            producto.index = lst_producto.length;
            producto.producto_id = $("#select").val();
            producto.producto_nombre = encodeURIComponent($("#select option:selected").text());
            producto.local_id = $("#locales_in").val();
            producto.local_nombre = encodeURIComponent($("#locales_in option:selected").text());
            producto.total_minimo = $("#total_unidades").val();
            producto.um_min_nombre = $("#um_minimo").html().trim();
            producto.tasa = $("#tasa").val();
            producto.costo_minimo = $("#precio").val();
            producto.moneda_id = $("#tasa").attr('data-moneda-id');

            producto.detalles = {};
        }


        //AGREGO EL PRODUCTO A lst_producto
        $(".cantidad-input").each(function () {
            var input = $(this);
            var um_id = input.attr('data-unidad_id');

            if (index == -1) {

                producto.detalles[um_id] = {
                    cantidad: input.val(),
                    costo_unitario: parseFloat(input.attr('data-unidades') * $("#precio").val()).toFixed(2),
                    moneda_simbolo: $(".tipo_tasa").html().trim(),
                    unidad: um_id,
                    unidad_nombre: input.attr('data-unidad_nombre'),
                    unidades: input.attr('data-unidades'),
                    um_min: $("#um_minimo").html().trim(),
                    orden: input.attr('data-orden')
                };


            }
            else {
                lst_producto[index].detalles[um_id].cantidad = input.val();
                lst_producto[index].detalles[um_id].costo_unitario = parseFloat(input.attr('data-unidades') * $("#precio").val()).toFixed(2);
                lst_producto[index].total_minimo = $("#total_unidades").val();
            }
        });

        if (index == -1)
            lst_producto.push(producto);

        console.log(producto);


        $("#select").val("").trigger("chosen:updated");
        $("#select").change();


        updateView('detalle');


    }


    function editProducto(producto_id) {
        $("#select").val(producto_id).trigger("chosen:updated");
        $("#select").change();
    }

    function deleteProducto(item) {

        lst_producto.splice(item, 1);

        for (var i = 0; i < lst_producto.length; i++) {
            lst_producto[i].index = i;
        }

        updateView('detalle');
    }

    function sendeteleproducto(){
        $("#confirmareliminar").modal('hide')
        deleteProducto($("#id_producto_eliminar").val())
    }

    function confirmdeleteProducto(item) {

        $("#id_producto_eliminar").val('')
        $("#id_producto_eliminar").val(item)
        $("#confirmareliminar").modal('show')
    }

    function cancelareliminar(){
        $("#confirmareliminar").modal('hide')
    }

    function hide_modal_cerrar(){
        $("#confirmarcerrar").modal('hide');
    }


    function cancelarcerrar() {
        $("#confirmarcerrar").modal('show');
    }
    //refresca la tabla con la vista seleccionada
    function updateView(type) {


        $("#body_productos").html('');

        $("#head_productos").html('<tr>' +
            '<th>#</th>' +
            '<th>Producto</th>' +
            '<th>Detalles</th>' +
            '<th>Total Minimo</th>' +
            '<th>Acciones</th>' +
            '</tr>');

        switch (type) {
            case 'detalle':
            {
                for (var i = 0; i < lst_producto.length; i++) {
                    addTable(lst_producto[i], type);
                }
                break;
            }
        }

    }

    //añade un elemento a la tabla, tiene sus variaciones dependiendo del tipo de vista
    function addTable(producto, type) {
        var template = '<tr>';

        template += '<td>' + (producto.index + 1) + '</td>';
        template += '<td>' + decodeURIComponent(producto.producto_nombre) + '</td>';
        template += '<td style="text-align: center; width: 400px;">';

        template += '<div class="row" style="margin: 0;">';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff; padding: 0;">Nueva Existencia</div>';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff;">UM</div>';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff;">Unidades</div>';
        template += '<div class="col-sm-3" style="background-color: #ADADAD; color: #fff;">Costo Unitario</div>';
        template += '</div>';

        var det = detalles_sort(producto.detalles);
        for (var i = 0; i < det.length; i++) {
            template += '<div class="row" style="margin: 0;">';
            template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2;">' + det[i].cantidad + '</div>';
            template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2;">' + det[i].unidad_nombre + '</div>';
            template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2;">' + det[i].unidades + ' ' + det[i].um_min + '</div>';
            template += '<div class="col-sm-3" style="border: solid 1px #e2e2e2; text-align: right;">' + det[i].moneda_simbolo + ' ' + det[i].costo_unitario + '</div>';
            template += '</div>';
        }

        template += '</td>';
        template += '<td style="text-align: center;">' + producto.total_minimo + ' (' + producto.um_min_nombre + ')</td>';

        template += '<td style="text-align: center;">';

        template += '<div class="btn-group"><a class="btn btn-default" data-toggle="tooltip" title="Editar cantidad" data-original-title="Editar cantidad" onclick="editProducto(' + producto.producto_id + ');">';
        template += '<i class="fa fa-edit"></i></a>';
        template += '</div>';

        template += '<div style="margin-left: 10px;" class="btn-group"><a class="btn btn-danger" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="confirmdeleteProducto(' + producto.index + ');">';
        template += '<i class="fa fa-trash-o"></i></a>';
        template += '</div>';
        template += '</td>';

        template += '</tr>';

        $("#body_productos").append(template);
    }

    function detalles_sort(detalles) {
        var tmp_detalles = [];

        for (var um_id in detalles)
            tmp_detalles.push(detalles[um_id]);

        tmp_detalles.sort(function (a, b) {
            return parseInt(a.orden) - parseInt(b.orden);
        });

        return tmp_detalles;

    }

</script>