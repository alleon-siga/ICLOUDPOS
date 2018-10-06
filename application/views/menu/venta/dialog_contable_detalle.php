<style>
    #producto_id_chosen span {
        text-align: left !important;
    }
</style>
<input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
<div class="modal-dialog" style="width: 90%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Editar Venta Contable <span
                    id="venta_numero"><?= sumCod($venta->venta_id, 6) ?></span></h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="row" style="font-size: 15px;">
                        <div class="col-md-3">
                            <span style="display: <?= $venta->documento_id == 1 ? 'block' : 'none' ?>">
                            <input type="checkbox" value="1"
                                   id="incluir_igv" <?= $venta->documento_id == 1 ? 'checked' : '' ?>> Incluir IGV
                        </span>
                        </div>

                        <div class="col-md-1"><label class="control-label">Subtotal:</label></div>
                        <div class="col-md-2"><?= $venta->moneda_simbolo ?> <span
                                id="h_subtotal">
                                    0.00
                                </span>
                        </div>

                        <div class="col-md-1"><label class="control-label">Impuesto:</label></div>
                        <div class="col-md-2"><?= $venta->moneda_simbolo ?> <span
                                id="h_impuesto">
                                    0.00
                                </span>
                        </div>

                        <div class="col-md-1"><label class="control-label">Total:</label></div>
                        <div class="col-md-2"><?= $venta->moneda_simbolo ?> <span
                                id="h_total">
                                    0.00
                                </span>
                        </div>


                    </div>

                    <hr class="hr-margin-5">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>UM</th>
                            <th>Precio Contable (<?= valueOption("COSTO_AUMENTO", '5') ?>%)</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="body_productos">
                        <tr>
                            <td>
                                <select name="producto_id" id="producto_id" class='form-control'
                                        data-placeholder="Agregar Producto">
                                    <option value=""></option>
                                    <?php foreach ($productos as $producto): ?>
                                        <option value="<?= $producto->producto_id ?>"
                                                data-nombre="<?= $producto->producto_nombre ?>">
                                            <?= getCodigoValue($producto->producto_id, $producto->codigo) . ' - ' . $producto->producto_nombre ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="width: 150px;">
                                <input class="form-control new_producto focus_select"
                                       id="cantidad_new"
                                       type="number"
                                       style="text-align: center;"
                                       readonly
                                       min="0"
                                       value="0">
                            </td>
                            <td style="width: 100px;">
                                <select name="unidad_id" id="unidad_id" class='form-control'
                                        data-placeholder="" disabled>
                                </select>
                            </td>
                            <td style="width: 150px;">
                                <input class="form-control new_producto focus_select"
                                       id="precio_new"
                                       type="number"
                                       style="text-align: center;"
                                       readonly
                                       min="0"
                                       value="0">
                            </td>
                            <td style="text-align: right; width: 150px;"><?= $venta->moneda_simbolo ?>
                                <span id="subtotal_new">
                                            0.00
                                        </span>
                            </td>
                            <td style="text-align: center; width: 70px;">
                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Agregar Producto" data-original-title="Agregar Producto"
                                   href="#"
                                   onclick="add_producto_detalle();">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </td>
                        </tr>
                        <?php $n = 0; ?>
                        <?php foreach ($venta->detalles as $detalle): ?>
                            <tr id="producto_detalle_<?= $n ?>" class="producto_detalles_list"
                                data-id="<?= $detalle->detalle_id ?>" data-index="<?= $n ?>"
                                data-unidad_id="<?= $detalle->unidad_id ?>"
                                data-producto_id="<?= $detalle->producto_id ?>"
                                data-key="<?= $detalle->producto_id . '-' . $detalle->unidad_id ?>">
                                <td><?= $detalle->producto_nombre ?></td>
                                <td style="width: 150px;">
                                    <input class="form-control edit_producto focus_select"
                                           id="cantidad_<?= $n ?>"
                                           data-index="<?= $n ?>"
                                           type="number"
                                           style="text-align: center;"
                                           min="0"
                                           value="<?= $detalle->producto_cualidad=="PESABLE"?$detalle->cantidad:number_format($detalle->cantidad,0) ?>">
                                </td>
                                <td style="width: 100px;"><?= $detalle->unidad_nombre ?></td>
                                <td style="width: 150px;">
                                    <input class="form-control edit_producto focus_select"
                                           id="precio_<?= $n ?>"
                                           data-index="<?= $n ?>"
                                           type="number"
                                           style="text-align: center;"
                                           min="0"
                                           value="<?= $detalle->precio ?>">
                                </td>
                                <td style="text-align: right; width: 150px;"><?= $venta->moneda_simbolo ?>
                                    <span id="subtotal_<?= $n ?>" class="subtotales">
                                            <?= number_format($detalle->importe, 2) ?>
                                        </span>
                                </td>
                                <td style="text-align: center; width: 70px;">
                                    <a class="btn btn-danger" data-toggle="tooltip"
                                       title="Eliminar Producto" data-original-title="Eliminar Producto"
                                       href="#"
                                       onclick="elimimar_producto_detalle('<?= $n++ ?>');">
                                        <i class="fa fa-remove"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">

                    <div class="col-md-12">
                        <input id="save_venta_contable" type="button" class='btn btn-primary' value="Confirmar">

                        <input type="button" class='btn btn-danger' value="Cancelar"
                               onclick="$('#dialog_edit_contable').modal('hide');">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var my_index = $("#body_productos tr").length;

    $(document).ready(function () {
        refresh_totals();

        set_edit_events();

        $('#producto_id, #unidad_id').chosen({
            search_contains: true
        });

        $('.focus_select').on('focus', function () {
            $(this).select();
        });

        $("#incluir_igv").on('change', function () {
            refresh_totals();
        });

        $("#producto_id").on('change', function () {
            var unidades = $("#unidad_id");

            unidades.html("").trigger("chosen:updated");
            unidades.attr('disabled', 'disabled').trigger("chosen:updated");

            $('#cantidad_new').val('0');
            $('#precio_new').val('0');
            $('#subtotal_new').html('0.00');
            $('#cantidad_new').attr('readonly', 'readonly');
            $('#precio_new').attr('readonly', 'readonly');

            $.ajax({
                url: '<?= base_url("venta_new/get_productos_unidades/" . $venta->moneda_id)?>',
                type: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                data: {'producto_id': $("#producto_id").val(), 'precio_id': 3},
                success: function (data) {

                    unidades.html('');


                    var unidad_minima = data.unidades[data.unidades.length - 1];

                    var index = 0;
                    for (var i = 0; i < data.unidades.length; i++) {

                        if (data.unidades[i].presentacion == '1') {
                            var template = '';
                            template += '<option ';
                            template += 'value="' + data.unidades[i].id_unidad + '"';
                            template += 'data-unidades="' + data.unidades[i].unidades + '"';
                            template += 'data-precio_contable="' + parseFloat(data.unidades[i].unidades * data.precio_contable) + '"';
                            template += 'data-precio="' + data.unidades[i].precio + '">';
                            template += data.unidades[i].nombre_unidad;
                            template += '</option>';

                            unidades.append(template);
                        }
                    }

                    unidades.val(unidad_minima.id_unidad);
                    unidades.removeAttr('disabled').trigger("chosen:updated");

                    unidades.on('change', function () {
                        $('#precio_new').val(parseFloat($("#unidad_id option:selected").attr('data-precio_contable')).toFixed(2));
                        $('#precio_new').trigger('keyup');
                    });

                    $('#cantidad_new').removeAttr('readonly');
                    $('#precio_new').removeAttr('readonly');

                    $('#cantidad_new').trigger('focus');
                    $('#precio_new').val(parseFloat(data.precio_contable).toFixed(2));

                },
                complete: function (data) {

                },
                error: function (data) {
                    alert('not');
                }
            });
        });


        $('.new_producto').on('keyup', function () {
            var cantidad = isNaN(parseFloat($('#cantidad_new').val())) ? 0 : parseFloat($('#cantidad_new').val());
            var precio = isNaN(parseFloat($('#precio_new').val())) ? 0 : parseFloat($('#precio_new').val());
            var subtotal = $('#subtotal_new');
            subtotal.html(parseFloat(cantidad * precio).toFixed(2));
        });


        $("#save_venta_contable").on('click', function () {
            var detalles_productos = prepare_detalle_contable();
            if (detalles_productos == false) {
                $.bootstrapGrowl('<h4>Error.</h4> Las Cantidades y/o Precios que deseas guardar no son validos.', {
                    type: 'warning',
                    delay: 5000,
                    allow_dismiss: true
                });
                return false;
            }

            $("#save_venta_contable").attr('disabled', 'disabled');
            $.ajax({
                url: '<?= base_url("venta_new/save_venta_contable")?>',
                type: 'POST',
                dataType: 'json',
                data: '&detalles_productos=' + detalles_productos + '&venta_id=' + $('#venta_id').val(),
                success: function (data) {
                    if (data.success == '1') {
                        $.bootstrapGrowl('<h4>Correcto. </h4><p>La venta contable ha sido actualizada.</p>', {
                            type: 'success',
                            delay: 5000,
                            allow_dismiss: true
                        });
                        $('#dialog_edit_contable').modal('hide');
                    }
                    else {
                        $.bootstrapGrowl('<h4>Error. </h4><p>Ha ocurrido un error inesperado.</p>', {
                            type: 'danger',
                            delay: 5000,
                            allow_dismiss: true
                        });
                    }
                },
                error: function (data) {
                    $.bootstrapGrowl('<h4>Error. </h4><p>Ha ocurrido un error inesperado.</p>', {
                        type: 'danger',
                        delay: 5000,
                        allow_dismiss: true
                    });
                },
                complete: function (data) {
                    $("#save_venta_contable").removeAttr('disabled');
                }
            });
        });

    });

    function prepare_detalle_contable() {
        var productos = [];
        var flag = true;

        $('.producto_detalles_list').each(function () {
            var producto = {};
            var tr = $(this);
            var index = tr.attr('data-index');
            producto.producto_id = tr.attr('data-producto_id');
            producto.unidad_id = tr.attr('data-unidad_id');
            producto.precio = parseFloat($('#precio_' + index).val()).toFixed(2);
            producto.cantidad = parseFloat($('#cantidad_' + index).val()).toFixed(2);

            if (producto.precio <= 0 || producto.cantidad <= 0)
                flag = false;

            productos.push(producto);
        });

        if (flag == false)
            return false;
        else
            return JSON.stringify(productos);
    }

    function set_edit_events() {
        $('.edit_producto').on('keyup', function () {
            var index = $(this).attr('data-index');
            edit_producto(index);
            refresh_totals();
        });
    }

    function refresh_totals() {
        var subtotales = 0;
        $('.subtotales').each(function () {
            subtotales += parseFloat($(this).html().trim());
        });

        if ($("#incluir_igv").prop('checked')) {
            var subtotal = parseFloat(subtotales);
            var impuesto = parseFloat(subtotal * 18 / 100);
            var total = subtotal + impuesto;

            $('#h_total').html(formatPrice(total));
            $('#h_impuesto').html(parseFloat(impuesto).toFixed(2));
            $('#h_subtotal').html(parseFloat(subtotal).toFixed(2));
        }
        else {
            $('#h_total').html(formatPrice(subtotales));
            $('#h_impuesto').html(parseFloat(0).toFixed(2));
            $('#h_subtotal').html(parseFloat(0).toFixed(2));
        }


    }

    function add_producto_detalle() {
        var body = $('#body_productos');

        var producto_id = $("#producto_id").val();
        var producto_nombre = $("#producto_id option:selected").attr('data-nombre');
        var cantidad_new = parseFloat($("#cantidad_new").val());
        var precio_new = parseFloat($("#precio_new").val());
        var unidad_id = $("#unidad_id").val();
        var unidad_nombre = $("#unidad_id option:selected").text();

        if (cantidad_new <= 0) {
            $.bootstrapGrowl('<h4>Error.</h4> Cantidad no valida.', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });

            return false;
        }

        if (precio_new <= 0) {
            $.bootstrapGrowl('<h4>Error.</h4> Precio no valido.', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });

            return false;
        }

        var tr = $('.producto_detalles_list[data-key="' + producto_id + '-' + unidad_id + '"]');

        if (tr.length == 0) {
            var template = '<tr';
            template += ' id="producto_detalle_' + my_index + '"';
            template += ' class="producto_detalles_list"';
            template += ' data-unidad_id="' + unidad_id + '"';
            template += ' data-producto_id="' + producto_id + '"';
            template += ' data-key="' + producto_id + '-' + unidad_id + '"';
            template += ' data-id="0"';
            template += ' data-index="' + my_index + '">';

            template += '<td>' + producto_nombre + '</td>';

            template += '<td style="width: 150px;">';
            template += '<input class="form-control edit_producto focus_select"';
            template += ' id="cantidad_' + my_index + '"';
            template += ' data-index="' + my_index + '"';
            template += ' value="' + cantidad_new + '"';
            template += ' type="number" style="text-align: center;" min="0">';
            template += '</td>';

            template += '<td style="width: 100px;">' + unidad_nombre + '</td>';

            template += '<td style="width: 150px;">';
            template += '<input class="form-control edit_producto focus_select"';
            template += ' id="precio_' + my_index + '"';
            template += ' data-index="' + my_index + '"';
            template += ' value="' + precio_new + '"';
            template += ' type="number" style="text-align: center;" min="0">';
            template += '</td>';

            template += '<td style="text-align: right; width: 150px;"><?= $venta->moneda_simbolo ?>';
            template += '<span id="subtotal_' + my_index + '" class="subtotales">';
            template += parseFloat(cantidad_new * precio_new).toFixed(2);
            template += '</span></td>';

            template += '<td style="text-align: center; width: 70px;">';
            template += '<a class="btn btn-danger" data-toggle="tooltip" href="#"';
            template += ' title="Eliminar Producto" data-original-title="Eliminar Producto"';
            template += 'onclick="elimimar_producto_detalle(\'' + my_index + '\');">';
            template += '<i class="fa fa-remove"></i></a></td>';

            template += '</tr>';

            my_index++;
            body.append(template);

            set_edit_events();

            $('.focus_select').on('focus', function () {
                $(this).select();
            });
        }
        else {
            var index = tr.attr('data-index');
            $("#cantidad_" + index).val(cantidad_new);
            $("#precio_" + index).val(precio_new);
            $("#subtotal_" + index).html(parseFloat(cantidad_new * precio_new).toFixed(2));
            refresh_totals();
        }

        $("#producto_id").val("").trigger("chosen:updated");
        $("#producto_id").change();

    }

    function edit_producto(index) {
        var cantidad = isNaN(parseFloat($('#cantidad_' + index).val())) ? 0 : parseFloat($('#cantidad_' + index).val());
        var precio = isNaN(parseFloat($('#precio_' + index).val())) ? 0 : parseFloat($('#precio_' + index).val());
        var subtotal = $('#subtotal_' + index);
        subtotal.html(parseFloat(cantidad * precio).toFixed(2));
    }

    function elimimar_producto_detalle(index) {
        if (confirm('Estas Seguro?'))
            $('#producto_detalle_' + index).remove();
        else
            return false;

        refresh_totals();
    }

</script>
