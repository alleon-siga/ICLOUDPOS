<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Numeros de Serie del Producto</h4>
        </div>
        <div class="modal-body">

            <h3><?php echo $producto['producto_nombre']; ?></h3>

            <table class='table table-bordered'>
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Serie del Producto</th>
                </tr>
                </thead>
                <tbody id="table_data">
                <?php $n = 1; ?>
                <?php foreach ($series as $s) { ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td>
                            <input <?php echo $read_only != false ? 'readonly' : '' ?>
                                class="form-control serie-number" type="text" data-id="<?php echo $s['id'] ?>"
                                value="<?php echo $s['serie'] ?>">
                        </td>
                    </tr>
                <?php } ?>


                </tbody>
            </table>
            <div class="row">
                <div class="col-md-4">Sin Serie: <span
                        id="serie_full"><?php echo $producto['cantidad'] - $n + 1 ?></span>
                    / <?php echo $producto['cantidad'] ?></div>
                <?php if ($read_only == false): ?>
                    <div class="col-md-4 panel-admin-text text-right">Cantidad a agregar:</div>
                    <div class="col-md-2"><input id="cantidad_serie" class="form-control" type="text" value="1"></div>
                    <div class="col-md-2 text-right">
                        <a id="add_serie" data-number="<?php echo $n; ?>" class="btn btn-default btn-default tip"
                           data-original-title="Agregar Serie" title="Agregar Serie">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="modal-footer">
            <?php if ($read_only == false): ?>
                <button type="button" id="save_series" class="btn btn-primary">
                    Confirmar
                </button>
            <?php endif; ?>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

        </div>


    </div>
    <!-- /.modal-content -->
</div>


<script>
    $(function () {

        $("#add_serie").click(function (e) {
            e.preventDefault();
            cant_left = parseInt($("#serie_full").html().trim());

            if (cant_left < parseInt($("#cantidad_serie").val()))
                return false;

            cant = 1;
            if (parseInt($("#cantidad_serie").val()) > 1)
                cant = parseInt($("#cantidad_serie").val());

            for (i = 0; i < cant; i++) {
                var number = $(this).attr("data-number");
                html = '<tr>';
                html += '<td>' + $(this).attr("data-number") + '</td>';
                html += '<td><input class="form-control serie-number" type="text" data-id="" data-number="' + number + '"> </td>';
                html += '</tr>';
                $("#table_data").append(html);
                $(this).attr("data-number", (parseInt($(this).attr("data-number")) + 1));
            }
            $("#serie_full").html(cant_left - cant);
        });

        $("#save_series").click(function (e) {
            if (validateSerie() == true) {
                var serie_list = [];
                var serie_new = [];
                var prod_id = $("#tbody tr.ui-selected").attr('id');
                var local = $("#locales").val();
                i = 0;
                $(".serie-number").each(function () {
                    var item = $(this);
                    if (item.attr('data-id') != "")
                        serie_list.push({id: item.attr('data-id'), serie: item.val().trim()});
                    else if (item.val().trim() != "")
                        serie_new.push({serie: item.val().trim(), producto_id: prod_id, local_id: local});
                });

                var datas = JSON.stringify(serie_list);
                var new_datas = JSON.stringify(serie_new);
                $.ajax({
                    type: 'POST',
                    data: {data: datas, new_data: new_datas},
                    url: '<?php echo base_url();?>' + 'producto/update_series',
                    dataType: 'json',
                    success: function (data) {
                        $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                            type: 'success',
                            delay: 2500,
                            allow_dismiss: true
                        });
                        $("#serie").modal('hide');
                    },
                    error: function (data) {
                        alert(data.responseText);
                        $.bootstrapGrowl('<h4>Ocurrio un problema al intentar guardar</h4>', {
                            type: 'warning',
                            delay: 2500,
                            allow_dismiss: true
                        });
                        $("#serie").modal('hide');
                    }
                });
            }
            else {
                $.bootstrapGrowl('<h4>Los numeros de Serie no pueden coincidir</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        });

        function validateSerie() {
            var series = $(".serie-number");
            var temp = series;
            var flag = true;
            var error = [];

            series.each(function () {
                var item = $(this);
                temp.each(function () {
                    var item2 = $(this);
                    if ((item.attr('data-number') != item2.attr('data-number') || item.attr('data-id') != item2.attr('data-id')) && item.val().trim() != "")
                        if (item.val().trim() == item2.val().trim()) {
                            error.push({item: item});
                            flag = false;
                        }

                });
                item.css('border', '1px solid green');
            });

            for (i = 0; i < error.length; i++)
                error[i].item.css('border', '1px solid red');

            return flag;
        }

    });


</script>